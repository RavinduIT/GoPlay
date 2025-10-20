<?php
$title = 'Booking Management - Ground Owner Dashboard';
$additionalCSS = ['/public/css/pages/ground-owner-dashboard.css'];
$additionalJS = [];
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
                <h1 class="page-title">
                    <i class="fas fa-calendar-check"></i> Booking Management
                </h1>
            </div>
            <div class="header-right">
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

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card bookings">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Bookings</h3>
                        <p class="stat-number" id="totalBookings">-</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>All time bookings</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card earnings">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Today's Bookings</h3>
                        <p class="stat-number" id="todayBookings">-</p>
                        <div class="stat-change positive">
                            <i class="fas fa-calendar-day"></i>
                            <span>Active today</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card occupancy">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Upcoming</h3>
                        <p class="stat-number" id="upcomingBookings">-</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-right"></i>
                            <span>Future bookings</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card rating">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Completed</h3>
                        <p class="stat-number" id="completedBookings">-</p>
                        <div class="stat-change positive">
                            <i class="fas fa-check-double"></i>
                            <span>Successfully completed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings Section -->
            <div class="content-card">
                <div class="card-header">
                    <h2>
                        <i class="fas fa-calendar-alt"></i>
                        Recent Bookings
                    </h2>
                    <div class="filter-controls">
                        <select class="filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select class="filter-select" id="facilityFilter">
                            <option value="">All Facilities</option>
                        </select>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Loading State -->
                    <div id="loadingMessage" class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading bookings...</p>
                    </div>

                    <!-- Bookings Table -->
                    <div class="table-responsive" id="bookingsTableContainer" style="display: none;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Facility</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                            </tbody>
                        </table>
                    </div>

                    <!-- No Bookings State -->
                    <div id="noBookings" class="empty-state" style="display: none;">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No bookings found</h3>
                        <p>No bookings match your current filters.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    /* Additional styles specific to booking dashboard */
    .filter-controls {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .filter-select {
        padding: 0.5rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        font-size: 0.875rem;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.2s;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }

    .loading-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .loading-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--primary-color);
    }

    .loading-state p {
        font-size: 1rem;
        margin-top: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
        color: #94a3b8;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        color: #334155;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #64748b;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead tr {
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .data-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
    }

    .data-table tbody tr {
        transition: background-color 0.2s;
    }

    .data-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .data-table td small {
        color: #64748b;
        font-size: 0.813rem;
    }

    .status {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status.confirmed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status.completed {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status.cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .action-btn {
        padding: 0.5rem 0.875rem;
        border: none;
        border-radius: 6px;
        font-size: 0.813rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        margin-right: 0.5rem;
    }

    .btn-complete {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-complete:hover {
        background-color: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-cancel {
        background-color: #ef4444;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .text-muted {
        color: #94a3b8;
        font-style: italic;
    }

    @media (max-width: 768px) {
        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .data-table {
            font-size: 0.813rem;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem 0.5rem;
        }

        .action-btn {
            padding: 0.375rem 0.625rem;
            font-size: 0.75rem;
        }
    }
</style>

<script>
    // Dashboard functionality
    class BookingDashboard {
        constructor() {
            this.bookings = [];
            this.stats = {};
            this.facilities = [];
            this.init();
        }

        async init() {
            await this.loadDashboardData();
            this.setupEventListeners();
            this.renderStats();
            this.renderBookings();
        }

        async loadDashboardData() {
            try {
                const response = await fetch('/api/ground-owner/dashboard-stats');
                const data = await response.json();

                if (data.success) {
                    this.stats = data.stats;
                    this.bookings = data.recent_bookings || [];
                    this.facilities = data.facilities || [];
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }

        async loadBookings(filters = {}) {
            try {
                const queryParams = new URLSearchParams(filters).toString();
                const response = await fetch(`/api/ground-owner/bookings?${queryParams}`);
                const data = await response.json();

                if (data.success) {
                    this.bookings = data.bookings;
                    this.renderBookings();
                }
            } catch (error) {
                console.error('Error loading bookings:', error);
            }
        }

        renderStats() {
            if (this.stats.bookings) {
                document.getElementById('totalBookings').textContent = this.stats.bookings.total_bookings || 0;
                document.getElementById('todayBookings').textContent = this.stats.bookings.today_bookings || 0;
                document.getElementById('upcomingBookings').textContent = this.stats.bookings.upcoming_bookings || 0;
                document.getElementById('completedBookings').textContent = this.stats.bookings.completed_bookings || 0;
            }
        }

        renderBookings() {
            const tbody = document.getElementById('bookingsTableBody');
            const loadingMessage = document.getElementById('loadingMessage');
            const tableContainer = document.getElementById('bookingsTableContainer');
            const noBookings = document.getElementById('noBookings');

            loadingMessage.style.display = 'none';

            if (this.bookings.length === 0) {
                tableContainer.style.display = 'none';
                noBookings.style.display = 'block';
                return;
            }

            tableContainer.style.display = 'block';
            noBookings.style.display = 'none';

            tbody.innerHTML = this.bookings.map(booking => `
                <tr>
                    <td><strong>#${booking.id}</strong></td>
                    <td>
                        <strong>${booking.first_name} ${booking.last_name}</strong><br>
                        <small>${booking.email}</small>
                    </td>
                    <td>${booking.facility_name}</td>
                    <td>${new Date(booking.booking_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                    <td>${booking.start_time} - ${booking.end_time}</td>
                    <td>${booking.duration_hours}h</td>
                    <td><span class="status ${booking.status}">${booking.status}</span></td>
                    <td>
                        ${this.getActionButtons(booking)}
                    </td>
                </tr>
            `).join('');
        }

        getActionButtons(booking) {
            let buttons = '';

            if (booking.status === 'confirmed' || booking.status === 'pending') {
                buttons += `<button class="action-btn btn-complete" onclick="dashboard.updateBookingStatus(${booking.id}, 'completed')">
                    <i class="fas fa-check"></i> Complete
                </button>`;
            }

            if (booking.status !== 'cancelled' && booking.status !== 'completed') {
                buttons += `<button class="action-btn btn-cancel" onclick="dashboard.cancelBooking(${booking.id})">
                    <i class="fas fa-times"></i> Cancel
                </button>`;
            }

            return buttons || '<span class="text-muted">No actions</span>';
        }

        async updateBookingStatus(bookingId, status) {
            if (!confirm(`Are you sure you want to mark this booking as ${status}?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/ground-owner/bookings/${bookingId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Booking status updated successfully');
                    await this.loadDashboardData();
                    this.renderStats();
                    this.renderBookings();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error updating booking status:', error);
                alert('Error updating booking status');
            }
        }

        async cancelBooking(bookingId) {
            const reason = prompt('Please provide a reason for cancellation:');
            if (!reason) return;

            try {
                const response = await fetch(`/api/ground-owner/bookings/${bookingId}/cancel`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ reason })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Booking cancelled successfully');
                    await this.loadDashboardData();
                    this.renderStats();
                    this.renderBookings();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error cancelling booking:', error);
                alert('Error cancelling booking');
            }
        }

        setupEventListeners() {
            const statusFilter = document.getElementById('statusFilter');
            const facilityFilter = document.getElementById('facilityFilter');

            statusFilter.addEventListener('change', () => {
                this.applyFilters();
            });

            facilityFilter.addEventListener('change', () => {
                this.applyFilters();
            });
        }

        applyFilters() {
            const filters = {};
            const status = document.getElementById('statusFilter').value;
            const facility = document.getElementById('facilityFilter').value;

            if (status) filters.status = status;
            if (facility) filters.facility_id = facility;

            this.loadBookings(filters);
        }
    }

    // Initialize dashboard
    const dashboard = new BookingDashboard();
</script>
