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
        'status'
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
                WHERE sf.owner_id = ? AND sf.status != 'inactive'
                ORDER BY sf.created_at DESC";
        
        $statement = $this->query($sql, [$ownerId]);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Create a new facility
     */
    public function create(array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->query($sql, array_values($data));
        return $this->db->lastInsertId();
    }
    
    /**
     * Update a facility
     */
    public function update(int $id, array $data): bool
    {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $params = array_merge(array_values($data), [$id]);
        
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
        // Use subquery to get only the latest record for each facility name (handles duplicates)
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
     * Check if facility is available for booking at given time
     */
    public function isAvailable(int $facilityId, string $date, string $startTime, string $endTime): bool
    {
        $sql = "SELECT COUNT(*) as booking_count 
                FROM facility_bookings 
                WHERE facility_id = ? 
                AND booking_date = ? 
                AND status IN ('confirmed', 'pending')
                AND (
                    (start_time <= ? AND end_time > ?) OR
                    (start_time < ? AND end_time >= ?) OR
                    (start_time >= ? AND start_time < ?)
                )";
        
        $result = $this->query($sql, [
            $facilityId, $date, 
            $startTime, $startTime,
            $endTime, $endTime,
            $startTime, $endTime
        ])->fetch();
        
        return $result['booking_count'] == 0;
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
}