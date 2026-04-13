<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<!-- Profile Page Content - Uses main.php layout -->

<div class="profile-container">
    <!-- Profile Header Section -->
    <div class="profile-header">
        <div class="profile-banner">
            <div class="profile-info">
                <div class="avatar-section">
                    <div class="avatar-container">
                        <img src="<?= htmlspecialchars($user['profile_picture'] ?? '/public/assets/images/default-avatar.png') ?>" 
                             alt="Profile Picture" 
                             id="profile-avatar"
                             onerror="this.src='<?= $_base ?>/public/assets/images/default-avatar.png'">
                        <button class="avatar-upload-btn" onclick="document.getElementById('avatar-input').click()" title="Change Profile Picture">
                            <i class="fas fa-camera"></i>
                        </button>
                        <input type="file" id="avatar-input" accept="image/*" style="display: none;">
                    </div>
                </div>
                <div class="user-details">
                    <h1><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'User') ?></h1>
                    <p class="user-email">
                        <i class="fas fa-envelope"></i>
                        <?= htmlspecialchars($user['email'] ?? '') ?>
                    </p>
                    <p class="user-type">
                        <i class="fas fa-user-tag"></i>
                        <?= ucfirst(str_replace('_', ' ', $user['user_type'] ?? 'user')) ?>
                    </p>
                    <p class="member-since">
                        <i class="fas fa-calendar-alt"></i>
                        Member since <?= isset($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : 'N/A' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-content">
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['total_bookings'] ?? 0) ?></h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['total_orders'] ?? 0) ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3>LKR <?= number_format($stats['total_spent'] ?? 0, 2) ?></h3>
                    <p>Total Spent</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3><?= ucfirst($user['status'] ?? 'unknown') ?></h3>
                    <p>Account Status</p>
                </div>
            </div>
        </div>

        <!-- Profile Sections -->
        <div class="profile-sections">
            <!-- Personal Information -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-user"></i> Personal Information</h2>
                    <button class="edit-btn" onclick="toggleEdit('personal')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
                <div class="section-content" id="personal-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>First Name</label>
                            <span id="first_name-display"><?= htmlspecialchars($user['first_name'] ?? 'Not provided') ?></span>
                            <input type="text" id="first_name-edit" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" style="display: none;" placeholder="Enter your first name">
                        </div>
                        <div class="info-item">
                            <label>Last Name</label>
                            <span id="last_name-display"><?= htmlspecialchars($user['last_name'] ?? 'Not provided') ?></span>
                            <input type="text" id="last_name-edit" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" style="display: none;" placeholder="Enter your last name">
                        </div>
                        <div class="info-item">
                            <label>Phone Number</label>
                            <span id="phone-display"><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></span>
                            <input type="tel" id="phone-edit" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="display: none;" placeholder="+94 71 234 5678">
                        </div>
                        <div class="info-item">
                            <label>Date of Birth</label>
                            <span id="date_of_birth-display"><?= 
                                isset($user['date_of_birth']) && $user['date_of_birth'] 
                                ? date('F j, Y', strtotime($user['date_of_birth'])) 
                                : 'Not provided' 
                            ?></span>
                            <input type="date" id="date_of_birth-edit" value="<?= $user['date_of_birth'] ?? '' ?>" style="display: none;">
                        </div>
                        <div class="info-item">
                            <label>Email Address</label>
                            <span><?= htmlspecialchars($user['email'] ?? 'Not provided') ?></span>
                            <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">Email cannot be changed. Contact support if needed.</small>
                        </div>
                        <div class="info-item">
                            <label>Username</label>
                            <span><?= htmlspecialchars($user['username'] ?? 'Not provided') ?></span>
                            <small style="color: #666; font-size: 0.85rem; margin-top: 5px; display: block;">Username cannot be changed.</small>
                        </div>
                    </div>
                    <div class="edit-actions" id="personal-actions" style="display: none;">
                        <button class="btn btn-primary" onclick="saveChanges('personal')">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button class="btn btn-secondary" onclick="cancelEdit('personal')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-shield-alt"></i> Security Settings</h2>
                </div>
                <div class="section-content">
                    <div class="security-item">
                        <div class="security-info">
                            <h4>Password</h4>
                            <p>Keep your account secure with a strong password</p>
                            <small>Last updated: <?= isset($user['updated_at']) ? date('F j, Y', strtotime($user['updated_at'])) : 'N/A' ?></small>
                        </div>
                        <button class="btn btn-outline" onclick="changePassword()">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </div>
                </div>
            </div>

            <!-- Account Activity -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-activity"></i> Recent Activity</h2>
                    <button class="btn btn-outline" onclick="location.reload()" title="Refresh Activity">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                <div class="section-content">
                    <div class="activity-list" id="activity-list">
                        <div class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Loading recent activity...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="section-card">
                <div class="section-header">
                    <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
                <div class="section-content">
                    <div class="quick-actions">
                        <a href="<?= $_base ?>/book-ground" class="action-btn" title="Book a sports ground">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Book Ground</span>
                        </a>
                        <a href="<?= $_base ?>/book-coach" class="action-btn" title="Book a sports coach">
                            <i class="fas fa-user-tie"></i>
                            <span>Book Coach</span>
                        </a>
                        <a href="<?= $_base ?>/shop" class="action-btn" title="Shop sports equipment">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Shop Equipment</span>
                        </a>
                        <a href="<?= $_base ?>/my-bookings" class="action-btn" title="View your bookings">
                            <i class="fas fa-calendar-alt"></i>
                            <span>My Bookings</span>
                        </a>
                        <a href="<?= $_base ?>/my-orders" class="action-btn" title="View your orders">
                            <i class="fas fa-receipt"></i>
                            <span>My Orders</span>
                        </a>
                        <a href="<?= $_base ?>/cart" class="action-btn" title="View shopping cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Shopping Cart</span>
                        </a>
                        <a href="<?= $_base ?>/news" class="action-btn" title="Read sports news">
                            <i class="fas fa-newspaper"></i>
                            <span>Sports News</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="password-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Change Password</h3>
            <span class="close" onclick="closePasswordModal()" title="Close">&times;</span>
        </div>
        <form id="password-form">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required minlength="1" placeholder="Enter your current password">
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="8" placeholder="Enter a new password">
                <small>Password must be at least 8 characters long and contain a mix of letters, numbers, and symbols</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8" placeholder="Confirm your new password">
                <small>Re-enter your new password to confirm</small>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Change Password
                </button>
                <button type="button" class="btn btn-secondary" onclick="closePasswordModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Profile-specific JavaScript -->
<script src="<?= $_base ?>/public/js/pages/user-profile.js"></script>