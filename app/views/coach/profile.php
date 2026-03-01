<?php $currentPage = 'profile'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile – GoPlay Coach</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="/public/css/pages/coach-profile.css">
</head>
<body>
<div class="coach-dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <!-- Loading -->
        <div id="profileLoading">
            <div class="spin"></div>
            <span>Loading your profile…</span>
        </div>

        <!-- Main content (hidden until data loads) -->
        <div id="profileContent" style="display:none;">

            <!-- ── Hero Banner ─────────────────────────────── -->
            <section class="profile-hero">
                <div class="hero-cover"></div>

                <div class="hero-body">
                    <!-- Avatar -->
                    <div class="hero-avatar-wrap">
                        <img id="heroAvatar" src="/public/assets/images/default-avatar.png"
                             alt="Coach Avatar" class="hero-avatar">
                        <div class="online-dot" title="Active"></div>
                        <button class="avatar-cam" onclick="coachProfile.changeAvatar()" title="Change photo">
                            <i class="fas fa-camera"></i>
                        </button>
                        <input type="file" id="avatarInput" accept="image/*" style="display:none;">
                    </div>

                    <!-- Info -->
                    <div class="hero-info">
                        <h1 class="hero-name" id="heroName">—</h1>
                        <div class="hero-sport-badge">
                            <i class="fas fa-running"></i>
                            <span id="heroSport">—</span>
                        </div>
                        <div class="hero-meta">
                            <span class="hero-meta-item" id="heroLocation" style="display:none;">
                                <i class="fas fa-map-marker-alt"></i><span></span>
                            </span>
                            <span class="hero-meta-item" id="heroExp" style="display:none;">
                                <i class="fas fa-briefcase"></i><span></span>
                            </span>
                            <span class="hero-meta-item" id="heroEmail" style="display:none;">
                                <i class="fas fa-envelope"></i><span></span>
                            </span>
                        </div>
                        <div class="hero-stats">
                            <div class="hero-stat">
                                <span class="hero-stat-num" id="hRating">—</span>
                                <span class="hero-stat-label"><i class="fas fa-star" style="color:#fbbf24;font-size:.6rem;"></i> Rating</span>
                            </div>
                            <div class="hero-stat">
                                <span class="hero-stat-num" id="hSessions">—</span>
                                <span class="hero-stat-label">Sessions</span>
                            </div>
                            <div class="hero-stat">
                                <span class="hero-stat-num" id="hReviews">—</span>
                                <span class="hero-stat-label">Reviews</span>
                            </div>
                            <div class="hero-stat">
                                <span class="hero-stat-num" id="hYears">—</span>
                                <span class="hero-stat-label">Exp. Yrs</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="hero-actions">
                        <button class="btn btn-primary" onclick="coachProfile.openEditModal()">
                            <i class="fas fa-pen"></i> Edit Profile
                        </button>
                        <a href="/coach/availability" class="btn btn-white">
                            <i class="fas fa-calendar-alt"></i> Availability
                        </a>
                    </div>
                </div>

                <!-- Footer badges + rate -->
                <div class="hero-footer">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <span class="hero-badge verified"><i class="fas fa-check-circle"></i> Verified</span>
                        <span class="hero-badge premium"><i class="fas fa-crown"></i> Premium</span>
                        <span class="hero-badge top-rated"><i class="fas fa-medal"></i> Top Rated</span>
                    </div>
                    <div class="hero-rate-tag" id="heroRateTag" style="display:none;">
                        <strong id="heroRate">—</strong> <span>/ hour</span>
                    </div>
                </div>
            </section>

            <!-- ── Tab Navigation ──────────────────────────── -->
            <nav class="profile-tabs">
                <button class="tab-btn active" data-tab="profile">
                    <i class="fas fa-user"></i> Profile
                </button>
                <button class="tab-btn" data-tab="certificates">
                    <i class="fas fa-certificate"></i> Certificates
                    <span class="tab-count" id="certCount">0</span>
                </button>
                <button class="tab-btn" data-tab="achievements">
                    <i class="fas fa-trophy"></i> Achievements
                    <span class="tab-count" id="achCount">0</span>
                </button>
            </nav>

            <!-- ── Profile Tab ─────────────────────────────── -->
            <div class="tab-panel active" id="tab-profile">
                <div class="profile-body">
                    <div class="profile-2col">

                        <!-- LEFT: bio + specializations -->
                        <div class="profile-left">

                            <!-- Bio -->
                            <div class="pcard">
                                <div class="pcard-head">
                                    <h3>
                                        <span class="head-icon" style="background:rgba(102,126,234,.1);color:#667eea;">
                                            <i class="fas fa-align-left"></i>
                                        </span>
                                        About Me
                                    </h3>
                                    <button class="btn btn-sm btn-outline" onclick="coachProfile.openEditModal()">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>
                                </div>
                                <div class="pcard-body">
                                    <div class="bio-card-inner">
                                        <span class="bio-quote">"</span>
                                        <p class="bio-text" id="bioPara">
                                            <span class="bio-text-placeholder">No bio added yet. Click Edit Profile to add one.</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Specializations -->
                            <div class="pcard">
                                <div class="pcard-head">
                                    <h3>
                                        <span class="head-icon" style="background:rgba(59,130,246,.1);color:#3b82f6;">
                                            <i class="fas fa-tags"></i>
                                        </span>
                                        Specializations
                                    </h3>
                                </div>
                                <div class="pcard-body">
                                    <div class="spec-tags" id="specTags">
                                        <span style="color:var(--c-muted);font-size:.88rem;">No specializations added yet.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact info -->
                            <div class="pcard">
                                <div class="pcard-head">
                                    <h3>
                                        <span class="head-icon" style="background:rgba(79,172,254,.1);color:#4facfe;">
                                            <i class="fas fa-address-card"></i>
                                        </span>
                                        Contact &amp; Personal
                                    </h3>
                                    <button class="btn btn-sm btn-outline" onclick="coachProfile.openEditModal()">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>
                                </div>
                                <div class="pcard-body">
                                    <div class="info-rows">
                                        <div class="info-row">
                                            <div class="info-row-icon blue"><i class="fas fa-user"></i></div>
                                            <div class="info-row-body">
                                                <div class="info-row-label">Full Name</div>
                                                <div class="info-row-value" id="iName">—</div>
                                            </div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-row-icon purple"><i class="fas fa-envelope"></i></div>
                                            <div class="info-row-body">
                                                <div class="info-row-label">Email</div>
                                                <div class="info-row-value" id="iEmail">—</div>
                                            </div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-row-icon green"><i class="fas fa-phone"></i></div>
                                            <div class="info-row-body">
                                                <div class="info-row-label">Phone</div>
                                                <div class="info-row-value" id="iPhone">—</div>
                                            </div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-row-icon orange"><i class="fas fa-map-marker-alt"></i></div>
                                            <div class="info-row-body">
                                                <div class="info-row-label">Location</div>
                                                <div class="info-row-value" id="iLocation">—</div>
                                            </div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-row-icon pink"><i class="fas fa-running"></i></div>
                                            <div class="info-row-body">
                                                <div class="info-row-label">Sport</div>
                                                <div class="info-row-value" id="iSport">—</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: stats + rate + completeness -->
                        <div class="profile-right">

                            <!-- Stats -->
                            <div class="pcard">
                                <div class="pcard-head">
                                    <h3>
                                        <span class="head-icon" style="background:rgba(67,233,123,.1);color:#38d39f;">
                                            <i class="fas fa-chart-bar"></i>
                                        </span>
                                        Performance
                                    </h3>
                                </div>
                                <div class="pcard-body">
                                    <div class="mini-stats">
                                        <div class="mini-stat blue">
                                            <div class="mini-stat-icon"><i class="fas fa-star"></i></div>
                                            <div class="mini-stat-num" id="msRating">—</div>
                                            <div class="mini-stat-label">Rating</div>
                                        </div>
                                        <div class="mini-stat purple">
                                            <div class="mini-stat-icon"><i class="fas fa-dumbbell"></i></div>
                                            <div class="mini-stat-num" id="msSessions">—</div>
                                            <div class="mini-stat-label">Sessions</div>
                                        </div>
                                        <div class="mini-stat green">
                                            <div class="mini-stat-icon"><i class="fas fa-comment-stars"></i></div>
                                            <div class="mini-stat-num" id="msReviews">—</div>
                                            <div class="mini-stat-label">Reviews</div>
                                        </div>
                                        <div class="mini-stat orange">
                                            <div class="mini-stat-icon"><i class="fas fa-briefcase"></i></div>
                                            <div class="mini-stat-num" id="msYears">—</div>
                                            <div class="mini-stat-label">Exp. Yrs</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hourly rate -->
                            <div class="pcard" id="rateCard" style="display:none;">
                                <div class="pcard-head">
                                    <h3>
                                        <span class="head-icon" style="background:rgba(251,191,36,.1);color:#fbbf24;">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        Hourly Rate
                                    </h3>
                                </div>
                                <div class="pcard-body">
                                    <div class="rate-display">
                                        <div class="rate-icon"><i class="fas fa-coins"></i></div>
                                        <div>
                                            <div class="rate-amount" id="rateAmount">—</div>
                                            <div class="rate-label">per session hour</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile completeness -->
                            <div class="pcard">
                                <div class="pcard-head">
                                    <h3>
                                        <span class="head-icon" style="background:rgba(102,126,234,.1);color:#667eea;">
                                            <i class="fas fa-tasks"></i>
                                        </span>
                                        Profile Strength
                                    </h3>
                                </div>
                                <div class="pcard-body">
                                    <div class="completeness-wrap">
                                        <div class="completeness-ring-wrap completeness-ring">
                                            <svg width="56" height="56" viewBox="0 0 56 56">
                                                <defs>
                                                    <linearGradient id="ringGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                                        <stop offset="0%" stop-color="#3b82f6"/>
                                                        <stop offset="100%" stop-color="#1d4ed8"/>
                                                    </linearGradient>
                                                </defs>
                                                <circle class="track"    cx="28" cy="28" r="22"/>
                                                <circle class="progress" cx="28" cy="28" r="22" id="ringProgress"/>
                                            </svg>
                                            <span class="ring-text" id="ringPct">0%</span>
                                        </div>
                                        <div class="completeness-info">
                                            <strong id="completenessLabel">Incomplete</strong>
                                            <p id="completenessHint">Fill in your profile to attract more students.</p>
                                            <div class="completeness-bar-wrap">
                                                <div class="completeness-bar" id="completenessBar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick links -->
                            <div class="pcard">
                                <div class="pcard-head">
                                    <h3>
                                        <span class="head-icon" style="background:rgba(248,113,113,.1);color:#f87171;">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        Quick Links
                                    </h3>
                                </div>
                                <div class="pcard-body" style="display:flex;flex-direction:column;gap:10px;">
                                    <a href="/coach/availability" class="info-row" style="text-decoration:none;">
                                        <div class="info-row-icon blue"><i class="fas fa-clock"></i></div>
                                        <div class="info-row-body">
                                            <div class="info-row-label">Manage</div>
                                            <div class="info-row-value">Availability Schedule</div>
                                        </div>
                                        <i class="fas fa-chevron-right" style="color:var(--c-muted);font-size:.8rem;"></i>
                                    </a>
                                    <a href="/coach/sessions" class="info-row" style="text-decoration:none;">
                                        <div class="info-row-icon purple"><i class="fas fa-dumbbell"></i></div>
                                        <div class="info-row-body">
                                            <div class="info-row-label">View</div>
                                            <div class="info-row-value">Training Sessions</div>
                                        </div>
                                        <i class="fas fa-chevron-right" style="color:var(--c-muted);font-size:.8rem;"></i>
                                    </a>
                                    <a href="/coach/reviews" class="info-row" style="text-decoration:none;">
                                        <div class="info-row-icon orange"><i class="fas fa-star"></i></div>
                                        <div class="info-row-body">
                                            <div class="info-row-label">Read</div>
                                            <div class="info-row-value">Student Reviews</div>
                                        </div>
                                        <i class="fas fa-chevron-right" style="color:var(--c-muted);font-size:.8rem;"></i>
                                    </a>
                                </div>
                            </div>

                        </div><!-- /right -->
                    </div><!-- /2col -->
                </div><!-- /body -->
            </div><!-- /tab-profile -->

            <!-- ── Certificates Tab ────────────────────────── -->
            <div class="tab-panel" id="tab-certificates">
                <div class="profile-body">
                    <div class="cred-section-header">
                        <h3>
                            <i class="fas fa-certificate" style="color:#4facfe;"></i>
                            My Certificates
                        </h3>
                        <button class="btn btn-primary" onclick="coachProfile.openCertModal()">
                            <i class="fas fa-plus"></i> Add Certificate
                        </button>
                    </div>
                    <div class="cert-grid" id="certGrid">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-certificate"></i></div>
                            <h4>No certificates yet</h4>
                            <p>Add your coaching licenses and qualifications.</p>
                            <button class="btn btn-primary" onclick="coachProfile.openCertModal()">
                                <i class="fas fa-plus"></i> Add Certificate
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Achievements Tab ────────────────────────── -->
            <div class="tab-panel" id="tab-achievements">
                <div class="profile-body">
                    <div class="cred-section-header">
                        <h3>
                            <i class="fas fa-trophy" style="color:#fda085;"></i>
                            My Achievements
                        </h3>
                        <button class="btn btn-primary" onclick="coachProfile.openAchModal()">
                            <i class="fas fa-plus"></i> Add Achievement
                        </button>
                    </div>
                    <div class="ach-grid" id="achGrid">
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-trophy"></i></div>
                            <h4>No achievements yet</h4>
                            <p>Showcase your awards, milestones and competitions.</p>
                            <button class="btn btn-primary" onclick="coachProfile.openAchModal()">
                                <i class="fas fa-plus"></i> Add Achievement
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /profileContent -->
    </main>
