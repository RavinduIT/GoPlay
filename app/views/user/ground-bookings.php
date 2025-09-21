<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Ground Bookings - GoPlay Sports Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-section {
            padding: 2rem 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card .icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #667eea;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-card .label {
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .bookings-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 1.5rem;
            color: #333;
            margin: 0;
        }

        .new-booking-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .new-booking-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .bookings-list {
            padding: 1.5rem;
        }

        .booking-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .booking-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .booking-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .booking-info {
            flex: 1;
        }

        .facility-name {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-item i {
            color: #667eea;
            width: 20px;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: capitalize;
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

        .booking-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #c82333;
        }

        .btn-view {
            background-color: #6c757d;
            color: white;
        }

        .btn-view:hover {
            background-color: #5a6268;
        }

        .loading {
            text-align: center;
            padding: 3rem;
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

        .special-requests {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-top: 1rem;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .booking-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-calendar-check"></i> My Ground Bookings</h1>
            <p>Manage your sports ground reservations</p>
        </div>
    </div>

    <div class="container">
        <div class="stats-section">
            <div class="stats-grid" id="statsGrid">
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="number" id="totalBookings">-</div>
                    <div class="label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div class="number" id="upcomingBookings">-</div>
                    <div class="label">Upcoming</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <div class="number" id="completedBookings">-</div>
                    <div class="label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                    <div class="number" id="cancelledBookings">-</div>
                    <div class="label">Cancelled</div>
                </div>
            </div>

            <div class="bookings-section">
                <div class="section-header">
                    <h2 class="section-title">Your Bookings</h2>
                    <a href="/book-ground" class="new-booking-btn">
                        <i class="fas fa-plus"></i> Book New Ground
                    </a>
                </div>

                <div class="bookings-list">
                    <div id="loadingMessage" class="loading">
                        <i class="fas fa-spinner fa-spin"></i> Loading your bookings...
                    </div>

                    <div id="bookingsList" style="display: none;"></div>

                    <div id="noBookings" class="no-bookings" style="display: none;">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No bookings yet</h3>
                        <p>You haven't made any ground bookings yet. Book your first ground to get started!</p>
                        <a href="/book-ground" class="new-booking-btn" style="margin-top: 1rem;">
                            <i class="fas fa-plus"></i> Book Your First Ground
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        class UserBookingDashboard {
            constructor() {
                this.bookings = [];
                this.stats = {};
                this.init();
            }

            async init() {
                await this.loadBookings();
                this.renderStats();
                this.renderBookings();
            }

            async loadBookings() {
                try {
                    const response = await fetch('/api/user/ground-bookings');
                    const data = await response.json();

                    if (data.success) {
                        this.bookings = data.bookings || [];
                        this.stats = data.stats || {};
                    } else {
                        console.error('Error loading bookings:', data.message);
                    }
                } catch (error) {
                    console.error('Error loading bookings:', error);
                }
            }

            renderStats() {
                document.getElementById('totalBookings').textContent = this.stats.total_bookings || 0;
                document.getElementById('upcomingBookings').textContent = this.stats.upcoming_bookings || 0;
                document.getElementById('completedBookings').textContent = this.stats.completed_bookings || 0;
                document.getElementById('cancelledBookings').textContent = this.stats.cancelled_bookings || 0;
            }

            renderBookings() {
                const loadingMessage = document.getElementById('loadingMessage');
                const bookingsList = document.getElementById('bookingsList');
                const noBookings = document.getElementById('noBookings');

                loadingMessage.style.display = 'none';

                if (this.bookings.length === 0) {
                    noBookings.style.display = 'block';
                    bookingsList.style.display = 'none';
                    return;
                }

                noBookings.style.display = 'none';
                bookingsList.style.display = 'block';

                bookingsList.innerHTML = this.bookings.map(booking => `
                    <div class="booking-card">
                        <div class="booking-header">
                            <div class="booking-info">
                                <h3 class="facility-name">${booking.facility_name}</h3>
                                <span class="status ${booking.status}">${booking.status}</span>
                            </div>
                        </div>

                        <div class="booking-details">
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Date:</strong> ${new Date(booking.booking_date).toLocaleDateString()}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span><strong>Time:</strong> ${booking.start_time} - ${booking.end_time}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-hourglass-half"></i>
                                <span><strong>Duration:</strong> ${booking.duration_hours} hours</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><strong>Location:</strong> ${booking.facility_address || 'N/A'}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-tag"></i>
                                <span><strong>Sport:</strong> ${booking.sport_name || 'General'}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span><strong>Owner:</strong> ${booking.owner_phone || 'N/A'}</span>
                            </div>
                        </div>

                        ${booking.special_requests ? `
                            <div class="special-requests">
                                <strong>Special Requests:</strong> ${booking.special_requests}
                            </div>
                        ` : ''}

                        <div class="booking-actions">
                            ${this.getActionButtons(booking)}
                        </div>
                    </div>
                `).join('');
            }

            getActionButtons(booking) {
                let buttons = '';

                // Show cancel button only for confirmed or pending bookings
                if (booking.status === 'confirmed' || booking.status === 'pending') {
                    buttons += `
                        <button class="btn btn-cancel" onclick="dashboard.cancelBooking(${booking.id})">
                            <i class="fas fa-times"></i> Cancel Booking
                        </button>
                    `;
                }

                buttons += `
                    <button class="btn btn-view" onclick="dashboard.viewBookingDetails(${booking.id})">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                `;

                return buttons;
            }

            async cancelBooking(bookingId) {
                const reason = prompt('Please provide a reason for cancellation (optional):');
                if (reason === null) return; // User clicked cancel

                if (!confirm('Are you sure you want to cancel this booking?')) {
                    return;
                }

                try {
                    const response = await fetch(`/api/user/ground-bookings/${bookingId}/cancel`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ reason: reason || 'Cancelled by user' })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Booking cancelled successfully');
                        await this.loadBookings();
                        this.renderStats();
                        this.renderBookings();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error cancelling booking:', error);
                    alert('Error cancelling booking. Please try again.');
                }
            }

            async viewBookingDetails(bookingId) {
                try {
                    const response = await fetch(`/api/user/ground-bookings/${bookingId}`);
                    const data = await response.json();

                    if (data.success) {
                        const booking = data.booking;

                        const details = `
Booking Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📍 Facility: ${booking.facility_name}
📅 Date: ${new Date(booking.booking_date).toLocaleDateString()}
🕐 Time: ${booking.start_time} - ${booking.end_time}
⏱️ Duration: ${booking.duration_hours} hours
🏷️ Status: ${booking.status}
🏃 Sport: ${booking.sport_name || 'General'}
📍 Address: ${booking.facility_address || 'N/A'}
📞 Owner Contact: ${booking.owner_phone || 'N/A'}

${booking.special_requests ? `📝 Special Requests: ${booking.special_requests}` : ''}

Booking ID: #${booking.id}
Created: ${new Date(booking.created_at).toLocaleString()}
                        `;

                        alert(details);
                    } else {
                        alert('Error loading booking details: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error loading booking details:', error);
                    alert('Error loading booking details');
                }
            }
        }

        // Initialize dashboard
        const dashboard = new UserBookingDashboard();
    </script>
</body>
</html>