<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Transactions</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fa;
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            color: #1f2937;
        }

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
            margin-bottom: 24px;
        }

        .page-title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        .page-subtitle {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Card
        |--------------------------------------------------------------------------
        */

        .transactions-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        /*
        |--------------------------------------------------------------------------
        | Toolbar
        |--------------------------------------------------------------------------
        */

        .transactions-toolbar {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .toolbar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
            min-width: 280px;
            max-width: 560px;
        }

        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            height: 44px;
            padding: 0 42px 0 42px;
            border: 1px solid #dfe3e8;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition: 0.2s ease;
        }

        .search-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.10);
        }

        .search-clear {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #9ca3af;
            display: none;
            cursor: pointer;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar-button {
            height: 42px;
            padding: 0 14px;
            border: 1px solid #dfe3e8;
            background: #ffffff;
            border-radius: 9px;
            color: #374151;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar-button:hover {
            background: #f9fafb;
        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        .filters-row {
            display: grid;
            grid-template-columns:
                minmax(150px, 1fr)
                minmax(150px, 1fr)
                minmax(150px, 1fr)
                minmax(150px, 1fr)
                auto;
            gap: 10px;
            margin-top: 14px;
        }

        .filter-control {
            height: 40px;
            width: 100%;
            border: 1px solid #dfe3e8;
            border-radius: 8px;
            padding: 0 12px;
            background: #ffffff;
            color: #374151;
            font-size: 13px;
            outline: none;
        }

        .filter-control:focus {
            border-color: #6366f1;
        }

        .filter-button {
            height: 40px;
            padding: 0 14px;
            border: 1px solid #dfe3e8;
            border-radius: 8px;
            background: #ffffff;
            color: #374151;
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
        }

        .filter-button:hover {
            background: #f9fafb;
        }

        /*
        |--------------------------------------------------------------------------
        | Advanced Filters
        |--------------------------------------------------------------------------
        */

        .advanced-filters {
            display: none;
            margin-top: 14px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f9fafb;
        }

        .advanced-filters.show {
            display: block;
        }

        .advanced-grid {
            display: grid;
            grid-template-columns:
                repeat(3, minmax(180px, 1fr));
            gap: 12px;
        }

        .filter-label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
        }

        /*
        |--------------------------------------------------------------------------
        | Table Header
        |--------------------------------------------------------------------------
        */

        .table-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .transaction-count {
            font-size: 14px;
            color: #6b7280;
        }

        .transaction-count strong {
            color: #111827;
        }

        .page-size-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6b7280;
        }

        .page-size-select {
            height: 34px;
            border: 1px solid #dfe3e8;
            border-radius: 7px;
            padding: 0 8px;
            background: #ffffff;
            font-size: 13px;
            outline: none;
        }

        /*
        |--------------------------------------------------------------------------
        | Table
        |--------------------------------------------------------------------------
        */

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .transactions-table thead th {
            padding: 13px 15px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
            text-align: left;
        }

        .transactions-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f0f1f3;
            font-size: 13px;
            color: #374151;
            vertical-align: middle;
        }

        .transactions-table tbody tr:hover {
            background: #fafbfc;
        }

        .transaction-number {
            color: #9ca3af;
            font-weight: 600;
            width: 50px;
        }

        .transaction-type {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            white-space: nowrap;
        }

        .transaction-type.cash-in {
            color: #15803d;
        }

        .transaction-type.cash-out {
            color: #dc2626;
        }

        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 7px;
            background: #f3f4f6;
            color: #374151;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .amount {
            font-weight: 700;
            white-space: nowrap;
        }

        .amount.cash-in {
            color: #15803d;
        }

        .amount.cash-out {
            color: #dc2626;
        }

        .person-name {
            font-weight: 600;
            color: #374151;
        }

        .muted {
            color: #9ca3af;
        }

        .description-cell {
            max-width: 260px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .date-cell {
            white-space: nowrap;
            color: #6b7280;
        }

        /*
        |--------------------------------------------------------------------------
        | View Button
        |--------------------------------------------------------------------------
        */

        .view-button {
            width: 34px;
            height: 34px;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            background: #ffffff;
            color: #6b7280;
            cursor: pointer;
        }

        .view-button:hover {
            background: #f3f4f6;
            color: #111827;
        }

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        .empty-state {
            padding: 55px 20px;
            text-align: center;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .empty-state h5 {
            margin: 0 0 5px;
            color: #374151;
            font-size: 15px;
        }

        .empty-state p {
            margin: 0;
            font-size: 13px;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 20px;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-info {
            font-size: 13px;
            color: #6b7280;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            margin: 0;
        }

        .pagination-button {
            min-width: 34px;
            height: 34px;
            padding: 0 9px;
            border: 1px solid #e5e7eb;
            border-radius: 7px;
            background: #ffffff;
            color: #4b5563;
            cursor: pointer;
            font-size: 13px;
        }

        .pagination-button:hover:not(:disabled) {
            background: #f3f4f6;
        }

        .pagination-button.active {
            background: #111827;
            border-color: #111827;
            color: #ffffff;
        }

        .pagination-button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .pagination-ellipsis {
            padding: 0 5px;
            color: #9ca3af;
        }

        /*
        |--------------------------------------------------------------------------
        | Column Selector
        |--------------------------------------------------------------------------
        */

        .column-menu {
            position: relative;
        }

        .column-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 7px);
            width: 230px;
            padding: 12px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
            z-index: 100;
            display: none;
        }

        .column-dropdown.show {
            display: block;
        }

        .column-dropdown-title {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
        }

        .column-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 4px;
            font-size: 13px;
            color: #4b5563;
            cursor: pointer;
        }

        .column-option input {
            cursor: pointer;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {

            .transactions-page {
                padding: 18px;
            }

            .filters-row {
                grid-template-columns:
                    repeat(2, minmax(150px, 1fr));
            }

            .advanced-grid {
                grid-template-columns:
                    repeat(2, minmax(150px, 1fr));
            }

        }

        @media (max-width: 600px) {

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .toolbar-top {
                align-items: stretch;
                flex-direction: column;
            }

            .search-wrapper {
                max-width: none;
            }

            .filters-row {
                grid-template-columns: 1fr;
            }

            .advanced-grid {
                grid-template-columns: 1fr;
            }

            .table-summary,
            .pagination-container {
                align-items: flex-start;
                flex-direction: column;
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
                View, search and filter financial transactions.
            </p>

        </div>

    </div>


    <!--
    |--------------------------------------------------------------------------
    | Transactions Card
    |--------------------------------------------------------------------------
    -->

    <div class="transactions-card">


        <!--
        |--------------------------------------------------------------------------
        | Toolbar
        |--------------------------------------------------------------------------
        -->

        <div class="transactions-toolbar">

            <div class="toolbar-top">

                <!-- Search -->

                <div class="search-wrapper">

                    <i class="fas fa-search"></i>

                    <input
                        type="text"
                        id="transaction-search"
                        class="search-input"
                        placeholder="Search transactions..."
                    >

                    <button
                        type="button"
                        id="search-clear"
                        class="search-clear"
                    >
                        <i class="fas fa-times"></i>
                    </button>

                </div>


                <!-- Toolbar Actions -->

                <div class="toolbar-actions">

                    <!-- Advanced Filters -->

                    <button
                        type="button"
                        class="toolbar-button"
                        id="advanced-filter-button"
                    >
                        <i class="fas fa-sliders"></i>
                        Filters
                    </button>


                    <!-- Columns -->

                    <div class="column-menu">

                        <button
                            type="button"
                            class="toolbar-button"
                            id="column-button"
                        >
                            <i class="fas fa-table-columns"></i>
                            Columns
                        </button>


                        <div
                            class="column-dropdown"
                            id="column-dropdown"
                        >

                            <div class="column-dropdown-title">
                                Show Columns
                            </div>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="type"
                                    checked
                                >
                                Type
                            </label>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="category"
                                    checked
                                >
                                Category
                            </label>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="amount"
                                    checked
                                >
                                Amount
                            </label>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="description"
                                    checked
                                >
                                Description
                            </label>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="person"
                                    checked
                                >
                                Client / Officer
                            </label>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="recordedBy"
                                    checked
                                >
                                Recorded By
                            </label>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="transactionDate"
                                    checked
                                >
                                Transaction Date
                            </label>

                            <label class="column-option">
                                <input
                                    type="checkbox"
                                    data-column="createdAt"
                                >
                                Created At
                            </label>

                        </div>

                    </div>

                </div>

            </div>


            <!--
            |--------------------------------------------------------------------------
            | Main Filters
            |--------------------------------------------------------------------------
            -->

            <div class="filters-row">

                <select
                    id="filter-type"
                    class="filter-control"
                >
                    <option value="">
                        All Transaction Types
                    </option>

                    <option value="cash_in">
                        Cash In
                    </option>

                    <option value="cash_out">
                        Cash Out
                    </option>

                </select>


                <select
                    id="filter-category"
                    class="filter-control"
                >

                    <option value="">
                        All Categories
                    </option>

                </select>


                <input
                    type="date"
                    id="filter-date-from"
                    class="filter-control"
                >


                <input
                    type="date"
                    id="filter-date-to"
                    class="filter-control"
                >


                <button
                    type="button"
                    id="clear-filters"
                    class="filter-button"
                >
                    <i class="fas fa-rotate-left"></i>
                    Clear
                </button>

            </div>


            <!--
            |--------------------------------------------------------------------------
            | Advanced Filters
            |--------------------------------------------------------------------------
            -->

            <div
                class="advanced-filters"
                id="advanced-filters"
            >

                <div class="advanced-grid">

                    <div>

                        <label class="filter-label">
                            Client / Officer
                        </label>

                        <input
                            type="text"
                            id="advanced-person"
                            class="filter-control"
                            placeholder="Search person..."
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Recorded By
                        </label>

                        <input
                            type="text"
                            id="advanced-recorded-by"
                            class="filter-control"
                            placeholder="Search staff member..."
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Description
                        </label>

                        <input
                            type="text"
                            id="advanced-description"
                            class="filter-control"
                            placeholder="Search description..."
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Minimum Amount
                        </label>

                        <input
                            type="number"
                            id="advanced-min-amount"
                            class="filter-control"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Maximum Amount
                        </label>

                        <input
                            type="number"
                            id="advanced-max-amount"
                            class="filter-control"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                        >

                    </div>


                    <div>

                        <label class="filter-label">
                            Sort By
                        </label>

                        <select
                            id="advanced-sort"
                            class="filter-control"
                        >

                            <option value="date_desc">
                                Newest First
                            </option>

                            <option value="date_asc">
                                Oldest First
                            </option>

                            <option value="amount_desc">
                                Highest Amount
                            </option>

                            <option value="amount_asc">
                                Lowest Amount
                            </option>

                            <option value="category">
                                Category
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | Table Summary
        |--------------------------------------------------------------------------
        -->

        <div class="table-summary">

            <div
                class="transaction-count"
                id="transaction-count"
            >
                Showing
                <strong>0</strong>
                transactions
            </div>


            <div class="page-size-wrapper">

                <span>
                    Rows per page
                </span>

                <select
                    id="page-size"
                    class="page-size-select"
                >

                    <option value="10">
                        10
                    </option>

                    <option value="20">
                        20
                    </option>

                    <option value="50">
                        50
                    </option>

                    <option value="100">
                        100
                    </option>

                </select>

            </div>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | Table
        |--------------------------------------------------------------------------
        -->

        <div class="table-container">

            <table
                class="transactions-table"
                id="transactions-table"
            >

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th data-column-header="type">
                            Type
                        </th>

                        <th data-column-header="category">
                            Category
                        </th>

                        <th data-column-header="amount">
                            Amount
                        </th>

                        <th data-column-header="description">
                            Description
                        </th>

                        <th data-column-header="person">
                            Client / Officer
                        </th>

                        <th data-column-header="recordedBy">
                            Recorded By
                        </th>

                        <th data-column-header="transactionDate">
                            Transaction Date
                        </th>

                        <!-- <th data-column-header="createdAt">
                            Created At
                        </th> -->

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody id="transactions-body">
                </tbody>

            </table>

        </div>


        <!--
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        -->

        <div class="pagination-container">

            <div
                class="pagination-info"
                id="pagination-info"
            >
                Showing 0 to 0 of 0 transactions
            </div>


            <div
                class="pagination"
                id="pagination"
            >
            </div>

        </div>

    </div>

</div>


<script>

const TransactionsTable = {

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    transactions: [],

    filteredTransactions: [],

    currentPage: 1,

    pageSize: 10,


    /*
    |--------------------------------------------------------------------------
    | DOM Elements
    |--------------------------------------------------------------------------
    */

    elements: {

        search:
            document.getElementById(
                'transaction-search'
            ),

        searchClear:
            document.getElementById(
                'search-clear'
            ),

        typeFilter:
            document.getElementById(
                'filter-type'
            ),

        categoryFilter:
            document.getElementById(
                'filter-category'
            ),

        dateFrom:
            document.getElementById(
                'filter-date-from'
            ),

        dateTo:
            document.getElementById(
                'filter-date-to'
            ),

        clearFilters:
            document.getElementById(
                'clear-filters'
            ),

        advancedFilters:
            document.getElementById(
                'advanced-filters'
            ),

        advancedFilterButton:
            document.getElementById(
                'advanced-filter-button'
            ),

        person:
            document.getElementById(
                'advanced-person'
            ),

        recordedBy:
            document.getElementById(
                'advanced-recorded-by'
            ),

        description:
            document.getElementById(
                'advanced-description'
            ),

        minAmount:
            document.getElementById(
                'advanced-min-amount'
            ),

        maxAmount:
            document.getElementById(
                'advanced-max-amount'
            ),

        sort:
            document.getElementById(
                'advanced-sort'
            ),

        pageSize:
            document.getElementById(
                'page-size'
            ),

        body:
            document.getElementById(
                'transactions-body'
            ),

        count:
            document.getElementById(
                'transaction-count'
            ),

        paginationInfo:
            document.getElementById(
                'pagination-info'
            ),

        pagination:
            document.getElementById(
                'pagination'
            ),

        columnButton:
            document.getElementById(
                'column-button'
            ),

        columnDropdown:
            document.getElementById(
                'column-dropdown'
            )

    },


    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init() {

        this.generateMockData();

        this.populateCategories();

        this.bindEvents();

        this.applyFilters();

    },


    /*
    |--------------------------------------------------------------------------
    | Mock Data
    |--------------------------------------------------------------------------
    */

    generateMockData() {

        const names = [
            'Alex Tumuhereze',
            'Sarah Namukasa',
            'David Mugisha',
            'Brian Okello',
            'Grace Atwine',
            'John Kato',
            'Mary Nansubuga',
            'Daniel Turyasingura'
        ];


        const staff = [
            'Praise Mugabi',
            'John Smith',
            'Sarah Namanya'
        ];


        const categories = [

            {
                name: 'Client Payment',
                type: 'cash_in'
            },

            {
                name: 'Capital From Boss',
                type: 'cash_in'
            },

            {
                name: 'Field Officer',
                type: 'cash_out'
            },

            {
                name: 'Office Expense',
                type: 'cash_out'
            },

            {
                name: 'Bank Deposit',
                type: 'cash_out'
            },

            {
                name: 'Loan Repayment',
                type: 'cash_out'
            }

        ];


        const descriptions = [
            'Payment received from client',
            'Capital injected into the business',
            'Money given to field officer',
            'Office supplies and operational expenses',
            'Daily deposit made at the bank',
            'Loan repayment received'
        ];


        const data = [];


        for (let i = 1; i <= 87; i++) {

            const category =
                categories[
                    Math.floor(
                        Math.random() *
                        categories.length
                    )
                ];


            const person =
                names[
                    Math.floor(
                        Math.random() *
                        names.length
                    )
                ];


            const recorder =
                staff[
                    Math.floor(
                        Math.random() *
                        staff.length
                    )
                ];


            const amount =
                Math.floor(
                    Math.random() * 900000
                ) + 10000;


            const daysAgo =
                Math.floor(
                    Math.random() * 60
                );


            const transactionDate =
                new Date();

            transactionDate.setDate(
                transactionDate.getDate() -
                daysAgo
            );


            const createdDate =
                new Date(
                    transactionDate
                );

            createdDate.setHours(
                8 +
                Math.floor(
                    Math.random() * 9
                )
            );


            data.push({

                id: `TRXN${i}`,

                transaction_type:
                    category.type,

                transaction_category_name:
                    category.name,

                transaction_amount:
                    amount,

                transaction_description:
                    descriptions[
                        categories.indexOf(category)
                    ],

                client_first_name:
                    category.name ===
                    'Client Payment'
                        ? person.split(' ')[0]
                        : null,

                client_last_name:
                    category.name ===
                    'Client Payment'
                        ? person.split(' ')[1]
                        : null,

                officer_first_name:
                    category.name ===
                    'Field Officer'
                        ? person.split(' ')[0]
                        : null,

                officer_last_name:
                    category.name ===
                    'Field Officer'
                        ? person.split(' ')[1]
                        : null,

                recorded_by_first_name:
                    recorder.split(' ')[0],

                recorded_by_last_name:
                    recorder.split(' ')[1],

                transaction_trxn_date:
                    this.formatDateTime(
                        transactionDate
                    ),

                transaction_created_at:
                    this.formatDateTime(
                        createdDate
                    )

            });

        }


        this.transactions = data;

    },


    /*
    |--------------------------------------------------------------------------
    | Populate Categories
    |--------------------------------------------------------------------------
    */

    populateCategories() {

        const categories = [
            ...new Set(
                this.transactions.map(
                    transaction =>
                        transaction
                            .transaction_category_name
                )
            )
        ];


        categories.sort();


        categories.forEach(category => {

            const option =
                document.createElement(
                    'option'
                );

            option.value = category;

            option.textContent = category;

            this.elements.categoryFilter
                .appendChild(option);

        });

    },


    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        this.elements.search
            .addEventListener(
                'input',
                () => {

                    this.elements.searchClear
                        .style.display =
                        this.elements.search.value
                            ? 'block'
                            : 'none';

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );


        this.elements.searchClear
            .addEventListener(
                'click',
                () => {

                    this.elements.search.value = '';

                    this.elements.searchClear
                        .style.display =
                        'none';

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );


        this.elements.typeFilter
            .addEventListener(
                'change',
                () => {

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );


        this.elements.categoryFilter
            .addEventListener(
                'change',
                () => {

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );


        this.elements.dateFrom
            .addEventListener(
                'change',
                () => {

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );


        this.elements.dateTo
            .addEventListener(
                'change',
                () => {

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );


        this.elements.clearFilters
            .addEventListener(
                'click',
                () => this.clearFilters()
            );


        this.elements.advancedFilterButton
            .addEventListener(
                'click',
                () => {

                    this.elements.advancedFilters
                        .classList.toggle(
                            'show'
                        );

                }
            );


        [
            this.elements.person,
            this.elements.recordedBy,
            this.elements.description,
            this.elements.minAmount,
            this.elements.maxAmount,
            this.elements.sort
        ].forEach(element => {

            element.addEventListener(
                'input',
                () => {

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );


            element.addEventListener(
                'change',
                () => {

                    this.currentPage = 1;

                    this.applyFilters();

                }
            );

        });


        this.elements.pageSize
            .addEventListener(
                'change',
                () => {

                    this.pageSize =
                        Number(
                            this.elements.pageSize
                                .value
                        );

                    this.currentPage = 1;

                    this.render();

                }
            );


        this.elements.columnButton
            .addEventListener(
                'click',
                event => {

                    event.stopPropagation();

                    this.elements.columnDropdown
                        .classList.toggle(
                            'show'
                        );

                }
            );


        this.elements.columnDropdown
            .addEventListener(
                'click',
                event => {

                    event.stopPropagation();

                }
            );


        document.addEventListener(
            'click',
            () => {

                this.elements.columnDropdown
                    .classList.remove(
                        'show'
                    );

            }
        );


        document
            .querySelectorAll(
                '.column-option input'
            )
            .forEach(input => {

                input.addEventListener(
                    'change',
                    () => {

                        this.updateColumnVisibility();

                    }
                );

            });

    },


    /*
    |--------------------------------------------------------------------------
    | Apply Filters
    |--------------------------------------------------------------------------
    */

    applyFilters() {

        const search =
            this.elements.search.value
                .trim()
                .toLowerCase();


        const type =
            this.elements.typeFilter.value;


        const category =
            this.elements.categoryFilter.value;


        const dateFrom =
            this.elements.dateFrom.value;


        const dateTo =
            this.elements.dateTo.value;


        const personSearch =
            this.elements.person.value
                .trim()
                .toLowerCase();


        const recordedBySearch =
            this.elements.recordedBy.value
                .trim()
                .toLowerCase();


        const descriptionSearch =
            this.elements.description.value
                .trim()
                .toLowerCase();


        const minAmount =
            parseFloat(
                this.elements.minAmount.value
            );


        const maxAmount =
            parseFloat(
                this.elements.maxAmount.value
            );


        this.filteredTransactions =
            this.transactions.filter(
                transaction => {

                    /*
                    | General Search
                    */

                    const person =
                        this.getPersonName(
                            transaction
                        );


                    const recordedBy =
                        this.getRecordedBy(
                            transaction
                        );


                    const searchableText = [
                        transaction
                            .transaction_category_name,

                        transaction
                            .transaction_description,

                        transaction
                            .transaction_type,

                        person,

                        recordedBy

                    ]
                        .join(' ')
                        .toLowerCase();


                    if (
                        search &&
                        !searchableText.includes(
                            search
                        )
                    ) {
                        return false;
                    }


                    /*
                    | Transaction Type
                    */

                    if (
                        type &&
                        transaction.transaction_type !==
                        type
                    ) {
                        return false;
                    }


                    /*
                    | Category
                    */

                    if (
                        category &&
                        transaction
                            .transaction_category_name !==
                        category
                    ) {
                        return false;
                    }


                    /*
                    | Date From
                    */

                    if (
                        dateFrom &&
                        transaction
                            .transaction_trxn_date
                            .substring(0, 10) <
                        dateFrom
                    ) {
                        return false;
                    }


                    /*
                    | Date To
                    */

                    if (
                        dateTo &&
                        transaction
                            .transaction_trxn_date
                            .substring(0, 10) >
                        dateTo
                    ) {
                        return false;
                    }


                    /*
                    | Person
                    */

                    if (
                        personSearch &&
                        !person
                            .toLowerCase()
                            .includes(
                                personSearch
                            )
                    ) {
                        return false;
                    }


                    /*
                    | Recorded By
                    */

                    if (
                        recordedBySearch &&
                        !recordedBy
                            .toLowerCase()
                            .includes(
                                recordedBySearch
                            )
                    ) {
                        return false;
                    }


                    /*
                    | Description
                    */

                    if (
                        descriptionSearch &&
                        !String(
                            transaction
                                .transaction_description ||
                            ''
                        )
                            .toLowerCase()
                            .includes(
                                descriptionSearch
                            )
                    ) {
                        return false;
                    }


                    /*
                    | Minimum Amount
                    */

                    if (
                        !isNaN(minAmount) &&
                        Number(
                            transaction
                                .transaction_amount
                        ) <
                        minAmount
                    ) {
                        return false;
                    }


                    /*
                    | Maximum Amount
                    */

                    if (
                        !isNaN(maxAmount) &&
                        Number(
                            transaction
                                .transaction_amount
                        ) >
                        maxAmount
                    ) {
                        return false;
                    }


                    return true;

                }
            );


        this.sortTransactions();

        this.render();

    },


    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    sortTransactions() {

        const sort =
            this.elements.sort.value;


        this.filteredTransactions.sort(
            (a, b) => {

                if (sort === 'amount_desc') {

                    return Number(
                        b.transaction_amount
                    ) -
                    Number(
                        a.transaction_amount
                    );

                }


                if (sort === 'amount_asc') {

                    return Number(
                        a.transaction_amount
                    ) -
                    Number(
                        b.transaction_amount
                    );

                }


                if (sort === 'category') {

                    return a
                        .transaction_category_name
                        .localeCompare(
                            b.transaction_category_name
                        );

                }


                if (sort === 'date_asc') {

                    return new Date(
                        a.transaction_trxn_date
                    ) -
                    new Date(
                        b.transaction_trxn_date
                    );

                }


                return new Date(
                    b.transaction_trxn_date
                ) -
                new Date(
                    a.transaction_trxn_date
                );

            }
        );

    },


    /*
    |--------------------------------------------------------------------------
    | Render Table
    |--------------------------------------------------------------------------
    */

    render() {

        const total =
            this.filteredTransactions.length;


        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    total /
                    this.pageSize
                )
            );


        if (
            this.currentPage >
            totalPages
        ) {
            this.currentPage =
                totalPages;
        }


        const start =
            (
                this.currentPage -
                1
            ) *
            this.pageSize;


        const end =
            Math.min(
                start +
                this.pageSize,
                total
            );


        const pageData =
            this.filteredTransactions.slice(
                start,
                end
            );


        this.elements.body.innerHTML = '';


        if (pageData.length === 0) {

            this.elements.body.innerHTML = `

                <tr>

                    <td
                        colspan="10"
                    >

                        <div class="empty-state">

                            <i class="fas fa-receipt"></i>

                            <h5>
                                No transactions found
                            </h5>

                            <p>
                                Try changing your search or filters.
                            </p>

                        </div>

                    </td>

                </tr>

            `;

        } else {

            pageData.forEach(
                (transaction, index) => {

                    const rowNumber =
                        start +
                        index +
                        1;


                    const type =
                        transaction
                            .transaction_type;


                    const isCashIn =
                        type === 'cash_in';


                    const person =
                        this.getPersonName(
                            transaction
                        );


                    const recordedBy =
                        this.getRecordedBy(
                            transaction
                        );


                    const row =
                        document.createElement(
                            'tr'
                        );


                    row.innerHTML = `

                        <td>
                            <span class="transaction-number">
                                ${rowNumber}
                            </span>
                        </td>


                        <td data-column="type">

                            <span
                                class="transaction-type ${
                                    isCashIn
                                        ? 'cash-in'
                                        : 'cash-out'
                                }"
                            >

                                <i class="fas ${
                                    isCashIn
                                        ? 'fa-arrow-down'
                                        : 'fa-arrow-up'
                                }"></i>

                                ${
                                    isCashIn
                                        ? 'Cash In'
                                        : 'Cash Out'
                                }

                            </span>

                        </td>


                        <td data-column="category">

                            <span class="category-badge">

                                ${this.escapeHtml(
                                    transaction
                                        .transaction_category_name
                                )}

                            </span>

                        </td>


                        <td data-column="amount">

                            <span
                                class="amount ${
                                    isCashIn
                                        ? 'cash-in'
                                        : 'cash-out'
                                }"
                            >

                                ${
                                    isCashIn
                                        ? '+'
                                        : '-'
                                }

                                ${this.formatMoney(
                                    transaction
                                        .transaction_amount
                                )}

                            </span>

                        </td>


                        <td
                            data-column="description"
                            class="description-cell"
                            title="${this.escapeHtml(
                                transaction
                                    .transaction_description ||
                                ''
                            )}"
                        >

                            ${
                                this.escapeHtml(
                                    transaction
                                        .transaction_description ||
                                    '—'
                                )
                            }

                        </td>


                        <td data-column="person">

                            ${
                                person
                                    ? `
                                        <span class="person-name">
                                            ${this.escapeHtml(person)}
                                        </span>
                                    `
                                    : `
                                        <span class="muted">
                                            —
                                        </span>
                                    `
                            }

                        </td>


                        <td data-column="recordedBy">

                            <span class="person-name">

                                ${this.escapeHtml(
                                    recordedBy
                                )}

                            </span>

                        </td>


                        <td
                            data-column="transactionDate"
                            class="date-cell"
                        >

                            ${this.formatDisplayDate(
                                transaction
                                    .transaction_trxn_date
                            )}

                        </td>


                        <td
                            data-column="createdAt"
                            class="date-cell"
                        >

                            ${this.formatDisplayDate(
                                transaction
                                    .transaction_created_at
                            )}

                        </td>


                        <td>

                            <button
                                type="button"
                                class="view-button"
                                title="View transaction"
                                onclick="TransactionsTable.viewTransaction('${transaction.id}')"
                            >

                                <i class="fas fa-eye"></i>

                            </button>

                        </td>

                    `;


                    this.elements.body
                        .appendChild(row);

                }
            );

        }


        /*
        | Summary
        */

        this.elements.count.innerHTML = `

            Showing
            <strong>
                ${total}
            </strong>
            transactions

        `;


        this.elements.paginationInfo
            .textContent =
            total === 0
                ? 'Showing 0 to 0 of 0 transactions'
                : `Showing ${
                    start + 1
                } to ${
                    end
                } of ${
                    total
                } transactions`;


        this.renderPagination(
            totalPages
        );


        this.updateColumnVisibility();

    },


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    renderPagination(totalPages) {

        this.elements.pagination
            .innerHTML = '';


        const previous =
            document.createElement(
                'button'
            );


        previous.className =
            'pagination-button';


        previous.innerHTML =
            '<i class="fas fa-chevron-left"></i>';


        previous.disabled =
            this.currentPage === 1;


        previous.addEventListener(
            'click',
            () => {

                if (
                    this.currentPage >
                    1
                ) {

                    this.currentPage--;

                    this.render();

                }

            }
        );


        this.elements.pagination
            .appendChild(previous);


        const pages =
            this.getPaginationPages(
                totalPages
            );


        pages.forEach(page => {

            if (page === '...') {

                const span =
                    document.createElement(
                        'span'
                    );

                span.className =
                    'pagination-ellipsis';

                span.textContent =
                    '...';

                this.elements.pagination
                    .appendChild(span);

                return;

            }


            const button =
                document.createElement(
                    'button'
                );


            button.className =
                'pagination-button';


            if (
                page ===
                this.currentPage
            ) {

                button.classList.add(
                    'active'
                );

            }


            button.textContent =
                page;


            button.addEventListener(
                'click',
                () => {

                    this.currentPage =
                        page;

                    this.render();

                }
            );


            this.elements.pagination
                .appendChild(button);

        });


        const next =
            document.createElement(
                'button'
            );


        next.className =
            'pagination-button';


        next.innerHTML =
            '<i class="fas fa-chevron-right"></i>';


        next.disabled =
            this.currentPage ===
            totalPages;


        next.addEventListener(
            'click',
            () => {

                if (
                    this.currentPage <
                    totalPages
                ) {

                    this.currentPage++;

                    this.render();

                }

            }
        );


        this.elements.pagination
            .appendChild(next);

    },


    /*
    |--------------------------------------------------------------------------
    | Pagination Pages
    |--------------------------------------------------------------------------
    */

    getPaginationPages(totalPages) {

        if (totalPages <= 7) {

            return Array.from(
                {
                    length: totalPages
                },
                (_, index) =>
                    index + 1
            );

        }


        const pages = [
            1
        ];


        if (
            this.currentPage >
            4
        ) {
            pages.push('...');
        }


        const start =
            Math.max(
                2,
                this.currentPage - 1
            );


        const end =
            Math.min(
                totalPages - 1,
                this.currentPage + 1
            );


        for (
            let page = start;
            page <= end;
            page++
        ) {

            pages.push(page);

        }


        if (
            this.currentPage <
            totalPages - 3
        ) {
            pages.push('...');
        }


        pages.push(totalPages);


        return pages;

    },


    /*
    |--------------------------------------------------------------------------
    | Column Visibility
    |--------------------------------------------------------------------------
    */

    updateColumnVisibility() {

        document
            .querySelectorAll(
                '.column-option input'
            )
            .forEach(input => {

                const column =
                    input.dataset.column;


                const visible =
                    input.checked;


                document
                    .querySelectorAll(
                        `[data-column="${column}"]`
                    )
                    .forEach(element => {

                        element.style.display =
                            visible
                                ? ''
                                : 'none';

                    });

            });

    },


    /*
    |--------------------------------------------------------------------------
    | Clear Filters
    |--------------------------------------------------------------------------
    */

    clearFilters() {

        this.elements.search.value = '';

        this.elements.searchClear
            .style.display = 'none';

        this.elements.typeFilter.value = '';

        this.elements.categoryFilter.value = '';

        this.elements.dateFrom.value = '';

        this.elements.dateTo.value = '';

        this.elements.person.value = '';

        this.elements.recordedBy.value = '';

        this.elements.description.value = '';

        this.elements.minAmount.value = '';

        this.elements.maxAmount.value = '';

        this.elements.sort.value =
            'date_desc';

        this.currentPage = 1;

        this.applyFilters();

    },


    /*
    |--------------------------------------------------------------------------
    | Get Person
    |--------------------------------------------------------------------------
    */

    getPersonName(transaction) {

        if (
            transaction.client_first_name
        ) {

            return [
                transaction.client_first_name,
                transaction.client_last_name
            ]
                .filter(Boolean)
                .join(' ');

        }


        if (
            transaction.officer_first_name
        ) {

            return [
                transaction.officer_first_name,
                transaction.officer_last_name
            ]
                .filter(Boolean)
                .join(' ');

        }


        return '';

    },


    /*
    |--------------------------------------------------------------------------
    | Get Recorded By
    |--------------------------------------------------------------------------
    */

    getRecordedBy(transaction) {

        return [
            transaction.recorded_by_first_name,
            transaction.recorded_by_last_name
        ]
            .filter(Boolean)
            .join(' ');

    },


    /*
    |--------------------------------------------------------------------------
    | View Transaction
    |--------------------------------------------------------------------------
    */

    viewTransaction(id) {

        const transaction =
            this.transactions.find(
                item =>
                    item.id === id
            );


        if (!transaction) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Temporary behaviour
        |--------------------------------------------------------------------------
        |
        | No modal yet.
        | This simply demonstrates that the
        | correct transaction is available.
        |
        */

        console.log(
            'Selected transaction:',
            transaction
        );

    },


    /*
    |--------------------------------------------------------------------------
    | Format Money
    |--------------------------------------------------------------------------
    */

    formatMoney(amount) {

        return Number(amount)
            .toLocaleString(
                'en-US',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );

    },


    /*
    |--------------------------------------------------------------------------
    | Format Date Time
    |--------------------------------------------------------------------------
    */

    formatDateTime(date) {

        const year =
            date.getFullYear();


        const month =
            String(
                date.getMonth() + 1
            )
                .padStart(2, '0');


        const day =
            String(
                date.getDate()
            )
                .padStart(2, '0');


        const hours =
            String(
                date.getHours()
            )
                .padStart(2, '0');


        const minutes =
            String(
                date.getMinutes()
            )
                .padStart(2, '0');


        const seconds =
            String(
                date.getSeconds()
            )
                .padStart(2, '0');


        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    },


    /*
    |--------------------------------------------------------------------------
    | Display Date
    |--------------------------------------------------------------------------
    */

    formatDisplayDate(dateString) {

        const date =
            new Date(
                dateString.replace(
                    ' ',
                    'T'
                )
            );


        if (
            Number.isNaN(
                date.getTime()
            )
        ) {

            return '—';

        }


        return date.toLocaleString(
            'en-GB',
            {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }
        );

    },


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    escapeHtml(value) {

        const div =
            document.createElement(
                'div'
            );

        div.textContent =
            String(
                value ?? ''
            );

        return div.innerHTML;

    }

};


/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    () => {

        TransactionsTable.init();

    }
);

</script>

</body>

</html>

