<?php
require_once 'config/db.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

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

try {
    $userId = $_SESSION['2fa_user_id'];
    $email = $_SESSION['2fa_email'];
    $name = $_SESSION['2fa_name'];

    // Generate OTP
    $otp = random_int(100000, 999999);

    // Hash OTP using Argon2id
    $otpHash = password_hash(
        $otp,
        PASSWORD_ARGON2ID
    );

    // Generate verification ID
    $verificationId = "VER" . uniqid();

    // Expire after 5 minutes
    $expiresAt = date("Y-m-d H:i:s",strtotime("+5 minutes"));

    // Store OTP
    $stmt = $conn->prepare("
        INSERT INTO email_verifications
        (
            verification_id,
            user_id,
            otp_hash,
            expires_at
        )
        VALUES
        (
            :verification_id,
            :user_id,
            :otp_hash,
            :expires_at
        )
    ");

    $stmt->bindParam(":verification_id", $verificationId, PDO::PARAM_STR);
    $stmt->bindParam(":user_id", $userId, PDO::PARAM_STR);
    $stmt->bindParam(":otp_hash", $otpHash, PDO::PARAM_STR);
    $stmt->bindParam(":expires_at", $expiresAt, PDO::PARAM_STR);

    $stmt->execute();

    // Send email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USERNAME'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $_ENV['MAIL_PORT'];

    $mail->setFrom(
        $_ENV['MAIL_FROM_EMAIL'],
        $_ENV['MAIL_FROM_NAME']
    );

    $mail->addAddress(
        $email,
        $name
    );

    $mail->isHTML(true);
    $mail->Subject = "Your Verification Code";
    $mail->Body = "
        <h3>Hello {$name}</h3>
        <p>Your verification code is:</p>
        <h1>{$otp}</h1>
        <p>This code expires in 5 minutes.</p>
        <p>
            If you did not request this code,
            please ignore this email.
        </p>
    ";

    $mail->send();

    echo json_encode([
        "status" => "success",
        "message" => "Verification code sent successfully."
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        // "message" => $e->getMessage()
        "message" => "Something went wrong."
    ]);
}