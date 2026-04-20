/**
 * Admin Promotions/Banners Management
 * Handles CRUD operations, live preview, and status toggling
 */
const BASE = window.BASE_URL || '';
let allPromotions = [];

document.addEventListener('DOMContentLoaded', () => {
    loadPromotions();
    setupPreview();
    setupImagePreview();
});

// =====================================================================
// DATA LOADING
// =====================================================================

async function loadPromotions(status = 'all') {
    const tbody = document.getElementById('promotionsBody');
    tbody.innerHTML = '<tr><td colspan="7" class="loading-cell"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';

    try {
        const res = await fetch(`${BASE}/api/admin/promotions?status=${status}`);
        const result = await res.json();
        if (result.success) {
            allPromotions = result.data;
            renderTable(result.data);
            updateStats(result.stats);
        } else {
            tbody.innerHTML = '<tr><td colspan="7" class="loading-cell">Failed to load promotions</td></tr>';
        }
    } catch (err) {
        console.error('Load error:', err);
        tbody.innerHTML = '<tr><td colspan="7" class="loading-cell">Network error</td></tr>';
    }
}

function renderTable(promotions) {
    const tbody = document.getElementById('promotionsBody');
    if (!promotions.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="loading-cell"><i class="fas fa-bullhorn" style="font-size:2rem;opacity:0.2;display:block;margin-bottom:8px"></i>No promotions found. Click "New Promotion" to create one.</td></tr>';
        return;
    }

    tbody.innerHTML = promotions.map(p => {
        const posClass = `badge-${p.position}`;
        const statusClass = p.is_active == 1 ? 'badge-active' : 'badge-inactive';
        const schedule = p.starts_at ? `${formatDate(p.starts_at)} – ${p.ends_at ? formatDate(p.ends_at) : 'No end'}` : 'Always';

        // Show image thumbnail or color dot in preview
        let previewHtml;
        if (p.image_url_full) {
            previewHtml = `<img src="${esc(p.image_url_full)}" class="preview-thumb" alt="Banner" />`;
        } else {
            previewHtml = `<div class="color-dot" style="background: ${esc(p.bg_color || '#3b82f6')};"></div>`;
        }

        return `<tr>
            <td>${previewHtml}</td>
            <td>
                <strong>${esc(p.title)}</strong>
                ${p.subtitle ? `<br><small style="color:#64748b">${esc(p.subtitle).substring(0, 60)}${p.subtitle.length > 60 ? '…' : ''}</small>` : ''}
                ${p.link_url ? `<br><small style="color:#3b82f6"><i class="fas fa-link"></i> ${esc(p.link_url)}</small>` : ''}
            </td>
            <td><span class="badge ${posClass}">${esc(p.position)}</span></td>
            <td>${p.priority}</td>
            <td><span class="badge ${statusClass}">${p.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
            <td><small style="color:#64748b">${schedule}</small></td>
            <td>
                <div class="actions-cell">
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(${p.id})" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm ${p.is_active == 1 ? 'btn-warning' : 'btn-success'}" onclick="toggleStatus(${p.id}, ${p.is_active == 1 ? 0 : 1})" title="${p.is_active == 1 ? 'Deactivate' : 'Activate'}">
                        <i class="fas fa-${p.is_active == 1 ? 'eye-slash' : 'eye'}"></i>
                    </button>
                    <button class="btn btn-danger btn-sm promo-delete-btn" data-id="${p.id}" title="Delete"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');

    // Bind delete buttons via event delegation (avoids string escaping issues)
    tbody.querySelectorAll('.promo-delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const promo = allPromotions.find(x => x.id == id);
            confirmDelete(id, promo ? promo.title : '');
        });
    });
}

function updateStats(stats) {
    document.getElementById('statTotal').textContent = stats.total || 0;
    document.getElementById('statActive').textContent = stats.active || 0;
    document.getElementById('statInactive').textContent = stats.inactive || 0;
}

function filterPromotions(status, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    if (btn) btn.classList.add('active');
    loadPromotions(status);
}

// =====================================================================
// CREATE / EDIT MODAL
// =====================================================================

function openCreateModal() {
    document.getElementById('promoModalTitle').textContent = 'New Promotion';
    document.getElementById('promoId').value = '';
    document.getElementById('promoForm').reset();
    // Set defaults after reset
    document.getElementById('promoBgColor').value = '#3b82f6';
    document.getElementById('promoTextColor').value = '#ffffff';
    document.getElementById('promoLinkText').value = 'Learn More';
    document.getElementById('promoPriority').value = '0';
    document.getElementById('promoIsActive').checked = true;
    document.getElementById('bgColorLabel').textContent = '#3b82f6';
    document.getElementById('textColorLabel').textContent = '#ffffff';
    // Clear image preview
    document.getElementById('currentImageWrap').style.display = 'none';
    document.getElementById('currentImage').src = '';
    document.getElementById('promoImage').value = '';
    updatePreview();
    document.getElementById('promoModal').classList.add('active');
}

function openEditModal(id) {
    const p = allPromotions.find(x => x.id == id);
    if (!p) return;

    document.getElementById('promoModalTitle').textContent = 'Edit Promotion';
    document.getElementById('promoId').value = p.id;
    document.getElementById('promoTitle').value = p.title || '';
    document.getElementById('promoSubtitle').value = p.subtitle || '';
    document.getElementById('promoLinkUrl').value = p.link_url || '';
    document.getElementById('promoLinkText').value = p.link_text || 'Learn More';
    document.getElementById('promoPosition').value = p.position || 'hero';
    document.getElementById('promoBgColor').value = p.bg_color || '#3b82f6';
    document.getElementById('promoTextColor').value = p.text_color || '#ffffff';
    document.getElementById('bgColorLabel').textContent = p.bg_color || '#3b82f6';
    document.getElementById('textColorLabel').textContent = p.text_color || '#ffffff';
    document.getElementById('promoPriority').value = p.priority || 0;
    document.getElementById('promoIsActive').checked = p.is_active == 1;
    document.getElementById('promoStartsAt').value = p.starts_at ? p.starts_at.replace(' ', 'T').substring(0, 16) : '';
    document.getElementById('promoEndsAt').value = p.ends_at ? p.ends_at.replace(' ', 'T').substring(0, 16) : '';

    // Show existing image
    const imgWrap = document.getElementById('currentImageWrap');
    const imgEl = document.getElementById('currentImage');
    if (p.image_url_full) {
        imgEl.src = p.image_url_full;
        imgWrap.style.display = 'block';
    } else {
        imgEl.src = '';
        imgWrap.style.display = 'none';
    }
    document.getElementById('promoImage').value = '';

    updatePreview();
    document.getElementById('promoModal').classList.add('active');
}

function closeModal() {
    document.getElementById('promoModal').classList.remove('active');
}

// =====================================================================
// FORM SUBMISSION (Create & Edit both use FormData)
// =====================================================================

async function handlePromoSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('promoId').value;
    const btn = document.getElementById('promoSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        const formData = new FormData();
        formData.append('title', document.getElementById('promoTitle').value);
        formData.append('subtitle', document.getElementById('promoSubtitle').value);
        formData.append('link_url', document.getElementById('promoLinkUrl').value);
        formData.append('link_text', document.getElementById('promoLinkText').value);
        formData.append('position', document.getElementById('promoPosition').value);
        formData.append('bg_color', document.getElementById('promoBgColor').value);
        formData.append('text_color', document.getElementById('promoTextColor').value);
        formData.append('priority', document.getElementById('promoPriority').value);
        formData.append('is_active', document.getElementById('promoIsActive').checked ? '1' : '0');
        formData.append('starts_at', document.getElementById('promoStartsAt').value || '');
        formData.append('ends_at', document.getElementById('promoEndsAt').value || '');

        // Attach image file if selected
        const imageFile = document.getElementById('promoImage').files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        let url, method;
        if (id) {
            url = `${BASE}/api/admin/promotions/${id}`;
            method = 'POST'; // Use POST for FormData with _method override
            formData.append('_method', 'PUT');
        } else {
            url = `${BASE}/api/admin/promotions`;
            method = 'POST';
        }

        const res = await fetch(url, { method, body: formData });
        const result = await res.json();

        if (result.success) {
            showToast(id ? 'Promotion updated' : 'Promotion created', 'success');
            closeModal();
            loadPromotions();
        } else {
            showToast(result.message || 'Save failed', 'error');
        }
    } catch (err) {
        console.error('Save error:', err);
        showToast('Server error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save';
    }
}

// =====================================================================
// STATUS TOGGLE & DELETE
// =====================================================================

async function toggleStatus(id, newStatus) {
    try {
        const res = await fetch(`${BASE}/api/admin/promotions/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_active: newStatus })
        });
        const result = await res.json();
        if (result.success) {
            showToast(newStatus ? 'Promotion activated' : 'Promotion deactivated', 'success');
            loadPromotions();
        } else {
            showToast(result.message || 'Toggle failed', 'error');
        }
    } catch (err) {
        console.error('Toggle error:', err);
        showToast('Server error', 'error');
    }
}

