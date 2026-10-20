<?php
/*
|--------------------------------------------------------------------------
| Transaction Table Demo
|--------------------------------------------------------------------------
|
| Standalone frontend prototype.
|
| Uses mock transaction data in JavaScript.
| No database or API connection is required.
|
|--------------------------------------------------------------------------
*/
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Transactions</title>


    <!-- Bootstrap 5 -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Font Awesome -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <style>

        /*
        |--------------------------------------------------------------------------
        | Base
        |--------------------------------------------------------------------------
        */

        body {
            background: #f6f7fb;
            color: #252a34;
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }


        /*
        |--------------------------------------------------------------------------
        | Page
        |--------------------------------------------------------------------------
        */

        .transactions-page {
            padding: 30px;
        }


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .page-title {
            margin: 0;
            font-size: 25px;
            font-weight: 700;
        }

        .page-subtitle {
            margin: 5px 0 0;
            color: #7a8494;
            font-size: 14px;
        }


        /*
        |--------------------------------------------------------------------------
        | Summary cards
        |--------------------------------------------------------------------------
        */

        .summary-grid {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid #e9ecf2;
            border-radius: 12px;
            padding: 18px;
        }

        .summary-card-label {
            color: #7b8492;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .summary-card-value {
            font-size: 23px;
            font-weight: 700;
        }

        .summary-card-meta {
            margin-top: 5px;
            color: #98a1ae;
            font-size: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | Main card
        |--------------------------------------------------------------------------
        */

        .transactions-card {
            background: #ffffff;
            border: 1px solid #e7eaf0;
            border-radius: 14px;
            overflow: hidden;
        }


        /*
        |--------------------------------------------------------------------------
        | Toolbar
        |--------------------------------------------------------------------------
        */

        .transactions-toolbar {
            padding: 18px;
            border-bottom: 1px solid #edf0f4;
        }

        .toolbar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 15px;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
            max-width: 520px;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9aa3af;
        }

        .transaction-search {
            width: 100%;
            height: 42px;
            padding:
                0 42px 0 42px;

            border:
                1px solid #dfe3e9;

            border-radius: 9px;
            outline: none;
            font-size: 14px;
        }

        .transaction-search:focus {
            border-color: #8c96a5;
        }

        .clear-search {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);

            border: 0;
            background: transparent;
            color: #9aa3af;

            display: none;
        }


        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        .filters-row {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 10px;
        }

        .filter-control {
            height: 40px;
            border:
                1px solid #dfe3e9;

            border-radius: 8px;
            padding: 0 11px;

            background: #fff;
            color: #404854;

            font-size: 13px;
            outline: none;
        }

        .filter-control:focus {
            border-color: #8c96a5;
        }

        .filter-label {
            display: block;
            margin-bottom: 5px;

            font-size: 11px;
            font-weight: 600;

            color: #7d8795;
        }

        .filter-item {
            min-width: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Advanced filters
        |--------------------------------------------------------------------------
        */

        .advanced-filters {
            display: none;
            margin-top: 12px;

            padding: 14px;

            border:
                1px solid #e7eaf0;

            border-radius: 10px;
            background: #fafbfc;
        }

        .advanced-filters.show {
            display: block;
        }

        .advanced-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | Table controls
        |--------------------------------------------------------------------------
        */

        .table-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 12px 18px;

            border-bottom:
                1px solid #edf0f4;

            background: #fafbfc;
        }

        .result-count {
            color: #707a88;
            font-size: 13px;
        }

        .table-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }


        /*
        |--------------------------------------------------------------------------
        | Buttons
        |--------------------------------------------------------------------------
        */

        .btn-soft {
            border:
                1px solid #dfe3e9;

            background: #ffffff;
            color: #4c5562;

            border-radius: 8px;
            font-size: 13px;

            padding:
                8px 12px;
        }

        .btn-soft:hover {
            background: #f4f5f7;
        }


        /*
        |--------------------------------------------------------------------------
        | Table
        |--------------------------------------------------------------------------
        */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .transactions-table {
            width: 100%;
            min-width: 1150px;
            border-collapse: collapse;
        }

        .transactions-table th {
            padding: 13px 14px;

            background: #fafbfc;

            border-bottom:
                1px solid #e7eaf0;

            color: #727c89;

            font-size: 11px;
            font-weight: 700;

            text-transform: uppercase;
            letter-spacing: .03em;

            white-space: nowrap;
        }

        .transactions-table td {
            padding: 14px;

            border-bottom:
                1px solid #eef0f3;

            vertical-align: middle;

            font-size: 13px;
        }

        .transactions-table tbody tr {
            transition: background .15s ease;
        }

        .transactions-table tbody tr:hover {
            background: #fafbfc;
        }


        /*
        |--------------------------------------------------------------------------
        | Transaction ID
        |--------------------------------------------------------------------------
        */

        .transaction-id {
            font-family: monospace;
            font-size: 12px;
            color: #657080;
        }


        /*
        |--------------------------------------------------------------------------
        | Type badges
        |--------------------------------------------------------------------------
        */

        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            padding:
                5px 9px;

            border-radius: 20px;

            font-size: 11px;
            font-weight: 700;
        }

        .type-in {
            background: #eaf8ef;
            color: #218848;
        }

        .type-out {
            background: #fff0ef;
            color: #c53e35;
        }


        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        .category-name {
            font-weight: 600;
            color: #303743;
        }

        .category-id {
            display: block;

            margin-top: 3px;

            color: #9aa3af;
            font-family: monospace;
            font-size: 10px;
        }


        /*
        |--------------------------------------------------------------------------
        | Amount
        |--------------------------------------------------------------------------
        */

        .amount {
            font-weight: 700;
            white-space: nowrap;
        }

        .amount-in {
            color: #218848;
        }

        .amount-out {
            color: #c53e35;
        }


        /*
        |--------------------------------------------------------------------------
        | Person
        |--------------------------------------------------------------------------
        */

        .person-name {
            font-weight: 600;
            color: #3c4450;
        }

        .person-type {
            display: block;
            color: #9aa3af;
            font-size: 11px;
            margin-top: 2px;
        }


        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        .description {
            max-width: 240px;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;

            color: #687382;
        }


        /*
        |--------------------------------------------------------------------------
        | Date
        |--------------------------------------------------------------------------
        */

        .date-main {
            white-space: nowrap;
            font-weight: 500;
        }

        .date-secondary {
            margin-top: 3px;

            color: #9aa3af;
            font-size: 11px;
        }


        /*
        |--------------------------------------------------------------------------
        | Action button
        |--------------------------------------------------------------------------
        */

        .view-button {
            width: 34px;
            height: 34px;

            border:
                1px solid #e1e5ea;

            background: #fff;

            border-radius: 8px;

            color: #687382;
        }

        .view-button:hover {
            background: #f4f5f7;
        }


        /*
        |--------------------------------------------------------------------------
        | Empty state
        |--------------------------------------------------------------------------
        */

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state-icon {
            width: 55px;
            height: 55px;

            margin: 0 auto 15px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #f1f3f6;
            color: #8993a1;

            font-size: 20px;
        }

        .empty-state-title {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .empty-state-text {
            color: #8993a1;
            font-size: 13px;
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        .pagination-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 14px 18px;
        }

        .pagination-info {
            color: #7d8794;
            font-size: 12px;
        }

        .pagination {
            margin: 0;
        }

        .page-link {
            color: #505966;
            border-color: #e1e5ea;
            font-size: 12px;
        }

        .page-item.active .page-link {
            background: #343a40;
            border-color: #343a40;
            color: #fff;
        }


        /*
        |--------------------------------------------------------------------------
        | Column visibility dropdown
        |--------------------------------------------------------------------------
        */

        .column-menu {
            min-width: 220px;
            padding: 10px;
        }

        .column-option {
            display: flex;
            align-items: center;
            gap: 8px;

            padding: 7px 5px;

            font-size: 13px;
        }


        /*
        |--------------------------------------------------------------------------
        | Modal
        |--------------------------------------------------------------------------
        */

        .detail-section {
            margin-bottom: 22px;
        }

        .detail-section-title {
            margin-bottom: 10px;

            font-size: 12px;
            font-weight: 700;

            color: #7a8491;

            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .detail-grid {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 12px;
        }

        .detail-item {
            padding: 11px;

            background: #f8f9fb;

            border-radius: 8px;
        }

        .detail-label {
            display: block;

            margin-bottom: 4px;

            color: #8993a0;
            font-size: 11px;
        }

        .detail-value {
            color: #303743;
            font-size: 13px;
            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1100px) {

            .summary-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .filters-row,
            .advanced-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

        }


        @media (max-width: 700px) {

            .transactions-page {
                padding: 15px;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .filters-row,
            .advanced-grid {
                grid-template-columns: 1fr;
            }

            .toolbar-top {
                flex-direction: column;
                align-items: stretch;
            }

            .search-wrapper {
                max-width: none;
            }

            .table-controls {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }

            .pagination-wrapper {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }

        }

    </style>

</head>


<body>


<div class="transactions-page">


    <!--
    |--------------------------------------------------------------------------
    | Page Header
    |--------------------------------------------------------------------------
    -->

    <div class="page-header">

        <div>

            <h1 class="page-title">
                Transactions
            </h1>

            <p class="page-subtitle">
                View, search and analyse all financial transactions.
            </p>

        </div>


        <button
            type="button"
            class="btn btn-dark"
            onclick="resetFilters()"
        >
            <i class="fas fa-rotate-left me-1"></i>
            Reset Filters
        </button>

    </div>


    <!--
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    -->

    <div class="summary-grid">

        <div class="summary-card">

            <div class="summary-card-label">
                Total Transactions
            </div>

            <div
                class="summary-card-value"
                id="summary-total"
            >
                0
            </div>

            <div class="summary-card-meta">
                Matching current filters
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-card-label">
                Total Cash In
            </div>

            <div
                class="summary-card-value"
                id="summary-cash-in"
            >
                UGX 0
            </div>

            <div class="summary-card-meta">
                Money received
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-card-label">
                Total Cash Out
            </div>

            <div
                class="summary-card-value"
                id="summary-cash-out"
            >
                UGX 0
            </div>

            <div class="summary-card-meta">
                Money spent
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-card-label">
                Net Movement
            </div>

            <div
                class="summary-card-value"
                id="summary-net"
            >
                UGX 0
            </div>

            <div class="summary-card-meta">
                Cash in minus cash out
            </div>

        </div>

    </div>


    <!--
    |--------------------------------------------------------------------------
    | Transaction Card
    |--------------------------------------------------------------------------
    -->

    <div class="transactions-card">


        <!-- Toolbar -->

        <div class="transactions-toolbar">


            <div class="toolbar-top">


                <div class="search-wrapper">

                    <i class="fas fa-search"></i>

                    <input
                        type="text"
                        id="transaction-search"
                        class="transaction-search"
                        placeholder="Search transaction ID, category, person, description..."
                    >

                    <button
                        type="button"
                        id="clear-search"
                        class="clear-search"
                        onclick="clearSearch()"
                    >
                        <i class="fas fa-times"></i>
                    </button>

                </div>


                <div class="table-actions">

                    <button
                        type="button"
                        class="btn-soft"
                        onclick="toggleAdvancedFilters()"
                    >
                        <i class="fas fa-sliders me-1"></i>
                        Advanced Filters
                    </button>


                    <div class="dropdown">

                        <button
                            type="button"
                            class="btn-soft dropdown-toggle"
                            data-bs-toggle="dropdown"
                        >
                            <i class="fas fa-table-columns me-1"></i>
                            Columns
                        </button>


                        <div class="dropdown-menu dropdown-menu-end column-menu">

                            <div class="column-option">
                                <input
                                    type="checkbox"
                                    checked
                                    onchange="toggleColumn('type', this.checked)"
                                >
                                <span>Type</span>
                            </div>

                            <div class="column-option">
                                <input
                                    type="checkbox"
                                    checked
                                    onchange="toggleColumn('category', this.checked)"
                                >
                                <span>Category</span>
                            </div>

                            <div class="column-option">
                                <input
                                    type="checkbox"
                                    checked
                                    onchange="toggleColumn('amount', this.checked)"
                                >
                                <span>Amount</span>
                            </div>

                            <div class="column-option">
                                <input
                                    type="checkbox"
                                    checked
                                    onchange="toggleColumn('person', this.checked)"
                                >
                                <span>Person</span>
                            </div>

                            <div class="column-option">
                                <input
                                    type="checkbox"
                                    checked
                                    onchange="toggleColumn('description', this.checked)"
                                >
                                <span>Description</span>
                            </div>

                            <div class="column-option">
                                <input
                                    type="checkbox"
                                    checked
                                    onchange="toggleColumn('recorded_by', this.checked)"
                                >
                                <span>Recorded By</span>
                            </div>

                            <div class="column-option">
                                <input
                                    type="checkbox"
                                    checked
                                    onchange="toggleColumn('date', this.checked)"
                                >
                                <span>Date</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Basic Filters -->

            <div class="filters-row">


                <div class="filter-item">

                    <label class="filter-label">
                        Transaction Type
                    </label>

                    <select
                        id="filter-type"
                        class="filter-control w-100"
                        onchange="applyFilters()"
                    >

                        <option value="">
                            All Types
                        </option>

                        <option value="cash_in">
                            Cash In
                        </option>

                        <option value="cash_out">
                            Cash Out
                        </option>

                    </select>

                </div>


                <div class="filter-item">

                    <label class="filter-label">
                        Category
                    </label>

                    <select
                        id="filter-category"
                        class="filter-control w-100"
                        onchange="applyFilters()"
                    >

                        <option value="">
                            All Categories
                        </option>

                    </select>

                </div>


                <div class="filter-item">

                    <label class="filter-label">
                        Date From
                    </label>

                    <input
                        type="date"
                        id="filter-date-from"
                        class="filter-control w-100"
                        onchange="applyFilters()"
                    >

                </div>


                <div class="filter-item">

                    <label class="filter-label">
                        Date To
                    </label>

                    <input
                        type="date"
                        id="filter-date-to"
                        class="filter-control w-100"
                        onchange="applyFilters()"
                    >

                </div>

            </div>


            <!-- Advanced Filters -->

            <div
                id="advanced-filters"
                class="advanced-filters"
            >

                <div class="advanced-grid">


                    <div>

                        <label class="filter-label">
                            Transaction ID
                        </label>

                        <input
                            type="text"
                            id="filter-transaction-id"
                            class="filter-control w-100"
                            placeholder="TRXN..."
                            oninput="applyFilters()"
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Client ID
                        </label>

                        <input
                            type="text"
                            id="filter-client-id"
                            class="filter-control w-100"
                            placeholder="Client ID"
                            oninput="applyFilters()"
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Field Officer ID
                        </label>

                        <input
                            type="text"
                            id="filter-officer-id"
                            class="filter-control w-100"
                            placeholder="Officer ID"
                            oninput="applyFilters()"
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Recorded By
                        </label>

                        <select
                            id="filter-recorded-by"
                            class="filter-control w-100"
                            onchange="applyFilters()"
                        >

                            <option value="">
                                Anyone
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <!-- Table controls -->

        <div class="table-controls">

            <div
                class="result-count"
                id="result-count"
            >
                Showing 0 transactions
            </div>


            <div class="table-actions">

                <select
                    id="page-size"
                    class="filter-control"
                    onchange="changePageSize()"
                >

                    <option value="10">
                        10 per page
                    </option>

                    <option value="20" selected>
                        20 per page
                    </option>

                    <option value="50">
                        50 per page
                    </option>

                </select>


                <button
                    type="button"
                    class="btn-soft"
                    onclick="exportTransactions()"
                >
                    <i class="fas fa-download me-1"></i>
                    Export
                </button>

            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | Table
        |--------------------------------------------------------------------------
        -->

        <div class="table-wrapper">

            <table class="transactions-table">

                <thead>

                    <tr>

                        <th>
                            Transaction
                        </th>

                        <th data-column="type">
                            Type
                        </th>

                        <th data-column="category">
                            Category
                        </th>

                        <th data-column="amount">
                            Amount
                        </th>

                        <th data-column="person">
                            Person
                        </th>

                        <th data-column="description">
                            Description
                        </th>

                        <th data-column="recorded_by">
                            Recorded By
                        </th>

                        <th data-column="date">
                            Date
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody id="transactions-table-body">

                </tbody>

            </table>

        </div>


        <!-- Pagination -->

        <div class="pagination-wrapper">

            <div
                class="pagination-info"
                id="pagination-info"
            >
                Showing 0–0 of 0
            </div>


            <nav>

                <ul
                    class="pagination pagination-sm"
                    id="pagination"
                >
                </ul>

            </nav>

        </div>

    </div>

</div>



<!--
|--------------------------------------------------------------------------
| Transaction Details Modal
|--------------------------------------------------------------------------
-->

<div
    class="modal fade"
    id="transaction-details-modal"
    tabindex="-1"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title">
                        Transaction Details
                    </h5>

                    <small
                        id="modal-transaction-id"
                        class="text-muted"
                    >
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div
                class="modal-body"
                id="transaction-details-body"
            >
            </div>

        </div>

    </div>

</div>



<!-- Bootstrap -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<script>

/*
|--------------------------------------------------------------------------
| Mock Transaction Data
|--------------------------------------------------------------------------
|
| These objects intentionally mirror the response produced by:
|
| api/transactions/transactions.php
|
|--------------------------------------------------------------------------
*/

const transactions = [

    {
        transaction_id: "TRXN6A91A001",

        transaction_type: "cash_in",

        transaction_category_id: "TRXNCAT001",

        transaction_category_name: "Client Payment",

        transaction_amount: "850000.00",

        transaction_description:
            "Payment for completed software development work.",

        transaction_recorded_by: "USR001",

        recorded_by_first_name: "Praise",
        recorded_by_last_name: "Mugabi",

        transaction_trxn_date:
            "2026-08-28 16:45:00",

        transaction_created_at:
            "2026-08-28 16:45:12",

        transaction_updated_at:
            "2026-08-28 16:45:12",

        client_id: "CLI001",

        client_first_name: "John",
        client_last_name: "Mugisha",

        officer_id: null,

        officer_first_name: null,
        officer_last_name: null
    },


    {
        transaction_id: "TRXN6A91A002",

        transaction_type: "cash_out",

        transaction_category_id: "TRXNCAT002",

        transaction_category_name: "Field Officer",

        transaction_amount: "150000.00",

        transaction_description:
            "Money given to field officer for field operations.",

        transaction_recorded_by: "USR002",

        recorded_by_first_name: "Alex",
        recorded_by_last_name: "Tumuhereze",

        transaction_trxn_date:
            "2026-08-28 14:20:00",

        transaction_created_at:
            "2026-08-28 14:20:18",

        transaction_updated_at:
            "2026-08-28 14:20:18",

        client_id: null,

        client_first_name: null,
        client_last_name: null,

        officer_id: "OFF001",

        officer_first_name: "David",
        officer_last_name: "Kato"
    },


    {
        transaction_id: "TRXN6A91A003",

        transaction_type: "cash_out",

        transaction_category_id: "TRXNCAT003",

        transaction_category_name: "Office Expense",

        transaction_amount: "85000.00",

        transaction_description:
            "Office stationery and printing.",

        transaction_recorded_by: "USR001",

        recorded_by_first_name: "Praise",
        recorded_by_last_name: "Mugabi",

        transaction_trxn_date:
            "2026-08-27 11:15:00",

        transaction_created_at:
            "2026-08-27 11:15:30",

        transaction_updated_at:
            "2026-08-27 11:15:30",

        client_id: null,

        client_first_name: null,
        client_last_name: null,

        officer_id: null,

        officer_first_name: null,
        officer_last_name: null
    },


    {
        transaction_id: "TRXN6A91A004",

        transaction_type: "cash_in",

        transaction_category_id: "TRXNCAT004",

        transaction_category_name: "Capital From Boss",

        transaction_amount: "2500000.00",

        transaction_description:
            "Additional business capital.",

        transaction_recorded_by: "USR001",

        recorded_by_first_name: "Praise",
        recorded_by_last_name: "Mugabi",

        transaction_trxn_date:
            "2026-08-26 09:00:00",

        transaction_created_at:
            "2026-08-26 09:01:00",

        transaction_updated_at:
            "2026-08-26 09:01:00",

        client_id: null,

        client_first_name: null,
        client_last_name: null,

        officer_id: null,

        officer_first_name: null,
        officer_last_name: null
    },


    {
        transaction_id: "TRXN6A91A005",

        transaction_type: "cash_out",

        transaction_category_id: "TRXNCAT005",

        transaction_category_name: "Bank Deposit",

        transaction_amount: "1000000.00",

        transaction_description:
            "End of day banking.",

        transaction_recorded_by: "USR002",

        recorded_by_first_name: "Alex",
        recorded_by_last_name: "Tumuhereze",

        transaction_trxn_date:
            "2026-08-25 17:30:00",

        transaction_created_at:
            "2026-08-25 17:31:00",

        transaction_updated_at:
            "2026-08-25 17:31:00",

        client_id: null,

        client_first_name: null,
        client_last_name: null,

        officer_id: null,

        officer_first_name: null,
        officer_last_name: null
    },


    {
        transaction_id: "TRXN6A91A006",

        transaction_type: "cash_in",

        transaction_category_id: "TRXNCAT001",

        transaction_category_name: "Client Payment",

        transaction_amount: "450000.00",

        transaction_description:
            "Client payment received.",

        transaction_recorded_by: "USR002",

        recorded_by_first_name: "Alex",
        recorded_by_last_name: "Tumuhereze",

        transaction_trxn_date:
            "2026-08-24 15:10:00",

        transaction_created_at:
            "2026-08-24 15:10:25",

        transaction_updated_at:
            "2026-08-24 15:10:25",

        client_id: "CLI002",

        client_first_name: "Sarah",
        client_last_name: "Namukasa",

        officer_id: null,

        officer_first_name: null,
        officer_last_name: null
    },


    {
        transaction_id: "TRXN6A91A007",

        transaction_type: "cash_out",

        transaction_category_id: "TRXNCAT003",

        transaction_category_name: "Office Expense",

        transaction_amount: "120000.00",

        transaction_description:
            "Internet and office utilities.",

        transaction_recorded_by: "USR001",

        recorded_by_first_name: "Praise",
        recorded_by_last_name: "Mugabi",

        transaction_trxn_date:
            "2026-08-23 10:30:00",

        transaction_created_at:
            "2026-08-23 10:30:20",

        transaction_updated_at:
            "2026-08-23 10:30:20",

        client_id: null,

        client_first_name: null,
        client_last_name: null,

        officer_id: null,

        officer_first_name: null,
        officer_last_name: null
    }

];



/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

let filteredTransactions = [...transactions];

let currentPage = 1;

let pageSize = 20;



/*
|--------------------------------------------------------------------------
| Column visibility
|--------------------------------------------------------------------------
*/

const visibleColumns = {

    type: true,

    category: true,

    amount: true,

    person: true,

    description: true,

    recorded_by: true,

    date: true

};



/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function () {

        populateCategoryFilter();

        populateRecordedByFilter();

        setupSearch();

        applyFilters();

    }
);



/*
|--------------------------------------------------------------------------
| Populate categories
|--------------------------------------------------------------------------
*/

function populateCategoryFilter() {

    const select =
        document.getElementById(
            "filter-category"
        );


    const categories = [
        ...new Set(
            transactions.map(
                transaction =>
                    transaction.transaction_category_name
            )
        )
    ];


    categories.sort(
        (a, b) =>
            a.localeCompare(b)
    );


    categories.forEach(
        category => {

            const option =
                document.createElement(
                    "option"
                );

            option.value = category;

            option.textContent = category;

            select.appendChild(option);

        }
    );

}



/*
|--------------------------------------------------------------------------
| Populate recorded-by filter
|--------------------------------------------------------------------------
*/

function populateRecordedByFilter() {

    const select =
        document.getElementById(
            "filter-recorded-by"
        );


    const users = new Map();


    transactions.forEach(
        transaction => {

            users.set(
                transaction.transaction_recorded_by,
                `${transaction.recorded_by_first_name} ${transaction.recorded_by_last_name}`
            );

        }
    );


    users.forEach(
        (name, id) => {

            const option =
                document.createElement(
                    "option"
                );

            option.value = id;

            option.textContent =
                `${name} (${id})`;

            select.appendChild(option);

        }
    );

}



/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

function setupSearch() {

    const searchInput =
        document.getElementById(
            "transaction-search"
        );


    searchInput.addEventListener(
        "input",
        function () {

            document.getElementById(
                "clear-search"
            ).style.display =
                this.value
                    ? "block"
                    : "none";

            applyFilters();

        }
    );

}



/*
|--------------------------------------------------------------------------
| Apply all filters
|--------------------------------------------------------------------------
*/

function applyFilters() {

    const search =
        document.getElementById(
            "transaction-search"
        ).value
            .trim()
            .toLowerCase();


    const type =
        document.getElementById(
            "filter-type"
        ).value;


    const category =
        document.getElementById(
            "filter-category"
        ).value;


    const dateFrom =
        document.getElementById(
            "filter-date-from"
        ).value;


    const dateTo =
        document.getElementById(
            "filter-date-to"
        ).value;


    const transactionId =
        document.getElementById(
            "filter-transaction-id"
        ).value
            .trim()
            .toLowerCase();


    const clientId =
        document.getElementById(
            "filter-client-id"
        ).value
            .trim()
            .toLowerCase();


    const officerId =
        document.getElementById(
            "filter-officer-id"
        ).value
            .trim()
            .toLowerCase();


    const recordedBy =
        document.getElementById(
            "filter-recorded-by"
        ).value;


    filteredTransactions =
        transactions.filter(
            transaction => {


                /*
                |--------------------------------------------------------------------------
                | Global search
                |--------------------------------------------------------------------------
                */

                if (search) {

                    const searchableText = [

                        transaction.transaction_id,

                        transaction.transaction_type,

                        transaction.transaction_category_name,

                        transaction.transaction_category_id,

                        transaction.transaction_description,

                        transaction.client_id,

                        transaction.client_first_name,

                        transaction.client_last_name,

                        transaction.officer_id,

                        transaction.officer_first_name,

                        transaction.officer_last_name,

                        transaction.transaction_recorded_by,

                        transaction.recorded_by_first_name,

                        transaction.recorded_by_last_name

                    ]
                        .filter(Boolean)
                        .join(" ")
                        .toLowerCase();


                    if (
                        !searchableText.includes(search)
                    ) {
                        return false;
                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Type
                |--------------------------------------------------------------------------
                */

                if (
                    type &&
                    transaction.transaction_type !== type
                ) {
                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | Category
                |--------------------------------------------------------------------------
                */

                if (
                    category &&
                    transaction.transaction_category_name !==
                        category
                ) {
                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | Transaction ID
                |--------------------------------------------------------------------------
                */

                if (
                    transactionId &&
                    !transaction.transaction_id
                        .toLowerCase()
                        .includes(transactionId)
                ) {
                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | Client ID
                |--------------------------------------------------------------------------
                */

                if (
                    clientId &&
                    !String(
                        transaction.client_id || ""
                    )
                        .toLowerCase()
                        .includes(clientId)
                ) {
                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | Officer ID
                |--------------------------------------------------------------------------
                */

                if (
                    officerId &&
                    !String(
                        transaction.officer_id || ""
                    )
                        .toLowerCase()
                        .includes(officerId)
                ) {
                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | Recorded By
                |--------------------------------------------------------------------------
                */

                if (
                    recordedBy &&
                    transaction.transaction_recorded_by !==
                        recordedBy
                ) {
                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | Date From
                |--------------------------------------------------------------------------
                */

                const transactionDate =
                    transaction.transaction_trxn_date
                        .substring(0, 10);


                if (
                    dateFrom &&
                    transactionDate < dateFrom
                ) {
                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | Date To
                |--------------------------------------------------------------------------
                */

                if (
                    dateTo &&
                    transactionDate > dateTo
                ) {
                    return false;
                }


                return true;

            }
        );


    currentPage = 1;

    updateSummary();

    renderTable();

    renderPagination();

}



/*
|--------------------------------------------------------------------------
| Render table
|--------------------------------------------------------------------------
*/

function renderTable() {

    const tbody =
        document.getElementById(
            "transactions-table-body"
        );


    const start =
        (currentPage - 1) *
        pageSize;


    const end =
        start + pageSize;


    const pageTransactions =
        filteredTransactions.slice(
            start,
            end
        );


    tbody.innerHTML = "";


    if (
        pageTransactions.length === 0
    ) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="9"
                >

                    <div class="empty-state">

                        <div class="empty-state-icon">

                            <i class="fas fa-receipt"></i>

                        </div>

                        <div class="empty-state-title">
                            No transactions found
                        </div>

                        <div class="empty-state-text">
                            Try changing your search or filters.
                        </div>

                    </div>

                </td>

            </tr>

        `;

        updateResultInfo();

        return;

    }


    pageTransactions.forEach(
        transaction => {

            tbody.appendChild(
                createTransactionRow(
                    transaction
                )
            );

        }
    );


    updateResultInfo();

}



/*
|--------------------------------------------------------------------------
| Create table row
|--------------------------------------------------------------------------
*/

function createTransactionRow(
    transaction
) {

    const row =
        document.createElement("tr");


    const isCashIn =
        transaction.transaction_type ===
        "cash_in";


    const person =
        transaction.client_id
            ? `${transaction.client_first_name} ${transaction.client_last_name}`

            : transaction.officer_id
                ? `${transaction.officer_first_name} ${transaction.officer_last_name}`

                : "—";


    const personType =
        transaction.client_id
            ? "Client"

            : transaction.officer_id
                ? "Field Officer"

                : "";


    const recordedBy =
        `${transaction.recorded_by_first_name} ${transaction.recorded_by_last_name}`;


    const date =
        formatDate(
            transaction.transaction_trxn_date
        );


    const amount =
        formatCurrency(
            transaction.transaction_amount
        );


    row.innerHTML = `

        <td>

            <div class="transaction-id">
                ${escapeHtml(
                    transaction.transaction_id
                )}
            </div>

        </td>


        <td
            data-column="type"
            ${visibleColumns.type ? "" : "style='display:none'"}
        >

            <span
                class="
                    type-badge
                    ${isCashIn
                        ? "type-in"
                        : "type-out"}
                "
            >

                <i
                    class="
                        fas
                        ${isCashIn
                            ? "fa-arrow-down"
                            : "fa-arrow-up"}
                    "
                ></i>

                ${isCashIn
                    ? "Cash In"
                    : "Cash Out"}

            </span>

        </td>


        <td
            data-column="category"
            ${visibleColumns.category ? "" : "style='display:none'"}
        >

            <div class="category-name">

                ${escapeHtml(
                    transaction.transaction_category_name
                )}

            </div>

            <span class="category-id">

                ${escapeHtml(
                    transaction.transaction_category_id
                )}

            </span>

        </td>


        <td
            data-column="amount"
            ${visibleColumns.amount ? "" : "style='display:none'"}
        >

            <div
                class="
                    amount
                    ${isCashIn
                        ? "amount-in"
                        : "amount-out"}
                "
            >

                ${isCashIn ? "+" : "-"}
                ${amount}

            </div>

        </td>


        <td
            data-column="person"
            ${visibleColumns.person ? "" : "style='display:none'"}
        >

            <div class="person-name">

                ${escapeHtml(person)}

            </div>

            ${
                personType
                    ? `<span class="person-type">
                        ${personType}
                       </span>`
                    : ""
            }

        </td>


        <td
            data-column="description"
            ${visibleColumns.description ? "" : "style='display:none'"}
        >

            <div
                class="description"
                title="${escapeHtml(
                    transaction.transaction_description || ""
                )}"
            >

                ${escapeHtml(
                    transaction.transaction_description ||
                    "No description"
                )}

            </div>

        </td>


        <td
            data-column="recorded_by"
            ${visibleColumns.recorded_by ? "" : "style='display:none'"}
        >

            ${escapeHtml(recordedBy)}

        </td>


        <td
            data-column="date"
            ${visibleColumns.date ? "" : "style='display:none'"}
        >

            <div class="date-main">

                ${date.date}

            </div>

            <div class="date-secondary">

                ${date.time}

            </div>

        </td>


        <td>

            <button
                type="button"
                class="view-button"
                title="View transaction"
                onclick="viewTransaction('${transaction.transaction_id}')"
            >

                <i class="fas fa-eye"></i>

            </button>

        </td>

    `;


    return row;

}



/*
|--------------------------------------------------------------------------
| Update summary
|--------------------------------------------------------------------------
*/

function updateSummary() {

    const total =
        filteredTransactions.length;


    let cashIn = 0;

    let cashOut = 0;


    filteredTransactions.forEach(
        transaction => {

            const amount =
                parseFloat(
                    transaction.transaction_amount
                ) || 0;


            if (
                transaction.transaction_type ===
                "cash_in"
            ) {
                cashIn += amount;
            } else {
                cashOut += amount;
            }

        }
    );


    const net =
        cashIn - cashOut;


    document.getElementById(
        "summary-total"
    ).textContent =
        total;


    document.getElementById(
        "summary-cash-in"
    ).textContent =
        formatCurrency(cashIn);


    document.getElementById(
        "summary-cash-out"
    ).textContent =
        formatCurrency(cashOut);


    document.getElementById(
        "summary-net"
    ).textContent =
        formatCurrency(net);

}



/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

function renderPagination() {

    const pagination =
        document.getElementById(
            "pagination"
        );


    pagination.innerHTML = "";


    const totalPages =
        Math.ceil(
            filteredTransactions.length /
            pageSize
        );


    if (totalPages <= 1) {
        return;
    }


    addPaginationButton(
        pagination,
        "Previous",
        currentPage > 1,
        () => {

            currentPage--;

            renderTable();

            renderPagination();

        }
    );


    const maxVisiblePages = 5;


    let startPage =
        Math.max(
            1,
            currentPage - 2
        );


    let endPage =
        Math.min(
            totalPages,
            startPage + maxVisiblePages - 1
        );


    if (
        endPage - startPage <
        maxVisiblePages - 1
    ) {

        startPage =
            Math.max(
                1,
                endPage - maxVisiblePages + 1
            );

    }


    for (
        let page = startPage;
        page <= endPage;
        page++
    ) {

        addPaginationButton(
            pagination,
            page,
            true,
            () => {

                currentPage = page;

                renderTable();

                renderPagination();

            },
            page === currentPage
        );

    }


    addPaginationButton(
        pagination,
        "Next",
        currentPage < totalPages,
        () => {

            currentPage++;

            renderTable();

            renderPagination();

        }
    );

}



/*
|--------------------------------------------------------------------------
| Pagination button
|--------------------------------------------------------------------------
*/

function addPaginationButton(
    container,
    label,
    enabled,
    callback,
    active = false
) {

    const li =
        document.createElement("li");


    li.className =
        `page-item ${active ? "active" : ""}`;


    if (!enabled) {
        li.classList.add("disabled");
    }


    const button =
        document.createElement("button");


    button.type = "button";

    button.className =
        "page-link";


    button.textContent = label;


    button.disabled =
        !enabled;


    button.addEventListener(
        "click",
        callback
    );


    li.appendChild(button);

    container.appendChild(li);

}



/*
|--------------------------------------------------------------------------
| Result information
|--------------------------------------------------------------------------
*/

function updateResultInfo() {

    const total =
        filteredTransactions.length;


    const start =
        total === 0
            ? 0
            : ((currentPage - 1) * pageSize) + 1;


    const end =
        Math.min(
            currentPage * pageSize,
            total
        );


    document.getElementById(
        "result-count"
    ).textContent =
        `${total} transaction${total === 1 ? "" : "s"}`;


    document.getElementById(
        "pagination-info"
    ).textContent =
        `Showing ${start}–${end} of ${total}`;

}



/*
|--------------------------------------------------------------------------
| Page size
|--------------------------------------------------------------------------
*/

function changePageSize() {

    pageSize =
        parseInt(
            document.getElementById(
                "page-size"
            ).value,
            10
        );


    currentPage = 1;

    renderTable();

    renderPagination();

}



/*
|--------------------------------------------------------------------------
| Clear search
|--------------------------------------------------------------------------
*/

function clearSearch() {

    document.getElementById(
        "transaction-search"
    ).value = "";


    document.getElementById(
        "clear-search"
    ).style.display = "none";


    applyFilters();

}



/*
|--------------------------------------------------------------------------
| Reset filters
|--------------------------------------------------------------------------
*/

function resetFilters() {

    document.getElementById(
        "transaction-search"
    ).value = "";


    document.getElementById(
        "filter-type"
    ).value = "";


    document.getElementById(
        "filter-category"
    ).value = "";


    document.getElementById(
        "filter-date-from"
    ).value = "";


    document.getElementById(
        "filter-date-to"
    ).value = "";


    document.getElementById(
        "filter-transaction-id"
    ).value = "";


    document.getElementById(
        "filter-client-id"
    ).value = "";


    document.getElementById(
        "filter-officer-id"
    ).value = "";


    document.getElementById(
        "filter-recorded-by"
    ).value = "";


    document.getElementById(
        "clear-search"
    ).style.display = "none";


    applyFilters();

}



/*
|--------------------------------------------------------------------------
| Advanced filters
|--------------------------------------------------------------------------
*/

function toggleAdvancedFilters() {

    document
        .getElementById(
            "advanced-filters"
        )
        .classList.toggle("show");

}



/*
|--------------------------------------------------------------------------
| Column visibility
|--------------------------------------------------------------------------
*/

function toggleColumn(
    column,
    visible
) {

    visibleColumns[column] =
        visible;


    document
        .querySelectorAll(
            `[data-column="${column}"]`
        )
        .forEach(
            element => {

                element.style.display =
                    visible
                        ? ""
                        : "none";

            }
        );

}



/*
|--------------------------------------------------------------------------
| View transaction
|--------------------------------------------------------------------------
*/

function viewTransaction(
    transactionId
) {

    const transaction =
        transactions.find(
            item =>
                item.transaction_id ===
                transactionId
        );


    if (!transaction) {
        return;
    }


    const isCashIn =
        transaction.transaction_type ===
        "cash_in";


    const person =
        transaction.client_id
            ? `${transaction.client_first_name} ${transaction.client_last_name}`

            : transaction.officer_id
                ? `${transaction.officer_first_name} ${transaction.officer_last_name}`

                : "Not applicable";


    const personId =
        transaction.client_id ||
        transaction.officer_id ||
        "—";


    const personType =
        transaction.client_id
            ? "Client"

            : transaction.officer_id
                ? "Field Officer"

                : "—";


    const recordedBy =
        `${transaction.recorded_by_first_name} ${transaction.recorded_by_last_name}`;


    document.getElementById(
        "modal-transaction-id"
    ).textContent =
        transaction.transaction_id;


    document.getElementById(
        "transaction-details-body"
    ).innerHTML = `

        <div class="detail-section">

            <div class="detail-section-title">
                Transaction
            </div>

            <div class="detail-grid">

                <div class="detail-item">

                    <span class="detail-label">
                        Transaction Type
                    </span>

                    <span class="detail-value">

                        ${isCashIn
                            ? "Cash In"
                            : "Cash Out"}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Amount
                    </span>

                    <span class="detail-value">

                        ${isCashIn ? "+" : "-"}
                        ${formatCurrency(
                            transaction.transaction_amount
                        )}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Category
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(
                            transaction.transaction_category_name
                        )}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Category ID
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(
                            transaction.transaction_category_id
                        )}

                    </span>

                </div>

            </div>

        </div>


        <div class="detail-section">

            <div class="detail-section-title">
                Related Person
            </div>

            <div class="detail-grid">

                <div class="detail-item">

                    <span class="detail-label">
                        Person
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(person)}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Type
                    </span>

                    <span class="detail-value">

                        ${personType}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        ID
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(personId)}

                    </span>

                </div>

            </div>

        </div>


        <div class="detail-section">

            <div class="detail-section-title">
                Description
            </div>

            <div class="detail-item">

                <span class="detail-value">

                    ${escapeHtml(
                        transaction.transaction_description ||
                        "No description"
                    )}

                </span>

            </div>

        </div>


        <div class="detail-section">

            <div class="detail-section-title">
                Audit Information
            </div>

            <div class="detail-grid">

                <div class="detail-item">

                    <span class="detail-label">
                        Recorded By
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(recordedBy)}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Recorded By ID
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(
                            transaction.transaction_recorded_by
                        )}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Transaction Date
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(
                            transaction.transaction_trxn_date
                        )}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Created At
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(
                            transaction.transaction_created_at
                        )}

                    </span>

                </div>


                <div class="detail-item">

                    <span class="detail-label">
                        Updated At
                    </span>

                    <span class="detail-value">

                        ${escapeHtml(
                            transaction.transaction_updated_at
                        )}

                    </span>

                </div>

            </div>

        </div>

    `;


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            document.getElementById(
                "transaction-details-modal"
            )
        );


    modal.show();

}



/*
|--------------------------------------------------------------------------
| Format currency
|--------------------------------------------------------------------------
*/

function formatCurrency(
    amount
) {

    const number =
        parseFloat(amount) || 0;


    return new Intl.NumberFormat(
        "en-UG",
        {
            style: "currency",
            currency: "UGX",
            maximumFractionDigits: 0
        }
    ).format(number);

}



/*
|--------------------------------------------------------------------------
| Format date
|--------------------------------------------------------------------------
*/

function formatDate(
    value
) {

    const date =
        new Date(
            value.replace(" ", "T")
        );


    if (
        Number.isNaN(
            date.getTime()
        )
    ) {

        return {
            date: value,
            time: ""
        };

    }


    return {

        date: date.toLocaleDateString(
            "en-GB",
            {
                day: "2-digit",
                month: "short",
                year: "numeric"
            }
        ),

        time: date.toLocaleTimeString(
            "en-GB",
            {
                hour: "2-digit",
                minute: "2-digit"
            }
        )

    };

}



/*
|--------------------------------------------------------------------------
| Escape HTML
|--------------------------------------------------------------------------
*/

function escapeHtml(
    value
) {

    return String(
        value ?? ""
    )
        .replace(
            /&/g,
            "&amp;"
        )
        .replace(
            /</g,
            "&lt;"
        )
        .replace(
            />/g,
            "&gt;"
        )
        .replace(
            /"/g,
            "&quot;"
        )
        .replace(
            /'/g,
            "&#039;"
        );

}



/*
|--------------------------------------------------------------------------
| Export
|--------------------------------------------------------------------------
*/

function exportTransactions() {

    if (
        filteredTransactions.length === 0
    ) {

        alert(
            "There are no transactions to export."
        );

        return;

    }


    const headers = [

        "Transaction ID",

        "Transaction Type",

        "Category",

        "Amount",

        "Person",

        "Person ID",

        "Description",

        "Recorded By",

        "Recorded By ID",

        "Transaction Date",

        "Created At",

        "Updated At"

    ];


    const rows =
        filteredTransactions.map(
            transaction => {

                const person =
                    transaction.client_id
                        ? `${transaction.client_first_name} ${transaction.client_last_name}`

                        : transaction.officer_id
                            ? `${transaction.officer_first_name} ${transaction.officer_last_name}`

                            : "";


                const personId =
                    transaction.client_id ||
                    transaction.officer_id ||
                    "";


                const recordedBy =
                    `${transaction.recorded_by_first_name} ${transaction.recorded_by_last_name}`;


                return [

                    transaction.transaction_id,

                    transaction.transaction_type,

                    transaction.transaction_category_name,

                    transaction.transaction_amount,

                    person,

                    personId,

                    transaction.transaction_description || "",

                    recordedBy,

                    transaction.transaction_recorded_by,

                    transaction.transaction_trxn_date,

                    transaction.transaction_created_at,

                    transaction.transaction_updated_at

                ];

            }
        );


    const csv = [

        headers,

        ...rows

    ]
        .map(
            row =>
                row.map(
                    value =>
                        `"${String(value)
                            .replace(/"/g, '""')}"`
                ).join(",")
        )
        .join("\n");


    const blob =
        new Blob(
            [csv],
            {
                type: "text/csv;charset=utf-8;"
            }
        );


    const url =
        URL.createObjectURL(blob);


    const link =
        document.createElement("a");


    link.href = url;

    link.download =
        "transactions.csv";


    document.body.appendChild(link);

    link.click();

    document.body.removeChild(link);

    URL.revokeObjectURL(url);

}

</script>

</body>

</html>

