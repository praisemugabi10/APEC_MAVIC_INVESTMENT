// this file is used to manage the clients of a field officer
// - recording payment and viewing payment history
/* Load and display clients assigned to a field officer */
async function viewOfficerClients(officerId, officerName) {
    const container = document.getElementById('officer-clients-container');
    document.getElementById('clients-officer-id').value = officerId;
    document.getElementById('clients-officer-name').innerText = officerName;

    container.innerHTML = `
        <div class="flex justify-center items-center py-12 text-slate-500">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            <span>Loading clients...</span>
        </div>
    `;

    const modalElement = document.getElementById('modal-officer-clients');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();

    const formData = new FormData();
    formData.append('action', 'clients');
    formData.append('officer_id', officerId);

    try {
        const response = await fetch(FIELD_OFFICER_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            container.innerHTML = `
                <div class="text-center py-12 text-red-500 font-bold">
                    ${result.message}
                </div>
            `;
            showToast(result.message, 'error');
            return;
        }

        const activeClients = result.data.active_clients || [];
        const clearedClients = result.data.cleared_clients || [];

        window.officerClientsData = {
            active: activeClients,
            cleared: clearedClients,
            officerId: officerId,
            officerName: officerName
        };

        window.currentOfficerClientTab = 'active';
        renderOfficerClientsTab();
    } catch (error) {
        console.error('Fetch clients error:', error);

        container.innerHTML = `
            <div class="text-center py-12 text-red-500 font-bold">
                Failed to load clients
            </div>
        `;

        showToast('Failed to load clients', 'error');
    }
}

/* Render the active or cleared clients tab for a field officer */
function renderOfficerClientsTab(clientsToRender = null) {
    const container = document.getElementById('officer-clients-container');
    const data = window.officerClientsData;

    if (!data) {
        return;
    }

    const activeClients = data.active || [];
    const clearedClients = data.cleared || [];
    const currentTab = window.currentOfficerClientTab || 'active';
    const clients = clientsToRender !== null
        ? clientsToRender
        : (currentTab === 'active' ? activeClients : clearedClients);
    const tabTitle = currentTab === 'active'
        ? 'Active Clients'
        : 'Cleared Clients';

    let html = `
        <div class="mb-4">
            <div class="flex flex-wrap items-center gap-2 border-bottom pb-2">
                <button
                    type="button"
                    onclick="switchOfficerClientTab('active')"
                    class="btn py-2 px-4 rounded-xl font-black text-sm shadow-none border-0 ${currentTab === 'active' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500'}">
                    <i class="fas fa-users me-2"></i>
                    Active Clients
                    <span class="ms-1 px-2 py-0.5 rounded-full text-[10px] ${currentTab === 'active' ? 'bg-white text-blue-600' : 'bg-slate-200 text-slate-600'}">${activeClients.length}</span>
                </button>
                <button
                    type="button"
                    onclick="switchOfficerClientTab('cleared')"
                    class="btn py-2 px-4 rounded-xl font-black text-sm shadow-none border-0 ${currentTab === 'cleared' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500'}">
                    <i class="fas fa-circle-check me-2"></i>
                    Cleared
                    <span class="ms-1 px-2 py-0.5 rounded-full text-[10px] ${currentTab === 'cleared' ? 'bg-white text-emerald-600' : 'bg-slate-200 text-slate-600'}">${clearedClients.length}</span>
                </button>
            </div>
        </div>

        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <span class="text-[10px] font-black uppercase text-slate-400">${tabTitle}</span>
                <div id="clients-count" class="text-xl font-black text-slate-900">${clients.length}</div>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2 w-full sm:w-auto">
                <button
                    type="button"
                    onclick="viewPaymentDayBreakdown('${data.officerId}', '${data.officerName.replace(/'/g, "\\'")}')"
                    class="btn py-2 px-3 rounded-xl font-black text-blue-600 bg-blue-50 border-0 shadow-none">
                    <i class="fas fa-calendar-check me-2"></i>Daily Payments
                </button>
                <div class="relative w-full sm:w-80">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input
                        type="text"
                        id="officer-client-search"
                        oninput="searchOfficerClients()"
                        placeholder="Search client by name..."
                        class="form-control ps-5 pe-5 py-2 rounded-xl font-semibold border-2 border-slate-100 bg-slate-50 shadow-none">
                    <button
                        type="button"
                        id="clear-officer-client-search"
                        onclick="clearOfficerClientSearch()"
                        class="hidden absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-slate-200 text-slate-500 hover:bg-slate-300 hover:text-slate-700 border-0 items-center justify-center">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    `;

    if (clients.length === 0) {
        html += `
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="fas ${currentTab === 'active' ? 'fa-users' : 'fa-circle-check'} text-slate-400 text-2xl"></i>
                </div>
                <p class="text-slate-500 font-bold mb-0">
                    ${currentTab === 'active'
                        ? 'No active clients found for this officer.'
                        : 'No cleared clients found for this officer.'}
                </p>
            </div>
        `;

        container.innerHTML = html;
        return;
    }

    html += `
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr class="border-bottom">
                        <th class="px-2 py-2 text-[10px] font-black uppercase text-slate-400">Client Name</th>
                        <th class="px-2 py-2 text-[10px] font-black uppercase text-slate-400">Phone Number</th>
                        <th class="px-2 py-2 text-[10px] font-black uppercase text-slate-400 text-end">Amount Taken</th>
                        <th class="px-2 py-2 text-[10px] font-black uppercase text-slate-400 text-end">Amount Expected</th>
                        <th class="px-2 py-2 text-[10px] font-black uppercase text-slate-400 text-end">Amount Paid</th>
                        <th class="px-2 py-2 text-[10px] font-black uppercase text-slate-400 text-end">Balance</th>
                        <th class="px-2 py-2 text-[10px] font-black uppercase text-slate-400 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;

    clients.forEach(client => {
        const amountTaken = parseFloat(client.principal_amount || 0);
        const totalRepayable = parseFloat(client.total_repayable || 0);
        const amountPaid = parseFloat(client.amount_paid || 0);
        const balance = parseFloat(client.balance || 0);
        const hasLoan = !!client.loan_id;
        const isCleared = client.loan_status === 'cleared';

        html += `
            <tr class="border-bottom">
                <td class="px-2 py-2">
                    <div class="font-black text-slate-800 text-sm">${client.first_name} ${client.last_name}</div>
                </td>
                <td class="px-2 py-2">
                    <div class="font-semibold text-slate-600 text-sm">${client.phone_primary}</div>
                    ${client.phone_secondary
                        ? `<div class="text-[11px] text-slate-400">${client.phone_secondary}</div>`
                        : ''}
                </td>
                <td class="px-2 py-2 text-end">
                    <span class="font-bold text-slate-700 text-sm">${hasLoan ? amountTaken.toLocaleString() : '-'}</span>
                </td>
                <td class="px-2 py-2 text-end">
                    <span class="font-bold text-slate-700 text-sm">${hasLoan ? totalRepayable.toLocaleString() : '-'}</span>
                </td>
                <td class="px-2 py-2 text-end">
                    <span class="font-bold text-slate-700 text-sm">${hasLoan ? amountPaid.toLocaleString() : '-'}</span>
                </td>
                <td class="px-2 py-2 text-end">
                    <span class="font-black text-sm ${balance > 0 ? 'text-red-600' : 'text-green-600'}">${hasLoan ? balance.toLocaleString() : '-'}</span>
                </td>
                <td class="px-2 py-2">
                    ${hasLoan ? `
                        <div class="flex items-center justify-center gap-2">
                            ${!isCleared ? `
                                <button
                                    type="button"
                                    onclick='openClientPayment(${JSON.stringify(client)})'
                                    title="Record Payment"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors font-bold text-xs">
                                    <i class="fas fa-money-bill-wave text-sm"></i>
                                    <span class="hidden sm:inline">Cash In</span>
                                </button>
                            ` : ''}
                            ${isCleared ? `
                                <button
                                    type="button"
                                    onclick='openRepeatLoan(${JSON.stringify(client)})'
                                    title="Issue New Loan"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors font-bold text-xs">
                                    <i class="fas fa-hand-holding-dollar text-sm"></i>
                                    <span class="hidden sm:inline">New Loan</span>
                                </button>
                            ` : ''}
                            <button
                                type="button"
                                onclick='viewClientHistory(${JSON.stringify(client)})'
                                title="View Payment History"
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors font-bold text-xs">
                                <i class="fas fa-clock-rotate-left text-sm"></i>
                                <span class="hidden sm:inline">History</span>
                            </button>
                        </div>
                    ` : `
                        <span class="text-[11px] text-slate-400 font-bold">No loan</span>
                    `}
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

function switchOfficerClientTab(tab) {
    window.currentOfficerClientTab = tab;
    renderOfficerClientsTab();
}

/* Open the new-loan form for a client with a cleared loan */
function openRepeatLoan(client) {
    if (client.loan_status !== 'cleared') {
        showToast(
            'Only clients with a cleared loan can take a new loan.',
            'error'
        );
        return;
    }

    resetClientForm();

    document.getElementById('cl-existing-client-id').value = client.client_id;
    document.getElementById('cl-fname').value = client.first_name || '';
    document.getElementById('cl-lname').value = client.last_name || '';
    document.getElementById('cl-phone-1').value = client.phone_primary || '';
    document.getElementById('cl-phone-2').value = client.phone_secondary || '';
    document.getElementById('cl-location').value = client.location || '';
    document.getElementById('cl-officer-id').value = client.officer_id || '';
    document.getElementById('cl-g-fname').value = client.g_first_name || '';
    document.getElementById('cl-g-lname').value = client.g_last_name || '';
    document.getElementById('cl-g-phone').value = client.g_phone || '';

    document.getElementById('client-modal-title').textContent =
        `New Loan — ${client.first_name} ${client.last_name}`;
    document.getElementById('btn-save-client').textContent = 'Issue New Loan';

    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('modal-client')
    ).show();
}

