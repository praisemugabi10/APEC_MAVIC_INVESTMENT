<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

try {
    // Use the configured Uganda timezone.
    $timezone = new DateTimeZone('Africa/Kampala');
    $today = new DateTimeImmutable('today', $timezone);

    // Default to today when no date is supplied.
    $selectedDate = $_GET['date'] ?? $today->format('Y-m-d');

    // Validate the supplied date format and calendar date.
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
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    // Reject today’s future dates and all dates after today.
    if ($dateObject > $today) {
        http_response_code(400);

        echo json_encode([
            'status' => 'error',
            'message' => 'Future dates are not allowed.'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    // Define the selected day's time boundaries.
    $startDate = $dateObject->format('Y-m-d 00:00:00');
    $endDate = $dateObject->modify('+1 day')->format('Y-m-d 00:00:00');

    $stmt = $conn->prepare("
        SELECT
            COALESCE(
                SUM(
                    CASE
                        WHEN transaction_type = 'cash_in'
                         AND transaction_trxn_date >= :cash_in_start
                         AND transaction_trxn_date < :cash_in_end
                        THEN transaction_amount
                        ELSE 0
                    END
                ),
                0
            ) AS total_cash_in,

            COALESCE(
                SUM(
                    CASE
                        WHEN transaction_type = 'cash_out'
                         AND transaction_trxn_date >= :cash_out_start
                         AND transaction_trxn_date < :cash_out_end
                        THEN transaction_amount
                        ELSE 0
                    END
                ),
                0
            ) AS total_cash_out,

            COALESCE(
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
            ) AS balance

        FROM `transaction`
    ");

    $stmt->execute([
        'cash_in_start' => $startDate,
        'cash_in_end' => $endDate,
        'cash_out_start' => $startDate,
        'cash_out_end' => $endDate
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalCashIn = (float) $result['total_cash_in'];
    $totalCashOut = (float) $result['total_cash_out'];
    $balance = (float) $result['balance'];

    echo json_encode([
        'status' => 'success',
        'data' => [
            'date' => $dateObject->format('Y-m-d'),
            'total_cash_in' => $totalCashIn,
            'total_cash_out' => $totalCashOut,
            'balance' => $balance
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    error_log('Transaction balance error: ' . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to calculate transaction balance.'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
