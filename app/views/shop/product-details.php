
<?php 
$title = ($product['name'] ?? 'Product') . ' - GoPlay Sports Shop';
$additionalCSS = ['/public/css/components/cart.css'];
$additionalJS = ['/public/js/cart-api.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* Professional Product Details Styles */
:root {
    --primary-color: #2563eb;
    --primary-dark: #1e40af;
    --primary-light: #f1f5f9;
    --secondary-color: #0891b2;
    --accent-color: #ffc107;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-primary: #2c3e50;
    --text-secondary: #6c757d;
    --text-light: #adb5bd;
    --text-white: #ffffff;
    --background-white: #ffffff;
    --background-light: #f8f9fa;
    --border-color: #e9ecef;
    --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
    --shadow-medium: 0 4px 16px rgba(0,0,0,0.12);
    --shadow-heavy: 0 8px 32px rgba(0,0,0,0.16);
    --border-radius: 12px;
    --border-radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    line-height: 1.6;
    color: var(--text-primary);
    background-color: var(--background-light);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* Loading & Error States */
.loading-container, .error-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    flex-direction: column;
    gap: 1rem;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid var(--border-color);
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Breadcrumb */
.breadcrumb {
    padding: 1rem 0;
    background: var(--background-white);
    border-bottom: 1px solid var(--border-color);
}

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.breadcrumb a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: var(--transition);
}

.breadcrumb a:hover {
    color: var(--primary-color);
}

.breadcrumb .current {
    color: var(--text-primary);
    font-weight: 600;
}

/* Product Details Container */
.product-details {
    padding: 2rem 0;
    background: var(--background-light);
}

/* Product Header */
.product-header {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
    background: var(--background-white);
    padding: 2rem;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-light);
}

/* Product Gallery */
.product-gallery {
    position: relative;
}

.main-image {
    width: 100%;
    height: 500px;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-medium);
    margin-bottom: 1rem;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.main-image:hover img {
    transform: scale(1.05);
}

.thumbnail-gallery {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
}

.thumbnail {
    width: 100%;
    height: 80px;
    border-radius: var(--border-radius);
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: var(--transition);
}

.thumbnail.active {
    border-color: var(--primary-color);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail:hover {
    border-color: var(--primary-light);
    transform: translateY(-2px);
}

/* Product Info */
.product-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.product-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--primary-light);
    color: var(--primary-color);
    border-radius: var(--border-radius);
    font-size: 0.85rem;
    font-weight: 600;
    width: fit-content;
}

.product-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.product-category {
    color: var(--text-secondary);
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.rating-section {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stars {
    display: flex;
    gap: 2px;
}

.star {
    color: var(--accent-color);
    font-size: 1.2rem;
}

.rating-text {
    font-weight: 600;
    color: var(--text-primary);
}

.reviews-link {
    color: var(--text-secondary);
    text-decoration: none;
    transition: var(--transition);
}

.reviews-link:hover {
    color: var(--primary-color);
}

.price-section {
    background: var(--background-light);
    padding: 1.5rem;
    border-radius: var(--border-radius);
    border-left: 4px solid var(--primary-color);
}

.price-current {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.price-original {
    font-size: 1.5rem;
    color: var(--text-light);
    text-decoration: line-through;
    margin-left: 1rem;
}

.price-discount {
    background: var(--danger-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: var(--border-radius);
    font-size: 0.9rem;
    font-weight: 600;
    margin-left: 1rem;
}

.stock-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: var(--border-radius);
    font-weight: 500;
}

.in-stock {
    background: #d1fae5;
    color: #065f46;
}

.out-of-stock {
    background: #fee2e2;
    color: #991b1b;
}

.low-stock {
    background: #fef3c7;
    color: #92400e;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.quantity-label {
    font-weight: 600;
    color: var(--text-primary);
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    overflow: hidden;
}

.quantity-btn {
    background: var(--background-white);
    border: none;
    padding: 0.75rem 1rem;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-secondary);
    transition: var(--transition);
    border-right: 1px solid var(--border-color);
}

.quantity-btn:hover {
    background: var(--background-light);
    color: var(--primary-color);
}

.quantity-btn:last-child {
    border-right: none;
    border-left: 1px solid var(--border-color);
}

.quantity-input {
    border: none;
    padding: 0.75rem;
    text-align: center;
    font-size: 1rem;
    font-weight: 600;
    width: 80px;
    background: var(--background-white);
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn {
    padding: 1rem 2rem;
    border: none;
    border-radius: var(--border-radius);
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
    min-width: 180px;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.btn-secondary {
    background: var(--background-white);
    color: var(--text-primary);
    border: 2px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--background-light);
    border-color: var(--primary-color);
    color: var(--primary-color);
}

/* Product Content */
.product-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 3rem;
}

.main-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.content-section {
    background: var(--background-white);
    padding: 2rem;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-light);
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.product-description {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--text-secondary);
}

/* Specifications */
.specs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.spec-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: var(--background-light);
    border-radius: var(--border-radius);
}

