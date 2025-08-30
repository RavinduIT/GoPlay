<?php

namespace App\Models;

use Core\BaseModel;

class SportsCategory extends BaseModel
{
    protected string $table = 'sports_categories';
    
    protected array $fillable = [
        'name',
        'description',
        'icon',
        'image',
        'is_active'
    ];
    
    protected array $casts = [
        'is_active' => 'bool'
    ];
    
    /**
     * Get all active sports categories
     */
    public function getAllActive(): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 
                ORDER BY name ASC";
        
        $statement = $this->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get categories with facility counts
     */
    public function getCategoriesWithFacilityCounts(): array
    {
        $sql = "SELECT sc.*, COUNT(sf.id) as facility_count 
                FROM {$this->table} sc 
                LEFT JOIN sports_facilities sf ON sc.id = sf.sport_category_id AND sf.status = 'active'
                WHERE sc.is_active = 1 
                GROUP BY sc.id 
                ORDER BY sc.name ASC";
        
        $results = $this->query($sql)->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get category by name
     */
    public function getByName(string $name): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE name = ? AND is_active = 1";
        
        $result = $this->query($sql, [$name])->fetch();
        return $result ? $this->castAttributes($result) : null;
    }
    
    /**
     * Get popular categories based on facility bookings
     */
    public function getPopularCategories(int $limit = 6): array
    {
        $sql = "SELECT sc.*, COUNT(fb.id) as booking_count,
                       COUNT(DISTINCT sf.id) as facility_count
                FROM {$this->table} sc 
                LEFT JOIN sports_facilities sf ON sc.id = sf.sport_category_id AND sf.status = 'active'
                LEFT JOIN facility_bookings fb ON sf.id = fb.facility_id 
                WHERE sc.is_active = 1 
                GROUP BY sc.id 
                ORDER BY booking_count DESC, facility_count DESC
                LIMIT ?";
        
        $results = $this->query($sql, [$limit])->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Search categories by name or description
     */
    public function search(string $query): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (name LIKE ? OR description LIKE ?) 
                AND is_active = 1 
                ORDER BY name ASC";
        
        $searchTerm = '%' . $query . '%';
        $results = $this->query($sql, [$searchTerm, $searchTerm])->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
}