<?php
$title = 'Commission Invoices - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <div class="go-page-header">
            <div class="go-header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="go-page-title"><i class="fas fa-file-invoice-dollar"></i> Commission Invoices</h1>
                    <p class="go-page-subtitle">Platform 10% commission invoices from admin</p>
                </div>
            </div>
        </div>

        <div style="padding:24px">

            <!-- Stats -->
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:24px">
                <div class="ci-stat">
                    <div class="ci-stat-icon ci-blue"><i class="fas fa-file-invoice"></i></div>
                    <div><div class="ci-val" id="stTotal">—</div><div class="ci-lbl">Total Invoices</div></div>
                </div>
                <div class="ci-stat">
                    <div class="ci-stat-icon ci-orange"><i class="fas fa-clock"></i></div>
                    <div><div class="ci-val" id="stPending">—</div><div class="ci-lbl">Pending Payment</div></div>
                </div>
                <div class="ci-stat">
                    <div class="ci-stat-icon ci-green"><i class="fas fa-check-circle"></i></div>
                    <div><div class="ci-val" id="stPaid">—</div><div class="ci-lbl">Paid</div></div>
                </div>
                <div class="ci-stat">
                    <div class="ci-stat-icon ci-red"><i class="fas fa-exclamation-circle"></i></div>
                    <div><div class="ci-val" id="stOverdue">—</div><div class="ci-lbl">Overdue</div></div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="ci-card">
                <div class="ci-card-head">
                    <h3><i class="fas fa-list-alt" style="color:#3b82f6"></i> My Commission Invoices</h3>
                    <button class="ci-btn ci-btn-ghost" onclick="loadInvoices()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
                <div style="overflow-x:auto;padding:0 20px 20px">
                    <div id="ciLoading" style="padding:40px;text-align:center;color:#94a3b8"><i class="fas fa-spinner fa-spin"></i> Loading…</div>
                    <table id="ciTable" style="display:none;width:100%;border-collapse:collapse;font-size:13px">
                        <thead>
                            <tr>
                                <th>Invoice #</th><th>Period</th><th>Total Earnings</th>
                                <th>Commission (10%)</th><th>Due Date</th><th>Status</th><th></th>
                            </tr>
                        </thead>
                        <tbody id="ciTbody"></tbody>
                    </table>
                    <div id="ciEmpty" style="display:none;padding:50px;text-align:center;color:#94a3b8">
                        <i class="fas fa-file-invoice" style="font-size:36px;margin-bottom:12px;display:block"></i>
                        No commission invoices yet.
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Invoice Detail Modal -->
<div class="ci-backdrop" id="ciDetailBackdrop"></div>
<div class="ci-modal" id="ciDetailModal">
    <div class="ci-modal-box">
        <div class="ci-modal-head">
            <h3><i class="fas fa-receipt"></i> Invoice Detail</h3>
            <button onclick="closeDetail()"><i class="fas fa-times"></i></button>
        </div>
        <div class="ci-modal-body" id="ciDetailContent"></div>
        <div class="ci-modal-foot">
            <button class="ci-btn ci-btn-ghost" onclick="closeDetail()">Close</button>
        </div>
    </div>
</div>

