<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../reusable_functions.php";

header("Content-Type: application/json");

/*
|--------------------------------------------------------------------------
| Generate Transaction Category ID
|--------------------------------------------------------------------------
*/
function generateTransactionCategoryId(): string
{
    return "TRXNCAT" . uniqid();
}

/*
|--------------------------------------------------------------------------
| Get Action
|--------------------------------------------------------------------------
*/
$action = sanitize_data(
    $_POST["action"] ?? $_GET["action"] ?? ""
);

try {

    /*
    |--------------------------------------------------------------------------
    | CREATE TRANSACTION CATEGORY
    |--------------------------------------------------------------------------
    */
    if ($action === "create_transaction_category") {

        $name = sanitize_data(
            trim($_POST["transaction_category_name"] ?? "")
        );

        $description = sanitize_data(
            trim($_POST["transaction_category_description"] ?? "")
        );

        $type = sanitize_data(
            trim($_POST["transaction_category_type"] ?? "")
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Category Name
        |--------------------------------------------------------------------------
        */
        if ($name === "") {
            response(
                "error",
                "Transaction category name is required."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Category Type
        |--------------------------------------------------------------------------
        */
        if ($type === "") {
            response(
                "error",
                "Transaction category type is required."
            );
        }

        if (!in_array($type, ["cash_in", "cash_out"], true)) {
            response(
                "error",
                "Invalid transaction category type. Use cash_in or cash_out."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Check for Duplicate Category Name
        |--------------------------------------------------------------------------
        */
        $check = $conn->prepare("
            SELECT transaction_category_id
            FROM transaction_category
            WHERE transaction_category_name = :transaction_category_name
            LIMIT 1
        ");

        $check->bindParam(
            ":transaction_category_name",
            $name,
            PDO::PARAM_STR
        );

        $check->execute();

        if ($check->fetch(PDO::FETCH_ASSOC)) {
            response(
                "error",
                "A transaction category with this name already exists."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Generate ID
        |--------------------------------------------------------------------------
        */
        $transactionCategoryId = generateTransactionCategoryId();

        /*
        |--------------------------------------------------------------------------
        | Insert Category
        |--------------------------------------------------------------------------
        */
        $stmt = $conn->prepare("
            INSERT INTO transaction_category (
                transaction_category_id,
                transaction_category_name,
                transaction_category_description,
                transaction_category_type
            )
            VALUES (
                :transaction_category_id,
                :transaction_category_name,
                :transaction_category_description,
                :transaction_category_type
            )
        ");

        $stmt->bindParam(
            ":transaction_category_id",
            $transactionCategoryId,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ":transaction_category_name",
            $name,
            PDO::PARAM_STR
        );

        $descriptionValue = $description !== "" ? $description : null;

        $stmt->bindParam(
            ":transaction_category_description",
            $descriptionValue,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ":transaction_category_type",
            $type,
            PDO::PARAM_STR
        );

        $stmt->execute();

        response(
            "success",
            "Transaction category created successfully.",
            [
                "transaction_category_id" => $transactionCategoryId
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH ALL TRANSACTION CATEGORIES
    |--------------------------------------------------------------------------
    */
    elseif ($action === "fetch_transaction_categories") {

        $stmt = $conn->prepare("
            SELECT
                transaction_category_id,
                transaction_category_name,
                transaction_category_description,
                transaction_category_type,
                transaction_category_is_active,
                transaction_category_created_at,
                transaction_category_updated_at
            FROM transaction_category
            WHERE transaction_category_is_active = '1'
            ORDER BY transaction_category_created_at DESC
        ");

        $stmt->execute();

        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        response(
            "success",
            "Transaction categories fetched successfully.",
            $categories
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FETCH SINGLE TRANSACTION CATEGORY
    |--------------------------------------------------------------------------
    */
    elseif ($action === "fetch_transaction_category") {

        $transactionCategoryId = sanitize_data(
            trim(
                $_POST["transaction_category_id"]
                ?? $_GET["transaction_category_id"]
                ?? ""
            )
        );

        if ($transactionCategoryId === "") {
            response(
                "error",
                "Transaction category ID is required."
            );
        }

        $stmt = $conn->prepare("
            SELECT
                transaction_category_id,
                transaction_category_name,
                transaction_category_description,
                transaction_category_type,
                transaction_category_is_active,
                transaction_category_created_at,
                transaction_category_updated_at
            FROM transaction_category
            WHERE transaction_category_id = :transaction_category_id
            LIMIT 1
        ");

        $stmt->bindParam(
            ":transaction_category_id",
            $transactionCategoryId,
            PDO::PARAM_STR
        );

        $stmt->execute();

        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            response(
                "error",
                "Transaction category not found."
            );
        }

        response(
            "success",
            "Transaction category fetched successfully.",
            $category
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TRANSACTION CATEGORY
    |--------------------------------------------------------------------------
    */
    elseif ($action === "update_transaction_category") {

        $transactionCategoryId = sanitize_data(
            trim($_POST["transaction_category_id"] ?? "")
        );

        $name = sanitize_data(
            trim($_POST["transaction_category_name"] ?? "")
        );

        $description = sanitize_data(
            trim($_POST["transaction_category_description"] ?? "")
        );

        $type = sanitize_data(
            trim($_POST["transaction_category_type"] ?? "")
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Category ID
        |--------------------------------------------------------------------------
        */
        if ($transactionCategoryId === "") {
            response(
                "error",
                "Transaction category ID is required."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Category Name
        |--------------------------------------------------------------------------
        */
        if ($name === "") {
            response(
                "error",
                "Transaction category name is required."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Category Type
        |--------------------------------------------------------------------------
        */
        if ($type === "") {
            response(
                "error",
                "Transaction category type is required."
            );
        }

        if (!in_array($type, ["cash_in", "cash_out"], true)) {
            response(
                "error",
                "Invalid transaction category type. Use cash_in or cash_out."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Check for Duplicate Category Name
        |--------------------------------------------------------------------------
        */
        $check = $conn->prepare("
            SELECT transaction_category_id
            FROM transaction_category
            WHERE transaction_category_name = :transaction_category_name
            AND transaction_category_id != :transaction_category_id
            LIMIT 1
        ");

        $check->bindParam(
            ":transaction_category_name",
            $name,
            PDO::PARAM_STR
        );

        $check->bindParam(
            ":transaction_category_id",
            $transactionCategoryId,
            PDO::PARAM_STR
        );

        $check->execute();

        if ($check->fetch(PDO::FETCH_ASSOC)) {
            response(
                "error",
                "A transaction category with this name already exists."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Category
        |--------------------------------------------------------------------------
        */
        $stmt = $conn->prepare("
            UPDATE transaction_category
            SET
                transaction_category_name = :transaction_category_name,
                transaction_category_description = :transaction_category_description,
                transaction_category_type = :transaction_category_type
            WHERE transaction_category_id = :transaction_category_id
        ");

        $stmt->bindParam(
            ":transaction_category_name",
            $name,
            PDO::PARAM_STR
        );

        $descriptionValue = $description !== "" ? $description : null;

        $stmt->bindParam(
            ":transaction_category_description",
            $descriptionValue,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ":transaction_category_type",
            $type,
            PDO::PARAM_STR
        );

        $stmt->bindParam(
            ":transaction_category_id",
            $transactionCategoryId,
            PDO::PARAM_STR
        );

        $stmt->execute();

        /*
        |--------------------------------------------------------------------------
        | Check if Category Exists / Changes Were Made
        |--------------------------------------------------------------------------
        */
        if ($stmt->rowCount() === 0) {

            $check = $conn->prepare("
                SELECT transaction_category_id
                FROM transaction_category
                WHERE transaction_category_id = :transaction_category_id
                LIMIT 1
            ");

            $check->bindParam(
                ":transaction_category_id",
                $transactionCategoryId,
                PDO::PARAM_STR
            );

            $check->execute();

            if (!$check->fetch(PDO::FETCH_ASSOC)) {
                response(
                    "error",
                    "Transaction category not found."
                );
            }

            response(
                "error",
                "No changes detected."
            );
        }

        response(
            "success",
            "Transaction category updated successfully."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE TRANSACTION CATEGORY
    |--------------------------------------------------------------------------
    */
    elseif ($action === "delete_transaction_category") {

        $transactionCategoryId = sanitize_data(
            trim($_POST["transaction_category_id"] ?? "")
        );

        if ($transactionCategoryId === "") {
            response(
                "error",
                "Transaction category ID is required."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Soft Delete
        |--------------------------------------------------------------------------
        */
        $stmt = $conn->prepare("
            UPDATE transaction_category
            SET
                transaction_category_is_active = '0'
            WHERE transaction_category_id = :transaction_category_id
        ");

        $stmt->bindParam(
            ":transaction_category_id",
            $transactionCategoryId,
            PDO::PARAM_STR
        );

        $stmt->execute();

        /*
        |--------------------------------------------------------------------------
        | Check if Category Exists
        |--------------------------------------------------------------------------
        */
        if ($stmt->rowCount() === 0) {

            $check = $conn->prepare("
                SELECT transaction_category_id
                FROM transaction_category
                WHERE transaction_category_id = :transaction_category_id
                LIMIT 1
            ");

            $check->bindParam(
                ":transaction_category_id",
                $transactionCategoryId,
                PDO::PARAM_STR
            );

            $check->execute();

            if (!$check->fetch(PDO::FETCH_ASSOC)) {
                response(
                    "error",
                    "Transaction category not found."
                );
            }

            response(
                "success",
                "Transaction category is already inactive."
            );
        }

        response(
            "success",
            "Transaction category deleted successfully."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | INVALID ACTION
    |--------------------------------------------------------------------------
    */
    else {

        response(
            "error",
            "Something went wrong : Invalid or missing action."
        );
    }

} catch (PDOException $e) {

    response(
        "error",
        "Something went wrong : Database error occurred."
    );
}

