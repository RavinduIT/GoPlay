<?php
$title = 'My Grounds - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-grounds.css',
];
$additionalJS = ['/public/js/pages/ground-owner-grounds.js'];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <!-- ── Page Header ───────────────────────────────── -->
        <div class="gr-page-header">
            <div class="gr-header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="gr-page-title"><i class="fas fa-map-marker-alt"></i> My Grounds</h1>
                    <p class="gr-page-sub">Manage your sports facilities</p>
                </div>
            </div>
            <div class="gr-header-right">
                <button class="gr-btn gr-btn-primary" id="addGroundBtn">
                    <i class="fas fa-plus"></i> Add New Ground
                </button>
            </div>
        </div>

        <div class="gr-content">

            <!-- ── Stats Grid ─────────────────────────── -->
            <div class="gr-stats-grid">
                <div class="gr-stat-card gr-stat-blue">
                    <div class="gr-stat-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="gr-stat-body">
                        <div class="gr-stat-value" id="statTotal">—</div>
                        <div class="gr-stat-label">Total Grounds</div>
                    </div>
                </div>
                <div class="gr-stat-card gr-stat-green">
                    <div class="gr-stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="gr-stat-body">
                        <div class="gr-stat-value" id="statActive">—</div>
                        <div class="gr-stat-label">Active</div>
                    </div>
                </div>
                <div class="gr-stat-card gr-stat-orange">
                    <div class="gr-stat-icon"><i class="fas fa-tools"></i></div>
                    <div class="gr-stat-body">
                        <div class="gr-stat-value" id="statMaintenance">—</div>
                        <div class="gr-stat-label">Maintenance</div>
                    </div>
                </div>
                <div class="gr-stat-card gr-stat-red">
                    <div class="gr-stat-icon"><i class="fas fa-pause-circle"></i></div>
                    <div class="gr-stat-body">
                        <div class="gr-stat-value" id="statInactive">—</div>
                        <div class="gr-stat-label">Inactive</div>
                    </div>
                </div>
            </div>

            <!-- ── Filters ────────────────────────────── -->
            <div class="gr-filters-bar">
                <div class="gr-search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="filterSearch" placeholder="Search by name or city…">
                </div>
                <select id="filterSport">
                    <option value="">All Sports</option>
                </select>
                <select id="filterStatus">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- ── Loading ────────────────────────────── -->
            <div id="grLoading" class="gr-loading">
                <i class="fas fa-spinner fa-spin"></i> Loading your grounds…
            </div>

            <!-- ── Empty State ────────────────────────── -->
            <div id="grEmpty" class="gr-empty" style="display:none">
                <i class="fas fa-map-marker-alt"></i>
                <h3>No grounds found</h3>
                <p>Add your first ground to start accepting bookings.</p>
                <button class="gr-btn gr-btn-primary" id="addGroundBtnEmpty">
                    <i class="fas fa-plus"></i> Add Ground
                </button>
            </div>

            <!-- ── Grounds Grid ───────────────────────── -->
            <div id="grGrid" class="gr-grid" style="display:none"></div>

        </div><!-- .gr-content -->
    </main>
</div>

