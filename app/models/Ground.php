<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Ground Model
 *
 * Handles ground/facility data
 */
class Ground extends BaseModel
{
    protected string $table = 'grounds';
    
    protected array $fillable = [
        'name',
        'description',
        'type',
        'location',
        'address',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'capacity',
        'hourly_rate',
        'facilities',
        'rules',
        'images',
        'availability',
        'contact_phone',
        'contact_email',
        'owner_id',
        'status'
    ];

    protected array $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'capacity' => 'int',
        'hourly_rate' => 'float',
        'facilities' => 'array',
        'rules' => 'array',
        'images' => 'array',
        'availability' => 'array'
    ];

    /**
     * Get available grounds
     */
    public function getAvailable(): array
    {
        return $this->where(['status' => 'active']);
    }

    /**
     * Get grounds by type
     */
    public function getByType(string $type): array
    {
        return $this->where(['type' => $type, 'status' => 'active']);
    }

    /**
     * Get grounds by location
     */
    public function getByCity(string $city): array
    {
        return $this->where(['city' => $city, 'status' => 'active']);
    }

    /**
     * Search grounds
     */
    public function search(string $query, array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'active' 
                AND (name LIKE ? OR description LIKE ? OR location LIKE ?)";
        
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];
        
        if (isset($filters['type'])) {
            $sql .= " AND type = ?";
            $params[] = $filters['type'];
        }
        
        if (isset($filters['city'])) {
            $sql .= " AND city = ?";
            $params[] = $filters['city'];
        }
        
        if (isset($filters['max_rate'])) {
            $sql .= " AND hourly_rate <= ?";
            $params[] = $filters['max_rate'];
        }
        
        $sql .= " ORDER BY name ASC";
        
        return $this->query($sql, $params);
    }

    /**
     * Get grounds with bookings
     */
    public function getWithBookings(int $id): ?array
    {
        $sql = "SELECT g.*, 
                (SELECT COUNT(*) FROM bookings WHERE facility_id = g.id AND booking_status = 'confirmed') as total_bookings
                FROM {$this->table} g 
                WHERE g.id = ?";
        
        return $this->queryFirst($sql, [$id]);
    }

    /**
     * Get nearby grounds
     */
    public function getNearby(float $latitude, float $longitude, float $radius = 10): array
    {
        $sql = "SELECT *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance 
                FROM {$this->table} 
                WHERE status = 'active'
                HAVING distance < ? 
                ORDER BY distance ASC";
        
        return $this->query($sql, [$latitude, $longitude, $latitude, $radius]);
    }
}