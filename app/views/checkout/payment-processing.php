<?php
$title = 'Processing Payment | GoPlay';
$additionalCSS = ['/public/css/pages/checkout.css'];
$additionalJS = ['/public/js/payment-gateway.js'];

// Ensure user has contact details
session_start();
$contactDetails = $_SESSION['checkout_contact'] ?? null;

if (!$contactDetails) {
    header('Location: /checkout/contact-details');
    exit;
}
?>

<div class="checkout-container payment-processing">
        <div class="checkout-wrapper narrow">
            <div class="section-card payment-card-form">
                <div class="card-header">
                    <h2><i class="fas fa-credit-card"></i> Card Payment</h2>
                    <p>Enter your card details securely</p>
                </div>

                <div class="payment-security-notice">
                    <i class="fas fa-lock"></i>
                    <span>Your payment information is encrypted and secure</span>
                </div>

                <form id="paymentForm" class="checkout-form">
                    <div class="form-group">
                        <label for="cardNumber">
                            <i class="fas fa-credit-card"></i> Card Number *
                        </label>
                        <input 
                            type="text" 
                            id="cardNumber" 
                            name="cardNumber" 
                            placeholder="1234 5678 9012 3456"
                            maxlength="19"
                            required
                        >
                        <span class="error-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="cardName">
                            <i class="fas fa-user"></i> Name on Card *
                        </label>
                        <input 
                            type="text" 
                            id="cardName" 
                            name="cardName" 
                            placeholder="JOHN DOE"
                            required
                        >
                        <span class="error-message"></span>
                    </div>

                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label for="expiryDate">
                                <i class="fas fa-calendar"></i> Expiry Date *
                            </label>
                            <input 
                                type="text" 
                                id="expiryDate" 
                                name="expiryDate" 
                                placeholder="MM/YY"
                                maxlength="5"
                                required
                            >
                            <span class="error-message"></span>
                        </div>

                        <div class="form-group">
                            <label for="cvv">
                                <i class="fas fa-lock"></i> CVV *
                            </label>
                            <input 
                                type="text" 
                                id="cvv" 
                                name="cvv" 
                                placeholder="123"
                                maxlength="3"
                                required
                            >
                            <span class="error-message"></span>
                        </div>
                    </div>

                    <div class="order-total-display">
                        <span>Total Amount:</span>
                        <strong id="totalAmount">Loading...</strong>
                    </div>

                    <div class="form-actions">
                        <a href="/checkout/payment-method" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary" id="payNowBtn">
                            <i class="fas fa-lock"></i> Pay Securely
                        </button>
                    </div>

                    <!-- Payment Processing Overlay -->
                    <div id="processingOverlay" class="processing-overlay" style="display: none;">
                        <div class="processing-content">
                            <div class="spinner-large">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </div>
                            <h3>Processing Payment...</h3>
                            <p>Please wait while we process your payment securely.</p>
                            <p class="warning-text">Do not close this window or press back button</p>
                        </div>
                    </div>
                </form>

                <div class="payment-logos">
                    <p>We accept:</p>
                    <div class="logos">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-amex"></i>
                        <i class="fab fa-cc-discover"></i>
                    </div>
                </div>

                <div class="security-info">
                    <div class="info-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>SSL Encrypted</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-lock"></i>
                        <span>PCI Compliant</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Secure Payment</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
