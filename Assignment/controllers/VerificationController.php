<?php
/**
 * Verification Controller
 */

require_once CONTROLLERS_PATH . '/BaseController.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Verification.php';
require_once SERVICES_PATH . '/EmailService.php';
require_once SERVICES_PATH . '/SessionService.php';

class VerificationController extends BaseController {

    private User $userModel;
    private Verification $verificationModel;

    public function __construct() {
        $this->userModel = new User();
        $this->verificationModel = new Verification();
    }

    /**
     * Show Verification View
     */
    public function showVerify(): void {
        SessionService::init();
        
        if (SessionService::isLoggedIn()) {
            header("Location: " . url('/dashboard'));
            exit;
        }
        
        if (empty($_SESSION['pending_verification_email']) || empty($_SESSION['pending_verification_user_id'])) {
            $_SESSION['error_message'] = 'No pending email verification session found.';
            header("Location: " . url('/login'));
            exit;
        }
        
        $email = $_SESSION['pending_verification_email'];
        $this->renderView('auth/verify_otp', ['email' => $email]);
    }

    /**
     * Verify OTP (API / AJAX)
     */
    public function verify(): void {
        $this->jsonResponse($this->handleVerify());
    }

    private function handleVerify(): array {
        SessionService::init();
        
        $input = $this->sanitizeInput($_POST);
        
        $otp = $input['otp'] ?? '';
        $userId = $_SESSION['pending_verification_user_id'] ?? 0;
        $email = $_SESSION['pending_verification_email'] ?? '';
        
        if (empty($otp)) {
            return ['status' => 'error', 'message' => 'Please enter the 6-digit OTP code.'];
        }
        
        if ($userId <= 0) {
            return ['status' => 'error', 'message' => 'Session expired. Please log in again to request a code.'];
        }
        
        // 1. Validate OTP
        $result = $this->verificationModel->verifyOTP($userId, $otp);
        if (!$result['status']) {
            return ['status' => 'error', 'message' => $result['message']];
        }
        
        try {
            // Promote status
            $this->userModel->verifyEmail($userId);
            
            // Auto-login user
            $user = $this->userModel->findById($userId);
            SessionService::login($user);
            
            // Clean verification session state
            unset($_SESSION['pending_verification_email']);
            unset($_SESSION['pending_verification_user_id']);
            
            return [
                'status' => 'success',
                'message' => 'Email verified successfully! Access granted.',
                'redirect' => url('/dashboard')
            ];
            
        } catch (Exception $e) {
            error_log("Error in verifyOTP: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'System error activating account.'];
        }
    }

    /**
     * Resend OTP Verification Code (API / AJAX)
     */
    public function resend(): void {
        $this->jsonResponse($this->handleResend());
    }

    private function handleResend(): array {
        SessionService::init();
        
        $userId = $_SESSION['pending_verification_user_id'] ?? 0;
        $email = $_SESSION['pending_verification_email'] ?? '';
        
        if ($userId <= 0 || empty($email)) {
            return ['status' => 'error', 'message' => 'Session expired. Please log in to request a code.'];
        }
        
        // Cooldown check
        $currentTime = time();
        if (isset($_SESSION['last_otp_resend_time'])) {
            $secondsSinceLast = $currentTime - $_SESSION['last_otp_resend_time'];
            $cooldown = 60;
            
            if ($secondsSinceLast < $cooldown) {
                $wait = $cooldown - $secondsSinceLast;
                return ['status' => 'error', 'message' => "Please wait {$wait} seconds before requesting another code."];
            }
        }
        
        try {
            $user = $this->userModel->findById($userId);
            if (!$user) {
                return ['status' => 'error', 'message' => 'User not found.'];
            }
            
            // Generate OTP
            $otp = $this->verificationModel->createOTP($userId);
            
            // Send email
            $emailSent = EmailService::sendOTP($email, $otp, $user['name']);
            
            if ($emailSent) {
                $_SESSION['last_otp_resend_time'] = $currentTime;
                return ['status' => 'success', 'message' => 'A fresh verification code has been dispatched.'];
            } else {
                return ['status' => 'error', 'message' => 'Failed to deliver code. Please try again.'];
            }
        } catch (Exception $e) {
            error_log("Error during resending verification: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'An error occurred generating the code.'];
        }
    }
}
