<?php

namespace App\Models;

use Core\BaseModel;

class Product extends BaseModel
{
    protected string $table = 'products';
    
    protected array $fillable = [
        'name',
        'sku',
        'description',
        'short_description',
        'category_id',
        'shop_owner_id',
        'brand',
        'price',
        'compare_price',
        'cost_price',
        'stock_quantity',
        'min_stock_level',
        'weight',
        'dimensions',
        'specifications',
        'features',
        'images',
        'tags',
        'status',
        'rating',
        'total_reviews',
        'total_sales'
    ];
    
    protected array $casts = [
        'id' => 'int',
        'category_id' => 'int',
        'shop_owner_id' => 'int',
        'price' => 'float',
        'compare_price' => 'float',
        'cost_price' => 'float',
        'stock_quantity' => 'int',
        'min_stock_level' => 'int',
        'weight' => 'float',
        'rating' => 'float',
        'total_reviews' => 'int',
        'total_sales' => 'int',
        'specifications' => 'array',
        'features' => 'array',
        'images' => 'array',
        'tags' => 'array'
    ];
    
    /**
     * Get all active products with optional filtering
     */
    public function getActiveProducts(array $filters = []): array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 'active'";
        
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
            $sql .= " AND p.rating >= 4.5";
        }
        
        // Add sorting
        $sortOptions = [
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'rating' => 'p.rating DESC',
            'newest' => 'p.created_at DESC',
            'name' => 'p.name ASC',
            'featured' => 'p.rating DESC, p.created_at DESC'
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
    public function getProductsByCategory(string $categorySlug, ?int $limit = null): array
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
                WHERE p.id = ? AND p.status = 'active'";
        
        $result = $this->query($sql, [$id])->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        // Cast attributes and ensure arrays are properly handled
        $product = $this->castAttributes($result);
        
        // Ensure images is an array
        if (!is_array($product['images'])) {
            $product['images'] = $product['images'] ? [$product['images']] : [];
        }
        
        // Add original_price if compare_price exists
        if ($product['compare_price'] && $product['compare_price'] > $product['price']) {
            $product['original_price'] = $product['compare_price'];
        }
        
        // Set default rating if not set
        if (!$product['rating']) {
            $product['rating'] = 4.5;
        }
        
        return $product;
    }
    
    /**
     * Get product with category and shop owner details
     */
    public function getProductWithShopOwner(int $id): ?array
    {
        $sql = "SELECT p.*, 
                       c.name as category_name, c.slug as category_slug,
                       sop.shop_name, sop.business_name, sop.business_description,
                       sop.business_phone, sop.business_email, sop.shop_address,
                       sop.shop_city, sop.shop_state, sop.shop_postal_code,
                       sop.shop_logo, sop.website_url, sop.facebook, sop.instagram,
                       sop.twitter, sop.operating_hours, sop.delivery_options,
                       sop.total_products, sop.total_sales, sop.average_rating,
                       sop.total_reviews, sop.return_policy, sop.warranty_policy,
                       u.first_name as owner_first_name, u.last_name as owner_last_name,
                       u.email as owner_email
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN shop_owner_profiles sop ON p.shop_owner_id = sop.user_id
                LEFT JOIN users u ON sop.user_id = u.id
                WHERE p.id = ? AND p.status = 'active'";
        
        $result = $this->query($sql, [$id])->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        // Cast attributes and ensure arrays are properly handled
        $product = $this->castAttributes($result);
        
        // Ensure images is an array
        if (!is_array($product['images'])) {
            $product['images'] = $product['images'] ? [$product['images']] : [];
        }
        
        // Add original_price if compare_price exists
        if ($product['compare_price'] && $product['compare_price'] > $product['price']) {
            $product['original_price'] = $product['compare_price'];
        }
        
        // Set default rating if not set
        if (!$product['rating']) {
            $product['rating'] = 4.5;
        }
        
        // Parse JSON fields from shop owner profile
        if (!empty($product['operating_hours']) && is_string($product['operating_hours'])) {
            $product['operating_hours'] = json_decode($product['operating_hours'], true);
        }
        if (!empty($product['delivery_options']) && is_string($product['delivery_options'])) {
            $product['delivery_options'] = json_decode($product['delivery_options'], true);
        }
        
        return $product;
    }
    
    /**
     * Get related products (same category, different product)
     */
    public function getRelatedProducts(int $productId, int $categoryId, int $limit = 4): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
                ORDER BY p.rating DESC, p.created_at DESC 
                LIMIT ?";
        
        $results = $this->query($sql, [$categoryId, $productId, $limit])->fetchAll();
        return array_map([$this, 'castAttributes'], $results);
    }
    
    /**
     * Get product reviews
     */
    public function getProductReviews(int $productId, int $limit = 10): array
    {
        // Check if the product_reviews table exists first
        try {
            $sql = "SELECT pr.*, u.first_name, u.last_name, 
                           CONCAT(u.first_name, ' ', u.last_name) as user_name
                    FROM product_reviews pr
                    LEFT JOIN users u ON pr.user_id = u.id
                    WHERE pr.product_id = ?
                    ORDER BY pr.created_at DESC
                    LIMIT ?";
            
            $results = $this->query($sql, [$productId, $limit])->fetchAll(\PDO::FETCH_ASSOC);
            return $results ?: [];
            
        } catch (\Exception $e) {
            // If table doesn't exist or has different structure, return empty array
            return [];
        }
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
                WHERE stock_quantity <= min_stock_level AND status = 'active' 
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
                LEFT JOIN {$this->table} p ON c.id = p.category_id AND p.status = 'active' 
                GROUP BY c.id 
                ORDER BY c.name ASC";
        
        return $this->query($sql)->fetchAll();
    }



    /**
     * Get products by shop owner with filters
     */
    public function getProductsByShopOwner(int $shopOwnerId, array $filters = []): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE (p.shop_owner_id = ? OR p.shop_owner_id IS NULL)";
        
        $params = [$shopOwnerId];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.brand LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        if (!empty($filters['category'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['stock'])) {
            if ($filters['stock'] === 'in-stock') {
                $sql .= " AND p.stock_quantity > p.min_stock_level";
            } elseif ($filters['stock'] === 'low-stock') {
                $sql .= " AND p.stock_quantity > 0 AND p.stock_quantity <= p.min_stock_level";
            } elseif ($filters['stock'] === 'out-of-stock') {
                $sql .= " AND p.stock_quantity = 0";
            }
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        $statement = $this->query($sql, $params);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    public function createProductForShopOwner(array $data, int $shopOwnerId): ?int
    {
        $data['shop_owner_id'] = $shopOwnerId;
        if (empty($data['sku'])) {
            $data['sku'] = 'PRD-' . strtoupper(uniqid());
        }
        if (!isset($data['rating'])) {
            $data['rating'] = 0;
        }
        if (!isset($data['total_reviews'])) {
            $data['total_reviews'] = 0;
        }
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        }
        return $this->create($data);
    }

    public function updateProductForShopOwner(int $id, array $data, int $shopOwnerId): bool
    {
        $product = $this->find($id);
        if (!$product) {
            return false;
        }
        if ($product['shop_owner_id'] !== null && $product['shop_owner_id'] != $shopOwnerId) {
            return false;
        }
        if ($product['shop_owner_id'] === null) {
            $data['shop_owner_id'] = $shopOwnerId;
        }
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        }
        return $this->update($id, $data);
    }

    public function deleteProductForShopOwner(int $id, int $shopOwnerId): bool
    {
        $product = $this->find($id);
        if (!$product) {
            return false;
        }
        if ($product['shop_owner_id'] !== null && $product['shop_owner_id'] != $shopOwnerId) {
            return false;
        }
        return $this->delete($id);
    }

    public function getShopOwnerStats(int $shopOwnerId): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_products,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                    SUM(CASE WHEN stock_quantity > min_stock_level THEN 1 ELSE 0 END) as in_stock,
                    SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= min_stock_level THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock
                FROM {$this->table} 
                WHERE (shop_owner_id = ? OR shop_owner_id IS NULL)";
        $result = $this->queryFirst($sql, [$shopOwnerId]);
        return $result ? $result : ['total_products' => 0, 'active_products' => 0, 'in_stock' => 0, 'low_stock' => 0, 'out_of_stock' => 0];
    }

    public function getRecentProducts(int $shopOwnerId, int $limit = 5): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.shop_owner_id = ? 
                ORDER BY p.created_at DESC 
                LIMIT ?";
        $statement = $this->query($sql, [$shopOwnerId, $limit]);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    public function getLowStockProductsByOwner(int $shopOwnerId, int $limit = 10): array
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.shop_owner_id = ? 
                AND (p.stock_quantity = 0 OR p.stock_quantity < 10)
                ORDER BY p.stock_quantity ASC, p.name ASC
                LIMIT ?";
        $statement = $this->query($sql, [$shopOwnerId, $limit]);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * INVENTORY MANAGEMENT METHODS
     */

    /**
     * Get inventory with stock status for shop owner
     */
    public function getInventoryWithStatus(int $shopOwnerId): array
    {
        $sql = "SELECT 
                    p.id, p.name, p.sku, p.stock_quantity, 
                    p.min_stock_level, p.status, p.updated_at,
                    c.name as category_name,
                    CASE 
                        WHEN p.stock_quantity = 0 THEN 'out-of-stock'
                        WHEN p.stock_quantity <= p.min_stock_level THEN 'low-stock'
                        ELSE 'in-stock'
                    END as stock_status
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.shop_owner_id = ?
                ORDER BY 
                    CASE 
                        WHEN p.stock_quantity = 0 THEN 1
                        WHEN p.stock_quantity <= p.min_stock_level THEN 2
                        ELSE 3
                    END,
                    p.name ASC";
        
        $statement = $this->query($sql, [$shopOwnerId]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Add stock quantity to product
     */
    public function addStock(int $productId, int $quantity, int $shopOwnerId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET stock_quantity = stock_quantity + ?,
                    updated_at = NOW()
                WHERE id = ? AND shop_owner_id = ?";
        $statement = $this->query($sql, [$quantity, $productId, $shopOwnerId]);
        return $statement && $statement->rowCount() > 0;
    }

    /**
     * Reduce stock quantity (for orders or removal)
     */
    public function removeStock(int $productId, int $quantity, string $reason, int $shopOwnerId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET stock_quantity = GREATEST(0, stock_quantity - ?),
                    updated_at = NOW()
                WHERE id = ? AND shop_owner_id = ?";
        $statement = $this->query($sql, [$quantity, $productId, $shopOwnerId]);
        return $statement && $statement->rowCount() > 0;
    }

    /**
     * Update minimum stock level (reorder level)
     */
    public function updateMinStockLevel(int $productId, int $minLevel, int $shopOwnerId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET min_stock_level = ?
                WHERE id = ? AND shop_owner_id = ?";
        $statement = $this->query($sql, [$minLevel, $productId, $shopOwnerId]);
        return $statement && $statement->rowCount() > 0;
    }

    /**
     * Reduce stock for order placement
     */
    public function reduceStockForOrder(int $productId, int $quantity, int $shopOwnerId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET stock_quantity = GREATEST(0, stock_quantity - ?),
                    updated_at = NOW()
                WHERE id = ? AND shop_owner_id = ? AND stock_quantity >= ?";
        $statement = $this->query($sql, [$quantity, $productId, $shopOwnerId, $quantity]);
        return $statement && $statement->rowCount() > 0;
    }
}