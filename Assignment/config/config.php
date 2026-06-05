<?php
/**
 * Global Configuration and Bootstrap
 */

// Define absolute path constants
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('MODELS_PATH', ROOT_PATH . '/models');
define('MIDDLEWARE_PATH', ROOT_PATH . '/middleware');
define('SERVICES_PATH', ROOT_PATH . '/services');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Load environment variables from .env file
function loadEnv() {
    $envPath = ROOT_PATH . '/.env';
    if (!file_exists($envPath)) {
        return;
    }
    
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            // Strip quotes
            if (preg_match('/^"([^"]*)"$/', $value, $matches) || preg_match("/^'([^']*)'$/", $value, $matches)) {
                $value = $matches[1];
            }
            
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

// Execute environment loading
loadEnv();

/**
 * Retrieve environment variables with fallback
 */
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        
        return $value;
    }
}

// Session security configurations
$sessionLifetime = (int)env('SESSION_LIFETIME', 900); // 15 mins default
$secureCookie = env('APP_ENV', 'production') === 'production';

ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', $sessionLifetime);
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.cookie_httponly', 1);

if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secureCookie,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
} else {
    session_set_cookie_params(0, '/; SameSite=Lax', '', $secureCookie, true);
}

// Start Session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Setup Error Reporting
if (env('APP_ENV', 'development') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set Default Timezone
date_default_timezone_set('UTC');

// Custom Security Headers
function sendSecurityHeaders() {
    if (headers_sent()) {
        return;
    }
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-ancestors 'none';");
}

sendSecurityHeaders();

if (!function_exists('url')) {
    function url(string $path = ''): string {
        // Detect protocol, host, and port dynamically to avoid CORS and domain mismatch errors
        if (isset($_SERVER['HTTP_HOST'])) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            
            // Extract the subfolder path from APP_URL env
            $envUrl = env('APP_URL', '');
            $subfolder = '';
            if (!empty($envUrl)) {
                $parsed = parse_url($envUrl);
                $subfolder = $parsed['path'] ?? '';
            }
            
            $baseUrl = $protocol . '://' . $host . '/' . ltrim($subfolder, '/');
        } else {
            // Fallback for CLI/Cron/Migration scripts
            $baseUrl = env('APP_URL', 'http://localhost:8000');
        }
        
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}
