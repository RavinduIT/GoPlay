<?php
$title = 'Checkout - Payment Method | GoPlay';
$additionalCSS = ['/public/css/pages/checkout.css'];
$additionalJS = ['/public/js/pages/checkout.js'];

// Get contact details from session
session_start();
$contactDetails = $_SESSION['checkout_contact'] ?? null;

if (!$contactDetails) {
    header('Location: /checkout/contact-details');
    exit;
}
?>

<div class="checkout-container">
        <div class="checkout-wrapper">
            <!-- Progress Steps -->
            <div class="checkout-progress">
                <div class="progress-step completed">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Contact Details</div>
                </div>
                <div class="progress-line active"></div>
                <div class="progress-step active">
                    <div class="step-circle">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Complete</div>
                </div>
            </div>

            <div class="checkout-content">
                <!-- Left Side: Payment Methods -->
                <div class="checkout-form-section">
                    <div class="section-card">
                        <div class="card-header">
                            <h2><i class="fas fa-credit-card"></i> Payment Method</h2>
                            <p>Choose your preferred payment method</p>
                        </div>

                        <div class="payment-methods">
                            <!-- Cash on Delivery -->
                            <div class="payment-option" data-method="cod">
                                <input type="radio" id="cod" name="paymentMethod" value="cod">
                                <label for="cod" class="payment-card">
                                    <div class="payment-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h3>Cash on Delivery</h3>
                                        <p>Pay when you receive your order</p>
                                        <ul class="payment-features">
                                            <li><i class="fas fa-check-circle"></i> No advance payment required</li>
                                            <li><i class="fas fa-check-circle"></i> Inspect before payment</li>
                                            <li><i class="fas fa-check-circle"></i> Available in all areas</li>
                                        </ul>
                                    </div>
                                    <div class="payment-badge">
                                        <span class="badge-popular">Most Popular</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Card Payment -->
                            <div class="payment-option" data-method="card">
                                <input type="radio" id="card" name="paymentMethod" value="card">
                                <label for="card" class="payment-card">
                                    <div class="payment-icon card-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="payment-info">
                                        <h3>Credit / Debit Card</h3>
                                        <p>Secure online payment</p>
                                        <ul class="payment-features">
                                            <li><i class="fas fa-check-circle"></i> Instant order confirmation</li>
                                            <li><i class="fas fa-check-circle"></i> Secure SSL encryption</li>
                                            <li><i class="fas fa-check-circle"></i> All major cards accepted</li>
                                        </ul>
                                        <div class="card-logos">
                                            <i class="fab fa-cc-visa"></i>
                                            <i class="fab fa-cc-mastercard"></i>
                                            <i class="fab fa-cc-amex"></i>
                                        </div>
                                    </div>
                                    <div class="payment-badge">
                                        <i class="fas fa-lock"></i> Secure
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Contact Info Review -->
                        <div class="contact-review">
                            <h3><i class="fas fa-info-circle"></i> Delivery Information</h3>
                            <div class="contact-details">
                                <div class="detail-item">
                                    <i class="fas fa-user"></i>
                                    <span><?= htmlspecialchars($contactDetails['fullName']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= htmlspecialchars($contactDetails['email']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-phone"></i>
                                    <span><?= htmlspecialchars($contactDetails['phone']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>
                                        <?= htmlspecialchars($contactDetails['address']) ?>, 
                                        <?= htmlspecialchars($contactDetails['city']) ?>, 
                                        <?= htmlspecialchars($contactDetails['postalCode']) ?>
                                    </span>
                                </div>
                            </div>
                            <a href="/checkout/contact-details" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit Details
                            </a>
                        </div>

                        <div class="form-actions">
                            <a href="/checkout/contact-details" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button id="placeOrderBtn" class="btn btn-primary" disabled>
                                Place Order <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Order Summary -->
                <div class="checkout-summary-section">
                    <div class="section-card">
                        <div class="card-header">
                            <h3><i class="fas fa-shopping-cart"></i> Order Summary</h3>
                        </div>

                        <div id="orderSummary" class="order-summary">
                            <div class="loading-spinner">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading cart...</p>
                            </div>
                        </div>
                    </div>

                    <div class="security-badges">
                        <div class="badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>100% Secure</span>
                        </div>
                        <div class="badge">
                            <i class="fas fa-undo"></i>
                            <span>Easy Returns</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
