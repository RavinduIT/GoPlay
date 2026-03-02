/* ═══════════════════════════════════════════════════════════════
   Shop Owner — Orders Dashboard JS
   ═══════════════════════════════════════════════════════════════ */

let currentOrderData = null;

/* ── Init ─────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    setupFilters();
    document.getElementById('editOrderForm')
        ?.addEventListener('submit', handleUpdateOrderStatus);

    // Close modals on backdrop click
    document.querySelectorAll('.so-modal').forEach(m => {
        m.addEventListener('click', e => {
            if (e.target === m) closeModal(m.id);
        });
    });
});

/* ── Filter / Search ─────────────────────────────────────────── */
function setupFilters() {
    let debounce;
    document.getElementById('orderSearch')?.addEventListener('input', () => {
        clearTimeout(debounce);
        debounce = setTimeout(applyFilters, 480);
    });

    ['statusFilter','paymentMethodFilter','paymentStatusFilter','dateFrom','dateTo']
        .forEach(id => document.getElementById(id)?.addEventListener('change', applyFilters));
}

function applyFilters() {
    const params = new URLSearchParams();
    const get = id => document.getElementById(id)?.value || '';
    if (get('orderSearch'))        params.set('search',         get('orderSearch'));
    if (get('statusFilter'))       params.set('status',         get('statusFilter'));
    if (get('paymentMethodFilter'))params.set('payment_method', get('paymentMethodFilter'));
    if (get('paymentStatusFilter'))params.set('payment_status', get('paymentStatusFilter'));
    if (get('dateFrom'))           params.set('date_from',      get('dateFrom'));
    if (get('dateTo'))             params.set('date_to',        get('dateTo'));

    const qs = params.toString();
    window.location.href = window.location.pathname + (qs ? '?' + qs : '');
}

function clearFilters()  { window.location.href = window.location.pathname; }
function refreshOrders() { window.location.reload(); }

/* ── View Order ──────────────────────────────────────────────── */
async function viewOrder(orderId) {
    openModal('viewOrderModal');
    document.getElementById('orderDetailsContent').innerHTML = `
        <div class="so-loading">
            <i class="fas fa-spinner fa-spin"></i><p>Loading order details…</p>
        </div>`;

    try {
        const res  = await fetch(`/shop-owner/get-order?id=${orderId}`, { credentials: 'same-origin' });
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch {
            showToast('Invalid server response', 'error');
            closeModal('viewOrderModal');
            return;
        }
        if (data.success && data.order) {
            displayOrderDetails(data.order);
        } else {
            showToast(data.message || 'Failed to load order', 'error');
            closeModal('viewOrderModal');
        }
    } catch {
        showToast('Failed to load order details', 'error');
        closeModal('viewOrderModal');
    }
}