function confirmDelete(id, title) {
    // Show custom confirm dialog
    const overlay = document.getElementById('deleteConfirmOverlay');
    document.getElementById('deletePromoName').textContent = title || 'this promotion';
    document.getElementById('confirmDeleteBtn').onclick = () => doDelete(id);
    overlay.classList.add('active');
}

function closeDeleteConfirm() {
    document.getElementById('deleteConfirmOverlay').classList.remove('active');
}

async function doDelete(id) {
    closeDeleteConfirm();
    try {
        const res = await fetch(`${BASE}/api/admin/promotions/${id}`, { method: 'DELETE' });
        const result = await res.json();
        if (result.success) {
            showToast('Promotion deleted', 'success');
            loadPromotions();
        } else {
            showToast(result.message || 'Delete failed', 'error');
        }
    } catch (err) {
        console.error('Delete error:', err);
        showToast('Server error', 'error');
    }
}

// =====================================================================
// LIVE PREVIEW
// =====================================================================

function setupPreview() {
    // Listen on both 'input' and 'change' for color pickers (browser compat)
    ['promoTitle', 'promoSubtitle', 'promoLinkText'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', updatePreview);
    });

    ['promoBgColor', 'promoTextColor'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        }
    });
}

function setupImagePreview() {
    const fileInput = document.getElementById('promoImage');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const preview = document.getElementById('bannerPreview');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.style.backgroundImage = `url(${e.target.result})`;
                    preview.style.backgroundSize = 'cover';
                    preview.style.backgroundPosition = 'center';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.style.backgroundImage = '';
            }
        });
    }
}

