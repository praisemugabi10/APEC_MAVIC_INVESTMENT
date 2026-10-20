// this file handles cash received at the office by cashier
function openCashierRecordPayment() {
    const modalElement = document.getElementById('modal-client-payment');

    if (!modalElement) {
        showToast("Payment recording interface is not available", "error");
        return;
    }

    document.getElementById('payment-recording-mode').value = 'cashier';
    document.getElementById('payment-client-id').value = '';
    document.getElementById('payment-loan-id').value = '';
    document.getElementById('payment-client-name').innerText = '';
    document.getElementById('payment-total-repayable').innerText = '';
    document.getElementById('payment-current-balance').innerText = '';
    document.getElementById('payment-amount').value = '';
    document.getElementById('payment-date').value = getTodayDate();

    document.getElementById('payment-modal-title').innerText = 'Record Cash In';
    document.getElementById('cashier-client-selection').classList.remove('d-none');
    document.getElementById('cashier-client-selected').innerText = 'Select client';
    document.getElementById('cashier-client-search').value = '';

    const recordBtn = document.getElementById('record-payment-btn');

    recordBtn.innerHTML = `<i class="fas fa-money-bill-wave me-2"></i>Record Cash In`;

    document.getElementById('cashier-client-options').innerHTML = `
        <div class="px-3 py-3 text-sm font-bold text-slate-400">
            Loading clients...
        </div>
    `;

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    modal.show();

    loadCashierPaymentClients();
}

function toggleCashierClientDropdown() {
    const menu = document.getElementById('cashier-client-menu');
    const searchInput = document.getElementById('cashier-client-search');

    if (!menu) {
        return;
    }

    menu.classList.toggle('d-none');

    if (!menu.classList.contains('d-none')) {
        setTimeout(() => {
            searchInput?.focus();
        }, 50);
    }
}

async function loadCashierPaymentClients() {
    const optionsContainer = document.getElementById('cashier-client-options');

    if (!optionsContainer) {
        return;
    }

    const formData = new FormData();

    formData.append('action', 'cashier_payment_clients');

    try {
        const response = await fetch(CLIENT_API, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!response.ok || result.status !== "success") {
            throw new Error(result.message || "Failed to load clients");
        }

        const clients = Array.isArray(result.data) ? result.data : [];

        window.cashierPaymentClients = clients;

        renderCashierClientOptions(clients);
    } catch (error) {
        console.error("Cashier payment clients error:", error);

        optionsContainer.innerHTML = `
            <div class="px-3 py-3 text-sm font-bold text-red-500">
                Failed to load clients
            </div>
        `;

        showToast("Failed to load clients", "error");
    }
}

function renderCashierClientOptions(clients) {
    const optionsContainer = document.getElementById('cashier-client-options');

    if (!optionsContainer) {
        return;
    }

    if (clients.length === 0) {
        optionsContainer.innerHTML = `
            <div class="px-3 py-3 text-sm font-bold text-slate-400">
                No clients with active loans
            </div>
        `;
        return;
    }

    optionsContainer.innerHTML = '';

    clients.forEach(client => {
        const clientName = `${client.first_name || ''} ${client.last_name || ''}`.trim();

        const option = document.createElement('button');

        option.type = 'button';
        option.className = 'w-100 text-start border-0 bg-white px-3 py-3 font-bold text-slate-700 hover:bg-slate-50 transition-colors';
        option.textContent = clientName;

        option.addEventListener('click', function() {
            selectCashierClient(client);
        });

        optionsContainer.appendChild(option);
    });
}

function selectCashierClient(client) {
    const clientId = client.client_id;
    const loanId = client.loan_id || '';
    const totalRepayable = Number(client.total_repayable || 0);
    const balance = Number(client.balance || 0);
    const clientName = `${client.first_name || ''} ${client.last_name || ''}`.trim();

    document.getElementById('payment-client-id').value = clientId;
    document.getElementById('payment-loan-id').value = loanId;
    document.getElementById('payment-client-name').innerText = clientName;
    document.getElementById('payment-total-repayable').innerText = totalRepayable.toLocaleString();
    document.getElementById('payment-current-balance').innerText = balance.toLocaleString();
    document.getElementById('payment-amount').value = '';

    document.getElementById('cashier-client-selected').innerText = clientName;
    document.getElementById('cashier-client-search').value = '';
    document.getElementById('cashier-client-menu').classList.add('d-none');
}

document.getElementById('cashier-client-search')?.addEventListener('input', function() {
    const searchTerm = this.value.trim().toLowerCase();
    const clients = window.cashierPaymentClients || [];

    const filteredClients = clients.filter(client => {
        const clientName = `${client.first_name || ''} ${client.last_name || ''}`.trim().toLowerCase();

        return clientName.includes(searchTerm);
    });

    renderCashierClientOptions(filteredClients);
});

