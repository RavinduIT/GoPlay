
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
<div id="product-content" class="container product-details" style="display: none;">
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
                <a href="#reviews" id="reviews-link" class="reviews-link">(24 reviews)</a>
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
        </div>
    </div>

    <!-- Product Content -->
    <div class="product-content">
        <div class="main-content">
            <!-- Description -->
            <div class="content-section">
                <h3 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Description
                </h3>
                <div id="product-description" class="product-description">
                    Product description will be loaded here...
                </div>
            </div>

            <!-- Specifications -->
            <div class="content-section">
                <h3 class="section-title">
                    <i class="fas fa-cog"></i>
                    Specifications
                </h3>
                <div id="product-specs" class="specs-grid">
                    <!-- Specifications will be populated dynamically -->
                </div>
            </div>

            <!-- Reviews -->
            <div id="reviews" class="content-section">
                <h3 class="section-title">
                    <i class="fas fa-star"></i>
                    Customer Reviews
                </h3>
                <div class="reviews-summary">
                    <div class="rating">
                        <div id="reviews-stars" class="stars"></div>
                        <span id="reviews-average" class="rating-text">4.5</span>
                        <span class="reviews-text">based on <span id="reviews-count">0</span> reviews</span>
                    </div>
                </div>
                
                <!-- Review Form (for logged-in users who haven't reviewed yet) -->
                <?php 
                // Session is already started by the controller
                $isLoggedIn = isset($_SESSION['user_id']);
                $userReview = $userReview ?? null;
                
                // Display success/error messages
                if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" style="margin: 1rem 0; padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 8px; border-left: 4px solid #10b981;">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error" style="margin: 1rem 0; padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; border-left: 4px solid #ef4444;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!$isLoggedIn): ?>
                    <!-- Guest user - show login prompt -->
                    <div class="review-login-prompt" style="margin: 2rem 0; padding: 2rem; background: var(--primary-light); border-radius: var(--border-radius); text-align: center;">
                        <i class="fas fa-lock" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                        <h4 style="margin-bottom: 0.5rem;">Want to leave a review?</h4>
                        <p style="color: var(--text-secondary); margin-bottom: 1rem;">Please log in to write a review for this product</p>
                        <a href="/login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login to Review
                        </a>
                    </div>
                <?php elseif ($userReview): ?>
                    <!-- User has already reviewed - show their review with edit/delete options -->
                    <div class="user-review-card" style="margin: 2rem 0; padding: 1.5rem; background: #fef3c7; border-radius: var(--border-radius); border-left: 4px solid var(--accent-color);">
                        <h4 style="margin-bottom: 1rem;"><i class="fas fa-edit"></i> Your Review</h4>
                        <div id="user-review-display">
                            <div class="review-rating-display">
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $userReview['rating'] ? '<i class="fas fa-star" style="color: var(--accent-color);"></i>' : '<i class="far fa-star" style="color: var(--accent-color);"></i>';
                                }
                                ?>
                            </div>
                            <?php if ($userReview['title']): ?>
                                <h5 style="margin-top: 0.5rem;"><?php echo htmlspecialchars($userReview['title']); ?></h5>
                            <?php endif; ?>
                            <?php if ($userReview['review_text']): ?>
                                <p style="margin-top: 0.5rem; color: var(--text-secondary);"><?php echo nl2br(htmlspecialchars($userReview['review_text'])); ?></p>
                            <?php endif; ?>
                            <div style="margin-top: 1rem;">
                                <button onclick="showEditReviewForm()" class="btn btn-secondary" style="margin-right: 0.5rem;">
                                    <i class="fas fa-edit"></i> Edit Review
                                </button>
                                <button onclick="deleteReview(<?php echo $userReview['id']; ?>)" class="btn btn-secondary" style="background: var(--danger-color); color: white; border-color: var(--danger-color);">
                                    <i class="fas fa-trash"></i> Delete Review
                                </button>
                            </div>
                        </div>
                        
                        <!-- Edit form (hidden by default) -->
                        <form id="edit-review-form" action="/product/review/update" method="POST" style="display: none;">
                            <input type="hidden" name="review_id" value="<?php echo $userReview['id']; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id'] ?? 0; ?>">
                            
                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Rating *</label>
                                <div class="star-rating-input" id="edit-star-rating">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                </div>
                                <input type="hidden" name="rating" id="edit-rating-value" value="<?php echo $userReview['rating']; ?>" required>
                            </div>
                            
                            <div style="margin-bottom: 1rem;">
                                <label for="edit-title" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Review Title</label>
                                <input type="text" id="edit-title" name="title" value="<?php echo htmlspecialchars($userReview['title'] ?? ''); ?>" 
                                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: var(--border-radius);" 
                                       maxlength="200" placeholder="Sum up your experience (optional)">
                            </div>
                            
                            <div style="margin-bottom: 1rem;">
                                <label for="edit-review-text" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Your Review</label>
                                <textarea id="edit-review-text" name="review_text" rows="4" 
                                          style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: var(--border-radius);" 
                                          maxlength="2000" placeholder="Share your thoughts about this product (optional)"><?php echo htmlspecialchars($userReview['review_text'] ?? ''); ?></textarea>
                            </div>
                            
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Review
                                </button>
                                <button type="button" onclick="hideEditReviewForm()" class="btn btn-secondary" style="margin-left: 0.5rem;">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- Logged-in user hasn't reviewed yet - show review form -->
                    <div class="write-review-card" style="margin: 2rem 0; padding: 2rem; background: var(--background-light); border-radius: var(--border-radius);">
                        <h4 style="margin-bottom: 1.5rem;"><i class="fas fa-pencil-alt"></i> Write a Review</h4>
                        <form id="review-form" action="/product/<?php echo $product['id'] ?? 0; ?>/review" method="POST">
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Rating *</label>
                                <div class="star-rating-input" id="star-rating">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                </div>
                                <input type="hidden" name="rating" id="rating-value" required>
                                <small style="color: var(--text-secondary);">Click on the stars to rate this product</small>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label for="review-title" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Review Title</label>
                                <input type="text" id="review-title" name="title" 
                                       style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem;" 
                                       maxlength="200" placeholder="Sum up your experience (optional)">
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label for="review-text" style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Your Review</label>
                                <textarea id="review-text" name="review_text" rows="5" 
                                          style="width: 100%; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: var(--border-radius); font-size: 1rem;" 
                                          maxlength="2000" placeholder="Share your thoughts about this product (optional)"></textarea>
                                <small style="color: var(--text-secondary);">Maximum 2000 characters</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
                
                <!-- All Reviews List -->
                <div style="margin-top: 2rem;">
                    <h4 style="margin-bottom: 1rem;">All Reviews</h4>
                    <div id="reviews-list">
                        <!-- Reviews will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <!-- Delivery Info -->
            <div class="sidebar-section">
                <h3 class="section-title">
                    <i class="fas fa-truck"></i>
                    Delivery
                </h3>
                <div class="spec-item">
                    <span class="spec-label">Free delivery</span>
                    <span class="spec-value">Orders over LKR 5000</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Standard delivery</span>
                    <span class="spec-value">2-3 business days</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Express delivery</span>
                    <span class="spec-value">Next business day</span>
                </div>
            </div>

            <!-- Return Policy -->
            <div class="sidebar-section">
                <h3 class="section-title">
                    <i class="fas fa-undo"></i>
                    Return Policy
                </h3>
                <div class="spec-item">
                    <span class="spec-label">Return window</span>
                    <span class="spec-value">30 days</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Condition</span>
                    <span class="spec-value">Original packaging</span>
                </div>
                <div class="spec-item">
                    <span class="spec-label">Return fee</span>
                    <span class="spec-value">Free</span>
                </div>
            </div>
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
const productData = <?php echo json_encode($product ?? null); ?>;
const relatedProducts = <?php echo json_encode($relatedProducts ?? []); ?>;
const reviews = <?php echo json_encode($reviews ?? []); ?>;

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
    
    // Images - use category-specific images
    const mainImage = document.getElementById('main-product-image');

    // Get category-specific image using existing product images
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

    let imageUrl = productImages[categoryName] || '/public/assets/images/products/football.jpg';
    mainImage.src = imageUrl;
    mainImage.alt = product.name;

    // Create thumbnails with category-specific images
    const thumbnailImages = [imageUrl];
    displayThumbnails(thumbnailImages);

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
        document.getElementById('reviews-list').innerHTML = '<p style="color: var(--text-secondary); padding: 1rem; text-align: center;">No reviews yet. Be the first to review this product!</p>';
        return;
    }

    document.getElementById('reviews-count').textContent = reviews.length;

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

