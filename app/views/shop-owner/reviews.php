<?php 
$title = 'Reviews - GoPlay';

// Session is already started by the controller
// Display success/error messages
$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="shop-owner-dashboard">

  <!-- Sidebar include -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-main">
    <!-- Top Header -->
    <header class="dashboard-header">
      <div class="header-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        <div class="page-title-section">
          <h1 class="page-title">Customer Reviews</h1>
          <p class="page-subtitle">Manage reviews for your products</p>
        </div>
      </div>
    </header>

    <!-- Dashboard Content -->
    <div class="dashboard-content">

    <!-- Success/Error Messages -->
    <?php if ($successMessage): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
      </div>
    <?php endif; ?>
    
    <?php if ($errorMessage): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
      </div>
    <?php endif; ?>

    <!-- Review Statistics -->
    <div class="stats-container" id="review-stats" style="display: none;">
      <div class="stat-card">
        <i class="fas fa-comments"></i>
        <div class="stat-value" id="total-reviews">0</div>
        <div class="stat-label">Total Reviews</div>
      </div>
      <div class="stat-card">
        <i class="fas fa-star"></i>
        <div class="stat-value" id="avg-rating">0.0</div>
        <div class="stat-label">Average Rating</div>
      </div>
      <div class="stat-card">
        <i class="fas fa-star"></i>
        <div class="stat-value" id="five-star">0</div>
        <div class="stat-label">5 Star Reviews</div>
      </div>
      <div class="stat-card">
        <i class="fas fa-thumbs-up"></i>
        <div class="stat-value" id="four-star">0</div>
        <div class="stat-label">4 Star Reviews</div>
      </div>
    </div>

    <!-- Loading State -->
    <div id="loading-reviews" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading reviews...</p>
    </div>

    <!-- No Reviews State -->
    <div id="no-reviews" class="no-data-container" style="display: none;">
      <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
      <h3>No Reviews Yet</h3>
      <p>Your products haven't received any reviews yet.</p>
    </div>

    <!-- Reviews List -->
    <section class="reviews-list" id="reviews-list">
      <!-- Reviews will be populated dynamically -->
    </section>
  </main>
</div>

<style>
/* Alert Messages */
.alert {
  padding: 1rem 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.alert-success {
  background: #d1fae5;
  color: #065f46;
  border-left: 4px solid #10b981;
}

.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border-left: 4px solid #ef4444;
}

/* Statistics Cards */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  border: 1px solid #e3e3e3;
  text-align: center;
}

.stat-card i {
  font-size: 2rem;
  color: #f39c12;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  color: #222;
  margin: 0.5rem 0;
}

.stat-label {
  color: #666;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Loading */
.loading-container {
  text-align: center;
  padding: 3rem;
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.no-data-container {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 12px;
  color: #666;
}

/* ---- Reviews List ---- */
.reviews-list { display: flex; flex-direction: column; gap: 15px; }

.review-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  background: #fff;
  padding: 1.5rem;
  border-radius: 12px;
  border: 1px solid #e3e3e3;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
}

.review-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  transform: translateY(-2px);
}

.review-info {
  flex: 1;
}

.review-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
}

.reviewer-name {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #222;
}

.review-date {
  color: #999;
  font-size: 0.85rem;
}

.review-rating {
  font-size: 1rem;
  color: #f39c12; /* Gold stars */
  margin-bottom: 0.5rem;
}

.product-info {
  margin: 0.75rem 0;
  padding: 0.5rem;
  background: #f8f9fa;
  border-radius: 6px;
  font-size: 0.9rem;
}

.product-name {
  font-weight: 600;
  color: #2563eb;
}

.product-id {
  color: #666;
  margin-left: 1rem;
}

.review-title {
  font-weight: 600;
  color: #333;
  margin: 0.5rem 0;
}

.review-comment {
  margin: 0.5rem 0 0;
  font-size: 0.95rem;
  color: #555;
  line-height: 1.6;
}

