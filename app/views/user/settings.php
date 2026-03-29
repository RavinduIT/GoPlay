<!-- Settings Page -->

<div class="settings-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-cog"></i> Settings</h1>
            <p class="subtitle">Manage your account preferences and privacy</p>
        </div>
    </div>

    <!-- Settings Navigation -->
    <div class="settings-layout">
        <!-- Sidebar Navigation -->
        <div class="settings-sidebar">
            <button class="settings-nav-item active" data-section="account">
                <i class="fas fa-user"></i> Account Settings
            </button>
            <button class="settings-nav-item" data-section="security">
                <i class="fas fa-lock"></i> Security & Privacy
            </button>
            <button class="settings-nav-item" data-section="notifications">
                <i class="fas fa-bell"></i> Notification Preferences
            </button>
            <button class="settings-nav-item" data-section="payment">
                <i class="fas fa-credit-card"></i> Payment Methods
            </button>
            <button class="settings-nav-item" data-section="preferences">
                <i class="fas fa-sliders-h"></i> App Preferences
            </button>
            <button class="settings-nav-item" data-section="data">
                <i class="fas fa-database"></i> Data & Privacy
            </button>
        </div>

        <!-- Settings Content -->
        <div class="settings-content">
            <!-- Account Settings Section -->
            <div class="settings-section active" id="account-section">
                <h2>Account Settings</h2>
                <p class="section-description">Update your personal information and contact details</p>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Personal Information</h3>
                        <button class="btn btn-outline btn-sm" onclick="editPersonalInfo()">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Full Name</label>
                            <span id="display-name">Loading...</span>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <span id="display-email">Loading...</span>
                        </div>
                        <div class="info-item">
                            <label>Phone</label>
                            <span id="display-phone">Not set</span>
                        </div>
                        <div class="info-item">
                            <label>Date of Birth</label>
                            <span id="display-dob">Not set</span>
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Account Status</h3>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Account Type</label>
                            <span id="display-account-type">User</span>
                        </div>
                        <div class="info-item">
                            <label>Member Since</label>
                            <span id="display-member-since">Loading...</span>
                        </div>
                        <div class="info-item">
                            <label>Status</label>
                            <span class="badge badge-success" id="display-status">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security & Privacy Section -->
            <div class="settings-section" id="security-section">
                <h2>Security & Privacy</h2>
                <p class="section-description">Manage your password and security preferences</p>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Password</h3>
                    </div>
                    <p class="card-description">Keep your account secure with a strong password</p>
                    <button class="btn btn-primary" onclick="changePassword()">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </div>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Two-Factor Authentication</h3>
                    </div>
                    <p class="card-description">Add an extra layer of security to your account</p>
                    <div class="toggle-setting">
                        <label class="toggle-label">
                            <input type="checkbox" id="2fa-toggle" class="toggle-input">
                            <span class="toggle-slider"></span>
                            <span class="toggle-text">Enable Two-Factor Authentication</span>
                        </label>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Login Activity</h3>
                    </div>
                    <p class="card-description">View recent login activity on your account</p>
                    <button class="btn btn-outline" onclick="viewLoginActivity()">
                        <i class="fas fa-history"></i> View Activity
                    </button>
                </div>
            </div>

            <!-- Notification Preferences Section -->
            <div class="settings-section" id="notifications-section">
                <h2>Notification Preferences</h2>
                <p class="section-description">Choose what notifications you want to receive</p>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Email Notifications</h3>
                    </div>
                    <div class="toggle-settings-list">
                        <div class="toggle-setting">
                            <label class="toggle-label">
                                <input type="checkbox" class="toggle-input" checked>
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Booking Confirmations</span>
                            </label>
                        </div>
                        <div class="toggle-setting">
                            <label class="toggle-label">
                                <input type="checkbox" class="toggle-input" checked>
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Order Updates</span>
                            </label>
                        </div>
                        <div class="toggle-setting">
                            <label class="toggle-label">
                                <input type="checkbox" class="toggle-input">
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Promotional Emails</span>
                            </label>
                        </div>
                        <div class="toggle-setting">
                            <label class="toggle-label">
                                <input type="checkbox" class="toggle-input" checked>
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Newsletter</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Push Notifications</h3>
                    </div>
                    <div class="toggle-settings-list">
                        <div class="toggle-setting">
                            <label class="toggle-label">
                                <input type="checkbox" class="toggle-input" checked>
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Booking Reminders</span>
                            </label>
                        </div>
                        <div class="toggle-setting">
                            <label class="toggle-label">
                                <input type="checkbox" class="toggle-input" checked>
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Payment Confirmations</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Section -->
            <div class="settings-section" id="payment-section">
                <h2>Payment Methods</h2>
                <p class="section-description">Manage your saved payment methods</p>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Saved Cards</h3>
                        <button class="btn btn-primary btn-sm" onclick="addPaymentMethod()">
                            <i class="fas fa-plus"></i> Add Card
                        </button>
                    </div>
                    <div class="payment-methods-list" id="payment-methods-list">
                        <p class="text-muted">No saved payment methods</p>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Billing Address</h3>
                        <button class="btn btn-outline btn-sm" onclick="editBillingAddress()">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                    <div class="billing-address" id="billing-address">
                        <p class="text-muted">No billing address set</p>
                    </div>
                </div>
            </div>

            <!-- App Preferences Section -->
            <div class="settings-section" id="preferences-section">
                <h2>App Preferences</h2>
                <p class="section-description">Customize your app experience</p>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Display Settings</h3>
                    </div>
                    <div class="toggle-settings-list">
                        <div class="toggle-setting">
                            <label class="toggle-label">
                                <input type="checkbox" id="dark-mode-toggle" class="toggle-input">
                                <span class="toggle-slider"></span>
                                <span class="toggle-text">Dark Mode</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Language & Region</h3>
                    </div>
                    <div class="form-group">
                        <label>Language</label>
                        <select class="form-select">
                            <option value="en">English</option>
                            <option value="si">Sinhala</option>
                            <option value="ta">Tamil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Currency</label>
                        <select class="form-select">
                            <option value="LKR">LKR - Sri Lankan Rupee</option>
                            <option value="USD">USD - US Dollar</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Data & Privacy Section -->
            <div class="settings-section" id="data-section">
                <h2>Data & Privacy</h2>
                <p class="section-description">Control your data and privacy settings</p>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Download Your Data</h3>
                    </div>
                    <p class="card-description">Get a copy of all your data stored in our system</p>
                    <button class="btn btn-outline" onclick="downloadData()">
                        <i class="fas fa-download"></i> Download Data
                    </button>
                </div>

                <div class="settings-card">
                    <div class="card-header">
                        <h3>Delete Account</h3>
                    </div>
                    <p class="card-description">Permanently delete your account and all associated data</p>
                    <button class="btn btn-danger" onclick="deleteAccount()">
                        <i class="fas fa-trash"></i> Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1e40af;
    --primary-light: #dbeafe;
    --danger-color: #ef4444;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --background-gray: #f9fafb;
}

