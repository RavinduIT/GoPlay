<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;

class AdminController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function dashboard(Request $request): Response
    {
        // Check if user is authenticated and is admin
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->redirect('/login');
        }

        return $this->view('admin/dashboard');
    }

    /**
     * Show provider applications page
     */
    public function providerApplications(Request $request): Response
    {
        $this->startSession();
        $this->requireAdmin();
        return $this->view('admin/provider-applications');
    }

    /**
     * Get applications list with filters
     */
    public function getApplicationsList(Request $request): Response
    {
        $this->startSession();

        // Check admin authentication first
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $page = (int)($request->query('page') ?? 1);
        $limit = (int)($request->query('limit') ?? 10);
        $status = $request->query('status') ?? '';
        $type = $request->query('type') ?? '';
        $search = $request->query('search') ?? '';

        $offset = ($page - 1) * $limit;

        // Build query
        $where = [];
        $params = [];

        if ($status) {
            $where[] = "status = ?";
            $params[] = $status;
        }

        if ($type) {
            $where[] = "provider_type = ?";
            $params[] = $type;
        }

        if ($search) {
            $where[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        try {
            $db = $this->getDatabase();

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM provider_applications {$whereClause}";
            $countStmt = $db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get applications
            $sql = "SELECT * FROM provider_applications {$whereClause}
                    ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $applications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $this->json([
                'success' => true,
                'applications' => $applications,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_items' => $total,
                    'per_page' => $limit
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Error getting applications list: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to load applications'
            ], 500);
        }
    }

    /**
     * Get applications statistics
     */
    public function getApplicationsStatistics(Request $request): Response
    {
        $this->startSession();

        // Check admin authentication and return JSON error
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $db = $this->getDatabase();

            $sql = "SELECT
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN status = 'approved' AND DATE(reviewed_at) = CURDATE() THEN 1 ELSE 0 END) as approved_today
                    FROM provider_applications";

            $stmt = $db->query($sql);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $this->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            error_log("Error getting applications statistics: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Get application details
     */
    public function getApplicationDetails(Request $request, int $id): Response
    {
        $this->startSession();

        // Check admin authentication and return JSON error
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $db = $this->getDatabase();
            $sql = "SELECT * FROM provider_applications WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            $application = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$application) {
                return $this->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'application' => $application
            ]);
        } catch (\Exception $e) {
            error_log("Error getting application details: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to load application details'
            ], 500);
        }
    }

    /**
     * Approve application
     */
    public function approveApplication(Request $request, int $id): Response
    {
        $this->startSession();

        // Check admin authentication and return JSON error
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $db = $this->getDatabase();

            // Get application
            $sql = "SELECT * FROM provider_applications WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            $application = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$application) {
                return $this->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Update application status
            $updateSql = "UPDATE provider_applications
                          SET status = 'approved',
                              reviewed_by = ?,
                              reviewed_at = NOW()
                          WHERE id = ?";
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->execute([$_SESSION['user_id'], $id]);

            // Update user type if user_id exists
            if ($application['user_id']) {
                $userUpdateSql = "UPDATE users SET user_type = ? WHERE id = ?";
                $userUpdateStmt = $db->prepare($userUpdateSql);
                $userUpdateStmt->execute([$application['provider_type'], $application['user_id']]);
            }

            // TODO: Send approval email to applicant

            return $this->json([
                'success' => true,
                'message' => 'Application approved successfully'
            ]);
        } catch (\Exception $e) {
            error_log("Error approving application: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to approve application'
            ], 500);
        }
    }

    /**
     * Reject application
     */
    public function rejectApplication(Request $request, int $id): Response
    {
        $this->startSession();

        // Check admin authentication and return JSON error
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        try {
            $data = $request->getJsonBody();
            $reason = $data['reason'] ?? '';

            if (empty($reason)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Rejection reason is required'
                ], 400);
            }

            $db = $this->getDatabase();

            // Update application status
            $sql = "UPDATE provider_applications
                    SET status = 'rejected',
                        reviewed_by = ?,
                        reviewed_at = NOW(),
                        rejection_reason = ?
                    WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$_SESSION['user_id'], $reason, $id]);

            // TODO: Send rejection email to applicant

            return $this->json([
                'success' => true,
                'message' => 'Application rejected'
            ]);
        } catch (\Exception $e) {
            error_log("Error rejecting application: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to reject application'
            ], 500);
        }
    }

    /**
     * Get database connection
     */
    private function getDatabase(): \PDO
    {
        return \Core\Database::getInstance()->getConnection();
    }

    /**
     * Require admin authentication
     */
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: /');
            exit;
        }
    }
}