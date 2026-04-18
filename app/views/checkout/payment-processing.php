<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Processing Payment | GoPlay';
$additionalCSS = ['/public/css/pages/checkout.css'];
$additionalJS = [];

// Ensure user has contact details
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$contactDetails = $_SESSION['checkout_contact'] ?? null;

if (!$contactDetails) {
    header('Location: /checkout/contact-details');
    exit;
}
?>

<!-- PayHere SDK -->
<script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

<div class="checkout-container payment-processing">
    <div class="checkout-wrapper narrow">
        <div class="section-card payment-card-form">
            <div class="card-header">
                <h2><i class="fas fa-credit-card"></i> Secure Payment with PayHere</h2>
                <p>Complete your payment securely through PayHere payment gateway</p>
            </div>

            <div class="payment-security-notice">
                <i class="fas fa-lock"></i>
                <span>Your payment information is encrypted and secure with PayHere</span>
            </div>

            <!-- Order Summary -->
            <div class="order-summary-section">
                <h3><i class="fas fa-shopping-cart"></i> Order Summary</h3>
                <div id="orderSummaryContent" class="summary-loading">
                    <i class="fas fa-spinner fa-spin"></i> Loading order details...
                </div>
            </div>

            <!-- Payment Button -->
            <div class="payment-action-section">
                <div class="order-total-display">
                    <span>Total Amount:</span>
                    <strong id="totalAmount">Loading...</strong>
                </div>

                <div class="form-actions">
                    <a href="<?= $_base ?>/checkout/payment-method" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="button" class="btn btn-primary btn-large" id="payNowBtn" onclick="initiatePayment()">
                        <i class="fas fa-lock"></i> Pay with PayHere
                    </button>
                </div>
            </div>

            <!-- Payment Processing Overlay -->
            <div id="processingOverlay" class="processing-overlay" style="display: none;">
                <div class="processing-content">
                    <div class="spinner-large">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </div>
                    <h3>Initializing Payment...</h3>
                    <p>Please wait while we prepare your payment session.</p>
                </div>
            </div>

            <div class="payment-logos">
                <p>Powered by PayHere - We accept:</p>
                <div class="logos">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <span style="font-weight: bold; font-size: 0.9rem; margin-left: 10px;">Lanka QR</span>
                </div>
            </div>

            <div class="security-info">
                <div class="info-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>SSL Encrypted</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-lock"></i>
                    <span>PCI DSS Compliant</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Secure by PayHere</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load cart totals and display order summary
async function loadOrderSummary() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/cart');
        const data = await response.json();
        
        if (data.success && data.data) {
            const cart = data.data;
            const totals = cart.totals || {};
            const items = cart.items || [];
            
            // Display order summary
            const summaryHtml = `
                <div class="summary-items">
                    ${items.map(item => `
                        <div class="summary-item">
                            <span class="item-name">${item.product_name} × ${item.quantity}</span>
                            <span class="item-price">Rs. ${parseFloat(item.total_price).toFixed(2)}</span>
                        </div>
                    `).join('')}
                </div>
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>Rs. ${parseFloat(totals.subtotal || 0).toFixed(2)}</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (8%):</span>
                        <span>Rs. ${(Math.round(parseFloat(totals.subtotal || 0) * 0.08 * 100) / 100).toFixed(2)}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span>Rs. ${(parseFloat(totals.subtotal || 0) >= 5000 ? 0 : 500).toFixed(2)}</span>
                    </div>
                </div>
            `;
            
            document.getElementById('orderSummaryContent').innerHTML = summaryHtml;
            
            // Calculate and display total
            const subtotal = parseFloat(totals.subtotal || 0);
            const tax = Math.round(subtotal * 0.08 * 100) / 100;
            const shipping = subtotal >= 5000 ? 0 : 500;
            const total = subtotal + tax + shipping;
            
            document.getElementById('totalAmount').textContent = 'Rs. ' + total.toFixed(2);
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        document.getElementById('orderSummaryContent').innerHTML = '<p style="color: red;">Failed to load order details</p>';
    }
}

// Initialize PayHere payment
async function initiatePayment() {
    const payBtn = document.getElementById('payNowBtn');
    const overlay = document.getElementById('processingOverlay');
    
    // Disable button and show overlay
    payBtn.disabled = true;
    overlay.style.display = 'flex';
    
    try {
        // Call backend to initialize payment
        const response = await fetch((window.BASE_URL||'')+'/api/checkout/initialize-payhere', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.paymentData) {
            // Hide overlay
            overlay.style.display = 'none';
            
            // Configure PayHere payment
            const payment = {
                sandbox: data.paymentData.sandbox,
                merchant_id: data.paymentData.merchant_id,
                return_url: data.paymentData.return_url,
                cancel_url: data.paymentData.cancel_url,
                notify_url: data.paymentData.notify_url,
                order_id: data.paymentData.order_id,
                items: data.paymentData.items,
                amount: data.paymentData.amount,
                currency: data.paymentData.currency,
                first_name: data.paymentData.first_name,
                last_name: data.paymentData.last_name,
                email: data.paymentData.email,
                phone: data.paymentData.phone,
                address: data.paymentData.address,
                city: data.paymentData.city,
                country: data.paymentData.country,
                hash: data.paymentData.hash
            };
            
            // Configure PayHere callbacks
            payhere.onCompleted = function onCompleted(orderId) {
                console.log("Payment completed. OrderID:" + orderId);
                showNotification('Payment completed successfully! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = payment.return_url + '?order_id=' + orderId;
                }, 2000);
            };
            
            payhere.onDismissed = function onDismissed() {
                console.log("Payment dismissed");
                payBtn.disabled = false;
                showNotification('Payment cancelled by user', 'warning');
            };
            
            payhere.onError = function onError(error) {
                console.log("Payment error:" + error);
                payBtn.disabled = false;
                showNotification('Payment error: ' + error, 'error');
            };
            
            // Start payment
            payhere.startPayment(payment);
            
        } else {
            overlay.style.display = 'none';
            payBtn.disabled = false;
            showNotification(data.message || 'Failed to initialize payment', 'error');
        }
        
    } catch (error) {
        console.error('Payment initialization error:', error);
        overlay.style.display = 'none';
        payBtn.disabled = false;
        showNotification('Failed to initialize payment. Please try again.', 'error');
    }
}

// Notification helper
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification-toast notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        z-index: 10001;
        animation: slideIn 0.3s ease;
    `;
    
    const iconMap = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const colorMap = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    
    notification.innerHTML = `
        <i class="fas fa-${iconMap[type]}" style="color: ${colorMap[type]}; font-size: 1.25rem;"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Load order summary on page load
document.addEventListener('DOMContentLoaded', loadOrderSummary);
</script>

<style>
@keyframes slideIn {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}

.order-summary-section {
    margin: 1.5rem 0;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 12px;
}

.order-summary-section h3 {
    margin: 0 0 1rem 0;
    color: #1f2937;
    font-size: 1.1rem;
}

.summary-items {
    margin-bottom: 1rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-totals {
    border-top: 2px solid #d1d5db;
    padding-top: 1rem;
    margin-top: 1rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.4rem 0;
    font-weight: 500;
}

.summary-loading {
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.payment-action-section {
    margin-top: 1.5rem;
}

.btn-large {
    padding: 1rem 2rem !important;
    font-size: 1.1rem !important;
}
</style>
