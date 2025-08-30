<?php 
$title = 'My Grounds - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-grounds.css'
];
$additionalJS = ['/public/js/pages/ground-owner-grounds.js'];
?>

<div class="ground-owner-dashboard">
    <!-- Include Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">My Grounds</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="btn-primary" onclick="addNewGround()">
                        <i class="fas fa-plus"></i>
                        Add New Ground
                    </button>
                </div>
                <div class="header-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">3</span>
                    </button>
                </div>
                <div class="owner-profile">
                    <div class="profile-info">
                        <span class="profile-name">Rajesh Perera</span>
                        <span class="profile-role">Ground Owner</span>
                    </div>
                    <img src="/public/assets/images/owner-avatar.jpg" alt="Owner" class="profile-avatar">
                    <button class="profile-dropdown">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Grounds Content -->
        <div class="dashboard-content">
            <!-- Filter and Search -->
            <div class="grounds-filters">
                <div class="filter-group">
                    <select id="sportFilter" class="filter-select">
                        <option value="">All Sports</option>
                        <option value="football">Football</option>
                        <option value="cricket">Cricket</option>
                        <option value="tennis">Tennis</option>
                        <option value="basketball">Basketball</option>
                        <option value="swimming">Swimming</option>
                        <option value="badminton">Badminton</option>
                    </select>
                    <select id="statusFilter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="search-group">
                    <input type="text" id="groundSearch" placeholder="Search grounds..." class="search-input">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Grounds Grid -->
            <div class="grounds-grid" id="groundsContainer">
                <!-- Grounds will be loaded dynamically -->
                <div class="ground-card loading-placeholder">
                    <div class="loading-shimmer"></div>
                </div>
                <div class="ground-card loading-placeholder">
                    <div class="loading-shimmer"></div>
                </div>
                <div class="ground-card loading-placeholder">
                    <div class="loading-shimmer"></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit Ground Modal -->
    <div id="groundModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Add New Ground</h3>
                <button class="modal-close" onclick="closeGroundModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="groundForm">
                    <input type="hidden" id="groundId">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="groundName">Ground Name *</label>
                            <input type="text" id="groundName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="sportCategory">Sport Category *</label>
                            <select id="sportCategory" name="sport_category_id" required>
                                <option value="">Select Sport</option>
                                <option value="1">Basketball</option>
                                <option value="2">Tennis</option>
                                <option value="3">Football</option>
                                <option value="4">Cricket</option>
                                <option value="5">Swimming</option>
                                <option value="6">Badminton</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" rows="2" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="postalCode">Postal Code</label>
                            <input type="text" id="postalCode" name="postal_code">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="hourlyRate">Hourly Rate (LKR) *</label>
                            <input type="number" id="hourlyRate" name="hourly_rate" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="capacity">Capacity</label>
                            <input type="number" id="capacity" name="capacity">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="amenities">Amenities</label>
                        <div class="amenities-grid">
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="floodlights">
                                <span>Floodlights</span>
                            </label>
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="changing_rooms">
                                <span>Changing Rooms</span>
                            </label>
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="parking">
                                <span>Parking</span>
                            </label>
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="refreshments">
                                <span>Refreshments</span>
                            </label>
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="security">
                                <span>Security</span>
                            </label>
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="first_aid">
                                <span>First Aid</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rules">Ground Rules</label>
                        <textarea id="rules" name="rules" rows="3" placeholder="Enter specific rules for your ground..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeGroundModal()">Cancel</button>
                <button type="submit" class="btn-primary" form="groundForm">Save Ground</button>
            </div>
        </div>
    </div>
</div>