/* Refresh officer clients when their modal is open */
function refreshOfficerClientsIfOpen() {
    const modalElement = document.getElementById('modal-officer-clients');
    const officerId = document.getElementById('clients-officer-id').value;
    const officerName = document.getElementById(
        'clients-officer-name'
    ).innerText;

    if (modalElement.classList.contains('show') && officerId && officerName) {
        viewOfficerClients(officerId, officerName);
    }
}

/* Filter officer clients by name and update the visible count */
function searchOfficerClients() {
    const searchInput = document.getElementById('officer-client-search');
    const clearButton = document.getElementById('clear-officer-client-search');

    if (!searchInput) {
        return;
    }

    const searchTerm = searchInput.value.trim().toLowerCase();

    if (clearButton) {
        clearButton.classList.toggle('hidden', !searchInput.value);
        clearButton.classList.toggle('flex', !!searchInput.value);
    }

    const rows = document.querySelectorAll(
        '#officer-clients-container tbody tr'
    );
    let visibleCount = 0;

    rows.forEach(row => {
        const clientName = row.querySelector('td:nth-child(1)')
            ?.innerText.toLowerCase() || '';
        const matches = clientName.includes(searchTerm);

        row.style.display = matches ? '' : 'none';

        if (matches) {
            visibleCount++;
        }
    });

    const countElement = document.getElementById('clients-count');

    if (countElement) {
        countElement.innerText = visibleCount;
    }

    let emptyMessage = document.getElementById('no-client-search-results');

    if (searchTerm && visibleCount === 0) {
        if (!emptyMessage) {
            emptyMessage = document.createElement('div');
            emptyMessage.id = 'no-client-search-results';
            emptyMessage.className = 'text-center py-12';
            document.getElementById(
                'officer-clients-container'
            ).appendChild(emptyMessage);
        }

        emptyMessage.innerHTML = `
            <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-3">
                <i class="fas fa-user-slash text-slate-400 text-xl"></i>
            </div>
            <p class="text-slate-500 font-bold mb-1">No client matches that name</p>
            <p class="text-sm text-slate-400 mb-0">
                No client found for "<span class="font-bold">${searchInput.value.trim()}</span>"
            </p>
        `;
    } else if (emptyMessage) {
        emptyMessage.remove();
    }
}

