<?php

namespace App\Models;

use Core\BaseModel;
use PDO;

class CommissionInvoice extends BaseModel
{
    protected string $table = 'commission_invoices';

    protected array $fillable = [
        'invoice_number', 'provider_type', 'owner_id', 'coach_id',
        'period_start', 'period_end', 'total_earnings', 'commission_rate',
        'commission_amount', 'status', 'due_date', 'paid_date', 'notes', 'created_by'
    ];

    // Ground owners with uninvoiced earnings
    public function getOwnerSummary(): array
    {
        $sql = "SELECT
                    u.id            AS owner_id,
                    CONCAT(u.first_name,' ',u.last_name) AS owner_name,
                    u.email,
                    u.phone,
                    COUNT(ge.id)          AS uninvoiced_count,
                    SUM(ge.amount)        AS total_earnings,
                    SUM(ge.amount * 0.10) AS commission_owed,
                    MIN(ge.earning_date)  AS earliest_date,
                    MAX(ge.earning_date)  AS latest_date
                FROM ground_owner_earnings ge
                JOIN users u ON ge.owner_id = u.id
                WHERE ge.id NOT IN (
                    SELECT earning_id
                    FROM commission_invoice_items
                    WHERE earning_id IS NOT NULL
                )
                  AND ge.payment_status = 'paid'
                GROUP BY u.id, u.first_name, u.last_name, u.email, u.phone
                ORDER BY commission_owed DESC";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // All invoices with provider info (ground owners + coaches)
    public function getAllInvoices(): array
    {
        $sql = "SELECT
                    ci.*,
                    CASE
                        WHEN ci.provider_type = 'coach' THEN CONCAT(u2.first_name,' ',u2.last_name)
                        ELSE CONCAT(u.first_name,' ',u.last_name)
                    END AS owner_name,
                    CASE
                        WHEN ci.provider_type = 'coach' THEN u2.email
                        ELSE u.email
                    END AS owner_email
                FROM commission_invoices ci
                LEFT JOIN users u  ON ci.owner_id = u.id AND ci.provider_type = 'ground_owner'
                LEFT JOIN coaches c ON ci.coach_id = c.id AND ci.provider_type = 'coach'
                LEFT JOIN users u2 ON c.user_id = u2.id  AND ci.provider_type = 'coach'
                ORDER BY ci.created_at DESC";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Invoice detail with line items (handles both ground_owner and coach)
    public function getInvoiceWithItems(int $invoiceId): ?array
    {
        $invoice = $this->queryFirst(
            "SELECT ci.*,
                    CASE WHEN ci.provider_type='coach' THEN CONCAT(u2.first_name,' ',u2.last_name)
                         ELSE CONCAT(u.first_name,' ',u.last_name) END AS owner_name,
                    CASE WHEN ci.provider_type='coach' THEN u2.email ELSE u.email END AS owner_email,
                    CASE WHEN ci.provider_type='coach' THEN u2.phone ELSE u.phone END AS owner_phone
             FROM commission_invoices ci
             LEFT JOIN users u   ON ci.owner_id = u.id  AND ci.provider_type = 'ground_owner'
             LEFT JOIN coaches c  ON ci.coach_id = c.id  AND ci.provider_type = 'coach'
             LEFT JOIN users u2  ON c.user_id = u2.id   AND ci.provider_type = 'coach'
             WHERE ci.id = ?",
            [$invoiceId]
        );

        if (!$invoice) return null;

        if ($invoice['provider_type'] === 'coach') {
            $items = $this->query(
                "SELECT cii.*, ce.earning_date, ce.payment_method, cii.provider_name AS facility_name
                 FROM commission_invoice_items cii
                 JOIN coach_earnings ce ON cii.coach_earning_id = ce.id
                 WHERE cii.invoice_id = ?
                 ORDER BY ce.earning_date ASC",
                [$invoiceId]
            )->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $items = $this->query(
                "SELECT cii.*, ge.earning_date, ge.payment_method, sf.name AS facility_name
                 FROM commission_invoice_items cii
                 JOIN ground_owner_earnings ge ON cii.earning_id = ge.id
                 LEFT JOIN sports_facilities sf ON ge.facility_id = sf.id
                 WHERE cii.invoice_id = ?
                 ORDER BY ge.earning_date ASC",
                [$invoiceId]
            )->fetchAll(PDO::FETCH_ASSOC);
        }

        $invoice['items'] = $items;
        return $invoice;
    }

    // Invoices for a specific ground owner
    public function getInvoicesByOwner(int $ownerId): array
    {
        $sql = "SELECT * FROM commission_invoices
                WHERE provider_type = 'ground_owner' AND owner_id = ?
                ORDER BY created_at DESC";

        return $this->query($sql, [$ownerId])->fetchAll(PDO::FETCH_ASSOC);
    }

    // Generate invoice for owner — wraps all uninvoiced earnings
    public function generateInvoice(int $ownerId, int $adminId, ?string $notes = null): ?array
    {
        $db = $this->db->getConnection();

        // Get all uninvoiced paid earnings for this owner
        $earnings = $db->prepare(
            "SELECT ge.id, ge.amount, ge.earning_date, sf.name AS facility_name
             FROM ground_owner_earnings ge
             LEFT JOIN sports_facilities sf ON ge.facility_id = sf.id
             WHERE ge.owner_id = ?
               AND ge.payment_status = 'paid'
               AND ge.id NOT IN (
                   SELECT earning_id
                   FROM commission_invoice_items
                   WHERE earning_id IS NOT NULL
               )
             ORDER BY ge.earning_date ASC"
        );
        $earnings->execute([$ownerId]);
        $rows = $earnings->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) return null;

        $totalEarnings = array_sum(array_column($rows, 'amount'));
        $commissionAmount = round($totalEarnings * 0.10, 2);
        $periodStart = $rows[0]['earning_date'];
        $periodEnd   = $rows[count($rows) - 1]['earning_date'];
        $invoiceNumber = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $dueDate = date('Y-m-d', strtotime('+14 days'));

        $db->beginTransaction();
        try {
            // Insert invoice
            $db->prepare(
                "INSERT INTO commission_invoices
                 (invoice_number, owner_id, period_start, period_end, total_earnings,
                  commission_rate, commission_amount, status, due_date, notes, created_by)
                 VALUES (?,?,?,?,?,0.10,?,?,?,?,?)"
            )->execute([
                $invoiceNumber, $ownerId, $periodStart, $periodEnd,
                $totalEarnings, $commissionAmount, 'sent', $dueDate, $notes, $adminId
            ]);

            $invoiceId = (int)$db->lastInsertId();

            // Insert line items
            $itemStmt = $db->prepare(
                "INSERT INTO commission_invoice_items
                 (invoice_id, earning_id, earning_date, facility_name, gross_amount, commission_amount)
                 VALUES (?,?,?,?,?,?)"
            );
            foreach ($rows as $row) {
                $itemStmt->execute([
                    $invoiceId,
                    $row['id'],
                    $row['earning_date'],
                    $row['facility_name'],
                    $row['amount'],
                    round($row['amount'] * 0.10, 2)
                ]);
            }

            $db->commit();
            return $this->getInvoiceWithItems($invoiceId);
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('generateInvoice error: ' . $e->getMessage());
            return null;
        }
    }

    // Generate commission invoice for a coach
    public function generateCoachInvoice(int $coachId, int $adminId, ?string $notes = null): ?array
    {
        $db = $this->db->getConnection();

        // Get coach's user info for label
        $coachInfo = $db->prepare(
            "SELECT c.id AS coach_id, CONCAT(u.first_name,' ',u.last_name) AS coach_name
             FROM coaches c JOIN users u ON c.user_id = u.id WHERE c.id = ?"
        );
        $coachInfo->execute([$coachId]);
        $coach = $coachInfo->fetch(PDO::FETCH_ASSOC);
        if (!$coach) return null;

        // Uninvoiced paid earnings
        $earnStmt = $db->prepare(
            "SELECT ce.id, ce.amount, ce.earning_date,
                    CONCAT(u.first_name,' ',u.last_name) AS client_name
             FROM coach_earnings ce
             LEFT JOIN coach_bookings cb ON ce.booking_id = cb.id
             LEFT JOIN users u ON cb.user_id = u.id
             WHERE ce.coach_id = ?
               AND ce.payment_status = 'paid'
               AND ce.id NOT IN (
                   SELECT coach_earning_id FROM commission_invoice_items
                   WHERE coach_earning_id IS NOT NULL
               )
             ORDER BY ce.earning_date ASC"
        );
        $earnStmt->execute([$coachId]);
        $rows = $earnStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) return null;

        $totalEarnings   = array_sum(array_column($rows, 'amount'));
        $commissionAmount = round($totalEarnings * 0.10, 2);
        $periodStart     = $rows[0]['earning_date'];
        $periodEnd       = $rows[count($rows) - 1]['earning_date'];
        $invoiceNumber   = 'CINV-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $dueDate         = date('Y-m-d', strtotime('+14 days'));

        $db->beginTransaction();
        try {
            $db->prepare(
                "INSERT INTO commission_invoices
                 (provider_type, coach_id, invoice_number, period_start, period_end,
                  total_earnings, commission_rate, commission_amount, status, due_date, notes, created_by)
                 VALUES ('coach',?,?,?,?,?,0.10,?,'sent',?,?,?)"
            )->execute([
                $coachId, $invoiceNumber, $periodStart, $periodEnd,
                $totalEarnings, $commissionAmount, $dueDate, $notes, $adminId
            ]);

            $invoiceId = (int)$db->lastInsertId();

            $itemStmt = $db->prepare(
                "INSERT INTO commission_invoice_items
                 (invoice_id, coach_earning_id, earning_date, provider_name, gross_amount, commission_amount)
                 VALUES (?,?,?,?,?,?)"
            );
            foreach ($rows as $row) {
                $itemStmt->execute([
                    $invoiceId,
                    $row['id'],
                    $row['earning_date'],
                    $row['client_name'] ?? $coach['coach_name'],
                    $row['amount'],
                    round($row['amount'] * 0.10, 2)
                ]);
            }

            $db->commit();
            return $this->getInvoiceWithItems($invoiceId);
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('generateCoachInvoice error: ' . $e->getMessage());
            return null;
        }
    }

