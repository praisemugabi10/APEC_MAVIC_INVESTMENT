const CLIENT_ENDPOINT = "api/manage_client.php";
const OFFICER_ENDPOINT = "api/manage_field_officer.php";
/* ==========================================================================
   PROCESS TRANSACTION
   ========================================================================== */
async function processTransaction() {
    const mainType = document.getElementById('t-main-type').value;
    const subType = document.getElementById('t-sub-type').value;
    const amount = parseFloat(document.getElementById('t-amount').value);
    const note = document.getElementById('t-note').value.trim();
    const targetId = document.getElementById('t-selected-id').value;

    // Validation
    if (!amount || amount <= 0) {
        return showToast("Enter a valid amount", "error");
    }
    if (!mainType) {
        return showToast("Please select a transaction type", "error");
    }
    if (!subType) {
        return showToast("Please select a transaction category", "error");
    }

    const selectedCategory = transactionCategories.find(
        category => String(category.transaction_category_id) === String(subType)
    );
    if (!selectedCategory) {
        return showToast("Selected transaction category could not be found.", "error");
    }

    const categoryName = String(selectedCategory.transaction_category_name || "").trim().toLowerCase();
    if (
        (categoryName === "client payment" || categoryName === "field officer") &&
        !targetId
    ) {
        return showToast(
            categoryName === "client payment"
                ? "Please select a client from the list"
                : "Please select a field officer from the list",
            "error"
        );
    }

    const transactionType = mainType === "in" ? "cash_in" : "cash_out";
    const expectedType = mainType === "in" ? "cash_in" : "cash_out";
    const categoryType = String(selectedCategory.transaction_category_type || "").toLowerCase();
    if (categoryType !== expectedType) {
        return showToast("The selected category does not match the transaction type.", "error");
    }

    // Prepare Transaction Payload
    const payload = {
        transaction_type: transactionType,
        transaction_category_id: selectedCategory.transaction_category_id,
        amount: amount,
        description: note || null,
        transaction_date: new Date().toISOString()
    };
    if (categoryName === "client payment") {
        payload.client_id = targetId;
    }
    if (categoryName === "field officer") {
        payload.field_officer_id = targetId;
    }

    // Show Loading State
    const saveBtn = document.getElementById('btn-save-transaction');
    const saveBtnContent = document.getElementById('btn-save-transaction-content');
    saveBtn.disabled = true;
    saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
    saveBtnContent.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
        Processing...
    `;

    // Submit Transaction
    try {
        const response = await fetch('api/transactions/transactions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Failed to create transaction.');
        }

        // Update Transactions and Dashboard
        const createdTransaction = result.data?.transaction;
        if (createdTransaction) {
            renderTransactions();
        }
        await updateTransactionBalance();
        closeModal('modal-transaction');
        showToast("Transaction recorded successfully", "success");

        // Reset Form
        document.getElementById('t-main-type').value = '';
        document.getElementById('t-sub-type').innerHTML = `
            <option value="">Select Category...</option>
        `;
        document.getElementById('t-amount').value = '';
        document.getElementById('t-note').value = '';
        document.getElementById('t-selected-id').value = '';
        document.getElementById('t-search-input').value = '';
        document.getElementById('search-results').innerHTML = '';
        document.getElementById('search-results').classList.add('hidden');
        handleMainTypeChange();
    } catch (error) {
        console.error("Error creating transaction:", error);
        showToast(error.message || "Failed to create transaction.", "error");
    } finally {
        // Restore Save Button
        saveBtn.disabled = false;
        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        saveBtnContent.innerHTML = `Save Record`;
    }
}


/*
|--------------------------------------------------------------------------
| Update Transaction Balance
|--------------------------------------------------------------------------
*/
// async function updateTransactionBalance() {
//     const dateInput = document.getElementById(
//         'dashboard-transaction-date'
//     );

//     const selectedDate = dateInput?.value;

//     const url = new URL(
//         'api/transactions/transaction_balance.php',
//         window.location.href
//     );

//     if (selectedDate) {
//         url.searchParams.set('date', selectedDate);
//     }

//     try {
//         const response = await fetch(url, {
//             method: 'GET',
//             headers: {
//                 'Accept': 'application/json'
//             },
//             cache: 'no-store'
//         });

//         const result = await response.json();

//         if (!response.ok || result.status !== 'success') {
//             throw new Error(
//                 result.message || 'Failed to update transaction balance.'
//             );
//         }

//         const balanceData = result.data || {};
//         const totalCashIn = Number(balanceData.total_cash_in || 0);
//         const totalCashOut = Number(balanceData.total_cash_out || 0);
//         const balance = Number(balanceData.balance || 0);

//         const balanceElement = document.getElementById('dash-balance');
//         const cashInElement = document.getElementById('dash-in-total');
//         const cashOutElement = document.getElementById('dash-out-total');

//         if (balanceElement) {
//             balanceElement.textContent =
//                 `UGX ${formatTransactionAmount(balance)}`;
//         }

//         if (cashInElement) {
//             cashInElement.textContent =
//                 `UGX ${formatTransactionAmount(totalCashIn)}`;
//         }

//         if (cashOutElement) {
//             cashOutElement.textContent =
//                 `UGX ${formatTransactionAmount(totalCashOut)}`;
//         }
//     } catch (error) {
//         console.error('Error updating transaction balance:', error);
//         showToast('Failed to load transaction balance.', 'error');
//     }
// }


/* ==========================================================================
   RENDER TRANSACTIONS
   ========================================================================== */
async function renderTransactions() {
    const transactionList = document.getElementById('transaction-list');
    if (!transactionList) {
        console.error('Transaction table body (#transaction-list) was not found.');
        return;
    }
    // Loading State
    transactionList.innerHTML = `
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
    `;
    
    // Fetch and Render Transactions
    try {
        const response = await fetch('api/transactions/transactions.php?action=fetch_transactions', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
            cache: 'no-store'
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Unable to load transactions.');
        }
        const transactions = Array.isArray(result.data?.transactions) ? result.data.transactions : [];
        // Empty State
        if (transactions.length === 0) {
            transactionList.innerHTML = `
                <tr id="transaction-empty-state">
                    <td colspan="8" class="text-center py-5">
                        <div class="py-4">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-slate-100 flex items-center justify-center">
                                <i class="fas fa-receipt text-slate-400"></i>
                            </div>
                            <p class="text-slate-500 font-bold mb-1">No transactions yet</p>
                            <p class="text-sm text-slate-400 mb-0">New transactions will appear here.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        // Render Transaction Rows
        transactionList.innerHTML = transactions.map((transaction, index) => {
            const type = String(transaction.transaction_type || '').toLowerCase();
            const typeLabel = type === 'cash_in' ? 'Cash In' : 'Cash Out';
            const typeClass = type === 'cash_in' ? 'text-blue-600' : 'text-orange-600';
            const category = transaction.transaction_category_name || '—';
            const amount = Number(transaction.transaction_amount || 0);
            const description = transaction.transaction_description || '—';
            let person = '—';
            const clientFirstName = String(transaction.client_first_name || '').trim();
            const clientLastName = String(transaction.client_last_name || '').trim();
            const officerFirstName = String(transaction.officer_first_name || '').trim();
            const officerLastName = String(transaction.officer_last_name || '').trim();
            if (clientFirstName || clientLastName) {
                person = `${clientFirstName} ${clientLastName}`.trim();
            } else if (officerFirstName || officerLastName) {
                person = `${officerFirstName} ${officerLastName}`.trim();
            }
            const recordedBy = `${transaction.recorded_by_first_name || ''} ${transaction.recorded_by_last_name || ''}`.trim() || '—';
            const transactionDate = transaction.transaction_trxn_date || transaction.transaction_created_at || '';
            let formattedDate = '—';
            if (transactionDate) {
                const parsedDate = new Date(transactionDate.replace(' ', 'T'));
                if (!isNaN(parsedDate.getTime())) {
                    formattedDate = parsedDate.toLocaleString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
            const escapeHtml = (value) => String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            return `
                <tr>
                    <td class="px-4 px-sm-5 py-4 text-sm font-bold text-slate-500">${index + 1}</td>
                    <td class="px-4 py-4">
                        <span class="${typeClass} font-black text-sm whitespace-nowrap">${escapeHtml(typeLabel)}</span>
                    </td>
                    <td class="px-4 py-4 text-sm font-bold text-slate-700">${escapeHtml(category)}</td>
                    <td class="px-4 py-4 text-sm font-black text-slate-900 whitespace-nowrap">UGX ${amount.toLocaleString('en-US')}</td>
                    <td class="px-4 py-4 text-sm text-slate-600">${escapeHtml(description)}</td>
                    <td class="px-4 py-4 text-sm font-bold text-slate-700">${escapeHtml(person)}</td>
                    <td class="px-4 py-4 text-sm font-bold text-slate-700">${escapeHtml(recordedBy)}</td>
                    <td class="px-4 px-sm-5 py-4 text-sm font-bold text-slate-500 whitespace-nowrap">${escapeHtml(formattedDate)}</td>
                </tr>
            `;
        }).join('');
    } catch (error) {
        console.error('Error rendering transactions:', error);
        // Error State
        const escapeHtml = (value) => String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        transactionList.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="py-4">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-red-50 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <p class="text-red-500 font-bold mb-1">Unable to load transactions</p>
                        <p class="text-sm text-slate-400 mb-0">${escapeHtml(error.message || 'Please try again later.')}</p>
                    </div>
                </td>
            </tr>
        `;
    }
}

function formatTransactionAmount(amount) {
    return Number(amount || 0).toLocaleString(
        'en-US',
        {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }
    );
}


/* ==========================================================================
   HANDLE MAIN TRANSACTION TYPE
   ========================================================================== */

async function handleMainTypeChange() {
    const mainType = document.getElementById('t-main-type').value;
    const subTypeSelect = document.getElementById('t-sub-type');
    const subTypeContainer = document.getElementById('container-sub-type');
    const searchContainer = document.getElementById('container-searchable');
    const amountContainer = document.getElementById('container-amount');
    const saveBtn = document.getElementById('btn-save-transaction');
    // Reset Subsequent Steps
    subTypeContainer.classList.add('hidden');
    searchContainer.classList.add('hidden');
    amountContainer.classList.add('hidden');
    saveBtn.classList.add('hidden');
    document.getElementById('t-selected-id').value = '';
    document.getElementById('t-search-input').value = '';
    subTypeSelect.innerHTML = `<option value="">Select Category...</option>`;
    if (!mainType) {
        return;
    }
    const transactionType = mainType === 'in' ? 'cash_in' : 'cash_out';
    subTypeContainer.classList.remove('hidden');
    // Load Transaction Categories
    subTypeSelect.disabled = true;
    subTypeSelect.innerHTML = `<option value="">Loading categories...</option>`;
    try {
        const response = await fetch(`${TRANSACTION_CATEGORY_ENDPOINT}?action=fetch_transaction_categories`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || "Unable to load transaction categories.");
        }
        transactionCategories = Array.isArray(result.data) ? result.data : [];
        const filteredCategories = transactionCategories.filter(
            category => String(category.transaction_category_type || "").toLowerCase() === transactionType
        );
        if (filteredCategories.length === 0) {
            subTypeSelect.innerHTML = `
                <option value="">
                    No ${transactionType === 'cash_in' ? 'Cash In' : 'Cash Out'} categories available
                </option>
            `;
            return;
        }
        subTypeSelect.innerHTML = `<option value="">Select Category...</option>`;
        filteredCategories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.transaction_category_id;
            option.textContent = category.transaction_category_name;
            subTypeSelect.appendChild(option);
        });
    } catch (error) {
        console.error("Error fetching transaction categories:", error);
        subTypeSelect.innerHTML = `<option value="">Unable to load categories</option>`;
        showToast(error.message || "Unable to load transaction categories.", "error");
    } finally {
        subTypeSelect.disabled = false;
    }
}


/* ==========================================================================
   HANDLE TRANSACTION CATEGORY CHANGE
   ========================================================================== */
function handleSubTypeChange() {
    const subType = document.getElementById('t-sub-type').value;
    const searchContainer = document.getElementById('container-searchable');
    const amountContainer = document.getElementById('container-amount');
    const saveBtn = document.getElementById('btn-save-transaction');
    // Reset Subsequent Steps
    searchContainer.classList.add('hidden');
    amountContainer.classList.add('hidden');
    saveBtn.classList.add('hidden');
    document.getElementById('t-selected-id').value = '';
    document.getElementById('t-search-input').value = '';
    if (!subType) {
        return;
    }
    // Find Selected Category
    const selectedCategory = transactionCategories.find(
        category => String(category.transaction_category_id) === String(subType)
    );
    if (!selectedCategory) {
        showToast("Selected transaction category could not be found.", "error");
        return;
    }
    const categoryName = String(selectedCategory.transaction_category_name || "").trim().toLowerCase();
    // Handle Category Selection
    if (categoryName === "client payment") {
        setupSearch('client');
        searchContainer.classList.remove('hidden');
        return;
    }
    if (categoryName === "field officer") {
        setupSearch('officer');
        searchContainer.classList.remove('hidden');
        return;
    }
    amountContainer.classList.remove('hidden');
    saveBtn.classList.remove('hidden');
}

/* ==========================================================================
   SETUP PERSON SEARCH
   ========================================================================== */
async function setupSearch(type) {
    const label = document.getElementById('search-label');
    const input = document.getElementById('t-search-input');
    const results = document.getElementById('search-results');
    // Reset Selection and Set Label
    input.value = '';
    document.getElementById('t-selected-id').value = '';
    label.innerText = type === 'client' ? 'Select Client' : 'Select Field Officer';
    const isClient = type === 'client';
    const endpoint = isClient ? CLIENT_ENDPOINT : OFFICER_ENDPOINT;
    const loadingMessage = isClient ? 'Loading clients...' : 'Loading field officers...';
    const emptyMessage = isClient ? 'No active clients found.' : 'No field officers found.';
    // Fetch People
    results.innerHTML = `<div class="p-3 text-sm font-bold text-slate-400">${loadingMessage}</div>`;
    results.classList.remove('hidden');
    try {
        const formData = new FormData();
        formData.append('action', 'list');
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || `Unable to load ${isClient ? 'clients' : 'field officers'}.`);
        }
        const people = Array.isArray(result.data) ? result.data : [];
        if (people.length === 0) {
            results.innerHTML = `<div class="p-3 text-sm font-bold text-slate-400">${emptyMessage}</div>`;
            return;
        }
        // Render Search Results
        results.innerHTML = people.map(person => {
            const personId = escapeTransactionCategoryAttribute(isClient ? person.client_id : person.officer_id);
            const personName = `${person.first_name} ${person.last_name}`;
            const safeName = escapeTransactionCategoryHtml(personName);
            const safeAttributeName = escapeTransactionCategoryAttribute(personName);
            const location = isClient && person.location
                ? escapeTransactionCategoryHtml(person.location)
                : '';
            return `
                <div onclick="selectPerson('${personId}', '${safeAttributeName}')" class="p-3 hover:bg-blue-50 cursor-pointer border-b border-slate-50 font-bold text-sm">
                    <div class="text-slate-700">${safeName}</div>
                    ${location ? `<div class="text-[10px] text-slate-400 mt-0.5">${location}</div>` : ''}
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error(`Error loading ${isClient ? 'clients' : 'field officers'}:`, error);
        results.innerHTML = `<div class="p-3 text-sm font-bold text-red-500">Unable to load ${isClient ? 'clients' : 'field officers'}.</div>`;
    }
}


/* ==========================================================================
   FILTER PERSON SEARCH
   ========================================================================== */
let clientSearchTimeout = null;
function filterSearchList() {
    const input = document.getElementById('t-search-input');
    const results = document.getElementById('search-results');
    const query = input.value.trim();
    // Clear Previous Request Timer
    clearTimeout(clientSearchTimeout);
    results.classList.remove('hidden');
    // Determine Selected Category
    const subType = document.getElementById('t-sub-type').value;
    const selectedCategory = transactionCategories.find(category => String(category.transaction_category_id) === String(subType));
    const categoryName = String(selectedCategory?.transaction_category_name || '').trim().toLowerCase();
    // Client Payment
    if (categoryName === 'client payment') {
        clientSearchTimeout = setTimeout(() => searchClients(query), 300);
        return;
    }
    // Officer Search
    const items = results.querySelectorAll('div');
    items.forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(query.toLowerCase()) ? 'block' : 'none';
    });
}