/* Clear the officer client search and restore all rows */
function clearOfficerClientSearch() {
    const searchInput = document.getElementById('officer-client-search');

    if (!searchInput) {
        return;
    }

    searchInput.value = '';
    searchOfficerClients();
    searchInput.focus();
}

/* Open the payment form for a client's active loan */
function openClientPayment(client) {
    if (!client.loan_id) {
        showToast('This client does not have an active loan.', 'error');
        return;
    }

    document.getElementById('payment-recording-mode').value = 'field_officer';
    document.getElementById('cashier-client-selection').classList.add('d-none');
    document.getElementById('payment-modal-title').innerText = 'Record Payment';
    document.getElementById('payment-client-id').value = client.client_id;
    document.getElementById('payment-loan-id').value = client.loan_id;
    document.getElementById('payment-client-name').innerText =
        `${client.first_name} ${client.last_name}`;
    document.getElementById('payment-total-repayable').innerText =
        parseFloat(client.total_repayable || 0).toLocaleString();
    document.getElementById('payment-current-balance').innerText =
        parseFloat(client.balance || 0).toLocaleString();
    document.getElementById('payment-amount').value = '';
    document.getElementById('payment-date').value = getTodayDate();

    document.getElementById('record-payment-btn').innerHTML = `
        <i class="fas fa-money-bill-wave me-2"></i>Record Payment
    `;

    const modal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('modal-client-payment')
    );

    modal.show();
}

/* Open the payment history modal for a client's loan */
function viewClientHistory(client) {
    if (!client.loan_id) {
        showToast('This client does not have an active loan.', 'error');
        return;
    }

    document.getElementById('history-client-id').value = client.client_id;
    document.getElementById('history-loan-id').value = client.loan_id;
    document.getElementById('history-client-name').innerText =
        `${client.first_name} ${client.last_name}`;

    const container = document.getElementById('client-history-container');

    container.innerHTML = `
        <div class="flex justify-center items-center py-12 text-slate-500">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            <span>Loading payment history...</span>
        </div>
    `;

    const modal = bootstrap.Modal.getOrCreateInstance(
        document.getElementById('modal-client-history')
    );

    modal.show();
    loadClientPaymentHistory(client);
}

