<?php
// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - GoPlay Sports Platform</title>
    <link rel="stylesheet" href="/public/css/pages/user-profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include APP_PATH . '/views/components/navbar.php'; ?>
    
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-banner">
                <div class="profile-info">
                    <div class="avatar-section">
                        <div class="avatar-container">
                            <img src="<?= $user['profile_picture'] ?? '/public/assets/images/default-avatar.png' ?>" 
                                 alt="Profile Picture" 
                                 id="profile-avatar">
                            <button class="avatar-upload-btn" onclick="document.getElementById('avatar-input').click()">
                                <i class="fas fa-camera"></i>
                            </button>
                            <input type="file" id="avatar-input" accept="image/*" style="display: none;">
                        </div>
                    </div>
                    <div class="user-details">
                        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                        <p class="user-email">
                            <i class="fas fa-envelope"></i>
                            <?= htmlspecialchars($user['email']) ?>
                        </p>
                        <p class="user-type">
                            <i class="fas fa-user-tag"></i>
                            <?= ucfirst(str_replace('_', ' ', $user['user_type'])) ?>
                        </p>
                        <p class="member-since">
                            <i class="fas fa-calendar-alt"></i>
                            Member since <?= date('F Y', strtotime($user['created_at'])) ?>
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
                        <h3><?= $stats['total_bookings'] ?? 0 ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['total_orders'] ?? 0 ?></h3>
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
                        <h3><?= $user['status'] === 'active' ? 'Active' : 'Inactive' ?></h3>
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
                                <span id="first_name-display"><?= htmlspecialchars($user['first_name']) ?></span>
                                <input type="text" id="first_name-edit" value="<?= htmlspecialchars($user['first_name']) ?>" style="display: none;">
                            </div>
                            <div class="info-item">
                                <label>Last Name</label>
                                <span id="last_name-display"><?= htmlspecialchars($user['last_name']) ?></span>
                                <input type="text" id="last_name-edit" value="<?= htmlspecialchars($user['last_name']) ?>" style="display: none;">
                            </div>
                            <div class="info-item">
                                <label>Phone Number</label>
                                <span id="phone-display"><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></span>
                                <input type="tel" id="phone-edit" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="display: none;">
                            </div>
                            <div class="info-item">
                                <label>Date of Birth</label>
                                <span id="date_of_birth-display"><?= $user['date_of_birth'] ? date('F j, Y', strtotime($user['date_of_birth'])) : 'Not provided' ?></span>
                                <input type="date" id="date_of_birth-edit" value="<?= $user['date_of_birth'] ?? '' ?>" style="display: none;">
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
                                <p>Last updated: <?= date('F j, Y', strtotime($user['updated_at'])) ?></p>
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
                    </div>
                    <div class="section-content">
                        <div class="activity-list" id="activity-list">
                            <div class="loading">Loading recent activity...</div>
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
                            <a href="/book-ground" class="action-btn">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Book Ground</span>
                            </a>
                            <a href="/book-coach" class="action-btn">
                                <i class="fas fa-user-tie"></i>
                                <span>Book Coach</span>
                            </a>
                            <a href="/shop" class="action-btn">
                                <i class="fas fa-shopping-bag"></i>
                                <span>Shop Equipment</span>
                            </a>
                            <a href="/my-bookings" class="action-btn">
                                <i class="fas fa-calendar-alt"></i>
                                <span>My Bookings</span>
                            </a>
                            <a href="/cart" class="action-btn">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Shopping Cart</span>
                            </a>
                            <a href="/news" class="action-btn">
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
                <h3>Change Password</h3>
                <span class="close" onclick="closePasswordModal()">&times;</span>
            </div>
            <form id="password-form">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" id="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" id="new_password" required minlength="8">
                    <small>Password must be at least 8 characters long</small>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" id="confirm_password" required>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Change Password
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closePasswordModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="/public/js/pages/user-profile.js"></script>
</body>
</html>