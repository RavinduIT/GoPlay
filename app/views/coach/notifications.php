<?php $currentPage = 'notifications'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - GoPlay Coach</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #0891b2;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --background-light: #f9fafb;
            --background-white: #ffffff;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --transition: all 0.2s ease-in-out;
        }

        .main-content {
            padding: 2rem;
            background: var(--background-light);
            min-height: 100vh;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: white;
            color: var(--primary-color);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .notifications-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .notifications-filters {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-tabs {
            display: flex;
            gap: 0.5rem;
        }

        .filter-tab {
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            background: white;
            border-radius: 2rem;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .filter-tab:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-tab.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .filter-actions {
            display: flex;
            gap: 0.75rem;
        }

        .notification-list {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .notification-item {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            gap: 1rem;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item:hover {
            background: var(--background-light);
        }

        .notification-item.unread {
            background: #eff6ff;
        }

        .notification-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--primary-color);
        }

        .notification-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .notification-icon.booking {
            background: #dbeafe;
            color: var(--primary-color);
        }

        .notification-icon.payment {
            background: #d1fae5;
            color: var(--success-color);
        }

        .notification-icon.cancellation {
            background: #fee2e2;
            color: var(--danger-color);
        }

        .notification-icon.review {
            background: #fef3c7;
            color: var(--warning-color);
        }

        .notification-icon.system {
            background: #e0f2fe;
            color: var(--info-color);
        }

        .notification-content {
            flex: 1;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .notification-title {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
            margin: 0;
        }

        .notification-time {
            font-size: 0.85rem;
            color: var(--text-light);
            white-space: nowrap;
        }

        .notification-message {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 0.75rem;
        }

        .notification-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 0.75rem;
        }

        .notification-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .notification-btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .notification-btn-primary:hover {
            background: var(--primary-dark);
        }

        .notification-btn-secondary {
            background: var(--background-light);
            color: var(--text-secondary);
        }

        .notification-btn-secondary:hover {
            background: var(--border-color);
        }

        .notification-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .badge-urgent {
            background: #fee2e2;
            color: var(--danger-color);
        }

        .badge-new {
            background: #dbeafe;
            color: var(--primary-color);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .notification-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.unread {
            background: #dbeafe;
            color: var(--primary-color);
        }

        .stat-icon.total {
            background: #f3e8ff;
            color: #7c3aed;
        }

        .stat-content h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }

        .stat-content p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .notifications-filters {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-tabs {
                flex-wrap: wrap;
            }

            .notification-item {
                flex-direction: column;
            }

            .notification-header {
                flex-direction: column;
                gap: 0.5rem;
            }

            .notification-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="coach-dashboard">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <div>
                    <h1>
                        <i class="fas fa-bell"></i>
                        Notifications
                    </h1>
                    <p>Stay updated with your coaching activities</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i>
                        Mark all as read
                    </button>
                    <button class="btn btn-primary" onclick="openSettings()">
                        <i class="fas fa-cog"></i>
                        Settings
                    </button>
                </div>
            </div>

            <div class="notifications-container">
                <!-- Notification Stats -->
                <div class="notification-stats">
                    <div class="stat-card">
                        <div class="stat-icon unread">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="unreadCount">5</h3>
                            <p>Unread Notifications</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="totalCount">12</h3>
                            <p>Total Notifications</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="notifications-filters">
                    <div class="filter-tabs">
                        <button class="filter-tab active" data-filter="all">
                            All
                        </button>
                        <button class="filter-tab" data-filter="unread">
                            Unread
                        </button>
                        <button class="filter-tab" data-filter="bookings">
                            Bookings
                        </button>
                        <button class="filter-tab" data-filter="payments">
                            Payments
                        </button>
                        <button class="filter-tab" data-filter="reviews">
                            Reviews
                        </button>
                    </div>
                    <div class="filter-actions">
                        <button class="btn btn-secondary" onclick="clearAll()">
                            <i class="fas fa-trash"></i>
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="notification-list" id="notificationsList">
                    <!-- New Booking -->
                    <div class="notification-item unread" data-type="bookings" data-id="1">
                        <div class="notification-icon booking">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">New Session Booked</h3>
                                <span class="notification-time">5 minutes ago</span>
                            </div>
                            <p class="notification-message">
                                <strong>Saman Perera</strong> has booked an Individual Training session for October 25, 2025 at 10:00 AM.
                            </p>
                            <span class="notification-badge badge-new">New Booking</span>
                            <div class="notification-actions">
                                <a href="/coach/sessions" class="notification-btn notification-btn-primary">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                                <button class="notification-btn notification-btn-secondary" onclick="markAsRead(1)">
                                    Mark as Read
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Received -->
                    <div class="notification-item unread" data-type="payments" data-id="2">
                        <div class="notification-icon payment">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">Payment Received</h3>
                                <span class="notification-time">1 hour ago</span>
                            </div>
                            <p class="notification-message">
                                Payment of <strong>LKR 4,800.00</strong> received for session with Nimal Silva on October 23, 2025.
                            </p>
                            <div class="notification-actions">
                                <a href="/coach/earnings" class="notification-btn notification-btn-primary">
                                    <i class="fas fa-receipt"></i>
                                    View Receipt
                                </a>
                                <button class="notification-btn notification-btn-secondary" onclick="markAsRead(2)">
                                    Mark as Read
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation -->
                    <div class="notification-item unread" data-type="bookings" data-id="3">
                        <div class="notification-icon cancellation">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">Session Cancelled</h3>
                                <span class="notification-time">3 hours ago</span>
                            </div>
                            <p class="notification-message">
                                <strong>Kasun Fernando</strong> has cancelled the Group Session scheduled for October 24, 2025 at 3:00 PM.
                            </p>
                            <span class="notification-badge badge-urgent">Cancellation</span>
                            <div class="notification-actions">
                                <a href="/coach/sessions" class="notification-btn notification-btn-primary">
                                    <i class="fas fa-info-circle"></i>
                                    View Details
                                </a>
                                <button class="notification-btn notification-btn-secondary" onclick="markAsRead(3)">
                                    Mark as Read
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- New Review -->
                    <div class="notification-item unread" data-type="reviews" data-id="4">
                        <div class="notification-icon review">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">New Review Received</h3>
                                <span class="notification-time">5 hours ago</span>
                            </div>
                            <p class="notification-message">
                                <strong>Dilini Jayawardena</strong> left a 5-star review: "Excellent coaching! Very professional and helpful."
                            </p>
                            <div class="notification-actions">
                                <a href="/coach/reviews" class="notification-btn notification-btn-primary">
                                    <i class="fas fa-comment"></i>
                                    View Review
                                </a>
                                <button class="notification-btn notification-btn-secondary" onclick="markAsRead(4)">
                                    Mark as Read
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reminder -->
                    <div class="notification-item unread" data-type="bookings" data-id="5">
                        <div class="notification-icon system">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">Session Reminder</h3>
                                <span class="notification-time">6 hours ago</span>
                            </div>
                            <p class="notification-message">
                                You have a session with <strong>Ravindu Silva</strong> tomorrow at 9:00 AM. Don't forget to prepare!
                            </p>
                            <div class="notification-actions">
                                <a href="/coach/sessions" class="notification-btn notification-btn-primary">
                                    <i class="fas fa-calendar"></i>
                                    View Schedule
                                </a>
                                <button class="notification-btn notification-btn-secondary" onclick="markAsRead(5)">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Read Notifications -->
                    <div class="notification-item" data-type="payments" data-id="6">
                        <div class="notification-icon payment">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">Payment Received</h3>
                                <span class="notification-time">1 day ago</span>
                            </div>
                            <p class="notification-message">
                                Payment of <strong>LKR 3,200.00</strong> received for session with Thilini Perera.
                            </p>
                        </div>
                    </div>

                    <div class="notification-item" data-type="reviews" data-id="7">
                        <div class="notification-icon review">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">New Review Received</h3>
                                <span class="notification-time">2 days ago</span>
                            </div>
                            <p class="notification-message">
                                <strong>Chamara Wijesinghe</strong> left a 4-star review for your coaching session.
                            </p>
                        </div>
                    </div>

                    <div class="notification-item" data-type="bookings" data-id="8">
                        <div class="notification-icon booking">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <h3 class="notification-title">Session Completed</h3>
                                <span class="notification-time">2 days ago</span>
                            </div>
                            <p class="notification-message">
                                Your session with <strong>Amal Rajapaksa</strong> has been marked as completed.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Filter notifications
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;
                const notifications = document.querySelectorAll('.notification-item');

                notifications.forEach(notification => {
                    if (filter === 'all') {
                        notification.style.display = 'flex';
                    } else if (filter === 'unread') {
                        notification.style.display = notification.classList.contains('unread') ? 'flex' : 'none';
                    } else {
                        notification.style.display = notification.dataset.type === filter ? 'flex' : 'none';
                    }
                });
            });
        });

        // Mark as read
        function markAsRead(id) {
            const notification = document.querySelector(`[data-id="${id}"]`);
            if (notification) {
                notification.classList.remove('unread');
                updateCounts();
            }
        }

        // Mark all as read
        function markAllAsRead() {
            if (confirm('Mark all notifications as read?')) {
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                });
                updateCounts();
            }
        }

        // Clear all notifications
        function clearAll() {
            if (confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
                document.getElementById('notificationsList').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <h3>No Notifications</h3>
                        <p>You're all caught up! No notifications to display.</p>
                    </div>
                `;
                updateCounts();
            }
        }

        // Update notification counts
        function updateCounts() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            const totalCount = document.querySelectorAll('.notification-item').length;
            
            document.getElementById('unreadCount').textContent = unreadCount;
            document.getElementById('totalCount').textContent = totalCount;
        }

        // Open settings
        function openSettings() {
            window.location.href = '/coach/settings';
        }

        // Click on notification to mark as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (!e.target.closest('.notification-btn')) {
                    const id = this.dataset.id;
                    markAsRead(id);
                }
            });
        });

        // Initialize counts on load
        updateCounts();
    </script>
</body>
</html>
