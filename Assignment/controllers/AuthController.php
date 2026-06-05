<?php
/**
 * Authentication Controller
 */

require_once CONTROLLERS_PATH . '/BaseController.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Verification.php';
require_once SERVICES_PATH . '/EmailService.php';
require_once SERVICES_PATH . '/SessionService.php';

class AuthController extends BaseController {
    
    private User $userModel;
    private Verification $verificationModel;

    public function __construct() {
        $this->userModel = new User();
        $this->verificationModel = new Verification();
    }

    /**
     * Show Login View
     */
    public function showLogin(): void {
        if (SessionService::isLoggedIn()) {
            header("Location: " . url('/dashboard'));
            exit;
        }
        $this->renderView('auth/login');
    }

    /**
     * Show Register View
     */
    public function showRegister(): void {
        if (SessionService::isLoggedIn()) {
            header("Location: " . url('/dashboard'));
            exit;
        }
        $this->renderView('auth/register');
    }

    /**
     * Handle User Registration (API / AJAX)
     */
    public function register(): void {
        $this->jsonResponse($this->handleRegister());
    }

    private function handleRegister(): array {
        $input = $this->sanitizeInput($_POST);
        
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';
        $role = $input['role'] ?? 'User';
        
        // 1. Inputs validation
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            return ['status' => 'error', 'message' => 'All fields are required.'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Please enter a valid email address.'];
        }

        if ($role !== 'User' && $role !== 'Admin') {
            return ['status' => 'error', 'message' => 'Invalid role selected.'];
        }
        
        if ($password !== $confirmPassword) {
            return ['status' => 'error', 'message' => 'Passwords do not match.'];
        }
        
        // Password Complexity
        if (strlen($password) < 4) {
            return [
                'status' => 'error', 
                'message' => 'Password must be at least 4 characters long.'
            ];
        }
        
        // Check duplicate
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser) {
            return ['status' => 'error', 'message' => 'This email address is already registered.'];
        }
        
        try {
            // Create user
            $userId = $this->userModel->create($name, $email, $password, $role);
            
            // Generate OTP verification mapping to user_id
            $otp = $this->verificationModel->createOTP($userId);
            
            // Send OTP
            $emailSent = EmailService::sendOTP($email, $otp, $name);
            
            if ($emailSent) {
                $_SESSION['pending_verification_user_id'] = $userId;
                $_SESSION['pending_verification_email'] = $email;
                
                return [
                    'status' => 'success', 
                    'message' => 'Registration successful! A 6-digit verification code has been generated.',
                    'redirect' => url('/verify-otp')
                ];
            } else {
                return [
                    'status' => 'error', 
                    'message' => 'Registration succeeded, but email delivery failed. Please try logging in to trigger a new code.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error during registration: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'A system error occurred. Please try again.'];
        }
    }

    /**
     * Handle User Login (API / AJAX)
     */
    public function login(): void {
        $this->jsonResponse($this->handleLogin());
    }

    private function handleLogin(): array {
        $input = $this->sanitizeInput($_POST);
        
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $remember = isset($input['remember']) && ($input['remember'] === 'on' || $input['remember'] === true || $input['remember'] === 'true');
        
        if (empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'Email and Password are required.'];
        }
        
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Invalid email address or password.'];
        }
        
        if (!password_verify($password, $user['password'])) {
            return ['status' => 'error', 'message' => 'Invalid email address or password.'];
        }
        
        // Verify verification status
        if ((int)$user['is_verified'] === 0) {
            // Resend code dynamically
            $otp = $this->verificationModel->createOTP((int)$user['id']);
            EmailService::sendOTP($email, $otp, $user['name']);
            
            $_SESSION['pending_verification_user_id'] = (int)$user['id'];
            $_SESSION['pending_verification_email'] = $email;
            
            return [
                'status' => 'verify', 
                'message' => 'Your email address is not verified yet. A fresh OTP code has been generated.',
                'redirect' => url('/verify-otp')
            ];
        }
        
        // Log in
        SessionService::login($user, $remember);
        
        return [
            'status' => 'success', 
            'message' => 'Login successful!',
            'redirect' => url('/dashboard')
        ];
    }

    /**
     * Handle User Logout
     */
    public function logout(): void {
        SessionService::init();
        SessionService::logout();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['success_message'] = 'You have logged out successfully.';
        
        header("Location: " . url('/login'));
        exit;
    }
}
