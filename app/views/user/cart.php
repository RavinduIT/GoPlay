<?php
$title = 'Shopping Cart - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Professional Cart Page Styles */
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #dbeafe;
        --secondary-color: #0891b2;
        --accent-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-light: #9ca3af;
        --background-white: #ffffff;
        --background-light: #f9fafb;
        --border-color: #e5e7eb;
        --shadow-light: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-medium: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-heavy: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --border-radius: 8px;
        --border-radius-lg: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        line-height: 1.6;
        color: var(--text-primary);
        background-color: var(--background-light);
    }

    .cart-container {
        min-height: 100vh;
        padding: 2rem 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Header Section */
    .cart-header {
        margin-bottom: 2rem;
        text-align: center;
    }

    .cart-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .cart-subtitle {
        font-size: 1.1rem;
        color: var(--text-secondary);
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .breadcrumb a {
        color: var(--primary-color);
        text-decoration: none;
        transition: var(--transition);
    }

    .breadcrumb a:hover {
        color: var(--primary-dark);
    }

    /* Main Layout */
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
        align-items: start;
    }

    /* Cart Items Section */
    .cart-items {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-medium);
        overflow: hidden;
    }

    .cart-items-header {
        padding: 1.5rem;
        background: var(--background-light);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cart-items-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .cart-items-count {
        background: var(--primary-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .cart-items-list {
        padding: 1.5rem;
    }

    .cart-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid var(--border-color);
        transition: var(--transition);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item:hover {
        background: var(--background-light);
        margin: 0 -1.5rem;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        border-radius: var(--border-radius);
    }

    .item-image {
        width: 80px;
        height: 80px;
        border-radius: var(--border-radius);
        object-fit: cover;
        background: var(--background-light);
        border: 1px solid var(--border-color);
    }

    .item-details {
        flex: 1;
        min-width: 0;
    }

    .item-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .item-description {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .item-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .item-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--background-light);
        border-radius: var(--border-radius);
        padding: 0.25rem;
    }

    .quantity-btn {
        width: 32px;
        height: 32px;
        border: none;
        background: var(--background-white);
        color: var(--text-secondary);
        border-radius: var(--border-radius);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        font-weight: 600;
    }

    .quantity-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-display {
        min-width: 40px;
        text-align: center;
        font-weight: 600;
        color: var(--text-primary);
    }

    .remove-btn {
        background: none;
        border: none;
        color: var(--danger-color);
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: var(--border-radius);
        transition: var(--transition);
    }

    .remove-btn:hover {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
    }

    /* Cart Actions */
    .cart-actions {
        padding: 1.5rem;
        background: var(--background-light);
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .continue-shopping {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .continue-shopping:hover {
        color: var(--primary-dark);
    }

    .clear-cart-btn {
        background: var(--danger-color);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .clear-cart-btn:hover {
        background: #dc2626;
    }

    /* Order Summary */
    .order-summary {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-medium);
        position: sticky;
        top: 2rem;
    }

    .summary-header {
        padding: 1.5rem;
        background: var(--background-light);
        border-bottom: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
    }

    .summary-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .summary-content {
        padding: 1.5rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .summary-row:last-child {
        margin-bottom: 0;
    }

    .summary-label {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .summary-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    .summary-total {
        padding-top: 1rem;
        border-top: 2px solid var(--border-color);
        margin-top: 1rem;
    }

    .summary-total .summary-label {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .summary-total .summary-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .checkout-btn {
        width: 100%;
        background: var(--accent-color);
        color: white;
        border: none;
        padding: 1rem;
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .checkout-btn:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: var(--shadow-medium);
    }

    .checkout-btn:disabled {
        background: var(--text-light);
        cursor: not-allowed;
        transform: none;
    }

    /* Coupon Section */
    .coupon-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .coupon-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }

    .coupon-form {
        display: flex;
        gap: 0.5rem;
    }

    .coupon-input {
        flex: 1;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .coupon-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .coupon-btn {
        background: var(--text-secondary);
        color: white;
        border: none;
        padding: 0.75rem 1.25rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
    }

    .coupon-btn:hover {
        background: var(--text-primary);
    }

    /* Empty Cart State */
    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-medium);
    }

    .empty-cart-icon {
        font-size: 4rem;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .empty-cart-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .empty-cart-message {
        color: var(--text-secondary);
        margin-bottom: 2rem;
    }

    .shop-now-btn {
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        padding: 1rem 2rem;
        border-radius: var(--border-radius);
        font-weight: 600;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .shop-now-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    /* Loading State */
    .loading-spinner {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        color: var(--text-secondary);
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid var(--border-color);
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 1rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .cart-layout {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .order-summary {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 0.5rem;
        }

        .cart-title {
            font-size: 2rem;
        }

        .cart-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .item-controls {
            width: 100%;
            justify-content: space-between;
        }

        .cart-actions {
            flex-direction: column;
            gap: 1rem;
        }

        .coupon-form {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .cart-container {
            padding: 1rem 0;
        }

        .cart-items,
        .order-summary {
            border-radius: var(--border-radius);
        }

        .item-image {
            width: 60px;
            height: 60px;
        }
    }
</style>

<div class="cart-container">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="/">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="/shop">Shop</a>
            <i class="fas fa-chevron-right"></i>
            <span>Shopping Cart</span>
        </nav>

        <!-- Header -->
        <div class="cart-header">
            <h1 class="cart-title">Shopping Cart</h1>
            <p class="cart-subtitle">Review your selected items before checkout</p>
        </div>

        <!-- Loading State -->
        <div id="loading-state" class="loading-spinner">
            <div class="spinner"></div>
            <span>Loading your cart...</span>
        </div>

        <!-- Main Cart Content -->
        <div id="cart-content" class="cart-layout" style="display: none;">
            <!-- Cart Items Section -->
            <div class="cart-items">
                <div class="cart-items-header">
                    <h2 class="cart-items-title">Your Items</h2>
                    <span id="cart-count" class="cart-items-count">0 items</span>
                </div>

                <div class="cart-items-list" id="cart-items-list">
                    <!-- Cart items will be populated dynamically -->
                </div>

                <div class="cart-actions">
                    <a href="/shop" class="continue-shopping">
                        <i class="fas fa-arrow-left"></i>
                        Continue Shopping
                    </a>
                    <button class="clear-cart-btn" onclick="clearCart()">
                        <i class="fas fa-trash"></i>
                        Clear Cart
                    </button>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-header">
                    <h3 class="summary-title">Order Summary</h3>
                </div>

                <div class="summary-content">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value" id="subtotal">LKR 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Shipping</span>
                        <span class="summary-value" id="shipping">LKR 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Tax (18%)</span>
                        <span class="summary-value" id="tax">LKR 0</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span class="summary-label">Total</span>
                        <span class="summary-value" id="total">LKR 0</span>
                    </div>

                    <button class="checkout-btn" id="checkout-btn" onclick="proceedToCheckout()" disabled>
                        <i class="fas fa-lock"></i>
                        Proceed to Checkout
                    </button>

                    <!-- Coupon Section -->
                    <div class="coupon-section">
                        <h4 class="coupon-title">Have a coupon code?</h4>
                        <form class="coupon-form" onsubmit="applyCoupon(event)">
                            <input type="text" class="coupon-input" id="coupon-code" placeholder="Enter coupon code">
                            <button type="submit" class="coupon-btn">Apply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty Cart State -->
        <div id="empty-cart" class="empty-cart" style="display: none;">
            <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2 class="empty-cart-title">Your cart is empty</h2>
            <p class="empty-cart-message">Looks like you haven't added any items to your cart yet. Start shopping to fill it up!</p>
            <a href="/shop" class="shop-now-btn">
                <i class="fas fa-shopping-bag"></i>
                Start Shopping
            </a>
        </div>
    </div>
</div>

<script>
    // Global cart data
    let cartData = null;
    let cartLoading = false;

    // Initialize cart on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCart();
    });

    // Load cart data from API
    async function loadCart() {
        try {
            cartLoading = true;
            showLoading(true);

            const response = await fetch('/api/cart', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();
            console.log('Cart API response:', data);

            if (data.success) {
                cartData = data.data; // The cart data is in data.data from the API
                displayCart();
            } else {
                throw new Error(data.message || 'Failed to load cart');
            }
        } catch (error) {
            console.error('Cart loading error:', error);
            showEmptyCart();
        } finally {
            cartLoading = false;
            showLoading(false);
        }
    }

    // Display cart items
    function displayCart() {
        if (!cartData || !cartData.items || cartData.items.length === 0) {
            showEmptyCart();
            return;
        }

        showCartContent();

        // Update cart count using totals from database
        const totalItems = cartData.totals ? cartData.totals.total_quantity : cartData.items.reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById('cart-count').textContent = `${totalItems} item${totalItems !== 1 ? 's' : ''}`;

        // Render cart items
        const cartItemsList = document.getElementById('cart-items-list');
        cartItemsList.innerHTML = cartData.items.map(item => createCartItemHTML(item)).join('');

        // Update summary
        updateOrderSummary();
    }

    // Create HTML for a cart item
    function createCartItemHTML(item) {
        // Parse images from database - it's stored as JSON string
        let imageUrl = '/public/assets/images/products/default.jpg';
        if (item.images) {
            try {
                const images = typeof item.images === 'string' ? JSON.parse(item.images) : item.images;
                if (Array.isArray(images) && images.length > 0) {
                    imageUrl = images[0];
                }
            } catch (e) {
                console.log('Error parsing images:', e);
            }
        }

        const itemTotal = item.unit_price * item.quantity;
        const productName = item.product_name || item.name || 'Product';
        const brandInfo = item.brand ? ` by ${item.brand}` : '';

        return `
            <div class="cart-item" data-item-id="${item.product_id}">
                <img src="${imageUrl}" alt="${productName}" class="item-image" onerror="this.src='/public/assets/images/products/default.jpg'">
                <div class="item-details">
                    <h3 class="item-name">${productName}${brandInfo}</h3>
                    <p class="item-description">${item.category_name || 'Sports Equipment'} • SKU: ${item.sku || 'N/A'}</p>
                    <div class="item-price">LKR ${new Intl.NumberFormat().format(itemTotal)}</div>
                    ${item.stock_quantity <= 5 ? `<div style="color: #f59e0b; font-size: 0.8rem; margin-top: 0.25rem;">Only ${item.stock_quantity} left in stock</div>` : ''}
                </div>
                <div class="item-controls">
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="updateQuantity(${item.product_id}, ${item.quantity - 1})" ${item.quantity <= 1 ? 'disabled' : ''}>
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="quantity-display">${item.quantity}</span>
                        <button class="quantity-btn" onclick="updateQuantity(${item.product_id}, ${item.quantity + 1})" ${item.quantity >= item.stock_quantity ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button class="remove-btn" onclick="removeItem(${item.product_id})" title="Remove item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
    }

    // Update order summary
    function updateOrderSummary() {
        if (!cartData || !cartData.items) return;

        // Calculate subtotal from items (using unit_price from database)
        const subtotal = cartData.totals ? cartData.totals.subtotal : cartData.items.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
        const shipping = subtotal > 5000 ? 0 : 500; // Free shipping over LKR 5000
        const taxRate = 0.18; // 18% tax
        const tax = subtotal * taxRate;
        const total = subtotal + shipping + tax;

        document.getElementById('subtotal').textContent = `LKR ${new Intl.NumberFormat().format(subtotal)}`;
        document.getElementById('shipping').textContent = shipping === 0 ? 'Free' : `LKR ${new Intl.NumberFormat().format(shipping)}`;
        document.getElementById('tax').textContent = `LKR ${new Intl.NumberFormat().format(tax)}`;
        document.getElementById('total').textContent = `LKR ${new Intl.NumberFormat().format(total)}`;

        // Enable/disable checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.disabled = cartData.items.length === 0;
    }

    // Update item quantity
    async function updateQuantity(productId, newQuantity) {
        if (newQuantity < 1) return;

        try {
            const response = await fetch('/api/cart/update', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: newQuantity
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update with fresh cart data from server
                cartData = data.data;
                displayCart();
            } else {
                throw new Error(data.message || 'Failed to update quantity');
            }
        } catch (error) {
            console.error('Update quantity error:', error);
            alert('Failed to update quantity. Please try again.');
        }
    }

    // Remove item from cart
    async function removeItem(productId) {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }

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

            const data = await response.json();

            if (data.success) {
                // Update with fresh cart data from server
                cartData = data.data;
                displayCart();
            } else {
                throw new Error(data.message || 'Failed to remove item');
            }
        } catch (error) {
            console.error('Remove item error:', error);
            alert('Failed to remove item. Please try again.');
        }
    }

    // Clear entire cart
    async function clearCart() {
        if (!confirm('Are you sure you want to clear your entire cart?')) {
            return;
        }

        try {
            const response = await fetch('/api/cart/clear', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                cartData = { items: [], totals: { total_quantity: 0, subtotal: 0 } };
                showEmptyCart();
            } else {
                throw new Error(data.message || 'Failed to clear cart');
            }
        } catch (error) {
            console.error('Clear cart error:', error);
            alert('Failed to clear cart. Please try again.');
        }
    }

    // Apply coupon code
    async function applyCoupon(event) {
        event.preventDefault();

        const couponCode = document.getElementById('coupon-code').value.trim();
        if (!couponCode) {
            alert('Please enter a coupon code');
            return;
        }

        try {
            // For now, show a message (implement coupon API later)
            alert('Coupon functionality will be implemented soon!');
        } catch (error) {
            console.error('Coupon error:', error);
            alert('Failed to apply coupon. Please try again.');
        }
    }

    // Proceed to checkout
    function proceedToCheckout() {
        if (!cartData || cartData.items.length === 0) {
            alert('Your cart is empty');
            return;
        }

        // Redirect to checkout page
        window.location.href = '/checkout/contact-details';
    }

    // Show/hide loading state
    function showLoading(show) {
        document.getElementById('loading-state').style.display = show ? 'flex' : 'none';
    }

    // Show cart content
    function showCartContent() {
        document.getElementById('cart-content').style.display = 'grid';
        document.getElementById('empty-cart').style.display = 'none';
    }

    // Show empty cart state
    function showEmptyCart() {
        document.getElementById('cart-content').style.display = 'none';
        document.getElementById('empty-cart').style.display = 'block';
    }
</script>