<?php
$_base      = defined('BASE_URL') ? BASE_URL : '';
$activePage = 'commission';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commission Invoices – Admin</title>
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
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:16px;margin-bottom:24px}
        .stat-card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,.07);display:flex;align-items:center;gap:14px}
        .stat-icon{width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
        .si-blue{background:linear-gradient(135deg,#4299e1,#3182ce);color:#fff}
        .si-orange{background:linear-gradient(135deg,#ed8936,#dd6b20);color:#fff}
        .si-green{background:linear-gradient(135deg,#48bb78,#38a169);color:#fff}
        .si-red{background:linear-gradient(135deg,#f56565,#e53e3e);color:#fff}
        .si-purple{background:linear-gradient(135deg,#9f7aea,#805ad5);color:#fff}
        .stat-body .val{font-size:22px;font-weight:700;color:#1a202c;line-height:1}
        .stat-body .lbl{font-size:12px;color:#718096;margin-top:3px}

        /* Tabs */
        .card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);margin-bottom:24px}
        .card-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #f1f5f9}
        .card-head h3{font-size:15px;font-weight:700;color:#1a202c;display:flex;align-items:center;gap:8px}
        .tab-row{display:flex;gap:4px;padding:16px 22px 0}
        .tab-btn{padding:8px 18px;border:none;background:none;font-size:13px;font-weight:600;color:#718096;cursor:pointer;border-bottom:2px solid transparent;transition:all .2s}
        .tab-btn.active{color:#3182ce;border-bottom-color:#3182ce}

        /* Tables */
        .tbl-wrap{overflow-x:auto;padding:0 22px 22px}
        table{width:100%;border-collapse:collapse;font-size:13px}
        th{text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#718096;border-bottom:2px solid #f1f5f9}
        td{padding:11px 12px;border-bottom:1px solid #f8fafc;color:#374151;vertical-align:middle}
        tr:hover td{background:#fafbfc}

        /* Badges */
        .badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700}
        .badge-sent{background:#ebf8ff;color:#2b6cb0}
        .badge-paid{background:#f0fff4;color:#276749}
        .badge-overdue{background:#fff5f5;color:#c53030}

        /* Buttons */
        .btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
        .btn-primary{background:#3182ce;color:#fff}.btn-primary:hover{background:#2b6cb0}
        .btn-success{background:#38a169;color:#fff}.btn-success:hover{background:#276749}
        .btn-ghost{background:#f7fafc;color:#4a5568;border:1px solid #e2e8f0}.btn-ghost:hover{background:#edf2f7}
        .btn:disabled{opacity:.5;cursor:not-allowed}

        /* Modal */
        .modal-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center}
        .modal-backdrop.open{display:flex}
        .modal-box{background:#fff;border-radius:14px;width:min(560px,96vw);max-height:85vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2)}
        .modal-head{background:linear-gradient(135deg,#1e40af,#2563eb);padding:18px 22px;display:flex;align-items:center;justify-content:space-between}
        .modal-head h3{color:#fff;font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px}
        .modal-head button{background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:13px}
        .modal-body{padding:20px 22px;overflow-y:auto;flex:1}
        .modal-foot{padding:14px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:8px}
        .form-group{margin-bottom:14px}
        .form-group label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px}
        .form-group textarea{width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;font-family:inherit}
        .inv-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f1f5f9;font-size:13px}
        .inv-row:last-child{border:none}
        .inv-total{font-weight:700;font-size:14px;color:#1a202c;margin-top:8px;padding-top:8px;border-top:2px solid #e2e8f0}
        .empty-state{padding:50px 20px;text-align:center;color:#a0aec0}
        .empty-state i{font-size:40px;margin-bottom:12px;display:block}
        .loading{padding:40px;text-align:center;color:#a0aec0}

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
                    <h1><i class="fas fa-file-invoice-dollar" style="color:#3182ce"></i> Commission Invoices</h1>
                    <p>Track and invoice 10% platform commission from ground owner walk-in earnings</p>
                </div>
                <button class="btn btn-ghost" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            </div>
        </div>

        <div class="content">

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon si-purple"><i class="fas fa-coins"></i></div>
                    <div class="stat-body"><div class="val" id="statUninvoiced">—</div><div class="lbl">Uninvoiced Commission</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-orange"><i class="fas fa-clock"></i></div>
                    <div class="stat-body"><div class="val" id="statOutstanding">—</div><div class="lbl">Outstanding</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-body"><div class="val" id="statCollected">—</div><div class="lbl">Total Collected</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-red"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-body"><div class="val" id="statOverdue">—</div><div class="lbl">Overdue Invoices</div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-blue"><i class="fas fa-file-invoice"></i></div>
                    <div class="stat-body"><div class="val" id="statTotal">—</div><div class="lbl">Total Invoices</div></div>
                </div>
            </div>

            <!-- Owners with uninvoiced earnings -->
            <div class="card">
                <div class="card-head">
                    <h3><i class="fas fa-user-tie" style="color:#3182ce"></i> Ground Owners — Uninvoiced Earnings</h3>
                    <button class="btn btn-ghost" onclick="loadOwners()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
                <div class="tbl-wrap">
                    <div id="ownersLoading" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                    <table id="ownersTable" style="display:none">
                        <thead><tr>
                            <th>Owner</th><th>Email</th><th>Transactions</th>
                            <th>Period</th><th>Total Earnings</th><th>Commission (10%)</th><th></th>
                        </tr></thead>
                        <tbody id="ownersTbody"></tbody>
                    </table>
                    <div id="ownersEmpty" class="empty-state" style="display:none">
                        <i class="fas fa-check-circle" style="color:#38a169"></i>
                        All earnings have been invoiced.
                    </div>
                </div>
            </div>

            <!-- All invoices -->
            <div class="card">
                <div class="card-head">
                    <h3><i class="fas fa-list-alt" style="color:#3182ce"></i> All Invoices</h3>
                    <button class="btn btn-ghost" onclick="loadInvoices()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
                <div class="tbl-wrap">
                    <div id="invLoading" class="loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                    <table id="invTable" style="display:none">
                        <thead><tr>
                            <th>Invoice #</th><th>Owner</th><th>Period</th>
                            <th>Earnings</th><th>Commission</th><th>Due Date</th><th>Status</th><th></th>
                        </tr></thead>
                        <tbody id="invTbody"></tbody>
                    </table>
                    <div id="invEmpty" class="empty-state" style="display:none">
                        <i class="fas fa-file-invoice"></i> No invoices yet.
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Generate Invoice Modal -->
<div class="modal-backdrop" id="genBackdrop">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="fas fa-file-invoice-dollar"></i> Generate Commission Invoice</h3>
            <button onclick="closeGen()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:13px;color:#4a5568;margin-bottom:16px">
                This will create an invoice for all uninvoiced earnings of
                <strong id="genOwnerName"></strong>.
            </p>
            <div id="genPreview" style="background:#f7fafc;border-radius:8px;padding:14px;margin-bottom:16px">
                <div class="inv-row"><span>Total Earnings</span><span id="genEarnings">—</span></div>
                <div class="inv-row"><span>Commission Rate</span><span>10%</span></div>
                <div class="inv-row inv-total"><span>Commission Due</span><span id="genCommission">—</span></div>
                <div class="inv-row" style="font-size:12px;color:#718096"><span>Period</span><span id="genPeriod">—</span></div>
                <div class="inv-row" style="font-size:12px;color:#718096"><span>Due Date</span><span>14 days from today</span></div>
            </div>
            <div class="form-group">
                <label>Notes (optional)</label>
                <textarea id="genNotes" rows="2" placeholder="Any additional instructions…"></textarea>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-ghost" onclick="closeGen()">Cancel</button>
            <button class="btn btn-primary" id="genSubmitBtn" onclick="submitInvoice()">
                <i class="fas fa-paper-plane"></i> Send Invoice
            </button>
        </div>
    </div>
</div>

<!-- Invoice Detail Modal -->
<div class="modal-backdrop" id="detailBackdrop">
    <div class="modal-box" style="width:min(680px,96vw)">
        <div class="modal-head" style="background:linear-gradient(135deg,#276749,#38a169)">
            <h3><i class="fas fa-receipt"></i> Invoice Detail</h3>
            <button onclick="closeDetail()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" id="detailContent"></div>
        <div class="modal-foot" id="detailFoot"></div>
    </div>
</div>

<script>
(function(){
'use strict';

const BASE = window.BASE_URL || '';
let pendingOwnerId   = null;
let pendingOwnerData = null;

function lkr(n){ return 'Rs. ' + (parseFloat(n)||0).toLocaleString('en-LK',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtDate(d){ return d ? new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '—'; }

function statusBadge(s){
    const map = {sent:'badge-sent',paid:'badge-paid',overdue:'badge-overdue'};
    const icon = {sent:'fa-paper-plane',paid:'fa-check',overdue:'fa-exclamation-triangle'};
    return '<span class="badge '+(map[s]||'badge-sent')+'"><i class="fas '+(icon[s]||'fa-circle')+'"></i> '+s.charAt(0).toUpperCase()+s.slice(1)+'</span>';
}

// ── Stats ──────────────────────────────────────────
async function loadStats(){
    try{
        const r = await fetch(BASE+'/api/admin/commission/stats');
        const d = await r.json();
        if(!d.success) return;
        const s = d.stats;
        document.getElementById('statUninvoiced').textContent  = lkr(s.uninvoiced_commission||0);
        document.getElementById('statOutstanding').textContent = lkr(s.total_outstanding||0);
        document.getElementById('statCollected').textContent   = lkr(s.total_collected||0);
        document.getElementById('statOverdue').textContent     = s.overdue||0;
        document.getElementById('statTotal').textContent       = s.total_invoices||0;
    }catch(e){ console.error(e); }
}

// ── Owners ────────────────────────────────────────
async function loadOwners(){
    document.getElementById('ownersLoading').style.display = 'block';
    document.getElementById('ownersTable').style.display   = 'none';
    document.getElementById('ownersEmpty').style.display   = 'none';
    try{
        const r = await fetch(BASE+'/api/admin/commission/owners');
        const d = await r.json();
        const rows = d.owners || [];
        document.getElementById('ownersLoading').style.display = 'none';
        if(!rows.length){ document.getElementById('ownersEmpty').style.display='block'; return; }
        document.getElementById('ownersTable').style.display = 'table';
        document.getElementById('ownersTbody').innerHTML = rows.map(o => {
            const period = fmtDate(o.earliest_date) + ' – ' + fmtDate(o.latest_date);
            return '<tr>' +
                '<td><strong>'+o.owner_name+'</strong></td>' +
                '<td style="color:#718096">'+o.email+'</td>' +
                '<td>'+o.uninvoiced_count+' transactions</td>' +
                '<td style="font-size:12px">'+period+'</td>' +
                '<td class="amount">'+lkr(o.total_earnings)+'</td>' +
                '<td style="color:#c05621;font-weight:700">'+lkr(o.commission_owed)+'</td>' +
                '<td><button class="btn btn-primary" onclick="openGen('+JSON.stringify(o).replace(/"/g,'&quot;')+')">' +
                    '<i class="fas fa-file-invoice-dollar"></i> Generate Invoice</button></td>' +
                '</tr>';
        }).join('');
    }catch(e){ console.error(e); document.getElementById('ownersLoading').innerHTML='<i class="fas fa-exclamation-circle"></i> Failed to load'; }
}
window.loadOwners = loadOwners;

// ── Invoices ──────────────────────────────────────
async function loadInvoices(){
    document.getElementById('invLoading').style.display = 'block';
    document.getElementById('invTable').style.display   = 'none';
    document.getElementById('invEmpty').style.display   = 'none';
    try{
        const r = await fetch(BASE+'/api/admin/commission/invoices');
        const d = await r.json();
        const rows = d.invoices || [];
        document.getElementById('invLoading').style.display = 'none';
        if(!rows.length){ document.getElementById('invEmpty').style.display='block'; return; }
        document.getElementById('invTable').style.display = 'table';
        document.getElementById('invTbody').innerHTML = rows.map(inv => {
            const period = fmtDate(inv.period_start) + ' – ' + fmtDate(inv.period_end);
            return '<tr>' +
                '<td><strong style="color:#2b6cb0">'+inv.invoice_number+'</strong></td>' +
                '<td>'+inv.owner_name+'</td>' +
                '<td style="font-size:12px">'+period+'</td>' +
                '<td>'+lkr(inv.total_earnings)+'</td>' +
                '<td style="color:#c05621;font-weight:700">'+lkr(inv.commission_amount)+'</td>' +
                '<td style="font-size:12px">'+fmtDate(inv.due_date)+'</td>' +
                '<td>'+statusBadge(inv.status)+'</td>' +
                '<td style="display:flex;gap:6px">' +
                    '<button class="btn btn-ghost" onclick="viewInvoice('+inv.id+')"><i class="fas fa-eye"></i></button>' +
                    (inv.status !== 'paid' ? '<button class="btn btn-success" onclick="markPaid('+inv.id+')"><i class="fas fa-check"></i> Paid</button>' : '') +
                '</td>' +
                '</tr>';
        }).join('');
    }catch(e){ console.error(e); }
}
window.loadInvoices = loadInvoices;

// ── Generate Invoice Modal ────────────────────────
window.openGen = function(owner){
    if(typeof owner === 'string') owner = JSON.parse(owner);
    pendingOwnerId   = owner.owner_id;
    pendingOwnerData = owner;
    document.getElementById('genOwnerName').textContent = owner.owner_name;
    document.getElementById('genEarnings').textContent  = lkr(owner.total_earnings);
    document.getElementById('genCommission').textContent = lkr(owner.commission_owed);
    const p = fmtDate(owner.earliest_date)+' – '+fmtDate(owner.latest_date);
    document.getElementById('genPeriod').textContent = p;
    document.getElementById('genNotes').value = '';
    document.getElementById('genBackdrop').classList.add('open');
};

window.closeGen = function(){
    document.getElementById('genBackdrop').classList.remove('open');
    pendingOwnerId = null; pendingOwnerData = null;
};

window.submitInvoice = async function(){
    if(!pendingOwnerId) return;
    const btn = document.getElementById('genSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending…';
    try{
        const res  = await fetch(BASE+'/api/admin/commission/invoices',{
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ owner_id: pendingOwnerId, notes: document.getElementById('genNotes').value.trim()||null })
        });
        const data = await res.json();
        if(data.success){
            closeGen();
            alert('Invoice '+data.invoice.invoice_number+' sent to '+data.invoice.owner_name+'!\nCommission: '+lkr(data.invoice.commission_amount));
            loadStats(); loadOwners(); loadInvoices();
        } else {
            alert(data.message || 'Failed to generate invoice');
        }
    }catch(e){ alert('Network error'); }
    finally{ btn.disabled=false; btn.innerHTML='<i class="fas fa-paper-plane"></i> Send Invoice'; }
};

// ── Invoice Detail ────────────────────────────────
window.viewInvoice = async function(id){
    document.getElementById('detailContent').innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading…</div>';
    document.getElementById('detailFoot').innerHTML = '';
    document.getElementById('detailBackdrop').classList.add('open');
    try{
        const r = await fetch(BASE+'/api/admin/commission/invoices/'+id);
        const d = await r.json();
        if(!d.success){ document.getElementById('detailContent').innerHTML='<p>Failed to load.</p>'; return; }
        const inv = d.invoice;
        const itemsHtml = (inv.items||[]).map(item =>
            '<div class="inv-row">' +
            '<span style="font-size:12px">'+(item.facility_name||'—')+' &nbsp;<span style="color:#a0aec0">'+fmtDate(item.earning_date)+'</span></span>' +
            '<span style="font-size:12px">'+lkr(item.gross_amount)+' → <strong style="color:#c05621">'+lkr(item.commission_amount)+'</strong></span>' +
            '</div>'
        ).join('');
        document.getElementById('detailContent').innerHTML =
            '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;font-size:13px">'+
            '<div><span style="color:#718096">Invoice #</span><br><strong>'+inv.invoice_number+'</strong></div>'+
            '<div><span style="color:#718096">Status</span><br>'+statusBadge(inv.status)+'</div>'+
            '<div><span style="color:#718096">Owner</span><br><strong>'+inv.owner_name+'</strong><br><span style="color:#718096;font-size:12px">'+inv.owner_email+'</span></div>'+
            '<div><span style="color:#718096">Due Date</span><br><strong>'+fmtDate(inv.due_date)+'</strong>'+
                (inv.paid_date ? '<br><span style="color:#38a169;font-size:12px">Paid: '+fmtDate(inv.paid_date)+'</span>' : '')+'</div>'+
            '</div>'+
            '<div style="background:#f7fafc;border-radius:8px;padding:14px;margin-bottom:12px">'+
            '<p style="font-size:11px;font-weight:700;text-transform:uppercase;color:#718096;margin-bottom:8px">Line Items</p>'+
            itemsHtml+
            '<div class="inv-row inv-total"><span>Total Earnings</span><span>'+lkr(inv.total_earnings)+'</span></div>'+
            '<div class="inv-row" style="color:#c05621;font-weight:700"><span>Commission (10%)</span><span>'+lkr(inv.commission_amount)+'</span></div>'+
            '</div>'+
            (inv.notes ? '<p style="font-size:13px;color:#4a5568"><strong>Notes:</strong> '+inv.notes+'</p>' : '');

        document.getElementById('detailFoot').innerHTML =
            '<button class="btn btn-ghost" onclick="closeDetail()">Close</button>'+
            (inv.status !== 'paid' ? '<button class="btn btn-success" onclick="markPaid('+inv.id+');closeDetail()"><i class="fas fa-check"></i> Mark as Paid</button>' : '');
    }catch(e){ console.error(e); }
};

window.closeDetail = function(){ document.getElementById('detailBackdrop').classList.remove('open'); };

// ── Mark Paid ─────────────────────────────────────
window.markPaid = async function(id){
    if(!confirm('Mark this invoice as paid?')) return;
    try{
        const r = await fetch(BASE+'/api/admin/commission/invoices/'+id+'/paid',{method:'PUT'});
        const d = await r.json();
        if(d.success){ loadStats(); loadOwners(); loadInvoices(); }
        else alert(d.message||'Failed');
    }catch(e){ alert('Network error'); }
};

// ── Init ──────────────────────────────────────────
loadStats();
loadOwners();
loadInvoices();

})();
</script>
</body>
</html>
