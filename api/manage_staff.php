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
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

function validateName($name) {
    return preg_match("/^[a-zA-Z\s'-]{2,100}$/", $name);
}

function normalizePhone($phone) {
    if (empty($phone)) {
        return null;
    }

    $phone = preg_replace('/\s+/', '', $phone);

    if (preg_match('/^0(7|1)[0-9]{8}$/', $phone)) {
        return '256' . substr($phone, 1);
    }

    if (preg_match('/^256(7|1)[0-9]{8}$/', $phone)) {
        return $phone;
    }

    return false;
}

function verifyManager($conn) {
    $userId = cleanInput($_SESSION['user_id']);

    if (empty($userId)) {
        response("error", null, "User session not found");
    }

    $stmt = $conn->prepare(
        "SELECT
            u.user_id,
            u.staff_id,
            u.role,
            s.active
        FROM user u
        INNER JOIN staff s ON s.staff_id = u.staff_id
        WHERE u.user_id = :user_id
        AND u.role = 'manager'
        AND s.type = 'manager'
        AND s.active = 1
        LIMIT 1"
    );

    $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
    $stmt->execute();

    $manager = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$manager) {
        response("error", null, "Manager authorization required");
    }

    return $manager;
}

$action = cleanInput($_POST['action']);

$manager = verifyManager($conn);

switch ($action) {

    case "create":
        createStaff($conn);
        break;

    case "list":
        listStaff($conn);
        break;

    case "update":
        updateStaff($conn);
        break;

    case "delete":
        deleteStaff($conn);
        break;

    case "reactivate":
        reactivateStaff($conn);
        break;

    default:
        response("error", null, "Invalid action");
}

function createStaff($conn) {

    $firstName = cleanInput($_POST['first_name']);
    $lastName = cleanInput($_POST['last_name']);
    $email = strtolower(cleanInput($_POST['email']));
    $phoneNumber = normalizePhone(cleanInput($_POST['phone_number']));
    $staffType = cleanInput($_POST['type']);

    if (!validateName($firstName) || !validateName($lastName)) {
        response("error", null, "Invalid staff name");
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response("error", null, "Invalid email address");
    }

    if (!$phoneNumber) {
        response("error", null, "Invalid phone number");
    }

    if (!in_array($staffType, ['field_officer', 'cashier'], true)) {
        response("error", null, "Invalid staff type");
    }

    try {

        $conn->beginTransaction();

        $duplicateStmt = $conn->prepare(
            "SELECT
                staff_id,
                email,
                phone_number
            FROM staff
            WHERE email = :email
            OR phone_number = :phone_number
            LIMIT 1
            FOR UPDATE"
        );

        $duplicateStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $duplicateStmt->bindValue(':phone_number', $phoneNumber, PDO::PARAM_STR);
        $duplicateStmt->execute();

        $existingStaff = $duplicateStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingStaff) {
            if (strcasecmp($existingStaff['email'], $email) === 0) {
                throw new RuntimeException("A staff member with this email already exists");
            }

            if ($existingStaff['phone_number'] === $phoneNumber) {
                throw new RuntimeException("A staff member with this phone number already exists");
            }
        }

        $staffId = uniqid("STF_");

        $staffStmt = $conn->prepare(
            "INSERT INTO staff (
                staff_id,
                first_name,
                last_name,
                email,
                phone_number,
                type,
                active
            )
            VALUES (
                :staff_id,
                :first_name,
                :last_name,
                :email,
                :phone_number,
                :type,
                1
            )"
        );

        $staffStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $staffStmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
        $staffStmt->bindValue(':last_name', $lastName, PDO::PARAM_STR);
        $staffStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $staffStmt->bindValue(':phone_number', $phoneNumber, PDO::PARAM_STR);
        $staffStmt->bindValue(':type', $staffType, PDO::PARAM_STR);
        $staffStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "staff_id" => $staffId,
                "first_name" => $firstName,
                "last_name" => $lastName,
                "email" => $email,
                "phone_number" => $phoneNumber,
                "type" => $staffType,
                "active" => 1
            ],
            "Staff member created successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response("error", null, $e->getMessage());

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log("Failed to create staff: " . $e->getMessage());

        response("error", null, "Failed to create staff member");
    }
}

function listStaff($conn) {

    try {

        $stmt = $conn->prepare(
            "SELECT
                s.staff_id,
                s.first_name,
                s.last_name,
                s.email,
                s.phone_number,
                s.type,
                s.active,
                u.user_id,
                u.role AS user_role
            FROM staff s
            LEFT JOIN user u ON u.staff_id = s.staff_id
            WHERE s.type IN ('field_officer', 'cashier')
            ORDER BY s.first_name ASC, s.last_name ASC"
        );

        $stmt->execute();

        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

        response(
            "success",
            $staff,
            "Staff members fetched successfully"
        );

    } catch (PDOException $e) {

        error_log("Failed to list staff: " . $e->getMessage());

        response("error", null, "Failed to load staff members");
    }
}

