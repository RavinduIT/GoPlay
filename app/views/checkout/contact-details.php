<?php
$title = 'Checkout - Contact Details | GoPlay';
$additionalCSS = ['/public/css/pages/checkout.css'];
$additionalJS = ['/public/js/pages/checkout.js'];
?>

<div class="checkout-container">
        <div class="checkout-wrapper">
            <!-- Progress Steps -->
            <div class="checkout-progress">
                <div class="progress-step active">
                    <div class="step-circle">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="step-label">Contact Details</div>
                </div>
                <div class="progress-line"></div>
                <div class="progress-step">
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
                <!-- Left Side: Contact Form -->
                <div class="checkout-form-section">
                    <div class="section-card">
                        <div class="card-header">
                            <h2><i class="fas fa-user-circle"></i> Contact Information</h2>
                            <p>Please provide your contact details for order confirmation</p>
                        </div>

                        <form id="contactDetailsForm" class="checkout-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fullName">
                                        <i class="fas fa-user"></i> Full Name *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="fullName" 
                                        name="fullName" 
                                        placeholder="Enter your full name"
                                        value="<?= htmlspecialchars($savedContact['fullName'] ?? '') ?>"
                                        required
                                    >
                                    <span class="error-message"></span>
                                </div>
                            </div>

                            <div class="form-row form-row-2">
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i> Email Address *
                                    </label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        placeholder="your.email@example.com"
                                        value="<?= htmlspecialchars($savedContact['email'] ?? '') ?>"
                                        required
                                    >
                                    <span class="error-message"></span>
                                </div>

                                <div class="form-group">
                                    <label for="phone">
                                        <i class="fas fa-phone"></i> Phone Number *
                                    </label>
                                    <input 
                                        type="tel" 
                                        id="phone" 
                                        name="phone" 
                                        placeholder="077 123 4567"
                                        value="<?= htmlspecialchars($savedContact['phone'] ?? '') ?>"
                                        required
                                    >
                                    <span class="error-message"></span>
                                </div>
                            </div>

                            <div class="section-divider">
                                <h3><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address">
                                        <i class="fas fa-home"></i> Street Address *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="address" 
                                        name="address" 
                                        placeholder="House number and street name"
                                        value="<?= htmlspecialchars($savedContact['address'] ?? '') ?>"
                                        required
                                    >
                                    <span class="error-message"></span>
                                </div>
                            </div>

                            <div class="form-row form-row-3">
                                <div class="form-group">
                                    <label for="city">
                                        <i class="fas fa-city"></i> City *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="city" 
                                        name="city" 
                                        placeholder="Colombo"
                                        value="<?= htmlspecialchars($savedContact['city'] ?? '') ?>"
                                        required
                                    >
                                    <span class="error-message"></span>
                                </div>

                                <div class="form-group">
                                    <label for="postalCode">
                                        <i class="fas fa-mail-bulk"></i> Postal Code *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="postalCode" 
                                        name="postalCode" 
                                        placeholder="00100"
                                        value="<?= htmlspecialchars($savedContact['postalCode'] ?? '') ?>"
                                        required
                                    >
                                    <span class="error-message"></span>
                                </div>

                                <div class="form-group">
                                    <label for="province">
                                        <i class="fas fa-map"></i> Province
                                    </label>
                                    <select id="province" name="province">
                                        <?php $savedProvince = $savedContact['province'] ?? 'Western'; ?>
                                        <option value="Western" <?= $savedProvince === 'Western' ? 'selected' : '' ?>>Western</option>
                                        <option value="Central" <?= $savedProvince === 'Central' ? 'selected' : '' ?>>Central</option>
                                        <option value="Southern" <?= $savedProvince === 'Southern' ? 'selected' : '' ?>>Southern</option>
                                        <option value="Northern" <?= $savedProvince === 'Northern' ? 'selected' : '' ?>>Northern</option>
                                        <option value="Eastern" <?= $savedProvince === 'Eastern' ? 'selected' : '' ?>>Eastern</option>
                                        <option value="North Western" <?= $savedProvince === 'North Western' ? 'selected' : '' ?>>North Western</option>
                                        <option value="North Central" <?= $savedProvince === 'North Central' ? 'selected' : '' ?>>North Central</option>
                                        <option value="Uva" <?= $savedProvince === 'Uva' ? 'selected' : '' ?> <?= $savedProvince === 'Uva' ? 'selected' : '' ?>>Uva</option>
                                        <option value="Sabaragamuwa" <?= $savedProvince === 'Sabaragamuwa' ? 'selected' : '' ?>>Sabaragamuwa</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="notes">
                                        <i class="fas fa-sticky-note"></i> Additional Notes (Optional)
                                    </label>
                                    <textarea 
                                        id="notes" 
                                        name="notes" 
                                        rows="3"
                                        placeholder="Any special delivery instructions..."
                                    ><?= htmlspecialchars($savedContact['notes'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="/shop" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Continue Shopping
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Continue to Payment <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
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
                            <i class="fas fa-lock"></i>
                            <span>Secure Checkout</span>
                        </div>
                        <div class="badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Data Protected</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
