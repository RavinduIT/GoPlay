<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use App\Models\User;
use App\Services\EmailService;

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

            $filters = [
                'search' => $search,
                'user_type' => $userType,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ];

            // Get total count
            $total = $this->userModel->getUsersCount($filters);

            // Get users
            $users = $this->userModel->getUsersWithFilters($filters, $limit, $offset);

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
            $stats = $this->userModel->getAdminStatistics();

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
                $this->userModel->logAdminAction(
                    $_SESSION['user_id'],
                    'role_change',
                    $userId,
                    ['new_role' => $data['user_type']]
                );

                // Send email notification if user is upgraded to service provider
                $providerTypes = ['ground_owner', 'coach', 'shop_owner'];
                if (in_array($data['user_type'], $providerTypes)) {
                    try {
                        // Get user details for email
                        $user = $this->userModel->getUserDetails($userId);
                        if ($user) {
                            $emailService = new EmailService();
                            $fullName = $user['first_name'] . ' ' . $user['last_name'];
                            $emailService->sendProviderApprovalEmail(
                                $user['email'],
                                $fullName,
                                $data['user_type']
                            );
                            error_log("Provider approval email sent to: {$user['email']}");
                        }
                    } catch (\Exception $e) {
                        // Log error but don't fail the role update if email fails
                        error_log("Failed to send provider approval email: " . $e->getMessage());
                    }
                }

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
                $this->userModel->logAdminAction(
                    $_SESSION['user_id'],
                    'status_change',
                    $userId,
                    [
                        'new_status' => $data['status'],
                        'reason' => $data['reason'] ?? null
                    ]
                );

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
                $this->userModel->logAdminAction(
                    $_SESSION['user_id'],
                    'user_delete',
                    $userId,
                    []
                );

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
                $this->userModel->logAdminAction(
                    $_SESSION['user_id'],
                    'password_reset',
                    $userId,
                    []
                );

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
}