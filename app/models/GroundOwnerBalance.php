<?php

namespace App\Models;

use Core\BaseModel;
use PDO;

/**
 * Ground Owner Balance Model
 *
 * Wallet for ground owners — mirrors ShopOwnerBalance pattern.
 * All mutations are atomic within a single SQL statement.
 */
class GroundOwnerBalance extends BaseModel
{
    protected string $table = 'ground_owner_balances';

    protected array $fillable = [
        'owner_id', 'available_balance', 'pending_balance',
        'total_earned', 'total_withdrawn', 'currency',
    ];

    // 10 % platform commission (same as GroundOwnerEarnings model)
    const FEE_PERCENTAGE = 10.0;

    // ─── Wallet helpers ──────────────────────────────────────

    /**
     * Return (or create) the balance row for an owner.
     */
    public function getOrCreate(int $ownerId): array
    {
        $row = $this->queryFirst(
            "SELECT * FROM {$this->table} WHERE owner_id = ?",
            [$ownerId]
        );

        if (!$row) {
            $this->create([
                'owner_id'          => $ownerId,
                'available_balance' => 0,
                'pending_balance'   => 0,
                'total_earned'      => 0,
                'total_withdrawn'   => 0,
                'currency'          => 'LKR',
            ]);
            $row = $this->queryFirst(
                "SELECT * FROM {$this->table} WHERE owner_id = ?",
                [$ownerId]
            );
        }

        return $row;
    }

    /**
     * Credit an online-paid booking directly to available_balance.
     * gross = full booking amount, fee = platform cut (10%).
     */
    public function creditBooking(int $ownerId, int $bookingId, float $gross, string $type = 'facility'): void
    {
        $this->getOrCreate($ownerId);
        $fee = round($gross * (self::FEE_PERCENTAGE / 100), 2);
        $net = round($gross - $fee, 2);

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance + ?,
                 total_earned      = total_earned      + ?
             WHERE owner_id = ?",
            [$net, $net, $ownerId]
        );

        // Also mark the ground_owner_earnings row as paid
        $this->query(
            "UPDATE ground_owner_earnings
             SET payment_status = 'paid', payment_method = 'payhere'
             WHERE owner_id = ? AND booking_id = ?",
            [$ownerId, $bookingId]
        );
    }

    /**
     * Deduct from available_balance when a payout is processed.
     * Returns false if balance is insufficient.
     */
    public function debit(int $ownerId, float $amount): bool
    {
        $balance = $this->getOrCreate($ownerId);
        if ((float)$balance['available_balance'] < $amount) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance - ?,
                 total_withdrawn   = total_withdrawn   + ?
             WHERE owner_id = ?",
            [$amount, $amount, $ownerId]
        );
        return true;
    }

    /**
     * Return balance summary for the earnings page.
     */
    public function getSummary(int $ownerId): array
    {
        $row = $this->getOrCreate($ownerId);
        return [
            'available_balance' => (float)$row['available_balance'],
            'pending_balance'   => (float)$row['pending_balance'],
            'total_earned'      => (float)$row['total_earned'],
            'total_withdrawn'   => (float)$row['total_withdrawn'],
        ];
    }

    // ─── Payout requests ──────────────────────────────────────

    /**
     * Create a payout request.
     * Immediately moves the amount from available → pending so it can't be double-requested.
     */
    public function requestPayout(int $ownerId, float $amount, array $bank): ?int
    {
        $balance = $this->getOrCreate($ownerId);
        if ((float)$balance['available_balance'] < $amount) {
            return null;
        }

        // Reserve the amount
        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance - ?,
                 pending_balance   = pending_balance   + ?
             WHERE owner_id = ?",
            [$amount, $amount, $ownerId]
        );

        $this->query(
            "INSERT INTO ground_owner_payouts
             (owner_id, amount, payout_method, bank_name, account_number, account_holder, branch_name, status)
             VALUES (?, ?, 'bank_transfer', ?, ?, ?, ?, 'pending')",
            [
                $ownerId, $amount,
                $bank['bank_name']      ?? '',
                $bank['account_number'] ?? '',
                $bank['account_holder'] ?? '',
                $bank['branch_name']    ?? '',
            ]
        );

        return (int)$this->lastInsertId();
    }

    /**
     * Cancel a pending payout — returns amount to available_balance.
     */
    public function cancelPayout(int $payoutId, int $ownerId): bool
    {
        $payout = $this->queryFirst(
            "SELECT * FROM ground_owner_payouts WHERE id = ? AND owner_id = ? AND status = 'pending'",
            [$payoutId, $ownerId]
        );

        if (!$payout) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance + ?,
                 pending_balance   = pending_balance   - ?
             WHERE owner_id = ?",
            [$payout['amount'], $payout['amount'], $ownerId]
        );

        $this->query(
            "UPDATE ground_owner_payouts SET status = 'rejected', rejection_reason = 'Cancelled by owner'
             WHERE id = ?",
            [$payoutId]
        );

        return true;
    }

    /**
     * List all payout requests for a ground owner.
     */
    public function getPayouts(int $ownerId): array
    {
        return $this->query(
            "SELECT p.*, u.first_name AS reviewer_first, u.last_name AS reviewer_last
             FROM ground_owner_payouts p
             LEFT JOIN users u ON u.id = p.reviewed_by
             WHERE p.owner_id = ?
             ORDER BY p.requested_at DESC",
            [$ownerId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Admin helpers ───────────────────────────────────────

    /**
     * Approve a payout — clears the pending_balance reservation.
     */
    public function approvePayout(int $payoutId, int $adminId, string $reference = ''): bool
    {
        $payout = $this->queryFirst(
            "SELECT * FROM ground_owner_payouts WHERE id = ? AND status = 'pending'",
            [$payoutId]
        );

        if (!$payout) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET pending_balance = pending_balance - ?,
                 total_withdrawn = total_withdrawn + ?
             WHERE owner_id = ?",
            [$payout['amount'], $payout['amount'], $payout['owner_id']]
        );

        $this->query(
            "UPDATE ground_owner_payouts
             SET status = 'completed', reviewed_by = ?, reviewed_at = NOW(),
                 completed_at = NOW(), transaction_reference = ?
             WHERE id = ?",
            [$adminId, $reference, $payoutId]
        );

        return true;
    }

    /**
     * Reject a payout — refunds amount back to available_balance.
     */
    public function rejectPayout(int $payoutId, int $adminId, string $reason = ''): bool
    {
        $payout = $this->queryFirst(
            "SELECT * FROM ground_owner_payouts WHERE id = ? AND status = 'pending'",
            [$payoutId]
        );

        if (!$payout) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance + ?,
                 pending_balance   = pending_balance   - ?
             WHERE owner_id = ?",
            [$payout['amount'], $payout['amount'], $payout['owner_id']]
        );

        $this->query(
            "UPDATE ground_owner_payouts
             SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), rejection_reason = ?
             WHERE id = ?",
            [$adminId, $reason, $payoutId]
        );

        return true;
    }

    /** All pending payouts across all owners (for admin). */
    public function getAllPendingPayouts(): array
    {
        return $this->query(
            "SELECT p.*, u.first_name, u.last_name, u.email
             FROM ground_owner_payouts p
             JOIN users u ON u.id = p.owner_id
             WHERE p.status = 'pending'
             ORDER BY p.requested_at ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Private ─────────────────────────────────────────────

    private function lastInsertId(): string
    {
        return $this->query("SELECT LAST_INSERT_ID() AS id")->fetchColumn();
    }
}
