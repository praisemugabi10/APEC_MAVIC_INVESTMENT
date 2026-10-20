<!-- Transaction Categories Modal -->
<div class="modal fade" id="modal-transaction-categories" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden">
            <!-- HEADER -->
            <div class="modal-header border-0 pt-4 px-4 px-sm-5 pb-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-0 truncate">
                                Transaction Categories
                            </h3>
                            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">
                                Manage your transaction categories
                            </p>
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    class="btn-close shadow-none ms-3 flex-shrink-0"
                    data-bs-dismiss="modal">
                </button>
            </div>
            <!-- BODY -->
            <div class="modal-body px-4 px-sm-5 py-4">
                <!-- SEARCH + ADD -->
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <!-- SEARCH -->
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        <input
                            type="text"
                            id="transaction-category-search"
                            placeholder="Search categories..."
                            autocomplete="off"
                            class="w-full pl-12 pr-12 py-3.5 bg-slate-50 border-2 border-slate-100 rounded-full text-sm font-bold text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all">
                        <!-- CLEAR SEARCH -->
                        <button
                            type="button"
                            id="transaction-category-clear-search"
                            onclick="clearTransactionCategorySearch()"
                            class="hidden absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-slate-200 text-slate-500 hover:bg-slate-300 hover:text-slate-700 transition-all items-center justify-center">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                    <!-- ADD CATEGORY -->
                    <button
                        type="button"
                        onclick="showTransactionCategoryForm()"
                        class="w-full sm:w-auto px-5 py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-black text-sm shadow-md hover:shadow-lg active:scale-95 transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                        <i class="fas fa-plus"></i>
                        Add Category
                    </button>
                </div>
                <!-- CATEGORY FORM -->
                <div
                    id="transaction-category-form-container"
                    class="hidden mb-5 bg-slate-50 border border-slate-100 rounded-[1.75rem] p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h4
                                id="transaction-category-form-title"
                                class="text-lg font-black text-slate-900">
                                Add Category
                            </h4>
                            <p class="text-xs font-medium text-slate-400">
                                Enter the category details below.
                            </p>
                        </div>
                        <button
                            type="button"
                            onclick="hideTransactionCategoryForm()"
                            class="w-9 h-9 rounded-full bg-white text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all flex items-center justify-center">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    <form id="transaction-category-form">
                        <input type="hidden" id="transaction-category-id">
                        <div class="space-y-4">
                            <!-- NAME -->
                            <div>
                                <label
                                    for="transaction-category-name"
                                    class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-2 ml-1">
                                    Category Name
                                </label>
                                <input
                                    type="text"
                                    id="transaction-category-name"
                                    maxlength="100"
                                    placeholder="e.g. Loan Repayment"
                                    class="w-full px-4 py-3.5 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all">
                            </div>
                            <!-- TRANSACTION TYPE -->
                            <div>
                                <label
                                    for="transaction-category-type"
                                    class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-2 ml-1">
                                    Transaction Type
                                </label>
                                <select
                                    id="transaction-category-type"
                                    required
                                    class="w-full px-4 py-3.5 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all">
                                    <option value="" selected disabled>
                                        Select transaction type
                                    </option>
                                    <option value="cash_in">
                                        Cash In
                                    </option>
                                    <option value="cash_out">
                                        Cash Out
                                    </option>
                                </select>
                            </div>
                            <!-- DESCRIPTION -->
                            <div>
                                <label
                                    for="transaction-category-description"
                                    class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-2 ml-1">
                                    Description
                                    <span class="text-slate-300 normal-case tracking-normal">
                                        (Optional)
                                    </span>
                                </label>
                                <textarea
                                    id="transaction-category-description"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Brief description of this category..."
                                    class="w-full px-4 py-3.5 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all resize-none"></textarea>
                            </div>
                            <!-- FORM BUTTONS -->
                            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-1">
                                <button
                                    type="button"
                                    onclick="hideTransactionCategoryForm()"
                                    class="w-full sm:w-auto px-5 py-3 rounded-2xl font-bold text-slate-500 hover:bg-white transition-all">
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    id="transaction-category-save-button"
                                    class="w-full sm:flex-1 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-2xl font-black shadow-lg hover:shadow-xl active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-plus" id="transaction-category-save-icon"></i>
                                    <span id="transaction-category-save-text">
                                        Add Category
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- LOADING -->
                <div
                    id="transaction-category-loading"
                    class="hidden py-12 flex-col items-center justify-center text-center">
                    <div class="w-12 h-12 rounded-full border-4 border-blue-100 border-t-blue-600 animate-spin mb-4"></div>
                    <p class="text-sm font-bold text-slate-500">
                        Loading categories...
                    </p>
                </div>
                <!-- EMPTY STATE -->
                <div
                    id="transaction-category-empty"
                    class="hidden py-12 flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-300 flex items-center justify-center mb-4">
                        <i class="fas fa-tags text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-black text-slate-700">
                        No categories found
                    </h4>
                    <p class="text-sm font-medium text-slate-400 mt-1">
                        Try another search or add a new category.
                    </p>
                </div>
                <!-- CATEGORY LIST -->
                <div
                    id="transaction-category-list"
                    class="space-y-3 max-h-[48vh] overflow-y-auto pe-1">
                    <!-- Categories injected by JavaScript -->
                </div>
            </div>
            <!-- FOOTER -->
            <div class="modal-footer border-0 bg-slate-50/50 px-4 px-sm-5 py-4">
                <div class="w-full flex items-center justify-between gap-3">
                    <p
                        id="transaction-category-count"
                        class="text-xs font-bold text-slate-400">
                        0 categories
                    </p>
                    <button
                        type="button"
                        class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 font-bold text-sm transition-all"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

