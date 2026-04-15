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
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $statement = $this->query($sql, [$email]);
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        
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
        return $this->where(['user_type' => $role, 'status' => 'active']);
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
     * Record user login
     */
    public function recordLogin(int $userId,): bool
    {
        try {
            $sql = "INSERT INTO user_logins (user_id, last_login_at, last_login_ip, user_agent) 
                    VALUES (?, NOW(), ?, ?)";
            
            $this->query($sql, [
                $userId,
                $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);

            return true;
        } catch (\Exception $e) {
            error_log("Failed to record login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log user activity
     */
    public function logActivity(int $userId, string $activityType, string $description = ''): bool
    {
        try {
            $sql = "INSERT INTO user_activity_log (user_id, activity_type, activity_description, ip_address, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $this->query($sql, [
                $userId,
                $activityType,
                $description,
                $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            return true;
        } catch (\Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log admin action
     */
    public function logAdminAction(int $adminId, string $actionType, ?int $targetUserId, array $data = []): bool
    {
        try {
            $sql = "INSERT INTO admin_logs (admin_id, action_type, target_user_id, action_data, ip_address, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $this->query($sql, [
                $adminId,
                $actionType,
                $targetUserId,
                json_encode($data),
                $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);

            return true;
        } catch (\Exception $e) {
            error_log("Failed to log admin action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get users with filters and pagination
     */
    public function getUsersWithFilters(array $filters, int $limit = 20, int $offset = 0): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?)";
            $searchParam = "%{$filters['search']}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }

        if (!empty($filters['user_type'])) {
            $where[] = "user_type = ?";
            $params[] = $filters['user_type'];
        }

        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'DESC';

        $sql = "SELECT u.* 
                FROM users u
                {$whereClause}
                ORDER BY {$sortBy} {$sortOrder}
                LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get total users count with filters
     */
    public function getUsersCount(array $filters): int
    {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = "(username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?)";
            $searchParam = "%{$filters['search']}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }

        if (!empty($filters['user_type'])) {
            $where[] = "user_type = ?";
            $params[] = $filters['user_type'];
        }

        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $sql = "SELECT COUNT(*) as total FROM users {$whereClause}";
        
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Get user statistics for admin dashboard
     */
    public function getAdminStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN user_type = 'user' THEN 1 ELSE 0 END) as regular_users,
                    SUM(CASE WHEN user_type = 'coach' THEN 1 ELSE 0 END) as coaches,
                    SUM(CASE WHEN user_type = 'ground_owner' THEN 1 ELSE 0 END) as ground_owners,
                    SUM(CASE WHEN user_type = 'shop_owner' THEN 1 ELSE 0 END) as shop_owners,
                    SUM(CASE WHEN user_type = 'admin' THEN 1 ELSE 0 END) as admins,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
                    SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended_users,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_signups,
                    SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week_signups,
                    SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as month_signups
                FROM users";

        $stmt = $this->query($sql);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Get active users in last 24 hours (may fail if user_logins doesn't exist)
        try {
            $activeSql = "SELECT COUNT(DISTINCT user_id) as active_24h 
                          FROM user_logins 
                          WHERE last_login_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $activeStmt = $this->query($activeSql);
            $activeResult = $activeStmt->fetch(\PDO::FETCH_ASSOC);
            $stats['active_24h'] = $activeResult['active_24h'] ?? 0;
        } catch (\Exception $e) {
            $stats['active_24h'] = 0;
        }

        return $stats;
    }

    /**
     * Get user details with extended info
     */
    public function getUserDetails(int $userId): ?array
    {
        try {
            $sql = "SELECT u.*, 
                           ul.last_login_at,
                           ul.last_login_ip,
                           ul.user_agent,
                           (SELECT COUNT(*) FROM coach_bookings WHERE user_id = u.id) as booking_count,
                           (SELECT COUNT(*) FROM facility_bookings WHERE user_id = u.id) as facility_booking_count,
                           (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
                           (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id AND status = 'completed') as total_spent
                    FROM users u
                    LEFT JOIN user_logins ul ON u.id = ul.user_id AND ul.id = (
                        SELECT id FROM user_logins WHERE user_id = u.id ORDER BY last_login_at DESC LIMIT 1
                    )
                    WHERE u.id = ?";
            
            $stmt = $this->query($sql, [$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($user) {
                unset($user['password_hash']);
                return $user;
            }
            
            return null;
        } catch (\Exception $e) {
            error_log("Failed to get user details: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user's last login
     */
    public function getLastLogin(int $userId): ?array
    {
        try {
            $sql = "SELECT * FROM user_logins WHERE user_id = ? ORDER BY last_login_at DESC LIMIT 1";
            $stmt = $this->query($sql, [$userId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            error_log("Failed to get last login: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user's bookings
     */
    public function getBookings(int $userId): array
    {
        try {
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

            usort($bookings, function($a, $b) {
                return strtotime($b['booking_date'] . ' ' . $b['start_time']) - 
                       strtotime($a['booking_date'] . ' ' . $a['start_time']);
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
            $sql = "SELECT 
                        (SELECT COUNT(*) FROM coach_bookings WHERE user_id = ?) as coach_bookings,
                        (SELECT COUNT(*) FROM facility_bookings WHERE user_id = ?) as facility_bookings,
                        (SELECT COUNT(*) FROM orders WHERE user_id = ?) as total_orders,
                        (SELECT SUM(total_amount) FROM orders WHERE user_id = ? AND status IN ('completed', 'delivered')) as total_spent";
            
            $statement = $this->query($sql, [$userId, $userId, $userId, $userId]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            
            $stats['total_bookings'] = ($result['coach_bookings'] ?? 0) + ($result['facility_bookings'] ?? 0);
            $stats['total_orders'] = $result['total_orders'] ?? 0;
            $stats['total_spent'] = floatval($result['total_spent'] ?? 0);
            
        } catch (\Exception $e) {
            error_log("Statistics query failed for user {$userId}: " . $e->getMessage());
        }
        
        return $stats;
    }
}