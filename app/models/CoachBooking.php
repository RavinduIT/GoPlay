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
            'status'           => 'confirmed',
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
                    u.first_name   AS coach_first_name,
                    u.last_name    AS coach_last_name,
                    u.profile_picture AS coach_avatar,
                    c.hourly_rate,
                    c.location     AS coach_location,
                    sc.name        AS sport_name
                FROM {$this->table} cb
                JOIN coaches c  ON cb.coach_id = c.id
                JOIN users   u  ON c.user_id   = u.id
                LEFT JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE cb.user_id = ?
                ORDER BY cb.booking_date DESC, cb.start_time DESC";

        return $this->query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
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
}
