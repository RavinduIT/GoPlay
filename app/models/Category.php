<?php

namespace App\Models;

use Core\BaseModel;

class Category extends BaseModel
{
    protected string $table = 'categories';
    
    protected array $fillable = [
        'name',
        'slug',
        'description',
        'icon'
    ];
    
    /**
     * Get all categories
     */
    public function getAllCategories(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        return $this->query($sql)->fetchAll();
    }
    
    /**
     * Get category by slug
     */
    public function getCategoryBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?";
        $result = $this->db->query($sql, [$slug])->fetch();
        return $result ?: null;
    }
    
    /**
     * Get categories with product counts
     */
    public function getCategoriesWithProductCounts(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 
                GROUP BY c.id 
                ORDER BY c.name ASC";
        
        return $this->query($sql)->fetchAll();
    }
}