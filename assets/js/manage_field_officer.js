const FIELD_OFFICER_API = 'api/manage_field_officer.php';

/* Save a field officer */
async function saveOfficer() {
    const officerId = document.getElementById('of-id').value;
    const firstName = document.getElementById('of-fname').value.trim();
    const lastName = document.getElementById('of-lname').value.trim();
    const phone = document.getElementById('of-phone').value.trim();
    const email = document.getElementById('of-email').value.trim();
    const saveBtn = document.querySelector(
        'button[onclick="saveOfficer()"]'
    );

    if (!firstName || !lastName || !phone) {
        showToast('Please fill in all fields', 'error');
        return;
    }

    const action = officerId ? 'update' : 'create';
    const formData = new FormData();

    formData.append('action', action);
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('phone_number', phone);
    formData.append('email', email);

    if (officerId) {
        formData.append('officer_id', officerId);
    }

    const originalText = saveBtn.innerHTML;

    saveBtn.disabled = true;
    saveBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Processing...
    `;

    try {
        const response = await fetch(FIELD_OFFICER_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            showToast(result.message, 'success');

            const modalElement = document.getElementById('modal-officer');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            modalInstance.hide();
            resetOfficerForm();

            if (typeof listFieldOfficers === 'function') {
                listFieldOfficers();
            }

            if (typeof fetchOfficersForSelect === 'function') {
                fetchOfficersForSelect();
            }
        } else {
            showToast('Error: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Submission Error:', error);
        showToast(
            'An error occurred while saving the officer.',
            'error'
        );
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
}

// Helper function to clear form fields
function resetOfficerForm() {
    document.getElementById('of-id').value = "";
    document.getElementById('of-fname').value = "";
    document.getElementById('of-lname').value = "";
    document.getElementById('of-phone').value = "";
    document.getElementById('of-email').value = "";
    
    // Reset modal title and button text if you changed them for editing
    document.querySelector('#modal-officer h3').innerText = "Add Field Officer";
    document.querySelector('button[onclick="saveOfficer()"]').innerText = "Register Officer";
}

/* Pagination state for field officers */
let officerCurrentPage = 1;
const officerPageSize = 10;

/* Load and display field officers */
async function listFieldOfficers(searchTerm = '', page = 1) {
    const container = document.getElementById('officer-list');
    const paginationContainer = document.getElementById('officer-pagination');

    if (!container) {
        return;
    }

    officerCurrentPage = Math.max(1, Number(page) || 1);

    const formData = new FormData();
    formData.append('action', 'list');
    formData.append('search', searchTerm);
    formData.append('page', officerCurrentPage);
    formData.append('limit', officerPageSize);

    container.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-8">
                <div class="flex justify-center items-center gap-3 text-slate-500">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    <span>Loading officers...</span>
                </div>
            </td>
        </tr>
    `;

    if (paginationContainer) {
        paginationContainer.innerHTML = '';
    }

    try {
        const response = await fetch(FIELD_OFFICER_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Failed to load officers');
        }

        const data = result.data || {};
        const officers = Array.isArray(data.officers) ? data.officers : [];
        const totalOfficers = Number(data.total_count || 0);
        const totalPages = Math.ceil(totalOfficers / officerPageSize);

        if (officers.length === 0) {
            container.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-8 text-slate-500">
                        No officers found
                    </td>
                </tr>
            `;

            renderOfficerPagination(1, 0, 0, searchTerm);
            return;
        }

        let html = '';

        officers.forEach(officer => {
            const officerData = JSON.stringify(officer).replace(
                /"/g,
                '&quot;'
            );

            html += `
                <tr class="align-middle hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5 font-bold text-slate-700">
                        ${officer.first_name} ${officer.last_name}
                    </td>
                    <td class="px-6 py-5">
                        ${officer.email}
                    </td>
                    <td class="px-6 py-5">
                        ${officer.phone_number}
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-700">
                                ${officer.client_count}
                            </span>
                            <button
                                type="button"
                                onclick="viewOfficerClients('${officer.officer_id}', '${officer.first_name} ${officer.last_name}')"
                                class="bg-blue-100 text-blue-700 px-3 py-2 rounded-xl font-bold text-sm">
                                View Clients
                            </button>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex gap-2">
                            <button
                                type="button"
                                onclick="editOfficer(${officerData})"
                                class="bg-blue-100 text-blue-700 px-4 py-2 rounded-xl font-bold text-sm">
                                Edit
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        container.innerHTML = html;

        renderOfficerPagination(
            officerCurrentPage,
            totalPages,
            totalOfficers,
            searchTerm
        );
    } catch (error) {
        console.error('Fetch error:', error);

        container.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8 text-red-500">
                    Failed to load officers
                </td>
            </tr>
        `;

        if (paginationContainer) {
            paginationContainer.innerHTML = '';
        }

        showToast('Failed to load officers', 'error');
    }
}

/* Render field officer pagination */
function renderOfficerPagination(
    currentPage,
    totalPages,
    totalOfficers,
    searchTerm = ''
) {
    const paginationContainer = document.getElementById('officer-pagination');

    if (!paginationContainer) {
        return;
    }

    if (totalOfficers === 0) {
        paginationContainer.innerHTML = '';
        return;
    }

    const start = ((currentPage - 1) * officerPageSize) + 1;
    const end = Math.min(currentPage * officerPageSize, totalOfficers);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = `
            <div class="text-sm font-bold text-slate-500">
                Showing
                <span class="text-slate-900">${start}-${end}</span>
                of
                <span class="text-slate-900">${totalOfficers}</span>
                officers
            </div>
        `;
        return;
    }

    const safeSearchTerm = escapeOfficerSearch(searchTerm);
    const maxVisiblePages = 5;

    let startPage = Math.max(
        1,
        currentPage - Math.floor(maxVisiblePages / 2)
    );
    let endPage = Math.min(
        totalPages,
        startPage + maxVisiblePages - 1
    );

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    let pageButtons = '';

    for (let page = startPage; page <= endPage; page++) {
        const active = page === currentPage;

        pageButtons += `
            <button
                type="button"
                onclick="listFieldOfficers('${safeSearchTerm}', ${page})"
                class="min-w-[40px] h-10 px-3 rounded-xl font-bold text-sm transition-all ${
                    active
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20'
                        : 'bg-slate-50 text-slate-600 hover:bg-blue-50 hover:text-blue-600'
                }">
                ${page}
            </button>
        `;
    }

    paginationContainer.innerHTML = `
        <div class="text-sm font-bold text-slate-500 text-center sm:text-left">
            Showing
            <span class="text-slate-900">${start}-${end}</span>
            of
            <span class="text-slate-900">${totalOfficers}</span>
            officers
        </div>
        <div class="flex items-center justify-center gap-2 flex-wrap">
            <button
                type="button"
                onclick="listFieldOfficers('${safeSearchTerm}', ${currentPage - 1})"
                ${currentPage === 1 ? 'disabled' : ''}
                class="h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm transition-all hover:bg-blue-50 hover:text-blue-600 disabled:opacity-40 disabled:cursor-not-allowed">
                <i class="fas fa-chevron-left"></i>
                <span class="hidden sm:inline ml-1">Previous</span>
            </button>
            ${startPage > 1 ? `
                <button
                    type="button"
                    onclick="listFieldOfficers('${safeSearchTerm}', 1)"
                    class="min-w-[40px] h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm hover:bg-blue-50 hover:text-blue-600 transition-all">
                    1
                </button>
                ${startPage > 2
                    ? '<span class="px-1 text-slate-400 font-bold">...</span>'
                    : ''}
            ` : ''}
            ${pageButtons}
            ${endPage < totalPages ? `
                ${endPage < totalPages - 1
                    ? '<span class="px-1 text-slate-400 font-bold">...</span>'
                    : ''}
                <button
                    type="button"
                    onclick="listFieldOfficers('${safeSearchTerm}', ${totalPages})"
                    class="min-w-[40px] h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm hover:bg-blue-50 hover:text-blue-600 transition-all">
                    ${totalPages}
                </button>
            ` : ''}
            <button
                type="button"
                onclick="listFieldOfficers('${safeSearchTerm}', ${currentPage + 1})"
                ${currentPage === totalPages ? 'disabled' : ''}
                class="h-10 px-3 rounded-xl bg-slate-50 text-slate-600 font-bold text-sm transition-all hover:bg-blue-50 hover:text-blue-600 disabled:opacity-40 disabled:cursor-not-allowed">
                <span class="hidden sm:inline mr-1">Next</span>
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    `;
}

function escapeOfficerSearch(value) {
    return String(value || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

// edit officers
function editOfficer(officer) {
    try {

        document.getElementById('edit-of-id').value = officer.officer_id;
        document.getElementById('edit-of-fname').value = officer.first_name;
        document.getElementById('edit-of-lname').value = officer.last_name;
        document.getElementById('edit-of-phone').value = officer.phone_number;
        document.getElementById('edit-of-email').value = officer.email;

        const modalElement = document.getElementById('modal-edit-officer');
        const modal = new bootstrap.Modal(modalElement);

        modal.show();

    } catch (error) {

        console.error("Edit officer error:", error);
        showToast("Failed to load officer details", "error");

    }
}

/* Update an existing field officer */
async function updateOfficer() {
    const officerId = document.getElementById('edit-of-id').value;
    const firstName = document.getElementById('edit-of-fname').value.trim();
    const lastName = document.getElementById('edit-of-lname').value.trim();
    const phone = document.getElementById('edit-of-phone').value.trim();
    const email = document.getElementById('edit-of-email').value.trim();
    const updateBtn = document.querySelector(
        'button[onclick="updateOfficer()"]'
    );

    if (!officerId || !firstName || !lastName || !phone || !email) {
        showToast('Please fill in all fields', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('officer_id', officerId);
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('phone_number', phone);
    formData.append('email', email);

    const originalText = updateBtn.innerHTML;

    updateBtn.disabled = true;
    updateBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Updating...
    `;

    try {
        const response = await fetch(FIELD_OFFICER_API, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            showToast(result.message, 'success');

            const modalElement = document.getElementById(
                'modal-edit-officer'
            );
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (modalInstance) {
                modalInstance.hide();
            }

            if (typeof listFieldOfficers === 'function') {
                listFieldOfficers();
            }
        } else {
            showToast(result.message, 'error');
        }
    } catch (error) {
        console.error('Update officer error:', error);
        showToast('Failed to update officer', 'error');
    } finally {
        updateBtn.disabled = false;
        updateBtn.innerHTML = originalText;
    }
}

// delete officer
function openDeleteOfficer(id, name) {

    document.getElementById('delete-of-id').value = id;
    document.getElementById('delete-of-name').innerText = name;

    const modalElement = document.getElementById('modal-delete-officer');
    const modal = new bootstrap.Modal(modalElement);

    modal.show();
}

async function deleteOfficer() {

    const officerId = document.getElementById('delete-of-id').value;
    const deleteBtn = document.getElementById('confirm-delete-officer');

    if (!officerId) {
        showToast("Invalid officer selected", "error");
        return;
    }

    const originalText = deleteBtn.innerHTML;

    deleteBtn.disabled = true;
    deleteBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm text-white me-2" role="status" aria-hidden="true"></span>
        <span class="text-white">Deleting...</span>
    `;

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('officer_id', officerId);

    try {

        const response = await fetch(FIELD_OFFICER_API, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.status === "success") {

            showToast(result.message, "success");

            const modalElement = document.getElementById('modal-delete-officer');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();

            listFieldOfficers();

        } else {

            showToast(result.message, "error");

        }

    } catch (error) {

        console.error("Delete officer error:", error);
        showToast("Failed to delete officer", "error");

    } finally {

        deleteBtn.disabled = false;
        deleteBtn.innerHTML = originalText;

    }
}

//search field officers
let searchTimer;

/* Handle input from the officer search bar */
function handleOfficerSearch(query) {
    const clearBtn = document.getElementById('clear-search-btn');

    if (query.length > 0) {
        clearBtn.classList.remove('hidden');
    } else {
        clearBtn.classList.add('hidden');
    }

    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        listFieldOfficers(query);
    }, 300);
}

/* Clear the officer search and reload the full list */
function clearOfficerSearch() {
    const searchInput = document.getElementById('officer-search');
    const clearBtn = document.getElementById('clear-search-btn');

    searchInput.value = '';
    clearBtn.classList.add('hidden');
    searchInput.focus();

    listFieldOfficers('');
}
// initialize the data.
document.addEventListener('DOMContentLoaded', function() {
    listFieldOfficers();
});