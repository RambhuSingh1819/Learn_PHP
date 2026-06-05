<?php
/**
 * Front Controller and Application Router
 */

// 1. Support built-in PHP web server static files routing
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (file_exists($filePath) && is_file($filePath)) {
        return false;
    }
}

// 2. Initialize configs and Database connection
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';

// Initialize session
require_once __DIR__ . '/services/SessionService.php';
SessionService::init();

// Parse request
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '/';

// Strip project subfolder path prefix if running in a subdirectory (e.g. XAMPP /Assignment_PW)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$subfolder = dirname($scriptName); // e.g. '/Assignment_PW' or '\' or '/'
$subfolder = str_replace('\\', '/', $subfolder);
if ($subfolder !== '/' && $subfolder !== '') {
    if (str_starts_with($path, $subfolder)) {
        $path = substr($path, strlen($subfolder));
    }
}
if (empty($path)) {
    $path = '/';
}

// Trim trailing slashes except for root
if ($path !== '/' && str_ends_with($path, '/')) {
    $path = rtrim($path, '/');
}

// Auto-load Middleware classes
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RoleMiddleware.php';
require_once __DIR__ . '/middleware/CSRFMiddleware.php';

// Check CSRF Protection for state-modifying requests (POST)
if ($requestMethod === 'POST') {
    $isApi = str_starts_with($path, '/api/') || 
             str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ||
             (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
             
    CSRFMiddleware::handle($isApi);
}

// 3. Routing Table
try {
    switch ($path) {
        case '/':
            if (SessionService::isLoggedIn()) {
                header("Location: " . url('/dashboard'));
            } else {
                header("Location: " . url('/login'));
            }
            exit;
            
        case '/login':
            if ($requestMethod === 'GET') {
                require_once CONTROLLERS_PATH . '/AuthController.php';
                (new AuthController())->showLogin();
            } else {
                require_once CONTROLLERS_PATH . '/AuthController.php';
                (new AuthController())->login();
            }
            break;
            
        case '/register':
            if ($requestMethod === 'GET') {
                require_once CONTROLLERS_PATH . '/AuthController.php';
                (new AuthController())->showRegister();
            } else {
                require_once CONTROLLERS_PATH . '/AuthController.php';
                (new AuthController())->register();
            }
            break;
            
        case '/logout':
            require_once CONTROLLERS_PATH . '/AuthController.php';
            (new AuthController())->logout();
            break;
            
        case '/verify-otp':
            if ($requestMethod === 'GET') {
                require_once CONTROLLERS_PATH . '/VerificationController.php';
                (new VerificationController())->showVerify();
            } else {
                require_once CONTROLLERS_PATH . '/VerificationController.php';
                (new VerificationController())->verify();
            }
            break;
            
        case '/verify-otp/resend':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/VerificationController.php';
                (new VerificationController())->resend();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/forgot-password':
            if ($requestMethod === 'GET') {
                require_once CONTROLLERS_PATH . '/PasswordResetController.php';
                (new PasswordResetController())->showForgot();
            } else {
                require_once CONTROLLERS_PATH . '/PasswordResetController.php';
                (new PasswordResetController())->forgot();
            }
            break;
            
        case '/forgot-password/verify':
            if ($requestMethod === 'GET') {
                require_once CONTROLLERS_PATH . '/PasswordResetController.php';
                (new PasswordResetController())->showVerifyOTP();
            } else {
                require_once CONTROLLERS_PATH . '/PasswordResetController.php';
                (new PasswordResetController())->verifyOTP();
            }
            break;
            
        case '/forgot-password/reset':
            if ($requestMethod === 'GET') {
                require_once CONTROLLERS_PATH . '/PasswordResetController.php';
                (new PasswordResetController())->showResetPassword();
            } else {
                require_once CONTROLLERS_PATH . '/PasswordResetController.php';
                (new PasswordResetController())->resetPassword();
            }
            break;
            
        case '/dashboard':
            if ($requestMethod === 'GET') {
                require_once CONTROLLERS_PATH . '/DashboardController.php';
                (new DashboardController())->index();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/admin/user/toggle-status':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/DashboardController.php';
                (new DashboardController())->toggleStatus();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/admin/user/update-role':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/DashboardController.php';
                (new DashboardController())->updateRole();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/admin/user/create':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/DashboardController.php';
                (new DashboardController())->createUser();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/admin/user/edit':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/DashboardController.php';
                (new DashboardController())->editUser();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/admin/user/delete':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/DashboardController.php';
                (new DashboardController())->deleteUser();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/profile/update':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/ProfileController.php';
                (new ProfileController())->update();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/profile/change-password':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/ProfileController.php';
                (new ProfileController())->changePassword();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/profile/upload-file':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/ProfileController.php';
                (new ProfileController())->uploadFile();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/profile/delete-file':
            if ($requestMethod === 'POST') {
                require_once CONTROLLERS_PATH . '/ProfileController.php';
                (new ProfileController())->deleteFile();
            } else {
                http_response_code(405);
                echo "Method Not Allowed";
            }
            break;
            
        case '/403':
            http_response_code(403);
            require_once VIEWS_PATH . '/layout/header.php';
            require_once VIEWS_PATH . '/errors/403.php';
            require_once VIEWS_PATH . '/layout/footer.php';
            break;
            
        case '/404':
        default:
            http_response_code(404);
            require_once VIEWS_PATH . '/layout/header.php';
            require_once VIEWS_PATH . '/errors/404.php';
            require_once VIEWS_PATH . '/layout/footer.php';
            break;
    }
} catch (Exception $e) {
    error_log("Unhandled Application Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    require_once VIEWS_PATH . '/layout/header.php';
    if (env('APP_ENV') === 'development') {
        echo "<div class='container my-5'><div class='alert alert-danger'><h4 class='alert-heading'>Unhandled System Exception!</h4><p>" . htmlspecialchars($e->getMessage()) . "</p><hr><pre class='mb-0'>" . htmlspecialchars($e->getTraceAsString()) . "</pre></div></div>";
    } else {
        echo "<div class='container my-5 text-center'><div class='card shadow-lg p-5 border-0'><div class='card-body'><h1 class='display-4 text-danger mb-4'><i class='bi bi-exclamation-triangle-fill'></i> System Error</h1><p class='lead'>A critical system error occurred. Please try again later.</p><a href='" . url('/login') . "' class='btn btn-primary btn-lg mt-3'>Return to Login</a></div></div></div>";
    }
    require_once VIEWS_PATH . '/layout/footer.php';
}
