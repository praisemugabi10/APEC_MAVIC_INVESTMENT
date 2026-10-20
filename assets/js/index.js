const DASHBOARD_PAYMENT_API = 'api/manage_client.php';


/*
|--------------------------------------------------------------------------
| helper functions
|--------------------------------------------------------------------------
*/// --- escape html ---
function escapeHTML(str) {
  if (str === null || str === undefined) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

// --- decode html ---
function decodeHTML(str) {
  if (!str) return "";
  const txt = document.createElement("textarea");
  let decoded = str;
  let prev;
  do {
    prev = decoded;
    txt.innerHTML = decoded;
    decoded = txt.value;
  } while (decoded !== prev);
  return decoded;
}

/*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
*/
function handleLogoClick() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth >= 1024) {
        if (sidebar.classList.contains('collapsed')) {
            sidebar.classList.add('expanding');
            sidebar.classList.remove('collapsed');
            setTimeout(() => {
                sidebar.classList.remove('expanding');
            }, 50);
            localStorage.setItem('sidebar_pref', 'expanded');
        } else {
            sidebar.classList.add('collapsed');
            localStorage.setItem('sidebar_pref', 'collapsed');
        }
    }
}

/*
|--------------------------------------------------------------------------
| MOBILE SIDEBAR
|--------------------------------------------------------------------------
*/
function toggleMobileMenu() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('-translate-x-full');
    sidebar.classList.remove('collapsed');
}


/*
|--------------------------------------------------------------------------
| SIDEBAR NAVIGATION
|--------------------------------------------------------------------------
*/
function navigateFromSidebar(section) {
    router(section);
    if (window.innerWidth < 1024) {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('collapsed');
    }
}


/*
|--------------------------------------------------------------------------
| TRANSACTION SEARCH CLEAR BUTTON
|--------------------------------------------------------------------------
*/
function toggleClearBtn(input) {
    const btn = document.getElementById('clear-search');
    if (!btn) {
        return;
    }
    if (input.value.length > 0) {
        btn.classList.remove('hidden');
    } else {
        btn.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const clearSearch = document.getElementById('clear-search');
    if (!clearSearch) {
        return;
    }
    clearSearch.addEventListener('click', function () {
        const input = document.getElementById('transaction-search');
        if (!input) {
            return;
        }
        input.value = '';
        this.classList.add('hidden');
        input.focus();
        /*
         * Transaction search/filter logic
         * can be added here later.
         */
    });
});


/*
|--------------------------------------------------------------------------
| INITIALIZE DESKTOP SIDEBAR PREFERENCE
|--------------------------------------------------------------------------
*/
window.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) {
        return;
    }
    if (window.innerWidth >= 1024) {
        const pref = localStorage.getItem('sidebar_pref');
        /*
         * Default is collapsed.
         */
        if (pref === 'expanded') {
            sidebar.classList.remove('collapsed');
        } else {
            sidebar.classList.add('collapsed');
        }
    } else {
        /*
         * Mobile always uses the full sidebar.
         */
        sidebar.classList.remove('collapsed');
        sidebar.classList.add('-translate-x-full');
    }
});


/*
|--------------------------------------------------------------------------
| KEEP MOBILE SIDEBAR FULL WHEN RESIZING
|--------------------------------------------------------------------------
*/
window.addEventListener('resize', () => {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) {
        return;
    }
    if (window.innerWidth < 1024) {
        sidebar.classList.remove('collapsed');
    }
});


/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/
function router(path) {
    /*
    |--------------------------------------------------------------------------
    | MOBILE SIDEBAR
    |--------------------------------------------------------------------------
    */
    if (window.innerWidth < 1024) {
        toggleSidebar(false);
    }
    /*
    |--------------------------------------------------------------------------
    | UPDATE PAGE VISIBILITY
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('.page-section').forEach(section => {
        section.classList.remove('active');
    });
    const targetPage = document.getElementById(path);
    if (targetPage) {
        targetPage.classList.add('active');
    }
    /*
    |--------------------------------------------------------------------------
    | UPDATE NAVIGATION UI
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        const onclick = link.getAttribute('onclick');
        if (onclick && onclick.includes(`'${path}'`)) {
            link.classList.add('active');
        }
    });
    /*
    |--------------------------------------------------------------------------
    | UPDATE HEADER TITLE
    |--------------------------------------------------------------------------
    */
    const currentTitle = document.getElementById('current-title');
    if (currentTitle) {
        currentTitle.innerText = path.replace('-', ' ');
    }
    /*
    |--------------------------------------------------------------------------
    | TRIGGER DATA LOADS
    |--------------------------------------------------------------------------
    */
    if (path === 'dashboard') {
        loadDashboardData();
    }
    if (path === 'cashier-collections') {
        loadCashierCollections();
    }
    if (path === 'officers') {
        listFieldOfficers();
    }
    if (path === 'clients') {
        listClients();
    }
    if (path === 'staff-management') {
        loadStaff();
    }
    if (path === 'user-management') {
        loadUsers();
    }
    if (path === 'reports') {
        renderReport();
    }
}


