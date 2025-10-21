<link rel="stylesheet" href="/public/css/pages/admin-dashboard.css">
<link rel="stylesheet" href="/public/css/pages/admin-users.css">

<div class="admin-dashboard">
    <?php 
    // Set active page and optional badge counts
    $activePage = 'users';
    $userCount = 892;
    $groundCount = 45;
    $coachCount = 23;
    $shopCount = 156;
    
    // Include the reusable sidebar component
    include __DIR__ . '/../components/admin-sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title">User Management</h1>
            </div>
            <div class="header-right">
                <div class="admin-profile">
                    <div class="profile-info">
                        <span class="profile-name">Admin User</span>
                        <span class="profile-role">Super Admin</span>
                    </div>
                    <img src="/public/assets/images/default-avatar.png" alt="Admin" class="profile-avatar">
                </div>
            </div>
        </header>

        <!-- User Management Content -->
        <div class="admin-users-container">
            <!-- Page Header -->
            <div class="page-header">
                <div class="header-content">
                    <h1 class="page-title">
                        <i class="fas fa-users"></i>
                        User Management
                    </h1>
                    <p class="page-subtitle">Manage all users, roles, and permissions</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="exportUsers()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                    <button class="btn btn-primary" onclick="showAddUserModal()">
                        <i class="fas fa-user-plus"></i>
                        Add User
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid" id="statsGrid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value" id="totalUsers">-</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span id="totalUsersChange">-</span> this month
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Active Users</div>
                        <div class="stat-value" id="activeUsers">-</div>
                        <div class="stat-change">
                            <i class="fas fa-circle"></i>
                            <span id="active24h">-</span> active today
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">New Users</div>
                        <div class="stat-value" id="newUsers">-</div>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span id="weekSignups">-</span> this week
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-label">Coaches</div>
                        <div class="stat-value" id="coachCount">-</div>
                        <div class="stat-change">
                            <i class="fas fa-store"></i>
                            <span id="shopOwnerCount">-</span> shop owners
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="filters-section">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search users by name, email, phone..." />
                </div>

                <div class="filters">
                    <select id="userTypeFilter" class="filter-select">
                        <option value="">All User Types</option>
                        <option value="user">Regular Users</option>
                        <option value="admin">Admins</option>
                        <option value="coach">Coaches</option>
                        <option value="ground_owner">Ground Owners</option>
                        <option value="shop_owner">Shop Owners</option>
                    </select>

                    <select id="statusFilter" class="filter-select">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>

                    <select id="sortBy" class="filter-select">
                        <option value="created_at">Newest First</option>
                        <option value="first_name">Name (A-Z)</option>
                        <option value="email">Email</option>
                        <option value="user_type">User Type</option>
                    </select>

                    <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
                        <i class="fas fa-redo"></i>
                        Reset
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="users-table-container">
                <div class="table-header">
                    <h3>Users List</h3>
                    <div class="table-actions">
                        <span class="result-count" id="resultCount">Showing 0 users</span>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="users-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" />
                                </th>
                                <th>User</th>
                                <th>Email / Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Activity</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr>
                                <td colspan="9" class="loading-cell">
                                    <div class="loading-spinner">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <span>Loading users...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container" id="paginationContainer">
                    <div class="pagination-info">
                        <span id="paginationInfo">-</span>
                    </div>
                    <div class="pagination-controls" id="paginationControls">
                        <!-- Pagination buttons will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-user-edit"></i>
                Edit User
            </h3>
            <button class="close-modal" onclick="closeModal('editUserModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                <input type="hidden" id="editUserId" />
                
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" id="editFirstName" class="form-control" readonly />
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="editLastName" class="form-control" readonly />
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="editEmail" class="form-control" readonly />
                </div>

                <div class="form-group">
                    <label>User Type / Role</label>
                    <select id="editUserType" class="form-control">
                        <option value="user">Regular User</option>
                        <option value="admin">Admin</option>
                        <option value="coach">Coach</option>
                        <option value="ground_owner">Ground Owner</option>
                        <option value="shop_owner">Shop Owner</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select id="editStatus" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <div class="form-group" id="statusReasonGroup" style="display: none;">
                    <label>Reason for Status Change</label>
                    <textarea id="statusReason" class="form-control" rows="3" placeholder="Enter reason for suspension or status change..."></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveUserChanges()">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- User Details Modal -->
<div class="modal" id="userDetailsModal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>
                <i class="fas fa-user-circle"></i>
                User Details
            </h3>
            <button class="close-modal" onclick="closeModal('userDetailsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="userDetailsContent">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading user details...</span>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal" id="resetPasswordModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-key"></i>
                Reset Password
            </h3>
            <button class="close-modal" onclick="closeModal('resetPasswordModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="resetPasswordForm">
                <input type="hidden" id="resetUserId" />
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>This will reset the user's password. They will need to use the new password to login.</span>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" id="newPassword" class="form-control" placeholder="Enter new password (min 8 characters)" />
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm new password" />
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('resetPasswordModal')">Cancel</button>
            <button class="btn btn-danger" onclick="confirmPasswordReset()">
                <i class="fas fa-key"></i>
                Reset Password
            </button>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal" id="deleteUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-exclamation-triangle"></i>
                Delete User
            </h3>
            <button class="close-modal" onclick="closeModal('deleteUserModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span>Are you sure you want to delete this user? This action cannot be undone.</span>
            </div>
            <input type="hidden" id="deleteUserId" />
            <p id="deleteUserInfo"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('deleteUserModal')">Cancel</button>
            <button class="btn btn-danger" onclick="confirmUserDeletion()">
                <i class="fas fa-trash"></i>
                Delete User
            </button>
        </div>
    </div>
</div>

<script src="/public/js/pages/admin-users.js"></script>