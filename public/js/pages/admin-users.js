// Admin Users Management JavaScript

let currentPage = 1;
let totalPages = 1;
let usersData = [];
let statisticsData = {};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    loadUsers();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Search input with debounce
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadUsers();
        }, 500);
    });

    // Filter change events
    document.getElementById('userTypeFilter').addEventListener('change', () => {
        currentPage = 1;
        loadUsers();
    });

    document.getElementById('statusFilter').addEventListener('change', () => {
        currentPage = 1;
        loadUsers();
    });

    document.getElementById('sortBy').addEventListener('change', () => {
        currentPage = 1;
        loadUsers();
    });

    // Status change listener for edit modal
    document.getElementById('editStatus').addEventListener('change', function() {
        const statusReasonGroup = document.getElementById('statusReasonGroup');
        if (this.value === 'suspended') {
            statusReasonGroup.style.display = 'block';
        } else {
            statusReasonGroup.style.display = 'none';
        }
    });
}

// Load statistics
async function loadStatistics() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/users/statistics');
        const data = await response.json();

        if (data.success) {
            statisticsData = data.statistics;
            displayStatistics(statisticsData);
        }
    } catch (error) {
        console.error('Failed to load statistics:', error);
    }
}

// Display statistics
function displayStatistics(stats) {
    document.getElementById('totalUsers').textContent = formatNumber(stats.total_users);
    document.getElementById('activeUsers').textContent = formatNumber(stats.active_users);
    document.getElementById('newUsers').textContent = formatNumber(stats.month_signups);
    document.getElementById('coachCount').textContent = formatNumber(stats.coaches);
    
    document.getElementById('totalUsersChange').textContent = formatNumber(stats.month_signups);
    document.getElementById('active24h').textContent = formatNumber(stats.active_24h);
    document.getElementById('weekSignups').textContent = formatNumber(stats.week_signups);
    document.getElementById('shopOwnerCount').textContent = formatNumber(stats.shop_owners);
}

// Load users
async function loadUsers() {
    const tableBody = document.getElementById('usersTableBody');
    tableBody.innerHTML = `
        <tr>
            <td colspan="9" class="loading-cell">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Loading users...</span>
                </div>
            </td>
        </tr>
    `;

    try {
        const search = document.getElementById('searchInput').value;
        const userType = document.getElementById('userTypeFilter').value;
        const status = document.getElementById('statusFilter').value;
        const sortBy = document.getElementById('sortBy').value;

        const params = new URLSearchParams({
            page: currentPage,
            limit: 20,
            search: search,
            user_type: userType,
            status: status,
            sort_by: sortBy,
            sort_order: 'DESC'
        });

        const response = await fetch(`${window.BASE_URL||""}/api/admin/users?${params}`);
        const data = await response.json();

        if (data.success) {
            usersData = data.users;
            totalPages = data.pagination.pages;
            displayUsers(data.users);
            updatePagination(data.pagination);
        } else {
            showError('Failed to load users');
        }
    } catch (error) {
        console.error('Failed to load users:', error);
        showError('Failed to load users');
    }
}

