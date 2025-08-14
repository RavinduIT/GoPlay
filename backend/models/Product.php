<?php
/**
 * Product Model
 * Handles product-related database operations
 */

require_once 'BaseModel.php';

class Product extends BaseModel {
    
    protected $table = 'products';
    protected $fillable = [
        'name', 'sku', 'description', 'category_id', 'brand', 'price', 
        'compare_price', 'cost_price', 'stock_quantity', 'min_stock_level',
        'weight', 'dimensions', 'specifications', 'features', 'images', 
        'tags', 'status'
    ];
    
    /**
     * Get products with category information
     */
    public function getWithCategory($productId = null) {
        $sql = "
            SELECT p.*, pc.name as category_name, pc.description as category_description
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
        ";
        
        if ($productId) {
            $sql .= " WHERE p.id = ?";
            return $this->db->getOne($sql, [$productId]);
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        return $this->db->getAll($sql);
    }
    
    /**
     * Get active products
     */
    public function getActiveProducts() {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.status = 'active'
            ORDER BY p.name
        ";
        return $this->db->getAll($sql);
    }
    
    /**
     * Get products by category
     */
    public function getByCategory($categoryId) {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.category_id = ? AND p.status = 'active'
            ORDER BY p.name
        ";
        return $this->db->getAll($sql, [$categoryId]);
    }
    
    /**
     * Search products
     */
    public function searchProducts($searchTerm, $categoryId = null, $minPrice = null, $maxPrice = null, $brand = null) {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.status = 'active'
        ";
        $params = [];
        
        if (!empty($searchTerm)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
            $searchParam = "%$searchTerm%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($minPrice !== null) {
            $sql .= " AND p.price >= ?";
            $params[] = $minPrice;
        }
        
        if ($maxPrice !== null) {
            $sql .= " AND p.price <= ?";
            $params[] = $maxPrice;
        }
        
        if ($brand) {
            $sql .= " AND p.brand = ?";
            $params[] = $brand;
        }
        
        $sql .= " ORDER BY p.name";
        
        return $this->db->getAll($sql, $params);
    }
    
    /**
     * Get products with low stock
     */
    public function getLowStockProducts() {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.stock_quantity <= p.min_stock_level
            ORDER BY p.stock_quantity ASC
        ";
        return $this->db->getAll($sql);
    }
    
    /**
     * Update stock quantity
     */
    public function updateStock($productId, $quantity, $operation = 'set') {
        if ($operation === 'add') {
            $sql = "UPDATE {$this->table} SET stock_quantity = stock_quantity + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        } elseif ($operation === 'subtract') {
            $sql = "UPDATE {$this->table} SET stock_quantity = stock_quantity - ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND stock_quantity >= ?";
            return $this->db->update($sql, [$quantity, $productId, $quantity]);
        } else {
            $sql = "UPDATE {$this->table} SET stock_quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        }
        
        return $this->db->update($sql, [$quantity, $productId]);
    }
    
    /**
     * Get product reviews
     */
    public function getProductReviews($productId) {
        $sql = "
            SELECT pr.*, u.first_name, u.last_name, u.profile_picture
            FROM product_reviews pr
            LEFT JOIN users u ON pr.user_id = u.id
            WHERE pr.product_id = ?
            ORDER BY pr.created_at DESC
        ";
        return $this->db->getAll($sql, [$productId]);
    }
    
    /**
     * Get product rating summary
     */
    public function getRatingSummary($productId) {
        $sql = "
            SELECT 
                AVG(rating) as average_rating,
                COUNT(*) as total_reviews,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            FROM product_reviews
            WHERE product_id = ?
        ";
        return $this->db->getOne($sql, [$productId]);
    }
    
    /**
     * Update product rating
     */
    public function updateProductRating($productId) {
        $ratingData = $this->getRatingSummary($productId);
        
        if ($ratingData && $ratingData['total_reviews'] > 0) {
            $avgRating = round($ratingData['average_rating'], 2);
            $totalReviews = $ratingData['total_reviews'];
            
            $sql = "UPDATE {$this->table} SET rating = ?, total_reviews = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            return $this->db->update($sql, [$avgRating, $totalReviews, $productId]);
        }
        
        return false;
    }
    
    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 8) {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.status = 'active' AND p.rating >= 4.0
            ORDER BY p.rating DESC, p.total_reviews DESC
            LIMIT ?
        ";
        return $this->db->getAll($sql, [$limit]);
    }
    
    /**
     * Get best selling products
     */
    public function getBestSellingProducts($limit = 8) {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.status = 'active'
            ORDER BY p.total_sales DESC
            LIMIT ?
        ";
        return $this->db->getAll($sql, [$limit]);
    }
    
    /**
     * Get related products
     */
    public function getRelatedProducts($productId, $categoryId, $limit = 4) {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
            ORDER BY p.rating DESC
            LIMIT ?
        ";
        return $this->db->getAll($sql, [$categoryId, $productId, $limit]);
    }
    
    /**
     * Get product statistics
     */
    public function getProductStats() {
        $sql = "
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN status = 'out_of_stock' THEN 1 ELSE 0 END) as out_of_stock,
                SUM(stock_quantity) as total_stock,
                AVG(price) as average_price,
                SUM(total_sales) as total_sales
            FROM {$this->table}
        ";
        return $this->db->getOne($sql);
    }
    
    /**
     * Get products by brand
     */
    public function getByBrand($brand) {
        $sql = "
            SELECT p.*, pc.name as category_name
            FROM {$this->table} p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.brand = ? AND p.status = 'active'
            ORDER BY p.name
        ";
        return $this->db->getAll($sql, [$brand]);
    }
    
    /**
     * Get all brands
     */
    public function getAllBrands() {
        $sql = "SELECT DISTINCT brand FROM {$this->table} WHERE brand IS NOT NULL AND status = 'active' ORDER BY brand";
        return $this->db->getAll($sql);
    }
    
    /**
     * Increase total sales count
     */
    public function increaseSalesCount($productId, $quantity = 1) {
        $sql = "UPDATE {$this->table} SET total_sales = total_sales + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->update($sql, [$quantity, $productId]);
    }
}