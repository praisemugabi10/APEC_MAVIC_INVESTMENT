<?php

require_once 'config/db.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    return trim(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'));
}

function verifyManager($conn) {

    if (empty($_SESSION['user_id'])) {
        response("error", null, "User session not found");
    }

    $userId = cleanInput($_SESSION['user_id']);

    $stmt = $conn->prepare(
        "SELECT
            u.user_id,
            u.staff_id,
            u.role,
            s.active,
            s.type
        FROM user u
        INNER JOIN staff s
            ON s.staff_id = u.staff_id
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

function generateUserId() {
    return 'USR_' . bin2hex(random_bytes(16));
}

function validatePassword($password) {

    if (strlen($password) < 8) {
        return false;
    }

    if (strlen($password) > 72) {
        return false;
    }

    return true;
}


/*
|--------------------------------------------------------------------------
| Authenticate and authorize the requesting manager
|--------------------------------------------------------------------------
*/

$manager = verifyManager($conn);

$action = cleanInput($_POST['action'] ?? '');


switch ($action) {

    case "create":
        createUser($conn);
        break;

    case "list":
        listUsers($conn);
        break;

    case "deactivate":
        deactivateUser($conn);
        break;

    case "reactivate":
        reactivateUser($conn);
        break;

    case "reset_password":
        resetUserPassword($conn);
        break;

    case "available_staff":
        listAvailableStaff($conn);
        break;

    default:
        response("error", null, "Invalid action");
}


/*
|--------------------------------------------------------------------------
| CREATE USER
|--------------------------------------------------------------------------
|
| Creates a login account for an existing staff member.
|
| Allowed staff types:
|   - field_officer
|   - cashier
|
| The role is determined from the staff type rather than allowing the
| manager to submit an arbitrary role.
|
*/

function createUser($conn) {

    $staffId = cleanInput($_POST['staff_id'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (empty($staffId)) {
        response("error", null, "Staff ID is required");
    }

    if (!validatePassword($password)) {
        response(
            "error",
            null,
            "Password must be between 8 and 72 characters"
        );
    }

    try {

        $conn->beginTransaction();

        /*
         * Lock the staff record while creating the account.
         * This prevents two simultaneous requests from creating accounts
         * for the same staff member.
         */
        $staffStmt = $conn->prepare(
            "SELECT
                s.staff_id,
                s.first_name,
                s.last_name,
                s.email,
                s.type,
                s.active,
                u.user_id,
                u.role AS existing_role
            FROM staff s
            LEFT JOIN user u
                ON u.staff_id = s.staff_id
            WHERE s.staff_id = :staff_id
            LIMIT 1
            FOR UPDATE"
        );

        $staffStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);

        $staffStmt->execute();

        $staff = $staffStmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff) {
            throw new RuntimeException("Staff member not found");
        }

        /*
         * Only active staff members can receive system accounts.
         */
        if ((int) $staff['active'] !== 1) {
            throw new RuntimeException(
                "Staff member is inactive and cannot be assigned a system account"
            );
        }

        /*
         * Only field officers and cashiers can currently be created
         * as system users by the manager.
         */
        if (!in_array(
            $staff['type'],
            ['field_officer', 'cashier'],
            true
        )) {
            throw new RuntimeException(
                "Only field officers and cashiers can be created as users"
            );
        }

        /*
         * Because user.staff_id is UNIQUE, a staff member can only
         * have one system account.
         */
        if (!empty($staff['user_id'])) {
            throw new RuntimeException(
                "This staff member already has a system account"
            );
        }

        /*
         * The role is derived from the staff type.
         * DO NOT accept a role from POST.
         */
        $role = $staff['type'];

        /*
         * Hash the password using PHP's password hashing API with the Argon2id algorithm.
         */
        $passwordHash = password_hash(
            $password,
            PASSWORD_ARGON2ID
        );

        if ($passwordHash === false) {
            throw new RuntimeException(
                "Failed to secure the password"
            );
        }
        $userId = generateUserId();

        $userStmt = $conn->prepare(
            "INSERT INTO user (
                user_id,
                staff_id,
                role,
                password_hash
            )
            VALUES (
                :user_id,
                :staff_id,
                :role,
                :password_hash
            )"
        );

        $userStmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $userStmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
        $userStmt->bindValue(':role', $role, PDO::PARAM_STR);
        $userStmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        
        $userStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "user_id" => $userId,
                "staff_id" => $staffId,
                "first_name" => $staff['first_name'],
                "last_name" => $staff['last_name'],
                "email" => $staff['email'],
                "role" => $role
            ],
            "System user created successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response(
            "error",
            null,
            "Something went wrong while creating the system user"
        );

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        /*
         * Do not expose database errors to the client.
         */
        error_log(
            "Failed to create system user: " . $e->getMessage()
        );

        response(
            "error",
            null,
            "Failed to create system user"
        );
    }
}


