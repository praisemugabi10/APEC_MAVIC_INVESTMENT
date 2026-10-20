
const TRANSACTION_CATEGORY_ENDPOINT = "api/transactions/manage_transaction_category.php";

/* ==========================================================================
   TRANSACTION CATEGORY MANAGEMENT
   ========================================================================== */

let transactionCategories = [];

let editingTransactionCategory = false;


/* Open transaction category modal */
function openTransactionCategoryModal() {
    const modalElement = document.getElementById(
        'modal-transaction-categories'
    );
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

    resetTransactionCategoryInterface();
    modal.show();
    fetchTransactionCategories();
}

/* Reset transaction category interface */
function resetTransactionCategoryInterface() {
    transactionCategories = [];
    editingTransactionCategory = false;

    document.getElementById('transaction-category-search').value = '';
    document.getElementById(
        'transaction-category-clear-search'
    ).classList.add('hidden');

    hideTransactionCategoryForm();

    document.getElementById('transaction-category-list').innerHTML = '';
    document.getElementById('transaction-category-count').textContent =
        '0 categories';

    hideTransactionCategoryEmptyState();
}

/* Fetch transaction categories */
async function fetchTransactionCategories() {
    const loading = document.getElementById(
        'transaction-category-loading'
    );
    const list = document.getElementById('transaction-category-list');
    const empty = document.getElementById('transaction-category-empty');

    loading.classList.remove('hidden');
    loading.classList.add('flex');
    empty.classList.add('hidden');
    empty.classList.remove('flex');
    list.innerHTML = '';

    try {
        const response = await fetch(
            `${TRANSACTION_CATEGORY_ENDPOINT}?action=fetch_transaction_categories`,
            {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }
        );
        const result = await response.json();

        if (result.status !== 'success') {
            throw new Error(
                result.message ||
                'Failed to fetch transaction categories.'
            );
        }

        transactionCategories = Array.isArray(result.data)
            ? result.data
            : [];

        renderTransactionCategories(transactionCategories);
    } catch (error) {
        list.innerHTML = `
            <div class="py-12 text-center">
                <div
                    class="w-16 h-16 mx-auto rounded-3xl bg-red-50 text-red-500 flex items-center justify-center mb-4">
                    <i class="fas fa-triangle-exclamation text-2xl"></i>
                </div>
                <h4 class="text-lg font-black text-slate-800">
                    Unable to load categories
                </h4>
                <p class="text-sm font-medium text-slate-400 mt-1">
                    ${escapeTransactionCategoryHtml(error.message)}
                </p>
                <button
                    type="button"
                    onclick="fetchTransactionCategories()"
                    class="mt-5 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-black transition-all">
                    <i class="fas fa-rotate-right mr-2"></i>
                    Try Again
                </button>
            </div>
        `;
    } finally {
        loading.classList.add('hidden');
        loading.classList.remove('flex');
    }
}


