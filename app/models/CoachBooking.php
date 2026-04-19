<?php

namespace App\Models;

use Core\BaseModel;

/**
 * CoachBooking Model
 *
 * Handles coach session bookings – creation, retrieval, editing,
 * cancellation, and schedule display for the coach dashboard.
 */
class CoachBooking extends BaseModel
{
    protected string $table = 'coach_bookings';

    protected array $fillable = [
        'user_id', 'coach_id', 'booking_date', 'start_time', 'end_time',
        'duration', 'session_type', 'session_title', 'total_amount',
        'payment_status', 'status', 'special_requests', 'coach_notes',
        'cancellation_reason', 'cancelled_by', 'cancelled_at',
    ];

    protected array $casts = [
        'total_amount' => 'float',
        'duration'     => 'int',
    ];

    // ─────────────────────────────────────────────────────────
    // Booking creation
    // ─────────────────────────────────────────────────────────

    /**
     * Create a new coach session booking.
     */
    public function createBooking(array $data): ?int
    {
        $insert = [
            'user_id'          => (int)$data['user_id'],
            'coach_id'         => (int)$data['coach_id'],
            'booking_date'     => $data['booking_date'],
            'start_time'       => $data['start_time'],
            'end_time'         => $data['end_time'],
            'duration'         => (int)($data['duration'] ?? 60),
            'session_type'     => $data['session_type'] ?? 'individual',
            'session_title'    => $data['session_title'] ?? null,
            'total_amount'     => (float)$data['total_amount'],
            'payment_status'   => 'pending',
            'status'           => 'pending',
            'special_requests' => $data['special_requests'] ?? null,
        ];

        return $this->create($insert);
    }

    // ─────────────────────────────────────────────────────────
    // Availability checking
    // ─────────────────────────────────────────────────────────

