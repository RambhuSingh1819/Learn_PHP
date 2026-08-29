<?php
/**
 * Profile Controller
 */

require_once CONTROLLERS_PATH . '/BaseController.php';
require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/UserFile.php';
require_once SERVICES_PATH . '/SessionService.php';
require_once MIDDLEWARE_PATH . '/AuthMiddleware.php';

class ProfileController extends BaseController {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Update User Profile Name (API / AJAX)
     */
    public function update(): void {
        AuthMiddleware::handle(true);
        $this->jsonResponse($this->handleUpdate());
    }

    private function handleUpdate(): array {
        $input = $this->sanitizeInput($_POST);
        $name = $input['name'] ?? '';
        
        if (empty($name)) {
            return ['status' => 'error', 'message' => 'Profile name cannot be empty.'];
        }
        
        $userId = SessionService::getUserId();
        
        try {
            $success = $this->userModel->updateProfile($userId, $name);
            
            if ($success) {
                $_SESSION['user_name'] = $name;
                return [
                    'status' => 'success',
                    'message' => 'Profile updated successfully!',
                    'name' => $name
                ];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update profile.'];
            }
        } catch (Exception $e) {
            error_log("Error in profile update: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'System error occurred.'];
        }
    }

    /**
     * Change User Password (API / AJAX)
     */
    public function changePassword(): void {
        AuthMiddleware::handle(true);
        $this->jsonResponse($this->handleChangePassword());
    }

    private function handleChangePassword(): array {
        $input = $this->sanitizeInput($_POST);
        
        $currentPassword = $input['current_password'] ?? '';
        $newPassword = $input['new_password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return ['status' => 'error', 'message' => 'All password fields are required.'];
        }
        
        if ($newPassword !== $confirmPassword) {
            return ['status' => 'error', 'message' => 'New passwords do not match.'];
        }
        
        $userId = SessionService::getUserId();
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            return ['status' => 'error', 'message' => 'User account not found.'];
        }
        
        if (!password_verify($currentPassword, $user['password'])) {
            return ['status' => 'error', 'message' => 'Incorrect current password.'];
        }
        
        if ($currentPassword === $newPassword) {
            return ['status' => 'error', 'message' => 'New password must be different from current password.'];
        }
        
        // Strength Check
        if (strlen($newPassword) < 4) {
            return [
                'status' => 'error', 
                'message' => 'New password must be at least 4 characters long.'
            ];
        }
        
        try {
            $success = $this->userModel->updatePassword($userId, $newPassword);
            
            if ($success) {
                return [
                    'status' => 'success',
                    'message' => 'Your password has been changed successfully!'
                ];
            } else {
                return ['status' => 'error', 'message' => 'Failed to update your password.'];
            }
        } catch (Exception $e) {
            error_log("Error changing password: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'A system error occurred.'];
        }
    }

    /**
     * Upload user file (API / AJAX)
     */
    public function uploadFile(): void {
        AuthMiddleware::handle(true);
        $this->jsonResponse($this->handleUploadFile());
    }

    private function handleUploadFile(): array {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errorCode = $_FILES['file']['error'] ?? 'no file';
            return ['status' => 'error', 'message' => 'No file uploaded or upload error occurred. Code: ' . $errorCode];
        }

        $file = $_FILES['file'];
        $originalName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];

        // Validate Size (5MB limit)
        $maxSize = 5 * 1024 * 1024;
        if ($fileSize > $maxSize) {
            return ['status' => 'error', 'message' => 'File exceeds the maximum limit of 5MB.'];
        }

        // Validate Extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip'];
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedExtensions)) {
            return ['status' => 'error', 'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions)];
        }

        // Create uploads folder if not exists
        $uploadDir = ROOT_PATH . '/uploads';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return ['status' => 'error', 'message' => 'Failed to create upload directory. Check permissions.'];
            }
            chmod($uploadDir, 0777);
        }

        // Generate unique filename to prevent path traversal / overwrite / php execution
        $uniqueName = uniqid('uf_', true) . '.' . $fileExt;
        $destination = $uploadDir . '/' . $uniqueName;

        if (move_uploaded_file($fileTmp, $destination)) {
            // Save to database
            $userId = SessionService::getUserId();
            $userFileModel = new UserFile();
            
            $success = $userFileModel->create($userId, $originalName, $uniqueName, $fileExt, $fileSize);
            
            if ($success) {
                return [
                    'status' => 'success',
                    'message' => 'File uploaded successfully!'
                ];
            } else {
                if (file_exists($destination)) {
                    unlink($destination);
                }
                return ['status' => 'error', 'message' => 'Failed to record file in database.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Failed to move uploaded file. Check server permissions.'];
        }
    }

    /**
     * Delete user file (API / AJAX)
     */
    public function deleteFile(): void {
        AuthMiddleware::handle(true);
        $this->jsonResponse($this->handleDeleteFile());
    }

    private function handleDeleteFile(): array {
        $input = $this->sanitizeInput($_POST);
        $fileId = isset($input['file_id']) ? (int)$input['file_id'] : 0;

        if ($fileId <= 0) {
            return ['status' => 'error', 'message' => 'Invalid file ID specified.'];
        }

        $userId = SessionService::getUserId();
        $userFileModel = new UserFile();
        
        $fileRecord = $userFileModel->findById($fileId);
        if (!$fileRecord) {
            return ['status' => 'error', 'message' => 'File record not found.'];
        }

        // Verify ownership
        if ((int)$fileRecord['user_id'] !== $userId) {
            return ['status' => 'error', 'message' => 'Unauthorized action.'];
        }

        // Delete from database
        $dbDeleted = $userFileModel->delete($fileId, $userId);
        if ($dbDeleted) {
            // Remove from disk
            $filePath = ROOT_PATH . '/uploads/' . $fileRecord['filepath'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return [
                'status' => 'success',
                'message' => 'File deleted successfully!'
            ];
        }

        return ['status' => 'error', 'message' => 'Failed to delete file from system.'];
    }
}
