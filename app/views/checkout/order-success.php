<?php
$title = 'Order Successful | GoPlay';
$additionalCSS = ['/public/css/pages/order-success.css'];

// Get order number from URL
$orderNumber = $_GET['order'] ?? '';

if (empty($orderNumber)) {
    header('Location: /shop');
    exit;
}
?>

<div class="success-container">
        <div class="success-wrapper">
            <!-- Success Animation -->
            <div class="success-animation">
                <div class="checkmark-circle">
                    <div class="checkmark-icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <div class="celebration-confetti"></div>
            </div>

            <!-- Success Message -->
            <div class="success-message">
                <h1>🎉 Order Placed Successfully!</h1>
                <p>Thank you for your purchase. Your order has been confirmed.</p>
            </div>

            <!-- Order Details Card -->
            <div class="order-details-card">
                <div class="order-header">
                    <div class="order-number">
                        <span class="label">Order Number</span>
                        <h2><?= htmlspecialchars($orderNumber) ?></h2>
                    </div>
                    <div class="order-status">
                        <span class="status-badge processing">
                            <i class="fas fa-clock"></i> Processing
                        </span>
                    </div>
                </div>

                <div class="order-info-grid">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <div>
                            <span class="label">Order Date</span>
                            <strong><?= date('F j, Y') ?></strong>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-truck"></i>
                        <div>
                            <span class="label">Estimated Delivery</span>
                            <strong><?= date('F j, Y', strtotime('+5 days')) ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Timeline -->
            <div class="delivery-timeline">
                <h3><i class="fas fa-shipping-fast"></i> Delivery Timeline</h3>
                <div class="timeline">
                    <div class="timeline-step active">
                        <div class="step-dot"></div>
                        <div class="step-content">
                            <h4>Order Confirmed</h4>
                            <p><?= date('M j, g:i A') ?></p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-dot"></div>
                        <div class="step-content">
                            <h4>Processing</h4>
                            <p>Within 24 hours</p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-dot"></div>
                        <div class="step-content">
                            <h4>Shipped</h4>
                            <p>1-2 business days</p>
                        </div>
                    </div>
                    <div class="timeline-step">
                        <div class="step-dot"></div>
                        <div class="step-content">
                            <h4>Delivered</h4>
                            <p>3-5 business days</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- What's Next Section -->
            <div class="next-steps-card">
                <h3><i class="fas fa-info-circle"></i> What's Next?</h3>
                <div class="steps-grid">
                    <div class="step">
                        <div class="step-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Confirmation Email</h4>
                        <p>Check your email for order confirmation and tracking details</p>
                    </div>
                    <div class="step">
                        <div class="step-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <h4>Order Tracking</h4>
                        <p>Track your order status in your account dashboard</p>
                    </div>
                    <div class="step">
                        <div class="step-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4>Customer Support</h4>
                        <p>Contact us if you have any questions about your order</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="/orders" class="btn btn-secondary">
                    <i class="fas fa-list"></i> View My Orders
                </a>
                <a href="/shop" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>

            <!-- Help Section -->
            <div class="help-section">
                <p>
                    <i class="fas fa-question-circle"></i>
                    Need help with your order? 
                    <a href="/contact">Contact our support team</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Confetti animation
        function createConfetti() {
            const container = document.querySelector('.celebration-confetti');
            const colors = ['#2563eb', '#0891b2', '#10b981', '#f59e0b', '#ef4444'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti-piece';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                container.appendChild(confetti);
            }
        }

        // Run confetti animation on page load
        window.addEventListener('load', createConfetti);
    </script>
</div>
