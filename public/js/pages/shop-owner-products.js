/**
 * Shop Owner — Products Dashboard
 * Renders products from window.shopProducts (PHP JSON embed).
 * Handles: filter, sort, paginate, table/grid view, edit/delete modals.
 */

const ITEMS_PER_PAGE = 20;

let filteredProducts = [];
let currentPage      = 1;
let currentView      = 'table';

// ============================================================
// INIT
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    filteredProducts = [...(window.shopProducts || [])];
    sortProducts();
    renderProducts();
    updatePagination();
    updateResultsCount();
    setupSearch();
    setupFilters();
    autoHideAlerts();
});

// ============================================================
// FILTER + SORT
// ============================================================

function applyFilters() {
    const search   = (document.getElementById('productSearch')?.value || '').toLowerCase().trim();
    const category = document.getElementById('categoryFilter')?.value || '';
    const stock    = document.getElementById('stockFilter')?.value   || '';

    filteredProducts = (window.shopProducts || []).filter(p => {
        if (search) {
            const haystack = (p.name + ' ' + p.sku + ' ' + (p.brand || '')).toLowerCase();
            if (!haystack.includes(search)) return false;
        }
        if (category && String(p.category_id) !== String(category)) return false;
        if (stock) {
            if (getStockClass(p) !== stock) return false;
        }
        return true;
    });

    sortProducts();
    currentPage = 1;
    renderProducts();
    updatePagination();
    updateResultsCount();

    // Show/hide search clear button
    const clearBtn = document.getElementById('searchClearBtn');
    if (clearBtn) clearBtn.style.display = search ? '' : 'none';
}

function sortProducts() {
    const sort = document.getElementById('sortFilter')?.value || 'newest';
    filteredProducts.sort((a, b) => {
        switch (sort) {
            case 'name-asc':   return a.name.localeCompare(b.name);
            case 'name-desc':  return b.name.localeCompare(a.name);
            case 'price-low':  return a.price - b.price;
            case 'price-high': return b.price - a.price;
            case 'stock-low':  return a.stock_quantity - b.stock_quantity;
            case 'oldest':     return new Date(a.updated_at) - new Date(b.updated_at);
            default:           return new Date(b.updated_at) - new Date(a.updated_at);
        }
    });
}

function setupSearch() {
    const input = document.getElementById('productSearch');
    if (!input) return;
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(applyFilters, 280);
    });
}

function setupFilters() {
    ['categoryFilter', 'stockFilter'].forEach(id => {
        document.getElementById(id)?.addEventListener('change', applyFilters);
    });
    document.getElementById('sortFilter')?.addEventListener('change', () => {
        sortProducts();
        currentPage = 1;
        renderProducts();
        updatePagination();
    });
}