</div><!-- /coach-dashboard -->

<!-- ════════════════════════════════════════════════════════
     MODALS
═════════════════════════════════════════════════════════ -->

<!-- Edit Profile -->
<div id="modalEditProfile" class="modal" style="display:none;">
    <div class="modal-box modal-lg">
        <div class="modal-head">
            <div class="modal-head-icon purple"><i class="fas fa-user-edit"></i></div>
            <h3>Edit Profile</h3>
            <button class="modal-close" onclick="coachProfile.closeModal('modalEditProfile')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editProfileForm" autocomplete="off">
                <div class="form-section">
                    <div class="form-section-title">Personal Information</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" required placeholder="First name">
                        </div>
                        <div class="form-group">
                            <label>Last Name <span class="required">*</span></label>
                            <input type="text" name="last_name" required placeholder="Last name">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" name="email" required placeholder="your@email.com">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" placeholder="+94 71 000 0000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" placeholder="City, Country">
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Professional Information</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Experience (Years)</label>
                            <input type="number" name="experience_years" min="0" max="60" placeholder="e.g. 5">
                        </div>
                        <div class="form-group">
                            <label>Hourly Rate (LKR)</label>
                            <input type="number" name="hourly_rate" min="0" step="50" placeholder="e.g. 2500">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Specializations</label>
                        <input type="text" name="specializations" placeholder="Fast Bowling, Youth Development, …">
                        <div class="form-hint">Separate multiple skills with commas.</div>
                    </div>
                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="4" placeholder="Tell students about your coaching philosophy, experience, and achievements…"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="coachProfile.closeModal('modalEditProfile')">Cancel</button>
            <button class="btn btn-primary" onclick="coachProfile.saveProfile()">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Add / Edit Certificate -->