.spec-label {
    font-weight: 500;
    color: var(--text-secondary);
}

.spec-value {
    font-weight: 600;
    color: var(--text-primary);
}

/* Reviews */
.reviews-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.review-item {
    padding: 1.5rem 0;
    border-bottom: 1px solid var(--border-color);
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.reviewer-name {
    font-weight: 600;
    color: var(--text-primary);
}

.review-date {
    color: var(--text-light);
    font-size: 0.9rem;
}

.review-text {
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Sidebar */
.sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sidebar-section {
    background: var(--background-white);
    padding: 1.5rem;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-light);
}

/* Shop Owner Sidebar */
.shop-info-content {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.shop-name-title {
    margin: 0;
    font-size: 1.1rem;
    color: var(--text-primary);
    font-weight: 600;
}

.shop-business-subtitle {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 0.25rem 0 0 0;
}

.shop-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.shop-rating .stars {
    color: var(--accent-color);
    font-size: 0.9rem;
}

.shop-rating .rating-text {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.shop-description {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.shop-description p {
    font-size: 0.9rem;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

.shop-contact {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-color);
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.contact-item:last-child {
    margin-bottom: 0;
}

.contact-item i {
    color: var(--primary-color);
    width: 18px;
    margin-top: 0.15rem;
    font-size: 0.95rem;
}

.shop-social {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    flex-wrap: wrap;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--background-light);
    color: var(--text-secondary);
    transition: var(--transition);
    text-decoration: none;
    font-size: 1rem;
}

.social-link:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.visit-shop-btn {
    width: 100%;
    padding: 0.875rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.visit-shop-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-medium);
}

.visit-shop-btn i {
    font-size: 1rem;
}

/* Related Products */
.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.related-product-card {
    background: var(--background-white);
    border-radius: var(--border-radius);
    padding: 1rem;
    box-shadow: var(--shadow-light);
    transition: var(--transition);
    cursor: pointer;
}

.related-product-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.related-product-image {
    width: 100%;
    height: 150px;
    border-radius: var(--border-radius);
    overflow: hidden;
    margin-bottom: 1rem;
}

.related-product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-product-name {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.related-product-price {
    color: var(--primary-color);
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 0 1rem;
    }

    .product-header,
    .product-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .product-title {
        font-size: 2rem;
    }

    .price-current {
        font-size: 2rem;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        min-width: auto;
    }

    .related-products-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .product-title {
        font-size: 1.5rem;
    }

    .price-current {
        font-size: 1.5rem;
    }

    .content-section,
    .sidebar-section {
        padding: 1.5rem;
    }
}

/* Feature Badges */
.feature-badges {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}
.feature-badge {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.8rem;
    color: var(--text-secondary);
    background: var(--background-light);
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    border: 1px solid var(--border-color);
}
.feature-badge i { color: var(--success-color); }

/* Sold By Card */
.sold-by-card {
    background: var(--background-white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-light);
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    display: none;
    align-items: flex-start;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.sold-by-card.visible { display: flex; }
.sold-by-logo {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: var(--background-light);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.8rem;
    color: var(--primary-color);
    overflow: hidden;
    border: 1px solid var(--border-color);
}
.sold-by-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
.sold-by-logo i { display: flex; }
.sold-by-main { flex: 1; min-width: 200px; }
.sold-by-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
}
.sold-by-name { font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.4rem; }
.sold-by-meta {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
    font-size: 0.86rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}
.sold-by-meta i { color: var(--primary-color); margin-right: 0.25rem; }
.sold-by-meta .shop-stars-inline { color: var(--accent-color); font-size: 0.8rem; }
.sold-by-desc { font-size: 0.9rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 0.6rem; }
.sold-by-socials { display: flex; gap: 0.5rem; margin-top: 0.25rem; }
.sold-by-actions { display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end; justify-content: center; }
.btn-visit-shop {
    padding: 0.6rem 1.2rem;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    white-space: nowrap;
    text-decoration: none;
}
.btn-visit-shop:hover { background: var(--primary-dark); }

/* Tabs */
.tabs-container {
    background: var(--background-white);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-light);
    margin-bottom: 2rem;
    overflow: hidden;
}
.tab-bar {
    display: flex;
    border-bottom: 2px solid var(--border-color);
    background: var(--background-white);
    overflow-x: auto;
}
.tab-btn {
    padding: 1rem 1.5rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    white-space: nowrap;
}
.tab-btn:hover { color: var(--primary-color); background: var(--background-light); }
.tab-btn.active { color: var(--primary-color); border-bottom-color: var(--primary-color); }
.tab-pane { display: none; padding: 2rem; }
.tab-pane.active { display: block; }

/* Review orders note */
.review-orders-note {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    background: var(--primary-light);
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
    font-size: 0.92rem;
    color: var(--text-secondary);
}
.review-orders-note i { color: var(--primary-color); flex-shrink: 0; }
.review-orders-note a { color: var(--primary-color); font-weight: 600; text-decoration: none; }
.review-orders-note a:hover { text-decoration: underline; }
</style>

<!-- Product Details Content -->

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav class="breadcrumb-nav">
            <a href="/">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="/shop">Shop</a>
            <i class="fas fa-chevron-right"></i>
            <span id="breadcrumb-category">Category</span>
            <i class="fas fa-chevron-right"></i>
            <span class="current" id="breadcrumb-current">Product</span>
        </nav>
    </div>
</div>

<!-- Loading State -->
<div id="loading" class="loading-container">
    <div class="loading-spinner"></div>
    <p>Loading product details...</p>
</div>

<!-- Error State -->
<div id="error-state" class="container" style="display: none;">
    <div class="error-container">
        <h2 class="error-title">Product Not Found</h2>
        <p class="error-message">The requested product could not be found.</p>
        <a href="/shop" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i>
            Browse All Products
        </a>
    </div>
</div>

<!-- Product Details Content -->
<div id="product-content" class="container" style="display: none;">
    <!-- Product Header -->
    <div class="product-header">
        <div class="product-gallery">
            <div class="main-image">
                <img id="main-product-image" src="" alt="Product Image">
            </div>
            <div id="thumbnail-gallery" class="thumbnail-gallery">
                <!-- Thumbnails will be populated dynamically -->
            </div>
        </div>
        
        <div class="product-info">
            <div id="product-badge" class="product-badge" style="display: none;">
                <i class="fas fa-star"></i>
                <span id="badge-text">Featured</span>
            </div>
            
            <div id="product-category" class="product-category">Category</div>
            <h1 id="product-title" class="product-title">Product Name</h1>
            
            <div class="rating-section">
                <div class="rating">
                    <div id="product-stars" class="stars"></div>
                    <span id="product-rating" class="rating-text">4.5</span>
                </div>
                <a href="#" id="reviews-link" class="reviews-link" onclick="switchTab('reviews', document.querySelector('[data-tab=reviews]')); return false;">(0 reviews)</a>
            </div>
            
            <div class="price-section">
                <span id="product-price-current" class="price-current">LKR 0</span>
                <span id="product-price-original" class="price-original" style="display: none;">LKR 0</span>
                <span id="product-price-discount" class="price-discount" style="display: none;">0% OFF</span>
            </div>
            
            <div id="stock-info" class="stock-info in-stock">
                <i class="fas fa-check-circle"></i>
                <span id="stock-text">In Stock</span>
            </div>
            
            <div class="quantity-selector">
                <span class="quantity-label">Quantity:</span>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="updateQuantity(-1)">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="quantity-input" class="quantity-input" value="1" min="1" max="10">
                    <button class="quantity-btn" onclick="updateQuantity(1)">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            
            <div class="action-buttons">
                <button id="add-to-cart-btn" class="btn btn-primary" onclick="addToCartFromDetails()">
                    <i class="fas fa-cart-plus"></i>
                    Add to Cart
                </button>
                <a href="/shop" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Shop
                </a>
            </div>

            <div class="feature-badges">
                <span class="feature-badge"><i class="fas fa-truck"></i> Free Delivery over LKR 5,000</span>
                <span class="feature-badge"><i class="fas fa-undo"></i> 30-day Returns</span>
                <span class="feature-badge"><i class="fas fa-shield-alt"></i> Secure Payment</span>
            </div>
        </div>
    </div>

    <!-- Sold By Card -->
    <div id="sold-by-card" class="sold-by-card">
        <div class="sold-by-logo" id="sold-by-logo">
            <i class="fas fa-store"></i>
            <!-- img replaced dynamically by JS; FA icon is the fallback -->
        </div>
        <div class="sold-by-main">
            <div class="sold-by-label">Sold By</div>
            <div class="sold-by-name" id="sold-by-name">Shop Name</div>
            <div class="sold-by-meta" id="sold-by-meta"></div>
            <div class="sold-by-desc" id="sold-by-desc" style="display:none;"></div>
            <div class="sold-by-socials" id="sold-by-socials"></div>
        </div>
        <div class="sold-by-actions">
            <button id="sold-by-visit-btn" class="btn-visit-shop" style="display:none;">
                <i class="fas fa-store"></i> Visit Shop
            </button>
        </div>
    </div>

    <!-- Tabs: Description | Specifications | Reviews -->
    <div class="tabs-container">
        <div class="tab-bar">
            <button class="tab-btn active" data-tab="description" onclick="switchTab('description', this)">
                <i class="fas fa-info-circle"></i> Description
            </button>
            <button class="tab-btn" data-tab="specifications" onclick="switchTab('specifications', this)">
                <i class="fas fa-cog"></i> Specifications
            </button>
            <button class="tab-btn" data-tab="reviews" onclick="switchTab('reviews', this)">
                <i class="fas fa-star"></i> Reviews (<span id="reviews-count">0</span>)
            </button>
        </div>

        <!-- Description tab -->
        <div id="tab-description" class="tab-pane active">
            <div id="product-description" class="product-description">
                Product description will be loaded here...
            </div>
        </div>

        <!-- Specifications tab -->
        <div id="tab-specifications" class="tab-pane">
            <div id="product-specs" class="specs-grid"></div>
        </div>

        <!-- Reviews tab -->
        <div id="tab-reviews" class="tab-pane">
            <div class="reviews-summary">
                <div class="rating">
                    <div id="reviews-stars" class="stars"></div>
                    <span id="reviews-average" class="rating-text">0</span>
                    <span class="reviews-text">based on <span id="reviews-count-detail">0</span> reviews</span>
                </div>
            </div>
            <div class="review-orders-note">
                <i class="fas fa-info-circle"></i>
                <span>Purchased this product? Leave a review from <a href="/my-orders">My Orders</a></span>
            </div>
            <div id="reviews-list"></div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="content-section">
        <h3 class="section-title">
            <i class="fas fa-heart"></i>
            You May Also Like
        </h3>
        <div id="related-products" class="related-products-grid">
            <!-- Related products will be populated dynamically -->
        </div>
    </div>
</div>

<script>
// Product data will be loaded from PHP
const productData = <?php echo json_encode($product ?? null, JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE) ?: 'null'; ?>;
const relatedProducts = <?php echo json_encode($relatedProducts ?? [], JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE) ?: '[]'; ?>;
const reviews = <?php echo json_encode($reviews ?? [], JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE) ?: '[]'; ?>;

let currentQuantity = 1;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (productData) {
        displayProductDetails(productData);
        displayRelatedProducts(relatedProducts);
        displayReviews(reviews);
        hideLoading();
    } else {
        showError();
    }
});

