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
        'notes',
        'cod_payment_proof'
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
                    'total_price' => $item['total_price'],
                    'selected_size' => $item['selected_size'] ?? null,
                    'selected_color' => $item['selected_color'] ?? null
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
    public function getOrdersByShopOwner(int $shopOwnerId, array $filters = [], ?int $limit = null): array
    {
        // Build SQL query to get orders containing products from this shop owner
        $sql = "SELECT 
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
        
        $params = [$shopOwnerId];
        
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
        
        // Add limit if specified
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        $statement = $this->query($sql, $params);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all COD orders for a shop owner with proof status.
     */
    public function getCODOrdersByShopOwner(int $shopOwnerId): array
    {
        $sql = "SELECT
                    o.id, o.order_number, o.created_at, o.status, o.payment_status,
                    o.total_amount, o.cod_payment_proof,
                    u.username as customer_name, u.phone as customer_phone,
                    COALESCE(SUM(oi.total_price), 0) as shop_owner_subtotal
                FROM {$this->table} o
                INNER JOIN order_items oi ON o.id = oi.order_id
                INNER JOIN products p ON oi.product_id = p.id
                LEFT JOIN users u ON o.user_id = u.id
                WHERE p.shop_owner_id = ? AND o.payment_method = 'cash'
                GROUP BY o.id, o.order_number, o.created_at, o.status, o.payment_status,
                         o.total_amount, o.cod_payment_proof, u.username, u.phone
                ORDER BY o.created_at DESC";
        $rows = $this->query($sql, [$shopOwnerId])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(function($row) {
            $row['shop_owner_subtotal'] = (float)$row['shop_owner_subtotal'];
            $row['total_amount'] = (float)$row['total_amount'];
            return $row;
        }, $rows);
    }

    /**
     * Get order details with items for shop owner
     */
    public function getOrderDetailsForShopOwner(int $orderId, int $shopOwnerId): ?array
    {
        // Get order with customer details - verify shop owner has at least one product in this order
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
        
        if (!$order) {
            error_log("Order not found or shop owner has no products in order: $orderId");
            return null;
        }
        
        // Get ONLY items belonging to this shop owner
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
        
        // Calculate shop owner's subtotal from their items only
        $shopOwnerSubtotal = 0;
        foreach ($items as $item) {
            $shopOwnerSubtotal += floatval($item['total_price']);
        }
        
        // Check if this is a multi-seller order by counting total items
        $totalItemsSql = "SELECT COUNT(*) as total_count FROM order_items WHERE order_id = ?";
        $totalItemsResult = $this->query($totalItemsSql, [$orderId])->fetch(\PDO::FETCH_ASSOC);
        $isMultiSeller = ($totalItemsResult['total_count'] > count($items));
        
        error_log("Found " . count($items) . " items for shop owner $shopOwnerId in order $orderId");
        
        $order['items'] = $items;
        $order['shop_owner_subtotal'] = $shopOwnerSubtotal;
        $order['is_multi_seller'] = $isMultiSeller;
        $order['shop_owner_item_count'] = count($items);
        $order['total_item_count'] = $totalItemsResult['total_count'];
        
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
        
        // Prevent changing payment status if already paid (security measure)
        if ($order['payment_status'] === 'paid') {
            return ['success' => false, 'message' => 'Cannot modify payment status for paid orders'];
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
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT o.id) as total_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'pending' THEN o.id END) as pending_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'processing' THEN o.id END) as processing_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'shipped' THEN o.id END) as shipped_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'delivered' THEN o.id END) as delivered_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'cancelled' THEN o.id END) as cancelled_orders,
                        COUNT(DISTINCT CASE WHEN o.payment_status = 'pending' THEN o.id END) as pending_payments,
                        COUNT(DISTINCT CASE WHEN o.payment_status = 'paid' THEN o.id END) as paid_orders,
                        COALESCE(SUM(oi.total_price), 0) as total_revenue,
                        COALESCE(SUM(CASE WHEN MONTH(o.created_at) = MONTH(CURRENT_DATE()) 
                            AND YEAR(o.created_at) = YEAR(CURRENT_DATE()) 
                            THEN oi.total_price ELSE 0 END), 0) as monthly_revenue
                    FROM orders o
                    INNER JOIN order_items oi ON o.id = oi.order_id
                    INNER JOIN products p ON oi.product_id = p.id
                    WHERE p.shop_owner_id = ?";
            
            $statement = $this->query($sql, [$shopOwnerId]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [
                    'total_orders' => 0,
                    'pending_orders' => 0,
                    'processing_orders' => 0,
                    'shipped_orders' => 0,
                    'delivered_orders' => 0,
                    'cancelled_orders' => 0,
                    'pending_payments' => 0,
                    'paid_orders' => 0,
                    'total_revenue' => 0,
                    'monthly_revenue' => 0
                ];
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
                'total_revenue' => (float)($result['total_revenue'] ?? 0),
                'monthly_revenue' => (float)($result['monthly_revenue'] ?? 0)
            ];
        } catch (\Exception $e) {
            error_log("Error in getShopOwnerOrderStats: " . $e->getMessage());
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'processing_orders' => 0,
                'shipped_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'pending_payments' => 0,
                'paid_orders' => 0,
                'total_revenue' => 0,
                'monthly_revenue' => 0
            ];
        }
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

    /**
     * Get order details for a specific user (validates ownership)
     */
    public function getOrderDetailsForUser(int $orderId, int $userId): ?array
    {
        // Get order and verify user owns it
        $sql = "SELECT o.*,
                    u.username as customer_name,
                    u.email as customer_email,
                    u.phone as customer_phone
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?
                LIMIT 1";
        $order = $this->queryFirst($sql, [$orderId, $userId]);
        
        if (!$order) {
            error_log("Order not found or user does not own order: $orderId");
            return null;
        }
        
        // Get all items for this order
        $itemsSql = "SELECT 
                        oi.*,
                        p.name as product_name,
                        p.sku,
                        p.images,
                        p.brand
                    FROM order_items oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
        $items = $this->query($itemsSql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
        
        $order['items'] = $items;
        return $order;
    }

    /**
     * Check if an order can be cancelled
     */
    public function canBeCancelled(string $status): bool
    {
        return in_array($status, ['pending', 'processing']);
    }

    /**
     * Cancel an order with user ownership validation
     */
    public function cancelOrder(int $orderId, int $userId, string $reason = ''): array
    {
        try {
            // Get order and verify ownership
            $order = $this->queryFirst(
                "SELECT * FROM {$this->table} WHERE id = ? AND user_id = ?",
                [$orderId, $userId]
            );
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Order not found or access denied'
                ];
            }
            
            // Check if order can be cancelled
            if (!$this->canBeCancelled($order['status'])) {
                return [
                    'success' => false,
                    'message' => 'Order cannot be cancelled. It has already been ' . $order['status']
                ];
            }
            
            // Restore stock for all items in the order BEFORE cancelling
            $this->restockProducts($orderId);
            
            // Handle refund logic for paid card payments
            $paymentStatus = $order['payment_status'];
            if ($order['payment_method'] === 'card' && $order['payment_status'] === 'paid') {
                $paymentStatus = 'refund_pending';
            }
            
            // Update order status
            $updated = $this->update($orderId, [
                'status' => 'cancelled',
                'payment_status' => $paymentStatus,
                'notes' => $order['notes'] . "\n\nCancelled by customer: " . $reason
            ]);
            
            if (!$updated) {
                return [
                    'success' => false,
                    'message' => 'Failed to cancel order'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Order cancelled successfully',
                'refund_status' => $paymentStatus === 'refund_pending' ? 'Refund will be processed in 5-7 business days' : null
            ];
            
        } catch (\Exception $e) {
            error_log("Error cancelling order: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Restore stock for all items when order is cancelled
     */
    private function restockProducts(int $orderId): void
    {
        try {
            // Get all items for this order with their shop owner info
            $sql = "SELECT 
                        oi.product_id,
                        oi.quantity,
                        p.shop_owner_id
                    FROM order_items oi
                    INNER JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
            
            $items = $this->query($sql, [$orderId])->fetchAll(\PDO::FETCH_ASSOC);
            
            if (empty($items)) {
                error_log("No items found to restock for order: $orderId");
                return;
            }
            
            // Restore stock for each product
            $productModel = new Product();
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $shopOwnerId = $item['shop_owner_id'];
                
                $success = $productModel->addStock($productId, $quantity, $shopOwnerId);
                
                if ($success) {
                    error_log("Restocked product $productId: +$quantity units (Order: $orderId)");
                } else {
                    error_log("Failed to restock product $productId for order $orderId");
                }
            }
            
        } catch (\Exception $e) {
            error_log("Error restocking products for order $orderId: " . $e->getMessage());
        }
    }

    /**
     * Get user order statistics
     */
    public function getUserOrderStats(int $userId): array
    {
        try {
            $sql = "SELECT 
                        COUNT(DISTINCT o.id) as total_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'pending' THEN o.id END) as pending_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'processing' THEN o.id END) as processing_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'delivered' THEN o.id END) as delivered_orders,
                        COUNT(DISTINCT CASE WHEN o.status = 'cancelled' THEN o.id END) as cancelled_orders,
                        COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN o.total_amount ELSE 0 END), 0) as total_spent
                    FROM orders o
                    WHERE o.user_id = ?";
            
            $result = $this->db->query($sql, [$userId])->fetch(\PDO::FETCH_ASSOC);
            
            return $result ?: [
                'total_orders' => 0,
                'pending_orders' => 0,
                'processing_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'total_spent' => 0
            ];
        } catch (\Exception $e) {
            error_log("Error getting user order stats: " . $e->getMessage());
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'processing_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
                'total_spent' => 0
            ];
        }
    }
}
