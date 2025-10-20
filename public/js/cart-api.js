/**
 * Cart API JavaScript - Database-driven cart functionality
 * Replaces the inline cart JS in shop.php with API calls
 */

class CartAPI {
    constructor() {
        this.cart = {
            items: [],
            totals: {
                item_count: 0,
                total_quantity: 0,
                subtotal: 0
            }
        };
        
        // Initialize cart on page load
        this.loadCart();
    }

    /**
     * Load cart from database via API
     */
    async loadCart() {
        try {
            const response = await fetch('/api/cart', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const result = await response.json();
            
            if (result.success && result.data) {
                this.cart = result.data;
                this.updateCartDisplay();
            }
        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    /**
     * Add item to cart via API
     */
    async addToCart(productId, quantity = 1) {
        try {
            const response = await fetch('/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const result = await response.json();
            
            if (result.success) {
                this.cart = result.data;
                this.updateCartDisplay();
                // this.showCartNotification('Item added to cart!', 'success');
                this.showCartSummary();
                return true;
            } else {
                // this.showCartNotification(result.message || 'Failed to add item', 'error');
                return false;
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            // this.showCartNotification('Error adding item to cart', 'error');
            return false;
        }
    }

    /**
     * Update item quantity in cart
     */
    async updateCartItem(productId, quantity) {
        try {
            const response = await fetch('/api/cart/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const result = await response.json();
            
            if (result.success) {
                this.cart = result.data;
                this.updateCartDisplay();
                // this.showCartNotification(result.message, 'success');
                return true;
            } else {
                // this.showCartNotification(result.message || 'Failed to update item', 'error');
                return false;
            }
        } catch (error) {
            console.error('Error updating cart item:', error);
            // this.showCartNotification('Error updating item', 'error');
            return false;
        }
    }

    /**
     * Remove item from cart
     */
    async removeFromCart(productId) {
        try {
            const response = await fetch('/api/cart/remove', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId
                })
            });

            const result = await response.json();
            
            if (result.success) {
                this.cart = result.data;
                this.updateCartDisplay();
                // this.showCartNotification('Item removed from cart', 'success');
                return true;
            } else {
                // this.showCartNotification(result.message || 'Failed to remove item', 'error');
                return false;
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            // this.showCartNotification('Error removing item', 'error');
            return false;
        }
    }

    /**
     * Clear entire cart
     */
    async clearCart() {
        try {
            const response = await fetch('/api/cart/clear', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const result = await response.json();
            
            if (result.success) {
                this.cart = {
                    items: [],
                    totals: {
                        item_count: 0,
                        total_quantity: 0,
                        subtotal: 0
                    }
                };
                this.updateCartDisplay();
                // this.showCartNotification('Cart cleared', 'success');
                return true;
            } else {
                // this.showCartNotification(result.message || 'Failed to clear cart', 'error');
                return false;
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
            // this.showCartNotification('Error clearing cart', 'error');
            return false;
        }
    }

    /**
     * Get cart count only (lightweight call)
     */
    async getCartCount() {
        try {
            const response = await fetch('/api/cart/count', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const result = await response.json();
            
            if (result.success) {
                return result.count || 0;
            }
            return 0;
        } catch (error) {
            console.error('Error getting cart count:', error);
            return 0;
        }
    }

    /**
     * Update cart display with professional UI
     */
    updateCartDisplay() {
        const cartCountElement = document.getElementById('cart-count');
        const cartTotalElement = document.getElementById('cart-total');
        const cartSubtotalElement = document.getElementById('cart-subtotal');
        const cartItemCountElement = document.getElementById('cart-item-count');
        const cartItemsElement = document.getElementById('cart-items');
        const cartEmptyElement = document.getElementById('cart-empty');
        const cartFooterElement = document.getElementById('cart-footer');

        const itemCount = this.cart.totals.total_quantity || 0;
        const subtotal = Number(this.cart.totals.subtotal || 0);

        // Update cart count badge
        if (cartCountElement) {
            cartCountElement.textContent = itemCount;
            if (itemCount > 0) {
                cartCountElement.style.display = 'flex';
                // Trigger pulse animation
                cartCountElement.style.animation = 'none';
                setTimeout(() => {
                    cartCountElement.style.animation = 'cartCountPulse 0.6s ease-out';
                }, 10);
            } else {
                cartCountElement.style.display = 'none';
            }
        }

        // Update totals
        if (cartTotalElement) {
            cartTotalElement.textContent = subtotal.toLocaleString();
        }
        if (cartSubtotalElement) {
            cartSubtotalElement.textContent = subtotal.toLocaleString();
        }
        if (cartItemCountElement) {
            cartItemCountElement.textContent = itemCount;
        }

        // Show/hide empty state and footer
        const hasItems = this.cart.items && Array.isArray(this.cart.items) && this.cart.items.length > 0;
        
        if (cartEmptyElement) {
            cartEmptyElement.style.display = hasItems ? 'none' : 'block';
        }
        if (cartFooterElement) {
            cartFooterElement.style.display = hasItems ? 'block' : 'none';
        }

        // Update cart items
        if (cartItemsElement) {
            if (hasItems) {
                cartItemsElement.innerHTML = this.cart.items.map(item => `
                    <div class="cart-item cart-item-enter">
                        <div class="cart-item-image">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div class="cart-item-details">
                            <h4 class="cart-item-name">${item.product_name}</h4>
                            <div class="cart-item-info">
                                <span>LKR ${Number(item.unit_price).toLocaleString()}</span>
                                <span>•</span>
                                <span>${item.brand || 'Brand'}</span>
                            </div>
                            <div class="cart-item-quantity">
                                <button class="qty-btn" onclick="cartAPI.updateQuantity(${item.product_id}, ${item.quantity - 1})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="qty-value">${item.quantity}</span>
                                <button class="qty-btn" onclick="cartAPI.updateQuantity(${item.product_id}, ${item.quantity + 1})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="cart-item-actions">
                            <div class="cart-item-price">LKR ${Number(item.total_price).toLocaleString()}</div>
                            <button class="remove-btn" onclick="cartAPI.removeFromCart(${item.product_id})" title="Remove item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            } else {
                cartItemsElement.innerHTML = '';
            }
        }
    }

    /**
     * Update item quantity (helper function)
     */
    async updateQuantity(productId, newQuantity) {
        if (newQuantity <= 0) {
            return this.removeFromCart(productId);
        }
        return this.updateCartItem(productId, newQuantity);
    }

    /**
     * Show cart summary popup
     */
    showCartSummary() {
        const cartSummary = document.getElementById('cart-summary');
        if (cartSummary) {
            cartSummary.classList.add('visible');
            setTimeout(() => {
                cartSummary.classList.remove('visible');
            }, 3000);
        }
    }

    /**
     * Toggle cart summary visibility
     */
    toggleCart() {
        const cartSummary = document.getElementById('cart-summary');
        if (cartSummary) {
            cartSummary.classList.toggle('visible');
        }
    }

    /**
     * Show professional notification message
     */
    showCartNotification(message, type = 'info') {
        // Remove existing notification
        const existingNotification = document.getElementById('cart-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create new notification
        const notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.className = `cart-notification ${type}`;
        
        // Set icon based on type
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        notification.innerHTML = `
            <div class="notification-icon">
                <i class="${icons[type] || icons.info}"></i>
            </div>
            <span>${message}</span>
        `;

        document.body.appendChild(notification);

        // Show notification
        requestAnimationFrame(() => {
            notification.classList.add('visible');
        });

        // Hide after 3 seconds
        setTimeout(() => {
            notification.classList.remove('visible');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }

    /**
     * Proceed to checkout
     */
    async checkout() {
        if (!this.cart.items || this.cart.items.length === 0) {
            // this.showCartNotification('Your cart is empty!', 'error');
            return false;
        }

        // Redirect to checkout page
        window.location.href = '/payment?type=shop';
        return true;
    }

    /**
     * Merge guest cart when user logs in
     */
    async mergeGuestCart(guestSessionId) {
        try {
            const response = await fetch('/api/cart/merge', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    guest_session_id: guestSessionId
                })
            });

            const result = await response.json();
            
            if (result.success) {
                this.cart = result.data;
                this.updateCartDisplay();
                // this.showCartNotification('Cart updated successfully', 'success');
                return true;
            } else {
                console.error('Failed to merge cart:', result.message);
                return false;
            }
        } catch (error) {
            console.error('Error merging cart:', error);
            return false;
        }
    }
}

// Initialize cart API globally
const cartAPI = new CartAPI();

// Global functions for backward compatibility with existing buttons
function addToCart(productId, quantity = 1) {
    return cartAPI.addToCart(productId, quantity);
}

function toggleCart() {
    cartAPI.toggleCart();
}

function checkout() {
    return cartAPI.checkout();
}

function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        return cartAPI.clearCart();
    }
    return false;
}