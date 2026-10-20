<div
    id="modal-staff"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="modal-staff-title"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">

            <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3
                        id="modal-staff-title"
                        class="text-xl font-black text-slate-900">
                        Add Staff
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Create a field officer or cashier staff record.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeModal('modal-staff')"
                    class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">

                <form
                    id="staff-form"
                    onsubmit="saveStaff(event)"
                    class="p-6 sm:p-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label
                                for="staff-first-name"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                First Name
                            </label>

                            <input
                                type="text"
                                id="staff-first-name"
                                name="first_name"
                                required
                                maxlength="100"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="Enter first name">
                        </div>

                        <div>
                            <label
                                for="staff-last-name"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Last Name
                            </label>

                            <input
                                type="text"
                                id="staff-last-name"
                                name="last_name"
                                required
                                maxlength="100"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="Enter last name">
                        </div>

                        <div>
                            <label
                                for="staff-email"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Email Address
                            </label>

                            <input
                                type="email"
                                id="staff-email"
                                name="email"
                                maxlength="150"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="Enter email address">
                        </div>

                        <div>
                            <label
                                for="staff-phone"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="staff-phone"
                                name="phone_number"
                                maxlength="20"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="e.g. 0771234567">
                        </div>

                        <div class="md:col-span-2">
                            <label
                                for="staff-type"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Staff Type
                            </label>

                            <select
                                id="staff-type"
                                name="type"
                                required
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all">

                                <option value="">Select staff type</option>
                                <option value="field_officer">Field Officer</option>
                                <option value="cashier">Cashier</option>

                            </select>
                        </div>

                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">

                        <button
                            type="button"
                            onclick="closeModal('modal-staff')"
                            class="px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            id="save-staff-btn"
                            class="px-7 py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md transition-all flex items-center justify-center gap-2">

                            <span id="save-staff-btn-text">
                                Add Staff
                            </span>

                            <i
                                id="save-staff-spinner"
                                class="fas fa-spinner fa-spin hidden">
                            </i>

                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</div>


<!-- EDIT STAFF MODAL -->
<div
    id="modal-edit-staff"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="modal-edit-staff-title"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">

            <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3
                        id="modal-edit-staff-title"
                        class="text-xl font-black text-slate-900">
                        Edit Staff
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Update staff contact and personal details.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeModal('modal-edit-staff')"
                    class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">

                <form
                    id="edit-staff-form"
                    onsubmit="updateStaff(event)"
                    class="p-6 sm:p-8">

                    <input
                        type="hidden"
                        id="edit-staff-id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label
                                for="edit-staff-first-name"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                First Name
                            </label>

                            <input
                                type="text"
                                id="edit-staff-first-name"
                                required
                                maxlength="100"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="Enter first name">
                        </div>

                        <div>
                            <label
                                for="edit-staff-last-name"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Last Name
                            </label>

                            <input
                                type="text"
                                id="edit-staff-last-name"
                                required
                                maxlength="100"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="Enter last name">
                        </div>

                        <div>
                            <label
                                for="edit-staff-email"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Email Address
                            </label>

                            <input
                                type="email"
                                id="edit-staff-email"
                                maxlength="150"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="Enter email address">
                        </div>

                        <div>
                            <label
                                for="edit-staff-phone"
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="edit-staff-phone"
                                maxlength="20"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                placeholder="e.g. 0771234567">
                        </div>

                        <div class="md:col-span-2">

                            <label
                                class="block text-sm font-bold text-slate-700 mb-2">
                                Staff Type
                            </label>

                            <div
                                id="edit-staff-type"
                                class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-100 text-slate-600 font-semibold rounded-xl">
                            </div>

                            <p class="text-xs text-slate-400 mt-2">
                                Staff type cannot be changed after creation.
                            </p>

                        </div>

                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">

                        <button
                            type="button"
                            onclick="closeModal('modal-edit-staff')"
                            class="px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            id="update-staff-btn"
                            class="px-7 py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md transition-all flex items-center justify-center gap-2">

                            <span id="update-staff-btn-text">
                                Save Changes
                            </span>

                            <i
                                id="update-staff-spinner"
                                class="fas fa-spinner fa-spin hidden">
                            </i>

                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>
</div>


<!-- DEACTIVATE STAFF MODAL -->
<div
    id="modal-deactivate-staff"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="modal-deactivate-staff-title"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">

            <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3
                        id="modal-deactivate-staff-title"
                        class="text-xl font-black text-slate-900">
                        Deactivate Staff
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Confirm that you want to deactivate this staff member.
                    </p>
                </div>

                <button
                    type="button"
                    onclick="closeModal('modal-deactivate-staff')"
                    class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 sm:p-8">

                <div class="flex items-start gap-4 p-4 rounded-2xl bg-red-50 border border-red-100">

                    <div class="w-11 h-11 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-user-slash"></i>
                    </div>

                    <div>
                        <p class="font-bold text-slate-900">
                            Deactivate
                            <span id="deactivate-staff-name"></span>?
                        </p>

                        <p class="text-sm text-slate-600 mt-1">
                            This staff member will no longer be active in the system.
                            Their existing records will not be deleted.
                        </p>
                    </div>

                </div>

                <input
                    type="hidden"
                    id="deactivate-staff-id">

                <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">

                    <button
                        type="button"
                        onclick="closeModal('modal-deactivate-staff')"
                        class="px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">
                        Cancel
                    </button>

                    <button
                        type="button"
                        id="confirm-deactivate-staff-btn"
                        onclick="confirmDeactivateStaff()"
                        class="px-7 py-3.5 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold shadow-md transition-all flex items-center justify-center gap-2">

                        <span id="confirm-deactivate-staff-btn-text">
                            Deactivate Staff
                        </span>

                        <i
                            id="confirm-deactivate-staff-spinner"
                            class="fas fa-spinner fa-spin hidden">
                        </i>

                    </button>

                </div>

            </div>

        </div>
    </div>
</div>

<!-- Reactivate Staff Modal -->
<div class="modal fade" id="modal-reactivate-staff" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">

            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h5 class="text-xl font-black text-slate-900 mb-1">
                            Reactivate Staff
                        </h5>
                        <p class="text-sm text-slate-500 mb-0">
                            Restore this staff member's active status.
                        </p>
                    </div>

                    <button type="button"
                        class="btn-close shrink-0"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 sm:p-8">
                <input type="hidden" id="reactivate-staff-id">

                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-user-check"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-900 mb-1">
                                Reactivate <span id="reactivate-staff-name"></span>?
                            </p>

                            <p class="text-sm text-slate-600 mb-0">
                                This staff member will become active again and will be available for normal system operations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 shrink-0">
                <button type="button"
                    onclick="closeModal('modal-reactivate-staff')"
                    class="w-full sm:w-auto px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">
                    Cancel
                </button>

                <button type="button"
                    id="confirm-reactivate-staff-btn"
                    onclick="confirmReactivateStaff()"
                    class="w-full sm:w-auto px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <span id="confirm-reactivate-staff-btn-text">
                        Reactivate Staff
                    </span>

                    <span id="confirm-reactivate-staff-spinner"
                        class="hidden">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>

        </div>
    </div>
</div>

