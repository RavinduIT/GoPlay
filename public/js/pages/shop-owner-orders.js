// Shop Owner Orders JavaScript
let currentOrderData = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    setupSearchAndFilters();
});

// Setup event listeners
function setupEventListeners() {
    // Edit Order Form Submit
    const editOrderForm = document.getElementById('editOrderForm');
    if (editOrderForm) {
        editOrderForm.addEventListener('submit', handleUpdateOrderStatus);
    }
}

// Setup search and filters
function setupSearchAndFilters() {
    const searchInput = document.getElementById('orderSearch');
    const statusFilter = document.getElementById('statusFilter');
    const paymentMethodFilter = document.getElementById('paymentMethodFilter');
    const paymentStatusFilter = document.getElementById('paymentStatusFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    
    // Real-time search with debounce
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }
    
    // Filter change events
    [statusFilter, paymentMethodFilter, paymentStatusFilter, dateFrom, dateTo].forEach(element => {
        if (element) {
            element.addEventListener('change', applyFilters);
        }
    });
}

// Apply filters and reload
function applyFilters() {
    const search = document.getElementById('orderSearch')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    const paymentMethod = document.getElementById('paymentMethodFilter')?.value || '';
    const paymentStatus = document.getElementById('paymentStatusFilter')?.value || '';
    const dateFrom = document.getElementById('dateFrom')?.value || '';
    const dateTo = document.getElementById('dateTo')?.value || '';
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (paymentMethod) params.append('payment_method', paymentMethod);
    if (paymentStatus) params.append('payment_status', paymentStatus);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    
    const queryString = params.toString();
    const newUrl = window.location.pathname + (queryString ? '?' + queryString : '');
    window.location.href = newUrl;
}

// Clear all filters
function clearFilters() {
    window.location.href = window.location.pathname;
}

// Refresh orders
function refreshOrders() {
    window.location.reload();
}

