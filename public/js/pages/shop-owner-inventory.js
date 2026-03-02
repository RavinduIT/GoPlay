/* ═══════════════════════════════════════════════════════════════
   Shop Owner — Inventory Dashboard JS
   ═══════════════════════════════════════════════════════════════ */

let currentFilter = 'all';
let currentSearch = '';

/* ── Init ─────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    // Auto-dismiss alerts
    document.querySelectorAll('.si-alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .3s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        }, 5000);
    });

    // Filter tabs
    document.querySelectorAll('.si-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.si-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    // Search
    const searchEl = document.getElementById('inventorySearch');
    if (searchEl) {
        let debounce;
        searchEl.addEventListener('input', () => {
            clearTimeout(debounce);
            debounce = setTimeout(() => {
                currentSearch = searchEl.value.toLowerCase().trim();
                applyFilters();
            }, 220);
        });
    }

    // Form validations
    setupAddForm();
    setupRemoveForm();

    // Backdrop close
    document.querySelectorAll('.si-modal').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
    });

    // ESC key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('.si-modal.open').forEach(m => closeModal(m.id));
    });
});

/* ── Filter & Search ─────────────────────────────────────────── */
function applyFilters() {
    const rows = document.querySelectorAll('#inventoryBody tr[data-status]');
    let visible = 0;
    rows.forEach(row => {
        const status  = row.dataset.status;
        const name    = row.querySelector('.si-product-name')?.textContent.toLowerCase() || '';
        const sku     = row.querySelector('.si-sku')?.textContent.toLowerCase() || '';
        const matchF  = currentFilter === 'all' || status === currentFilter;
        const matchS  = !currentSearch || name.includes(currentSearch) || sku.includes(currentSearch);
        const show    = matchF && matchS;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const countEl = document.getElementById('siCount');
    if (countEl) countEl.textContent = `${visible} product${visible !== 1 ? 's' : ''}`;
}

/* ── Modals ──────────────────────────────────────────────────── */
function openModal(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
}

/* Add Stock */
function openAddModal(productId, productName, currentStock) {
    document.getElementById('add-product-id').value = productId;
    document.getElementById('add-pname').textContent = productName;

    const stockEl = document.getElementById('add-pstock');
    stockEl.innerHTML = `Current stock: <strong>${currentStock}</strong> units`;
    stockEl.className = 'si-modal-pstock' + (currentStock === 0 ? ' has-out' : currentStock <= 5 ? ' has-low' : '');

    document.getElementById('add-quantity').value = '';
    openModal('addStockModal');
    setTimeout(() => document.getElementById('add-quantity').focus(), 200);
}

/* Remove Stock */
function openRemoveModal(productId, productName, currentStock) {
    document.getElementById('rem-product-id').value = productId;
    document.getElementById('rem-pname').textContent = productName;

    const stockEl = document.getElementById('rem-pstock');
    stockEl.innerHTML = `Current stock: <strong>${currentStock}</strong> units`;
    stockEl.className = 'si-modal-pstock' + (currentStock === 0 ? ' has-out' : currentStock <= 5 ? ' has-low' : '');

    document.getElementById('rem-quantity').value = '';
    document.getElementById('rem-reason').value = '';
    openModal('removeStockModal');
    setTimeout(() => document.getElementById('rem-quantity').focus(), 200);
}

/* Reorder Level */
function openReorderModal(productId, productName, currentLevel) {
    document.getElementById('reorder-product-id').value = productId;
    document.getElementById('reorder-pname').textContent = productName;
    document.getElementById('reorder-level').value = currentLevel;
    openModal('reorderModal');
    setTimeout(() => document.getElementById('reorder-level').focus(), 200);
}

/* ── Form Validation ─────────────────────────────────────────── */
function setupAddForm() {
    document.getElementById('addStockForm')?.addEventListener('submit', e => {
        const qty = parseInt(document.getElementById('add-quantity').value);
        if (!qty || qty < 1) {
            e.preventDefault();
            showToast('Please enter a valid quantity (minimum 1)', 'error');
        }
    });
}

function setupRemoveForm() {
    document.getElementById('removeStockForm')?.addEventListener('submit', e => {
        const qty    = parseInt(document.getElementById('rem-quantity').value);
        const reason = document.getElementById('rem-reason').value;
        const name   = document.getElementById('rem-pname').textContent;

        if (!qty || qty < 1) {
            e.preventDefault();
            showToast('Please enter a valid quantity (minimum 1)', 'error');
            return;
        }
        if (!reason) {
            e.preventDefault();
            showToast('Please select a reason for removing stock', 'error');
            return;
        }
        if (!confirm(`Remove ${qty} units of "${name}"?\nReason: ${reason}\n\nThis cannot be undone.`)) {
            e.preventDefault();
        }
    });
}

/* ── Toast ───────────────────────────────────────────────────── */
function showToast(message, type = 'success') {
    const t = document.getElementById('siToast');
    if (!t) return;
    const icons = { success: 'fa-check-circle', error: 'fa-times-circle' };
    t.innerHTML  = `<i class="fas ${icons[type] || icons.success}"></i> ${message}`;
    t.className  = `si-toast si-toast--${type} show`;
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 3200);
}