<div id="modalCert" class="modal" style="display:none;">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-icon blue"><i class="fas fa-certificate"></i></div>
            <h3 id="certModalTitle">Add Certificate</h3>
            <button class="modal-close" onclick="coachProfile.closeModal('modalCert')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="certForm" autocomplete="off">
                <input type="hidden" name="cert_id">
                <div class="form-group">
                    <label>Certificate Title <span class="required">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. Level 3 Coaching License">
                </div>
                <div class="form-group">
                    <label>Issuing Organization</label>
                    <input type="text" name="issuing_organization" placeholder="e.g. Sri Lanka Cricket Board">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Issue Date</label>
                        <input type="date" name="issue_date">
                    </div>
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date">
                    </div>
                </div>
                <div class="form-group">
                    <label>Credential / License ID</label>
                    <input type="text" name="credential_id" placeholder="Optional ID number">
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="coachProfile.closeModal('modalCert')">Cancel</button>
            <button class="btn btn-primary" onclick="coachProfile.saveCert()">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>
</div>

<!-- Add / Edit Achievement -->
<div id="modalAch" class="modal" style="display:none;">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-icon purple"><i class="fas fa-trophy"></i></div>
            <h3 id="achModalTitle">Add Achievement</h3>
            <button class="modal-close" onclick="coachProfile.closeModal('modalAch')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="achForm" autocomplete="off">
                <input type="hidden" name="ach_id">
                <div class="form-group">
                    <label>Achievement Title <span class="required">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. Best Youth Coach Award">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="award">🏆 Award</option>
                            <option value="competition">🥇 Competition</option>
                            <option value="certification">📜 Certification</option>
                            <option value="milestone">🏁 Milestone</option>
                            <option value="other">⭐ Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date Achieved</label>
                        <input type="date" name="date_achieved">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Brief description of this achievement…"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="coachProfile.closeModal('modalAch')">Cancel</button>
            <button class="btn btn-primary" onclick="coachProfile.saveAch()">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>
