<?php
/**
 * User Model
 * Handles user-related database operations
 */

require_once 'BaseModel.php';

class User extends BaseModel {
    
    protected $table = 'users';
    protected $fillable = [
        'username', 'email', 'password_hash', 'first_name', 'last_name', 
        'phone', 'date_of_birth', 'profile_picture', 'user_type', 'status'
    ];
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        return $this->db->getOne($sql, [$email]);
    }
    
    /**
     * Find user by username
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        return $this->db->getOne($sql, [$username]);
    }
    
    /**
     * Find user by email or username
     */
    public function findByEmailOrUsername($identifier) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? OR username = ?";
        return $this->db->getOne($sql, [$identifier, $identifier]);
    }
    
    /**
     * Create user with hashed password
     */
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return $this->create($data);
    }
    
    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password_hash' => $hashedPassword]);
    }
    
    /**
     * Verify user password
     */
    public function verifyPassword($user, $password) {
        return password_verify($password, $user['password_hash']);
    }
    
    /**
     * Get user with addresses
     */
    public function findWithAddresses($userId) {
        $user = $this->find($userId);
        if ($user) {
            $sql = "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC";
            $user['addresses'] = $this->db->getAll($sql, [$userId]);
        }
        return $user;
    }
    
    /**
     * Get users by type
     */
    public function findByType($userType) {
        return $this->findAll(['user_type' => $userType, 'status' => 'active']);
    }
    
    /**
     * Get active users
     */
    public function getActiveUsers() {
        return $this->findAll(['status' => 'active']);
    }
    
    /**
     * Update user status
     */
    public function updateStatus($userId, $status) {
        return $this->update($userId, ['status' => $status]);
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats() {
        $sql = "
            SELECT 
                user_type,
                COUNT(*) as count,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count
            FROM {$this->table} 
            GROUP BY user_type
        ";
        return $this->db->getAll($sql);
    }
    
    /**
     * Search users
     */
    public function searchUsers($searchTerm, $userType = null) {
        $sql = "
            SELECT * FROM {$this->table} 
            WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR username LIKE ?)
        ";
        $params = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
        
        if ($userType) {
            $sql .= " AND user_type = ?";
            $params[] = $userType;
        }
        
        $sql .= " ORDER BY first_name, last_name";
        
        return $this->db->getAll($sql, $params);
    }
}