<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

/*
|--------------------------------------------------------------------------
| Load environment variables
|--------------------------------------------------------------------------
*/

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

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
 * Generate a transaction-related ID.
 *
 * We are not using AUTO_INCREMENT because all IDs in this
 * database are VARCHAR(50).
 */
function generateId(): string
{
    return bin2hex(random_bytes(25));
}


/**
 * Get the authenticated user's ID.
 *
 * IMPORTANT:
 * Replace this section with the authentication mechanism
 * already used by your application.
 */
function getAuthenticatedUserId(): string
{
    /*
     * Example if your authentication middleware sets:
     *
     * $_SESSION['user_id']
     */

    if (!empty($_SESSION['user_id'])) {
        return (string) $_SESSION['user_id'];
    }

    /*
     * If your authentication middleware instead sets
     * something such as $GLOBALS['authenticated_user_id'],
     * use that here.
     */

    if (!empty($GLOBALS['authenticated_user_id'])) {
        return (string) $GLOBALS['authenticated_user_id'];
    }

    respond(
        'error',
        'Authenticated user could not be determined.',
        null,
        401
    );
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
 * Check whether a value is a valid positive monetary amount.
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

    /*
     * Format to two decimal places before inserting into
     * DECIMAL(15,2).
     */
    return number_format($amount, 2, '.', '');
}


/**
 * Fetch a category and determine what type of transaction
 * it represents.
 */
