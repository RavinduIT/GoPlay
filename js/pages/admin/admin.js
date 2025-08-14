// Clean Admin Dashboard JavaScript
class AdminDashboard {
    constructor() {
        this.dataUtils = new DataUtils();
        this.data = {};
        this.currentApplication = null;
        this.maintenanceMode = false;
    }

    // Initialize the dashboard
    async init() {
        try {
            console.log('Admin Dashboard initializing...');
            await this.loadData();
            this.setupEventListeners();
            this.renderAll();
        } catch (error) {
            console.error('Error initializing dashboard:', error);
            this.showToast('Error loading dashboard data', 'error');
        }
    }

    // Load all data
    async loadData() {
        try {
            this.data = await this.dataUtils.loadAllData();
        } catch (error) {
            console.error('Error loading data:', error);
            this.showToast('Some data failed to load', 'warning');
        }
    }

    // Setup event listeners
    setupEventListeners() {
        // Tab switching
        document.querySelectorAll('.tab-trigger').forEach(tab => {
            tab.addEventListener('click', () => this.switchTab(tab.dataset.tab));
        });

        // Modal close handlers
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });

        // Escape key to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.show').forEach(modal => {
                    this.closeModal(modal.id);
                });
            }
        });
    }

    // Render all components
    renderAll() {
        this.renderUsers();
        this.renderApplications();
        this.renderActivity();
        this.updateStats();
    }

    // Switch tabs
    switchTab(tabName) {
        // Update tab triggers
        document.querySelectorAll('.tab-trigger').forEach(tab => {
            tab.classList.remove('active');
        });
        const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeTab) {
            activeTab.classList.add('active');
        }

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        const activeContent = document.getElementById(`${tabName}-tab`);
        if (activeContent) {
            activeContent.classList.add('active');
        }
    }

    // Transform data for user display
    transformCoachesToUsers(coaches) {
        return coaches.map(coach => ({
            id: `coach_${coach.id}`,
            name: coach.name,
            email: coach.email,
            role: 'Coach',
            status: coach.status || 'Active',
            joinDate: coach.joinDate,
            avatar: 'ðŸ†',
            lastActive: 'Recently',
            specialization: Array.isArray(coach.specialization) ? coach.specialization.join(', ') : 'N/A',
            experience: coach.experience,
            rating: coach.rating
        }));
    }

    transformGroundsToUsers(grounds) {
        return grounds.map(ground => ({
            id: `ground_${ground.id}`,
            name: ground.ownerName || ground.name,
            email: ground.email || 'No email',
            role: 'Complex Owner',
            status: ground.status || 'Active',
            joinDate: ground.joinDate || '2024-01-01',
            avatar: 'ðŸŸï¸',
            lastActive: 'Recently',
            facilityName: ground.name,
            location: ground.address || ground.location,
            rating: ground.rating
        }));
    }

    extractShopOwners(products) {
        const shopOwners = [];
        const seenOwners = new Set();
        
        products.forEach(product => {
            if (product.seller && !seenOwners.has(product.seller)) {
                seenOwners.add(product.seller);
                shopOwners.push({
                    id: `shop_${shopOwners.length}`,
                    name: product.seller,
                    email: product.sellerEmail || 'No email',
                    role: 'Shop Owner',
                    status: 'Active',
                    joinDate: product.listedDate || '2024-01-01',
                    avatar: 'ðŸ›ï¸',
                    lastActive: 'Recently',
                    totalProducts: products.filter(p => p.seller === product.seller).length
                });
            }
        });
        
        return shopOwners;
    }

    // Render users section
    renderUsers() {
        const coachUsers = this.transformCoachesToUsers(this.data.coaches || []);
        const groundUsers = this.transformGroundsToUsers(this.data.grounds || []);
        const shopOwners = this.extractShopOwners(this.data.products || []);
        const regularUsers = this.data.users || [];

        this.renderUserList('coaches-list', coachUsers, 'coaches-loading');
        this.renderUserList('grounds-list', groundUsers, 'grounds-loading');
        this.renderUserList('shops-list', shopOwners, 'shops-loading');
        this.renderUserList('regular-users-list', regularUsers);

        // Update counts
        this.updateElementText('coaches-count', coachUsers.length);
        this.updateElementText('grounds-count', groundUsers.length);
        this.updateElementText('shops-count', shopOwners.length);
        this.updateElementText('regular-users-count', regularUsers.length);
    }

    // Render user list
    renderUserList(containerId, userList, loadingId = null) {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (loadingId) {
            const loading = document.getElementById(loadingId);
            if (loading) loading.style.display = 'none';
        }

        if (userList.length === 0) {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-users"></i><p>No users found</p></div>';
            return;
        }

        container.innerHTML = userList.map(user => `
            <div class="user-item">
                <div class="user-info">
                    <div class="user-avatar">${user.avatar}</div>
                    <div class="user-details">
                        <h4>${user.name}</h4>
                        <p>${user.email}</p>
                        <p>Role: ${user.role} â€¢ Joined: ${this.formatDate(user.joinDate)}</p>
                        ${user.specialization ? `<p>Specialization: ${user.specialization}</p>` : ''}
                        ${user.facilityName ? `<p>Facility: ${user.facilityName}</p>` : ''}
                        ${user.totalProducts ? `<p>Products: ${user.totalProducts}</p>` : ''}
                        ${user.rating ? `<p>Rating: â­ ${user.rating}</p>` : ''}
                    </div>
                </div>
                <div class="user-actions">
                    <div class="user-controls">
                        <div class="select-wrapper">
                            <select onchange="admin.updateUserRole('${user.id}', this.value)">
                                <option value="Player" ${user.role === 'Player' ? 'selected' : ''}>Player</option>
                                <option value="Coach" ${user.role === 'Coach' ? 'selected' : ''}>Coach</option>
                                <option value="Shop Owner" ${user.role === 'Shop Owner' ? 'selected' : ''}>Shop Owner</option>
                                <option value="Complex Owner" ${user.role === 'Complex Owner' ? 'selected' : ''}>Complex Owner</option>
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <select onchange="admin.updateUserStatus('${user.id}', this.value)">
                                <option value="Active" ${user.status === 'Active' ? 'selected' : ''}>Active</option>
                                <option value="Pending" ${user.status === 'Pending' ? 'selected' : ''}>Pending</option>
                                <option value="Suspended" ${user.status === 'Suspended' ? 'selected' : ''}>Suspended</option>
                                <option value="Inactive" ${user.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <span class="status-badge ${user.status.toLowerCase()}">${user.status}</span>
                </div>
            </div>
        `).join('');
    }

    // Render applications
    renderApplications() {
        const container = document.getElementById('applications-list');
        const loading = document.getElementById('applications-loading');
        const applications = this.data.applications || [];
        
        if (!container) return;
        
        if (loading) loading.style.display = 'none';
        
        const pendingCount = applications.filter(app => app.status === 'pending').length;
        this.updateElementText('pending-applications-count', pendingCount);
        
        const badge = document.getElementById('pending-applications-count');
        if (badge) {
            badge.style.display = pendingCount > 0 ? 'inline-flex' : 'none';
        }
        
        if (applications.length === 0) {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-file-text"></i><p>No applications yet</p></div>';
            return;
        }

        container.innerHTML = applications.map(app => `
            <div class="application-item">
                <div class="application-header">
                    <div class="application-info">
                        <div class="application-icon">
                            <i class="fas fa-${this.getApplicationIcon(app.type)}"></i>
                        </div>
                        <div class="application-details">
                            <h4>${app.personalInfo?.name || 'Unknown'}</h4>
                            <p>${app.businessInfo?.businessName || 'No business name'}</p>
                            <p>Applied: ${this.formatDate(app.submittedDate)}</p>
                        </div>
                    </div>
                    <div class="application-actions">
                        <span class="status-badge ${app.status}">
                            <i class="fas fa-${this.getStatusIcon(app.status)}"></i>
                            ${app.status.charAt(0).toUpperCase() + app.status.slice(1)}
                        </span>
                        ${app.status === 'pending' ? `
                            <button class="btn primary" onclick="admin.reviewApplication('${app.id}')">
                                <i class="fas fa-eye"></i>
                                Review
                            </button>
                        ` : ''}
                    </div>
                </div>
                <div class="application-summary">
                    <p><strong>Email:</strong> ${app.personalInfo?.email || 'No email'}</p>
                    <p><strong>Type:</strong> ${app.type?.charAt(0).toUpperCase() + app.type?.slice(1) || 'Unknown'}</p>
                    ${app.status !== 'pending' && app.reviewDate ? `
                        <p><strong>Reviewed:</strong> ${this.formatDate(app.reviewDate)} by ${app.reviewedBy}</p>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    // Render activity
    renderActivity() {
        const container = document.getElementById('activity-list');
        const activity = this.data.activity || [];
        
        if (!container) return;
        
        if (activity.length === 0) {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-chart-line"></i><p>No recent activity</p></div>';
            return;
        }

        container.innerHTML = activity.map(item => `
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-${this.getActivityIcon(item.type)}"></i>
                </div>
                <div class="activity-content">
                    <p>${item.action}</p>
                    <span>by ${item.user}</span>
                </div>
                <div class="activity-time">${item.time}</div>
            </div>
        `).join('');
    }

    // Update statistics
    updateStats() {
        const coaches = this.data.coaches || [];
        const grounds = this.data.grounds || [];
        const products = this.data.products || [];
        const users = this.data.users || [];
        
        const shopOwnersCount = new Set((products).map(p => p.seller)).size;
        
        const stats = {
            totalUsers: users.length + coaches.length + grounds.length + shopOwnersCount,
            activeGrounds: grounds.length,
            totalProducts: products.length,
            activeCoaches: coaches.length
        };

        this.updateElementText('total-users', stats.totalUsers.toLocaleString());
        this.updateElementText('active-grounds', stats.activeGrounds);
        this.updateElementText('total-products', stats.totalProducts.toLocaleString());
        this.updateElementText('active-coaches', stats.activeCoaches);
    }

    // User management functions
    updateUserRole(userId, newRole) {
        if (confirm(`Are you sure you want to change this user's role to ${newRole}?`)) {
            // Here you would typically make an API call
            this.showToast(`User role updated to ${newRole}`);
            console.log(`Updated user ${userId} role to ${newRole}`);
        }
    }

    updateUserStatus(userId, newStatus) {
        const confirmMessage = newStatus === 'Suspended' 
            ? `Are you sure you want to suspend this user? This will restrict their access.`
            : `Are you sure you want to change this user's status to ${newStatus}?`;
        
        if (confirm(confirmMessage)) {
            // Here you would typically make an API call
            this.showToast(`User status updated to ${newStatus}`);
            console.log(`Updated user ${userId} status to ${newStatus}`);
        }
    }

    // Application management
    reviewApplication(applicationId) {
        const applications = this.data.applications || [];
        const application = applications.find(app => app.id === applicationId);
        if (!application) return;

        this.currentApplication = application;
        
        const modalBody = document.getElementById('application-modal-body');
        if (!modalBody) return;
        
        modalBody.innerHTML = `
            <div class="application-review">
                <div class="form-group">
                    <h4>Personal Information</h4>
                    <div class="info-grid">
                        <p><strong>Name:</strong> ${application.personalInfo?.name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${application.personalInfo?.email || 'N/A'}</p>
                        <p><strong>Phone:</strong> ${application.personalInfo?.phone || 'N/A'}</p>
                        <p><strong>Location:</strong> ${application.personalInfo?.location || 'N/A'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <h4>Business Information</h4>
                    <div class="info-grid">
                        <p><strong>Business Name:</strong> ${application.businessInfo?.businessName || 'N/A'}</p>
                        <p><strong>Type:</strong> ${application.type?.charAt(0).toUpperCase() + application.type?.slice(1) || 'N/A'}</p>
                        <p><strong>Experience:</strong> ${application.businessInfo?.experience || 'N/A'}</p>
                        <p><strong>Description:</strong> ${application.businessInfo?.description || 'N/A'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <h4>Documents</h4>
                    <div class="info-grid">
                        <p><strong>Photos:</strong> ${application.documents?.photos?.length || 0}</p>
                        <p><strong>Certificates:</strong> ${application.documents?.certificates?.length || 0}</p>
                        <p><strong>ID Card:</strong> ${application.documents?.idCard ? 'Provided' : 'Not provided'}</p>
                    </div>
                </div>
            </div>
        `;
        
        this.showModal('application-modal');
    }

    approveApplication() {
        if (!this.currentApplication) return;
        
        if (confirm(`Are you sure you want to approve ${this.currentApplication.personalInfo?.name}'s application?`)) {
            this.handleApplicationAction(this.currentApplication.id, 'approve');
            this.closeModal('application-modal');
        }
    }

    rejectApplication() {
        if (!this.currentApplication) return;
        
        if (confirm(`Are you sure you want to reject ${this.currentApplication.personalInfo?.name}'s application?`)) {
            this.handleApplicationAction(this.currentApplication.id, 'reject');
            this.closeModal('application-modal');
        }
    }

    handleApplicationAction(applicationId, action) {
        const applications = this.data.applications || [];
        const appIndex = applications.findIndex(app => app.id === applicationId);
        if (appIndex === -1) return;

        applications[appIndex] = {
            ...applications[appIndex],
            status: action === 'approve' ? 'approved' : 'rejected',
            reviewedBy: 'Admin',
            reviewDate: new Date().toISOString(),
            reviewNotes: ''
        };
        
        this.renderApplications();
        
        const message = action === 'approve' 
            ? `Application approved successfully`
            : `Application rejected`;
        this.showToast(message);
    }

    // System functions
    showNotificationDialog() {
        this.showModal('notification-modal');
    }

    sendNotification() {
        const titleInput = document.getElementById('notification-title');
        const messageInput = document.getElementById('notification-message');
        
        if (!titleInput || !messageInput) return;
        
        const title = titleInput.value;
        const message = messageInput.value;
        
        if (!title || !message) {
            this.showToast('Please fill in all fields');
            return;
        }
        
        const button = event.target;
        button.classList.add('loading');
        button.textContent = 'Sending...';
        
        setTimeout(() => {
            button.classList.remove('loading');
            button.textContent = 'Send Notification';
            this.closeModal('notification-modal');
            this.showToast(`"${title}" has been sent to all users`);
            
            // Clear form
            titleInput.value = 'System Update';
            messageInput.value = 'We have updated our system with new features and improvements.';
        }, 2000);
    }

    exportData() {
        const exportData = {
            exportDate: new Date().toISOString(),
            summary: {
                totalUsers: (this.data.users || []).length,
                totalCoaches: (this.data.coaches || []).length,
                totalGrounds: (this.data.grounds || []).length,
                totalProducts: (this.data.products || []).length,
                totalShopOwners: new Set((this.data.products || []).map(p => p.seller)).size
            },
            ...this.data
        };
        
        const dataStr = JSON.stringify(exportData, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });
        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `admin-export-${new Date().toISOString().split('T')[0]}.json`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        this.showToast('Data exported successfully');
    }

    generateReports() {
        const button = event.target;
        button.classList.add('loading');
        button.textContent = 'Generating...';
        
        setTimeout(() => {
            const coaches = this.data.coaches || [];
            const grounds = this.data.grounds || [];
            const products = this.data.products || [];
            const users = this.data.users || [];
            const applications = this.data.applications || [];
            
            const shopOwnersCount = new Set(products.map(p => p.seller)).size;
            
            const reportData = {
                reportDate: new Date().toISOString(),
                period: "Current Data Analysis",
                metrics: {
                    totalUsers: users.length,
                    totalCoaches: coaches.length,
                    totalGrounds: grounds.length,
                    totalProducts: products.length,
                    totalShopOwners: shopOwnersCount,
                    pendingApplications: applications.filter(app => app.status === 'pending').length,
                    averageCoachRating: coaches.length > 0 ? (coaches.reduce((sum, coach) => sum + (coach.rating || 0), 0) / coaches.length).toFixed(1) : 0,
                    averageGroundRating: grounds.length > 0 ? (grounds.reduce((sum, ground) => sum + (ground.rating || 0), 0) / grounds.length).toFixed(1) : 0
                },
                insights: [
                    `${coaches.length} active coaches registered with average rating of ${coaches.length > 0 ? (coaches.reduce((sum, coach) => sum + (coach.rating || 0), 0) / coaches.length).toFixed(1) : 0}`,
                    `${grounds.length} sports facilities available across different locations`,
                    `${products.length} products listed by ${shopOwnersCount} unique sellers`,
                    `${applications.filter(app => app.status === 'pending').length} applications pending admin review`
                ],
                topCategories: this.getTopProductCategories(),
                topCoachSpecializations: this.getTopCoachSpecializations()
            };
            
            const reportStr = JSON.stringify(reportData, null, 2);
            const reportBlob = new Blob([reportStr], { type: 'application/json' });
            const url = URL.createObjectURL(reportBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `admin-report-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            button.classList.remove('loading');
            button.textContent = 'Generate Analytics Report';
            this.showToast('Analytics report generated and downloaded');
        }, 3000);
    }

    toggleMaintenanceMode() {
        this.maintenanceMode = !this.maintenanceMode;
        const button = document.getElementById('maintenance-btn');
        const warning = document.getElementById('maintenance-warning');
        
        if (!button) return;
        
        if (this.maintenanceMode) {
            button.innerHTML = '<i class="fas fa-shield-alt"></i> Exit Maintenance Mode';
            button.className = 'action-button primary';
            if (warning) warning.style.display = 'block';
            this.showToast('Maintenance mode activated', 'warning');
        } else {
            button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Enter Maintenance Mode';
            button.className = 'action-button danger';
            if (warning) warning.style.display = 'none';
            this.showToast('Maintenance mode deactivated');
        }
    }

    // Helper functions
    getTopProductCategories() {
        const products = this.data.products || [];
        const categories = {};
        products.forEach(product => {
            const category = product.category || 'Uncategorized';
            categories[category] = (categories[category] || 0) + 1;
        });
        return Object.entries(categories)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 5)
            .map(([category, count]) => ({ category, count }));
    }

    getTopCoachSpecializations() {
        const coaches = this.data.coaches || [];
        const specializations = {};
        coaches.forEach(coach => {
            if (coach.specialization && Array.isArray(coach.specialization)) {
                coach.specialization.forEach(spec => {
                    specializations[spec] = (specializations[spec] || 0) + 1;
                });
            }
        });
        return Object.entries(specializations)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 5)
            .map(([specialization, count]) => ({ specialization, count }));
    }

    getApplicationIcon(type) {
        switch (type) {
            case 'coach': return 'user-check';
            case 'ground': return 'building';
            case 'shop': return 'store';
            default: return 'file-text';
        }
    }

    getStatusIcon(status) {
        switch (status) {
            case 'pending': return 'clock';
            case 'approved': return 'check-circle';
            case 'rejected': return 'times-circle';
            default: return 'question-circle';
        }
    }

    getActivityIcon(type) {
        switch (type) {
            case 'user': return 'users';
            case 'booking': return 'calendar-check';
            case 'profile': return 'user-edit';
            case 'payment': return 'credit-card';
            case 'shop': return 'store';
            default: return 'bell';
        }
    }

    // Modal management
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            this.currentApplication = null;
        }
    }

    // Toast notifications
    showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        if (!toast || !toastMessage) return;
        
        toastMessage.textContent = message;
        toast.classList.add('show');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
            this.hideToast();
        }, 3000);
    }

    hideToast() {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.classList.remove('show');
        }
    }

    // Utility functions
    updateElementText(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        }
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            return new Date(dateString).toLocaleDateString();
        } catch (error) {
            return dateString;
        }
    }
}

// Initialize admin dashboard
let admin;

document.addEventListener('DOMContentLoaded', async () => {
    admin = new AdminDashboard();
    await admin.init();
});

// Global functions for HTML onclick handlers
window.admin = {
    updateUserRole: (userId, newRole) => admin.updateUserRole(userId, newRole),
    updateUserStatus: (userId, newStatus) => admin.updateUserStatus(userId, newStatus),
    reviewApplication: (applicationId) => admin.reviewApplication(applicationId),
    approveApplication: () => admin.approveApplication(),
    rejectApplication: () => admin.rejectApplication(),
    showNotificationDialog: () => admin.showNotificationDialog(),
    sendNotification: () => admin.sendNotification(),
    exportData: () => admin.exportData(),
    generateReports: () => admin.generateReports(),
    toggleMaintenanceMode: () => admin.toggleMaintenanceMode(),
    showModal: (modalId) => admin.showModal(modalId),
    closeModal: (modalId) => admin.closeModal(modalId),
    hideToast: () => admin.hideToast(),
    showToast: (message, type) => admin.showToast(message, type)
};