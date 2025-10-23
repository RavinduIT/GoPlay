<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Platform Earnings Model
 * 
 * Tracks all service fees and platform earnings
 */
class PlatformEarnings extends BaseModel
{
    protected string $table = 'platform_earnings';
    
    protected array $fillable = [
        'payment_id',
        'order_id',
        'booking_id',
        'booking_type',
        'transaction_amount',
        'service_fee_percentage',
        'service_fee_amount',
        'merchant_amount',
        'merchant_id',
        'currency',
        'status',
        'description'
    ];

    protected array $casts = [
        'transaction_amount' => 'float',
        'service_fee_percentage' => 'float',
        'service_fee_amount' => 'float',
        'merchant_amount' => 'float'
    ];

    /**
     * Calculate and record service fee for a payment
     */
    public function recordServiceFee(
        int $paymentId,
        float $amount,
        ?int $orderId = null,
        ?int $bookingId = null,
        ?string $bookingType = null,
        ?int $merchantId = null,
        ?string $description = null
    ): ?int {
        // Get service fee percentage from settings
        $serviceFeePercentage = $this->getServiceFeePercentage();
        
        // Calculate fees
        $serviceFeeAmount = round($amount * ($serviceFeePercentage / 100), 2);
        $merchantAmount = round($amount - $serviceFeeAmount, 2);
        
        // Create earning record
        $data = [
            'payment_id' => $paymentId,
            'order_id' => $orderId,
            'booking_id' => $bookingId,
            'booking_type' => $bookingType,
            'transaction_amount' => $amount,
            'service_fee_percentage' => $serviceFeePercentage,
            'service_fee_amount' => $serviceFeeAmount,
            'merchant_amount' => $merchantAmount,
            'merchant_id' => $merchantId,
            'status' => 'pending',
            'description' => $description ?? "Service fee from transaction"
        ];
        
        return $this->create($data);
    }

    /**
     * Get service fee percentage from settings
     */
    private function getServiceFeePercentage(): float
    {
        $sql = "SELECT value FROM settings WHERE key_name = 'service_fee_percentage' LIMIT 1";
        $result = $this->queryFirst($sql);
        return $result ? (float)$result['value'] : 5.00;
    }

    /**
     * Get total platform earnings
     */
    public function getTotalEarnings(?array $filters = null): float
    {
        $sql = "SELECT COALESCE(SUM(service_fee_amount), 0) as total 
                FROM {$this->table} 
                WHERE status IN ('completed', 'pending')";
        
        $params = [];
        
        if ($filters) {
            if (isset($filters['date_from'])) {
                $sql .= " AND created_at >= ?";
                $params[] = $filters['date_from'];
            }
            if (isset($filters['date_to'])) {
                $sql .= " AND created_at <= ?";
                $params[] = $filters['date_to'];
            }
            if (isset($filters['booking_type'])) {
                $sql .= " AND booking_type = ?";
                $params[] = $filters['booking_type'];
            }
        }
        
        $result = $this->queryFirst($sql, $params);
        return (float)($result['total'] ?? 0);
    }

    /**
     * Get available balance for withdrawal
     */
    public function getAvailableBalance(): float
    {
        // Total completed earnings
        $sql = "SELECT COALESCE(SUM(service_fee_amount), 0) as total 
                FROM {$this->table} 
                WHERE status = 'completed'";
        $result = $this->queryFirst($sql);
        $totalEarnings = (float)($result['total'] ?? 0);
        
        // Total withdrawn amount
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM withdrawal_requests 
                WHERE status IN ('completed', 'processing')";
        $result = $this->queryFirst($sql);
        $totalWithdrawn = (float)($result['total'] ?? 0);
        
        return $totalEarnings - $totalWithdrawn;
    }

