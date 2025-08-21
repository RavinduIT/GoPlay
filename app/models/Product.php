<?php

namespace App\Models;

use Core\BaseModel;

class Product extends BaseModel
{
    protected string $table = 'products';
    
    protected array $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'category_id',
        'price',
        'original_price',
        'discount_percentage',
        'sku',
        'stock_quantity',
        'min_stock_level',
        'weight',
        'dimensions',
        'brand',
        'model',
        'color',
        'size',
        'material',
        'image_url',
        'gallery',
        'features',
        'specifications',
        'rating',
        'review_count',
        'is_featured',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];
    
    protected array $casts = [
        'price' => 'float',
        'original_price' => 'float',
        'discount_percentage' => 'int',
        'stock_quantity' => 'int',
        'min_stock_level' => 'int',
        'weight' => 'float',
        'rating' => 'float',
        'review_count' => 'int',
        'is_featured' => 'bool',
        'is_active' => 'bool',
        'gallery' => 'array',
        'features' => 'array',
        'specifications' => 'array'
    ];
    
    /**
     * Get all active products with optional filtering
     */
    public function getActiveProducts(array $filters = []): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1";
        
        $params = [];
        
        // Add category filter
        if (!empty($filters['category'])) {
            $sql .= " AND c.slug = ?";
            $params[] = $filters['category'];
        }
        
        // Add search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Add price range filter
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        // Add featured filter
        if (!empty($filters['featured'])) {
            $sql .= " AND p.is_featured = 1";
        }
        
        // Add sorting
        $sortOptions = [
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'rating' => 'p.rating DESC',
            'newest' => 'p.created_at DESC',
            'name' => 'p.name ASC',
            'featured' => 'p.is_featured DESC, p.rating DESC'
        ];
        
        $sort = $filters['sort'] ?? 'featured';
        $orderBy = $sortOptions[$sort] ?? $sortOptions['featured'];
        $sql .= " ORDER BY {$orderBy}";
        
        // Add limit
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = (int)$filters['limit'];
        }
        
        $results = $this->query($sql, $params)->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get featured products
     */
    public function getFeaturedProducts(int $limit = 8): array
    {
        return $this->getActiveProducts(['featured' => true, 'limit' => $limit]);
    }
    
    /**
     * Get products by category
     */
    public function getProductsByCategory(string $categorySlug, int $limit = null): array
    {
        $filters = ['category' => $categorySlug];
        if ($limit) {
            $filters['limit'] = $limit;
        }
        return $this->getActiveProducts($filters);
    }
    
    /**
     * Search products
     */
    public function searchProducts(string $query, array $filters = []): array
    {
        $filters['search'] = $query;
        return $this->getActiveProducts($filters);
    }
    
    /**
     * Get product with category details
     */
    public function getProductWithCategory(int $id): ?array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ? AND p.is_active = 1";
        
        $result = $this->db->query($sql, [$id])->fetch();
        return $result ? $this->castAttributes($result) : null;
    }
    
    /**
     * Get related products (same category, different product)
     */
    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 4): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
                ORDER BY p.rating DESC, p.created_at DESC 
                LIMIT ?";
        
        $results = $this->query($sql, [$categoryId, $productId, $limit])->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get product reviews
     */
    public function getProductReviews(int $productId): array
    {
        $sql = "SELECT * FROM product_reviews 
                WHERE product_id = ? AND is_approved = 1 
                ORDER BY created_at DESC";
        
        return $this->query($sql, [$productId])->fetchAll();
    }
    
    /**
     * Update product rating based on reviews
     */
    public function updateProductRating(int $productId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET rating = (
                    SELECT ROUND(AVG(rating), 2) 
                    FROM product_reviews 
                    WHERE product_id = ? AND is_approved = 1
                ),
                review_count = (
                    SELECT COUNT(*) 
                    FROM product_reviews 
                    WHERE product_id = ? AND is_approved = 1
                )
                WHERE id = ?";
        
        $statement = $this->query($sql, [$productId, $productId, $productId]);
        return $statement && $statement->rowCount() > 0;
    }
    
    /**
     * Check if product is in stock
     */
    public function isInStock(int $productId, int $quantity = 1): bool
    {
        $product = $this->find($productId);
        return $product && $product['stock_quantity'] >= $quantity;
    }
    
    /**
     * Reduce stock quantity
     */
    public function reduceStock(int $productId, int $quantity): bool
    {
        $sql = "UPDATE {$this->table} 
                SET stock_quantity = stock_quantity - ? 
                WHERE id = ? AND stock_quantity >= ?";
        
        $statement = $this->query($sql, [$quantity, $productId, $quantity]);
        return $statement && $statement->rowCount() > 0;
    }
    
    /**
     * Get low stock products
     */
    public function getLowStockProducts(): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE stock_quantity <= min_stock_level AND is_active = 1 
                ORDER BY stock_quantity ASC";
        
        $results = $this->query($sql)->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get categories with product counts
     */
    public function getCategoriesWithCounts(): array
    {
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN {$this->table} p ON c.id = p.category_id AND p.is_active = 1 
                GROUP BY c.id 
                ORDER BY c.name ASC";
        
        return $this->query($sql)->fetchAll();
    }
}