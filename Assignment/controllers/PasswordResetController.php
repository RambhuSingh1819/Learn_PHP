<?php
/**
 * Password Reset Controller
 */

require_once CONTROLLERS_PATH . '/BaseController.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/PasswordReset.php';
require_once SERVICES_PATH . '/EmailService.php';
require_once SERVICES_PATH . '/SessionService.php';

class PasswordResetController extends BaseController {

    private User $userModel;
    private PasswordReset $passwordResetModel;

    public function __construct() {
        $this->userModel = new User();
        $this->passwordResetModel = new PasswordReset();
    }

    /**
     * Show Forgot Password View
     */
    public function showForgot(): void {
        SessionService::init();
        if (SessionService::isLoggedIn()) {
            header("Location: " . url('/dashboard'));
            exit;
        }
        $this->renderView('auth/forgot_password');
    }

    /**
     * Handle Forgot Password Request (API / AJAX)
     */
    public function forgot(): void {
        $this->jsonResponse($this->handleForgot());
    }

    private function handleForgot(): array {
        SessionService::init();
        
        $input = $this->sanitizeInput($_POST);
        $email = $input['email'] ?? '';
        
        if (empty($email)) {
            return ['status' => 'error', 'message' => 'Please enter your Gmail address.'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Please enter a valid email address.'];
        }
        
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return ['status' => 'error', 'message' => 'No account was found with that email address.'];
        }
        
        try {
            // Generate OTP
            $otp = $this->passwordResetModel->createOTP($email);
            
            // Deliver email
            $emailSent = EmailService::sendPasswordResetOTP($email, $otp, $user['name']);
            
            if ($emailSent) {
                $_SESSION['reset_email'] = $email;
                return [
                    'status' => 'success',
                    'message' => 'A password reset OTP has been sent to your Gmail address.',
                    'redirect' => url('/forgot-password/verify')
                ];
            } else {
                return ['status' => 'error', 'message' => 'Failed to deliver recovery email.'];
            }
        } catch (Exception $e) {
            error_log("Error during forgot request: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'A system error occurred. Please try again.'];
        }
    }

    /**
     * Show OTP Verification View
     */
    public function showVerifyOTP(): void {
        SessionService::init();
        if (empty($_SESSION['reset_email'])) {
            $_SESSION['error_message'] = 'Please enter your email first.';
            header("Location: " . url('/forgot-password'));
            exit;
        }
        $this->renderView('auth/forgot_password_verify', ['email' => $_SESSION['reset_email']]);
    }

    /**
     * Verify Reset OTP (API / AJAX)
     */
    public function verifyOTP(): void {
        $this->jsonResponse($this->handleVerifyOTP());
    }

    private function handleVerifyOTP(): array {
        SessionService::init();
        
        $input = $this->sanitizeInput($_POST);
        $otp = $input['otp'] ?? '';
        $email = $_SESSION['reset_email'] ?? $input['email'] ?? '';
        
        if (empty($otp)) {
            return ['status' => 'error', 'message' => 'Please enter the 6-digit reset code.'];
        }
        
        if (empty($email)) {
            return ['status' => 'error', 'message' => 'Session expired. Please request a new code.'];
        }
        
        $result = $this->passwordResetModel->verifyOTP($email, $otp);
        if (!$result['status']) {
            return ['status' => 'error', 'message' => $result['message']];
        }
        
        $_SESSION['reset_otp_verified'] = true;
        
        return [
            'status' => 'success',
            'message' => 'Reset code verified successfully!',
            'redirect' => url('/forgot-password/reset')
        ];
    }

    /**
     * Show New Password Input View
     */
    public function showResetPassword(): void {
        SessionService::init();
        if (empty($_SESSION['reset_email']) || empty($_SESSION['reset_otp_verified'])) {
            $_SESSION['error_message'] = 'Unauthorized recovery step.';
            header("Location: " . url('/forgot-password'));
            exit;
        }
        $this->renderView('auth/reset_password');
    }

    /**
     * Handle Password Reset (API / AJAX)
     */
    public function resetPassword(): void {
        $this->jsonResponse($this->handleResetPassword());
    }

    private function handleResetPassword(): array {
        SessionService::init();
        
        if (empty($_SESSION['reset_email']) || empty($_SESSION['reset_otp_verified'])) {
            return ['status' => 'error', 'message' => 'Unauthorized operation.'];
        }
        
        $input = $this->sanitizeInput($_POST);
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';
        $email = $_SESSION['reset_email'];
        
        if (empty($password) || empty($confirmPassword)) {
            return ['status' => 'error', 'message' => 'Please fill in all password fields.'];
        }
        
        if ($password !== $confirmPassword) {
            return ['status' => 'error', 'message' => 'Passwords do not match.'];
        }
        
        // Strength Check
        if (strlen($password) < 8 || 
            !preg_match('/[A-Z]/', $password) || 
            !preg_match('/[a-z]/', $password) || 
            !preg_match('/[0-9]/', $password) || 
            !preg_match('/[^A-Za-z0-9]/', $password)) {
            return [
                'status' => 'error', 
                'message' => 'Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
            ];
        }
        
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return ['status' => 'error', 'message' => 'Account not found.'];
        }
        
        try {
            // Update password
            $this->userModel->updatePassword((int)$user['id'], $password);
            
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_otp_verified']);
            
            $_SESSION['success_message'] = 'Your password has been reset successfully! Please sign in.';
            
            return [
                'status' => 'success',
                'message' => 'Password reset successful! Redirecting to login...',
                'redirect' => url('/login')
            ];
            
        } catch (Exception $e) {
            error_log("Error during password reset: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred while resetting your password.'];
        }
    }
}
