<?php

namespace App\Models;

use Core\BaseModel;

/**
 * CoachBooking Model
 *
 * Simple CRUD operations for coach session bookings
 */
class CoachBooking extends BaseModel
{
    protected string $table = 'coach_bookings';

    protected array $fillable = [
        'user_id',
        'coach_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
        'session_type',
        'total_amount',
        'special_requests',
        'status',
        'payment_status'
    ];

    protected array $casts = [
        'booking_date' => 'date',
        'duration_hours' => 'float',
        'total_amount' => 'float'
    ];

    /**
     * Create a new coach booking
     *
     * @param array $bookingData Booking information
     * @return int|null Booking ID if successful
     */
    public function createBooking(array $bookingData): ?int
    {
        // Calculate duration in hours
        $startTime = strtotime($bookingData['start_time']);
        $endTime = strtotime($bookingData['end_time']);
        $duration = ($endTime - $startTime) / 3600;

        // Prepare booking data
        $data = [
            'user_id' => $bookingData['user_id'],
            'coach_id' => $bookingData['coach_id'],
            'booking_date' => $bookingData['booking_date'],
            'start_time' => $bookingData['start_time'],
            'end_time' => $bookingData['end_time'],
            'duration_hours' => $duration,
            'session_type' => $bookingData['session_type'] ?? 'individual',
            'total_amount' => $bookingData['total_amount'],
            'special_requests' => $bookingData['special_requests'] ?? '',
            'status' => 'pending', // Pending until payment
            'payment_status' => 'pending'
        ];

        return $this->create($data);
    }

    /**
     * Get all bookings for a specific user
     *
     * @param int $userId User ID
     * @return array List of bookings
     */
    public function getBookingsByUser(int $userId): array
    {
        $sql = "SELECT
                    cb.*,
                    u.first_name as coach_first_name,
                    u.last_name as coach_last_name,
                    c.hourly_rate,
                    c.rating as coach_rating,
                    c.location as coach_location,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} cb
                JOIN coaches c ON cb.coach_id = c.id
                JOIN users u ON c.user_id = u.id
                LEFT JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE cb.user_id = ?
                ORDER BY cb.booking_date DESC, cb.start_time DESC";

        $results = $this->query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get all bookings for a specific coach
     *
     * @param int $coachId Coach ID
     * @return array List of bookings
     */
    public function getBookingsByCoach(int $coachId): array
    {
        $sql = "SELECT
                    cb.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone
                FROM {$this->table} cb
                JOIN users u ON cb.user_id = u.id
                WHERE cb.coach_id = ?
                ORDER BY cb.booking_date DESC, cb.start_time DESC";

        $results = $this->query($sql, [$coachId])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Check if a time slot is available for booking
     *
     * @param int $coachId Coach ID
     * @param string $date Booking date (YYYY-MM-DD)
     * @param string $startTime Start time (HH:MM)
     * @param string $endTime End time (HH:MM)
     * @return bool True if available
     */
    public function isTimeSlotAvailable(int $coachId, string $date, string $startTime, string $endTime): bool
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE coach_id = ?
                AND booking_date = ?
                AND status NOT IN ('cancelled')
                AND (
                    (start_time < ? AND end_time > ?)
                    OR (start_time < ? AND end_time > ?)
                    OR (start_time >= ? AND end_time <= ?)
                )";

        $params = [$coachId, $date, $endTime, $startTime, $endTime, $endTime, $startTime, $endTime];
        $result = $this->queryFirst($sql, $params);

        return $result['count'] == 0;
    }

    /**
     * Update booking status
     *
     * @param int $bookingId Booking ID
     * @param string $status New status
     * @return bool Success
     */
    public function updateStatus(int $bookingId, string $status): bool
    {
        return $this->update($bookingId, ['status' => $status]);
    }

    /**
     * Cancel a booking
     *
     * @param int $bookingId Booking ID
     * @return bool Success
     */
    public function cancelBooking(int $bookingId): bool
    {
        return $this->update($bookingId, ['status' => 'cancelled']);
    }

    /**
     * Get booking statistics for a coach
     *
     * @param int $coachId Coach ID
     * @return array Statistics
     */
    public function getCoachBookingStats(int $coachId): array
    {
        $sql = "SELECT
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                    SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_earnings
                FROM {$this->table}
                WHERE coach_id = ?";

        return $this->queryFirst($sql, [$coachId]) ?? [];
    }
}
