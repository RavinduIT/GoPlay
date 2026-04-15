<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Shop Owner Payout Model
 * 
 * Handles withdrawal requests from shop owners.
 * All payouts require admin approval — no auto-approval.
 */
class ShopOwnerPayout extends BaseModel
{
    protected string $table = 'shop_owner_payouts';
    
    protected array $fillable = [
        'shop_owner_id', 'amount', 'currency', 'status',
        'payout_method', 'bank_name', 'account_number', 
        'account_holder', 'branch_name',
        'admin_notes', 'rejection_reason',
        'reviewed_by', 'reviewed_at',
        'transaction_reference', 'completed_at'
    ];

    protected array $casts = [
        'amount' => 'float'
    ];

    /**
     * Create a new payout request.
     * Validates balance threshold and prevents duplicate pending requests.
     */
    public function createRequest(int $shopOwnerId, array $payoutDetails): array
    {
        $balanceModel = new ShopOwnerBalance();
        $eligibility = $balanceModel->canWithdraw($shopOwnerId);
        
        if (!$eligibility['can_withdraw']) {
            return [
                'success' => false,
                'message' => $eligibility['message']
            ];
        }
        
        $amount = (float)($payoutDetails['amount'] ?? $eligibility['available_balance']);
        
        // Validate amount
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Invalid withdrawal amount'];
        }
        
        if ($amount > $eligibility['available_balance']) {
            return ['success' => false, 'message' => 'Withdrawal amount exceeds available balance'];
        }
        
        if ($amount < $eligibility['min_amount']) {
            return [
                'success' => false, 
                'message' => 'Minimum payout amount is LKR ' . number_format($eligibility['min_amount'], 2)
            ];
        }
        
        // Validate bank details
        if (empty($payoutDetails['bank_name']) || empty($payoutDetails['account_number']) || empty($payoutDetails['account_holder'])) {
            return ['success' => false, 'message' => 'Bank details are required. Please update your payout details first.'];
        }
        
        // Create request
        $sql = "INSERT INTO {$this->table} 
                (shop_owner_id, amount, currency, status, payout_method, 
                 bank_name, account_number, account_holder, branch_name)
                VALUES (?, ?, 'LKR', 'pending', 'bank_transfer', ?, ?, ?, ?)";
        
        $this->query($sql, [
            $shopOwnerId,
            $amount,
            $payoutDetails['bank_name'],
            $payoutDetails['account_number'],
            $payoutDetails['account_holder'],
            $payoutDetails['branch_name'] ?? ''
        ]);
        
        $payoutId = (int)$this->db->lastInsertId();
        
        // Create notification for admins
        $this->notifyAdminsOfPayoutRequest($shopOwnerId, $payoutId, $amount);
        
        error_log("Payout request #{$payoutId} created by shop owner {$shopOwnerId} for LKR {$amount}");
        
