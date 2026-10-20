<div class="modal fade" id="modal-transaction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <h3 class="text-2xl font-black text-slate-900 mb-0">New Transaction</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 px-sm-5 py-2">
                <div class="space-y-4">
                    <!-- Step 1: Main Type -->
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-400 mb-2 ml-1">Transaction Type</label>
                        <select id="t-main-type" onchange="handleMainTypeChange()" class="form-select custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                            <option value="">Select Transaction Type...</option>
                            <option value="in">Cash In</option>
                            <option value="out">Cash Out</option>
                        </select>
                    </div>

                    <!-- Step 2: Sub-Category (Dynamic) -->
                    <div id="container-sub-type" class="hidden">
                        <label class="block text-xs font-black uppercase text-slate-400 mb-2 ml-1">Transaction Category</label>
                        <select id="t-sub-type" onchange="handleSubTypeChange()" class="form-select custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                            <!-- Options injected by JS -->
                        </select>
                    </div>

                    <!-- Step 3: Specific Selection (Client or Officer) -->
                    <div id="container-searchable" class="hidden">
                        <label id="search-label" class="block text-xs font-black uppercase text-slate-400 mb-2 ml-1">Select Person</label>
                        <div class="relative">
                            <input type="text" id="t-search-input" onkeyup="filterSearchList()" class="form-control custom-input py-3 px-4 rounded-t-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none" placeholder="Type to search...">
                            <div id="search-results" class="absolute z-50 w-full max-h-40 overflow-y-auto bg-white border-2 border-t-0 border-slate-100 rounded-b-2xl shadow-xl hidden">
                                <!-- List items injected by JS -->
                            </div>
                            <input type="hidden" id="t-selected-id">
                        </div>
                    </div>

                    <!-- Step 4: Amount & Note -->
                    <div id="container-amount" class="hidden">
                        <div class="mb-4">
                            <label class="block text-xs font-black uppercase text-slate-400 mb-2 ml-1">Amount (UGX)</label>
                            <input type="number" id="t-amount" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-400 mb-2 ml-1">Note / Description</label>
                            <input type="text" id="t-note" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none" placeholder="Optional details...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">
                <button type="button" class="btn py-3 px-4 font-bold text-slate-500 order-2 order-sm-1" data-bs-dismiss="modal">Cancel</button>
                <button
                    onclick="processTransaction()"
                    id="btn-save-transaction"
                    class="btn btn-primary bg-blue-600 border-0 py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1 order-1 order-sm-2 hidden"
                >
                    <span id="btn-save-transaction-content">
                        Save Record
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>