// Initialize star rating inputs
document.addEventListener('DOMContentLoaded', function() {
    // For new review form
    const starRating = document.getElementById('star-rating');
    if (starRating) {
        initStarRating(starRating, 'rating-value');
    }
    
    // For edit review form
    const editStarRating = document.getElementById('edit-star-rating');
    if (editStarRating) {
        const currentRating = document.getElementById('edit-rating-value').value;
        initStarRating(editStarRating, 'edit-rating-value', parseInt(currentRating));
    }
});

// Initialize interactive star rating
function initStarRating(container, inputId, defaultRating = 0) {
    const stars = container.querySelectorAll('i');
    const input = document.getElementById(inputId);
    
    // Set default rating if provided
    if (defaultRating > 0) {
        stars.forEach((star, index) => {
            if (index < defaultRating) {
                star.classList.remove('far');
                star.classList.add('fas');
            }
        });
    }
    
    stars.forEach(star => {
        // Hover effect
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            highlightStars(stars, rating);
        });
        
        // Click to select
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            input.value = rating;
            setStars(stars, rating);
        });
    });
    
    // Reset on mouse leave
    container.addEventListener('mouseleave', function() {
        const currentRating = input.value ? parseInt(input.value) : 0;
        setStars(stars, currentRating);
    });
}

