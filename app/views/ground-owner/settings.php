<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Settings - Ground Owner Dashboard';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css'
];
$additionalJS = [];
include __DIR__ . '/layout-head.php';
?>

<style>
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary-color: #0891b2;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --background-light: #f9fafb;
        --border-color: #e5e7eb;
    }

    .settings-page {
        background: var(--background-light);
        min-height: 100vh;
        padding: 2rem;
    }

    .settings-wrapper {
        max-width: 900px;
        margin: 0 auto;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
    }

    .page-header h1 {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header p {
        opacity: 0.9;
    }

    .settings-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .section-header h2 {
        font-size: 1.25rem;
        color: var(--text-primary);
    }

    .section-header i {
        color: var(--primary-color);
        font-size: 1.25rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.9rem;
        color: var(--text-secondary);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-group input,
    .form-group select,
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
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-group input:disabled {
        background: var(--background-light);
        cursor: not-allowed;
    }

    .toggle-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: var(--background-light);
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .toggle-info {
        flex: 1;
    }

    .toggle-info h4 {
        font-size: 1rem;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .toggle-info p {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .toggle-switch {
        position: relative;
        width: 50px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        border-radius: 28px;
        transition: 0.4s;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        border-radius: 50%;
        transition: 0.4s;
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: var(--success-color);
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }

    .btn-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
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

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: var(--background-light);
        color: var(--text-primary);
        border: 2px solid var(--border-color);
    }

    .btn-secondary:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    .password-input-group {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .danger-zone {
        border: 2px solid var(--danger-color);
        background: #fef2f2;
    }

    .danger-zone .section-header {
        border-bottom-color: #fecaca;
    }

    .danger-zone .section-header i {
        color: var(--danger-color);
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

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

    @media (max-width: 768px) {
        .settings-page {
            padding: 1rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="settings-page">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <div class="settings-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-cog"></i> Settings</h1>
                <p>Manage your account settings and preferences</p>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading settings...</p>
            </div>

            <!-- Settings Content -->
            <div id="settingsContent" style="display: none;">
                <!-- Alert Messages -->
                <div id="alertContainer"></div>

                <!-- Account Information -->
                <div class="settings-section">
                    <div class="section-header">
                        <i class="fas fa-user"></i>
                        <h2>Account Information</h2>
                    </div>
                    <form id="accountForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" disabled>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="settings-section">
                    <div class="section-header">
                        <i class="fas fa-lock"></i>
                        <h2>Change Password</h2>
                    </div>
                    <form id="passwordForm">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="password-input-group">
                                <input type="password" id="current_password" name="current_password">
                                <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <div class="password-input-group">
                                    <input type="password" id="new_password" name="new_password">
                                    <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <div class="password-input-group">
                                    <input type="password" id="confirm_password" name="confirm_password">
                                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Notification Preferences -->
                <div class="settings-section">
                    <div class="section-header">
                        <i class="fas fa-bell"></i>
                        <h2>Notification Preferences</h2>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <h4>Email Notifications</h4>
                            <p>Receive booking confirmations and updates via email</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="email_notifications" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <h4>SMS Notifications</h4>
                            <p>Receive booking alerts via SMS</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="sms_notifications">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <h4>Marketing Emails</h4>
                            <p>Receive promotional offers and news</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="marketing_emails">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="saveNotificationPreferences()">
                            <i class="fas fa-save"></i> Save Preferences
                        </button>
                    </div>
                </div>

                <!-- Business Hours -->
                <div class="settings-section">
                    <div class="section-header">
                        <i class="fas fa-clock"></i>
                        <h2>Default Operating Hours</h2>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="opening_time">Opening Time</label>
                            <input type="time" id="opening_time" value="06:00">
                        </div>
                        <div class="form-group">
                            <label for="closing_time">Closing Time</label>
                            <input type="time" id="closing_time" value="22:00">
                        </div>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <h4>Open on Weekends</h4>
                            <p>Allow bookings on Saturday and Sunday</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="weekend_open" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="saveBusinessHours()">
                            <i class="fas fa-save"></i> Save Hours
                        </button>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="settings-section danger-zone">
                    <div class="section-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h2>Danger Zone</h2>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <h4>Deactivate Account</h4>
                            <p>Temporarily disable your account. You can reactivate it anytime.</p>
                        </div>
                        <button class="btn btn-secondary" onclick="deactivateAccount()">
                            <i class="fas fa-pause"></i> Deactivate
                        </button>
                    </div>
                    <div class="toggle-group">
                        <div class="toggle-info">
                            <h4>Delete Account</h4>
                            <p>Permanently delete your account and all associated data. This action cannot be undone.</p>
                        </div>
                        <button class="btn btn-danger" onclick="deleteAccount()">
                            <i class="fas fa-trash"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
class SettingsManager {
    constructor() {
        this.userData = null;
        this.init();
    }

    async init() {
        await this.loadUserData();
        this.setupFormHandlers();
        this.hideLoading();
    }

    async loadUserData() {
        try {
            const response = await fetch((window.BASE_URL||'')+'/api/ground-owner/profile');
            const data = await response.json();

            if (data.success) {
                this.userData = data.profile;
                this.populateForm();
            }
        } catch (error) {
            console.error('Error loading user data:', error);
        }
    }

    populateForm() {
        if (!this.userData) return;

        const { user } = this.userData;

        document.getElementById('first_name').value = user.first_name || '';
        document.getElementById('last_name').value = user.last_name || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('phone').value = user.phone || '';
    }

    setupFormHandlers() {
        // Account form
        document.getElementById('accountForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.updateAccountInfo();
        });

        // Password form
        document.getElementById('passwordForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.changePassword();
        });
    }

    async updateAccountInfo() {
        const formData = {
            first_name: document.getElementById('first_name').value.trim(),
            last_name: document.getElementById('last_name').value.trim(),
            phone: document.getElementById('phone').value.trim()
        };

        try {
            const response = await fetch('/api/user/profile', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('success', 'Account information updated successfully!');
            } else {
                this.showAlert('error', data.error || 'Failed to update account');
            }
        } catch (error) {
            this.showAlert('error', 'Failed to update account information');
        }
    }

    async changePassword() {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
            this.showAlert('error', 'Please fill in all password fields');
            return;
        }

        if (newPassword !== confirmPassword) {
            this.showAlert('error', 'New passwords do not match');
            return;
        }

        if (newPassword.length < 8) {
            this.showAlert('error', 'Password must be at least 8 characters long');
            return;
        }

        try {
            const response = await fetch('/api/user/change-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert('success', 'Password changed successfully!');
                document.getElementById('passwordForm').reset();
            } else {
                this.showAlert('error', data.error || 'Failed to change password');
            }
        } catch (error) {
            this.showAlert('error', 'Failed to change password');
        }
    }

    showAlert(type, message) {
        const container = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        container.innerHTML = '';
        container.appendChild(alert);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('settingsContent').style.display = 'block';
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function saveNotificationPreferences() {
    const preferences = {
        email_notifications: document.getElementById('email_notifications').checked,
        sms_notifications: document.getElementById('sms_notifications').checked,
        marketing_emails: document.getElementById('marketing_emails').checked
    };

    // For now, just show success message
    // In production, this would call an API endpoint
    settingsManager.showAlert('success', 'Notification preferences saved!');
}

function saveBusinessHours() {
    const hours = {
        opening_time: document.getElementById('opening_time').value,
        closing_time: document.getElementById('closing_time').value,
        weekend_open: document.getElementById('weekend_open').checked
    };

    // For now, just show success message
    settingsManager.showAlert('success', 'Business hours saved!');
}

function deactivateAccount() {
    if (confirm('Are you sure you want to deactivate your account? You can reactivate it anytime by logging in.')) {
        alert('Account deactivation feature will be implemented soon.');
    }
}

function deleteAccount() {
    if (confirm('WARNING: This will permanently delete your account and all associated data. This action cannot be undone.\n\nAre you absolutely sure?')) {
        if (confirm('Please confirm one more time. All your facilities, bookings, and earnings data will be lost forever.')) {
            alert('Account deletion feature will be implemented soon.');
        }
    }
}

// Initialize
const settingsManager = new SettingsManager();
</script>
<?php include __DIR__ . '/layout-foot.php'; ?>