function updateStaff($conn) {

    $staffId = cleanInput($_POST['staff_id']);
    $firstName = cleanInput($_POST['first_name']);
    $lastName = cleanInput($_POST['last_name']);
    $email = strtolower(cleanInput($_POST['email']));
    $phoneNumber = normalizePhone(cleanInput($_POST['phone_number']));
    // $staffType = cleanInput($_POST['type']);

    if (empty($staffId)) {
        response("error", null, "Staff ID is required");
    }

    if (!validateName($firstName) || !validateName($lastName)) {
        response("error", null, "Invalid staff name");
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        response("error", null, "Invalid email address");
    }

    if (!$phoneNumber) {
        response("error", null, "Invalid phone number");
    }

    // if (!in_array($staffType, ['field_officer', 'cashier'], true)) {
    //     response("error", null, "Invalid staff type");
    // }

    try {

        $conn->beginTransaction();

        $staffStmt = $conn->prepare(
            "SELECT
                staff_id,
                type,
                active
            FROM staff
            WHERE staff_id = :staff_id
            LIMIT 1
            FOR UPDATE"
        );

        $staffStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $staffStmt->execute();

        $staff = $staffStmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff) {
            throw new RuntimeException("Staff member not found");
        }

        if (!in_array($staff['type'], ['field_officer', 'cashier'], true)) {
            throw new RuntimeException("This staff member cannot be managed here");
        }

        $duplicateStmt = $conn->prepare(
            "SELECT
                staff_id,
                email,
                phone_number
            FROM staff
            WHERE staff_id <> :staff_id
            AND (
                email = :email
                OR phone_number = :phone_number
            )
            LIMIT 1
            FOR UPDATE"
        );

        $duplicateStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $duplicateStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $duplicateStmt->bindValue(':phone_number', $phoneNumber, PDO::PARAM_STR);
        $duplicateStmt->execute();

        $existingStaff = $duplicateStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingStaff) {
            if (strcasecmp($existingStaff['email'], $email) === 0) {
                throw new RuntimeException("Another staff member already uses this email");
            }

            if ($existingStaff['phone_number'] === $phoneNumber) {
                throw new RuntimeException("Another staff member already uses this phone number");
            }
        }

        $updateStmt = $conn->prepare(
            "UPDATE staff
            SET
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                phone_number = :phone_number
            WHERE staff_id = :staff_id"
        );

        $updateStmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
        $updateStmt->bindValue(':last_name', $lastName, PDO::PARAM_STR);
        $updateStmt->bindValue(':email', $email, PDO::PARAM_STR);
        $updateStmt->bindValue(':phone_number', $phoneNumber, PDO::PARAM_STR);
        // $updateStmt->bindValue(':type', $staffType, PDO::PARAM_STR);
        $updateStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $updateStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "staff_id" => $staffId
            ],
            "Staff member updated successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response("error", null, $e->getMessage());

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log("Failed to update staff: " . $e->getMessage());

        response("error", null, "Failed to update staff member");
    }
}

function deleteStaff($conn) {

    $staffId = cleanInput($_POST['staff_id']);

    if (empty($staffId)) {
        response("error", null, "Staff ID is required");
    }

    try {

        $conn->beginTransaction();

        $staffStmt = $conn->prepare(
            "SELECT
                staff_id,
                type,
                active
            FROM staff
            WHERE staff_id = :staff_id
            LIMIT 1
            FOR UPDATE"
        );

        $staffStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $staffStmt->execute();

        $staff = $staffStmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff) {
            throw new RuntimeException("Staff member not found");
        }

        if (!in_array($staff['type'], ['field_officer', 'cashier'], true)) {
            throw new RuntimeException("This staff member cannot be deactivated here");
        }

        if ((int) $staff['active'] === 0) {
            throw new RuntimeException("Staff member is already inactive");
        }

        $updateStmt = $conn->prepare(
            "UPDATE staff
            SET active = 0
            WHERE staff_id = :staff_id"
        );

        $updateStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $updateStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "staff_id" => $staffId,
                "active" => 0
            ],
            "Staff member deactivated successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response("error", null, $e->getMessage());

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log("Failed to deactivate staff: " . $e->getMessage());

        response("error", null, "Failed to deactivate staff member");
    }
}

function reactivateStaff($conn) {

    $staffId = cleanInput($_POST['staff_id']);

    if (empty($staffId)) {
        response("error", null, "Staff ID is required");
    }

    try {

        $conn->beginTransaction();

        $staffStmt = $conn->prepare(
            "SELECT
                staff_id,
                type,
                active
            FROM staff
            WHERE staff_id = :staff_id
            LIMIT 1
            FOR UPDATE"
        );

        $staffStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $staffStmt->execute();

        $staff = $staffStmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff) {
            throw new RuntimeException("Staff member not found");
        }

        if (!in_array($staff['type'], ['field_officer', 'cashier'], true)) {
            throw new RuntimeException("This staff member cannot be reactivated here");
        }

        if ((int) $staff['active'] === 1) {
            throw new RuntimeException("Staff member is already active");
        }

        $updateStmt = $conn->prepare(
            "UPDATE staff
            SET active = 1
            WHERE staff_id = :staff_id"
        );

        $updateStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $updateStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "staff_id" => $staffId,
                "active" => 1
            ],
            "Staff member reactivated successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response("error", null, $e->getMessage());

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log("Failed to reactivate staff: " . $e->getMessage());

        response("error", null, "Failed to reactivate staff member");
    }
}