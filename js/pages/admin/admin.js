// Global Variables
let coaches = [];
let grounds = [];
let products = [];
let applications = [];
let recentActivity = [];
let currentApplication = null;
let maintenanceMode = false;

// Sample regular users data
const regularUsers = [
    {
        id: 1,
        name: 'John Martinez',
        email: 'john@email.com',
        role: 'Player',
        status: 'Active',
        joinDate: '2023-01-15',
        avatar: '👨‍💼',
        lastActive: '2 hours ago'
    },
    {
        id: 2,
        name: 'Sarah Thompson',
        email: 'sarah@email.com',
        role: 'Player',
        status: 'Active',
        joinDate: '2023-02-20',
        avatar: '👩‍💼',
        lastActive: '1 day ago'
    },
    {
        id: 3,
        name: 'Mike Johnson',
        email: 'mike@email.com',
        role: 'Player',
        status: 'Pending',
        joinDate: '2024-01-10',
        avatar: '👨‍🏫',
        lastActive: '3 days ago'
    },
    {
        id: 4,
        name: 'Lisa Chen',
        email: 'lisa@email.com',
        role: 'Player',
        status: 'Active',
        joinDate: '2023-08-05',
        avatar: '👩‍🏫',
        lastActive: '5 hours ago'
    },
    {
        id: 5,
        name: 'David Wilson',
        email: 'david@email.com',
        role: 'Player',
        status: 'Pending',
        joinDate: '2024-01-20',
        avatar: '👨‍🏫',
        lastActive: '1 hour ago'
    },
    {
        id: 6,
        name: 'Emma Davis',
        email: 'emma@email.com',
        role: 'Player',
        status: 'Active',
        joinDate: '2023-11-15',
        avatar: '👩‍💼',
        lastActive: '4 hours ago'
    },
    {
        id: 7,
        name: 'Robert Brown',
        email: 'robert@email.com',
        role: 'Player',
        status: 'Pending',
        joinDate: '2024-01-25',
        avatar: '👨‍💼',
        lastActive: '2 days ago'
    }
];

// Sample applications data
const sampleApplications = [
    {
        id: 'app1',
        type: 'coach',
        status: 'pending',
        submittedDate: '2024-01-15',
        personalInfo: {
            name: 'Alex Rodriguez',
            email: 'alex@email.com',
            phone: '+1234567890',
            location: 'New York'
        },
        businessInfo: {
            businessName: 'Pro Tennis Academy',
            experience: '5 years',
            description: 'Specialized in tennis coaching for beginners and advanced players'
        },
        documents: {
            photos: ['photo1.jpg', 'photo2.jpg'],
            certificates: ['cert1.pdf'],
            idCard: true
        }
    },
    {
        id: 'app2', 
        type: 'shop',
        status: 'approved',
        submittedDate: '2024-01-10',
        reviewDate: '2024-01-12',
        reviewedBy: 'Admin',
        personalInfo: {
            name: 'Maria Garcia',
            email: 'maria@email.com',
            phone: '+1234567891',
            location: 'California'
        },
        businessInfo: {
            businessName: 'Sports Equipment Pro',
            experience: '8 years',
            description: 'Premium sports equipment and accessories'
        },
        documents: {
            photos: ['shop1.jpg', 'shop2.jpg'],
            certificates: ['business_license.pdf'],
            idCard: true
        }
    }
];

// Sample data for development (fallback data)
const sampleCoaches = [
    {
        id: 1,
        name: 'John Smith',
        email: 'john.smith@email.com',
        specialization: ['Tennis', 'Badminton'],
        experience: '5 years',
        rating: 4.8,
        status: 'Active',
        joinDate: '2023-03-15'
    },
    {
        id: 2,
        name: 'Sarah Johnson',
        email: 'sarah.johnson@email.com',
        specialization: ['Football', 'Basketball'],
        experience: '3 years',
        rating: 4.5,
        status: 'Active',
        joinDate: '2023-06-20'
    }
];

