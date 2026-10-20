const USER_API_URL = 'api/manage_user.php';

let userManagementData = [];
let currentUserFilter = 'all';
let currentUserSearch = '';
let currentUserPage = 1;
const userPerPage = 10;


/* Load users */
/* Load users */
async function loadUsers() {
    const userList = document.getElementById('user-list');
    userList.innerHTML = `
        <tr>
            <td colspan="6" class="px-8 py-10 text-center text-slate-400">
                <div class="flex flex-col items-center gap-2">
                    <i class="fas fa-spinner fa-spin text-3xl"></i>
                    <p>Loading users...</p>
                </div>
            </td>
        </tr>
    `;
    const formData = new FormData();
    formData.append('action', 'list');
    try {
        const response = await fetch(USER_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.status !== 'success') {
            userList.innerHTML = `
                <tr>
                    <td colspan="6" class="px-8 py-10 text-center text-red-400">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-exclamation-circle text-3xl"></i>
                            <p>
                                ${escapeHTML(decodeHTML(
                                    result.message || 'Failed to load users.'
                                ))}
                            </p>
                        </div>
                    </td>
                </tr>
            `;
            showToast(result.message || 'Failed to load users.', 'error');
            return;
        }
        userManagementData = Array.isArray(result.data) ? result.data : [];
        currentUserPage = 1;
        renderUsers();
    } catch (error) {
        console.error('Error loading users:', error);
        userList.innerHTML = `
            <tr>
                <td colspan="6" class="px-8 py-10 text-center text-red-400">
                    <div class="flex flex-col items-center gap-2">
                        <i class="fas fa-exclamation-circle text-3xl"></i>
                        <p>Unable to load users.</p>
                    </div>
                </td>
            </tr>
        `;
        showToast('Unable to load users.', 'error');
    }
}

/* Render users */
function renderUsers() {
    const userList = document.getElementById('user-list');
    const filteredUsers = userManagementData.filter(function(user) {
        const matchesFilter = currentUserFilter === 'all' ||
            user.role === currentUserFilter;
        const searchValue = currentUserSearch.toLowerCase();
        const firstName = decodeHTML(user.first_name || '');
        const lastName = decodeHTML(user.last_name || '');
        const email = decodeHTML(user.email || '');
        const phone = decodeHTML(user.phone_number || '');
        const fullName = `${firstName} ${lastName}`.trim().toLowerCase();
        const matchesSearch = !searchValue ||
            fullName.includes(searchValue) ||
            email.toLowerCase().includes(searchValue) ||
            phone.toLowerCase().includes(searchValue);
        return matchesFilter && matchesSearch;
    });
    const totalPages = Math.max(
        1,
        Math.ceil(filteredUsers.length / userPerPage)
    );
    if (currentUserPage > totalPages) {
        currentUserPage = totalPages;
    }
    const startIndex = (currentUserPage - 1) * userPerPage;
    const paginatedUsers = filteredUsers.slice(
        startIndex,
        startIndex + userPerPage
    );
    if (paginatedUsers.length === 0) {
        userList.innerHTML = `
            <tr>
                <td colspan="6" class="px-8 py-12 text-center text-slate-400">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fas fa-user-slash text-4xl opacity-30"></i>
                        <p class="font-semibold">No users found.</p>
                        <p class="text-sm">Try changing your search or filter.</p>
                    </div>
                </td>
            </tr>
        `;
        renderUserPagination(0, 0);
        return;
    }
    userList.innerHTML = paginatedUsers.map(function(user) {
        const userId = decodeHTML(user.user_id || '');
        const firstName = decodeHTML(user.first_name || '');
        const lastName = decodeHTML(user.last_name || '');
        const email = decodeHTML(user.email || '');
        const phone = decodeHTML(user.phone_number || '');
        const fullName = `${firstName} ${lastName}`.trim();
        const roleLabel = user.role === 'field_officer'
            ? 'Field Officer'
            : 'Cashier';
        const roleBadge = user.role === 'field_officer'
            ? 'bg-blue-50 text-blue-700'
            : 'bg-purple-50 text-purple-700';
        const statusBadge = Number(user.staff_active) === 1
            ? 'bg-emerald-50 text-emerald-700'
            : 'bg-red-50 text-red-700';
        const statusText = Number(user.staff_active) === 1
            ? 'Active'
            : 'Inactive';
        const actionHtml = Number(user.staff_active) === 1
            ? `
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="openResetUserPasswordModal('${escapeJS(userId)}')"
                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-600
                            hover:bg-blue-100 flex items-center justify-center
                            transition-all"
                        title="Reset Password">
                        <i class="fas fa-key text-sm"></i>
                    </button>
                    <button
                        type="button"
                        onclick="deactivateUser('${escapeJS(userId)}')"
                        class="w-9 h-9 rounded-full bg-red-50 text-red-600
                            hover:bg-red-100 flex items-center justify-center
                            transition-all"
                        title="Deactivate User">
                        <i class="fas fa-user-slash text-sm"></i>
                    </button>
                </div>
            `
            : `
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="openResetUserPasswordModal('${escapeJS(userId)}')"
                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-600
                            hover:bg-blue-100 flex items-center justify-center
                            transition-all"
                        title="Reset Password">
                        <i class="fas fa-key text-sm"></i>
                    </button>
                    <button
                        type="button"
                        onclick="reactivateUser('${escapeJS(userId)}')"
                        class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600
                            hover:bg-emerald-100 flex items-center justify-center
                            transition-all"
                        title="Reactivate User">
                        <i class="fas fa-user-check text-sm"></i>
                    </button>
                </div>
            `;
        return `
            <tr class="hover:bg-slate-50/70 transition-colors">
                <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600
                            flex items-center justify-center font-black shrink-0">
                            ${escapeHTML(
                                decodeHTML(firstName.charAt(0) || 'U')
                            ).toUpperCase()}
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">
                                ${escapeHTML(decodeHTML(fullName))}
                            </p>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-3 py-1
                                    rounded-full text-xs font-bold bg-emerald-50
                                    text-emerald-700">
                                    <i class="fas fa-user-lock mr-1.5"></i>
                                    Login Account
                                </span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full
                        text-xs font-bold ${roleBadge}">
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
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full
                        text-xs font-bold ${statusBadge}">
                        ${escapeHTML(decodeHTML(statusText))}
                    </span>
                </td>
                <td class="px-6 py-5">${actionHtml}</td>
            </tr>
        `;
    }).join('');
    renderUserPagination(filteredUsers.length, totalPages);
}

