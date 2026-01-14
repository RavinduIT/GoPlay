<?php

namespace App\Models;

use Core\BaseModel;

class OrderItem extends BaseModel
{
    protected string $table = 'order_items';
    
    protected array $fillable = [
        'order_id',
        'product_id',
        'coach_booking_id',
        'facility_booking_id',
        'item_name',
        'item_description',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected array $casts = [
        'order_id' => 'int',
        'product_id' => 'int',
        'quantity' => 'int',
        'unit_price' => 'float',
        'total_price' => 'float'
    ];

    /**
     * Get items by order ID
     */
    public function getByOrderId(int $orderId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ?";
        return $this->query($sql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get order items with product details
     */
    public function getOrderItemsWithProducts(int $orderId): array
    {
        $sql = "SELECT 
                    oi.*,
                    p.name as product_name,
                    p.images,
                    p.sku,
                    p.brand
                FROM {$this->table} oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
        
        return $this->query($sql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Calculate total for order
     */
    public function getOrderTotal(int $orderId): float
    {
        $sql = "SELECT SUM(total_price) as total FROM {$this->table} WHERE order_id = ?";
        $result = $this->queryFirst($sql, [$orderId]);
        return (float) ($result['total'] ?? 0);
    }
}
