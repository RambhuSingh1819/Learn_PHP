<?php
$pageTitle = 'Admin Console - Secure Auth System';

$totalUsers = count($users);
$verifiedCount = 0;
$unverifiedCount = 0;

foreach ($users as $u) {
    if ((int)$u['is_verified'] === 1) {
        $verifiedCount++;
    } else {
        $unverifiedCount++;
    }
}
?>

<div class="container-fluid my-5 px-md-5 animate-fade-in-up">
    <!-- Welcome Greeting Section -->
    <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom border-secondary border-opacity-20">
        <div>
            <h1 class="display-font text-white mb-1">Administrative Console</h1>
            <p class="text-muted mb-0">System configuration, role management, user verification states, and OTP tracking.</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-30 px-3 py-2 rounded-pill fw-600">
                <i class="bi bi-person-workspace me-1"></i> Admin Access
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="glass-card py-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1 small uppercase fw-700">Total Registered Users</h6>
                    <h2 class="display-font text-white mb-0 display-6"><?= $totalUsers ?></h2>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3"><i class="bi bi-people-fill fs-3"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card py-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1 small uppercase fw-700">Verified Accounts</h6>
                    <h2 class="display-font text-success mb-0 display-6"><?= $verifiedCount ?></h2>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-3"><i class="bi bi-patch-check-fill fs-3"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card py-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1 small uppercase fw-700">Pending OTP Verification</h6>
                    <h2 class="display-font text-warning mb-0 display-6"><?= $unverifiedCount ?></h2>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3"><i class="bi bi-envelope-exclamation-fill fs-3"></i></div>
            </div>
        </div>
    </div>

    <!-- User Directory -->
    <div class="glass-card mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <h3 class="display-font text-white fs-4 mb-0"><i class="bi bi-people text-primary me-2"></i>User Database Directory</h3>
            
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- Add User Button -->
                <button type="button" class="btn btn-success btn-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Add User
                </button>

                <!-- Filters Form -->
                <form action="<?= url('/dashboard') ?>" method="GET" class="d-flex flex-wrap gap-2 align-items-center mb-0">
                    <input type="text" name="search" class="form-control form-control-sm border-0 text-white" style="width: 220px;" placeholder="Search name/email..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                    
                    <select name="role" class="form-select form-select-sm border-0 text-white" style="width: 140px; background-color: rgba(15, 23, 42, 0.6); border-radius: 12px;">
                        <option value="">All Roles</option>
                        <option value="Admin" <?= ($filters['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="User" <?= ($filters['role'] ?? '') === 'User' ? 'selected' : '' ?>>User</option>
                    </select>
                    
                    <select name="status" class="form-select form-select-sm border-0 text-white" style="width: 120px; background-color: rgba(15, 23, 42, 0.6); border-radius: 12px;">
                        <option value="">All Verification</option>
                        <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Verified</option>
                        <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Unverified</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary btn-sm px-3 py-2"><i class="bi bi-funnel-fill"></i> Filter</button>
                    <?php if (!empty($filters['search']) || !empty($filters['role']) || $filters['status'] !== ''): ?>
                        <a href="<?= url('/dashboard') ?>" class="btn btn-secondary btn-sm px-3 py-2"><i class="bi bi-x-circle"></i> Clear</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>User Profile</th>
                        <th>Account Role (Real-time update)</th>
                        <th>Email Verification</th>
                        <th>Verified Status Switch</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5 border-0">No users found matching the filter query.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-white fw-600"><?= htmlspecialchars($u['name']) ?></h6>
                                            <small class="text-muted" style="font-size: 0.8rem;"><?= htmlspecialchars($u['email']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <!-- Role Select Dropdown directly hooks into AJAXapp.js -->
                                    <select class="form-select form-select-sm user-role-select text-white border-0" 
                                            data-user-id="<?= $u['id'] ?>"
                                            <?= (int)$u['id'] === SessionService::getUserId() ? 'disabled' : '' ?>
                                            style="width: 120px; background-color: rgba(15, 23, 42, 0.6); border-radius: 12px; cursor: pointer;">
                                        <option value="User" <?= $u['role'] === 'User' ? 'selected' : '' ?>>User</option>
                                        <option value="Admin" <?= $u['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </td>
                                <td>
                                    <span id="status-badge-<?= $u['id'] ?>" class="<?= (int)$u['is_verified'] === 1 ? 'badge-verified' : 'badge-unverified' ?>"
                                          data-bs-toggle="tooltip" data-bs-html="true" 
                                          title="Last OTP Status:<br>Used/Verified: <?= $u['otp_status'] ? ($u['otp_status']['is_used'] ? 'Yes' : 'No') : 'Never Sent' ?><br>Expires: <?= $u['otp_status'] ? htmlspecialchars(date('M d H:i', strtotime($u['otp_status']['expires_at']))) : 'N/A' ?>">
                                        <?= (int)$u['is_verified'] === 1 ? 'Verified' : 'Unverified' ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Real-time Verification switch -->
                                    <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
                                        <input class="form-check-input user-status-toggle p-0 m-0" type="checkbox" role="switch" 
                                               data-user-id="<?= $u['id'] ?>" 
                                               <?= (int)$u['is_verified'] === 1 ? 'checked' : '' ?>
                                               <?= (int)$u['id'] === SessionService::getUserId() ? 'disabled' : '' ?>
                                               style="width: 2.5rem; height: 1.25rem; cursor: pointer;">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-edit-user" 
                                                data-user-id="<?= $u['id'] ?>"
                                                data-user-name="<?= htmlspecialchars($u['name']) ?>"
                                                data-user-email="<?= htmlspecialchars($u['email']) ?>"
                                                data-user-role="<?= htmlspecialchars($u['role']) ?>"
                                                data-user-verified="<?= (int)$u['is_verified'] ?>"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal" title="Edit User">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        
                                        <?php if ((int)$u['id'] !== SessionService::getUserId()): ?>
                                            <form action="<?= url('/admin/user/delete') ?>" method="POST" data-ajax="true" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user and all their uploaded files?');">
                                                <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete User">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Cannot delete yourself">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-secondary border-opacity-20 text-white" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); border-radius: 20px;">
            <div class="modal-header border-secondary border-opacity-10 py-3">
                <h5 class="modal-title display-font" id="addUserModalLabel"><i class="bi bi-person-plus-fill text-primary me-2"></i>Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('/admin/user/create') ?>" method="POST" data-ajax="true">
                <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="new_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control text-white" id="new_name" name="name" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                    </div>
                    <div class="mb-3">
                        <label for="new_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control text-white" id="new_email" name="email" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password</label>
                        <input type="password" class="form-control text-white" id="new_password" name="password" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                    </div>
                    <div class="mb-3">
                        <label for="new_role" class="form-label">Account Role</label>
                        <select class="form-select text-white" id="new_role" name="role" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                            <option value="User" selected>Standard User</option>
                            <option value="Admin">Administrator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-10 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-secondary border-opacity-20 text-white" style="background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(15px); border-radius: 20px;">
            <div class="modal-header border-secondary border-opacity-10 py-3">
                <h5 class="modal-title display-font" id="editUserModalLabel"><i class="bi bi-pencil-fill text-primary me-2"></i>Edit User Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('/admin/user/edit') ?>" method="POST" data-ajax="true">
                <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                <input type="hidden" id="edit_user_id" name="user_id">
                
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control text-white" id="edit_name" name="name" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control text-white" id="edit_email" name="email" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">New Password <span class="text-muted">(leave blank to keep current)</span></label>
                        <input type="password" class="form-control text-white" id="edit_password" name="password" placeholder="••••••••" style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Account Role</label>
                        <select class="form-select text-white" id="edit_role" name="role" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                            <option value="User">Standard User</option>
                            <option value="Admin">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_verified" class="form-label">Verification Status</label>
                        <select class="form-select text-white" id="edit_verified" name="is_verified" required style="background: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
                            <option value="1">Verified</option>
                            <option value="0">Unverified</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-10 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Edit User modal prefill logic
    const editUserModal = document.getElementById('editUserModal');
    if (editUserModal) {
        editUserModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const userEmail = button.getAttribute('data-user-email');
            const userRole = button.getAttribute('data-user-role');
            const userVerified = button.getAttribute('data-user-verified');

            // Set inputs
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_role').value = userRole;
            document.getElementById('edit_verified').value = userVerified;
            document.getElementById('edit_password').value = ''; // Reset password field
        });
    }
});
</script>
