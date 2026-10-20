<!-- Dashboard Field Officer Payment Breakdown Modal -->
<div class="modal fade" id="modal-dashboard-officer-breakdown" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl">

            <div class="modal-header border-0 pt-4 px-4 px-sm-5">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">
                        Field Officer Breakdown
                    </h3>

                    <p id="dashboard-officer-breakdown-date"
                        class="text-sm font-bold text-slate-400 mb-0">
                    </p>
                </div>

                <button type="button"
                    class="btn-close shadow-none"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body px-3 px-sm-5 py-4 overflow-y-auto">

                <div id="dashboard-officer-breakdown-container">
                    <div class="flex justify-center items-center py-12 text-slate-500">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <span>Loading officer breakdown...</span>
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 pb-4 px-4 px-sm-5">
                <button type="button"
                    class="btn py-3 px-4 font-bold text-slate-500"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>