/*
|--------------------------------------------------------------------------
| DASHBOARD DATA
|--------------------------------------------------------------------------
*/
async function loadDashboardData() {
    initializeDashboardTransactionDatePicker();

    await updateTransactionBalance();
    await renderTransactions();
    await loadDashboardPaymentPercentage();
}


/*
|--------------------------------------------------------------------------
| DASHBOARD PAYMENT PERCENTAGE
|--------------------------------------------------------------------------
*/
async function loadDashboardPaymentPercentage() {
    const dateInput = document.getElementById('dash-payment-date');
    const percentageElement = document.getElementById('dash-payment-percentage');
    const summaryElement = document.getElementById('dash-payment-percentage-summary');
    const dateElement = document.getElementById('dash-payment-percentage-date');
    if (!dateInput) {
        return;
    }
    const selectedDate = dateInput.value || getDashboardTodayDate();
    dateInput.value = selectedDate;
    if (percentageElement) {
        percentageElement.innerText = '...';
    }
    if (summaryElement) {
        summaryElement.innerText = 'Calculating...';
    }
    if (dateElement) {
        dateElement.innerText = formatDashboardPaymentDate(selectedDate);
    }
    const formData = new FormData();
    formData.append('action', 'overall_payment_day_percentage');
    formData.append('selected_date', selectedDate);
    try {
        const response = await fetch(DASHBOARD_PAYMENT_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Failed to load daily payment percentage.');
        }
        const data = result.data || {};
        const paidClientCount = Number(data.paid_count || 0);
        const expectedClientCount = Number(data.total_expected_count || 0);
        const dailyPaymentPercentage = expectedClientCount > 0 ? (paidClientCount / expectedClientCount) * 100 : 0;
        const formattedPercentage = Number.isInteger(dailyPaymentPercentage) ? dailyPaymentPercentage : dailyPaymentPercentage.toFixed(2);
        if (percentageElement) {
            percentageElement.innerText = `${formattedPercentage}%`;
        }
        if (summaryElement) {
            summaryElement.innerText = `${paidClientCount} of ${expectedClientCount} paid`;
        }
        if (dateElement) {
            dateElement.innerText = formatDashboardPaymentDate(selectedDate);
        }
    } catch (error) {
        console.error('Dashboard payment percentage error:', error);
        if (percentageElement) {
            percentageElement.innerText = '0%';
        }
        if (summaryElement) {
            summaryElement.innerText = 'Unable to calculate';
        }
        showToast('Failed to load daily payment percentage.', 'error');
    }
}


/*
|--------------------------------------------------------------------------
| DASHBOARD PAYMENT DATE - TODAY
|--------------------------------------------------------------------------
*/
function getDashboardTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/*
|--------------------------------------------------------------------------
| SET DASHBOARD PAYMENT DATE TO TODAY
|--------------------------------------------------------------------------
*/
function setDashboardPaymentDateToday() {
    const dateInput = document.getElementById('dash-payment-date');
    if (!dateInput) {
        return;
    }
    dateInput.value = getDashboardTodayDate();
    loadDashboardPaymentPercentage();
}


/*
|--------------------------------------------------------------------------
| FORMAT DASHBOARD PAYMENT DATE
|--------------------------------------------------------------------------
*/
function formatDashboardPaymentDate(paymentDate) {
    const dateObject = new Date(`${paymentDate}T00:00:00`);
    if (Number.isNaN(dateObject.getTime())) {
        return paymentDate;
    }
    return dateObject.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}