// View order details
async function viewOrder(orderId) {
    try {
        console.log('=== VIEW ORDER START ===');
        console.log('Order ID:', orderId);
        
        const url = `/shop-owner/get-order?id=${orderId}`;
        console.log('Fetching URL:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            credentials: 'same-origin', // Include cookies/session
            headers: {
                'Accept': 'application/json'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        console.log('Response ok:', response.ok);
        
        const contentType = response.headers.get('content-type');
        console.log('Content-Type:', contentType);
        
        const text = await response.text();
        console.log('Raw response text:', text);
        
        let data;
        try {
            data = JSON.parse(text);
            console.log('Parsed JSON data:', data);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            console.error('Response was:', text);
            showToast('Invalid server response', 'error');
            return;
        }
        
        if (data.success && data.order) {
            console.log('Order loaded successfully:', data.order);
            openModal('viewOrderModal');
            displayOrderDetails(data.order);
        } else {
            console.error('Request failed:', data.message);
            showToast(data.message || 'Failed to load order details', 'error');
        }
    } catch (error) {
        console.error('=== ERROR IN VIEW ORDER ===');
        console.error('Error:', error);
        console.error('Stack:', error.stack);
        showToast('Failed to load order details', 'error');
    }
}

// Display order details in modal
function displayOrderDetails(order) {
    const content = document.getElementById('orderDetailsContent');
    
    // Parse addresses
    let shippingAddress = '';
    let billingAddress = '';
    
    try {
        if (order.shipping_address) {
            const addr = typeof order.shipping_address === 'string' 
                ? JSON.parse(order.shipping_address) 
                : order.shipping_address;
            shippingAddress = formatAddress(addr);
        }
        
        if (order.billing_address) {
            const addr = typeof order.billing_address === 'string' 
                ? JSON.parse(order.billing_address) 
                : order.billing_address;
            billingAddress = formatAddress(addr);
        }
    } catch (e) {
        console.error('Error parsing addresses:', e);
    }
    
    // Format date
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    });
    
    let html = `
        <div class="order-details">
            <div class="order-header-info">
                <div class="detail-group">
                    <label>Order Number:</label>
                    <span class="order-number-large">${escapeHtml(order.order_number)}</span>
                </div>
                <div class="detail-group">
                    <label>Order Date:</label>
                    <span>${formattedDate}</span>
                </div>
                <div class="detail-group">
                    <label>Status:</label>
                    <span class="badge badge-${order.status}">${capitalizeFirst(order.status)}</span>
                </div>
            </div>
            
            <div class="customer-section">
                <h3><i class="fas fa-user"></i> Customer Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Name:</label>
                        <span>${escapeHtml(order.customer_name || 'N/A')}</span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span>${escapeHtml(order.customer_email || 'N/A')}</span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span>${escapeHtml(order.customer_phone || 'N/A')}</span>
                    </div>
                </div>
            </div>
            
            ${shippingAddress ? `
            <div class="address-section">
                <h3><i class="fas fa-map-marker-alt"></i> Shipping Address</h3>
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
                        </tr>
                    </thead>
                    <tbody>
    `;
    
    if (order.items && order.items.length > 0) {
        order.items.forEach(item => {
            html += `
                <tr>
                    <td>${escapeHtml(item.item_name || item.product_name || 'N/A')}</td>
                    <td>${escapeHtml(item.sku || '-')}</td>
                    <td>${item.quantity}</td>
                    <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(item.total_price).toFixed(2)}</td>
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
                    <span>Rs. ${parseFloat(order.shipping_amount || 0).toFixed(2)}</span>
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
                        <span>${capitalizeFirst(order.payment_method)}</span>
                    </div>
                    <div class="info-item">
                        <label>Payment Status:</label>
                        <span class="badge badge-payment-${order.payment_status}">${capitalizeFirst(order.payment_status)}</span>
                    </div>
                </div>
            </div>
            
            ${order.notes ? `
            <div class="notes-section">
                <h3><i class="fas fa-sticky-note"></i> Order Notes</h3>
                <p>${escapeHtml(order.notes)}</p>
            </div>
            ` : ''}
        </div>
    `;
    
    content.innerHTML = html;
}

// Edit order status
async function editOrderStatus(orderId) {
    try {
        console.log('Editing order status:', orderId);
        // Fetch order details
        const response = await fetch(`/shop-owner/get-order?id=${orderId}`, {
            credentials: 'same-origin'
        });
        console.log('Edit response status:', response.status);
        
        const data = await response.json();
        console.log('Edit response data:', data);
        
        if (data.success && data.order) {
            currentOrderData = data.order;
            
            // Populate form with current values
            document.getElementById('editOrderId').value = orderId;
            document.getElementById('orderStatus').value = data.order.status || 'pending';
            document.getElementById('paymentStatus').value = data.order.payment_status || 'pending';
            
            openModal('editOrderModal');
        } else {
            showToast(data.message || 'Failed to load order', 'error');
        }
    } catch (error) {
        console.error('Error fetching order:', error);
        showToast('Failed to load order', 'error');
    }
}

// Handle update order status
async function handleUpdateOrderStatus(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('editOrderId').value;
    const status = document.getElementById('orderStatus').value;
    const paymentStatus = document.getElementById('paymentStatus').value;
    
    console.log('=== UPDATE ORDER STATUS START ===');
    console.log('Order ID:', orderId);
    console.log('New Status:', status);
    console.log('Payment Status:', paymentStatus);
    console.log('Current Order Data:', currentOrderData);
    
    if (!orderId || !status || !paymentStatus) {
        showToast('Please select both order status and payment status', 'error');
        return;
    }
    
    // Confirmation
    if (!confirm('Are you sure you want to update this order?')) {
        return;
    }
    
    try {
        const payload = {
            order_id: parseInt(orderId),
            status: status,
            payment_status: paymentStatus
        };
        
        console.log('Updating order with payload:', payload);
        
        const response = await fetch('/shop-owner/update-order-status', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        
        console.log('Update response status:', response.status);
        console.log('Update response ok:', response.ok);
        
        const text = await response.text();
        console.log('Raw update response:', text);
        
        let data;
        try {
            data = JSON.parse(text);
            console.log('Parsed update response:', data);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            console.error('Response was:', text);
            showToast('Invalid server response', 'error');
            return;
        }
        
        if (data.success) {
            showToast(data.message || 'Order updated successfully', 'success');
            closeModal('editOrderModal');
            setTimeout(() => refreshOrders(), 1000);
        } else {
            showToast(data.message || 'Failed to update order', 'error');
        }
    } catch (error) {
        console.error('=== ERROR IN UPDATE ORDER ===');
        console.error('Error:', error);
        console.error('Stack:', error.stack);
        showToast('Failed to update order', 'error');
    }
}

// Delete order
function deleteOrder(orderId) {
    // Find order row to get order number
    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
    const orderNumber = row ? row.querySelector('.order-number')?.textContent : '';
    
    document.getElementById('deleteOrderId').value = orderId;
    document.getElementById('deleteOrderInfo').textContent = `Order: ${orderNumber}`;
    
    openModal('deleteOrderModal');
}

// Confirm delete order
async function confirmDeleteOrder() {
    const orderId = document.getElementById('deleteOrderId').value;
    
    console.log('Deleting order:', orderId);
    
    try {
        const response = await fetch('/shop-owner/delete-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ order_id: parseInt(orderId) })
        });
        
        console.log('Delete response status:', response.status);
        const data = await response.json();
        console.log('Delete response data:', data);
        
        if (data.success) {
            showToast(data.message || 'Order deleted successfully', 'success');
            closeModal('deleteOrderModal');
            setTimeout(() => refreshOrders(), 1000);
        } else {
            showToast(data.message || 'Failed to delete order', 'error');
        }
    } catch (error) {
        console.error('Error deleting order:', error);
        showToast('Failed to delete order', 'error');
    }
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    toast.textContent = message;
    toast.className = `toast toast-${type} show`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Utility functions
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatAddress(addr) {
    if (!addr) return '';
    
    const parts = [];
    if (addr.street) parts.push(addr.street);
    if (addr.address) parts.push(addr.address);
    if (addr.city) parts.push(addr.city);
    if (addr.state) parts.push(addr.state);
    if (addr.zip || addr.postal_code) parts.push(addr.zip || addr.postal_code);
    if (addr.country) parts.push(addr.country);
    
    return parts.join(', ');
}

// Sidebar toggle (if needed)
function toggleSidebar() {
    const sidebar = document.querySelector('.dashboard-sidebar');
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
}