    /**
     * Returns TRUE when the requested slot does not clash with an existing booking.
     */
    public function isTimeSlotAvailable(
        int     $coachId,
        string  $date,
        string  $startTime,
        string  $endTime,
        ?int    $excludeId = null
    ): bool {
        $sql = "SELECT COUNT(*) AS cnt
                FROM {$this->table}
                WHERE coach_id = ?
                  AND booking_date = ?
                  AND status NOT IN ('cancelled','no_show')
                  AND (
                        (start_time <  ? AND end_time   > ?)  -- overlaps left
                     OR (start_time <  ? AND end_time   > ?)  -- overlaps right
                     OR (start_time >= ? AND start_time < ?)  -- contained within
                  )";

        $params = [
            $coachId, $date,
            $endTime,   $startTime,
            $endTime,   $startTime,
            $startTime, $endTime,
        ];

        if ($excludeId) {
            $sql     .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $row = $this->queryFirst($sql, $params);
        return ($row['cnt'] ?? 1) == 0;
    }

    /**
     * Generate all bookable time slots for a coach on a given date,
     * marking each one as available or taken.
     */
    public function getAvailableSlots(int $coachId, string $date): array
    {
        // Get the day-of-week availability config
        $dayName = date('l', strtotime($date)); // e.g. "Monday"

        $availSql = "SELECT * FROM coach_availability
                     WHERE coach_id = ? AND day_of_week = ? AND is_available = 1";
        $avail = $this->queryFirst($availSql, [$coachId, $dayName]);

        if (!$avail) {
            return []; // Coach not available that day
        }

        // Get all existing (non-cancelled) bookings for this coach on this date
        $bookedSql = "SELECT start_time, end_time
                      FROM {$this->table}
                      WHERE coach_id = ?
                        AND booking_date = ?
                        AND status NOT IN ('cancelled','no_show')";
        $booked = $this->query($bookedSql, [$coachId, $date])->fetchAll(\PDO::FETCH_ASSOC);

        // Build booked time ranges
        $bookedRanges = array_map(fn($b) => [
            strtotime($date . ' ' . $b['start_time']),
            strtotime($date . ' ' . $b['end_time']),
        ], $booked);

        $slotMins   = (int)($avail['slot_duration'] ?? 60);
        $openTs     = strtotime($date . ' ' . $avail['start_time']);
        $closeTs    = strtotime($date . ' ' . $avail['end_time']);
        $slots      = [];
        $now        = time();

        for ($ts = $openTs; $ts + ($slotMins * 60) <= $closeTs; $ts += $slotMins * 60) {
            $slotEnd = $ts + ($slotMins * 60);

            // Skip past slots
            if ($slotEnd <= $now) {
                continue;
            }

            $isBooked = false;
            foreach ($bookedRanges as [$bs, $be]) {
                if ($ts < $be && $slotEnd > $bs) {
                    $isBooked = true;
                    break;
                }
            }

            $slots[] = [
                'start_time' => date('H:i', $ts),
                'end_time'   => date('H:i', $slotEnd),
                'label'      => date('g:i A', $ts) . ' – ' . date('g:i A', $slotEnd),
                'available'  => !$isBooked,
            ];
        }

        return $slots;
    }

    // ─────────────────────────────────────────────────────────
    // Queries – user perspective
    // ─────────────────────────────────────────────────────────

    /**
     * All bookings belonging to a user (with coach info).
     */
    public function getBookingsByUser(int $userId): array
    {
        $sql = "SELECT
                    cb.*,
                    u.first_name      AS coach_first_name,
                    u.last_name       AS coach_last_name,
                    u.profile_picture AS coach_avatar,
                    c.hourly_rate,
                    c.location        AS coach_location,
                    c.rating          AS coach_rating,
                    sc.name           AS sport_name,
                    cr.id             AS review_id,
                    cr.rating         AS review_rating,
                    cr.review_text    AS review_text
                FROM {$this->table} cb
                JOIN coaches c  ON cb.coach_id = c.id
                JOIN users   u  ON c.user_id   = u.id
                LEFT JOIN sports_categories sc ON c.sport_category_id = sc.id
                LEFT JOIN coach_reviews cr ON cr.booking_id = cb.id AND cr.user_id = ?
                WHERE cb.user_id = ?
                ORDER BY cb.booking_date DESC, cb.start_time DESC";

        $rows = $this->query($sql, [$userId, $userId])->fetchAll(\PDO::FETCH_ASSOC);
        // Add convenient boolean flag
        foreach ($rows as &$r) {
            $r['has_review'] = !empty($r['review_id']);
        }
        return $rows;
    }

    /**
     * Submit a review for a completed coach booking.
     * Returns false if booking not found / not completed / already reviewed.
     */
    public function submitReview(int $bookingId, int $userId, int $coachId, int $rating, string $text): bool
    {
        // Guard: booking must belong to user and be completed
        $booking = $this->queryFirst(
            "SELECT id FROM {$this->table} WHERE id = ? AND user_id = ? AND status = 'completed'",
            [$bookingId, $userId]
        );
        if (!$booking) return false;

        // Guard: one review per booking
        $exists = $this->queryFirst(
            "SELECT id FROM coach_reviews WHERE booking_id = ? AND user_id = ?",
            [$bookingId, $userId]
        );
        if ($exists) return false;

        $this->query(
            "INSERT INTO coach_reviews (coach_id, user_id, booking_id, rating, review_text)
             VALUES (?, ?, ?, ?, ?)",
            [$coachId, $userId, $bookingId, $rating, $text]
        );

        // Recalculate coach aggregate rating
        $this->query(
            "UPDATE coaches
             SET rating        = (SELECT COALESCE(AVG(rating),0) FROM coach_reviews WHERE coach_id = ?),
                 total_reviews = (SELECT COUNT(*)                FROM coach_reviews WHERE coach_id = ?)
             WHERE id = ?",
            [$coachId, $coachId, $coachId]
        );

        return true;
    }

    /**
     * Single booking detail (with coach + user info).
     */
    public function getBookingWithDetails(int $bookingId): ?array
    {
        $sql = "SELECT
                    cb.*,
                    u_user.first_name   AS user_first_name,
                    u_user.last_name    AS user_last_name,
                    u_user.email        AS user_email,
                    u_user.phone        AS user_phone,
                    u_coach.first_name  AS coach_first_name,
                    u_coach.last_name   AS coach_last_name,
                    u_coach.profile_picture AS coach_avatar,
                    c.hourly_rate,
                    sc.name AS sport_name
                FROM {$this->table} cb
                JOIN users   u_user  ON cb.user_id   = u_user.id
                JOIN coaches c       ON cb.coach_id  = c.id
                JOIN users   u_coach ON c.user_id    = u_coach.id
                LEFT JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE cb.id = ?";

        return $this->queryFirst($sql, [$bookingId]);
    }

    // ─────────────────────────────────────────────────────────
    // Queries – coach perspective
    // ─────────────────────────────────────────────────────────

    /**
     * All bookings for a coach (with client info).
     */
    public function getBookingsByCoach(int $coachId): array
    {
        $sql = "SELECT
                    cb.*,
                    u.first_name  AS client_first_name,
                    u.last_name   AS client_last_name,
                    u.email       AS client_email,
                    u.phone       AS client_phone,
                    u.profile_picture AS client_avatar
                FROM {$this->table} cb
                JOIN users u ON cb.user_id = u.id
                WHERE cb.coach_id = ?
                ORDER BY cb.booking_date DESC, cb.start_time DESC";

        return $this->query($sql, [$coachId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Upcoming bookings for a coach (today and future, status confirmed).
     */
    public function getUpcomingBookings(int $coachId, int $limit = 20): array
    {
        $sql = "SELECT
                    cb.*,
                    u.first_name AS client_first_name,
                    u.last_name  AS client_last_name,
                    u.phone      AS client_phone,
                    u.profile_picture AS client_avatar
                FROM {$this->table} cb
                JOIN users u ON cb.user_id = u.id
                WHERE cb.coach_id = ?
                  AND (
                        cb.booking_date > CURDATE()
                     OR (cb.booking_date = CURDATE() AND cb.start_time >= CURTIME())
                  )
                  AND cb.status IN ('confirmed','pending')
                ORDER BY cb.booking_date ASC, cb.start_time ASC
                LIMIT ?";

        return $this->query($sql, [$coachId, $limit])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Today's sessions for a coach.
     */
    public function getTodaySessions(int $coachId): array
    {
        $sql = "SELECT
                    cb.*,
                    u.first_name AS client_first_name,
                    u.last_name  AS client_last_name,
                    u.phone      AS client_phone,
                    u.profile_picture AS client_avatar
                FROM {$this->table} cb
                JOIN users u ON cb.user_id = u.id
                WHERE cb.coach_id    = ?
                  AND cb.booking_date = CURDATE()
                  AND cb.status NOT IN ('cancelled','no_show')
                ORDER BY cb.start_time ASC";

        return $this->query($sql, [$coachId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Week schedule (Mon-Sun of the given week_start date).
     */
    public function getWeekSchedule(int $coachId, string $weekStart): array
    {
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));

        $sql = "SELECT
                    cb.*,
                    u.first_name AS client_first_name,
                    u.last_name  AS client_last_name,
                    u.profile_picture AS client_avatar
                FROM {$this->table} cb
                JOIN users u ON cb.user_id = u.id
                WHERE cb.coach_id    = ?
                  AND cb.booking_date BETWEEN ? AND ?
                  AND cb.status NOT IN ('cancelled','no_show')
                ORDER BY cb.booking_date ASC, cb.start_time ASC";

        return $this->query($sql, [$coachId, $weekStart, $weekEnd])->fetchAll(\PDO::FETCH_ASSOC);
    }

    // ─────────────────────────────────────────────────────────
    // Statistics
    // ─────────────────────────────────────────────────────────

    /**
     * Booking summary stats for a coach.
     */
    public function getCoachBookingStats(int $coachId): array
    {
        $stats = [
            'total_bookings'     => 0,
            'today_sessions'     => 0,
            'upcoming_sessions'  => 0,
            'completed_sessions' => 0,
            'cancelled_sessions' => 0,
            'monthly_earnings'   => 0.0,
            'total_earnings'     => 0.0,
        ];

        try {
            $queries = [
                'total_bookings'     => "SELECT COUNT(*) AS v FROM {$this->table} WHERE coach_id = ?",
                'today_sessions'     => "SELECT COUNT(*) AS v FROM {$this->table} WHERE coach_id = ? AND booking_date = CURDATE() AND status NOT IN ('cancelled','no_show')",
                'upcoming_sessions'  => "SELECT COUNT(*) AS v FROM {$this->table} WHERE coach_id = ? AND booking_date > CURDATE() AND status IN ('confirmed','pending')",
                'completed_sessions' => "SELECT COUNT(*) AS v FROM {$this->table} WHERE coach_id = ? AND status = 'completed'",
                'cancelled_sessions' => "SELECT COUNT(*) AS v FROM {$this->table} WHERE coach_id = ? AND status = 'cancelled'",
                'monthly_earnings'   => "SELECT COALESCE(SUM(total_amount),0) AS v FROM {$this->table} WHERE coach_id = ? AND status = 'completed' AND MONTH(booking_date) = MONTH(CURDATE()) AND YEAR(booking_date) = YEAR(CURDATE())",
                'total_earnings'     => "SELECT COALESCE(SUM(total_amount),0) AS v FROM {$this->table} WHERE coach_id = ? AND status = 'completed'",
            ];

            foreach ($queries as $key => $sql) {
                $row = $this->queryFirst($sql, [$coachId]);
                $stats[$key] = $row['v'] ?? 0;
            }
        } catch (\Exception $e) {
            error_log("CoachBookingStats error for coachId=$coachId: " . $e->getMessage());
        }

        return $stats;
    }

    // ─────────────────────────────────────────────────────────
    // Mutations
    // ─────────────────────────────────────────────────────────

    /**
     * Reschedule a booking (checks availability first).
     * Returns TRUE on success, FALSE if slot is taken.
     */
    public function rescheduleBooking(int $bookingId, string $newDate, string $newStart, string $newEnd): bool
    {
        $booking = $this->find($bookingId);
        if (!$booking) {
            return false;
        }

        if (!$this->isTimeSlotAvailable($booking['coach_id'], $newDate, $newStart, $newEnd, $bookingId)) {
            return false;
        }

        $duration = (int)(abs(strtotime($newEnd) - strtotime($newStart)) / 60);

        return $this->update($bookingId, [
            'booking_date' => $newDate,
            'start_time'   => $newStart,
            'end_time'     => $newEnd,
            'duration'     => $duration,
        ]);
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(int $bookingId, string $cancelledBy = 'user', string $reason = ''): bool
    {
        return $this->update($bookingId, [
            'status'              => 'cancelled',
            'cancelled_by'        => $cancelledBy,
            'cancellation_reason' => $reason,
            'cancelled_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Update booking status.
     */
    public function updateStatus(int $bookingId, string $status): bool
    {
        return $this->update($bookingId, ['status' => $status]);
    }

    // ─────────────────────────────────────────────────────────
    // Earnings
    // ─────────────────────────────────────────────────────────

    /**
     * Resolve a named date range into [startDate, endDate] strings (or nulls for "all").
     */
    public function resolveDateRange(string $range, ?string $customStart = null, ?string $customEnd = null): array
    {
        switch ($range) {
            case 'today':
                $d = date('Y-m-d');
                return [$d, $d];
            case 'week':
                return [date('Y-m-d', strtotime('monday this week')),
                        date('Y-m-d', strtotime('sunday this week'))];
            case 'month':
                return [date('Y-m-01'), date('Y-m-t')];
            case 'lastMonth':
                return [date('Y-m-01', strtotime('first day of last month')),
                        date('Y-m-t',  strtotime('last day of last month'))];
            case 'quarter':
                $m       = (int)date('n');
                $qm      = (int)(($m - 1) / 3) * 3 + 1;
                $qStart  = date('Y-m-01', mktime(0, 0, 0, $qm,     1, (int)date('Y')));
                $qEnd    = date('Y-m-t',  mktime(0, 0, 0, $qm + 2, 1, (int)date('Y')));
                return [$qStart, $qEnd];
            case 'year':
                return [date('Y-01-01'), date('Y-12-31')];
            case 'custom':
                return [$customStart ?: null, $customEnd ?: null];
            default:  // 'all'
                return [null, null];
        }
    }

    /** Previous period of the same length. */
    private function previousPeriod(?string $start, ?string $end): array
    {
        if (!$start || !$end) return [null, null];
        $days    = (int)(abs(strtotime($end) - strtotime($start)) / 86400) + 1;
        $prevEnd = date('Y-m-d', strtotime($start) - 86400);
        $prevSt  = date('Y-m-d', strtotime($prevEnd) - ($days - 1) * 86400);
        return [$prevSt, $prevEnd];
    }

    /**
     * Paginated, filtered list of earnings records.
     */
    public function getEarningsList(int $coachId, array $filters = [], int $page = 1, int $limit = 10): array
    {
        [$start, $end] = $this->resolveDateRange(
            $filters['dateRange'] ?? 'all',
            $filters['startDate'] ?? null,
            $filters['endDate']   ?? null
        );

        $where  = "cb.coach_id = ? AND cb.status != 'cancelled'";
        $params = [$coachId];

        if ($start) { $where .= ' AND cb.booking_date >= ?'; $params[] = $start; }
        if ($end)   { $where .= ' AND cb.booking_date <= ?'; $params[] = $end;   }

        if (!empty($filters['sessionType'])) {
            $where  .= ' AND cb.session_type = ?';
            $params[] = $filters['sessionType'];
        }
        if (!empty($filters['paymentStatus'])) {
            $where  .= ' AND cb.payment_status = ?';
            $params[] = $filters['paymentStatus'];
        }

        $sort = match ($filters['sortBy'] ?? 'date_desc') {
            'date_asc'    => 'cb.booking_date ASC,  cb.start_time ASC',
            'amount_desc' => 'cb.total_amount DESC',
            'amount_asc'  => 'cb.total_amount ASC',
            default       => 'cb.booking_date DESC, cb.start_time DESC',
        };

        // Total count
        $cntRow     = $this->queryFirst("SELECT COUNT(*) AS cnt FROM {$this->table} cb WHERE $where", $params);
        $total      = (int)($cntRow['cnt'] ?? 0);
        $totalPages = max(1, (int)ceil($total / $limit));
        $offset     = ($page - 1) * $limit;

        // Rows
        $rows = $this->query(
            "SELECT cb.id, cb.booking_date, cb.start_time, cb.end_time,
                    cb.session_type, cb.status, cb.payment_status,
                    cb.total_amount, cb.duration, cb.special_requests, cb.coach_notes,
                    CONCAT(u.first_name,' ',u.last_name) AS client_name,
                    u.email AS client_email, u.phone AS client_phone
             FROM {$this->table} cb
             JOIN users u ON cb.user_id = u.id
             WHERE $where
             ORDER BY $sort
             LIMIT ? OFFSET ?",
            array_merge($params, [$limit, $offset])
        )->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['duration_hours'] = $r['duration'] ? round($r['duration'] / 60, 1) : 1;
        }
        unset($r);

        return ['data' => $rows, 'total' => $total, 'totalPages' => $totalPages, 'page' => $page];
    }

    /**
     * Stats summary (current period + percentage change vs previous period).
     */
    public function getEarningsStats(int $coachId, array $filters = []): array
    {
        [$start, $end]       = $this->resolveDateRange($filters['dateRange'] ?? 'all', $filters['startDate'] ?? null, $filters['endDate'] ?? null);
        [$prevSt, $prevEnd]  = $this->previousPeriod($start, $end);

        $sum = function (string $extra, ?string $s, ?string $e) use ($coachId): float {
            $p   = [$coachId];
            $sql = "SELECT COALESCE(SUM(total_amount),0) AS v FROM {$this->table}
                    WHERE coach_id = ? AND status != 'cancelled' $extra";
            if ($s) { $sql .= ' AND booking_date >= ?'; $p[] = $s; }
            if ($e) { $sql .= ' AND booking_date <= ?'; $p[] = $e; }
            return (float)($this->queryFirst($sql, $p)['v'] ?? 0);
        };

        $cnt = function (string $extra, ?string $s, ?string $e) use ($coachId): int {
            $p   = [$coachId];
            $sql = "SELECT COUNT(*) AS v FROM {$this->table}
                    WHERE coach_id = ? AND status != 'cancelled' $extra";
            if ($s) { $sql .= ' AND booking_date >= ?'; $p[] = $s; }
            if ($e) { $sql .= ' AND booking_date <= ?'; $p[] = $e; }
            return (int)($this->queryFirst($sql, $p)['v'] ?? 0);
        };

        $pct = function (float $curr, float $prev): ?float {
            if ($prev == 0) return $curr > 0 ? 100.0 : null;
            return round((($curr - $prev) / $prev) * 100, 1);
        };

        // Coach hourly rate
        $rateRow = $this->queryFirst("SELECT hourly_rate FROM coaches WHERE id = ?", [$coachId]);
        $avgRate = $rateRow ? (float)$rateRow['hourly_rate'] : 0.0;

        $earned     = $sum("AND payment_status = 'paid'",    $start,  $end);
        $prevEarned = $sum("AND payment_status = 'paid'",    $prevSt, $prevEnd);
        $pending    = $sum("AND payment_status = 'pending'", $start,  $end);
        $pendingCnt = $cnt("AND payment_status = 'pending'", $start,  $end);
        $done       = $cnt("AND status = 'completed'",       $start,  $end);
        $prevDone   = $cnt("AND status = 'completed'",       $prevSt, $prevEnd);

        return [
            'total_earnings'     => $earned,
            'earnings_change'    => $pct($earned, $prevEarned),
            'pending_payments'   => $pending,
            'pending_count'      => $pendingCnt,
            'completed_sessions' => $done,
            'completed_change'   => $pct((float)$done, (float)$prevDone),
            'avg_rate'           => $avgRate,
        ];
    }

    /**
     * Monthly trend data for the line chart.
     */
    public function getEarningsTrend(int $coachId, string $period = '6months'): array
    {
        $today = date('Y-m-d');
        $since = match ($period) {
            '12months' => date('Y-m-01', strtotime('-11 months')),
            'year'     => date('Y-01-01'),
            default    => date('Y-m-01', strtotime('-5 months')),  // 6 months incl. current
        };

        $rows = $this->query(
            "SELECT DATE_FORMAT(booking_date,'%b %Y') AS label,
                    YEAR(booking_date) AS yr, MONTH(booking_date) AS mo,
                    COALESCE(SUM(total_amount),0)                 AS total
             FROM {$this->table}
             WHERE coach_id = ? AND status != 'cancelled'
               AND payment_status = 'paid'
               AND booking_date >= ? AND booking_date <= ?
             GROUP BY YEAR(booking_date), MONTH(booking_date)
             ORDER BY YEAR(booking_date), MONTH(booking_date)",
            [$coachId, $since, $today]
        )->fetchAll(\PDO::FETCH_ASSOC);

        // Fill in empty months
        $cursor  = new \DateTime($since);
        $endDt   = new \DateTime(date('Y-m-01'));
        $labels  = [];
        $values  = [];

        while ($cursor <= $endDt) {
            $yr  = (int)$cursor->format('Y');
            $mo  = (int)$cursor->format('n');
            $lbl = $cursor->format('M Y');
            $hit = array_filter($rows, fn($r) => (int)$r['yr'] === $yr && (int)$r['mo'] === $mo);
            $hit = reset($hit);
            $labels[] = $lbl;
            $values[] = $hit ? (float)$hit['total'] : 0.0;
            $cursor->modify('+1 month');
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Earnings breakdown by session type.
     */
    public function getSessionBreakdown(int $coachId): array
    {
        $rows = $this->query(
            "SELECT session_type AS lbl,
                    COALESCE(SUM(total_amount),0) AS val
             FROM {$this->table}
             WHERE coach_id = ? AND payment_status = 'paid' AND status != 'cancelled'
             GROUP BY session_type
             ORDER BY val DESC",
            [$coachId]
        )->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'labels' => array_map(fn($r) => ucfirst($r['lbl'] ?? 'Other'), $rows),
            'values' => array_map(fn($r) => (float)$r['val'],              $rows),
        ];
    }

    /**
     * Single earnings record detail.
     */
    public function getEarningById(int $id, int $coachId): ?array
    {
        $row = $this->queryFirst(
            "SELECT cb.*,
                    CONCAT(u.first_name,' ',u.last_name) AS client_name,
                    u.email AS client_email, u.phone AS client_phone
             FROM {$this->table} cb
             JOIN users u ON cb.user_id = u.id
             WHERE cb.id = ? AND cb.coach_id = ?",
            [$id, $coachId]
        );
        if ($row) {
            $row['duration_hours'] = $row['duration'] ? round($row['duration'] / 60, 1) : 1;
        }
        return $row;
    }
}