// Display users in table
function displayUsers(users) {
    const tableBody = document.getElementById('usersTableBody');
    
    if (users.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="loading-cell">
                    <div style="text-align: center; color: #64748b;">
                        <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>No users found</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('resultCount').textContent = 'No users found';
        return;
    }

    tableBody.innerHTML = users.map(user => `
        <tr>
            <td>
                <input type="checkbox" class="user-checkbox" value="${user.id}" />
            </td>
            <td>
                <div class="user-info">
                    <div class="user-avatar">
                        ${user.profile_picture ? 
                            `<img src="${(window.BASE_URL||'')+'/'+user.profile_picture.replace(/^\//,'')}" alt="${user.first_name}" style="width: 100%; height: 100%; border-radius: 50%;" />` :
                            `${user.first_name.charAt(0)}${user.last_name.charAt(0)}`
                        }
                    </div>
                    <div class="user-details">
                        <span class="user-name">${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}</span>
                        <span class="user-username">@${escapeHtml(user.username)}</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="user-contact">
                    <span class="contact-item">
                        <i class="fas fa-envelope"></i>
                        ${escapeHtml(user.email)}
                    </span>
                    ${user.phone ? `
                        <span class="contact-item">
                            <i class="fas fa-phone"></i>
                            ${escapeHtml(user.phone)}
                        </span>
                    ` : ''}
                </div>
            </td>
            <td>
                <span class="role-badge ${user.user_type}">
                    ${getRoleIcon(user.user_type)}
                    ${formatRole(user.user_type)}
                </span>
            </td>
            <td>
                <span class="status-badge ${user.status}">
                    <i class="fas fa-circle"></i>
                    ${capitalizeFirst(user.status)}
                </span>
            </td>
            <td>
                ${user.last_login_at ? `
                    <div class="last-login">
                        <span class="login-time">${formatDateTime(user.last_login_at)}</span>
                        <span class="login-ip">
                            <i class="fas fa-map-marker-alt"></i>
                            ${user.last_login_ip || 'N/A'}
                        </span>
                    </div>
                ` : '<span class="never-logged-in">Never logged in</span>'}
            </td>
            <td>
                <div class="activity-info">
                    <span class="activity-stat">
                        <i class="fas fa-calendar"></i>
                        ${user.booking_count || 0} bookings
                    </span>
                    <span class="activity-stat">
                        <i class="fas fa-shopping-cart"></i>
                        ${user.order_count || 0} orders
                    </span>
                </div>
            </td>
            <td>
                <span style="color: #64748b; font-size: 0.813rem;">
                    ${formatDate(user.created_at)}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn view" onclick="viewUserDetails(${user.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn edit" onclick="editUser(${user.id})" title="Edit User">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn" onclick="resetUserPassword(${user.id})" title="Reset Password">
                        <i class="fas fa-key"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteUser(${user.id})" title="Delete User">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    document.getElementById('resultCount').textContent = `Showing ${users.length} users`;
}

// Update pagination
function updatePagination(pagination) {
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationControls = document.getElementById('paginationControls');

    const start = (pagination.page - 1) * pagination.limit + 1;
    const end = Math.min(pagination.page * pagination.limit, pagination.total);
    
    paginationInfo.textContent = `Showing ${start}-${end} of ${pagination.total} users`;

    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <button class="page-btn" onclick="changePage(${pagination.page - 1})" ${pagination.page === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, pagination.page - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(pagination.pages, startPage + maxVisiblePages - 1);

    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    if (startPage > 1) {
        paginationHTML += `<button class="page-btn" onclick="changePage(1)">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<span style="padding: 0.5rem;">...</span>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button class="page-btn ${i === pagination.page ? 'active' : ''}" onclick="changePage(${i})">
                ${i}
            </button>
        `;
    }

    if (endPage < pagination.pages) {
        if (endPage < pagination.pages - 1) {
            paginationHTML += `<span style="padding: 0.5rem;">...</span>`;
        }
        paginationHTML += `<button class="page-btn" onclick="changePage(${pagination.pages})">${pagination.pages}</button>`;
    }

    // Next button
    paginationHTML += `
        <button class="page-btn" onclick="changePage(${pagination.page + 1})" ${pagination.page === pagination.pages ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    paginationControls.innerHTML = paginationHTML;
}

// Change page
function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadUsers();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// View user details
async function viewUserDetails(userId) {
    const modal = document.getElementById('userDetailsModal');
    const content = document.getElementById('userDetailsContent');
    
    modal.classList.add('active');
    content.innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <span>Loading user details...</span>
        </div>
    `;

    try {
        const response = await fetch(`${window.BASE_URL||""}/api/admin/users/${userId}`);
        const data = await response.json();

        if (data.success) {
            const user = data.user;
            content.innerHTML = `
                <div style="display: grid; gap: 2rem;">
                    <!-- User Profile -->
                    <div>
                        <h4 style="margin-bottom: 1rem; color: #0f172a;">Profile Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Full Name</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}</p>
                            </div>
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Username</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">@${escapeHtml(user.username)}</p>
                            </div>
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Email</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">${escapeHtml(user.email)}</p>
                            </div>
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Phone</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">${escapeHtml(user.phone || 'N/A')}</p>
                            </div>
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">User Type</label>
                                <p style="margin-top: 0.25rem;">
                                    <span class="role-badge ${user.user_type}">${formatRole(user.user_type)}</span>
                                </p>
                            </div>
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Status</label>
                                <p style="margin-top: 0.25rem;">
                                    <span class="status-badge ${user.status}">${capitalizeFirst(user.status)}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Statistics -->
                    <div>
                        <h4 style="margin-bottom: 1rem; color: #0f172a;">Activity Statistics</h4>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;">${user.booking_count || 0}</div>
                                <div style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">Bookings</div>
                            </div>
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">${user.facility_booking_count || 0}</div>
                                <div style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">Facilities</div>
                            </div>
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6;">${user.order_count || 0}</div>
                                <div style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">Orders</div>
                            </div>
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">Rs.${formatNumber(user.total_spent || 0)}</div>
                                <div style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">Total Spent</div>
                            </div>
                        </div>
                    </div>

                    <!-- Login Information -->
                    <div>
                        <h4 style="margin-bottom: 1rem; color: #0f172a;">Login Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Last Login</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">${user.last_login_at ? formatDateTime(user.last_login_at) : 'Never'}</p>
                            </div>
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">IP Address</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">${escapeHtml(user.last_login_ip || 'N/A')}</p>
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <label style="color: #64748b; font-size: 0.875rem;">User Agent</label>
                                <p style="margin-top: 0.25rem; font-size: 0.813rem; color: #64748b;">${escapeHtml(user.user_agent || 'N/A')}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Dates -->
                    <div>
                        <h4 style="margin-bottom: 1rem; color: #0f172a;">Account Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Member Since</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">${formatDate(user.created_at)}</p>
                            </div>
                            <div>
                                <label style="color: #64748b; font-size: 0.875rem;">Last Updated</label>
                                <p style="margin-top: 0.25rem; font-weight: 500;">${formatDate(user.updated_at)}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = `<p style="text-align: center; color: #ef4444;">Failed to load user details</p>`;
        }
    } catch (error) {
        console.error('Failed to load user details:', error);
        content.innerHTML = `<p style="text-align: center; color: #ef4444;">Failed to load user details</p>`;
    }
}

