// Payment Gateway Integration - GoPlay Platform
// This is a simplified payment gateway simulation
// In production, integrate with actual payment gateway (Stripe, PayPal, etc.)

document.addEventListener('DOMContentLoaded', function() {
    initPaymentForm();
    loadOrderTotal();
});

function initPaymentForm() {
    const form = document.getElementById('paymentForm');
    
    if (!form) return;
    
    // Card number formatting
    const cardNumberInput = document.getElementById('cardNumber');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', formatCardNumber);
    }
    
    // Expiry date formatting
    const expiryInput = document.getElementById('expiryDate');
    if (expiryInput) {
        expiryInput.addEventListener('input', formatExpiryDate);
    }
    
    // CVV validation
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3);
        });
    }
    
    // Card name to uppercase
    const cardNameInput = document.getElementById('cardName');
    if (cardNameInput) {
        cardNameInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Form submission
    form.addEventListener('submit', handlePaymentSubmit);
}

function formatCardNumber(e) {
    let value = e.target.value.replace(/\s/g, '');
    value = value.replace(/[^0-9]/g, '');
    
    // Add space every 4 digits
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    
    e.target.value = formattedValue;
}

function formatExpiryDate(e) {
    let value = e.target.value.replace(/\//g, '');
    value = value.replace(/[^0-9]/g, '');
    
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    
    e.target.value = value;
}

function validateCardNumber(cardNumber) {
    const cleaned = cardNumber.replace(/\s/g, '');
    
    if (!/^\d{16}$/.test(cleaned)) {
        return { valid: false, message: 'Card number must be 16 digits' };
    }
    
    // Luhn algorithm for card validation
    let sum = 0;
    let isEven = false;
    
    for (let i = cleaned.length - 1; i >= 0; i--) {
        let digit = parseInt(cleaned[i]);
        
        if (isEven) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }
        
        sum += digit;
        isEven = !isEven;
    }
    
    if (sum % 10 !== 0) {
        return { valid: false, message: 'Invalid card number' };
    }
    
    return { valid: true };
}

function validateExpiryDate(expiry) {
    const parts = expiry.split('/');
    
    if (parts.length !== 2) {
        return { valid: false, message: 'Invalid expiry format (MM/YY)' };
    }
    
    const month = parseInt(parts[0]);
    const year = parseInt('20' + parts[1]);
    
    if (month < 1 || month > 12) {
        return { valid: false, message: 'Invalid month' };
    }
    
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth() + 1;
    
    if (year < currentYear || (year === currentYear && month < currentMonth)) {
        return { valid: false, message: 'Card has expired' };
    }
    
    return { valid: true };
}

function validateCVV(cvv) {
    if (!/^\d{3}$/.test(cvv)) {
        return { valid: false, message: 'CVV must be 3 digits' };
    }
    
    return { valid: true };
}

function handlePaymentSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const cardNumber = document.getElementById('cardNumber').value;
    const cardName = document.getElementById('cardName').value;
    const expiryDate = document.getElementById('expiryDate').value;
    const cvv = document.getElementById('cvv').value;
    
    // Validate all fields
    const cardValidation = validateCardNumber(cardNumber);
    if (!cardValidation.valid) {
        showError('cardNumber', cardValidation.message);
        return;
    }
    
    if (!cardName.trim()) {
        showError('cardName', 'Card name is required');
        return;
    }
    
    const expiryValidation = validateExpiryDate(expiryDate);
    if (!expiryValidation.valid) {
        showError('expiryDate', expiryValidation.message);
        return;
    }
    
    const cvvValidation = validateCVV(cvv);
    if (!cvvValidation.valid) {
        showError('cvv', cvvValidation.message);
        return;
    }
    
    // All validations passed, process payment
    processCardPayment({
        cardNumber: cardNumber,
        cardName: cardName,
        expiryDate: expiryDate,
        cvv: cvv
    });
}

function processCardPayment(cardData) {
    // Show processing overlay
    const overlay = document.getElementById('processingOverlay');
    overlay.style.display = 'flex';
    
    // Disable back button during processing
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, '', window.location.href);
    };
    
    // Send payment to server
    // In production, you would send this to your backend which communicates with the payment gateway
    fetch('/api/checkout/process-card', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            // Don't send actual card details to server in production
            // Instead, use payment gateway's tokenization
            cardLast4: cardData.cardNumber.slice(-4),
            cardName: cardData.cardName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Payment successful, redirect to success page
            window.location.href = '/checkout/order-success?order=' + data.orderNumber;
        } else {
            // Payment failed
            overlay.style.display = 'none';
            window.onpopstate = null;
            showNotification(data.message || 'Payment failed. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        overlay.style.display = 'none';
        window.onpopstate = null;
        showNotification('An error occurred. Please try again.', 'error');
    });
}

function loadOrderTotal() {
    // Load cart total for display
    fetch('/api/cart')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const totals = data.data.totals || {};
                const subtotal = parseFloat(totals.subtotal || 0);
                const tax = Math.round(subtotal * 0.08 * 100) / 100;
                const shipping = subtotal >= 5000 ? 0 : 500;
                const total = subtotal + tax + shipping;
                
                const totalElement = document.getElementById('totalAmount');
                if (totalElement) {
                    totalElement.textContent = 'LKR ' + total.toFixed(2);
                }
            }
        })
        .catch(error => {
            console.error('Error loading cart:', error);
        });
}

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    
    field.classList.add('error');
    const errorSpan = field.nextElementSibling;
    if (errorSpan && errorSpan.classList.contains('error-message')) {
        errorSpan.textContent = message;
        errorSpan.style.display = 'block';
    }
    
    // Remove error after field is edited
    field.addEventListener('input', function() {
        field.classList.remove('error');
        if (errorSpan) {
            errorSpan.style.display = 'none';
        }
    }, { once: true });
}

function showNotification(message, type = 'info') {
    // Remove existing notification if any
    const existing = document.querySelector('.notification-toast');
    if (existing) {
        existing.remove();
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification-toast notification-${type}`;
    
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Hide after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Add notification styles dynamically
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    .notification-toast {
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
        z-index: 10000;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .notification-toast.show {
        transform: translateX(0);
        opacity: 1;
    }
    
    .notification-toast i {
        font-size: 1.25rem;
    }
    
    .notification-success {
        border-left: 4px solid #10b981;
    }
    
    .notification-success i {
        color: #10b981;
    }
    
    .notification-error {
        border-left: 4px solid #ef4444;
    }
    
    .notification-error i {
        color: #ef4444;
    }
    
    .notification-info {
        border-left: 4px solid #3b82f6;
    }
    
    .notification-info i {
        color: #3b82f6;
    }
`;
document.head.appendChild(notificationStyles);

/**
 * INTEGRATION WITH ACTUAL PAYMENT GATEWAYS
 * 
 * For production, you should integrate with a real payment gateway.
 * Here are examples for popular gateways:
 * 
 * 1. STRIPE:
 *    - Include Stripe.js: <script src="https://js.stripe.com/v3/"></script>
 *    - Initialize: const stripe = Stripe('your_publishable_key');
 *    - Create payment intent on server
 *    - Use stripe.confirmCardPayment() to process
 * 
 * 2. PAYPAL:
 *    - Include PayPal SDK
 *    - Use PayPal Buttons for checkout
 *    - Handle order creation and capture
 * 
 * 3. PAYHERE (Sri Lanka):
 *    - Include PayHere SDK
 *    - Use PayHere.startPayment() with merchant details
 * 
 * Never store or transmit raw card details to your server!
 * Always use the payment gateway's tokenization/encryption.
 */
