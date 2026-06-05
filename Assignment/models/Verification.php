<?php
/**
 * Verification Model (Email OTPs lifecycle)
 */

class Verification {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Generate and store a secure 6-digit OTP for a user
     * 
     * @param int $userId
     * @return string Generated OTP
     * @throws Exception
     */
    public function createOTP(int $userId): string {
        // Invalidate older unused OTPs for this user
        $stmtInvalidate = $this->db->prepare("
            UPDATE email_otps 
            SET verified = 1 
            WHERE user_id = :user_id AND verified = 0
        ");
        $stmtInvalidate->execute([':user_id' => $userId]);
        
        // Generate secure 6-digit number
        $otp = (string)random_int(100000, 999999);
        
        // Expiration time: 10 minutes from now
        $expiresAt = date('Y-m-d H:i:s', time() + 600);
        
        // Insert record
        $stmt = $this->db->prepare("
            INSERT INTO email_otps (user_id, otp, expires_at, verified) 
            VALUES (:user_id, :otp, :expires_at, 0)
        ");
        
        $stmt->execute([
            ':user_id' => $userId,
            ':otp' => $otp,
            ':expires_at' => $expiresAt
        ]);
        
        return $otp;
    }

    /**
     * Validate an OTP for a user
     * 
     * @param int $userId
     * @param string $otp
     * @return array [bool 'status', string 'message']
     */
    public function verifyOTP(int $userId, string $otp): array {
        $stmt = $this->db->prepare("
            SELECT * FROM email_otps 
            WHERE user_id = :user_id AND otp = :otp 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':otp' => $otp
        ]);
        
        $record = $stmt->fetch();
        
        if (!$record) {
            return ['status' => false, 'message' => 'Invalid verification code.'];
        }
        
        if ((int)$record['verified'] === 1) {
            return ['status' => false, 'message' => 'This OTP code has already been used.'];
        }
        
        // Expiration check
        $expiryTime = strtotime($record['expires_at']);
        if (time() > $expiryTime) {
            return ['status' => false, 'message' => 'This OTP code has expired (10-minute limit exceeded).'];
        }
        
        // Mark as verified/used
        $stmtMark = $this->db->prepare("UPDATE email_otps SET verified = 1 WHERE id = :id");
        $stmtMark->execute([':id' => $record['id']]);
        
        return ['status' => true, 'message' => 'OTP verified successfully.'];
    }

    /**
     * Retrieve status of recent OTP for Admin tooltip
     */
    public function getOTPStatus(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT expires_at, verified as is_used 
            FROM email_otps 
            WHERE user_id = :user_id 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        $status = $stmt->fetch();
        return $status ?: null;
    }
}
