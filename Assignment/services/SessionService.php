<?php
/**
 * Secure Session and Authentication Service
 */

require_once MODELS_PATH . '/User.php';

class SessionService {
    private static string $cookieName = 'remember_auth_token';

    /**
     * Start and bootstrap secure sessions
     */
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        self::checkInactivityTimeout();
        
        if (!self::isLoggedIn()) {
            self::checkRememberMe();
        }
        
        self::updateActivity();
    }

    /**
     * Authenticate and establish a session for a user
     * 
     * @param array $user User database record
     * @param bool $remember
     */
    public static function login(array $user, bool $remember = false): void {
        // Prevent Session Fixation by regenerating Session ID on login
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role']; // 'Admin' or 'User'
        $_SESSION['last_activity'] = time();
        
        if ($remember) {
            self::createRememberMe((int)$user['id']);
        }
    }

    /**
     * Terminate the session and clean cookies
     */
    public static function logout(): void {
        $userId = self::getUserId();
        
        if ($userId) {
            $userModel = new User();
            $userModel->clearRememberToken($userId);
        }
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        
        if (isset($_COOKIE[self::$cookieName])) {
            setcookie(self::$cookieName, '', time() - 3600, '/', '', env('APP_ENV') === 'production', true);
        }
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Check if a user is logged in
     */
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current logged-in user ID
     */
    public static function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Check if the user has a specific role
     */
    public static function hasRole(string $role): bool {
        if (!self::isLoggedIn()) {
            return false;
        }
        return ($_SESSION['user_role'] ?? 'User') === $role;
    }

    /**
     * Verify inactivity timeout (15-minute default)
     */
    private static function checkInactivityTimeout(): void {
        if (self::isLoggedIn() && isset($_SESSION['last_activity'])) {
            $timeout = (int)env('SESSION_LIFETIME', 900);
            $inactiveTime = time() - $_SESSION['last_activity'];
            
            if ($inactiveTime > $timeout) {
                self::logout();
                
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['timeout_message'] = 'You have been logged out due to inactivity.';
                
                header("Location: " . url('/login'));
                exit;
            }
        }
    }

    /**
     * Refresh last activity timestamp
     */
    public static function updateActivity(): void {
        if (self::isLoggedIn()) {
            $_SESSION['last_activity'] = time();
        }
    }

    /**
     * Generate secure Remember Me cookie & store in Database
     */
    private static function createRememberMe(int $userId): void {
        try {
            $token = bin2hex(random_bytes(32));
            $expiryDays = 30;
            $expiryTime = time() + ($expiryDays * 24 * 60 * 60);
            $expiresAtStr = date('Y-m-d H:i:s', $expiryTime);
            
            $userModel = new User();
            $userModel->updateRememberToken($userId, $token, $expiresAtStr);
            
            $cookieValue = $userId . ':' . $token;
            $secure = env('APP_ENV') === 'production';
            
            if (PHP_VERSION_ID >= 70300) {
                setcookie(self::$cookieName, $cookieValue, [
                    'expires' => $expiryTime,
                    'path' => '/',
                    'domain' => '',
                    'secure' => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            } else {
                setcookie(self::$cookieName, $cookieValue, $expiryTime, '/; SameSite=Lax', '', $secure, true);
            }
        } catch (Exception $e) {
            error_log("Failed to create remember me cookie: " . $e->getMessage());
        }
    }

    /**
     * Check Remember Me cookie and login user automatically if valid
     */
    private static function checkRememberMe(): void {
        if (!isset($_COOKIE[self::$cookieName])) {
            return;
        }
        
        $parts = explode(':', $_COOKIE[self::$cookieName], 2);
        if (count($parts) !== 2) {
            return;
        }
        
        $userId = (int)$parts[0];
        $token = $parts[1];
        
        $userModel = new User();
        $user = $userModel->findById($userId);
        
        if ($user && $user['remember_token'] && hash_equals($user['remember_token'], $token)) {
            $expiryTime = strtotime($user['remember_expires_at']);
            if (time() < $expiryTime) {
                self::login($user, true); // Rotate token
            } else {
                $userModel->clearRememberToken($userId);
            }
        }
    }

    /**
     * CSRF Protection: Generate secure token
     */
    public static function getCSRFToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * CSRF Protection: Verify token securely
     */
    public static function verifyCSRFToken(?string $token): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
