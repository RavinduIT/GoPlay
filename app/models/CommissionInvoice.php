<?php

namespace App\Models;

use Core\BaseModel;
use PDO;

class CommissionInvoice extends BaseModel
{
    protected string $table = 'commission_invoices';

    protected array $fillable = [
        'invoice_number', 'owner_id', 'period_start', 'period_end',
        'total_earnings', 'commission_rate', 'commission_amount',
        'status', 'due_date', 'paid_date', 'notes', 'created_by'
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
                WHERE ge.id NOT IN (SELECT earning_id FROM commission_invoice_items)
                  AND ge.payment_status = 'paid'
                GROUP BY u.id, u.first_name, u.last_name, u.email, u.phone
                ORDER BY commission_owed DESC";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // All invoices with owner info
    public function getAllInvoices(): array
    {
        $sql = "SELECT
                    ci.*,
                    CONCAT(u.first_name,' ',u.last_name) AS owner_name,
                    u.email AS owner_email
                FROM commission_invoices ci
                JOIN users u ON ci.owner_id = u.id
                ORDER BY ci.created_at DESC";

        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Invoice detail with line items
    public function getInvoiceWithItems(int $invoiceId): ?array
    {
        $invoice = $this->queryFirst(
            "SELECT ci.*, CONCAT(u.first_name,' ',u.last_name) AS owner_name,
                    u.email AS owner_email, u.phone AS owner_phone
             FROM commission_invoices ci
             JOIN users u ON ci.owner_id = u.id
             WHERE ci.id = ?",
            [$invoiceId]
        );

        if (!$invoice) return null;

        $items = $this->query(
            "SELECT cii.*, ge.earning_date, ge.payment_method, sf.name AS facility_name
             FROM commission_invoice_items cii
             JOIN ground_owner_earnings ge ON cii.earning_id = ge.id
             LEFT JOIN sports_facilities sf ON ge.facility_id = sf.id
             WHERE cii.invoice_id = ?
             ORDER BY ge.earning_date ASC",
            [$invoiceId]
        )->fetchAll(PDO::FETCH_ASSOC);

        $invoice['items'] = $items;
        return $invoice;
    }

    // Invoices for a specific ground owner
    public function getInvoicesByOwner(int $ownerId): array
    {
        $sql = "SELECT * FROM commission_invoices
                WHERE owner_id = ?
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
               AND ge.id NOT IN (SELECT earning_id FROM commission_invoice_items)
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

        // Also compute uninvoiced commission still owed
        $uninvoiced = $this->queryFirst(
            "SELECT COALESCE(SUM(amount * 0.10),0) AS uninvoiced_commission
             FROM ground_owner_earnings
             WHERE payment_status = 'paid'
               AND id NOT IN (SELECT earning_id FROM commission_invoice_items)", []
        );

        return array_merge($row ?? [], $uninvoiced ?? []);
    }
}