/*
|--------------------------------------------------------------------------
| LIST USERS
|--------------------------------------------------------------------------
*/

function listUsers($conn) {

    try {

        $stmt = $conn->prepare(
            "SELECT
                u.user_id,
                u.staff_id,
                u.role,
                u.created_at,
                s.first_name,
                s.last_name,
                s.email,
                s.phone_number,
                s.type AS staff_type,
                s.active AS staff_active
            FROM user u
            INNER JOIN staff s
                ON s.staff_id = u.staff_id
            WHERE u.role IN ('field_officer', 'cashier')
            ORDER BY
                s.first_name ASC,
                s.last_name ASC"
        );

        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        response(
            "success",
            $users,
            "System users fetched successfully"
        );

    } catch (PDOException $e) {

        error_log(
            "Failed to list system users. "
        );

        response(
            "error",
            null,
            "Failed to load system users"
        );
    }
}


/*
|--------------------------------------------------------------------------
| DEACTIVATE USER
|--------------------------------------------------------------------------
|
| Do NOT delete the user record.
|
| The account becomes unusable because its associated staff member
| becomes inactive.
|
| This endpoint also verifies that the target is not the manager.
|
*/

function deactivateUser($conn) {

    $userId = cleanInput($_POST['user_id'] ?? '');

    if (empty($userId)) {
        response("error", null, "User ID is required");
    }

    try {

        $conn->beginTransaction();

        $stmt = $conn->prepare(
            "SELECT
                u.user_id,
                u.staff_id,
                u.role,
                s.active,
                s.type
            FROM user u
            INNER JOIN staff s
                ON s.staff_id = u.staff_id
            WHERE u.user_id = :user_id
            LIMIT 1
            FOR UPDATE"
        );

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new RuntimeException(
                "System user not found"
            );
        }

        if (!in_array(
            $user['role'],
            ['field_officer', 'cashier'],
            true
        )) {
            throw new RuntimeException(
                "This user cannot be managed here"
            );
        }

        if ((int) $user['active'] === 0) {
            throw new RuntimeException(
                "User is already inactive"
            );
        }

        /*
         * Deactivate the staff member.
         *
         * Because login authorization should also verify staff.active,
         * this prevents the account from being used.
         */
        $updateStmt = $conn->prepare(
            "UPDATE staff
             SET active = 0
             WHERE staff_id = :staff_id"
        );

        $updateStmt->bindValue(':staff_id', $user['staff_id'], PDO::PARAM_STR);

        $updateStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "user_id" => $userId,
                "staff_id" => $user['staff_id'],
                "active" => 0
            ],
            "User deactivated successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response(
            "error",
            null,
            "Something went wrong."
        );

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log(
            "Failed to deactivate system user: " . $e->getMessage()
        );

        response(
            "error",
            null,
            "Failed to deactivate system user. Something went wrong."
        );
    }
}


/*
|--------------------------------------------------------------------------
| REACTIVATE USER
|--------------------------------------------------------------------------
*/

