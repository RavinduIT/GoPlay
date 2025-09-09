<?php 
$title = 'Bookings Management - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-bookings.css'
];
$additionalJS = ['/public/js/pages/ground-owner-bookings.js'];
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
                <h1 class="page-title">Bookings Management</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="btn-secondary" onclick="exportBookings()">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                    <div class="date-filter">
                        <input type="date" id="startDate" class="date-input">
                        <span>to</span>
                        <input type="date" id="endDate" class="date-input">
                    </div>
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

        <!-- Bookings Content -->
        <div class="dashboard-content">
            <!-- Bookings Stats -->
            <div class="bookings-stats">
                <div class="stat-card total">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Bookings</h3>
                        <p class="stat-number" id="totalBookings">0</p>
                        <span class="stat-period">This Month</span>
                    </div>
                </div>
                <div class="stat-card confirmed">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Confirmed</h3>
                        <p class="stat-number" id="confirmedBookings">0</p>
                        <span class="stat-period">This Month</span>
                    </div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Pending</h3>
                        <p class="stat-number" id="pendingBookings">0</p>
                        <span class="stat-period">Awaiting Response</span>
                    </div>
                </div>
                <div class="stat-card revenue">
                    <div class="stat-icon">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Revenue</h3>
                        <p class="stat-number" id="monthlyRevenue">₹0</p>
                        <span class="stat-period">This Month</span>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bookings-filters">
                <div class="filter-group">
                    <select id="statusFilter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select id="groundFilter" class="filter-select">
                        <option value="">All Grounds</option>
                    </select>
                    <select id="sortBy" class="filter-select">
                        <option value="booking_date">Sort by Date</option>
                        <option value="created_at">Sort by Created</option>
                        <option value="amount">Sort by Amount</option>
                    </select>
                </div>
                <div class="search-group">
                    <input type="text" id="bookingSearch" placeholder="Search bookings..." class="search-input">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Bookings Calendar View -->
            <div class="view-toggles">
                <button class="view-btn active" data-view="list">
                    <i class="fas fa-list"></i>
                    List View
                </button>
                <button class="view-btn" data-view="calendar">
                    <i class="fas fa-calendar"></i>
                    Calendar View
                </button>
                <button class="view-btn" data-view="timeline">
                    <i class="fas fa-clock"></i>
                    Timeline View
                </button>
            </div>

            <!-- Bookings List -->
            <div class="bookings-container" id="bookingsContainer">
                <!-- List View -->
                <div class="bookings-list view-content active" id="listView">
                    <div class="bookings-table">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Ground</th>
                                    <th>Customer</th>
                                    <th>Date & Time</th>
                                    <th>Duration</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Calendar View -->
                <div class="bookings-calendar view-content" id="calendarView">
                    <div class="calendar-header">
                        <button class="calendar-nav" id="prevMonth">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <h3 id="currentMonth">December 2024</h3>
                        <button class="calendar-nav" id="nextMonth">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="calendar-grid" id="calendarGrid">
                        <!-- Calendar will be generated dynamically -->
                    </div>
                </div>

                <!-- Timeline View -->
                <div class="bookings-timeline view-content" id="timelineView">
                    <div class="timeline-container" id="timelineContainer">
                        <!-- Timeline will be generated dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Booking Details Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3 id="modalTitle">Booking Details</h3>
                <button class="modal-close" onclick="closeBookingModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="booking-details-grid">
                    <div class="booking-info-section">
                        <h4>Booking Information</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Booking ID:</label>
                                <span id="bookingId">-</span>
                            </div>
                            <div class="info-item">
                                <label>Ground:</label>
                                <span id="bookingGround">-</span>
                            </div>
                            <div class="info-item">
                                <label>Date:</label>
                                <span id="bookingDate">-</span>
                            </div>
                            <div class="info-item">
                                <label>Time:</label>
                                <span id="bookingTime">-</span>
                            </div>
                            <div class="info-item">
                                <label>Duration:</label>
                                <span id="bookingDuration">-</span>
                            </div>
                            <div class="info-item">
                                <label>Amount:</label>
                                <span id="bookingAmount">-</span>
                            </div>
                            <div class="info-item">
                                <label>Status:</label>
                                <span id="bookingStatus" class="status-badge">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="customer-info-section">
                        <h4>Customer Information</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Name:</label>
                                <span id="customerName">-</span>
                            </div>
                            <div class="info-item">
                                <label>Email:</label>
                                <span id="customerEmail">-</span>
                            </div>
                            <div class="info-item">
                                <label>Phone:</label>
                                <span id="customerPhone">-</span>
                            </div>
                            <div class="info-item">
                                <label>Previous Bookings:</label>
                                <span id="customerBookings">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="booking-notes">
                    <h4>Special Requests/Notes</h4>
                    <p id="bookingNotes">No special requests</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeBookingModal()">Close</button>
                <button type="button" class="btn-success" onclick="confirmBooking()" id="confirmBtn">Confirm</button>
                <button type="button" class="btn-danger" onclick="cancelBooking()" id="cancelBtn">Cancel</button>
                <button type="button" class="btn-primary" onclick="contactCustomer()" id="contactBtn">Contact Customer</button>
            </div>
        </div>
    </div>
</div>