function updatePreview() {
    const bg = document.getElementById('promoBgColor').value;
    const text = document.getElementById('promoTextColor').value;
    const preview = document.getElementById('bannerPreview');

    // If no file selected, show solid color; if file is selected the backgroundImage is set by setupImagePreview
    if (!document.getElementById('promoImage').files?.length) {
        preview.style.backgroundImage = '';
        preview.style.background = bg;
    } else {
        // Overlay the color as a semi-transparent layer
        preview.style.backgroundColor = bg;
    }
    preview.style.color = text;

    document.getElementById('previewTitle').textContent = document.getElementById('promoTitle').value || 'Your Title Here';
    document.getElementById('previewSubtitle').textContent = document.getElementById('promoSubtitle').value || 'Your subtitle text';
    document.getElementById('previewBtn').textContent = document.getElementById('promoLinkText').value || 'Learn More';
    document.getElementById('bgColorLabel').textContent = bg;
    document.getElementById('textColorLabel').textContent = text;
}

// =====================================================================
// UTILITIES
// =====================================================================

function formatDate(d) {
    if (!d) return '';
    return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function esc(t) {
    if (!t) return '';
    const d = document.createElement('div');
    d.textContent = t;
    return d.innerHTML;
}

function showToast(msg, type) {
    let c = document.querySelector('.toast-container');
    if (!c) {
        c = document.createElement('div');
        c.className = 'toast-container';
        document.body.appendChild(c);
    }
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle' };
    const colors = { success: '#10b981', error: '#ef4444' };
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<i class="fas ${icons[type] || icons.success}" style="color:${colors[type]}"></i><span>${esc(msg)}</span>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}

// Close modals on backdrop click or Escape
document.getElementById('promoModal')?.addEventListener('click', e => { if (e.target.id === 'promoModal') closeModal(); });
document.getElementById('deleteConfirmOverlay')?.addEventListener('click', e => { if (e.target.id === 'deleteConfirmOverlay') closeDeleteConfirm(); });
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal();
        closeDeleteConfirm();
    }
});