/*
|--------------------------------------------------------------------------
| FIELD OFFICER PAYMENT BREAKDOWN
|--------------------------------------------------------------------------
*/
async function openDashboardOfficerBreakdown() {
    const selectedDateInput = document.getElementById('dash-payment-date');
    const selectedDate = selectedDateInput?.value || getDashboardTodayDate();
    const container = document.getElementById('dashboard-officer-breakdown-container');
    const dateElement = document.getElementById('dashboard-officer-breakdown-date');
    if (!container) {
        return;
    }
    if (dateElement) {
        dateElement.innerText = formatDashboardPaymentDate(selectedDate);
    }
    container.innerHTML = `
        <div class="flex justify-center items-center py-12 text-slate-500">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            <span>Loading officer breakdown...</span>
        </div>
    `;
    const modalElement = document.getElementById('modal-dashboard-officer-breakdown');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
    const formData = new FormData();
    formData.append('action', 'overall_officer_payment_breakdown');
    formData.append('selected_date', selectedDate);
    try {
        const response = await fetch(DASHBOARD_PAYMENT_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Failed to load officer breakdown.');
        }
        const officers = result.data?.officers || [];
        renderDashboardOfficerBreakdown(officers);
    } catch (error) {
        console.error('Dashboard officer breakdown error:', error);
        container.innerHTML = `
            <div class="text-center py-12 text-red-500 font-bold">
                Failed to load officer breakdown
            </div>
        `;
        showToast('Failed to load officer breakdown.', 'error');
    }
}

function renderDashboardOfficerBreakdown(officers) {
    const container = document.getElementById('dashboard-officer-breakdown-container');
    if (!container) {
        return;
    }
    if (!officers.length) {
        container.innerHTML = `
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="fas fa-users text-slate-400 text-2xl"></i>
                </div>
                <p class="text-slate-500 font-bold mb-1">
                    No field officers found
                </p>
                <p class="text-sm text-slate-400 mb-0">
                    There are no field officers to display.
                </p>
            </div>
        `;
        return;
    }
    let html = `
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr class="text-[10px] uppercase text-slate-400">
                        <th class="border-0 px-2 py-3">
                            Field Officer
                        </th>
                        <th class="border-0 px-2 py-3 text-end">
                            Payment %
                        </th>
                    </tr>
                </thead>
                <tbody>
    `;
    officers.forEach(officer => {
        const percentage = Number(officer.payment_percentage || 0);
        const formattedPercentage = Number.isInteger(percentage) ? percentage : percentage.toFixed(2);
        html += `
            <tr class="border-bottom">
                <td class="px-2 py-3">
                    <div class="font-black text-slate-800 text-sm">
                        ${officer.officer_name || '-'}
                    </div>
                </td>
                <td class="px-2 py-3 text-end">
                    <span class="font-black text-blue-600 text-sm">
                        ${formattedPercentage}%
                    </span>
                </td>
            </tr>
        `;
    });
    html += `
                </tbody>
            </table>
        </div>
    `;
    container.innerHTML = html;
}

/*
|--------------------------------------------------------------------------
| UPDATE TRANSACTION BALANCE
|--------------------------------------------------------------------------
|
| Endpoint:
|
| api/transactions/transaction_balance.php
|
| Updates:
|
| #dash-balance
| #dash-in-total
| #dash-out-total
|
*/


/**
 * Get today's date in Uganda time as YYYY-MM-DD.
 */
