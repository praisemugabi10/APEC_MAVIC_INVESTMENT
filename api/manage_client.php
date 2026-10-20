<?php
require_once 'config/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response("error", null, "Invalid request method");
}

function response($status, $data = null, $message = "") {
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data,
        "timestamp" => time()
    ]);
    exit;
}

function cleanInput($value) {
    return trim(htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'));
}

function validateName($name) {
    return preg_match("/^[a-zA-Z\s'-]{2,100}$/", $name);
}

function normalizePhone($phone) {
    if (empty($phone)) return null;
    $phone = preg_replace('/\s+/', '', $phone);
    if (preg_match('/^0(7|1)[0-9]{8}$/', $phone)) {
        return '256' . substr($phone, 1);
    }
    if (preg_match('/^256(7|1)[0-9]{8}$/', $phone)) {
        return $phone;
    }
    return false;
}

$action = cleanInput($_POST['action'] ?? '');

switch ($action) {
    case "create":
        createClient($conn);
        break;
    case "list":
        listClients($conn);
        break;
    case "update":
        updateClient($conn);
        break;
    case "delete":
        deleteClient($conn);
        break;
    case "record_payment":
        recordClientPayment($conn);
        break;
    case "cashier_collections":
        cashierCollections($conn);
        break;
    case "payment_history":
        paymentHistory($conn);
        break;
    case "edit_payment":
        editClientPayment($conn);
        break;
    case "payment_day_breakdown":
        paymentDayBreakdown($conn);
        break;
    case "overall_payment_day_percentage":
        overallPaymentDayPercentage($conn);
        break;
    case "overall_officer_payment_breakdown":
        overallOfficerPaymentBreakdown($conn);
        break;
    case 'record_cashier_payment':
        recordCashierPayment($conn);
        break;
    case 'cashier_payment_clients':
        listCashierPaymentClients($conn);
        break;
    default:
        response("error", null, "Invalid action");
}

// function createClient($conn) {
//     // Client Info

//     $firstName = cleanInput($_POST['first_name'] ?? '');
//     $lastName = cleanInput($_POST['last_name'] ?? '');
//     $phonePrimary = normalizePhone(cleanInput($_POST['phone_primary'] ?? ''));
//     $phoneSecondary = normalizePhone(cleanInput($_POST['phone_secondary'] ?? ''));
//     $location = cleanInput($_POST['location'] ?? '');
//     $mortgage = cleanInput($_POST['mortgage'] ?? '');
//     $officerId = cleanInput($_POST['officer_id'] ?? '');
//     $clientId = cleanInput($_POST['client_id'] ?? '');

//     // Guarantor Info

//     $gFirstName = cleanInput($_POST['g_first_name'] ?? '');
//     $gLastName = cleanInput($_POST['g_last_name'] ?? '');
//     $gPhone = normalizePhone(cleanInput($_POST['g_phone'] ?? ''));

//     // Loan Info

//     $principal = floatval($_POST['principal_amount'] ?? 0);
//     $interestRate = floatval($_POST['interest_rate'] ?? 0);
//     $totalRepayable = floatval($_POST['total_repayable'] ?? 0);
//     $dateTaken = $_POST['date_taken'] ?? date('Y-m-d');

//     // Basic Validation

//     if (!validateName($firstName) || !validateName($lastName)) {
//         response("error", null, "Invalid client name");
//     }

//     if (!$phonePrimary) {
//         response("error", null, "Invalid primary phone");
//     }

//     if (empty($officerId)) {
//         response("error", null, "Field Officer is required");
//     }

//     if (!validateName($gFirstName) || !validateName($gLastName)) {
//         response("error", null, "Invalid guarantor name");
//     }

//     if (!$gPhone) {
//         response("error", null, "Invalid guarantor phone number");
//     }

//     if ($principal <= 0) {
//         response("error", null, "Invalid loan amount");
//     }

//     if ($totalRepayable <= 0) {
//         response("error", null, "Invalid total repayable amount");
//     }

//     try {
//         $conn->beginTransaction();

//         // Verify the selected field officer.

//         $officerStmt = $conn->prepare(
//             "SELECT staff_id
//              FROM staff
//              WHERE staff_id = :staff_id
//              AND type = 'field_officer'
//              AND active = 1
//              LIMIT 1
//              FOR UPDATE"
//         );

//         $officerStmt->bindValue(':staff_id', $officerId, PDO::PARAM_STR);
//         $officerStmt->execute();

//         if (!$officerStmt->fetch(PDO::FETCH_ASSOC)) {
//             throw new RuntimeException("Selected field officer does not exist or is inactive");
//         }

//         // 1. Create Guarantor

//         $guarantorId = uniqid("GNT_");

//         $stmtG = $conn->prepare(
//             "INSERT INTO guarantor (
//                 guarantor_id,
//                 first_name,
//                 last_name,
//                 phone_number
//             )
//             VALUES (:guarantor_id, :first_name, :last_name, :phone_number)"
//         );

//         $stmtG->bindValue(':guarantor_id', $guarantorId, PDO::PARAM_STR);
//         $stmtG->bindValue(':first_name', $gFirstName, PDO::PARAM_STR);
//         $stmtG->bindValue(':last_name', $gLastName, PDO::PARAM_STR);
//         $stmtG->bindValue(':phone_number', $gPhone, PDO::PARAM_STR);
//         $stmtG->execute();

//         // 2. Reuse a cleared borrower's client record, or create a new one.

//         if ($clientId) {
//             $existingClientStmt = $conn->prepare(
//                 "SELECT client_id
//                  FROM client
//                  WHERE client_id = :client_id
//                  AND active = 1
//                  FOR UPDATE"
//             );

//             $existingClientStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
//             $existingClientStmt->execute();

//             if (!$existingClientStmt->fetch(PDO::FETCH_ASSOC)) {
//                 throw new RuntimeException("Client not found or inactive");
//             }

//             $latestLoanStmt = $conn->prepare(
//                 "SELECT loan_status
//                  FROM loan
//                  WHERE client_id = :client_id
//                  ORDER BY date_taken DESC, loan_id DESC
//                  LIMIT 1
//                  FOR UPDATE"
//             );

//             $latestLoanStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
//             $latestLoanStmt->execute();

//             $latestLoan = $latestLoanStmt->fetch(PDO::FETCH_ASSOC);

//             if (!$latestLoan || $latestLoan['loan_status'] !== 'cleared') {
//                 throw new RuntimeException("This client must clear their latest loan before taking another one");
//             }

//             $stmtC = $conn->prepare(
//                 "UPDATE client
//                  SET first_name = :first_name,
//                      last_name = :last_name,
//                      phone_primary = :phone_primary,
//                      phone_secondary = :phone_secondary,
//                      location = :location,
//                      mortgage = :mortgage,
//                      staff_id = :staff_id
//                  WHERE client_id = :client_id
//                  AND active = 1"
//             );

//             $stmtC->bindValue(':first_name', $firstName, PDO::PARAM_STR);
//             $stmtC->bindValue(':last_name', $lastName, PDO::PARAM_STR);
//             $stmtC->bindValue(':phone_primary', $phonePrimary, PDO::PARAM_STR);
//             $stmtC->bindValue(':phone_secondary', $phoneSecondary ?: null, PDO::PARAM_STR);
//             $stmtC->bindValue(':location', $location, PDO::PARAM_STR);
//             $stmtC->bindValue(':mortgage', $mortgage ?: null, PDO::PARAM_STR);
//             $stmtC->bindValue(':staff_id', $officerId, PDO::PARAM_STR);
//             $stmtC->bindValue(':client_id', $clientId, PDO::PARAM_STR);
//             $stmtC->execute();
//         } else {
//             $clientId = uniqid("CL_");

//             $stmtC = $conn->prepare(
//                 "INSERT INTO client (
//                     client_id,
//                     first_name,
//                     last_name,
//                     phone_primary,
//                     phone_secondary,
//                     location,
//                     mortgage,
//                     staff_id
//                 )
//                 VALUES (:client_id, :first_name, :last_name, :phone_primary, :phone_secondary, :location, :mortgage, :staff_id)"
//             );

//             $stmtC->bindValue(':client_id', $clientId, PDO::PARAM_STR);
//             $stmtC->bindValue(':first_name', $firstName, PDO::PARAM_STR);
//             $stmtC->bindValue(':last_name', $lastName, PDO::PARAM_STR);
//             $stmtC->bindValue(':phone_primary', $phonePrimary, PDO::PARAM_STR);
//             $stmtC->bindValue(':phone_secondary', $phoneSecondary ?: null, PDO::PARAM_STR);
//             $stmtC->bindValue(':location', $location, PDO::PARAM_STR);
//             $stmtC->bindValue(':mortgage', $mortgage ?: null, PDO::PARAM_STR);
//             $stmtC->bindValue(':staff_id', $officerId, PDO::PARAM_STR);
//             $stmtC->execute();
//         }

//         // 3. Create Loan

//         $loanId = uniqid("LOAN_");

//         $stmtL = $conn->prepare(
//             "INSERT INTO loan (
//                 loan_id,
//                 client_id,
//                 guarantor_id,
//                 principal_amount,
//                 interest_rate,
//                 total_repayable,
//                 date_taken
//             )
//             VALUES (:loan_id, :client_id, :guarantor_id, :principal_amount, :interest_rate, :total_repayable, :date_taken)"
//         );

//         $stmtL->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
//         $stmtL->bindValue(':client_id', $clientId, PDO::PARAM_STR);
//         $stmtL->bindValue(':guarantor_id', $guarantorId, PDO::PARAM_STR);
//         $stmtL->bindValue(':principal_amount', $principal);
//         $stmtL->bindValue(':interest_rate', $interestRate);
//         $stmtL->bindValue(':total_repayable', $totalRepayable);
//         $stmtL->bindValue(':date_taken', $dateTaken, PDO::PARAM_STR);
//         $stmtL->execute();

//         $conn->commit();

//         response(
//             "success",
//             [
//                 "client_id" => $clientId,
//                 "loan_id" => $loanId
//             ],
//             "Client, Guarantor and Loan registered successfully"
//         );
//     } catch (RuntimeException $e) {
//         if ($conn->inTransaction()) {
//             $conn->rollBack();
//         }

//         response("error", null, $e->getMessage());
//     } catch (Exception $e) {
//         if ($conn->inTransaction()) {
//             $conn->rollBack();
//         }

//         response("error", null, "Failed to register client");
//     }
// }

function createClient($conn) {
    // Client Info
    $firstName = cleanInput($_POST['first_name'] ?? '');
    $lastName = cleanInput($_POST['last_name'] ?? '');
    $phonePrimary = normalizePhone(cleanInput($_POST['phone_primary'] ?? ''));
    $phoneSecondary = normalizePhone(cleanInput($_POST['phone_secondary'] ?? ''));
    $location = cleanInput($_POST['location'] ?? '');
    $mortgage = cleanInput($_POST['mortgage'] ?? '');
    $officerId = cleanInput($_POST['officer_id'] ?? '');
    $clientId = cleanInput($_POST['client_id'] ?? '');

    // Guarantor Info
    $gFirstName = cleanInput($_POST['g_first_name'] ?? '');
    $gLastName = cleanInput($_POST['g_last_name'] ?? '');
    $gPhone = normalizePhone(cleanInput($_POST['g_phone'] ?? ''));

    // Loan Info
    $principal = floatval($_POST['principal_amount'] ?? 0);
    $interestRate = floatval($_POST['interest_rate'] ?? 0);
    $totalRepayable = floatval($_POST['total_repayable'] ?? 0);
    $dateTaken = $_POST['date_taken'] ?? date('Y-m-d');

    // Basic Validation
    if (!validateName($firstName) || !validateName($lastName)) {
        response("error", null, "Invalid client name");
    }

    if (!$phonePrimary) {
        response("error", null, "Invalid client phone number");
    }

    if (empty($officerId)) {
        response("error", null, "Field Officer is required");
    }

    if (!validateName($gFirstName) || !validateName($gLastName)) {
        response("error", null, "Invalid guarantor name");
    }

    if (!$gPhone) {
        response("error", null, "Invalid guarantor phone number");
    }

    if ($principal <= 0) {
        response("error", null, "Invalid loan amount");
    }

    if ($totalRepayable <= 0) {
        response("error", null, "Invalid total repayable amount");
    }

    try {
        $conn->beginTransaction();

        // Verify the selected field officer.
        $officerStmt = $conn->prepare(
            "SELECT staff_id
             FROM staff
             WHERE staff_id = :staff_id
             AND type = 'field_officer'
             AND active = 1
             LIMIT 1
             FOR UPDATE"
        );

        $officerStmt->bindValue(':staff_id', $officerId, PDO::PARAM_STR);
        $officerStmt->execute();

        if (!$officerStmt->fetch(PDO::FETCH_ASSOC)) {
            throw new RuntimeException("Selected field officer does not exist or is inactive");
        }

        // 1. Create Guarantor
        $guarantorId = uniqid("GNT_");

        $stmtG = $conn->prepare(
            "INSERT INTO guarantor (
                guarantor_id,
                first_name,
                last_name,
                phone_number
            )
            VALUES (:guarantor_id, :first_name, :last_name, :phone_number)"
        );

        $stmtG->bindValue(':guarantor_id', $guarantorId, PDO::PARAM_STR);
        $stmtG->bindValue(':first_name', $gFirstName, PDO::PARAM_STR);
        $stmtG->bindValue(':last_name', $gLastName, PDO::PARAM_STR);
        $stmtG->bindValue(':phone_number', $gPhone, PDO::PARAM_STR);
        $stmtG->execute();

        // 2. Reuse a cleared borrower's client record, or create a new one.
        if ($clientId) {
            $existingClientStmt = $conn->prepare(
                "SELECT client_id
                 FROM client
                 WHERE client_id = :client_id
                 AND active = 1
                 FOR UPDATE"
            );

            $existingClientStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
            $existingClientStmt->execute();

            if (!$existingClientStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new RuntimeException("Client not found or inactive");
            }

            $latestLoanStmt = $conn->prepare(
                "SELECT loan_status
                 FROM loan
                 WHERE client_id = :client_id
                 ORDER BY date_taken DESC, loan_id DESC
                 LIMIT 1
                 FOR UPDATE"
            );

            $latestLoanStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
            $latestLoanStmt->execute();

            $latestLoan = $latestLoanStmt->fetch(PDO::FETCH_ASSOC);

            if (!$latestLoan || $latestLoan['loan_status'] !== 'cleared') {
                throw new RuntimeException("This client must clear their latest loan before taking another one");
            }

            $stmtC = $conn->prepare(
                "UPDATE client
                 SET first_name = :first_name,
                     last_name = :last_name,
                     phone_primary = :phone_primary,
                     phone_secondary = :phone_secondary,
                     location = :location,
                     mortgage = :mortgage,
                     staff_id = :staff_id
                 WHERE client_id = :client_id
                 AND active = 1"
            );

            $stmtC->bindValue(':first_name', $firstName, PDO::PARAM_STR);
            $stmtC->bindValue(':last_name', $lastName, PDO::PARAM_STR);
            $stmtC->bindValue(':phone_primary', $phonePrimary, PDO::PARAM_STR);
            $stmtC->bindValue(':phone_secondary', $phoneSecondary ?: null, PDO::PARAM_STR);
            $stmtC->bindValue(':location', $location, PDO::PARAM_STR);
            $stmtC->bindValue(':mortgage', $mortgage ?: null, PDO::PARAM_STR);
            $stmtC->bindValue(':staff_id', $officerId, PDO::PARAM_STR);
            $stmtC->bindValue(':client_id', $clientId, PDO::PARAM_STR);
            $stmtC->execute();
        } else {
            $clientId = uniqid("CL_");

            $stmtC = $conn->prepare(
                "INSERT INTO client (
                    client_id,
                    first_name,
                    last_name,
                    phone_primary,
                    phone_secondary,
                    location,
                    mortgage,
                    staff_id
                )
                VALUES (:client_id, :first_name, :last_name, :phone_primary, :phone_secondary, :location, :mortgage, :staff_id)"
            );

            $stmtC->bindValue(':client_id', $clientId, PDO::PARAM_STR);
            $stmtC->bindValue(':first_name', $firstName, PDO::PARAM_STR);
            $stmtC->bindValue(':last_name', $lastName, PDO::PARAM_STR);
            $stmtC->bindValue(':phone_primary', $phonePrimary, PDO::PARAM_STR);
            $stmtC->bindValue(':phone_secondary', $phoneSecondary ?: null, PDO::PARAM_STR);
            $stmtC->bindValue(':location', $location, PDO::PARAM_STR);
            $stmtC->bindValue(':mortgage', $mortgage ?: null, PDO::PARAM_STR);
            $stmtC->bindValue(':staff_id', $officerId, PDO::PARAM_STR);
            $stmtC->execute();
        }

        // 3. Create Loan
        $loanId = uniqid("LOAN_");

        $stmtL = $conn->prepare(
            "INSERT INTO loan (
                loan_id,
                client_id,
                guarantor_id,
                principal_amount,
                interest_rate,
                total_repayable,
                date_taken
            )
            VALUES (:loan_id, :client_id, :guarantor_id, :principal_amount, :interest_rate, :total_repayable, :date_taken)"
        );

        $stmtL->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
        $stmtL->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $stmtL->bindValue(':guarantor_id', $guarantorId, PDO::PARAM_STR);
        $stmtL->bindValue(':principal_amount', $principal);
        $stmtL->bindValue(':interest_rate', $interestRate);
        $stmtL->bindValue(':total_repayable', $totalRepayable);
        $stmtL->bindValue(':date_taken', $dateTaken, PDO::PARAM_STR);
        $stmtL->execute();

        // 4. Create corresponding cash_out transaction for the loan disbursement
        $recordedBy = $_SESSION['user_id'] ?? '';

        if (empty($recordedBy)) {
            throw new RuntimeException("User session not found");
        }

        $categoryStmt = $conn->prepare(
            "SELECT transaction_category_id
             FROM transaction_category
             WHERE transaction_category_name = 'client cash out'
             AND transaction_category_type = 'cash_out'
             AND transaction_category_is_active = '1'
             LIMIT 1"
        );

        $categoryStmt->execute();
        $categoryId = $categoryStmt->fetchColumn();

        if (!$categoryId) {
            throw new RuntimeException("client cash out transaction category not found");
        }

        $transactionId = uniqid("TRXN_");
        $clientTransactionId = uniqid("CTRXN_");
        $transactionDate = $dateTaken . " 00:00:00";

        $transactionStmt = $conn->prepare(
            "INSERT INTO transaction (
                transaction_id,
                transaction_type,
                transaction_category_id,
                transaction_amount,
                transaction_description,
                transaction_recorded_by,
                transaction_trxn_date
            )
            VALUES (
                :transaction_id,
                'cash_out',
                :category_id,
                :amount,
                :description,
                :recorded_by,
                :transaction_date
            )"
        );

        $transactionStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $transactionStmt->bindValue(':category_id', $categoryId, PDO::PARAM_STR);
        $transactionStmt->bindValue(':amount', $principal);
        $transactionStmt->bindValue(':description', 'Loan principal given to client', PDO::PARAM_STR);
        $transactionStmt->bindValue(':recorded_by', $recordedBy, PDO::PARAM_STR);
        $transactionStmt->bindValue(':transaction_date', $transactionDate, PDO::PARAM_STR);
        $transactionStmt->execute();

        // Link transaction to client
        $clientTransactionStmt = $conn->prepare(
            "INSERT INTO client_transaction (
                client_transaction_id,
                client_transaction_transaction_id,
                client_transaction_client_id
            )
            VALUES (
                :client_transaction_id,
                :transaction_id,
                :client_id
            )"
        );

        $clientTransactionStmt->bindValue(':client_transaction_id', $clientTransactionId, PDO::PARAM_STR);
        $clientTransactionStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $clientTransactionStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $clientTransactionStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "client_id" => $clientId,
                "loan_id" => $loanId,
                "transaction_id" => $transactionId,
                "amount" => $principal
            ],
            "Client, Guarantor and Loan registered successfully"
        );
    } catch (RuntimeException $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response("error", null, $e->getMessage());
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response("error", null, "Failed to register client");
    }
}

