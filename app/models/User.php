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
        'username',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'profile_picture',
        'user_type',
        'status'
    ];

    protected array $hidden = [
        'password_hash'
    ];

    protected array $casts = [
        'date_of_birth' => 'date'
    ];

    /**
     * Get user by email
     */
    public function getByEmail(string $email): ?array
    {
        // For authentication, we need the password_hash, so query directly
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $statement = $this->query($sql, [$email]);
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        
        // Don't apply castAttributes here as it would hide password_hash
        return $user ?: null;
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
     * Update last login (disabled - no last_login column in database)
     */
    public function updateLastLogin(int $userId): bool
    {
        // Note: No last_login column exists in users table
        return true;
    }

    /**
     * Get user's bookings
     */
    public function getBookings(int $userId, ?string $type = null): array
    {
        try {
            // Check both coach_bookings and facility_bookings tables
            $bookings = [];
            
            // Get coach bookings
            $sql = "SELECT 'coach' as booking_type, cb.*, c.user_id as coach_user_id, 
                           u.first_name as coach_first_name, u.last_name as coach_last_name,
                           sc.name as sport_name
                    FROM coach_bookings cb 
                    LEFT JOIN coaches c ON cb.coach_id = c.id 
                    LEFT JOIN users u ON c.user_id = u.id
                    LEFT JOIN sports_categories sc ON c.sport_category_id = sc.id
                    WHERE cb.user_id = ? 
                    ORDER BY cb.booking_date DESC, cb.start_time DESC";
            
            $statement = $this->query($sql, [$userId]);
            $coachBookings = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $bookings = array_merge($bookings, $coachBookings);

            // Get facility bookings
            $sql = "SELECT 'facility' as booking_type, fb.*, sf.name as facility_name,
                           sf.address as facility_address, sc.name as sport_name
                    FROM facility_bookings fb 
                    LEFT JOIN sports_facilities sf ON fb.facility_id = sf.id 
                    LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
                    WHERE fb.user_id = ? 
                    ORDER BY fb.booking_date DESC, fb.start_time DESC";
            
            $statement = $this->query($sql, [$userId]);
            $facilityBookings = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $bookings = array_merge($bookings, $facilityBookings);

            // Sort all bookings by date
            usort($bookings, function($a, $b) {
                return strtotime($b['booking_date'] . ' ' . $b['start_time']) - strtotime($a['booking_date'] . ' ' . $a['start_time']);
            });

            return $bookings;
            
        } catch (\Exception $e) {
            error_log("Error fetching bookings: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user's orders
     */
    public function getOrders(int $userId): array
    {
        try {
            $sql = "SELECT o.*, 
                           COUNT(oi.id) as item_count,
                           GROUP_CONCAT(oi.item_name SEPARATOR ', ') as items
                    FROM orders o 
                    LEFT JOIN order_items oi ON o.id = oi.order_id 
                    WHERE o.user_id = ? 
                    GROUP BY o.id 
                    ORDER BY o.created_at DESC";
            
            $statement = $this->query($sql, [$userId]);
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Error fetching orders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user statistics
     */
    public function getStatistics(int $userId): array
    {
        $stats = [
            'total_bookings' => 0,
            'total_orders' => 0,
            'total_spent' => 0
        ];
        
        try {
            // Count coach bookings
            $sql = "SELECT COUNT(*) as count FROM coach_bookings WHERE user_id = ?";
            $statement = $this->query($sql, [$userId]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            $coachBookings = $result['count'] ?? 0;
            
            // Count facility bookings
            $sql = "SELECT COUNT(*) as count FROM facility_bookings WHERE user_id = ?";
            $statement = $this->query($sql, [$userId]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            $facilityBookings = $result['count'] ?? 0;
            
            $stats['total_bookings'] = $coachBookings + $facilityBookings;
            
            // Count orders
            $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
            $statement = $this->query($sql, [$userId]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            $stats['total_orders'] = $result['count'] ?? 0;
            
            // Sum total spent
            $sql = "SELECT SUM(total_amount) as total FROM orders WHERE user_id = ? AND status IN ('completed', 'delivered')";
            $statement = $this->query($sql, [$userId]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            $stats['total_spent'] = floatval($result['total'] ?? 0);
            
        } catch (\Exception $e) {
            error_log("Statistics query failed for user {$userId}: " . $e->getMessage());
            // Return default values on error
        }
        
        return $stats;
    }
}