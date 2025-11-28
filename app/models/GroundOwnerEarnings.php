<?php

namespace App\Models;

use PDO;
use PDOException;

class GroundOwnerEarnings {
    private $conn;
    private $table = 'ground_owner_earnings';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get total earnings overview for owner
    public function getEarningsOverview($ownerId) {
        try {
            // Total revenue (all time) - using 'amount' column and calculating commission
            $totalQuery = "SELECT
                SUM(amount) as total_revenue,
                SUM(amount * 0.90) as total_net_earnings,
                SUM(amount * 0.10) as total_commission,
                COUNT(*) as total_bookings
            FROM {$this->table} WHERE owner_id = ?";

            $stmt = $this->conn->prepare($totalQuery);
            $stmt->execute([$ownerId]);
            $totals = $stmt->fetch(PDO::FETCH_ASSOC);

            // This month earnings
            $monthQuery = "SELECT
                SUM(amount) as month_revenue,
                SUM(amount * 0.90) as month_net_earnings,
                COUNT(*) as month_bookings
            FROM {$this->table}
            WHERE owner_id = ?
            AND MONTH(earning_date) = MONTH(CURRENT_DATE)
            AND YEAR(earning_date) = YEAR(CURRENT_DATE)";

            $stmt = $this->conn->prepare($monthQuery);
            $stmt->execute([$ownerId]);
            $monthly = $stmt->fetch(PDO::FETCH_ASSOC);

            // Pending payments
            $pendingQuery = "SELECT SUM(amount * 0.90) as pending_amount
            FROM {$this->table}
            WHERE owner_id = ? AND payment_status = 'pending'";

            $stmt = $this->conn->prepare($pendingQuery);
            $stmt->execute([$ownerId]);
            $pending = $stmt->fetch(PDO::FETCH_ASSOC);

            // Average per booking
            $avgPerBooking = $totals['total_bookings'] > 0
                ? $totals['total_revenue'] / $totals['total_bookings']
                : 0;

            return [
                'totalRevenue' => floatval($totals['total_revenue'] ?? 0),
                'monthlyEarnings' => floatval($monthly['month_net_earnings'] ?? 0),
                'averageBooking' => round($avgPerBooking, 2),
                'pendingPayments' => floatval($pending['pending_amount'] ?? 0),
                'totalBookings' => intval($totals['total_bookings'] ?? 0),
                'monthBookings' => intval($monthly['month_bookings'] ?? 0),
                'revenueChange' => 0,
                'monthlyChange' => 0,
                'avgChange' => 0,
                'pendingCount' => intval($pending['pending_amount'] > 0 ? 2 : 0)
            ];
        } catch(PDOException $e) {
            error_log("Error in getEarningsOverview: " . $e->getMessage());
            return null;
        }
    }

