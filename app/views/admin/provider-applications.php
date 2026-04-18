<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Provider Applications - Admin Dashboard';
$activePage = 'registrations';
$additionalCSS = [
    '/public/css/pages/admin-dashboard.css',
    '/public/css/pages/admin-applications.css'
];
$additionalJS = ['/public/js/admin-applications.js'];
?>

<div class="admin-dashboard">
    <!-- Sidebar -->
    <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title">Provider Applications</h1>
                <p class="page-subtitle">Review and approve provider applications</p>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 id="pendingCount">0</h3>
                    <p>Pending Applications</p>
                </div>
            </div>

            <div class="stat-card approved">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="approvedCount">0</h3>
                    <p>Approved Today</p>
                </div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="rejectedCount">0</h3>
                    <p>Rejected</p>
                </div>
            </div>

            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 id="totalCount">0</h3>
                    <p>Total Applications</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <div class="filter-group">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending" selected>Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="typeFilter">Provider Type:</label>
                <select id="typeFilter" class="filter-select">
                    <option value="">All Types</option>
                    <option value="ground_owner">Ground Owner</option>
                    <option value="coach">Coach</option>
                    <option value="shop_owner">Shop Owner</option>
                </select>
            </div>

            <div class="filter-group search-group">
                <input type="text" id="searchInput" placeholder="Search by name or email..." class="search-input">
                <button class="search-btn" onclick="loadApplications()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="table-container">
            <table class="applications-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Provider Type</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="applicationsTableBody">
                    <tr>
                        <td colspan="6" class="loading-row">
                            <div class="loading-spinner"></div>
                            <p>Loading applications...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination" id="pagination"></div>
    </main>
</div>

<!-- Application Detail Modal -->
<div id="applicationModal" class="modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3>Application Details</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="applicationDetails">
            <!-- Details will be loaded here -->
        </div>
        <div class="modal-footer">
            <button class="btn-reject" onclick="showRejectForm()">
                <i class="fas fa-times"></i> Reject
            </button>
            <button class="btn-approve" onclick="approveApplication()">
                <i class="fas fa-check"></i> Approve
            </button>
        </div>
    </div>
</div>

<!-- Reject Reason Modal -->
<div id="rejectModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reject Application</h3>
            <button class="modal-close" onclick="closeRejectModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="rejectionReason">Reason for Rejection *</label>
                <textarea id="rejectionReason" rows="5" placeholder="Please provide a reason for rejecting this application..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeRejectModal()">Cancel</button>
            <button class="btn-reject" onclick="rejectApplication()">
                <i class="fas fa-times"></i> Confirm Rejection
            </button>
        </div>
    </div>
</div>

