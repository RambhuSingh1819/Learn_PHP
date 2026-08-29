<?php
/**
 * Role-Based Access Control (RBAC) Middleware Guard
 */

class RoleMiddleware {
    /**
     * Handle the incoming request and restrict access based on role
     * 
     * @param string $allowedRole 'Admin' or 'User'
     * @param bool $isApi If true, returns JSON instead of redirecting
     */
    public static function handle(string $allowedRole, bool $isApi = false): void {
        // Enforce user logged in first
        AuthMiddleware::handle($isApi);
        
        if (!SessionService::hasRole($allowedRole)) {
            if ($isApi || self::isAjaxRequest()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Forbidden. You do not have permission to access this resource.'
                ]);
                exit;
            } else {
                header("Location: " . url('/403'));
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