const sampleGrounds = [
    {
        id: 1,
        name: 'Central Sports Complex',
        ownerName: 'Mike Wilson',
        email: 'mike.wilson@email.com',
        address: '123 Sports Ave, City',
        rating: 4.6,
        status: 'Active',
        joinDate: '2023-01-10'
    },
    {
        id: 2,
        name: 'Elite Tennis Club',
        ownerName: 'Lisa Anderson',
        email: 'lisa.anderson@email.com',
        address: '456 Tennis St, City',
        rating: 4.9,
        status: 'Active',
        joinDate: '2023-04-25'
    }
];

const sampleProducts = [
    {
        id: 1,
        name: 'Professional Tennis Racket',
        seller: 'Sports Pro Shop',
        sellerEmail: 'info@sportsproshop.com',
        category: 'Tennis Equipment',
        price: 150,
        listedDate: '2024-01-05'
    },
    {
        id: 2,
        name: 'Basketball Shoes',
        seller: 'Athletic Gear Store',
        sellerEmail: 'sales@athleticgear.com',
        category: 'Footwear',
        price: 120,
        listedDate: '2024-01-08'
    },
    {
        id: 3,
        name: 'Football Jersey',
        seller: 'Sports Pro Shop',
        sellerEmail: 'info@sportsproshop.com',
        category: 'Apparel',
        price: 75,
        listedDate: '2024-01-12'
    }
];

// Initialize data when DOM is loaded
document.addEventListener('DOMContentLoaded', async () => {
    console.log('Admin Dashboard initializing...');
    await initializeData();
    setupEventListeners();
});

// Initialize data from JSON files
const initializeData = async () => {
    try {
        console.log('Loading data from JSON files...');
        
        // Load all data concurrently
        await Promise.all([
            loadCoaches(),
            loadGrounds(),
            loadProducts()
        ]);
        
        // Set applications and activity
        applications = [...sampleApplications];
        generateRecentActivity();
        
        // Render all components
        renderUsers();
        renderApplications();
        renderActivity();
        updateStats();
        
        console.log('Data loaded successfully:', {
            coaches: coaches.length,
            grounds: grounds.length,
            products: products.length,
            applications: applications.length
        });
        
    } catch (error) {
        console.error('Error initializing data:', error);
        showToast('Error loading data. Using sample data instead.', 'warning');
    }
};

// Load coaches data
const loadCoaches = async () => {
    try {
        const response = await fetch('./data/coaches.json');
        if (response.ok) {
            coaches = await response.json();
            console.log(`Loaded ${coaches.length} coaches`);
        } else {
            throw new Error(`Failed to load coaches: ${response.status}`);
        }
    } catch (error) {
        console.error('Error loading coaches:', error);
        coaches = [...sampleCoaches];
        console.log('Using sample coaches data');
    }
};

// Load grounds data
const loadGrounds = async () => {
    try {
        const response = await fetch('./data/grounds.json');
        if (response.ok) {
            grounds = await response.json();
            console.log(`Loaded ${grounds.length} grounds`);
        } else {
            throw new Error(`Failed to load grounds: ${response.status}`);
        }
    } catch (error) {
        console.error('Error loading grounds:', error);
        grounds = [...sampleGrounds];
        console.log('Using sample grounds data');
    }
};

// Load products data
const loadProducts = async () => {
    try {
        const response = await fetch('./data/products.json');
        if (response.ok) {
            products = await response.json();
            console.log(`Loaded ${products.length} products`);
        } else {
            throw new Error(`Failed to load products: ${response.status}`);
        }
    } catch (error) {
        console.error('Error loading products:', error);
        products = [...sampleProducts];
        console.log('Using sample products data');
    }
};

