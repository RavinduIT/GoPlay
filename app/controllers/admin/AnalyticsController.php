<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use Core\Database;

/**
 * Analytics Controller
 * 
 * Handles analytics and reporting for admin dashboard
 */
class AnalyticsController extends BaseController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Display analytics dashboard
     */
    public function index(Request $request): Response
    {
        // Check if user is authenticated and is admin
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->redirect('/login');
        }

        $activePage = 'analytics';
        return $this->view('admin/analytics', compact('activePage'));
    }

    /**
     * Get overview statistics
     */
    public function getOverviewStats(Request $request): Response
    {
        try {
            $timeRange = $request->get('range', '30'); // days

            // Total Users Statistics
            $totalUsers = $this->db->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
            $newUsers = $this->db->query(
                "SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)",
                [$timeRange]
            )->fetch()['count'];
            $activeUsers = $this->db->query(
                "SELECT COUNT(DISTINCT user_id) as count FROM (
                    SELECT user_id FROM facility_bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    UNION
                    SELECT user_id FROM coach_bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    UNION
                    SELECT user_id FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ) as active_users",
                [$timeRange, $timeRange, $timeRange]
            )->fetch()['count'];

            // Revenue Statistics
            $totalRevenue = $this->db->query(
                "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed'"
            )->fetch()['total'];
            $periodRevenue = $this->db->query(
                "SELECT COALESCE(SUM(amount), 0) as total FROM payments 
                WHERE status = 'completed' AND processed_at >= DATE_SUB(NOW(), INTERVAL ? DAY)",
                [$timeRange]
            )->fetch()['total'];

            // Booking Statistics
            $totalBookings = $this->db->query(
                "SELECT 
                    (SELECT COUNT(*) FROM facility_bookings) + 
                    (SELECT COUNT(*) FROM coach_bookings) as count"
            )->fetch()['count'];
            $periodBookings = $this->db->query(
                "SELECT 
                    (SELECT COUNT(*) FROM facility_bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)) + 
                    (SELECT COUNT(*) FROM coach_bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)) as count",
                [$timeRange, $timeRange]
            )->fetch()['count'];

            // Product Statistics
            $totalProducts = $this->db->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'")->fetch()['count'];
            $totalOrders = $this->db->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'];
            $periodOrders = $this->db->query(
                "SELECT COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)",
                [$timeRange]
            )->fetch()['count'];

            return $this->json([
                'success' => true,
                'data' => [
                    'users' => [
                        'total' => (int)$totalUsers,
                        'new' => (int)$newUsers,
                        'active' => (int)$activeUsers,
                        'growth' => $totalUsers > 0 ? round(($newUsers / $totalUsers) * 100, 1) : 0
                    ],
                    'revenue' => [
                        'total' => (float)$totalRevenue,
                        'period' => (float)$periodRevenue,
                        'growth' => $totalRevenue > 0 ? round((($periodRevenue - $totalRevenue) / $totalRevenue) * 100, 1) : 0
                    ],
                    'bookings' => [
                        'total' => (int)$totalBookings,
                        'period' => (int)$periodBookings,
                        'growth' => $totalBookings > 0 ? round(($periodBookings / $totalBookings) * 100, 1) : 0
                    ],
                    'products' => [
                        'total' => (int)$totalProducts,
                        'orders' => (int)$totalOrders,
                        'periodOrders' => (int)$periodOrders
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user analytics data
     */
    public function getUserAnalytics(Request $request): Response
    {
        try {
            // User type distribution
            $userDistribution = $this->db->query(
                "SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type"
            )->fetchAll();

            // User registrations over time
            $registrations = $this->db->query(
                "SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM users 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date"
            )->fetchAll();

            // Active users by type
            $activeByType = $this->db->query(
                "SELECT u.user_type, COUNT(DISTINCT u.id) as count
                FROM users u
                LEFT JOIN facility_bookings fb ON u.id = fb.user_id AND fb.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                LEFT JOIN coach_bookings cb ON u.id = cb.user_id AND cb.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                WHERE fb.id IS NOT NULL OR cb.id IS NOT NULL
                GROUP BY u.user_type"
            )->fetchAll();

            return $this->json([
                'success' => true,
                'data' => [
                    'distribution' => $userDistribution,
                    'registrations' => $registrations,
                    'activeByType' => $activeByType
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get revenue analytics data
     */
    public function getRevenueAnalytics(Request $request): Response
    {
        try {
            $days = (int)$request->get('days', 30);

            // Revenue over time
            $revenueOverTime = $this->db->query(
                "SELECT DATE(processed_at) as date, SUM(amount) as revenue
                FROM payments
                WHERE status = 'completed' AND processed_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(processed_at)
                ORDER BY date",
                [$days]
            )->fetchAll();

            // Revenue by source
            $revenueBySource = $this->db->query(
                "SELECT 
                    CASE 
                        WHEN o.order_type = 'product' THEN 'Products'
                        WHEN o.order_type = 'booking' THEN 'Bookings'
                        ELSE 'Other'
                    END as source,
                    SUM(p.amount) as revenue
                FROM payments p
                JOIN orders o ON p.order_id = o.id
                WHERE p.status = 'completed' AND p.processed_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY source",
                [$days]
            )->fetchAll();

            // Top revenue days
            $topDays = $this->db->query(
                "SELECT DATE(processed_at) as date, SUM(amount) as revenue
                FROM payments
                WHERE status = 'completed' AND processed_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(processed_at)
                ORDER BY revenue DESC
                LIMIT 5",
                [$days]
            )->fetchAll();

            return $this->json([
                'success' => true,
                'data' => [
                    'revenueOverTime' => $revenueOverTime,
                    'revenueBySource' => $revenueBySource,
                    'topDays' => $topDays
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get booking analytics data
     */
    public function getBookingAnalytics(Request $request): Response
    {
        try {
            // Ground bookings by sport
            $groundBookingsBySport = $this->db->query(
                "SELECT sc.name as sport, COUNT(*) as count
                FROM facility_bookings fb
                JOIN sports_facilities sf ON fb.facility_id = sf.id
                JOIN sports_categories sc ON sf.sport_category_id = sc.id
                WHERE fb.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY sc.name
                ORDER BY count DESC"
            )->fetchAll();

            // Coach bookings by sport
            $coachBookingsBySport = $this->db->query(
                "SELECT sc.name as sport, COUNT(*) as count
                FROM coach_bookings cb
                JOIN coaches c ON cb.coach_id = c.id
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE cb.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY sc.name
                ORDER BY count DESC"
            )->fetchAll();

            // Booking status distribution
            $bookingStatus = $this->db->query(
                "SELECT status, COUNT(*) as count FROM (
                    SELECT status FROM facility_bookings
                    UNION ALL
                    SELECT status FROM coach_bookings
                ) as all_bookings
                GROUP BY status"
            )->fetchAll();

            // Peak booking hours
            $peakHours = $this->db->query(
                "SELECT HOUR(start_time) as hour, COUNT(*) as count FROM (
                    SELECT start_time FROM facility_bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    UNION ALL
                    SELECT start_time FROM coach_bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ) as all_bookings
                GROUP BY HOUR(start_time)
                ORDER BY count DESC
                LIMIT 5"
            )->fetchAll();

            return $this->json([
                'success' => true,
                'data' => [
                    'groundBookingsBySport' => $groundBookingsBySport,
                    'coachBookingsBySport' => $coachBookingsBySport,
                    'bookingStatus' => $bookingStatus,
                    'peakHours' => $peakHours
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get product analytics data
     */
    public function getProductAnalytics(Request $request): Response
    {
        try {
            // Top selling products
            $topProducts = $this->db->query(
                "SELECT p.name, p.price, SUM(oi.quantity) as total_sold, SUM(oi.total_price) as revenue
                FROM products p
                JOIN order_items oi ON p.id = oi.product_id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT 10"
            )->fetchAll();

            // Products by category
            $productsByCategory = $this->db->query(
                "SELECT c.name as category, COUNT(*) as count
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active'
                GROUP BY c.name"
            )->fetchAll();

            // Low stock products
            $lowStock = $this->db->query(
                "SELECT name, stock_quantity, min_stock_level
                FROM products
                WHERE stock_quantity <= min_stock_level AND status = 'active'
                ORDER BY stock_quantity ASC
                LIMIT 10"
            )->fetchAll();

            // Sales trends
            $salesTrends = $this->db->query(
                "SELECT DATE(o.created_at) as date, COUNT(*) as orders, SUM(o.total_amount) as revenue
                FROM orders o
                WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(o.created_at)
                ORDER BY date"
            )->fetchAll();

            return $this->json([
                'success' => true,
                'data' => [
                    'topProducts' => $topProducts,
                    'productsByCategory' => $productsByCategory,
                    'lowStock' => $lowStock,
                    'salesTrends' => $salesTrends
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export analytics data
     */
    public function exportData(Request $request): Response
    {
        try {
            $type = $request->get('type', 'csv');
            $report = $request->get('report', 'overview');

            // Get data based on report type
            $data = $this->getReportData($report);

            if ($type === 'csv') {
                return $this->exportCSV($data, $report);
            } else if ($type === 'json') {
                return $this->json(['success' => true, 'data' => $data]);
            }

            return $this->json(['success' => false, 'message' => 'Invalid export type'], 400);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getReportData(string $report): array
    {
        switch ($report) {
            case 'users':
                return $this->db->query(
                    "SELECT id, username, email, first_name, last_name, user_type, status, created_at FROM users"
                )->fetchAll();
            case 'bookings':
                return $this->db->query(
                    "SELECT * FROM (
                        SELECT 'Ground' as type, fb.*, u.email as user_email, sf.name as facility_name
                        FROM facility_bookings fb
                        JOIN users u ON fb.user_id = u.id
                        JOIN sports_facilities sf ON fb.facility_id = sf.id
                        UNION ALL
                        SELECT 'Coach' as type, cb.*, u.email as user_email, CONCAT(cu.first_name, ' ', cu.last_name) as facility_name
                        FROM coach_bookings cb
                        JOIN users u ON cb.user_id = u.id
                        JOIN coaches c ON cb.coach_id = c.id
                        JOIN users cu ON c.user_id = cu.id
                    ) as all_bookings
                    ORDER BY created_at DESC"
                )->fetchAll();
            case 'revenue':
                return $this->db->query(
                    "SELECT p.*, o.order_number, u.email as user_email
                    FROM payments p
                    JOIN orders o ON p.order_id = o.id
                    JOIN users u ON o.user_id = u.id
                    WHERE p.status = 'completed'
                    ORDER BY p.processed_at DESC"
                )->fetchAll();
            default:
                return [];
        }
    }

    private function exportCSV(array $data, string $filename): Response
    {
        if (empty($data)) {
            return $this->json(['success' => false, 'message' => 'No data to export'], 400);
        }

        $output = fopen('php://temp', 'w');
        
        // Write headers
        fputcsv($output, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        $response = new Response($csv);
        $response->setHeader('Content-Type', 'text/csv');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
        
        return $response;
    }
}