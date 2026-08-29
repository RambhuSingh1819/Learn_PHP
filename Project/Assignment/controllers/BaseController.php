<?php
/**
 * Base Application Controller
 */

abstract class BaseController {
    
    /**
     * Render an HTML view safely passing variables
     * 
     * @param string $view Relative view path (e.g. 'auth/login')
     * @param array $data Variables to pass to the view template
     */
    protected function renderView(string $view, array $data = []): void {
        extract($data);
        
        $viewFile = VIEWS_PATH . '/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            $this->renderError(404, "View [{$view}] not found.");
            return;
        }
        
        require_once VIEWS_PATH . '/layout/header.php';
        require_once $viewFile;
        require_once VIEWS_PATH . '/layout/footer.php';
    }

    /**
     * Send a structured API JSON response
     * 
     * @param array $data Response body parameters
     * @param int $statusCode HTTP Status Code
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void {
        if (ob_get_length()) {
            ob_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        echo json_encode($data);
        exit;
    }

    /**
     * Deep sanitization of inputs to prevent XSS (escapes HTML tags recursively)
     * 
     * @param mixed $data Input string, array, or object
     * @return mixed Sanitized input
     */
    protected function sanitizeInput(mixed $data): mixed {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeInput($value);
            }
            return $data;
        }
        
        if (is_string($data)) {
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }

    /**
     * Trigger a standard error response
     */
    protected function renderError(int $code = 404, string $message = ''): void {
        http_response_code($code);
        $data = ['error_message' => $message];
        
        if (file_exists(VIEWS_PATH . "/errors/{$code}.php")) {
            require_once VIEWS_PATH . '/layout/header.php';
            require_once VIEWS_PATH . "/errors/{$code}.php";
            require_once VIEWS_PATH . '/layout/footer.php';
        } else {
            echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($message) . "</p>";
        }
        exit;
    }

    /**
     * Get Client IP Address securely
     */
    protected function getClientIP(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if ($ip === '::1') {
            return '127.0.0.1';
        }
        return $ip;
    }
}