// Highlight stars on hover
function highlightStars(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
            star.style.color = 'var(--accent-color)';
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = 'var(--text-light)';
        }
    });
}

// Set stars permanently
function setStars(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
            star.style.color = 'var(--accent-color)';
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
            star.style.color = 'var(--text-light)';
        }
    });
}

// Show edit review form
function showEditReviewForm() {
    document.getElementById('user-review-display').style.display = 'none';
    document.getElementById('edit-review-form').style.display = 'block';
}

// Hide edit review form
function hideEditReviewForm() {
    document.getElementById('user-review-display').style.display = 'block';
    document.getElementById('edit-review-form').style.display = 'none';
}

// Delete review with confirmation
function deleteReview(reviewId) {
    if (!confirm('Are you sure you want to delete your review? This action cannot be undone.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/product/review/delete';
    
    const reviewIdInput = document.createElement('input');
    reviewIdInput.type = 'hidden';
    reviewIdInput.name = 'review_id';
    reviewIdInput.value = reviewId;
    
    const productIdInput = document.createElement('input');
    productIdInput.type = 'hidden';
    productIdInput.name = 'product_id';
    productIdInput.value = productData.id;
    
    form.appendChild(reviewIdInput);
    form.appendChild(productIdInput);
    document.body.appendChild(form);
    form.submit();
}

// Add CSS for star rating
const starRatingStyles = document.createElement('style');
starRatingStyles.innerHTML = `
    .star-rating-input {
        display: flex;
        gap: 0.5rem;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .star-rating-input i {
        cursor: pointer;
        transition: all 0.2s ease;
        color: var(--text-light);
    }
    
    .star-rating-input i:hover {
        transform: scale(1.2);
        color: var(--accent-color);
    }
    
    .star-rating-input i.fas {
        color: var(--accent-color);
    }
    
    .review-item:last-child {
        border-bottom: none !important;
    }
`;
document.head.appendChild(starRatingStyles);


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
