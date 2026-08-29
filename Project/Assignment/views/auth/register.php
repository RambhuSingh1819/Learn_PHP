<?php
$pageTitle = 'Register - Secure Auth System';
?>

<div class="container my-5 py-4 animate-fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 col-xl-5">
            <div class="glass-card">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-plus-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="display-font text-white mb-1">Create Account</h2>
                    <p class="text-muted small">Join us to experience modern, secure systems access.</p>
                </div>

                <form action="<?= url('/register') ?>" method="POST" data-ajax="true" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="username@example.com" required>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Enter a valid email address.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Min. 4 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">Account Role</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-person-badge"></i></span>
                            <select class="form-select" id="role" name="role" required style="cursor: pointer;">
                                <option value="User" selected>Standard User</option>
                                <option value="Admin">Administrator</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-person-plus-fill me-1"></i> Register Account
                    </button>
                    
                    <div class="text-center">
                        <span class="text-muted small">Already have an account? <a href="<?= url('/login') ?>" class="text-primary text-decoration-none fw-500">Sign In</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