<!-- ── Add / Edit Ground Modal ──────────────────────────────── -->
<div id="grBackdrop" class="gr-backdrop"></div>
<div id="grModal" class="gr-modal">
    <div class="gr-modal-box">
        <div class="gr-modal-head">
            <h3 id="grModalTitle"><i class="fas fa-plus-circle"></i> Add New Ground</h3>
            <button id="grCloseModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="gr-modal-body">
            <form id="grForm" autocomplete="off">
                <input type="hidden" id="grId">

                <!-- Section 1: Basic Info -->
                <div class="gr-form-section">
                    <div class="gr-section-title"><i class="fas fa-info-circle"></i> Basic Information</div>
                    <div class="gr-form-row">
                        <div class="gr-fgroup gr-span2">
                            <label>Ground Name <span class="gr-req">*</span></label>
                            <input type="text" id="grName" placeholder="e.g. Green Valley Football Ground">
                        </div>
                        <div class="gr-fgroup">
                            <label>Sport Category <span class="gr-req">*</span></label>
                            <select id="grCategory">
                                <option value="">Select Sport</option>
                            </select>
                        </div>
                    </div>
                    <div class="gr-form-row">
                        <div class="gr-fgroup gr-span2">
                            <label>Description</label>
                            <textarea id="grDesc" rows="3" placeholder="Describe your ground — surface type, size, special features…"></textarea>
                        </div>
                        <div class="gr-fgroup">
                            <label>Status</label>
                            <select id="grStatus">
                                <option value="active">Active — open for bookings</option>
                                <option value="maintenance">Maintenance — temporarily closed</option>
                                <option value="inactive">Inactive — hidden from public</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Location -->
                <div class="gr-form-section">
                    <div class="gr-section-title"><i class="fas fa-map-marker-alt"></i> Location</div>
                    <div class="gr-fgroup">
                        <label>Street Address <span class="gr-req">*</span></label>
                        <textarea id="grAddress" rows="2" placeholder="Full street address"></textarea>
                    </div>
                    <div class="gr-form-row gr-form-row3">
                        <div class="gr-fgroup">
                            <label>City <span class="gr-req">*</span></label>
                            <input type="text" id="grCity" placeholder="e.g. Colombo">
                        </div>
                        <div class="gr-fgroup">
                            <label>Province / State</label>
                            <input type="text" id="grState" placeholder="e.g. Western Province">
                        </div>
                        <div class="gr-fgroup">
                            <label>Postal Code</label>
                            <input type="text" id="grPostal" placeholder="e.g. 10100">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Pricing & Capacity -->
                <div class="gr-form-section">
                    <div class="gr-section-title"><i class="fas fa-tag"></i> Pricing &amp; Capacity</div>
                    <div class="gr-form-row">
                        <div class="gr-fgroup">
                            <label>Hourly Rate <span class="gr-req">*</span></label>
                            <div class="gr-input-prefix">
                                <span>LKR</span>
                                <input type="number" id="grRate" placeholder="0" min="0" step="50">
                            </div>
                        </div>
                        <div class="gr-fgroup">
                            <label>Max Capacity <small>(players)</small></label>
                            <input type="number" id="grCapacity" placeholder="e.g. 22" min="1">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Amenities -->
                <div class="gr-form-section">
                    <div class="gr-section-title"><i class="fas fa-concierge-bell"></i> Amenities</div>
                    <div class="gr-amenities-grid">
                        <label class="gr-amenity-item" data-val="floodlights">
                            <input type="checkbox" value="floodlights">
                            <i class="fas fa-lightbulb"></i>
                            <span>Floodlights</span>
                        </label>
                        <label class="gr-amenity-item" data-val="changing_rooms">
                            <input type="checkbox" value="changing_rooms">
                            <i class="fas fa-door-open"></i>
                            <span>Changing Rooms</span>
                        </label>
                        <label class="gr-amenity-item" data-val="parking">
                            <input type="checkbox" value="parking">
                            <i class="fas fa-parking"></i>
                            <span>Parking</span>
                        </label>
                        <label class="gr-amenity-item" data-val="refreshments">
                            <input type="checkbox" value="refreshments">
                            <i class="fas fa-coffee"></i>
                            <span>Refreshments</span>
                        </label>
                        <label class="gr-amenity-item" data-val="security">
                            <input type="checkbox" value="security">
                            <i class="fas fa-shield-alt"></i>
                            <span>Security</span>
                        </label>
                        <label class="gr-amenity-item" data-val="first_aid">
                            <input type="checkbox" value="first_aid">
                            <i class="fas fa-first-aid"></i>
                            <span>First Aid</span>
                        </label>
                        <label class="gr-amenity-item" data-val="equipment_rental">
                            <input type="checkbox" value="equipment_rental">
                            <i class="fas fa-futbol"></i>
                            <span>Equipment Rental</span>
                        </label>
                        <label class="gr-amenity-item" data-val="shower">
                            <input type="checkbox" value="shower">
                            <i class="fas fa-shower"></i>
                            <span>Showers</span>
                        </label>
                        <label class="gr-amenity-item" data-val="wifi">
                            <input type="checkbox" value="wifi">
                            <i class="fas fa-wifi"></i>
                            <span>WiFi</span>
                        </label>
                        <label class="gr-amenity-item" data-val="cctv">
                            <input type="checkbox" value="cctv">
                            <i class="fas fa-video"></i>
                            <span>CCTV</span>
                        </label>
                        <label class="gr-amenity-item" data-val="cafeteria">
                            <input type="checkbox" value="cafeteria">
                            <i class="fas fa-utensils"></i>
                            <span>Cafeteria</span>
                        </label>
                        <label class="gr-amenity-item" data-val="wheelchair_access">
                            <input type="checkbox" value="wheelchair_access">
                            <i class="fas fa-wheelchair"></i>
                            <span>Wheelchair Access</span>
                        </label>
                    </div>
                </div>

                <!-- Section 5: Rules -->
                <div class="gr-form-section">
                    <div class="gr-section-title"><i class="fas fa-clipboard-list"></i> Ground Rules</div>
                    <div class="gr-fgroup">
                        <label>Rules &amp; Regulations</label>
                        <textarea id="grRules" rows="4" placeholder="e.g. No smoking on premises, Proper sports shoes required, Cancellations must be made 24 hours in advance…"></textarea>
                    </div>
                </div>

            </form>
        </div>
        <div class="gr-modal-footer">
            <button class="gr-btn gr-btn-ghost" id="grCancelBtn">Cancel</button>
            <button class="gr-btn gr-btn-primary" id="grSaveBtn">
                <i class="fas fa-save"></i> Save Ground
            </button>
        </div>
    </div>
</div>

<!-- ── Delete Confirm Modal ──────────────────────────────────── -->
<div id="grDelBackdrop" class="gr-backdrop"></div>
<div id="grDelModal" class="gr-modal">
    <div class="gr-modal-box gr-modal-sm">
        <div class="gr-modal-head gr-head-red">
            <h3><i class="fas fa-trash-alt"></i> Remove Ground</h3>
            <button id="grDelClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="gr-modal-body" style="padding:24px">
            <p style="margin:0;font-size:15px;color:#374151;line-height:1.6">
                Are you sure you want to remove <strong id="grDelName"></strong>?
                This will deactivate the ground and hide it from public bookings.
            </p>
        </div>
        <div class="gr-modal-footer">
            <button class="gr-btn gr-btn-ghost" id="grDelCancel">Keep Ground</button>
            <button class="gr-btn gr-btn-danger" id="grDelConfirm">
                <i class="fas fa-trash-alt"></i> Remove
            </button>
        </div>
    </div>
</div>

<script src="/public/js/pages/ground-owner-grounds.js"></script>