function getCategory(PDO $conn, string $categoryId): array
{
    $stmt = $conn->prepare(
        "SELECT
            transaction_category_id,
            transaction_category_name,
            transaction_category_description,
            transaction_category_is_active
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
 * Determine whether a category is one of the categories
 * that requires a client or field officer relationship.
 *
 * The category table intentionally does NOT contain cash_in/cash_out.
 * Therefore the application enforces these business rules here.
 */
function validateCategoryForTransactionType(
    string $transactionType,
    string $categoryName
): void {
    $categoryName = strtolower(trim($categoryName));

    $cashInCategories = [
        'capital',
        'client payment'
    ];

    $cashOutCategories = [
        'field officer',
        'office expense',
        'payment at the bank'
    ];

    if ($transactionType === 'cash_in') {
        if (!in_array($categoryName, $cashInCategories, true)) {
            respond(
                'error',
                'The selected category is not valid for a cash-in transaction.',
                null,
                422
            );
        }

        return;
    }

    if ($transactionType === 'cash_out') {
        if (!in_array($categoryName, $cashOutCategories, true)) {
            respond(
                'error',
                'The selected category is not valid for a cash-out transaction.',
                null,
                422
            );
        }

        return;
    }

    respond(
        'error',
        'Invalid transaction type.',
        null,
        422
    );
}


/**
 * Fetch a client and make sure it exists and is active.
 */
function validateClient(PDO $conn, string $clientId): void
{
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
 * Fetch a field officer and make sure they exist.
 */
function validateFieldOfficer(PDO $conn, string $officerId): void
{
    $stmt = $conn->prepare(
        "SELECT officer_id
         FROM `field_officer`
         WHERE officer_id = :officer_id
         LIMIT 1"
    );

    $stmt->execute([
        ':officer_id' => $officerId
    ]);

    if (!$stmt->fetch()) {
        respond(
            'error',
            'The selected field officer does not exist.',
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
| Authentication
|--------------------------------------------------------------------------
|
| If your application starts sessions through your authentication
| middleware, uncomment session_start() or include your existing
| authentication file here.
|
*/

// session_start();

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');


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
    $limit = min(
        100,
        max(1, (int) ($_GET['limit'] ?? 20))
    );

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

            u.first_name AS recorded_by_first_name,
            u.last_name AS recorded_by_last_name,

            t.transaction_trxn_date,
            t.transaction_created_at,
            t.transaction_updated_at,

            ct.client_transaction_client_id AS client_id,

            c.first_name AS client_first_name,
            c.last_name AS client_last_name,

            fot.field_officer_transaction_officer_id AS officer_id,

            fo.first_name AS officer_first_name,
            fo.last_name AS officer_last_name

        FROM `transaction` t

        INNER JOIN `transaction_category` tc
            ON tc.transaction_category_id =
               t.transaction_category_id

        INNER JOIN `user` u
            ON u.user_id = t.transaction_recorded_by

        LEFT JOIN `client_transaction` ct
            ON ct.client_transaction_transaction_id =
               t.transaction_id

        LEFT JOIN `client` c
            ON c.client_id = ct.client_transaction_client_id

        LEFT JOIN `field_officer_transaction` fot
            ON fot.field_officer_transaction_transaction_id =
               t.transaction_id

        LEFT JOIN `field_officer` fo
            ON fo.officer_id =
               fot.field_officer_transaction_officer_id
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
        $where[] =
            'fot.field_officer_transaction_officer_id = :officer_id';

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
        $where[] =
            't.transaction_trxn_date >= :date_from';

        $params[':date_from'] =
            date('Y-m-d 00:00:00', strtotime($dateFrom));
    }

    if ($dateTo !== '') {
        $where[] =
            't.transaction_trxn_date <= :date_to';

        $params[':date_to'] =
            date('Y-m-d 23:59:59', strtotime($dateTo));
    }

    /*
     * Add WHERE clause.
     */
    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= "
        ORDER BY t.transaction_trxn_date DESC,
                 t.transaction_created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->bindValue(
        ':limit',
        $limit,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        ':offset',
        $offset,
        PDO::PARAM_INT
    );

    $stmt->execute();

    $transactions = $stmt->fetchAll();

    /*
     * Count total records for pagination.
     */
    $countSql = "
        SELECT COUNT(DISTINCT t.transaction_id)

        FROM `transaction` t

        INNER JOIN `transaction_category` tc
            ON tc.transaction_category_id =
               t.transaction_category_id

        LEFT JOIN `client_transaction` ct
            ON ct.client_transaction_transaction_id =
               t.transaction_id

        LEFT JOIN `field_officer_transaction` fot
            ON fot.field_officer_transaction_transaction_id =
               t.transaction_id
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

    $totalPages = $limit > 0
        ? (int) ceil($total / $limit)
        : 0;

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
    $transactionType = trim(
        (string) ($data['transaction_type'] ?? '')
    );

    $categoryId = trim(
        (string) ($data['transaction_category_id'] ?? '')
    );

    $amount = $data['amount'] ?? null;

    $description = isset($data['description'])
        ? trim((string) $data['description'])
        : null;

    $transactionDate = trim(
        (string) ($data['transaction_date'] ?? '')
    );

    /*
     * Client and field officer IDs are optional at the
     * general request level, but become REQUIRED depending
     * on the selected category.
     */
    $clientId = trim(
        (string) ($data['client_id'] ?? '')
    );

    $officerId = trim(
        (string) ($data['field_officer_id'] ?? '')
    );


    /*
     * Validate transaction type.
     */
    if (!in_array(
        $transactionType,
        ['cash_in', 'cash_out'],
        true
    )) {
        respond(
            'error',
            'transaction_type must be either cash_in or cash_out.',
            null,
            422
        );
    }


    /*
     * Category is required.
     */
    if ($categoryId === '') {
        respond(
            'error',
            'transaction_category_id is required.',
            null,
            422
        );
    }


    /*
     * Amount.
     */
    $amount = validateAmount($amount);


    /*
     * Transaction date.
     */
    if ($transactionDate === '') {
        respond(
            'error',
            'transaction_date is required.',
            null,
            422
        );
    }

    $transactionDate = validateTransactionDate(
        $transactionDate
    );


    /*
     * Description is optional.
     */
    if ($description === '') {
        $description = null;
    }


    /*
     * Get category.
     */
    $category = getCategory(
        $conn,
        $categoryId
    );

    $categoryName = trim(
        $category['transaction_category_name']
    );


    /*
     * Ensure the category belongs to the selected
     * transaction type.
     */
    validateCategoryForTransactionType(
        $transactionType,
        $categoryName
    );


    /*
     * Normalize category name for business logic.
     */
    $normalizedCategory = strtolower(
        $categoryName
    );


    /*
     * Client Payment requires client_id.
     */
    if ($normalizedCategory === 'client payment') {

        if ($clientId === '') {
            respond(
                'error',
                'client_id is required for a Client Payment transaction.',
                null,
                422
            );
        }

        validateClient(
            $conn,
            $clientId
        );
    }


    /*
     * Field Officer requires field_officer_id.
     */
    if ($normalizedCategory === 'field officer') {

        if ($officerId === '') {
            respond(
                'error',
                'field_officer_id is required for a Field Officer transaction.',
                null,
                422
            );
        }

        validateFieldOfficer(
            $conn,
            $officerId
        );
    }


    /*
     * Prevent unnecessary relationships.
     *
     * For example, don't allow a Capital transaction to
     * also receive a client_id.
     */
    if (
        $normalizedCategory !== 'client payment' &&
        $clientId !== ''
    ) {
        respond(
            'error',
            'client_id can only be supplied for a Client Payment transaction.',
            null,
            422
        );
    }


    if (
        $normalizedCategory !== 'field officer' &&
        $officerId !== ''
    ) {
        respond(
            'error',
            'field_officer_id can only be supplied for a Field Officer transaction.',
            null,
            422
        );
    }


    /*
     * Get authenticated user.
     */
    $recordedBy = getAuthenticatedUserId();


    /*
     * Make sure the authenticated user exists.
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
        respond(
            'error',
            'The authenticated user does not exist.',
            null,
            401
        );
    }


    /*
     * Generate transaction ID.
     */
    $transactionId = generateId();


    /*
     * Begin database transaction.
     */
    try {

        $conn->beginTransaction();


        /*
         * 1. Insert main transaction.
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
         * 2. Client Payment.
         *
         * Create the relationship in client_transaction.
         */
        if ($normalizedCategory === 'client payment') {

            $clientTransactionId = generateId();

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
         * 3. Field Officer.
         *
         * Create the relationship in field_officer_transaction.
         */
        if ($normalizedCategory === 'field officer') {

            $officerTransactionId = generateId();

            $officerTransactionStmt = $conn->prepare(
                "INSERT INTO `field_officer_transaction` (
                    field_officer_transaction_id,
                    field_officer_transaction_transaction_id,
                    field_officer_transaction_officer_id
                )
                VALUES (
                    :id,
                    :transaction_id,
                    :officer_id
                )"
            );

            $officerTransactionStmt->execute([
                ':id' => $officerTransactionId,
                ':transaction_id' => $transactionId,
                ':officer_id' => $officerId
            ]);
        }


        /*
         * Everything succeeded.
         */
        $conn->commit();


        /*
         * Fetch the newly created transaction so the API
         * returns the same structure as GET.
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

                u.first_name AS recorded_by_first_name,
                u.last_name AS recorded_by_last_name,

                t.transaction_trxn_date,
                t.transaction_created_at,
                t.transaction_updated_at,

                ct.client_transaction_client_id AS client_id,

                c.first_name AS client_first_name,
                c.last_name AS client_last_name,

                fot.field_officer_transaction_officer_id AS officer_id,

                fo.first_name AS officer_first_name,
                fo.last_name AS officer_last_name

            FROM `transaction` t

            INNER JOIN `transaction_category` tc
                ON tc.transaction_category_id =
                   t.transaction_category_id

            INNER JOIN `user` u
                ON u.user_id =
                   t.transaction_recorded_by

            LEFT JOIN `client_transaction` ct
                ON ct.client_transaction_transaction_id =
                   t.transaction_id

            LEFT JOIN `client` c
                ON c.client_id =
                   ct.client_transaction_client_id

            LEFT JOIN `field_officer_transaction` fot
                ON fot.field_officer_transaction_transaction_id =
                   t.transaction_id

            LEFT JOIN `field_officer` fo
                ON fo.officer_id =
                   fot.field_officer_transaction_officer_id

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
        error_log(
            'Transaction creation failed: ' .
            $e->getMessage()
        );

        respond(
            'error',
            'Failed to create transaction.',
            null,
            500
        );
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


// version 2