function getUgandaTodayDate() {
    const parts = new Intl.DateTimeFormat('en-GB', {
        timeZone: 'Africa/Kampala',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).formatToParts(new Date());

    const dateParts = {};

    parts.forEach(({ type, value }) => {
        dateParts[type] = value;
    });

    return `${dateParts.year}-${dateParts.month}-${dateParts.day}`;
}

/**
 * Initialize the dashboard transaction date picker.
 */
function initializeDashboardTransactionDatePicker() {
    const dateInput = document.getElementById(
        'dashboard-transaction-date'
    );

    const todayButton = document.getElementById(
        'dashboard-transaction-today'
    );

    if (!dateInput || !todayButton) {
        console.warn('Dashboard transaction date controls not found.');
        return;
    }

    const today = getUgandaTodayDate();

    dateInput.max = today;

    if (!dateInput.value || dateInput.value > today) {
        dateInput.value = today;
    }

    dateInput.onchange = function () {
        updateTransactionBalance();
    };

    todayButton.onclick = function () {
        dateInput.max = getUgandaTodayDate();
        dateInput.value = getUgandaTodayDate();

        updateTransactionBalance();
    };
}

/**
 * Fetch transaction totals for the selected date.
 * Balance remains cumulative across all transactions.
 */
async function updateTransactionBalance() {
    const balanceElement = document.getElementById('dash-balance');
    const cashInElement = document.getElementById('dash-in-total');
    const cashOutElement = document.getElementById('dash-out-total');

    const dateInput = document.getElementById(
        'dashboard-transaction-date'
    );

    const today = getUgandaTodayDate();

    let selectedDate = dateInput?.value || today;

    // Always enforce today's maximum date.
    if (dateInput) {
        dateInput.max = today;
    }

    // Default to today and reject future dates.
    if (!selectedDate || selectedDate > today) {
        selectedDate = today;

        if (dateInput) {
            dateInput.value = today;
        }
    }

    const spinner = `
        <span
            class="spinner-border spinner-border-sm"
            style="width: 1.5rem; height: 1.5rem; border-width: 3px;"
            role="status"
            aria-label="Loading">
        </span>
    `;

    if (balanceElement) {
        balanceElement.innerHTML = spinner;
    }

    if (cashInElement) {
        cashInElement.innerHTML = spinner;
    }

    if (cashOutElement) {
        cashOutElement.innerHTML = spinner;
    }

    try {
        const url = new URL(
            'api/transactions/transaction_balance.php',
            window.location.href
        );

        url.searchParams.set('date', selectedDate);

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            cache: 'no-store'
        });

        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(
                result.message || 'Failed to load transaction balance.'
            );
        }

        const balanceData = result.data || {};

        const totalCashIn = Number(
            balanceData.total_cash_in || 0
        );

        const totalCashOut = Number(
            balanceData.total_cash_out || 0
        );

        const balance = Number(
            balanceData.balance || 0
        );

        // Update cumulative balance.
        if (balanceElement) {
            balanceElement.innerText =
                `UGX ${formatTransactionAmount(balance)}`;
        }

        // Update Cash In for the selected date.
        if (cashInElement) {
            cashInElement.innerText =
                `UGX ${formatTransactionAmount(totalCashIn)}`;
        }

        // Update Cash Out for the selected date.
        if (cashOutElement) {
            cashOutElement.innerText =
                `UGX ${formatTransactionAmount(totalCashOut)}`;
        }

    } catch (error) {
        console.error('Error loading transaction balance:', error);

        showToast('Failed to load transaction balance.', 'error');
    }
}

// Initialize the date picker and load today's figures.



