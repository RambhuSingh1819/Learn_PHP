<?php
/**
 * Authentication Middleware / Guard
 */

class AuthMiddleware {
    /**
     * Handle the incoming request
     * 
     * @param bool $isApi If true, returns JSON instead of redirecting
     */
    public static function handle(bool $isApi = false): void {
        SessionService::init();
        
        if (!SessionService::isLoggedIn()) {
            if ($isApi || self::isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Unauthorized. Please login to access this resource.'
                ]);
                exit;
            } else {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
                $_SESSION['error_message'] = 'Please log in to access this page.';
                
                header("Location: " . url('/login'));
                exit;
            }
        }
    }

    /**
     * Determine if request is AJAX
     */
    private static function isAjaxRequest(): bool {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' ||
               (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
               str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}
