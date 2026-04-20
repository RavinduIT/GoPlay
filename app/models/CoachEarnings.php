<?php

namespace App\Models;

use Core\BaseModel;
use PDO;
use PDOException;

class CoachEarnings extends BaseModel
{
    protected string $table = 'coach_earnings';

    public function recordEarning(
        int $coachId,
        ?int $bookingId,
        float $amount,
        string $paymentMethod,
        string $paymentStatus = 'paid',
        ?string $notes = null
    ): bool {
        try {
            if ($bookingId) {
                $stmt = $this->db->getConnection()->prepare(
                    "SELECT id FROM {$this->table} WHERE booking_id = ? LIMIT 1"
                );
                $stmt->execute([$bookingId]);
                if ($stmt->fetch()) {
                    return true;
                }
            }

            $stmt = $this->db->getConnection()->prepare(
                "INSERT INTO {$this->table}
                 (coach_id, booking_id, amount, earning_date, payment_status, payment_method, notes)
                 VALUES (?, ?, ?, CURDATE(), ?, ?, ?)"
            );

            return $stmt->execute([
                $coachId,
                $bookingId,
                $amount,
                $paymentStatus,
                $paymentMethod,
                $notes
            ]);
        } catch (PDOException $e) {
            error_log('CoachEarnings::recordEarning error: ' . $e->getMessage());
            return false;
        }
    }

