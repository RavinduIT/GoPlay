<?php
$title = 'Profile - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-profile.css'
];
$additionalJS = ['/public/js/pages/ground-owner-profile.js'];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Profile</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="btn-primary" id="gpEditBtn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                </div>
            </div>
        </header>

        <div class="dashboard-content">

            <!-- Hero Banner -->
            <div class="gp-hero" id="gpHero">
                <div class="gp-hero-bg"></div>
                <div class="gp-hero-body">
                    <!-- Avatar -->
                    <div class="gp-avatar-wrap">
                        <div class="gp-avatar" id="gpAvatarDisplay">
                            <span class="gp-avatar-initials" id="gpInitials">—</span>
                        </div>
                        <label class="gp-avatar-overlay" for="gpAvatarInput" title="Change photo">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="gpAvatarInput" accept="image/*" style="display:none">
                    </div>

                    <!-- Name + role -->
                    <div class="gp-hero-info">
                        <h2 class="gp-hero-name" id="gpHeroName">Loading…</h2>
                        <span class="gp-hero-role">Ground Owner</span>
                        <p class="gp-hero-biz" id="gpHeroBiz"></p>
                    </div>

                    <!-- Stats -->
                    <div class="gp-hero-stats">
                        <div class="gp-hero-stat">
                            <strong id="gpStatGrounds">—</strong>
                            <span>Grounds</span>
                        </div>
                        <div class="gp-hero-stat">
                            <strong id="gpStatBookings">—</strong>
                            <span>Bookings</span>
                        </div>
                        <div class="gp-hero-stat">
                            <strong id="gpStatRating">—</strong>
                            <span>Avg Rating</span>
                        </div>
                        <div class="gp-hero-stat">
                            <strong id="gpStatYears">—</strong>
                            <span>Years Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two-column body -->
            <div class="gp-body-grid">

                <!-- LEFT SIDEBAR -->
                <aside class="gp-sidebar">

                    <!-- Contact -->
                    <div class="gp-card">
                        <div class="gp-card-header">
                            <i class="fas fa-address-card"></i> Contact
                        </div>
                        <div class="gp-card-body">
                            <div class="gp-info-row" id="gpPhone">
                                <i class="fas fa-phone"></i> <span>—</span>
                            </div>
                            <div class="gp-info-row" id="gpEmail">
                                <i class="fas fa-envelope"></i> <span>—</span>
                            </div>
                            <div class="gp-info-row" id="gpBizEmail">
                                <i class="fas fa-briefcase"></i> <span>—</span>
                            </div>
                            <div class="gp-info-row" id="gpWebsite">
                                <i class="fas fa-globe"></i> <span>—</span>
                            </div>
                            <div class="gp-info-row" id="gpAddress">
                                <i class="fas fa-map-marker-alt"></i> <span>—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Social -->
                    <div class="gp-card">
                        <div class="gp-card-header">
                            <i class="fas fa-share-alt"></i> Social Media
                        </div>
                        <div class="gp-card-body" id="gpSocialLinks">
                            <p class="gp-empty-hint">No social links added yet.</p>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="gp-card">
                        <div class="gp-card-header">
                            <i class="fas fa-credit-card"></i> Payment Methods
                        </div>
                        <div class="gp-card-body" id="gpPayments">
                            <p class="gp-empty-hint">No payment methods added yet.</p>
                        </div>
                    </div>

                </aside>

                <!-- RIGHT MAIN -->
                <div class="gp-main">

                    <!-- Business Info -->
                    <div class="gp-card">
                        <div class="gp-card-header">
                            <i class="fas fa-building"></i> Business Information
                        </div>
                        <div class="gp-card-body">
                            <div class="gp-detail-grid">
                                <div class="gp-detail-item">
                                    <label>Business Name</label>
                                    <span id="gpBizName">—</span>
                                </div>
                                <div class="gp-detail-item">
                                    <label>Registration No.</label>
                                    <span id="gpRegNo">—</span>
                                </div>
                                <div class="gp-detail-item">
                                    <label>Years in Business</label>
                                    <span id="gpYears">—</span>
                                </div>
                                <div class="gp-detail-item">
                                    <label>Total Facilities</label>
                                    <span id="gpFacCount">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- About -->
                    <div class="gp-card">
                        <div class="gp-card-header">
                            <i class="fas fa-info-circle"></i> About
                        </div>
                        <div class="gp-card-body">
                            <p class="gp-bio-text" id="gpBio">No bio added yet.</p>
                        </div>
                    </div>

                    <!-- Banking -->
                    <div class="gp-card">
                        <div class="gp-card-header">
                            <i class="fas fa-university"></i> Banking Information
                        </div>
                        <div class="gp-card-body">
                            <div class="gp-detail-grid">
                                <div class="gp-detail-item">
                                    <label>Bank</label>
                                    <span id="gpBankName">—</span>
                                </div>
                                <div class="gp-detail-item">
                                    <label>Account Holder</label>
                                    <span id="gpBankHolder">—</span>
                                </div>
                                <div class="gp-detail-item gp-full">
                                    <label>Account Number</label>
                                    <span id="gpBankAcc" class="gp-masked">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /gp-main -->
            </div><!-- /gp-body-grid -->

        </div><!-- /dashboard-content -->
    </main>