// function listClients($conn) {
//     $search = cleanInput($_POST['search'] ?? '');
//     $page = max(1, (int) ($_POST['page'] ?? 1));
//     $limit = 10;
//     $offset = ($page - 1) * $limit;

//     try {
//         $whereSql = "WHERE c.active = 1";

//         if (!empty($search)) {
//             $whereSql .= " AND (
//                 c.first_name LIKE :s1
//                 OR c.last_name LIKE :s2
//                 OR CONCAT(c.first_name, ' ', c.last_name) LIKE :s3
//                 OR c.phone_primary LIKE :s4
//                 OR c.client_id LIKE :s5
//                 OR l.loan_id LIKE :s6
//             )";
//         }

//         $countSql = "SELECT COUNT(*)
//                      FROM client c
//                      INNER JOIN loan l ON c.client_id = l.client_id
//                      INNER JOIN guarantor g ON l.guarantor_id = g.guarantor_id
//                      INNER JOIN staff f ON c.staff_id = f.staff_id
//                      $whereSql";

//         $countStmt = $conn->prepare($countSql);

//         if (!empty($search)) {
//             $searchTerm = "%" . $search . "%";

//             $countStmt->bindValue(':s1', $searchTerm, PDO::PARAM_STR);
//             $countStmt->bindValue(':s2', $searchTerm, PDO::PARAM_STR);
//             $countStmt->bindValue(':s3', $searchTerm, PDO::PARAM_STR);
//             $countStmt->bindValue(':s4', $searchTerm, PDO::PARAM_STR);
//             $countStmt->bindValue(':s5', $searchTerm, PDO::PARAM_STR);
//             $countStmt->bindValue(':s6', $searchTerm, PDO::PARAM_STR);
//         }

