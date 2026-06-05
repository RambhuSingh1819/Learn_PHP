<?php
$pageTitle = 'Login - Secure Auth System';
?>

<div class="container my-5 py-5 animate-fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-lock-fill" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="display-font text-white mb-1">Welcome Back</h2>
                    <p class="text-muted small">Please sign in to access your secure account dashboard.</p>
                </div>

                <form action="<?= url('/login') ?>" method="POST" data-ajax="true" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="example@example.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            <a href="<?= url('/forgot-password') ?>" class="text-primary text-decoration-none small">Forgot Password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <div class="mb-4 form-check d-flex align-items-center gap-2">
                        <input type="checkbox" class="form-check-input mt-0" id="remember" name="remember" style="background-color: rgba(15, 23, 42, 0.6); border: 1.5px solid var(--glass-border); cursor: pointer;">
                        <label class="form-check-label text-muted small" for="remember" style="cursor: pointer; user-select: none;">Remember me for 30 days</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </button>
                    
                    <div class="text-center">
                        <span class="text-muted small">Don't have an account? <a href="<?= url('/register') ?>" class="text-primary text-decoration-none fw-500">Register</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
