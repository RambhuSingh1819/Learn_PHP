<?php
$pageTitle = 'Reset Password - Secure Auth System';
?>

<div class="container my-5 py-5 animate-fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-key-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="display-font text-white mb-1">Reset Password</h2>
                    <p class="text-muted small">Your identity has been verified. You can now define a new secure password for your account.</p>
                </div>

                <form action="<?= url('/forgot-password/reset') ?>" method="POST" data-ajax="true" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Min. 4 characters</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-shield-check me-1"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