// Display product details
function displayProductDetails(product) {
    // Update page title and breadcrumb
    document.title = `${product.name} - GoPlay Sports Shop`;
    document.getElementById('breadcrumb-current').textContent = product.name;
    document.getElementById('breadcrumb-category').textContent = product.category_name || 'Products';

    // Basic info
    document.getElementById('product-title').textContent = product.name;
    document.getElementById('product-category').textContent = (product.category_name || 'Product').toUpperCase();
    
    // Images - load from database or use fallback
    const mainImage = document.getElementById('main-product-image');
    let productImages = [];

    // Check if product has images in database
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
        productImages = product.images.map(img => {
            // Handle both relative and absolute paths
            if (img.startsWith('http')) return img;
            if (img.startsWith('/public/')) return img;
            if (img.startsWith('public/')) return '/' + img;
            return '/public/uploads/products/' + img;
        });
    } else {
        // Fallback to category-specific images if no images in database
        const categoryName = product.category_name ? product.category_name.toLowerCase() : 'general';
        const categoryImages = {
            'football': '/public/assets/images/products/football.jpg',
            'cricket': '/public/assets/images/products/cricket-bat.jpg',
            'tennis': '/public/assets/images/products/tennis-racket.jpg',
            'basketball': '/public/assets/images/products/basketball.jpg',
            'badminton': '/public/assets/images/products/badminton-racket.jpg',
            'volleyball': '/public/assets/images/products/football.jpg',
            'swimming': '/public/assets/images/products/football.jpg',
            'fitness': '/public/assets/images/products/football.jpg',
            'gym': '/public/assets/images/products/football.jpg'
        };
        productImages = [categoryImages[categoryName] || '/public/assets/images/products/football.jpg'];
    }

    mainImage.src = productImages[0];
    mainImage.alt = product.name;

    // Create thumbnails
    displayThumbnails(productImages);

    // Rating
    displayRating(product.rating || 4.5);

    // Price
    displayPricing(product);

    // Stock status
    displayStock(product);

    // Description
    document.getElementById('product-description').innerHTML = product.description || 'No description available.';

    // Specifications
    displaySpecifications(product);

    // Badge
    displayBadge(product);
    
    // Display shop owner information
    displayShopOwnerInfo(product);
}

