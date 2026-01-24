<?php

namespace App\Models;

use Core\BaseModel;

class ProductReview extends BaseModel
{
    protected string $table = 'product_reviews';
    
    protected array $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'review_text',
        'images',
        'is_verified_purchase'
    ];
    
    protected array $casts = [
        'id' => 'int',
        'product_id' => 'int',
        'user_id' => 'int',
        'order_id' => 'int',
        'rating' => 'int',
        'is_verified_purchase' => 'bool',
        'images' => 'array'
    ];

    /**
     * Get all reviews for a specific product
     */
    public function getProductReviews(int $productId): array
    {
        $sql = "SELECT pr.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email as user_email
                FROM {$this->table} pr
                LEFT JOIN users u ON pr.user_id = u.id
                WHERE pr.product_id = ?
                ORDER BY pr.created_at DESC";
        
        $statement = $this->query($sql, [$productId]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Check if user has already reviewed a product
     */
    public function getUserProductReview(int $userId, int $productId): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? AND product_id = ?";
        
        return $this->queryFirst($sql, [$userId, $productId]);
    }

    /**
     * Create a new review and update product rating
     */
    public function createReview(array $data): ?int
    {
        // Create the review using BaseModel's create method
        $reviewId = $this->create($data);
        
        if ($reviewId) {
            // Update product's average rating
            $this->updateProductRating($data['product_id']);
        }
        
        return $reviewId;
    }

    /**
     * Update an existing review and recalculate product rating
     */
    public function updateReview(int $reviewId, array $data): bool
    {
        // Get the review to find product_id
        $review = $this->find($reviewId);
        
        if (!$review) {
            return false;
        }
        
        // Update the review using BaseModel's update method
        $success = $this->update($reviewId, $data);
        
        if ($success) {
            // Recalculate product rating
            $this->updateProductRating($review['product_id']);
        }
        
        return $success;
    }

    /**
     * Delete a review and update product rating
     */
    public function deleteReview(int $reviewId): bool
    {
        // Get the review to find product_id before deletion
        $review = $this->find($reviewId);
        
        if (!$review) {
            return false;
        }
        
        $productId = $review['product_id'];
        
        // Delete the review using BaseModel's delete method
        $success = $this->delete($reviewId);
        
        if ($success) {
            // Update product rating
            $this->updateProductRating($productId);
        }
        
        return $success;
    }

    /**
     * Update product's average rating and total reviews count
     */
    public function updateProductRating(int $productId): void
    {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
                FROM {$this->table}
                WHERE product_id = ?";
        
        $result = $this->queryFirst($sql, [$productId]);
        
        $avgRating = $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
        $totalReviews = $result['total_reviews'] ?? 0;
        
        // Update the products table
        $updateSql = "UPDATE products 
                      SET rating = ?, total_reviews = ? 
                      WHERE id = ?";
        
        $this->query($updateSql, [$avgRating, $totalReviews, $productId]);
    }

    /**
     * Get review with product information
     */
    public function getReviewWithProduct(int $reviewId): ?array
    {
        $sql = "SELECT pr.*, p.name as product_name, p.id as product_id,
                       CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM {$this->table} pr
                LEFT JOIN products p ON pr.product_id = p.id
                LEFT JOIN users u ON pr.user_id = u.id
                WHERE pr.id = ?";
        
        return $this->queryFirst($sql, [$reviewId]);
    }

    /**
     * Get all reviews for products owned by a shop owner
     */
    public function getShopOwnerReviews(int $shopOwnerId, int $limit = null): array
    {
        $sql = "SELECT pr.*, p.name as product_name, p.id as product_id,
                       CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email as user_email
                FROM {$this->table} pr
                INNER JOIN products p ON pr.product_id = p.id
                LEFT JOIN users u ON pr.user_id = u.id
                WHERE p.shop_owner_id = ?
                ORDER BY pr.created_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $statement = $this->query($sql, [$shopOwnerId, $limit]);
        } else {
            $statement = $this->query($sql, [$shopOwnerId]);
        }
        
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get review statistics for a shop owner
     */
    public function getShopOwnerReviewStats(int $shopOwnerId): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_reviews,
                    AVG(pr.rating) as avg_rating,
                    SUM(CASE WHEN pr.rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN pr.rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN pr.rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN pr.rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN pr.rating = 1 THEN 1 ELSE 0 END) as one_star
                FROM {$this->table} pr
                INNER JOIN products p ON pr.product_id = p.id
                WHERE p.shop_owner_id = ?";
        
        $result = $this->queryFirst($sql, [$shopOwnerId]);
        
        return [
            'total_reviews' => (int)($result['total_reviews'] ?? 0),
            'avg_rating' => $result['avg_rating'] ? round($result['avg_rating'], 1) : 0,
            'five_star' => (int)($result['five_star'] ?? 0),
            'four_star' => (int)($result['four_star'] ?? 0),
            'three_star' => (int)($result['three_star'] ?? 0),
            'two_star' => (int)($result['two_star'] ?? 0),
            'one_star' => (int)($result['one_star'] ?? 0)
        ];
    }

    /**
     * Check if user owns a review
     */
    public function isReviewOwner(int $reviewId, int $userId): bool
    {
        $review = $this->find($reviewId);
        return $review && $review['user_id'] == $userId;
    }
}