    // Get daily revenue trends for charts
    public function getDailyTrends($ownerId, $days = 30) {
        try {
            $query = "SELECT
                DATE(earning_date) as date,
                SUM(amount) as revenue,
                SUM(amount * 0.90) as net_earnings,
                COUNT(*) as bookings
            FROM {$this->table}
            WHERE owner_id = ?
            AND earning_date >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
            GROUP BY DATE(earning_date)
            ORDER BY date ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$ownerId, $days]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getDailyTrends: " . $e->getMessage());
            return [];
        }
    }

    // Get earnings by facility (ground performance)
    public function getEarningsByFacility($ownerId) {
        try {
            $query = "SELECT
                sf.id,
                sf.name,
                sf.city,
                COUNT(e.id) as total_bookings,
                SUM(e.amount) as total_revenue,
                SUM(e.amount * 0.90) as net_earnings,
                AVG(e.amount) as avg_revenue
            FROM {$this->table} e
            JOIN sports_facilities sf ON e.facility_id = sf.id
            WHERE e.owner_id = ?
            GROUP BY sf.id, sf.name, sf.city
            ORDER BY total_revenue DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$ownerId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getEarningsByFacility: " . $e->getMessage());
            return [];
        }
    }

    // Get recent transactions
    public function getRecentTransactions($ownerId, $limit = 10) {
        try {
            $query = "SELECT
                e.id,
                e.earning_date as date,
                sf.name as ground,
                'Walk-in Customer' as customer,
                e.amount,
                (e.amount * 0.10) as fee,
                (e.amount * 0.90) as net,
                CASE
                    WHEN e.payment_status = 'paid' THEN 'completed'
                    WHEN e.payment_status = 'pending' THEN 'pending'
                    ELSE 'failed'
                END as status,
                e.created_at
            FROM {$this->table} e
            JOIN sports_facilities sf ON e.facility_id = sf.id
            WHERE e.owner_id = ?
            ORDER BY e.created_at DESC
            LIMIT ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$ownerId, $limit]);

            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convert to proper format
            foreach ($transactions as &$transaction) {
                $transaction['amount'] = floatval($transaction['amount']);
                $transaction['fee'] = floatval($transaction['fee']);
                $transaction['net'] = floatval($transaction['net']);
            }

            return $transactions;
        } catch(PDOException $e) {
            error_log("Error in getRecentTransactions: " . $e->getMessage());
            return [];
        }
    }

    // Get earnings breakdown by period
    public function getEarningsBreakdown($ownerId, $period = 'daily') {
        try {
            $groupBy = match($period) {
                'weekly' => 'YEARWEEK(earning_date)',
                'monthly' => 'DATE_FORMAT(earning_date, "%Y-%m")',
                default => 'DATE(earning_date)'
            };

            $dateFormat = match($period) {
                'weekly' => 'CONCAT(YEAR(earning_date), "-W", WEEK(earning_date))',
                'monthly' => 'DATE_FORMAT(earning_date, "%Y-%m")',
                default => 'DATE(earning_date)'
            };

            $query = "SELECT
                {$dateFormat} as period,
                SUM(amount) as revenue,
                SUM(amount * 0.90) as net_earnings,
                COUNT(*) as bookings
            FROM {$this->table}
            WHERE owner_id = ?
            AND earning_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            GROUP BY {$groupBy}
            ORDER BY earning_date DESC
            LIMIT 10";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$ownerId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getEarningsBreakdown: " . $e->getMessage());
            return [];
        }
    }

    // Get payment analytics
    public function getPaymentAnalytics($ownerId) {
        try {
            $query = "SELECT
                COUNT(*) as total_transactions,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as completed_amount,
                SUM(CASE WHEN payment_status = 'pending' THEN amount ELSE 0 END) as pending_amount
            FROM {$this->table}
            WHERE owner_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$ownerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $total = intval($result['total_transactions']);
            $successRate = $total > 0 ? ($result['completed'] / $total) * 100 : 0;

            return [
                'total_transactions' => $total,
                'completed' => intval($result['completed']),
                'pending' => intval($result['pending']),
                'success_rate' => round($successRate, 1),
                'completed_amount' => floatval($result['completed_amount']),
                'pending_amount' => floatval($result['pending_amount'])
            ];
        } catch(PDOException $e) {
            error_log("Error in getPaymentAnalytics: " . $e->getMessage());
            return null;
        }
    }

    // Get all earnings data in one call (for main API endpoint)
    public function getAllEarningsData($ownerId) {
        return [
            'overview' => $this->getEarningsOverview($ownerId),
            'daily_trends' => $this->getDailyTrends($ownerId, 30),
            'ground_performance' => $this->getEarningsByFacility($ownerId),
            'recent_transactions' => $this->getRecentTransactions($ownerId, 10),
            'earnings_breakdown' => $this->getEarningsBreakdown($ownerId, 'daily'),
            'payment_analytics' => $this->getPaymentAnalytics($ownerId)
        ];
    }
}