// Generate inline shop star HTML
function generateShopStars(rating) {
    const full = Math.floor(rating);
    const half = rating % 1 >= 0.5;
    let s = '';
    for (let i = 0; i < full; i++) s += '<i class="fas fa-star"></i>';
    if (half) s += '<i class="fas fa-star-half-alt"></i>';
    const empty = 5 - full - (half ? 1 : 0);
    for (let i = 0; i < empty; i++) s += '<i class="far fa-star"></i>';
    return s;
}

// Switch tab
function switchTab(tabName, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');
    if (btn) btn.classList.add('active');
}

// Display shop owner information in sold-by card
function displayShopOwnerInfo(product) {
    const card = document.getElementById('sold-by-card');

    if (!product.shop_name && !product.business_name && !product.shop_owner_id) {
        return; // card stays hidden
    }

    card.classList.add('visible');

    // Logo — always set an img; onerror falls back to the FA icon
    const logoEl = document.getElementById('sold-by-logo');
    const logoSrc = product.shop_logo || '';
    logoEl.innerHTML = logoSrc
        ? `<img src="${logoSrc}" alt="Shop Logo" onerror="this.style.display='none';this.parentElement.querySelector('i').style.display='flex';">
           <i class="fas fa-store" style="display:none;"></i>`
        : `<i class="fas fa-store"></i>`;

    // Name — prefer shop_name, then business_name, then owner's real name, then generic fallback
    const ownerName = [product.owner_first_name, product.owner_last_name].filter(Boolean).join(' ');
    const shopName = product.shop_name || product.business_name || ownerName || 'GoPlay Store';
    document.getElementById('sold-by-name').textContent = shopName;

    // Meta row: rating + location + contact
    let metaHTML = '';
    if (product.average_rating && product.average_rating > 0) {
        metaHTML += `<span><span class="shop-stars-inline">${generateShopStars(product.average_rating)}</span> ${parseFloat(product.average_rating).toFixed(1)} (${product.total_reviews || 0} reviews)</span>`;
    }
    if (product.shop_city) {
        const location = [product.shop_city, product.shop_state].filter(Boolean).join(', ');
        metaHTML += `<span><i class="fas fa-map-marker-alt"></i>${location}</span>`;
    }
    if (product.business_phone) {
        metaHTML += `<span><i class="fas fa-phone"></i>${product.business_phone}</span>`;
    }
    if (product.business_email) {
        metaHTML += `<span><i class="fas fa-envelope"></i>${product.business_email}</span>`;
    }
    document.getElementById('sold-by-meta').innerHTML = metaHTML;

    // Description snippet
    if (product.business_description) {
        const descEl = document.getElementById('sold-by-desc');
        descEl.textContent = product.business_description.substring(0, 150) + (product.business_description.length > 150 ? '...' : '');
        descEl.style.display = 'block';
    }


    // Social links
    let socialHTML = '';
    if (product.facebook) socialHTML += `<a href="${product.facebook}" target="_blank" class="social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>`;
    if (product.instagram) socialHTML += `<a href="${product.instagram}" target="_blank" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>`;
    if (product.twitter) socialHTML += `<a href="${product.twitter}" target="_blank" class="social-link" title="Twitter"><i class="fab fa-twitter"></i></a>`;
    if (product.website_url) socialHTML += `<a href="${product.website_url}" target="_blank" class="social-link" title="Website"><i class="fas fa-globe"></i></a>`;
    if (socialHTML) document.getElementById('sold-by-socials').innerHTML = socialHTML;

    // Visit shop button
    if (product.shop_owner_id) {
        const visitBtn = document.getElementById('sold-by-visit-btn');
        visitBtn.style.display = 'flex';
        visitBtn.onclick = () => window.location.href = `/store/${product.shop_owner_id}`;
    }
}