//         $countStmt->execute();

//         $totalCount = (int) $countStmt->fetchColumn();

//         $sql = "SELECT
//                     c.client_id,
//                     c.first_name,
//                     c.last_name,
//                     c.phone_primary,
//                     c.phone_secondary,
//                     c.location,
//                     c.created_at,
//                     g.guarantor_id,
//                     g.first_name AS g_first_name,
//                     g.last_name AS g_last_name,
//                     g.phone_number AS g_phone,
//                     l.loan_id,
//                     l.principal_amount,
//                     l.interest_rate,
//                     l.total_repayable,
//                     l.loan_status,
//                     l.date_taken,
//                     f.first_name AS officer_fname,
//                     f.last_name AS officer_lname
//                 FROM client c
//                 INNER JOIN loan l ON c.client_id = l.client_id
//                 INNER JOIN guarantor g ON l.guarantor_id = g.guarantor_id
//                 INNER JOIN staff f ON c.staff_id = f.staff_id
//                 $whereSql
//                 ORDER BY c.created_at DESC
//                 LIMIT :limit OFFSET :offset";

//         $stmt = $conn->prepare($sql);

//         if (!empty($search)) {
//             $searchTerm = "%" . $search . "%";

//             $stmt->bindValue(':s1', $searchTerm, PDO::PARAM_STR);
//             $stmt->bindValue(':s2', $searchTerm, PDO::PARAM_STR);
//             $stmt->bindValue(':s3', $searchTerm, PDO::PARAM_STR);
//             $stmt->bindValue(':s4', $searchTerm, PDO::PARAM_STR);
//             $stmt->bindValue(':s5', $searchTerm, PDO::PARAM_STR);
//             $stmt->bindValue(':s6', $searchTerm, PDO::PARAM_STR);
//         }

//         $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
//         $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
//         $stmt->execute();

//         $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

