<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Cart;

class CartController extends BaseController
{
    private ?Cart $cartModel = null;
    
    private function getCartModel(): Cart
    {
        if ($this->cartModel === null) {
            $this->cartModel = new Cart();
        }
        return $this->cartModel;
    }

    /**
     * Get cart session info
     */
    private function getCartSession(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'session_id' => session_id()
        ];
    }

    /**
     * GET /api/cart - Get current cart details
     */
    public function getCart(Request $request): Response
    {
        try {
            $session = $this->getCartSession();
            $cartModel = $this->getCartModel();
            
            // Get or create cart
            $cart = $cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
            
            // Get full cart details
            $cartDetails = $cartModel->getCartDetails($cart['id']);
            
            return $this->json([
                'success' => true,
                'data' => $cartDetails
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/cart/add - Add item to cart
     */
    public function addToCart(Request $request): Response
    {
        try {
            $data = $request->getJsonBody();
            
            if (!isset($data['product_id'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product ID is required'
                ], 400);
            }

            $productId = (int) $data['product_id'];
            $quantity = (int) ($data['quantity'] ?? 1);
            $selectedSize = $data['selected_size'] ?? null;
            $selectedColor = $data['selected_color'] ?? null;

            if ($quantity < 1) {
                return $this->json([
                    'success' => false,
                    'message' => 'Quantity must be at least 1'
                ], 400);
            }

            $session = $this->getCartSession();
            $cartModel = $this->getCartModel();
            
            // Get or create cart
            $cart = $cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
            
            // Add item to cart with size and color
            $success = $cartModel->addItem($cart['id'], $productId, $quantity, $selectedSize, $selectedColor);
            
            if ($success) {
                // Return updated cart details
                $cartDetails = $cartModel->getCartDetails($cart['id']);
                
                return $this->json([
                    'success' => true,
                    'message' => 'Item added to cart',
                    'data' => $cartDetails
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to add item to cart'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * PUT /api/cart/update - Update item quantity
     */
    public function updateCartItem(Request $request): Response
    {
        try {
            $data = $request->getJsonBody();
            
            if (!isset($data['product_id'], $data['quantity'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product ID and quantity are required'
                ], 400);
            }

            $productId = (int) $data['product_id'];
            $quantity = (int) $data['quantity'];

            $session = $this->getCartSession();
            $cartModel = $this->getCartModel();
            
            // Find existing cart
            $cart = $cartModel->findActiveCart($session['user_id'], $session['session_id']);
            
            if (!$cart) {
                return $this->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }
            
            // Update item quantity
            $success = $cartModel->updateItemQuantity($cart['id'], $productId, $quantity);
            
            if ($success) {
                // Return updated cart details
                $cartDetails = $cartModel->getCartDetails($cart['id']);
                
                return $this->json([
                    'success' => true,
                    'message' => $quantity > 0 ? 'Item quantity updated' : 'Item removed from cart',
                    'data' => $cartDetails
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Item not found in cart'
                ], 404);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * DELETE /api/cart/remove - Remove item from cart
     */
    public function removeFromCart(Request $request): Response
    {
        try {
            $data = $request->getJsonBody();
            
            if (!isset($data['product_id'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product ID is required'
                ], 400);
            }

            $productId = (int) $data['product_id'];

            $session = $this->getCartSession();
            $cartModel = $this->getCartModel();
            
            // Find existing cart
            $cart = $cartModel->findActiveCart($session['user_id'], $session['session_id']);
            
            if (!$cart) {
                return $this->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }
            
            // Remove item from cart
            $success = $cartModel->removeItem($cart['id'], $productId);
            
            if ($success) {
                // Return updated cart details
                $cartDetails = $cartModel->getCartDetails($cart['id']);
                
                return $this->json([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'data' => $cartDetails
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Item not found in cart'
                ], 404);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/cart/clear - Clear entire cart
     */
    public function clearCart(Request $request): Response
    {
        try {
            $session = $this->getCartSession();
            $cartModel = $this->getCartModel();
            
            // Find existing cart
            $cart = $cartModel->findActiveCart($session['user_id'], $session['session_id']);
            
            if (!$cart) {
                return $this->json([
                    'success' => false,
                    'message' => 'Cart not found'
                ], 404);
            }
            
            // Clear cart
            $success = $cartModel->clearCart($cart['id']);
            
            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Cart cleared successfully'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to clear cart'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/cart/count - Get cart item count only
     */
    public function getCartCount(Request $request): Response
    {
        try {
            $session = $this->getCartSession();
            $cartModel = $this->getCartModel();
            
            // Find existing cart
            $cart = $cartModel->findActiveCart($session['user_id'], $session['session_id']);
            
            if (!$cart) {
                return $this->json([
                    'success' => true,
                    'count' => 0
                ]);
            }
            
            // Get cart summary
            $cartDetails = $cartModel->getCartDetails($cart['id']);
            
            return $this->json([
                'success' => true,
                'count' => $cartDetails['totals']['total_quantity']
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'count' => 0
            ], 500);
        }
    }

    /**
     * POST /api/cart/merge - Merge guest cart with user cart (when user logs in)
     */
    public function mergeGuestCart(Request $request): Response
    {
        try {
            $data = $request->getJsonBody();
            $guestSessionId = $data['guest_session_id'] ?? null;
            
            if (!$guestSessionId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Guest session ID is required'
                ], 400);
            }

            $session = $this->getCartSession();
            
            if (!$session['user_id']) {
                return $this->json([
                    'success' => false,
                    'message' => 'User must be logged in'
                ], 401);
            }

            $cartModel = $this->getCartModel();
            
            // Merge carts
            $success = $cartModel->mergeGuestCartWithUserCart($guestSessionId, $session['user_id']);
            
            if ($success) {
                // Return updated user cart
                $userCart = $cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
                $cartDetails = $cartModel->getCartDetails($userCart['id']);
                
                return $this->json([
                    'success' => true,
                    'message' => 'Carts merged successfully',
                    'data' => $cartDetails
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to merge carts'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Checkout - Redirect to checkout page
     */
    public function checkout(Request $request): Response
    {
        try {
            $session = $this->getCartSession();
            $cartModel = $this->getCartModel();
            
            // Get or create cart
            $cart = $cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
            
            // Check if cart has items
            $cartDetails = $cartModel->getCartDetails($cart['id']);
            
            if (empty($cartDetails['items'])) {
                return $this->redirect('/shop');
            }
            
            // Redirect to checkout contact details page
            return $this->redirect('/checkout/contact-details');
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