// Display product images
function displayThumbnails(images) {
    const gallery = document.getElementById('thumbnail-gallery');
    
    if (images.length <= 1) {
        gallery.style.display = 'none';
        return;
    }

    const thumbnails = images.map((image, index) => `
        <div class="thumbnail ${index === 0 ? 'active' : ''}" onclick="changeMainImage('${image}', ${index})">
            <img src="${image}" alt="Product image ${index + 1}">
        </div>
    `).join('');

    gallery.innerHTML = thumbnails;
}

// Change main image
function changeMainImage(imageSrc, index) {
    document.getElementById('main-product-image').src = imageSrc;
    
    // Update thumbnail active state
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach((thumb, i) => {
        thumb.classList.toggle('active', i === index);
    });
}

// Display rating
function displayRating(rating) {
    const starsContainer = document.getElementById('product-stars');
    const reviewsStars = document.getElementById('reviews-stars');
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;

    let starsHTML = '';
    
    // Full stars
    for (let i = 0; i < fullStars; i++) {
        starsHTML += '<i class="fas fa-star star"></i>';
    }
    
    // Half star
    if (hasHalfStar) {
        starsHTML += '<i class="fas fa-star-half-alt star"></i>';
    }
    
    // Empty stars
    const remainingStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    for (let i = 0; i < remainingStars; i++) {
        starsHTML += '<i class="far fa-star star"></i>';
    }

    starsContainer.innerHTML = starsHTML;
    if (reviewsStars) reviewsStars.innerHTML = starsHTML;
    
    document.getElementById('product-rating').textContent = rating.toFixed(1);
    if (document.getElementById('reviews-average')) {
        document.getElementById('reviews-average').textContent = rating.toFixed(1);
    }
}

