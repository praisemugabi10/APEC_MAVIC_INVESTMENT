<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {

        http_response_code(400);

        echo json_encode([
            "status" => "error",
            "message" => "Invalid request."
        ]);

        exit;
    }

    $first_name = trim($data['first_name'] ?? '');
    $last_name = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone_number = trim($data['phone_number'] ?? '');
    $password = $data['password'] ?? '';

    if (
        $first_name === '' ||
        $last_name === '' ||
        $email === '' ||
        $phone_number === '' ||
        $password === ''
    ) {

        http_response_code(400);

        echo json_encode([
            "status" => "error",
            "message" => "Please fill in all fields."
        ]);

        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        http_response_code(400);

        echo json_encode([
            "status" => "error",
            "message" => "Invalid email address."
        ]);

        exit;
    }

    if (!preg_match('/^2567\d{8}$/', $phone_number)) {

        http_response_code(400);

        echo json_encode([
            "status" => "error",
            "message" => "Phone number must be in the format 2567XXXXXXXX."
        ]);

        exit;
    }

    if (strlen($password) < 8) {

        http_response_code(400);

        echo json_encode([
            "status" => "error",
            "message" => "Password must be at least 8 characters."
        ]);

        exit;
    }

    try {

        // $checkUsers = $conn->query("
        //     SELECT user_id
        //     FROM user
        //     LIMIT 1
        // ");

        // if ($checkUsers->fetch()) {

        //     http_response_code(403);

        //     echo json_encode([
        //         "status" => "error",
        //         "message" => "System setup has already been completed."
        //     ]);

        //     exit;
        // }

        $checkStaff = $conn->prepare("
            SELECT staff_id
            FROM staff
            WHERE email = :email
               OR phone_number = :phone_number
            LIMIT 1
        ");

        $checkStaff->execute([
            ":email" => $email,
            ":phone_number" => $phone_number
        ]);

        if ($checkStaff->fetch()) {

            http_response_code(409);

            echo json_encode([
                "status" => "error",
                "message" => "Email or phone number already exists."
            ]);

            exit;
        }

        $conn->beginTransaction();

        $staff_id = "STF" . uniqid();

        $user_id = "USR" . uniqid();

        $password_hash = password_hash(
            $password,
            PASSWORD_ARGON2ID
        );

        $insertStaff = $conn->prepare("
            INSERT INTO staff
            (
                staff_id,
                first_name,
                last_name,
                email,
                phone_number,
                type
            )
            VALUES
            (
                :staff_id,
                :first_name,
                :last_name,
                :email,
                :phone_number,
                'manager'
            )
        ");

        $insertStaff->execute([
            ":staff_id" => $staff_id,
            ":first_name" => $first_name,
            ":last_name" => $last_name,
            ":email" => $email,
            ":phone_number" => $phone_number
        ]);

        $insertUser = $conn->prepare("
            INSERT INTO user
            (
                user_id,
                staff_id,
                role,
                password_hash
            )
            VALUES
            (
                :user_id,
                :staff_id,
                'manager',
                :password_hash
            )
        ");

        $insertUser->execute([
            ":user_id" => $user_id,
            ":staff_id" => $staff_id,
            ":password_hash" => $password_hash
        ]);

        $conn->commit();

        echo json_encode([
            "status" => "success",
            "message" => "Manager account created successfully."
        ]);

    } catch (PDOException $e) {

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        http_response_code(500);

        echo json_encode([
            "status" => "error",
            "message" => "Database error."
        ]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial System Setup</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f5f5;
            padding: 40px;
        }

        .container {
            max-width: 550px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
        }

        h2 {
            margin-bottom: 10px;
            text-align: center;
        }

        .description {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #0d6efd;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 6px;
            background: #0d6efd;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #0b5ed7;
        }

        button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .message {
            display: none;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
        }

        .message.success {
            display: block;
            background: #d1e7dd;
            color: #0f5132;
        }

        .message.error {
            display: block;
            background: #f8d7da;
            color: #842029;
        }

        .error-text {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2>Initial System Setup</h2>

        <p class="description">
            Create the first manager account for the system.
            This account will be responsible for creating and managing other staff members.
        </p>

        <div id="responseMessage" class="message"></div>

        <form id="userForm" autocomplete="off">

            <div class="form-group">
                <label for="first_name">First Name</label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    maxlength="100"
                    required>

                <div class="error-text" id="first_name_error"></div>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    maxlength="100"
                    required>

                <div class="error-text" id="last_name_error"></div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    maxlength="255"
                    required>

                <div class="error-text" id="email_error"></div>
            </div>

            <div class="form-group">
                <label for="phone_number">
                    Phone Number
                    <small>(2567XXXXXXXX)</small>
                </label>

                <input
                    type="text"
                    id="phone_number"
                    name="phone_number"
                    placeholder="2567XXXXXXXX"
                    maxlength="12"
                    required>

                <div class="error-text" id="phone_error"></div>
            </div>

            <div class="form-group">
                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    required>

                <div class="error-text" id="password_error"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    minlength="8"
                    required>

                <div class="error-text" id="confirm_password_error"></div>
            </div>

            <button
                type="submit"
                id="submitBtn">
                Create Manager Account
            </button>

        </form>

    </div>

    <script>
        const form = document.getElementById("userForm");

        const submitBtn = document.getElementById("submitBtn");

        const responseMessage = document.getElementById("responseMessage");

        const phoneRegex = /^2567\d{8}$/;

        function clearErrors() {

            document.querySelectorAll(".error-text").forEach(error => {

                error.style.display = "none";
                error.innerHTML = "";

            });

            responseMessage.className = "message";
            responseMessage.style.display = "none";
            responseMessage.innerHTML = "";

        }

        function showError(id, message) {

            const element = document.getElementById(id);

            element.innerHTML = message;
            element.style.display = "block";

        }

        function showResponse(type, message) {

            responseMessage.className = "message " + type;
            responseMessage.innerHTML = message;
            responseMessage.style.display = "block";

        }

        form.addEventListener("submit", async function(e) {

            e.preventDefault();

            clearErrors();

            let valid = true;

            const first_name = document.getElementById("first_name").value.trim();

            const last_name = document.getElementById("last_name").value.trim();

            const email = document.getElementById("email").value.trim();

            const phone_number = document.getElementById("phone_number").value.trim();

            const password = document.getElementById("password").value;

            const confirm_password = document.getElementById("confirm_password").value;

            if (first_name === "") {

                showError("first_name_error", "First name is required.");
                valid = false;

            }

            if (last_name === "") {

                showError("last_name_error", "Last name is required.");
                valid = false;

            }

            if (email === "") {

                showError("email_error", "Email address is required.");
                valid = false;

            } else {

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailRegex.test(email)) {

                    showError("email_error", "Invalid email address.");
                    valid = false;

                }

            }

            if (phone_number === "") {

                showError("phone_error", "Phone number is required.");
                valid = false;

            } else if (!phoneRegex.test(phone_number)) {

                showError(
                    "phone_error",
                    "Phone number must be in the format 2567XXXXXXXX."
                );

                valid = false;

            }

            if (password.length < 8) {

                showError(
                    "password_error",
                    "Password must be at least 8 characters."
                );

                valid = false;

            }

            if (password !== confirm_password) {

                showError(
                    "confirm_password_error",
                    "Passwords do not match."
                );

                valid = false;

            }

            if (!valid) {

                return;

            }

            submitBtn.disabled = true;

            submitBtn.innerHTML = "Creating Manager Account...";

            try {

                const response = await fetch(window.location.href, {

                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({

                        first_name,
                        last_name,
                        email,
                        phone_number,
                        password

                    })

                });

                const result = await response.json();

                if (result.status === "success") {

                    showResponse("success", result.message);

                    form.reset();

                } else {

                    showResponse("error", result.message);

                }

            } catch (error) {

                showResponse(
                    "error",
                    "An unexpected error occurred."
                );

            }

            submitBtn.disabled = false;

            submitBtn.innerHTML = "Create Manager Account";

        });
    </script>

</body>

</html>

