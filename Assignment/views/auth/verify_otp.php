<?php
$pageTitle = 'Verify Email - Secure Auth System';
?>

<div class="container my-5 py-5 animate-fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="display-font text-white mb-1">Verify Your Email</h2>
                    <p class="text-muted small">We've generated a secure 6-digit OTP code for your Gmail address:<br><strong class="text-white"><?= htmlspecialchars($email) ?></strong></p>
                    
                    <?php if (env('EMAIL_API_KEY') === 'simulate'): ?>
                        <div class="alert alert-info mt-3 p-2 small border-0" style="background: rgba(99, 102, 241, 0.15); color: #cbd5e1; border-radius: 10px;">
                            <i class="bi bi-info-circle-fill"></i> <strong>Simulation Mode Active:</strong><br>Check <code>logs/otp.log</code> in your project folder to get the 6-digit OTP code!
                        </div>
                    <?php endif; ?>
                </div>

                <form action="<?= url('/verify-otp') ?>" method="POST" data-ajax="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    
                    <div class="mb-4">
                        <label for="otp" class="form-label text-center d-block">Enter 6-Digit Code</label>
                        <input type="text" class="form-control text-center fs-2 fw-bold letter-spacing-lg" 
                               id="otp" name="otp" placeholder="000000" maxlength="6" pattern="\d{6}" 
                               style="letter-spacing: 12px; font-family: monospace;" required autocomplete="off">
                        <small class="text-muted text-center d-block mt-2" style="font-size: 0.75rem;">OTP codes expire after 10 minutes.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-shield-check me-1"></i> Verify OTP Code
                    </button>
                    
                    <div class="text-center">
                        <span class="text-muted small">Didn't receive the code? 
                            <button type="button" id="resend-otp-btn" class="btn btn-link text-primary text-decoration-none p-0 fw-500 border-0 bg-transparent align-baseline" 
                                    onclick="resendOTP(<?= json_encode($email) ?>)">
                                Resend OTP
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.letter-spacing-lg {
    letter-spacing: 8px;
}
</style>
