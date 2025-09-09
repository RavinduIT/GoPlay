<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Coach Model
 * 
 * Handles coach data and operations
 */
class Coach extends BaseModel
{
    protected string $table = 'coaches';
    
    protected array $fillable = [
        'user_id',
        'sport_category_id',
        'experience_years',
        'hourly_rate',
        'bio',
        'specializations',
        'certifications',
        'rating',
        'total_reviews',
        'total_sessions',
        'availability_schedule',
        'location',
        'status'
    ];

    protected array $casts = [
        'experience_years' => 'int',
        'hourly_rate' => 'float',
        'availability_schedule' => 'array',
        'rating' => 'float',
        'total_reviews' => 'int',
        'total_sessions' => 'int'
    ];

    /**
     * Get available coaches with user and sport category details
     */
    public function getAvailable(): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active' 
                ORDER BY c.rating DESC, c.total_reviews DESC";
        
        $results = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get coaches by sport category
     */
    public function getBySport(string $sportName): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active' AND sc.name = ?
                ORDER BY c.rating DESC, c.total_reviews DESC";
        
        $results = $this->query($sql, [$sportName])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get top rated coaches
     */
    public function getTopRated(int $limit = 10): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active' 
                ORDER BY c.rating DESC, c.total_sessions DESC 
                LIMIT ?";
        
        $results = $this->query($sql, [$limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Search coaches with filters
     */
    public function search(string $query = '', array $filters = []): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active'";
        
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR c.bio LIKE ? OR sc.name LIKE ? OR c.specializations LIKE ?)";
            $queryParam = "%{$query}%";
            $params = array_fill(0, 5, $queryParam);
        }
        
        if (isset($filters['sport']) && !empty($filters['sport'])) {
            $sql .= " AND sc.name = ?";
            $params[] = $filters['sport'];
        }
        
        if (isset($filters['experience']) && !empty($filters['experience'])) {
            if ($filters['experience'] === '1-3') {
                $sql .= " AND c.experience_years BETWEEN 1 AND 3";
            } elseif ($filters['experience'] === '3-5') {
                $sql .= " AND c.experience_years BETWEEN 3 AND 5";
            } elseif ($filters['experience'] === '5+') {
                $sql .= " AND c.experience_years >= 5";
            }
        }
        
        if (isset($filters['price']) && !empty($filters['price'])) {
            if ($filters['price'] === '0-2000') {
                $sql .= " AND c.hourly_rate BETWEEN 0 AND 2000";
            } elseif ($filters['price'] === '2000-4000') {
                $sql .= " AND c.hourly_rate BETWEEN 2000 AND 4000";
            } elseif ($filters['price'] === '4000+') {
                $sql .= " AND c.hourly_rate >= 4000";
            }
        }
        
        $sql .= " ORDER BY c.rating DESC, c.total_reviews DESC";
        
        $results = $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get coach with full details
     */
    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.id = ?";
        
        $result = $this->queryFirst($sql, [$id]);
        return $result ? $this->castAttributes($result) : null;
    }

    /**
     * Get coach bookings
     */
    public function getBookings(int $coachId): array
    {
        $sql = "SELECT cb.*, u.first_name, u.last_name 
                FROM coach_bookings cb 
                JOIN users u ON cb.user_id = u.id 
                WHERE cb.coach_id = ? 
                ORDER BY cb.booking_date DESC, cb.start_time DESC";
        
        $results = $this->query($sql, [$coachId])->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get coach reviews
     */
    public function getReviews(int $coachId): array
    {
        $sql = "SELECT cr.*, u.first_name, u.last_name 
                FROM coach_reviews cr 
                JOIN users u ON cr.user_id = u.id 
                WHERE cr.coach_id = ? 
                ORDER BY cr.created_at DESC";
        
        $results = $this->query($sql, [$coachId])->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * Update coach rating based on reviews
     */
    public function updateRating(int $coachId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET rating = (
                    SELECT COALESCE(AVG(rating), 0) 
                    FROM coach_reviews 
                    WHERE coach_id = ?
                ),
                total_reviews = (
                    SELECT COUNT(*) 
                    FROM coach_reviews 
                    WHERE coach_id = ?
                ),
                total_sessions = (
                    SELECT COUNT(*) 
                    FROM coach_bookings 
                    WHERE coach_id = ? AND status = 'completed'
                )
                WHERE id = ?";
        
        $statement = $this->query($sql, [$coachId, $coachId, $coachId, $coachId]);
        return $statement && $statement->rowCount() > 0;
    }

    /**
     * Get all sports categories
     */
    public function getSportsCategories(): array
    {
        $sql = "SELECT * FROM sports_categories WHERE is_active = 1 ORDER BY name";
        $results = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }
}