    /**
     * Get earnings breakdown by type
     */
    public function getEarningsBreakdown(?array $filters = null): array
    {
        $sql = "SELECT 
                    booking_type,
                    COUNT(*) as transaction_count,
                    COALESCE(SUM(transaction_amount), 0) as total_amount,
                    COALESCE(SUM(service_fee_amount), 0) as total_fees,
                    COALESCE(AVG(service_fee_percentage), 0) as avg_percentage
                FROM {$this->table}
                WHERE status IN ('completed', 'pending')";
        
        $params = [];
        
        if ($filters) {
            if (isset($filters['date_from'])) {
                $sql .= " AND created_at >= ?";
                $params[] = $filters['date_from'];
            }
            if (isset($filters['date_to'])) {
                $sql .= " AND created_at <= ?";
                $params[] = $filters['date_to'];
            }
        }
        
        $sql .= " GROUP BY booking_type";
        
        $statement = $this->query($sql, $params);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get earnings history with pagination
     */
    public function getEarningsHistory(int $page = 1, int $perPage = 20, ?array $filters = null): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT 
                    pe.*,
                    u.first_name as merchant_first_name,
                    u.last_name as merchant_last_name,
                    p.transaction_id,
                    CASE 
                        WHEN pe.booking_type = 'product' THEN o.order_number
                        WHEN pe.booking_type = 'facility' THEN CONCAT('Facility-', pe.booking_id)
                        WHEN pe.booking_type = 'coach' THEN CONCAT('Coach-', pe.booking_id)
                        ELSE 'N/A'
                    END as reference_number
                FROM {$this->table} pe
                LEFT JOIN users u ON pe.merchant_id = u.id
                LEFT JOIN payments p ON pe.payment_id = p.id
                LEFT JOIN orders o ON pe.order_id = o.id
                WHERE 1=1";
        
        $params = [];
        
        if ($filters) {
            if (isset($filters['status'])) {
                $sql .= " AND pe.status = ?";
                $params[] = $filters['status'];
            }
            if (isset($filters['booking_type'])) {
                $sql .= " AND pe.booking_type = ?";
                $params[] = $filters['booking_type'];
            }
            if (isset($filters['date_from'])) {
                $sql .= " AND pe.created_at >= ?";
                $params[] = $filters['date_from'];
            }
            if (isset($filters['date_to'])) {
                $sql .= " AND pe.created_at <= ?";
                $params[] = $filters['date_to'];
            }
            if (isset($filters['search'])) {
                $sql .= " AND (pe.description LIKE ? OR p.transaction_id LIKE ? OR o.order_number LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM ({$sql}) as count_query";
        $totalResult = $this->queryFirst($countSql, $params);
        $total = (int)($totalResult['total'] ?? 0);
        
        // Add pagination
        $sql .= " ORDER BY pe.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $statement = $this->query($sql, $params);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ];
    }

    /**
     * Get daily earnings statistics
     */
    public function getDailyEarnings(int $days = 30): array
    {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as transaction_count,
                    COALESCE(SUM(transaction_amount), 0) as total_amount,
                    COALESCE(SUM(service_fee_amount), 0) as total_fees
                FROM {$this->table}
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                    AND status IN ('completed', 'pending')
                GROUP BY DATE(created_at)
                ORDER BY date DESC";
        
        $statement = $this->query($sql, [$days]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Mark earnings as completed
     */
    public function markAsCompleted(int $id): bool
    {
        return $this->update($id, ['status' => 'completed']);
    }

    /**
     * Mark multiple earnings as withdrawn
     */
    public function markAsWithdrawn(array $ids): bool
    {
        if (empty($ids)) {
            return false;
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "UPDATE {$this->table} 
                SET status = 'withdrawn', updated_at = NOW() 
                WHERE id IN ({$placeholders}) AND status = 'completed'";
        
        $statement = $this->query($sql, $ids);
        return $statement !== false;
    }

    /**
     * Get earnings statistics summary
     */
    public function getStatistics(): array
    {
        // Total earnings
        $totalEarnings = $this->getTotalEarnings();
        
        // Available balance
        $availableBalance = $this->getAvailableBalance();
        
        // This month earnings
        $thisMonthEarnings = $this->getTotalEarnings([
            'date_from' => date('Y-m-01 00:00:00'),
            'date_to' => date('Y-m-t 23:59:59')
        ]);
        
        // Last month earnings
        $lastMonthStart = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $lastMonthEnd = date('Y-m-t 23:59:59', strtotime('last day of last month'));
        $lastMonthEarnings = $this->getTotalEarnings([
            'date_from' => $lastMonthStart,
            'date_to' => $lastMonthEnd
        ]);
        
        // Calculate growth
        $growth = 0;
        if ($lastMonthEarnings > 0) {
            $growth = (($thisMonthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100;
        }
        
        // Transaction count
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status IN ('completed', 'pending')";
        $result = $this->queryFirst($sql);
        $totalTransactions = (int)($result['count'] ?? 0);
        
        // Pending earnings
        $sql = "SELECT COALESCE(SUM(service_fee_amount), 0) as total FROM {$this->table} WHERE status = 'pending'";
        $result = $this->queryFirst($sql);
        $pendingEarnings = (float)($result['total'] ?? 0);
        
        return [
            'total_earnings' => $totalEarnings,
            'available_balance' => $availableBalance,
            'this_month_earnings' => $thisMonthEarnings,
            'last_month_earnings' => $lastMonthEarnings,
            'growth_percentage' => round($growth, 2),
            'total_transactions' => $totalTransactions,
            'pending_earnings' => $pendingEarnings
        ];
    }
}