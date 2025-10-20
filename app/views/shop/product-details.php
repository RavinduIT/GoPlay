
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
                        <span class="reviews-text">based on <span id="reviews-count">24</span> reviews</span>
                    </div>
                </div>
                <div id="reviews-list">
                    <!-- Reviews will be populated dynamically -->
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
    document.getElementById('product-price-current').textContent = `LKR ${currentPrice.toLocaleString()}`;
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

// Display reviews
function displayReviews(reviews) {
    if (!reviews || reviews.length === 0) {
        document.getElementById('reviews-list').innerHTML = '<p>No reviews yet. Be the first to review this product!</p>';
        return;
    }

    document.getElementById('reviews-count').textContent = reviews.length;

    const reviewsHTML = reviews.map(review => `
        <div class="review-item">
            <div class="review-header">
                <div>
                    <div class="reviewer-name">${review.user_name || 'Anonymous'}</div>
                    <div class="rating">
                        ${generateStars(review.rating || 5)}
                    </div>
                </div>
                <div class="review-date">${formatDate(review.created_at)}</div>
            </div>
            <div class="review-text">${review.comment || 'Great product!'}</div>
        </div>
    `).join('');

    document.getElementById('reviews-list').innerHTML = reviewsHTML;
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