function reactivateUser($conn) {

    $userId = cleanInput($_POST['user_id'] ?? '');

    if (empty($userId)) {
        response("error", null, "User ID is required");
    }

    try {

        $conn->beginTransaction();

        $stmt = $conn->prepare(
            "SELECT
                u.user_id,
                u.staff_id,
                u.role,
                s.active,
                s.type
            FROM user u
            INNER JOIN staff s
                ON s.staff_id = u.staff_id
            WHERE u.user_id = :user_id
            LIMIT 1
            FOR UPDATE"
        );

        $stmt->bindValue(
            ':user_id',
            $userId,
            PDO::PARAM_STR
        );

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new RuntimeException(
                "System user not found"
            );
        }

        if (!in_array(
            $user['role'],
            ['field_officer', 'cashier'],
            true
        )) {
            throw new RuntimeException(
                "This user cannot be managed here"
            );
        }

        if ((int) $user['active'] === 1) {
            throw new RuntimeException(
                "User is already active"
            );
        }

        $updateStmt = $conn->prepare(
            "UPDATE staff
             SET active = 1
             WHERE staff_id = :staff_id"
        );

        $updateStmt->bindValue(
            ':staff_id',
            $user['staff_id'],
            PDO::PARAM_STR
        );

        $updateStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "user_id" => $userId,
                "staff_id" => $user['staff_id'],
                "active" => 1
            ],
            "User reactivated successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response(
            "error",
            null,
            "Something went wrong."
        );

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log(
            "Failed to reactivate system user: " . $e->getMessage()
        );

        response(
            "error",
            null,
            "Failed to reactivate system user. Something went wrong."
        );
    }
}


/*
|--------------------------------------------------------------------------
| RESET USER PASSWORD
|--------------------------------------------------------------------------
*/

function resetUserPassword($conn) {

    $userId = cleanInput($_POST['user_id'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (empty($userId)) {
        response("error", null, "User ID is required");
    }

    if (!validatePassword($password)) {
        response(
            "error",
            null,
            "Password must be between 8 and 72 characters"
        );
    }

    try {

        $conn->beginTransaction();

        $stmt = $conn->prepare(
            "SELECT
                u.user_id,
                u.staff_id,
                u.role,
                s.active
            FROM user u
            INNER JOIN staff s
                ON s.staff_id = u.staff_id
            WHERE u.user_id = :user_id
            LIMIT 1
            FOR UPDATE"
        );

        $stmt->bindValue(
            ':user_id',
            $userId,
            PDO::PARAM_STR
        );

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new RuntimeException(
                "System user not found"
            );
        }

        if (!in_array(
            $user['role'],
            ['field_officer', 'cashier'],
            true
        )) {
            throw new RuntimeException(
                "This user's password cannot be managed here"
            );
        }

        $passwordHash = password_hash(
            $password,
            PASSWORD_ARGON2ID
        );

        if ($passwordHash === false) {
            throw new RuntimeException(
                "Failed to secure the password"
            );
        }

        $updateStmt = $conn->prepare(
            "UPDATE user
             SET password_hash = :password_hash
             WHERE user_id = :user_id"
        );

        $updateStmt->bindValue(
            ':password_hash',
            $passwordHash,
            PDO::PARAM_STR
        );

        $updateStmt->bindValue(
            ':user_id',
            $userId,
            PDO::PARAM_STR
        );

        $updateStmt->execute();

        $conn->commit();

        response(
            "success",
            [
                "user_id" => $userId
            ],
            "User password updated successfully"
        );

    } catch (RuntimeException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        response(
            "error",
            null,
            "Something went wrong while resetting the user password"
        );

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        error_log(
            "Failed to reset user password: " . $e->getMessage()
        );

        response(
            "error",
            null,
            "Failed to reset user password. Something went wrong."
        );
    }
}

// list available staff members for creating users
function listAvailableStaff($conn) {

    try {

        $stmt = $conn->prepare(
            "SELECT
                s.staff_id,
                s.first_name,
                s.last_name,
                s.email,
                s.phone_number,
                s.type
            FROM staff s
            LEFT JOIN user u ON u.staff_id = s.staff_id
            WHERE s.type IN ('field_officer', 'cashier')
            AND s.active = 1
            AND u.user_id IS NULL
            ORDER BY s.first_name ASC, s.last_name ASC"
        );

        $stmt->execute();

        $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

        response(
            "success",
            $staff,
            "Available staff members fetched successfully"
        );

    } catch (PDOException $e) {

        error_log("Failed to list available staff: " . $e->getMessage());

        response("error", null, "Failed to load available staff members");
    }
}