//         response(
//             "success",
//             [
//                 "clients" => $results,
//                 "total_count" => $totalCount,
//                 "page" => $page,
//                 "page_size" => $limit
//             ],
//             "Active clients fetched successfully"
//         );
//     } catch (PDOException $e) {
//         response("error", null, "Database error occurred");
//     }
// }
function listClients($conn) {
    $search = cleanInput($_POST['search'] ?? '');
    $status = cleanInput($_POST['status'] ?? 'active');
    $page = max(1, (int) ($_POST['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    /*
     * Active:
     *     Client has an active loan.
     *
     * Inactive:
     *     Client's loan is cleared or defaulted.
     */
    if ($status === 'inactive') {
        $whereSql = "WHERE l.loan_status IN ('cleared', 'defaulted')";
    } else {
        $whereSql = "WHERE l.loan_status = 'active'";
        $status = 'active';
    }

    if (!empty($search)) {
        $whereSql .= " AND (c.first_name LIKE :s1 OR c.last_name LIKE :s2 OR CONCAT(c.first_name, ' ', c.last_name) LIKE :s3 OR c.phone_primary LIKE :s4 OR c.client_id LIKE :s5 OR l.loan_id LIKE :s6)";
    }

    try {
        $countSql = "SELECT COUNT(*)
                     FROM client c
                     INNER JOIN loan l ON c.client_id = l.client_id
                     INNER JOIN guarantor g ON l.guarantor_id = g.guarantor_id
                     INNER JOIN staff f ON c.staff_id = f.staff_id
                     $whereSql";

        $countStmt = $conn->prepare($countSql);

        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";
            $countStmt->bindValue(':s1', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':s2', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':s3', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':s4', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':s5', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':s6', $searchTerm, PDO::PARAM_STR);
        }

        $countStmt->execute();
        $totalCount = (int) $countStmt->fetchColumn();

        $sql = "SELECT c.client_id,
                       c.first_name,
                       c.last_name,
                       c.phone_primary,
                       c.phone_secondary,
                       c.location,
                       c.mortgage,
                       c.created_at,
                       g.guarantor_id,
                       g.first_name AS g_first_name,
                       g.last_name AS g_last_name,
                       g.phone_number AS g_phone,
                       l.loan_id,
                       l.principal_amount,
                       l.interest_rate,
                       l.total_repayable,
                       l.loan_status,
                       l.date_taken,
                       f.first_name AS officer_fname,
                       f.last_name AS officer_lname
                FROM client c
                INNER JOIN loan l ON c.client_id = l.client_id
                INNER JOIN guarantor g ON l.guarantor_id = g.guarantor_id
                INNER JOIN staff f ON c.staff_id = f.staff_id
                $whereSql
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($sql);

        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";
            $stmt->bindValue(':s1', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':s2', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':s3', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':s4', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':s5', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':s6', $searchTerm, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $message = $status === 'active' ? 'Active clients fetched successfully' : 'Inactive clients fetched successfully';

        response(
            "success",
            [
                "clients" => $results,
                "total_count" => $totalCount,
                "page" => $page,
                "page_size" => $limit
            ],
            $message
        );
    } catch (PDOException $e) {
        response("error", null, "Database error occurred");
    }
}

function updateClient($conn) {
    $clientId = cleanInput($_POST['client_id'] ?? '');
    $firstName = cleanInput($_POST['first_name'] ?? '');
    $lastName = cleanInput($_POST['last_name'] ?? '');
    $location = cleanInput($_POST['location'] ?? '');
    $mortgage = cleanInput($_POST['mortgage'] ?? '');

    if (empty($clientId)) response("error", null, "Client ID required");

    try {
        $stmt = $conn->prepare("UPDATE client SET first_name = ?, last_name = ?, location = ?, mortgage = ? WHERE client_id = ?");
        $stmt->execute([$firstName, $lastName, $location, $mortgage ?: null, $clientId]);
        response("success", null, "Client updated successfully");
    } catch (PDOException $e) {
        response("error", null, "Update failed");
    }
}

function deleteClient($conn) {
    $clientId = cleanInput($_POST['client_id'] ?? '');

    if (empty($clientId)) {
        response("error", null, "Client ID required");
    }

    try {
        // Check whether the client has an active loan
        $stmt = $conn->prepare("
            SELECT loan_id
            FROM loan
            WHERE client_id = :client_id
            AND loan_status = 'active'
            LIMIT 1
        ");

        $stmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->fetch()) {
            response(
                "error",
                null,
                "Client already has an active loan"
            );
        }

        // Deactivate the client
        $stmt = $conn->prepare("
            UPDATE client
            SET active = 0
            WHERE client_id = :client_id
        ");

        $stmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            response(
                "success",
                ["client_id" => $clientId],
                "Client deactivated successfully"
            );
        } else {
            response(
                "error",
                null,
                "Client not found or already inactive"
            );
        }

    } catch (PDOException $e) {
        response("error", null, "Deletion failed");
    }
}

function recordClientPayment($conn) {
    $clientId = cleanInput($_POST['client_id'] ?? '');
    $loanId = cleanInput($_POST['loan_id'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $paymentDate = cleanInput($_POST['payment_date'] ?? '');
    $recordedBy = $_SESSION['user_id'] ?? '';
    $collectedBy = cleanInput($_POST['collected_by'] ?? '');

    if (empty($clientId)) {
        response("error", null, "Client ID is required");
    }

    if (empty($loanId)) {
        response("error", null, "Loan ID is required");
    }

    if ($amount <= 0) {
        response("error", null, "Enter a valid payment amount");
    }

    if (empty($paymentDate)) {
        response("error", null, "Payment date is required");
    }

    $dateObject = DateTime::createFromFormat('Y-m-d', $paymentDate);

    if (!$dateObject || $dateObject->format('Y-m-d') !== $paymentDate) {
        response("error", null, "Invalid payment date");
    }

    if (empty($recordedBy)) {
        response("error", null, "User session not found");
    }
    if (empty($collectedBy)) {
        response("error", null, "Collector ID is required");
    }

    try {
        $conn->beginTransaction();

        // Get the authenticated user's staff ID.
        // The user ID is used as recorded_by and the staff ID as collected_by.

        $userStmt = $conn->prepare(
            "SELECT u.user_id, u.staff_id
             FROM user u
             INNER JOIN staff s ON s.staff_id = u.staff_id
             WHERE u.user_id = :user_id
             AND s.active = 1
             LIMIT 1
             FOR UPDATE"
        );

        $userStmt->bindValue(':user_id', $recordedBy, PDO::PARAM_STR);
        $userStmt->execute();

        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $conn->rollBack();
            response("error", null, "Authenticated user not found or inactive");
        }
     
        // validate the collector (field officer) is active and exists
        $collectorStmt = $conn->prepare(
            "SELECT staff_id, first_name, last_name
            FROM staff
            WHERE staff_id = :staff_id
            AND type = 'field_officer'
            AND active = 1
            LIMIT 1
            FOR UPDATE"
        );

        $collectorStmt->bindValue(':staff_id', $collectedBy, PDO::PARAM_STR);
        $collectorStmt->execute();

        $collector = $collectorStmt->fetch(PDO::FETCH_ASSOC);

        if (!$collector) {
            $conn->rollBack();
            response("error", null, "Selected field officer is not valid or inactive");
        }

        // Verify client and active loan.

        $loanStmt = $conn->prepare(
            "SELECT l.loan_id, l.client_id, l.total_repayable, l.loan_status, c.staff_id AS client_officer_id
            FROM loan l
            INNER JOIN client c ON l.client_id = c.client_id
            WHERE l.loan_id = :loan_id
            AND l.client_id = :client_id
            AND c.active = 1
            FOR UPDATE"
        );

        $loanStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
        $loanStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $loanStmt->execute();

        $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);

        if (!$loan) {
            $conn->rollBack();
            response("error", null, "Client or loan not found");
        }

        if (empty($loan['client_officer_id'])) {
            $conn->rollBack();
            response("error", null, "This client is not assigned to a field officer");
        }

        if ($collectedBy !== $loan['client_officer_id']) {
            $conn->rollBack();
            response("error", null, "Selected field officer is not assigned to this client");
        }

        if ($loan['loan_status'] !== 'active') {
            $conn->rollBack();
            response("error", null, "This loan is no longer active");
        }


        // Calculate current balance.

        $paidStmt = $conn->prepare(
            "SELECT COALESCE(SUM(amount_paid), 0)
             FROM payment
             WHERE loan_id = :loan_id"
        );

        $paidStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
        $paidStmt->execute();

        $amountPaid = (float) $paidStmt->fetchColumn();
        $totalRepayable = (float) $loan['total_repayable'];
        $currentBalance = max($totalRepayable - $amountPaid, 0);

        if ($currentBalance <= 0) {
            $conn->rollBack();
            response("error", null, "This loan has already been fully paid");
        }

        if ($amount > $currentBalance) {
            $conn->rollBack();
            response(
                "error",
                null,
                "Payment cannot exceed the remaining balance of " . number_format($currentBalance, 2)
            );
        }

        $newBalance = $currentBalance - $amount;

        // Find client cash in transaction category.

        $categoryStmt = $conn->prepare(
            "SELECT transaction_category_id
             FROM transaction_category
             WHERE transaction_category_name = 'client cash in'
             AND transaction_category_type = 'cash_in'
             AND transaction_category_is_active = '1'
             LIMIT 1"
        );

        $categoryStmt->execute();

        $categoryId = $categoryStmt->fetchColumn();

        if (!$categoryId) {
            throw new Exception("client cash in transaction category not found");
        }

        $paymentId = uniqid("PAY_");
        $transactionId = uniqid("TRXN_");

        // Record client cash-in transaction.

        $transactionDate = $paymentDate . " 00:00:00";
        $description = "Loan payment from client";

        $transactionStmt = $conn->prepare(
            "INSERT INTO transaction (
                transaction_id,
                transaction_type,
                transaction_category_id,
                transaction_amount,
                transaction_description,
                transaction_recorded_by,
                transaction_trxn_date
            )
            VALUES (
                :transaction_id,
                'cash_in',
                :category_id,
                :amount,
                :description,
                :recorded_by,
                :transaction_date
            )"
        );

        $transactionStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $transactionStmt->bindValue(':category_id', $categoryId, PDO::PARAM_STR);
        $transactionStmt->bindValue(':amount', $amount);
        $transactionStmt->bindValue(':description', $description, PDO::PARAM_STR);
        $transactionStmt->bindValue(':recorded_by', $recordedBy, PDO::PARAM_STR);
        $transactionStmt->bindValue(':transaction_date', $transactionDate, PDO::PARAM_STR);
        $transactionStmt->execute();

        // Record payment.

        $paymentDateTime = $paymentDate . " 00:00:00";

        $paymentStmt = $conn->prepare(
            "INSERT INTO payment (
                payment_id,
                loan_id,
                transaction_id,
                collected_by,
                recorded_by,
                amount_paid,
                balance_at_time,
                date_paid
            )
            VALUES (
                :payment_id,
                :loan_id,
                :transaction_id,
                :collected_by,
                :recorded_by,
                :amount_paid,
                :balance_at_time,
                :date_paid
            )"
        );

        $paymentStmt->bindValue(':payment_id', $paymentId, PDO::PARAM_STR);
        $paymentStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
        $paymentStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $paymentStmt->bindValue(':collected_by', $collectedBy, PDO::PARAM_STR);
        $paymentStmt->bindValue(':recorded_by', $recordedBy, PDO::PARAM_STR);
        $paymentStmt->bindValue(':amount_paid', $amount);
        $paymentStmt->bindValue(':balance_at_time', $newBalance);
        $paymentStmt->bindValue(':date_paid', $paymentDateTime, PDO::PARAM_STR);
        $paymentStmt->execute();

        // Link transaction to client.

        $clientTransactionId = uniqid("CTRXN_");

        $clientTransactionStmt = $conn->prepare(
            "INSERT INTO client_transaction (
                client_transaction_id,
                client_transaction_transaction_id,
                client_transaction_client_id
            )
            VALUES (
                :client_transaction_id,
                :transaction_id,
                :client_id
            )"
        );

        $clientTransactionStmt->bindValue(':client_transaction_id', $clientTransactionId, PDO::PARAM_STR);
        $clientTransactionStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $clientTransactionStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $clientTransactionStmt->execute();

        // Mark loan as cleared when fully paid.

        if ($newBalance <= 0) {
            $clearStmt = $conn->prepare(
                "UPDATE loan
                 SET loan_status = 'cleared'
                 WHERE loan_id = :loan_id"
            );

            $clearStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
            $clearStmt->execute();
        }

        $conn->commit();

        response(
            "success",
            [
                "payment_id" => $paymentId,
                "transaction_id" => $transactionId,
                "amount_paid" => $amount,
                "previous_balance" => $currentBalance,
                "balance" => $newBalance,
                "loan_status" => $newBalance <= 0 ? "cleared" : "active"
            ],
            "Payment recorded successfully"
        );
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log("Failed to record client cash in: " . $e->getMessage());

        response("error", null, "Failed to record payment" . $e->getMessage());
    }
}