</div>

<!-- Delete confirm -->
<div id="modalDelete" class="modal" style="display:none;">
    <div class="modal-box" style="max-width:400px;text-align:center;">
        <div class="modal-head" style="justify-content:flex-end;padding-bottom:0;">
            <button class="modal-close" onclick="coachProfile.closeModal('modalDelete')">&times;</button>
        </div>
        <div class="modal-body" style="padding-top:0;">
            <div class="delete-modal-icon"><i class="fas fa-trash-alt"></i></div>
            <h3 style="margin-bottom:8px;font-size:1.1rem;">Confirm Delete</h3>
            <p id="deleteMsg" style="color:var(--c-text2);font-size:.9rem;"></p>
        </div>
        <div class="modal-foot" style="justify-content:center;">
            <button class="btn btn-secondary" onclick="coachProfile.closeModal('modalDelete')">Cancel</button>
            <button class="btn btn-danger" id="deleteConfirmBtn">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>
</div>

<script>
/* ============================================================
   CoachProfile JS – real API, no mock data
============================================================ */
class CoachProfile {
    constructor() {
        this.p = {}; // profile data
        this.init();
    }

    async init() {
        this._bindTabs();
        this._bindGlobal();
        await Promise.all([
            this.loadProfile(),
            this.loadCertificates(),
            this.loadAchievements(),
        ]);
    }