// Generate recent activity from loaded data
const generateRecentActivity = () => {
    recentActivity = [
        { 
            id: 1, 
            action: 'New coach registered', 
            user: coaches.length > 0 ? coaches[0].name : 'Unknown Coach', 
            time: '2 minutes ago', 
            type: 'user' 
        },
        { 
            id: 2, 
            action: 'Ground booking confirmed', 
            user: grounds.length > 0 ? grounds[0].name : 'Unknown Ground', 
            time: '15 minutes ago', 
            type: 'booking' 
        },
        { 
            id: 3, 
            action: 'Product added to shop', 
            user: products.length > 0 ? products[0].seller : 'Unknown Seller', 
            time: '1 hour ago', 
            type: 'shop' 
        },
        { 
            id: 4, 
            action: 'Coach profile updated', 
            user: coaches.length > 1 ? coaches[1].name : 'Unknown Coach', 
            time: '2 hours ago', 
            type: 'profile' 
        },
        { 
            id: 5, 
            action: 'New equipment listed', 
            user: products.length > 1 ? products[1].seller : 'Unknown Seller', 
            time: '3 hours ago', 
            type: 'shop' 
        }
    ];
};

// Event Listeners Setup
const setupEventListeners = () => {
    // Tab switching
    document.querySelectorAll('.tab-trigger').forEach(tab => {
        tab.addEventListener('click', () => switchTab(tab.dataset.tab));
    });

    // Modal close on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // Escape key to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.show').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
};

// Tab Management
const switchTab = (tabName) => {
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
};

// Render Users
const renderUsers = () => {
    // Convert coaches to user format
    const coachUsers = coaches.map((coach) => ({
        id: `coach_${coach.id}`,
        name: coach.name,
        email: coach.email,
        role: 'Coach',
        status: coach.status || 'Active',
        joinDate: coach.joinDate,
        avatar: '🏆',
        lastActive: 'Recently',
        specialization: coach.specialization?.join(', ') || 'N/A',
        experience: coach.experience,
        rating: coach.rating
    }));

    // Convert grounds to user format
    const groundUsers = grounds.map((ground) => ({
        id: `ground_${ground.id}`,
        name: ground.ownerName,
        email: ground.email,
        role: 'Complex Owner',
        status: ground.status || 'Active',
        joinDate: ground.joinDate,
        avatar: '🏟️',
        lastActive: 'Recently',
        facilityName: ground.name,
        location: ground.address,
        rating: ground.rating
    }));

    // Extract unique shop owners from products
    const shopOwners = [];
    const seenOwners = new Set();
    products.forEach((product) => {
        if (product.seller && !seenOwners.has(product.seller)) {
            seenOwners.add(product.seller);
            shopOwners.push({
                id: `shop_${shopOwners.length}`,
                name: product.seller,
                email: product.sellerEmail || 'No email',
                role: 'Shop Owner',
                status: 'Active',
                joinDate: product.listedDate || '2024-01-01',
                avatar: '🛍️',
                lastActive: 'Recently',
                totalProducts: products.filter(p => p.seller === product.seller).length
            });
        }
    });

    // Render all user lists
    renderUserList('coaches-list', coachUsers, 'coaches-loading');
    renderUserList('grounds-list', groundUsers, 'grounds-loading');
    renderUserList('shops-list', shopOwners, 'shops-loading');
    renderRegularUsers();

    // Update counts
    updateElementText('coaches-count', coachUsers.length);
    updateElementText('grounds-count', groundUsers.length);
    updateElementText('shops-count', shopOwners.length);
    updateElementText('regular-users-count', regularUsers.length);
};

// Helper function to safely update element text
const updateElementText = (elementId, text) => {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = text;
    }
};

