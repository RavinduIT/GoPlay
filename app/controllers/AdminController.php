<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;
use App\Models\ShopOwnerProfile;
use App\Models\GroundOwnerProfile;

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
        $this->startSession();
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

            // Validate state transition — only pending applications can be approved
            if ($application['status'] !== 'pending') {
                return $this->json([
                    'success' => false,
                    'message' => "Cannot approve an application with status '{$application['status']}'. Only pending applications can be approved."
                ], 400);
            }

            $db->beginTransaction();

            try {
                // Update application status
                $updateSql = "UPDATE provider_applications
                              SET status = 'approved',
                                  reviewed_by = ?,
                                  reviewed_at = NOW()
                              WHERE id = ? AND status = 'pending'";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->execute([$_SESSION['user_id'], $id]);

                if ($updateStmt->rowCount() === 0) {
                    $db->rollBack();
                    return $this->json([
                        'success' => false,
                        'message' => 'Application was already processed by another admin.'
                    ], 409);
                }

                // Update user type if user_id exists
                if ($application['user_id']) {
                    $userUpdateSql = "UPDATE users SET user_type = ? WHERE id = ?";
                    $userUpdateStmt = $db->prepare($userUpdateSql);
                    $userUpdateStmt->execute([$application['provider_type'], $application['user_id']]);
                    
                    // Auto-create profile based on provider type
                    $this->createProviderProfile($application);
                }

                $db->commit();
            } catch (\Exception $txErr) {
                $db->rollBack();
                error_log("Approval transaction failed: " . $txErr->getMessage());
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to approve application. Profile creation error: ' . $txErr->getMessage()
                ], 500);
            }

            // Send approval email to applicant (outside transaction — non-critical)
            $emailWarning = '';
            try {
                $emailService = new \App\Services\EmailService();
                $emailService->sendProviderApprovalEmail(
                    $application['email'],
                    $application['first_name'] . ' ' . $application['last_name'],
                    $application['provider_type']
                );
            } catch (\Exception $emailErr) {
                error_log("Approval email failed: " . $emailErr->getMessage());
                $emailWarning = ' Note: confirmation email could not be sent.';
            }

            // Create in-app notification for the user
            try {
                if ($application['user_id']) {
                    $notificationModel = new \App\Models\Notification();
                    $providerLabel = str_replace('_', ' ', ucfirst($application['provider_type']));
                    $notificationModel->createNotification(
                        (int)$application['user_id'],
                        'provider_approved',
<<<<<<< HEAD
                        'Application Approved',
=======
                        'Application Approved! 🎉',
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
                        "Congratulations! Your {$providerLabel} application has been approved. You can now access your provider dashboard and start managing your services.",
                        [
                            'application_id' => $id,
                            'provider_type' => $application['provider_type']
                        ]
                    );
                }
            } catch (\Exception $notifErr) {
                error_log("Provider approval notification failed: " . $notifErr->getMessage());
            }

            return $this->json([
                'success' => true,
                'message' => 'Application approved successfully.' . $emailWarning
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
     * Create provider profile based on application data
     * Throws on failure so the calling transaction can roll back.
     */
    private function createProviderProfile(array $application): void
    {
        $userId = $application['user_id'];
        $providerType = $application['provider_type'];
        
        switch ($providerType) {
            case 'shop_owner':
                $this->createShopOwnerProfile($userId, $application);
                break;
                
            case 'ground_owner':
                $this->createGroundOwnerProfile($userId, $application);
                break;
                
            case 'coach':
                $this->createCoachProfile($userId, $application);
                break;
        }
    }
    
    /**
     * Create shop owner profile from application data
     */
    private function createShopOwnerProfile(int $userId, array $application): void
    {
        $shopOwnerProfile = new ShopOwnerProfile();
        
        // Check if profile already exists
        $existingProfile = $shopOwnerProfile->getByUserId($userId);
        if ($existingProfile) {
            return; // Profile already exists
        }
        
        // Prepare profile data from application
        $profileData = [
            'user_id' => $userId,
            'shop_name' => $application['shop_name'] ?? '',
            'business_name' => $application['shop_name'] ?? '',
            'business_registration_number' => $application['business_registration_number'] ?? '',
            'tax_identification_number' => $application['business_registration_number'] ?? '',
            'business_type' => $application['business_type'] ?? 'sole_proprietor',
            'year_established' => $application['year_established'] ?? null,
            'number_of_employees' => $application['number_of_employees'] ?? 0,
            'business_phone' => $application['phone'] ?? '',
            'business_email' => $application['email'] ?? '',
            'shop_address' => $application['shop_address'] ?? $application['address'] ?? '',
            'shop_city' => $application['shop_city'] ?? $application['city'] ?? '',
            'shop_postal_code' => $application['shop_postal'] ?? $application['postal_code'] ?? '',
            'business_description' => $application['business_description'] ?? '',
            'product_categories' => $application['product_categories'] ?? null,
            'brand_names' => $application['brand_names'] ?? '',
            'website_url' => $application['website_url'] ?? '',
            'social_media' => $application['social_media'] ?? '',
            'business_license_document' => $application['business_registration'] ?? '',
            'tax_document' => $application['tax_document'] ?? '',
            'shop_images' => $application['shop_images'] ?? null,
            'delivery_options' => $application['delivery_options'] ?? null,
            'profile_completion_percentage' => 35 // Base percentage from application
        ];
        
        // Create the profile
        $shopOwnerProfile->create($profileData);
    }
    
    /**
     * Create ground owner profile from application data
     */
    private function createGroundOwnerProfile(int $userId, array $application): void
    {
        $groundOwnerProfile = new GroundOwnerProfile();
        
        // Check if profile already exists
        $existingProfile = $groundOwnerProfile->getByUserId($userId);
        if ($existingProfile) {
            return; // Profile already exists
        }
        
        // Prepare profile data from application
        $profileData = [
            'user_id' => $userId,
            'business_name' => $application['facility_name'] ?? '',
            'business_phone' => $application['phone'] ?? '',
            'business_email' => $application['email'] ?? '',
            'business_address' => $application['facility_address'] ?? '',
            'profile_completion_percentage' => 30
        ];
        
        // Create the profile
        $groundOwnerProfile->create($profileData);
    }

    /**
     * Create coach profile from application data
     */
    private function createCoachProfile(int $userId, array $application): void
    {
        try {
            $db = $this->getDatabase();
            
            // Check if coach profile already exists
            $check = $db->prepare("SELECT id FROM coaches WHERE user_id = ?");
            $check->execute([$userId]);
            if ($check->fetch()) {
                return; // Already exists
            }

            // Find sport_category_id from specialization name
            $sportCategoryId = null;
            $sportName = $application['sport_specialization'] ?? '';
            if (!empty($sportName)) {
                $catStmt = $db->prepare("SELECT id FROM sports_categories WHERE name LIKE ? AND is_active = 1 LIMIT 1");
                $catStmt->execute(['%' . $sportName . '%']);
                $cat = $catStmt->fetch(\PDO::FETCH_ASSOC);
                if ($cat) {
                    $sportCategoryId = (int)$cat['id'];
                }
            }
            // If no match found, use the first active category as fallback
            if ($sportCategoryId === null) {
                $fallback = $db->query("SELECT id FROM sports_categories WHERE is_active = 1 ORDER BY id ASC LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
                $sportCategoryId = $fallback ? (int)$fallback['id'] : 1;
            }

            // Avoid double JSON encoding - specialties may already be a JSON string
            $specialties = $application['specialties'] ?? '[]';
            if (is_string($specialties)) {
                $decoded = json_decode($specialties, true);
                $specialties = $decoded !== null ? json_encode($decoded) : $specialties;
            } else {
                $specialties = json_encode($specialties);
            }

            $stmt = $db->prepare(
                "INSERT INTO coaches (user_id, sport_category_id, experience_years, hourly_rate, bio, specializations, certifications, location, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')"
            );
            $stmt->execute([
                $userId,
                $sportCategoryId,
                (int)($application['experience_years'] ?? 0),
                (float)($application['session_rate'] ?? 0),
                $application['bio'] ?? '',
                $specialties,
                $application['qualifications'] ?? '',
                ($application['city'] ?? '') . ', Sri Lanka'
            ]);
        } catch (\Exception $e) {
            error_log("Error creating coach profile: " . $e->getMessage());
            throw $e; // Re-throw so the approval transaction can roll back
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

            // Fetch application and validate state transition
            $checkStmt = $db->prepare("SELECT * FROM provider_applications WHERE id = ?");
            $checkStmt->execute([$id]);
            $application = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$application) {
                return $this->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            if ($application['status'] !== 'pending') {
                return $this->json([
                    'success' => false,
                    'message' => "Cannot reject an application with status '{$application['status']}'. Only pending applications can be rejected."
                ], 400);
            }

            // Update application status
            $sql = "UPDATE provider_applications
                    SET status = 'rejected',
                        reviewed_by = ?,
                        reviewed_at = NOW(),
                        rejection_reason = ?
                    WHERE id = ? AND status = 'pending'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$_SESSION['user_id'], $reason, $id]);

            // Send rejection email and in-app notification
            $emailWarning = '';
            try {
                $emailService = new \App\Services\EmailService();
                $emailService->sendNotification(
                    $application['email'],
                    $application['first_name'] . ' ' . $application['last_name'],
                    'GoPlay - Application Update',
                    '<h2>Application Update</h2>'
                    . '<p>We have reviewed your provider application and unfortunately we are unable to approve it at this time.</p>'
                    . '<p><strong>Reason:</strong> ' . htmlspecialchars($reason) . '</p>'
                    . '<p>You are welcome to submit a new application after addressing the above feedback.</p>'
                    . '<br><p>Best regards,<br><strong>The GoPlay Team</strong></p>'
                );
            } catch (\Exception $emailErr) {
                error_log("Rejection email failed: " . $emailErr->getMessage());
                $emailWarning = ' Note: notification email could not be sent.';
            }

            // Create in-app notification for the user
            try {
                if (!empty($application['user_id'])) {
                    $notificationModel = new \App\Models\Notification();
                    $providerLabel = str_replace('_', ' ', ucfirst($application['provider_type']));
                    $notificationModel->createNotification(
                        (int)$application['user_id'],
                        'provider_rejected',
                        'Application Not Approved',
                        "Your {$providerLabel} application was not approved. Reason: {$reason}. You can resubmit after addressing the feedback.",
                        [
                            'application_id' => $id,
                            'provider_type' => $application['provider_type'],
                            'reason' => $reason
                        ]
                    );
                }
            } catch (\Exception $notifErr) {
                error_log("Rejection notification failed: " . $notifErr->getMessage());
            }

            return $this->json([
                'success' => true,
                'message' => 'Application rejected.' . $emailWarning
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
     * Dashboard API: Get overview statistics
     */
    public function getStats(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();

            // Users
            $totalUsers = $db->query("SELECT COUNT(*) as c FROM users")->fetch(\PDO::FETCH_ASSOC)['c'];
            $newUsersThisMonth = $db->query("SELECT COUNT(*) as c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch(\PDO::FETCH_ASSOC)['c'];
            $usersGrowth = $totalUsers > 0 ? round(($newUsersThisMonth / $totalUsers) * 100, 1) : 0;

            // Revenue
            $totalRevenue = $db->query("SELECT COALESCE(SUM(amount), 0) as t FROM payments WHERE status = 'completed'")->fetch(\PDO::FETCH_ASSOC)['t'];
            $monthRevenue = $db->query("SELECT COALESCE(SUM(amount), 0) as t FROM payments WHERE status = 'completed' AND processed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch(\PDO::FETCH_ASSOC)['t'];
            $prevMonthRevenue = $db->query("SELECT COALESCE(SUM(amount), 0) as t FROM payments WHERE status = 'completed' AND processed_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND processed_at < DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch(\PDO::FETCH_ASSOC)['t'];
            $revenueGrowth = $prevMonthRevenue > 0 ? round((($monthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1) : 0;

            // Grounds
            $totalGrounds = $db->query("SELECT COUNT(*) as c FROM sports_facilities WHERE status = 'active'")->fetch(\PDO::FETCH_ASSOC)['c'];

            // Coaches
            $totalCoaches = $db->query("SELECT COUNT(*) as c FROM coaches WHERE status = 'active'")->fetch(\PDO::FETCH_ASSOC)['c'];

            return $this->json([
                'success' => true,
                'stats' => [
                    'users' => [
                        'total' => (int)$totalUsers,
                        'label' => "+{$newUsersThisMonth} this month",
                        'growth' => $usersGrowth
                    ],
                    'revenue' => [
                        'total' => (float)$totalRevenue,
                        'label' => "Rs." . number_format($monthRevenue) . " this month",
                        'growth' => $revenueGrowth
                    ],
                    'grounds' => [
                        'total' => (int)$totalGrounds,
                        'label' => 'Active facilities'
                    ],
                    'coaches' => [
                        'total' => (int)$totalCoaches,
                        'label' => 'Active coaches'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Dashboard stats error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to load statistics'], 500);
        }
    }

    /**
     * Dashboard API: Get revenue chart data
     */
    public function getRevenueChart(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();
            $period = (int)($request->query('period') ?? 7);
            if ($period < 1 || $period > 365) $period = 7;

            $stmt = $db->prepare(
                "SELECT DATE(processed_at) as date, COALESCE(SUM(amount), 0) as revenue
                 FROM payments
                 WHERE status = 'completed' AND processed_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                 GROUP BY DATE(processed_at)
                 ORDER BY date ASC"
            );
            $stmt->execute([$period]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $data = [];
            foreach ($rows as $row) {
                $data[] = [
                    'label' => date('M j', strtotime($row['date'])),
                    'revenue' => (float)$row['revenue']
                ];
            }

            // If no data, return zeros for period
            if (empty($data)) {
                for ($i = $period - 1; $i >= 0; $i--) {
                    $data[] = [
                        'label' => date('M j', strtotime("-{$i} days")),
                        'revenue' => 0
                    ];
                }
            }

            return $this->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            error_log("Revenue chart error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to load revenue data'], 500);
        }
    }

    /**
     * Dashboard API: Get recent user registrations
     */
    public function getRecentRegistrations(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();
            $limit = min(20, max(1, (int)($request->query('limit') ?? 5)));

            $stmt = $db->prepare(
                "SELECT id, first_name, last_name, email, user_type, created_at
                 FROM users ORDER BY created_at DESC LIMIT ?"
            );
            $stmt->execute([$limit]);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $typeClasses = [
                'user' => 'user',
                'admin' => 'admin',
                'ground_owner' => 'ground-owner',
                'coach' => 'coach',
                'shop_owner' => 'shop-owner'
            ];

            $data = [];
            foreach ($users as $u) {
                $diff = time() - strtotime($u['created_at']);
                if ($diff < 3600) {
                    $timeAgo = max(1, floor($diff / 60)) . ' min ago';
                } elseif ($diff < 86400) {
                    $timeAgo = floor($diff / 3600) . ' hours ago';
                } else {
                    $timeAgo = floor($diff / 86400) . ' days ago';
                }

                $data[] = [
                    'name' => $u['first_name'] . ' ' . $u['last_name'],
                    'email' => $u['email'],
                    'type' => ucwords(str_replace('_', ' ', $u['user_type'])),
                    'typeClass' => $typeClasses[$u['user_type']] ?? 'user',
                    'timeAgo' => $timeAgo
                ];
            }

            return $this->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            error_log("Recent registrations error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to load registrations'], 500);
        }
    }

    /**
     * Dashboard API: Get recent content additions
     */
    public function getRecentContent(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();
            $limit = min(20, max(1, (int)($_GET['limit'] ?? 5)));

            $content = [];

            // Recent grounds
            $stmt = $db->prepare(
                "SELECT sf.name, sf.created_at, 'Ground' as type
                 FROM sports_facilities sf
                 ORDER BY sf.created_at DESC LIMIT ?"
            );
            $stmt->execute([$limit]);
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $content[] = array_merge($row, ['icon' => 'fa-map-marker-alt', 'typeClass' => 'ground']);
            }

            // Recent news
            $stmt = $db->prepare(
                "SELECT title as name, created_at, 'News' as type
                 FROM news ORDER BY created_at DESC LIMIT ?"
            );
            $stmt->execute([$limit]);
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $content[] = array_merge($row, ['icon' => 'fa-newspaper', 'typeClass' => 'product']);
            }

            // Recent products
            $stmt = $db->prepare(
                "SELECT name, created_at, 'Product' as type
                 FROM products ORDER BY created_at DESC LIMIT ?"
            );
            $stmt->execute([$limit]);
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $content[] = array_merge($row, ['icon' => 'fa-box', 'typeClass' => 'product']);
            }

            // Sort combined by date, take top N
            usort($content, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            $content = array_slice($content, 0, $limit);

            // Add timeAgo
            foreach ($content as &$item) {
                $diff = time() - strtotime($item['created_at']);
                if ($diff < 3600) {
                    $item['timeAgo'] = max(1, floor($diff / 60)) . ' min ago';
                } elseif ($diff < 86400) {
                    $item['timeAgo'] = floor($diff / 3600) . ' hours ago';
                } else {
                    $item['timeAgo'] = floor($diff / 86400) . ' days ago';
                }
            }

            return $this->json(['success' => true, 'data' => $content]);
        } catch (\Exception $e) {
            error_log("Recent content error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to load content'], 500);
        }
    }

    /**
     * Dashboard API: Get user breakdown by type
     */
    public function getUserBreakdown(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();
            $rows = $db->query(
                "SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type"
            )->fetchAll(\PDO::FETCH_ASSOC);

            return $this->json(['success' => true, 'data' => $rows]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to load breakdown'], 500);
        }
    }

    /**
     * Dashboard API: Get admin notifications
     */
    public function getNotifications(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();
            $userId = $_SESSION['user_id'];

            // Get recent notifications for this admin
            $stmt = $db->prepare(
                "SELECT id, type, title, message, is_read, created_at
                 FROM notifications
                 WHERE user_id = ?
                 ORDER BY created_at DESC
                 LIMIT 20"
            );
            $stmt->execute([$userId]);
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get unread count
            $countStmt = $db->prepare(
                "SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = 0"
            );
            $countStmt->execute([$userId]);
            $unreadCount = (int)$countStmt->fetch(\PDO::FETCH_ASSOC)['unread'];

            return $this->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            error_log("Admin getNotifications error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to load notifications'], 500);
        }
    }

    /**
     * Dashboard API: Get admin profile info
     */
    public function getProfile(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();
            $stmt = $db->prepare(
                "SELECT first_name, last_name, profile_picture, user_type FROM users WHERE id = ?"
            );
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                return $this->json(['success' => false, 'message' => 'User not found'], 404);
            }

            return $this->json([
                'success' => true,
                'profile' => [
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'role' => 'Super Admin',
                    'avatar' => $user['profile_picture'] ?? '/public/assets/images/default-avatar.png'
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to load profile'], 500);
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
            header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/');
            exit;
        }
    }

    // =========================================================
    // PAYOUT MANAGEMENT METHODS
    // =========================================================

    /**
     * Render admin payouts page
     */
    public function payoutsPage(Request $request): Response
    {
        $this->startSession();
        $this->requireAdmin();
        return $this->view('admin/payouts');
    }

    /**
     * API: Get payout requests list
     */
    public function getPayouts(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $page = max(1, (int)($request->query('page') ?? 1));
            $status = $request->query('status') ?? 'all';

            $payoutModel = new \App\Models\ShopOwnerPayout();
            $payouts = $payoutModel->getPendingRequests($page, 20, $status);
            $stats = $payoutModel->getPayoutStats();

            return $this->json([
                'success' => true,
                'payouts' => $payouts,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            error_log("Admin getPayouts error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to load payouts'], 500);
        }
    }

    /**
     * API: Approve a payout request
     */
    public function approvePayout(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $data = $request->getJsonBody();
            $payoutId = (int)($data['payout_id'] ?? 0);
            $transactionRef = $data['transaction_reference'] ?? '';

            if (!$payoutId) {
                return $this->json(['success' => false, 'message' => 'Payout ID is required'], 400);
            }

            if (empty($transactionRef)) {
                return $this->json(['success' => false, 'message' => 'Transaction reference is required'], 400);
            }

            $payoutModel = new \App\Models\ShopOwnerPayout();
            $result = $payoutModel->approveRequest($payoutId, $_SESSION['user_id'], $transactionRef);

            return $this->json($result, $result['success'] ? 200 : 400);

        } catch (\Exception $e) {
            error_log("Admin approvePayout error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to approve payout'], 500);
        }
    }

    /**
     * API: Reject a payout request
     */
    public function rejectPayout(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $data = $request->getJsonBody();
            $payoutId = (int)($data['payout_id'] ?? 0);
            $reason = $data['reason'] ?? '';

            if (!$payoutId) {
                return $this->json(['success' => false, 'message' => 'Payout ID is required'], 400);
            }

            if (empty($reason)) {
                return $this->json(['success' => false, 'message' => 'Rejection reason is required'], 400);
            }

            $payoutModel = new \App\Models\ShopOwnerPayout();
            $result = $payoutModel->rejectRequest($payoutId, $_SESSION['user_id'], $reason);

            return $this->json($result, $result['success'] ? 200 : 400);

        } catch (\Exception $e) {
            error_log("Admin rejectPayout error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to reject payout'], 500);
        }
    }


    /**
     * API: Mark notification(s) as read
     */
    public function markNotificationsRead(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $db = $this->getDatabase();
            $userId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            if (!empty($data['id'])) {
                // Mark a single notification as read
                $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
                $stmt->execute([(int)$data['id'], $userId]);
            } else {
                // Mark all as read
                $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
                $stmt->execute([$userId]);
            }

            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            error_log("Admin markNotificationsRead error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to update notifications'], 500);
        }
    }
}