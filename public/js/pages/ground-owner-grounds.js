/* 
   Ground Owner – My Grounds JS  (IIFE, gr- prefix)
 */
(function () {
'use strict';

/*  state  */
let allGrounds     = [];
let deleteTargetId = null;

/*  toast  */
function toast(msg, type = 'info') {
    let wrap = document.getElementById('grToastWrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id        = 'grToastWrap';
        wrap.className = 'gr-toast-wrap';
        document.body.appendChild(wrap);
    }
    const icons = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle' };
    const t = document.createElement('div');
    t.className = `gr-toast ${type}`;
    t.innerHTML = `<i class="fas fa-${icons[type] || 'info-circle'}"></i> ${msg}`;
    wrap.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 320); }, 3500);
}

/*  modal helpers  */
function openModal(id, bd) {
    document.getElementById(bd).classList.add('open');
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(id, bd) {
    document.getElementById(bd).classList.remove('open');
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

/*  sport → photo path  */
const _base = window.BASE_URL || '';
const GROUND_IMAGES = {
    football:   _base + '/public/assets/images/grounds/football-ground.jpg',
    soccer:     _base + '/public/assets/images/grounds/football-ground.jpg',
    cricket:    _base + '/public/assets/images/grounds/cricket-ground.jpg',
    tennis:     _base + '/public/assets/images/grounds/tennis-court.jpg',
    basketball: _base + '/public/assets/images/grounds/basketball-court.jpg',
    swimming:   _base + '/public/assets/images/grounds/swimming-pool.jpg',
    badminton:  _base + '/public/assets/images/grounds/badminton-court.jpg',
    volleyball: _base + '/public/assets/images/grounds/volleyball-court.jpg',
};

function groundImage(cat) {
    const n = (cat || '').toLowerCase();
    for (const [key, path] of Object.entries(GROUND_IMAGES)) {
        if (n.includes(key)) return path;
    }
    return null; // fall back to gradient
}

/*  sport → cover class (fallback when no photo)  */
function coverClass(cat) {
    const n = (cat || '').toLowerCase();
    if (n.includes('football') || n.includes('soccer')) return 'gr-cover-football';
    if (n.includes('cricket'))    return 'gr-cover-cricket';
    if (n.includes('tennis'))     return 'gr-cover-tennis';
    if (n.includes('basketball')) return 'gr-cover-basketball';
    if (n.includes('swimming'))   return 'gr-cover-swimming';
    if (n.includes('badminton'))  return 'gr-cover-badminton';
    if (n.includes('volleyball')) return 'gr-cover-volleyball';
    return 'gr-cover-default';
}

/*  amenity lookup  */
const amenityLabels = {
    floodlights:       'Floodlights',
    changing_rooms:    'Changing Rooms',
    parking:           'Parking',
    refreshments:      'Refreshments',
    security:          'Security',
    first_aid:         'First Aid',
    equipment_rental:  'Equipment',
    shower:            'Showers',
    wifi:              'WiFi',
    cctv:              'CCTV',
    cafeteria:         'Cafeteria',
    wheelchair_access: 'Wheelchair',
};

function getAmenities(g) {
    if (Array.isArray(g.amenities)) return g.amenities;
    if (typeof g.amenities === 'string' && g.amenities) {
        try { return JSON.parse(g.amenities); } catch (_) {}
    }
    return [];
}

/*  load sports categories  */
async function loadCategories() {
    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/categories');
        const data = await res.json();
        const formSel   = document.getElementById('grCategory');
        const filterSel = document.getElementById('filterSport');
        (data.categories || []).forEach(c => {
            const o = document.createElement('option');
            o.value       = c.id;
            o.textContent = c.name;
            formSel.appendChild(o.cloneNode(true));
            filterSel.appendChild(o);
        });
    } catch (e) { console.error('Category load error:', e); }
}

/*  load all grounds  */
async function loadGrounds() {
    document.getElementById('grLoading').style.display = 'block';
    document.getElementById('grGrid').style.display    = 'none';
    document.getElementById('grEmpty').style.display   = 'none';

    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/grounds');
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Load failed');
        allGrounds = data.grounds || [];
        updateStats();
        applyFilters();
    } catch (e) {
        console.error('Load error:', e);
        document.getElementById('grLoading').innerHTML =
            '<i class="fas fa-exclamation-circle" style="color:#dc2626"></i> Failed to load grounds. Please refresh.';
    }
}

/*  update stat cards  */
function updateStats() {
    const counts = { active: 0, maintenance: 0, inactive: 0 };
    allGrounds.forEach(g => {
        const s = g.status || 'active'; // treat null/undefined as active (matches card rendering)
        if (s in counts) counts[s]++;
    });
    document.getElementById('statTotal').textContent       = allGrounds.length;
    document.getElementById('statActive').textContent      = counts.active;
    document.getElementById('statMaintenance').textContent = counts.maintenance;
    document.getElementById('statInactive').textContent    = counts.inactive;
}

