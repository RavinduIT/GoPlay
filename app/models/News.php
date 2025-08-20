<?php

namespace App\Models;

/**
 * News Model
 * 
 * Handles news and articles data
 */
class News extends BaseModel
{
    protected string $table = 'news';
    
    protected array $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'category',
        'tags',
        'author_id',
        'status',
        'published_at',
        'meta_title',
        'meta_description'
    ];

    protected array $casts = [
        'published_at' => 'datetime',
        'tags' => 'array'
    ];

    /**
     * Get published news
     */
    public function getPublished(): array
    {
        return $this->where([
            'status' => 'published',
            'published_at <=' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get featured news
     */
    public function getFeatured(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published' 
                AND published_at <= NOW() 
                AND featured_image IS NOT NULL
                ORDER BY published_at DESC 
                LIMIT ?";
        
        return $this->query($sql, [$limit]);
    }

    /**
     * Get news by category
     */
    public function getByCategory(string $category): array
    {
        return $this->where([
            'category' => $category,
            'status' => 'published'
        ]);
    }

    /**
     * Get news by slug
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->where(['slug' => $slug])[0] ?? null;
    }

    /**
     * Search news
     */
    public function search(string $query): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published'
                AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)
                ORDER BY published_at DESC";
        
        return $this->query($sql, ["%{$query}%", "%{$query}%", "%{$query}%"]);
    }

    /**
     * Get recent news
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published' 
                AND published_at <= NOW()
                ORDER BY published_at DESC 
                LIMIT ?";
        
        return $this->query($sql, [$limit]);
    }

    /**
     * Get related news
     */
    public function getRelated(int $newsId, int $limit = 5): array
    {
        $news = $this->find($newsId);
        if (!$news) return [];
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE id != ? 
                AND status = 'published'
                AND (category = ? OR tags LIKE ?)
                ORDER BY published_at DESC 
                LIMIT ?";
        
        return $this->query($sql, [
            $newsId, 
            $news['category'], 
            '%' . ($news['tags'][0] ?? '') . '%',
            $limit
        ]);
    }
}