<!-- Add Field Officer Modal -->
<div class="modal fade" id="modal-officer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <h3 class="text-2xl font-black text-slate-900 mb-0">Add Field Officer</h3>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 px-sm-5 py-4 overflow-y-auto">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">First
                            Name</label>
                        <input type="text" id="of-fname"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="John">
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">Last
                            Name</label>
                        <input type="text" id="of-lname"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="Doe">
                    </div>
                    <div class="col-12">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">Phone
                            Number</label>
                        <input type="tel" id="of-phone"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="+256 ...">
                    </div>
                    <div class="col-12">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">Email
                            Address</label>
                        <input type="email" id="of-email"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="officer@apec.com">
                    </div>
                    <!-- Add this hidden field to track the ID during updates -->
                    <input type="hidden" id="of-id" value="">
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">
                <button onclick="saveOfficer()" type="button"
                    class="btn btn-primary bg-blue-600 border-0 py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1">Register
                    Officer</button>
                <button type="button" class="btn py-3 px-4 font-bold text-slate-500"
                    data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Field Officer Modal -->
<div class="modal fade" id="modal-edit-officer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">

            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <h3 class="text-2xl font-black text-slate-900 mb-0">
                    Edit Field Officer
                </h3>

                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 px-sm-5 py-4 overflow-y-auto">
                <div class="row g-3">

                    <input type="hidden" id="edit-of-id">

                    <div class="col-12 col-sm-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">
                            First Name
                        </label>

                        <input type="text" id="edit-of-fname"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="John">
                    </div>


                    <div class="col-12 col-sm-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">
                            Last Name
                        </label>

                        <input type="text" id="edit-of-lname"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="Doe">
                    </div>


                    <div class="col-12">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">
                            Phone Number
                        </label>

                        <input type="tel" id="edit-of-phone"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="+256 ...">
                    </div>


                    <div class="col-12">
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">
                            Email Address
                        </label>

                        <input type="email" id="edit-of-email"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                            placeholder="officer@apec.com">
                    </div>

                </div>
            </div>


            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">

                <button type="button"
                    class="btn py-3 px-4 font-bold text-slate-500 order-2 order-sm-1"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button onclick="updateOfficer()" type="button"
                    class="btn btn-primary bg-blue-600 border-0 py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1 order-1 order-sm-2">
                    <i class="fas fa-save mr-2"></i>
                    Update Officer
                </button>

            </div>

        </div>
    </div>
</div>

<!-- Delete Field Officer Modal -->
<div class="modal fade" id="modal-delete-officer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">

            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <h3 class="text-2xl font-black text-slate-900 mb-0">
                    Delete Field Officer
                </h3>

                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>


            <div class="modal-body px-4 px-sm-5 py-4">

                <input type="hidden" id="delete-of-id">

                <div class="text-center space-y-4">

                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-trash text-red-600 text-2xl"></i>
                    </div>

                    <p class="text-slate-600 font-medium">
                        Are you sure you want to delete this field officer?
                    </p>

                    <p id="delete-of-name" class="font-black text-slate-900 text-lg"></p>

                </div>

            </div>


            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">

                <button type="button"
                    class="btn py-3 px-4 font-bold text-slate-500 order-2 order-sm-1"
                    data-bs-dismiss="modal">
                    Cancel
                </button>


                <button id="confirm-delete-officer" onclick="deleteOfficer()" type="button"
                    class="btn btn-danger py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1 order-1 order-sm-2">
                    <i class="fas fa-trash me-2"></i> <!-- Changed mr-2 to me-2 for Bootstrap 5 consistency -->
                    Delete Officer
                </button>

            </div>

        </div>
    </div>
</div>

<!-- Field Officer Clients Modal -->
<div class="modal fade" id="modal-officer-clients" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-md-down" style="max-width: 1400px;">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">Field Officer Clients</h3>
                    <p id="clients-officer-name" class="text-sm font-bold text-slate-400 mb-0"></p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-3 px-sm-5 py-3 overflow-y-auto">
                <input type="hidden" id="clients-officer-id">
                <div id="officer-clients-container">
                    <div class="flex justify-center items-center py-12 text-slate-500">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <span>Loading clients...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4 px-sm-5">
                <button type="button" class="btn py-2 px-4 font-bold text-slate-500" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- record client payment modal -->
