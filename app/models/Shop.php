<?php

namespace App\Models;

use Core\BaseModel;

class Shop extends BaseModel
{
    protected string $table = 'shops';
    
    protected array $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'city',
        'phone',
        'email',
        'website',
        'logo',
        'banner_image',
        'business_hours',
        'delivery_options',
        'payment_methods',
        'rating',
        'total_reviews',
        'status'
    ];

    protected array $hidden = [];

    public function getByOwnerId(int $ownerId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $statement = $this->query($sql, [$ownerId]);
        $shop = $statement->fetch(\PDO::FETCH_ASSOC);
        
        return $shop ?: null;
    }

    public function getActiveShops(): array
    {
        return $this->where(['status' => 'active']);
    }

    public function getProducts(int $shopId): array
    {
        $sql = "SELECT p.* FROM products p WHERE p.shop_id = ? AND p.status = 'active' ORDER BY p.created_at DESC";
        $statement = $this->query($sql, [$shopId]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getOrders(int $shopId): array
    {
        $sql = "SELECT o.* FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id 
                JOIN products p ON oi.product_id = p.id 
                WHERE p.shop_id = ? 
                GROUP BY o.id 
                ORDER BY o.created_at DESC";
        $statement = $this->query($sql, [$shopId]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function getStatistics(int $shopId): array
    {
        $stats = [];
        
        // Total products
        $result = $this->queryFirst("SELECT COUNT(*) as count FROM products WHERE shop_id = ?", [$shopId]);
        $stats['total_products'] = $result['count'] ?? 0;
        
        // Total orders
        $result = $this->queryFirst("SELECT COUNT(DISTINCT o.id) as count FROM orders o 
                                   JOIN order_items oi ON o.id = oi.order_id 
                                   JOIN products p ON oi.product_id = p.id 
                                   WHERE p.shop_id = ?", [$shopId]);
        $stats['total_orders'] = $result['count'] ?? 0;
        
        // Total revenue
        $result = $this->queryFirst("SELECT SUM(o.total_amount) as total FROM orders o 
                                   JOIN order_items oi ON o.id = oi.order_id 
                                   JOIN products p ON oi.product_id = p.id 
                                   WHERE p.shop_id = ? AND o.status = 'completed'", [$shopId]);
        $stats['total_revenue'] = $result['total'] ?? 0;
        
        return $stats;
    }
}