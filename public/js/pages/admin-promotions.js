/**
 * Admin Promotions/Banners Management
 */
document.addEventListener('DOMContentLoaded', () => {
    loadPromotions();
    setupPreview();
});

let allPromotions = [];

async function loadPromotions(status = 'all') {
    try {
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/promotions?status=${status}`);
        const result = await res.json();
        if (result.success) {
            allPromotions = result.data;
            renderTable(result.data);
            updateStats(result.stats);
        }
    } catch (err) {
        console.error('Load error:', err);
    }
}

function renderTable(promotions) {
    const tbody = document.getElementById('promotionsBody');
    if (!promotions.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="loading-cell">No promotions found</td></tr>';
        return;
    }

    tbody.innerHTML = promotions.map(p => {
        const posClass = `badge-${p.position}`;
        const statusClass = p.is_active == 1 ? 'badge-active' : 'badge-inactive';
        const schedule = p.starts_at ? `${formatDate(p.starts_at)} - ${p.ends_at ? formatDate(p.ends_at) : 'No end'}` : 'Always';

        return `<tr>
            <td><div class="color-dot" style="background: ${esc(p.bg_color || '#3b82f6')};"></div></td>
            <td>
                <strong>${esc(p.title)}</strong>
                ${p.subtitle ? `<br><small style="color:#64748b">${esc(p.subtitle).substring(0, 50)}...</small>` : ''}
            </td>
            <td><span class="badge ${posClass}">${esc(p.position)}</span></td>
            <td>${p.priority}</td>
            <td><span class="badge ${statusClass}">${p.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
            <td><small style="color:#64748b">${schedule}</small></td>
            <td>
                <div class="actions-cell">
                    <button class="btn btn-secondary btn-sm" onclick="openEditModal(${p.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm ${p.is_active == 1 ? 'btn-warning' : 'btn-success'}" onclick="toggleStatus(${p.id}, ${p.is_active == 1 ? 0 : 1})">
                        <i class="fas fa-${p.is_active == 1 ? 'eye-slash' : 'eye'}"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deletePromotion(${p.id})"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');
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

function openCreateModal() {
    document.getElementById('promoModalTitle').textContent = 'New Promotion';
    document.getElementById('promoId').value = '';
    document.getElementById('promoForm').reset();
    document.getElementById('promoBgColor').value = '#3b82f6';
    document.getElementById('promoTextColor').value = '#ffffff';
    document.getElementById('bgColorLabel').textContent = '#3b82f6';
    document.getElementById('textColorLabel').textContent = '#ffffff';
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
    document.getElementById('promoStartsAt').value = p.starts_at ? p.starts_at.replace(' ', 'T').substring(0, 16) : '';
    document.getElementById('promoEndsAt').value = p.ends_at ? p.ends_at.replace(' ', 'T').substring(0, 16) : '';
    updatePreview();
    document.getElementById('promoModal').classList.add('active');
}

function closeModal() {
    document.getElementById('promoModal').classList.remove('active');
}

async function handlePromoSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('promoId').value;
    const btn = document.getElementById('promoSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        if (id) {
            // Update via JSON
            const data = {
                title: document.getElementById('promoTitle').value,
                subtitle: document.getElementById('promoSubtitle').value,
                link_url: document.getElementById('promoLinkUrl').value,
                link_text: document.getElementById('promoLinkText').value,
                position: document.getElementById('promoPosition').value,
                bg_color: document.getElementById('promoBgColor').value,
                text_color: document.getElementById('promoTextColor').value,
                priority: parseInt(document.getElementById('promoPriority').value),
                starts_at: document.getElementById('promoStartsAt').value || null,
                ends_at: document.getElementById('promoEndsAt').value || null
            };
            const res = await fetch(`${window.BASE_URL || ""}/api/admin/promotions/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) { showToast('Promotion updated', 'success'); closeModal(); loadPromotions(); }
            else showToast(result.message, 'error');
        } else {
            // Create via FormData
            const form = document.getElementById('promoForm');
            const formData = new FormData(form);
            const res = await fetch((window.BASE_URL || '') + '/api/admin/promotions', { method: 'POST', body: formData });
            const result = await res.json();
            if (result.success) { showToast('Promotion created', 'success'); closeModal(); loadPromotions(); }
            else showToast(result.message, 'error');
        }
    } catch (err) {
        showToast('Server error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save';
    }
}

async function toggleStatus(id, newStatus) {
    try {
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/promotions/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_active: newStatus })
        });
        const result = await res.json();
        if (result.success) { showToast(newStatus ? 'Activated' : 'Deactivated', 'success'); loadPromotions(); }
    } catch (err) { showToast('Error', 'error'); }
}

async function deletePromotion(id) {
    if (!confirm('Delete this promotion permanently?')) return;
    try {
        const res = await fetch(`${window.BASE_URL || ""}/api/admin/promotions/${id}`, { method: 'DELETE' });
        const result = await res.json();
        if (result.success) { showToast('Deleted', 'success'); loadPromotions(); }
        else showToast(result.message, 'error');
    } catch (err) { showToast('Error', 'error'); }
}

function setupPreview() {
    ['promoTitle', 'promoSubtitle', 'promoLinkText', 'promoBgColor', 'promoTextColor'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', updatePreview);
    });
}

function updatePreview() {
    const bg = document.getElementById('promoBgColor').value;
    const text = document.getElementById('promoTextColor').value;
    const preview = document.getElementById('bannerPreview');
    preview.style.background = bg;
    preview.style.color = text;
    document.getElementById('previewTitle').textContent = document.getElementById('promoTitle').value || 'Your Title Here';
    document.getElementById('previewSubtitle').textContent = document.getElementById('promoSubtitle').value || 'Your subtitle text';
    document.getElementById('previewBtn').textContent = document.getElementById('promoLinkText').value || 'Learn More';
    document.getElementById('bgColorLabel').textContent = bg;
    document.getElementById('textColorLabel').textContent = text;
}

function formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }); }
function esc(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
function showToast(msg, type) {
    let c = document.querySelector('.toast-container');
    if (!c) { c = document.createElement('div'); c.className = 'toast-container'; document.body.appendChild(c); }
    const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle' };
    const colors = { success: '#10b981', error: '#ef4444' };
    const t = document.createElement('div'); t.className = `toast ${type}`;
    t.innerHTML = `<i class="fas ${icons[type]||icons.success}" style="color:${colors[type]}"></i><span>${esc(msg)}</span>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 4000);
}

document.getElementById('promoModal')?.addEventListener('click', e => { if (e.target.id === 'promoModal') closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