.verified-badge {
  display: inline-block;
  background: #d1fae5;
  color: #065f46;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-top: 0.5rem;
}

/* Actions */
.review-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-delete {
  background: #d93025;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.9rem;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-delete:hover { 
  background: #b82018;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(217, 48, 37, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-content { margin-left: 0; }
  .review-card { flex-direction: column; gap: 1rem; }
  .review-actions { align-self: flex-start; margin-top: 1rem; }
  .stats-container { grid-template-columns: 1fr; }
}
</style>

<script>
// Load reviews on page load
document.addEventListener('DOMContentLoaded', function() {
  loadReviews();
});

// Load reviews from API
async function loadReviews() {
  try {
    const response = await fetch('/api/shop-owner/reviews');
    const data = await response.json();
    
    document.getElementById('loading-reviews').style.display = 'none';
    
    if (!data.success) {
      showError(data.message || 'Failed to load reviews');
      return;
    }
    
    // Display statistics
    if (data.stats && data.stats.total_reviews > 0) {
      displayStats(data.stats);
      document.getElementById('review-stats').style.display = 'grid';
    }
    
    // Display reviews
    if (data.reviews && data.reviews.length > 0) {
      displayReviews(data.reviews);
    } else {
      document.getElementById('no-reviews').style.display = 'block';
    }
    
  } catch (error) {
    console.error('Error loading reviews:', error);
    document.getElementById('loading-reviews').style.display = 'none';
    showError('An error occurred while loading reviews');
  }
}

// Display statistics
function displayStats(stats) {
  document.getElementById('total-reviews').textContent = stats.total_reviews || 0;
  document.getElementById('avg-rating').textContent = (stats.avg_rating || 0).toFixed(1);
  document.getElementById('five-star').textContent = stats.five_star || 0;
  document.getElementById('four-star').textContent = stats.four_star || 0;
}

// Display reviews list
function displayReviews(reviews) {
  const container = document.getElementById('reviews-list');
  
  const reviewsHTML = reviews.map(review => `
    <div class="review-card">
      <div class="review-info">
        <div class="review-meta">
          <h3 class="reviewer-name">${escapeHtml(review.customer_name || review.user_name || 'Anonymous')}</h3>
          <span class="review-date">${formatDate(review.created_at)}</span>
        </div>
        <div class="review-rating">
          ${generateStars(review.rating)}
        </div>
        <div class="product-info">
          <span class="product-name">${escapeHtml(review.product_name)}</span>
          <span class="product-id">Product ID: #${review.product_id}</span>
        </div>
        ${review.title ? `<p class="review-title">${escapeHtml(review.title)}</p>` : ''}
        ${review.review_text ? `<p class="review-comment">${escapeHtml(review.review_text)}</p>` : ''}
        ${review.is_verified_purchase ? '<span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Purchase</span>' : ''}
      </div>
      <div class="review-actions">
        <button class="btn-delete" onclick="deleteReview(${review.id})">
          <i class="fas fa-trash"></i> Delete
        </button>
      </div>
    </div>
  `).join('');
  
  container.innerHTML = reviewsHTML;
}

// Generate star icons
function generateStars(rating) {
  let stars = '';
  for (let i = 1; i <= 5; i++) {
    stars += i <= rating ? '★' : '☆';
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

// Escape HTML
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Delete review with confirmation
function deleteReview(reviewId) {
  if (!confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
    return;
  }
  
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '/shop-owner/reviews/delete';
  
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'review_id';
  input.value = reviewId;
  
  form.appendChild(input);
  document.body.appendChild(form);
  form.submit();
}

// Show error message
function showError(message) {
  const container = document.getElementById('reviews-list');
  container.innerHTML = `
    <div class="no-data-container">
      <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
      <h3>Error</h3>
      <p>${escapeHtml(message)}</p>
    </div>
  `;
}
</script>
