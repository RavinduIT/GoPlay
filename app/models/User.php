<?php

namespace App\Models;

use Core\BaseModel;

/**
 * User Model
 * 
 * Handles user data and authentication
 */
class User extends BaseModel
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'preferences',
        'status',
        'email_verified_at',
        'last_login_at'
    ];

    protected array $hidden = [
        'password'
    ];

    protected array $casts = [
        'date_of_birth' => 'date',
        'preferences' => 'array',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime'
    ];

    /**
     * Get user by email
     */
    public function getByEmail(string $email): ?array
    {
        return $this->where(['email' => $email])[0] ?? null;
    }

    /**
     * Get active users
     */
    public function getActive(): array
    {
        return $this->where(['status' => 'active']);
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): array
    {
        return $this->where(['role' => $role, 'status' => 'active']);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash password
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, ['last_login_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Get user's bookings
     */
    public function getBookings(int $userId, ?string $type = null): array
    {
        $sql = "SELECT b.* FROM bookings b WHERE b.user_id = ?";
        $params = [$userId];
        
        if ($type) {
            $sql .= " AND b.type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY b.booking_date DESC";
        
        return $this->query($sql, $params);
    }

    /**
     * Get user's orders
     */
    public function getOrders(int $userId): array
    {
        $sql = "SELECT o.* FROM orders o WHERE o.user_id = ? ORDER BY o.created_at DESC";
        return $this->query($sql, [$userId]);
    }

    /**
     * Get user statistics
     */
    public function getStatistics(int $userId): array
    {
        $stats = [];
        
        // Total bookings
        $result = $this->queryFirst("SELECT COUNT(*) as count FROM bookings WHERE user_id = ?", [$userId]);
        $stats['total_bookings'] = $result['count'] ?? 0;
        
        // Total orders
        $result = $this->queryFirst("SELECT COUNT(*) as count FROM orders WHERE user_id = ?", [$userId]);
        $stats['total_orders'] = $result['count'] ?? 0;
        
        // Total spent
        $result = $this->queryFirst("SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND status = 'completed'", [$userId]);
        $stats['total_spent'] = $result['total'] ?? 0;
        
        return $stats;
    }
}