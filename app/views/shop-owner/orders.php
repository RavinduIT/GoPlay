<?php 
$title = 'Orders - GoPlay';
?>

<div class="shop-owner-dashboard">

  <!-- Sidebar include -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="orders-header">
      <h1>Orders</h1>
    </header>

    <div class="orders-actions-container">
      <div class="orders-search">
        <input type="text" placeholder="Search Orders...">
      </div>
    </div>

    <!-- Orders List -->
    <section class="orders-list">

      <!-- Order Card 1 -->
      <div class="order-card">
        <div class="order-info">
          <h3 class="order-id">Order #1001</h3>
          <p><strong>Customer:</strong> Kavinda Ranasinghe</p>
          <p><strong>Items:</strong> 3 (Football, Cricket Bat, Basketball)</p>
          <p><strong>Total:</strong> Rs. 2450</p>
          <p><strong>Status:</strong> <span class="status processing">Processing</span></p>
          <p><strong>Date:</strong> 7 hours ago</p>
        </div>
        <div class="order-actions">
          <button class="btn-view">View</button>
          <button class="btn-delete">Delete</button>
        </div>
      </div>

      <!-- Order Card 2 -->
      <div class="order-card">
        <div class="order-info">
          <h3 class="order-id">Order #1002</h3>
          <p><strong>Customer:</strong> John Doe</p>
          <p><strong>Items:</strong> 2 (Badminton Racket, Shuttle)</p>
          <p><strong>Total:</strong> Rs. 1250</p>
          <p><strong>Status:</strong> <span class="status completed">Completed</span></p>
          <p><strong>Date:</strong> 1 day ago</p>
        </div>
        <div class="order-actions">
          <button class="btn-view">View</button>
          <button class="btn-delete">Delete</button>
        </div>
      </div>

      <!-- Order Card 3 -->
      <div class="order-card">
        <div class="order-info">
          <h3 class="order-id">Order #1003</h3>
          <p><strong>Customer:</strong> Emily Smith</p>
          <p><strong>Items:</strong> 1 (Tennis Racket)</p>
          <p><strong>Total:</strong> Rs. 3000</p>
          <p><strong>Status:</strong> <span class="status pending">Pending</span></p>
          <p><strong>Date:</strong> 2 days ago</p>
        </div>
        <div class="order-actions">
          <button class="btn-view">View</button>
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
.orders-header { margin-bottom: 1rem; }

/* ---- Orders List ---- */
.orders-list { display: flex; flex-direction: column; gap: 15px; }

.order-card {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  background: #fff;
  padding: 16px 20px;
  border-radius: 12px;
  border: 1px solid #e3e3e3;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.order-info {
  max-width: 80%;
  font-size: 0.95rem;
  color: #444;
}

.order-id {
  margin: 0 0 8px;
  font-size: 1.1rem;
  font-weight: 600;
  color: #222;
}

/* Status badges */
.status {
  padding: 2px 8px;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}
.status.processing { background: #fff3cd; color: #856404; }
.status.completed { background: #d4edda; color: #155724; }
.status.pending   { background: #f8d7da; color: #721c24; }

/* Actions */
.order-actions {
  display: flex;
  flex-direction: row;
  gap: 8px;
}
.btn-view {
  background: #02ad1cff;
  color: #fff;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  font-size: .9rem;
  cursor: pointer;
  font-weight: 600;
}
.btn-view:hover { background: #0069d9; }

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
  .order-card { flex-direction: column; align-items: flex-start; }
  .order-info { max-width: 100%; margin-bottom: 12px; }
  .order-actions { flex-direction: row; gap: 10px; align-self: flex-end; }
}

.orders-actions-container {
  background: white;
  border: 1px solid #e3e3e3;
  border-radius: 8px;
  padding: 15px 20px;
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.orders-search {
  display: flex;
  gap: 1rem;
  width: 100%;
}

.orders-search input {
  flex: 1;
  padding: .55rem .75rem;
  border: 1px solid #e3e3e3;
  border-radius: 6px;
}

.orders-search button {
  padding: .55rem .9rem;
  border: none;
  border-radius: 6px;
  background: #0fa930ff;
  color: white;
  cursor: pointer;
}
</style>