function clearFilters() {
    ['productSearch', 'categoryFilter', 'stockFilter'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    const sort = document.getElementById('sortFilter');
    if (sort) sort.value = 'newest';
    const clearBtn = document.getElementById('searchClearBtn');
    if (clearBtn) clearBtn.style.display = 'none';
    applyFilters();
}

function clearSearch() {
    const input = document.getElementById('productSearch');
    if (input) { input.value = ''; input.focus(); }
    const clearBtn = document.getElementById('searchClearBtn');
    if (clearBtn) clearBtn.style.display = 'none';
    applyFilters();
}

// ============================================================
// RENDER
// ============================================================

function renderProducts() {
    const start = (currentPage - 1) * ITEMS_PER_PAGE;
    const page  = filteredProducts.slice(start, start + ITEMS_PER_PAGE);
    currentView === 'table' ? renderTable(page) : renderGrid(page);
}

/* --- TABLE --- */
function renderTable(products) {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;

    if (products.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="10">
                <div class="sp-empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No products found</h3>
                    <p>${filteredProducts.length === 0 && (window.shopProducts || []).length > 0
                        ? 'Try adjusting your search or filters.'
                        : 'Get started by adding your first product.'}</p>
                    <button class="sp-btn-primary" onclick="openAddProductModal()">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>
            </td></tr>`;
        return;
    }

    tbody.innerHTML = products.map(buildTableRow).join('');
    setupSelectAllCheckbox();
}

function buildTableRow(p) {
    const img       = getFirstImage(p);
    const sc        = getStockClass(p);
    const disc      = getDiscount(p);
    const timeAgo   = getTimeAgo(p.updated_at);

    return `
    <tr class="sp-product-row" data-id="${p.id}">
        <td><input type="checkbox" class="sp-checkbox sp-product-checkbox" value="${p.id}"></td>
        <td>
            <div class="sp-product-info">
                <div class="sp-product-thumb">
                    <img src="${esc(img)}" alt="${esc(p.name)}"
                         onerror="this.src='/public/assets/images/placeholder-product.svg'">
                    ${disc ? `<span class="sp-discount-badge">-${disc}%</span>` : ''}
                </div>
                <div class="sp-product-meta">
                    <h4 title="${esc(p.name)}">${esc(p.name)}</h4>
                    <span class="sp-brand-tag">${esc(p.brand || 'No Brand')}</span>
                </div>
            </div>
        </td>
        <td><span class="sp-sku">${esc(p.sku)}</span></td>
        <td><span class="sp-category-badge">${esc(p.category_name)}</span></td>
        <td>
            <div class="sp-price-cell">
                <span class="sp-price">LKR ${fmt(p.price)}</span>
                ${p.compare_price && p.compare_price > p.price
                    ? `<span class="sp-compare-price">LKR ${fmt(p.compare_price)}</span>` : ''}
            </div>
        </td>
        <td>
            <div class="sp-stock-cell ${sc}">
                <span class="sp-stock-dot"></span>
                <span>${p.stock_quantity} units</span>
            </div>
        </td>
        <td><span class="sp-status-badge ${p.status}">${capFirst(p.status)}</span></td>
        <td>
            <div class="sp-rating">
                <i class="fas fa-star"></i>
                <span>${parseFloat(p.rating || 0).toFixed(1)} (${p.total_reviews || 0})</span>
            </div>
        </td>
        <td><span class="sp-time-ago">${timeAgo}</span></td>
        <td>
            <div class="sp-action-btns">
                <button class="sp-btn-action sp-btn-edit"
                        onclick="editProduct(${p.id})" title="Edit product">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="sp-btn-action sp-btn-delete"
                        onclick="deleteProduct(${p.id}, '${esc(p.name)}')" title="Delete product">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </td>
    </tr>`;
}

/* --- GRID --- */
function renderGrid(products) {
    const grid = document.getElementById('gridView');
    if (!grid) return;

    if (products.length === 0) {
        grid.innerHTML = `
            <div class="sp-empty-full sp-empty-state">
                <i class="fas fa-box-open"></i>
                <h3>No products found</h3>
                <p>Try adjusting your filters.</p>
                <button class="sp-btn-primary" onclick="openAddProductModal()">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>`;
        return;
    }

    grid.innerHTML = products.map(buildGridCard).join('');
}

function buildGridCard(p) {
    const img  = getFirstImage(p);
    const sc   = getStockClass(p);
    const disc = getDiscount(p);

    return `
    <div class="sp-product-card ${sc}">
        <div class="sp-card-img">
            <img src="${esc(img)}" alt="${esc(p.name)}"
                 onerror="this.src='/public/assets/images/placeholder-product.svg'">
            <div class="sp-card-badges">
                <span class="sp-status-badge ${p.status}">${capFirst(p.status)}</span>
                ${disc ? `<span class="sp-discount-badge">-${disc}%</span>` : ''}
            </div>
        </div>
        <div class="sp-card-body">
            <div class="sp-card-category">${esc(p.category_name)}</div>
            <h4 class="sp-card-name" title="${esc(p.name)}">${esc(p.name)}</h4>
            <div class="sp-card-brand">${esc(p.brand || 'No Brand')}</div>
            <div class="sp-card-price-row">
                <span class="sp-price">LKR ${fmt(p.price)}</span>
                ${p.compare_price && p.compare_price > p.price
                    ? `<span class="sp-compare-price">LKR ${fmt(p.compare_price)}</span>` : ''}
            </div>
            <div class="sp-card-stock ${sc}">
                <span class="sp-stock-dot"></span>
                <span>${p.stock_quantity} units</span>
            </div>
            <div class="sp-card-rating">
                <i class="fas fa-star"></i>
                <span>${parseFloat(p.rating || 0).toFixed(1)} (${p.total_reviews || 0})</span>
            </div>
        </div>
        <div class="sp-card-actions">
            <button class="sp-card-btn sp-card-btn-edit" onclick="editProduct(${p.id})">
                <i class="fas fa-edit"></i> Edit
            </button>
            <button class="sp-card-btn sp-card-btn-delete"
                    onclick="deleteProduct(${p.id}, '${esc(p.name)}')" title="Delete">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>`;
}

// ============================================================
// VIEW TOGGLE
// ============================================================

function setView(view) {
    currentView = view;
    const tableView = document.getElementById('tableView');
    const gridView  = document.getElementById('gridView');
    const tableBtn  = document.getElementById('tableViewBtn');
    const gridBtn   = document.getElementById('gridViewBtn');

    if (view === 'table') {
        tableView.style.display = '';
        gridView.style.display  = 'none';
        tableBtn.classList.add('active');
        gridBtn.classList.remove('active');
    } else {
        tableView.style.display = 'none';
        gridView.style.display  = 'grid';
        tableBtn.classList.remove('active');
        gridBtn.classList.add('active');
    }
    renderProducts();
}

// ============================================================
// PAGINATION
// ============================================================

function updatePagination() {
    const total      = filteredProducts.length;
    const totalPages = Math.ceil(total / ITEMS_PER_PAGE);
    const start      = total === 0 ? 0 : (currentPage - 1) * ITEMS_PER_PAGE + 1;
    const end        = Math.min(currentPage * ITEMS_PER_PAGE, total);

    const infoEl = document.getElementById('paginationInfo');
    if (infoEl) {
        infoEl.textContent = total === 0
            ? 'No products'
            : `Showing ${start}–${end} of ${total} product${total !== 1 ? 's' : ''}`;
    }

    const btnsEl = document.getElementById('paginationBtns');
    if (!btnsEl) return;
    if (totalPages <= 1) { btnsEl.innerHTML = ''; return; }

    let html = `<button class="sp-page-btn" onclick="goToPage(${currentPage - 1})"
                        ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>`;

    for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1) {
            html += `<button class="sp-page-btn ${p === currentPage ? 'active' : ''}"
                             onclick="goToPage(${p})">${p}</button>`;
        } else if (Math.abs(p - currentPage) === 2) {
            html += `<span class="sp-page-ellipsis">…</span>`;
        }
    }

    html += `<button class="sp-page-btn" onclick="goToPage(${currentPage + 1})"
                     ${currentPage === totalPages ? 'disabled' : ''}>
                 <i class="fas fa-chevron-right"></i>
             </button>`;

    btnsEl.innerHTML = html;
}

function goToPage(page) {
    const totalPages = Math.ceil(filteredProducts.length / ITEMS_PER_PAGE);
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    renderProducts();
    updatePagination();
    // Scroll table/grid into view
    document.getElementById('tableView')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function updateResultsCount() {
    const el    = document.getElementById('resultsCount');
    const total = filteredProducts.length;
    if (el) el.textContent = `${total} product${total !== 1 ? 's' : ''}`;
}

// ============================================================
// CHECKBOXES
// ============================================================

function setupSelectAllCheckbox() {
    const selectAll = document.getElementById('selectAll');
    if (!selectAll) return;
    selectAll.checked       = false;
    selectAll.indeterminate = false;

    selectAll.addEventListener('change', function () {
        document.querySelectorAll('.sp-product-checkbox')
            .forEach(cb => { cb.checked = this.checked; });
    });

    document.querySelectorAll('.sp-product-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            const all     = document.querySelectorAll('.sp-product-checkbox');
            const checked = document.querySelectorAll('.sp-product-checkbox:checked');
            selectAll.checked       = all.length === checked.length;
            selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
        });
    });
}

// ============================================================
// EDIT PRODUCT
// ============================================================

function editProduct(productId) {
    const p = (window.shopProducts || []).find(x => x.id === productId);
    if (!p) return;

    setValue('editProductId',   productId);
    setValue('editProductName', p.name);
    setValue('editProductSku',  p.sku);
    setValue('editBrand',       p.brand || '');
    setValue('editPrice',       p.price);
    setValue('editComparePrice',p.compare_price || '');
    setValue('editStock',       p.stock_quantity);
    setValue('editMinStock',    p.min_stock_level || 10);
    setValue('editStatus',      p.status);
    setValue('editDescription', p.description || '');

    const catSel = document.getElementById('editCategory');
    if (catSel) catSel.value = String(p.category_id);

    // Show existing images
    const imgWrap = document.getElementById('editCurrentImages');
    const images  = p.images || [];
    if (imgWrap) {
        if (images.length > 0) {
            imgWrap.innerHTML = images.map(src => `
                <div class="sp-existing-img">
                    <img src="${esc(src)}"
                         onerror="this.src='/public/assets/images/placeholder-product.svg'">
                </div>`).join('');
            imgWrap.style.display = 'flex';
        } else {
            imgWrap.innerHTML    = '';
            imgWrap.style.display = 'none';
        }
    }

    // Clear new-image preview
    const newPrev = document.getElementById('imagePreviewEdit');
    if (newPrev) newPrev.innerHTML = '';
    const fileInput = document.getElementById('editProductImages');
    if (fileInput) fileInput.value = '';

    openModal('editProductModal');
}

// ============================================================
// DELETE PRODUCT
// ============================================================

function deleteProduct(productId, productName) {
    setValue('deleteProductId', productId);
    const nameEl = document.getElementById('deleteProductName');
    if (nameEl) nameEl.textContent = productName;
    openModal('deleteProductModal');
}

// ============================================================
// MODAL HELPERS
// ============================================================

function openModal(id) {
    document.getElementById(id)?.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id)?.classList.remove('active');
    document.body.style.overflow = '';
}

function openAddProductModal() {
    document.getElementById('addProductForm')?.reset();
    const p = document.getElementById('imagePreviewAdd');
    if (p) p.innerHTML = '';
    openModal('addProductModal');
}
function closeAddProductModal()  { closeModal('addProductModal');  }
function closeEditProductModal() { closeModal('editProductModal'); }
function closeDeleteProductModal(){ closeModal('deleteProductModal'); }

// Close modal on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        ['addProductModal','editProductModal','deleteProductModal'].forEach(closeModal);
    }
});

// ============================================================
// IMAGE PREVIEW
// ============================================================

function previewImages(input, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.innerHTML = '';

    const files    = input.files;
    if (!files || files.length === 0) return;
    const maxFiles = Math.min(files.length, 5);

    for (let i = 0; i < maxFiles; i++) {
        const file = files[i];
        if (!file.type.match('image.*')) continue;
        if (file.size > 5 * 1024 * 1024) {
            alert(`"${file.name}" is too large (max 5 MB).`);
            continue;
        }
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'sp-img-preview-item';
            div.innerHTML = `
                <img src="${e.target.result}">
                <div class="sp-img-preview-overlay">
                    <span>${esc(file.name)}</span>
                    <span>${fmtBytes(file.size)}</span>
                </div>`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    }
}

function fmtBytes(b) {
    if (b < 1024)     return b + ' B';
    if (b < 1048576)  return (b / 1024).toFixed(1) + ' KB';
    return (b / 1048576).toFixed(1) + ' MB';
}

// ============================================================
// SIDEBAR TOGGLE
// ============================================================

function toggleSidebar() {
    document.getElementById('dashboardSidebar')?.classList.toggle('collapsed');
}

// ============================================================
// AUTO-HIDE ALERTS
// ============================================================

function autoHideAlerts() {
    document.querySelectorAll('.sp-alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity    = '0';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });
}

// ============================================================
// UTILITY
// ============================================================

function getFirstImage(p) {
    const imgs = p.images || [];
    return imgs.length > 0 ? imgs[0] : '/public/assets/images/placeholder-product.svg';
}

function getStockClass(p) {
    if (p.stock_quantity === 0)                                    return 'out-of-stock';
    if (p.stock_quantity <= (p.min_stock_level || 10))             return 'low-stock';
    return 'in-stock';
}

function getDiscount(p) {
    if (p.compare_price && p.compare_price > p.price) {
        return Math.round((1 - p.price / p.compare_price) * 100);
    }
    return 0;
}

function getTimeAgo(dateStr) {
    if (!dateStr) return '—';
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff < 60)     return 'Just now';
    if (diff < 3600)   return Math.floor(diff / 60)    + 'm ago';
    if (diff < 86400)  return Math.floor(diff / 3600)  + 'h ago';
    if (diff < 172800) return 'Yesterday';
    if (diff < 2592000)return Math.floor(diff / 86400) + 'd ago';
    return Math.floor(diff / 2592000) + 'mo ago';
}

function fmt(n) {
    return parseFloat(n || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });
}

function capFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
}

function esc(str) {
    const d = document.createElement('div');
    d.textContent = String(str || '');
    return d.innerHTML;
}

function setValue(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}

// ============================================================
// BULK ACTIONS
// ============================================================

function getSelectedIds() {
    return [...document.querySelectorAll('.sp-product-checkbox:checked')]
        .map(cb => parseInt(cb.value))
        .filter(Boolean);
}

function updateBulkBar() {
    const count  = document.querySelectorAll('.sp-product-checkbox:checked').length;
    const bar    = document.getElementById('spBulkBar');
    const badge  = document.getElementById('spBulkCount');
    if (bar)   bar.classList.toggle('active', count > 0);
    if (badge) badge.textContent = count;
}

function clearBulkSelection() {
    document.querySelectorAll('.sp-product-checkbox').forEach(cb => { cb.checked = false; });
    const sa = document.getElementById('selectAll');
    if (sa) { sa.checked = false; sa.indeterminate = false; }
    updateBulkBar();
}

async function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    if (!confirm(`Permanently delete ${ids.length} product(s)? This cannot be undone.`)) return;

    try {
        const res  = await fetch('/shop-owner/products/bulk-delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_ids: ids }),
        });
        const data = await res.json();
        if (data.success) {
            window.shopProducts = (window.shopProducts || []).filter(p => !ids.includes(p.id));
            applyFilters();
            clearBulkSelection();
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Delete failed', 'error');
        }
    } catch {
        showToast('Network error — please try again', 'error');
    }
}

async function bulkSetStatus(status) {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    const label = status === 'active' ? 'Activate' : 'Deactivate';
    if (!confirm(`${label} ${ids.length} selected product(s)?`)) return;

    try {
        const res  = await fetch('/shop-owner/products/bulk-status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_ids: ids, status }),
        });
        const data = await res.json();
        if (data.success) {
            const now = new Date().toISOString();
            window.shopProducts = (window.shopProducts || []).map(p =>
                ids.includes(p.id) ? { ...p, status, updated_at: now } : p
            );
            applyFilters();
            clearBulkSelection();
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Update failed', 'error');
        }
    } catch {
        showToast('Network error — please try again', 'error');
    }
}

function bulkExportCsv() {
    const ids      = getSelectedIds();
    const source   = ids.length > 0
        ? (window.shopProducts || []).filter(p => ids.includes(p.id))
        : filteredProducts;

    if (source.length === 0) { showToast('No products to export', 'error'); return; }

    const headers = ['ID','Name','SKU','Brand','Category','Price (LKR)','Compare Price','Stock','Min Stock','Status','Rating','Reviews'];
    const rows    = source.map(p => [
        p.id, p.name, p.sku, p.brand || '',
        p.category_name, p.price, p.compare_price || '',
        p.stock_quantity, p.min_stock_level, p.status,
        parseFloat(p.rating || 0).toFixed(1), p.total_reviews || 0,
    ]);

    const csv  = [headers, ...rows]
        .map(row => row.map(c => `"${String(c).replace(/"/g, '""')}"`).join(','))
        .join('\n');

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `products-${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);

    const scope = ids.length > 0 ? `${ids.length} selected` : `${source.length} filtered`;
    showToast(`Exported ${scope} product(s)`, 'success');
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `sp-toast sp-toast-${type}`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 380);
    }, 3500);
}

// Wire bulk-bar updates into checkbox state changes using event delegation
document.addEventListener('change', e => {
    if (e.target.matches('.sp-product-checkbox, #selectAll')) {
        updateBulkBar();
    }
});

// ============================================================
// GLOBAL EXPORTS
// ============================================================

Object.assign(window, {
    applyFilters, clearFilters, clearSearch,
    setView, goToPage,
    editProduct, deleteProduct,
    openAddProductModal, closeAddProductModal,
    closeEditProductModal, closeDeleteProductModal,
    previewImages, toggleSidebar,
    clearBulkSelection, bulkDelete, bulkSetStatus, bulkExportCsv,
});