// Render user list
const renderUserList = (containerId, userList, loadingId) => {
    const container = document.getElementById(containerId);
    const loading = document.getElementById(loadingId);
    
    if (!container) return;
    
    if (loading) {
        loading.style.display = 'none';
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
                    <p>Role: ${user.role} • Joined: ${formatDate(user.joinDate)}</p>
                    ${user.specialization ? `<p>Specialization: ${user.specialization}</p>` : ''}
                    ${user.facilityName ? `<p>Facility: ${user.facilityName}</p>` : ''}
                    ${user.totalProducts ? `<p>Products: ${user.totalProducts}</p>` : ''}
                    ${user.rating ? `<p>Rating: ⭐ ${user.rating}</p>` : ''}
                </div>
            </div>
            <div class="user-actions">
                <div class="user-controls">
                    <div class="select-wrapper">
                        <select onchange="updateUserRole('${user.id}', this.value)">
                            <option value="Player" ${user.role === 'Player' ? 'selected' : ''}>Player</option>
                            <option value="Coach" ${user.role === 'Coach' ? 'selected' : ''}>Coach</option>
                            <option value="Shop Owner" ${user.role === 'Shop Owner' ? 'selected' : ''}>Shop Owner</option>
                            <option value="Complex Owner" ${user.role === 'Complex Owner' ? 'selected' : ''}>Complex Owner</option>
                        </select>
                    </div>
                    <div class="select-wrapper">
                        <select onchange="updateUserStatus('${user.id}', this.value)">
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
};

// Render regular users
const renderRegularUsers = () => {
    const container = document.getElementById('regular-users-list');
    if (!container) return;
    
    container.innerHTML = regularUsers.map(user => `
        <div class="user-item">
            <div class="user-info">
                <div class="user-avatar">${user.avatar}</div>
                <div class="user-details">
                    <h4>${user.name}</h4>
                    <p>${user.email}</p>
                    <p>Joined ${formatDate(user.joinDate)} • Last active ${user.lastActive}</p>
                </div>
            </div>
            <div class="user-actions">
                <div class="user-controls">
                    <div class="select-wrapper">
                        <select onchange="updateUserRole('${user.id}', this.value)">
                            <option value="Player" ${user.role === 'Player' ? 'selected' : ''}>Player</option>
                            <option value="Coach" ${user.role === 'Coach' ? 'selected' : ''}>Coach</option>
                            <option value="Shop Owner" ${user.role === 'Shop Owner' ? 'selected' : ''}>Shop Owner</option>
                            <option value="Complex Owner" ${user.role === 'Complex Owner' ? 'selected' : ''}>Complex Owner</option>
                        </select>
                    </div>
                    <div class="select-wrapper">
                        <select onchange="updateUserStatus('${user.id}', this.value)">
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
};

// Update user role
const updateUserRole = (userId, newRole) => {
    if (confirm(`Are you sure you want to change this user's role to ${newRole}?`)) {
        // Here you would typically make an API call
        showToast(`User role updated to ${newRole}`);
        console.log(`Updated user ${userId} role to ${newRole}`);
    }
};

// Update user status
const updateUserStatus = (userId, newStatus) => {
    const confirmMessage = newStatus === 'Suspended' 
        ? `Are you sure you want to suspend this user? This will restrict their access.`
        : `Are you sure you want to change this user's status to ${newStatus}?`;
    
    if (confirm(confirmMessage)) {
        // Here you would typically make an API call
        showToast(`User status updated to ${newStatus}`);
        console.log(`Updated user ${userId} status to ${newStatus}`);
    }
};

// Render applications
const renderApplications = () => {
    const container = document.getElementById('applications-list');
    const loading = document.getElementById('applications-loading');
    const pendingCount = applications.filter(app => app.status === 'pending').length;
    
    if (!container) return;
    
    if (loading) {
        loading.style.display = 'none';
    }
    
    updateElementText('pending-applications-count', pendingCount);
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
                        <i class="fas fa-${getApplicationIcon(app.type)}"></i>
                    </div>
                    <div class="application-details">
                        <h4>${app.personalInfo?.name || 'Unknown'}</h4>
                        <p>${app.businessInfo?.businessName || 'No business name'}</p>
                        <p>Applied: ${formatDate(app.submittedDate)}</p>
                    </div>
                </div>
                <div class="application-actions">
                    <span class="status-badge ${app.status}">
                        <i class="fas fa-${getStatusIcon(app.status)}"></i>
                        ${app.status.charAt(0).toUpperCase() + app.status.slice(1)}
                    </span>
                    ${app.status === 'pending' ? `
                        <button class="btn primary" onclick="reviewApplication('${app.id}')">
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
                    <p><strong>Reviewed:</strong> ${formatDate(app.reviewDate)} by ${app.reviewedBy}</p>
                ` : ''}
            </div>
        </div>
    `).join('');
};

// Get application icon
const getApplicationIcon = (type) => {
    switch (type) {
        case 'coach': return 'user-check';
        case 'ground': return 'building';
        case 'shop': return 'store';
        default: return 'file-text';
    }
};

// Get status icon
const getStatusIcon = (status) => {
    switch (status) {
        case 'pending': return 'clock';
        case 'approved': return 'check-circle';
        case 'rejected': return 'times-circle';
        default: return 'question-circle';
    }
};

// Review application
const reviewApplication = (applicationId) => {
    const application = applications.find(app => app.id === applicationId);
    if (!application) return;

    currentApplication = application;
    
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
    
    showModal('application-modal');
};

// Approve application
const approveApplication = () => {
    if (!currentApplication) return;
    
    if (confirm(`Are you sure you want to approve ${currentApplication.personalInfo?.name}'s application?`)) {
        handleApplicationAction(currentApplication.id, 'approve');
        closeModal('application-modal');
    }
};

