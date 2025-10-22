<?php
$title = 'My Bookings - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #dbeafe;
        --secondary-color: #0891b2;
        --success-color: #059669;
        --warning-color: #d97706;
        --danger-color: #dc2626;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --background-light: #f9fafb;
        --border-color: #e5e7eb;
    }

    .bookings-page {
        background: var(--background-light);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    .page-header-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
    }

    .page-header-section h1 {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .page-header-section p {
        font-size: 1.1rem;
        opacity: 0.95;
    }

    /* Stats Cards */
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
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid var(--border-color);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .stat-card .icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-card .number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .stat-card .label {
        color: var(--text-secondary);
        font-size: 0.95rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Tab Navigation */
    .tabs-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .tabs-nav {
        display: flex;
        background: var(--background-light);
        border-bottom: 2px solid var(--border-color);
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .tab-button {
        flex: 1;
        padding: 1.25rem 2rem;
        background: none;
        border: none;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .tab-button:hover {
        background: rgba(37, 99, 235, 0.05);
        color: var(--primary-color);
    }

    .tab-button.active {
        background: white;
        color: var(--primary-color);
    }

    .tab-button.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-color);
    }

    .tab-badge {
        background: var(--primary-color);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .tab-button:not(.active) .tab-badge {
        background: var(--text-secondary);
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 2rem;
        animation: fadeIn 0.3s ease;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Booking Cards */
    .bookings-grid {
        display: grid;
        gap: 1.5rem;
    }

    .booking-card {
        background: var(--background-light);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .booking-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: var(--primary-color);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .booking-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.15);
        transform: translateY(-2px);
    }

    .booking-card:hover::before {
        opacity: 1;
    }

    .booking-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1.25rem;
        gap: 1rem;
    }

    .booking-title {
        flex: 1;
    }

    .booking-title h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .booking-subtitle {
        color: var(--text-secondary);
        font-size: 0.95rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-badge.confirmed {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.completed {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-badge.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .booking-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: white;
        padding: 0.75rem;
        border-radius: 8px;
    }

    .detail-item i {
        color: var(--primary-color);
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }

    .detail-item strong {
        color: var(--text-secondary);
        font-size: 0.85rem;
        display: block;
        font-weight: 600;
    }

    .detail-item span {
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .booking-amount {
        background: linear-gradient(135deg, var(--success-color), #047857);
        color: white;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        margin-top: 1rem;
    }

    .booking-amount .label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 0.25rem;
    }

    .booking-amount .amount {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .special-requests {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1rem;
        border-left: 3px solid var(--primary-color);
    }

    .special-requests strong {
        color: var(--text-secondary);
        font-size: 0.85rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .booking-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-cancel {
        background: var(--danger-color);
        color: white;
    }

    .btn-cancel:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-view {
        background: var(--text-secondary);
        color: white;
    }

    .btn-view:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    /* Loading & Empty States */
    .loading-state, .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .loading-state i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .empty-state i {
        font-size: 5rem;
        color: var(--border-color);
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .empty-state p {
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-container {
            padding: 0 1rem;
        }

        .page-header-section h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .tabs-nav {
            flex-direction: column;
        }

        .tab-button {
            padding: 1rem;
        }

        .booking-details {
            grid-template-columns: 1fr;
        }

        .booking-header {
            flex-direction: column;
        }

        .booking-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="bookings-page">
    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header-section">
            <h1><i class="fas fa-calendar-check"></i> My Bookings</h1>
            <p>Manage your sports ground and coaching session bookings</p>
        </div>

        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="number" id="totalBookings">0</div>
                <div class="label">Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="number" id="upcomingBookings">0</div>
                <div class="label">Upcoming</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="number" id="completedBookings">0</div>
                <div class="label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="number" id="totalSpent">LKR 0</div>
                <div class="label">Total Spent</div>
            </div>
        </div>

        <!-- Tabs Container -->
        <div class="tabs-container">
            <ul class="tabs-nav">
                <li>
                    <button class="tab-button active" onclick="switchTab('grounds')" id="groundsTab">
                        <i class="fas fa-futbol"></i>
                        Ground Bookings
                        <span class="tab-badge" id="groundsCount">0</span>
                    </button>
                </li>
                <li>
                    <button class="tab-button" onclick="switchTab('coaches')" id="coachesTab">
                        <i class="fas fa-user-tie"></i>
                        Coach Sessions
                        <span class="tab-badge" id="coachesCount">0</span>
                    </button>
                </li>
            </ul>

            <!-- Ground Bookings Tab -->
            <div id="groundsContent" class="tab-content active">
                <div id="groundsLoading" class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading ground bookings...</p>
                </div>
                <div id="groundsList" class="bookings-grid" style="display: none;"></div>
                <div id="groundsEmpty" class="empty-state" style="display: none;">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No ground bookings yet</h3>
                    <p>You haven't booked any sports grounds yet</p>
                    <a href="/book-ground" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Book Your First Ground
                    </a>
                </div>
            </div>

            <!-- Coach Sessions Tab -->
            <div id="coachesContent" class="tab-content">
                <div id="coachesLoading" class="loading-state">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading coach sessions...</p>
                </div>
                <div id="coachesList" class="bookings-grid" style="display: none;"></div>
                <div id="coachesEmpty" class="empty-state" style="display: none;">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No coach sessions yet</h3>
                    <p>You haven't booked any coaching sessions yet</p>
                    <a href="/book-coach" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Book Your First Session
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    class MyBookingsDashboard {
        constructor() {
            this.groundBookings = [];
            this.coachBookings = [];
            this.init();
        }

        async init() {
            await Promise.all([
                this.loadGroundBookings(),
                this.loadCoachBookings()
            ]);
            this.updateStats();
            this.renderGroundBookings();
            this.renderCoachBookings();
        }

        async loadGroundBookings() {
            try {
                const response = await fetch('/api/user/ground-bookings');
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                const data = await response.json();
                if (data.success) {
                    this.groundBookings = data.bookings || [];
                }
            } catch (error) {
                console.error('Error loading ground bookings:', error);
            }
        }

        async loadCoachBookings() {
            try {
                const response = await fetch('/api/my-coach-bookings');
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                const data = await response.json();
                if (data.success) {
                    this.coachBookings = data.bookings || [];
                }
            } catch (error) {
                console.error('Error loading coach bookings:', error);
            }
        }

        updateStats() {
            const total = this.groundBookings.length + this.coachBookings.length;
            const upcoming = [...this.groundBookings, ...this.coachBookings].filter(b =>
                b.status === 'confirmed' || b.status === 'pending'
            ).length;
            const completed = [...this.groundBookings, ...this.coachBookings].filter(b =>
                b.status === 'completed'
            ).length;
            const totalSpent = [...this.groundBookings, ...this.coachBookings]
                .filter(b => b.status !== 'cancelled')
                .reduce((sum, b) => sum + parseFloat(b.total_amount || 0), 0);

            document.getElementById('totalBookings').textContent = total;
            document.getElementById('upcomingBookings').textContent = upcoming;
            document.getElementById('completedBookings').textContent = completed;
            document.getElementById('totalSpent').textContent = 'LKR ' + totalSpent.toLocaleString();
            document.getElementById('groundsCount').textContent = this.groundBookings.length;
            document.getElementById('coachesCount').textContent = this.coachBookings.length;
        }

        renderGroundBookings() {
            const loading = document.getElementById('groundsLoading');
            const list = document.getElementById('groundsList');
            const empty = document.getElementById('groundsEmpty');

            loading.style.display = 'none';

            if (this.groundBookings.length === 0) {
                empty.style.display = 'block';
                list.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            list.style.display = 'grid';

            list.innerHTML = this.groundBookings.map(booking => `
                <div class="booking-card">
                    <div class="booking-header">
                        <div class="booking-title">
                            <h3>${booking.facility_name}</h3>
                            <div class="booking-subtitle">
                                <i class="fas fa-tag"></i> ${booking.sport_name || 'General Sports'}
                            </div>
                        </div>
                        <span class="status-badge ${booking.status}">${booking.status}</span>
                    </div>

                    <div class="booking-details">
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Date</strong>
                                <span>${new Date(booking.booking_date).toLocaleDateString()}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Time</strong>
                                <span>${booking.start_time} - ${booking.end_time}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-hourglass-half"></i>
                            <div>
                                <strong>Duration</strong>
                                <span>${booking.duration_hours} hours</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Location</strong>
                                <span>${booking.facility_location || 'N/A'}</span>
                            </div>
                        </div>
                    </div>

                    ${booking.special_requests ? `
                        <div class="special-requests">
                            <strong><i class="fas fa-comment"></i> Special Requests</strong>
                            <p>${booking.special_requests}</p>
                        </div>
                    ` : ''}

                    <div class="booking-actions">
                        ${(booking.status === 'confirmed' || booking.status === 'pending') ? `
                            <button class="btn btn-cancel" onclick="dashboard.cancelGroundBooking(${booking.id})">
                                <i class="fas fa-times"></i> Cancel Booking
                            </button>
                        ` : ''}
                        <button class="btn btn-view" onclick="dashboard.viewGroundDetails(${booking.id})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            `).join('');
        }

        renderCoachBookings() {
            const loading = document.getElementById('coachesLoading');
            const list = document.getElementById('coachesList');
            const empty = document.getElementById('coachesEmpty');

            loading.style.display = 'none';

            if (this.coachBookings.length === 0) {
                empty.style.display = 'block';
                list.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            list.style.display = 'grid';

            list.innerHTML = this.coachBookings.map(booking => {
                const coachName = `${booking.coach_first_name} ${booking.coach_last_name}`;
                return `
                <div class="booking-card">
                    <div class="booking-header">
                        <div class="booking-title">
                            <h3><i class="fas fa-user-tie"></i> ${coachName}</h3>
                            <div class="booking-subtitle">
                                <i class="fas fa-trophy"></i> ${booking.sport_name || 'General Coaching'}
                                ${booking.coach_rating ? `<span style="margin-left: 0.5rem"><i class="fas fa-star" style="color: #fbbf24"></i> ${booking.coach_rating}</span>` : ''}
                            </div>
                        </div>
                        <span class="status-badge ${booking.status}">${booking.status}</span>
                    </div>

                    <div class="booking-details">
                        <div class="detail-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <strong>Date</strong>
                                <span>${new Date(booking.booking_date).toLocaleDateString()}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Time</strong>
                                <span>${booking.start_time} - ${booking.end_time}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>Session Type</strong>
                                <span>${booking.session_type.charAt(0).toUpperCase() + booking.session_type.slice(1)}</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Location</strong>
                                <span>${booking.coach_location || 'N/A'}</span>
                            </div>
                        </div>
                    </div>

                    ${booking.total_amount ? `
                        <div class="booking-amount">
                            <div class="label">Session Fee</div>
                            <div class="amount">LKR ${parseFloat(booking.total_amount).toLocaleString()}</div>
                        </div>
                    ` : ''}

                    ${booking.special_requests ? `
                        <div class="special-requests">
                            <strong><i class="fas fa-comment"></i> Special Requests</strong>
                            <p>${booking.special_requests}</p>
                        </div>
                    ` : ''}

                    <div class="booking-actions">
                        ${(booking.status === 'confirmed' || booking.status === 'pending') ? `
                            <button class="btn btn-cancel" onclick="dashboard.cancelCoachBooking(${booking.id})">
                                <i class="fas fa-times"></i> Cancel Session
                            </button>
                        ` : ''}
                        <button class="btn btn-view" onclick="dashboard.viewCoachDetails(${booking.id})">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            `}).join('');
        }

        async cancelGroundBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this booking?')) return;

            try {
                const response = await fetch(`/api/user/ground-bookings/${bookingId}/cancel`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ reason: 'Cancelled by user' })
                });

                const data = await response.json();
                if (data.success) {
                    alert('Booking cancelled successfully');
                    await this.loadGroundBookings();
                    this.updateStats();
                    this.renderGroundBookings();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to cancel booking');
            }
        }

        async cancelCoachBooking(bookingId) {
            if (!confirm('Are you sure you want to cancel this coaching session?')) return;

            try {
                const response = await fetch(`/api/coach-bookings/${bookingId}/cancel`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' }
                });

                const data = await response.json();
                if (data.success) {
                    alert('Session cancelled successfully');
                    await this.loadCoachBookings();
                    this.updateStats();
                    this.renderCoachBookings();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to cancel session');
            }
        }

        viewGroundDetails(bookingId) {
            const booking = this.groundBookings.find(b => b.id === bookingId);
            if (!booking) return;

            alert(`Ground Booking Details\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\nFacility: ${booking.facility_name}\nDate: ${new Date(booking.booking_date).toLocaleDateString()}\nTime: ${booking.start_time} - ${booking.end_time}\nDuration: ${booking.duration_hours} hours\nStatus: ${booking.status}\n\nBooking ID: #${booking.id}`);
        }

        viewCoachDetails(bookingId) {
            const booking = this.coachBookings.find(b => b.id === bookingId);
            if (!booking) return;

            const coachName = `${booking.coach_first_name} ${booking.coach_last_name}`;
            alert(`Coach Session Details\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\nCoach: ${coachName}\nSport: ${booking.sport_name || 'General'}\nDate: ${new Date(booking.booking_date).toLocaleDateString()}\nTime: ${booking.start_time} - ${booking.end_time}\nType: ${booking.session_type}\nFee: LKR ${parseFloat(booking.total_amount).toLocaleString()}\nStatus: ${booking.status}\n\nBooking ID: #${booking.id}`);
        }
    }

    function switchTab(tab) {
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.getElementById(tab + 'Tab').classList.add('active');

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(tab + 'Content').classList.add('active');
    }

    // Initialize dashboard
    const dashboard = new MyBookingsDashboard();
</script>
