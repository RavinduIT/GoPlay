<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Profile - GoPlay';

// Get data passed from controller
$user = $user ?? null;
$profile = $profile ?? null;

// Personal Details (from users table)
$firstName = $user['first_name'] ?? '';
$lastName = $user['last_name'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
$username = $user['username'] ?? '';
$profilePicture = $user['profile_picture'] ?? 'uploads/default-avatar.png';

// Business Details (from shop_owner_profiles table)
$shopName = $profile['shop_name'] ?? '';
$businessName = $profile['business_name'] ?? '';
$businessPhone = $profile['business_phone'] ?? '';
$businessEmail = $profile['business_email'] ?? '';
$shopAddress = $profile['shop_address'] ?? '';
$shopCity = $profile['shop_city'] ?? '';
$shopPostalCode = $profile['shop_postal_code'] ?? '';
$businessDescription = $profile['business_description'] ?? '';
$shopLogo = $profile['shop_logo'] ?? 'uploads/default-shop-logo.png';
$completionPercentage = $profile['profile_completion_percentage'] ?? 10;
$businessRegNumber = $profile['business_registration_number'] ?? '';
$taxIdNumber = $profile['tax_identification_number'] ?? '';
$yearEstablished = $profile['year_established'] ?? '';
$businessType = $profile['business_type'] ?? 'sole_proprietor';
$numEmployees = $profile['number_of_employees'] ?? 0;
$website = $profile['website_url'] ?? '';
$facebook = $profile['facebook'] ?? '';
$instagram = $profile['instagram'] ?? '';
$brandNames = $profile['brand_names'] ?? '';
$bankName = $profile['bank_name'] ?? '';
$bankAccountNumber = $profile['bank_account_number'] ?? '';
$bankAccountHolder = $profile['bank_account_holder'] ?? '';
$bankBranch = $profile['bank_branch'] ?? '';
?>

<link rel="stylesheet" href="<?= $_base ?>/public/css/pages/shop-owner-dashboard.css">

<div class="shop-owner-dashboard">
  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-main">
    
    <div class="profile-header-section">
      <div class="profile-avatar-container">
        <img src="<?= $_base ?>/public/<?php echo htmlspecialchars($shopLogo); ?>" alt="Shop Logo" id="logo-preview" class="profile-avatar">
        <form id="logo-form" enctype="multipart/form-data">
          <label class="btn-change-avatar">
            <i class="fas fa-camera"></i>
            <input type="file" name="shop_logo" id="shop_logo_input" accept="image/*" hidden>
          </label>
        </form>
      </div>
      <div class="profile-header-info">
        <h1 class="shop-name"><?php echo htmlspecialchars($shopName ?: 'Your Shop Name'); ?></h1>
        <p class="shop-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($shopCity ?: 'Location'); ?></p>
        <div class="completion-badge-large">
          <div class="completion-circle">
            <svg class="progress-ring" width="60" height="60">
              <circle class="progress-ring-circle" stroke="#e0e7ff" stroke-width="4" fill="transparent" r="26" cx="30" cy="30"/>
              <circle class="progress-ring-circle progress" stroke="#2563eb" stroke-width="4" fill="transparent" r="26" cx="30" cy="30"
                      style="stroke-dasharray: 163.36; stroke-dashoffset: <?php echo 163.36 * (1 - $completionPercentage / 100); ?>;"/>
            </svg>
            <span class="completion-percentage"><?php echo $completionPercentage; ?>%</span>
          </div>
          <span class="completion-text">Profile Complete</span>
        </div>
      </div>
    </div>
    
    <div class="dashboard-content">

    <!-- Success/Error Messages -->
    <div id="message-container"></div>

    <!-- Tab Navigation -->
    <div class="profile-tabs">
      <button class="tab-btn active" data-tab="personal">
        <i class="fas fa-user"></i> Personal Details
      </button>
      <button class="tab-btn" data-tab="business">
        <i class="fas fa-store"></i> Business Information
      </button>
      <button class="tab-btn" data-tab="banking">
        <i class="fas fa-university"></i> Banking Details
      </button>
      <button class="tab-btn" data-tab="social">
        <i class="fas fa-share-alt"></i> Social Media
      </button>
    </div>

    <!-- Tab Content -->
    <div class="profile-content">
      
      <!-- Personal Details Tab -->
      <div class="tab-content active" id="personal-tab">
        <div class="content-card">
          <div class="card-header">
            <h2><i class="fas fa-user-circle"></i> Personal Information</h2>
            <p class="card-subtitle">Your personal account details</p>
          </div>
          <form id="personal-form" class="profile-form">
            <div class="form-grid">
              <div class="form-group">
                <label><i class="fas fa-user"></i> First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($firstName); ?>" class="form-input" readonly>
                <small class="form-hint">Contact admin to change</small>
              </div>
              <div class="form-group">
                <label><i class="fas fa-user"></i> Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($lastName); ?>" class="form-input" readonly>
                <small class="form-hint">Contact admin to change</small>
              </div>
            </div>
            
            <div class="form-grid">
              <div class="form-group">
                <label><i class="fas fa-at"></i> Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" class="form-input" readonly>
              </div>
              <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="form-input" readonly>
                <small class="form-hint">Primary contact email</small>
              </div>
            </div>

            <div class="form-group">
              <label><i class="fas fa-phone"></i> Personal Phone</label>
              <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" class="form-input" readonly>
            </div>
          </form>
        </div>
      </div>

      <!-- Business Information Tab -->
      <div class="tab-content" id="business-tab">
        <div class="content-card">
          <div class="card-header">
            <h2><i class="fas fa-store-alt"></i> Business Information</h2>
            <p class="card-subtitle">Details about your shop and business</p>
          </div>
          <form id="business-form" class="profile-form">
            <div class="form-section">
              <h3 class="section-title"><i class="fas fa-info-circle"></i> Basic Information</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>Shop Name *</label>
                  <input type="text" name="shop_name" value="<?php echo htmlspecialchars($shopName); ?>" class="form-input" required>
                </div>
                <div class="form-group">
                  <label>Business Name *</label>
                  <input type="text" name="business_name" value="<?php echo htmlspecialchars($businessName); ?>" class="form-input" required>
                </div>
              </div>

              <div class="form-grid grid-3">
                <div class="form-group">
                  <label>Business Type</label>
                  <select name="business_type" class="form-input">
                    <option value="sole_proprietor" <?php echo $businessType === 'sole_proprietor' ? 'selected' : ''; ?>>Sole Proprietor</option>
                    <option value="partnership" <?php echo $businessType === 'partnership' ? 'selected' : ''; ?>>Partnership</option>
                    <option value="private_limited" <?php echo $businessType === 'private_limited' ? 'selected' : ''; ?>>Private Limited</option>
                    <option value="public_limited" <?php echo $businessType === 'public_limited' ? 'selected' : ''; ?>>Public Limited</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Year Established</label>
                  <input type="number" name="year_established" value="<?php echo htmlspecialchars($yearEstablished); ?>" class="form-input" min="1900" max="<?php echo date('Y'); ?>">
                </div>
                <div class="form-group">
                  <label>Number of Employees</label>
                  <input type="number" name="number_of_employees" value="<?php echo htmlspecialchars($numEmployees); ?>" class="form-input" min="0">
                </div>
              </div>
            </div>

            <div class="form-section">
              <h3 class="section-title"><i class="fas fa-file-alt"></i> Registration Details</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>Business Registration Number</label>
                  <input type="text" name="business_registration_number" value="<?php echo htmlspecialchars($businessRegNumber); ?>" class="form-input">
                </div>
                <div class="form-group">
                  <label>Tax Identification Number</label>
                  <input type="text" name="tax_identification_number" value="<?php echo htmlspecialchars($taxIdNumber); ?>" class="form-input">
                </div>
              </div>
            </div>

            <div class="form-section">
              <h3 class="section-title"><i class="fas fa-phone-alt"></i> Contact Information</h3>
              <div class="form-grid">
                <div class="form-group">
                  <label>Business Phone *</label>
                  <input type="tel" name="business_phone" value="<?php echo htmlspecialchars($businessPhone); ?>" class="form-input" required>
                </div>
                <div class="form-group">
                  <label>Business Email *</label>
                  <input type="email" name="business_email" value="<?php echo htmlspecialchars($businessEmail); ?>" class="form-input" required>
                </div>
              </div>
            </div>

            <div class="form-section">
              <h3 class="section-title"><i class="fas fa-map-marked-alt"></i> Shop Address</h3>
              <div class="form-group">
                <label>Street Address *</label>
                <input type="text" name="shop_address" value="<?php echo htmlspecialchars($shopAddress); ?>" class="form-input" required>
              </div>
              <div class="form-grid">
                <div class="form-group">
                  <label>City *</label>
                  <input type="text" name="shop_city" value="<?php echo htmlspecialchars($shopCity); ?>" class="form-input" required>
                </div>
                <div class="form-group">
                  <label>Postal Code</label>
                  <input type="text" name="shop_postal_code" value="<?php echo htmlspecialchars($shopPostalCode); ?>" class="form-input">
                </div>
              </div>
            </div>

            <div class="form-section">
              <h3 class="section-title"><i class="fas fa-align-left"></i> Business Description</h3>
              <div class="form-group">
                <textarea name="business_description" rows="5" class="form-input" placeholder="Describe your shop and what you sell..."><?php echo htmlspecialchars($businessDescription); ?></textarea>
              </div>
            </div>

            <div class="form-section">
              <h3 class="section-title"><i class="fas fa-tags"></i> Products & Brands</h3>
              <div class="form-group">
                <label>Brand Names</label>
                <textarea name="brand_names" rows="3" class="form-input" placeholder="Enter brand names separated by commas..."><?php echo htmlspecialchars($brandNames); ?></textarea>
                <small class="form-hint">Example: Nike, Adidas, Puma, Wilson</small>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <span class="btn-text">Save Business Information</span>
                <span class="btn-loading" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Banking Details Tab -->
      <div class="tab-content" id="banking-tab">
        <div class="content-card">
          <div class="card-header">
            <h2><i class="fas fa-university"></i> Banking Details</h2>
            <p class="card-subtitle">For receiving payments from sales</p>
          </div>
          <form id="banking-form" class="profile-form">
            <div class="form-grid">
              <div class="form-group">
                <label><i class="fas fa-landmark"></i> Bank Name</label>
                <input type="text" name="bank_name" value="<?php echo htmlspecialchars($bankName); ?>" class="form-input" placeholder="e.g., Commercial Bank of Ceylon">
              </div>
              <div class="form-group">
                <label><i class="fas fa-building"></i> Branch</label>
                <input type="text" name="bank_branch" value="<?php echo htmlspecialchars($bankBranch); ?>" class="form-input" placeholder="e.g., Colombo Main Branch">
              </div>
            </div>

            <div class="form-group">
              <label><i class="fas fa-user-tie"></i> Account Holder Name</label>
              <input type="text" name="bank_account_holder" value="<?php echo htmlspecialchars($bankAccountHolder); ?>" class="form-input" placeholder="Full name as per bank account">
            </div>

            <div class="form-group">
              <label><i class="fas fa-credit-card"></i> Account Number</label>
              <input type="text" name="bank_account_number" value="<?php echo htmlspecialchars($bankAccountNumber); ?>" class="form-input" placeholder="Enter your bank account number">
            </div>

            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i>
              <div>
                <strong>Secure Information</strong>
                <p>Your banking details are encrypted and will only be used for processing payments.</p>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <span class="btn-text">Save Banking Details</span>
                <span class="btn-loading" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Social Media Tab -->
      <div class="tab-content" id="social-tab">
        <div class="content-card">
          <div class="card-header">
            <h2><i class="fas fa-share-alt"></i> Social Media & Online Presence</h2>
            <p class="card-subtitle">Connect your social media accounts</p>
          </div>
          <form id="social-form" class="profile-form">
            <div class="form-group">
              <label><i class="fas fa-globe"></i> Website URL</label>
              <input type="url" name="website_url" value="<?php echo htmlspecialchars($website); ?>" class="form-input" placeholder="https://yourwebsite.com">
            </div>

            <div class="form-group">
              <label><i class="fab fa-facebook"></i> Facebook Page</label>
              <input type="text" name="facebook" value="<?php echo htmlspecialchars($facebook); ?>" class="form-input" placeholder="https://facebook.com/yourpage">
            </div>

            <div class="form-group">
              <label><i class="fab fa-instagram"></i> Instagram Handle</label>
              <input type="text" name="instagram" value="<?php echo htmlspecialchars($instagram); ?>" class="form-input" placeholder="@yourhandle">
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <span class="btn-text">Save Social Links</span>
                <span class="btn-loading" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
    </div>
  </main>
</div>

<style>
/* Profile Header Section */
.profile-header-section {
  background: white;
  padding: 40px 40px 30px;
  display: flex;
  align-items: center;
  gap: 30px;
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.profile-avatar-container {
  position: relative;
  flex-shrink: 0;
}
.profile-avatar {
  width: 140px;
  height: 140px;
  border-radius: 16px;
  object-fit: cover;
  border: 4px solid white;
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.btn-change-avatar {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: #2563eb;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
}
.btn-change-avatar:hover { background: #1d4ed8; transform: scale(1.1); }

.profile-header-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.shop-name {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 8px 0;
  line-height: 1.2;
}
.shop-location {
  color: #6b7280;
  font-size: 1rem;
  margin: 0;
}
.shop-location i { margin-right: 5px; color: #ef4444; }

.completion-badge-large {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-top: 15px;
}
.completion-circle {
  position: relative;
  width: 60px;
  height: 60px;
}
.progress-ring {
  transform: rotate(-90deg);
}
.progress-ring-circle {
  transition: stroke-dashoffset 0.35s;
}
.completion-percentage {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 14px;
  font-weight: 700;
  color: #2563eb;
}
.completion-text {
  font-size: 0.9rem;
  color: #6b7280;
  font-weight: 600;
}

/* Messages */
#message-container {
  padding: 0 40px;
  margin-top: 20px;
}
.alert {
  padding: 14px 18px;
  border-radius: 10px;
  margin-bottom: 20px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 12px;
  animation: slideDown 0.3s ease;
}
@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.alert i { font-size: 1.2rem; }
.alert-success {
  background: #d1fae5;
  color: #065f46;
  border: 1px solid #6ee7b7;
}
.alert-error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}
.alert-info {
  background: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}
.alert-info div {
  flex: 1;
}
.alert-info strong {
  display: block;
  margin-bottom: 4px;
}
.alert-info p {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 400;
}

/* Tab Navigation */
.profile-tabs {
  display: flex;
  gap: 0;
  background: white;
  padding: 0 40px;
  border-bottom: 2px solid #e5e7eb;
}
.tab-btn {
  background: none;
  border: none;
  padding: 18px 24px;
  font-size: 0.95rem;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  position: relative;
  transition: all 0.3s;
  display: flex;
  align-items: center;
  gap: 8px;
}
.tab-btn:hover {
  color: #2563eb;
  background: #f3f4f6;
}
.tab-btn.active {
  color: #2563eb;
}
.tab-btn.active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 3px;
  background: #2563eb;
}

/* Tab Content */
.profile-content {
  padding: 30px 40px;
}
.tab-content {
  display: none;
  animation: fadeIn 0.3s ease;
}
.tab-content.active {
  display: block;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Content Card */
.content-card {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}
.card-header {
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 2px solid #f3f4f6;
}
.card-header h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 8px 0;
  display: flex;
  align-items: center;
  gap: 12px;
}
.card-header h2 i {
  color: #2563eb;
}
.card-subtitle {
  color: #6b7280;
  font-size: 0.95rem;
  margin: 0;
}

/* Form Styles */
.profile-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.form-section {
  padding: 20px 0;
  border-bottom: 1px solid #f3f4f6;
}
.form-section:last-of-type {
  border-bottom: none;
}
.section-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 20px 0;
  display: flex;
  align-items: center;
  gap: 10px;
}
.section-title i {
  color: #6b7280;
  font-size: 1rem;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.form-grid.grid-3 {
  grid-template-columns: repeat(3, 1fr);
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.form-group label {
  font-weight: 600;
  color: #374151;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 8px;
}
.form-group label i {
  color: #9ca3af;
  font-size: 0.85rem;
}
.form-input {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.95rem;
  transition: all 0.3s;
  font-family: inherit;
}
.form-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}
.form-input:read-only {
  background: #f9fafb;
  color: #6b7280;
  cursor: not-allowed;
}
textarea.form-input {
  resize: vertical;
  min-height: 100px;
}
.form-hint {
  color: #9ca3af;
  font-size: 0.85rem;
  margin-top: 4px;
}

/* Form Actions */
.form-actions {
  margin-top: 12px;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}
.btn {
  padding: 12px 28px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: none;
}
.btn-primary {
  background: #2563eb;
  color: white;
}
.btn-primary:hover {
  background: #1d4ed8;
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
}
.btn-primary:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
}

/* Responsive */
@media (max-width: 1024px) {
  .dashboard-content { margin-left: 0; }
  .profile-header-section { flex-direction: column; align-items: center; text-align: center; margin-top: -60px; }
  .profile-avatar { width: 120px; height: 120px; }
  .form-grid, .form-grid.grid-3 { grid-template-columns: 1fr; }
  .profile-tabs { overflow-x: auto; }
  .profile-content { padding: 20px; }
}

@media (max-width: 768px) {
  .profile-banner { height: 180px; }
  .content-card { padding: 20px; }
  .tab-btn { padding: 14px 16px; font-size: 0.9rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile page loaded');
    
    // Clear any old messages on page load
    const messageContainer = document.getElementById('message-container');
    if (messageContainer) {
        messageContainer.innerHTML = '';
    }
    
    // Tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabName = btn.dataset.tab;
            
            // Remove active class from all tabs
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab
            btn.classList.add('active');
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });

    // Logo upload
    const logoInput = document.getElementById('shop_logo_input');
    const logoPreview = document.getElementById('logo-preview');
    
    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            showMessage('File size must be less than 2MB', 'error');
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            logoPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
        
        // Upload image
        const formData = new FormData();
        formData.append('shop_logo', file);
        
        fetch((window.BASE_URL||'')+'/api/shop-owner/profile/upload-logo', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('Shop logo uploaded successfully!', 'success');
                logoPreview.src = data.logo_url;
            } else {
                showMessage(data.message || 'Failed to upload logo', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while uploading logo', 'error');
        });
    });

    // Form submissions
    const forms = ['business', 'banking', 'social'];
    
    forms.forEach(formType => {
        const form = document.getElementById(`${formType}-form`);
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('.btn-primary');
            
            // Manually collect form data to avoid issues with FormData on hidden elements
            const formElements = form.querySelectorAll('input[name], select[name], textarea[name]');
            const data = {};
            
            formElements.forEach(element => {
                if (!element.hasAttribute('readonly') && !element.disabled && element.name) {
                    data[element.name] = element.value;
                    console.log(`  Field: ${element.name} = "${element.value}"`);
                }
            });
            
            console.log(`Form ${formType} - Found ${formElements.length} elements`);
            console.log(`Form ${formType} - Collected ${Object.keys(data).length} fields`);
            console.log(`Data object:`, JSON.stringify(data, null, 2));
            console.log(`Submitting ${formType} form:`, data);
            
            // Validate that we have data
            if (Object.keys(data).length === 0) {
                console.error('No form data collected! All fields might be readonly or disabled.');
                showMessage('Error: No data to submit. Please fill in the form fields.', 'error');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.querySelector('.btn-text').style.display = 'none';
            submitBtn.querySelector('.btn-loading').style.display = 'inline-flex';
            
            fetch((window.BASE_URL||'')+'/api/shop-owner/profile/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showMessage(`${formType.charAt(0).toUpperCase() + formType.slice(1)} information updated successfully!`, 'success');
                    // Update completion percentage if returned
                    if (data.profile && data.profile.profile_completion_percentage) {
                        updateCompletionPercentage(data.profile.profile_completion_percentage);
                    }
                } else {
                    showMessage(data.message || 'Failed to update profile', 'error');
                    console.error('Update failed:', data);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showMessage('An error occurred while updating profile: ' + error.message, 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.querySelector('.btn-text').style.display = 'inline';
                submitBtn.querySelector('.btn-loading').style.display = 'none';
            });
        });
    });

    function updateCompletionPercentage(percentage) {
        const percentageEl = document.querySelector('.completion-percentage');
        const progressCircle = document.querySelector('.progress-ring-circle.progress');
        
        if (percentageEl) {
            percentageEl.textContent = percentage + '%';
        }
        
        if (progressCircle) {
            const circumference = 163.36;
            const offset = circumference * (1 - percentage / 100);
            progressCircle.style.strokeDashoffset = offset;
        }
    }

    function showMessage(message, type) {
        const container = document.getElementById('message-container');
        container.innerHTML = `
            <div class="alert alert-${type}">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${message}
            </div>
        `;
        
        setTimeout(() => {
            container.innerHTML = '';
        }, 5000);
    }
});
</script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