// Reject application
const rejectApplication = () => {
    if (!currentApplication) return;
    
    if (confirm(`Are you sure you want to reject ${currentApplication.personalInfo?.name}'s application?`)) {
        handleApplicationAction(currentApplication.id, 'reject');
        closeModal('application-modal');
    }
};

// Handle application action
const handleApplicationAction = (applicationId, action) => {
    const appIndex = applications.findIndex(app => app.id === applicationId);
    if (appIndex === -1) return;

    applications[appIndex] = {
        ...applications[appIndex],
        status: action === 'approve' ? 'approved' : 'rejected',
        reviewedBy: 'Admin',
        reviewDate: new Date().toISOString(),
        reviewNotes: ''
    };
    
    renderApplications();
    
    const message = action === 'approve' 
        ? `Application approved successfully`
        : `Application rejected`;
    showToast(message);
};

// Render activity
const renderActivity = () => {
    const container = document.getElementById('activity-list');
    if (!container) return;
    
    container.innerHTML = recentActivity.map(activity => `
        <div class="activity-item">
            <div class="activity-icon">
                <i class="fas fa-${getActivityIcon(activity.type)}"></i>
            </div>
            <div class="activity-content">
                <p>${activity.action}</p>
                <span>by ${activity.user}</span>
            </div>
            <div class="activity-time">${activity.time}</div>
        </div>
    `).join('');
};

// Get activity icon
const getActivityIcon = (type) => {
    switch (type) {
        case 'user': return 'users';
        case 'booking': return 'calendar-check';
        case 'profile': return 'user-edit';
        case 'payment': return 'credit-card';
        case 'shop': return 'store';
        default: return 'bell';
    }
};

// Update statistics
const updateStats = () => {
    const shopOwnersCount = new Set(products.map(p => p.seller)).size;
    const stats = {
        totalUsers: regularUsers.length + coaches.length + grounds.length + shopOwnersCount,
        activeGrounds: grounds.length,
        totalProducts: products.length,
        activeCoaches: coaches.length
    };

    updateElementText('total-users', stats.totalUsers.toLocaleString());
    updateElementText('active-grounds', stats.activeGrounds);
    updateElementText('total-products', stats.totalProducts.toLocaleString());
    updateElementText('active-coaches', stats.activeCoaches);
};

// Modal management
const showModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
};

const closeModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        currentApplication = null;
    }
};

// Notification functions
const showNotificationDialog = () => {
    showModal('notification-modal');
};

const sendNotification = () => {
    const titleInput = document.getElementById('notification-title');
    const messageInput = document.getElementById('notification-message');
    
    if (!titleInput || !messageInput) return;
    
    const title = titleInput.value;
    const message = messageInput.value;
    
    if (!title || !message) {
        showToast('Please fill in all fields');
        return;
    }
    
    const button = event.target;
    button.classList.add('loading');
    button.textContent = 'Sending...';
    
    setTimeout(() => {
        button.classList.remove('loading');
        button.textContent = 'Send Notification';
        closeModal('notification-modal');
        showToast(`"${title}" has been sent to all users`);
        
        // Clear form
        titleInput.value = 'System Update';
        messageInput.value = 'We have updated our system with new features and improvements.';
    }, 2000);
};

