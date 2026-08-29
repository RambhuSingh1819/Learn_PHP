<?php
/**
 * User File Model
 */

class UserFile {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Record a new user file upload
     * 
     * @param int $userId
     * @param string $filename
     * @param string $filepath
     * @param string $filetype
     * @param int $filesize
     * @return bool
     */
    public function create(int $userId, string $filename, string $filepath, string $filetype, int $filesize): bool {
        $stmt = $this->db->prepare("
            INSERT INTO user_files (user_id, filename, filepath, filetype, filesize) 
            VALUES (:user_id, :filename, :filepath, :filetype, :filesize)
        ");
        return $stmt->execute([
            ':user_id' => $userId,
            ':filename' => $filename,
            ':filepath' => $filepath,
            ':filetype' => $filetype,
            ':filesize' => $filesize
        ]);
    }

    /**
     * Find all files uploaded by a user
     * 
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM user_files WHERE user_id = :user_id ORDER BY uploaded_at DESC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Find file details by file ID
     * 
     * @param int $fileId
     * @return array|null
     */
    public function findById(int $fileId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM user_files WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $fileId]);
        $file = $stmt->fetch();
        return $file ?: null;
    }

    /**
     * Delete user file record from database
     * 
     * @param int $fileId
     * @param int $userId
     * @return bool
     */
    public function delete(int $fileId, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM user_files WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            ':id' => $fileId,
            ':user_id' => $userId
        ]);
    }
}
