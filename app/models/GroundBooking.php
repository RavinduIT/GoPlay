<?php

namespace App\Models;

use Core\BaseModel;

/**
 * GroundBooking Model
 *
 * Professional booking system for sports grounds/facilities
 * Works with existing facility_bookings table
 */
class GroundBooking extends BaseModel
{
    protected string $table = 'facility_bookings';

    protected array $fillable = [
        'user_id',
        'facility_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
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
     * Create a new ground booking (no payment required)
     */
    public function createBooking(array $bookingData): ?int
    {
        // Calculate duration
        $startTime = strtotime($bookingData['start_time']);
        $endTime = strtotime($bookingData['end_time']);
        $duration = ($endTime - $startTime) / 3600; // Convert to hours

        // Prepare booking data
        $data = [
            'user_id' => $bookingData['user_id'],
            'facility_id' => $bookingData['facility_id'],
            'booking_date' => $bookingData['booking_date'],
            'start_time' => $bookingData['start_time'],
            'end_time' => $bookingData['end_time'],
            'duration_hours' => $duration,
            'total_amount' => 0.00, // No payment required for ground bookings
            'special_requests' => $bookingData['special_requests'] ?? '',
            'status' => 'confirmed', // Auto-confirm since no payment needed
            'payment_status' => 'paid' // Mark as paid since no payment required
        ];

        return $this->create($data);
    }

    /**
     * Get bookings for a specific ground owner
     */
    public function getBookingsByOwner(int $ownerId): array
    {
        $sql = "SELECT
                    fb.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    sf.name as facility_name,
                    sf.address as facility_address,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} fb
                JOIN users u ON fb.user_id = u.id
                JOIN sports_facilities sf ON fb.facility_id = sf.id
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
                WHERE sf.owner_id = ?
                ORDER BY fb.booking_date DESC, fb.start_time DESC";

        $results = $this->query($sql, [$ownerId])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get bookings for a specific user
     */
    public function getBookingsByUser(int $userId): array
    {
        $sql = "SELECT
                    fb.*,
                    sf.name as facility_name,
                    sf.address as facility_address,
                    CONCAT(sf.city, ', ', sf.state) as facility_location,
                    sc.name as sport_name,
                    sc.icon as sport_icon,
                    u_owner.first_name as owner_first_name,
                    u_owner.last_name as owner_last_name,
                    u_owner.phone as owner_phone
                FROM {$this->table} fb
                JOIN sports_facilities sf ON fb.facility_id = sf.id
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
                LEFT JOIN users u_owner ON sf.owner_id = u_owner.id
                WHERE fb.user_id = ?
                ORDER BY fb.booking_date DESC, fb.start_time DESC";

        $results = $this->query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get bookings for a specific facility
     */
    public function getBookingsByFacility(int $facilityId, ?string $status = null): array
    {
        $sql = "SELECT
                    fb.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone
                FROM {$this->table} fb
                JOIN users u ON fb.user_id = u.id
                WHERE fb.facility_id = ?";

        $params = [$facilityId];

        if ($status) {
            $sql .= " AND fb.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY fb.booking_date DESC, fb.start_time DESC";

        $results = $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Check if facility is available for booking
     */
    public function isTimeSlotAvailable(int $facilityId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE facility_id = ?
                AND booking_date = ?
                AND status NOT IN ('cancelled', 'no_show')
                AND (
                    (start_time <= ? AND end_time > ?) OR
                    (start_time < ? AND end_time >= ?) OR
                    (start_time >= ? AND start_time < ?)
                )";

        $params = [
            $facilityId, $date,
            $startTime, $startTime,
            $endTime, $endTime,
            $startTime, $endTime
        ];

        if ($excludeBookingId) {
            $sql .= " AND id != ?";
            $params[] = $excludeBookingId;
        }

        $result = $this->queryFirst($sql, $params);
        return ($result['count'] ?? 0) == 0;
    }

    /**
     * Cancel booking (can be done by user or ground owner)
     */
    public function cancelBooking(int $bookingId, int $cancelledBy, string $reason = ''): bool
    {
        // Get booking details first
        $booking = $this->find($bookingId);
        if (!$booking) {
            return false;
        }

        // Update booking status
        $updateData = [
            'status' => 'cancelled'
        ];

        // Add cancellation reason if provided
        if (!empty($reason)) {
            $updateData['special_requests'] = ($booking['special_requests'] ?? '') .
                "\n\nCancelled by user ID $cancelledBy: $reason";
        }

        return $this->update($bookingId, $updateData);
    }

    /**
     * Get booking statistics for ground owner dashboard
     */
    public function getOwnerBookingStats(int $ownerId): array
    {
        $stats = [
            'total_bookings' => 0,
            'today_bookings' => 0,
            'upcoming_bookings' => 0,
            'completed_bookings' => 0,
            'cancelled_bookings' => 0,
            'this_month_bookings' => 0
        ];

        try {
            // Total bookings
            $sql = "SELECT COUNT(*) as count
                    FROM {$this->table} fb
                    JOIN sports_facilities sf ON fb.facility_id = sf.id
                    WHERE sf.owner_id = ?";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['total_bookings'] = $result['count'] ?? 0;

            // Today's bookings
            $sql = "SELECT COUNT(*) as count
                    FROM {$this->table} fb
                    JOIN sports_facilities sf ON fb.facility_id = sf.id
                    WHERE sf.owner_id = ? AND fb.booking_date = CURDATE()";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['today_bookings'] = $result['count'] ?? 0;

            // Upcoming bookings
            $sql = "SELECT COUNT(*) as count
                    FROM {$this->table} fb
                    JOIN sports_facilities sf ON fb.facility_id = sf.id
                    WHERE sf.owner_id = ?
                    AND fb.booking_date > CURDATE()
                    AND fb.status IN ('confirmed', 'pending')";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['upcoming_bookings'] = $result['count'] ?? 0;

            // Completed bookings
            $sql = "SELECT COUNT(*) as count
                    FROM {$this->table} fb
                    JOIN sports_facilities sf ON fb.facility_id = sf.id
                    WHERE sf.owner_id = ? AND fb.status = 'completed'";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['completed_bookings'] = $result['count'] ?? 0;

            // Cancelled bookings
            $sql = "SELECT COUNT(*) as count
                    FROM {$this->table} fb
                    JOIN sports_facilities sf ON fb.facility_id = sf.id
                    WHERE sf.owner_id = ? AND fb.status = 'cancelled'";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['cancelled_bookings'] = $result['count'] ?? 0;

            // This month's bookings
            $sql = "SELECT COUNT(*) as count
                    FROM {$this->table} fb
                    JOIN sports_facilities sf ON fb.facility_id = sf.id
                    WHERE sf.owner_id = ?
                    AND MONTH(fb.booking_date) = MONTH(CURDATE())
                    AND YEAR(fb.booking_date) = YEAR(CURDATE())";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['this_month_bookings'] = $result['count'] ?? 0;

        } catch (\Exception $e) {
            error_log("Error fetching booking stats for owner $ownerId: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get user booking statistics
     */
    public function getUserBookingStats(int $userId): array
    {
        $stats = [
            'total_bookings' => 0,
            'upcoming_bookings' => 0,
            'completed_bookings' => 0,
            'cancelled_bookings' => 0
        ];

        try {
            // Total bookings
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
            $result = $this->queryFirst($sql, [$userId]);
            $stats['total_bookings'] = $result['count'] ?? 0;

            // Upcoming bookings
            $sql = "SELECT COUNT(*) as count
                    FROM {$this->table}
                    WHERE user_id = ?
                    AND booking_date > CURDATE()
                    AND status IN ('confirmed', 'pending')";
            $result = $this->queryFirst($sql, [$userId]);
            $stats['upcoming_bookings'] = $result['count'] ?? 0;

            // Completed bookings
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND status = 'completed'";
            $result = $this->queryFirst($sql, [$userId]);
            $stats['completed_bookings'] = $result['count'] ?? 0;

            // Cancelled bookings
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND status = 'cancelled'";
            $result = $this->queryFirst($sql, [$userId]);
            $stats['cancelled_bookings'] = $result['count'] ?? 0;

        } catch (\Exception $e) {
            error_log("Error fetching booking stats for user $userId: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get recent bookings for dashboard
     */
    public function getRecentBookings(int $ownerId, int $limit = 5): array
    {
        $sql = "SELECT
                    fb.*,
                    u.first_name,
                    u.last_name,
                    sf.name as facility_name
                FROM {$this->table} fb
                JOIN users u ON fb.user_id = u.id
                JOIN sports_facilities sf ON fb.facility_id = sf.id
                WHERE sf.owner_id = ?
                ORDER BY fb.created_at DESC
                LIMIT ?";

        $results = $this->query($sql, [$ownerId, $limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get today's bookings for ground owner
     */
    public function getTodayBookings(int $ownerId): array
    {
        $sql = "SELECT
                    fb.*,
                    u.first_name,
                    u.last_name,
                    u.phone,
                    sf.name as facility_name
                FROM {$this->table} fb
                JOIN users u ON fb.user_id = u.id
                JOIN sports_facilities sf ON fb.facility_id = sf.id
                WHERE sf.owner_id = ?
                AND fb.booking_date = CURDATE()
                AND fb.status NOT IN ('cancelled')
                ORDER BY fb.start_time ASC";

        $results = $this->query($sql, [$ownerId])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Search bookings with filters
     */
    public function searchBookings(int $ownerId, array $filters = []): array
    {
        $sql = "SELECT
                    fb.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    sf.name as facility_name
                FROM {$this->table} fb
                JOIN users u ON fb.user_id = u.id
                JOIN sports_facilities sf ON fb.facility_id = sf.id
                WHERE sf.owner_id = ?";

        $params = [$ownerId];

        // Date range filter
        if (!empty($filters['start_date'])) {
            $sql .= " AND fb.booking_date >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND fb.booking_date <= ?";
            $params[] = $filters['end_date'];
        }

        // Status filter
        if (!empty($filters['status'])) {
            $sql .= " AND fb.status = ?";
            $params[] = $filters['status'];
        }

        // Facility filter
        if (!empty($filters['facility_id'])) {
            $sql .= " AND fb.facility_id = ?";
            $params[] = $filters['facility_id'];
        }

        // User search
        if (!empty($filters['user_search'])) {
            $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $filters['user_search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY fb.booking_date DESC, fb.start_time DESC";

        $results = $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }
}