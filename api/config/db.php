<?php
// api/config/db.php

date_default_timezone_set('Africa/Kampala');

$host = 'mysql';
$db   = 'apec_mavic_db';
$user = 'apec';
$pass = 'apec@2026';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [

    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    PDO::ATTR_EMULATE_PREPARES   => false,

];

try {

    $conn = new PDO($dsn, $user, $pass, $options);
    $conn->exec("SET time_zone = '+03:00'");
} catch (\PDOException $e) {

    // In production, don't reveal $e->getMessage() to the user

    header('Content-Type: application/json');

    echo json_encode(["status" => "error", "message" => "something went wrong"]);

    exit;
}

// start the session

session_start();

// sanitize data
function sanitize_data(mixed $data): mixed
{
    if (is_array($data)) {
        return array_map('sanitize_data', $data);
    }

    if (!is_string($data)) {
        return $data;
    }

    return htmlspecialchars(
        strip_tags($data),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );
}