    /* ── Tabs ──────────────────────────────────────────── */
    _bindTabs() {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('tab-' + tab).classList.add('active');
            });
        });
    }

    _bindGlobal() {
        document.getElementById('avatarInput').addEventListener('change', e => this.handleAvatarChange(e));
        // Close on backdrop click
        document.querySelectorAll('.modal').forEach(m => {
            m.addEventListener('click', e => { if (e.target === m) this.closeModal(m.id); });
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                const open = document.querySelector('.modal[style*="flex"]');
                if (open) this.closeModal(open.id);
            }
        });
    }

    /* ── Profile ───────────────────────────────────────── */
    async loadProfile() {
        try {
            const r = await fetch('/api/coach/profile');
            const d = await r.json();
            if (!d.success) throw new Error(d.error || 'Failed');
            this.p = d.profile;
            this._renderProfile();
            document.getElementById('profileLoading').style.display = 'none';
            document.getElementById('profileContent').style.display = '';
        } catch (e) {
            this.toast('Failed to load profile: ' + e.message, 'error');
        }
    }

    _renderProfile() {
        const p = this.p;
        const name = (p.first_name || '') + ' ' + (p.last_name || '');

        // Hero
        this._set('heroName', name.trim() || '—');
        this._set('heroSport', p.sport || '—');
        this._set('hRating',   p.stats?.rating   || '—');
        this._set('hSessions', p.stats?.sessions || '—');
        this._set('hReviews',  p.stats?.reviews  || '—');
        this._set('hYears',    p.stats?.years    || '—');

        if (p.location)  this._showMeta('heroLocation', p.location);
        if (p.experience) this._showMeta('heroExp', p.experience + ' yrs exp.');
        if (p.email)     this._showMeta('heroEmail', p.email);

        if (p.hourlyRate) {
            this._set('heroRate', 'LKR ' + Number(p.hourlyRate).toLocaleString());
            document.getElementById('heroRateTag').style.display = '';
        }

        if (p.avatar) {
            document.getElementById('heroAvatar').src = p.avatar;
        }

        // Stats sidebar
        this._set('msRating',   p.stats?.rating   || '—');
        this._set('msSessions', p.stats?.sessions || '—');
        this._set('msReviews',  p.stats?.reviews  || '—');
        this._set('msYears',    p.stats?.years    || '—');

        // Rate card
        if (p.hourlyRate) {
            this._set('rateAmount', 'LKR ' + Number(p.hourlyRate).toLocaleString());
            document.getElementById('rateCard').style.display = '';
        }

        // Info rows
        this._set('iName',     name.trim() || '—');
        this._set('iEmail',    p.email     || '—');
        this._set('iPhone',    p.phone     || '—');
        this._set('iLocation', p.location  || '—');
        this._set('iSport',    p.sport     || '—');

        // Bio
        const bioEl = document.getElementById('bioPara');
        if (p.bio) {
            bioEl.innerHTML = `<span>${this.esc(p.bio)}</span>`;
        }

        // Specializations
        const specEl = document.getElementById('specTags');
        if (p.specializations) {
            const tags = p.specializations.split(',').map(s => s.trim()).filter(Boolean);
            if (tags.length) {
                specEl.innerHTML = tags.map(t =>
                    `<span class="spec-tag"><i class="fas fa-hashtag" style="font-size:.65rem;opacity:.6;"></i>${this.esc(t)}</span>`
                ).join('');
            }
        }

        // Completeness
        this._calcCompleteness();
    }

    _showMeta(id, text) {
        const el = document.getElementById(id);
        if (!el) return;
        el.querySelector('span').textContent = text;
        el.style.display = 'flex';
    }

    _calcCompleteness() {
        const p = this.p;
        const fields = [p.first_name, p.last_name, p.email, p.phone, p.location, p.bio, p.specializations, p.hourlyRate];
        const filled = fields.filter(v => v && String(v).trim()).length;
        const pct    = Math.round((filled / fields.length) * 100);
        const circ   = 138.2;
        const offset = circ - (circ * pct / 100);

        const ring = document.getElementById('ringProgress');
        if (ring) {
            setTimeout(() => { ring.style.strokeDashoffset = offset; }, 300);
        }

        document.getElementById('ringPct').textContent = pct + '%';
        document.getElementById('completenessBar').style.width = pct + '%';

        const label = document.getElementById('completenessLabel');
        const hint  = document.getElementById('completenessHint');

        if (pct === 100) {
            label.textContent = 'Complete!';
            hint.textContent  = 'Your profile is fully set up.';
        } else if (pct >= 75) {
            label.textContent = 'Almost there!';
            hint.textContent  = 'Just a few more details needed.';
        } else if (pct >= 50) {
            label.textContent = 'Good start';
            hint.textContent  = 'Add more info to attract students.';
        } else {
            label.textContent = 'Incomplete';
            hint.textContent  = 'Fill in your profile to attract more students.';
        }
    }

    openEditModal() {
        const p = this.p;
        const f = document.getElementById('editProfileForm');
        f.querySelector('[name="first_name"]').value    = p.first_name    || '';
        f.querySelector('[name="last_name"]').value     = p.last_name     || '';
        f.querySelector('[name="email"]').value         = p.email         || '';
        f.querySelector('[name="phone"]').value         = p.phone         || '';
        f.querySelector('[name="location"]').value      = p.location      || '';
        f.querySelector('[name="experience_years"]').value = p.experience || '';
        f.querySelector('[name="hourly_rate"]').value   = p.hourlyRate    || '';
        f.querySelector('[name="specializations"]').value = p.specializations || '';
        f.querySelector('[name="bio"]').value           = p.bio           || '';
        this.openModal('modalEditProfile');
    }

    async saveProfile() {
        const form = document.getElementById('editProfileForm');
        const data = Object.fromEntries(new FormData(form).entries());

        if (!data.first_name || !data.last_name || !data.email) {
            this.toast('First name, last name and email are required', 'error'); return;
        }

        try {
            const r = await fetch('/api/coach/profile', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });
            const d = await r.json();
            if (!d.success) throw new Error(d.error || 'Update failed');
            this.toast('Profile updated!', 'success');
            this.closeModal('modalEditProfile');
            await this.loadProfile();
        } catch (e) { this.toast('Error: ' + e.message, 'error'); }
    }

    /* ── Avatar ────────────────────────────────────────── */
    changeAvatar() { document.getElementById('avatarInput').click(); }

    async handleAvatarChange(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) { this.toast('Please select an image file', 'error'); return; }
        if (file.size > 5 * 1024 * 1024)    { this.toast('Max 5 MB allowed', 'error'); return; }

        const fd = new FormData();
        fd.append('avatar', file);
        try {
            const r = await fetch('/api/coach/profile/avatar', { method: 'POST', body: fd });
            const d = await r.json();
            if (!d.success) throw new Error(d.error || 'Upload failed');
            document.getElementById('heroAvatar').src = d.avatarUrl;
            this.toast('Avatar updated', 'success');
        } catch (e) { this.toast('Error: ' + e.message, 'error'); }
    }

    /* ── Certificates ──────────────────────────────────── */
    async loadCertificates() {
        try {
            const r = await fetch('/api/coach/certificates');
            const d = await r.json();
            this._renderCerts(d.success ? d.certificates : []);
        } catch { this._renderCerts([]); }
    }

    _renderCerts(list) {
        document.getElementById('certCount').textContent = list.length;
        const grid = document.getElementById('certGrid');

        if (!list.length) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1;">
                    <div class="empty-state-icon"><i class="fas fa-certificate"></i></div>
                    <h4>No certificates yet</h4>
                    <p>Add your coaching licenses and qualifications.</p>
                    <button class="btn btn-primary" onclick="coachProfile.openCertModal()">
                        <i class="fas fa-plus"></i> Add Certificate
                    </button>
                </div>`;
            return;
        }

        const now = new Date().toISOString().slice(0,10);
        grid.innerHTML = list.map(c => {
            const isExpired = c.expiry_date && c.expiry_date < now;
            const status = isExpired
                ? `<span class="cert-status-chip expired"><i class="fas fa-times-circle"></i> Expired</span>`
                : `<span class="cert-status-chip active"><i class="fas fa-check-circle"></i> Active</span>`;
            return `
            <div class="cert-card">
                <div class="cert-card-top">
                    <div class="cert-ribbon"><i class="fas fa-star"></i></div>
                    <div class="cert-card-icon"><i class="fas fa-certificate"></i></div>
                    <div class="cert-card-title">${this.esc(c.title)}</div>
                    ${c.issuing_organization
                        ? `<div class="cert-card-org"><i class="fas fa-building"></i>${this.esc(c.issuing_organization)}</div>`
                        : ''}
                </div>
                <div class="cert-card-body">
                    <div class="cert-meta-row">
                        ${c.issue_date  ? `<span class="cert-meta-item"><i class="far fa-calendar-check"></i> Issued: ${c.issue_date}</span>` : ''}
                        ${c.expiry_date ? `<span class="cert-meta-item"><i class="far fa-clock"></i> Expires: ${c.expiry_date}</span>` : ''}
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                        ${status}
                        ${c.credential_id ? `<span class="cert-id-chip"><i class="fas fa-hashtag"></i>${this.esc(c.credential_id)}</span>` : ''}
                    </div>
                </div>
                <div class="cert-card-actions">
                    <button class="btn btn-xs btn-outline" onclick="coachProfile.editCert(${c.id})">
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <button class="btn btn-xs btn-danger-soft" onclick="coachProfile.confirmDelete('cert', ${c.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    openCertModal(cert = null) {
        const f = document.getElementById('certForm');
        f.reset();
        document.getElementById('certModalTitle').textContent = cert ? 'Edit Certificate' : 'Add Certificate';
        if (cert) {
            f.querySelector('[name="cert_id"]').value              = cert.id;
            f.querySelector('[name="title"]').value                = cert.title || '';
            f.querySelector('[name="issuing_organization"]').value = cert.issuing_organization || '';
            f.querySelector('[name="issue_date"]').value           = cert.issue_date  || '';
            f.querySelector('[name="expiry_date"]').value          = cert.expiry_date || '';
            f.querySelector('[name="credential_id"]').value        = cert.credential_id || '';
        }
        this.openModal('modalCert');
    }

    async editCert(id) {
        const r = await fetch('/api/coach/certificates');
        const d = await r.json();
        const c = (d.certificates || []).find(x => x.id == id);
        if (c) this.openCertModal(c);
    }

    async saveCert() {
        const form = document.getElementById('certForm');
        const data = Object.fromEntries(new FormData(form).entries());
        if (!data.title) { this.toast('Title is required', 'error'); return; }

        const id = data.cert_id;
        const payload = {
            title: data.title,
            issuing_organization: data.issuing_organization || null,
            issue_date:    data.issue_date   || null,
            expiry_date:   data.expiry_date  || null,
            credential_id: data.credential_id || null,
        };

        try {
            const r = await fetch(id ? `/api/coach/certificates/${id}` : '/api/coach/certificates', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const d = await r.json();
            if (!d.success) throw new Error(d.error || 'Failed');
            this.toast(id ? 'Certificate updated' : 'Certificate added', 'success');
            this.closeModal('modalCert');
            await this.loadCertificates();
        } catch (e) { this.toast('Error: ' + e.message, 'error'); }
    }

    /* ── Achievements ──────────────────────────────────── */
    async loadAchievements() {
        try {
            const r = await fetch('/api/coach/achievements');
            const d = await r.json();
            this._renderAchs(d.success ? d.achievements : []);
        } catch { this._renderAchs([]); }
    }

    _catIcon = { award:'fa-trophy', competition:'fa-medal', certification:'fa-certificate', milestone:'fa-flag-checkered', other:'fa-star' };

    _renderAchs(list) {
        document.getElementById('achCount').textContent = list.length;
        const grid = document.getElementById('achGrid');

        if (!list.length) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1;">
                    <div class="empty-state-icon"><i class="fas fa-trophy"></i></div>
                    <h4>No achievements yet</h4>
                    <p>Showcase your awards, milestones and competitions.</p>
                    <button class="btn btn-primary" onclick="coachProfile.openAchModal()">
                        <i class="fas fa-plus"></i> Add Achievement
                    </button>
                </div>`;
            return;
        }

        grid.innerHTML = list.map(a => {
            const cat  = a.category || 'other';
            const icon = this._catIcon[cat] || 'fa-star';
            return `
            <div class="ach-card">
                <div class="ach-card-banner ${cat}"></div>
                <div class="ach-card-body">
                    <div class="ach-icon-wrap ${cat}"><i class="fas ${icon}"></i></div>
                    <div class="ach-content">
                        <div class="ach-title">${this.esc(a.title)}</div>
                        <div class="ach-cat-row">
                            <span class="ach-cat-badge ${cat}">${cat.charAt(0).toUpperCase()+cat.slice(1)}</span>
                            ${a.date_achieved
                                ? `<span class="ach-date"><i class="far fa-calendar"></i>${a.date_achieved}</span>`
                                : ''}
                        </div>
                        ${a.description ? `<div class="ach-desc">${this.esc(a.description)}</div>` : ''}
                    </div>
                </div>
                <div class="ach-card-footer">
                    <button class="btn btn-xs btn-outline" onclick="coachProfile.editAch(${a.id})">
                        <i class="fas fa-pen"></i> Edit
                    </button>
                    <button class="btn btn-xs btn-danger-soft" onclick="coachProfile.confirmDelete('ach', ${a.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    openAchModal(ach = null) {
        const f = document.getElementById('achForm');
        f.reset();
        document.getElementById('achModalTitle').textContent = ach ? 'Edit Achievement' : 'Add Achievement';
        if (ach) {
            f.querySelector('[name="ach_id"]').value        = ach.id;
            f.querySelector('[name="title"]').value         = ach.title || '';
            f.querySelector('[name="category"]').value      = ach.category || 'other';
            f.querySelector('[name="date_achieved"]').value = ach.date_achieved || '';
            f.querySelector('[name="description"]').value   = ach.description || '';
        }
        this.openModal('modalAch');
    }

    async editAch(id) {
        const r = await fetch('/api/coach/achievements');
        const d = await r.json();
        const a = (d.achievements || []).find(x => x.id == id);
        if (a) this.openAchModal(a);
    }

    async saveAch() {
        const form = document.getElementById('achForm');
        const data = Object.fromEntries(new FormData(form).entries());
        if (!data.title) { this.toast('Title is required', 'error'); return; }

        const id = data.ach_id;
        const payload = {
            title: data.title,
            category: data.category || 'other',
            date_achieved: data.date_achieved || null,
            description:   data.description  || null,
        };

        try {
            const r = await fetch(id ? `/api/coach/achievements/${id}` : '/api/coach/achievements', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const d = await r.json();
            if (!d.success) throw new Error(d.error || 'Failed');
            this.toast(id ? 'Achievement updated' : 'Achievement added', 'success');
            this.closeModal('modalAch');
            await this.loadAchievements();
        } catch (e) { this.toast('Error: ' + e.message, 'error'); }
    }

    /* ── Delete ────────────────────────────────────────── */
    confirmDelete(type, id) {
        const label = type === 'cert' ? 'certificate' : 'achievement';
        document.getElementById('deleteMsg').textContent =
            `Are you sure you want to delete this ${label}? This action cannot be undone.`;
        document.getElementById('deleteConfirmBtn').onclick = () => this._doDelete(type, id);
        this.openModal('modalDelete');
    }

    async _doDelete(type, id) {
        const url = type === 'cert' ? `/api/coach/certificates/${id}` : `/api/coach/achievements/${id}`;
        try {
            const r = await fetch(url, { method: 'DELETE' });
            const d = await r.json();
            if (!d.success) throw new Error(d.message || 'Delete failed');
            this.toast('Deleted successfully', 'success');
            this.closeModal('modalDelete');
            if (type === 'cert') await this.loadCertificates();
            else                 await this.loadAchievements();
        } catch (e) { this.toast('Error: ' + e.message, 'error'); }
    }

    /* ── Modals ────────────────────────────────────────── */
    openModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    closeModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.style.display = 'none';
        document.body.style.overflow = '';
    }

    /* ── Utils ─────────────────────────────────────────── */
    _set(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    esc(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    toast(msg, type = 'info') {
        const el = document.createElement('div');
        el.className = `toast toast-${type}`;
        const iconMap = { success:'check-circle', error:'exclamation-circle', info:'info-circle' };
        el.innerHTML = `<i class="fas fa-${iconMap[type]||'info-circle'}"></i><span>${msg}</span>`;
        document.body.appendChild(el);
        setTimeout(() => el.classList.add('show'), 60);
        setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 350); }, 3800);
    }
}

const coachProfile = new CoachProfile();
</script>
</body>
</html>