/* Render transaction categories */
function renderTransactionCategories(categories) {
    const list = document.getElementById('transaction-category-list');
    const empty = document.getElementById('transaction-category-empty');
    const count = document.getElementById('transaction-category-count');

    list.innerHTML = '';
    count.textContent = `${categories.length} categor${categories.length === 1 ? 'y' : 'ies'}`;

    if (categories.length === 0) {
        empty.classList.remove('hidden');
        empty.classList.add('flex');
        return;
    }

    empty.classList.add('hidden');
    empty.classList.remove('flex');

    categories.forEach(category => {
        const isActive = String(
            category.transaction_category_is_active
        ) === '1';
        const transactionType = String(
            category.transaction_category_type || ''
        ).toLowerCase();
        const isCashIn = transactionType === 'cash_in';
        const transactionTypeLabel = isCashIn ? 'Cash In' : 'Cash Out';
        const item = document.createElement('div');

        item.className = `
            group
            bg-white
            border
            border-slate-100
            rounded-[1.5rem]
            p-4
            sm:p-5
            shadow-sm
            hover:shadow-md
            hover:border-blue-100
            transition-all
        `;

        item.innerHTML = `
            <div class="flex items-start gap-3 sm:gap-4">
                <div
                    class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex-shrink-0
                    ${
                        isActive
                            ? 'bg-blue-50 text-blue-600'
                            : 'bg-slate-100 text-slate-400'
                    }
                    flex items-center justify-center">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h4
                            class="text-sm sm:text-base font-black text-slate-900 break-words">
                            ${escapeTransactionCategoryHtml(
                                category.transaction_category_name
                            )}
                        </h4>
                        <span
                            class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider
                            ${
                                isCashIn
                                    ? 'bg-blue-50 text-blue-600'
                                    : 'bg-orange-50 text-orange-600'
                            }">
                            ${transactionTypeLabel}
                        </span>
                    </div>
                    <p
                        class="text-xs sm:text-sm text-slate-400 font-medium mt-1 break-words">
                        ${
                            category.transaction_category_description
                                ? escapeTransactionCategoryHtml(
                                    category.transaction_category_description
                                )
                                : 'No description provided.'
                        }
                    </p>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    ${
                        isActive
                            ? `
                                <button
                                    type="button"
                                    onclick="editTransactionCategory('${escapeTransactionCategoryAttribute(
                                        category.transaction_category_id
                                    )}')"
                                    title="Edit category"
                                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                <button
                                    type="button"
                                    onclick="deleteTransactionCategory('${escapeTransactionCategoryAttribute(
                                        category.transaction_category_id
                                    )}')"
                                    title="Deactivate category"
                                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            `
                            : ''
                    }
                </div>
            </div>
        `;

        list.appendChild(item);
    });
}

/* Search transaction categories */
function searchTransactionCategories(searchValue) {
    const search = searchValue.trim().toLowerCase();
    const clearButton = document.getElementById(
        'transaction-category-clear-search'
    );

    if (search !== '') {
        clearButton.classList.remove('hidden');
        clearButton.classList.add('flex');
    } else {
        clearButton.classList.add('hidden');
        clearButton.classList.remove('flex');
    }

    const filteredCategories = transactionCategories.filter(category => {
        const name = String(
            category.transaction_category_name || ''
        ).toLowerCase();
        const description = String(
            category.transaction_category_description || ''
        ).toLowerCase();
        const type = String(
            category.transaction_category_type || ''
        ).toLowerCase();

        return (
            name.includes(search) ||
            description.includes(search) ||
            type.includes(search) ||
            (type === 'cash_in' && 'cash in'.includes(search)) ||
            (type === 'cash_out' && 'cash out'.includes(search))
        );
    });

    renderTransactionCategories(filteredCategories);
}

/* Clear transaction category search */
function clearTransactionCategorySearch() {
    const input = document.getElementById(
        'transaction-category-search'
    );

    input.value = '';
    searchTransactionCategories('');
    input.focus();
}


//* Show transaction category form */
function showTransactionCategoryForm() {
    editingTransactionCategory = false;

    document.getElementById('transaction-category-form-title').textContent =
        'Add Category';
    document.getElementById('transaction-category-save-text').textContent =
        'Add Category';
    document.getElementById('transaction-category-save-icon').className =
        'fas fa-plus';
    document.getElementById('transaction-category-id').value = '';
    document.getElementById('transaction-category-name').value = '';
    document.getElementById('transaction-category-description').value = '';
    document.getElementById('transaction-category-type').value = '';

    document.getElementById(
        'transaction-category-form-container'
    ).classList.remove('hidden');

    setTimeout(() => {
        document.getElementById('transaction-category-name').focus();
    }, 100);
}

/* Hide transaction category form */
function hideTransactionCategoryForm() {
    document.getElementById(
        'transaction-category-form-container'
    ).classList.add('hidden');

    document.getElementById('transaction-category-form').reset();
    document.getElementById('transaction-category-id').value = '';
    document.getElementById('transaction-category-type').value = '';

    editingTransactionCategory = false;
}