    // Coach views their own invoices
    public function getInvoicesByCoach(int $coachId): array
    {
        return $this->query(
            "SELECT * FROM commission_invoices
             WHERE provider_type = 'coach' AND coach_id = ?
             ORDER BY created_at DESC",
            [$coachId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mark invoice as paid
    public function markPaid(int $invoiceId): bool
    {
        $db = $this->db->getConnection();
        $stmt = $db->prepare(
            "UPDATE commission_invoices SET status = 'paid', paid_date = CURDATE() WHERE id = ?"
        );
        return $stmt->execute([$invoiceId]);
    }

    // Mark overdue invoices automatically
    public function updateOverdue(): void
    {
        $this->db->getConnection()->prepare(
            "UPDATE commission_invoices SET status = 'overdue'
             WHERE status = 'sent' AND due_date < CURDATE()"
        )->execute();
    }

    // Dashboard summary counts
    public function getStats(): array
    {
        $row = $this->queryFirst(
            "SELECT
                COUNT(*)                                                    AS total_invoices,
                SUM(CASE WHEN status='sent'    THEN 1 ELSE 0 END)          AS sent,
                SUM(CASE WHEN status='paid'    THEN 1 ELSE 0 END)          AS paid,
                SUM(CASE WHEN status='overdue' THEN 1 ELSE 0 END)          AS overdue,
                COALESCE(SUM(commission_amount),0)                         AS total_billed,
                COALESCE(SUM(CASE WHEN status='paid' THEN commission_amount ELSE 0 END),0) AS total_collected,
                COALESCE(SUM(CASE WHEN status!='paid' THEN commission_amount ELSE 0 END),0) AS total_outstanding
             FROM commission_invoices", []
        );

        // Uninvoiced commission from ground owners
        $uninvoicedGO = $this->queryFirst(
            "SELECT COALESCE(SUM(amount * 0.10),0) AS amt
             FROM ground_owner_earnings
             WHERE payment_status = 'paid'
               AND id NOT IN (SELECT earning_id FROM commission_invoice_items WHERE earning_id IS NOT NULL)", []
        );

        // Uninvoiced commission from coaches
        $uninvoicedC = $this->queryFirst(
            "SELECT COALESCE(SUM(amount * 0.10),0) AS amt
             FROM coach_earnings
             WHERE payment_status = 'paid'
               AND id NOT IN (SELECT coach_earning_id FROM commission_invoice_items WHERE coach_earning_id IS NOT NULL)", []
        );

        $uninvoiced = ['uninvoiced_commission' =>
            round(($uninvoicedGO['amt'] ?? 0) + ($uninvoicedC['amt'] ?? 0), 2)
        ];

        return array_merge($row ?? [], $uninvoiced);
    }
}