/*
|--------------------------------------------------------------------------
| FORMAT TRANSACTION AMOUNT
|--------------------------------------------------------------------------
*/
function formatTransactionAmount(amount) {
    return Number(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}


/*
|--------------------------------------------------------------------------
| REPORT
|--------------------------------------------------------------------------
|
*/
function getLocalDate() {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

function formatUGX(amount) {
    return `UGX ${Number(amount || 0).toLocaleString('en-UG', {
        maximumFractionDigits: 0
    })}`;
}

function escapeReportText(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[character]);
}

async function renderReport() {
    const expensesBody = document.getElementById('expenses-body');
    const expensesTotal = document.getElementById('expenses-total');
    const cashOutsBody = document.getElementById('cash-outs-body');
    const datePicker = document.getElementById('report-date-picker');
    const reportDate = document.getElementById('report-date');
    const reportTitle = document.getElementById('report-title');
    const reportBody = document.getElementById('report-body');
    const turnupBody = document.getElementById('turnup-body');
    const generatedBy = document.getElementById('report-generated-by');

    if (!datePicker || !reportDate || !reportTitle || !reportBody || !cashOutsBody ||  !expensesBody || !expensesTotal || !turnupBody) {
        return;
    }
    if (!datePicker.value) {
        datePicker.value = getLocalDate();
    }

    const selectedDate = datePicker.value;

    const formattedDate = new Date(
        `${selectedDate}T12:00:00`
    ).toLocaleDateString('en-GB', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    reportDate.textContent = formattedDate;
    // reportTitle.textContent = `Report for ${formattedDate}`;

    reportBody.innerHTML = `
        <tr>
            <td colspan="4" class="px-4 py-6 text-center text-slate-500">
                Loading financial figures...
            </td>
        </tr>
    `;
    cashOutsBody.innerHTML = `
        <tr>
            <td colspan="3" class="px-4 py-6 text-center text-slate-500">
                Loading cash outs...
            </td>
        </tr>
    `;
    expensesBody.innerHTML = `
        <tr>
            <td colspan="3" class="px-4 py-6 text-center text-slate-500">
                Loading expenses...
            </td>
        </tr>
    `;
    expensesTotal.textContent = 'Loading...';
    turnupBody.innerHTML = `
        <tr>
            <td colspan="6" class="px-4 py-6 text-center text-slate-500">
                Loading turn-up figures...
            </td>
        </tr>
    `;

    try {
        const response = await fetch(
            `api/transactions/daily_cash_report.php?date=${encodeURIComponent(selectedDate)}`,
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Failed to load report.');
        }

        const financial = result.data.financial;
        generatedBy.textContent = result.data.generated_by
            ? `Generated by: ${result.data.generated_by}`
            : '';

        const rows = [
            {
                description: 'Cash at Hand',
                amount: financial.cash_at_hand
            },
            {
                description: 'Cash In',
                amount: financial.cash_in
            },
            {
                total: financial.total_available,
                isTotal: true
            },
            {
                description: 'Cash Out',
                amount: financial.cash_out
            },
            {
                description: 'Banking',
                amount: financial.banking
            },
            {
                description: 'Expenses',
                amount: financial.expenses
            },
            {
                total: financial.total_deductions,
                isTotal: true
            },
            {
                description: 'Balance',
                balance: financial.closing_balance,
                isBalance: true
            }
        ];

        // report table
        reportBody.innerHTML = rows.map(row => `
            <tr class="${row.isTotal || row.isBalance ? 'font-black bg-slate-50' : ''}">
                <td class="border border-slate-300 px-4 py-3">
                    ${row.description || ''}
                </td>
                <td class="border border-slate-300 px-4 py-3 text-right whitespace-nowrap">
                    ${row.amount !== undefined ? formatUGX(row.amount) : ''}
                </td>
                <td class="border border-slate-300 px-4 py-3 text-right whitespace-nowrap">
                    ${row.total !== undefined ? formatUGX(row.total) : ''}
                </td>
                <td class="border border-slate-300 px-4 py-3 text-right whitespace-nowrap">
                    ${row.balance !== undefined ? formatUGX(row.balance) : ''}
                </td>
            </tr>
        `).join('');

        // cash outs - loans given to clients
        const cashOuts = result.data.cash_outs || [];

        cashOutsBody.innerHTML = cashOuts.length
            ? cashOuts.map(row => `
                <tr>
                    <td class="border border-slate-300 px-4 py-3">
                        ${row.number}
                    </td>
                    <td class="border border-slate-300 px-4 py-3">
                        ${escapeReportText(row.client_name)}
                    </td>
                    <td class="border border-slate-300 px-4 py-3 text-right whitespace-nowrap">
                        ${formatUGX(row.amount_taken)}
                    </td>
                </tr>
            `).join('')
            : `
                <tr>
                    <td colspan="3" class="border border-slate-300 px-4 py-6 text-center text-slate-500">
                        No loans were issued on this date.
                    </td>
                </tr>
            `;


        // Expenses - office expenses for the selected date
        const expenses = result.data.expenses_list?.items || [];
        const totalExpenses = Number(result.data.expenses_list?.total || 0);

        expensesBody.innerHTML = expenses.length
            ? expenses.map(row => `
                <tr>
                    <td class="border border-slate-300 px-4 py-3">
                        ${row.number}
                    </td>
                    <td class="border border-slate-300 px-4 py-3">
                        ${row.description?.trim()
                            ? escapeReportText(row.description.trim())
                            : '<em>No description provided</em>'}                    
                    </td>
                    <td class="border border-slate-300 px-4 py-3 text-right whitespace-nowrap">
                        ${formatUGX(row.amount)}
                    </td>
                </tr>
            `).join('')
            : `
                <tr>
                    <td colspan="3" class="border border-slate-300 px-4 py-6 text-center text-slate-500">
                        No expenses recorded on this date.
                    </td>
                </tr>
            `;

        expensesTotal.textContent = formatUGX(totalExpenses);

        // turn up - payments made by clients
        turnupBody.innerHTML = result.data.turn_up.map(row => `
            <tr class="${row.name === 'Total' ? 'font-black bg-slate-50' : ''}">
                <td class="border border-slate-300 px-3 py-3">
                    ${escapeReportText(row.name)}
                </td>
                <td class="border border-slate-300 px-3 py-3 text-right">
                    ${
                        Number(row.office_paid) > 0
                            ? `<span
                                class="inline-flex items-center justify-center
                                    min-w-7 rounded-md bg-amber-100 px-2 py-1
                                    font-semibold text-amber-800 cursor-help"
                                title="${Number(row.office_paid)} ${
                                    Number(row.office_paid) === 1 ? 'client' : 'clients'
                                } paid at the office"
                            >${row.paid ?? 0}</span>`
                            : (row.paid ?? 0)
                    }
                </td>
                <td class="border border-slate-300 px-3 py-3 text-right">
                    ${row.total ?? '—'}
                </td>
                <td class="border border-slate-300 px-3 py-3 text-right">
                    ${row.new_clients ?? '—'}
                </td>
                <td class="border border-slate-300 px-3 py-3 text-right whitespace-nowrap">
                    ${formatUGX(row.amount_collected)}
                </td>
                <td class="border border-slate-300 px-3 py-3 text-right">
                    ${row.percentage == null ? '—' : `${Number(row.percentage).toFixed(2)}%`}
                </td>
            </tr>
        `).join('');

    } catch (error) {
        console.error('Daily report error:', error);

        reportBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-red-600">
                    ${escapeReportText(error.message || 'Unable to load report.')}
                </td>
            </tr>
        `;

        cashOutsBody.innerHTML = `
            <tr>
                <td colspan="3" class="px-4 py-6 text-center text-red-600">
                    Cash-out data could not be loaded.
                </td>
            </tr>
        `;

        expensesBody.innerHTML = `
            <tr>
                <td colspan="3" class="px-4 py-6 text-center text-red-600">
                    Expense data could not be loaded.
                </td>
            </tr>
        `;
        expensesTotal.textContent = '—';

        turnupBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-red-600">
                    Report data could not be loaded.
                </td>
            </tr>
        `;

    }
}

/*
|--------------------------------------------------------------------------
| MODALS
|--------------------------------------------------------------------------
*/
function openModal(id) {
    const modalElement = document.getElementById(id);
    if (!modalElement) {
        return;
    }
    const modalInstance = new bootstrap.Modal(modalElement);
    modalInstance.show();
}

/*
|--------------------------------------------------------------------------
| CLOSE BOOTSTRAP MODAL
|--------------------------------------------------------------------------
*/
function closeModal(id) {
    const modalElement = document.getElementById(id);
    if (!modalElement) {
        return;
    }
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.hide();
    }
}

