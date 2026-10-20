<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';


/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
*/

function respond(
    string $status,
    string $message,
    mixed $data = null,
    int $httpCode = 200
): never {
    http_response_code($httpCode);

    $response = [
        'status' => $status,
        'message' => $message
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}


/**
 * Generate an ID using a table-specific prefix.
 */
function generateId(string $prefix): string
{
    return $prefix . uniqid();
}


/**
 * Get the currently authenticated user's ID.
 *
 * The authentication middleware has already created the session.
 */
function getAuthenticatedUserId(): string
{
    if (
        empty($_SESSION['user_id']) ||
        !is_string($_SESSION['user_id'])
    ) {
        respond(
            'error',
            'Authenticated user could not be determined.',
            null,
            401
        );
    }

    return $_SESSION['user_id'];
}


/**
 * Read JSON request body.
 */
function getJsonInput(): array
{
    $rawInput = file_get_contents('php://input');

    if ($rawInput === false || trim($rawInput) === '') {
        respond(
            'error',
            'Request body is required.',
            null,
            400
        );
    }

    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        respond(
            'error',
            'Invalid JSON request body.',
            null,
            400
        );
    }

    return $data;
}


/**
 * Validate and format a monetary amount.
 */
function validateAmount(mixed $amount): string
{
    if ($amount === null || $amount === '') {
        respond(
            'error',
            'Amount is required.',
            null,
            422
        );
    }

    if (!is_numeric($amount)) {
        respond(
            'error',
            'Amount must be a valid number.',
            null,
            422
        );
    }

    $amount = (float) $amount;

    if ($amount <= 0) {
        respond(
            'error',
            'Amount must be greater than zero.',
            null,
            422
        );
    }

    return number_format($amount, 2, '.', '');
}


/**
 * Fetch and validate a transaction category.
 */
function getCategory(
    PDO $conn,
    string $categoryId
): array {
    $stmt = $conn->prepare(
        "SELECT
            transaction_category_id,
            transaction_category_name,
            transaction_category_description,
            transaction_category_type,
            transaction_category_is_active,
            transaction_category_created_at,
            transaction_category_updated_at
         FROM `transaction_category`
         WHERE transaction_category_id = :category_id
         LIMIT 1"
    );

    $stmt->execute([
        ':category_id' => $categoryId
    ]);

    $category = $stmt->fetch();

    if (!$category) {
        respond(
            'error',
            'The selected transaction category does not exist.',
            null,
            422
        );
    }

    if ((string) $category['transaction_category_is_active'] !== '1') {
        respond(
            'error',
            'The selected transaction category is inactive.',
            null,
            422
        );
    }

    return $category;
}


/**
 * Validate that the selected category belongs to
 * the selected transaction type.
 */
