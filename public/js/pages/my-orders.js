// My Orders JavaScript
let reviewedProductIds = new Set();
let currentReviewProductId = null;

class MyOrdersDashboard {
    constructor() {
        this.orders = [];
        this.stats = {};
        this.currentFilters = {
            search: '',
            status: '',
            paymentStatus: '',
            sort: 'newest'
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadData();
        this.loadReviewedProducts();
        this.initReviewStarInput();
    }

    setupEventListeners() {
        // Search with debounce
        let searchTimeout;
        const searchInput = document.getElementById('searchOrders');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.currentFilters.search = e.target.value;
                    this.loadOrders();
                }, 500);
            });
        }

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.currentFilters.status = e.target.value;
                this.loadOrders();
            });
        }

        // Payment status filter
        const paymentFilter = document.getElementById('paymentStatusFilter');
        if (paymentFilter) {
            paymentFilter.addEventListener('change', (e) => {
                this.currentFilters.paymentStatus = e.target.value;
                this.loadOrders();
            });
        }

        // Sort
        const sortSelect = document.getElementById('sortOrders');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.currentFilters.sort = e.target.value;
                this.loadOrders();
            });
        }
    }

    async loadData() {
        await Promise.all([
            this.loadOrders(),
            this.loadStats()
        ]);
    }

    async loadReviewedProducts() {
        try {
            const response = await fetch('/api/user/reviewed-products', { credentials: 'same-origin' });
            if (!response.ok) return;
            const data = await response.json();
            if (data.success && data.product_ids) {
                reviewedProductIds = new Set(data.product_ids.map(Number));
            }
        } catch (e) {
            // silently ignore — user may not be logged in
        }
    }

    initReviewStarInput() {
        const container = document.getElementById('review-stars');
        if (!container) return;
        const stars = container.querySelectorAll('i');
        const input = document.getElementById('review-rating-value');

        stars.forEach(star => {
            star.addEventListener('mouseenter', () => {
                const r = parseInt(star.getAttribute('data-rating'));
                stars.forEach((s, i) => {
                    s.classList.toggle('fas', i < r);
                    s.classList.toggle('far', i >= r);
                });
            });
            star.addEventListener('click', () => {
                const r = parseInt(star.getAttribute('data-rating'));
                input.value = r;
                stars.forEach((s, i) => {
                    s.classList.toggle('fas', i < r);
                    s.classList.toggle('far', i >= r);
                });
            });
        });

        container.addEventListener('mouseleave', () => {
            const current = parseInt(input.value) || 0;
            stars.forEach((s, i) => {
                s.classList.toggle('fas', i < current);
                s.classList.toggle('far', i >= current);
            });
        });
    }

    async loadOrders() {
        try {
            const params = new URLSearchParams();
            if (this.currentFilters.search) params.append('search', this.currentFilters.search);
            if (this.currentFilters.status) params.append('status', this.currentFilters.status);
            if (this.currentFilters.paymentStatus) params.append('payment_status', this.currentFilters.paymentStatus);
            if (this.currentFilters.sort) params.append('sort', this.currentFilters.sort);

            const response = await fetch(`/api/user/orders?${params.toString()}`, {
                credentials: 'same-origin'
            });

            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            const data = await response.json();
            
            if (data.success) {
                this.orders = data.orders || [];
                this.renderOrders();
            } else {
                this.showError('Failed to load orders');
            }
        } catch (error) {
            console.error('Error loading orders:', error);
            this.showError('Failed to load orders');
        }
    }

    async loadStats() {
        try {
            const response = await fetch('/api/user/orders/stats', {
                credentials: 'same-origin'
            });

            if (response.status === 401) {
                return;
            }

            const data = await response.json();
            
            if (data.success) {
                this.stats = data.stats;
                this.updateStats();
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    updateStats() {
        document.getElementById('totalOrders').textContent = this.stats.total_orders || 0;
        document.getElementById('pendingOrders').textContent = this.stats.pending_orders || 0;
        document.getElementById('deliveredOrders').textContent = this.stats.delivered_orders || 0;
        document.getElementById('totalSpent').textContent = 'Rs. ' + (parseFloat(this.stats.total_spent || 0)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    renderOrders() {
        const loading = document.getElementById('ordersLoading');
        const list = document.getElementById('ordersList');
        const empty = document.getElementById('ordersEmpty');

        loading.style.display = 'none';

        if (this.orders.length === 0) {
            empty.style.display = 'flex';
            list.style.display = 'none';
            return;
        }

        empty.style.display = 'none';
        list.style.display = 'block';

        list.innerHTML = this.orders.map(order => this.createOrderCard(order)).join('');
    }

    createOrderCard(order) {
        const date = new Date(order.created_at);
        const formattedDate = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const canCancel = order.status === 'pending' || order.status === 'processing';
        const statusColors = {
            pending: 'warning',
            processing: 'info',
            shipped: 'primary',
            delivered: 'success',
            cancelled: 'danger'
        };
        const paymentColors = {
            paid: 'success',
            pending: 'warning',
            failed: 'danger',
            refund_pending: 'warning'
        };

        return `
            <div class="order-card">
                <div class="order-card-header">
                    <div class="order-info">
                        <h3 class="order-number">#${this.escapeHtml(order.order_number)}</h3>
                        <span class="order-date"><i class="fas fa-calendar"></i> ${formattedDate}</span>
                    </div>
                    <div class="order-badges">
                        <span class="badge badge-${statusColors[order.status] || 'secondary'}">${this.capitalizeFirst(order.status)}</span>
                        <span class="badge badge-payment-${paymentColors[order.payment_status] || 'secondary'}">
                            ${this.capitalizeFirst(order.payment_status?.replace('_', ' '))}
                        </span>
                    </div>
                </div>

                <div class="order-card-body">
                    <div class="order-details-grid">
                        <div class="detail-item">
                            <i class="fas fa-box"></i>
                            <div>
                                <strong>Items</strong>
                                <span>${order.item_count} item(s)</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-credit-card"></i>
                            <div>
                                <strong>Payment</strong>
                                <span>${this.capitalizeFirst(order.payment_method)}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <div>
                                <strong>Total</strong>
                                <span class="order-total">Rs. ${parseFloat(order.total_amount).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>

                    ${order.items ? `
                        <div class="order-items-preview">
                            <strong>Items:</strong> ${this.escapeHtml(order.items)}
                        </div>
                    ` : ''}
                </div>

                <div class="order-card-footer">
                    <button class="btn btn-primary btn-sm" onclick="dashboard.viewOrderDetails(${order.id})">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                    ${canCancel ? `
                        <button class="btn btn-danger btn-sm" onclick="dashboard.showCancelModal(${order.id}, '${this.escapeHtml(order.order_number)}')">
                            <i class="fas fa-times"></i> Cancel Order
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }

    async viewOrderDetails(orderId) {
        try {
            const modal = document.getElementById('viewOrderModal');
            const content = document.getElementById('orderDetailsContent');
            
            modal.style.display = 'flex';
            content.innerHTML = `
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading order details...</p>
                </div>
            `;

            const response = await fetch(`/api/user/orders/${orderId}`, {
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success && data.order) {
                this.displayOrderDetails(data.order);
            } else {
                this.showToast(data.message || 'Failed to load order details', 'error');
                closeModal();
            }
        } catch (error) {
            console.error('Error loading order details:', error);
            this.showToast('Failed to load order details', 'error');
            closeModal();
        }
    }

    displayOrderDetails(order) {
        const content = document.getElementById('orderDetailsContent');
        const date = new Date(order.created_at);
        const formattedDate = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        let shippingAddress = '';
        if (order.shipping_address) {
            try {
                const addr = typeof order.shipping_address === 'string' 
                    ? JSON.parse(order.shipping_address) 
                    : order.shipping_address;
                shippingAddress = `
                    ${addr.name || ''}<br>
                    ${addr.phone || ''}<br>
                    ${addr.address || ''}<br>
                    ${addr.city || ''}, ${addr.postal_code || ''}<br>
                    ${addr.province || ''}
                `;
            } catch (e) {
                console.error('Error parsing address:', e);
            }
        }

        const statusSteps = {
            pending: 1,
            processing: 2,
            shipped: 3,
            delivered: 4,
            cancelled: 0
        };
        const currentStep = statusSteps[order.status] || 1;
        const isCancelled = order.status === 'cancelled';
        const canCancel = order.status === 'pending' || order.status === 'processing';

        let html = `
            <div class="order-details">
                <div class="order-header-info">
                    <div class="detail-group">
                        <label>Order Number:</label>
                        <span class="order-number-large">#${this.escapeHtml(order.order_number)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Order Date:</label>
                        <span>${formattedDate}</span>
                    </div>
                    <div class="detail-group">
                        <label>Status:</label>
                        <span class="badge badge-${this.getStatusColor(order.status)}">${this.capitalizeFirst(order.status)}</span>
                    </div>
                </div>

                ${!isCancelled ? `
                <div class="order-timeline">
                    <div class="timeline-step ${currentStep >= 1 ? 'active' : ''}">
                        <div class="timeline-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div class="timeline-label">Order Placed</div>
                    </div>
                    <div class="timeline-line ${currentStep >= 2 ? 'active' : ''}"></div>
                    <div class="timeline-step ${currentStep >= 2 ? 'active' : ''}">
                        <div class="timeline-icon"><i class="fas fa-cog"></i></div>
                        <div class="timeline-label">Processing</div>
                    </div>
                    <div class="timeline-line ${currentStep >= 3 ? 'active' : ''}"></div>
                    <div class="timeline-step ${currentStep >= 3 ? 'active' : ''}">
                        <div class="timeline-icon"><i class="fas fa-shipping-fast"></i></div>
                        <div class="timeline-label">Shipped</div>
                    </div>
                    <div class="timeline-line ${currentStep >= 4 ? 'active' : ''}"></div>
                    <div class="timeline-step ${currentStep >= 4 ? 'active' : ''}">
                        <div class="timeline-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="timeline-label">Delivered</div>
                    </div>
                </div>
                ` : `
                <div class="cancelled-notice">
                    <i class="fas fa-times-circle"></i>
                    <span>This order has been cancelled</span>
                </div>
                `}

                ${shippingAddress ? `
                <div class="address-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Delivery Address</h3>
                    <div class="address-text">${shippingAddress}</div>
                </div>
                ` : ''}

                <div class="items-section">
                    <h3><i class="fas fa-box"></i> Order Items</h3>
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                ${order.status === 'delivered' ? '<th class="review-col">Review</th>' : ''}
                            </tr>
                        </thead>
                        <tbody>
        `;

        if (order.items && order.items.length > 0) {
            const isDelivered = order.status === 'delivered';
            order.items.forEach(item => {
                const productId = item.product_id ? parseInt(item.product_id) : null;
                let reviewCell = '';
                if (isDelivered && productId) {
                    if (reviewedProductIds.has(productId)) {
                        reviewCell = `<span class="badge-reviewed"><i class="fas fa-check"></i> Reviewed</span>`;
                    } else {
                        const itemName = this.escapeHtml(item.item_name || item.product_name || 'Product');
                        reviewCell = `<button class="btn-write-review" onclick="openReviewModal(${productId}, '${itemName}')"><i class="fas fa-star"></i> Review</button>`;
                    }
                }
                html += `
                    <tr>
                        <td>${this.escapeHtml(item.item_name || item.product_name || 'N/A')}</td>
                        <td>${this.escapeHtml(item.sku || '-')}</td>
                        <td>${item.quantity}</td>
                        <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>Rs. ${parseFloat(item.total_price).toFixed(2)}</td>
                        ${isDelivered ? `<td class="review-col">${reviewCell}</td>` : ''}
                    </tr>
                `;
            });
        }

        html += `
                        </tbody>
                    </table>
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <label>Subtotal:</label>
                        <span>Rs. ${parseFloat(order.subtotal).toFixed(2)}</span>
                    </div>
                    <div class="summary-row">
                        <label>Tax:</label>
                        <span>Rs. ${parseFloat(order.tax_amount || 0).toFixed(2)}</span>
                    </div>
                    <div class="summary-row">
                        <label>Shipping:</label>
                        <span>${parseFloat(order.shipping_amount) === 0 ? 'FREE' : 'Rs. ' + parseFloat(order.shipping_amount).toFixed(2)}</span>
                    </div>
                    ${order.discount_amount > 0 ? `
                    <div class="summary-row">
                        <label>Discount:</label>
                        <span>- Rs. ${parseFloat(order.discount_amount).toFixed(2)}</span>
                    </div>
                    ` : ''}
                    <div class="summary-row total">
                        <label>Total Amount:</label>
                        <span>Rs. ${parseFloat(order.total_amount).toFixed(2)}</span>
                    </div>
                </div>

                <div class="payment-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Payment Method:</label>
                            <span>${this.capitalizeFirst(order.payment_method)}</span>
                        </div>
                        <div class="info-item">
                            <label>Payment Status:</label>
                            <span class="badge badge-payment-${this.getPaymentColor(order.payment_status)}">
                                ${this.capitalizeFirst(order.payment_status?.replace('_', ' '))}
                            </span>
                        </div>
                    </div>
                </div>

                ${order.notes ? `
                <div class="notes-section">
                    <h3><i class="fas fa-sticky-note"></i> Notes</h3>
                    <p>${this.escapeHtml(order.notes)}</p>
                </div>
                ` : ''}

                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                    ${canCancel ? `
                        <button class="btn btn-danger" onclick="dashboard.showCancelModal(${order.id}, '${this.escapeHtml(order.order_number)}')">
                            <i class="fas fa-times"></i> Cancel Order
                        </button>
                    ` : ''}
                </div>
            </div>
        `;

        content.innerHTML = html;
    }

    showCancelModal(orderId, orderNumber) {
        closeModal(); // Close details modal if open
        
        const modal = document.getElementById('cancelOrderModal');
        document.getElementById('cancelOrderId').value = orderId;
        document.getElementById('cancelOrderInfo').textContent = `Order #${orderNumber}`;
        document.getElementById('cancelReason').value = '';
        modal.style.display = 'flex';
    }

    async confirmCancelOrder() {
        const orderId = document.getElementById('cancelOrderId').value;
        const reason = document.getElementById('cancelReason').value;

        try {
            const response = await fetch(`/api/user/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ reason })
            });

            const data = await response.json();

            if (data.success) {
                this.showToast(data.message || 'Order cancelled successfully', 'success');
                if (data.refund_status) {
                    this.showToast(data.refund_status, 'info');
                }
                closeCancelModal();
                this.loadData(); // Reload orders
            } else {
                this.showToast(data.message || 'Failed to cancel order', 'error');
            }
        } catch (error) {
            console.error('Error cancelling order:', error);
            this.showToast('Failed to cancel order', 'error');
        }
    }

    getStatusColor(status) {
        const colors = {
            pending: 'warning',
            processing: 'info',
            shipped: 'primary',
            delivered: 'success',
            cancelled: 'danger'
        };
        return colors[status] || 'secondary';
    }

    getPaymentColor(status) {
        const colors = {
            paid: 'success',
            pending: 'warning',
            failed: 'danger',
            refund_pending: 'warning'
        };
        return colors[status] || 'secondary';
    }

    showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast toast-${type} show`;
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    showError(message) {
        const loading = document.getElementById('ordersLoading');
        const list = document.getElementById('ordersList');
        const empty = document.getElementById('ordersEmpty');

        loading.style.display = 'none';
        list.style.display = 'none';
        empty.style.display = 'flex';
        empty.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Error Loading Orders</h3>
            <p>${message}</p>
            <button class="btn btn-primary" onclick="dashboard.loadData()">
                <i class="fas fa-redo"></i> Try Again
            </button>
        `;
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
}

// Modal functions
function closeModal() {
    const modal = document.getElementById('viewOrderModal');
    modal.style.display = 'none';
}

function closeCancelModal() {
    const modal = document.getElementById('cancelOrderModal');
    modal.style.display = 'none';
}

function confirmCancelOrder() {
    if (dashboard) {
        dashboard.confirmCancelOrder();
    }
}

function clearFilters() {
    document.getElementById('searchOrders').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('paymentStatusFilter').value = '';
    document.getElementById('sortOrders').value = 'newest';
    
    dashboard.currentFilters = {
        search: '',
        status: '',
        paymentStatus: '',
        sort: 'newest'
    };
    dashboard.loadOrders();
}

// Close modals on outside click
window.onclick = function(event) {
    const viewModal = document.getElementById('viewOrderModal');
    const cancelModal = document.getElementById('cancelOrderModal');
    const reviewModal = document.getElementById('reviewModal');

    if (event.target === viewModal) closeModal();
    if (event.target === cancelModal) closeCancelModal();
    if (event.target === reviewModal) closeReviewModal();
};

// ─── Review Modal ─────────────────────────────────────────────

function openReviewModal(productId, productName) {
    currentReviewProductId = productId;

    // Populate product name
    document.getElementById('review-product-info').textContent = productName;

    // Reset form
    document.getElementById('review-rating-value').value = '';
    document.getElementById('review-title-input').value = '';
    document.getElementById('review-text-input').value = '';
    document.querySelectorAll('#review-stars i').forEach(s => {
        s.classList.remove('fas');
        s.classList.add('far');
    });

    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
    currentReviewProductId = null;
}

async function submitReviewFromOrders() {
    const rating = parseInt(document.getElementById('review-rating-value').value);
    if (!rating || rating < 1 || rating > 5) {
        dashboard.showToast('Please select a star rating', 'error');
        return;
    }
    if (!currentReviewProductId) return;

    const title = document.getElementById('review-title-input').value.trim();
    const reviewText = document.getElementById('review-text-input').value.trim();

    try {
        const response = await fetch('/api/user/product-reviews', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                product_id: currentReviewProductId,
                rating,
                title,
                review_text: reviewText
            })
        });

        const data = await response.json();

        if (data.success) {
            reviewedProductIds.add(currentReviewProductId);
            closeReviewModal();
            dashboard.showToast('Review submitted! Thank you.', 'success');

            // Update any open order detail: swap Review button → Reviewed badge
            const pid = currentReviewProductId;
            document.querySelectorAll('.btn-write-review').forEach(btn => {
                if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(`openReviewModal(${pid},`)) {
                    const badge = document.createElement('span');
                    badge.className = 'badge-reviewed';
                    badge.innerHTML = '<i class="fas fa-check"></i> Reviewed';
                    btn.replaceWith(badge);
                }
            });
        } else {
            dashboard.showToast(data.message || 'Failed to submit review', 'error');
        }
    } catch (e) {
        console.error('Review submit error:', e);
        dashboard.showToast('Failed to submit review', 'error');
    }
}

// Initialize dashboard
let dashboard;
document.addEventListener('DOMContentLoaded', function() {
    dashboard = new MyOrdersDashboard();
});