function recordCashierPayment($conn) {
    $clientId = cleanInput($_POST['client_id'] ?? '');
    $loanId = cleanInput($_POST['loan_id'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $paymentDate = cleanInput($_POST['payment_date'] ?? '');
    $recordedBy = $_SESSION['user_id'] ?? '';

    if (empty($clientId)) {
        response("error", null, "Client ID is required");
    }

    if (empty($loanId)) {
        response("error", null, "Loan ID is required");
    }

    if ($amount <= 0) {
        response("error", null, "Enter a valid payment amount");
    }

    if (empty($paymentDate)) {
        response("error", null, "Payment date is required");
    }

    $dateObject = DateTime::createFromFormat('Y-m-d', $paymentDate);

    if (!$dateObject || $dateObject->format('Y-m-d') !== $paymentDate) {
        response("error", null, "Invalid payment date");
    }

    if (empty($recordedBy)) {
        response("error", null, "User session not found");
    }

    try {
        $conn->beginTransaction();

        // Get the authenticated cashier.

        $userStmt = $conn->prepare(
            "SELECT u.user_id, u.staff_id, u.role, s.type, s.first_name, s.last_name
             FROM user u
             INNER JOIN staff s ON s.staff_id = u.staff_id
             WHERE u.user_id = :user_id
             AND u.role = 'cashier'
             AND s.type = 'cashier'
             AND s.active = 1
             LIMIT 1
             FOR UPDATE"
        );

        $userStmt->bindValue(':user_id', $recordedBy, PDO::PARAM_STR);
        $userStmt->execute();

        $cashier = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$cashier) {
            $conn->rollBack();
            response("error", null, "You need to log in as a cashier to record this payment");
        }

        $collectedBy = $cashier['staff_id'];

        // Verify client and active loan.

        $loanStmt = $conn->prepare(
            "SELECT l.loan_id, l.client_id, l.total_repayable, l.loan_status
             FROM loan l
             INNER JOIN client c ON l.client_id = c.client_id
             WHERE l.loan_id = :loan_id
             AND l.client_id = :client_id
             AND c.active = 1
             FOR UPDATE"
        );

        $loanStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
        $loanStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $loanStmt->execute();

        $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);

        if (!$loan) {
            $conn->rollBack();
            response("error", null, "Client or loan not found");
        }

        if ($loan['loan_status'] !== 'active') {
            $conn->rollBack();
            response("error", null, "This loan is no longer active");
        }

        // Calculate current balance.

        $paidStmt = $conn->prepare(
            "SELECT COALESCE(SUM(amount_paid), 0)
             FROM payment
             WHERE loan_id = :loan_id"
        );

        $paidStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
        $paidStmt->execute();

        $amountPaid = (float) $paidStmt->fetchColumn();
        $totalRepayable = (float) $loan['total_repayable'];
        $currentBalance = max($totalRepayable - $amountPaid, 0);

        if ($currentBalance <= 0) {
            $conn->rollBack();
            response("error", null, "This loan has already been fully paid");
        }

        if ($amount > $currentBalance) {
            $conn->rollBack();
            response(
                "error",
                null,
                "Payment cannot exceed the remaining balance of " . number_format($currentBalance, 2)
            );
        }

        $newBalance = $currentBalance - $amount;

        // Find client cash in transaction category.

        $categoryStmt = $conn->prepare(
            "SELECT transaction_category_id
             FROM transaction_category
             WHERE transaction_category_name = 'client cash in'
             AND transaction_category_type = 'cash_in'
             AND transaction_category_is_active = '1'
             LIMIT 1"
        );

        $categoryStmt->execute();

        $categoryId = $categoryStmt->fetchColumn();

        if (!$categoryId) {
            throw new Exception("client cash in transaction category not found");
        }

        $paymentId = uniqid("PAY_");
        $transactionId = uniqid("TRXN_");

        // Record client cash-in transaction.

        $transactionDate = $paymentDate . " 00:00:00";
        $description = "Loan payment from client at office";

        $transactionStmt = $conn->prepare(
            "INSERT INTO transaction (
                transaction_id,
                transaction_type,
                transaction_category_id,
                transaction_amount,
                transaction_description,
                transaction_recorded_by,
                transaction_trxn_date
            )
            VALUES (
                :transaction_id,
                'cash_in',
                :category_id,
                :amount,
                :description,
                :recorded_by,
                :transaction_date
            )"
        );

        $transactionStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $transactionStmt->bindValue(':category_id', $categoryId, PDO::PARAM_STR);
        $transactionStmt->bindValue(':amount', $amount);
        $transactionStmt->bindValue(':description', $description, PDO::PARAM_STR);
        $transactionStmt->bindValue(':recorded_by', $recordedBy, PDO::PARAM_STR);
        $transactionStmt->bindValue(':transaction_date', $transactionDate, PDO::PARAM_STR);
        $transactionStmt->execute();

        // Record payment.

        $paymentDateTime = $paymentDate . " 00:00:00";

        $paymentStmt = $conn->prepare(
            "INSERT INTO payment (
                payment_id,
                loan_id,
                transaction_id,
                collected_by,
                recorded_by,
                amount_paid,
                balance_at_time,
                date_paid
            )
            VALUES (
                :payment_id,
                :loan_id,
                :transaction_id,
                :collected_by,
                :recorded_by,
                :amount_paid,
                :balance_at_time,
                :date_paid
            )"
        );

        $paymentStmt->bindValue(':payment_id', $paymentId, PDO::PARAM_STR);
        $paymentStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
        $paymentStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $paymentStmt->bindValue(':collected_by', $collectedBy, PDO::PARAM_STR);
        $paymentStmt->bindValue(':recorded_by', $recordedBy, PDO::PARAM_STR);
        $paymentStmt->bindValue(':amount_paid', $amount);
        $paymentStmt->bindValue(':balance_at_time', $newBalance);
        $paymentStmt->bindValue(':date_paid', $paymentDateTime, PDO::PARAM_STR);
        $paymentStmt->execute();

        // Link transaction to client.

        $clientTransactionId = uniqid("CTRXN_");

        $clientTransactionStmt = $conn->prepare(
            "INSERT INTO client_transaction (
                client_transaction_id,
                client_transaction_transaction_id,
                client_transaction_client_id
            )
            VALUES (
                :client_transaction_id,
                :transaction_id,
                :client_id
            )"
        );

        $clientTransactionStmt->bindValue(':client_transaction_id', $clientTransactionId, PDO::PARAM_STR);
        $clientTransactionStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $clientTransactionStmt->bindValue(':client_id', $clientId, PDO::PARAM_STR);
        $clientTransactionStmt->execute();

        // Link transaction to cashier.

        $staffTransactionId = uniqid("STRXN_");

        $staffTransactionStmt = $conn->prepare(
            "INSERT INTO staff_transaction (
                staff_transaction_id,
                staff_transaction_transaction_id,
                staff_transaction_staff_id
            )
            VALUES (
                :staff_transaction_id,
                :transaction_id,
                :staff_id
            )"
        );

        $staffTransactionStmt->bindValue(':staff_transaction_id', $staffTransactionId, PDO::PARAM_STR);
        $staffTransactionStmt->bindValue(':transaction_id', $transactionId, PDO::PARAM_STR);
        $staffTransactionStmt->bindValue(':staff_id', $collectedBy, PDO::PARAM_STR);
        $staffTransactionStmt->execute();

        // Mark loan as cleared when fully paid.

        if ($newBalance <= 0) {
            $clearStmt = $conn->prepare(
                "UPDATE loan
                 SET loan_status = 'cleared'
                 WHERE loan_id = :loan_id"
            );

            $clearStmt->bindValue(':loan_id', $loanId, PDO::PARAM_STR);
            $clearStmt->execute();
        }

        $conn->commit();

        response(
            "success",
            [
                "payment_id" => $paymentId,
                "transaction_id" => $transactionId,
                "amount_paid" => $amount,
                "previous_balance" => $currentBalance,
                "balance" => $newBalance,
                "loan_status" => $newBalance <= 0 ? "cleared" : "active"
            ],
            "Cash payment recorded successfully"
        );
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log("Failed to record cashier payment: " . $e->getMessage());

        response("error", null, "Failed to record cash payment");
    }
}


