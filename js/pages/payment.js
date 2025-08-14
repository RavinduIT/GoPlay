// Payment Page JavaScript

class PaymentApp {
    constructor() {
        this.currentPaymentMethod = 'card';
        this.orderTotal = 56.70;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadOrderData();
        this.formatCardInputs();
    }

    setupEventListeners() {
        // Payment method selection
        const paymentOptions = document.querySelectorAll('.payment-option');
        paymentOptions.forEach(option => {
            option.addEventListener('click', () => this.selectPaymentMethod(option));
        });

        // Payment form submission
        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            paymentForm.addEventListener('submit', (e) => this.handlePaymentSubmission(e));
        }

        // Card number formatting
        const cardNumberInput = document.getElementById('cardNumber');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', (e) => this.formatCardNumber(e));
        }

        // Expiry date formatting
        const expiryInput = document.getElementById('expiryDate');
        if (expiryInput) {
            expiryInput.addEventListener('input', (e) => this.formatExpiryDate(e));
        }

        // CVV input restriction
        const cvvInput = document.getElementById('cvv');
        if (cvvInput) {
            cvvInput.addEventListener('input', (e) => this.restrictCVV(e));
        }

        // Copy bank details
        const copyButton = document.querySelector('.bank-container .btn-primary');
        if (copyButton) {
            copyButton.addEventListener('click', () => this.copyBankDetails());
        }
    }

    selectPaymentMethod(selectedOption) {
        // Remove active class from all options
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('active');
        });

        // Add active class to selected option
        selectedOption.classList.add('active');

        // Update radio button
        const radio = selectedOption.querySelector('input[type="radio"]');
        radio.checked = true;

        // Get payment method
        this.currentPaymentMethod = radio.value;

        // Show/hide relevant sections
        this.togglePaymentSections();
    }

    togglePaymentSections() {
        const cardSection = document.querySelector('.card-section');
        const paypalSection = document.getElementById('paypalSection');
        const bankSection = document.getElementById('bankSection');
        const paymentForm = document.getElementById('paymentForm');

        // Hide all sections first
        if (cardSection) cardSection.style.display = 'block';
        if (paypalSection) paypalSection.style.display = 'none';
        if (bankSection) bankSection.style.display = 'none';

        switch (this.currentPaymentMethod) {
            case 'card':
                if (paymentForm) paymentForm.style.display = 'block';
                break;
            case 'paypal':
                if (cardSection) cardSection.style.display = 'none';
                if (paypalSection) paypalSection.style.display = 'block';
                if (paymentForm) paymentForm.style.display = 'none';
                break;
            case 'bank':
                if (cardSection) cardSection.style.display = 'none';
                if (bankSection) bankSection.style.display = 'block';
                if (paymentForm) paymentForm.style.display = 'none';
                break;
        }
    }

    formatCardInputs() {
        // Format card number with spaces
        const cardNumberInput = document.getElementById('cardNumber');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('keypress', (e) => {
                // Only allow numbers and backspace
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
                    e.preventDefault();
                }
            });
        }
    }

    formatCardNumber(event) {
        let value = event.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ');
        if (formattedValue) {
            event.target.value = formattedValue;
        }
    }

    formatExpiryDate(event) {
        let value = event.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        event.target.value = value;
    }

    restrictCVV(event) {
        let value = event.target.value.replace(/\D/g, '');
        event.target.value = value;
    }

    loadOrderData() {
        // Load order data from URL parameters or localStorage
        const urlParams = new URLSearchParams(window.location.search);
        const orderData = {
            itemName: urlParams.get('item') || 'Basketball Court Booking',
            description: urlParams.get('description') || 'Premium court at Sports Complex',
            date: urlParams.get('date') || 'March 15, 2024',
            time: urlParams.get('time') || '2:00 PM - 4:00 PM',
            price: parseFloat(urlParams.get('price')) || 50.00
        };

        // Update order display
        this.updateOrderDisplay(orderData);
    }

    updateOrderDisplay(orderData) {
        const elements = {
            orderItemName: document.getElementById('orderItemName'),
            orderItemDescription: document.getElementById('orderItemDescription'),
            orderDate: document.getElementById('orderDate'),
            orderTime: document.getElementById('orderTime'),
            orderItemPrice: document.getElementById('orderItemPrice'),
            subtotal: document.getElementById('subtotal'),
            serviceFee: document.getElementById('serviceFee'),
            tax: document.getElementById('tax'),
            totalAmount: document.getElementById('totalAmount'),
            payAmount: document.getElementById('payAmount')
        };

        // Calculate totals
        const subtotal = orderData.price;
        const serviceFee = subtotal * 0.05; // 5% service fee
        const tax = subtotal * 0.084; // 8.4% tax
        const total = subtotal + serviceFee + tax;

        this.orderTotal = total;

        // Update display
        if (elements.orderItemName) elements.orderItemName.textContent = orderData.itemName;
        if (elements.orderItemDescription) elements.orderItemDescription.textContent = orderData.description;
        if (elements.orderDate) elements.orderDate.textContent = orderData.date;
        if (elements.orderTime) elements.orderTime.textContent = orderData.time;
        if (elements.orderItemPrice) elements.orderItemPrice.textContent = `$${subtotal.toFixed(2)}`;
        if (elements.subtotal) elements.subtotal.textContent = `$${subtotal.toFixed(2)}`;
        if (elements.serviceFee) elements.serviceFee.textContent = `$${serviceFee.toFixed(2)}`;
        if (elements.tax) elements.tax.textContent = `$${tax.toFixed(2)}`;
        if (elements.totalAmount) elements.totalAmount.textContent = `$${total.toFixed(2)}`;
        if (elements.payAmount) elements.payAmount.textContent = total.toFixed(2);
    }

    validateCardForm() {
        const requiredFields = [
            'cardName',
            'cardNumber',
            'expiryDate',
            'cvv',
            'billingAddress',
            'billingCity',
            'billingZip',
            'billingCountry'
        ];

        const errors = [];

        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field || !field.value.trim()) {
                errors.push(`${fieldId.replace(/([A-Z])/g, ' $1').toLowerCase()} is required`);
            }
        });

        // Validate card number (basic check)
        const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
        if (cardNumber.length < 13 || cardNumber.length > 19) {
            errors.push('Invalid card number');
        }

        // Validate expiry date
        const expiryDate = document.getElementById('expiryDate').value;
        if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
            errors.push('Invalid expiry date format');
        }

        // Validate CVV
        const cvv = document.getElementById('cvv').value;
        if (cvv.length < 3 || cvv.length > 4) {
            errors.push('Invalid CVV');
        }

        // Check terms agreement
        const termsAgreement = document.getElementById('termsAgreement');
        if (!termsAgreement.checked) {
            errors.push('You must agree to the terms and conditions');
        }

        return errors;
    }

    async handlePaymentSubmission(event) {
        event.preventDefault();

        if (this.currentPaymentMethod === 'card') {
            const errors = this.validateCardForm();
            if (errors.length > 0) {
                this.showErrorMessages(errors);
                return;
            }
        }

        // Show processing modal
        this.showProcessingModal();

        try {
            // Simulate payment processing
            await this.processPayment();
            
            // Hide processing modal
            this.hideProcessingModal();
            
            // Redirect to success page
            this.redirectToSuccess();
        } catch (error) {
            console.error('Payment error:', error);
            this.hideProcessingModal();
            this.showErrorMessages(['Payment processing failed. Please try again.']);
        }
    }

    async processPayment() {
        // Simulate API call
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Simulate random success/failure for demo
                if (Math.random() > 0.1) { // 90% success rate
                    resolve({ success: true, transactionId: this.generateTransactionId() });
                } else {
                    reject(new Error('Payment declined'));
                }
            }, 3000);
        });
    }

    generateTransactionId() {
        return 'GP-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9).toUpperCase();
    }

    showProcessingModal() {
        const modal = document.getElementById('processingModal');
        if (modal) {
            modal.classList.add('show');
        }
    }

    hideProcessingModal() {
        const modal = document.getElementById('processingModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    showErrorMessages(errors) {
        // Create or update error display
        let errorContainer = document.querySelector('.error-messages');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.className = 'error-messages';
            errorContainer.style.cssText = `
                background: #fee2e2;
                color: #dc2626;
                padding: 1rem;
                border-radius: 8px;
                margin-bottom: 1rem;
                border: 1px solid #fecaca;
            `;
            
            const form = document.getElementById('paymentForm');
            if (form) {
                form.insertBefore(errorContainer, form.firstChild);
            }
        }

        errorContainer.innerHTML = `
            <h4 style="margin: 0 0 0.5rem 0; font-size: 1rem;">Please fix the following errors:</h4>
            <ul style="margin: 0; padding-left: 1.5rem;">
                ${errors.map(error => `<li>${error}</li>`).join('')}
            </ul>
        `;

        // Scroll to top of form
        errorContainer.scrollIntoView({ behavior: 'smooth' });
    }

    copyBankDetails() {
        const bankDetails = `
Bank: Commercial Bank 
Account Number: 8001234567
Account Name: GoPlay Sports Platform
Branch: Colombo 03
Reference: ${document.getElementById('bankReference').textContent}
        `.trim();

        navigator.clipboard.writeText(bankDetails).then(() => {
            const button = document.querySelector('.bank-container .btn-primary');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copied!';
            button.style.background = '#10b981';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = '';
            }, 2000);
        });
    }

    redirectToSuccess() {
        // Store order details for success page
        const orderDetails = {
            transactionId: this.generateTransactionId(),
            amount: this.orderTotal,
            paymentMethod: this.currentPaymentMethod,
            timestamp: new Date().toISOString()
        };

        localStorage.setItem('lastPayment', JSON.stringify(orderDetails));
        window.location.href = '/pages/payment-success.html';
    }
}

// Initialize payment app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PaymentApp();
});

// Global functions
window.paymentApp = {
    selectPaymentMethod: (method) => {
        const option = document.querySelector(`[data-method="${method}"]`);
        if (option) {
            option.click();
        }
    }
};