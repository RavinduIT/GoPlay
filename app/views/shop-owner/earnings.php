<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/shop-owner-dashboard.css">
<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Earnings & Payouts - GoPlay';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css'];
$additionalJS = ['/public/js/shop-owner-sidebar.js'];
?>

<div class="shop-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Earnings & Payouts</h1>
            </div>
        </header>

        <div class="dashboard-content">
            <!-- Loading State -->
            <div id="earningsLoading" class="earnings-loading">
                <div class="loading-spinner"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
                <p>Loading earnings data...</p>
            </div>

            <!-- Main Earnings Content (hidden until loaded) -->
            <div id="earningsContent" style="display: none;">

                <!-- Balance Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Available Balance</h3>
                            <p class="stat-number" id="availableBalance">Rs 0.00</p>
                            <div class="stat-change positive">
                                <i class="fas fa-info-circle"></i>
                                <span>Ready to withdraw</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Earned</h3>
                            <p class="stat-number" id="totalEarned">Rs 0.00</p>
                            <div class="stat-change positive">
                                <i class="fas fa-trophy"></i>
                                <span>Lifetime earnings</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3>This Month</h3>
                            <p class="stat-number" id="monthlyEarnings">Rs 0.00</p>
                            <div class="stat-change positive">
                                <i class="fas fa-calendar"></i>
                                <span>Current month</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Withdrawn</h3>
                            <p class="stat-number" id="totalWithdrawn">Rs 0.00</p>
                            <div class="stat-change neutral">
                                <i class="fas fa-university"></i>
                                <span>Paid out</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Info Banner -->
                <div class="dashboard-card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: 1px solid rgba(0, 210, 255, 0.15);">
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <div style="flex: 0 0 auto; width: 40px; height: 40px; background: rgba(0, 210, 255, 0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-info-circle" style="color: #00d2ff;"></i>
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <div style="color: #e0e0e0; font-size: 0.9rem;">
                                Platform commission: <strong id="feePercentage">5</strong>% per sale. 
                                Minimum payout: <strong>Rs <span id="minPayoutAmount">10,000.00</span></strong>.
                                All payouts require admin approval.
                            </div>
                        </div>
                        <button class="btn-action" onclick="openPayoutModal()" id="requestPayoutBtn" disabled style="flex: 0 0 auto; padding: 10px 24px; background: linear-gradient(135deg, #00d2ff, #7b2ff7); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-paper-plane"></i> Request Payout
                        </button>
                    </div>
                    <div id="payoutEligibility" style="margin-top: 0.5rem; font-size: 0.8rem; color: #aaa;"></div>
                </div>

                <!-- Two Column Layout -->
                <div class="dashboard-grid">
                    <!-- Transaction History -->
                    <div class="dashboard-card" style="grid-column: 1 / -1;">
                        <div class="card-header">
                            <h3><i class="fas fa-list-alt" style="margin-right: 8px; color: #00d2ff;"></i>Transaction History</h3>
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="data-table" id="transactionsTable" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <th style="padding: 12px; text-align: left; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Date</th>
                                        <th style="padding: 12px; text-align: left; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Type</th>
                                        <th style="padding: 12px; text-align: left; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Order</th>
                                        <th style="padding: 12px; text-align: right; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Gross</th>
                                        <th style="padding: 12px; text-align: right; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Fee</th>
                                        <th style="padding: 12px; text-align: right; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Net</th>
                                        <th style="padding: 12px; text-align: right; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Balance</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsBody">
                                    <tr><td colspan="7" style="padding: 40px; text-align: center; color: #888;">No transactions yet</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="transactionsPagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 1rem;"></div>
                    </div>

                    <!-- Payout History -->
                    <div class="dashboard-card" style="grid-column: 1 / -1;">
                        <div class="card-header">
                            <h3><i class="fas fa-history" style="margin-right: 8px; color: #7b2ff7;"></i>Payout History</h3>
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="data-table" id="payoutsTable" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                        <th style="padding: 12px; text-align: left; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Date</th>
                                        <th style="padding: 12px; text-align: right; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Amount</th>
                                        <th style="padding: 12px; text-align: left; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Bank</th>
                                        <th style="padding: 12px; text-align: center; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Status</th>
                                        <th style="padding: 12px; text-align: left; color: #aaa; font-size: 0.8rem; text-transform: uppercase;">Reference</th>
                                    </tr>
                                </thead>
                                <tbody id="payoutsBody">
                                    <tr><td colspan="5" style="padding: 40px; text-align: center; color: #888;">No payout requests yet</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Bank Details Card -->
                    <div class="dashboard-card" style="grid-column: 1 / -1;">
                        <div class="card-header">
                            <h3><i class="fas fa-university" style="margin-right: 8px; color: #00d2ff;"></i>Payout Bank Details</h3>
                        </div>
                        <form id="payoutDetailsForm" onsubmit="savePayoutDetails(event)" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 0.5rem 0;">
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 6px; color: #ccc; font-size: 0.85rem;">Bank Name *</label>
                                <input type="text" id="bankName" name="bank_name" required
                                    style="width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-size: 0.9rem;"
                                    placeholder="e.g. Bank of Ceylon">
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 6px; color: #ccc; font-size: 0.85rem;">Account Number *</label>
                                <input type="text" id="accountNumber" name="account_number" required
                                    style="width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-size: 0.9rem;"
                                    placeholder="e.g. 1234567890">
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 6px; color: #ccc; font-size: 0.85rem;">Account Holder Name *</label>
                                <input type="text" id="accountHolder" name="account_holder" required
                                    style="width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-size: 0.9rem;"
                                    placeholder="e.g. John Doe">
                            </div>
                            <div class="form-group">
                                <label style="display: block; margin-bottom: 6px; color: #ccc; font-size: 0.85rem;">Branch Name</label>
                                <input type="text" id="branchName" name="branch_name"
                                    style="width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-size: 0.9rem;"
                                    placeholder="e.g. Colombo Main">
                            </div>
                            <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end;">
                                <button type="submit" style="padding: 10px 28px; background: linear-gradient(135deg, #00d2ff, #7b2ff7); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                    <i class="fas fa-save"></i> Save Bank Details
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Payout Request Modal -->
<div id="payoutModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #1a1a2e; border-radius: 16px; padding: 2rem; max-width: 450px; width: 90%; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: #fff; margin: 0;"><i class="fas fa-paper-plane" style="color: #00d2ff; margin-right: 8px;"></i>Request Payout</h3>
            <button onclick="closePayoutModal()" style="background: none; border: none; color: #888; font-size: 1.2rem; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        
        <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(0,210,255,0.05); border-radius: 10px; border: 1px solid rgba(0,210,255,0.15);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="color: #aaa; font-size: 0.85rem;">Available Balance</span>
                <span style="color: #00d2ff; font-weight: 600;" id="modalAvailableBalance">Rs 0.00</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #aaa; font-size: 0.85rem;">Minimum Payout</span>
                <span style="color: #ccc;" id="modalMinPayout">Rs 10,000.00</span>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 6px; color: #ccc; font-size: 0.85rem;">Withdrawal Amount (LKR) *</label>
            <input type="number" id="payoutAmount" step="0.01" min="1"
                style="width: 100%; padding: 12px 14px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #fff; font-size: 1.1rem; font-weight: 600;"
                placeholder="Enter amount">
        </div>

        <div id="payoutBankSummary" style="margin-bottom: 1.5rem; padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px; font-size: 0.85rem; color: #aaa;"></div>

        <div style="display: flex; gap: 0.75rem;">
            <button onclick="closePayoutModal()" style="flex: 1; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; color: #ccc; cursor: pointer;">Cancel</button>
            <button onclick="submitPayoutRequest()" id="submitPayoutBtn" style="flex: 1; padding: 12px; background: linear-gradient(135deg, #00d2ff, #7b2ff7); border: none; border-radius: 8px; color: white; font-weight: 600; cursor: pointer;">
                <i class="fas fa-check"></i> Submit Request
            </button>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= $_base ?>';
