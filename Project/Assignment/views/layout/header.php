<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Secure Auth System' ?></title>
    
    <!-- CSRF Token Meta for AJAX Requests -->
    <meta name="csrf-token" content="<?= SessionService::getCSRFToken() ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom Premium Styles -->
    <link rel="stylesheet" href="<?= url('/assets/css/style.css') ?>">
    
    <script>
        const APP_URL = '<?= url() ?>';
    </script>
</head>
<body>
    
    <!-- Global Loading spinner for AJAX calls -->
    <div class="loading-overlay">
        <div class="spinner-border text-primary" style="width: 3.5rem; height: 3.5rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Navigation Header -->
    <nav class="navbar navbar-expand-lg navbar-dark border-bottom" style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('/') ?>">
                <i class="bi bi-shield-lock-fill text-primary" style="font-size: 1.5rem;"></i>
                <span class="display-font" style="font-weight: 700; font-size: 1.25rem;">SECURE<span class="text-primary">AUTH</span></span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
                    <?php if (SessionService::isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white fw-500 d-flex align-items-center gap-1" href="<?= url('/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="text-white fw-500 d-none d-lg-inline"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                            </div>
                        </li>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="<?= url('/logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?= url('/login') ?>"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary rounded-pill px-4" href="<?= url('/register') ?>"><i class="bi bi-person-plus-fill"></i> Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