function validateCategoryForTransactionType(
    string $transactionType,
    string $categoryType
): void {

    /*
    |--------------------------------------------------------------------------
    | Normalize Values
    |--------------------------------------------------------------------------
    */

    $transactionType =
        strtolower(
            trim($transactionType)
        );

    $categoryType =
        strtolower(
            trim($categoryType)
        );


    /*
    |--------------------------------------------------------------------------
    | Validate Transaction Type
    |--------------------------------------------------------------------------
    */

    if (!in_array(
        $transactionType,
        ['cash_in', 'cash_out'],
        true
    )) {

        respond(
            'error',
            'Invalid transaction type.',
            null,
            422
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Category Type
    |--------------------------------------------------------------------------
    |
    | The category type comes directly from the database.
    |
    | Example:
    |
    | transaction_type = cash_out
    | category_type    = cash_out
    |
    | This is valid.
    |
    */

    if ($categoryType !== $transactionType) {

        respond(
            'error',
            'The selected category does not belong to the selected transaction type.',
            null,
            422
        );
    }
}

/**
 * Validate that a client exists and is active.
 */
function validateClient(
    PDO $conn,
    string $clientId
): void {
    $stmt = $conn->prepare(
        "SELECT client_id
         FROM `client`
         WHERE client_id = :client_id
           AND active = 1
         LIMIT 1"
    );

    $stmt->execute([
        ':client_id' => $clientId
    ]);

    if (!$stmt->fetch()) {
        respond(
            'error',
            'The selected client does not exist or is inactive.',
            null,
            422
        );
    }
}


/**
 * Validate that a field officer exists.
 */
function validateFieldOfficer(
    PDO $conn,
    string $officerId
): void {
    $stmt = $conn->prepare(
        "SELECT staff_id
         FROM `staff`
         WHERE staff_id = :staff_id
           AND type = 'field_officer'
           AND active = 1
         LIMIT 1"
    );

    $stmt->execute([
        ':staff_id' => $officerId
    ]);

    if (!$stmt->fetch()) {
        respond(
            'error',
            'The selected field officer does not exist or is inactive.',
            null,
            422
        );
    }
}


/**
 * Validate the transaction date.
 */
function validateTransactionDate(string $date): string
{
    $timestamp = strtotime($date);

    if ($timestamp === false) {
        respond(
            'error',
            'Invalid transaction date.',
            null,
            422
        );
    }

    return date('Y-m-d H:i:s', $timestamp);
}


/*
|--------------------------------------------------------------------------
| HTTP method
|--------------------------------------------------------------------------
*/

$method = strtoupper(
    $_SERVER['REQUEST_METHOD'] ?? 'GET'
);


/*
|--------------------------------------------------------------------------
| GET - Fetch transactions
|--------------------------------------------------------------------------
*/

if ($method === 'GET') {
    /*
     * Optional filters:
     *
     * ?transaction_id=...
     * ?transaction_type=cash_in
     * ?category_id=...
     * ?client_id=...
     * ?officer_id=...
     * ?recorded_by=...
     * ?date_from=2026-08-01
     * ?date_to=2026-08-31
     *
     * Pagination:
     *
     * ?page=1
     * ?limit=20
     */

    $transactionId = trim($_GET['transaction_id'] ?? '');
    $transactionType = trim($_GET['transaction_type'] ?? '');
    $categoryId = trim($_GET['category_id'] ?? '');
    $clientId = trim($_GET['client_id'] ?? '');
    $officerId = trim($_GET['officer_id'] ?? '');
    $recordedBy = trim($_GET['recorded_by'] ?? '');
    $dateFrom = trim($_GET['date_from'] ?? '');
    $dateTo = trim($_GET['date_to'] ?? '');

    $page = max(1, (int) ($_GET['page'] ?? 1));
    $limit = min(100, max(1, (int) ($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];

    /*
     * Main transaction query.
     *
     * LEFT JOIN is important because not every transaction
     * belongs to a client or field officer.
     */

    $sql = "
        SELECT
            t.transaction_id,
            t.transaction_type,
            t.transaction_category_id,
            tc.transaction_category_name,
            t.transaction_amount,
            t.transaction_description,
            t.transaction_recorded_by,
            s.first_name AS recorded_by_first_name,
            s.last_name AS recorded_by_last_name,
            t.transaction_trxn_date,
            t.transaction_created_at,
            t.transaction_updated_at,
            ct.client_transaction_client_id AS client_id,
            c.first_name AS client_first_name,
            c.last_name AS client_last_name,
            st.staff_transaction_staff_id AS officer_id,
            fo.first_name AS officer_first_name,
            fo.last_name AS officer_last_name
        FROM `transaction` t
        INNER JOIN `transaction_category` tc
            ON tc.transaction_category_id = t.transaction_category_id
        INNER JOIN `user` u
            ON u.user_id = t.transaction_recorded_by
        INNER JOIN `staff` s
            ON s.staff_id = u.staff_id
        LEFT JOIN `client_transaction` ct
            ON ct.client_transaction_transaction_id = t.transaction_id
        LEFT JOIN `client` c
            ON c.client_id = ct.client_transaction_client_id
        LEFT JOIN `staff_transaction` st
            ON st.staff_transaction_transaction_id = t.transaction_id
        LEFT JOIN `staff` fo
            ON fo.staff_id = st.staff_transaction_staff_id
    ";

    /*
     * Transaction ID filter.
     */

    if ($transactionId !== '') {
        $where[] = 't.transaction_id = :transaction_id';
        $params[':transaction_id'] = $transactionId;
    }

    /*
     * Transaction type filter.
     */

    if ($transactionType !== '') {
        if (!in_array($transactionType, ['cash_in', 'cash_out'], true)) {
            respond('error', 'Invalid transaction type.', null, 422);
        }

        $where[] = 't.transaction_type = :transaction_type';
        $params[':transaction_type'] = $transactionType;
    }

    /*
     * Category filter.
     */

    if ($categoryId !== '') {
        $where[] = 't.transaction_category_id = :category_id';
        $params[':category_id'] = $categoryId;
    }

    /*
     * Client filter.
     */

    if ($clientId !== '') {
        $where[] = 'ct.client_transaction_client_id = :client_id';
        $params[':client_id'] = $clientId;
    }

    /*
     * Field officer filter.
     */

    if ($officerId !== '') {
        $where[] = 'st.staff_transaction_staff_id = :officer_id';
        $params[':officer_id'] = $officerId;
    }

    /*
     * Recorded-by filter.
     */

    if ($recordedBy !== '') {
        $where[] = 't.transaction_recorded_by = :recorded_by';
        $params[':recorded_by'] = $recordedBy;
    }

    /*
     * Date range.
     */

    if ($dateFrom !== '') {
        $timestamp = strtotime($dateFrom);

        if ($timestamp === false) {
            respond('error', 'Invalid date_from.', null, 422);
        }

        $where[] = 't.transaction_trxn_date >= :date_from';
        $params[':date_from'] = date('Y-m-d 00:00:00', $timestamp);
    }

    if ($dateTo !== '') {
        $timestamp = strtotime($dateTo);

        if ($timestamp === false) {
            respond('error', 'Invalid date_to.', null, 422);
        }

        $where[] = 't.transaction_trxn_date <= :date_to';
        $params[':date_to'] = date('Y-m-d 23:59:59', $timestamp);
    }

    /*
     * WHERE clause.
     */

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    /*
     * Ordering and pagination.
     */

    $sql .= "
        ORDER BY t.transaction_trxn_date DESC, t.transaction_created_at DESC
        LIMIT :limit
        OFFSET :offset
    ";

    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $transactions = $stmt->fetchAll();

    /*
     * Count total records.
     */

    $countSql = "
        SELECT COUNT(DISTINCT t.transaction_id)
        FROM `transaction` t
        INNER JOIN `transaction_category` tc
            ON tc.transaction_category_id = t.transaction_category_id
        LEFT JOIN `client_transaction` ct
            ON ct.client_transaction_transaction_id = t.transaction_id
        LEFT JOIN `staff_transaction` st
            ON st.staff_transaction_transaction_id = t.transaction_id
    ";

    if (!empty($where)) {
        $countSql .= ' WHERE ' . implode(' AND ', $where);
    }

    $countStmt = $conn->prepare($countSql);

    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }

    $countStmt->execute();

    $total = (int) $countStmt->fetchColumn();

    $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 0;

    respond(
        'success',
        'Transactions fetched successfully.',
        [
            'transactions' => $transactions,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ]
    );
}


/*
|--------------------------------------------------------------------------
| POST - Create transaction
|--------------------------------------------------------------------------
*/

if ($method === 'POST') {
    $data = getJsonInput();

    /*
     * Required fields.
     */

    $transactionType = trim((string) ($data['transaction_type'] ?? ''));
    $categoryId = trim((string) ($data['transaction_category_id'] ?? ''));
    $amount = $data['amount'] ?? null;
    $description = isset($data['description']) ? trim((string) $data['description']) : null;
    $transactionDate = trim((string) ($data['transaction_date'] ?? ''));

    /*
     * Client and field officer IDs.
     */

    $clientId = trim((string) ($data['client_id'] ?? ''));
    $officerId = trim((string) ($data['field_officer_id'] ?? ''));

    /*
     * Validate transaction type.
     */

    if (!in_array($transactionType, ['cash_in', 'cash_out'], true)) {
        respond('error', 'transaction_type must be either cash_in or cash_out.', null, 422);
    }

    /*
     * Category is required.
     */

    if ($categoryId === '') {
        respond('error', 'transaction_category_id is required.', null, 422);
    }

    /*
     * Validate amount.
     */

    $amount = validateAmount($amount);

    /*
     * Validate transaction date.
     */

    if ($transactionDate === '') {
        respond('error', 'transaction_date is required.', null, 422);
    }

    $transactionDate = validateTransactionDate($transactionDate);

    /*
     * Description is optional.
     */

    if ($description === '') {
        $description = null;
    }

    /*
     * Get the selected category from the database.
     */

    $category = getCategory($conn, $categoryId);

    /*
     * Get category type and name from the database.
     */

    $categoryType = strtolower(trim($category['transaction_category_type']));
    $categoryName = strtolower(trim($category['transaction_category_name']));

    /*
     * Validate that the category belongs to the selected transaction type.
     */

    validateCategoryForTransactionType($transactionType, $categoryType);

    /*
     * Client Payment requires a client.
     */

    if ($categoryName === 'client payment') {
        if ($clientId === '') {
            respond('error', 'client_id is required for a Client Payment transaction.', null, 422);
        }

        validateClient($conn, $clientId);
    }

    /*
     * Field Officer requires a field officer.
     */

    if ($categoryName === 'field officer') {
        if ($officerId === '') {
            respond('error', 'field_officer_id is required for a Field Officer transaction.', null, 422);
        }

        validateFieldOfficer($conn, $officerId);
    }

    /*
     * Prevent unnecessary client relationship.
     */

    if ($categoryName !== 'client payment' && $clientId !== '') {
        respond('error', 'client_id can only be supplied for a Client Payment transaction.', null, 422);
    }

    /*
     * Prevent unnecessary field officer relationship.
     */

    if ($categoryName !== 'field officer' && $officerId !== '') {
        respond('error', 'field_officer_id can only be supplied for a Field Officer transaction.', null, 422);
    }

    /*
     * Get authenticated user.
     */

    $recordedBy = getAuthenticatedUserId();

    /*
     * Verify authenticated user exists.
     */

    $userStmt = $conn->prepare(
        "SELECT user_id
         FROM `user`
         WHERE user_id = :user_id
         LIMIT 1"
    );

    $userStmt->execute([
        ':user_id' => $recordedBy
    ]);

    if (!$userStmt->fetch()) {
        respond('error', 'The authenticated user does not exist.', null, 401);
    }

    /*
     * Generate transaction ID.
     */

    $transactionId = generateId('TRXN');

    /*
     * Begin database transaction.
     */

    try {
        $conn->beginTransaction();

        /*
         * ------------------------------------------------------
         * 1. Insert main transaction
         * ------------------------------------------------------
         */

        $transactionStmt = $conn->prepare(
            "INSERT INTO `transaction` (
                transaction_id,
                transaction_type,
                transaction_category_id,
                transaction_amount,
                transaction_description,
                transaction_recorded_by,
                transaction_trxn_date
            )
            VALUES (
                :transaction_id,
                :transaction_type,
                :category_id,
                :amount,
                :description,
                :recorded_by,
                :transaction_date
            )"
        );

        $transactionStmt->execute([
            ':transaction_id' => $transactionId,
            ':transaction_type' => $transactionType,
            ':category_id' => $categoryId,
            ':amount' => $amount,
            ':description' => $description,
            ':recorded_by' => $recordedBy,
            ':transaction_date' => $transactionDate
        ]);

        /*
         * ------------------------------------------------------
         * 2. Client Payment
         *
         * Insert into client_transaction.
         * ------------------------------------------------------
         */

        if ($categoryName === 'client payment') {
            $clientTransactionId = generateId('CTRXN');

            $clientTransactionStmt = $conn->prepare(
                "INSERT INTO `client_transaction` (
                    client_transaction_id,
                    client_transaction_transaction_id,
                    client_transaction_client_id
                )
                VALUES (
                    :id,
                    :transaction_id,
                    :client_id
                )"
            );

            $clientTransactionStmt->execute([
                ':id' => $clientTransactionId,
                ':transaction_id' => $transactionId,
                ':client_id' => $clientId
            ]);
        }

        /*
         * ------------------------------------------------------
         * 3. Field Officer
         *
         * Insert into staff_transaction.
         * ------------------------------------------------------
         */

        if ($categoryName === 'field officer') {
            $staffTransactionId = generateId('STRTXN');

            $staffTransactionStmt = $conn->prepare(
                "INSERT INTO `staff_transaction` (
                    staff_transaction_id,
                    staff_transaction_transaction_id,
                    staff_transaction_staff_id
                )
                VALUES (
                    :id,
                    :transaction_id,
                    :staff_id
                )"
            );

            $staffTransactionStmt->execute([
                ':id' => $staffTransactionId,
                ':transaction_id' => $transactionId,
                ':staff_id' => $officerId
            ]);
        }

        /*
         * Everything succeeded.
         */

        $conn->commit();

        /*
         * Fetch the newly created transaction.
         */

        $fetchStmt = $conn->prepare(
            "SELECT
                t.transaction_id,
                t.transaction_type,
                t.transaction_category_id,
                tc.transaction_category_name,
                t.transaction_amount,
                t.transaction_description,
                t.transaction_recorded_by,
                s.first_name AS recorded_by_first_name,
                s.last_name AS recorded_by_last_name,
                t.transaction_trxn_date,
                t.transaction_created_at,
                t.transaction_updated_at,
                ct.client_transaction_client_id AS client_id,
                c.first_name AS client_first_name,
                c.last_name AS client_last_name,
                st.staff_transaction_staff_id AS officer_id,
                fo.first_name AS officer_first_name,
                fo.last_name AS officer_last_name
            FROM `transaction` t
            INNER JOIN `transaction_category` tc
                ON tc.transaction_category_id = t.transaction_category_id
            INNER JOIN `user` u
                ON u.user_id = t.transaction_recorded_by
            INNER JOIN `staff` s
                ON s.staff_id = u.staff_id
            LEFT JOIN `client_transaction` ct
                ON ct.client_transaction_transaction_id = t.transaction_id
            LEFT JOIN `client` c
                ON c.client_id = ct.client_transaction_client_id
            LEFT JOIN `staff_transaction` st
                ON st.staff_transaction_transaction_id = t.transaction_id
            LEFT JOIN `staff` fo
                ON fo.staff_id = st.staff_transaction_staff_id
            WHERE t.transaction_id = :transaction_id
            LIMIT 1"
        );

        $fetchStmt->execute([
            ':transaction_id' => $transactionId
        ]);

        $transaction = $fetchStmt->fetch();

        respond(
            'success',
            'Transaction created successfully.',
            [
                'transaction' => $transaction
            ],
            201
        );
    } catch (Throwable $e) {
        /*
         * Roll back everything if anything failed.
         */

        if ($conn->inTransaction()) {
            $conn->rollBack();
        }

        /*
         * Log the real error on the server.
         */

        error_log('Transaction creation failed: ' . $e->getMessage());

        respond('error', 'Failed to create transaction.', null, 500);
    }
}


/*
|--------------------------------------------------------------------------
| Unsupported HTTP method
|--------------------------------------------------------------------------
*/

respond(
    'error',
    'Method not allowed.',
    null,
    405
);