// Export functions
const exportData = () => {
    const shopOwnersCount = new Set(products.map(p => p.seller)).size;
    const exportData = {
        exportDate: new Date().toISOString(),
        summary: {
            totalUsers: regularUsers.length,
            totalCoaches: coaches.length,
            totalGrounds: grounds.length,
            totalProducts: products.length,
            totalShopOwners: shopOwnersCount
        },
        users: regularUsers,
        coaches: coaches,
        grounds: grounds,
        products: products,
        applications: applications,
        recentActivity: recentActivity
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
    
    showToast('Data exported successfully');
};

const generateReports = () => {
    const button = event.target;
    button.classList.add('loading');
    button.textContent = 'Generating...';
    
    setTimeout(() => {
        const shopOwnersCount = new Set(products.map(p => p.seller)).size;
        const reportData = {
            reportDate: new Date().toISOString(),
            period: "Current Data Analysis",
            metrics: {
                totalUsers: regularUsers.length,
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
            topCategories: getTopProductCategories(),
            topCoachSpecializations: getTopCoachSpecializations()
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
        showToast('Analytics report generated and downloaded');
    }, 3000);
};

// Helper functions for reports
const getTopProductCategories = () => {
    const categories = {};
    products.forEach(product => {
        const category = product.category || 'Uncategorized';
        categories[category] = (categories[category] || 0) + 1;
    });
    return Object.entries(categories)
        .sort(([,a], [,b]) => b - a)
        .slice(0, 5)
        .map(([category, count]) => ({ category, count }));
};

const getTopCoachSpecializations = () => {
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
};

// Maintenance mode
const toggleMaintenanceMode = () => {
    maintenanceMode = !maintenanceMode;
    const button = document.getElementById('maintenance-btn');
    const warning = document.getElementById('maintenance-warning');
    
    if (!button) return;
    
    if (maintenanceMode) {
        button.innerHTML = '<i class="fas fa-shield-alt"></i> Exit Maintenance Mode';
        button.className = 'action-button primary';
        if (warning) warning.style.display = 'block';
        showToast('Maintenance mode activated', 'warning');
    } else {
        button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Enter Maintenance Mode';
        button.className = 'action-button danger';
        if (warning) warning.style.display = 'none';
        showToast('Maintenance mode deactivated');
    }
};

// Toast notification system
const showToast = (message, type = 'success') => {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    
    if (!toast || !toastMessage) return;
    
    toastMessage.textContent = message;
    toast.classList.add('show');
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        hideToast();
    }, 3000);
};

const hideToast = () => {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.classList.remove('show');
    }
};

// Error handling
window.addEventListener('error', (event) => {
    console.error('JavaScript Error:', event.error);
    showToast('An error occurred. Please check the console for details.', 'error');
});

// Utility functions
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        return new Date(dateString).toLocaleDateString();
    } catch (error) {
        return dateString;
    }
};

const formatCurrency = (amount) => {
    if (typeof amount !== 'number') return 'N/A';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
};

// Additional utility functions for better error handling
const safeQuerySelector = (selector) => {
    try {
        return document.querySelector(selector);
    } catch (error) {
        console.warn(`Invalid selector: ${selector}`);
        return null;
    }
};

const safeGetElementById = (id) => {
    try {
        return document.getElementById(id);
    } catch (error) {
        console.warn(`Element not found: ${id}`);
        return null;
    }
};

// Initialize error handling for fetch requests
const safeFetch = async (url, options = {}) => {
    try {
        const response = await fetch(url, options);
        return response;
    } catch (error) {
        console.error(`Fetch error for ${url}:`, error);
        throw error;
    }
};

// Performance optimization - debounce function
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Export functions to global scope for HTML onclick handlers
window.updateUserRole = updateUserRole;
window.updateUserStatus = updateUserStatus;
window.reviewApplication = reviewApplication;
window.approveApplication = approveApplication;
window.rejectApplication = rejectApplication;
window.showNotificationDialog = showNotificationDialog;
window.sendNotification = sendNotification;
window.exportData = exportData;
window.generateReports = generateReports;
window.toggleMaintenanceMode = toggleMaintenanceMode;
window.showModal = showModal;
window.closeModal = closeModal;
window.hideToast = hideToast;
window.showToast = showToast;