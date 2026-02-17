// Checkout Page JavaScript - GoPlay Platform

document.addEventListener('DOMContentLoaded', function() {
    // Determine which page we're on
    const currentPath = window.location.pathname;
    
    if (currentPath.includes('contact-details')) {
        initContactDetailsPage();
    } else if (currentPath.includes('payment-method')) {
        initPaymentMethodPage();
    }
    
    // Load cart summary on all checkout pages
    loadCartSummary();
});

// ===== CONTACT DETAILS PAGE =====
function initContactDetailsPage() {
    const form = document.getElementById('contactDetailsForm');
    
    if (!form) return;
    
    // Form validation
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.classList.contains('error')) {
                validateField(input);
            }
        });
    });
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', formatPhoneNumber);
    }
    
    // Postal code validation
    const postalCodeInput = document.getElementById('postalCode');
    if (postalCodeInput) {
        postalCodeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
    
    // Form submission
    form.addEventListener('submit', handleContactFormSubmit);
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Check if field is empty
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    // Email validation
    else if (field.type === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    // Phone validation (Sri Lankan format)
    else if (field.id === 'phone') {
        const phoneRegex = /^[0-9]{10}$/;
        const cleanPhone = value.replace(/\s/g, '');
        if (!phoneRegex.test(cleanPhone)) {
            isValid = false;
            errorMessage = 'Please enter a valid 10-digit phone number';
        }
    }
    // Postal code validation
    else if (field.id === 'postalCode') {
        if (value.length < 5) {
            isValid = false;
            errorMessage = 'Please enter a valid postal code';
        }
    }
    
    // Update UI
    if (isValid) {
        field.classList.remove('error');
        const errorSpan = field.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('error-message')) {
            errorSpan.textContent = '';
        }
    } else {
        field.classList.add('error');
        const errorSpan = field.nextElementSibling;
        if (errorSpan && errorSpan.classList.contains('error-message')) {
            errorSpan.textContent = errorMessage;
        }
    }
    
    return isValid;
}

function formatPhoneNumber(e) {
    let value = e.target.value.replace(/\s/g, '');
    if (value.length > 3 && value.length <= 6) {
        value = value.slice(0, 3) + ' ' + value.slice(3);
    } else if (value.length > 6) {
        value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6, 10);
    }
    e.target.value = value;
}

function handleContactFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    // Validate all fields
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        showNotification('Please fix the errors before continuing', 'error');
        return;
    }
    
    // Convert FormData to object
    const contactData = {};
    formData.forEach((value, key) => {
        contactData[key] = value;
    });
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
    
    // Send to server
    fetch('/api/checkout/save-contact', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(contactData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to payment method page
            window.location.href = '/checkout/payment-method';
        } else {
            showNotification(data.message || 'Failed to save contact details', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// ===== PAYMENT METHOD PAGE =====
function initPaymentMethodPage() {
    const paymentOptions = document.querySelectorAll('.payment-option input[type="radio"]');
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    
    if (!paymentOptions.length || !placeOrderBtn) return;
    
    // Enable place order button when payment method is selected
    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            placeOrderBtn.disabled = false;
        });
    });
    
    // Handle place order button
    placeOrderBtn.addEventListener('click', handlePlaceOrder);
}

function handlePlaceOrder(e) {
    e.preventDefault();
    
    const selectedMethod = document.querySelector('.payment-option input[type="radio"]:checked');
    
    if (!selectedMethod) {
        showNotification('Please select a payment method', 'error');
        return;
    }
    
    const method = selectedMethod.value;
    const btn = e.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;
    
    if (method === 'cod') {
        // Process cash on delivery
        processCashOnDelivery(btn, originalText);
    } else if (method === 'card') {
        // Redirect to card payment page
        window.location.href = '/checkout/payment-processing';
    }
}

function processCashOnDelivery(btn, originalText) {
    fetch('/api/checkout/process-cod', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to success page with order number
            window.location.href = '/checkout/order-success?order=' + data.orderNumber;
        } else {
            showNotification(data.message || 'Failed to process order', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// ===== CART SUMMARY =====
function loadCartSummary() {
    const summaryContainer = document.getElementById('orderSummary');
    
    if (!summaryContainer) return;
    
    fetch('/api/cart')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                renderCartSummary(data.data, summaryContainer);
            } else {
                summaryContainer.innerHTML = '<p>Your cart is empty</p>';
            }
        })
        .catch(error => {
            console.error('Error loading cart:', error);
            summaryContainer.innerHTML = '<p>Error loading cart</p>';
        });
}

function renderCartSummary(cartData, container) {
    const items = cartData.items || [];
    const totals = cartData.totals || {};
    
    if (items.length === 0) {
        container.innerHTML = '<p>Your cart is empty</p>';
        return;
    }
    
    // Calculate order totals
    const subtotal = parseFloat(totals.subtotal || 0);
    const tax = Math.round(subtotal * 0.08 * 100) / 100;
    const shipping = subtotal >= 5000 ? 0 : 500;
    const total = subtotal + tax + shipping;
    
    let html = '<div class="cart-items">';
    
    items.forEach(item => {
        const imageUrl = item.images ? JSON.parse(item.images)[0] : '/public/assets/images/placeholder.png';
        html += `
            <div class="cart-item">
                <img src="${imageUrl}" alt="${item.product_name}" class="item-image">
                <div class="item-details">
                    <div class="item-name">${item.product_name}</div>
                    <div class="item-quantity">Qty: ${item.quantity}</div>
                </div>
                <div class="item-price">LKR ${parseFloat(item.total_price).toFixed(2)}</div>
            </div>
        `;
    });
    
    html += '</div>';
    
    html += `
        <div class="order-totals">
            <div class="total-row subtotal">
                <span>Subtotal:</span>
                <span class="amount">LKR ${subtotal.toFixed(2)}</span>
            </div>
            <div class="total-row tax">
                <span>Tax (8%):</span>
                <span class="amount">LKR ${tax.toFixed(2)}</span>
            </div>
            <div class="total-row shipping">
                <span>Shipping:</span>
                <span class="amount">${shipping === 0 ? 'FREE' : 'LKR ' + shipping.toFixed(2)}</span>
            </div>
            <div class="total-row final-total">
                <span>Total:</span>
                <span class="amount">LKR ${total.toFixed(2)}</span>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    
    // Update total amount display if it exists (for card payment page)
    const totalAmountEl = document.getElementById('totalAmount');
    if (totalAmountEl) {
        totalAmountEl.textContent = 'LKR ' + total.toFixed(2);
    }
}

// ===== NOTIFICATIONS =====
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
