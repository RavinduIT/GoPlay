<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Shop Owner Balance Model
 * 
 * Manages per-seller wallet balances with atomic credit/debit operations.
 * All balance mutations must go through this model to ensure consistency.
 */
class ShopOwnerBalance extends BaseModel
{
    protected string $table = 'shop_owner_balances';
    
    protected array $fillable = [
        'shop_owner_id',
        'available_balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
        'currency'
    ];

    protected array $casts = [
        'available_balance' => 'float',
        'pending_balance' => 'float',
        'total_earned' => 'float',
        'total_withdrawn' => 'float'
    ];

    /**
     * Public query executor for external callers.
     */
    public function runQuery(string $sql, array $params = [])
    {
        return $this->query($sql, $params);
    }

    /**
     * Get shop owner shares for an order (order items grouped by shop_owner_id).
     */
    public function getShopOwnerSharesByOrder(int $orderId): array
    {
        $sql = "SELECT 
                    p.shop_owner_id,
                    SUM(oi.total_price) as owner_total
                FROM order_items oi
                INNER JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ? AND p.shop_owner_id IS NOT NULL
                GROUP BY p.shop_owner_id";
        return $this->query($sql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get distinct shop owner IDs from an order.
     */
    public function getShopOwnersByOrder(int $orderId): array
    {
        $sql = "SELECT DISTINCT p.shop_owner_id 
                FROM order_items oi 
                INNER JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ? AND p.shop_owner_id IS NOT NULL";
        return $this->query($sql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Insert a notification.
     */
    public function insertNotification(int $userId, string $type, string $title, string $message, array $data = []): void
    {
        $sql = "INSERT INTO notifications (user_id, type, title, message, data, is_read) VALUES (?, ?, ?, ?, ?, 0)";
        $this->query($sql, [$userId, $type, $title, $message, json_encode($data)]);
    }

    /**
     * Get or create a balance record for a shop owner.
     * Lazy-initializes the row if it doesn't exist.
     */
    public function getOrCreateBalance(int $shopOwnerId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE shop_owner_id = ? LIMIT 1";
        $balance = $this->queryFirst($sql, [$shopOwnerId]);
        
        if (!$balance) {
            $id = $this->create([
                'shop_owner_id' => $shopOwnerId,
                'available_balance' => 0.00,
                'pending_balance' => 0.00,
                'total_earned' => 0.00,
                'total_withdrawn' => 0.00,
                'currency' => 'LKR'
            ]);
            $balance = $this->find($id);
        }
        
        return $balance;
    }

    /**
     * Credit a shop owner's balance after a successful sale.
     * Uses SELECT ... FOR UPDATE for atomic operation.
     * 
     * @param int $shopOwnerId  The shop owner's user ID
     * @param int $orderId      The order that generated this credit
     * @param float $grossAmount The total sale amount for this shop owner's items
     * @param float $feeAmount   Platform fee deducted
     * @return bool
     */
    public function creditSale(int $shopOwnerId, int $orderId, float $grossAmount, float $feeAmount): bool
    {
        try {
            $netAmount = round($grossAmount - $feeAmount, 2);
            
            // Idempotency check: don't double-credit the same order
            $existingTx = $this->queryFirst(
                "SELECT id FROM shop_owner_transactions WHERE shop_owner_id = ? AND order_id = ? AND type = 'credit_sale' LIMIT 1",
                [$shopOwnerId, $orderId]
            );
            
            if ($existingTx) {
                error_log("Idempotency guard: order {$orderId} already credited to shop owner {$shopOwnerId}");
                return true; // Already processed
            }
            
            // Ensure balance row exists
            $this->getOrCreateBalance($shopOwnerId);
            
            // Atomic update: credit available_balance and total_earned
            $sql = "UPDATE {$this->table} 
                    SET available_balance = available_balance + ?,
                        total_earned = total_earned + ?
                    WHERE shop_owner_id = ?";
            $this->query($sql, [$netAmount, $netAmount, $shopOwnerId]);
            
            // Get updated balance
            $balance = $this->getOrCreateBalance($shopOwnerId);
            
            // Record transaction in ledger
            $this->recordTransaction($shopOwnerId, [
                'order_id' => $orderId,
                'type' => 'credit_sale',
                'gross_amount' => $grossAmount,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'balance_after' => $balance['available_balance'],
                'description' => "Sale credit from order #{$orderId} (Gross: LKR " . number_format($grossAmount, 2) . ", Fee: LKR " . number_format($feeAmount, 2) . ")",
                'status' => 'completed'
            ]);
            
            error_log("Credited LKR {$netAmount} to shop owner {$shopOwnerId} for order {$orderId}");
            return true;
            
        } catch (\Exception $e) {
            error_log("Failed to credit shop owner {$shopOwnerId}: " . $e->getMessage());
            return false;
        }
    }

    /**
<<<<<<< HEAD
=======
     * Credit a shop owner's PENDING balance for a COD order.
     * Cash has not been collected yet — holds in pending_balance until delivery confirmed.
     *
     * @param int $shopOwnerId
     * @param int $orderId
     * @param float $grossAmount  Total sale amount for this shop owner's items
     * @param float $feeAmount    Platform fee (5%)
     * @return bool
     */
    public function creditSalePending(int $shopOwnerId, int $orderId, float $grossAmount, float $feeAmount): bool
    {
        try {
            $netAmount = round($grossAmount - $feeAmount, 2);

            // Idempotency check
            $existingTx = $this->queryFirst(
                "SELECT id FROM shop_owner_transactions WHERE shop_owner_id = ? AND order_id = ? AND type = 'credit_sale' LIMIT 1",
                [$shopOwnerId, $orderId]
            );
            if ($existingTx) {
                error_log("Idempotency guard (COD pending): order {$orderId} already recorded for shop owner {$shopOwnerId}");
                return true;
            }

            // Ensure balance row exists
            $this->getOrCreateBalance($shopOwnerId);

            // Hold in pending_balance only — available_balance unchanged until delivery
            $sql = "UPDATE {$this->table}
                    SET pending_balance = pending_balance + ?
                    WHERE shop_owner_id = ?";
            $this->query($sql, [$netAmount, $shopOwnerId]);

            $balance = $this->getOrCreateBalance($shopOwnerId);

            // Record as pending transaction
            $this->recordTransaction($shopOwnerId, [
                'order_id'    => $orderId,
                'type'        => 'credit_sale',
                'gross_amount'=> $grossAmount,
                'fee_amount'  => $feeAmount,
                'net_amount'  => $netAmount,
                'balance_after' => $balance['available_balance'], // available unchanged
                'description' => "COD pending — order #{$orderId} (Gross: LKR " . number_format($grossAmount, 2) . ", Fee: LKR " . number_format($feeAmount, 2) . ")",
                'status'      => 'pending'
            ]);

            error_log("COD pending credit LKR {$netAmount} held for shop owner {$shopOwnerId}, order {$orderId}");
            return true;

        } catch (\Exception $e) {
            error_log("Failed to pending-credit shop owner {$shopOwnerId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Confirm all pending COD credits for an order when delivery is confirmed.
     * Moves pending_balance → available_balance and marks transactions completed.
     *
     * @param int $orderId
     * @return bool
     */
    public function confirmCODCredit(int $orderId): bool
    {
        try {
            // Find all pending transactions for this order
            $txList = $this->query(
                "SELECT * FROM shop_owner_transactions WHERE order_id = ? AND type = 'credit_sale' AND status = 'pending'",
                [$orderId]
            )->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($txList)) {
                // Either already confirmed or this was an online-payment order (already completed)
                return true;
            }

            foreach ($txList as $tx) {
                $shopOwnerId = (int)$tx['shop_owner_id'];
                $netAmount   = (float)$tx['net_amount'];

                // Move pending → available, update total_earned
                $sql = "UPDATE {$this->table}
                        SET available_balance = available_balance + ?,
                            pending_balance   = GREATEST(0, pending_balance - ?),
                            total_earned      = total_earned + ?
                        WHERE shop_owner_id = ?";
                $this->query($sql, [$netAmount, $netAmount, $netAmount, $shopOwnerId]);

                $balance = $this->getOrCreateBalance($shopOwnerId);

                // Mark transaction completed and update balance_after
                $this->query(
                    "UPDATE shop_owner_transactions SET status = 'completed', balance_after = ?, description = REPLACE(description, 'COD pending', 'COD confirmed') WHERE id = ?",
                    [$balance['available_balance'], $tx['id']]
                );

                error_log("COD credit confirmed: LKR {$netAmount} released to shop owner {$shopOwnerId} for order {$orderId}");
            }

            return true;

        } catch (\Exception $e) {
            error_log("Failed to confirm COD credit for order {$orderId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel pending COD credits for an order (order cancelled before delivery).
     * Reduces pending_balance and marks transactions cancelled — no available_balance impact.
     *
     * @param int $orderId
     * @return bool
     */
    public function cancelPendingCODCredit(int $orderId): bool
    {
        try {
            $txList = $this->query(
                "SELECT * FROM shop_owner_transactions WHERE order_id = ? AND type = 'credit_sale' AND status = 'pending'",
                [$orderId]
            )->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($txList as $tx) {
                $shopOwnerId = (int)$tx['shop_owner_id'];
                $netAmount   = (float)$tx['net_amount'];

                $this->query(
                    "UPDATE {$this->table} SET pending_balance = GREATEST(0, pending_balance - ?) WHERE shop_owner_id = ?",
                    [$netAmount, $shopOwnerId]
                );

                $this->query(
                    "UPDATE shop_owner_transactions SET status = 'cancelled', description = CONCAT(description, ' [CANCELLED]') WHERE id = ?",
                    [$tx['id']]
                );

                error_log("COD pending credit cancelled for shop owner {$shopOwnerId}, order {$orderId}");
            }

            return true;

        } catch (\Exception $e) {
            error_log("Failed to cancel COD credit for order {$orderId}: " . $e->getMessage());
            return false;
        }
    }

    /**
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
     * Debit a shop owner's balance for a payout withdrawal.
     * 
     * @param int $shopOwnerId The shop owner's user ID
     * @param int $payoutId    The payout request ID
     * @param float $amount    Amount being withdrawn
     * @return bool
     */
    public function debitWithdrawal(int $shopOwnerId, int $payoutId, float $amount): bool
    {
        try {
            // Verify sufficient balance
            $balance = $this->getOrCreateBalance($shopOwnerId);
            
            if ($balance['available_balance'] < $amount) {
                error_log("Insufficient balance for shop owner {$shopOwnerId}: has {$balance['available_balance']}, needs {$amount}");
                return false;
            }
            
            // Atomic update: debit available_balance, increase total_withdrawn
            $sql = "UPDATE {$this->table} 
                    SET available_balance = available_balance - ?,
                        total_withdrawn = total_withdrawn + ?
                    WHERE shop_owner_id = ? AND available_balance >= ?";
            $stmt = $this->query($sql, [$amount, $amount, $shopOwnerId, $amount]);
            
            // Get updated balance
            $updatedBalance = $this->getOrCreateBalance($shopOwnerId);
            
            // Record transaction in ledger
            $this->recordTransaction($shopOwnerId, [
                'payout_id' => $payoutId,
                'type' => 'debit_withdrawal',
                'gross_amount' => $amount,
                'fee_amount' => 0,
                'net_amount' => $amount,
                'balance_after' => $updatedBalance['available_balance'],
                'description' => "Withdrawal payout #" . $payoutId,
                'status' => 'completed'
            ]);
            
            error_log("Debited LKR {$amount} from shop owner {$shopOwnerId} for payout {$payoutId}");
            return true;
            
        } catch (\Exception $e) {
            error_log("Failed to debit shop owner {$shopOwnerId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if shop owner can request a withdrawal.
     */
    public function canWithdraw(int $shopOwnerId): array
    {
        $balance = $this->getOrCreateBalance($shopOwnerId);
        $minAmount = $this->getMinPayoutAmount();
        
        // Check for pending payouts
        $pendingPayout = $this->queryFirst(
            "SELECT id FROM shop_owner_payouts WHERE shop_owner_id = ? AND status IN ('pending', 'processing') LIMIT 1",
            [$shopOwnerId]
        );
        
        return [
            'can_withdraw' => $balance['available_balance'] >= $minAmount && !$pendingPayout,
            'available_balance' => $balance['available_balance'],
            'min_amount' => $minAmount,
            'has_pending_payout' => !empty($pendingPayout),
            'message' => $pendingPayout 
                ? 'You already have a pending payout request' 
                : ($balance['available_balance'] < $minAmount 
                    ? 'Minimum payout amount is LKR ' . number_format($minAmount, 2) 
                    : 'You are eligible to request a payout')
        ];
    }

    /**
     * Get minimum payout amount from settings.
     */
    public function getMinPayoutAmount(): float
    {
        $sql = "SELECT value FROM settings WHERE key_name = 'min_payout_amount' LIMIT 1";
        $result = $this->queryFirst($sql);
        return $result ? (float)$result['value'] : 10000.00;
    }

    /**
     * Get service fee percentage from settings.
     */
    public function getServiceFeePercentage(): float
    {
        $sql = "SELECT value FROM settings WHERE key_name = 'service_fee_percentage' LIMIT 1";
        $result = $this->queryFirst($sql);
        return $result ? (float)$result['value'] : 5.00;
    }

    /**
     * Get transaction history for a shop owner.
     */
    public function getTransactionHistory(int $shopOwnerId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Count
        $countSql = "SELECT COUNT(*) as total FROM shop_owner_transactions WHERE shop_owner_id = ?";
        $countResult = $this->queryFirst($countSql, [$shopOwnerId]);
        $total = (int)($countResult['total'] ?? 0);
        
        // Data
        $sql = "SELECT t.*, o.order_number 
                FROM shop_owner_transactions t
                LEFT JOIN orders o ON t.order_id = o.id
                WHERE t.shop_owner_id = ?
                ORDER BY t.created_at DESC
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
     * Record a transaction in the ledger.
     */
    private function recordTransaction(int $shopOwnerId, array $data): ?int
    {
        $txData = [
            'shop_owner_id' => $shopOwnerId,
            'order_id' => $data['order_id'] ?? null,
            'payout_id' => $data['payout_id'] ?? null,
            'type' => $data['type'],
            'gross_amount' => $data['gross_amount'],
            'fee_amount' => $data['fee_amount'],
            'net_amount' => $data['net_amount'],
            'balance_after' => $data['balance_after'],
            'description' => $data['description'] ?? '',
            'status' => $data['status'] ?? 'completed'
        ];
        
        $sql = "INSERT INTO shop_owner_transactions 
                (shop_owner_id, order_id, payout_id, type, gross_amount, fee_amount, net_amount, balance_after, description, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->query($sql, [
            $txData['shop_owner_id'],
            $txData['order_id'],
            $txData['payout_id'],
            $txData['type'],
            $txData['gross_amount'],
            $txData['fee_amount'],
            $txData['net_amount'],
            $txData['balance_after'],
            $txData['description'],
            $txData['status']
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get earnings summary for dashboard cards.
     */
    public function getEarningsSummary(int $shopOwnerId): array
    {
        $balance = $this->getOrCreateBalance($shopOwnerId);
        
        // This month earnings
        $monthSql = "SELECT COALESCE(SUM(net_amount), 0) as total 
                     FROM shop_owner_transactions 
                     WHERE shop_owner_id = ? 
                       AND type = 'credit_sale' 
                       AND status = 'completed'
                       AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')";
        $monthResult = $this->queryFirst($monthSql, [$shopOwnerId]);
        
        // Pending payouts total
        $pendingSql = "SELECT COALESCE(SUM(amount), 0) as total 
                       FROM shop_owner_payouts 
                       WHERE shop_owner_id = ? AND status IN ('pending', 'processing')";
        $pendingResult = $this->queryFirst($pendingSql, [$shopOwnerId]);
        
        // Total orders generating revenue
        $ordersSql = "SELECT COUNT(DISTINCT order_id) as count 
                      FROM shop_owner_transactions 
                      WHERE shop_owner_id = ? AND type = 'credit_sale'";
        $ordersResult = $this->queryFirst($ordersSql, [$shopOwnerId]);
        
        return [
            'available_balance' => (float)$balance['available_balance'],
<<<<<<< HEAD
=======
            'pending_balance'   => (float)$balance['pending_balance'],
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
            'total_earned' => (float)$balance['total_earned'],
            'total_withdrawn' => (float)$balance['total_withdrawn'],
            'pending_payouts' => (float)($pendingResult['total'] ?? 0),
            'this_month_earnings' => (float)($monthResult['total'] ?? 0),
            'total_orders' => (int)($ordersResult['count'] ?? 0),
            'min_payout_amount' => $this->getMinPayoutAmount(),
            'service_fee_percentage' => $this->getServiceFeePercentage()
        ];
    }
}
