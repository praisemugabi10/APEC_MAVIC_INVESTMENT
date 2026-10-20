<?php
require_once "api/config/db.php";

// Check if the user is NOT logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: auth/login.html");
    exit;
}

// Session variables
$userName = $_SESSION['full_name'];
$userRole = $_SESSION['role'];
// echo "userName: $userName, userRole: $userRole"; // Debugging line

// Field Officer interface is not available yet
if ($userRole === 'field_officer') {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>APEC | Field Officer</title>

        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    </head>

    <body class="min-h-screen bg-slate-50 flex items-center justify-center px-6">
        <div class="w-full max-w-lg text-center">
            <!-- LOGO -->
            <div class="w-20 h-20 mx-auto mb-7 rounded-3xl bg-blue-600
                        flex items-center justify-center
                        text-white text-3xl font-black shadow-xl">
                A
            </div>

            <!-- MESSAGE -->
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mb-4">
                Your Interface Is Coming Soon
            </h1>

            <p class="text-slate-500 text-base sm:text-lg leading-relaxed mb-8">
                Hello, <?= htmlspecialchars($userName) ?>.
                Your Field Officer interface is currently being developed
                and will be available soon.
            </p>

            <!-- STATUS -->
            <div class="inline-flex items-center gap-3
                        px-6 py-4 rounded-2xl
                        bg-blue-50 text-blue-700
                        font-bold">
                <i class="fas fa-code"></i>
                <span>We're working on your interface</span>
            </div>

            <!-- LOGOUT -->
            <div class="mt-8">
                <button
                    onclick="logout()"
                    class="inline-flex items-center gap-3
                        px-6 py-3 rounded-2xl
                        bg-rose-500 text-white
                        font-bold shadow-lg
                        hover:bg-rose-600
                        active:scale-95
                        transition-all">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>
    </body>

    <script>
        async function logout() {
            try {
                const response = await fetch('api/logout.php', { method: 'POST' });
                const data = await response.json();
                if (data.status === 'success') {
                    window.location.href = 'auth/login.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        }
    </script>
    </html>
    <?php
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <title>APEC | Loan Management System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>

    </style>
</head>

<body class="text-slate-800">
    <!-- APP WRAPPER -->
    <div id="main-app" class="min-h-screen flex flex-col lg:flex-row overflow-x-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 h-screen bg-[#0f172a] text-slate-400 sidebar-transition -translate-x-full lg:translate-x-0 lg:static flex flex-col shadow-2xl collapsed overflow-hidden">
            <!-- SIDEBAR HEADER -->
            <div class="h-24 flex items-center justify-between px-6 mb-4">
                <button onclick="handleLogoClick()" class="flex items-center gap-4 relative group">
                    <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-lg shrink-0 overflow-hidden">
                        <img
                            src="assets/images/logo.thumbnail"
                            alt="APEC MAVIC"
                            class="w-full h-full object-contain"
                        >
                    </div>

                    <span class="text-xl font-extrabold text-white tracking-tight nav-text">
                        APEC MAVIC
                    </span>

                    <!-- LOGO TOOLTIP -->
                    <span class="sidebar-tooltip">
                        Open Sidebar
                    </span>
                </button>

                <!-- MOBILE CLOSE BUTTON -->
                <button onclick="toggleMobileMenu()"
                    class="lg:hidden text-slate-400 hover:text-white p-2">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- NAVIGATION -->
            <nav class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden px-4 space-y-3 hide-scrollbar">

                <button onclick="navigateFromSidebar('dashboard')"
                    class="nav-link active w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-semibold transition-all group relative">
                    <i class="fas fa-th-large text-lg shrink-0 w-6"></i>
                    <span class="nav-text text-sm">Dashboard</span>
                    <span class="sidebar-tooltip">Dashboard</span>
                </button>

                <button onclick="navigateFromSidebar('cashier-collections')"
                    class="nav-link w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-semibold transition-all hover:text-white hover:bg-white/5 group relative">
                    <i class="fas fa-cash-register text-lg shrink-0 w-6"></i>
                    <span class="nav-text text-sm">Cashier Collections</span>
                    <span class="sidebar-tooltip">Cashier Collections</span>
                </button>

                <button onclick="navigateFromSidebar('officers')"
                    class="nav-link w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-semibold transition-all hover:text-white hover:bg-white/5 group relative">
                    <i class="fas fa-user-shield text-lg shrink-0 w-6"></i>
                    <span class="nav-text text-sm">Field Officers</span>
                    <span class="sidebar-tooltip">Field Officers</span>
                </button>

                <button onclick="navigateFromSidebar('clients')"
                    class="nav-link w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-semibold transition-all hover:text-white hover:bg-white/5 group relative">
                    <i class="fas fa-users text-lg shrink-0 w-6"></i>
                    <span class="nav-text text-sm">Client List</span>
                    <span class="sidebar-tooltip">Client List</span>
                </button>

                 <!-- // manage employees -->
                <?php if (($userRole ?? '') === 'manager'): ?>
                    <button onclick="navigateFromSidebar('staff-management')"
                        class="nav-link w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-semibold transition-all hover:text-white hover:bg-white/5 group relative">
                        <i class="fas fa-user-cog text-lg shrink-0 w-6"></i>
                        <span class="nav-text text-sm">Staff Management</span>
                        <span class="sidebar-tooltip">Staff Management</span>
                    </button>

                    <button onclick="navigateFromSidebar('user-management')"
                        class="nav-link w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-semibold transition-all hover:text-white hover:bg-white/5 group relative">
                        <i class="fas fa-user-lock text-lg shrink-0 w-6"></i>
                        <span class="nav-text text-sm">User Management</span>
                        <span class="sidebar-tooltip">User Management</span>
                    </button>
                <?php endif; ?>

                <button onclick="navigateFromSidebar('reports')"
                    class="nav-link w-full flex items-center gap-4 px-4 py-4 rounded-2xl font-semibold transition-all hover:text-white hover:bg-white/5 group relative">
                    <i class="fas fa-file-invoice-dollar text-lg shrink-0 w-6"></i>
                    <span class="nav-text text-sm">Financial Report</span>
                    <span class="sidebar-tooltip">Financial Report</span>
                </button>
            </nav>

            <!-- LOGOUT -->
            <div class="p-4 mt-auto">
                <button onclick="logout()"
                    class="nav-link w-full flex items-center gap-4 px-4 py-4 rounded-2xl bg-rose-500/10 text-rose-500 font-bold hover:bg-rose-500 hover:text-white transition-all group relative">
                    <i class="fas fa-sign-out-alt text-lg shrink-0 w-6"></i>
                    <span class="nav-text text-sm">Logout</span>
                    <span class="sidebar-tooltip">Logout</span>
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">

            <!-- TOPBAR -->
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-6 sm:px-10 sticky top-0 z-50">
                <div class="flex items-center gap-4">
                    <!-- MOBILE MENU BUTTON -->
                    <button onclick="toggleMobileMenu()" class="lg:hidden p-2 text-slate-600 bg-slate-100 rounded-xl">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 id="current-title" class="text-xl font-extrabold text-slate-900 tracking-tight">
                        Dashboard
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-right">
                        <p id="clock" class="text-sm font-bold text-slate-900"></p>
                        <p class="text-[10px] font-bold text-blue-600 uppercase">
                            System Online
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE CONTENT -->
            <div id="content-viewport"
                class="flex-1 overflow-y-auto p-4 sm:p-10 space-y-8">

                <!-- DASHBOARD -->
                <section id="dashboard" class="page-section active space-y-8 animate-in fade-in duration-500">
                    <!-- HERO BALANCE CARD -->
                    <div class="relative overflow-hidden rounded-3xl lg:rounded-[2.5rem] bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 p-6 sm:p-8 lg:p-12 text-white shadow-2xl">
                        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            
                            <!-- BALANCE DISPLAY -->
                            <div class="min-w-0">
                                <p class="mb-2 text-xs sm:text-sm lg:text-base font-black uppercase tracking-[0.2em] text-blue-100/80">
                                    Balance
                                </p>
                                <h3 id="dash-balance" class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-black tracking-tight tabular-nums">
                                    <span
                                        class="spinner-border spinner-border-sm"
                                        style="width: 1.5rem; height: 1.5rem; border-width: 3px;"
                                        role="status"
                                        aria-label="Loading">
                                    </span>
                                </h3>
                            </div>

                            <!-- 1. COMPACT VIEW: Phones & Medium Screens (< 1024px) -->
                            <div class="flex items-center gap-5 sm:gap-6 self-start sm:self-auto lg:hidden">
                                <!-- New Transaction -->
                                <button
                                    type="button"
                                    onclick="openModal('modal-transaction')"
                                    class="group flex flex-col items-center gap-1.5 focus:outline-none">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-blue-700 shadow-lg transition group-hover:scale-105 active:scale-95">
                                        <i class="fas fa-plus text-base" aria-hidden="true"></i>
                                    </div>
                                    <span class="text-[11px] font-bold text-white/90">New</span>
                                </button>

                                <!-- Manage Categories -->
                                <button
                                    type="button"
                                    onclick="openTransactionCategoryModal()"
                                    class="group flex flex-col items-center gap-1.5 focus:outline-none">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/20 bg-blue-500/30 text-white backdrop-blur-md transition group-hover:bg-blue-500/50 active:scale-95">
                                        <i class="fas fa-tags text-sm" aria-hidden="true"></i>
                                    </div>
                                    <span class="text-[11px] font-bold text-white/90">Categories</span>
                                </button>

                                <!-- View Report -->
                                <button
                                    type="button"
                                    onclick="router('reports')"
                                    class="group flex flex-col items-center gap-1.5 focus:outline-none">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/20 bg-blue-500/30 text-white backdrop-blur-md transition group-hover:bg-blue-500/50 active:scale-95">
                                        <i class="fas fa-file-lines text-sm" aria-hidden="true"></i>
                                    </div>
                                    <span class="text-[11px] font-bold text-white/90">Reports</span>
                                </button>
                            </div>

                            <!-- 2. ORIGINAL VIEW: Wide Screens Only (>= 1024px) -->
                            <div class="hidden lg:flex flex-col gap-3 min-w-[240px]">
                                <!-- NEW TRANSACTION -->
                                <button
                                    type="button"
                                    onclick="openModal('modal-transaction')"
                                    class="flex items-center justify-center gap-3 rounded-2xl bg-white px-6 py-3.5 font-extrabold text-blue-700 shadow-xl transition-all hover:scale-105 active:scale-95 whitespace-nowrap">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                    <span>New Transaction</span>
                                </button>

                                <!-- MANAGE TRANSACTION CATEGORIES -->
                                <button
                                    type="button"
                                    onclick="openTransactionCategoryModal()"
                                    class="flex items-center justify-center gap-3 rounded-2xl border border-white/20 bg-blue-500/30 px-6 py-3.5 font-extrabold text-white transition-all hover:bg-blue-500/50 active:scale-95 whitespace-nowrap">
                                    <i class="fas fa-tags" aria-hidden="true"></i>
                                    <span>Manage Categories</span>
                                </button>

                                <!-- VIEW REPORT -->
                                <button
                                    type="button"
                                    onclick="router('reports')"
                                    class="flex items-center justify-center gap-3 rounded-2xl border border-white/20 bg-blue-500/30 px-6 py-3.5 font-extrabold text-white transition-all hover:bg-blue-500/50 active:scale-95 whitespace-nowrap">
                                    <i class="fas fa-file-lines" aria-hidden="true"></i>
                                    <span>View Report</span>
                                </button>
                            </div>

                        </div>

                        <!-- Decorative blur glow -->
                        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 sm:h-80 sm:w-80 rounded-full bg-white/10 blur-3xl"></div>
                    </div>

                    <!-- KPI GRID -->
                    <section class="overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5 shadow-sm">
    <!-- TOOLBAR / DATE SELECTOR HEADER -->
    <div class="mb-4 sm:mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-sm sm:text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-wallet text-slate-400 text-xs sm:text-sm"></i>
                Cash Flow Overview
            </h3>
            <p class="text-xs font-medium text-slate-500">
                Summary of today's cash in & cash out
            </p>
        </div>

        <!-- Integrated Compact Date Pill -->
        <div class="flex items-center justify-between sm:justify-start gap-1 rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-1 sm:p-1.5 shadow-xs w-full sm:w-auto">
            <div class="relative flex items-center flex-1 sm:flex-initial">
                <i class="fas fa-calendar-alt pointer-events-none absolute left-3 text-xs text-slate-400" aria-hidden="true"></i>
                <input
                    type="date"
                    id="dashboard-transaction-date"
                    class="w-full cursor-pointer rounded-lg sm:rounded-xl border-0 bg-transparent py-1.5 pl-8 pr-2 text-xs font-bold text-slate-700 outline-none transition focus:ring-2 focus:ring-blue-100"
                    aria-label="Filter transactions by date">
            </div>

            <button
                type="button"
                id="dashboard-transaction-today"
                class="inline-flex items-center gap-1.5 rounded-lg sm:rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-200 whitespace-nowrap">
                <i class="fas fa-calendar-day text-[11px]" aria-hidden="true"></i>
                Today
            </button>
        </div>
    </div>

    <!-- STAT CARDS (Edge-to-edge on phones, separate cards on tablet/desktop) -->
    
<div class="-mx-4 -mb-4 sm:mx-0 sm:mb-0 grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 border-t border-slate-200/80 sm:border-t-0 divide-y-0">
        
        <!-- TOTAL CASH IN -->
        <div class="flex items-center justify-between bg-white p-4 sm:p-5 rounded-none sm:rounded-2xl border-0 sm:border sm:border-slate-200/80 shadow-none sm:shadow-sm transition hover:shadow-md">
            <div>
                <p class="mb-1 text-[11px] sm:text-xs font-black uppercase tracking-wider text-slate-400">
                    Total Cash In
                </p>
                <h4 id="dash-in-total" class="text-2xl sm:text-3xl font-black text-blue-600 tracking-tight">
                    <span
                        class="spinner-border spinner-border-sm"
                        style="width: 1.25rem; height: 1.25rem; border-width: 2.5px;"
                        role="status"
                        aria-label="Loading">
                    </span>
                </h4>
            </div>
            <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl sm:rounded-2xl bg-blue-50 text-lg sm:text-xl text-blue-600">
                <i class="fas fa-arrow-down" aria-hidden="true"></i>
            </div>
        </div>

        <!-- TOTAL CASH OUT -->
        <div class="flex items-center justify-between bg-white p-4 sm:p-5 rounded-none sm:rounded-2xl border-0 sm:border sm:border-slate-200/80 shadow-none sm:shadow-sm transition hover:shadow-md">
            <div>
                <p class="mb-1 text-[11px] sm:text-xs font-black uppercase tracking-wider text-slate-400">
                    Total Cash Out
                </p>
                <h4 id="dash-out-total" class="text-2xl sm:text-3xl font-black text-orange-600 tracking-tight">
                    <span
                        class="spinner-border spinner-border-sm"
                        style="width: 1.25rem; height: 1.25rem; border-width: 2.5px;"
                        role="status"
                        aria-label="Loading">
                    </span>
                </h4>
            </div>
            <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-xl sm:rounded-2xl bg-orange-50 text-lg sm:text-xl text-orange-600">
                <i class="fas fa-arrow-up" aria-hidden="true"></i>
            </div>
        </div>

    </div>
</section>
                    
                    <!-- DAILY COLLECTION PERFORMANCE -->
                    <div class="rounded-3xl lg:rounded-[2.5rem] border border-slate-200 bg-white p-5 sm:p-6 lg:p-8 shadow-sm">
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                            
                            <!-- LEFT: KPI METRIC -->
                            <div class="min-w-0">
                                <p class="mb-1 text-xs sm:text-[13px] font-black uppercase tracking-wider text-slate-400">
                                    Daily Collection Performance
                                </p>
                                <div class="flex flex-wrap items-baseline gap-2 sm:gap-3">
                                    <h4 id="dash-payment-percentage" class="text-3xl sm:text-4xl lg:text-5xl font-black text-blue-600 tracking-tight">
                                        ...
                                    </h4>
                                    <span id="dash-payment-percentage-summary" class="text-xs sm:text-sm font-bold text-slate-500">
                                        Calculating...
                                    </span>
                                </div>
                                <p id="dash-payment-percentage-date" class="mt-1 text-xs sm:text-sm font-semibold text-slate-400 mb-0">
                                    Loading date...
                                </p>
                            </div>

                            <!-- RIGHT: ACTION CONTROLS (Grouped) -->
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3">
                                <!-- View Officer Breakdown Button -->
                                <button
                                    type="button"
                                    onclick="openDashboardOfficerBreakdown()"
                                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-slate-100 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-300">
                                    <i class="fas fa-users text-slate-500 text-xs sm:text-sm" aria-hidden="true"></i>
                                    <span>Officer Breakdown</span>
                                </button>

                                <!-- Integrated Date Toolbar Pill -->
                                <div class="flex items-center justify-between sm:justify-start gap-1 rounded-xl border border-slate-200 bg-slate-50 p-1">
                                    <input
                                        type="date"
                                        id="dash-payment-date"
                                        class="w-full sm:w-auto cursor-pointer rounded-lg border-0 bg-transparent px-2.5 py-1.5 text-xs sm:text-sm font-semibold text-slate-700 outline-none transition focus:bg-white focus:ring-2 focus:ring-blue-100"
                                        aria-label="Payment Date">

                                    <button
                                        type="button"
                                        onclick="setDashboardPaymentDateToday()"
                                        class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-blue-600 px-3 py-1.5 text-xs sm:text-sm font-bold text-white shadow-xs transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                        <i class="fas fa-calendar-day text-[11px]" aria-hidden="true"></i>
                                        Today
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- TRANSACTION TABLE -->
                    <div class="table-responsive transaction-table-wrapper">
                        <table class="table table-hover align-middle mb-0 transaction-table">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 px-sm-5 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">#</th>
                                    <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">Type</th>
                                    <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">Category</th>
                                    <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">Amount</th>
                                    <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">Description</th>
                                    <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">Client / Officer</th>
                                    <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">Recorded By</th>
                                    <th class="px-4 px-sm-5 py-4 text-[10px] font-black uppercase text-slate-400 whitespace-nowrap">Date</th>
                                </tr>
                            </thead>

                            <!-- JavaScript will populate this -->
                            <tbody id="transaction-list">
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <span
                                            class="spinner-border spinner-border-sm"
                                            style="width: 1.5rem; height: 1.5rem; border-width: 0.2em;"
                                            role="status"
                                            aria-label="Loading transactions">
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- CASHIER COLLECTIONS -->
                <section id="cashier-collections" class="page-section space-y-6">
                    <!-- HEADER -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">
                                Cashier Collections
                            </h3>
                            <p class="text-sm font-semibold text-slate-400 mt-1">
                                Payments received directly at the office.
                            </p>
                        </div>
                        <button
                            type="button"
                            onclick="openCashierRecordPayment()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-md hover:shadow-lg flex items-center gap-2 transition-all active:scale-95 font-bold text-sm whitespace-nowrap">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Record Cash In</span>
                        </button>
                    </div>

                    <!-- SUMMARY CARDS -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- TODAY'S CASH IN -->
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 sm:p-7">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">
                                        Today's Cash In
                                    </p>
                                    <div id="cashier-today-total" class="text-2xl sm:text-3xl font-black text-slate-900">
                                        UGX 0
                                    </div>
                                    <p class="text-xs font-bold text-slate-400 mt-2">
                                        Total Cash collected at office today
                                    </p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-money-bill-wave text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- CLIENTS PAID -->
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 sm:p-7">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">
                                        No. of Clients
                                    </p>
                                    <div id="cashier-today-client-count" class="text-2xl sm:text-3xl font-black text-slate-900">
                                        0
                                    </div>
                                    <p class="text-xs font-bold text-slate-400 mt-2">
                                        Total number of clients who made payments today at office
                                    </p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-users text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLLECTIONS -->
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                        <!-- COLLECTION HEADER -->
                        <div class="p-5 sm:p-6 border-b border-slate-100">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div>
                                    <h4 class="text-lg font-black text-slate-900">
                                        Collections
                                    </h4>
                                    <p id="cashier-collections-date-label" class="text-xs font-bold text-slate-400 mt-1">
                                        Today's office collections
                                    </p>
                                </div>
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                    <div class="flex w-full sm:w-auto">
                                        <input
                                            type="date"
                                            id="cashier-collections-date"
                                            class="form-control py-2.5 px-4 rounded-xl sm:rounded-r-none font-bold border-2 border-slate-100 bg-slate-50 shadow-none w-full sm:w-auto">
                                        <button
                                            type="button"
                                            onclick="selectCashierCollectionsToday()"
                                            class="px-4 py-2.5 rounded-r-xl rounded-l-none font-black text-slate-600 bg-slate-100 border-2 border-l-0 border-slate-100 hover:bg-slate-200 transition-colors whitespace-nowrap"
                                            title="Today"
                                            aria-label="Select today">
                                            <i class="fas fa-calendar-day"></i>
                                            <span class="hidden sm:inline ms-1">Today</span>
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        onclick="loadCashierCollections()"
                                        class="btn py-2.5 px-4 rounded-xl font-black text-slate-600 bg-slate-100 border-0 shadow-none hover:bg-slate-200 whitespace-nowrap">
                                        <i class="fas fa-refresh me-2"></i>
                                        Refresh
                                    </button>
                                    <button
                                        type="button"
                                        onclick="printCashierCollections()"
                                        class="btn py-2.5 px-4 rounded-xl font-black text-white bg-slate-800 border-0 shadow-none hover:bg-slate-900 whitespace-nowrap">
                                        <i class="fas fa-print me-2"></i>
                                        Print
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- COLLECTION TABLE -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[900px] compact-table">
                                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-black uppercase tracking-widest text-slate-500">
                                    <tr>
                                        <th class="px-8 py-5 text-slate-900">Client Name</th>
                                        <th class="px-6 py-5">Phone Number</th>
                                        <th class="px-6 py-5">Field Officer</th>
                                        <th class="px-6 py-5 text-end">Amount Paid</th>
                                        <th class="px-6 py-5 text-center">Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody id="cashier-collections-list" class="divide-y divide-slate-100 font-medium text-slate-700">
                                    <tr>
                                        <td colspan="5" class="px-8 py-12 text-center text-slate-400">
                                            <div class="flex flex-col items-center gap-3">
                                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                                <p class="mb-0 font-semibold">
                                                    Loading collections...
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot id="cashier-collections-total"></tfoot>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- FIELD OFFICERS -->
                <section id="officers" class="page-section space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">
                                Field Officers
                            </h3>
                        </div>
                        <button
                            onclick="openModal('modal-officer')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-md hover:shadow-lg flex items-center gap-2 transition-all active:scale-95 font-bold text-sm whitespace-nowrap">
                            <i class="fas fa-plus-circle"></i>
                            Add Officer
                        </button>
                    </div>

                    <!-- OFFICER SEARCH BAR -->
                    <div class="flex justify-start mb-4">
                        <div class="relative w-full max-w-md group">
                            <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            </span>
                            <input
                                type="text"
                                id="officer-search"
                                class="block w-full pl-12 pr-12 py-4 bg-white border-2 border-slate-100 rounded-full text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 shadow-sm transition-all"
                                placeholder="Search name, email, or phone..."
                                oninput="handleOfficerSearch(this.value)">
                            <button
                                id="clear-search-btn"
                                onclick="clearOfficerSearch()"
                                class="hidden absolute inset-y-0 right-0 pr-5 flex items-center">
                                <i class="fas fa-times-circle text-slate-300 hover:text-slate-500 text-lg transition-colors cursor-pointer"></i>
                            </button>
                        </div>
                    </div>

                    <!-- OFFICER TABLE -->
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden overflow-x-auto">
                        <table class="w-full text-left min-w-[1000px] compact-table">
                            <thead class="bg-slate-100 border-b-2 border-slate-300 text-sm font-black uppercase tracking-wider text-slate-900">
                                <tr>
                                    <th class="px-8 py-6">Officer Name</th>
                                    <th class="px-6 py-6">Email Address</th>
                                    <th class="px-6 py-6">Phone Number</th>
                                    <th class="px-6 py-6">Clients</th>
                                    <th class="px-6 py-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="officer-list" class="divide-y divide-slate-50 font-medium text-slate-700">
                                <!-- Dynamic Data -->
                            </tbody>
                        </table>
                    </div>

                    <!-- OFFICER PAGINATION -->
                    <div id="officer-pagination" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2"></div>
                </section>

                <!-- CLIENTS -->
                <section id="clients" class="page-section space-y-6">
                    <!-- HEADER -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Clients</h3>
                        </div>
                        <div class="flex items-center gap-3 w-full md:w-auto">
                            <!-- CLIENT SEARCH -->
                            <div class="relative flex-1 md:w-80">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="client-search-input" placeholder="Search name, phone, or ID..." class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-full outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm">
                            </div>
                            <!-- ADD CLIENT -->
                            <button id="btn-add-client" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-md hover:shadow-lg flex items-center gap-2 transition-all active:scale-95 font-bold text-sm whitespace-nowrap">
                                <i class="fas fa-plus-circle"></i>
                                <span>Add Client</span>
                            </button>
                        </div>
                    </div>

                    <!-- CLIENT TABLE -->
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                        <!-- CLIENT STATUS TABS -->
                        <div class="flex items-center gap-3 px-6 sm:px-8 pt-5 border-b border-slate-100">
                            <button
                                type="button"
                                id="client-active-tab"
                                onclick="switchClientTab('active')"
                                class="px-5 py-3 text-sm font-bold rounded-t-xl border-b-2 border-red-600 text-red-600 bg-red-50 transition-all flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                    <i class="fas fa-times text-xs"></i>
                                </span>
                                Not Cleared
                            </button>
                            <button
                                type="button"
                                id="client-inactive-tab"
                                onclick="switchClientTab('inactive')"
                                class="px-5 py-3 text-sm font-bold rounded-t-xl border-b-2 border-transparent text-slate-500 hover:text-green-600 hover:bg-green-50 transition-all flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                    <i class="fas fa-check text-xs"></i>
                                </span>
                                Cleared
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[1500px] compact-table">
                                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-black uppercase tracking-widest text-slate-500">
                                    <tr>
                                        <th class="px-8 py-6 text-slate-900">Client Name</th>
                                        <th class="px-6 py-6">Phone Numbers</th>
                                        <th class="px-6 py-6">Location</th>
                                        <th class="px-6 py-6">Loan Amount</th>
                                        <th class="px-6 py-6">Date Taken</th>
                                        <th class="px-6 py-6 text-blue-800">Interest (%)</th>
                                        <th class="px-6 py-6 text-emerald-800">Expected Return</th>
                                        <th class="px-6 py-6">Mortgage</th>
                                        <th class="px-6 py-6">Field Officer</th>
                                        <th class="px-6 py-6">Guarantor</th>
                                        <th class="px-8 py-6 text-center text-slate-900">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="client-list" class="font-medium text-slate-700">
                                    <tr>
                                        <td colspan="11" class="px-8 py-10 text-center text-slate-400">
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="fas fa-users text-4xl opacity-20"></i>
                                                <p>Loading clients...</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- PAGINATION -->
                        <div id="client-pagination" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 sm:px-8 py-5 border-t border-slate-100 bg-white"></div>
                    </div>
                </section>

                <?php if (($_SESSION['role'] ?? '') === 'manager'): ?>
                    <!-- STAFF MANAGEMENT -->
                    <section id="staff-management" class="page-section space-y-6">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div>
                                <h3 class="text-2xl font-black text-slate-900">
                                    Staff Management
                                </h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Manage field officers and cashiers
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                                <button
                                    onclick="openAddStaffModal()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-md hover:shadow-lg flex items-center justify-center gap-2 transition-all active:scale-95 font-bold text-sm whitespace-nowrap w-full sm:w-auto">
                                    <i class="fas fa-user-plus"></i>
                                    Add Staff
                                </button>
                            </div>
                        </div>

                        <!-- STAFF SEARCH AND FILTER -->
                        <div class="flex flex-col lg:flex-row justify-between gap-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full lg:w-auto">
                                <button
                                    id="staff-filter-all"
                                    onclick="filterStaff('all')"
                                    class="px-4 py-3 rounded-full bg-blue-600 text-white font-bold text-sm transition-all w-full">
                                    All Staff
                                </button>

                                <button
                                    id="staff-filter-field-officer"
                                    onclick="filterStaff('field_officer')"
                                    class="px-4 py-3 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-sm transition-all w-full">
                                    Field Officers
                                </button>

                                <button
                                    id="staff-filter-cashier"
                                    onclick="filterStaff('cashier')"
                                    class="px-4 py-3 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-sm transition-all w-full">
                                    Cashiers
                                </button>
                            </div>

                            <!-- searching staff -->
                             <div class="relative w-full lg:w-80">
                                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>

                                <input
                                    type="text"
                                    id="staff-search"
                                    oninput="handleStaffSearch(this.value)"
                                    placeholder="Search name, email or phone..."
                                    autocomplete="off"
                                    class="w-full pl-12 pr-12 py-3 rounded-full bg-slate-100 text-slate-700 placeholder-slate-400 font-medium text-sm border border-transparent focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all">

                                <button
                                    type="button"
                                    id="staff-search-clear"
                                    onclick="clearStaffSearch()"
                                    aria-label="Clear search"
                                    class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-200 items-center justify-center transition-all">
                                    <i class="far fa-times-circle text-base"></i>
                                </button>
                            </div>
                        </div>

                        <!-- STAFF TABLE -->
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">

                            <div class="overflow-x-auto">
                                <table class="w-full text-left min-w-[1500px] compact-table">
                                    <thead class="bg-slate-100 border-b-2 border-slate-300 text-sm font-black uppercase tracking-wider text-slate-900">
                                        <tr>
                                            <th class="px-8 py-6">Staff Name</th>
                                            <th class="px-6 py-6">Role</th>
                                            <th class="px-6 py-6">Email Address</th>
                                            <th class="px-6 py-6">Phone Number</th>
                                            <th class="px-6 py-6">Status</th>
                                            <th class="px-6 py-6">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="staff-list" class="divide-y divide-slate-50 font-medium text-slate-700">
                                        <tr>
                                            <td colspan="6" class="px-8 py-10 text-center text-slate-400">
                                                <div class="flex flex-col items-center gap-2">
                                                    <i class="fas fa-user-cog text-4xl opacity-20"></i>
                                                    <p>Loading staff...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- STAFF PAGINATION -->
                        <div id="staff-pagination" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2"></div>
                    </section>
                <?php endif; ?>

                <?php if (($_SESSION['role'] ?? '') === 'manager'): ?>
                    <!-- USER MANAGEMENT -->
                    <section id="user-management" class="page-section space-y-6">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div>
                                <h3 class="text-2xl font-black text-slate-900">
                                    User Management
                                </h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Manage system login accounts for field officers and cashiers
                                </p>
                            </div>

                            <div class="flex w-full lg:w-auto">
                                <button
                                    onclick="openAddUserModal()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full shadow-md hover:shadow-lg flex items-center justify-center gap-2 transition-all active:scale-95 font-bold text-sm whitespace-nowrap w-full lg:w-auto">
                                    <i class="fas fa-user-plus"></i>
                                    Add User
                                </button>
                            </div>
                        </div>

                        <!-- USER SEARCH AND FILTER -->
                        <div class="flex flex-col lg:flex-row justify-between gap-4">
                            <div class="relative w-full lg:max-w-md group">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                                </span>

                                <input
                                    type="text"
                                    id="user-search"
                                    class="block w-full pl-12 pr-12 py-4 bg-white border-2 border-slate-100 rounded-full text-sm font-bold placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-600 shadow-sm transition-all"
                                    placeholder="Search name, email, or phone..."
                                    oninput="handleUserSearch(this.value)">

                                <button
                                    id="clear-user-search-btn"
                                    onclick="clearUserSearch()"
                                    class="hidden absolute inset-y-0 right-0 pr-5 flex items-center">
                                    <i class="fas fa-times-circle text-slate-300 hover:text-slate-500 text-lg transition-colors cursor-pointer"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full lg:w-auto">
                                <button
                                    id="user-filter-all"
                                    onclick="filterUsers('all')"
                                    class="px-4 py-3 rounded-full bg-blue-600 text-white font-bold text-sm transition-all w-full">
                                    All Users
                                </button>

                                <button
                                    id="user-filter-field-officer"
                                    onclick="filterUsers('field_officer')"
                                    class="px-4 py-3 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-sm transition-all w-full">
                                    Field Officers
                                </button>

                                <button
                                    id="user-filter-cashier"
                                    onclick="filterUsers('cashier')"
                                    class="px-4 py-3 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold text-sm transition-all w-full">
                                    Cashiers
                                </button>
                            </div>
                        </div>

                        <!-- USER TABLE -->
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left min-w-[1000px] compact-table">
                                    <thead class="bg-slate-100 border-b-2 border-slate-300 text-sm font-black uppercase tracking-wider text-slate-900">
                                        <tr>
                                            <th class="px-8 py-6">User Name</th>
                                            <th class="px-6 py-6">Role</th>
                                            <th class="px-6 py-6">Email Address</th>
                                            <th class="px-6 py-6">Phone Number</th>
                                            <th class="px-6 py-6">Status</th>
                                            <th class="px-6 py-6">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="user-list" class="divide-y divide-slate-50 font-medium text-slate-700">
                                        <tr>
                                            <td colspan="6" class="px-8 py-10 text-center text-slate-400">
                                                <div class="flex flex-col items-center gap-2">
                                                    <i class="fas fa-user-lock text-4xl opacity-20"></i>
                                                    <p>Loading users...</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- USER PAGINATION -->
                        <div id="user-pagination" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2"></div>
                    </section>
                <?php endif; ?>

                <!-- REPORTS -->
                <section id="reports" class="page-section max-w-5xl mx-auto space-y-6 px-2 sm:px-0">
                    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h3 class="text-2xl font-black">Daily Cash Report</h3>

                        <div class="flex flex-wrap items-center gap-3">
                            <input
                                type="date"
                                id="report-date-picker"
                                class="border border-slate-300 rounded-xl px-4 py-3"
                            >

                            <button
                                type="button"
                                onclick="renderReport()"
                                class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold"
                            >
                                Load Report
                            </button>

                            <button
                                type="button"
                                onclick="window.print()"
                                class="bg-slate-800 text-white px-5 py-3 rounded-xl font-bold"
                            >
                                <i class="fas fa-print mr-2"></i>Print PDF
                            </button>
                        </div>
                    </div>

                    <div
                        id="report-printable"
                        class="bg-white p-5 sm:p-12 rounded-2xl shadow-xl border border-slate-200"
                    >
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
                            <div class="flex items-center gap-4">
                                <img
                                    src="assets/images/logo.png"
                                    alt="APEC Mavic Investment Logo"
                                    class="w-16 h-16 object-contain"
                                >

                                <div class="flex flex-col items-center text-center">
                                    <h1 class="text-xl sm:text-3xl font-black tracking-tight text-blue-700">
                                        APEC MAVIC INVESTMENT
                                    </h1>
                                    <p id="report-title" class="text-sm font-semibold text-slate-500"></p>
                                    <p
                                        id="report-generated-by"
                                        class="mt-2 font-bold uppercase text-slate-900"
                                    ></p>
                                </div>
                            </div>

                            <div class="sm:text-right">
                                <p class="text-xs font-bold uppercase text-slate-500">Report Date</p>
                                <p id="report-date" class="font-black text-slate-900"></p>
                            </div>
                        </div>

                        <div class="mb-10">

                            <div class="mb-8">
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-slate-300 min-w-[600px]">
                                        <tbody id="report-body" class="text-sm"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- CASH OUTS -->
                            <div class="mb-10">
                                <h2 class="text-lg font-black mb-3">CASH OUTS</h2>

                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-slate-300 min-w-[600px]">
                                        <thead>
                                            <tr class="bg-slate-100">
                                                <th class="border border-slate-300 px-4 py-3 text-left">
                                                    No.
                                                </th>
                                                <th class="border border-slate-300 px-4 py-3 text-left">
                                                    Client Name
                                                </th>
                                                <th class="border border-slate-300 px-4 py-3 text-right">
                                                    Amount (UGX)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="cash-outs-body" class="text-sm"></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- EXPENSES -->
                            <div class="mb-10">
                                <h2 class="text-lg font-black mb-3">EXPENSES</h2>

                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse border border-slate-300 min-w-[600px]">
                                        <thead>
                                            <tr class="bg-slate-100">
                                                <th class="border border-slate-300 px-4 py-3 text-left">
                                                    No.
                                                </th>
                                                <th class="border border-slate-300 px-4 py-3 text-left">
                                                    Description
                                                </th>
                                                <th class="border border-slate-300 px-4 py-3 text-right">
                                                    Amount (UGX)
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="expenses-body" class="text-sm"></tbody>
                                        <tfoot>
                                            <tr class="font-black bg-slate-50">
                                                <td colspan="2" class="border border-slate-300 px-4 py-3 text-right">
                                                    TOTAL
                                                </td>
                                                <td id="expenses-total" class="border border-slate-300 px-4 py-3 text-right whitespace-nowrap">
                                                    UGX 0
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-lg font-black mb-3">Daily Turn-Up</h2>

                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-slate-300 min-w-[600px]">
                                    <thead>
                                        <tr class="bg-slate-100">
                                            <th class="border border-slate-300 px-3 py-3 text-left">Staff</th>
                                            <th class="border border-slate-300 px-3 py-3 text-right">Paid</th>
                                            <th class="border border-slate-300 px-3 py-3 text-right">Total</th>
                                            <th class="border border-slate-300 px-3 py-3 text-right">New Client</th>
                                            <th class="border border-slate-300 px-3 py-3 text-right">Amount (UGX)</th>
                                            <th class="border border-slate-300 px-3 py-3 text-right">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody id="turnup-body" class="text-sm"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- MODALS -->
    <?php
    include "components/transaction_modal.php";
    include "components/field_officer_modals.php";
    include "components/client_modals.php";
    include "components/transaction_category_modal.php";
    include "components/dashboard_modals.php";
    include "components/manage_staff_modals.php";
    include "components/manage_user_modals.php";
    ?>

    <!-- BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- APPLICATION SCRIPTS -->
    <script src="assets/js/toast.js"></script>
    <script src="assets/js/index.js"></script>
    <!-- field officer management -->
    <script src="assets/js/manage_field_officer.js"></script>
    <script src="assets/js/manage_field_officer_clients.js"></script>
    <!-- client management -->
    <script src="assets/js/manage_client.js"></script>
    <script src="assets/js/transaction.js"></script>
    <!-- category management -->
    <script src="assets/js/manage_transaction_category.js"></script>
    <!-- cashier collections -->
    <script src="assets/js/cashier_collections.js"></script>
    <!-- staff management -->
    <script src="assets/js/manage_staff.js"></script>
    <!-- user management -->
    <script src="assets/js/manage_user.js"></script>

</body>

</html>