// payment history
function paymentHistory($conn)
{
    $clientId = cleanInput($_POST['client_id'] ?? '');
    $loanId = cleanInput($_POST['loan_id'] ?? '');

    if (empty($clientId)) {
        response("error", null, "Client ID is required");
    }

    if (empty($loanId)) {
        response("error", null, "Loan ID is required");
    }

    try {
        $loanStmt = $conn->prepare(
            "SELECT
                l.loan_id,
                l.client_id,
                l.principal_amount,
                l.interest_rate,
                l.total_repayable,
                l.date_taken,
                l.loan_status,
                c.first_name,
                c.last_name,
                c.phone_primary
             FROM loan l
             INNER JOIN client c ON l.client_id = c.client_id
             WHERE l.loan_id = :loan_id
             AND l.client_id = :client_id
             AND c.active = 1
             LIMIT 1"
        );
        $loanStmt->bindValue(":loan_id", $loanId, PDO::PARAM_STR);
        $loanStmt->bindValue(":client_id", $clientId, PDO::PARAM_STR);
        $loanStmt->execute();

        $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);

        if (!$loan) {
            response("error", null, "Client or loan not found");
        }

        $paymentStmt = $conn->prepare(
            "SELECT
                payment_id,
                amount_paid,
                balance_at_time,
                date_paid,
                payment_created_at
            FROM payment
            WHERE loan_id = :loan_id
            ORDER BY date_paid ASC, payment_id ASC"
        );
        $paymentStmt->bindValue(":loan_id", $loanId, PDO::PARAM_STR);
        $paymentStmt->execute();

        $payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

        $amountPaid = 0;
        $totalRepayable = (float)$loan['total_repayable'];
        $dailyExpected = $totalRepayable / 30;

        $dateTaken = new DateTime($loan['date_taken']);

        foreach ($payments as &$payment) {
            $paymentAmount = (float)$payment['amount_paid'];
            $amountPaid += $paymentAmount;

            $paymentDate = new DateTime($payment['date_paid']);
            $paymentDate->setTime(0, 0, 0);

            $dateTakenForCalculation = clone $dateTaken;
            $dateTakenForCalculation->setTime(0, 0, 0);

            // Repayment starts the day after the loan is issued.
            $daysElapsed = (int)$dateTakenForCalculation->diff($paymentDate)->days;

            if ($paymentDate < $dateTakenForCalculation) {
                $daysElapsed = 0;
            }

            // Maximum repayment period is 30 days.
            $repaymentDays = min(max($daysElapsed, 0), 30);

            // Expected amount accumulated by this payment date.
            $cumulativeExpected = min(
                $totalRepayable,
                $dailyExpected * $repaymentDays
            );

            // Earliers carry forward any amount that should have been paid
            // by this date but has not yet been paid.
            $earliers = max(
                $cumulativeExpected - $amountPaid,
                0
            );

            $payment['earliers'] = round($earliers, 2);
        }
        unset($payment);

        $currentBalance = max($totalRepayable - $amountPaid, 0);

        response("success", [
            "loan" => $loan,
            "amount_paid" => $amountPaid,
            "balance" => $currentBalance,
            "payments" => $payments
        ], "Payment history fetched successfully");

    } catch (PDOException $e) {
        response("error", null, "Database error occurred");
    }
}

// edit payment history
    function editClientPayment($conn)
    {
        $clientId = cleanInput($_POST['client_id'] ?? '');
        $loanId = cleanInput($_POST['loan_id'] ?? '');
        $paymentId = cleanInput($_POST['payment_id'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        $paymentDate = cleanInput($_POST['payment_date'] ?? '');

        if (empty($clientId)) {
            response("error", null, "Client ID is required");
        }

        if (empty($loanId)) {
            response("error", null, "Loan ID is required");
        }

        if (empty($paymentId)) {
            response("error", null, "Payment ID is required");
        }

        if ($amount <= 0) {
            response("error", null, "Enter a valid payment amount");
        }

        if (empty($paymentDate)) {
            response("error", null, "Payment date is required");
        }

        $dateObject = DateTime::createFromFormat('Y-m-d', $paymentDate);

        if (!$dateObject || $dateObject->format('Y-m-d') !== $paymentDate) {
            response("error", null, "Invalid payment date");
        }

        try {
            $conn->beginTransaction();

            // Lock the payment being edited
            $paymentStmt = $conn->prepare("SELECT payment_id, loan_id, transaction_id, amount_paid, balance_at_time, date_paid, payment_created_at, updated_at FROM payment WHERE payment_id = :payment_id AND loan_id = :loan_id FOR UPDATE");
            $paymentStmt->bindParam(":payment_id", $paymentId, PDO::PARAM_STR);
            $paymentStmt->bindParam(":loan_id", $loanId, PDO::PARAM_STR);
            $paymentStmt->execute();

            $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                $conn->rollBack();
                response("error", null, "Payment not found");
            }

            // Make sure the payment belongs to the supplied client
            $loanStmt = $conn->prepare("SELECT l.loan_id, l.client_id, l.total_repayable, l.loan_status FROM loan l INNER JOIN client c ON l.client_id = c.client_id WHERE l.loan_id = :loan_id AND l.client_id = :client_id AND c.active = 1 FOR UPDATE");
            $loanStmt->bindParam(":loan_id", $loanId, PDO::PARAM_STR);
            $loanStmt->bindParam(":client_id", $clientId, PDO::PARAM_STR);
            $loanStmt->execute();

            $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);

            if (!$loan) {
                $conn->rollBack();
                response("error", null, "Client or loan not found");
            }

            // The original entry must be within the 12-hour editing window
            $editWindowStmt = $conn->prepare("SELECT payment_created_at, TIMESTAMPDIFF(SECOND, payment_created_at, NOW()) AS seconds_since_creation FROM payment WHERE payment_id = :payment_id FOR UPDATE");
            $editWindowStmt->bindParam(":payment_id", $paymentId, PDO::PARAM_STR);
            $editWindowStmt->execute();

            $editWindow = $editWindowStmt->fetch(PDO::FETCH_ASSOC);

            if (!$editWindow) {
                $conn->rollBack();
                response("error", null, "Payment not found");
            }

            $secondsSinceCreation = (int)$editWindow['seconds_since_creation'];

            if ($secondsSinceCreation < 0 || $secondsSinceCreation > 43200) {
                $conn->rollBack();
                response("error", null, "This payment can no longer be edited. Payments can only be edited within 12 hours of being recorded.");
            }

            if (empty($payment['transaction_id'])) {
                $conn->rollBack();
                response("error", null, "This payment has no linked transaction and cannot be edited safely.");
            }

            $totalRepayable = (float)$loan['total_repayable'];
            $oldAmount = (float)$payment['amount_paid'];

            // Calculate total paid after replacing the old amount with the new amount
            $totalPaidStmt = $conn->prepare("SELECT COALESCE(SUM(amount_paid), 0) FROM payment WHERE loan_id = :loan_id AND payment_id <> :payment_id");
            $totalPaidStmt->bindParam(":loan_id", $loanId, PDO::PARAM_STR);
            $totalPaidStmt->bindParam(":payment_id", $paymentId, PDO::PARAM_STR);
            $totalPaidStmt->execute();

            $otherPaymentsTotal = (float)$totalPaidStmt->fetchColumn();
            $newTotalPaid = $otherPaymentsTotal + $amount;

            if ($newTotalPaid > $totalRepayable) {
                $conn->rollBack();
                response("error", null, "The new payment amount would exceed the loan's total repayable amount.");
            }

            $paymentDateTime = $paymentDate . " 00:00:00";

            // Update the payment itself
            $updatePaymentStmt = $conn->prepare("UPDATE payment SET amount_paid = :amount_paid, date_paid = :date_paid WHERE payment_id = :payment_id");
            $updatePaymentStmt->bindParam(":amount_paid", $amount);
            $updatePaymentStmt->bindParam(":date_paid", $paymentDateTime, PDO::PARAM_STR);
            $updatePaymentStmt->bindParam(":payment_id", $paymentId, PDO::PARAM_STR);
            $updatePaymentStmt->execute();

            // Update the exact transaction linked to this payment
            $updateTransactionStmt = $conn->prepare("UPDATE transaction SET transaction_amount = :amount, transaction_trxn_date = :transaction_date WHERE transaction_id = :transaction_id");
            $updateTransactionStmt->bindParam(":amount", $amount);
            $updateTransactionStmt->bindParam(":transaction_date", $paymentDateTime, PDO::PARAM_STR);
            $updateTransactionStmt->bindParam(":transaction_id", $payment['transaction_id'], PDO::PARAM_STR);
            $updateTransactionStmt->execute();

            // Lock all payments for this loan and recalculate balances chronologically
            $allPaymentsStmt = $conn->prepare("SELECT payment_id, amount_paid, date_paid, payment_created_at FROM payment WHERE loan_id = :loan_id ORDER BY date_paid ASC, payment_created_at ASC, payment_id ASC FOR UPDATE");
            $allPaymentsStmt->bindParam(":loan_id", $loanId, PDO::PARAM_STR);
            $allPaymentsStmt->execute();

            $allPayments = $allPaymentsStmt->fetchAll(PDO::FETCH_ASSOC);

            $runningPaid = 0;

            $updateBalanceStmt = $conn->prepare("UPDATE payment SET balance_at_time = :balance WHERE payment_id = :payment_id");

            foreach ($allPayments as $row) {
                $runningPaid += (float)$row['amount_paid'];

                $balanceAtTime = max($totalRepayable - $runningPaid, 0);

                $updateBalanceStmt->bindValue(":balance", $balanceAtTime);
                $updateBalanceStmt->bindValue(":payment_id", $row['payment_id'], PDO::PARAM_STR);
                $updateBalanceStmt->execute();
            }

            // Synchronize loan status with the recalculated total
            $finalBalance = max($totalRepayable - $runningPaid, 0);
            $loanStatus = $finalBalance <= 0 ? 'cleared' : 'active';

            $loanStatusStmt = $conn->prepare("UPDATE loan SET loan_status = :loan_status WHERE loan_id = :loan_id");
            $loanStatusStmt->bindParam(":loan_status", $loanStatus, PDO::PARAM_STR);
            $loanStatusStmt->bindParam(":loan_id", $loanId, PDO::PARAM_STR);
            $loanStatusStmt->execute();

            $conn->commit();

            response("success", [
                "payment_id" => $paymentId,
                "transaction_id" => $payment['transaction_id'],
                "old_amount" => $oldAmount,
                "new_amount" => $amount,
                "payment_date" => $paymentDate,
                "total_paid" => $runningPaid,
                "balance" => $finalBalance,
                "loan_status" => $loanStatus
            ], "Payment updated successfully");

        } catch (Exception $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            response("error", null, "Failed to update payment");
        }
    }

