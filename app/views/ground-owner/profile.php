<?php
$title = 'Ground Owner Profile - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-profile.css'
];
$additionalJS = ['/public/js/pages/ground-owner-profile.js'];
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary-color: #0891b2;
        --success-color: #059669;
        --danger-color: #dc2626;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --background-light: #f9fafb;
        --border-color: #e5e7eb;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .profile-container {
        background: var(--background-light);
        min-height: 100vh;
        padding: 2rem;
    }

    .profile-wrapper {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Section */
    .profile-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 3rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .profile-avatar-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid white;
        object-fit: cover;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .profile-info-header {
        flex: 1;
    }

    .profile-info-header h1 {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .profile-info-header .subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .profile-stats {
        display: flex;
        gap: 3rem;
        margin-top: 1.5rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        display: block;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 0.25rem;
    }

    /* Main Content Grid */
    .profile-grid {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 2rem;
    }

    /* Sidebar */
    .profile-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sidebar-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .sidebar-card h3 {
        font-size: 1.1rem;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-item {
        display: flex;
        align-items: start;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
        color: var(--primary-color);
        font-size: 1.2rem;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        display: block;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .social-links {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .social-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: white;
        transition: transform 0.3s;
    }

    .social-btn:hover {
        transform: translateY(-3px);
    }

    .social-btn.facebook { background: #1877f2; }
    .social-btn.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
    .social-btn.twitter { background: #1da1f2; }
    .social-btn.website { background: var(--success-color); }

    /* Main Content */
    .profile-main-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .content-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .content-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .content-card-header h2 {
        font-size: 1.5rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .edit-btn {
        padding: 0.5rem 1.5rem;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .edit-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Business Information */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .info-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .field-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .field-value {
        font-size: 1.05rem;
        color: var(--text-primary);
        padding: 0.75rem;
        background: var(--background-light);
        border-radius: 6px;
    }

    /* Bio Section */
    .bio-text {
        line-height: 1.8;
        color: var(--text-primary);
        background: var(--background-light);
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
    }

    /* Facilities Grid */
    .facilities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .facility-card {
        background: var(--background-light);
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid var(--border-color);
        transition: all 0.3s;
    }

    .facility-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.15);
        transform: translateY(-4px);
    }

    .facility-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .facility-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .facility-sport {
        font-size: 0.9rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .facility-status {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .facility-status.active {
        background: #d1fae5;
        color: #065f46;
    }

    .facility-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }

    .facility-stat {
        text-align: center;
    }

    .facility-stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .facility-stat-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    /* Payment Information */
    .payment-methods {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .payment-badge {
        padding: 0.5rem 1rem;
        background: var(--primary-color);
        color: white;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Loading State */
    .loading {
        text-align: center;
        padding: 4rem;
        color: var(--text-secondary);
    }

    .loading i {
        font-size: 3rem;
        color: var(--primary-color);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .profile-container {
            padding: 1rem;
        }

        .profile-header {
            padding: 2rem 1.5rem;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .profile-stats {
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
        }

        .profile-info-header h1 {
            font-size: 2rem;
        }

        .facilities-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-container">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="profile-wrapper">
            <!-- Loading State -->
            <div id="loadingState" class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading profile...</p>
            </div>

            <!-- Profile Header -->
            <div id="profileHeader" class="profile-header" style="display: none;">
                <div class="header-content">
                    <img src="/public/assets/images/default-avatar.jpg" alt="Profile" class="profile-avatar-large" id="headerAvatar">
                    <div class="profile-info-header">
                        <h1 id="headerBusinessName">Loading...</h1>
                        <p class="subtitle" id="headerOwnerName">Loading...</p>
                        <div class="profile-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="headerTotalFacilities">0</span>
                                <span class="stat-label">Active Facilities</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="headerYearsInBusiness">0</span>
                                <span class="stat-label">Years in Business</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="headerTotalBookings">0</span>
                                <span class="stat-label">Total Bookings</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="headerRating">0.0</span>
                                <span class="stat-label">Average Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div id="profileContent" class="profile-grid" style="display: none;">
                <!-- Sidebar -->
                <aside class="profile-sidebar">
                    <!-- Contact Information -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-address-card"></i> Contact Information</h3>
                        <div class="info-item">
                            <i class="info-icon fas fa-envelope"></i>
                            <div class="info-content">
                                <span class="info-label">Business Email</span>
                                <span class="info-value" id="sidebarBusinessEmail">-</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="info-icon fas fa-phone"></i>
                            <div class="info-content">
                                <span class="info-label">Business Phone</span>
                                <span class="info-value" id="sidebarBusinessPhone">-</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="info-icon fas fa-envelope"></i>
                            <div class="info-content">
                                <span class="info-label">Personal Email</span>
                                <span class="info-value" id="sidebarPersonalEmail">-</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="info-icon fas fa-mobile-alt"></i>
                            <div class="info-content">
                                <span class="info-label">Personal Phone</span>
                                <span class="info-value" id="sidebarPersonalPhone">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-map-marker-alt"></i> Business Location</h3>
                        <div class="info-item">
                            <i class="info-icon fas fa-building"></i>
                            <div class="info-content">
                                <span class="info-value" id="sidebarBusinessAddress">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-share-alt"></i> Social Media</h3>
                        <div class="social-links" id="socialLinks">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-credit-card"></i> Accepted Payments</h3>
                        <div class="payment-methods" id="paymentMethods">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="profile-main-content">
                    <!-- Business Information -->
                    <div class="content-card">
                        <div class="content-card-header">
                            <h2><i class="fas fa-briefcase"></i> Business Information</h2>
                            <button class="edit-btn" onclick="editProfile()">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                        </div>
                        <div class="info-grid">
                            <div class="info-field">
                                <span class="field-label">Business Registration Number</span>
                                <span class="field-value" id="businessRegNumber">-</span>
                            </div>
                            <div class="info-field">
                                <span class="field-label">Website</span>
                                <span class="field-value" id="websiteUrl">-</span>
                            </div>
                            <div class="info-field">
                                <span class="field-label">Bank Name</span>
                                <span class="field-value" id="bankName">-</span>
                            </div>
                            <div class="info-field">
                                <span class="field-label">Account Number</span>
                                <span class="field-value" id="accountNumber">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- About/Bio -->
                    <div class="content-card">
                        <div class="content-card-header">
                            <h2><i class="fas fa-info-circle"></i> About Us</h2>
                        </div>
                        <p class="bio-text" id="businessBio">No bio available.</p>
                    </div>

                    <!-- Facilities -->
                    <div class="content-card">
                        <div class="content-card-header">
                            <h2><i class="fas fa-building"></i> My Facilities</h2>
                            <a href="/ground-owner/grounds" class="edit-btn">
                                <i class="fas fa-cog"></i> Manage Facilities
                            </a>
                        </div>
                        <div class="facilities-grid" id="facilitiesGrid">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <div class="content-card">
                        <div class="content-card-header">
                            <h2><i class="fas fa-university"></i> Banking Information</h2>
                        </div>
                        <div class="info-grid">
                            <div class="info-field">
                                <span class="field-label">Bank Name</span>
                                <span class="field-value" id="bankNameDetail">-</span>
                            </div>
                            <div class="info-field">
                                <span class="field-label">Account Holder Name</span>
                                <span class="field-value" id="accountHolderName">-</span>
                            </div>
                            <div class="info-field">
                                <span class="field-label">Account Number</span>
                                <span class="field-value" id="accountNumberDetail">-</span>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </main>
</div>

<script>
class GroundOwnerProfile {
    constructor() {
        this.profileData = null;
        this.facilitiesData = null;
        this.init();
    }

    async init() {
        await this.loadProfileData();
        await this.loadFacilities();
        this.renderProfile();
        this.hideLoading();
    }

    async loadProfileData() {
        try {
            const response = await fetch('/api/ground-owner/profile');
            const data = await response.json();

            if (data.success) {
                this.profileData = data.profile;
            } else {
                console.error('Failed to load profile:', data.message);
            }
        } catch (error) {
            console.error('Error loading profile:', error);
        }
    }

    async loadFacilities() {
        try {
            const response = await fetch('/api/ground-owner/grounds');
            const data = await response.json();

            if (data.success) {
                this.facilitiesData = data.grounds;
            }
        } catch (error) {
            console.error('Error loading facilities:', error);
        }
    }

    renderProfile() {
        if (!this.profileData) return;

        const { user, owner_profile, stats } = this.profileData;

        // Header
        document.getElementById('headerBusinessName').textContent = owner_profile?.business_name || 'Business Name Not Set';
        document.getElementById('headerOwnerName').textContent = `${user.first_name} ${user.last_name}`;
        document.getElementById('headerTotalFacilities').textContent = stats?.total_facilities || 0;
        document.getElementById('headerYearsInBusiness').textContent = owner_profile?.years_in_business || 0;
        document.getElementById('headerTotalBookings').textContent = stats?.total_bookings || 0;
        document.getElementById('headerRating').textContent = (stats?.average_rating || 0).toFixed(1);

        if (user.profile_picture) {
            document.getElementById('headerAvatar').src = user.profile_picture;
        }

        // Sidebar
        document.getElementById('sidebarBusinessEmail').textContent = owner_profile?.business_email || '-';
        document.getElementById('sidebarBusinessPhone').textContent = owner_profile?.business_phone || '-';
        document.getElementById('sidebarPersonalEmail').textContent = user.email;
        document.getElementById('sidebarPersonalPhone').textContent = user.phone || '-';
        document.getElementById('sidebarBusinessAddress').textContent = owner_profile?.business_address || '-';

        // Social Media
        this.renderSocialLinks(owner_profile);

        // Payment Methods
        this.renderPaymentMethods(owner_profile);

        // Business Information
        document.getElementById('businessRegNumber').textContent = owner_profile?.business_registration_number || '-';
        document.getElementById('websiteUrl').textContent = owner_profile?.website || '-';
        document.getElementById('bankName').textContent = owner_profile?.bank_name || '-';
        document.getElementById('accountNumber').textContent = owner_profile?.bank_account_number ? '****' + owner_profile.bank_account_number.slice(-4) : '-';

        // Bio
        document.getElementById('businessBio').textContent = owner_profile?.bio || 'No bio available.';

        // Bank Details
        document.getElementById('bankNameDetail').textContent = owner_profile?.bank_name || '-';
        document.getElementById('accountHolderName').textContent = owner_profile?.bank_account_holder || '-';
        document.getElementById('accountNumberDetail').textContent = owner_profile?.bank_account_number || '-';

        // Facilities
        this.renderFacilities();
    }

    renderSocialLinks(profile) {
        const container = document.getElementById('socialLinks');
        let html = '';

        if (profile?.website) {
            html += `<a href="${profile.website}" target="_blank" class="social-btn website"><i class="fas fa-globe"></i></a>`;
        }
        if (profile?.facebook) {
            html += `<a href="${profile.facebook}" target="_blank" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>`;
        }
        if (profile?.instagram) {
            html += `<a href="${profile.instagram}" target="_blank" class="social-btn instagram"><i class="fab fa-instagram"></i></a>`;
        }
        if (profile?.twitter) {
            html += `<a href="${profile.twitter}" target="_blank" class="social-btn twitter"><i class="fab fa-twitter"></i></a>`;
        }

        container.innerHTML = html || '<p style="color: #6b7280;">No social media links</p>';
    }

    renderPaymentMethods(profile) {
        const container = document.getElementById('paymentMethods');

        if (!profile?.payment_methods) {
            container.innerHTML = '<p style="color: #6b7280;">No payment methods set</p>';
            return;
        }

        const methods = typeof profile.payment_methods === 'string'
            ? JSON.parse(profile.payment_methods)
            : profile.payment_methods;

        const icons = {
            cash: 'fa-money-bill',
            card: 'fa-credit-card',
            bank_transfer: 'fa-university',
            mobile_payment: 'fa-mobile-alt'
        };

        const labels = {
            cash: 'Cash',
            card: 'Card',
            bank_transfer: 'Bank Transfer',
            mobile_payment: 'Mobile Payment'
        };

        const html = methods.map(method =>
            `<div class="payment-badge">
                <i class="fas ${icons[method] || 'fa-check'}"></i>
                ${labels[method] || method}
            </div>`
        ).join('');

        container.innerHTML = html;
    }

    renderFacilities() {
        const container = document.getElementById('facilitiesGrid');

        if (!this.facilitiesData || this.facilitiesData.length === 0) {
            container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #6b7280; padding: 2rem;">No facilities yet. <a href="/ground-owner/grounds">Add your first facility</a></p>';
            return;
        }

        const html = this.facilitiesData.map(facility => `
            <div class="facility-card">
                <div class="facility-header">
                    <div>
                        <h4 class="facility-name">${facility.name}</h4>
                        <p class="facility-sport">
                            <i class="${facility.sport_icon || 'fas fa-futbol'}"></i>
                            ${facility.sport_name || 'Sports'}
                        </p>
                    </div>
                    <span class="facility-status ${facility.status}">${facility.status}</span>
                </div>
                <div class="facility-stats">
                    <div class="facility-stat">
                        <div class="facility-stat-value">LKR ${parseFloat(facility.hourly_rate || 0).toLocaleString()}</div>
                        <div class="facility-stat-label">Hourly Rate</div>
                    </div>
                    <div class="facility-stat">
                        <div class="facility-stat-value">${(facility.rating || 0).toFixed(1)} ⭐</div>
                        <div class="facility-stat-label">${facility.total_reviews || 0} Reviews</div>
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('profileHeader').style.display = 'block';
        document.getElementById('profileContent').style.display = 'grid';
    }
}

function editProfile() {
    profileManager.openEditModal();
}

// Initialize
const profileManager = new GroundOwnerProfile();
</script>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Edit Profile</h2>
            <span class="close-modal" onclick="profileManager.closeEditModal()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="editProfileForm">
                <!-- Business Information Section -->
                <div class="form-section">
                    <h3><i class="fas fa-briefcase"></i> Business Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="business_name">Business Name *</label>
                            <input type="text" id="business_name" name="business_name" required>
                        </div>
                        <div class="form-group">
                            <label for="business_registration_number">Registration Number</label>
                            <input type="text" id="business_registration_number" name="business_registration_number">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="business_address">Business Address</label>
                        <input type="text" id="business_address" name="business_address">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="years_in_business">Years in Business</label>
                            <input type="number" id="years_in_business" name="years_in_business" min="0">
                        </div>
                        <div class="form-group">
                            <label for="website">Website</label>
                            <input type="url" id="website" name="website" placeholder="https://">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bio">About / Bio</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Tell customers about your business..."></textarea>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section">
                    <h3><i class="fas fa-phone"></i> Contact Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="business_email">Business Email</label>
                            <input type="email" id="business_email" name="business_email">
                        </div>
                        <div class="form-group">
                            <label for="business_phone">Business Phone</label>
                            <input type="tel" id="business_phone" name="business_phone">
                        </div>
                    </div>
                </div>

                <!-- Social Media Section -->
                <div class="form-section">
                    <h3><i class="fas fa-share-alt"></i> Social Media</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="facebook"><i class="fab fa-facebook"></i> Facebook</label>
                            <input type="url" id="facebook" name="facebook" placeholder="https://facebook.com/...">
                        </div>
                        <div class="form-group">
                            <label for="instagram"><i class="fab fa-instagram"></i> Instagram</label>
                            <input type="url" id="instagram" name="instagram" placeholder="https://instagram.com/...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="twitter"><i class="fab fa-twitter"></i> Twitter</label>
                        <input type="url" id="twitter" name="twitter" placeholder="https://twitter.com/...">
                    </div>
                </div>

                <!-- Banking Information Section -->
                <div class="form-section">
                    <h3><i class="fas fa-university"></i> Banking Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" id="bank_name" name="bank_name">
                        </div>
                        <div class="form-group">
                            <label for="bank_account_holder">Account Holder Name</label>
                            <input type="text" id="bank_account_holder" name="bank_account_holder">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bank_account_number">Account Number</label>
                        <input type="text" id="bank_account_number" name="bank_account_number">
                    </div>
                </div>

                <!-- Payment Methods Section -->
                <div class="form-section">
                    <h3><i class="fas fa-credit-card"></i> Accepted Payment Methods</h3>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="payment_methods" value="cash">
                            <span><i class="fas fa-money-bill"></i> Cash</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="payment_methods" value="card">
                            <span><i class="fas fa-credit-card"></i> Card</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="payment_methods" value="bank_transfer">
                            <span><i class="fas fa-university"></i> Bank Transfer</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="payment_methods" value="mobile_payment">
                            <span><i class="fas fa-mobile-alt"></i> Mobile Payment</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="profileManager.closeEditModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn-save" onclick="profileManager.saveProfile()">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    overflow-y: auto;
    padding: 2rem;
}

.modal-content {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideDown 0.3s ease;
    margin: auto;
}

@keyframes slideDown {
    from { transform: translateY(-30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px 16px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.close-modal {
    font-size: 2rem;
    cursor: pointer;
    transition: transform 0.2s;
    line-height: 1;
}

.close-modal:hover {
    transform: scale(1.2) rotate(90deg);
}

.modal-body {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.form-section h3 {
    font-size: 1.1rem;
    color: var(--text-primary);
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section h3 i {
    color: var(--primary-color);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s;
    font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--background-light);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.checkbox-label:hover {
    border-color: var(--primary-color);
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-label span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.modal-footer {
    padding: 1.5rem 2rem;
    background: var(--background-light);
    border-radius: 0 0 16px 16px;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    position: sticky;
    bottom: 0;
}

.btn-cancel, .btn-save {
    padding: 0.875rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
}

.btn-cancel {
    background: #e5e7eb;
    color: var(--text-primary);
}

.btn-cancel:hover {
    background: #d1d5db;
}

.btn-save {
    background: var(--primary-color);
    color: white;
}

.btn-save:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

@media (max-width: 768px) {
    .modal {
        padding: 1rem;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .checkbox-group {
        grid-template-columns: 1fr;
    }

    .modal-footer {
        flex-direction: column;
    }

    .btn-cancel, .btn-save {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Add edit modal functionality to the existing GroundOwnerProfile class
GroundOwnerProfile.prototype.openEditModal = function() {
    if (!this.profileData) {
        alert('Profile data not loaded yet. Please try again.');
        return;
    }

    const { owner_profile } = this.profileData;

    // Populate form fields
    document.getElementById('business_name').value = owner_profile?.business_name || '';
    document.getElementById('business_registration_number').value = owner_profile?.business_registration_number || '';
    document.getElementById('business_address').value = owner_profile?.business_address || '';
    document.getElementById('years_in_business').value = owner_profile?.years_in_business || '';
    document.getElementById('website').value = owner_profile?.website || '';
    document.getElementById('bio').value = owner_profile?.bio || '';
    document.getElementById('business_email').value = owner_profile?.business_email || '';
    document.getElementById('business_phone').value = owner_profile?.business_phone || '';
    document.getElementById('facebook').value = owner_profile?.facebook || '';
    document.getElementById('instagram').value = owner_profile?.instagram || '';
    document.getElementById('twitter').value = owner_profile?.twitter || '';
    document.getElementById('bank_name').value = owner_profile?.bank_name || '';
    document.getElementById('bank_account_holder').value = owner_profile?.bank_account_holder || '';
    document.getElementById('bank_account_number').value = owner_profile?.bank_account_number || '';

    // Handle payment methods checkboxes
    let paymentMethods = [];
    if (owner_profile?.payment_methods) {
        paymentMethods = typeof owner_profile.payment_methods === 'string'
            ? JSON.parse(owner_profile.payment_methods)
            : owner_profile.payment_methods;
    }

    // Reset all checkboxes
    document.querySelectorAll('input[name="payment_methods"]').forEach(cb => {
        cb.checked = paymentMethods.includes(cb.value);
    });

    // Show modal
    document.getElementById('editProfileModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
};

GroundOwnerProfile.prototype.closeEditModal = function() {
    document.getElementById('editProfileModal').style.display = 'none';
    document.body.style.overflow = '';
};

GroundOwnerProfile.prototype.saveProfile = async function() {
    const form = document.getElementById('editProfileForm');

    // Gather form data
    const formData = {
        business_name: document.getElementById('business_name').value.trim(),
        business_registration_number: document.getElementById('business_registration_number').value.trim(),
        business_address: document.getElementById('business_address').value.trim(),
        years_in_business: parseInt(document.getElementById('years_in_business').value) || 0,
        website: document.getElementById('website').value.trim(),
        bio: document.getElementById('bio').value.trim(),
        business_email: document.getElementById('business_email').value.trim(),
        business_phone: document.getElementById('business_phone').value.trim(),
        facebook: document.getElementById('facebook').value.trim(),
        instagram: document.getElementById('instagram').value.trim(),
        twitter: document.getElementById('twitter').value.trim(),
        bank_name: document.getElementById('bank_name').value.trim(),
        bank_account_holder: document.getElementById('bank_account_holder').value.trim(),
        bank_account_number: document.getElementById('bank_account_number').value.trim()
    };

    // Gather payment methods
    const paymentMethods = [];
    document.querySelectorAll('input[name="payment_methods"]:checked').forEach(cb => {
        paymentMethods.push(cb.value);
    });
    formData.payment_methods = paymentMethods;

    // Validate required fields
    if (!formData.business_name) {
        alert('Business name is required');
        return;
    }

    try {
        const response = await fetch('/api/ground-owner/profile', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (data.success) {
            alert('✅ Profile updated successfully!');
            this.closeEditModal();
            // Reload profile data
            await this.loadProfileData();
            this.renderProfile();
        } else {
            alert('❌ Error: ' + (data.message || 'Failed to update profile'));
        }
    } catch (error) {
        console.error('Error saving profile:', error);
        alert('❌ Failed to save profile. Please try again.');
    }
};

// Close modal when clicking outside
document.getElementById('editProfileModal').addEventListener('click', function(e) {
    if (e.target === this) {
        profileManager.closeEditModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('editProfileModal');
        if (modal.style.display === 'flex') {
            profileManager.closeEditModal();
        }
    }
});
</script>