    public function getList(int $coachId, array $filters = [], int $page = 1, int $limit = 10): array
    {
        [$start, $end] = $this->resolveDateRange(
            $filters['dateRange'] ?? 'all',
            $filters['startDate'] ?? null,
            $filters['endDate'] ?? null
        );

        $where = ['ce.coach_id = ?'];
        $params = [$coachId];

        if ($start) {
            $where[] = 'ce.earning_date >= ?';
            $params[] = $start;
        }
        if ($end) {
            $where[] = 'ce.earning_date <= ?';
            $params[] = $end;
        }
        if (!empty($filters['sessionType'])) {
            $where[] = 'cb.session_type = ?';
            $params[] = $filters['sessionType'];
        }
        if (!empty($filters['paymentStatus'])) {
            $where[] = 'ce.payment_status = ?';
            $params[] = $filters['paymentStatus'];
        }

        $sort = match ($filters['sortBy'] ?? 'date_desc') {
            'date_asc'    => 'ce.earning_date ASC, ce.id ASC',
            'amount_desc' => 'ce.amount DESC',
            'amount_asc'  => 'ce.amount ASC',
            default       => 'ce.earning_date DESC, ce.id DESC',
        };

        $whereSql = implode(' AND ', $where);
        $countSql = "SELECT COUNT(*) FROM {$this->table} ce
                     LEFT JOIN coach_bookings cb ON ce.booking_id = cb.id
                     WHERE {$whereSql}";
        $countStmt = $this->db->getConnection()->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $offset = ($page - 1) * $limit;
        $rows = $this->query(
            "SELECT ce.id,
                    ce.amount,
                    ce.earning_date,
                    ce.payment_status,
                    ce.payment_method,
                    ce.notes,
                    ce.created_at,
                    cb.id AS booking_id,
                    cb.booking_date,
                    cb.start_time,
                    cb.end_time,
                    cb.session_type,
                    cb.status,
                    cb.duration,
                    cb.special_requests,
                    cb.coach_notes,
                    CONCAT(u.first_name,' ',u.last_name) AS client_name,
                    u.email AS client_email,
                    u.phone AS client_phone
             FROM {$this->table} ce
             LEFT JOIN coach_bookings cb ON ce.booking_id = cb.id
             LEFT JOIN users u ON cb.user_id = u.id
             WHERE {$whereSql}
             ORDER BY {$sort}
             LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['total_amount'] = (float)$row['amount'];
            $row['duration_hours'] = !empty($row['duration']) ? round(((int)$row['duration']) / 60, 1) : 1;
        }
        unset($row);

        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'totalPages' => max(1, (int)ceil($total / $limit)),
        ];
    }

    public function getStats(int $coachId, array $filters = []): array
    {
        [$start, $end] = $this->resolveDateRange(
            $filters['dateRange'] ?? 'all',
            $filters['startDate'] ?? null,
            $filters['endDate'] ?? null
        );
        [$prevStart, $prevEnd] = $this->previousPeriod($start, $end);

        $sum = function (string $extra, ?string $from, ?string $to) use ($coachId): float {
            $params = [$coachId];
            $sql = "SELECT COALESCE(SUM(amount),0) AS total
                    FROM {$this->table}
                    WHERE coach_id = ? {$extra}";
            if ($from) {
                $sql .= ' AND earning_date >= ?';
                $params[] = $from;
            }
            if ($to) {
                $sql .= ' AND earning_date <= ?';
                $params[] = $to;
            }
            return (float)($this->queryFirst($sql, $params)['total'] ?? 0);
        };

        $count = function (string $extra, ?string $from, ?string $to) use ($coachId): int {
            $params = [$coachId];
            $sql = "SELECT COUNT(*) AS total
                    FROM {$this->table}
                    WHERE coach_id = ? {$extra}";
            if ($from) {
                $sql .= ' AND earning_date >= ?';
                $params[] = $from;
            }
            if ($to) {
                $sql .= ' AND earning_date <= ?';
                $params[] = $to;
            }
            return (int)($this->queryFirst($sql, $params)['total'] ?? 0);
        };

        $pct = function (float $current, float $previous): ?float {
            if ($previous == 0.0) {
                return $current > 0 ? 100.0 : null;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $rateRow = $this->queryFirst(
            "SELECT hourly_rate FROM coaches WHERE id = ?",
            [$coachId]
        );
        $avgRate = (float)($rateRow['hourly_rate'] ?? 0);

        $earned = $sum("AND payment_status = 'paid'", $start, $end);
        $prevEarned = $sum("AND payment_status = 'paid'", $prevStart, $prevEnd);
        $pending = $sum("AND payment_status = 'pending'", $start, $end);
        $pendingCount = $count("AND payment_status = 'pending'", $start, $end);
        $completed = $count("AND payment_status = 'paid'", $start, $end);
        $prevCompleted = $count("AND payment_status = 'paid'", $prevStart, $prevEnd);

        return [
            'total_earnings' => $earned,
            'earnings_change' => $pct($earned, $prevEarned),
            'pending_payments' => $pending,
            'pending_count' => $pendingCount,
            'completed_sessions' => $completed,
            'completed_change' => $pct((float)$completed, (float)$prevCompleted),
            'avg_rate' => $avgRate,
        ];
    }

    public function getDetail(int $earningId, int $coachId): ?array
    {
        $row = $this->queryFirst(
            "SELECT ce.id,
                    ce.amount,
                    ce.earning_date,
                    ce.payment_status,
                    ce.payment_method,
                    ce.notes,
                    ce.created_at,
                    cb.id AS booking_id,
                    cb.booking_date,
                    cb.start_time,
                    cb.end_time,
                    cb.session_type,
                    cb.status,
                    cb.duration,
                    cb.special_requests,
                    cb.coach_notes,
                    CONCAT(u.first_name,' ',u.last_name) AS client_name,
                    u.email AS client_email,
                    u.phone AS client_phone
             FROM {$this->table} ce
             LEFT JOIN coach_bookings cb ON ce.booking_id = cb.id
             LEFT JOIN users u ON cb.user_id = u.id
             WHERE ce.id = ? AND ce.coach_id = ?",
            [$earningId, $coachId]
        );

        if (!$row) {
            return null;
        }

        $row['total_amount'] = (float)$row['amount'];
        $row['duration_hours'] = !empty($row['duration']) ? round(((int)$row['duration']) / 60, 1) : 1;
        return $row;
    }

    public function getUninvoiced(int $coachId): array
    {
        return $this->query(
            "SELECT ce.id, ce.amount, ce.earning_date, ce.payment_method,
                    CONCAT(u.first_name,' ',u.last_name) AS client_name
             FROM {$this->table} ce
             LEFT JOIN coach_bookings cb ON ce.booking_id = cb.id
             LEFT JOIN users u ON cb.user_id = u.id
             WHERE ce.coach_id = ?
               AND ce.payment_status = 'paid'
               AND ce.id NOT IN (
                   SELECT coach_earning_id
                   FROM commission_invoice_items
                   WHERE coach_earning_id IS NOT NULL
               )
             ORDER BY ce.earning_date ASC",
            [$coachId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCoachUninvoicedSummary(): array
    {
        return $this->query(
            "SELECT
                 c.id AS coach_id,
                 CONCAT(u.first_name,' ',u.last_name) AS coach_name,
                 u.email,
                 COUNT(ce.id) AS uninvoiced_count,
                 SUM(ce.amount) AS total_earnings,
                 SUM(ce.amount * 0.10) AS commission_owed,
                 MIN(ce.earning_date) AS earliest_date,
                 MAX(ce.earning_date) AS latest_date
             FROM {$this->table} ce
             JOIN coaches c ON ce.coach_id = c.id
             JOIN users u ON c.user_id = u.id
             WHERE ce.payment_status = 'paid'
               AND ce.id NOT IN (
                   SELECT coach_earning_id
                   FROM commission_invoice_items
                   WHERE coach_earning_id IS NOT NULL
               )
             GROUP BY c.id, u.first_name, u.last_name, u.email
             ORDER BY commission_owed DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function resolveDateRange(string $range, ?string $startDate, ?string $endDate): array
    {
        return match ($range) {
            'today' => [date('Y-m-d'), date('Y-m-d')],
            'week' => [date('Y-m-d', strtotime('monday this week')), date('Y-m-d')],
            'month' => [date('Y-m-01'), date('Y-m-d')],
            'lastMonth' => [
                date('Y-m-01', strtotime('first day of last month')),
                date('Y-m-t', strtotime('last day of last month'))
            ],
            'quarter' => [date('Y-m-01', strtotime('-2 months')), date('Y-m-d')],
            'year' => [date('Y-01-01'), date('Y-m-d')],
            'custom' => [$startDate ?: null, $endDate ?: null],
            default => [null, null],
        };
    }

    private function previousPeriod(?string $start, ?string $end): array
    {
        if (!$start || !$end) {
            return [null, null];
        }

        $startTs = strtotime($start);
        $endTs = strtotime($end);
        $days = max(1, (int)floor(($endTs - $startTs) / 86400) + 1);

        $prevEnd = date('Y-m-d', strtotime($start . ' -1 day'));
        $prevStart = date('Y-m-d', strtotime($prevEnd . ' -' . ($days - 1) . ' days'));

        return [$prevStart, $prevEnd];
    }
}