/* Edit transaction category */
function editTransactionCategory(categoryId) {
    const category = transactionCategories.find(
        item =>
            String(item.transaction_category_id) === String(categoryId)
    );

    if (!category) {
        alert('Transaction category not found.');
        return;
    }

    editingTransactionCategory = true;

    document.getElementById('transaction-category-form-title').textContent =
        'Edit Category';
    document.getElementById('transaction-category-save-text').textContent =
        'Update Category';
    document.getElementById('transaction-category-save-icon').className =
        'fas fa-save';
    document.getElementById('transaction-category-id').value =
        category.transaction_category_id;
    document.getElementById('transaction-category-name').value =
        category.transaction_category_name || '';
    document.getElementById('transaction-category-description').value =
        category.transaction_category_description || '';

    const typeSelect = document.getElementById(
        'transaction-category-type'
    );
    const categoryType = String(
        category.transaction_category_type || ''
    ).toLowerCase();

    if (categoryType === 'cash_in' || categoryType === 'cash_out') {
        typeSelect.value = categoryType;
    } else {
        typeSelect.value = '';
    }

    const formContainer = document.getElementById(
        'transaction-category-form-container'
    );

    formContainer.classList.remove('hidden');
    formContainer.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
    });

    setTimeout(() => {
        document.getElementById('transaction-category-name').focus();
    }, 100);
}

/* Save transaction category */
async function saveTransactionCategory(event) {
    event.preventDefault();

    const id = document.getElementById(
        'transaction-category-id'
    ).value.trim();
    const name = document.getElementById(
        'transaction-category-name'
    ).value.trim();
    const description = document.getElementById(
        'transaction-category-description'
    ).value.trim();
    const type = document.getElementById(
        'transaction-category-type'
    ).value;

    if (name === '') {
        alert('Transaction category name is required.');
        return;
    }

    if (type !== 'cash_in' && type !== 'cash_out') {
        alert(
            'Please select whether this category is Cash In or Cash Out.'
        );
        document.getElementById('transaction-category-type').focus();
        return;
    }

    const formData = new FormData();

    formData.append(
        'action',
        editingTransactionCategory
            ? 'update_transaction_category'
            : 'create_transaction_category'
    );
    formData.append('transaction_category_name', name);
    formData.append('transaction_category_description', description);
    formData.append('transaction_category_type', type);

    if (editingTransactionCategory) {
        formData.append('transaction_category_id', id);
    }

    const button = document.getElementById(
        'transaction-category-save-button'
    );
    const saveText = document.getElementById(
        'transaction-category-save-text'
    );
    const originalText = saveText.textContent;

    button.disabled = true;
    button.classList.add('opacity-70', 'cursor-not-allowed');
    saveText.textContent = 'Saving...';

    try {
        const response = await fetch(TRANSACTION_CATEGORY_ENDPOINT, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            alert(
                result.message ||
                'Unable to save transaction category.'
            );
            return;
        }

        hideTransactionCategoryForm();
        await fetchTransactionCategories();
    } catch (error) {
        alert(
            'Something went wrong while saving the transaction category.'
        );
    } finally {
        button.disabled = false;
        button.classList.remove('opacity-70', 'cursor-not-allowed');
        saveText.textContent = originalText;
    }
}

/* Deactivate transaction category */
async function deleteTransactionCategory(categoryId) {
    const category = transactionCategories.find(
        item =>
            String(item.transaction_category_id) === String(categoryId)
    );

    if (!category) {
        alert('Transaction category not found.');
        return;
    }

    const confirmed = confirm(
        `Deactivate "${category.transaction_category_name}"?`
    );

    if (!confirmed) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_transaction_category');
    formData.append('transaction_category_id', categoryId);

    try {
        const response = await fetch(TRANSACTION_CATEGORY_ENDPOINT, {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status !== 'success') {
            alert(
                result.message ||
                'Unable to deactivate transaction category.'
            );
            return;
        }

        await fetchTransactionCategories();
    } catch (error) {
        alert(
            'Something went wrong while deactivating the transaction category.'
        );
    }
}

/* Hide transaction category empty state */
function hideTransactionCategoryEmptyState() {
    const empty = document.getElementById(
        'transaction-category-empty'
    );

    empty.classList.add('hidden');
    empty.classList.remove('flex');
}

/* Escape HTML content */
function escapeTransactionCategoryHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';

    return div.innerHTML;
}

/* Escape attribute values */
function escapeTransactionCategoryAttribute(value) {
    return String(value ?? '')
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'");
}

/* Register transaction category event listeners */
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById(
        'transaction-category-search'
    );
    const form = document.getElementById(
        'transaction-category-form'
    );

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchTransactionCategories(this.value);
        });
    }

    if (form) {
        form.addEventListener('submit', saveTransactionCategory);
    }
});
