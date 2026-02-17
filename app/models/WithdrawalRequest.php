<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Withdrawal Request Model
 * 
 * Handles admin withdrawal requests for platform earnings
 */
class WithdrawalRequest extends BaseModel
{
    protected string $table = 'withdrawal_requests';
    
    protected array $fillable = [
        'admin_id',
        'amount',
        'currency',
        'withdrawal_method',
        'bank_name',
        'account_number',
        'account_holder',
        'paypal_email',
        'notes',
        'status',
        'processed_at',
        'processed_by',
        'rejection_reason',
        'transaction_reference'
    ];

    protected array $casts = [
        'amount' => 'float',
        'processed_at' => 'datetime'
    ];

    /**
     * Create new withdrawal request
     */
    public function createRequest(int $adminId, array $data): ?int
    {
        // Validate amount
        $amount = (float)$data['amount'];
        
        // Check minimum withdrawal amount
        $minAmount = $this->getMinWithdrawalAmount();
        if ($amount < $minAmount) {
            throw new \Exception("Minimum withdrawal amount is {$minAmount}");
        }
        
        // Check available balance
        $earningsModel = new PlatformEarnings();
        $availableBalance = $earningsModel->getAvailableBalance();
        
        if ($amount > $availableBalance) {
            throw new \Exception("Insufficient balance. Available: {$availableBalance}");
        }
        
        // Create withdrawal request
        $requestData = [
            'admin_id' => $adminId,
            'amount' => $amount,
            'currency' => $data['currency'] ?? 'LKR',
            'withdrawal_method' => $data['withdrawal_method'],
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'account_holder' => $data['account_holder'] ?? null,
            'paypal_email' => $data['paypal_email'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending'
        ];
        
        return $this->create($requestData);
    }

    /**
     * Get minimum withdrawal amount from settings
     */
    private function getMinWithdrawalAmount(): float
    {
        $sql = "SELECT value FROM settings WHERE key_name = 'min_withdrawal_amount' LIMIT 1";
        $result = $this->queryFirst($sql);
        return $result ? (float)$result['value'] : 1000.00;
    }

    /**
     * Get withdrawal requests with pagination
     */
    public function getRequests(int $page = 1, int $perPage = 20, ?array $filters = null): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT 
                    wr.*,
                    admin.username as admin_username,
                    admin.email as admin_email,
                    processor.username as processed_by_username
                FROM {$this->table} wr
                LEFT JOIN users admin ON wr.admin_id = admin.id
                LEFT JOIN users processor ON wr.processed_by = processor.id
                WHERE 1=1";
        
        $params = [];
        
        if ($filters) {
            if (isset($filters['status'])) {
                $sql .= " AND wr.status = ?";
                $params[] = $filters['status'];
            }
            if (isset($filters['admin_id'])) {
                $sql .= " AND wr.admin_id = ?";
                $params[] = $filters['admin_id'];
            }
            if (isset($filters['date_from'])) {
                $sql .= " AND wr.requested_at >= ?";
                $params[] = $filters['date_from'];
            }
            if (isset($filters['date_to'])) {
                $sql .= " AND wr.requested_at <= ?";
                $params[] = $filters['date_to'];
            }
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM ({$sql}) as count_query";
        $totalResult = $this->queryFirst($countSql, $params);
        $total = (int)($totalResult['total'] ?? 0);
        
        // Add pagination
        $sql .= " ORDER BY wr.requested_at DESC LIMIT ? OFFSET ?";
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
     * Process withdrawal request
     */
    public function processRequest(int $id, int $processedBy, string $transactionReference): bool
    {
        $data = [
            'status' => 'completed',
            'processed_at' => date('Y-m-d H:i:s'),
            'processed_by' => $processedBy,
            'transaction_reference' => $transactionReference
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Reject withdrawal request
     */
    public function rejectRequest(int $id, int $processedBy, string $reason): bool
    {
        $data = [
            'status' => 'rejected',
            'processed_at' => date('Y-m-d H:i:s'),
            'processed_by' => $processedBy,
            'rejection_reason' => $reason
        ];
        
        return $this->update($id, $data);
    }

    /**
     * Cancel withdrawal request
     */
    public function cancelRequest(int $id): bool
    {
        // Only allow cancelling pending requests
        $request = $this->find($id);
        if (!$request || $request['status'] !== 'pending') {
            return false;
        }
        
        return $this->update($id, ['status' => 'cancelled']);
    }

    /**
     * Get withdrawal statistics
     */
    public function getStatistics(): array
    {
        // Total withdrawn
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE status = 'completed'";
        $result = $this->queryFirst($sql);
        $totalWithdrawn = (float)($result['total'] ?? 0);
        
        // Pending withdrawals
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE status IN ('pending', 'processing')";
        $result = $this->queryFirst($sql);
        $pendingAmount = (float)($result['total'] ?? 0);
        
        // This month withdrawals
        $sql = "SELECT COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE status = 'completed' 
                    AND processed_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
        $result = $this->queryFirst($sql);
        $thisMonthWithdrawn = (float)($result['total'] ?? 0);
        
        // Total requests
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->queryFirst($sql);
        $totalRequests = (int)($result['count'] ?? 0);
        
        // Pending requests count
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'pending'";
        $result = $this->queryFirst($sql);
        $pendingRequests = (int)($result['count'] ?? 0);
        
        return [
            'total_withdrawn' => $totalWithdrawn,
            'pending_amount' => $pendingAmount,
            'this_month_withdrawn' => $thisMonthWithdrawn,
            'total_requests' => $totalRequests,
            'pending_requests' => $pendingRequests
        ];
    }
}