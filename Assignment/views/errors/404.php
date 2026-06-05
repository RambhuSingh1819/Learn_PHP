<?php
$pageTitle = '404 Page Not Found - Secure Auth System';
?>

<div class="container my-5 py-5 animate-fade-in-up">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6 text-center">
            <div class="glass-card py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-4" style="width: 80px; height: 80px;">
                    <i class="bi bi-compass-fill" style="font-size: 2.5rem;"></i>
                </div>
                
                <h1 class="display-font text-white display-4 mb-3">404 Not Found</h1>
                
                <p class="text-muted mb-4 lead" style="font-size: 1.1rem; line-height: 1.6;">
                    The page you are looking for does not exist, has been archived, or was moved to another directory.
                </p>
                
                <hr class="border-secondary border-opacity-20 my-4">
                
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="<?= url('/dashboard') ?>" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
                    </a>
                    <a href="<?= url('/login') ?>" class="btn btn-secondary px-4 py-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Return to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
