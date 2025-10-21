<link rel="stylesheet" href="/public/css/pages/admin-settings.css">
<div class="admin-dashboard">
    <?php include __DIR__ . '/../components/admin-sidebar.php'; ?>

    <main class="admin-main">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title">System Settings</h1>
            </div>
            <div class="header-right">
                <button class="save-btn" onclick="saveAllSettings()">
                    <i class="fas fa-save"></i>
                    Save All Changes
                </button>
            </div>
        </header>

        <!-- Settings Content -->
        <div class="settings-content">
            <!-- Settings Navigation Tabs -->
            <div class="settings-tabs">
                <button class="tab-btn active" data-tab="general">
                    <i class="fas fa-cog"></i>
                    General
                </button>
                <button class="tab-btn" data-tab="email">
                    <i class="fas fa-envelope"></i>
                    Email
                </button>
                <button class="tab-btn" data-tab="payment">
                    <i class="fas fa-credit-card"></i>
                    Payment
                </button>
                <button class="tab-btn" data-tab="booking">
                    <i class="fas fa-calendar"></i>
                    Booking
                </button>
                <button class="tab-btn" data-tab="system">
                    <i class="fas fa-server"></i>
                    System
                </button>
                <button class="tab-btn" data-tab="security">
                    <i class="fas fa-shield-alt"></i>
                    Security
                </button>
            </div>

            <!-- Settings Panels -->
            <div class="settings-panels">
                <!-- General Settings -->
                <div class="settings-panel active" id="general-panel">
                    <div class="panel-header">
                        <h2>General Settings</h2>
                        <p>Configure basic site information and preferences</p>
                    </div>

                    <div class="settings-form">
                        <div class="form-group">
                            <label for="site_name">Site Name</label>
                            <input type="text" id="site_name" name="site_name" placeholder="GoPlay Sports Platform">
                            <small>The name of your website</small>
                        </div>

                        <div class="form-group">
                            <label for="site_description">Site Description</label>
                            <textarea id="site_description" name="site_description" rows="3" placeholder="Your ultimate sports hub"></textarea>
                            <small>A brief description of your site</small>
                        </div>

                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" placeholder="contact@goplay.lk">
                            <small>Primary contact email address</small>
                        </div>

                        <div class="form-group">
                            <label for="support_phone">Support Phone</label>
                            <input type="tel" id="support_phone" name="support_phone" placeholder="+94 77 123 4567">
                            <small>Customer support phone number</small>
                        </div>

                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone" name="timezone">
                                <option value="Asia/Colombo">Asia/Colombo (Sri Lanka)</option>
                                <option value="UTC">UTC</option>
                                <option value="Asia/Kolkata">Asia/Kolkata (India)</option>
                            </select>
                            <small>Default timezone for the application</small>
                        </div>

                        <div class="form-group">
                            <label for="default_currency">Default Currency</label>
                            <select id="default_currency" name="default_currency">
                                <option value="LKR">LKR - Sri Lankan Rupee</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                            </select>
                            <small>Currency used for transactions</small>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="maintenance_mode" name="maintenance_mode">
                                <span>Enable Maintenance Mode</span>
                            </label>
                            <small>Temporarily disable the site for maintenance</small>
                        </div>
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="settings-panel" id="email-panel">
                    <div class="panel-header">
                        <h2>Email Configuration</h2>
                        <p>Configure email settings and SMTP</p>
                    </div>

                    <div class="settings-form">
                        <div class="form-group">
                            <label for="email_from_name">From Name</label>
                            <input type="text" id="email_from_name" name="email_from_name" placeholder="GoPlay Sports">
                            <small>Name shown in outgoing emails</small>
                        </div>

                        <div class="form-group">
                            <label for="email_from_address">From Address</label>
                            <input type="email" id="email_from_address" name="email_from_address" placeholder="noreply@goplay.lk">
                            <small>Email address used for outgoing emails</small>
                        </div>

                        <div class="form-group">
                            <label for="smtp_host">SMTP Host</label>
                            <input type="text" id="smtp_host" name="smtp_host" placeholder="smtp.gmail.com">
                            <small>SMTP server hostname</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="smtp_port">SMTP Port</label>
                                <input type="number" id="smtp_port" name="smtp_port" placeholder="587">
                                <small>Usually 587 or 465</small>
                            </div>

                            <div class="form-group">
                                <label for="smtp_encryption">Encryption</label>
                                <select id="smtp_encryption" name="smtp_encryption">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="smtp_username">SMTP Username</label>
                            <input type="text" id="smtp_username" name="smtp_username" placeholder="username">
                            <small>SMTP authentication username</small>
                        </div>

                        <div class="form-group">
                            <label for="smtp_password">SMTP Password</label>
                            <input type="password" id="smtp_password" name="smtp_password" placeholder="••••••••">
                            <small>SMTP authentication password</small>
                        </div>

                        <div class="form-actions">
                            <button class="test-btn" onclick="testEmailConfig()">
                                <i class="fas fa-paper-plane"></i>
                                Send Test Email
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Settings -->
                <div class="settings-panel" id="payment-panel">
                    <div class="panel-header">
                        <h2>Payment Gateway Settings</h2>
                        <p>Configure payment methods and gateways</p>
                    </div>

                    <div class="settings-form">
                        <div class="form-group">
                            <label for="tax_rate">Tax Rate (%)</label>
                            <input type="number" id="tax_rate" name="tax_rate" step="0.01" placeholder="8.4">
                            <small>Tax percentage applied to transactions</small>
                        </div>

                        <div class="form-group">
                            <label for="service_fee_rate">Service Fee (%)</label>
                            <input type="number" id="service_fee_rate" name="service_fee_rate" step="0.01" placeholder="5.0">
                            <small>Platform service fee percentage</small>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="enable_stripe" name="enable_stripe">
                                <span>Enable Stripe Payments</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="stripe_public_key">Stripe Public Key</label>
                            <input type="text" id="stripe_public_key" name="stripe_public_key" placeholder="pk_test_...">
                            <small>Your Stripe publishable key</small>
                        </div>

                        <div class="form-group">
                            <label for="stripe_secret_key">Stripe Secret Key</label>
                            <input type="password" id="stripe_secret_key" name="stripe_secret_key" placeholder="sk_test_...">
                            <small>Your Stripe secret key</small>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="enable_paypal" name="enable_paypal">
                                <span>Enable PayPal Payments</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="paypal_client_id">PayPal Client ID</label>
                            <input type="text" id="paypal_client_id" name="paypal_client_id" placeholder="Your PayPal Client ID">
                            <small>PayPal REST API Client ID</small>
                        </div>

                        <div class="form-group">
                            <label for="paypal_secret">PayPal Secret</label>
                            <input type="password" id="paypal_secret" name="paypal_secret" placeholder="Your PayPal Secret">
                            <small>PayPal REST API Secret</small>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="enable_cash" name="enable_cash" checked>
                                <span>Enable Cash on Delivery</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Booking Settings -->
                <div class="settings-panel" id="booking-panel">
                    <div class="panel-header">
                        <h2>Booking Configuration</h2>
                        <p>Configure booking rules and limitations</p>
                    </div>

                    <div class="settings-form">
                        <div class="form-group">
                            <label for="booking_advance_days">Advance Booking Days</label>
                            <input type="number" id="booking_advance_days" name="booking_advance_days" placeholder="30">
                            <small>Maximum days in advance bookings can be made</small>
                        </div>

                        <div class="form-group">
                            <label for="max_booking_duration">Maximum Booking Duration (hours)</label>
                            <input type="number" id="max_booking_duration" name="max_booking_duration" placeholder="8">
                            <small>Maximum hours per booking session</small>
                        </div>

                        <div class="form-group">
                            <label for="min_booking_duration">Minimum Booking Duration (hours)</label>
                            <input type="number" id="min_booking_duration" name="min_booking_duration" step="0.5" placeholder="1">
                            <small>Minimum hours required per booking</small>
                        </div>

                        <div class="form-group">
                            <label for="cancellation_hours">Cancellation Notice (hours)</label>
                            <input type="number" id="cancellation_hours" name="cancellation_hours" placeholder="24">
                            <small>Minimum hours before booking for cancellation</small>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="allow_same_day_booking" name="allow_same_day_booking">
                                <span>Allow Same Day Bookings</span>
                            </label>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="require_payment_upfront" name="require_payment_upfront">
                                <span>Require Payment Before Booking</span>
                            </label>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="auto_approve_bookings" name="auto_approve_bookings" checked>
                                <span>Auto-Approve Bookings</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="settings-panel" id="system-panel">
                    <div class="panel-header">
                        <h2>System Information & Maintenance</h2>
                        <p>System details and maintenance tools</p>
                    </div>

                    <div class="system-info-grid">
                        <div class="info-card">
                            <div class="info-icon blue">
                                <i class="fas fa-code"></i>
                            </div>
                            <div class="info-content">
                                <h4>PHP Version</h4>
                                <p id="phpVersion">Loading...</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon green">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="info-content">
                                <h4>Database</h4>
                                <p id="dbVersion">Loading...</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon purple">
                                <i class="fas fa-server"></i>
                            </div>
                            <div class="info-content">
                                <h4>Server</h4>
                                <p id="serverInfo">Loading...</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon orange">
                                <i class="fas fa-hdd"></i>
                            </div>
                            <div class="info-content">
                                <h4>Disk Space</h4>
                                <p id="diskSpace">Loading...</p>
                            </div>
                        </div>
                    </div>

                    <div class="maintenance-actions">
                        <h3>Maintenance Actions</h3>
                        <div class="action-grid">
                            <button class="action-card" onclick="clearCache()">
                                <i class="fas fa-broom"></i>
                                <span>Clear Cache</span>
                            </button>

                            <button class="action-card" onclick="backupDatabase()">
                                <i class="fas fa-download"></i>
                                <span>Backup Database</span>
                            </button>

                            <button class="action-card" onclick="viewLogs()">
                                <i class="fas fa-file-alt"></i>
                                <span>View Logs</span>
                            </button>

                            <button class="action-card" onclick="optimizeDatabase()">
                                <i class="fas fa-sync"></i>
                                <span>Optimize DB</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="settings-panel" id="security-panel">
                    <div class="panel-header">
                        <h2>Security Settings</h2>
                        <p>Configure security and access control</p>
                    </div>

                    <div class="settings-form">
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="enable_2fa" name="enable_2fa">
                                <span>Enable Two-Factor Authentication</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="session_timeout">Session Timeout (minutes)</label>
                            <input type="number" id="session_timeout" name="session_timeout" placeholder="60">
                            <small>User session expiry time</small>
                        </div>

                        <div class="form-group">
                            <label for="password_min_length">Minimum Password Length</label>
                            <input type="number" id="password_min_length" name="password_min_length" placeholder="8">
                            <small>Minimum characters required for passwords</small>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="require_strong_password" name="require_strong_password" checked>
                                <span>Require Strong Passwords</span>
                            </label>
                            <small>Enforce uppercase, lowercase, numbers and symbols</small>
                        </div>

                        <div class="form-group">
                            <label for="max_login_attempts">Max Login Attempts</label>
                            <input type="number" id="max_login_attempts" name="max_login_attempts" placeholder="5">
                            <small>Number of failed attempts before lockout</small>
                        </div>

                        <div class="form-group">
                            <label for="lockout_duration">Lockout Duration (minutes)</label>
                            <input type="number" id="lockout_duration" name="lockout_duration" placeholder="30">
                            <small>How long to lock account after max attempts</small>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="enable_activity_logs" name="enable_activity_logs" checked>
                                <span>Enable Activity Logging</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button (Floating) -->
            <div class="floating-save">
                <button class="save-all-btn" onclick="saveAllSettings()">
                    <i class="fas fa-save"></i>
                    Save All Changes
                </button>
            </div>
        </div>
    </main>
</div>

<script src="/public/js/pages/admin-settings.js"></script>