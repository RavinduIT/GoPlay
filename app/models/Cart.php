<?php

namespace App\Models;

use Core\BaseModel;

class Cart extends BaseModel
{
    protected string $table = 'shopping_carts';
    
    protected array $fillable = [
        'user_id',
        'session_id',
        'status',
        'expires_at'
    ];

    /**
     * CREATE: Get or create cart for user/session
     */
    public function getOrCreateCart(?int $userId, string $sessionId): array
    {
        // Try to find existing active cart
        $cart = $this->findActiveCart($userId, $sessionId);
        
        if (!$cart) {
            // Create new cart
            $cartData = [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'status' => 'active',
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days'))
            ];
            
            $cartId = $this->create($cartData);
            $cart = $this->find($cartId);
        }
        
        return $cart;
    }

    /**
     * READ: Find active cart by user or session
     */
    public function findActiveCart(?int $userId, string $sessionId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'active' AND expires_at > NOW() AND ";
        
        if ($userId) {
            $sql .= "user_id = ?";
            $params = [$userId];
        } else {
            $sql .= "session_id = ? AND user_id IS NULL";
            $params = [$sessionId];
        }
        
        $statement = $this->query($sql, $params);
        $cart = $statement->fetch(\PDO::FETCH_ASSOC);
        
        return $cart ?: null;
    }

    /**
     * READ: Get cart with full details (items, products, totals)
     */
    public function getCartDetails(int $cartId): array
    {
        // Get cart summary
        $sql = "SELECT * FROM cart_summary WHERE cart_id = ?";
        $statement = $this->query($sql, [$cartId]);
        $cartSummary = $statement->fetch(\PDO::FETCH_ASSOC);
        
        if (!$cartSummary) {
            return [
                'cart' => null,
                'items' => [],
                'totals' => ['item_count' => 0, 'total_quantity' => 0, 'subtotal' => 0]
            ];
        }

        // Get cart items with product details
        $sql = "SELECT 
                    ci.*,
                    p.name as product_name,
                    p.sku,
                    p.brand,
                    p.stock_quantity,
                    p.images,
                    'General' as category_name
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.cart_id = ?
                ORDER BY ci.added_at ASC";
        
        $statement = $this->query($sql, [$cartId]);
        $items = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return [
            'cart' => $cartSummary,
            'items' => $items,
            'totals' => [
                'item_count' => $cartSummary['item_count'],
                'total_quantity' => $cartSummary['total_quantity'],
                'subtotal' => $cartSummary['subtotal']
            ]
        ];
    }

    /**
     * CREATE/UPDATE: Add item to cart (or update quantity if exists)
     */
    public function addItem(int $cartId, int $productId, int $quantity = 1): bool
    {
        // Get current product price
        $product = $this->queryFirst("SELECT price, stock_quantity FROM products WHERE id = ? AND status = 'active'", [$productId]);
        
        if (!$product) {
            throw new \Exception('Product not found or inactive');
        }
        
        if ($product['stock_quantity'] < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        // Check if item already exists in cart
        $existingItem = $this->queryFirst(
            "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?", 
            [$cartId, $productId]
        );

        if ($existingItem) {
            // UPDATE: Increase quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            if ($product['stock_quantity'] < $newQuantity) {
                throw new \Exception('Cannot add more items. Stock limit exceeded');
            }
            
            $sql = "UPDATE cart_items SET quantity = ?, updated_at = NOW() WHERE id = ?";
            return $this->query($sql, [$newQuantity, $existingItem['id']]) !== false;
        } else {
            // CREATE: Add new item
            $sql = "INSERT INTO cart_items (cart_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            return $this->query($sql, [$cartId, $productId, $quantity, $product['price']]) !== false;
        }
    }

    /**
     * UPDATE: Update item quantity in cart
     */
    public function updateItemQuantity(int $cartId, int $productId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($cartId, $productId);
        }

        // Check stock availability
        $product = $this->queryFirst("SELECT stock_quantity FROM products WHERE id = ?", [$productId]);
        if (!$product || $product['stock_quantity'] < $quantity) {
            throw new \Exception('Insufficient stock available');
        }

        $sql = "UPDATE cart_items SET quantity = ?, updated_at = NOW() 
                WHERE cart_id = ? AND product_id = ?";
        
        $result = $this->query($sql, [$quantity, $cartId, $productId]);
        return $result !== false && $result->rowCount() > 0;
    }

    /**
     * DELETE: Remove item from cart
     */
    public function removeItem(int $cartId, int $productId): bool
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $result = $this->query($sql, [$cartId, $productId]);
        return $result !== false && $result->rowCount() > 0;
    }

    /**
     * DELETE: Clear entire cart
     */
    public function clearCart(int $cartId): bool
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        return $this->query($sql, [$cartId]) !== false;
    }

    /**
     * UPDATE: Convert cart to order (mark as converted)
     */
    public function convertCart(int $cartId): bool
    {
        $sql = "UPDATE {$this->table} SET status = 'converted', updated_at = NOW() WHERE id = ?";
        return $this->query($sql, [$cartId]) !== false;
    }

    /**
     * CLEANUP: Remove expired carts
     */
    public function cleanupExpiredCarts(): int
    {
        $sql = "DELETE FROM {$this->table} WHERE expires_at < NOW() AND status = 'active'";
        $result = $this->query($sql);
        return $result ? $result->rowCount() : 0;
    }

    /**
     * Merge guest cart with user cart when user logs in
     */
    public function mergeGuestCartWithUserCart(string $guestSessionId, int $userId): bool
    {
        // Find guest cart
        $guestCart = $this->findActiveCart(null, $guestSessionId);
        if (!$guestCart) {
            return true; // No guest cart to merge
        }

        // Find or create user cart
        $userCart = $this->getOrCreateCart($userId, session_id());

        // Move all items from guest cart to user cart
        $guestItems = $this->query(
            "SELECT * FROM cart_items WHERE cart_id = ?", 
            [$guestCart['id']]
        );

        foreach ($guestItems as $item) {
            try {
                $this->addItem($userCart['id'], $item['product_id'], $item['quantity']);
            } catch (\Exception $e) {
                // Continue with other items if one fails
                continue;
            }
        }

        // Delete guest cart
        $this->delete($guestCart['id']);
        
        return true;
    }
}