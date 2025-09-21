<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management - Ground Owner Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .dashboard-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #667eea;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-card .label {
            font-size: 1rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .bookings-section {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.8rem;
            color: #333;
        }

        .filters {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .filter-select {
            padding: 0.5rem 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            background: white;
            font-size: 0.9rem;
        }

        .bookings-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status.confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status.completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #c82333;
        }

        .btn-complete {
            background-color: #28a745;
            color: white;
        }

        .btn-complete:hover {
            background-color: #218838;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            font-size: 1.1rem;
            color: #666;
        }

        .no-bookings {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .no-bookings i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 2rem;
            }

            .stats-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .bookings-section {
                padding: 0 1rem;
            }

            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .bookings-table {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
     <!-- Include Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="dashboard-header">
        <h1><i class="fas fa-calendar-check"></i> Booking Management</h1>
        <p>Manage your ground bookings efficiently</p>
    </div>

    <div class="stats-container" id="statsContainer">
        <div class="stat-card">
            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="number" id="totalBookings">-</div>
            <div class="label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="fas fa-clock"></i></div>
            <div class="number" id="todayBookings">-</div>
            <div class="label">Today's Bookings</div>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
            <div class="number" id="upcomingBookings">-</div>
            <div class="label">Upcoming</div>
        </div>
        <div class="stat-card">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div class="number" id="completedBookings">-</div>
            <div class="label">Completed</div>
        </div>
    </div>

    <div class="bookings-section">
        <div class="section-header">
            <h2 class="section-title">Recent Bookings</h2>
            <div class="filters">
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

        <div class="bookings-table">
            <div id="loadingMessage" class="loading">
                <i class="fas fa-spinner fa-spin"></i> Loading bookings...
            </div>
            <table id="bookingsTable" style="display: none;">
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
            <div id="noBookings" class="no-bookings" style="display: none;">
                <i class="fas fa-calendar-times"></i>
                <h3>No bookings found</h3>
                <p>No bookings match your current filters.</p>
            </div>
        </div>
    </div>

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
                const table = document.getElementById('bookingsTable');
                const noBookings = document.getElementById('noBookings');

                loadingMessage.style.display = 'none';

                if (this.bookings.length === 0) {
                    table.style.display = 'none';
                    noBookings.style.display = 'block';
                    return;
                }

                table.style.display = 'table';
                noBookings.style.display = 'none';

                tbody.innerHTML = this.bookings.map(booking => `
                    <tr>
                        <td>#${booking.id}</td>
                        <td>
                            <strong>${booking.first_name} ${booking.last_name}</strong><br>
                            <small>${booking.email}</small>
                        </td>
                        <td>${booking.facility_name}</td>
                        <td>${new Date(booking.booking_date).toLocaleDateString()}</td>
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
</body>
</html>