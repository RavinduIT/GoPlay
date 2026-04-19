<?php

namespace App\Models;

use Core\BaseModel;
use PDO;

/**
 * Coach Balance Model
 *
 * Wallet for coaches. coach_id = coaches.id (not users.id).
 */
class CoachBalance extends BaseModel
{
    protected string $table = 'coach_balances';

    protected array $fillable = [
        'coach_id', 'available_balance', 'pending_balance',
        'total_earned', 'total_withdrawn', 'currency',
    ];

    const FEE_PERCENTAGE = 10.0;

    // ─── Wallet helpers ──────────────────────────────────────

    public function getOrCreate(int $coachId): array
    {
        $row = $this->queryFirst(
            "SELECT * FROM {$this->table} WHERE coach_id = ?",
            [$coachId]
        );

        if (!$row) {
            $this->create([
                'coach_id'          => $coachId,
                'available_balance' => 0,
                'pending_balance'   => 0,
                'total_earned'      => 0,
                'total_withdrawn'   => 0,
                'currency'          => 'LKR',
            ]);
            $row = $this->queryFirst(
                "SELECT * FROM {$this->table} WHERE coach_id = ?",
                [$coachId]
            );
        }

        return $row;
    }

    /**
     * Credit an online-paid coach session to available_balance.
     */
    public function creditSession(int $coachId, int $bookingId, float $gross): void
    {
        $this->getOrCreate($coachId);
        $fee = round($gross * (self::FEE_PERCENTAGE / 100), 2);
        $net = round($gross - $fee, 2);

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance + ?,
                 total_earned      = total_earned      + ?
             WHERE coach_id = ?",
            [$net, $net, $coachId]
        );
    }

    public function debit(int $coachId, float $amount): bool
    {
        $balance = $this->getOrCreate($coachId);
        if ((float)$balance['available_balance'] < $amount) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance - ?,
                 total_withdrawn   = total_withdrawn   + ?
             WHERE coach_id = ?",
            [$amount, $amount, $coachId]
        );
        return true;
    }

    public function getSummary(int $coachId): array
    {
        $row = $this->getOrCreate($coachId);
        return [
            'available_balance' => (float)$row['available_balance'],
            'pending_balance'   => (float)$row['pending_balance'],
            'total_earned'      => (float)$row['total_earned'],
            'total_withdrawn'   => (float)$row['total_withdrawn'],
        ];
    }

    // ─── Payout requests ─────────────────────────────────────

    public function requestPayout(int $coachId, float $amount, array $bank): ?int
    {
        $balance = $this->getOrCreate($coachId);
        if ((float)$balance['available_balance'] < $amount) {
            return null;
        }

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance - ?,
                 pending_balance   = pending_balance   + ?
             WHERE coach_id = ?",
            [$amount, $amount, $coachId]
        );

        $this->query(
            "INSERT INTO coach_payouts
             (coach_id, amount, payout_method, bank_name, account_number, account_holder, branch_name, status)
             VALUES (?, ?, 'bank_transfer', ?, ?, ?, ?, 'pending')",
            [
                $coachId, $amount,
                $bank['bank_name']      ?? '',
                $bank['account_number'] ?? '',
                $bank['account_holder'] ?? '',
                $bank['branch_name']    ?? '',
            ]
        );

        return (int)$this->lastInsertId();
    }

    public function cancelPayout(int $payoutId, int $coachId): bool
    {
        $payout = $this->queryFirst(
            "SELECT * FROM coach_payouts WHERE id = ? AND coach_id = ? AND status = 'pending'",
            [$payoutId, $coachId]
        );

        if (!$payout) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance + ?,
                 pending_balance   = pending_balance   - ?
             WHERE coach_id = ?",
            [$payout['amount'], $payout['amount'], $coachId]
        );

        $this->query(
            "UPDATE coach_payouts SET status = 'rejected', rejection_reason = 'Cancelled by coach'
             WHERE id = ?",
            [$payoutId]
        );

        return true;
    }

    public function getPayouts(int $coachId): array
    {
        return $this->query(
            "SELECT p.*, u.first_name AS reviewer_first, u.last_name AS reviewer_last
             FROM coach_payouts p
             LEFT JOIN users u ON u.id = p.reviewed_by
             WHERE p.coach_id = ?
             ORDER BY p.requested_at DESC",
            [$coachId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Admin ───────────────────────────────────────────────

    public function approvePayout(int $payoutId, int $adminId, string $reference = ''): bool
    {
        $payout = $this->queryFirst(
            "SELECT * FROM coach_payouts WHERE id = ? AND status = 'pending'",
            [$payoutId]
        );

        if (!$payout) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET pending_balance = pending_balance - ?,
                 total_withdrawn = total_withdrawn + ?
             WHERE coach_id = ?",
            [$payout['amount'], $payout['amount'], $payout['coach_id']]
        );

        $this->query(
            "UPDATE coach_payouts
             SET status = 'completed', reviewed_by = ?, reviewed_at = NOW(),
                 completed_at = NOW(), transaction_reference = ?
             WHERE id = ?",
            [$adminId, $reference, $payoutId]
        );

        return true;
    }

    public function rejectPayout(int $payoutId, int $adminId, string $reason = ''): bool
    {
        $payout = $this->queryFirst(
            "SELECT * FROM coach_payouts WHERE id = ? AND status = 'pending'",
            [$payoutId]
        );

        if (!$payout) {
            return false;
        }

        $this->query(
            "UPDATE {$this->table}
             SET available_balance = available_balance + ?,
                 pending_balance   = pending_balance   - ?
             WHERE coach_id = ?",
            [$payout['amount'], $payout['amount'], $payout['coach_id']]
        );

        $this->query(
            "UPDATE coach_payouts
             SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), rejection_reason = ?
             WHERE id = ?",
            [$adminId, $reason, $payoutId]
        );

        return true;
    }

    public function getAllPendingPayouts(): array
    {
        return $this->query(
            "SELECT p.*, c.id AS coach_id, u.first_name, u.last_name, u.email
             FROM coach_payouts p
             JOIN coaches c ON c.id = p.coach_id
             JOIN users u ON u.id = c.user_id
             WHERE p.status = 'pending'
             ORDER BY p.requested_at ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function lastInsertId(): string
    {
        return $this->query("SELECT LAST_INSERT_ID() AS id")->fetchColumn();
    }
}