/* ── Render Order Detail ─────────────────────────────────────── */
function displayOrderDetails(order) {
    const orderDate    = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('en-LK', {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });

    let shippingAddr = '';
    try {
        const a = typeof order.shipping_address === 'string'
            ? JSON.parse(order.shipping_address) : order.shipping_address;
        shippingAddr = formatAddress(a);
    } catch { /* ignore */ }

    /* items rows */
    const itemRows = (order.items || []).map(item => `
        <tr>
            <td>${esc(item.item_name || item.product_name || 'N/A')}</td>
            <td>${esc(item.sku || '—')}</td>
            <td>${item.quantity}</td>
            <td>Rs. ${parseFloat(item.unit_price).toFixed(2)}</td>
            <td>Rs. ${parseFloat(item.total_price).toFixed(2)}</td>
        </tr>`).join('');

    /* summary rows */
    const summaryRows = order.is_multi_seller
        ? `<div class="so-sum-row"><label>Your Products Subtotal</label><span>Rs. ${parseFloat(order.shop_owner_subtotal||0).toFixed(2)}</span></div>`
        : `<div class="so-sum-row"><label>Subtotal</label><span>Rs. ${parseFloat(order.shop_owner_subtotal||0).toFixed(2)}</span></div>
           <div class="so-sum-row"><label>Tax</label><span>Rs. ${parseFloat(order.tax_amount||0).toFixed(2)}</span></div>
           <div class="so-sum-row"><label>Shipping</label><span>Rs. ${parseFloat(order.shipping_amount||0).toFixed(2)}</span></div>
           ${order.discount_amount > 0 ? `<div class="so-sum-row"><label>Discount</label><span>− Rs. ${parseFloat(order.discount_amount).toFixed(2)}</span></div>` : ''}
           <div class="so-sum-row so-sum-row--total"><label>Order Total</label><span>Rs. ${parseFloat(order.total_amount).toFixed(2)}</span></div>`;

    document.getElementById('orderDetailsContent').innerHTML = `
        <div class="so-detail">
            <div class="so-detail-meta">
                <div class="so-detail-kv">
                    <label>Order Number</label>
                    <span class="so-order-num-lg">#${esc(order.order_number)}</span>
                </div>
                <div class="so-detail-kv">
                    <label>Placed On</label>
                    <span>${formattedDate}</span>
                </div>
                <div class="so-detail-kv">
                    <label>Order Status</label>
                    <span><span class="so-badge so-badge--${order.status}">${cap(order.status)}</span></span>
                </div>
                <div class="so-detail-kv">
                    <label>Payment Status</label>
                    <span><span class="so-badge so-badge--pay-${order.payment_status}">
                        ${order.payment_status === 'paid' ? '<i class="fas fa-lock" style="font-size:.6rem;"></i> ' : ''}${cap(order.payment_status)}
                    </span></span>
                </div>
            </div>

            <div class="so-section">
                <p class="so-section-head"><i class="fas fa-user"></i> Customer</p>
                <div class="so-info-grid">
                    <div class="so-info-item"><label>Name</label><span>${esc(order.customer_name||'N/A')}</span></div>
                    <div class="so-info-item"><label>Email</label><span>${esc(order.customer_email||'N/A')}</span></div>
                    <div class="so-info-item"><label>Phone</label><span>${esc(order.customer_phone||'N/A')}</span></div>
                </div>
            </div>

            ${shippingAddr ? `
            <div class="so-section">
                <p class="so-section-head"><i class="fas fa-map-marker-alt"></i> Shipping Address</p>
                <div class="so-addr-box">${shippingAddr}</div>
            </div>` : ''}

            <div class="so-section">
                <p class="so-section-head"><i class="fas fa-box-open"></i> Items (your products)</p>
                ${order.is_multi_seller ? `
                <div class="so-multi-notice">
                    <i class="fas fa-info-circle"></i>
                    <span>This order has products from multiple sellers. Showing your ${order.shop_owner_item_count} of ${order.total_item_count} items.</span>
                </div>` : ''}
                <table class="so-items-table">
                    <thead>
                        <tr><th>Product</th><th>SKU</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>${itemRows}</tbody>
                </table>
            </div>

            <div class="so-section">
                <p class="so-section-head"><i class="fas fa-receipt"></i> Order Summary</p>
                <div class="so-summary">${summaryRows}</div>
            </div>

            <div class="so-section">
                <p class="so-section-head"><i class="fas fa-credit-card"></i> Payment</p>
                <div class="so-info-grid">
                    <div class="so-info-item"><label>Method</label><span>${cap(order.payment_method)}</span></div>
                </div>
                ${order.payment_status === 'paid'
                    ? '<p style="font-size:.75rem;color:#059669;margin-top:.5rem;"><i class="fas fa-lock"></i> Payment is verified and locked.</p>'
                    : ''}
            </div>

            ${order.notes ? `
            <div class="so-section">
                <p class="so-section-head"><i class="fas fa-sticky-note"></i> Notes</p>
                <div class="so-addr-box">${esc(order.notes)}</div>
            </div>` : ''}
        </div>`;
}