document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('cashier-client-dropdown');
    const menu = document.getElementById('cashier-client-menu');

    if (!dropdown || !menu) {
        return;
    }

    if (!dropdown.contains(event.target)) {
        menu.classList.add('d-none');
    }
});



function getCashierCollectionsDate() {
    const dateInput = document.getElementById('cashier-collections-date');

    if (!dateInput) {
        return getTodayDate();
    }

    return dateInput.value || getTodayDate();
}

function formatCashierCollectionsDate(paymentDate) {
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

function updateCashierCollectionsDateLabel(selectedDate) {
    const label = document.getElementById('cashier-collections-date-label');

    if (!label) {
        return;
    }

    const today = getTodayDate();

    label.textContent = selectedDate === today
        ? "Today's office collections"
        : `Office collections for ${formatCashierCollectionsDate(selectedDate)}`;
}

// quickly filter today's collections
function selectCashierCollectionsToday() {
    const dateInput = document.getElementById('cashier-collections-date');

    if (!dateInput) {
        return;
    }

    dateInput.value = getTodayDate();
    loadCashierCollections();
}

// Load cashier collections for the selected date.
async function loadCashierCollections() {
    const container = document.getElementById('cashier-collections-list');
    const totalContainer = document.getElementById('cashier-collections-total');
    const totalElement = document.getElementById('cashier-today-total');
    const clientCountElement = document.getElementById('cashier-today-client-count');
    const dateInput = document.getElementById('cashier-collections-date');

    if (!container) {
        return;
    }

    if (dateInput && !dateInput.value) {
        dateInput.value = getTodayDate();
    }

    const selectedDate = getCashierCollectionsDate();

    updateCashierCollectionsDateLabel(selectedDate);

    container.innerHTML = `
        <tr>
            <td colspan="5" class="px-8 py-12 text-center text-slate-400">
                <div class="flex flex-col items-center gap-3">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    <p class="mb-0 font-semibold">Loading collections...</p>
                </div>
            </td>
        </tr>
    `;

    if (totalContainer) {
        totalContainer.innerHTML = '';
    }

    if (totalElement) {
        totalElement.textContent = 'UGX ...';
    }

    if (clientCountElement) {
        clientCountElement.textContent = '...';
    }

    const formData = new FormData();
    formData.append('action', 'cashier_collections');
    formData.append('selected_date', selectedDate);

    try {
        const response = await fetch(CLIENT_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(
                result.message || 'Failed to load cashier collections'
            );
        }

        const data = result.data || {};
        const collections = Array.isArray(data.collections)
            ? data.collections
            : [];
        const totalCashIn = Number(data.total_cash_in || 0);
        const clientCount = Number(
            data.client_count !== undefined
                ? data.client_count
                : collections.length
        );

        window.cashierCollectionsData = {
            collections,
            totalCashIn,
            clientCount,
            cashierName: data.cashier_name || '-',
            selectedDate
        };

        if (totalElement) {
            totalElement.textContent = `UGX ${totalCashIn.toLocaleString()}`;
        }

        if (clientCountElement) {
            clientCountElement.textContent = clientCount.toLocaleString();
        }

        if (collections.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="5" class="px-8 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                <i class="fas fa-money-bill-wave text-slate-400 text-2xl"></i>
                            </div>
                            <p class="text-slate-700 font-black mb-1">
                                No cash in recorded
                            </p>
                            <p class="text-sm text-slate-400 font-semibold mb-0">
                                No cashier collections were recorded for ${formatCashierCollectionsDate(selectedDate)}.
                            </p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';

        collections.forEach(collection => {
            const clientName = `${collection.first_name || ''} ${collection.last_name || ''}`.trim() || '-';
            const officerName = `${collection.officer_first_name || ''} ${collection.officer_last_name || ''}`.trim() || '-';
            const amountPaid = Number(collection.amount_paid || 0);

            html += `
                <tr class="align-middle hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="font-black text-slate-800 text-sm">
                            ${clientName}
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="font-semibold text-slate-600 text-sm">
                            ${collection.phone_primary || '-'}
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="font-bold text-slate-700 text-sm">
                            ${officerName}
                        </div>
                    </td>
                    <td class="px-6 py-5 text-end">
                        <span class="font-black text-emerald-600 text-sm">
                            UGX ${amountPaid.toLocaleString()}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="font-semibold text-slate-600 text-sm">
                            ${collection.payment_date || selectedDate}
                        </span>
                    </td>
                </tr>
            `;
        });

        container.innerHTML = html;

        if (totalContainer) {
            totalContainer.innerHTML = `
                <tr class="border-t-2 border-slate-300 bg-slate-50">
                    <td class="px-8 py-5">
                        <span class="font-black text-slate-900 text-sm">
                            Total
                        </span>
                    </td>
                    <td class="px-6 py-5"></td>
                    <td class="px-6 py-5">
                        <span class="text-xs font-bold text-slate-400">
                            ${clientCount.toLocaleString()} client${clientCount === 1 ? '' : 's'}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-end">
                        <span class="font-black text-emerald-600 text-sm">
                            UGX ${totalCashIn.toLocaleString()}
                        </span>
                    </td>
                    <td class="px-6 py-5"></td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Cashier collections error:', error);

        container.innerHTML = `
            <tr>
                <td colspan="5" class="px-8 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mb-4">
                            <i class="fas fa-triangle-exclamation text-red-400 text-2xl"></i>
                        </div>
                        <p class="text-red-500 font-black mb-1">
                            Failed to load collections
                        </p>
                        <p class="text-sm text-slate-400 font-semibold mb-0">
                            Please try again.
                        </p>
                    </div>
                </td>
            </tr>
        `;

        if (totalElement) {
            totalElement.textContent = 'UGX 0';
        }

        if (clientCountElement) {
            clientCountElement.textContent = '0';
        }

        if (totalContainer) {
            totalContainer.innerHTML = '';
        }

        showToast('Failed to load cashier collections', 'error');
    }
}

document.getElementById('cashier-collections-date')?.addEventListener(
    'change',
    function () {
        loadCashierCollections();
    }
);

// Print the loaded cashier collections report.
function printCashierCollections() {
    const data = window.cashierCollectionsData;

    if (!data) {
        showToast('Please load the cashier collections first', 'error');
        return;
    }

    const collections = Array.isArray(data.collections)
        ? data.collections
        : [];

    if (collections.length === 0) {
        showToast('There are no cashier collections to print', 'error');
        return;
    }

    const cashierName = data.cashierName || '-';
    const selectedDate = formatCashierCollectionsDate(data.selectedDate);
    const totalCashIn = Number(data.totalCashIn || 0);
    const clientCount = Number(data.clientCount || 0);

    let rows = '';

    collections.forEach(collection => {
        const clientName = `${collection.first_name || ''} ${collection.last_name || ''}`.trim() || '-';
        const officerName = `${collection.officer_first_name || ''} ${collection.officer_last_name || ''}`.trim() || '-';
        const phone = collection.phone_primary || '-';
        const amountPaid = Number(collection.amount_paid || 0);

        rows += `
            <tr>
                <td>${clientName}</td>
                <td>${phone}</td>
                <td>${officerName}</td>
                <td class="amount">UGX ${amountPaid.toLocaleString()}</td>
                <td>${collection.payment_date || data.selectedDate}</td>
            </tr>
        `;
    });

    const printWindow = window.open('', '_blank', 'width=1000,height=700');

    if (!printWindow) {
        showToast('Please allow pop-ups to print the report', 'error');
        return;
    }

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Cashier Collections - ${data.selectedDate}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    color: #111827;
                    margin: 35px;
                    font-size: 13px;
                }

                h1 {
                    margin: 0 0 6px;
                    font-size: 22px;
                }

                .subtitle {
                    color: #6b7280;
                    margin-bottom: 22px;
                }

                .details {
                    margin-bottom: 20px;
                    line-height: 1.7;
                }

                .details strong {
                    display: inline-block;
                    min-width: 110px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }

                th,
                td {
                    border: 1px solid #d1d5db;
                    padding: 9px 10px;
                    text-align: left;
                }

                th {
                    background: #f3f4f6;
                    font-weight: 700;
                }

                .amount {
                    text-align: right;
                    white-space: nowrap;
                }

                .summary {
                    margin-top: 20px;
                    border-top: 2px solid #111827;
                    padding-top: 12px;
                    display: flex;
                    justify-content: space-between;
                    font-weight: 700;
                }

                .total {
                    font-size: 16px;
                }

                @media print {
                    body {
                        margin: 20px;
                    }

                    button {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <h1>Cashier Collections</h1>
            <div class="subtitle">Office cash collections</div>

            <div class="details">
                <div><strong>Cashier:</strong> ${cashierName}</div>
                <div><strong>Date:</strong> ${selectedDate}</div>
                <div><strong>Clients:</strong> ${clientCount.toLocaleString()}</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Phone Number</th>
                        <th>Field Officer</th>
                        <th style="text-align: right;">Amount Paid</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>

            <div class="summary">
                <span>Total Cash Collected</span>
                <span class="total">UGX ${totalCashIn.toLocaleString()}</span>
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
    }, 300);
}