<div class="modal fade" id="modal-client-payment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <div>
                    <h3 id="payment-modal-title" class="text-2xl font-black text-slate-900 mb-1">Record Payment</h3>
                    <p id="payment-client-name" class="text-sm font-bold text-slate-400 mb-0"></p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 px-sm-5 py-4">
                <input type="hidden" id="payment-client-id">
                <input type="hidden" id="payment-loan-id">
                <input type="hidden" id="payment-recording-mode">

                <div id="cashier-client-selection" class="mb-4 d-none">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">
                        Select Client
                    </label>

                    <div id="cashier-client-dropdown" class="position-relative">
                        <button type="button"
                            id="cashier-client-select"
                            class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none text-start d-flex justify-content-between align-items-center"
                            onclick="toggleCashierClientDropdown()">
                            <span id="cashier-client-selected">Select client</span>
                            <i class="fas fa-chevron-down text-slate-400"></i>
                        </button>

                        <div id="cashier-client-menu"
                            class="position-absolute start-0 end-0 bg-white border border-slate-100 rounded-2xl shadow-lg mt-2 d-none"
                            style="z-index: 1055; max-height: 300px; overflow-y: auto;">

                            <div class="p-2 border-bottom border-slate-100">
                                <input type="text"
                                    id="cashier-client-search"
                                    class="form-control custom-input py-2 rounded-xl border-0 bg-slate-50 shadow-none"
                                    placeholder="Search client..."
                                    autocomplete="off"
                                    onclick="event.stopPropagation()">
                            </div>

                            <div id="cashier-client-options"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-4 mb-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-[10px] font-black uppercase text-slate-400">Total Repayable</div>
                            <div id="payment-total-repayable" class="text-lg font-black text-slate-900"></div>
                        </div>

                        <div class="col-6">
                            <div class="text-[10px] font-black uppercase text-slate-400">Current Balance</div>
                            <div id="payment-current-balance" class="text-lg font-black text-red-600"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">
                        Amount Received
                    </label>

                    <input type="number" id="payment-amount" min="0.01" step="0.01"
                        class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none"
                        placeholder="Enter amount received">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">
                        Transaction Date
                    </label>

                    <input type="date" id="payment-date"
                        class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-slate-50 shadow-none">
                </div>
            </div>

            <div class="modal-footer border-0 pb-4 px-4 px-sm-5 flex-column flex-sm-row gap-3">
                <button type="button"
                    class="btn py-3 px-4 font-bold text-slate-500 order-2 order-sm-1"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button id="record-payment-btn"
                    onclick="recordClientPayment()"
                    type="button"
                    class="btn btn-primary bg-blue-600 border-0 py-3 px-5 rounded-2xl font-black shadow-lg flex-grow-1 order-1 order-sm-2">
                    <i class="fas fa-money-bill-wave me-2"></i>Record Payment
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Client Payment History Modal -->
<div class="modal fade" id="modal-client-history" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-md-down" style="max-width: 1400px;">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">

            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">Payment History</h3>
                    <p id="history-client-name" class="text-sm font-bold text-slate-400 mb-0"></p>
                </div>

                <button
                    type="button"
                    class="btn-close shadow-none"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body px-3 px-sm-5 py-4 overflow-y-auto">

                <input type="hidden" id="history-client-id">
                <input type="hidden" id="history-loan-id">

                <div id="client-history-container">
                    <div class="flex justify-center items-center py-12 text-slate-500">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <span>Loading payment history...</span>
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 pb-4 px-4 px-sm-5">
                <button
                    type="button"
                    class="btn py-3 px-4 font-bold text-slate-500"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Client Payment Modal -->