/* Render user pagination */
function renderUserPagination(totalRecords, totalPages) {
    const pagination = document.getElementById('user-pagination');
    if (!totalRecords) {
        pagination.innerHTML = '';
        return;
    }

    const startRecord = ((currentUserPage - 1) * userPerPage) + 1;
    const endRecord = Math.min(currentUserPage * userPerPage, totalRecords);
    let paginationButtons = '';
    paginationButtons += `
        <button
            type="button"
            onclick="changeUserPage(${currentUserPage - 1})"
            ${currentUserPage === 1 ? 'disabled' : ''}
            class="w-10 h-10 rounded-full flex items-center justify-center font-bold
                transition-all ${
                    currentUserPage === 1
                        ? 'bg-slate-100 text-slate-300 cursor-not-allowed'
                        : 'bg-white text-slate-600 hover:bg-blue-50 hover:text-blue-600 border border-slate-200'
                }">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
    `;
    const maxVisiblePages = 5;
    let startPage = Math.max(
        1,
        currentUserPage - Math.floor(maxVisiblePages / 2)
    );
    let endPage = Math.min(
        totalPages,
        startPage + maxVisiblePages - 1
    );

    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    for (let page = startPage; page <= endPage; page++) {
        paginationButtons += `
            <button
                type="button"
                onclick="changeUserPage(${page})"
                class="w-10 h-10 rounded-full flex items-center justify-center font-bold
                    transition-all ${
                        page === currentUserPage
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
            onclick="changeUserPage(${currentUserPage + 1})"
            ${currentUserPage === totalPages ? 'disabled' : ''}
            class="w-10 h-10 rounded-full flex items-center justify-center font-bold
                transition-all ${
                    currentUserPage === totalPages
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

/* Change user page */
function changeUserPage(page) {
    const filteredUsers = userManagementData.filter(function(user) {
        const matchesFilter = currentUserFilter === 'all' ||
            user.role === currentUserFilter;
        const searchValue = currentUserSearch.toLowerCase();
        const firstName = decodeHTML(user.first_name || '');
        const lastName = decodeHTML(user.last_name || '');
        const email = decodeHTML(user.email || '');
        const phone = decodeHTML(user.phone_number || '');
        const fullName = `${firstName} ${lastName}`.trim().toLowerCase();
        const matchesSearch = !searchValue ||
            fullName.includes(searchValue) ||
            email.toLowerCase().includes(searchValue) ||
            phone.toLowerCase().includes(searchValue);
        return matchesFilter && matchesSearch;
    });
    const totalPages = Math.max(
        1,
        Math.ceil(filteredUsers.length / userPerPage)
    );
    if (page < 1 || page > totalPages) {
        return;
    }
    currentUserPage = page;
    renderUsers();
}

/* Handle user search */
function handleUserSearch(value) {
    currentUserSearch = decodeHTML(value || '').trim().toLowerCase();
    currentUserPage = 1;
    const clearButton = document.getElementById('clear-user-search-btn');
    if (currentUserSearch) {
        clearButton.classList.remove('hidden');
        clearButton.classList.add('flex');
    } else {
        clearButton.classList.add('hidden');
        clearButton.classList.remove('flex');
    }
    renderUsers();
}

/* Clear user search */
function clearUserSearch() {
    const searchInput = document.getElementById('user-search');
    searchInput.value = '';
    currentUserSearch = '';
    currentUserPage = 1;
    const clearButton = document.getElementById('clear-user-search-btn');
    clearButton.classList.add('hidden');
    clearButton.classList.remove('flex');
    renderUsers();
}

/* Filter users */
function filterUsers(type) {
    currentUserFilter = decodeHTML(type || 'all');
    currentUserPage = 1;
    const filterButtons = [
        'user-filter-all',
        'user-filter-field-officer',
        'user-filter-cashier'
    ];
    filterButtons.forEach(function(buttonId) {
        const button = document.getElementById(buttonId);
        button.classList.remove('bg-blue-600', 'text-white');
        button.classList.add('bg-slate-100', 'text-slate-600');
    });
    let activeButtonId = 'user-filter-all';
    if (currentUserFilter === 'field_officer') {
        activeButtonId = 'user-filter-field-officer';
    }
    if (currentUserFilter === 'cashier') {
        activeButtonId = 'user-filter-cashier';
    }
    const activeButton = document.getElementById(activeButtonId);
    activeButton.classList.remove('bg-slate-100', 'text-slate-600');
    activeButton.classList.add('bg-blue-600', 'text-white');
    renderUsers();
}

/* Load available staff */
async function loadAvailableStaff() {
    const staffSelect = document.getElementById('user-staff-id');

    if (!staffSelect) {
        return;
    }

    staffSelect.innerHTML = `
        <option value="">Loading staff...</option>
    `;

    const formData = new FormData();
    formData.append('action', 'available_staff');

    try {
        const response = await fetch(USER_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            staffSelect.innerHTML = `
                <option value="">Unable to load staff</option>
            `;
            showToast(
                result.message || 'Failed to load available staff.',
                'error'
            );
            return;
        }

        const availableStaff = Array.isArray(result.data)
            ? result.data
            : [];

        if (!availableStaff.length) {
            staffSelect.innerHTML = `
                <option value="">No eligible staff available</option>
            `;
            return;
        }

        staffSelect.innerHTML = `
            <option value="">Select staff member</option>
            ${availableStaff.map(function(staff) {
                const staffId = decodeHTML(staff.staff_id || '');
                const firstName = decodeHTML(staff.first_name || '');
                const lastName = decodeHTML(staff.last_name || '');
                const email = decodeHTML(staff.email || '');
                const type = decodeHTML(staff.type || '');
                const fullName = `${firstName} ${lastName}`.trim();
                const roleLabel = type === 'field_officer'
                    ? 'Field Officer'
                    : 'Cashier';
                const emailText = email ? ` - ${email}` : '';

                return `
                    <option value="${escapeHTML(staffId)}">
                        ${escapeHTML(decodeHTML(fullName))}
                        - ${escapeHTML(decodeHTML(roleLabel))}
                        ${emailText
                            ? escapeHTML(decodeHTML(emailText))
                            : ''}
                    </option>
                `;
            }).join('')}
        `;
    } catch (error) {
        console.error('Error loading available staff:', error);
        staffSelect.innerHTML = `
            <option value="">Unable to load staff</option>
        `;
        showToast('Unable to load available staff.', 'error');
    }
}

/* Open the add-user modal */
async function openAddUserModal() {
    const form = document.getElementById('user-form');

    if (form) {
        form.reset();
    }

    await loadAvailableStaff();
    openModal('modal-user');
}

/* Create a user account */
async function saveUser(event) {
    event.preventDefault();

    const saveButton = document.getElementById('save-user-btn');
    const saveButtonText = document.getElementById('save-user-btn-text');
    const saveSpinner = document.getElementById('save-user-spinner');
    const staffId = decodeHTML(
        document.getElementById('user-staff-id').value
    ).trim();
    const password = decodeHTML(
        document.getElementById('user-password').value
    );
    const confirmPassword = decodeHTML(
        document.getElementById('user-confirm-password').value
    );

    if (!staffId || !password || !confirmPassword) {
        showToast('Please fill in all required fields.', 'error');
        return;
    }

    if (password !== confirmPassword) {
        showToast('Passwords do not match.', 'error');
        return;
    }

    if (password.length < 8) {
        showToast('Password must be at least 8 characters.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('staff_id', staffId);
    formData.append('password', password);

    saveButton.disabled = true;
    saveButtonText.textContent = 'Creating...';
    saveSpinner.classList.remove('hidden');

    try {
        const response = await fetch(USER_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(result.message || 'Failed to create user.', 'error');
            return;
        }

        closeModal('modal-user');
        showToast(
            result.message || 'User created successfully.',
            'success'
        );
        await loadUsers();
    } catch (error) {
        console.error('Error creating user:', error);
        showToast('Unable to create user.', 'error');
    } finally {
        saveButton.disabled = false;
        saveButtonText.textContent = 'Create User';
        saveSpinner.classList.add('hidden');
    }
}

/* Open the user deactivation modal */
function deactivateUser(userId) {
    const cleanUserId = decodeHTML(userId || '');
    const user = userManagementData.find(function(item) {
        return decodeHTML(item.user_id || '') === cleanUserId;
    });

    if (!user) {
        showToast('User record not found.', 'error');
        return;
    }

    const firstName = decodeHTML(user.first_name || '');
    const lastName = decodeHTML(user.last_name || '');
    const fullName = `${firstName} ${lastName}`.trim();

    document.getElementById('deactivate-user-id').value = cleanUserId;
    document.getElementById('deactivate-user-name').textContent = fullName;
    openModal('modal-deactivate-user');
}

/* Deactivate a user account */
async function confirmDeactivateUser() {
    const userId = decodeHTML(
        document.getElementById('deactivate-user-id').value
    ).trim();

    if (!userId) {
        showToast('User record not found.', 'error');
        return;
    }

    const confirmButton = document.getElementById(
        'confirm-deactivate-user-btn'
    );
    const confirmButtonText = document.getElementById(
        'confirm-deactivate-user-btn-text'
    );
    const confirmSpinner = document.getElementById(
        'confirm-deactivate-user-spinner'
    );
    const formData = new FormData();

    formData.append('action', 'deactivate');
    formData.append('user_id', userId);

    confirmButton.disabled = true;
    confirmButtonText.textContent = 'Deactivating...';
    confirmSpinner.classList.remove('hidden');

    try {
        const response = await fetch(USER_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(
                result.message || 'Failed to deactivate user.',
                'error'
            );
            return;
        }

        closeModal('modal-deactivate-user');
        showToast(
            result.message || 'User deactivated successfully.',
            'success'
        );
        await loadUsers();
    } catch (error) {
        console.error('Error deactivating user:', error);
        showToast('Unable to deactivate user.', 'error');
    } finally {
        confirmButton.disabled = false;
        confirmButtonText.textContent = 'Deactivate User';
        confirmSpinner.classList.add('hidden');
    }
}

/* Open the user reactivation modal */
function reactivateUser(userId) {
    const cleanUserId = decodeHTML(userId || '');
    const user = userManagementData.find(function(item) {
        return decodeHTML(item.user_id || '') === cleanUserId;
    });

    if (!user) {
        showToast('User record not found.', 'error');
        return;
    }

    const firstName = decodeHTML(user.first_name || '');
    const lastName = decodeHTML(user.last_name || '');
    const fullName = `${firstName} ${lastName}`.trim();

    document.getElementById('reactivate-user-id').value = cleanUserId;
    document.getElementById('reactivate-user-name').textContent = fullName;
    openModal('modal-reactivate-user');
}

/* Reactivate a user account */
async function confirmReactivateUser() {
    const userId = decodeHTML(
        document.getElementById('reactivate-user-id').value
    ).trim();

    if (!userId) {
        showToast('User record not found.', 'error');
        return;
    }

    const confirmButton = document.getElementById(
        'confirm-reactivate-user-btn'
    );
    const confirmButtonText = document.getElementById(
        'confirm-reactivate-user-btn-text'
    );
    const confirmSpinner = document.getElementById(
        'confirm-reactivate-user-spinner'
    );
    const formData = new FormData();

    formData.append('action', 'reactivate');
    formData.append('user_id', userId);

    confirmButton.disabled = true;
    confirmButtonText.textContent = 'Reactivating...';
    confirmSpinner.classList.remove('hidden');

    try {
        const response = await fetch(USER_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(
                result.message || 'Failed to reactivate user.',
                'error'
            );
            return;
        }

        closeModal('modal-reactivate-user');
        showToast(
            result.message || 'User reactivated successfully.',
            'success'
        );
        await loadUsers();
    } catch (error) {
        console.error('Error reactivating user:', error);
        showToast('Unable to reactivate user.', 'error');
    } finally {
        confirmButton.disabled = false;
        confirmButtonText.textContent = 'Reactivate User';
        confirmSpinner.classList.add('hidden');
    }
}

/* Open the reset-password modal */
function openResetUserPasswordModal(userId) {
    const cleanUserId = decodeHTML(userId || '');
    const user = userManagementData.find(function(item) {
        return decodeHTML(item.user_id || '') === cleanUserId;
    });

    if (!user) {
        showToast('User record not found.', 'error');
        return;
    }

    const firstName = decodeHTML(user.first_name || '');
    const lastName = decodeHTML(user.last_name || '');
    const fullName = `${firstName} ${lastName}`.trim();

    document.getElementById('reset-user-password-form').reset();
    document.getElementById('reset-user-password-id').value = cleanUserId;
    document.getElementById('reset-user-password-name').textContent = fullName;
    openModal('modal-reset-user-password');
}

/* Reset a user's password */
async function resetUserPassword(event) {
    event.preventDefault();

    const resetButton = document.getElementById('reset-user-password-btn');
    const resetButtonText = document.getElementById(
        'reset-user-password-btn-text'
    );
    const resetSpinner = document.getElementById(
        'reset-user-password-spinner'
    );
    const userId = decodeHTML(
        document.getElementById('reset-user-password-id').value
    ).trim();
    const password = decodeHTML(
        document.getElementById('reset-user-password').value
    );
    const confirmPassword = decodeHTML(
        document.getElementById('reset-user-confirm-password').value
    );

    if (!userId || !password || !confirmPassword) {
        showToast('Please fill in all required fields.', 'error');
        return;
    }

    if (password !== confirmPassword) {
        showToast('Passwords do not match.', 'error');
        return;
    }

    if (password.length < 8) {
        showToast('Password must be at least 8 characters.', 'error');
        return;
    }

    const formData = new FormData();

    formData.append('action', 'reset_password');
    formData.append('user_id', userId);
    formData.append('password', password);

    resetButton.disabled = true;
    resetButtonText.textContent = 'Resetting...';
    resetSpinner.classList.remove('hidden');

    try {
        const response = await fetch(USER_API_URL, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            showToast(
                result.message || 'Failed to reset password.',
                'error'
            );
            return;
        }

        closeModal('modal-reset-user-password');
        showToast(
            result.message || 'Password reset successfully.',
            'success'
        );
        await loadUsers();
    } catch (error) {
        console.error('Error resetting user password:', error);
        showToast('Unable to reset user password.', 'error');
    } finally {
        resetButton.disabled = false;
        resetButtonText.textContent = 'Reset Password';
        resetSpinner.classList.add('hidden');
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