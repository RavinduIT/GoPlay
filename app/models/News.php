<?php

namespace App\Models;

use Core\BaseModel;

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
        'meta_description',
        'views',           // ADDED: Allow views to be updated
        'last_viewed_at'   // ADDED: Track when article was last viewed
    ];

    protected array $casts = [
        'published_at' => 'datetime',
        'last_viewed_at' => 'datetime',  // ADDED: Cast last viewed time
        'tags' => 'array'
    ];

    /**
     * Get published news
     */
    public function getPublished(): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published' 
                AND published_at <= NOW()
                ORDER BY published_at DESC";
        
        $results = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
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
        
        $results = $this->query($sql, [$limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get news by category
     */
    public function getByCategory(string $category): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category = ? 
                AND status = 'published'
                AND published_at <= NOW()
                ORDER BY published_at DESC";
        
        $results = $this->query($sql, [$category])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get news by slug
     */
    public function getBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?";
        $result = $this->queryFirst($sql, [$slug]);
        return $result ? $this->castAttributes($result) : null;
    }

    /**
     * Search news
     */
    public function search(string $query): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published'
                AND published_at <= NOW()
                AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)
                ORDER BY published_at DESC";
        
        $searchTerm = "%{$query}%";
        $results = $this->query($sql, [$searchTerm, $searchTerm, $searchTerm])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
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
        
        $results = $this->query($sql, [$limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
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
                AND published_at <= NOW()
                AND category = ?
                ORDER BY published_at DESC 
                LIMIT ?";
        
        $results = $this->query($sql, [$newsId, $news['category'], $limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get popular news (by views)
     */
    public function getPopular(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published' 
                AND published_at <= NOW()
                ORDER BY views DESC, published_at DESC 
                LIMIT ?";
        
        $results = $this->query($sql, [$limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get news by author
     */
    public function getByAuthor(int $authorId): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE author_id = ? 
                AND status = 'published'
                AND published_at <= NOW()
                ORDER BY published_at DESC";
        
        $results = $this->query($sql, [$authorId])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Increment view count for a news article
     */
    public function incrementViews(int $newsId): bool
    {
        try {
            $sql = "UPDATE {$this->table} 
                    SET views = COALESCE(views, 0) + 1, 
                        last_viewed_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->query($sql, [$newsId]);
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            error_log("Failed to increment views for news ID {$newsId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get trending news (based on recent views and engagement)
     */
    public function getTrending(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published' 
                AND published_at <= NOW()
                AND published_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY views DESC, published_at DESC 
                LIMIT ?";
        
        $results = $this->query($sql, [$limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get news statistics
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_articles,
                    SUM(views) as total_views,
                    AVG(views) as avg_views_per_article,
                    COUNT(CASE WHEN status = 'published' THEN 1 END) as published_articles,
                    COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft_articles
                FROM {$this->table}";
        
        $result = $this->queryFirst($sql);
        return $result ?: [];
    }
}