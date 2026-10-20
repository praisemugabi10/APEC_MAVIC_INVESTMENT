<?php
require_once 'config/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response("error", null, "Invalid request method");
}

// Updated response function using "status" and string values
function response($status, $data = null, $message = "")
{
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data,
        "timestamp" => time()
    ]);
    exit;
}

//validation
function cleanInput($value)
{
    return trim(htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'));
}

function validateName($name)
{
    return preg_match("/^[a-zA-Z\s'-]{2,100}$/", $name);
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function normalizePhone($phone)
{
    $phone = preg_replace('/\s+/', '', $phone);
    if (preg_match('/^0(7|1)[0-9]{8}$/', $phone)) {
        return '256' . substr($phone, 1);
    }
    if (preg_match('/^256(7|1)[0-9]{8}$/', $phone)) {
        return $phone;
    }
    return false;
}

// obtain the action from the user
$action = cleanInput($_POST['action'] ?? '');
switch ($action) {
    case "create":
        createFieldOfficer($conn);
        break;
    case "update":
        updateFieldOfficer($conn);
        break;
    // case "delete":
    //     deleteFieldOfficer($conn);
    //     break;
    case "list":
        listFieldOfficers($conn);
        break;
    case "clients":
        listOfficerClients($conn);
        break;
    default:
        response("error", null, "Invalid action");
}

// create field officer
function createFieldOfficer($conn) {
    $firstName = cleanInput($_POST['first_name'] ?? '');
    $lastName = cleanInput($_POST['last_name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $phone = cleanInput($_POST['phone_number'] ?? '');

    if (!validateName($firstName)) {
        response("error", null, "Invalid first name");
    }

    if (!validateName($lastName)) {
        response("error", null, "Invalid last name");
    }

    if (!validateEmail($email)) {
        response("error", null, "Invalid email address");
    }

    $phone = normalizePhone($phone);

    if (!$phone) {
        response("error", null, "Invalid phone number");
    }

    try {
        $check = $conn->prepare(
            "SELECT staff_id
             FROM staff
             WHERE email = :email
             OR phone_number = :phone
             LIMIT 1"
        );

        $check->execute([
            ':email' => $email,
            ':phone' => $phone
        ]);

        if ($check->fetch(PDO::FETCH_ASSOC)) {
            response("error", null, "Email or phone number already exists");
        }

        $officerId = uniqid("STF");

        $stmt = $conn->prepare(
            "INSERT INTO staff (
                staff_id,
                first_name,
                last_name,
                email,
                phone_number,
                type
            )
            VALUES (
                :staff_id,
                :first_name,
                :last_name,
                :email,
                :phone_number,
                'field_officer'
            )"
        );

        $stmt->execute([
            ':staff_id' => $officerId,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone_number' => $phone
        ]);

        response("success", [
            "officer_id" => $officerId
        ], "Field officer created successfully");

    } catch (PDOException $e) {
        response("error", null, "Database error occurred");
    }
}

// update officer
function updateFieldOfficer($conn) {
    $officerId = cleanInput($_POST['officer_id'] ?? '');
    $firstName = cleanInput($_POST['first_name'] ?? '');
    $lastName = cleanInput($_POST['last_name'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $phone = cleanInput($_POST['phone_number'] ?? '');

    if (empty($officerId)) {
        response("error", null, "Officer ID is required");
    }

    if (!validateName($firstName)) {
        response("error", null, "Invalid first name");
    }

    if (!validateName($lastName)) {
        response("error", null, "Invalid last name");
    }

    if (!validateEmail($email)) {
        response("error", null, "Invalid email address");
    }

    $phone = normalizePhone($phone);

    if (!$phone) {
        response("error", null, "Invalid phone number");
    }

    try {
        $checkOfficer = $conn->prepare(
            "SELECT staff_id
             FROM staff
             WHERE staff_id = :staff_id
             AND type = 'field_officer'
             LIMIT 1"
        );

        $checkOfficer->execute([
            ':staff_id' => $officerId
        ]);

        if (!$checkOfficer->fetch(PDO::FETCH_ASSOC)) {
            response("error", null, "Field officer not found");
        }

        $duplicate = $conn->prepare(
            "SELECT staff_id
             FROM staff
             WHERE (email = :email OR phone_number = :phone)
             AND staff_id != :staff_id
             LIMIT 1"
        );

        $duplicate->execute([
            ':email' => $email,
            ':phone' => $phone,
            ':staff_id' => $officerId
        ]);

        if ($duplicate->fetch(PDO::FETCH_ASSOC)) {
            response("error", null, "Email or phone number already exists");
        }

        $stmt = $conn->prepare(
            "UPDATE staff SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone_number = :phone_number
             WHERE staff_id = :staff_id
             AND type = 'field_officer'"
        );

        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone_number' => $phone,
            ':staff_id' => $officerId
        ]);

        response("success", [
            "officer_id" => $officerId
        ], "Field officer updated successfully");

    } catch (PDOException $e) {
        response("error", null, "Database error occurred");
    }
}


// delete field officer
// function deleteFieldOfficer($conn)
// {
//     $officerId = cleanInput($_POST['officer_id'] ?? '');

//     if (empty($officerId)) {
//         response("error", null, "Officer ID is required");
//     }

//     try {

//         $check = $conn->prepare(
//             "SELECT officer_id
//              FROM field_officer
//              WHERE officer_id = :officer_id"
//         );

//         $check->bindParam(":officer_id", $officerId, PDO::PARAM_STR);

//         $check->execute();

//         if ($check->rowCount() === 0) {
//             response("error", null, "Field officer not found");
//         }

//         $conn->beginTransaction();

//         $stmt = $conn->prepare(
//             "DELETE FROM field_officer
//              WHERE officer_id = :officer_id"
//         );


//         $stmt->bindParam(":officer_id", $officerId, PDO::PARAM_STR);

//         $stmt->execute();
//         $conn->commit();

//         response("success", [
//             "officer_id" => $officerId
//         ], "Field officer deleted successfully");


//     } catch (PDOException $e) {
//         if ($conn->inTransaction()) {
//             $conn->rollBack();
//         }
//         response("error", null, "Database error occurred");
//     }
// }

// fetch field officers
function listFieldOfficers($conn) {
    $search = cleanInput($_POST['search'] ?? '');
    $page = max(1, (int) ($_POST['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    try {
        $whereSql = "WHERE fo.type = 'field_officer' AND fo.active = 1";

        if (!empty($search)) {
            $whereSql .= " AND (
                fo.first_name LIKE :search1
                OR fo.last_name LIKE :search2
                OR CONCAT(fo.first_name, ' ', fo.last_name) LIKE :search3
                OR fo.email LIKE :search4
                OR fo.phone_number LIKE :search5
            )";
        }

        $countSql = "SELECT COUNT(*)
                     FROM staff fo
                     $whereSql";

        $countStmt = $conn->prepare($countSql);

        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";

            $countStmt->bindValue(':search1', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':search2', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':search3', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':search4', $searchTerm, PDO::PARAM_STR);
            $countStmt->bindValue(':search5', $searchTerm, PDO::PARAM_STR);
        }

        $countStmt->execute();
        $totalCount = (int) $countStmt->fetchColumn();

        $sql = "SELECT
                    fo.staff_id AS officer_id,
                    fo.first_name,
                    fo.last_name,
                    fo.email,
                    fo.phone_number,
                    fo.created_at,
                    fo.active,
                    (
                        SELECT COUNT(*)
                        FROM client c
                        INNER JOIN loan l ON l.loan_id = (
                            SELECT l2.loan_id
                            FROM loan l2
                            WHERE l2.client_id = c.client_id
                            ORDER BY l2.date_taken DESC, l2.loan_id DESC
                            LIMIT 1
                        )
                        WHERE c.staff_id = fo.staff_id
                        AND c.active = 1
                        AND l.loan_status = 'active'
                    ) AS client_count
                FROM staff fo
                $whereSql
                ORDER BY fo.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($sql);

        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";

            $stmt->bindValue(':search1', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':search2', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':search3', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':search4', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':search5', $searchTerm, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $officers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        response("success", [
            "officers" => $officers,
            "total_count" => $totalCount,
            "page" => $page,
            "page_size" => $limit
        ], "Field officers fetched successfully");

    } catch (PDOException $e) {
        response("error", null, "Something went wrong");
    }
}

// fetch clients belonging to a field officer
function listOfficerClients($conn) {
    $officerId = cleanInput($_POST['officer_id'] ?? '');

    if (empty($officerId)) {
        response("error", null, "Field officer ID is required. Have you selected a field officer?");
    }

    try {
        $checkOfficer = $conn->prepare(
            "SELECT
                staff_id AS officer_id,
                first_name,
                last_name
             FROM staff
             WHERE staff_id = :staff_id
             AND type = 'field_officer'
             LIMIT 1"
        );

        $checkOfficer->execute([
            ':staff_id' => $officerId
        ]);

        $officer = $checkOfficer->fetch(PDO::FETCH_ASSOC);

        if (!$officer) {
            response("error", null, "Field officer not found");
        }

        $stmt = $conn->prepare(
            "SELECT
                c.client_id,
                c.first_name,
                c.last_name,
                c.phone_primary,
                c.phone_secondary,
                c.location,
                c.created_at,
                c.staff_id AS officer_id,
                c.active,
                l.loan_id,
                l.principal_amount,
                l.interest_rate,
                l.total_repayable,
                l.date_taken,
                l.loan_status,
                g.first_name AS g_first_name,
                g.last_name AS g_last_name,
                g.phone_number AS g_phone,
                COALESCE(SUM(p.amount_paid), 0) AS amount_paid,
                GREATEST(l.total_repayable - COALESCE(SUM(p.amount_paid), 0), 0) AS balance
            FROM client c
            INNER JOIN loan l ON l.loan_id = (
                SELECT l2.loan_id
                FROM loan l2
                WHERE l2.client_id = c.client_id
                ORDER BY l2.date_taken DESC, l2.loan_id DESC
                LIMIT 1
            )
            LEFT JOIN guarantor g ON g.guarantor_id = l.guarantor_id
            LEFT JOIN payment p ON p.loan_id = l.loan_id
            WHERE c.staff_id = :staff_id
            AND c.active = 1
            GROUP BY
                c.client_id,
                c.first_name,
                c.last_name,
                c.phone_primary,
                c.phone_secondary,
                c.location,
                c.staff_id,
                c.created_at,
                c.active,
                l.loan_id,
                l.principal_amount,
                l.interest_rate,
                l.total_repayable,
                l.date_taken,
                l.loan_status,
                g.first_name,
                g.last_name,
                g.phone_number
            ORDER BY c.created_at DESC"
        );

        $stmt->execute([
            ':staff_id' => $officerId
        ]);

        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $activeClients = [];
        $clearedClients = [];

        foreach ($clients as $client) {
            if ($client['loan_status'] === 'active') {
                $activeClients[] = $client;
            } elseif ($client['loan_status'] === 'cleared') {
                $clearedClients[] = $client;
            }
        }

        response("success", [
            "officer" => $officer,
            "active_clients" => $activeClients,
            "cleared_clients" => $clearedClients,
            "active_count" => count($activeClients),
            "cleared_count" => count($clearedClients)
        ], "Clients fetched successfully");

    } catch (PDOException $e) {
        response("error", null, "Something went wrong");
    }
}