let earningsData = null;

// Load earnings data on page load
document.addEventListener('DOMContentLoaded', loadEarningsData);

async function loadEarningsData(page = 1) {
    try {
        const response = await fetch(`${BASE_URL}/api/shop-owner/earnings?page=${page}`);
        const data = await response.json();
        
        if (data.success) {
            earningsData = data;
            renderEarnings(data);
            document.getElementById('earningsLoading').style.display = 'none';
            document.getElementById('earningsContent').style.display = 'block';
        } else {
            showToast('Failed to load earnings data', 'error');
        }
    } catch (err) {
        console.error('Error loading earnings:', err);
        document.getElementById('earningsLoading').innerHTML = '<p style="color: #ff4757; text-align: center;">Failed to load earnings data. Please refresh the page.</p>';
    }
}

function renderEarnings(data) {
    const s = data.summary;
    
    // Update balance cards
    document.getElementById('availableBalance').textContent = 'Rs ' + formatNumber(s.available_balance);
    document.getElementById('totalEarned').textContent = 'Rs ' + formatNumber(s.total_earned);
    document.getElementById('monthlyEarnings').textContent = 'Rs ' + formatNumber(s.this_month_earnings);
    document.getElementById('totalWithdrawn').textContent = 'Rs ' + formatNumber(s.total_withdrawn);
    document.getElementById('feePercentage').textContent = s.service_fee_percentage;
    document.getElementById('minPayoutAmount').textContent = formatNumber(s.min_payout_amount);
    
    // Update payout eligibility
    const cw = data.can_withdraw;
    const btn = document.getElementById('requestPayoutBtn');
    const eligDiv = document.getElementById('payoutEligibility');
    
    if (cw.can_withdraw) {
        btn.disabled = false;
        btn.style.opacity = '1';
        eligDiv.innerHTML = '<i class="fas fa-check-circle" style="color: #2ed573;"></i> ' + cw.message;
        eligDiv.style.color = '#2ed573';
    } else {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        eligDiv.innerHTML = '<i class="fas fa-info-circle" style="color: #ffd32a;"></i> ' + cw.message;
        eligDiv.style.color = '#ffd32a';
    }
    
    // Render transactions
    renderTransactions(data.transactions);
    
    // Render payouts
    renderPayouts(data.payouts);
    
    // Fill bank details form
    if (data.payout_details) {
        document.getElementById('bankName').value = data.payout_details.bank_name || '';
        document.getElementById('accountNumber').value = data.payout_details.account_number || '';
        document.getElementById('accountHolder').value = data.payout_details.account_holder || '';
        document.getElementById('branchName').value = data.payout_details.branch_name || '';
    }
}