//payment day breakdown
function paymentDayBreakdown($conn) {
    $officerId = cleanInput($_POST['officer_id'] ?? '');
    $selectedDate = cleanInput($_POST['selected_date'] ?? '');

    if (empty($officerId)) {
        response("error", null, "Field Officer ID is required");
    }

    if (empty($selectedDate)) {
        response("error", null, "Payment date is required");
    }

    $selectedDateObject = DateTime::createFromFormat('Y-m-d', $selectedDate);

    if (!$selectedDateObject || $selectedDateObject->format('Y-m-d') !== $selectedDate) {
        response("error", null, "Invalid payment date");
    }

    try {
        $sql = "
            SELECT
                c.client_id,
                c.first_name,
                c.last_name,
                c.phone_primary,
                c.location,
                l.loan_id,
                l.principal_amount,
                l.interest_rate,
                l.total_repayable,
                l.date_taken,
                COALESCE(payment_summary.total_paid_as_at_date, 0) AS total_paid_as_at_date,
                COALESCE(payment_summary.payment_count_as_at_date, 0) AS payment_count_as_at_date,
                COALESCE(payment_summary.total_paid_on_date, 0) AS total_paid_on_date,
                COALESCE(payment_summary.officer_paid_on_date, 0) AS officer_paid_on_date,
                COALESCE(payment_summary.payment_count_on_date, 0) AS payment_count_on_date,
                CASE
                    WHEN COALESCE(payment_summary.payment_count_on_date, 0) > 0 THEN 'paid'
                    ELSE 'unpaid'
                END AS payment_day_status,
                GREATEST(
                    l.total_repayable - COALESCE(payment_summary.total_paid_as_at_date, 0),
                    0
                ) AS balance_as_at_date
            FROM client c
            INNER JOIN loan l
                ON c.client_id = l.client_id
            LEFT JOIN (
                SELECT
                    p.loan_id,
                    SUM(
                        CASE
                            WHEN DATE(p.date_paid) <= :selected_date_before
                            THEN p.amount_paid
                            ELSE 0
                        END
                    ) AS total_paid_as_at_date,
                    COUNT(
                        CASE
                            WHEN DATE(p.date_paid) <= :selected_date_count_before
                            THEN 1
                            ELSE NULL
                        END
                    ) AS payment_count_as_at_date,
                    SUM(
                        CASE
                            WHEN DATE(p.date_paid) = :selected_date_on
                            THEN p.amount_paid
                            ELSE 0
                        END
                    ) AS total_paid_on_date,
                    SUM(
                        CASE
                            WHEN DATE(p.date_paid) = :selected_date_officer
                            AND p.collected_by = :officer_id_payment
                            THEN p.amount_paid
                            ELSE 0
                        END
                    ) AS officer_paid_on_date,
                    COUNT(
                        CASE
                            WHEN DATE(p.date_paid) = :selected_date_on_count
                            THEN 1
                            ELSE NULL
                        END
                    ) AS payment_count_on_date
                FROM payment p
                GROUP BY p.loan_id
            ) payment_summary
                ON l.loan_id = payment_summary.loan_id
            WHERE c.active = 1
                AND c.staff_id = :officer_id
                AND (
                    DATE(l.date_taken) < :selected_date_loan_start
                    OR (
                        DATE(l.date_taken) = :selected_date_loan_same_day
                        AND COALESCE(payment_summary.payment_count_on_date, 0) > 0
                    )
                )
            HAVING balance_as_at_date > 0 OR payment_count_on_date > 0
            ORDER BY c.first_name ASC, c.last_name ASC
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':selected_date_before', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_count_before', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_on', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_officer', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':officer_id_payment', $officerId, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_on_count', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':officer_id', $officerId, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_loan_start', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_loan_same_day', $selectedDate, PDO::PARAM_STR);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $paidClients = [];
        $unpaidClients = [];

        foreach ($results as $client) {
            if ($client['payment_day_status'] === 'paid') {
                $paidClients[] = $client;
            } else {
                $unpaidClients[] = $client;
            }
        }

        $paidClientCount = count($paidClients);
        $expectedClientCount = count($results);

        $dailyPaymentPercentage = $expectedClientCount > 0
            ? round(($paidClientCount / $expectedClientCount) * 100, 2)
            : 0;

        response(
            "success",
            [
                "selected_date" => $selectedDate,
                "paid_count" => $paidClientCount,
                "unpaid_count" => count($unpaidClients),
                "total_expected_count" => $expectedClientCount,
                "daily_payment_percentage" => $dailyPaymentPercentage,
                "paid" => $paidClients,
                "unpaid" => $unpaidClients
            ],
            "Payment day breakdown fetched successfully"
        );
    } catch (PDOException $e) {
        error_log("Payment day breakdown error: " . $e->getMessage());
        response("error", null, "Database error occurred");
    }
}

function overallPaymentDayPercentage($conn)
{
    $selectedDate = cleanInput($_POST['selected_date'] ?? '');

    if (empty($selectedDate)) {
        response("error", null, "Payment date is required");
    }

    $selectedDateObject = DateTime::createFromFormat('Y-m-d', $selectedDate);

    if (!$selectedDateObject || $selectedDateObject->format('Y-m-d') !== $selectedDate) {
        response("error", null, "Invalid payment date");
    }

    try {
        $sql = "
            SELECT
                COUNT(*) AS total_expected_count,
                SUM(client_day_status.paid_status) AS paid_count
            FROM (
                SELECT
                    c.client_id,
                    MAX(
                        CASE
                            WHEN (
                                DATE(l.date_taken) < :selected_date_loan_start
                                OR (
                                    DATE(l.date_taken) = :selected_date_loan_same_day
                                    AND COALESCE(payment_summary.payment_count_on_date, 0) > 0
                                )
                            )
                            AND (
                                GREATEST(
                                    l.total_repayable - COALESCE(payment_summary.total_paid_as_at_date, 0),
                                    0
                                ) > 0
                                OR COALESCE(payment_summary.payment_count_on_date, 0) > 0
                            )
                            THEN 1
                            ELSE 0
                        END
                    ) AS expected_status,
                    MAX(
                        CASE
                            WHEN (
                                DATE(l.date_taken) < :selected_date_paid_loan_start
                                OR (
                                    DATE(l.date_taken) = :selected_date_paid_loan_same_day
                                    AND COALESCE(payment_summary.payment_count_on_date, 0) > 0
                                )
                            )
                            AND COALESCE(payment_summary.payment_count_on_date, 0) > 0
                            THEN 1
                            ELSE 0
                        END
                    ) AS paid_status
                FROM client c
                INNER JOIN loan l
                    ON c.client_id = l.client_id
                LEFT JOIN (
                    SELECT
                        p.loan_id,
                        SUM(
                            CASE
                                WHEN DATE(p.date_paid) <= :selected_date_before
                                THEN p.amount_paid
                                ELSE 0
                            END
                        ) AS total_paid_as_at_date,
                        COUNT(
                            CASE
                                WHEN DATE(p.date_paid) = :selected_date_on
                                THEN 1
                                ELSE NULL
                            END
                        ) AS payment_count_on_date
                    FROM payment p
                    GROUP BY p.loan_id
                ) payment_summary
                    ON l.loan_id = payment_summary.loan_id
                WHERE c.active = 1
                GROUP BY c.client_id
            ) client_day_status
            WHERE client_day_status.expected_status = 1
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':selected_date_loan_start', $selectedDate, PDO::PARAM_STR);
        $stmt->bindParam(':selected_date_loan_same_day', $selectedDate, PDO::PARAM_STR);
        $stmt->bindParam(':selected_date_paid_loan_start', $selectedDate, PDO::PARAM_STR);
        $stmt->bindParam(':selected_date_paid_loan_same_day', $selectedDate, PDO::PARAM_STR);
        $stmt->bindParam(':selected_date_before', $selectedDate, PDO::PARAM_STR);
        $stmt->bindParam(':selected_date_on', $selectedDate, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $expectedClientCount = (int) ($result['total_expected_count'] ?? 0);
        $paidClientCount = (int) ($result['paid_count'] ?? 0);

        $dailyPaymentPercentage = $expectedClientCount > 0
            ? round(($paidClientCount / $expectedClientCount) * 100, 2)
            : 0;

        response("success", [
            "selected_date" => $selectedDate,
            "paid_count" => $paidClientCount,
            "total_expected_count" => $expectedClientCount,
            "daily_payment_percentage" => $dailyPaymentPercentage
        ], "Overall daily payment percentage fetched successfully");

    } catch (PDOException $e) {
        response("error", null, "Database error occurred");
    }
}

