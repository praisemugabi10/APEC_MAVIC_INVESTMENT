<?php
require_once 'config/db.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Check pending 2FA session
if (
    !isset($_SESSION['2fa_pending']) ||
    $_SESSION['2fa_pending'] !== true
) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Authentication step required."
    ]);
    exit;
}
// Get JSON input
$data = json_decode(file_get_contents("php://input"));
// Check OTP provided
if (empty($data->otp)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Verification code is required."
    ]);
    exit;
}

$otp = trim($data->otp);
try {
    $userId = $_SESSION['2fa_user_id'];
    // Get latest OTP record
    $stmt = $conn->prepare("
        SELECT
            verification_id,
            otp_hash,
            expires_at,
            verified
        FROM email_verifications
        WHERE user_id = :user_id
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->bindParam(":user_id", $userId, PDO::PARAM_STR);
    $stmt->execute();
    $verification = $stmt->fetch();

    if (!$verification) {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Verification code not found."
        ]);
        exit;
    }

    // Check if already used
    if ($verification['verified']) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Verification code has already been used."
        ]);
        exit;
    }

    // Check expiry
    if (strtotime($verification['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Verification code has expired."
        ]);
        exit;
    }

    // Verify OTP hash
    if (!password_verify($otp, $verification['otp_hash'])) {
        http_response_code(401);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid verification code."
        ]);
        exit;
    }

    // Mark OTP as verified
    $update = $conn->prepare("
        UPDATE email_verifications
        SET verified = TRUE
        WHERE verification_id = :verification_id
    ");


    $update->bindParam(
        ":verification_id",
        $verification['verification_id'],
        PDO::PARAM_STR
    );

    $update->execute();

    // Regenerate session after successful authentication
    session_regenerate_id(true);

    // Create final login session
    $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
    $_SESSION['role'] = $_SESSION['2fa_role'];
    $_SESSION['full_name'] = $_SESSION['2fa_name'];
    $_SESSION['logged_in'] = true;

    // Remove temporary 2FA data
    unset($_SESSION['2fa_pending']);
    unset($_SESSION['2fa_user_id']);
    unset($_SESSION['2fa_email']);
    unset($_SESSION['2fa_name']);
    unset($_SESSION['2fa_role']);

    echo json_encode([
        "status" => "success",
        "message" => "Login successful.",
        "user" => [
            "id" => $_SESSION['user_id'],
            "name" => $_SESSION['full_name'],
            "role" => $_SESSION['role']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Verification failed."
    ]);

}