<?php
/**
 * Password Reset Model
 */

class PasswordReset {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Generate and store a secure 6-digit password reset OTP
     * 
     * @param string $email
     * @return string Generated OTP
     * @throws Exception
     */
    public function createOTP(string $email): string {
        // Clear previous resets for this email
        $stmtDelete = $this->db->prepare("DELETE FROM password_resets WHERE email = :email");
        $stmtDelete->execute([':email' => $email]);
        
        // Generate secure 6-digit code
        $otp = (string)random_int(100000, 999999);
        
        // Expiration: 10 minutes from now
        $expiresAt = date('Y-m-d H:i:s', time() + 600);
        
        // Insert record
        $stmt = $this->db->prepare("
            INSERT INTO password_resets (email, otp, expires_at) 
            VALUES (:email, :otp, :expires_at)
        ");
        
        $stmt->execute([
            ':email' => $email,
            ':otp' => $otp,
            ':expires_at' => $expiresAt
        ]);
        
        return $otp;
    }

    /**
     * Verify and invalidate a password reset OTP code
     * 
     * @param string $email
     * @param string $otp
     * @return array [bool 'status', string 'message']
     */
    public function verifyOTP(string $email, string $otp): array {
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets 
            WHERE email = :email AND otp = :otp 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([
            ':email' => $email,
            ':otp' => $otp
        ]);
        
        $record = $stmt->fetch();
        
        if (!$record) {
            return ['status' => false, 'message' => 'Invalid recovery code.'];
        }
        
        // Expiration check
        $expiryTime = strtotime($record['expires_at']);
        if (time() > $expiryTime) {
            return ['status' => false, 'message' => 'This recovery code has expired. Please request a new one.'];
        }
        
        // Success: Delete code immediately to prevent reuse (Neutralizes replay attack vectors)
        $stmtDelete = $this->db->prepare("DELETE FROM password_resets WHERE id = :id");
        $stmtDelete->execute([':id' => $record['id']]);
        
        return ['status' => true, 'message' => 'OTP verified successfully.'];
    }
}