/*
|--------------------------------------------------------------------------
| SIDEBAR TOGGLE
|--------------------------------------------------------------------------
*/
function toggleSidebar(force) {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) {
        return;
    }
    /*
    |--------------------------------------------------------------------------
    | FORCE HIDE
    |--------------------------------------------------------------------------
    */
    if (force === false) {
        sidebar.classList.add('-translate-x-full');
    }
    /*
    |--------------------------------------------------------------------------
    | FORCE SHOW
    |--------------------------------------------------------------------------
    */
    else if (force === true) {
        sidebar.classList.remove('-translate-x-full');
    }
    /*
    |--------------------------------------------------------------------------
    | TOGGLE
    |--------------------------------------------------------------------------
    */
    else {
        sidebar.classList.toggle('-translate-x-full');
    }
}

/*
|--------------------------------------------------------------------------
| ACCORDION
|--------------------------------------------------------------------------
*/
function toggleAccordion(id) {
    const element = document.getElementById(id);
    if (!element) {
        return;
    }
    element.classList.toggle('hidden');
}

/*
|--------------------------------------------------------------------------
| CLOCK
|--------------------------------------------------------------------------
*/
function initClock() {
    const updateClock = () => {
        const clock = document.getElementById('clock');
        if (!clock) {
            return;
        }
        const now = new Date();
        clock.innerText = now.toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    };
    updateClock();
    setInterval(updateClock, 1000);
}

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
async function logout() {
    try {
        const response = await fetch('api/logout.php', {
            method: 'POST'
        });
        const data = await response.json();
        if (data.status === 'success') {
            window.location.href = 'auth/login.html';
        }
    } catch (error) {
        console.error('Logout error:', error);
    }
}

// Date changer listener for changing the total percentage of payments made on the dashboard
document.getElementById('dash-payment-date')?.addEventListener('change', function () {
    loadDashboardPaymentPercentage();
});

/*
|--------------------------------------------------------------------------
| INITIALIZE APPLICATION
|--------------------------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', function () {
    initClock();
    /*
     * Load dashboard balance when the
     * application starts on the dashboard.
     */
    const dashboard = document.getElementById('dashboard');
    if (dashboard && dashboard.classList.contains('active')) {
        loadDashboardData();
    }
});