<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
/**
 * Admin Payouts Management Page
 * Review, approve, and reject payout requests for shop owners, ground owners, and coaches
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payout Management - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-dashboard.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; color: #2d3748; }
        .admin-container { display: flex; min-height: 100vh; }
        .admin-main { flex: 1; margin-left: 260px; transition: margin-left 0.3s ease; }
        .admin-header { background: #fff; padding: 20px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .header-content { display: flex; justify-content: space-between; align-items: center; }
        .page-title { font-size: 24px; font-weight: 600; color: #1a202c; }
        .page-subtitle { color: #718096; font-size: 14px; margin-top: 4px; }
        .content { padding: 30px; }

        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); transition: all 0.2s; }
        .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .stat-icon.warning { background: linear-gradient(135deg, #ed8936, #dd6b20); color: #fff; }
        .stat-icon.success { background: linear-gradient(135deg, #48bb78, #38a169); color: #fff; }
        .stat-icon.danger  { background: linear-gradient(135deg, #f56565, #e53e3e); color: #fff; }
        .stat-icon.info    { background: linear-gradient(135deg, #4299e1, #3182ce); color: #fff; }
        .stat-value { font-size: 28px; font-weight: 700; color: #1a202c; }
        .stat-label { color: #718096; font-size: 14px; font-weight: 500; }

        /* Tabs container */
        .tabs-container { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

        /* Type selector row */
        .type-selector { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .type-btn { padding: 10px 22px; border-radius: 8px; border: 2px solid #e2e8f0; background: #fff; color: #718096; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s; }
        .type-btn:hover { border-color: #667eea; color: #667eea; }
        .type-btn.active { border-color: #667eea; background: #667eea; color: #fff; }
        .type-btn .count-badge { background: rgba(255,255,255,0.3); padding: 1px 7px; border-radius: 8px; font-size: 11px; }
        .type-btn:not(.active) .count-badge { background: #f56565; color: #fff; }

        /* Status tabs */
        .tab-buttons { display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; }
        .tab-btn { padding: 10px 22px; background: none; border: none; color: #718096; font-size: 14px; font-weight: 600; cursor: pointer; position: relative; transition: color 0.2s; }
        .tab-btn.active { color: #667eea; }
        .tab-btn.active::after { content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px; background: #667eea; }
        .tab-btn .badge { background: #f56565; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 6px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f7fafc; padding: 12px; text-align: left; font-weight: 600; color: #4a5568; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 16px 12px; border-bottom: 1px solid #e2e8f0; }
        tr:hover { background: #f7fafc; }
        .status-badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block; }
        .status-pending   { background: #feebc8; color: #7c2d12; }
        .status-completed { background: #c6f6d5; color: #22543d; }
        .status-rejected  { background: #fed7d7; color: #742a2a; }

        /* Buttons */
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
        .btn-approve { background: #48bb78; color: #fff; }
        .btn-approve:hover { background: #38a169; }
        .btn-reject  { background: #f56565; color: #fff; }
        .btn-reject:hover  { background: #e53e3e; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* Modals */
        .modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: #fff; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-title { font-size: 20px; font-weight: 700; }
        .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: #a0aec0; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; margin-bottom: 6px; font-weight: 600; color: #2d3748; font-size: 14px; }
        .form-input { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px; }
        .form-input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
        textarea.form-input { min-height: 80px; resize: vertical; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .detail-label { color: #718096; }
        .detail-value { font-weight: 600; color: #2d3748; }

        .empty-state { text-align: center; padding: 60px 20px; color: #a0aec0; }
        .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }

        @media (max-width: 768px) { .admin-main { margin-left: 0; } .stats-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 480px) { .stats-grid { grid-template-columns: 1fr; } .type-selector { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>

        <div class="admin-main">
            <div class="admin-header">
                <div class="header-content">
                    <div>
                        <h1 class="page-title">Payout Management</h1>
                        <p class="page-subtitle" id="pageSubtitle">Review and process shop owner payout requests</p>
                    </div>
                </div>
            </div>

            <div class="content">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="pendingCount">0</div>
                                <div class="stat-label">Pending Requests</div>
                            </div>
                            <div class="stat-icon warning"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="pendingAmount">LKR 0</div>
                                <div class="stat-label">Pending Amount</div>
                            </div>
                            <div class="stat-icon info"><i class="fas fa-coins"></i></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="completedTotal">LKR 0</div>
                                <div class="stat-label">Total Paid Out</div>
                            </div>
                            <div class="stat-icon success"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="monthPaid">LKR 0</div>
                                <div class="stat-label">This Month</div>
                            </div>
                            <div class="stat-icon danger"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Payouts Table -->
                <div class="tabs-container">

                    <!-- Type selector -->
                    <div class="type-selector">
                        <button class="type-btn active" id="type-shop" onclick="switchType('shop')">
                            <i class="fas fa-store"></i> Shop Owners
                            <span class="count-badge" id="shopPendingBadge">0</span>
                        </button>
                        <button class="type-btn" id="type-ground_owner" onclick="switchType('ground_owner')">
                            <i class="fas fa-building"></i> Ground Owners
                            <span class="count-badge" id="groundPendingBadge">0</span>
                        </button>
                        <button class="type-btn" id="type-coach" onclick="switchType('coach')">
                            <i class="fas fa-user-tie"></i> Coaches
                            <span class="count-badge" id="coachPendingBadge">0</span>
                        </button>
                    </div>

                    <!-- Status tabs -->
                    <div class="tab-buttons">
                        <button class="tab-btn active" onclick="filterStatus('pending')" id="tab-pending">
                            <i class="fas fa-clock"></i> Pending <span class="badge" id="pendingBadge">0</span>
                        </button>
                        <button class="tab-btn" onclick="filterStatus('completed')" id="tab-completed">
                            <i class="fas fa-check"></i> Completed
                        </button>
                        <button class="tab-btn" onclick="filterStatus('rejected')" id="tab-rejected">
                            <i class="fas fa-times"></i> Rejected
                        </button>
                        <button class="tab-btn" onclick="filterStatus('all')" id="tab-all">
                            <i class="fas fa-list"></i> All
                        </button>
                    </div>

                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th id="colOwnerLabel">Shop Owner</th>
                                    <th>Amount</th>
                                    <th>Bank Details</th>
                                    <th>Balance</th>
                                    <th>Requested</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="payoutsTableBody">
                                <tr><td colspan="7" class="empty-state"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal" id="approveModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-check-circle" style="color: #48bb78; margin-right: 8px;"></i>Approve Payout</h3>
                <button class="close-modal" onclick="closeModal('approveModal')">&times;</button>
            </div>
            <div id="approveDetails"></div>
            <div class="form-group" style="margin-top: 16px;">
                <label class="form-label">Transaction Reference *</label>
                <input type="text" class="form-input" id="transactionReference" placeholder="e.g. BANK-TXN-123456">
                <small style="color: #718096; font-size: 12px;">Enter the bank transfer reference number after processing</small>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button class="btn" style="flex: 1; background: #e2e8f0; color: #2d3748;" onclick="closeModal('approveModal')">Cancel</button>
                <button class="btn btn-approve" style="flex: 1;" onclick="confirmApprove()" id="confirmApproveBtn">
                    <i class="fas fa-check"></i> Approve & Process
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal" id="rejectModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-times-circle" style="color: #f56565; margin-right: 8px;"></i>Reject Payout</h3>
                <button class="close-modal" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <div id="rejectDetails"></div>
            <div class="form-group" style="margin-top: 16px;">
                <label class="form-label">Rejection Reason *</label>
                <textarea class="form-input" id="rejectionReason" placeholder="Provide a reason for rejecting this payout request..."></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button class="btn" style="flex: 1; background: #e2e8f0; color: #2d3748;" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="btn btn-reject" style="flex: 1;" onclick="confirmReject()" id="confirmRejectBtn">
                    <i class="fas fa-times"></i> Reject Request
                </button>
            </div>
        </div>
    </div>

<script>
(function () {
    const BASE = '<?= $_base ?>';
    let currentType   = 'shop';
    let currentStatus = 'pending';
    let selectedPayoutId = null;

    const typeLabels = {
        shop:         { col: 'Shop Owner',    subtitle: 'Review and process shop owner payout requests',   icon: 'fas fa-store' },
        ground_owner: { col: 'Ground Owner',  subtitle: 'Review and process ground owner payout requests', icon: 'fas fa-building' },
        coach:        { col: 'Coach',         subtitle: 'Review and process coach payout requests',        icon: 'fas fa-user-tie' },
    };

    document.addEventListener('DOMContentLoaded', () => {
        loadPayouts();
        refreshAllTypeCounts();
    });

    /* ── Type switching ─────────────────────────────────────── */
    window.switchType = function (type) {
        currentType   = type;
        currentStatus = 'pending';

        document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('type-' + type).classList.add('active');

        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-pending').classList.add('active');

        const cfg = typeLabels[type] || typeLabels.shop;
        document.getElementById('colOwnerLabel').textContent = cfg.col;
        document.getElementById('pageSubtitle').textContent  = cfg.subtitle;

        loadPayouts();
    };

    /* ── Status filter ──────────────────────────────────────── */
    window.filterStatus = function (status) {
        currentStatus = status;
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + status).classList.add('active');
        loadPayouts();
    };

    /* ── Load payouts ───────────────────────────────────────── */
    async function loadPayouts() {
        document.getElementById('payoutsTableBody').innerHTML =
            '<tr><td colspan="7" class="empty-state"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
        try {
            const res  = await fetch(`${BASE}/api/admin/payouts?type=${currentType}&status=${currentStatus}`);
            const data = await res.json();
            if (data.success) {
                renderStats(data.stats);
                renderPayouts(data.payouts.data || []);
                updateTypeBadge(currentType, data.stats.pending_count);
            }
        } catch (err) {
            console.error('Failed to load payouts:', err);
            document.getElementById('payoutsTableBody').innerHTML =
                '<tr><td colspan="7" class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Failed to load payouts</p></td></tr>';
        }
    }

    /* Refresh pending counts for all three type badges on load */
    async function refreshAllTypeCounts() {
        const types = ['shop', 'ground_owner', 'coach'];
        const badgeIds = { shop: 'shopPendingBadge', ground_owner: 'groundPendingBadge', coach: 'coachPendingBadge' };
        await Promise.all(types.map(async t => {
            try {
                const res  = await fetch(`${BASE}/api/admin/payouts?type=${t}&status=pending`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById(badgeIds[t]).textContent = data.stats.pending_count || 0;
                }
            } catch (_) {}
        }));
    }

    function updateTypeBadge(type, count) {
        const ids = { shop: 'shopPendingBadge', ground_owner: 'groundPendingBadge', coach: 'coachPendingBadge' };
        if (ids[type]) document.getElementById(ids[type]).textContent = count || 0;
    }

    /* ── Stats ──────────────────────────────────────────────── */
    function renderStats(stats) {
        document.getElementById('pendingCount').textContent   = stats.pending_count || 0;
        document.getElementById('pendingAmount').textContent  = 'LKR ' + fmt(stats.pending_amount);
        document.getElementById('completedTotal').textContent = 'LKR ' + fmt(stats.completed_amount);
        document.getElementById('monthPaid').textContent      = 'LKR ' + fmt(stats.this_month_paid);
        document.getElementById('pendingBadge').textContent   = stats.pending_count || 0;
    }

    /* ── Table rows ─────────────────────────────────────────── */
    function renderPayouts(payouts) {
        const tbody = document.getElementById('payoutsTableBody');
        if (!payouts.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="empty-state"><i class="fas fa-inbox"></i><p>No payout requests found</p></td></tr>';
            return;
        }

        const cfg = typeLabels[currentType] || typeLabels.shop;

        tbody.innerHTML = payouts.map(p => {
            const name        = esc(p.owner_first_name || '') + ' ' + esc(p.owner_last_name || '');
            const entityLabel = currentType === 'coach'
                ? esc(p.owner_email || '')       /* coaches: show email as second line */
                : (esc(p.shop_name || 'N/A') + ' &bull; ' + esc(p.owner_email || ''));

            const actionCell = p.status === 'pending'
                ? `<button class="btn btn-approve btn-sm" onclick="openApprove(${p.id},'${esc(p.owner_first_name)} ${esc(p.owner_last_name)}',${p.amount},'${esc(p.bank_name)}','${esc(p.account_number)}')"><i class="fas fa-check"></i></button>
                   <button class="btn btn-reject btn-sm" onclick="openReject(${p.id},'${esc(p.owner_first_name)} ${esc(p.owner_last_name)}',${p.amount})"><i class="fas fa-times"></i></button>`
                : (p.transaction_reference
                    ? `<small>Ref: ${esc(p.transaction_reference)}</small>`
                    : (p.rejection_reason ? `<small style="color:#e53e3e;">${esc(p.rejection_reason)}</small>` : '-'));

            return `<tr>
                <td>
                    <strong>${name}</strong><br>
                    <small style="color:#718096;">${entityLabel}</small>
                </td>
                <td><strong style="font-size:16px;">LKR ${fmt(p.amount)}</strong></td>
                <td>
                    <small>${esc(p.bank_name || '-')}<br>
                    Acc: ${esc(p.account_number || '-')}<br>
                    ${esc(p.account_holder || '-')}</small>
                </td>
                <td><small>Available: LKR ${fmt(p.available_balance || 0)}<br>Total Earned: LKR ${fmt(p.total_earned || 0)}</small></td>
                <td><small>${formatDate(p.requested_at)}</small></td>
                <td><span class="status-badge status-${p.status}">${ucfirst(p.status)}</span></td>
                <td>${actionCell}</td>
            </tr>`;
        }).join('');
    }

    /* ── Approve / Reject modals ────────────────────────────── */
    window.openApprove = function (id, name, amount, bank, accNo) {
        selectedPayoutId = id;
        const cfg = typeLabels[currentType] || typeLabels.shop;
        document.getElementById('approveDetails').innerHTML = `
            <div class="detail-row"><span class="detail-label">${cfg.col}</span><span class="detail-value">${name}</span></div>
            <div class="detail-row"><span class="detail-label">Amount</span><span class="detail-value" style="color:#48bb78;font-size:18px;">LKR ${fmt(amount)}</span></div>
            <div class="detail-row"><span class="detail-label">Bank</span><span class="detail-value">${bank} (${accNo})</span></div>`;
        document.getElementById('transactionReference').value = '';
        document.getElementById('approveModal').classList.add('active');
    };

    window.openReject = function (id, name, amount) {
        selectedPayoutId = id;
        const cfg = typeLabels[currentType] || typeLabels.shop;
        document.getElementById('rejectDetails').innerHTML = `
            <div class="detail-row"><span class="detail-label">${cfg.col}</span><span class="detail-value">${name}</span></div>
            <div class="detail-row"><span class="detail-label">Amount</span><span class="detail-value" style="color:#f56565;">LKR ${fmt(amount)}</span></div>`;
        document.getElementById('rejectionReason').value = '';
        document.getElementById('rejectModal').classList.add('active');
    };

    window.confirmApprove = async function () {
        const ref = document.getElementById('transactionReference').value.trim();
        if (!ref) { alert('Please enter a transaction reference'); return; }

        const btn = document.getElementById('confirmApproveBtn');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            const res = await fetch(`${BASE}/api/admin/payouts/approve`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payout_id: selectedPayoutId, type: currentType, transaction_reference: ref })
            });
            const data = await res.json();
            if (data.success) {
                closeModal('approveModal');
                loadPayouts();
                refreshAllTypeCounts();
            } else { alert(data.message); }
        } catch (err) { alert('Failed to approve payout'); }
        finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check"></i> Approve & Process'; }
    };

    window.confirmReject = async function () {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) { alert('Please provide a rejection reason'); return; }

        const btn = document.getElementById('confirmRejectBtn');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            const res = await fetch(`${BASE}/api/admin/payouts/reject`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payout_id: selectedPayoutId, type: currentType, reason: reason })
            });
            const data = await res.json();
            if (data.success) {
                closeModal('rejectModal');
                loadPayouts();
                refreshAllTypeCounts();
            } else { alert(data.message); }
        } catch (err) { alert('Failed to reject payout'); }
        finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-times"></i> Reject Request'; }
    };

    window.closeModal = function (id) { document.getElementById(id).classList.remove('active'); };

    /* ── Helpers ────────────────────────────────────────────── */
    function fmt(n)      { return parseFloat(n || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function esc(s)      { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    function ucfirst(s)  { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
    function formatDate(d) { return d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '-'; }
})();
</script>
</body>
</html>
