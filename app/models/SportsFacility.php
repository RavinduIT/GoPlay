<?php

namespace App\Models;

use Core\BaseModel;

class SportsFacility extends BaseModel
{
    protected string $table = 'sports_facilities';
    
    protected array $fillable = [
        'owner_id',
        'name',
        'description',
        'sport_category_id',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'hourly_rate',
        'capacity',
        'amenities',
        'images',
        'rules',
        'status',
    ];
    
    protected array $casts = [
        'owner_id' => 'int',
        'sport_category_id' => 'int',
        'latitude' => 'float',
        'longitude' => 'float',
        'hourly_rate' => 'float',
        'capacity' => 'int',
        'rating' => 'float',
        'total_reviews' => 'int',
        'amenities' => 'array',
        'images' => 'array'
    ];
    
    /**
     * Get facilities by owner ID
     */
    public function getByOwnerId(int $ownerId): array
    {
        $sql = "SELECT sf.*, sc.name as category_name, sc.icon as category_icon
                FROM {$this->table} sf 
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id 
                WHERE sf.owner_id = ?
                ORDER BY sf.created_at DESC";
        
        $statement = $this->query($sql, [$ownerId]);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Create a new facility — only fillable fields are written.
     */
    public function create(array $data): int
    {
        $data   = array_intersect_key($data, array_flip($this->fillable));
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ")
                VALUES (" . implode(', ', $placeholders) . ")";

        $this->query($sql, array_values($data));
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update a facility — only fillable fields are written.
     */
    public function update(int $id, array $data): bool
    {
        $data   = array_intersect_key($data, array_flip($this->fillable));
        $fields = array_keys($data);

        if (empty($fields)) {
            return false;
        }

        $setClause = implode(' = ?, ', $fields) . ' = ?';
        $sql       = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $params    = array_merge(array_values($data), [$id]);

        $statement = $this->query($sql, $params);
        return $statement && $statement->rowCount() > 0;
    }
    
    /**
     * Find a facility by ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT sf.*, sc.name as category_name, sc.icon as category_icon
                FROM {$this->table} sf 
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id 
                WHERE sf.id = ?";
        
        $result = $this->query($sql, [$id])->fetch();
        return $result ? $this->castAttributes($result) : null;
    }
    
    /**
     * Get available facilities with filters
     */
    public function getAvailableFacilities(array $filters = []): array
    {

        $sql = "SELECT sf.*, sc.name as category_name, sc.icon as category_icon,
                       u.first_name, u.last_name, u.phone
                FROM {$this->table} sf
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
                LEFT JOIN users u ON sf.owner_id = u.id
                WHERE sf.status = 'active'
                AND sf.id IN (
                    SELECT MAX(id)
                    FROM {$this->table}
                    WHERE status = 'active'
                    GROUP BY name, address
                )";
        
        $params = [];
        
        // Add sport category filter
        if (!empty($filters['sport_category'])) {
            $sql .= " AND sf.sport_category_id = ?";
            $params[] = $filters['sport_category'];
        }
        
        // Add city filter
        if (!empty($filters['city'])) {
            $sql .= " AND sf.city LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
        }
        
        // Add rate range filters
        if (!empty($filters['min_rate'])) {
            $sql .= " AND sf.hourly_rate >= ?";
            $params[] = $filters['min_rate'];
        }
        
        if (!empty($filters['max_rate'])) {
            $sql .= " AND sf.hourly_rate <= ?";
            $params[] = $filters['max_rate'];
        }
        
        // Add search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (sf.name LIKE ? OR sf.description LIKE ? OR sf.city LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Add sorting
        $sortOptions = [
            'price_asc' => 'sf.hourly_rate ASC',
            'price_desc' => 'sf.hourly_rate DESC',
            'rating' => 'sf.rating DESC',
            'name' => 'sf.name ASC',
            'distance' => 'sf.city ASC', // Placeholder for distance sorting
            'newest' => 'sf.created_at DESC'
        ];
        
        $sort = $filters['sort'] ?? 'rating';
        $orderBy = $sortOptions[$sort] ?? $sortOptions['rating'];
        $sql .= " ORDER BY {$orderBy}";
        
        // Add limit
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }
        
        $statement = $this->query($sql, $params);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get facility with owner details
     */
    public function getFacilityWithDetails(int $id): ?array
    {
        $sql = "SELECT sf.*, sc.name as category_name, sc.icon as category_icon,
                       u.first_name, u.last_name, u.phone, u.email
                FROM {$this->table} sf 
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id 
                LEFT JOIN users u ON sf.owner_id = u.id
                WHERE sf.id = ? AND sf.status = 'active'";
        
        $result = $this->query($sql, [$id])->fetch();
        return $result ? $this->castAttributes($result) : null;
    }
    
    /**
     * Get facilities by sport category
     */
    public function getByCategoryId(int $categoryId): array
    {
        $sql = "SELECT sf.*, sc.name as category_name, sc.icon as category_icon
                FROM {$this->table} sf 
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id 
                WHERE sf.sport_category_id = ? AND sf.status = 'active'
                ORDER BY sf.rating DESC, sf.created_at DESC";
        
        $results = $this->query($sql, [$categoryId])->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get unique cities for filters
     */
    public function getUniqueCities(): array
    {
        $sql = "SELECT DISTINCT city 
                FROM {$this->table} 
                WHERE status = 'active' AND city IS NOT NULL AND city != ''
                ORDER BY city ASC";
        
        $statement = $this->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_column($results, 'city');
    }
    
    /**
     * Get facilities near location (placeholder for future GPS implementation)
     */
    public function getNearbyFacilities(float $latitude, float $longitude, int $radiusKm = 25): array
    {
        // For now, just return all active facilities
        // Future implementation would use spatial queries
        return $this->getAvailableFacilities(['limit' => 50]);
    }
    
    /**
     * Get popular/featured facilities
     */
    public function getFeaturedFacilities(int $limit = 6): array
    {
        $sql = "SELECT sf.*, sc.name as category_name, sc.icon as category_icon
                FROM {$this->table} sf 
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id 
                WHERE sf.status = 'active' AND sf.rating >= 4.0
                ORDER BY sf.rating DESC, sf.total_reviews DESC
                LIMIT ?";
        
        $results = $this->query($sql, [$limit])->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Update facility rating based on reviews
     */
    public function updateFacilityRating(int $facilityId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET rating = (
                    SELECT ROUND(AVG(rating), 2) 
                    FROM facility_reviews 
                    WHERE facility_id = ?
                ),
                total_reviews = (
                    SELECT COUNT(*) 
                    FROM facility_reviews 
                    WHERE facility_id = ?
                )
                WHERE id = ?";
        
        $statement = $this->query($sql, [$facilityId, $facilityId, $facilityId]);
        return $statement && $statement->rowCount() > 0;
    }
    
    /**
     * Return the first reason a facility is unavailable, or '' if it is free.
     * Checks (in order): maintenance → blocked dates → weekly schedule → existing bookings.
     */
    public function getAvailabilityReason(int $facilityId, string $date, string $startTime, string $endTime): string
    {
        // 1. Active maintenance scheduled for this date
        $row = $this->query(
            "SELECT COUNT(*) as cnt
             FROM facility_maintenance
             WHERE facility_id = ?
               AND status IN ('scheduled','in_progress')
               AND start_date <= ?
               AND (end_date IS NULL OR end_date >= ?)",
            [$facilityId, $date, $date]
        )->fetch(\PDO::FETCH_ASSOC);
        if ($row && (int)$row['cnt'] > 0) {
            return 'Under maintenance';
        }

        // 2. Owner-set blocked date / closure
        $row = $this->query(
            "SELECT reason
             FROM facility_blocked_dates
             WHERE facility_id = ?
               AND start_date <= ?
               AND end_date   >= ?
               AND (
                   start_time IS NULL
                   OR (start_time < ? AND end_time > ?)
               )
             LIMIT 1",
            [$facilityId, $date, $date, $endTime, $startTime]
        )->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            return 'Closed: ' . ucfirst(str_replace('_', ' ', $row['reason']));
        }

        // 3. Weekly availability schedule
        $dayOfWeek = date('l', strtotime($date)); // e.g. 'Monday'
        $row = $this->query(
            "SELECT opening_time, closing_time, is_available
             FROM facility_availability
             WHERE facility_id = ?
               AND day_of_week = ?
             LIMIT 1",
            [$facilityId, $dayOfWeek]
        )->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            if (!(int)$row['is_available']) {
                return 'Closed on ' . $dayOfWeek . 's';
            }
            if ($startTime < $row['opening_time'] || $endTime > $row['closing_time']) {
                $open  = substr($row['opening_time'],  0, 5);
                $close = substr($row['closing_time'],  0, 5);
                return "Outside opening hours ({$open}–{$close})";
            }
        }

        // 4. Conflicting confirmed/pending bookings
        // Standard overlap: existing.start < requested.end AND existing.end > requested.start
        $row = $this->query(
            "SELECT COUNT(*) as cnt
             FROM facility_bookings
             WHERE facility_id   = ?
               AND booking_date  = ?
               AND status IN ('confirmed','pending')
               AND start_time    < ?
               AND end_time      > ?",
            [$facilityId, $date, $endTime, $startTime]
        )->fetch(\PDO::FETCH_ASSOC);
        if ($row && (int)$row['cnt'] > 0) {
            return 'Already booked for this time';
        }

        return '';
    }

    /**
     * Check if facility is available for booking at given time.
     */
    public function isAvailable(int $facilityId, string $date, string $startTime, string $endTime): bool
    {
        return $this->getAvailabilityReason($facilityId, $date, $startTime, $endTime) === '';
    }

    /**
     * Generate 1-hour time slots for a given day.
     *
     * Uses the facility's weekly schedule (facility_availability) for opening/closing
     * times and slot_duration. If no schedule is set for that day, defaults to
     * 06:00–22:00 with 1-hour slots.
     *
     * Each slot has:
     *   start, end          – "HH:MM" strings
     *   start_full, end_full – "HH:MM:SS" for DB queries
     *   status              – available | booked | maintenance | blocked | closed
     *   reason              – human-readable explanation (empty when available)
     */
    public function getSlotsForDay(int $facilityId, string $date): array
    {
        $dayOfWeek = date('l', strtotime($date)); // e.g. 'Monday'

        // Look up the weekly schedule for this day
        $schedule = $this->query(
            "SELECT opening_time, closing_time, slot_duration, is_available
             FROM facility_availability
             WHERE facility_id = ? AND day_of_week = ?
             LIMIT 1",
            [$facilityId, $dayOfWeek]
        )->fetch(\PDO::FETCH_ASSOC);

        // Default hours when no schedule is configured
        $defaultOpen  = '06:00:00';
        $defaultClose = '22:00:00';

        // Closed all day
        if ($schedule && !(int)$schedule['is_available']) {
            return [[
                'start'       => '00:00',
                'end'         => '24:00',
                'start_full'  => '00:00:00',
                'end_full'    => '23:59:59',
                'status'      => 'closed',
                'reason'      => "Closed on {$dayOfWeek}s",
                'all_day'     => true,
            ]];
        }

        $openTime  = $schedule ? $schedule['opening_time']  : $defaultOpen;
        $closeTime = $schedule ? $schedule['closing_time']  : $defaultClose;

        // Always use 1-hour (3600 s) slots per the booking system requirement
        $slotSecs  = 3600;

        $slots   = [];
        $current = strtotime("1970-01-01 {$openTime}");
        $end     = strtotime("1970-01-01 {$closeTime}");

        while ($current + $slotSecs <= $end) {
            $slotStart = date('H:i:s', $current);
            $slotEnd   = date('H:i:s', $current + $slotSecs);

            $reason = $this->getAvailabilityReason($facilityId, $date, $slotStart, $slotEnd);

            if ($reason === '') {
                $status = 'available';
            } elseif (stripos($reason, 'booked') !== false) {
                $status = 'booked';
            } elseif (stripos($reason, 'maintenance') !== false) {
                $status = 'maintenance';
            } elseif (stripos($reason, 'Closed:') === 0) {
                $status = 'blocked';
            } else {
                $status = 'closed';
            }

            $slots[] = [
                'start'      => substr($slotStart, 0, 5),
                'end'        => substr($slotEnd,   0, 5),
                'start_full' => $slotStart,
                'end_full'   => $slotEnd,
                'status'     => $status,
                'reason'     => $reason,
            ];

            $current += $slotSecs;
        }

        return $slots;
    }
    
    /**
     * Get facility reviews
     */
    public function getFacilityReviews(int $facilityId): array
    {
        $sql = "SELECT fr.*, u.first_name, u.last_name, u.profile_picture
                FROM facility_reviews fr
                LEFT JOIN users u ON fr.user_id = u.id
                WHERE fr.facility_id = ?
                ORDER BY fr.created_at DESC";
        
        return $this->query($sql, [$facilityId])->fetchAll();
    }

    /**
     * Get detailed facility information for the details page
     */
    public function getDetailedFacility(int $id): ?array
    {
        try {
            // Get basic facility info with category and owner details
            $sql = "SELECT sf.*, 
                           sc.name as category_name, 
                           sc.icon as category_icon,
                           u.first_name, 
                           u.last_name, 
                           u.phone, 
                           u.email,
                           COUNT(DISTINCT fr.id) as total_reviews,
                           ROUND(AVG(fr.rating), 1) as avg_rating,
                           COUNT(DISTINCT b.id) as total_bookings
                    FROM {$this->table} sf 
                    LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id 
                    LEFT JOIN users u ON sf.owner_id = u.id
                    LEFT JOIN facility_reviews fr ON sf.id = fr.facility_id
                    LEFT JOIN facility_bookings b ON sf.id = b.facility_id AND b.status = 'completed'
                    WHERE sf.id = ? AND sf.status = 'active'
                    GROUP BY sf.id";
            
            $result = $this->query($sql, [$id])->fetch(\PDO::FETCH_ASSOC);
            
            if (!$result) {
                return null;
            }

            // Cast attributes and enhance data
            $facility = $this->castAttributes($result);
            
            // Add computed fields
            $facility['rating'] = $result['avg_rating'] ? floatval($result['avg_rating']) : 4.5;
            $facility['reviews'] = intval($result['total_reviews']);
            $facility['total_bookings'] = intval($result['total_bookings']);
            
            // Ensure images array exists
            if (!isset($facility['images']) || !is_array($facility['images'])) {
                $facility['images'] = ['/public/assets/images/ground.jpeg'];
            }
            
            // Ensure amenities array exists
            if (!isset($facility['amenities']) || !is_array($facility['amenities'])) {
                $facility['amenities'] = ['Parking', 'Changing Rooms', 'Equipment Rental'];
            }
            
            // Add availability schedule (default)
            $facility['availability'] = [
                'monday' => ['6:00 AM - 10:00 PM'],
                'tuesday' => ['6:00 AM - 10:00 PM'],
                'wednesday' => ['6:00 AM - 10:00 PM'],
                'thursday' => ['6:00 AM - 10:00 PM'],
                'friday' => ['6:00 AM - 10:00 PM'],
                'saturday' => ['7:00 AM - 9:00 PM'],
                'sunday' => ['8:00 AM - 8:00 PM']
            ];
            
            // Get recent reviews
            $facility['recent_reviews'] = $this->getFacilityReviews($id);

            return $facility;

        } catch (\Exception $e) {
            error_log("Error fetching detailed facility: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get active coaches linked to a facility.
     */
    public function getLinkedCoaches(int $facilityId): array
    {
        $sql = "SELECT cf.is_primary,
                       c.id AS coach_id,
                       c.hourly_rate, c.rating, c.experience_years, c.location,
                       u.first_name, u.last_name, u.profile_picture,
                       sc.name AS sport_name, sc.icon AS sport_icon
                FROM coach_facilities cf
                JOIN coaches c  ON cf.coach_id = c.id
                JOIN users u    ON c.user_id = u.id
                LEFT JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE cf.facility_id = ?
                  AND cf.status = 'approved'
                  AND c.status = 'active'
                ORDER BY cf.is_primary DESC, c.rating DESC";

        return $this->query($sql, [$facilityId])->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    // Admin: all grounds with owner info, optional status filter
    public function getAllForAdmin(string $status = '', string $search = ''): array
    {
        $sql = "SELECT sf.*, sc.name AS category_name,
                       CONCAT(u.first_name,' ',u.last_name) AS owner_name,
                       u.email AS owner_email, u.phone AS owner_phone
                FROM {$this->table} sf
                LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
                LEFT JOIN users u ON sf.owner_id = u.id
                WHERE 1=1";
        $params = [];
        if ($status) { $sql .= " AND sf.status = ?"; $params[] = $status; }
        if ($search) { $sql .= " AND (sf.name LIKE ? OR sf.city LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)"; $s = "%{$search}%"; $params = array_merge($params, [$s,$s,$s,$s]); }
        $sql .= " ORDER BY sf.created_at DESC";
        return $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    // Admin: set status
    public function setStatus(int $id, string $status): bool
    {
        return (bool)$this->query("UPDATE {$this->table} SET status=? WHERE id=?", [$status, $id])->rowCount();
    }

    // Admin: stats
    public function getAdminStats(): array
    {
        $row = $this->queryFirst(
            "SELECT
                COUNT(*) AS total,
                SUM(status='active') AS active,
                SUM(status='pending_review') AS pending,
                SUM(status='inactive') AS inactive,
                SUM(status='maintenance') AS maintenance
             FROM {$this->table}", []
        );
        return $row ?? [];
    }
}