<!-- CREATE USER MODAL -->
<div id="modal-user" class="modal fade" tabindex="-1" aria-labelledby="modal-user-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3 id="modal-user-title" class="text-xl font-black text-slate-900">Create User Account</h3>
                    <p class="text-sm text-slate-500 mt-1">Create a system login account for an active staff member.</p>
                </div>
                <button type="button" onclick="closeModal('modal-user')" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto">
                <form id="user-form" onsubmit="saveUser(event)" class="p-6 sm:p-8">
                    <div class="space-y-5">
                        <div>
                            <label for="user-staff-id" class="block text-sm font-bold text-slate-700 mb-2">Staff Member</label>
                            <select id="user-staff-id" name="staff_id" required class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                <option value="">Select staff member</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-2">Only active field officers and cashiers without an existing user account are available.</p>
                        </div>
                        <div>
                            <label for="user-password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                            <input type="password" id="user-password" name="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all" placeholder="Enter password">
                        </div>
                        <div>
                            <label for="user-confirm-password" class="block text-sm font-bold text-slate-700 mb-2">Confirm Password</label>
                            <input type="password" id="user-confirm-password" name="confirm_password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all" placeholder="Confirm password">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
                        <button type="button" onclick="closeModal('modal-user')" class="px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">Cancel</button>
                        <button type="submit" id="save-user-btn" class="px-7 py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md transition-all flex items-center justify-center gap-2">
                            <span id="save-user-btn-text">Create User</span>
                            <i id="save-user-spinner" class="fas fa-spinner fa-spin hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- DEACTIVATE USER MODAL -->
<div id="modal-deactivate-user" class="modal fade" tabindex="-1" aria-labelledby="modal-deactivate-user-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3 id="modal-deactivate-user-title" class="text-xl font-black text-slate-900">Deactivate User</h3>
                    <p class="text-sm text-slate-500 mt-1">Confirm that you want to deactivate this user account.</p>
                </div>
                <button type="button" onclick="closeModal('modal-deactivate-user')" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 sm:p-8">
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-red-50 border border-red-100">
                    <div class="w-11 h-11 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Deactivate <span id="deactivate-user-name"></span>?</p>
                        <p class="text-sm text-slate-600 mt-1">This user will no longer be active in the system. Their existing records will not be deleted.</p>
                    </div>
                </div>
                <input type="hidden" id="deactivate-user-id">
                <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('modal-deactivate-user')" class="px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">Cancel</button>
                    <button type="button" id="confirm-deactivate-user-btn" onclick="confirmDeactivateUser()" class="px-7 py-3.5 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold shadow-md transition-all flex items-center justify-center gap-2">
                        <span id="confirm-deactivate-user-btn-text">Deactivate User</span>
                        <i id="confirm-deactivate-user-spinner" class="fas fa-spinner fa-spin hidden"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REACTIVATE USER MODAL -->
<div id="modal-reactivate-user" class="modal fade" tabindex="-1" aria-labelledby="modal-reactivate-user-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h5 class="text-xl font-black text-slate-900 mb-1">Reactivate User</h5>
                        <p class="text-sm text-slate-500 mb-0">Restore this user account's active status.</p>
                    </div>
                    <button type="button" class="btn-close shrink-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-6 sm:p-8">
                <input type="hidden" id="reactivate-user-id">
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 mb-1">Reactivate <span id="reactivate-user-name"></span>?</p>
                            <p class="text-sm text-slate-600 mb-0">This user account will become active again and will be available for normal system operations.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 sm:px-8 py-4 border-t border-slate-100 flex flex-col sm:flex-row justify-end gap-3 shrink-0">
                <button type="button" onclick="closeModal('modal-reactivate-user')" class="w-full sm:w-auto px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">Cancel</button>
                <button type="button" id="confirm-reactivate-user-btn" onclick="confirmReactivateUser()" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <span id="confirm-reactivate-user-btn-text">Reactivate User</span>
                    <span id="confirm-reactivate-user-spinner" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- RESET USER PASSWORD MODAL -->
<div id="modal-reset-user-password" class="modal fade" tabindex="-1" aria-labelledby="modal-reset-user-password-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content bg-white border-0 shadow-2xl rounded-mobile-none rounded-desktop-3xl overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 sm:px-8 py-5 border-b border-slate-100 shrink-0">
                <div>
                    <h3 id="modal-reset-user-password-title" class="text-xl font-black text-slate-900">Reset User Password</h3>
                    <p class="text-sm text-slate-500 mt-1">Set a new password for this user account.</p>
                </div>
                <button type="button" onclick="closeModal('modal-reset-user-password')" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition-all shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto">
                <form id="reset-user-password-form" onsubmit="resetUserPassword(event)" class="p-6 sm:p-8">
                    <input type="hidden" id="reset-user-password-id">
                    <div class="space-y-5">
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100">
                            <p class="text-sm text-slate-700">Resetting the password for <strong id="reset-user-password-name"></strong>.</p>
                        </div>
                        <div>
                            <label for="reset-user-password" class="block text-sm font-bold text-slate-700 mb-2">New Password</label>
                            <input type="password" id="reset-user-password" name="password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all" placeholder="Enter new password">
                        </div>
                        <div>
                            <label for="reset-user-confirm-password" class="block text-sm font-bold text-slate-700 mb-2">Confirm New Password</label>
                            <input type="password" id="reset-user-confirm-password" name="confirm_password" required minlength="8" maxlength="72" autocomplete="new-password" class="w-full px-4 py-3.5 rounded-xl border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-50 outline-none transition-all" placeholder="Confirm new password">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
                        <button type="button" onclick="closeModal('modal-reset-user-password')" class="px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-all">Cancel</button>
                        <button type="submit" id="reset-user-password-btn" class="px-7 py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md transition-all flex items-center justify-center gap-2">
                            <span id="reset-user-password-btn-text">Reset Password</span>
                            <i id="reset-user-password-spinner" class="fas fa-spinner fa-spin hidden"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>