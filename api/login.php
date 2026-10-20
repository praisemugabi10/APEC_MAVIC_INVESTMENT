<?php
require_once 'config/db.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Get raw POST data
$data = json_decode(file_get_contents("php://input"));

// Check for email and password
if (!empty($data->email) && !empty($data->password)) {
    
    // Sanitize and Validate Email
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
    $password = $data->password;

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid email format."
        ]);
        exit;
    }

    try {

        // Query the user table
        $stmt = $conn->prepare("
            SELECT
                u.user_id,
                u.staff_id,
                u.role,
                u.password_hash,
                s.first_name,
                s.last_name,
                s.email,
                s.active
            FROM user u
            INNER JOIN staff s
                ON u.staff_id = s.staff_id
            WHERE s.email = :email
            LIMIT 1
        ");

        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch();

        // Verify password against Argon2id hash
        if (
            $user &&
            $user['active'] == 1 &&
            password_verify($password, $user['password_hash'])
        ) {

            // Regenerate session ID before storing temporary authentication state
            session_regenerate_id(true);

            // Store temporary 2FA pending session
            $_SESSION['2fa_pending'] = true;
            $_SESSION['2fa_user_id'] = $user['user_id'];
            $_SESSION['2fa_email'] = $user['email'];
            $_SESSION['2fa_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['2fa_role'] = $user['role'];

            echo json_encode([
                "status" => "success",
                "message" => "Password verified. Verification code required.",
                "next_step" => "verify_otp"
            ]);

        } else {

            http_response_code(401);

            echo json_encode([
                "status" => "error",
                "message" => "Invalid email or password"
            ]);

        }

    } catch (Exception $e) {

        http_response_code(500);

        echo json_encode([
            "status" => "error",
            "message" => "Something went wrong during login" . $e->getMessage()
        ]);

    }

} else {

    http_response_code(400);

    echo json_encode([
        "status" => "error",
        "message" => "Please provide both email and password"
    ]);

}