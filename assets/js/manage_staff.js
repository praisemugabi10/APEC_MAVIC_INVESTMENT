const STAFF_API_URL = 'api/manage_staff.php';

let staffManagementData = [];
let currentStaffFilter = 'all';
let currentStaffSearch = '';
let currentStaffPage = 1;
const staffPerPage = 10;

/* Load staff */
async function loadStaff() {
    const staffList = document.getElementById('staff-list');

    staffList.innerHTML = `
        <tr>
            <td colspan="6" class="px-8 py-10 text-center text-slate-400">
                <div class="flex flex-col items-center gap-2">
                    <i class="fas fa-spinner fa-spin text-3xl"></i>
                    <p>Loading staff...</p>
                </div>
            </td>
        </tr>
    `;

    const formData = new FormData();
    formData.append('action', 'list');

    try {
        const response = await fetch(STAFF_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            staffList.innerHTML = `
                <tr>
                    <td colspan="6" class="px-8 py-10 text-center text-red-400">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-exclamation-circle text-3xl"></i>
                            <p>
                                ${escapeHTML(decodeHTML(
                                    result.message || 'Failed to load staff.'
                                ))}
                            </p>
                        </div>
                    </td>
                </tr>
            `;

            showToast(result.message || 'Failed to load staff.', 'error');
            return;
        }

        staffManagementData = Array.isArray(result.data) ? result.data : [];
        currentStaffPage = 1;
        renderStaff();
    } catch (error) {
        console.error('Error loading staff:', error);

        staffList.innerHTML = `
            <tr>
                <td colspan="6" class="px-8 py-10 text-center text-red-400">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-exclamation-circle text-3xl"></i>
                        <p>Unable to load staff.</p>
                    </div>
                </td>
            </tr>
        `;

        showToast('Unable to load staff.', 'error');
    }
}