<style>
.go-page-header{background:#fff;padding:20px 28px;box-shadow:0 2px 4px rgba(0,0,0,.05);position:sticky;top:0;z-index:100;display:flex;align-items:center;justify-content:space-between}
.go-header-left{display:flex;align-items:center;gap:16px}
.go-page-title{font-size:22px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:8px}
.go-page-subtitle{font-size:13px;color:#64748b;margin:2px 0 0}

.ci-stat{background:#fff;border-radius:12px;padding:18px;box-shadow:0 2px 8px rgba(0,0,0,.07);display:flex;align-items:center;gap:14px}
.ci-stat-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.ci-blue{background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff}
.ci-orange{background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff}
.ci-green{background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff}
.ci-red{background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff}
.ci-val{font-size:22px;font-weight:700;color:#0f172a;line-height:1}
.ci-lbl{font-size:12px;color:#64748b;margin-top:3px}

.ci-card{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07)}
.ci-card-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9}
.ci-card-head h3{font-size:14px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;margin:0}

table th{text-align:left;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;border-bottom:2px solid #f1f5f9}
table td{padding:11px 12px;border-bottom:1px solid #f8fafc;color:#374151;vertical-align:middle}
table tr:hover td{background:#fafbfc}

.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700}
.b-sent{background:#eff6ff;color:#1d4ed8}
.b-paid{background:#f0fdf4;color:#15803d}
.b-overdue{background:#fef2f2;color:#b91c1c}

.ci-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.ci-btn-ghost{background:#f8fafc;color:#475569;border:1px solid #e2e8f0}.ci-btn-ghost:hover{background:#f1f5f9}
.ci-btn-primary{background:#3b82f6;color:#fff}.ci-btn-primary:hover{background:#2563eb}

.ci-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000}
.ci-backdrop.open{display:block}
.ci-modal{display:none;position:fixed;inset:0;z-index:1001;align-items:center;justify-content:center}
.ci-modal.open{display:flex}
.ci-modal-box{background:#fff;border-radius:14px;width:min(600px,96vw);max-height:85vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2)}
.ci-modal-head{background:linear-gradient(135deg,#1e40af,#2563eb);padding:16px 20px;display:flex;align-items:center;justify-content:space-between}
.ci-modal-head h3{color:#fff;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;margin:0}
.ci-modal-head button{background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:13px}
.ci-modal-body{padding:20px;overflow-y:auto;flex:1;font-size:13px}
.ci-modal-foot{padding:12px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end}
.ci-row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid #f1f5f9;font-size:13px}
.ci-row:last-child{border:none}
</style>

<script>
(function(){
'use strict';
const BASE = window.BASE_URL || '';

function lkr(n){ return 'Rs. '+(parseFloat(n)||0).toLocaleString('en-LK',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtDate(d){ return d ? new Date(d).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) : '—'; }

function badge(s){
    const map={sent:'b-sent',paid:'b-paid',overdue:'b-overdue'};
    const icon={sent:'fa-paper-plane',paid:'fa-check',overdue:'fa-exclamation-triangle'};
    return '<span class="badge '+(map[s]||'b-sent')+'"><i class="fas '+(icon[s]||'fa-circle')+'"></i> '+s.charAt(0).toUpperCase()+s.slice(1)+'</span>';
}

async function loadInvoices(){
    document.getElementById('ciLoading').style.display='block';
    document.getElementById('ciTable').style.display='none';
    document.getElementById('ciEmpty').style.display='none';
    try{
        const r = await fetch(BASE+'/api/ground-owner/commission/invoices');
        const d = await r.json();
        const rows = d.invoices||[];
        document.getElementById('ciLoading').style.display='none';
        // Stats
        let total=rows.length, pending=0, paid=0, overdue=0;
        rows.forEach(i=>{ if(i.status==='sent')pending++; else if(i.status==='paid')paid++; else if(i.status==='overdue')overdue++; });
        document.getElementById('stTotal').textContent=total;
        document.getElementById('stPending').textContent=pending;
        document.getElementById('stPaid').textContent=paid;
        document.getElementById('stOverdue').textContent=overdue;
        if(!rows.length){ document.getElementById('ciEmpty').style.display='block'; return; }
        document.getElementById('ciTable').style.display='table';
        document.getElementById('ciTbody').innerHTML=rows.map(inv=>{
            const period=fmtDate(inv.period_start)+' – '+fmtDate(inv.period_end);
            return '<tr>'+
                '<td><strong style="color:#2563eb">'+inv.invoice_number+'</strong></td>'+
                '<td style="font-size:12px">'+period+'</td>'+
                '<td>'+lkr(inv.total_earnings)+'</td>'+
                '<td style="color:#d97706;font-weight:700">'+lkr(inv.commission_amount)+'</td>'+
                '<td style="font-size:12px">'+fmtDate(inv.due_date)+'</td>'+
                '<td>'+badge(inv.status)+'</td>'+
                '<td><button class="ci-btn ci-btn-ghost" onclick="viewInvoice('+inv.id+')"><i class="fas fa-eye"></i> View</button></td>'+
                '</tr>';
        }).join('');
    }catch(e){ console.error(e); document.getElementById('ciLoading').innerHTML='<i class="fas fa-exclamation-circle"></i> Failed to load'; }
}
window.loadInvoices=loadInvoices;

window.viewInvoice=async function(id){
    document.getElementById('ciDetailContent').innerHTML='<div style="text-align:center;padding:30px;color:#94a3b8"><i class="fas fa-spinner fa-spin"></i> Loading…</div>';
    document.getElementById('ciDetailBackdrop').classList.add('open');
    document.getElementById('ciDetailModal').classList.add('open');
    try{
        const r=await fetch(BASE+'/api/admin/commission/invoices/'+id);
        const d=await r.json();
        if(!d.success){ document.getElementById('ciDetailContent').innerHTML='<p>Failed to load.</p>'; return; }
        const inv=d.invoice;
        const items=(inv.items||[]).map(it=>
            '<div class="ci-row">'+
            '<span>'+(it.facility_name||'—')+' &nbsp;<span style="color:#94a3b8;font-size:12px">'+fmtDate(it.earning_date)+'</span></span>'+
            '<span>'+lkr(it.gross_amount)+' → <strong style="color:#d97706">'+lkr(it.commission_amount)+'</strong></span>'+
            '</div>'
        ).join('');
        document.getElementById('ciDetailContent').innerHTML=
            '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">'+
            '<div><span style="color:#64748b;font-size:12px">Invoice #</span><br><strong>'+inv.invoice_number+'</strong></div>'+
            '<div><span style="color:#64748b;font-size:12px">Status</span><br>'+badge(inv.status)+'</div>'+
            '<div><span style="color:#64748b;font-size:12px">Period</span><br><strong>'+fmtDate(inv.period_start)+' – '+fmtDate(inv.period_end)+'</strong></div>'+
            '<div><span style="color:#64748b;font-size:12px">Due Date</span><br><strong>'+fmtDate(inv.due_date)+'</strong>'+
                (inv.paid_date?'<br><span style="color:#16a34a;font-size:12px">Paid: '+fmtDate(inv.paid_date)+'</span>':'')+'</div>'+
            '</div>'+
            '<div style="background:#f8fafc;border-radius:8px;padding:14px;margin-bottom:12px">'+
            '<p style="font-size:11px;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:8px">Line Items</p>'+
            items+
            '<div class="ci-row" style="font-weight:700"><span>Total Earnings</span><span>'+lkr(inv.total_earnings)+'</span></div>'+
            '<div class="ci-row" style="color:#d97706;font-weight:700"><span>Commission (10%)</span><span>'+lkr(inv.commission_amount)+'</span></div>'+
            '</div>'+
            (inv.notes?'<p style="font-size:13px;color:#374151"><strong>Notes:</strong> '+inv.notes+'</p>':'');
    }catch(e){ console.error(e); }
};

window.closeDetail=function(){
    document.getElementById('ciDetailBackdrop').classList.remove('open');
    document.getElementById('ciDetailModal').classList.remove('open');
};

loadInvoices();
})();
</script>

<?php include __DIR__ . '/layout-foot.php'; ?>