/* ── Edit Status ─────────────────────────────────────────────── */
async function editOrderStatus(orderId) {
    try {
        const res  = await fetch(`/shop-owner/get-order?id=${orderId}`, { credentials: 'same-origin' });
        const data = await res.json();
        if (!data.success || !data.order) {
            showToast(data.message || 'Failed to load order', 'error');
            return;
        }
        currentOrderData = data.order;

        document.getElementById('editOrderId').value    = orderId;
        document.getElementById('orderStatus').value    = data.order.status || 'pending';
        const payEl = document.getElementById('paymentStatus');
        const hint  = document.getElementById('payHint');

        payEl.value = data.order.payment_status || 'pending';

        if (data.order.payment_status === 'paid') {
            payEl.disabled            = true;
            hint.textContent          = 'Payment is verified — cannot be changed.';
            hint.className            = 'so-form-hint so-form-hint--ok';
        } else {
            payEl.disabled            = false;
            hint.textContent          = 'Updates payment status alongside order status.';
            hint.className            = 'so-form-hint';
        }

        openModal('editOrderModal');
    } catch {
        showToast('Failed to load order', 'error');
    }
}

/* ── Submit Status Update ────────────────────────────────────── */
async function handleUpdateOrderStatus(e) {
    e.preventDefault();

    const orderId  = document.getElementById('editOrderId').value;
    const status   = document.getElementById('orderStatus').value;
    const payEl    = document.getElementById('paymentStatus');

    if (!orderId || !status) { showToast('Please select an order status', 'error'); return; }

    const payload = { order_id: parseInt(orderId), status };
    if (!payEl.disabled) payload.payment_status = payEl.value;

    try {
        const res  = await fetch('/shop-owner/update-order-status', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch {
            showToast('Invalid server response', 'error'); return;
        }

        if (data.success) {
            showToast(data.message || 'Order updated successfully', 'success');
            closeModal('editOrderModal');
            setTimeout(refreshOrders, 900);
        } else {
            showToast(data.message || 'Failed to update order', 'error');
        }
    } catch {
        showToast('Failed to update order', 'error');
    }
}

/* ── Delete Order ────────────────────────────────────────────── */
function deleteOrder(orderId) {
    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
    const num = row?.querySelector('.so-order-num')?.textContent || '';
    document.getElementById('deleteOrderId').value   = orderId;
    document.getElementById('deleteOrderInfo').textContent = num;
    openModal('deleteOrderModal');
}

async function confirmDeleteOrder() {
    const orderId = document.getElementById('deleteOrderId').value;
    try {
        const res  = await fetch('/shop-owner/delete-order', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: parseInt(orderId) }),
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message || 'Order deleted', 'success');
            closeModal('deleteOrderModal');
            // Remove row from DOM without full reload
            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
            if (row) {
                row.style.transition = 'opacity .3s';
                row.style.opacity = '0';
                setTimeout(() => { row.remove(); updateCount(-1); }, 300);
            }
        } else {
            showToast(data.message || 'Failed to delete order', 'error');
        }
    } catch {
        showToast('Failed to delete order', 'error');
    }
}

function updateCount(delta) {
    const el = document.querySelector('.so-table-count');
    if (!el) return;
    const m = el.textContent.match(/(\d+)/);
    if (m) {
        const n = Math.max(0, parseInt(m[1]) + delta);
        el.textContent = n + ' result' + (n !== 1 ? 's' : '');
    }
}

/* ── Modal ───────────────────────────────────────────────────── */
function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('open');
    document.body.style.overflow = '';
}

/* ── Toast ───────────────────────────────────────────────────── */
function showToast(message, type = 'info') {
    const t = document.getElementById('soToast');
    if (!t) return;
    const icons = { success:'fa-check-circle', error:'fa-times-circle', warning:'fa-exclamation-triangle', info:'fa-info-circle' };
    t.innerHTML  = `<i class="fas ${icons[type]||icons.info}"></i> ${message}`;
    t.className  = `so-toast so-toast--${type} show`;
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 3200);
}

/* ── Utilities ───────────────────────────────────────────────── */
function esc(text) {
    if (!text) return '';
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}
function cap(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}
function formatAddress(a) {
    if (!a || typeof a !== 'object') return '';
    return [a.street, a.address, a.city, a.state, a.zip || a.postal_code, a.country]
        .filter(Boolean).join(', ');
}