/* Render staff */
function renderStaff() {
    const staffList = document.getElementById('staff-list');
    const filteredStaff = staffManagementData.filter(function(staff) {
        const matchesFilter = currentStaffFilter === 'all' ||
            staff.type === currentStaffFilter;
        const searchValue = currentStaffSearch.toLowerCase();
        const firstName = decodeHTML(staff.first_name || '');
        const lastName = decodeHTML(staff.last_name || '');
        const fullName = `${firstName} ${lastName}`.toLowerCase();
        const email = decodeHTML(staff.email || '').toLowerCase();
        const phone = decodeHTML(staff.phone_number || '').toLowerCase();
        const matchesSearch = !searchValue ||
            fullName.includes(searchValue) ||
            email.includes(searchValue) ||
            phone.includes(searchValue);

        return matchesFilter && matchesSearch;
    });

    const totalPages = Math.max(
        1,
        Math.ceil(filteredStaff.length / staffPerPage)
    );

    if (currentStaffPage > totalPages) {
        currentStaffPage = totalPages;
    }

    const startIndex = (currentStaffPage - 1) * staffPerPage;
    const paginatedStaff = filteredStaff.slice(
        startIndex,
        startIndex + staffPerPage
    );

    if (paginatedStaff.length === 0) {
        staffList.innerHTML = `
            <tr>
                <td colspan="6" class="px-8 py-12 text-center text-slate-400">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fas fa-users-slash text-4xl opacity-30"></i>
                        <p class="font-semibold">No staff found.</p>
                        <p class="text-sm">
                            Try changing your search or filter.
                        </p>
                    </div>
                </td>
            </tr>
        `;

        renderStaffPagination(0, 0);
        return;
    }

    staffList.innerHTML = paginatedStaff.map(function(staff) {
        const firstName = decodeHTML(staff.first_name || '');
        const lastName = decodeHTML(staff.last_name || '');
        const email = decodeHTML(staff.email || '');
        const phone = decodeHTML(staff.phone_number || '');
        const fullName = `${firstName} ${lastName}`.trim();
        const roleLabel = staff.type === 'field_officer'
            ? 'Field Officer'
            : 'Cashier';
        const roleBadge = staff.type === 'field_officer'
            ? 'bg-blue-50 text-blue-700'
            : 'bg-purple-50 text-purple-700';
        const statusBadge = Number(staff.active) === 1
            ? 'bg-emerald-50 text-emerald-700'
            : 'bg-red-50 text-red-700';
        const statusText = Number(staff.active) === 1
            ? 'Active'
            : 'Inactive';
        const userAccountHtml = staff.user_id
            ? `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                    <i class="fas fa-check-circle mr-1.5"></i>
                    User Account
                </span>
            `
            : `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                    No User Account
                </span>
            `;
        const actionHtml = Number(staff.active) === 1
            ? `
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="openEditStaffModal('${escapeJS(staff.staff_id)}')"
                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-all"
                        title="Edit Staff">
                        <i class="fas fa-edit text-sm"></i>
                    </button>
                    <button
                        type="button"
                        onclick="deactivateStaff('${escapeJS(staff.staff_id)}')"
                        class="w-9 h-9 rounded-full bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-all"
                        title="Deactivate Staff">
                        <i class="fas fa-user-slash text-sm"></i>
                    </button>
                </div>
            `
            : `
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="openEditStaffModal('${escapeJS(staff.staff_id)}')"
                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition-all"
                        title="Edit Staff">
                        <i class="fas fa-edit text-sm"></i>
                    </button>
                    <button
                        type="button"
                        onclick="reactivateStaff('${escapeJS(staff.staff_id)}')"
                        class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center transition-all"
                        title="Reactivate Staff">
                        <i class="fas fa-user-check text-sm"></i>
                    </button>
                </div>
            `;

        return `
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black shrink-0">
                            ${escapeHTML(
                                decodeHTML(firstName.charAt(0) || 'S')
                            ).toUpperCase()}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">
                                ${escapeHTML(decodeHTML(fullName))}
                            </p>
                            <div class="mt-1">
                                ${userAccountHtml}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold ${roleBadge}">
                        ${escapeHTML(decodeHTML(roleLabel))}
                    </span>
                </td>
                <td class="px-6 py-5">
                    <span class="text-sm">
                        ${email
                            ? escapeHTML(decodeHTML(email))
                            : '<span class="text-slate-400">Not provided</span>'}
                    </span>
                </td>
                <td class="px-6 py-5">
                    <span class="text-sm">
                        ${phone
                            ? escapeHTML(decodeHTML(phone))
                            : '<span class="text-slate-400">Not provided</span>'}
                    </span>
                </td>
                <td class="px-6 py-5">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold ${statusBadge}">
                        ${escapeHTML(decodeHTML(statusText))}
                    </span>
                </td>
                <td class="px-6 py-5">
                    ${actionHtml}
                </td>
            </tr>
        `;
    }).join('');

    renderStaffPagination(filteredStaff.length, totalPages);
}

/* Render staff pagination */
function renderStaffPagination(totalRecords, totalPages) {
    const pagination = document.getElementById('staff-pagination');

    if (!totalRecords) {
        pagination.innerHTML = '';
        return;
    }

    const startRecord = ((currentStaffPage - 1) * staffPerPage) + 1;
    const endRecord = Math.min(
        currentStaffPage * staffPerPage,
        totalRecords
    );

    let paginationButtons = '';

    paginationButtons += `
        <button
            type="button"
            onclick="changeStaffPage(${currentStaffPage - 1})"
            ${currentStaffPage === 1 ? 'disabled' : ''}
            class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all ${
                currentStaffPage === 1
                    ? 'bg-slate-100 text-slate-300 cursor-not-allowed'
                    : 'bg-white text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200'
            }">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
    `;

    const maxVisiblePages = 5;
    let startPage = Math.max(
        1,
        currentStaffPage - Math.floor(maxVisiblePages / 2)
    );
    let endPage = Math.min(
        totalPages,
        startPage + maxVisiblePages - 1
    );

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(
            1,
            endPage - maxVisiblePages + 1
        );
    }

    for (let page = startPage; page <= endPage; page++) {
        paginationButtons += `
            <button
                type="button"
                onclick="changeStaffPage(${page})"
                class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all ${
                    page === currentStaffPage
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'bg-white text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200'
                }">
                ${page}
            </button>
        `;
    }

    paginationButtons += `
        <button
            type="button"
            onclick="changeStaffPage(${currentStaffPage + 1})"
            ${currentStaffPage === totalPages ? 'disabled' : ''}
            class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all ${
                currentStaffPage === totalPages
                    ? 'bg-slate-100 text-slate-300 cursor-not-allowed'
                    : 'bg-white text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200'
            }">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
    `;

    pagination.innerHTML = `
        <p class="text-sm text-slate-500 font-medium">
            Showing
            <span class="font-bold text-slate-700">${startRecord}</span>
            -
            <span class="font-bold text-slate-700">${endRecord}</span>
            of
            <span class="font-bold text-slate-700">${totalRecords}</span>
        </p>
        <div class="flex items-center gap-2">
            ${paginationButtons}
        </div>
    `;
}