// Display pricing
function displayPricing(product) {
    const currentPrice = parseFloat(product.price);
    const originalPrice = parseFloat(product.original_price || 0);
    
    document.getElementById('product-price-current').textContent = `LKR ${currentPrice.toLocaleString()}`;
    
    if (originalPrice > currentPrice) {
        const discount = Math.round(((originalPrice - currentPrice) / originalPrice) * 100);
        document.getElementById('product-price-original').textContent = `LKR ${originalPrice.toLocaleString()}`;
        document.getElementById('product-price-original').style.display = 'inline';
        document.getElementById('product-price-discount').textContent = `${discount}% OFF`;
        document.getElementById('product-price-discount').style.display = 'inline';
    }
}

// Display stock status
function displayStock(product) {
    const stockInfo = document.getElementById('stock-info');
    const stockText = document.getElementById('stock-text');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const quantity = parseInt(product.stock_quantity || 0);

    stockInfo.className = 'stock-info';
    
    if (quantity <= 0) {
        stockInfo.classList.add('out-of-stock');
        stockText.innerHTML = '<i class="fas fa-times-circle"></i> Out of Stock';
        addToCartBtn.disabled = true;
        addToCartBtn.innerHTML = '<i class="fas fa-ban"></i> Out of Stock';
    } else if (quantity <= 5) {
        stockInfo.classList.add('low-stock');
        stockText.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Only ${quantity} left`;
    } else {
        stockInfo.classList.add('in-stock');
        stockText.innerHTML = '<i class="fas fa-check-circle"></i> In Stock';
    }

    // Update quantity input max
    document.getElementById('quantity-input').max = Math.min(quantity, 10);
}

// Display specifications
function displaySpecifications(product) {
    const specsGrid = document.getElementById('product-specs');
    
    const specs = [
        { label: 'SKU', value: product.sku || 'N/A' },
        { label: 'Brand', value: product.brand || 'GoPlay' },
        { label: 'Category', value: product.category_name || 'Sports Equipment' },
        { label: 'Stock', value: `${product.stock_quantity || 0} units` },
        { label: 'Weight', value: product.weight ? `${product.weight}kg` : 'N/A' },
        { label: 'Material', value: product.material || 'High Quality' }
    ];

    const specsHTML = specs.map(spec => `
        <div class="spec-item">
            <span class="spec-label">${spec.label}</span>
            <span class="spec-value">${spec.value}</span>
        </div>
    `).join('');

    specsGrid.innerHTML = specsHTML;
}

// Display badge
function displayBadge(product) {
    const badge = document.getElementById('product-badge');
    const badgeText = document.getElementById('badge-text');

    if (product.original_price && product.original_price > product.price) {
        const discount = Math.round(((product.original_price - product.price) / product.original_price) * 100);
        badgeText.innerHTML = `<i class="fas fa-percent"></i> ${discount}% OFF`;
        badge.style.display = 'flex';
        badge.style.background = '#fee2e2';
        badge.style.color = '#dc2626';
    } else if (product.is_featured) {
        badgeText.innerHTML = '<i class="fas fa-star"></i> Featured';
        badge.style.display = 'flex';
    }
}

// Display related products
function displayRelatedProducts(products) {
    if (!products || products.length === 0) return;

    const container = document.getElementById('related-products');
    const productsHTML = products.map(product => {
        // Get category-specific image for related products using existing images
        const categoryName = product.category_name ? product.category_name.toLowerCase() : 'general';
        const productImages = {
            'football': '/public/assets/images/products/football.jpg',
            'cricket': '/public/assets/images/products/cricket-bat.jpg',
            'tennis': '/public/assets/images/products/tennis-racket.jpg',
            'basketball': '/public/assets/images/products/basketball.jpg',
            'badminton': '/public/assets/images/products/badminton-racket.jpg',
            'volleyball': '/public/assets/images/products/football.jpg',
            'swimming': '/public/assets/images/products/football.jpg',
            'fitness': '/public/assets/images/products/football.jpg',
            'gym': '/public/assets/images/products/football.jpg'
        };

        const imageUrl = productImages[categoryName] || '/public/assets/images/products/football.jpg';

        return `
            <div class="related-product-card" onclick="viewProduct(${product.id})">
                <div class="related-product-image">
                    <img src="${imageUrl}" alt="${product.name}">
                </div>
                <div class="related-product-name">${product.name}</div>
                <div class="related-product-price">LKR ${parseFloat(product.price).toLocaleString()}</div>
            </div>
        `;
    }).join('');

    container.innerHTML = productsHTML;
}

// Display reviews
function displayReviews(reviews) {
    if (!reviews || reviews.length === 0) {
        document.getElementById('reviews-list').innerHTML = '<p style="color: var(--text-secondary); padding: 1rem; text-align: center;">No reviews yet.</p>';
        return;
    }

    document.getElementById('reviews-count').textContent = reviews.length;
    const detail = document.getElementById('reviews-count-detail');
    if (detail) detail.textContent = reviews.length;
    const rl = document.getElementById('reviews-link');
    if (rl) rl.textContent = `(${reviews.length} reviews)`;

    const reviewsHTML = reviews.map(review => `
        <div class="review-item" style="padding: 1.5rem 0; border-bottom: 1px solid var(--border-color);">
            <div class="review-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div>
                    <div class="reviewer-name" style="font-weight: 600; font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.25rem;">
                        ${escapeHtml(review.user_name || 'Anonymous')}
                    </div>
                    <div class="rating">
                        ${generateStars(review.rating || 5)}
                    </div>
                </div>
                <div class="review-date" style="color: var(--text-light); font-size: 0.9rem;">
                    ${formatDate(review.created_at)}
                </div>
            </div>
            ${review.title ? `<h5 style="margin-bottom: 0.5rem; font-weight: 600;">${escapeHtml(review.title)}</h5>` : ''}
            ${review.review_text ? `<div class="review-text" style="color: var(--text-secondary); line-height: 1.6;">${escapeHtml(review.review_text).replace(/\n/g, '<br>')}</div>` : ''}
            ${review.is_verified_purchase ? '<span style="color: var(--success-color); font-size: 0.85rem; margin-top: 0.5rem; display: inline-block;"><i class="fas fa-check-circle"></i> Verified Purchase</span>' : ''}
        </div>
    `).join('');

    document.getElementById('reviews-list').innerHTML = reviewsHTML;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}



// Generate stars for reviews
function generateStars(rating) {
    let stars = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star star"></i>';
    }
    
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt star"></i>';
    }
    
    const remainingStars = 5 - Math.ceil(rating);
    for (let i = 0; i < remainingStars; i++) {
        stars += '<i class="far fa-star star"></i>';
    }
    
    return stars;
}

// Format date
function formatDate(dateString) {
    if (!dateString) return 'Recently';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Update quantity
function updateQuantity(change) {
    const input = document.getElementById('quantity-input');
    const newValue = Math.max(1, Math.min(parseInt(input.max), parseInt(input.value) + change));
    input.value = newValue;
    currentQuantity = newValue;
}

// Add to cart from details page
function addToCartFromDetails() {
    if (!productData) return;
    
    const quantity = parseInt(document.getElementById('quantity-input').value);
    
    // Use the existing cart API
    addToCart(productData.id, quantity);
}

// Navigate to product
function viewProduct(productId) {
    window.location.href = `/product/${productId}`;
}

// Hide loading
function hideLoading() {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('product-content').style.display = 'block';
}

// Show error
function showError() {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('error-state').style.display = 'block';
}
</script>
