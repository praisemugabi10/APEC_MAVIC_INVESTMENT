<?php
require_once 'config/db.php'; // db connection, session_start, sanitize_data
/*
|--------------------------------------------------------------------------
| Response Helper
|--------------------------------------------------------------------------
*/
function response(string $status, string $message, mixed $data = null): void
{
    echo json_encode(
        sanitize_data([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ])
    );

    exit;
}