/* Change the current staff page */
function changeStaffPage(page) {
    const filteredStaff = staffManagementData.filter(function(staff) {
        const matchesFilter = currentStaffFilter === 'all' ||
            staff.type === currentStaffFilter;
        const searchValue = currentStaffSearch.toLowerCase();
        const fullName = (
            `${decodeHTML(staff.first_name || '')} ` +
            `${decodeHTML(staff.last_name || '')}`
        ).toLowerCase();
        const email = decodeHTML(staff.email || '').toLowerCase();
        const phone = decodeHTML(staff.phone_number || '').toLowerCase();
        const matchesSearch = !searchValue ||
            fullName.includes(searchValue) ||
            email.includes(searchValue) ||
            phone.includes(searchValue);

        return matchesFilter && matchesSearch;
    });

    const totalPages = Math.max(
        1,
        Math.ceil(filteredStaff.length / staffPerPage)
    );

    if (page < 1 || page > totalPages) {
        return;
    }

    currentStaffPage = page;
    renderStaff();
}

/* Handle staff search */
function handleStaffSearch(value) {
    currentStaffSearch = decodeHTML(value || '').trim().toLowerCase();
    currentStaffPage = 1;

    const clearButton = document.getElementById('staff-search-clear');
    const hasText = currentStaffSearch.length > 0;

    clearButton.classList.toggle('hidden', !hasText);
    clearButton.classList.toggle('flex', hasText);

    renderStaff();
}

/* Clear staff search */
function clearStaffSearch() {
    const searchInput = document.getElementById('staff-search');

    searchInput.value = '';
    currentStaffSearch = '';
    currentStaffPage = 1;

    const clearButton = document.getElementById('staff-search-clear');

    clearButton.classList.add('hidden');
    clearButton.classList.remove('flex');

    renderStaff();
    searchInput.focus();
}

/* Filter staff by type */
function filterStaff(type) {
    currentStaffFilter = decodeHTML(type || 'all');
    currentStaffPage = 1;

    const filterButtons = [
        'staff-filter-all',
        'staff-filter-field-officer',
        'staff-filter-cashier'
    ];

    filterButtons.forEach(function(buttonId) {
        const button = document.getElementById(buttonId);

        button.classList.remove('bg-blue-600', 'text-white');
        button.classList.add('bg-slate-100', 'text-slate-600');
    });

    let activeButtonId = 'staff-filter-all';

    if (currentStaffFilter === 'field_officer') {
        activeButtonId = 'staff-filter-field-officer';
    }

    if (currentStaffFilter === 'cashier') {
        activeButtonId = 'staff-filter-cashier';
    }

    const activeButton = document.getElementById(activeButtonId);

    activeButton.classList.remove('bg-slate-100', 'text-slate-600');
    activeButton.classList.add('bg-blue-600', 'text-white');

    renderStaff();
}

/* Open the add staff modal */
function openAddStaffModal() {
    document.getElementById('staff-form').reset();
    openModal('modal-staff');
}

