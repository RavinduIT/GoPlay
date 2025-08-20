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
        'sport_specialization',
        'experience_years',
        'certification',
        'hourly_rate',
        'availability',
        'description',
        'skills',
        'languages',
        'location',
        'rating',
        'total_sessions',
        'status'
    ];

    protected array $casts = [
        'experience_years' => 'int',
        'hourly_rate' => 'float',
        'availability' => 'array',
        'skills' => 'array',
        'languages' => 'array',
        'rating' => 'float',
        'total_sessions' => 'int'
    ];

    /**
     * Get available coaches
     */
    public function getAvailable(): array
    {
        return $this->where(['status' => 'active']);
    }

    /**
     * Get coaches by sport
     */
    public function getBySport(string $sport): array
    {
        return $this->where(['sport_specialization' => $sport, 'status' => 'active']);
    }

    /**
     * Get top rated coaches
     */
    public function getTopRated(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'active' 
                ORDER BY rating DESC, total_sessions DESC 
                LIMIT ?";
        
        return $this->query($sql, [$limit]);
    }

    /**
     * Search coaches
     */
    public function search(string $query, array $filters = []): array
    {
        $sql = "SELECT c.*, u.name, u.avatar 
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.status = 'active' 
                AND (u.name LIKE ? OR c.description LIKE ? OR c.sport_specialization LIKE ?)";
        
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];
        
        if (isset($filters['sport'])) {
            $sql .= " AND c.sport_specialization = ?";
            $params[] = $filters['sport'];
        }
        
        if (isset($filters['max_rate'])) {
            $sql .= " AND c.hourly_rate <= ?";
            $params[] = $filters['max_rate'];
        }
        
        if (isset($filters['min_rating'])) {
            $sql .= " AND c.rating >= ?";
            $params[] = $filters['min_rating'];
        }
        
        $sql .= " ORDER BY c.rating DESC";
        
        return $this->query($sql, $params);
    }

    /**
     * Get coach with user details
     */
    public function getWithUser(int $id): ?array
    {
        $sql = "SELECT c.*, u.name, u.email, u.phone, u.avatar 
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ?";
        
        return $this->queryFirst($sql, [$id]);
    }

    /**
     * Get coach bookings
     */
    public function getBookings(int $coachId): array
    {
        $sql = "SELECT b.*, u.name as customer_name 
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                WHERE b.coach_id = ? 
                ORDER BY b.booking_date DESC, b.start_time DESC";
        
        return $this->query($sql, [$coachId]);
    }

    /**
     * Update coach rating
     */
    public function updateRating(int $coachId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET rating = (
                    SELECT AVG(rating) 
                    FROM coach_reviews 
                    WHERE coach_id = ?
                ),
                total_sessions = (
                    SELECT COUNT(*) 
                    FROM bookings 
                    WHERE coach_id = ? AND booking_status = 'completed'
                )
                WHERE id = ?";
        
        $statement = $this->query($sql, [$coachId, $coachId, $coachId]);
        return $statement->rowCount() > 0;
    }
}