.settings-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--border-color);
}

.header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.header-content h1 i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.subtitle {
    color: var(--text-secondary);
    font-size: 1rem;
}

.settings-layout {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.settings-sidebar {
    background: white;
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.settings-nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
    padding: 0.875rem 1rem;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
    margin-bottom: 0.25rem;
}

.settings-nav-item:hover {
    background: var(--background-gray);
    color: var(--text-primary);
}

.settings-nav-item.active {
    background: var(--primary-light);
    color: var(--primary-color);
}

.settings-content {
    min-height: 500px;
}

.settings-section {
    display: none;
}

.settings-section.active {
    display: block;
}

.settings-section h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.section-description {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.settings-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.card-header h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.card-description {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 0.05em;
}

.info-item span {
    font-size: 0.9375rem;
    color: var(--text-primary);
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-success {
    background: #d1fae5;
    color: #10b981;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.875rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-outline {
    background: white;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.btn-outline:hover {
    background: var(--primary-light);
}

.btn-danger {
    background: var(--danger-color);
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.toggle-settings-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.toggle-setting {
    display: flex;
    align-items: center;
}

.toggle-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.toggle-input {
    display: none;
}

.toggle-slider {
    position: relative;
    width: 44px;
    height: 24px;
    background: #d1d5db;
    border-radius: 12px;
    transition: all 0.2s;
}

.toggle-slider::before {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    top: 3px;
    left: 3px;
    transition: all 0.2s;
}

.toggle-input:checked + .toggle-slider {
    background: var(--primary-color);
}

.toggle-input:checked + .toggle-slider::before {
    transform: translateX(20px);
}

.toggle-text {
    font-size: 0.9375rem;
    color: var(--text-primary);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-select {
    width: 100%;
    padding: 0.625rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-primary);
    background: white;
    cursor: pointer;
}

.form-select:focus {
    outline: none;
    border-color: var(--primary-color);
}

.text-muted {
    color: var(--text-secondary);
    font-style: italic;
}

@media (max-width: 768px) {
    .settings-layout {
        grid-template-columns: 1fr;
    }

    .settings-sidebar {
        position: static;
        display: flex;
        overflow-x: auto;
        padding: 0.5rem;
    }

    .settings-nav-item {
        white-space: nowrap;
        margin-right: 0.5rem;
        margin-bottom: 0;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadUserSettings();

    // Settings navigation
    document.querySelectorAll('.settings-nav-item').forEach(item => {
        item.addEventListener('click', function() {
            const section = this.dataset.section;

            // Update active nav item
            document.querySelectorAll('.settings-nav-item').forEach(nav => nav.classList.remove('active'));
            this.classList.add('active');

            // Update active section
            document.querySelectorAll('.settings-section').forEach(sec => sec.classList.remove('active'));
            document.getElementById(`${section}-section`).classList.add('active');
        });
    });

    // Dark mode toggle
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', function() {
            if (this.checked) {
                // Implement dark mode
                console.log('Dark mode enabled');
            } else {
                console.log('Dark mode disabled');
            }
        });
    }
});

async function loadUserSettings() {
    try {
        const response = await fetch('/api/user/profile');
        const data = await response.json();

        if (data.success && data.user) {
            const user = data.user;
            document.getElementById('display-name').textContent = `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Not set';
            document.getElementById('display-email').textContent = user.email || 'Not set';
            document.getElementById('display-phone').textContent = user.phone || 'Not set';
            document.getElementById('display-dob').textContent = user.date_of_birth || 'Not set';
            document.getElementById('display-account-type').textContent = capitalizeFirst(user.user_type || 'user');
            document.getElementById('display-status').textContent = capitalizeFirst(user.status || 'active');

            if (user.created_at) {
                const date = new Date(user.created_at);
                document.getElementById('display-member-since').textContent = date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long'
                });
            }
        }
    } catch (error) {
        console.error('Error loading user settings:', error);
    }
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function editPersonalInfo() {
    window.location.href = '/user/profile';
}

function changePassword() {
    document.getElementById('pwd-modal').style.display = 'flex';
    document.getElementById('pwd-form').reset();
    document.getElementById('pwd-error').textContent = '';
    document.getElementById('pwd-success').textContent = '';
}

function viewLoginActivity() {
    // TODO: Implement login activity modal
    alert('Login activity functionality coming soon!');
}

function addPaymentMethod() {
    // TODO: Implement add payment method
    alert('Add payment method functionality coming soon!');
}

function editBillingAddress() {
    // TODO: Implement edit billing address
    alert('Edit billing address functionality coming soon!');
}

function downloadData() {
    // TODO: Implement data download
    alert('Your data download will begin shortly. This feature is coming soon!');
}

function deleteAccount() {
    const confirmed = confirm('Are you sure you want to delete your account? This action cannot be undone.');
    if (confirmed) {
        const doubleConfirm = confirm('This will permanently delete all your data. Are you absolutely sure?');
        if (doubleConfirm) {
            // TODO: Implement account deletion
            alert('Account deletion functionality coming soon!');
        }
    }
}

function closePwdModal() {
    document.getElementById('pwd-modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('pwd-form');
    if (!form) return;
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const errEl  = document.getElementById('pwd-error');
        const okEl   = document.getElementById('pwd-success');
        const btn    = document.getElementById('pwd-submit-btn');
        errEl.textContent = '';
        okEl.textContent  = '';

        const current  = document.getElementById('pwd-current').value;
        const next     = document.getElementById('pwd-new').value;
        const confirm  = document.getElementById('pwd-confirm').value;

        if (next !== confirm) {
            errEl.textContent = 'New passwords do not match.';
            return;
        }
        if (next.length < 8) {
            errEl.textContent = 'New password must be at least 8 characters.';
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Saving…';
        try {
            const res  = await fetch('/api/user/change-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ current_password: current, new_password: next })
            });
            const data = await res.json();
            if (data.success) {
                okEl.textContent = data.message || 'Password changed successfully.';
                form.reset();
                setTimeout(closePwdModal, 1500);
            } else {
                errEl.textContent = data.error || 'Failed to change password.';
            }
        } catch (err) {
            errEl.textContent = 'Network error. Please try again.';
        } finally {
            btn.disabled = false;
            btn.textContent = 'Change Password';
        }
    });
});
</script>

<!-- Change Password Modal -->
<div id="pwd-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:2rem; width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,0.2); position:relative;">
        <button onclick="closePwdModal()" style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280;">&times;</button>
        <h3 style="margin-bottom:1.25rem; font-size:1.15rem; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-key" style="color:#2563eb;"></i> Change Password
        </h3>
        <form id="pwd-form">
            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.4rem;">Current Password</label>
                <input type="password" id="pwd-current" required style="width:100%; padding:0.6rem 0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.95rem; box-sizing:border-box;" placeholder="Enter current password">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.4rem;">New Password</label>
                <input type="password" id="pwd-new" required minlength="8" style="width:100%; padding:0.6rem 0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.95rem; box-sizing:border-box;" placeholder="At least 8 characters">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:0.875rem; font-weight:500; margin-bottom:0.4rem;">Confirm New Password</label>
                <input type="password" id="pwd-confirm" required minlength="8" style="width:100%; padding:0.6rem 0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.95rem; box-sizing:border-box;" placeholder="Re-enter new password">
            </div>
            <div id="pwd-error"  style="color:#dc2626; font-size:0.875rem; margin-bottom:0.75rem; min-height:1.2em;"></div>
            <div id="pwd-success" style="color:#059669; font-size:0.875rem; margin-bottom:0.75rem; min-height:1.2em;"></div>
            <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                <button type="button" onclick="closePwdModal()" style="padding:0.55rem 1.1rem; border:1px solid #d1d5db; border-radius:8px; background:white; cursor:pointer; font-size:0.9rem;">Cancel</button>
                <button type="submit" id="pwd-submit-btn" style="padding:0.55rem 1.25rem; border:none; border-radius:8px; background:#2563eb; color:white; font-weight:600; cursor:pointer; font-size:0.9rem;">Change Password</button>
            </div>
        </form>
    </div>
</div>
