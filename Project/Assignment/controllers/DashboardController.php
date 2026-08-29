<?php
/**
 * Dashboard Controller
 */

require_once CONTROLLERS_PATH . '/BaseController.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/Verification.php';
require_once SERVICES_PATH . '/SessionService.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';
require_once MIDDLEWARE_PATH . '/RoleMiddleware.php';

class DashboardController extends BaseController {

    private User $userModel;
    private Verification $verificationModel;

    public function __construct() {
        $this->userModel = new User();
        $this->verificationModel = new Verification();
    }

    /**
     * Route user based on role
     */
    public function index(): void {
        AuthMiddleware::handle();
        
        $userId = SessionService::getUserId();
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            SessionService::logout();
            header("Location: " . url('/login'));
            exit;
        }
        
        if (SessionService::hasRole('Admin')) {
            $this->showAdminDashboard($user);
        } else {
            $this->showUserDashboard($user);
        }
    }

    /**
     * Render User Dashboard
     */
    private function showUserDashboard(array $user): void {
        require_once MODELS_PATH . '/UserFile.php';
        $userFileModel = new UserFile();
        $files = $userFileModel->findByUserId((int)$user['id']);
        $this->renderView('dashboard/user', [
            'user' => $user,
            'files' => $files
        ]);
    }


    /**
     * Render Admin Dashboard
     */
    private function showAdminDashboard(array $user): void {
        $search = $this->sanitizeInput($_GET['search'] ?? '');
        $filterRole = $this->sanitizeInput($_GET['role'] ?? '');
        $filterStatus = $this->sanitizeInput($_GET['status'] ?? '');
        
        $users = $this->userModel->getAllUsers($search, $filterRole, $filterStatus);
        
        // Enrich user list with OTP statuses
        foreach ($users as &$u) {
            $u['otp_status'] = $this->verificationModel->getOTPStatus((int)$u['id']);
        }
        unset($u);
        
        $this->renderView('dashboard/admin', [
            'user' => $user,
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $filterRole,
                'status' => $filterStatus
            ]
        ]);
    }

    /**
     * Toggle User Verification Status (API / AJAX)
     * Guarded: Admin only
     */
    public function toggleStatus(): void {
        RoleMiddleware::handle('Admin', true);
        
        $input = $this->sanitizeInput($_POST);
        $userId = (int)($input['user_id'] ?? 0);
        $status = (int)($input['status'] ?? 0); // 1 = verified, 0 = unverified
        
        if ($userId <= 0) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid User ID.'], 400);
        }
        
        $targetUser = $this->userModel->findById($userId);
        if (!$targetUser) {
            $this->jsonResponse(['status' => 'error', 'message' => 'User not found.'], 404);
        }
        
        // Prevent toggling oneself
        if ($userId === SessionService::getUserId()) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Security check: You cannot toggle your own verification status.'], 400);
        }
        
        try {
            // Update verified state directly
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE users SET is_verified = :is_verified WHERE id = :id");
            $success = $stmt->execute([':is_verified' => $status, ':id' => $userId]);
            
            if ($success) {
                $statusWord = $status === 1 ? 'Verified' : 'Unverified';
                $this->jsonResponse([
                    'status' => 'success',
                    'message' => "User is now successfully set to {$statusWord}."
                ]);
            } else {
                $this->jsonResponse(['status' => 'error', 'message' => 'Failed to update user verification.'], 500);
            }
        } catch (Exception $e) {
            error_log("Error toggling verification: " . $e->getMessage());
            $this->jsonResponse(['status' => 'error', 'message' => 'System error updating status.'], 500);
        }
    }

    /**
     * Update User Role (API / AJAX)
     * Guarded: Admin only
     */
    public function updateRole(): void {
        RoleMiddleware::handle('Admin', true);
        
        $input = $this->sanitizeInput($_POST);
        $userId = (int)($input['user_id'] ?? 0);
        $role = $input['role'] ?? 'User';
        
        if ($userId <= 0) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid User ID.'], 400);
        }
        
        if ($role !== 'Admin' && $role !== 'User') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid role specified.'], 400);
        }
        
        $targetUser = $this->userModel->findById($userId);
        if (!$targetUser) {
            $this->jsonResponse(['status' => 'error', 'message' => 'User not found.'], 404);
        }
        
        // Prevent stripping oneself of Admin role
        if ($userId === SessionService::getUserId() && $role !== 'Admin') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Security check: You cannot strip yourself of the Admin role.'], 400);
        }
        
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("UPDATE users SET role = :role WHERE id = :id");
            $success = $stmt->execute([':role' => $role, ':id' => $userId]);
            
            if ($success) {
                $this->jsonResponse([
                    'status' => 'success',
                    'message' => "User role updated successfully to '{$role}'."
                ]);
            } else {
                $this->jsonResponse(['status' => 'error', 'message' => 'Failed to update user role.'], 500);
            }
        } catch (Exception $e) {
            error_log("Error in updateRole: " . $e->getMessage());
            $this->jsonResponse(['status' => 'error', 'message' => 'System error updating user role.'], 500);
        }
    }

    /**
     * Create a new user (API / AJAX)
     * Guarded: Admin only
     */
    public function createUser(): void {
        RoleMiddleware::handle('Admin', true);
        $this->jsonResponse($this->handleCreateUser());
    }

    private function handleCreateUser(): array {
        $input = $this->sanitizeInput($_POST);
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $role = $input['role'] ?? 'User';
        $password = $input['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            return ['status' => 'error', 'message' => 'All fields (Name, Email, Password) are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Please enter a valid email address.'];
        }

        if ($role !== 'User' && $role !== 'Admin') {
            return ['status' => 'error', 'message' => 'Invalid role specified.'];
        }

        // Check duplicate email
        $existing = $this->userModel->findByEmail($email);
        if ($existing) {
            return ['status' => 'error', 'message' => 'Email address is already in use.'];
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $db = Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO users (name, email, password, role, is_verified) 
                VALUES (:name, :email, :password, :role, 1)
            ");
            $success = $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role' => $role
            ]);

            if ($success) {
                return ['status' => 'success', 'message' => 'User created successfully!', 'redirect' => url('/dashboard')];
            } else {
                return ['status' => 'error', 'message' => 'Failed to insert user.'];
            }
        } catch (Exception $e) {
            error_log("Error in admin createUser: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'A system error occurred.'];
        }
    }

    /**
     * Edit user details (API / AJAX)
     * Guarded: Admin only
     */
    public function editUser(): void {
        RoleMiddleware::handle('Admin', true);
        $this->jsonResponse($this->handleEditUser());
    }

    private function handleEditUser(): array {
        $input = $this->sanitizeInput($_POST);
        $userId = isset($input['user_id']) ? (int)$input['user_id'] : 0;
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $role = $input['role'] ?? 'User';
        $isVerified = isset($input['is_verified']) ? (int)$input['is_verified'] : 0;
        $password = $input['password'] ?? ''; // Optional password update

        if ($userId <= 0 || empty($name) || empty($email)) {
            return ['status' => 'error', 'message' => 'User ID, Name, and Email are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'Please enter a valid email.'];
        }

        $targetUser = $this->userModel->findById($userId);
        if (!$targetUser) {
            return ['status' => 'error', 'message' => 'User not found.'];
        }

        // Prevent self-demotion or self-deactivation
        $currentAdminId = SessionService::getUserId();
        if ($userId === $currentAdminId) {
            if ($role !== 'Admin') {
                return ['status' => 'error', 'message' => 'You cannot remove your own Admin role.'];
            }
            if ($isVerified !== 1) {
                return ['status' => 'error', 'message' => 'You cannot deactivate your own account status.'];
            }
        }

        // Duplicate email check
        if (strtolower($targetUser['email']) !== strtolower($email)) {
            $existing = $this->userModel->findByEmail($email);
            if ($existing) {
                return ['status' => 'error', 'message' => 'Email address is already in use by another user.'];
            }
        }

        try {
            $db = Database::getConnection();
            
            $query = "UPDATE users SET name = :name, email = :email, role = :role, is_verified = :is_verified";
            $params = [
                ':name' => $name,
                ':email' => $email,
                ':role' => $role,
                ':is_verified' => $isVerified,
                ':id' => $userId
            ];

            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $query .= ", password = :password";
                $params[':password'] = $hashed;
            }

            $query .= " WHERE id = :id";
            $stmt = $db->prepare($query);
            $success = $stmt->execute($params);

            if ($success) {
                return ['status' => 'success', 'message' => 'User details updated successfully!', 'redirect' => url('/dashboard')];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update user.'];
            }
        } catch (Exception $e) {
            error_log("Error in editUser: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'A system error occurred.'];
        }
    }

    /**
     * Delete user (API / AJAX)
     * Guarded: Admin only
     */
    public function deleteUser(): void {
        RoleMiddleware::handle('Admin', true);
        $this->jsonResponse($this->handleDeleteUser());
    }

    private function handleDeleteUser(): array {
        $input = $this->sanitizeInput($_POST);
        $userId = isset($input['user_id']) ? (int)$input['user_id'] : 0;

        if ($userId <= 0) {
            return ['status' => 'error', 'message' => 'Invalid User ID specified.'];
        }

        if ($userId === SessionService::getUserId()) {
            return ['status' => 'error', 'message' => 'You cannot delete your own account.'];
        }

        $targetUser = $this->userModel->findById($userId);
        if (!$targetUser) {
            return ['status' => 'error', 'message' => 'User not found.'];
        }

        try {
            // Find all uploaded files for disk cleanup
            require_once MODELS_PATH . '/UserFile.php';
            $userFileModel = new UserFile();
            $files = $userFileModel->findByUserId($userId);

            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $success = $stmt->execute([':id' => $userId]);

            if ($success) {
                // Delete physical files from disk
                foreach ($files as $file) {
                    $diskPath = ROOT_PATH . '/uploads/' . $file['filepath'];
                    if (file_exists($diskPath)) {
                        unlink($diskPath);
                    }
                }
                return ['status' => 'success', 'message' => 'User and associated files deleted successfully!', 'redirect' => url('/dashboard')];
            } else {
                return ['status' => 'error', 'message' => 'Failed to delete user.'];
            }
        } catch (Exception $e) {
            error_log("Error in deleteUser: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'A system error occurred.'];
        }
    }
}
