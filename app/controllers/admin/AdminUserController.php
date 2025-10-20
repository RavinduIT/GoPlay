<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use App\Models\User;

/**
 * Admin User Controller
 * 
 * Handles user management for admin
 */
class AdminUserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Check if user is admin
     */
    private function requireAdmin(): array
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: /login');
            exit();
        }
        return $_SESSION;
    }

    /**
     * Display users management page
     */
    public function index(Request $request): Response
    {
        $this->requireAdmin();
        
        return $this->view('admin/users', [
            'title' => 'User Management - GoPlay Admin',
            'additionalCSS' => [
                '/public/css/pages/admin-users.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
            ],
            'additionalJS' => [
                '/public/js/pages/admin-users.js'
            ]
        ]);
    }

    /**
     * Get all users with filters (API)
     */
    public function getUsers(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $page = (int)($request->getQuery('page') ?? 1);
            $limit = (int)($request->getQuery('limit') ?? 20);
            $search = $request->getQuery('search') ?? '';
            $userType = $request->getQuery('user_type') ?? '';
            $status = $request->getQuery('status') ?? '';
            $sortBy = $request->getQuery('sort_by') ?? 'created_at';
            $sortOrder = $request->getQuery('sort_order') ?? 'DESC';

            $offset = ($page - 1) * $limit;

            // Build query
            $where = [];
            $params = [];

            if ($search) {
                $where[] = "(username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR phone LIKE ?)";
                $searchParam = "%{$search}%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
            }

            if ($userType) {
                $where[] = "user_type = ?";
                $params[] = $userType;
            }

            if ($status) {
                $where[] = "status = ?";
                $params[] = $status;
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM users {$whereClause}";
            $countStmt = $this->userModel->query($countSql, $params);
            $totalResult = $countStmt->fetch(\PDO::FETCH_ASSOC);
            $total = $totalResult['total'];

            // Get users with last login info
            $sql = "SELECT u.*, 
                           ul.last_login_at,
                           ul.last_login_ip,
                           (SELECT COUNT(*) FROM coach_bookings WHERE user_id = u.id) as booking_count,
                           (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count
                    FROM users u
                    LEFT JOIN user_logins ul ON u.id = ul.user_id AND ul.id = (
                        SELECT id FROM user_logins WHERE user_id = u.id ORDER BY last_login_at DESC LIMIT 1
                    )
                    {$whereClause}
                    ORDER BY {$sortBy} {$sortOrder}
                    LIMIT {$limit} OFFSET {$offset}";

            $stmt = $this->userModel->query($sql, $params);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Remove password hash from response
            foreach ($users as &$user) {
                unset($user['password_hash']);
            }

            return $this->json([
                'success' => true,
                'users' => $users,
                'pagination' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => ceil($total / $limit)
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics (API)
     */
    public function getStatistics(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
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

            $stmt = $this->userModel->query($sql);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Get active users in last 24 hours
            $activeSql = "SELECT COUNT(DISTINCT user_id) as active_24h 
                          FROM user_logins 
                          WHERE last_login_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $activeStmt = $this->userModel->query($activeSql);
            $activeResult = $activeStmt->fetch(\PDO::FETCH_ASSOC);
            $stats['active_24h'] = $activeResult['active_24h'];

            return $this->json([
                'success' => true,
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single user details (API)
     */
    public function getUser(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = (int)$request->getParam('id');
            
            if (!$userId) {
                return $this->json(['error' => 'Invalid user ID'], 400);
            }

            $user = $this->userModel->getUserDetails($userId);

            if (!$user) {
                return $this->json(['error' => 'User not found'], 404);
            }

            return $this->json([
                'success' => true,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user role (API)
     */
    public function updateRole(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = (int)$request->getParam('id');
            $data = $request->getJsonBody();

            if (!$userId) {
                return $this->json(['error' => 'Invalid user ID'], 400);
            }

            if (!isset($data['user_type'])) {
                return $this->json(['error' => 'User type is required'], 400);
            }

            $validTypes = ['user', 'admin', 'ground_owner', 'coach', 'shop_owner'];
            if (!in_array($data['user_type'], $validTypes)) {
                return $this->json(['error' => 'Invalid user type'], 400);
            }

            // Don't allow changing own role
            if ($userId == $_SESSION['user_id']) {
                return $this->json(['error' => 'Cannot change your own role'], 403);
            }

            $success = $this->userModel->update($userId, [
                'user_type' => $data['user_type']
            ]);

            if ($success) {
                // Log the action
                $this->logAdminAction('role_change', $userId, [
                    'new_role' => $data['user_type'],
                    'admin_id' => $_SESSION['user_id']
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'User role updated successfully'
                ]);
            } else {
                return $this->json(['error' => 'Failed to update user role'], 500);
            }

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user status (API)
     */
    public function updateStatus(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = (int)$request->getParam('id');
            $data = $request->getJsonBody();

            if (!$userId) {
                return $this->json(['error' => 'Invalid user ID'], 400);
            }

            if (!isset($data['status'])) {
                return $this->json(['error' => 'Status is required'], 400);
            }

            $validStatuses = ['active', 'inactive', 'suspended'];
            if (!in_array($data['status'], $validStatuses)) {
                return $this->json(['error' => 'Invalid status'], 400);
            }

            // Don't allow changing own status
            if ($userId == $_SESSION['user_id']) {
                return $this->json(['error' => 'Cannot change your own status'], 403);
            }

            $success = $this->userModel->update($userId, [
                'status' => $data['status']
            ]);

            if ($success) {
                // Log the action
                $this->logAdminAction('status_change', $userId, [
                    'new_status' => $data['status'],
                    'reason' => $data['reason'] ?? null,
                    'admin_id' => $_SESSION['user_id']
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'User status updated successfully'
                ]);
            } else {
                return $this->json(['error' => 'Failed to update user status'], 500);
            }

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user (API)
     */
    public function deleteUser(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = (int)$request->getParam('id');

            if (!$userId) {
                return $this->json(['error' => 'Invalid user ID'], 400);
            }

            // Don't allow deleting own account
            if ($userId == $_SESSION['user_id']) {
                return $this->json(['error' => 'Cannot delete your own account'], 403);
            }

            $success = $this->userModel->delete($userId);

            if ($success) {
                // Log the action
                $this->logAdminAction('user_delete', $userId, [
                    'admin_id' => $_SESSION['user_id']
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                return $this->json(['error' => 'Failed to delete user'], 500);
            }

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password (API)
     */
    public function resetPassword(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = (int)$request->getParam('id');
            $data = $request->getJsonBody();

            if (!$userId) {
                return $this->json(['error' => 'Invalid user ID'], 400);
            }

            if (!isset($data['new_password']) || strlen($data['new_password']) < 8) {
                return $this->json(['error' => 'Password must be at least 8 characters'], 400);
            }

            $hashedPassword = $this->userModel->hashPassword($data['new_password']);
            $success = $this->userModel->update($userId, [
                'password_hash' => $hashedPassword
            ]);

            if ($success) {
                // Log the action
                $this->logAdminAction('password_reset', $userId, [
                    'admin_id' => $_SESSION['user_id']
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'Password reset successfully'
                ]);
            } else {
                return $this->json(['error' => 'Failed to reset password'], 500);
            }

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log admin action
     */
    private function logAdminAction(string $action, int $targetUserId, array $data = []): void
    {
        try {
            $sql = "INSERT INTO admin_logs (admin_id, action_type, target_user_id, action_data, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $this->userModel->query($sql, [
                $data['admin_id'] ?? $_SESSION['user_id'],
                $action,
                $targetUserId,
                json_encode($data)
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the main action
            error_log("Failed to log admin action: " . $e->getMessage());
        }
    }
}