async function searchClients(search = '') {
    const results = document.getElementById('search-results');
    // Loading State
    results.innerHTML = `<div class="p-3 text-sm font-bold text-slate-400">Searching clients...</div>`;
    try {
        const formData = new FormData();
        formData.append('action', 'list');
        formData.append('search', search);
        const response = await fetch(CLIENT_ENDPOINT, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Unable to search clients.');
        }
        const clients = Array.isArray(result.data) ? result.data : [];
        // No Results
        if (clients.length === 0) {
            results.innerHTML = `<div class="p-3 text-sm font-bold text-slate-400">No active clients found.</div>`;
            return;
        }
        // Render Search Results
        results.innerHTML = clients.map(client => {
            const clientName = `${client.first_name} ${client.last_name}`;
            const safeName = escapeTransactionCategoryHtml(clientName);
            const safeId = escapeTransactionCategoryAttribute(client.client_id);
            const safeAttributeName = escapeTransactionCategoryAttribute(clientName);
            const location = client.location ? escapeTransactionCategoryHtml(client.location) : '';
            return `
                <div onclick="selectPerson('${safeId}', '${safeAttributeName}')" class="p-3 hover:bg-blue-50 cursor-pointer border-b border-slate-50 font-bold text-sm">
                    <div class="text-slate-700">${safeName}</div>
                    ${location ? `<div class="text-[10px] text-slate-400 mt-0.5">${location}</div>` : ''}
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('Error searching clients:', error);
        results.innerHTML = `<div class="p-3 text-sm font-bold text-red-500">Unable to search clients.</div>`;
    }
}


/* ==========================================================================
   SELECT PERSON
   ========================================================================== */

function selectPerson(id, name) {
    document.getElementById('t-search-input').value = name;
    document.getElementById('t-selected-id').value = id;
    document.getElementById('search-results').classList.add('hidden');
    document.getElementById('container-amount').classList.remove('hidden');
    document.getElementById('btn-save-transaction').classList.remove('hidden');
}