// Edit user
function editUser(userId) {
    const user = usersData.find(u => u.id === userId);
    if (!user) return;

    document.getElementById('editUserId').value = user.id;
    document.getElementById('editFirstName').value = user.first_name;
    document.getElementById('editLastName').value = user.last_name;
    document.getElementById('editEmail').value = user.email;
    document.getElementById('editUserType').value = user.user_type;
    document.getElementById('editStatus').value = user.status;

    if (user.status === 'suspended') {
        document.getElementById('statusReasonGroup').style.display = 'block';
    } else {
        document.getElementById('statusReasonGroup').style.display = 'none';
    }

    document.getElementById('editUserModal').classList.add('active');
}

// Save user changes
async function saveUserChanges() {
    const userId = document.getElementById('editUserId').value;
    const userType = document.getElementById('editUserType').value;
    const status = document.getElementById('editStatus').value;
    const reason = document.getElementById('statusReason').value;

    try {
        // Update role
        const roleResponse = await fetch(`${window.BASE_URL||""}/api/admin/users/${userId}/role`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_type: userType })
        });

        const roleData = await roleResponse.json();

        // Update status
        const statusResponse = await fetch(`${window.BASE_URL||""}/api/admin/users/${userId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: status, reason: reason })
        });

        const statusData = await statusResponse.json();

        if (roleData.success && statusData.success) {
            showSuccess('User updated successfully');
            closeModal('editUserModal');
            loadUsers();
            loadStatistics();
        } else {
            showError(roleData.message || statusData.message || 'Failed to update user');
        }
    } catch (error) {
        console.error('Failed to update user:', error);
        showError('Failed to update user');
    }
}

// Reset user password
function resetUserPassword(userId) {
    const user = usersData.find(u => u.id === userId);
    if (!user) return;

    document.getElementById('resetUserId').value = userId;
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    document.getElementById('resetPasswordModal').classList.add('active');
}

// Confirm password reset
async function confirmPasswordReset() {
    const userId = document.getElementById('resetUserId').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword.length < 8) {
        showError('Password must be at least 8 characters long');
        return;
    }

    if (newPassword !== confirmPassword) {
        showError('Passwords do not match');
        return;
    }

    try {
        const response = await fetch(`${window.BASE_URL||""}/api/admin/users/${userId}/reset-password`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ new_password: newPassword })
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Password reset successfully');
            closeModal('resetPasswordModal');
        } else {
            showError(data.message || 'Failed to reset password');
        }
    } catch (error) {
        console.error('Failed to reset password:', error);
        showError('Failed to reset password');
    }
}

// Delete user
function deleteUser(userId) {
    const user = usersData.find(u => u.id === userId);
    if (!user) return;

    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserInfo').innerHTML = `
        You are about to delete <strong>${escapeHtml(user.first_name)} ${escapeHtml(user.last_name)}</strong> (@${escapeHtml(user.username)}).
        All their data will be permanently removed.
    `;
    document.getElementById('deleteUserModal').classList.add('active');
}

// Confirm user deletion
async function confirmUserDeletion() {
    const userId = document.getElementById('deleteUserId').value;

    try {
        const response = await fetch(`${window.BASE_URL||""}/api/admin/users/${userId}`, {
            method: 'DELETE'
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('User deleted successfully');
            closeModal('deleteUserModal');
            loadUsers();
            loadStatistics();
        } else {
            showError(data.message || 'Failed to delete user');
        }
    } catch (error) {
        console.error('Failed to delete user:', error);
        showError('Failed to delete user');
    }
}

// Close modal
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Close modal on background click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('userTypeFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('sortBy').value = 'created_at';
    currentPage = 1;
    loadUsers();
}

// Toggle select all
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
}

// Export users (placeholder)
function exportUsers() {
    showSuccess('Export feature coming soon!');
}

// Show add user modal (placeholder)
function showAddUserModal() {
    showSuccess('Add user feature coming soon!');
}

// Utility Functions
function formatNumber(num) {
    return num ? num.toLocaleString() : '0';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatDateTime(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatRole(role) {
    const roleMap = {
        'user': 'Regular User',
        'admin': 'Administrator',
        'coach': 'Coach',
        'ground_owner': 'Ground Owner',
        'shop_owner': 'Shop Owner'
    };
    return roleMap[role] || role;
}

function getRoleIcon(role) {
    const iconMap = {
        'user': '<i class="fas fa-user"></i>',
        'admin': '<i class="fas fa-user-shield"></i>',
        'coach': '<i class="fas fa-user-tie"></i>',
        'ground_owner': '<i class="fas fa-building"></i>',
        'shop_owner': '<i class="fas fa-store"></i>'
    };
    return iconMap[role] || '<i class="fas fa-user"></i>';
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? String(text).replace(/[&<>"']/g, m => map[m]) : '';
}

// Toast notifications
function showSuccess(message) {
    showToast(message, 'success');
}

function showError(message) {
    showToast(message, 'error');
}

function showToast(message, type = 'info') {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        `;
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.style.cssText = `
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        min-width: 300px;
        border-left: 4px solid ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        animation: slideIn 0.3s ease;
    `;

    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    const color = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';

    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-${icon}" style="color: ${color}; font-size: 1.25rem;"></i>
            <span style="color: #0f172a; font-weight: 500;">${message}</span>
        </div>
    `;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);