/* Fetch and display payment history for a client's loan */
async function loadClientPaymentHistory(client) {
    const container = document.getElementById('client-history-container');
    const formData = new FormData();

    formData.append('action', 'payment_history');
    formData.append('client_id', client.client_id);
    formData.append('loan_id', client.loan_id);

    try {
        const response = await fetch(CLIENT_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            container.innerHTML = `
                <div class="text-center py-12 text-red-500 font-bold">
                    ${result.message}
                </div>
            `;

            showToast(result.message, 'error');
            return;
        }

        const loan = result.data.loan || {};
        const payments = result.data.payments || [];
        const principalAmount = parseFloat(
            loan.principal_amount || client.principal_amount || 0
        );
        const totalRepayable = parseFloat(
            loan.total_repayable || client.total_repayable || 0
        );
        const amountPaid = payments.reduce((total, payment) => {
            return total + parseFloat(payment.amount_paid || 0);
        }, 0);
        const balance = Math.max(totalRepayable - amountPaid, 0);

        let html = `
            <div class="bg-slate-50 rounded-2xl p-4 mb-4">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-[10px] font-black uppercase text-slate-400">
                            Principal
                        </div>
                        <div class="font-black text-slate-900">
                            ${principalAmount.toLocaleString()}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-[10px] font-black uppercase text-slate-400">
                            Repayable
                        </div>
                        <div class="font-black text-slate-900">
                            ${totalRepayable.toLocaleString()}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-[10px] font-black uppercase text-slate-400">
                            Paid
                        </div>
                        <div class="font-black text-green-600">
                            ${amountPaid.toLocaleString()}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-[10px] font-black uppercase text-slate-400">
                            Balance
                        </div>
                        <div class="font-black ${balance > 0 ? 'text-red-600' : 'text-green-600'}">
                            ${balance.toLocaleString()}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-[10px] font-black uppercase text-slate-400">
                            Date Taken
                        </div>
                        <div class="font-black text-blue-600">
                            ${loan.date_taken || '-'}
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (payments.length === 0) {
            html += `
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <i class="fas fa-receipt text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-bold mb-1">
                        No payment history yet
                    </p>
                    <p class="text-sm text-slate-400 mb-0">
                        No payments have been recorded for this loan.
                    </p>
                </div>
            `;
        } else {
            html += `
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-[10px] uppercase text-slate-400">
                                <th class="border-0">Date</th>
                                <th class="border-0">Description</th>
                                <th class="border-0 text-end">Amount</th>
                                <th class="border-0 text-end">Balance</th>
                                <th class="border-0 text-end">Arrias</th>
                                <th class="border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            payments.forEach(payment => {
                const paymentAmount = parseFloat(payment.amount_paid || 0);
                const paymentBalance = parseFloat(payment.balance_at_time || 0);
                const paymentEarliers = parseFloat(payment.earliers || 0);
                const paymentDate = payment.date_paid
                    ? payment.date_paid.split(' ')[0]
                    : '';

                html += `
                    <tr>
                        <td class="font-medium text-slate-600">
                            ${paymentDate || '-'}
                        </td>
                        <td class="font-bold text-slate-700">Payment</td>
                        <td class="font-black text-green-600 text-end">
                            ${paymentAmount.toLocaleString()}
                        </td>
                        <td class="font-black ${
                            paymentBalance > 0
                                ? 'text-red-600'
                                : 'text-green-600'
                        } text-end">
                            ${paymentBalance.toLocaleString()}
                        </td>
                        <td class="font-black ${
                            paymentEarliers > 0
                                ? 'text-red-600'
                                : 'text-green-600'
                        } text-end">
                            ${paymentEarliers.toLocaleString()}
                        </td>
                        <td class="text-end">
                            <button
                                type="button"
                                class="btn btn-sm btn-light text-blue-600 font-bold"
                                onclick="openEditClientPayment(
                                    '${payment.payment_id}',
                                    '${client.client_id}',
                                    '${client.loan_id}',
                                    '${paymentDate}',
                                    '${paymentAmount}',
                                    '${client.first_name} ${client.last_name}',
                                    '${payment.payment_created_at}'
                                )">
                                <i class="fas fa-pen"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }

        container.innerHTML = html;
    } catch (error) {
        console.error('Payment history error:', error);

        container.innerHTML = `
            <div class="text-center py-12 text-red-500 font-bold">
                Failed to load payment history
            </div>
        `;

        showToast('Failed to load payment history', 'error');
    }
}


/* Open the payment editing modal */
function openEditClientPayment(
    paymentId,
    clientId,
    loanId,
    paymentDate,
    paymentAmount,
    clientName,
    paymentCreatedAt
) {
    document.getElementById('edit-payment-client-id').value = clientId;
    document.getElementById('edit-payment-loan-id').value = loanId;
    document.getElementById('edit-payment-id').value = paymentId;
    document.getElementById('edit-payment-client-name').innerText = clientName;
    document.getElementById('edit-payment-date').value = paymentDate;
    document.getElementById('edit-payment-amount').value = paymentAmount;

    startPaymentEditCountdown(paymentCreatedAt);

    const historyModalElement = document.getElementById(
        'modal-client-history'
    );
    const editModalElement = document.getElementById(
        'modal-edit-client-payment'
    );
    const historyModal = bootstrap.Modal.getInstance(historyModalElement);

    if (historyModal) {
        historyModal.hide();
    }

    const editModal = bootstrap.Modal.getOrCreateInstance(editModalElement);
    editModal.show();
}

let paymentEditCountdownInterval = null;

/* Start the countdown for the payment editing deadline */
function startPaymentEditCountdown(paymentCreatedAt) {
    const countdownElement = document.getElementById(
        'edit-payment-countdown'
    );
    const deadlineElement = document.getElementById('edit-payment-deadline');
    const saveButton = document.getElementById('save-edited-payment-btn');

    if (!countdownElement || !deadlineElement) {
        return;
    }

    if (paymentEditCountdownInterval) {
        clearInterval(paymentEditCountdownInterval);
        paymentEditCountdownInterval = null;
    }

    const paymentCreatedDate = new Date(
        String(paymentCreatedAt).replace(' ', 'T')
    );

    if (isNaN(paymentCreatedDate.getTime())) {
        countdownElement.innerHTML = `
            <i class="fas fa-clock"></i>
            <span>Editing time unavailable</span>
        `;
        deadlineElement.innerText = '';
        return;
    }

    const editDeadline = new Date(
        paymentCreatedDate.getTime() + (12 * 60 * 60 * 1000)
    );
    const formattedDeadline = editDeadline.toLocaleString([], {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    });

    deadlineElement.innerText = `Editing allowed until: ${formattedDeadline}`;

    function updatePaymentEditCountdown() {
        const remainingMilliseconds = editDeadline.getTime() - Date.now();

        if (remainingMilliseconds <= 0) {
            clearInterval(paymentEditCountdownInterval);
            paymentEditCountdownInterval = null;

            countdownElement.innerHTML = `
                <i class="fas fa-lock"></i>
                <span>Editing expired</span>
            `;

            deadlineElement.innerText = `Editing ended at: ${formattedDeadline}`;

            if (saveButton) {
                saveButton.disabled = true;
            }

            return;
        }

        const totalSeconds = Math.floor(remainingMilliseconds / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        countdownElement.innerHTML = `
            <i class="fas fa-stopwatch"></i>
            <span>${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(seconds).padStart(2, '0')}s remaining</span>
        `;

        if (saveButton) {
            saveButton.disabled = false;
        }
    }

    updatePaymentEditCountdown();
    paymentEditCountdownInterval = setInterval(
        updatePaymentEditCountdown,
        1000
    );
}

/* Save changes to an existing client payment */
async function editClientPayment() {
    const clientId = document.getElementById(
        'edit-payment-client-id'
    ).value;
    const loanId = document.getElementById('edit-payment-loan-id').value;
    const paymentId = document.getElementById('edit-payment-id').value;
    const amount = document.getElementById('edit-payment-amount').value;
    const paymentDate = document.getElementById('edit-payment-date').value;
    const saveButton = document.getElementById('save-edited-payment-btn');

    if (!clientId) {
        showToast('Client ID is missing.', 'error');
        return;
    }

    if (!loanId) {
        showToast('Loan ID is missing.', 'error');
        return;
    }

    if (!paymentId) {
        showToast('Payment ID is missing.', 'error');
        return;
    }

    if (!paymentDate) {
        showToast('Payment date is required.', 'error');
        return;
    }

    if (!amount || parseFloat(amount) <= 0) {
        showToast('Enter a valid payment amount.', 'error');
        return;
    }

    const originalButtonContent = saveButton.innerHTML;

    saveButton.disabled = true;
    saveButton.innerHTML = `
        <span
            class="spinner-border spinner-border-sm me-2"
            role="status">
        </span>
        Saving...
    `;

    const formData = new FormData();
    formData.append('action', 'edit_payment');
    formData.append('client_id', clientId);
    formData.append('loan_id', loanId);
    formData.append('payment_id', paymentId);
    formData.append('amount', amount);
    formData.append('payment_date', paymentDate);

    try {
        const response = await fetch(CLIENT_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(result.message, 'error');
            return;
        }

        showToast(
            result.message || 'Payment updated successfully',
            'success'
        );

        const editModalElement = document.getElementById(
            'modal-edit-client-payment'
        );
        const editModal = bootstrap.Modal.getInstance(editModalElement);

        if (editModal) {
            editModal.hide();
        }

        const clientName = document.getElementById(
            'history-client-name'
        ).innerText;
        const client = {
            client_id: clientId,
            loan_id: loanId,
            first_name: clientName.split(' ')[0],
            last_name: clientName.split(' ').slice(1).join(' ')
        };

        document.getElementById('history-client-id').value = clientId;
        document.getElementById('history-loan-id').value = loanId;

        const historyModalElement = document.getElementById(
            'modal-client-history'
        );
        const historyModal = bootstrap.Modal.getOrCreateInstance(
            historyModalElement
        );

        historyModal.show();
        await loadClientPaymentHistory(client);
    } catch (error) {
        console.error('Edit payment error:', error);

        showToast(
            'Failed to update payment. Please try again.',
            'error'
        );
    } finally {
        saveButton.disabled = false;
        saveButton.innerHTML = originalButtonContent;
    }
}


document.getElementById('save-edited-payment-btn').addEventListener('click', editClientPayment);

// --------------------------------------
//    DAY PAYMENT BREAKDOWN
// --------------------------------------
let currentPaymentBreakdownTab = 'paid';
let paymentBreakdownData = {
    paid: [],
    unpaid: []
};

function viewPaymentDayBreakdown(officerId, officerName) {
    document.getElementById('breakdown-officer-id').value = officerId;
    document.getElementById('breakdown-officer-name').innerText = officerName;
    document.getElementById('breakdown-payment-date').value = getTodayDate();
    currentPaymentBreakdownTab = 'paid';
    paymentBreakdownData = {
        paid: [],
        unpaid: []
    };
    updatePaymentBreakdownTabs();
    const container = document.getElementById('payment-breakdown-container');
    container.innerHTML = `
        <div class="flex justify-center items-center py-12 text-slate-500">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            <span>Loading payment breakdown...</span>
        </div>
    `;
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-payment-day-breakdown'));
    modal.show();
    loadPaymentDayBreakdown();
}

function getTodayDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function setBreakdownDateToday() {
    const dateInput = document.getElementById('breakdown-payment-date');
    dateInput.value = getTodayDate();
    loadPaymentDayBreakdown();
}

async function loadPaymentDayBreakdown() {
    const officerId = document.getElementById('breakdown-officer-id').value;
    const selectedDate = document.getElementById('breakdown-payment-date').value;
    const container = document.getElementById('payment-breakdown-container');
    const paymentPercentage = document.getElementById('breakdown-payment-percentage');
    const paymentPercentageSummary = document.getElementById('breakdown-payment-percentage-summary');
    const paymentPercentageDate = document.getElementById('breakdown-percentage-date');

    if (!officerId || !selectedDate) {
        showToast("Please select an officer and date", "error");
        return;
    }

    container.innerHTML = `
        <div class="flex justify-center items-center py-12 text-slate-500">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            <span>Loading payment breakdown...</span>
        </div>
    `;

    paymentPercentage.textContent = '...';
    paymentPercentageSummary.textContent = 'Calculating...';
    paymentPercentageDate.textContent = formatBreakdownDate(selectedDate);

    const formData = new FormData();
    formData.append('action', 'payment_day_breakdown');
    formData.append('officer_id', officerId);
    formData.append('selected_date', selectedDate);

    try {
        const response = await fetch(CLIENT_API, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status !== "success") {
            container.innerHTML = `<div class="text-center py-12 text-red-500 font-bold">${result.message}</div>`;
            paymentPercentage.textContent = '0%';
            paymentPercentageSummary.textContent = 'Unable to calculate';
            showToast(result.message, "error");
            return;
        }

        paymentBreakdownData.paid = result.data.paid || [];
        paymentBreakdownData.unpaid = result.data.unpaid || [];

        const dailyPaidCount = Number(result.data.paid_count || 0);
        const dailyExpectedCount = Number(result.data.total_expected_count || 0);

        const dailyPaymentPercentage = dailyExpectedCount > 0
            ? (dailyPaidCount / dailyExpectedCount) * 100
            : 0;

        const formattedDailyPaymentPercentage = Number.isInteger(dailyPaymentPercentage)
            ? dailyPaymentPercentage
            : dailyPaymentPercentage.toFixed(2);

        paymentPercentage.textContent = `${formattedDailyPaymentPercentage}%`;
        paymentPercentageSummary.textContent = `${dailyPaidCount} of ${dailyExpectedCount} paid`;
        paymentPercentageDate.textContent = formatBreakdownDate(selectedDate);

        document.getElementById('breakdown-paid-count').innerText = `(${dailyPaidCount})`;
        document.getElementById('breakdown-unpaid-count').innerText = `(${paymentBreakdownData.unpaid.length})`;

        updatePaymentBreakdownTabs();
        renderPaymentBreakdownTable();

    } catch (error) {
        console.error("Payment day breakdown error:", error);

        container.innerHTML = `
            <div class="text-center py-12 text-red-500 font-bold">
                Failed to load payment breakdown
            </div>
        `;

        paymentPercentage.textContent = '0%';
        paymentPercentageSummary.textContent = 'Unable to calculate';

        showToast("Failed to load payment breakdown", "error");
    }
}

function formatBreakdownDate(paymentDate) {
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

function switchPaymentBreakdownTab(tab) {
    if (tab !== 'paid' && tab !== 'unpaid') {
        return;
    }
    currentPaymentBreakdownTab = tab;
    updatePaymentBreakdownTabs();
    renderPaymentBreakdownTable();
}

/* Update the styling of the paid and unpaid payment breakdown tabs */
function updatePaymentBreakdownTabs() {
    const paidTab = document.getElementById('breakdown-paid-tab');
    const unpaidTab = document.getElementById('breakdown-unpaid-tab');

    if (currentPaymentBreakdownTab === 'paid') {
        paidTab.className = 'btn bg-green-600 text-white border-0 py-2 px-4 rounded-2xl font-black shadow-none hover:bg-green-700 hover:text-white';
        unpaidTab.className = 'btn py-2 px-4 rounded-2xl font-black text-slate-500 bg-slate-100 border-0 shadow-none hover:bg-slate-200';
    } else {
        paidTab.className = 'btn py-2 px-4 rounded-2xl font-black text-slate-500 bg-slate-100 border-0 shadow-none hover:bg-slate-200';
        unpaidTab.className = 'btn bg-red-600 text-white border-0 py-2 px-4 rounded-2xl font-black shadow-none hover:bg-red-700 hover:text-white';
    }
}

// Render the payment breakdown table for the selected date and tab.
function renderPaymentBreakdownTable() {
    const container = document.getElementById('payment-breakdown-container');
    const selectedDate = document.getElementById('breakdown-payment-date').value;
    const records = currentPaymentBreakdownTab === 'paid'
        ? paymentBreakdownData.paid
        : paymentBreakdownData.unpaid;

    if (records.length === 0) {
        const icon = currentPaymentBreakdownTab === 'paid'
            ? 'fa-money-bill-wave'
            : 'fa-user-clock';
        const title = currentPaymentBreakdownTab === 'paid'
            ? 'No payments recorded'
            : 'Everyone paid';
        const message = currentPaymentBreakdownTab === 'paid'
            ? `No payments were recorded on ${selectedDate}.`
            : `All active clients with a payment due on ${selectedDate} made a payment.`;

        container.innerHTML = `
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="fas ${icon} text-slate-400 text-2xl"></i>
                </div>
                <p class="text-slate-500 font-bold mb-1">${title}</p>
                <p class="text-sm text-slate-400 mb-0">${message}</p>
            </div>
        `;
        return;
    }

    if (currentPaymentBreakdownTab === 'unpaid') {
        let html = `
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="text-[10px] uppercase text-slate-400">
                            <th class="border-0 px-2 py-2">Client Name</th>
                            <th class="border-0 px-2 py-2">Phone Number</th>
                            <th class="border-0 px-2 py-2 text-end">Amount Taken</th>
                            <th class="border-0 px-2 py-2 text-end">Expected</th>
                            <th class="border-0 px-2 py-2 text-end">Balance</th>
                            <th class="border-0 px-2 py-2 text-center">Loan Date</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        records.forEach(client => {
            const principalAmount = parseFloat(client.principal_amount || 0);
            const totalRepayable = parseFloat(client.total_repayable || 0);
            const balanceAsAtDate = parseFloat(client.balance_as_at_date || 0);
            const dailyExpected = totalRepayable > 0
                ? totalRepayable / 30
                : 0;

            html += `
                <tr class="border-bottom">
                    <td class="px-2 py-2">
                        <div class="font-black text-slate-800 text-sm">${client.first_name || ''} ${client.last_name || ''}</div>
                        <div class="text-[11px] text-slate-400">${client.location || '-'}</div>
                    </td>
                    <td class="px-2 py-2">
                        <div class="font-semibold text-slate-600 text-sm">${client.phone_primary || '-'}</div>
                        ${client.phone_secondary ? `<div class="text-[11px] text-slate-400">${client.phone_secondary}</div>` : ''}
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-bold text-slate-700 text-sm">${principalAmount.toLocaleString()}</span>
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-bold text-slate-700 text-sm">${dailyExpected.toLocaleString(undefined, {maximumFractionDigits: 2})}</span>
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-black text-red-600 text-sm">${balanceAsAtDate.toLocaleString()}</span>
                    </td>
                    <td class="px-2 py-2 text-center">
                        <span class="font-semibold text-slate-600 text-sm">${client.date_taken || '-'}</span>
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
        return;
    }

    const fieldOfficerRecords = records.filter(client =>
        parseFloat(client.officer_paid_on_date || 0) > 0
    );
    const cashierRecords = records.filter(client =>
        parseFloat(client.officer_paid_on_date || 0) <= 0
    );
    const totalExpected = fieldOfficerRecords.reduce(
        (total, client) =>
            total + (parseFloat(client.total_repayable || 0) / 30),
        0
    );
    const totalOfficerCollected = fieldOfficerRecords.reduce(
        (total, client) =>
            total + parseFloat(client.officer_paid_on_date || 0),
        0
    );

    let html = '';

    if (fieldOfficerRecords.length > 0) {
        html += `
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-blue-500">
                            Field Officer Collections
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-[10px] font-black uppercase text-slate-400">Collected</div>
                        <div class="font-black text-green-600">
                            ${totalOfficerCollected.toLocaleString(undefined, {maximumFractionDigits: 2})}
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="text-[10px] uppercase text-slate-400">
                            <th class="border-0 px-2 py-2">Client Name</th>
                            <th class="border-0 px-2 py-2">Phone Number</th>
                            <th class="border-0 px-2 py-2 text-end">Amount Taken</th>
                            <th class="border-0 px-2 py-2 text-end">Expected</th>
                            <th class="border-0 px-2 py-2 text-end">Amount Paid</th>
                            <th class="border-0 px-2 py-2 text-center">Loan Date</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        fieldOfficerRecords.forEach(client => {
            const principalAmount = parseFloat(client.principal_amount || 0);
            const totalRepayable = parseFloat(client.total_repayable || 0);
            const officerPaidOnDate = parseFloat(client.officer_paid_on_date || 0);
            const dailyExpected = totalRepayable > 0
                ? totalRepayable / 30
                : 0;

            html += `
                <tr class="border-bottom">
                    <td class="px-2 py-2">
                        <div class="font-black text-slate-800 text-sm">${client.first_name || ''} ${client.last_name || ''}</div>
                        <div class="text-[11px] text-slate-400">${client.location || '-'}</div>
                    </td>
                    <td class="px-2 py-2">
                        <div class="font-semibold text-slate-600 text-sm">${client.phone_primary || '-'}</div>
                        ${client.phone_secondary ? `<div class="text-[11px] text-slate-400">${client.phone_secondary}</div>` : ''}
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-bold text-slate-700 text-sm">${principalAmount.toLocaleString()}</span>
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-bold text-slate-700 text-sm">${dailyExpected.toLocaleString(undefined, {maximumFractionDigits: 2})}</span>
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-black text-green-600 text-sm">${officerPaidOnDate.toLocaleString(undefined, {maximumFractionDigits: 2})}</span>
                    </td>
                    <td class="px-2 py-2 text-center">
                        <span class="font-semibold text-slate-600 text-sm">${client.date_taken || '-'}</span>
                    </td>
                </tr>
            `;
        });

        html += `
                    <tr class="border-top border-2 bg-slate-50">
                        <td class="px-2 py-3">
                            <span class="font-black text-slate-900 text-sm">Field Officer Total</span>
                        </td>
                        <td class="px-2 py-3"></td>
                        <td class="px-2 py-3"></td>
                        <td class="px-2 py-3 text-end">
                            <span class="font-black text-slate-900 text-sm">
                                ${totalExpected.toLocaleString(undefined, {maximumFractionDigits: 2})}
                            </span>
                        </td>
                        <td class="px-2 py-3 text-end">
                            <span class="font-black text-green-600 text-sm">
                                ${totalOfficerCollected.toLocaleString(undefined, {maximumFractionDigits: 2})}
                            </span>
                        </td>
                        <td class="px-2 py-3"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        `;
    }

    if (cashierRecords.length > 0) {
        const cashierTotal = cashierRecords.reduce(
            (total, client) =>
                total + parseFloat(client.total_paid_on_date || 0),
            0
        );

        html += `
            <div class="flex items-center gap-3 my-5">
                <div class="flex-grow border-t-2 border-green-600"></div>
                <div class="text-[10px] font-black uppercase tracking-wider text-green-600 whitespace-nowrap">
                    Office Collections
                </div>
                <div class="flex-grow border-t-2 border-green-600"></div>
            </div>

            <div class="mb-3 px-1">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                            Collected by Cashier
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-[10px] font-black uppercase text-slate-400">Office Total</div>
                        <div class="font-black text-slate-700">
                            ${cashierTotal.toLocaleString(undefined, {maximumFractionDigits: 2})}
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="text-[10px] uppercase text-slate-400">
                            <th class="border-0 px-2 py-2">Client Name</th>
                            <th class="border-0 px-2 py-2">Phone Number</th>
                            <th class="border-0 px-2 py-2 text-end">Amount Taken</th>
                            <th class="border-0 px-2 py-2 text-end">Expected</th>
                            <th class="border-0 px-2 py-2 text-end">Amount Paid</th>
                            <th class="border-0 px-2 py-2 text-center">Loan Date</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        cashierRecords.forEach(client => {
            const principalAmount = parseFloat(client.principal_amount || 0);
            const totalRepayable = parseFloat(client.total_repayable || 0);
            const totalPaidOnDate = parseFloat(client.total_paid_on_date || 0);
            const dailyExpected = totalRepayable > 0
                ? totalRepayable / 30
                : 0;

            html += `
                <tr class="border-bottom bg-slate-50">
                    <td class="px-2 py-2">
                        <div class="font-black text-slate-800 text-sm">${client.first_name || ''} ${client.last_name || ''}</div>
                        <div class="text-[11px] text-slate-400">${client.location || '-'}</div>
                    </td>
                    <td class="px-2 py-2">
                        <div class="font-semibold text-slate-600 text-sm">${client.phone_primary || '-'}</div>
                        ${client.phone_secondary ? `<div class="text-[11px] text-slate-400">${client.phone_secondary}</div>` : ''}
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-bold text-slate-700 text-sm">${principalAmount.toLocaleString()}</span>
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-bold text-slate-700 text-sm">${dailyExpected.toLocaleString(undefined, {maximumFractionDigits: 2})}</span>
                    </td>
                    <td class="px-2 py-2 text-end">
                        <span class="font-black text-slate-700 text-sm">${totalPaidOnDate.toLocaleString(undefined, {maximumFractionDigits: 2})}</span>
                    </td>
                    <td class="px-2 py-2 text-center">
                        <span class="font-semibold text-slate-600 text-sm">${client.date_taken || '-'}</span>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;
    }

    if (!html) {
        html = `
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                    <i class="fas fa-money-bill-wave text-slate-400 text-2xl"></i>
                </div>
                <p class="text-slate-500 font-bold mb-1">No payments recorded</p>
                <p class="text-sm text-slate-400 mb-0">No payments were recorded on ${selectedDate}.</p>
            </div>
        `;
    }

    container.innerHTML = html;
}

function printPaymentBreakdown() {
    const selectedDate = document.getElementById('breakdown-payment-date').value;
    const officerName = document.getElementById('breakdown-officer-name').innerText;
    const records = currentPaymentBreakdownTab === 'paid'
        ? paymentBreakdownData.paid.filter(client => parseFloat(client.officer_paid_on_date || 0) > 0)
        : paymentBreakdownData.unpaid;
    const tabTitle = currentPaymentBreakdownTab === 'paid' ? 'Clients Who Paid' : 'Clients Who Did Not Pay';

    if (!records.length) {
        showToast("There is no data to print", "error");
        return;
    }

    const paidClientCount = paymentBreakdownData.paid.length;
    const expectedClientCount = paymentBreakdownData.paid.length + paymentBreakdownData.unpaid.length;
    const dailyPaymentPercentage = expectedClientCount > 0
        ? (paidClientCount / expectedClientCount) * 100
        : 0;

    const formattedDailyPaymentPercentage = Number.isInteger(dailyPaymentPercentage)
        ? dailyPaymentPercentage
        : dailyPaymentPercentage.toFixed(2);

    let rows = '';

    records.forEach(client => {
        const principalAmount = parseFloat(client.principal_amount || 0);
        const totalRepayable = parseFloat(client.total_repayable || 0);
        const totalPaidOnDate = parseFloat(client.officer_paid_on_date || 0);
        const balanceAsAtDate = parseFloat(client.balance_as_at_date || 0);
        const dailyExpected = totalRepayable > 0 ? totalRepayable / 30 : 0;

        rows += `
            <tr>
                <td>${client.first_name || ''} ${client.last_name || ''}</td>
                <td>${client.phone_primary || '-'}</td>
                <td class="number">${principalAmount.toLocaleString()}</td>
                <td class="number">${dailyExpected.toLocaleString(undefined, {maximumFractionDigits: 2})}</td>
                <td class="number">${currentPaymentBreakdownTab === 'paid' ? totalPaidOnDate.toLocaleString() : balanceAsAtDate.toLocaleString()}</td>
                <td>${client.date_taken || '-'}</td>
            </tr>
        `;
    });

    let totalExpected = 0;
    let totalPaid = 0;

    if (currentPaymentBreakdownTab === 'paid') {
        totalExpected = records.reduce(
            (total, client) => total + (parseFloat(client.total_repayable || 0) / 30),
            0
        );

        totalPaid = records.reduce(
            (total, client) => total + parseFloat(client.officer_paid_on_date || 0),
            0
        );

        rows += `
            <tr class="total-row">
                <td><strong>Total</strong></td>
                <td></td>
                <td></td>
                <td class="number"><strong>${totalExpected.toLocaleString(undefined, {maximumFractionDigits: 2})}</strong></td>
                <td class="number"><strong>${totalPaid.toLocaleString(undefined, {maximumFractionDigits: 2})}</strong></td>
                <td></td>
            </tr>
        `;
    }

    const printWindow = window.open('', '_blank', 'width=1200,height=800');

    if (!printWindow) {
        showToast("Unable to open print window. Please allow pop-ups.", "error");
        return;
    }

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Daily Payment Report - ${selectedDate}</title>

            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 30px;
                    color: #1e293b;
                }

                h1 {
                    margin: 0 0 5px;
                    font-size: 24px;
                }

                h2 {
                    margin: 0 0 20px;
                    font-size: 16px;
                    color: #64748b;
                }

                .details {
                    margin-bottom: 20px;
                    font-size: 14px;
                }

                .summary {
                    display: flex;
                    gap: 30px;
                    margin-bottom: 25px;
                    padding: 15px;
                    background: #f8fafc;
                    border: 1px solid #cbd5e1;
                }

                .summary-item {
                    font-size: 14px;
                }

                .summary-label {
                    color: #64748b;
                    font-size: 11px;
                    text-transform: uppercase;
                    font-weight: bold;
                }

                .summary-value {
                    font-size: 22px;
                    font-weight: bold;
                    margin-top: 4px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th,
                td {
                    border: 1px solid #cbd5e1;
                    padding: 9px;
                    text-align: left;
                    font-size: 12px;
                }

                th {
                    background: #f1f5f9;
                    font-weight: bold;
                    text-transform: uppercase;
                }

                .number {
                    text-align: right;
                }

                .total-row td {
                    font-weight: bold;
                    border-top: 2px solid #1e293b;
                    background: #f8fafc;
                }

                .cash-total {
                    margin-top: 20px;
                    border-top: 2px solid #111827;
                    padding-top: 12px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-weight: 700;
                    font-size: 15px;
                }

                .cash-total-value {
                    font-size: 18px;
                }

                @media print {
                    body {
                        padding: 0;
                    }
                }
            </style>
        </head>

        <body>
            <h1>Daily Cash in Report</h1>
            <h2>${tabTitle}</h2>

            <div class="details">
                <strong>Field Officer:</strong> ${officerName}<br>
                <strong>Date:</strong> ${selectedDate}
            </div>

            <div class="summary">
                <div class="summary-item">
                    <div class="summary-label">Daily Payment Percentage</div>
                    <div class="summary-value">${formattedDailyPaymentPercentage}%</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Paid Clients</div>
                    <div class="summary-value">${paidClientCount}</div>
                </div>

                <div class="summary-item">
                    <div class="summary-label">Expected Clients</div>
                    <div class="summary-value">${expectedClientCount}</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Phone Number</th>
                        <th>Amount Taken</th>
                        <th>Expected</th>
                        <th>${currentPaymentBreakdownTab === 'paid' ? 'Amount Paid' : 'Balance'}</th>
                        <th>Loan Date</th>
                    </tr>
                </thead>

                <tbody>
                    ${rows}
                </tbody>
            </table>

            ${currentPaymentBreakdownTab === 'paid' ? `
                <div class="cash-total">
                    <span>Total Cash in</span>
                    <span class="cash-total-value">UGX ${totalPaid.toLocaleString(undefined, {maximumFractionDigits: 2})}</span>
                </div>
            ` : ''}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 300);
}


document.getElementById('breakdown-payment-date')?.addEventListener('change', function() {
    loadPaymentDayBreakdown();
});

// --------------------------------------
//     END OF DAY PAYMENT BREAKDOWN
// --------------------------------------


// record payment
async function recordClientPayment() {
    const recordingMode = document.getElementById('payment-recording-mode').value;
    const clientId = document.getElementById('payment-client-id').value;
    const loanId = document.getElementById('payment-loan-id').value;
    const amount = document.getElementById('payment-amount').value.trim();
    const paymentDate = document.getElementById('payment-date').value;
    const recordBtn = document.getElementById('record-payment-btn');

    if (recordingMode !== 'field_officer' && recordingMode !== 'cashier') {
        showToast("Invalid payment recording mode", "error");
        return;
    }

    if (!clientId || !loanId) {
        showToast("Please select a client with an active loan", "error");
        return;
    }

    if (!amount || parseFloat(amount) <= 0) {
        showToast("Please enter a valid payment amount", "error");
        return;
    }

    if (!paymentDate) {
        showToast("Please select the transaction date", "error");
        return;
    }

    const formData = new FormData();

    if (recordingMode === 'field_officer') {
        const officerId = document.getElementById('clients-officer-id').value;

        if (!officerId) {
            showToast("Field officer not selected", "error");
            return;
        }

        formData.append('action', 'record_payment');
        formData.append('collected_by', officerId);
    } else {
        formData.append('action', 'record_cashier_payment');
    }

    formData.append('client_id', clientId);
    formData.append('loan_id', loanId);
    formData.append('amount', amount);
    formData.append('payment_date', paymentDate);

    const originalText = recordBtn.innerHTML;

    recordBtn.disabled = true;
    recordBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Recording...
    `;

    try {
        const response = await fetch(CLIENT_API, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status !== "success") {
            showToast(result.message, "error");
            return;
        }

        showToast(result.message, "success");

        const modalElement = document.getElementById('modal-client-payment');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);

        if (modalInstance) {
            modalInstance.hide();
        }

        if (recordingMode === 'field_officer') {
            const officerId = document.getElementById('clients-officer-id').value;
            const officerName = document.getElementById('clients-officer-name').innerText;

            listFieldOfficers();

            if (officerId) {
                viewOfficerClients(officerId, officerName);
            }
        } else {
            loadCashierCollections();
        }
    } catch (error) {
        console.error("Record payment error:", error);
        showToast("Failed to record payment", "error");
    } finally {
        recordBtn.disabled = false;
        recordBtn.innerHTML = originalText;
    }
}