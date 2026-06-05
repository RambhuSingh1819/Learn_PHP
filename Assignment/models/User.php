<?php
/**
 * User Model
 */

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new user record
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $role
     * @return int Created User ID
     */
    public function create(string $name, string $email, string $password, string $role = 'User'): int {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role, is_verified) 
            VALUES (:name, :email, :password, :role, 0)
        ");
        
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Find user by Email
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Verify user email
     */
    public function verifyEmail(int $id): bool {
        $stmt = $this->db->prepare("UPDATE users SET is_verified = 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Update user profile information
     */
    public function updateProfile(int $id, string $name): bool {
        $stmt = $this->db->prepare("UPDATE users SET name = :name WHERE id = :id");
        return $stmt->execute([
            ':name' => $name,
            ':id' => $id
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(int $id, string $password): bool {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $id
        ]);
    }

    /**
     * Save Remember Me token details
     */
    public function updateRememberToken(int $id, string $token, string $expiresAt): bool {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET remember_token = :token, remember_expires_at = :expires_at 
            WHERE id = :id
        ");
        return $stmt->execute([
            ':token' => $token,
            ':expires_at' => $expiresAt,
            ':id' => $id
        ]);
    }

    /**
     * Clear Remember Me token details
     */
    public function clearRememberToken(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET remember_token = NULL, remember_expires_at = NULL 
            WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Resolve user by remember-me token if not expired
     */
    public function findByRememberToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE remember_token = :token 
              AND remember_expires_at > NOW() 
            LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Fetch users list with custom searching and role/verification filtering for Admin dashboard
     */
    public function getAllUsers(string $search = '', string $role = '', string $status = ''): array {
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (name LIKE :search OR email LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        if (!empty($role)) {
            $query .= " AND role = :role";
            $params[':role'] = $role;
        }
        
        if ($status !== '') {
            $query .= " AND is_verified = :status";
            $params[':status'] = (int)$status;
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