</div>

<!-- 
     EDIT PROFILE MODAL
 -->
<div id="gpModal" class="gp-modal-backdrop" style="display:none">
    <div class="gp-modal">
        <div class="gp-modal-header">
            <h3><i class="fas fa-edit"></i> Edit Profile</h3>
            <button class="gp-modal-close" id="gpModalClose"><i class="fas fa-times"></i></button>
        </div>

        <!-- Tab Nav -->
        <div class="gp-tabs">
            <button class="gp-tab active" data-tab="personal">Personal</button>
            <button class="gp-tab" data-tab="business">Business</button>
            <button class="gp-tab" data-tab="social">Social</button>
            <button class="gp-tab" data-tab="banking">Banking</button>
        </div>

        <div class="gp-modal-body">

            <!-- Tab: Personal -->
            <div class="gp-tab-panel active" id="gpTabPersonal">
                <div class="gp-form-row">
                    <div class="gp-form-group">
                        <label>First Name</label>
                        <input type="text" id="gpFFirstName" placeholder="First name">
                    </div>
                    <div class="gp-form-group">
                        <label>Last Name</label>
                        <input type="text" id="gpFLastName" placeholder="Last name">
                    </div>
                </div>
                <div class="gp-form-group">
                    <label>Phone</label>
                    <input type="tel" id="gpFPhone" placeholder="+94 77 000 0000">
                </div>
                <div class="gp-form-group">
                    <label>Email <small class="gp-readonly-hint">(read-only)</small></label>
                    <input type="email" id="gpFEmail" disabled>
                </div>
            </div>

            <!-- Tab: Business -->
            <div class="gp-tab-panel" id="gpTabBusiness">
                <div class="gp-form-group">
                    <label>Business Name</label>
                    <input type="text" id="gpFBizName" placeholder="Your business name">
                </div>
                <div class="gp-form-row">
                    <div class="gp-form-group">
                        <label>Registration Number</label>
                        <input type="text" id="gpFRegNo" placeholder="BR-XXXXXXXX">
                    </div>
                    <div class="gp-form-group">
                        <label>Years in Business</label>
                        <input type="number" id="gpFYears" min="0" placeholder="0">
                    </div>
                </div>
                <div class="gp-form-group">
                    <label>Business Address</label>
                    <input type="text" id="gpFBizAddr" placeholder="Street, City">
                </div>
                <div class="gp-form-row">
                    <div class="gp-form-group">
                        <label>Business Phone</label>
                        <input type="tel" id="gpFBizPhone" placeholder="+94 11 000 0000">
                    </div>
                    <div class="gp-form-group">
                        <label>Business Email</label>
                        <input type="email" id="gpFBizEmail" placeholder="info@yourbiz.com">
                    </div>
                </div>
                <div class="gp-form-group">
                    <label>Website</label>
                    <input type="url" id="gpFWebsite" placeholder="https://yourbiz.com">
                </div>
                <div class="gp-form-group">
                    <label>About / Bio</label>
                    <textarea id="gpFBio" rows="4" placeholder="Tell customers about your business…"></textarea>
                </div>
                <div class="gp-form-group">
                    <label>Accepted Payment Methods</label>
                    <div class="gp-checks">
                        <label class="gp-check"><input type="checkbox" value="cash"> Cash</label>
                        <label class="gp-check"><input type="checkbox" value="card"> Card</label>
                        <label class="gp-check"><input type="checkbox" value="bank_transfer"> Bank Transfer</label>
                        <label class="gp-check"><input type="checkbox" value="payhere"> PayHere</label>
                        <label class="gp-check"><input type="checkbox" value="dialog_genie"> Dialog Genie</label>
                    </div>
                </div>
            </div>

            <!-- Tab: Social -->
            <div class="gp-tab-panel" id="gpTabSocial">
                <div class="gp-form-group">
                    <label><i class="fab fa-facebook" style="color:#1877f2"></i> Facebook</label>
                    <input type="url" id="gpFFacebook" placeholder="https://facebook.com/yourpage">
                </div>
                <div class="gp-form-group">
                    <label><i class="fab fa-instagram" style="color:#e1306c"></i> Instagram</label>
                    <input type="url" id="gpFInstagram" placeholder="https://instagram.com/yourhandle">
                </div>
                <div class="gp-form-group">
                    <label><i class="fab fa-twitter" style="color:#1da1f2"></i> Twitter / X</label>
                    <input type="url" id="gpFTwitter" placeholder="https://twitter.com/yourhandle">
                </div>
            </div>

            <!-- Tab: Banking -->
            <div class="gp-tab-panel" id="gpTabBanking">
                <div class="gp-form-group">
                    <label>Bank Name</label>
                    <select id="gpFBankName">
                        <option value="">Select Bank</option>
                        <option value="Bank of Ceylon">Bank of Ceylon</option>
                        <option value="People's Bank">People's Bank</option>
                        <option value="Commercial Bank">Commercial Bank</option>
                        <option value="Hatton National Bank">Hatton National Bank</option>
                        <option value="Sampath Bank">Sampath Bank</option>
                        <option value="Nations Trust Bank">Nations Trust Bank</option>
                        <option value="DFCC Bank">DFCC Bank</option>
                        <option value="National Development Bank">National Development Bank</option>
                        <option value="Seylan Bank">Seylan Bank</option>
                        <option value="Pan Asia Banking Corporation">Pan Asia Banking Corporation</option>
                        <option value="Union Bank">Union Bank</option>
                        <option value="Amana Bank">Amana Bank</option>
                        <option value="National Savings Bank">National Savings Bank</option>
                        <option value="Regional Development Bank">Regional Development Bank</option>
                        <option value="Sanasa Development Bank">Sanasa Development Bank</option>
                        <option value="HSBC Sri Lanka">HSBC Sri Lanka</option>
                        <option value="Standard Chartered Bank">Standard Chartered Bank</option>
                        <option value="Citi Bank">Citi Bank</option>
                    </select>
                </div>
                <div class="gp-form-group">
                    <label>Account Holder Name</label>
                    <input type="text" id="gpFBankHolder" placeholder="Full name as on account">
                </div>
                <div class="gp-form-group">
                    <label>Account Number</label>
                    <input type="text" id="gpFBankAcc" placeholder="XXXXXXXXXXXXXXXX">
                </div>
            </div>

        </div><!-- /gp-modal-body -->

        <div class="gp-modal-footer">
            <button class="gp-btn-secondary" id="gpModalCancel">Cancel</button>
            <button class="gp-btn-primary" id="gpModalSave">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="gpToast" class="gp-toast" aria-live="polite"></div>
<?php include __DIR__ . '/layout-foot.php'; ?>
