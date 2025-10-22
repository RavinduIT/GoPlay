<?php
/**
 * Admin Payments & Earnings Page
 * Displays platform earnings, service fees, and withdrawal management
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments & Earnings - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/pages/admin-dashboard.css">
   <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #2d3748;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .admin-main {
            flex: 1;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }

        .admin-header {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #1a202c;
        }

        .page-subtitle {
            color: #718096;
            font-size: 14px;
            margin-top: 4px;
        }

        .content {
            padding: 30px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
        .stat-icon.success { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: #fff; }
        .stat-icon.warning { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); color: #fff; }
        .stat-icon.info { background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); color: #fff; }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 4px;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 13px;
        }

        .trend-up { color: #48bb78; }
        .trend-down { color: #f56565; }

        /* Tabs */
        .tabs {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .tab-buttons {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .tab-button {
            padding: 12px 24px;
            background: none;
            border: none;
            color: #718096;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            transition: color 0.2s;
        }

        .tab-button.active {
            color: #667eea;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Settings Section */
        .settings-section {
            background: #f7fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .settings-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .settings-row:last-child {
            border-bottom: none;
        }

        .settings-label {
            font-weight: 600;
            color: #2d3748;
        }

        .settings-description {
            color: #718096;
            font-size: 14px;
            margin-top: 4px;
        }

        .settings-input {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="number"] {
            padding: 8px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
            width: 100px;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: #48bb78;
            color: #fff;
        }

        .btn-success:hover {
            background: #38a169;
        }

        .btn-danger {
            background: #f56565;
            color: #fff;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f7fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        tr:hover {
            background: #f7fafc;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-completed { background: #c6f6d5; color: #22543d; }
        .status-pending { background: #feebc8; color: #7c2d12; }
        .status-withdrawn { background: #bee3f8; color: #2c5282; }
        .status-cancelled { background: #fed7d7; color: #742a2a; }
        .status-processing { background: #e9d8fd; color: #44337a; }
        .status-rejected { background: #fed7d7; color: #742a2a; }

        /* Filters */
        .filters {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-label {
            font-size: 12px;
            color: #718096;
            font-weight: 600;
        }

        select, input[type="date"], input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #a0aec0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea.form-input {
            min-height: 100px;
            resize: vertical;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        /* Chart Container */
        .chart-container {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #718096;
        }

        .spinner {
            border: 3px solid #e2e8f0;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .admin-main {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filters {
                flex-direction: column;
            }

            .modal-content {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>
        
        <div class="admin-main">
            <div class="admin-header">
                <div class="header-content">
                    <div>
                        <h1 class="page-title">Payments & Earnings</h1>
                        <p class="page-subtitle">Manage platform earnings, service fees, and withdrawals</p>
                    </div>
                    <button class="btn btn-primary" onclick="openWithdrawalModal()">
                        <i class="fas fa-money-bill-wave"></i>
                        Request Withdrawal
                    </button>
                </div>
            </div>

            <div class="content">
                <!-- Alert Messages -->
                <div id="successAlert" class="alert alert-success"></div>
                <div id="errorAlert" class="alert alert-error"></div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="totalEarnings">LKR 0</div>
                                <div class="stat-label">Total Earnings</div>
                                <div class="stat-trend trend-up" id="earningsTrend">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>0% from last month</span>
                                </div>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="availableBalance">LKR 0</div>
                                <div class="stat-label">Available Balance</div>
                                <div class="stat-trend">
                                    <i class="fas fa-wallet"></i>
                                    <span>Ready to withdraw</span>
                                </div>
                            </div>
                            <div class="stat-icon success">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="thisMonthEarnings">LKR 0</div>
                                <div class="stat-label">This Month</div>
                                <div class="stat-trend">
                                    <i class="fas fa-calendar"></i>
                                    <span id="transactionCount">0 transactions</span>
                                </div>
                            </div>
                            <div class="stat-icon info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div>
                                <div class="stat-value" id="pendingAmount">LKR 0</div>
                                <div class="stat-label">Pending Earnings</div>
                                <div class="stat-trend">
                                    <i class="fas fa-clock"></i>
                                    <span>Processing</span>
                                </div>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Fee Settings -->
                <div class="tabs">
                    <h3 style="margin-bottom: 16px; font-size: 18px;">Service Fee Configuration</h3>
                    <div class="settings-section">
                        <div class="settings-row">
                            <div>
                                <div class="settings-label">Platform Service Fee</div>
                                <div class="settings-description">Percentage charged on each transaction</div>
                            </div>
                            <div class="settings-input">
                                <div class="input-group">
                                    <input type="number" id="serviceFeeInput" min="0" max="100" step="0.01" value="5.00">
                                    <span>%</span>
                                </div>
                                <button class="btn btn-primary" onclick="updateServiceFee()">
                                    <i class="fas fa-save"></i> Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Tabs -->
                <div class="tabs">
                    <div class="tab-buttons">
                        <button class="tab-button active" onclick="switchTab('earnings')">
                            <i class="fas fa-chart-bar"></i> Earnings History
                        </button>
                        <button class="tab-button" onclick="switchTab('withdrawals')">
                            <i class="fas fa-money-bill-transfer"></i> Withdrawals
                        </button>
                        <button class="tab-button" onclick="switchTab('breakdown')">
                            <i class="fas fa-pie-chart"></i> Breakdown
                        </button>
                    </div>

                    <!-- Earnings Tab -->
                    <div id="earnings-tab" class="tab-content active">
                        <div class="filters">
                            <div class="filter-group">
                                <label class="filter-label">Status</label>
                                <select id="earningsStatusFilter" onchange="loadEarningsHistory()">
                                    <option value="">All Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                    <option value="withdrawn">Withdrawn</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">Type</label>
                                <select id="earningsTypeFilter" onchange="loadEarningsHistory()">
                                    <option value="">All Types</option>
                                    <option value="product">Product Sales</option>
                                    <option value="facility">Facility Bookings</option>
                                    <option value="coach">Coach Bookings</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">From Date</label>
                                <input type="date" id="earningsDateFrom" onchange="loadEarningsHistory()">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">To Date</label>
                                <input type="date" id="earningsDateTo" onchange="loadEarningsHistory()">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">Search</label>
                                <input type="text" id="earningsSearch" placeholder="Search..." onkeyup="debounceSearch()">
                            </div>
                            <div class="filter-group" style="justify-content: flex-end;">
                                <label class="filter-label">&nbsp;</label>
                                <button class="btn btn-secondary" onclick="exportEarnings()">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </div>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Transaction</th>
                                        <th>Fee %</th>
                                        <th>Service Fee</th>
                                        <th>Merchant</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="earningsTableBody">
                                    <tr>
                                        <td colspan="8" class="loading">
                                            <div class="spinner"></div>
                                            Loading earnings data...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="earningsPagination" style="margin-top: 20px; text-align: center;"></div>
                    </div>

                    <!-- Withdrawals Tab -->
                    <div id="withdrawals-tab" class="tab-content">
                        <div class="filters">
                            <div class="filter-group">
                                <label class="filter-label">Status</label>
                                <select id="withdrawalStatusFilter" onchange="loadWithdrawals()">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">From Date</label>
                                <input type="date" id="withdrawalDateFrom" onchange="loadWithdrawals()">
                            </div>
                            <div class="filter-group">
                                <label class="filter-label">To Date</label>
                                <input type="date" id="withdrawalDateTo" onchange="loadWithdrawals()">
                            </div>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Details</th>
                                        <th>Status</th>
                                        <th>Processed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="withdrawalsTableBody">
                                    <tr>
                                        <td colspan="7" class="loading">
                                            <div class="spinner"></div>
                                            Loading withdrawals...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="withdrawalsPagination" style="margin-top: 20px; text-align: center;"></div>
                    </div>

                    <!-- Breakdown Tab -->
                    <div id="breakdown-tab" class="tab-content">
                        <div class="chart-container">
                            <div class="chart-header">
                                <h3 class="chart-title">Earnings by Type</h3>
                                <select id="breakdownPeriod" onchange="loadBreakdown()">
                                    <option value="7">Last 7 Days</option>
                                    <option value="30" selected>Last 30 Days</option>
                                    <option value="90">Last 90 Days</option>
                                    <option value="365">Last Year</option>
                                </select>
                            </div>
                            <div id="breakdownChart" style="height: 300px;">
                                <div class="loading">
                                    <div class="spinner"></div>
                                    Loading breakdown data...
                                </div>
                            </div>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-card">
                                <h4 style="margin-bottom: 12px; color: #718096;">Product Sales</h4>
                                <div class="stat-value" id="productEarnings">LKR 0</div>
                                <div class="stat-label" id="productCount">0 transactions</div>
                            </div>
                            <div class="stat-card">
                                <h4 style="margin-bottom: 12px; color: #718096;">Facility Bookings</h4>
                                <div class="stat-value" id="facilityEarnings">LKR 0</div>
                                <div class="stat-label" id="facilityCount">0 transactions</div>
                            </div>
                            <div class="stat-card">
                                <h4 style="margin-bottom: 12px; color: #718096;">Coach Bookings</h4>
                                <div class="stat-value" id="coachEarnings">LKR 0</div>
                                <div class="stat-label" id="coachCount">0 transactions</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Modal -->
    <div id="withdrawalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Request Withdrawal</h2>
                <button class="close-modal" onclick="closeWithdrawalModal()">&times;</button>
            </div>

            <form id="withdrawalForm" onsubmit="submitWithdrawal(event)">
                <div class="form-group">
                    <label class="form-label">Amount (LKR)</label>
                    <input type="number" class="form-input" id="withdrawalAmount" required min="1000" step="0.01">
                    <small style="color: #718096;">Available Balance: <span id="modalAvailableBalance">LKR 0</span></small>
                </div>

                <div class="form-group">
                    <label class="form-label">Withdrawal Method</label>
                    <select class="form-input" id="withdrawalMethod" required onchange="toggleMethodFields()">
                        <option value="">Select Method</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="paypal">PayPal</option>
                        <option value="check">Check</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div id="bankTransferFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" class="form-input" id="bankName">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Number</label>
                        <input type="text" class="form-input" id="accountNumber">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Holder Name</label>
                        <input type="text" class="form-input" id="accountHolder">
                    </div>
                </div>

                <div id="paypalFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">PayPal Email</label>
                        <input type="email" class="form-input" id="paypalEmail">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-input" id="withdrawalNotes"></textarea>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeWithdrawalModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentEarningsPage = 1;
        let currentWithdrawalsPage = 1;
        let searchTimeout;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadOverviewStats();
            loadEarningsHistory();
            loadWithdrawals();
        });

        // Load overview statistics
        async function loadOverviewStats() {
            try {
                const response = await fetch('/api/admin/payments/overview');
                const result = await response.json();

                if (result.success) {
                    const data = result.data;

                    document.getElementById('totalEarnings').textContent = formatCurrency(data.total_earnings);
                    document.getElementById('availableBalance').textContent = formatCurrency(data.available_balance);
                    document.getElementById('thisMonthEarnings').textContent = formatCurrency(data.this_month_earnings);
                    document.getElementById('pendingAmount').textContent = formatCurrency(data.pending_earnings);
                    document.getElementById('transactionCount').textContent = data.total_transactions + ' transactions';
                    document.getElementById('serviceFeeInput').value = data.service_fee_percentage;
                    document.getElementById('modalAvailableBalance').textContent = formatCurrency(data.available_balance);

                    // Update trend
                    const trend = data.growth_percentage;
                    const trendElement = document.getElementById('earningsTrend');
                    if (trend > 0) {
                        trendElement.className = 'stat-trend trend-up';
                        trendElement.innerHTML = `<i class="fas fa-arrow-up"></i><span>+${trend}% from last month</span>`;
                    } else if (trend < 0) {
                        trendElement.className = 'stat-trend trend-down';
                        trendElement.innerHTML = `<i class="fas fa-arrow-down"></i><span>${trend}% from last month</span>`;
                    }
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Load earnings history
        async function loadEarningsHistory(page = 1) {
            currentEarningsPage = page;
            
            const status = document.getElementById('earningsStatusFilter').value;
            const type = document.getElementById('earningsTypeFilter').value;
            const dateFrom = document.getElementById('earningsDateFrom').value;
            const dateTo = document.getElementById('earningsDateTo').value;
            const search = document.getElementById('earningsSearch').value;

            let url = `/api/admin/payments/earnings?page=${page}`;
            if (status) url += `&status=${status}`;
            if (type) url += `&booking_type=${type}`;
            if (dateFrom) url += `&date_from=${dateFrom}`;
            if (dateTo) url += `&date_to=${dateTo}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    displayEarningsTable(result.data);
                    displayPagination(result.pagination, 'earnings');
                }
            } catch (error) {
                console.error('Error loading earnings:', error);
                document.getElementById('earningsTableBody').innerHTML = 
                    '<tr><td colspan="8" style="text-align: center; color: #f56565;">Error loading data</td></tr>';
            }
        }

        // Display earnings table
        function displayEarningsTable(data) {
            const tbody = document.getElementById('earningsTableBody');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align: center;">No earnings found</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(item => `
                <tr>
                    <td>${formatDate(item.created_at)}</td>
                    <td>${item.reference_number || 'N/A'}</td>
                    <td><span class="status-badge">${formatType(item.booking_type)}</span></td>
                    <td>${formatCurrency(item.transaction_amount)}</td>
                    <td>${item.service_fee_percentage}%</td>
                    <td><strong>${formatCurrency(item.service_fee_amount)}</strong></td>
                    <td>${item.merchant_first_name || ''} ${item.merchant_last_name || ''}</td>
                    <td><span class="status-badge status-${item.status}">${item.status}</span></td>
                </tr>
            `).join('');
        }

        // Load withdrawals
        async function loadWithdrawals(page = 1) {
            currentWithdrawalsPage = page;
            
            const status = document.getElementById('withdrawalStatusFilter').value;
            const dateFrom = document.getElementById('withdrawalDateFrom').value;
            const dateTo = document.getElementById('withdrawalDateTo').value;

            let url = `/api/admin/payments/withdrawals?page=${page}`;
            if (status) url += `&status=${status}`;
            if (dateFrom) url += `&date_from=${dateFrom}`;
            if (dateTo) url += `&date_to=${dateTo}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    displayWithdrawalsTable(result.data);
                    displayPagination(result.pagination, 'withdrawals');
                }
            } catch (error) {
                console.error('Error loading withdrawals:', error);
            }
        }

        // Display withdrawals table
        function displayWithdrawalsTable(data) {
            const tbody = document.getElementById('withdrawalsTableBody');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">No withdrawals found</td></tr>';
                return;
            }

            tbody.innerHTML = data.map(item => `
                <tr>
                    <td>${formatDate(item.requested_at)}</td>
                    <td><strong>${formatCurrency(item.amount)}</strong></td>
                    <td>${formatMethod(item.withdrawal_method)}</td>
                    <td>${getWithdrawalDetails(item)}</td>
                    <td><span class="status-badge status-${item.status}">${item.status}</span></td>
                    <td>${item.processed_at ? formatDate(item.processed_at) : '-'}</td>
                    <td>
                        ${item.status === 'pending' ? `
                            <button class="btn btn-danger" onclick="cancelWithdrawal(${item.id})">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : '-'}
                    </td>
                </tr>
            `).join('');
        }

        // Load breakdown
        async function loadBreakdown() {
            const days = document.getElementById('breakdownPeriod').value;
            const dateFrom = new Date();
            dateFrom.setDate(dateFrom.getDate() - days);

            try {
                const response = await fetch(`/api/admin/payments/breakdown?date_from=${dateFrom.toISOString().split('T')[0]}`);
                const result = await response.json();

                if (result.success) {
                    displayBreakdown(result.data);
                }
            } catch (error) {
                console.error('Error loading breakdown:', error);
            }
        }

        // Display breakdown
        function displayBreakdown(data) {
            let productData = data.find(d => d.booking_type === 'product') || {};
            let facilityData = data.find(d => d.booking_type === 'facility') || {};
            let coachData = data.find(d => d.booking_type === 'coach') || {};

            document.getElementById('productEarnings').textContent = formatCurrency(productData.total_fees || 0);
            document.getElementById('productCount').textContent = (productData.transaction_count || 0) + ' transactions';

            document.getElementById('facilityEarnings').textContent = formatCurrency(facilityData.total_fees || 0);
            document.getElementById('facilityCount').textContent = (facilityData.transaction_count || 0) + ' transactions';

            document.getElementById('coachEarnings').textContent = formatCurrency(coachData.total_fees || 0);
            document.getElementById('coachCount').textContent = (coachData.transaction_count || 0) + ' transactions';

            // Simple visualization
            const total = (parseFloat(productData.total_fees || 0) + parseFloat(facilityData.total_fees || 0) + parseFloat(coachData.total_fees || 0));
            document.getElementById('breakdownChart').innerHTML = `
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    ${createBar('Products', productData.total_fees || 0, total, '#667eea')}
                    ${createBar('Facilities', facilityData.total_fees || 0, total, '#48bb78')}
                    ${createBar('Coaches', coachData.total_fees || 0, total, '#ed8936')}
                </div>
            `;
        }

        function createBar(label, value, total, color) {
            const percentage = total > 0 ? (value / total * 100).toFixed(1) : 0;
            return `
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 600;">${label}</span>
                        <span>${formatCurrency(value)} (${percentage}%)</span>
                    </div>
                    <div style="background: #e2e8f0; height: 32px; border-radius: 6px; overflow: hidden;">
                        <div style="background: ${color}; height: 100%; width: ${percentage}%; transition: width 0.3s;"></div>
                    </div>
                </div>
            `;
        }

        // Update service fee
        async function updateServiceFee() {
            const percentage = parseFloat(document.getElementById('serviceFeeInput').value);

            try {
                const response = await fetch('/api/admin/payments/service-fee', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ percentage })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Service fee updated successfully', 'success');
                    loadOverviewStats();
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error updating service fee', 'error');
            }
        }

        // Withdrawal modal functions
        function openWithdrawalModal() {
            document.getElementById('withdrawalModal').classList.add('active');
        }

        function closeWithdrawalModal() {
            document.getElementById('withdrawalModal').classList.remove('active');
            document.getElementById('withdrawalForm').reset();
        }

        function toggleMethodFields() {
            const method = document.getElementById('withdrawalMethod').value;
            document.getElementById('bankTransferFields').style.display = method === 'bank_transfer' ? 'block' : 'none';
            document.getElementById('paypalFields').style.display = method === 'paypal' ? 'block' : 'none';
        }

        // Submit withdrawal
        async function submitWithdrawal(event) {
            event.preventDefault();

            const data = {
                amount: parseFloat(document.getElementById('withdrawalAmount').value),
                withdrawal_method: document.getElementById('withdrawalMethod').value,
                bank_name: document.getElementById('bankName').value,
                account_number: document.getElementById('accountNumber').value,
                account_holder: document.getElementById('accountHolder').value,
                paypal_email: document.getElementById('paypalEmail').value,
                notes: document.getElementById('withdrawalNotes').value
            };

            try {
                const response = await fetch('/api/admin/payments/withdrawal', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Withdrawal request submitted successfully', 'success');
                    closeWithdrawalModal();
                    loadWithdrawals();
                    loadOverviewStats();
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error submitting withdrawal request', 'error');
            }
        }

        // Cancel withdrawal
        async function cancelWithdrawal(id) {
            if (!confirm('Are you sure you want to cancel this withdrawal request?')) return;

            try {
                const response = await fetch(`/api/admin/payments/withdrawal/${id}/cancel`, {
                    method: 'PUT'
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Withdrawal cancelled successfully', 'success');
                    loadWithdrawals();
                    loadOverviewStats();
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error cancelling withdrawal', 'error');
            }
        }

        // Export earnings
        function exportEarnings() {
            const dateFrom = document.getElementById('earningsDateFrom').value;
            const dateTo = document.getElementById('earningsDateTo').value;
            
            let url = '/api/admin/payments/export?format=csv';
            if (dateFrom) url += `&date_from=${dateFrom}`;
            if (dateTo) url += `&date_to=${dateTo}`;
            
            window.open(url, '_blank');
        }

        // Tab switching
        function switchTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(`${tab}-tab`).classList.add('active');

            if (tab === 'breakdown') {
                loadBreakdown();
            }
        }

        // Pagination
        function displayPagination(pagination, type) {
            const container = document.getElementById(`${type}Pagination`);
            if (!pagination || pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            for (let i = 1; i <= pagination.total_pages; i++) {
                html += `
                    <button class="btn ${i === pagination.current_page ? 'btn-primary' : 'btn-secondary'}" 
                            onclick="${type === 'earnings' ? 'loadEarningsHistory' : 'loadWithdrawals'}(${i})"
                            style="margin: 0 4px;">
                        ${i}
                    </button>
                `;
            }
            container.innerHTML = html;
        }

        // Debounce search
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => loadEarningsHistory(), 500);
        }

        // Helper functions
        function formatCurrency(amount) {
            return 'LKR ' + parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }

        function formatType(type) {
            if (!type) return 'N/A';
            return type.charAt(0).toUpperCase() + type.slice(1);
        }

        function formatMethod(method) {
            if (!method) return 'N/A';
            return method.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        function getWithdrawalDetails(item) {
            if (item.withdrawal_method === 'bank_transfer') {
                return `${item.bank_name || ''} - ${item.account_number || ''}`;
            } else if (item.withdrawal_method === 'paypal') {
                return item.paypal_email || '';
            }
            return item.notes || '-';
        }

        function showAlert(message, type) {
            const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
            const alertEl = document.getElementById(alertId);
            alertEl.textContent = message;
            alertEl.classList.add('show');
            
            setTimeout(() => {
                alertEl.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>