function overallOfficerPaymentBreakdown($conn) {
    $selectedDate = cleanInput($_POST['selected_date'] ?? '');

    if (empty($selectedDate)) {
        response("error", null, "Payment date is required");
    }

    $selectedDateObject = DateTime::createFromFormat('Y-m-d', $selectedDate);

    if (!$selectedDateObject || $selectedDateObject->format('Y-m-d') !== $selectedDate) {
        response("error", null, "Invalid payment date");
    }

    try {
        $sql = "
            SELECT
                fo.staff_id AS officer_id,
                CONCAT(
                    COALESCE(fo.first_name, ''),
                    ' ',
                    COALESCE(fo.last_name, '')
                ) AS officer_name,
                COUNT(client_day_status.client_id) AS expected_count,
                SUM(client_day_status.paid_status) AS paid_count
            FROM staff fo
            LEFT JOIN (
                SELECT
                    c.client_id,
                    c.staff_id,
                    MAX(
                        CASE
                            WHEN (
                                DATE(l.date_taken) < :selected_date_loan_start
                                OR (
                                    DATE(l.date_taken) = :selected_date_loan_same_day
                                    AND COALESCE(payment_summary.payment_count_on_date, 0) > 0
                                )
                            )
                            AND (
                                GREATEST(
                                    l.total_repayable - COALESCE(payment_summary.total_paid_as_at_date, 0),
                                    0
                                ) > 0
                                OR COALESCE(payment_summary.payment_count_on_date, 0) > 0
                            )
                            THEN 1
                            ELSE 0
                        END
                    ) AS expected_status,
                    MAX(
                        CASE
                            WHEN COALESCE(payment_summary.payment_count_on_date, 0) > 0
                            THEN 1
                            ELSE 0
                        END
                    ) AS paid_status
                FROM client c
                INNER JOIN loan l
                    ON c.client_id = l.client_id
                LEFT JOIN (
                    SELECT
                        p.loan_id,
                        SUM(
                            CASE
                                WHEN DATE(p.date_paid) <= :selected_date_before
                                THEN p.amount_paid
                                ELSE 0
                            END
                        ) AS total_paid_as_at_date,
                        COUNT(
                            CASE
                                WHEN DATE(p.date_paid) = :selected_date_on
                                THEN 1
                                ELSE NULL
                            END
                        ) AS payment_count_on_date
                    FROM payment p
                    GROUP BY p.loan_id
                ) payment_summary
                    ON l.loan_id = payment_summary.loan_id
                WHERE c.active = 1
                GROUP BY c.client_id, c.staff_id
            ) client_day_status
                ON fo.staff_id = client_day_status.staff_id
                AND client_day_status.expected_status = 1
            WHERE fo.type = 'field_officer'
                AND fo.active = 1
            GROUP BY fo.staff_id, fo.first_name, fo.last_name
            ORDER BY fo.first_name ASC, fo.last_name ASC
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(':selected_date_loan_start', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_loan_same_day', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_before', $selectedDate, PDO::PARAM_STR);
        $stmt->bindValue(':selected_date_on', $selectedDate, PDO::PARAM_STR);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $officers = [];

        foreach ($results as $officer) {
            $expectedCount = (int) ($officer['expected_count'] ?? 0);
            $paidCount = (int) ($officer['paid_count'] ?? 0);

            $paymentPercentage = $expectedCount > 0
                ? round(($paidCount / $expectedCount) * 100, 2)
                : 0;

            $officers[] = [
                "officer_id" => $officer['officer_id'],
                "officer_name" => trim($officer['officer_name']),
                "payment_percentage" => $paymentPercentage
            ];
        }

        response(
            "success",
            [
                "selected_date" => $selectedDate,
                "officers" => $officers
            ],
            "Field officer payment breakdown fetched successfully"
        );
    } catch (PDOException $e) {
        response("error", null, "Database error occurred");
    }
}

// cashier collections
function cashierCollections($conn) {

    $selectedDate = cleanInput($_POST['selected_date'] ?? '');
    $userId = $_SESSION['user_id'] ?? '';

    if (empty($selectedDate)) {
        response("error", null, "Payment date is required");
    }

    $dateObject = DateTime::createFromFormat('Y-m-d', $selectedDate);

    if (!$dateObject || $dateObject->format('Y-m-d') !== $selectedDate) {
        response("error", null, "Invalid payment date");
    }

    if (empty($userId)) {
        response("error", null, "User session not found");
    }

    try {

        /*
        |--------------------------------------------------------------------------
        | Verify authenticated user is an active cashier
        |--------------------------------------------------------------------------
        */

        $userStmt = $conn->prepare(
            "SELECT u.user_id, u.staff_id, s.type, s.first_name, s.last_name
             FROM user u
             INNER JOIN staff s ON s.staff_id = u.staff_id
             WHERE u.user_id = :user_id
             AND s.active = 1
             AND s.type IN ('cashier', 'manager')
             LIMIT 1"
        );

        $userStmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $userStmt->execute();

        $cashier = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$cashier) {
            response("error", null, "Authenticated user is not an active cashier");
        }

        $cashierStaffId = $cashier['staff_id'];

        /*
        |--------------------------------------------------------------------------
        | Fetch cashier collections for selected date
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare(
            "SELECT
                p.payment_id,
                p.amount_paid,
                DATE(p.date_paid) AS payment_date,

                c.client_id,
                c.first_name,
                c.last_name,
                c.phone_primary,

                l.loan_id,
                l.principal_amount,
                l.total_repayable,

                fo.staff_id AS officer_id,
                fo.first_name AS officer_first_name,
                fo.last_name AS officer_last_name

             FROM payment p

             INNER JOIN loan l
                ON l.loan_id = p.loan_id

             INNER JOIN client c
                ON c.client_id = l.client_id

             INNER JOIN staff fo
                ON fo.staff_id = c.staff_id

             WHERE p.collected_by = :cashier_staff_id
             AND DATE(p.date_paid) = :selected_date

             ORDER BY p.date_paid DESC"
        );

        $stmt->bindValue(
            ':cashier_staff_id',
            $cashierStaffId,
            PDO::PARAM_STR
        );

        $stmt->bindValue(
            ':selected_date',
            $selectedDate,
            PDO::PARAM_STR
        );

        $stmt->execute();

        $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /*
        |--------------------------------------------------------------------------
        | Calculate total cashier cash-in
        |--------------------------------------------------------------------------
        */

        $totalCashIn = 0;

        foreach ($collections as $collection) {
            $totalCashIn += (float) $collection['amount_paid'];
        }

        /*
        |--------------------------------------------------------------------------
        | Number of clients who paid
        |--------------------------------------------------------------------------
        */

        $clientCount = count($collections);

        response(
            "success",
            [
                "total_cash_in" => $totalCashIn,
                "client_count" => $clientCount,
                "collections" => $collections,
                "cashier_name" => trim($cashier['first_name'] . ' ' . $cashier['last_name'])
            ],
            "Cashier collections loaded successfully"
        );

    } catch (Exception $e) {

        error_log(
            "Failed to load cashier collections: " . $e->getMessage()
        );

        response(
            "error",
            null,
            "Failed to load cashier collections"
        );
    }
}

function listCashierPaymentClients($conn) {
    try {
        $sql = "SELECT
                    c.client_id,
                    c.first_name,
                    c.last_name,
                    l.loan_id,
                    l.total_repayable,
                    GREATEST(
                        l.total_repayable - COALESCE(SUM(p.amount_paid), 0),
                        0
                    ) AS balance
                FROM client c
                INNER JOIN loan l ON c.client_id = l.client_id
                LEFT JOIN payment p ON l.loan_id = p.loan_id
                WHERE c.active = 1
                AND l.loan_status = 'active'
                GROUP BY
                    c.client_id,
                    c.first_name,
                    c.last_name,
                    l.loan_id,
                    l.total_repayable
                HAVING balance > 0
                ORDER BY c.first_name ASC, c.last_name ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        response(
            "success",
            $clients,
            "Active loan clients fetched successfully"
        );
    } catch (PDOException $e) {
        error_log("Failed to load cashier payment clients: " . $e->getMessage());
        response("error", null, "Database error occurred");
    }
}
