<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

// start the session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// ensure the person generating the report is logged in
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to generate this report.'
    ]);
    exit;
}

$generatorStmt = $conn->prepare("
    SELECT CONCAT(s.first_name, ' ', s.last_name) AS generated_by
    FROM `user` u
    INNER JOIN `staff` s
        ON s.staff_id = u.staff_id
    WHERE u.user_id = :user_id
    LIMIT 1
");

$generatorStmt->execute([
    ':user_id' => $userId
]);

$generatedBy = $generatorStmt->fetchColumn();

if ($generatedBy === false) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'The logged-in user could not be identified.'
    ]);
    exit;
}

try {
    $timezone = new DateTimeZone('Africa/Kampala');
    $today = new DateTimeImmutable('today', $timezone);
    $selectedDate = $_GET['date'] ?? $today->format('Y-m-d');

    $dateObject = DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $selectedDate,
        $timezone
    );

    $dateErrors = DateTimeImmutable::getLastErrors();

    if (
        !$dateObject ||
        ($dateErrors !== false && (
            $dateErrors['warning_count'] > 0 ||
            $dateErrors['error_count'] > 0
        )) ||
        $dateObject->format('Y-m-d') !== $selectedDate
    ) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid date. Use YYYY-MM-DD.'
        ]);
        exit;
    }

    if ($dateObject > $today) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Future dates are not allowed.'
        ]);
        exit;
    }

    $date = $dateObject->format('Y-m-d');
    $start = $date . ' 00:00:00';
    $end = $dateObject->modify('+1 day')->format('Y-m-d 00:00:00');

    /*
    |--------------------------------------------------------------------------
    | 1. Opening balance: all transaction movement before this date
    |--------------------------------------------------------------------------
    */

    $openingStmt = $conn->prepare("
        SELECT COALESCE(
            SUM(
                CASE
                    WHEN transaction_type = 'cash_in'
                        THEN transaction_amount
                    WHEN transaction_type = 'cash_out'
                        THEN -transaction_amount
                    ELSE 0
                END
            ),
            0
        )
        FROM `transaction`
        WHERE transaction_trxn_date < :opening_date
    ");

    $openingStmt->execute([
        ':opening_date' => $start
    ]);

    $cashAtHand = (float) $openingStmt->fetchColumn();

    /*
    |--------------------------------------------------------------------------
    | 2. Cash in, cash out, banking and expenses
    |--------------------------------------------------------------------------
    */

    $movementStmt = $conn->prepare("
        SELECT
            COALESCE(SUM(
                CASE
                    WHEN t.transaction_type = 'cash_in'
                    THEN t.transaction_amount
                    ELSE 0
                END
            ), 0) AS cash_in,

            COALESCE(SUM(
                CASE
                    WHEN t.transaction_type = 'cash_out'
                    AND LOWER(TRIM(COALESCE(tc.transaction_category_name, '')))
                        NOT IN ('bank deposit', 'office expense')
                    THEN t.transaction_amount
                    ELSE 0
                END
            ), 0) AS cash_out,

            COALESCE(SUM(
                CASE
                    WHEN t.transaction_type = 'cash_out'
                    AND LOWER(TRIM(COALESCE(tc.transaction_category_name, '')))
                        = 'bank deposit'
                    THEN t.transaction_amount
                    ELSE 0
                END
            ), 0) AS banking,

            COALESCE(SUM(
                CASE
                    WHEN t.transaction_type = 'cash_out'
                    AND LOWER(TRIM(COALESCE(tc.transaction_category_name, '')))
                        = 'office expense'
                    THEN t.transaction_amount
                    ELSE 0
                END
            ), 0) AS expenses

        FROM `transaction` t
        LEFT JOIN `transaction_category` tc
            ON tc.transaction_category_id = t.transaction_category_id
        WHERE t.transaction_trxn_date >= :start_date
        AND t.transaction_trxn_date < :end_date
    ");

    $movementStmt->execute([
        ':start_date' => $start,
        ':end_date' => $end
    ]);

    $movement = $movementStmt->fetch(PDO::FETCH_ASSOC);

    $movementStmt->execute([
        ':start_date' => $start,
        ':end_date' => $end
    ]);

    $movement = $movementStmt->fetch(PDO::FETCH_ASSOC);

    $cashIn = (float) $movement['cash_in'];
    $cashOut = (float) $movement['cash_out'];
    $banking = (float) $movement['banking'];
    $expenses = (float) $movement['expenses'];

    $totalAvailable = $cashAtHand + $cashIn;
    $totalDeductions = $cashOut + $banking + $expenses;
    $closingBalance = $totalAvailable - $totalDeductions;

    /*
    |--------------------------------------------------------------------------
    | 3. Cash outs: loans issued on the selected date
    |--------------------------------------------------------------------------
    */

    $cashOutsStmt = $conn->prepare("
        SELECT
            l.loan_id,
            l.client_id,
            CONCAT(c.first_name, ' ', c.last_name) AS client_name,
            l.principal_amount AS amount_taken
        FROM `loan` l
        INNER JOIN `client` c
            ON c.client_id = l.client_id
        WHERE l.date_taken >= :start_date
        AND l.date_taken < :end_date
        ORDER BY c.first_name, c.last_name, l.loan_id
    ");

    $cashOutsStmt->execute([
        ':start_date' => $start,
        ':end_date' => $end
    ]);

    $cashOuts = $cashOutsStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cashOuts as $index => &$cashOutRow) {
        $cashOutRow['number'] = $index + 1;
        $cashOutRow['amount_taken'] = (float) $cashOutRow['amount_taken'];
    }
    unset($cashOutRow);

    /*
    |--------------------------------------------------------------------------
    | 4. Expenses: office expenses on the selected date
    |--------------------------------------------------------------------------
    */

    $expensesStmt = $conn->prepare("
        SELECT
            t.transaction_id,
            t.transaction_description AS description,
            t.transaction_amount AS amount
        FROM `transaction` t
        INNER JOIN `transaction_category` tc
            ON tc.transaction_category_id = t.transaction_category_id
        WHERE t.transaction_type = 'cash_out'
        AND LOWER(TRIM(tc.transaction_category_name)) = 'office expense'
        AND t.transaction_trxn_date >= :start_date
        AND t.transaction_trxn_date < :end_date
        ORDER BY t.transaction_trxn_date, t.transaction_id
    ");

    $expensesStmt->execute([
        ':start_date' => $start,
        ':end_date' => $end
    ]);

    $expenseRows = $expensesStmt->fetchAll(PDO::FETCH_ASSOC);

    $totalExpenses = 0;

    foreach ($expenseRows as $index => &$expense) {
        $expense['number'] = $index + 1;
        $expense['amount'] = (float) $expense['amount'];
        $totalExpenses += $expense['amount'];
    }

    unset($expense);

    /*
    |--------------------------------------------------------------------------
    | 3. Expected clients and new clients by field officer
    |--------------------------------------------------------------------------
    */

    $officersStmt = $conn->prepare("
        SELECT
            s.staff_id,
            s.first_name,
            s.last_name,
            COUNT(DISTINCT CASE
                WHEN (
                    (
                        l.loan_status = 'active'
                        AND DATE(l.date_taken) < :expected_date
                    )
                    OR (
                        DATE(l.date_taken) = :expected_same_day
                        AND EXISTS (
                            SELECT 1
                            FROM `payment` p
                            WHERE p.loan_id = l.loan_id
                            AND p.date_paid >= :expected_paid_start
                            AND p.date_paid < :expected_paid_end
                        )
                    )
                )
                THEN l.client_id
            END) AS total_clients,

            COUNT(DISTINCT CASE
                WHEN DATE(l.date_taken) = :new_client_date
                THEN l.client_id
            END) AS new_clients
        FROM `staff` s
        LEFT JOIN `client` c
            ON c.staff_id = s.staff_id
        LEFT JOIN `loan` l
            ON l.client_id = c.client_id
           AND l.date_taken <= :loans_through_date
        WHERE s.type = 'field_officer'
          AND s.active = 1
        GROUP BY s.staff_id, s.first_name, s.last_name
        ORDER BY s.first_name, s.last_name
    ");

    $officersStmt->execute([
        ':expected_date' => $date,
        ':expected_same_day' => $date,
        ':expected_paid_start' => $start,
        ':expected_paid_end' => $end,
        ':new_client_date' => $date,
        ':loans_through_date' => $date
    ]);

    $officers = $officersStmt->fetchAll(PDO::FETCH_ASSOC);

    
    /*
    |--------------------------------------------------------------------------
    | 4. Paid clients by assigned field officer
    |--------------------------------------------------------------------------
    */

    $paidClientsStmt = $conn->prepare("
        SELECT
            c.staff_id AS officer_id,
            COUNT(DISTINCT l.client_id) AS paid_clients
        FROM `payment` p
        INNER JOIN `loan` l
            ON l.loan_id = p.loan_id
        INNER JOIN `client` c
            ON c.client_id = l.client_id
        WHERE p.date_paid >= :paid_start
        AND p.date_paid < :paid_end
        GROUP BY c.staff_id
    ");

    $paidClientsStmt->execute([':paid_start' => $start, ':paid_end' => $end]);

    $paidClientsByOfficer = [];

    foreach ($paidClientsStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $officerId = (string) $row['officer_id'];

        $paidClientsByOfficer[$officerId] =
            (int) $row['paid_clients'];
    }

    /*
    |--------------------------------------------------------------------------
    | 4.1. Clients who paid at the office by assigned field officer
    |--------------------------------------------------------------------------
    */

    $officePaidStmt = $conn->prepare("
        SELECT
            c.staff_id AS officer_id,
            COUNT(DISTINCT l.client_id) AS office_paid
        FROM `payment` p
        INNER JOIN `loan` l
            ON l.loan_id = p.loan_id
        INNER JOIN `client` c
            ON c.client_id = l.client_id
        INNER JOIN `staff` s
            ON s.staff_id = p.collected_by
        WHERE p.date_paid >= :paid_start
        AND p.date_paid < :paid_end
        AND s.type <> 'field_officer'
        GROUP BY c.staff_id
    ");

    $officePaidStmt->execute([':paid_start' => $start, ':paid_end' => $end]);

    $officePaidByOfficer = [];

    foreach ($officePaidStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $officerId = (string) $row['officer_id'];

        $officePaidByOfficer[$officerId] = (int) $row['office_paid'];
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Amounts collected by staff member
    |--------------------------------------------------------------------------
    */

    $collectionsStmt = $conn->prepare("
        SELECT
            p.collected_by AS staff_id,
            s.type AS staff_type,
            COALESCE(SUM(p.amount_paid), 0) AS amount_collected,
            COUNT(DISTINCT l.client_id) AS clients_collected
        FROM `payment` p
        INNER JOIN `loan` l
            ON l.loan_id = p.loan_id
        INNER JOIN `staff` s
            ON s.staff_id = p.collected_by
        WHERE p.date_paid >= :paid_start
        AND p.date_paid < :paid_end
        GROUP BY p.collected_by, s.type
    ");

    $collectionsStmt->execute([':paid_start' => $start, ':paid_end' => $end]);

    $collections = $collectionsStmt->fetchAll(PDO::FETCH_ASSOC);

    $amountByOfficer = [];
    $officePaid = 0;
    $officeAmountCollected = 0;
    $totalAmountCollected = 0;

    foreach ($collections as $row) {
        $staffId = (string) $row['staff_id'];
        $amountCollected = (float) $row['amount_collected'];
        $clientsCollected = (int) $row['clients_collected'];

        $totalAmountCollected += $amountCollected;

        if ($row['staff_type'] === 'field_officer') {
            $amountByOfficer[$staffId] = $amountCollected;
        } else {
            $officePaid += $clientsCollected;
            $officeAmountCollected += $amountCollected;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Assemble field officer turn-up rows
    |--------------------------------------------------------------------------
    */

    $turnUp = [];
    $totalExpected = 0;
    $totalNewClients = 0;

    foreach ($officers as $officer) {
        $officerId = (string) $officer['staff_id'];
        $expected = (int) $officer['total_clients'];
        $newClients = (int) $officer['new_clients'];

        $paid = $paidClientsByOfficer[$officerId] ?? 0;
        $amountCollected = $amountByOfficer[$officerId] ?? 0;
        $officePaidCount = $officePaidByOfficer[$officerId] ?? 0;

        $turnUp[] = [
            'staff_id' => $officerId,
            'name' => trim(
                $officer['first_name'] . ' ' . $officer['last_name']
            ),
            'paid' => $paid,
            'office_paid' => $officePaidCount,
            'total' => $expected,
            'new_clients' => $newClients,
            'amount_collected' => $amountCollected,
            'percentage' => $expected > 0
                ? round(($paid / $expected) * 100, 2)
                : 0
        ];

        $totalExpected += $expected;
        $totalNewClients += $newClients;
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Overall paid clients
    |--------------------------------------------------------------------------
    */

    $overallPaidStmt = $conn->prepare("
        SELECT COUNT(DISTINCT l.client_id)
        FROM `payment` p
        INNER JOIN `loan` l
            ON l.loan_id = p.loan_id
        WHERE p.date_paid >= :paid_start
          AND p.date_paid < :paid_end
    ");

    $overallPaidStmt->execute([':paid_start' => $start, ':paid_end' => $end]);

    $totalPaid = (int) $overallPaidStmt->fetchColumn();

    /*
    |--------------------------------------------------------------------------
    | 7. Add Office and Total rows
    |--------------------------------------------------------------------------
    */

    array_unshift($turnUp, [
        'staff_id' => null,
        'name' => 'Office',
        'paid' => $officePaid,
        'total' => null,
        'new_clients' => null,
        'amount_collected' => $officeAmountCollected,
        'percentage' => null
    ]);

    $turnUp[] = [
        'staff_id' => null,
        'name' => 'Total',
        'paid' => $totalPaid,
        'total' => $totalExpected,
        'new_clients' => $totalNewClients,
        'amount_collected' => $totalAmountCollected,
        'percentage' => $totalExpected > 0
            ? round(($totalPaid / $totalExpected) * 100, 2)
            : 0
    ];

    /*
    |--------------------------------------------------------------------------
    | 8. Return JSON response
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        'status' => 'success',
        'message' => 'Daily cash report generated successfully.',
        'data' => [
            'date' => $date,
            'generated_by' => $generatedBy,
            'financial' => [
                'cash_at_hand' => $cashAtHand,
                'cash_in' => $cashIn,
                'total_available' => $totalAvailable,
                'cash_out' => $cashOut,
                'banking' => $banking,
                'expenses' => $expenses,
                'total_deductions' => $totalDeductions,
                'closing_balance' => $closingBalance
            ],
            'turn_up' => $turnUp,
            'cash_outs' => $cashOuts,
            'expenses_list' => [
                'items' => $expenseRows,
                'total' => $totalExpenses
            ]
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    error_log('Daily cash report error: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to generate the daily cash report.'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
