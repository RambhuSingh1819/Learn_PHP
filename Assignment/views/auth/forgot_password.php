<?php
$pageTitle = 'Recover Password - Secure Auth System';
?>

<div class="container my-5 py-5 animate-fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-question-circle" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="display-font text-white mb-1">Recover Password</h2>
                    <p class="text-muted small">Enter your registered Gmail address below and we'll dispatch a secure 6-digit OTP code to verify your identity and authorize recovery.</p>
                </div>

                <form action="<?= url('/forgot-password') ?>" method="POST" data-ajax="true" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 text-muted" style="background: rgba(15, 23, 42, 0.6);"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="username@example.com" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-envelope-paper me-1"></i> Send Recovery Code
                    </button>
                    
                    <div class="text-center">
                        <span class="text-muted small"><a href="<?= url('/login') ?>" class="text-primary text-decoration-none fw-500"><i class="bi bi-arrow-left"></i> Back to Sign In</a></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
