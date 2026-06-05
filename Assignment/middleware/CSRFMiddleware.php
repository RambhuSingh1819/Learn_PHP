<?php
/**
 * Cross-Site Request Forgery (CSRF) Protection Middleware
 */

class CSRFMiddleware {
    /**
     * Handle the request and validate the CSRF token on data-modifying HTTP methods
     * 
     * @param bool $isApi
     */
    public static function handle(bool $isApi = false): void {
        SessionService::init();
        
        $safeMethods = ['GET', 'HEAD', 'OPTIONS', 'TRACE'];
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        if (!in_array($requestMethod, $safeMethods, true)) {
            $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? null;
            
            if ($token === null) {
                $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            }
            
            if ($token === null || !SessionService::verifyCSRFToken($token)) {
                error_log("CSRF validation failed for client IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'));
                
                if ($isApi || self::isAjaxRequest()) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'CSRF verification failed. The form security token has expired or is invalid.'
                    ]);
                    exit;
                } else {
                    http_response_code(403);
                    $_SESSION['error_message'] = 'Security check failed: CSRF token invalid. Please refresh the page and try again.';
                    header("Location: " . url('/403'));
                    exit;
                }
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
