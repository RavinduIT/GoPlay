<?php

namespace App\Models;

use Core\BaseModel;

class Order extends BaseModel
{
    protected string $table = 'orders';
    
    protected array $fillable = [
        'order_number',
        'user_id',
        'order_type',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'payment_method',
        'shipping_address',
        'billing_address',
        'notes'
    ];

    protected array $casts = [
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'shipping_amount' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
        'shipping_address' => 'array',
        'billing_address' => 'array'
    ];

    /**
     * Generate unique order number
     */
    public function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        return "ORD-{$date}-{$random}";
    }

    /**
     * Create new order with items
     */
    public function createOrder(array $orderData, array $cartItems): ?int
    {
        try {
            // Generate order number
            $orderData['order_number'] = $this->generateOrderNumber();
            
            // Ensure order number is unique
            $attempts = 0;
            while ($this->findByOrderNumber($orderData['order_number']) && $attempts < 10) {
                $orderData['order_number'] = $this->generateOrderNumber();
                $attempts++;
            }

            // Create order
            $orderId = $this->create($orderData);
            
            if (!$orderId) {
                throw new \Exception('Failed to create order');
            }

            // Create order items
            $orderItemModel = new OrderItem();
            foreach ($cartItems as $item) {
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'item_name' => $item['product_name'],
                    'item_description' => $item['description'] ?? '',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price']
                ];
                
                $orderItemModel->create($orderItemData);
                
                // Update product stock
                $this->updateProductStock($item['product_id'], $item['quantity']);
            }

            return $orderId;
            
        } catch (\Exception $e) {
            error_log("Order creation failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find order by order number
     */
    public function findByOrderNumber(string $orderNumber): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_number = ?";
        return $this->queryFirst($sql, [$orderNumber]);
    }

    /**
     * Get order with items
     */
    public function getOrderWithItems(int $orderId): ?array
    {
        $order = $this->find($orderId);
        
        if (!$order) {
            return null;
        }

        $sql = "SELECT oi.*, p.images, p.sku 
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
        
        $items = $this->query($sql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
        
        $order['items'] = $items;
        return $order;
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(int $orderId, string $status): bool
    {
        return $this->update($orderId, ['status' => $status]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $orderId, string $paymentStatus): bool
    {
        return $this->update($orderId, ['payment_status' => $paymentStatus]);
    }

    /**
     * Get orders by user
     */
    public function getOrdersByUser(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC";
        return $this->query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update product stock after order
     */
    private function updateProductStock(int $productId, int $quantity): void
    {
        $sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
        $this->query($sql, [$quantity, $productId, $quantity]);
    }

    /**
     * Calculate order totals
     */
    public function calculateOrderTotals(array $cartItems): array
    {
        $subtotal = 0;
        
        foreach ($cartItems as $item) {
            $subtotal += $item['total_price'];
        }

        // Tax: 8% of subtotal
        $tax = round($subtotal * 0.08, 2);
        
        // Shipping: Free for orders over 5000 LKR, otherwise 500 LKR
        $shipping = $subtotal >= 5000 ? 0 : 500;
        
        // Discount: Can be applied later
        $discount = 0;
        
        // Total
        $total = $subtotal + $tax + $shipping - $discount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'shipping_amount' => $shipping,
            'discount_amount' => $discount,
            'total_amount' => $total
        ];
    }

    /**
     * Get orders by shop owner (only orders containing shop owner's products)
     */
    public function getOrdersByShopOwner(int $shopOwnerId, array $filters = []): array
    {
        // Check if shop_owner_id column exists
        if (!$this->columnExists('products', 'shop_owner_id')) {
            // Fallback: Return all orders (you might want to return empty array instead)
            error_log("Warning: shop_owner_id column doesn't exist in products table");
            
            $sql = "SELECT DISTINCT 
                        o.*,
                        u.username as customer_name,
                        u.email as customer_email,
                        u.phone as customer_phone,
                        COUNT(DISTINCT oi.id) as total_items
                    FROM {$this->table} o
                    INNER JOIN order_items oi ON o.id = oi.order_id
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE 1=1";
        } else {
            $sql = "SELECT DISTINCT 
                        o.*,
                        u.username as customer_name,
                        u.email as customer_email,
                        u.phone as customer_phone,
                        COUNT(DISTINCT oi.id) as total_items
                    FROM {$this->table} o
                    INNER JOIN order_items oi ON o.id = oi.order_id
                    INNER JOIN products p ON oi.product_id = p.id
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE p.shop_owner_id = ?";
        }
        
        $params = $this->columnExists('products', 'shop_owner_id') ? [$shopOwnerId] : [];
        
        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        // Status filter
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }
        
        // Payment method filter
        if (!empty($filters['payment_method'])) {
            $sql .= " AND o.payment_method = ?";
            $params[] = $filters['payment_method'];
        }
        
        // Payment status filter
        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }
        
        // Date filter
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }
        
        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
        
        $statement = $this->query($sql, $params);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get order details with items for shop owner
     */
    public function getOrderDetailsForShopOwner(int $orderId, int $shopOwnerId): ?array
    {
        // Check once if column exists and store result
        $hasShopOwnerColumn = $this->columnExists('products', 'shop_owner_id');
        
        error_log("getOrderDetailsForShopOwner - Column exists: " . ($hasShopOwnerColumn ? 'YES' : 'NO'));
        
        // Get order with customer details
        if ($hasShopOwnerColumn) {
            $sql = "SELECT DISTINCT o.*,
                        u.username as customer_name,
                        u.email as customer_email,
                        u.phone as customer_phone
                    FROM {$this->table} o
                    INNER JOIN order_items oi ON o.id = oi.order_id
                    INNER JOIN products p ON oi.product_id = p.id
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = ? AND p.shop_owner_id = ?
                    LIMIT 1";
            $order = $this->queryFirst($sql, [$orderId, $shopOwnerId]);
        } else {
            // Fallback when shop_owner_id doesn't exist
            error_log("Using fallback query without shop_owner_id");
            $sql = "SELECT o.*,
                        u.username as customer_name,
                        u.email as customer_email,
                        u.phone as customer_phone
                    FROM {$this->table} o
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.id = ?
                    LIMIT 1";
            $order = $this->queryFirst($sql, [$orderId]);
        }
        
        if (!$order) {
            error_log("Order not found: $orderId");
            return null;
        }
        
        // Get items from order using same column check
        if ($hasShopOwnerColumn) {
            $itemsSql = "SELECT 
                            oi.*,
                            p.name as product_name,
                            p.sku,
                            p.images,
                            p.brand
                        FROM order_items oi
                        INNER JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = ? AND p.shop_owner_id = ?";
            $items = $this->query($itemsSql, [$orderId, $shopOwnerId])->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // Fallback: Get all items
            error_log("Getting items without shop_owner_id filter");
            $itemsSql = "SELECT 
                            oi.*,
                            p.name as product_name,
                            p.sku,
                            p.images,
                            p.brand
                        FROM order_items oi
                        INNER JOIN products p ON oi.product_id = p.id
                        WHERE oi.order_id = ?";
            $items = $this->query($itemsSql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        error_log("Found " . count($items) . " items for order $orderId");
        $order['items'] = $items;
        return $order;
    }

    /**
     * Update order status with validation
     */
    public function updateOrderStatusWithValidation(int $orderId, string $newStatus, int $shopOwnerId): array
    {
        // Verify shop owner has access to this order
        $order = $this->getOrderDetailsForShopOwner($orderId, $shopOwnerId);
        
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found or access denied'];
        }
        
        $currentStatus = $order['status'];
        
        // Check if status is actually changing
        if ($currentStatus === $newStatus) {
            return ['success' => true, 'message' => 'Order status is already ' . $newStatus];
        }
        
        // Validation rules
        if ($currentStatus === 'delivered') {
            return ['success' => false, 'message' => 'Cannot change status of delivered orders'];
        }
        
        if ($currentStatus === 'cancelled') {
            return ['success' => false, 'message' => 'Cannot change status of cancelled orders'];
        }
        
        if ($newStatus === 'shipped' && $currentStatus === 'cancelled') {
            return ['success' => false, 'message' => 'Cannot ship cancelled orders'];
        }
        
        // Validate status values
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid order status'];
        }
        
        // Update status
        $updated = $this->update($orderId, ['status' => $newStatus]);
        
        if ($updated) {
            return ['success' => true, 'message' => 'Order status updated to ' . $newStatus];
        }
        
        return ['success' => false, 'message' => 'Failed to update order status'];
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatusForOrder(int $orderId, string $paymentStatus, int $shopOwnerId): array
    {
        // Verify shop owner has access to this order
        $order = $this->getOrderDetailsForShopOwner($orderId, $shopOwnerId);
        
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found or access denied'];
        }
        
        // Check if payment status is actually changing
        if ($order['payment_status'] === $paymentStatus) {
            return ['success' => true, 'message' => 'Payment status is already ' . $paymentStatus];
        }
        
        // Validate status values
        $validStatuses = ['pending', 'paid', 'failed'];
        if (!in_array($paymentStatus, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid payment status'];
        }
        
        $updated = $this->update($orderId, ['payment_status' => $paymentStatus]);
        
        if ($updated) {
            return ['success' => true, 'message' => 'Payment status updated to ' . $paymentStatus];
        }
        
        return ['success' => false, 'message' => 'Failed to update payment status'];
    }

    /**
     * Delete order with validation
     */
    public function deleteOrderWithValidation(int $orderId, int $shopOwnerId): array
    {
        // Verify shop owner has access to this order
        $order = $this->getOrderDetailsForShopOwner($orderId, $shopOwnerId);
        
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found or access denied'];
        }
        
        // Only allow deletion of pending or cancelled orders
        if (!in_array($order['status'], ['pending', 'cancelled'])) {
            return ['success' => false, 'message' => 'Can only delete pending or cancelled orders'];
        }
        
        $deleted = $this->delete($orderId);
        
        if ($deleted) {
            return ['success' => true, 'message' => 'Order deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete order'];
    }

    /**
     * Get shop owner order statistics
     */
    public function getShopOwnerOrderStats(int $shopOwnerId): array
    {
        if ($this->columnExists('products', 'shop_owner_id')) {
            $sql = "SELECT 
                        COUNT(DISTINCT o.id) as total_orders,
                        SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                        SUM(CASE WHEN o.status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                        SUM(CASE WHEN o.status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                        SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                        SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                        SUM(CASE WHEN o.payment_status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
                        SUM(CASE WHEN o.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
                        SUM(oi.total_price) as total_revenue
                    FROM orders o
                    INNER JOIN order_items oi ON o.id = oi.order_id
                    INNER JOIN products p ON oi.product_id = p.id
                    WHERE p.shop_owner_id = ?";
            $result = $this->queryFirst($sql, [$shopOwnerId]);
        } else {
            // Fallback: Get all orders stats
            $sql = "SELECT 
                        COUNT(DISTINCT o.id) as total_orders,
                        SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                        SUM(CASE WHEN o.status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                        SUM(CASE WHEN o.status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                        SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                        SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                        SUM(CASE WHEN o.payment_status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
                        SUM(CASE WHEN o.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_orders,
                        SUM(oi.total_price) as total_revenue
                    FROM orders o
                    INNER JOIN order_items oi ON o.id = oi.order_id";
            $result = $this->queryFirst($sql, []);
        }
        
        return [
            'total_orders' => (int)($result['total_orders'] ?? 0),
            'pending_orders' => (int)($result['pending_orders'] ?? 0),
            'processing_orders' => (int)($result['processing_orders'] ?? 0),
            'shipped_orders' => (int)($result['shipped_orders'] ?? 0),
            'delivered_orders' => (int)($result['delivered_orders'] ?? 0),
            'cancelled_orders' => (int)($result['cancelled_orders'] ?? 0),
            'pending_payments' => (int)($result['pending_payments'] ?? 0),
            'paid_orders' => (int)($result['paid_orders'] ?? 0),
            'total_revenue' => (float)($result['total_revenue'] ?? 0)
        ];
    }

    /**
     * Check if a column exists in a table
     */
    /**
     * Check if a column exists in a table
     */
    private function columnExists(string $table, string $column): bool
    {
        try {
            error_log("Checking if column '$column' exists in table '$table'");
            $sql = "SHOW COLUMNS FROM {$table} LIKE ?";
            $result = $this->query($sql, [$column])->fetch(\PDO::FETCH_ASSOC);
            $exists = !empty($result);
            error_log("Column '$column' in '$table': " . ($exists ? 'EXISTS' : 'DOES NOT EXIST'));
            return $exists;
        } catch (\Exception $e) {
            error_log("Error checking column existence: " . $e->getMessage());
            return false;
        }
    }
}
