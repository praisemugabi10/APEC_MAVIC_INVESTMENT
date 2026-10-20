<!-- Add Client Modal -->
<div class="modal fade" id="modal-client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <h3 id="client-modal-title" class="text-2xl font-black text-slate-900 mb-0">Register New Client</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 px-sm-5 py-4 overflow-y-auto" style="max-height: 70vh;">
                <form id="form-add-client">
                    <input type="hidden" id="cl-existing-client-id" value="">
                    <div class="row g-3">
                        <!-- SECTION: CLIENT INFO -->
                        <div class="col-12 mt-0">
                            <span class="text-[14px] font-black uppercase text-blue-600 tracking-wider">Client Personal Details</span>
                            <hr class="mt-1 border-slate-100">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">First Name</label>
                            <input type="text" id="cl-fname" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Last Name</label>
                            <input type="text" id="cl-lname" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Primary Phone</label>
                            <input type="tel" id="cl-phone-1" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none" placeholder="07...">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Secondary Phone (Optional)</label>
                            <input type="tel" id="cl-phone-2" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none" placeholder="07...">
                        </div>
                        <div class="col-12">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Residential Location</label>
                            <input type="text" id="cl-location" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                        <div class="col-12">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Mortgage</label>
                            <input type="text" id="cl-mortgage" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>

                        <!-- SECTION: LOAN INFO -->
                        <div class="col-12 mt-4">
                            <span class="text-[14px] font-black uppercase text-emerald-600 tracking-wider">Loan & Officer Assignment</span>
                            <hr class="mt-1 border-slate-100">
                        </div>
                        <div class="col-12">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Field Officer</label>

                            <input type="hidden" id="cl-officer-id" value="">

                            <div class="relative" id="field-officer-select">
                                <button
                                    type="button"
                                    id="field-officer-trigger"
                                    onclick="toggleFieldOfficerDropdown()"
                                    class="w-full form-control custom-input py-3 px-4 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none text-left flex items-center justify-between">
                                    <span id="field-officer-selected" class="text-slate-400">Select Officer...</span>
                                    <i class="fas fa-chevron-down text-slate-400 text-sm"></i>
                                </button>

                                <div
                                    id="field-officer-dropdown"
                                    class="hidden absolute z-50 left-0 right-0 mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden">

                                    <div class="p-3 border-b border-slate-100">
                                        <div class="relative">
                                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                            <input
                                                type="text"
                                                id="field-officer-search"
                                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-sm font-bold"
                                                placeholder="Search officer..."
                                                oninput="filterFieldOfficers(this.value)">
                                        </div>
                                    </div>

                                    <div id="field-officer-options" class="max-h-60 overflow-y-auto">
                                        <div class="px-4 py-4 text-sm text-slate-400 text-center">
                                            Loading officers...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Loan Amount (UGX)</label>
                            <input type="number" id="cl-amount" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Interest Rate (%)</label>
                            <input type="number" id="cl-interest" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="block text-[12px] font-black uppercase text-emerald-700 mb-2 ml-1">Expected Return</label>
                            <input type="text" id="cl-return" class="form-control custom-input py-3 rounded-2xl font-black border-2 border-emerald-100 bg-emerald-50 text-emerald-700 shadow-none" readonly value="0">
                        </div>

                        <!-- SECTION: GUARANTOR INFO -->
                        <div class="col-12 mt-4">
                            <span class="text-[14px] font-black uppercase text-amber-600 tracking-wider">Guarantor Information</span>
                            <hr class="mt-1 border-slate-100">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Guarantor First Name</label>
                            <input type="text" id="cl-g-fname" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Guarantor Last Name</label>
                            <input type="text" id="cl-g-lname" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Guarantor Phone</label>
                            <input type="tel" id="cl-g-phone" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none" placeholder="07...">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="block text-[12px] font-black uppercase text-slate-500 mb-2 ml-1">Guarantor Location</label>
                            <input type="text" id="cl-g-location" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">
                <button type="button" class="btn py-3 px-4 font-bold text-slate-500" data-bs-dismiss="modal">Cancel</button>
                <button id="btn-save-client" onclick="saveClient()" type="button" class="btn btn-primary bg-blue-600 border-0 py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1">
                    Register Client
                </button>
            </div>
        </div>
    </div>
</div>

<!-- edit client modal -->
<div class="modal fade" id="modal-edit-client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <h3 class="text-2xl font-black text-slate-900 mb-0">Edit Client</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 px-sm-5 py-4 overflow-y-auto">
                <div class="row g-3">
                    <input type="hidden" id="edit-cl-id">
                    <div class="col-12 col-sm-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">First Name</label>
                        <input type="text" id="edit-cl-fname" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">Last Name</label>
                        <input type="text" id="edit-cl-lname" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                    </div>
                    <div class="col-12">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">Residential Location</label>
                        <input type="text" id="edit-cl-location" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                    </div>
                    <div class="col-12">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">Mortgage</label>
                        <input type="text" id="edit-cl-mortgage" class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">
                <button type="button" class="btn py-3 px-4 font-bold text-slate-500 order-2 order-sm-1" data-bs-dismiss="modal">Cancel</button>
                <button onclick="updateClient()" type="button" class="btn btn-primary bg-blue-600 border-0 py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1 order-1 order-sm-2">
                    <i class="fas fa-save me-2"></i> Update Client
                </button>
            </div>
        </div>
    </div>
</div>

<!-- delete client modal -->
<div class="modal fade" id="modal-delete-client" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <h3 class="text-2xl font-black text-slate-900 mb-0">Deactivate Client</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 px-sm-5 py-4">
                <input type="hidden" id="delete-cl-id">
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-user-slash text-red-600 text-2xl"></i>
                    </div>
                    <p class="text-slate-600 font-medium">
                        Are you sure you want to deactivate this client? They will no longer appear in active lists.
                    </p>
                    <p id="delete-cl-name" class="font-black text-slate-900 text-lg"></p>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">
                <button type="button" class="btn py-3 px-4 font-bold text-slate-500 order-2 order-sm-1" data-bs-dismiss="modal">Cancel</button>
                <button onclick="deleteClient()" type="button" class="btn btn-danger py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1 order-1 order-sm-2">
                    <i class="fas fa-trash me-2"></i> Deactivate Client
                </button>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div class="modal fade" id="modal-history" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5 d-flex justify-content-between align-items-start">
                <div>
                    <h3 id="hist-client-name" class="text-2xl font-black text-slate-900 mb-0">Client History</h3>
                    <p class="text-sm text-slate-500 mb-0">Recent payments and balances</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 px-sm-5 py-4">
                <div id="history-container" class="space-y-4 overflow-y-auto" style="max-height: 60vh;">
                    <!-- Timeline Data -->
                </div>
            </div>
            <div class="modal-footer border-0 d-sm-none pb-4 px-4">
                <button type="button" class="btn btn-dark w-full py-3 rounded-2xl font-bold"
                    data-bs-dismiss="modal">Close View</button>
            </div>
        </div>
    </div>
</div>