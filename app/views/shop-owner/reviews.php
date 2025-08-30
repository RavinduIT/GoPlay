<?php 
$title = 'Reviews - GoPlay';
?>

<div class="shop-owner-dashboard">

  <!-- Sidebar include -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="reviews-header">
      <h1>Customer Reviews</h1>
    </header>

    <!-- Reviews List -->
    <section class="reviews-list">

      <!-- Review Card 1 -->
      <div class="review-card">
        <div class="review-info">
          <h3 class="reviewer-name">John Doe</h3>
          <div class="review-rating">
            ★★★★☆
          </div>
          <p>  Product Name: Football<span style="margin-left: 50px;"> Product ID:600#</p>
          <p class="review-comment">
            Great quality football! Really durable and worth the price.
          </p>
        </div>
        <div class="review-actions">
          <button class="btn-delete">Delete</button>
        </div>
      </div>

      <!-- Review Card 2 -->
      <div class="review-card">
        <div class="review-info">
          <h3 class="reviewer-name">Emily Smith</h3>
          <div class="review-rating">
            ★★★☆☆
          </div>
          <p>  Product Name: Cricket Bat<span style="margin-left: 50px;"> Product ID:300#</p>
          <p class="review-comment">
            Product is good, but delivery was delayed by 2 days.
          </p>
        </div>
        <div class="review-actions">
          <button class="btn-delete">Delete</button>
        </div>
      </div>

      <!-- Review Card 3 -->
      <div class="review-card">
        <div class="review-info">
          <h3 class="reviewer-name">Michael Lee</h3>
          <div class="review-rating">
            ★★★★★
          </div>
          <p>  Product Name: Basketball<span style="margin-left: 50px;"> Product ID:7800#</p>
          <p class="review-comment">
            Excellent basketball! Perfect grip and fast delivery.
          </p>
        </div>
        <div class="review-actions">
          <button class="btn-delete">Delete</button>
        </div>
      </div>

    </section>
  </main>
</div>

<style>
/* ---- Layout base ---- */
.shop-owner-dashboard { display: flex; min-height: 100vh; }
.dashboard-content { flex: 1; margin-left: 280px; padding: 20px; background: #f9f9f9; }

/* Header */
.reviews-header { margin-bottom: 1rem; }

/* ---- Reviews List ---- */
.reviews-list { display: flex; flex-direction: column; gap: 15px; }

.review-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  background: #fff;
  padding: 16px 20px;
  border-radius: 12px;
  border: 1px solid #e3e3e3;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.review-info {
  max-width: 80%;
}

.reviewer-name {
  margin: 0 0 6px;
  font-size: 1.1rem;
  font-weight: 600;
  color: #222;
}

.review-rating {
  font-size: 1rem;
  color: #f39c12; /* Gold stars */
  margin-bottom: 6px;
}

.review-comment {
  margin: 0;
  font-size: .95rem;
  color: #555;
}

/* Actions */
.review-actions {
  display: flex;
  align-items: center;
}
.btn-delete {
  background: #d93025;
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  font-size: .9rem;
  cursor: pointer;
  font-weight: 600;
}
.btn-delete:hover { background: #b82018; }

/* Responsive */
@media (max-width: 768px) {
  .review-card { flex-direction: column; align-items: flex-start; }
  .review-info { max-width: 100%; margin-bottom: 12px; }
  .review-actions { align-self: flex-end; }
}
</style>
