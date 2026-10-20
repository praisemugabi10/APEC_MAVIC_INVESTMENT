
const CLIENT_API = 'api/manage_client.php';
const OFFICER_API_URL = 'api/manage_field_officer.php'; // To fetch officers for the dropdown

// Initialize the page and register event listeners.
document.addEventListener('DOMContentLoaded', function () {
    listClients();
    fetchOfficersForSelect();

    document.getElementById('btn-add-client').addEventListener('click', () => {
        resetClientForm();
        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('modal-client')
        ).show();
    });

    document.getElementById('client-search-input').addEventListener('input', (e) => {
        handleClientSearch(e.target.value);
    });

    const amountInput = document.getElementById('cl-amount');
    const interestInput = document.getElementById('cl-interest');

    [amountInput, interestInput].forEach(el => {
        el.addEventListener('input', calculateExpectedReturn);
    });
});

function resetClientForm() {
    document.getElementById('form-add-client').reset();
    document.getElementById('cl-existing-client-id').value = '';
    document.getElementById('cl-return').value = '0';
    document.getElementById('client-modal-title').textContent = 'Register New Client';
    document.getElementById('btn-save-client').textContent = 'Register Client';
}

/**
 * 2. UTILITY: FORMAT CURRENCY
 */
function formatUGX(amount) {
    return new Intl.NumberFormat('en-UG', {
        style: 'currency',
        currency: 'UGX',
        maximumFractionDigits: 0
    }).format(amount);
}

/**
 * 3. CALCULATION LOGIC
 */
function calculateExpectedReturn() {
    const principal = parseFloat(document.getElementById('cl-amount').value) || 0;
    const interestRate = parseFloat(document.getElementById('cl-interest').value) || 0;
    const expected = principal + (principal * (interestRate / 100));
    
    document.getElementById('cl-return').value = expected;
}

/**
 * 4. FETCH OFFICERS FOR DROPDOWN
 */
