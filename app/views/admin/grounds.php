<?php
$_base      = defined('BASE_URL') ? BASE_URL : '';
$activePage = 'grounds';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ground Management – Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-dashboard.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',sans-serif;background:#f5f7fa;color:#2d3748}
        .admin-container{display:flex;min-height:100vh}
        .admin-main{flex:1;margin-left:260px;transition:margin-left .3s ease}
        .page-header{background:#fff;padding:20px 30px;box-shadow:0 2px 4px rgba(0,0,0,.05);position:sticky;top:0;z-index:100}
        .page-header h1{font-size:22px;font-weight:700;color:#1a202c}
        .page-header p{color:#718096;font-size:13px;margin-top:3px}
        .content{padding:28px}

        /* Stats */
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px}
        .stat-card{background:#fff;border-radius:12px;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.07);display:flex;align-items:center;gap:14px}
        .stat-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
        .si-yellow{background:linear-gradient(135deg,#f6ad55,#ed8936);color:#fff}
        .si-green{background:linear-gradient(135deg,#48bb78,#38a169);color:#fff}
        .si-blue{background:linear-gradient(135deg,#4299e1,#3182ce);color:#fff}
        .si-gray{background:linear-gradient(135deg,#a0aec0,#718096);color:#fff}
        .si-red{background:linear-gradient(135deg,#fc8181,#e53e3e);color:#fff}
        .stat-body .val{font-size:22px;font-weight:700;color:#1a202c;line-height:1}
        .stat-body .lbl{font-size:12px;color:#718096;margin-top:3px}

        /* Filters */
        .filters-card{background:#fff;border-radius:12px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,.07);margin-bottom:20px;display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end}
        .fg{display:flex;flex-direction:column;gap:5px}
        .fg label{font-size:12px;font-weight:700;color:#4a5568}
        .fg input,.fg select{padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;min-width:160px}
        .fg input:focus,.fg select:focus{outline:none;border-color:#3182ce}

        /* Table */
        .card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07)}
        .card-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9}
        .card-head h3{font-size:15px;font-weight:700;color:#1a202c;display:flex;align-items:center;gap:8px;margin:0}
        .tbl-wrap{overflow-x:auto;padding:0 20px 20px}
        table{width:100%;border-collapse:collapse;font-size:13px}
        th{text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#718096;border-bottom:2px solid #f1f5f9}
        td{padding:11px 12px;border-bottom:1px solid #f8fafc;color:#374151;vertical-align:middle}
        tr:hover td{background:#fafbfc}

        /* Badges */
        .badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
        .b-active{background:#f0fff4;color:#276749}
        .b-pending{background:#fffbeb;color:#92400e}
        .b-inactive{background:#fff5f5;color:#c53030}
        .b-maintenance{background:#ebf8ff;color:#2b6cb0}

        /* Buttons */
        .btn{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
        .btn-approve{background:#38a169;color:#fff}.btn-approve:hover{background:#276749}
        .btn-deactivate{background:#e53e3e;color:#fff}.btn-deactivate:hover{background:#c53030}
        .btn-activate{background:#3182ce;color:#fff}.btn-activate:hover{background:#2b6cb0}
        .btn-view{background:#f7fafc;color:#4a5568;border:1px solid #e2e8f0}.btn-view:hover{background:#edf2f7}
        .btn-reject{background:#fff5f5;color:#c53030;border:1px solid #feb2b2}.btn-reject:hover{background:#fed7d7}
        .btn:disabled{opacity:.5;cursor:not-allowed}
        .btn-primary{background:#3182ce;color:#fff}.btn-primary:hover{background:#2b6cb0}
        .btn-ghost{background:#f7fafc;color:#4a5568;border:1px solid #e2e8f0}.btn-ghost:hover{background:#edf2f7}

        /* Modal */
        .modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center}
        .modal-backdrop.open{display:flex}
        .modal-box{background:#fff;border-radius:14px;width:min(640px,96vw);max-height:88vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2)}
        .modal-head{padding:18px 22px;display:flex;align-items:center;justify-content:space-between}
        .modal-head h3{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px;margin:0}
        .modal-head button{background:rgba(255,255,255,.2);border:none;color:inherit;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:13px}
        .modal-body{padding:20px 22px;overflow-y:auto;flex:1;font-size:13px}
        .modal-foot{padding:14px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:8px}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px}
        .info-item label{font-size:11px;font-weight:700;text-transform:uppercase;color:#718096;display:block;margin-bottom:3px}
        .info-item span{font-size:13px;color:#1a202c;font-weight:500}
        .amenity-tag{display:inline-block;background:#ebf8ff;color:#2b6cb0;padding:2px 8px;border-radius:12px;font-size:11px;margin:2px}
        .form-group{margin-bottom:12px}
        .form-group label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px}
        .form-group textarea{width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;font-family:inherit}
        .loading{padding:40px;text-align:center;color:#a0aec0}
        .empty-state{padding:50px;text-align:center;color:#a0aec0}
        .empty-state i{font-size:40px;display:block;margin-bottom:12px}
        .ground-img{width:48px;height:36px;border-radius:6px;object-fit:cover;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#a0aec0;font-size:16px}
        @media(max-width:768px){.admin-main{margin-left:0}.stats-grid{grid-template-columns:repeat(2,1fr)}}
    </style>
</head>
<body>
<div class="admin-container">
    <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>
    <main class="admin-main">

        <div class="page-header">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <h1><i class="fas fa-map-marker-alt" style="color:#3182ce"></i> Ground Management</h1>
                    <p>Review, approve and manage sports grounds submitted by ground owners</p>
                </div>
                <button class="btn btn-ghost" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <div class="content">

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon si-blue"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="stat-body"><div class="val" id="stTotal">—</div><div class="lbl">Total Grounds</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-yellow"><i class="fas fa-hourglass-half"></i></div>
                    <div class="stat-body"><div class="val" id="stPending">—</div><div class="lbl">Pending Review</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-body"><div class="val" id="stActive">—</div><div class="lbl">Active / Live</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-red"><i class="fas fa-ban"></i></div>
                    <div class="stat-body"><div class="val" id="stInactive">—</div><div class="lbl">Inactive</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-gray"><i class="fas fa-tools"></i></div>
                    <div class="stat-body"><div class="val" id="stMaintenance">—</div><div class="lbl">Maintenance</div></div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-card">
                <div class="fg">
                    <label>Search</label>
                    <input type="text" id="fSearch" placeholder="Ground name, city, owner…">
                </div>
                <div class="fg">
                    <label>Status</label>
                    <select id="fStatus">
                        <option value="">All Status</option>
                        <option value="pending_review">Pending Review</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="fg" style="flex-direction:row;gap:8px;align-self:flex-end">
                    <button class="btn btn-primary" onclick="loadGrounds()"><i class="fas fa-search"></i> Apply</button>
                    <button class="btn btn-ghost" onclick="clearFilters()"><i class="fas fa-times"></i> Clear</button>
                </div>
            </div>

            <!-- Grounds Table -->
            <div class="card">
                <div class="card-head">
                    <h3><i class="fas fa-list-alt" style="color:#3182ce"></i> All Grounds</h3>
                    <button class="btn btn-ghost" onclick="loadGrounds()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
                <div class="tbl-wrap">
                    <div id="tblLoading" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                    <table id="tbl" style="display:none">
                        <thead><tr>
                            <th>Ground</th><th>Owner</th><th>City</th><th>Category</th>
                            <th>Rate/hr</th><th>Status</th><th>Submitted</th><th>Actions</th>
                        </tr></thead>
                        <tbody id="tblBody"></tbody>
                    </table>
                    <div id="tblEmpty" class="empty-state" style="display:none">
                        <i class="fas fa-map-marker-alt"></i>No grounds found.
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Ground Detail Modal -->
<div class="modal-backdrop" id="detailBackdrop">
    <div class="modal-box">
        <div class="modal-head" style="background:linear-gradient(135deg,#1e40af,#2563eb);color:#fff">
            <h3><i class="fas fa-map-marker-alt"></i> <span id="detailTitle">Ground Details</span></h3>
            <button onclick="closeDetail()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="detailBody"></div>
        <div class="modal-foot" id="detailFoot"></div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal-backdrop" id="rejectBackdrop">
    <div class="modal-box" style="width:min(420px,96vw)">
        <div class="modal-head" style="background:linear-gradient(135deg,#c53030,#e53e3e);color:#fff">
            <h3><i class="fas fa-times-circle"></i> Reject Ground</h3>
            <button onclick="closeReject()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="margin-bottom:14px;color:#4a5568">Provide a reason so the ground owner knows what to fix:</p>
            <div class="form-group">
                <label>Reason <span style="color:#e53e3e">*</span></label>
                <textarea id="rejectReason" rows="3" placeholder="e.g. Missing facility images, incomplete address…"></textarea>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-ghost" onclick="closeReject()">Cancel</button>
            <button class="btn btn-deactivate" id="rejectConfirmBtn" onclick="confirmReject()">
                <i class="fas fa-times-circle"></i> Reject
            </button>
        </div>
    </div>
</div>

<script>
(function(){
'use strict';
const BASE = window.BASE_URL || '';
let pendingRejectId = null;

function lkr(n){ return 'Rs. '+(parseFloat(n)||0).toLocaleString('en-LK',{minimumFractionDigits:0}); }
function fmtDate(d){ return d ? new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '—'; }

const statusBadge = s => ({
    active:         '<span class="badge b-active"><i class="fas fa-check-circle"></i> Active</span>',
    pending_review: '<span class="badge b-pending"><i class="fas fa-hourglass-half"></i> Pending Review</span>',
    inactive:       '<span class="badge b-inactive"><i class="fas fa-ban"></i> Inactive</span>',
    maintenance:    '<span class="badge b-maintenance"><i class="fas fa-tools"></i> Maintenance</span>',
})[s] || `<span class="badge">${s}</span>`;

// ── Load grounds ───────────────────────────────────
async function loadGrounds(){
    document.getElementById('tblLoading').style.display = 'block';
    document.getElementById('tbl').style.display = 'none';
    document.getElementById('tblEmpty').style.display = 'none';
    const status = document.getElementById('fStatus').value;
    const search = document.getElementById('fSearch').value.trim();
    try{
        const r = await fetch(`${BASE}/api/admin/grounds?status=${encodeURIComponent(status)}&search=${encodeURIComponent(search)}`);
        const d = await r.json();
        document.getElementById('tblLoading').style.display = 'none';
        if(d.stats){
            document.getElementById('stTotal').textContent       = d.stats.total || 0;
            document.getElementById('stPending').textContent     = d.stats.pending || 0;
            document.getElementById('stActive').textContent      = d.stats.active || 0;
            document.getElementById('stInactive').textContent    = d.stats.inactive || 0;
            document.getElementById('stMaintenance').textContent = d.stats.maintenance || 0;
        }
        const rows = d.grounds || [];
        if(!rows.length){ document.getElementById('tblEmpty').style.display='block'; return; }
        document.getElementById('tbl').style.display = 'table';
        document.getElementById('tblBody').innerHTML = rows.map(g => {
            const actions = buildActions(g);
            return `<tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="ground-img"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <strong style="display:block">${esc(g.name)}</strong>
                            <span style="font-size:11px;color:#718096">#${g.id}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <strong>${esc(g.owner_name||'—')}</strong>
                    <br><span style="font-size:11px;color:#718096">${esc(g.owner_email||'')}</span>
                </td>
                <td>${esc(g.city||'—')}</td>
                <td>${esc(g.category_name||'—')}</td>
                <td style="font-weight:600">${lkr(g.hourly_rate)}</td>
                <td>${statusBadge(g.status)}</td>
                <td style="font-size:12px;color:#718096">${fmtDate(g.created_at)}</td>
                <td><div style="display:flex;gap:6px;flex-wrap:wrap">${actions}</div></td>
            </tr>`;
        }).join('');
    }catch(e){ console.error(e); document.getElementById('tblLoading').innerHTML='<i class="fas fa-exclamation-circle"></i> Failed to load'; }
}
window.loadGrounds = loadGrounds;

function buildActions(g){
    let html = `<button class="btn btn-view" onclick="viewGround(${g.id})"><i class="fas fa-eye"></i></button>`;
    if(g.status === 'pending_review'){
        html += `<button class="btn btn-approve" onclick="approveGround(${g.id},this)"><i class="fas fa-check"></i> Approve</button>`;
        html += `<button class="btn btn-reject" onclick="openReject(${g.id})"><i class="fas fa-times"></i> Reject</button>`;
    } else if(g.status === 'active'){
        html += `<button class="btn btn-deactivate" onclick="deactivateGround(${g.id},this)"><i class="fas fa-ban"></i> Deactivate</button>`;
    } else if(g.status === 'inactive'){
        html += `<button class="btn btn-activate" onclick="activateGround(${g.id},this)"><i class="fas fa-check"></i> Activate</button>`;
    }
    return html;
}

function clearFilters(){
    document.getElementById('fSearch').value = '';
    document.getElementById('fStatus').value = '';
    loadGrounds();
}
window.clearFilters = clearFilters;

// ── Actions ────────────────────────────────────────
window.approveGround = async function(id, btn){
    if(!confirm('Approve this ground? It will go live immediately.')) return;
    btn.disabled = true;
    try{
        const r = await fetch(`${BASE}/api/admin/grounds/${id}/approve`,{method:'PUT'});
        const d = await r.json();
        if(d.success) loadGrounds(); else alert(d.message||'Failed');
    }catch(e){ alert('Network error'); }
    btn.disabled = false;
};

window.deactivateGround = async function(id, btn){
    if(!confirm('Deactivate this ground? It will be hidden from users.')) return;
    btn.disabled = true;
    try{
        const r = await fetch(`${BASE}/api/admin/grounds/${id}/deactivate`,{method:'PUT'});
        const d = await r.json();
        if(d.success) loadGrounds(); else alert(d.message||'Failed');
    }catch(e){ alert('Network error'); }
    btn.disabled = false;
};

window.activateGround = async function(id, btn){
    if(!confirm('Re-activate this ground?')) return;
    btn.disabled = true;
    try{
        const r = await fetch(`${BASE}/api/admin/grounds/${id}/approve`,{method:'PUT'});
        const d = await r.json();
        if(d.success) loadGrounds(); else alert(d.message||'Failed');
    }catch(e){ alert('Network error'); }
    btn.disabled = false;
};

// ── Reject modal ───────────────────────────────────
window.openReject = function(id){
    pendingRejectId = id;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectBackdrop').classList.add('open');
};
window.closeReject = function(){ document.getElementById('rejectBackdrop').classList.remove('open'); pendingRejectId=null; };

window.confirmReject = async function(){
    const reason = document.getElementById('rejectReason').value.trim();
    if(!reason){ alert('Please provide a reason.'); return; }
    const btn = document.getElementById('rejectConfirmBtn');
    btn.disabled = true;
    try{
        const r = await fetch(`${BASE}/api/admin/grounds/${pendingRejectId}/reject`,{
            method:'PUT', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({reason})
        });
        const d = await r.json();
        if(d.success){ closeReject(); loadGrounds(); } else alert(d.message||'Failed');
    }catch(e){ alert('Network error'); }
    btn.disabled = false;
};

// ── Detail modal ───────────────────────────────────
window.viewGround = async function(id){
    document.getElementById('detailBody').innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i></div>';
    document.getElementById('detailFoot').innerHTML = '';
    document.getElementById('detailBackdrop').classList.add('open');
    try{
        const r = await fetch(`${BASE}/api/admin/grounds/${id}`);
        const d = await r.json();
        if(!d.success){ document.getElementById('detailBody').innerHTML='<p>Failed to load.</p>'; return; }
        const g = d.ground;
        document.getElementById('detailTitle').textContent = g.name;
        const amenities = (Array.isArray(g.amenities)?g.amenities:(g.amenities?JSON.parse(g.amenities):[]));
        document.getElementById('detailBody').innerHTML = `
            <div class="info-grid">
                <div class="info-item"><label>Ground Name</label><span>${esc(g.name)}</span></div>
                <div class="info-item"><label>Status</label><span>${statusBadge(g.status)}</span></div>
                <div class="info-item"><label>Owner</label><span>${esc(g.owner_name||'—')}</span></div>
                <div class="info-item"><label>Category</label><span>${esc(g.category_name||'—')}</span></div>
                <div class="info-item"><label>City</label><span>${esc(g.city||'—')}</span></div>
                <div class="info-item"><label>Hourly Rate</label><span style="font-weight:700;color:#2b6cb0">${lkr(g.hourly_rate)}</span></div>
                <div class="info-item"><label>Capacity</label><span>${g.capacity ? g.capacity+' people' : '—'}</span></div>
                <div class="info-item"><label>Submitted</label><span>${fmtDate(g.created_at)}</span></div>
            </div>
            <div class="info-item" style="margin-bottom:12px">
                <label>Address</label>
                <span>${esc(g.address||'—')}${g.state?', '+esc(g.state):''}${g.postal_code?', '+esc(g.postal_code):''}</span>
            </div>
            ${g.description ? `<div class="info-item" style="margin-bottom:12px"><label>Description</label><p style="color:#4a5568;line-height:1.6">${esc(g.description)}</p></div>` : ''}
            ${amenities.length ? `<div class="info-item" style="margin-bottom:12px"><label>Amenities</label><div>${amenities.map(a=>'<span class="amenity-tag">'+esc(a)+'</span>').join('')}</div></div>` : ''}
            ${g.rules ? `<div class="info-item"><label>Rules</label><p style="color:#4a5568;line-height:1.6;font-size:12px">${esc(g.rules)}</p></div>` : ''}
        `;
        let footHtml = '<button class="btn btn-ghost" onclick="closeDetail()">Close</button>';
        if(g.status === 'pending_review'){
            footHtml += `<button class="btn btn-reject" onclick="closeDetail();openReject(${g.id})"><i class="fas fa-times"></i> Reject</button>`;
            footHtml += `<button class="btn btn-approve" onclick="closeDetail();approveGround(${g.id},this)"><i class="fas fa-check"></i> Approve</button>`;
        } else if(g.status === 'active'){
            footHtml += `<button class="btn btn-deactivate" onclick="closeDetail();deactivateGround(${g.id},this)"><i class="fas fa-ban"></i> Deactivate</button>`;
        } else if(g.status === 'inactive'){
            footHtml += `<button class="btn btn-activate" onclick="closeDetail();activateGround(${g.id},this)"><i class="fas fa-check"></i> Activate</button>`;
        }
        document.getElementById('detailFoot').innerHTML = footHtml;
    }catch(e){ console.error(e); }
};
window.closeDetail = function(){ document.getElementById('detailBackdrop').classList.remove('open'); };

function esc(s){ const d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }

// Init
loadGrounds();

// Auto-filter on status change
document.getElementById('fStatus').addEventListener('change', loadGrounds);

})();
</script>
</body>
</html>