/*  filter & render  */
function applyFilters() {
    const search = document.getElementById('filterSearch').value.trim().toLowerCase();
    const sport  = document.getElementById('filterSport').value;
    const status = document.getElementById('filterStatus').value;

    const list = allGrounds.filter(g => {
        if (search && !(g.name || '').toLowerCase().includes(search) &&
            !(g.city || '').toLowerCase().includes(search)) return false;
        if (sport  && String(g.sport_category_id) !== String(sport))  return false;
        if (status && (g.status || 'active') !== status) return false;
        return true;
    });

    renderGrid(list);
}

/*  render card grid  */
function renderGrid(list) {
    const loading = document.getElementById('grLoading');
    const empty   = document.getElementById('grEmpty');
    const grid    = document.getElementById('grGrid');

    loading.style.display = 'none';

    if (!list.length) {
        empty.style.display = 'block';
        grid.style.display  = 'none';
        return;
    }

    empty.style.display = 'none';
    grid.style.display  = 'grid';

    grid.innerHTML = list.map(g => {
        const amenities = getAmenities(g);
        const chips     = amenities.slice(0, 3).map(a =>
            `<span class="gr-chip">${amenityLabels[a] || a}</span>`).join('');
        const more = amenities.length > 3
            ? `<span class="gr-chip gr-chip-more">+${amenities.length - 3} more</span>`
            : '';

        const status   = g.status || 'active';
        const statusLbl= status.charAt(0).toUpperCase() + status.slice(1);
        const rating   = parseFloat(g.rating) || 0;
        const ratingHtml = rating > 0
            ? `<span><i class="fas fa-star" style="color:#f59e0b;font-size:11px"></i> ${rating.toFixed(1)}</span>`
            : '';

        const safeName = (g.name || '').replace(/\\/g,'\\\\').replace(/'/g,"\\'");
        const imgSrc   = groundImage(g.category_name);

        return `
        <div class="gr-card">
            <div class="gr-card-cover ${coverClass(g.category_name)}">
                ${imgSrc ? `<img src="${imgSrc}" alt="${g.category_name || 'Ground'}" loading="lazy">` : ''}
                <span class="gr-status-badge ${status}">${statusLbl}</span>
                ${g.category_name
                    ? `<span class="gr-cat-pill"><i class="fas fa-tag"></i> ${g.category_name}</span>`
                    : ''}
            </div>
            <div class="gr-card-body">
                <div class="gr-card-name" title="${g.name || ''}">${g.name || '—'}</div>
                <div class="gr-card-meta">
                    ${g.city ? `<span><i class="fas fa-map-marker-alt"></i> ${g.city}</span>` : ''}
                    ${g.capacity ? `<span><i class="fas fa-users"></i> ${g.capacity}</span>` : ''}
                    ${ratingHtml}
                </div>
                <div class="gr-card-rate">
                    LKR ${(parseFloat(g.hourly_rate) || 0).toLocaleString('en-LK')}
                    <span>/ hour</span>
                </div>
                <div class="gr-chip-row">${chips}${more}</div>
            </div>
            <div class="gr-card-footer">
                <button class="gr-act gr-act-edit" onclick="editGround(${g.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="gr-act gr-act-view" onclick="window.open((window.BASE_URL||'')+'/ground-details?id=${g.id}','_blank')">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="gr-act gr-act-delete" title="Remove" onclick="openDeleteModal(${g.id},'${safeName}')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>`;
    }).join('');
}

/*  open add modal  */
function openAddModal() {
    document.getElementById('grForm').reset();
    document.getElementById('grId').value = '';
    document.getElementById('grModalTitle').innerHTML =
        '<i class="fas fa-plus-circle"></i> Add New Ground';
    document.getElementById('grSaveBtn').innerHTML =
        '<i class="fas fa-save"></i> Add Ground';
    resetAmenities([]);
    openModal('grModal', 'grBackdrop');
}

/*  edit ground  */
window.editGround = async function (id) {
    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/ground-owner/grounds/${id}`);
        const data = await res.json();
        if (!data.success) { toast('Failed to load ground details', 'error'); return; }

        const g = data.ground;
        document.getElementById('grId').value       = g.id;
        document.getElementById('grName').value     = g.name || '';
        document.getElementById('grCategory').value = g.sport_category_id || '';
        document.getElementById('grDesc').value     = g.description || '';
        document.getElementById('grStatus').value   = g.status || 'active';
        document.getElementById('grAddress').value  = g.address || '';
        document.getElementById('grCity').value     = g.city || '';
        document.getElementById('grState').value    = g.state || '';
        document.getElementById('grPostal').value   = g.postal_code || '';
        document.getElementById('grRate').value     = g.hourly_rate || '';
        document.getElementById('grCapacity').value = g.capacity || '';
        document.getElementById('grRules').value    = g.rules || '';

        resetAmenities(getAmenities(g));

        document.getElementById('grModalTitle').innerHTML =
            '<i class="fas fa-edit"></i> Edit Ground';
        document.getElementById('grSaveBtn').innerHTML =
            '<i class="fas fa-save"></i> Save Changes';

        openModal('grModal', 'grBackdrop');
    } catch (e) {
        toast('Error loading ground', 'error');
    }
};

/*  amenity toggle reset  */
function resetAmenities(checked) {
    document.querySelectorAll('.gr-amenity-item').forEach(el => {
        const val = el.querySelector('input').value;
        el.classList.toggle('checked', checked.includes(val));
    });
}

/*  save form  */
document.getElementById('grSaveBtn').addEventListener('click', async () => {
    const id       = document.getElementById('grId').value;
    const name     = document.getElementById('grName').value.trim();
    const catId    = document.getElementById('grCategory').value;
    const address  = document.getElementById('grAddress').value.trim();
    const city     = document.getElementById('grCity').value.trim();
    const rate     = document.getElementById('grRate').value;

    if (!name || !catId || !address || !city || !rate) {
        toast('Please fill in all required fields', 'error');
        return;
    }

    const amenities = [];
    document.querySelectorAll('.gr-amenity-item.checked input').forEach(inp => {
        amenities.push(inp.value);
    });

    const payload = {
        name,
        sport_category_id: parseInt(catId),
        description:  document.getElementById('grDesc').value.trim()    || null,
        status:       document.getElementById('grStatus').value,
        address,
        city,
        state:        document.getElementById('grState').value.trim()   || null,
        postal_code:  document.getElementById('grPostal').value.trim()  || null,
        country:      'Sri Lanka',
        hourly_rate:  parseFloat(rate),
        capacity:     parseInt(document.getElementById('grCapacity').value) || null,
        amenities,
        rules:        document.getElementById('grRules').value.trim()   || null,
    };

    const btn = document.getElementById('grSaveBtn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    try {
        const url    = id ? `${window.BASE_URL||''}/api/ground-owner/grounds/${id}` : (window.BASE_URL||'')+'/api/ground-owner/grounds';
        const method = id ? 'PUT' : 'POST';
        const res    = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            toast(id ? 'Ground updated successfully' : 'Ground added successfully', 'success');
            closeModal('grModal', 'grBackdrop');
            await loadGrounds();
        } else {
            toast(data.message || 'Failed to save ground', 'error');
        }
    } catch (_) {
        toast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = id
            ? '<i class="fas fa-save"></i> Save Changes'
            : '<i class="fas fa-save"></i> Add Ground';
    }
});

/*  delete flow  */
window.openDeleteModal = function (id, name) {
    deleteTargetId = id;
    document.getElementById('grDelName').textContent = name;
    openModal('grDelModal', 'grDelBackdrop');
};

document.getElementById('grDelConfirm').addEventListener('click', async () => {
    if (!deleteTargetId) return;
    const btn = document.getElementById('grDelConfirm');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing…';

    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/ground-owner/grounds/${deleteTargetId}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) {
            toast('Ground removed successfully', 'success');
            closeModal('grDelModal', 'grDelBackdrop');
            deleteTargetId = null;
            await loadGrounds();
        } else {
            toast(data.message || 'Failed to remove ground', 'error');
        }
    } catch (_) {
        toast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-trash-alt"></i> Remove';
    }
});

/*  amenity click toggle  */
document.querySelectorAll('.gr-amenity-item').forEach(el => {
    el.addEventListener('click', (e) => {
        // The label contains a hidden checkbox; clicking the label fires a synthetic
        // click on the checkbox which then bubbles back here — toggling twice = no change.
        // e.preventDefault() blocks the synthetic checkbox click; the INPUT guard
        // catches any direct bubbled events before preventDefault takes effect.
        if (e.target.tagName === 'INPUT') return;
        e.preventDefault();
        el.classList.toggle('checked');
    });
});

/*  sidebar toggle  */
window.toggleSidebar = function () {
    document.getElementById('dashboardSidebar')?.classList.toggle('collapsed');
};

/*  wire up events  */
document.getElementById('addGroundBtn').addEventListener('click', openAddModal);
document.getElementById('addGroundBtnEmpty').addEventListener('click', openAddModal);

document.getElementById('grCloseModal').addEventListener('click', () => closeModal('grModal',    'grBackdrop'));
document.getElementById('grCancelBtn').addEventListener('click',  () => closeModal('grModal',    'grBackdrop'));
document.getElementById('grBackdrop').addEventListener('click',   () => closeModal('grModal',    'grBackdrop'));

document.getElementById('grDelClose').addEventListener('click',   () => closeModal('grDelModal', 'grDelBackdrop'));
document.getElementById('grDelCancel').addEventListener('click',  () => closeModal('grDelModal', 'grDelBackdrop'));
document.getElementById('grDelBackdrop').addEventListener('click',() => closeModal('grDelModal', 'grDelBackdrop'));

document.getElementById('filterSearch').addEventListener('input',  applyFilters);
document.getElementById('filterSport').addEventListener('change',  applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);

/*  init  */
loadCategories();
loadGrounds();

})();