        return [
            'success' => true,
            'message' => 'Payout request submitted successfully. It will be reviewed by administration.',
            'payout_id' => $payoutId
        ];
    }

    /**
     * Approve a payout request (admin action).
     * Debits the shop owner's balance.
     */
    public function approveRequest(int $payoutId, int $adminId, string $transactionRef): array
    {
        $payout = $this->find($payoutId);
        
        if (!$payout) {
            return ['success' => false, 'message' => 'Payout request not found'];
        }
        
        if ($payout['status'] !== 'pending') {
            return ['success' => false, 'message' => 'This payout has already been ' . $payout['status']];
        }
        
        // Debit the shop owner's balance
        $balanceModel = new ShopOwnerBalance();
        $debited = $balanceModel->debitWithdrawal($payout['shop_owner_id'], $payoutId, $payout['amount']);
        
        if (!$debited) {
            return ['success' => false, 'message' => 'Insufficient balance to process payout'];
        }
        
        // Update payout status
        $sql = "UPDATE {$this->table} 
                SET status = 'completed', 
                    reviewed_by = ?, 
                    reviewed_at = NOW(), 
                    transaction_reference = ?,
                    completed_at = NOW()
                WHERE id = ?";
        $this->query($sql, [$adminId, $transactionRef, $payoutId]);
        
        // Notify shop owner
        $this->notifyShopOwner($payout['shop_owner_id'], 'payout_approved',
            'Payout Approved',
            'Your payout request for LKR ' . number_format($payout['amount'], 2) . ' has been approved. Transaction reference: ' . $transactionRef,
            ['payout_id' => $payoutId, 'amount' => $payout['amount'], 'reference' => $transactionRef]
        );
        
        error_log("Payout #{$payoutId} approved by admin {$adminId}");
        
        return ['success' => true, 'message' => 'Payout approved and processed successfully'];
    }

    /**
     * Reject a payout request (admin action).
     * Does NOT debit balance — money stays in seller's wallet.
     */
    public function rejectRequest(int $payoutId, int $adminId, string $reason): array
    {
        $payout = $this->find($payoutId);
        
        if (!$payout) {
            return ['success' => false, 'message' => 'Payout request not found'];
        }
        
        if ($payout['status'] !== 'pending') {
            return ['success' => false, 'message' => 'This payout has already been ' . $payout['status']];
        }
        
        // Update payout status
        $sql = "UPDATE {$this->table} 
                SET status = 'rejected', 
                    reviewed_by = ?, 
                    reviewed_at = NOW(), 
                    rejection_reason = ?
                WHERE id = ?";
        $this->query($sql, [$adminId, $reason, $payoutId]);
        
        // Notify shop owner
        $this->notifyShopOwner($payout['shop_owner_id'], 'payout_rejected',
            'Payout Request Rejected',
            'Your payout request for LKR ' . number_format($payout['amount'], 2) . ' has been rejected. Reason: ' . $reason,
            ['payout_id' => $payoutId, 'amount' => $payout['amount'], 'reason' => $reason]
        );
        
        error_log("Payout #{$payoutId} rejected by admin {$adminId}: {$reason}");
        
        return ['success' => true, 'message' => 'Payout request rejected'];
    }

    /**
     * Get payout history for a shop owner.
     */
    public function getPayoutsByOwner(int $shopOwnerId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE shop_owner_id = ?";
        $countResult = $this->queryFirst($countSql, [$shopOwnerId]);
        $total = (int)($countResult['total'] ?? 0);
        
        $sql = "SELECT p.*, 
                    u.first_name as reviewer_first_name, 
                    u.last_name as reviewer_last_name
                FROM {$this->table} p
                LEFT JOIN users u ON p.reviewed_by = u.id
                WHERE p.shop_owner_id = ?
                ORDER BY p.requested_at DESC
                LIMIT ? OFFSET ?";
        $data = $this->query($sql, [$shopOwnerId, $perPage, $offset])->fetchAll(\PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / max($perPage, 1))
            ]
        ];
    }

    /**
     * Get all pending payout requests (for admin review).
     */
    public function getPendingRequests(int $page = 1, int $perPage = 20, ?string $statusFilter = null): array
    {
        $offset = ($page - 1) * $perPage;
        $status = $statusFilter ?: 'pending';
        
        $whereClause = $status === 'all' ? '1=1' : 'p.status = ?';
        $params = $status === 'all' ? [] : [$status];
        
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} p WHERE {$whereClause}";
        $countResult = $this->queryFirst($countSql, $params);
        $total = (int)($countResult['total'] ?? 0);
        
        $sql = "SELECT p.*, 
                    u.first_name as owner_first_name, 
                    u.last_name as owner_last_name,
                    u.email as owner_email,
                    u.phone as owner_phone,
                    sop.shop_name,
                    sob.available_balance,
                    sob.total_earned,
                    reviewer.first_name as reviewer_first_name,
                    reviewer.last_name as reviewer_last_name
                FROM {$this->table} p
                INNER JOIN users u ON p.shop_owner_id = u.id
                LEFT JOIN shop_owner_profiles sop ON u.id = sop.user_id
                LEFT JOIN shop_owner_balances sob ON u.id = sob.shop_owner_id
                LEFT JOIN users reviewer ON p.reviewed_by = reviewer.id
                WHERE {$whereClause}
                ORDER BY p.requested_at DESC
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $data = $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        
        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / max($perPage, 1))
            ]
        ];
    }

    /**
     * Get payout statistics for admin dashboard.
     */
    public function getPayoutStats(): array
    {
        $pendingSql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total FROM {$this->table} WHERE status = 'pending'";
        $pendingResult = $this->queryFirst($pendingSql);
        
        $completedSql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total FROM {$this->table} WHERE status = 'completed'";
        $completedResult = $this->queryFirst($completedSql);
        
        $rejectedSql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'rejected'";
        $rejectedResult = $this->queryFirst($rejectedSql);
        
        $monthSql = "SELECT COALESCE(SUM(amount), 0) as total FROM {$this->table} 
                     WHERE status = 'completed' AND completed_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
        $monthResult = $this->queryFirst($monthSql);
        
        return [
            'pending_count' => (int)($pendingResult['count'] ?? 0),
            'pending_amount' => (float)($pendingResult['total'] ?? 0),
            'completed_count' => (int)($completedResult['count'] ?? 0),
            'completed_amount' => (float)($completedResult['total'] ?? 0),
            'rejected_count' => (int)($rejectedResult['count'] ?? 0),
            'this_month_paid' => (float)($monthResult['total'] ?? 0)
        ];
    }

    /**
     * Notify all admins about a payout request.
     */
    private function notifyAdminsOfPayoutRequest(int $shopOwnerId, int $payoutId, float $amount): void
    {
        try {
            // Get shop owner name
            $owner = $this->queryFirst("SELECT first_name, last_name FROM users WHERE id = ?", [$shopOwnerId]);
            $ownerName = $owner ? $owner['first_name'] . ' ' . $owner['last_name'] : 'Shop Owner #' . $shopOwnerId;
            
            // Get all admin user IDs
            $admins = $this->query("SELECT id FROM users WHERE user_type = 'admin' AND status = 'active'")->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($admins as $admin) {
                $sql = "INSERT INTO notifications (user_id, type, title, message, data, is_read) VALUES (?, ?, ?, ?, ?, 0)";
                $this->query($sql, [
                    $admin['id'],
                    'payout_requested',
                    'New Payout Request',
                    "{$ownerName} has requested a payout of LKR " . number_format($amount, 2),
                    json_encode(['payout_id' => $payoutId, 'shop_owner_id' => $shopOwnerId, 'amount' => $amount])
                ]);
            }
        } catch (\Exception $e) {
            error_log("Failed to notify admins of payout request: " . $e->getMessage());
        }
    }

    /**
     * Send notification to shop owner.
     */
    private function notifyShopOwner(int $shopOwnerId, string $type, string $title, string $message, array $data = []): void
    {
        try {
            $sql = "INSERT INTO notifications (user_id, type, title, message, data, is_read) VALUES (?, ?, ?, ?, ?, 0)";
            $this->query($sql, [
                $shopOwnerId,
                $type,
                $title,
                $message,
                json_encode($data)
            ]);
        } catch (\Exception $e) {
            error_log("Failed to notify shop owner {$shopOwnerId}: " . $e->getMessage());
        }
    }
}