// Fetch field officers and populate the selection dropdown.
async function fetchOfficersForSelect() {
    const optionsContainer = document.getElementById('field-officer-options');
    const formData = new FormData();
    formData.append('action', 'list');

    try {
        const response = await fetch(OFFICER_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            let options = '';

            result.data.officers.forEach(of => {
                const officerId = escapeHTML(decodeHTML(of.officer_id || ''));
                const firstName = escapeHTML(decodeHTML(of.first_name || ''));
                const lastName = escapeHTML(decodeHTML(of.last_name || ''));
                const officerName = escapeHTML(
                    decodeHTML(`${of.first_name || ''} ${of.last_name || ''}`)
                ).replace(/'/g, '&#039;');

                options += `
                    <button
                        type="button"
                        class="w-full px-4 py-3 text-left text-sm font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                        onclick="selectFieldOfficer('${officerId}', '${officerName}', this)">
                        ${firstName} ${lastName}
                    </button>
                `;
            });

            optionsContainer.innerHTML = options || `
                <div class="px-4 py-4 text-sm text-slate-400 text-center">
                    No officers found
                </div>
            `;
        }
    } catch (error) {
        console.error('Failed to fetch officers', error);
    }
}

// Toggle the field officer dropdown and reset its search.
function toggleFieldOfficerDropdown() {
    const dropdown = document.getElementById('field-officer-dropdown');

    if (!dropdown) return;

    dropdown.classList.toggle('hidden');

    if (!dropdown.classList.contains('hidden')) {
        const search = document.getElementById('field-officer-search');

        if (search) {
            search.value = '';
            search.focus();
        }

        filterFieldOfficers('');
    }
}

// Set the selected field officer and close the dropdown.
function selectFieldOfficer(officerId, officerName) {
    const officerInput = document.getElementById('cl-officer-id');
    const selected = document.getElementById('field-officer-selected');
    const dropdown = document.getElementById('field-officer-dropdown');

    if (officerInput) {
        officerInput.value = decodeHTML(officerId);
    }

    if (selected) {
        selected.textContent = decodeHTML(officerName);
        selected.classList.remove('text-slate-400');
        selected.classList.add('text-slate-900');
    }

    if (dropdown) {
        dropdown.classList.add('hidden');
    }
}

// Filter the field officer options by name.
function filterFieldOfficers(searchTerm) {
    const optionsContainer = document.getElementById('field-officer-options');

    if (!optionsContainer) return;

    const search = decodeHTML(searchTerm || '').toLowerCase().trim();
    const options = optionsContainer.querySelectorAll('button');

    options.forEach(option => {
        const officerName = decodeHTML(option.textContent || '').toLowerCase();

        option.style.display = officerName.includes(search) ? '' : 'none';
    });
}

/**
 * 5. CREATE CLIENT
 */
async function saveClient() {
    const saveBtn = document.getElementById('btn-save-client');
    const formData = new FormData();

    // Mapping IDs to Form Data keys
    const fields = {
        'action': 'create',
        'first_name': 'cl-fname',
        'last_name': 'cl-lname',
        'phone_primary': 'cl-phone-1',
        'phone_secondary': 'cl-phone-2',
        'location': 'cl-location',
        'mortgage': 'cl-mortgage',
        'officer_id': 'cl-officer-id',
        'principal_amount': 'cl-amount',
        'interest_rate': 'cl-interest',
        'total_repayable': 'cl-return',
        'g_first_name': 'cl-g-fname',
        'g_last_name': 'cl-g-lname',
        'g_phone': 'cl-g-phone',
        'g_location': 'cl-g-location'
    };

    for (let key in fields) {
        if (key === "action") {
            formData.append("action", "create");
            continue;
        }

        const inputElement = document.getElementById(fields[key]);

        if (!inputElement) {
            console.error(`Missing element with id: ${fields[key]}`);
            continue;
        }

        formData.append(key, inputElement.value.trim());
    }

    const existingClientId = document.getElementById('cl-existing-client-id').value.trim();
    if (existingClientId) {
        formData.append('client_id', existingClientId);
    }

    // Basic Validation
    if (!formData.get('first_name') || !formData.get('principal_amount') || !formData.get('officer_id')) {
        showToast("Please fill in required fields (Name, Amount, Officer)", "error");
        return;
    }

    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Processing...`;

    try {
        const response = await fetch(CLIENT_API, { method: 'POST', body: formData });
        const result = await response.json();

        if (result.status === "success") {
            showToast(result.message, "success");
            bootstrap.Modal.getInstance(document.getElementById('modal-client'))?.hide();
            resetClientForm();

            if (typeof refreshOfficerClientsIfOpen === 'function') {
                refreshOfficerClientsIfOpen();
            }

            listFieldOfficers();
            listClients();
        } else {
            showToast(result.message, "error");
        }
    } catch (error) {
        showToast("Server error occurred", "error");
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
}

/**
 * 6. READ / LIST CLIENTS
 */
let clientCurrentPage = 1;
const clientPageSize = 10;
let clientStatus = 'active';

// switch between active and inactive clients
// Switch between active and inactive clients and refresh the list.
function switchClientTab(status) {
    clientStatus = status === 'inactive' ? 'inactive' : 'active';

    const activeTab = document.getElementById('client-active-tab');
    const inactiveTab = document.getElementById('client-inactive-tab');

    if (activeTab && inactiveTab) {
        if (clientStatus === 'active') {
            activeTab.className =
                'px-5 py-3 text-sm font-bold rounded-t-xl border-b-2 border-red-600 text-red-600 bg-red-50 transition-all flex items-center gap-2';
            inactiveTab.className =
                'px-5 py-3 text-sm font-bold rounded-t-xl border-b-2 border-transparent text-slate-500 hover:text-green-600 hover:bg-green-50 transition-all flex items-center gap-2';
        } else {
            inactiveTab.className =
                'px-5 py-3 text-sm font-bold rounded-t-xl border-b-2 border-green-600 text-green-600 bg-green-50 transition-all flex items-center gap-2';
            activeTab.className =
                'px-5 py-3 text-sm font-bold rounded-t-xl border-b-2 border-transparent text-slate-500 hover:text-red-600 hover:bg-red-50 transition-all flex items-center gap-2';
        }
    }

    const searchInput = document.getElementById('client-search-input');
    const searchTerm = searchInput
        ? decodeHTML(searchInput.value || '')
        : '';

    listClients(searchTerm, 1);
}

async function listClients(searchTerm = "", page = 1) {
    const container = document.getElementById('client-list');
    const paginationContainer = document.getElementById('client-pagination');

    if (!container) return;

    clientCurrentPage = Math.max(1, Number(page) || 1);

    const formData = new FormData();
    formData.append('action', 'list');
    formData.append('search', searchTerm);
    formData.append('page', clientCurrentPage);
    formData.append('limit', clientPageSize);
    formData.append('status', clientStatus);

    container.innerHTML = `
        <tr>
            <td colspan="11" class="px-8 py-10 text-center text-slate-400">
                <div class="flex flex-col items-center gap-2">
                    <i class="fas fa-spinner fa-spin text-3xl opacity-50"></i>
                    <p>Loading clients...</p>
                </div>
            </td>
        </tr>
    `;

    if (paginationContainer) paginationContainer.innerHTML = '';

    try {
        const response = await fetch(CLIENT_API, { method: 'POST', body: formData });
        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Failed to load clients.');
        }

        const data = result.data || {};
        const clients = Array.isArray(data.clients) ? data.clients : [];
        const totalClients = Number(data.total_count || 0);
        const totalPages = Math.ceil(totalClients / clientPageSize);

        if (clients.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="11" class="px-8 py-10 text-center text-slate-400">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-users text-4xl opacity-20"></i>
                            <p>No clients found</p>
                        </div>
                    </td>
                </tr>
            `;

            renderClientPagination(1, 0, 0, searchTerm);
            return;
        }

        let html = "";

        clients.forEach(client => {
            const clientId = escapeHTML(decodeHTML(client.client_id || ''));
            const firstName = escapeHTML(decodeHTML(client.first_name || ''));
            const lastName = escapeHTML(decodeHTML(client.last_name || ''));
            const phonePrimary = escapeHTML(decodeHTML(client.phone_primary || ''));
            const phoneSecondary = decodeHTML(client.phone_secondary || '');
            const location = escapeHTML(decodeHTML(client.location || ''));
            const mortgage = decodeHTML(client.mortgage || '');
            const dateTaken = escapeHTML(decodeHTML(client.date_taken || ''));
            const interestRate = escapeHTML(decodeHTML(client.interest_rate || ''));
            const officerFirstName = escapeHTML(decodeHTML(client.officer_fname || ''));
            const officerLastName = escapeHTML(decodeHTML(client.officer_lname || ''));
            const guarantorFirstName = escapeHTML(decodeHTML(client.g_first_name || ''));
            const guarantorLastName = escapeHTML(decodeHTML(client.g_last_name || ''));
            const guarantorPhone = escapeHTML(decodeHTML(client.g_phone || ''));

            const secondary = phoneSecondary
                ? `<br><small class="text-slate-400">${escapeHTML(phoneSecondary)}</small>`
                : '';

            const mortgageDisplay = mortgage
                ? escapeHTML(mortgage)
                : '<span class="text-slate-400 italic">No mortgage</span>';

            const clientData = JSON.stringify(client)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            const deleteName = escapeHTML(decodeHTML(`${client.first_name || ''} ${client.last_name || ''}`))
                .replace(/'/g, '&#039;');

            const rowBorderClass = clientStatus === 'active'
                ? 'border-red-100'
                : 'border-green-100';

            html += `
                <tr class="align-middle border-b ${rowBorderClass} hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="font-black text-slate-900">${firstName} ${lastName}</div>
                    </td>
                    <td class="px-6 py-5 text-sm">
                        <span class="font-bold">${phonePrimary}</span>${secondary}
                    </td>
                    <td class="px-6 py-5 text-sm">${location}</td>
                    <td class="px-6 py-5 font-bold">${formatUGX(client.principal_amount)}</td>
                    <td class="px-6 py-5 text-sm">${dateTaken}</td>
                    <td class="px-6 py-5 text-blue-700 font-black">${interestRate}%</td>
                    <td class="px-6 py-5 text-emerald-700 font-black">${formatUGX(client.total_repayable)}</td>
                    <td class="px-6 py-5 text-sm">${mortgageDisplay}</td>
                    <td class="px-6 py-5 text-sm">
                        <div class="font-bold text-slate-800">${officerFirstName} ${officerLastName}</div>
                    </td>
                    <td class="px-6 py-5 text-sm">
                        <div class="font-bold text-slate-800">${guarantorFirstName} ${guarantorLastName}</div>
                        <div class="text-[10px] text-slate-400">${guarantorPhone}</div>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex justify-center gap-2">
                            <button onclick="openEditClient(${clientData})" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="openDeleteClient('${clientId}', '${deleteName}')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        container.innerHTML = html;

        renderClientPagination(clientCurrentPage, totalPages, totalClients, searchTerm);

    } catch (error) {
        console.error('Client loading error:', error);

        container.innerHTML = `
            <tr>
                <td colspan="11" class="px-8 py-10 text-center text-red-400">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-exclamation-circle text-4xl opacity-50"></i>
                        <p>Failed to load clients</p>
                    </div>
                </td>
            </tr>
        `;

        if (paginationContainer) paginationContainer.innerHTML = '';

        showToast("Failed to load clients", "error");
    }
}

// client pagination
function renderClientPagination(currentPage, totalPages, totalClients, searchTerm = "") {
    const paginationContainer = document.getElementById('client-pagination');

    if (!paginationContainer) return;

    if (totalClients === 0) {
        paginationContainer.innerHTML = '';
        return;
    }

    const start = ((currentPage - 1) * clientPageSize) + 1;
    const end = Math.min(currentPage * clientPageSize, totalClients);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = `
            <div class="text-sm font-bold text-slate-500">
                Showing <span class="text-slate-900">${start}-${end}</span> of <span class="text-slate-900">${totalClients}</span> clients
            </div>
        `;
        return;
    }

    const safeSearchTerm = escapeClientSearch(searchTerm);
    const maxVisiblePages = 5;

    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    let pageButtons = '';

    for (let page = startPage; page <= endPage; page++) {
        const active = page === currentPage;

        pageButtons += `
            <button type="button" onclick="listClients('${safeSearchTerm}', ${page})" class="min-w-[40px] h-10 px-3 rounded-xl font-bold text-sm transition-all ${active ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600'}">
                ${page}
            </button>
        `;
    }

    paginationContainer.innerHTML = `
        <div class="text-sm font-bold text-slate-500 text-center sm:text-left">
            Showing <span class="text-slate-900">${start}-${end}</span> of <span class="text-slate-900">${totalClients}</span> clients
        </div>

        <div class="flex items-center justify-center gap-2 flex-wrap">
            <button type="button" onclick="listClients('${safeSearchTerm}', ${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm transition-all hover:bg-blue-50 hover:text-blue-600 disabled:opacity-40 disabled:cursor-not-allowed">
                <i class="fas fa-chevron-left"></i>
                <span class="hidden sm:inline ml-1">Previous</span>
            </button>

            ${startPage > 1 ? `
                <button type="button" onclick="listClients('${safeSearchTerm}', 1)" class="min-w-[40px] h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm hover:bg-blue-50 hover:text-blue-600 transition-all">1</button>
                ${startPage > 2 ? '<span class="px-1 text-slate-400 font-bold">...</span>' : ''}
            ` : ''}

            ${pageButtons}

            ${endPage < totalPages ? `
                ${endPage < totalPages - 1 ? '<span class="px-1 text-slate-400 font-bold">...</span>' : ''}
                <button type="button" onclick="listClients('${safeSearchTerm}', ${totalPages})" class="min-w-[40px] h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm hover:bg-blue-50 hover:text-blue-600 transition-all">${totalPages}</button>
            ` : ''}

            <button type="button" onclick="listClients('${safeSearchTerm}', ${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm transition-all hover:bg-blue-50 hover:text-blue-600 disabled:opacity-40 disabled:cursor-not-allowed">
                <span class="hidden sm:inline mr-1">Next</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    `;
}

function escapeClientSearch(value) {
    return String(value || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

/**
 * 7. UPDATE CLIENT
 */
function openEditClient(client) {
    document.getElementById('edit-cl-id').value = client.client_id;
    document.getElementById('edit-cl-fname').value = client.first_name;
    document.getElementById('edit-cl-lname').value = client.last_name;
    document.getElementById('edit-cl-location').value = client.location;
    document.getElementById('edit-cl-mortgage').value = client.mortgage || '';

    new bootstrap.Modal(document.getElementById('modal-edit-client')).show();
}

async function updateClient() {
    const updateBtn = document.querySelector('button[onclick="updateClient()"]');
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('client_id', document.getElementById('edit-cl-id').value);
    formData.append('first_name', document.getElementById('edit-cl-fname').value);
    formData.append('last_name', document.getElementById('edit-cl-lname').value);
    formData.append('location', document.getElementById('edit-cl-location').value);
    formData.append('mortgage', document.getElementById('edit-cl-mortgage').value);

    updateBtn.disabled = true;
    try {
        const response = await fetch(CLIENT_API, { method: 'POST', body: formData });
        const result = await response.json();
        if (result.status === "success") {
            showToast(result.message, "success");
            bootstrap.Modal.getInstance(document.getElementById('modal-edit-client')).hide();
            listClients();
        }
    } catch (e) { showToast("Update failed", "error"); }
    finally { updateBtn.disabled = false; }
}

/**
 * 8. DELETE CLIENT (Soft Delete)
 */
function openDeleteClient(id, name) {
    document.getElementById('delete-cl-id').value = id;
    document.getElementById('delete-cl-name').innerText = name;
    new bootstrap.Modal(document.getElementById('modal-delete-client')).show();
}

async function deleteClient() {
    const button = document.querySelector('#modal-delete-client button[onclick="deleteClient()"]');

    if (!button) return;

    const originalContent = button.innerHTML;

    button.disabled = true;
    button.innerHTML = `
        <i class="fas fa-spinner fa-spin me-2"></i> Deactivating...
    `;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('client_id', document.getElementById('delete-cl-id').value);

    try {
        const response = await fetch(CLIENT_API, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
            showToast(result.message, "success");

            bootstrap.Modal.getInstance(
                document.getElementById('modal-delete-client')
            ).hide();

            listClients();
        } else {
            showToast(result.message || "Unable to deactivate client", "error");

            button.disabled = false;
            button.innerHTML = originalContent;
        }

    } catch (e) {
        console.error("Delete client error:", e);

        showToast("Deletion failed", "error");

        button.disabled = false;
        button.innerHTML = originalContent;
    }
}

/**
 * 9. SEARCH DEBOUNCING
 */
let clientSearchTimer;
function handleClientSearch(query) {
    clearTimeout(clientSearchTimer);
    clientSearchTimer = setTimeout(() => {
        listClients(query);
    }, 400);
}