<div class="modal fade" id="modal-edit-client-payment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-2xl rounded-3xl">

            <div class="modal-header border-0 pt-4 px-4 px-sm-5">

                <div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">
                        Edit Payment
                    </h3>

                    <p
                        id="edit-payment-client-name"
                        class="text-sm font-bold text-slate-400 mb-0">
                    </p>
                </div>

                <button
                    type="button"
                    class="btn-close shadow-none"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body px-4 px-sm-5 py-4">

                <input type="hidden" id="edit-payment-client-id">
                <input type="hidden" id="edit-payment-loan-id">
                <input type="hidden" id="edit-payment-id">

                <div class="mb-4">
                    <label
                        for="edit-payment-date"
                        class="form-label text-sm font-black text-slate-700">
                        Payment Date
                    </label>

                    <input
                        type="date"
                        class="form-control form-control-lg"
                        id="edit-payment-date">
                </div>

                <div class="mb-4">
                    <label
                        for="edit-payment-amount"
                        class="form-label text-sm font-black text-slate-700">
                        Amount Paid
                    </label>

                    <input
                        type="number"
                        class="form-control form-control-lg"
                        id="edit-payment-amount"
                        min="0.01"
                        step="0.01"
                        placeholder="Enter payment amount">
                </div>
                <div class="bg-amber-50 rounded-2xl p-3">
                    <div class="flex items-start">
                        <i class="fas fa-clock text-amber-500 mt-1 me-2"></i>
                        <div class="text-sm text-amber-700">
                            <div class="font-black mb-1">Editing restriction</div>
                            <div id="edit-payment-countdown" class="font-black text-amber-600">
                                Checking editing time...
                            </div>
                            <div id="edit-payment-deadline" class="mt-1 text-xs text-amber-600"></div>
                            <div class="mt-1">
                                This payment can only be edited within 12 hours of when it was originally recorded.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pb-4 px-4 px-sm-5">

                <button
                    type="button"
                    class="btn py-3 px-4 font-bold text-slate-500"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-primary py-3 px-4 font-bold"
                    id="save-edited-payment-btn">
                    <i class="fas fa-save me-2"></i>
                    Save Changes
                </button>

            </div>

        </div>
    </div>
</div>

<!-- Client Daily Payment Breakdown Modal -->
<div class="modal fade" id="modal-payment-day-breakdown" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-md-down" style="max-width: 1400px;">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">
            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">Daily Payment Breakdown</h3>
                    <p id="breakdown-officer-name" class="text-sm font-bold text-slate-400 mb-0"></p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-3 px-sm-5 py-4 overflow-y-auto">
                <input type="hidden" id="breakdown-officer-id">

                <div class="bg-slate-50 rounded-2xl p-4 mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-7 col-md-8">
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 ml-1">Payment Date</label>
                            <input type="date" id="breakdown-payment-date"
                                class="form-control custom-input py-3 rounded-2xl font-bold border-2 border-slate-100 bg-white shadow-none">
                        </div>

                        <div class="col-12 col-sm-5 col-md-4">
                            <button type="button" onclick="setBreakdownDateToday()"
                                class="btn btn-primary bg-blue-600 border-0 py-3 px-4 rounded-2xl font-black shadow-lg w-100">
                                <i class="fas fa-calendar-day me-2"></i>Today
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-4">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-blue-500 mb-1">
                                Daily Payment Percentage
                            </p>
                            <div class="flex items-baseline gap-2">
                                <span id="breakdown-payment-percentage"
                                    class="text-3xl font-black text-blue-700">
                                    0%
                                </span>
                                <span id="breakdown-payment-percentage-summary"
                                    class="text-sm font-bold text-slate-500">
                                    0 of 0 paid
                                </span>
                            </div>
                        </div>

                        <div class="text-sm font-bold text-slate-500">
                            <i class="fas fa-calendar-day text-blue-500 me-1"></i>
                            <span id="breakdown-percentage-date"></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="breakdown-paid-tab" onclick="switchPaymentBreakdownTab('paid')"
                            class="btn btn-primary bg-blue-600 border-0 py-2 px-4 rounded-2xl font-black shadow-none">
                            <i class="fas fa-check-circle me-2"></i>Paid
                            <span id="breakdown-paid-count" class="ms-1">(0)</span>
                        </button>

                        <button type="button" id="breakdown-unpaid-tab" onclick="switchPaymentBreakdownTab('unpaid')"
                            class="btn py-2 px-4 rounded-2xl font-black text-slate-500 bg-slate-100 border-0 shadow-none">
                            <i class="fas fa-times-circle me-2"></i>Did Not Pay
                            <span id="breakdown-unpaid-count" class="ms-1">(0)</span>
                        </button>
                    </div>

                    <button type="button" onclick="printPaymentBreakdown()"
                        class="btn py-2 px-4 rounded-2xl font-black text-slate-600 bg-slate-100 border-0 shadow-none">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>

                <div id="payment-breakdown-container">
                    <div class="flex justify-center items-center py-12 text-slate-500">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <span>Loading payment breakdown...</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pb-4 px-4 px-sm-5">
                <button type="button" class="btn py-3 px-4 font-bold text-slate-500" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