/* Save a new staff member */
async function saveStaff(event) {
    event.preventDefault();

    const saveButton = document.getElementById('save-staff-btn');
    const saveButtonText = document.getElementById('save-staff-btn-text');
    const saveSpinner = document.getElementById('save-staff-spinner');

    const firstName = decodeHTML(
        document.getElementById('staff-first-name').value
    ).trim();
    const lastName = decodeHTML(
        document.getElementById('staff-last-name').value
    ).trim();
    const email = decodeHTML(
        document.getElementById('staff-email').value
    ).trim();
    const phoneNumber = decodeHTML(
        document.getElementById('staff-phone').value
    ).trim();
    const type = decodeHTML(
        document.getElementById('staff-type').value
    ).trim();

    if (!firstName || !lastName || !type) {
        showToast('Please fill in all required fields.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('phone_number', phoneNumber);
    formData.append('type', type);

    saveButton.disabled = true;
    saveButtonText.textContent = 'Adding...';
    saveSpinner.classList.remove('hidden');

    try {
        const response = await fetch(STAFF_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(result.message || 'Failed to add staff.', 'error');
            return;
        }

        closeModal('modal-staff');
        showToast(
            result.message || 'Staff added successfully.',
            'success'
        );

        await loadStaff();

        if (typeof fetchOfficersForSelect === 'function') {
            fetchOfficersForSelect();
        }
    } catch (error) {
        console.error('Error adding staff:', error);
        showToast('Unable to add staff.', 'error');
    } finally {
        saveButton.disabled = false;
        saveButtonText.textContent = 'Add Staff';
        saveSpinner.classList.add('hidden');
    }
}

/* Open the edit staff modal */
function openEditStaffModal(staffId) {
    const cleanStaffId = decodeHTML(staffId || '');
    const staff = staffManagementData.find(function(item) {
        return decodeHTML(item.staff_id || '') === cleanStaffId;
    });

    if (!staff) {
        showToast('Staff record not found.', 'error');
        return;
    }

    document.getElementById('edit-staff-id').value =
        decodeHTML(staff.staff_id || '');
    document.getElementById('edit-staff-first-name').value =
        decodeHTML(staff.first_name || '');
    document.getElementById('edit-staff-last-name').value =
        decodeHTML(staff.last_name || '');
    document.getElementById('edit-staff-email').value =
        decodeHTML(staff.email || '');
    document.getElementById('edit-staff-phone').value =
        decodeHTML(staff.phone_number || '');

    const typeLabel = staff.type === 'field_officer'
        ? 'Field Officer'
        : 'Cashier';

    document.getElementById('edit-staff-type').textContent =
        decodeHTML(typeLabel);

    openModal('modal-edit-staff');
}

/* Update an existing staff member */
async function updateStaff(event) {
    event.preventDefault();

    const updateButton = document.getElementById('update-staff-btn');
    const updateButtonText = document.getElementById('update-staff-btn-text');
    const updateSpinner = document.getElementById('update-staff-spinner');

    const staffId = decodeHTML(
        document.getElementById('edit-staff-id').value
    ).trim();
    const firstName = decodeHTML(
        document.getElementById('edit-staff-first-name').value
    ).trim();
    const lastName = decodeHTML(
        document.getElementById('edit-staff-last-name').value
    ).trim();
    const email = decodeHTML(
        document.getElementById('edit-staff-email').value
    ).trim();
    const phoneNumber = decodeHTML(
        document.getElementById('edit-staff-phone').value
    ).trim();

    if (!staffId || !firstName || !lastName) {
        showToast('Please fill in all required fields.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('staff_id', staffId);
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('phone_number', phoneNumber);

    updateButton.disabled = true;
    updateButtonText.textContent = 'Saving...';
    updateSpinner.classList.remove('hidden');

    try {
        const response = await fetch(STAFF_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(
                result.message || 'Failed to update staff.',
                'error'
            );
            return;
        }

        closeModal('modal-edit-staff');
        showToast(
            result.message || 'Staff updated successfully.',
            'success'
        );

        await loadStaff();
    } catch (error) {
        console.error('Error updating staff:', error);
        showToast('Unable to update staff.', 'error');
    } finally {
        updateButton.disabled = false;
        updateButtonText.textContent = 'Save Changes';
        updateSpinner.classList.add('hidden');
    }
}

/* Open the deactivate staff modal */
function deactivateStaff(staffId) {
    const cleanStaffId = decodeHTML(staffId || '');
    const staff = staffManagementData.find(function(item) {
        return decodeHTML(item.staff_id || '') === cleanStaffId;
    });

    if (!staff) {
        showToast('Staff record not found.', 'error');
        return;
    }

    const firstName = decodeHTML(staff.first_name || '');
    const lastName = decodeHTML(staff.last_name || '');
    const fullName = `${firstName} ${lastName}`.trim();

    document.getElementById('deactivate-staff-id').value = cleanStaffId;
    document.getElementById('deactivate-staff-name').textContent = fullName;

    openModal('modal-deactivate-staff');
}

/* Confirm staff deactivation */
async function confirmDeactivateStaff() {
    const staffId = decodeHTML(
        document.getElementById('deactivate-staff-id').value
    ).trim();

    if (!staffId) {
        showToast('Staff record not found.', 'error');
        return;
    }

    const confirmButton = document.getElementById(
        'confirm-deactivate-staff-btn'
    );
    const confirmButtonText = document.getElementById(
        'confirm-deactivate-staff-btn-text'
    );
    const confirmSpinner = document.getElementById(
        'confirm-deactivate-staff-spinner'
    );

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('staff_id', staffId);

    confirmButton.disabled = true;
    confirmButtonText.textContent = 'Deactivating...';
    confirmSpinner.classList.remove('hidden');

    try {
        const response = await fetch(STAFF_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(
                result.message || 'Failed to deactivate staff.',
                'error'
            );
            return;
        }

        closeModal('modal-deactivate-staff');
        showToast(
            result.message || 'Staff deactivated successfully.',
            'success'
        );

        await loadStaff();
    } catch (error) {
        console.error('Error deactivating staff:', error);
        showToast('Unable to deactivate staff.', 'error');
    } finally {
        confirmButton.disabled = false;
        confirmButtonText.textContent = 'Deactivate Staff';
        confirmSpinner.classList.add('hidden');
    }
}

/* Open the reactivate staff modal */
function reactivateStaff(staffId) {
    const cleanStaffId = decodeHTML(staffId || '');
    const staff = staffManagementData.find(function(item) {
        return decodeHTML(item.staff_id || '') === cleanStaffId;
    });

    if (!staff) {
        showToast('Staff record not found.', 'error');
        return;
    }

    const firstName = decodeHTML(staff.first_name || '');
    const lastName = decodeHTML(staff.last_name || '');
    const fullName = `${firstName} ${lastName}`.trim();

    document.getElementById('reactivate-staff-id').value = cleanStaffId;
    document.getElementById('reactivate-staff-name').textContent = fullName;

    openModal('modal-reactivate-staff');
}

/* Confirm staff reactivation */
async function confirmReactivateStaff() {
    const staffId = decodeHTML(
        document.getElementById('reactivate-staff-id').value
    ).trim();

    if (!staffId) {
        showToast('Staff record not found.', 'error');
        return;
    }

    const confirmButton = document.getElementById(
        'confirm-reactivate-staff-btn'
    );
    const confirmButtonText = document.getElementById(
        'confirm-reactivate-staff-btn-text'
    );
    const confirmSpinner = document.getElementById(
        'confirm-reactivate-staff-spinner'
    );

    const formData = new FormData();
    formData.append('action', 'reactivate');
    formData.append('staff_id', staffId);

    confirmButton.disabled = true;
    confirmButtonText.textContent = 'Reactivating...';
    confirmSpinner.classList.remove('hidden');

    try {
        const response = await fetch(STAFF_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(
                result.message || 'Failed to reactivate staff.',
                'error'
            );
            return;
        }

        closeModal('modal-reactivate-staff');
        showToast(
            result.message || 'Staff reactivated successfully.',
            'success'
        );

        await loadStaff();
    } catch (error) {
        console.error('Error reactivating staff:', error);
        showToast('Unable to reactivate staff.', 'error');
    } finally {
        confirmButton.disabled = false;
        confirmButtonText.textContent = 'Reactivate Staff';
        confirmSpinner.classList.add('hidden');
    }
}

function escapeJS(value) {
    return String(value || '')
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(/"/g, '\\"')
        .replace(/\r/g, '\\r')
        .replace(/\n/g, '\\n');
}