function renderTransactions(txData) {
    const tbody = document.getElementById('transactionsBody');
    if (!txData.data || txData.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="padding: 40px; text-align: center; color: #888;"><i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>No transactions yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = txData.data.map(tx => {
        const isCredit = tx.type.startsWith('credit');
        const typeLabels = {
            'credit_sale': '<span style="color: #2ed573;"><i class="fas fa-arrow-down"></i> Sale Credit</span>',
            'debit_withdrawal': '<span style="color: #ff6348;"><i class="fas fa-arrow-up"></i> Withdrawal</span>',
            'debit_refund': '<span style="color: #ff6348;"><i class="fas fa-undo"></i> Refund</span>',
            'credit_adjustment': '<span style="color: #2ed573;"><i class="fas fa-plus"></i> Adjustment</span>'
        };
        
        return `<tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
            <td style="padding: 12px; color: #ccc; font-size: 0.85rem;">${formatDate(tx.created_at)}</td>
            <td style="padding: 12px;">${typeLabels[tx.type] || tx.type}</td>
            <td style="padding: 12px; color: #aaa;">${tx.order_number ? '#' + tx.order_number : '-'}</td>
            <td style="padding: 12px; text-align: right; color: #ccc;">Rs ${formatNumber(tx.gross_amount)}</td>
            <td style="padding: 12px; text-align: right; color: #ff6348;">-Rs ${formatNumber(tx.fee_amount)}</td>
            <td style="padding: 12px; text-align: right; color: ${isCredit ? '#2ed573' : '#ff6348'}; font-weight: 600;">${isCredit ? '+' : '-'}Rs ${formatNumber(tx.net_amount)}</td>
            <td style="padding: 12px; text-align: right; color: #00d2ff; font-weight: 500;">Rs ${formatNumber(tx.balance_after)}</td>
        </tr>`;
    }).join('');
}

function renderPayouts(payoutData) {
    const tbody = document.getElementById('payoutsBody');
    if (!payoutData.data || payoutData.data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="padding: 40px; text-align: center; color: #888;"><i class="fas fa-money-check-alt" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>No payout requests yet</td></tr>';
        return;
    }
    
    const statusBadges = {
        'pending': '<span style="padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: rgba(255,211,42,0.15); color: #ffd32a;">Pending</span>',
        'processing': '<span style="padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: rgba(0,210,255,0.15); color: #00d2ff;">Processing</span>',
        'completed': '<span style="padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: rgba(46,213,115,0.15); color: #2ed573;">Completed</span>',
        'rejected': '<span style="padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: rgba(255,99,72,0.15); color: #ff6348;">Rejected</span>'
    };
    
    tbody.innerHTML = payoutData.data.map(p => `
        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
            <td style="padding: 12px; color: #ccc; font-size: 0.85rem;">${formatDate(p.requested_at)}</td>
            <td style="padding: 12px; text-align: right; color: #fff; font-weight: 600;">Rs ${formatNumber(p.amount)}</td>
            <td style="padding: 12px; color: #aaa; font-size: 0.85rem;">${p.bank_name || '-'} ${p.account_number ? '(**' + p.account_number.slice(-4) + ')' : ''}</td>
            <td style="padding: 12px; text-align: center;">${statusBadges[p.status] || p.status}</td>
            <td style="padding: 12px; color: #aaa; font-size: 0.85rem;">${p.transaction_reference || (p.rejection_reason ? '<span style="color: #ff6348;">' + p.rejection_reason + '</span>' : '-')}</td>
        </tr>
    `).join('');
}

function openPayoutModal() {
    if (!earningsData) return;
    
    const modal = document.getElementById('payoutModal');
    modal.style.display = 'flex';
    
    const available = earningsData.summary.available_balance;
    const minPayout = earningsData.summary.min_payout_amount;
    
    document.getElementById('modalAvailableBalance').textContent = 'Rs ' + formatNumber(available);
    document.getElementById('modalMinPayout').textContent = 'Rs ' + formatNumber(minPayout);
    document.getElementById('payoutAmount').value = available;
    document.getElementById('payoutAmount').max = available;
    
    // Show bank summary
    const bankName = document.getElementById('bankName').value;
    const accNo = document.getElementById('accountNumber').value;
    const accHolder = document.getElementById('accountHolder').value;
    
    if (bankName && accNo) {
        document.getElementById('payoutBankSummary').innerHTML = 
            '<i class="fas fa-university" style="margin-right: 6px;"></i> Payout to: ' + bankName + ' (**' + accNo.slice(-4) + ') - ' + accHolder;
    } else {
        document.getElementById('payoutBankSummary').innerHTML = 
            '<i class="fas fa-exclamation-triangle" style="color: #ffd32a; margin-right: 6px;"></i> Please save your bank details first before requesting a payout.';
    }
}

function closePayoutModal() {
    document.getElementById('payoutModal').style.display = 'none';
}

async function submitPayoutRequest() {
    const amount = parseFloat(document.getElementById('payoutAmount').value);
    
    if (!amount || amount <= 0) {
        showToast('Please enter a valid amount', 'error');
        return;
    }
    
    const btn = document.getElementById('submitPayoutBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    try {
        const response = await fetch(`${BASE_URL}/api/shop-owner/request-payout`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                amount: amount,
                bank_name: document.getElementById('bankName').value,
                account_number: document.getElementById('accountNumber').value,
                account_holder: document.getElementById('accountHolder').value,
                branch_name: document.getElementById('branchName').value
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message, 'success');
            closePayoutModal();
            loadEarningsData();
        } else {
            showToast(data.message, 'error');
        }
    } catch (err) {
        showToast('Failed to submit payout request', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Submit Request';
    }
}

async function savePayoutDetails(event) {
    event.preventDefault();
    
    try {
        const response = await fetch(`${BASE_URL}/api/shop-owner/payout-details`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                bank_name: document.getElementById('bankName').value,
                account_number: document.getElementById('accountNumber').value,
                account_holder: document.getElementById('accountHolder').value,
                branch_name: document.getElementById('branchName').value
            })
        });
        
        const data = await response.json();
        showToast(data.message, data.success ? 'success' : 'error');
    } catch (err) {
        showToast('Failed to save bank details', 'error');
    }
}

function formatNumber(num) {
    return parseFloat(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.cssText = `position: fixed; top: 20px; right: 20px; padding: 14px 24px; border-radius: 10px; color: white; font-weight: 500; z-index: 9999; animation: slideIn 0.3s ease; max-width: 400px;`;
    toast.style.background = type === 'success' ? 'linear-gradient(135deg, #2ed573, #17c964)' : type === 'error' ? 'linear-gradient(135deg, #ff6348, #ff4757)' : 'linear-gradient(135deg, #00d2ff, #7b2ff7)';
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}" style="margin-right: 8px;"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 3500);
}
</script>

<style>
.earnings-loading { text-align: center; padding: 60px 20px; color: #aaa; }
.earnings-loading .loading-spinner { margin-bottom: 1rem; }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@media (max-width: 768px) {
    #payoutDetailsForm { grid-template-columns: 1fr !important; }
    .stats-grid { grid-template-columns: 1fr 1fr !important; }
}
@media (max-width: 480px) {
    .stats-grid { grid-template-columns: 1fr !important; }
}
</style>
