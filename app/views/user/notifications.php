<!-- Notifications Page -->

<div class="notifications-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-bell"></i> Notifications</h1>
            <p class="subtitle">Stay updated with your latest activities</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-outline" onclick="markAllAsRead()">
                <i class="fas fa-check-double"></i> Mark All as Read
            </button>
            <button class="btn btn-danger-outline" onclick="clearAllNotifications()">
                <i class="fas fa-trash"></i> Clear All
            </button>
        </div>
    </div>

    <!-- Notification Filters -->
    <div class="notification-filters">
        <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">
                <i class="fas fa-list"></i> All
            </button>
            <button class="filter-tab" data-filter="unread">
                <i class="fas fa-envelope"></i> Unread
            </button>
            <button class="filter-tab" data-filter="booking_confirmation">
                <i class="fas fa-calendar-check"></i> Bookings
            </button>
            <button class="filter-tab" data-filter="payment_success">
                <i class="fas fa-credit-card"></i> Payments
            </button>
            <button class="filter-tab" data-filter="order_status">
                <i class="fas fa-shopping-bag"></i> Orders
            </button>
            <button class="filter-tab" data-filter="promotional">
                <i class="fas fa-gift"></i> Promotions
            </button>
        </div>
    </div>

    <!-- Notifications Stats -->
    <div class="notification-stats">
        <div class="stat-card">
            <div class="stat-icon" style="background: #dbeafe; color: #2563eb;">
                <i class="fas fa-bell"></i>
            </div>
            <div class="stat-info">
                <h3 id="total-notifications">0</h3>
                <p>Total Notifications</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-info">
                <h3 id="unread-notifications">0</h3>
                <p>Unread</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="read-notifications">0</h3>
                <p>Read</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #e0e7ff; color: #6366f1;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3 id="today-notifications">0</h3>
                <p>Today</p>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list" id="notifications-list">
        <!-- Loading State -->
        <div class="loading-state" id="loading-state">
            <div class="spinner"></div>
            <p>Loading notifications...</p>
        </div>

        <!-- Empty State -->
        <div class="empty-state" id="empty-state" style="display: none;">
            <i class="fas fa-bell-slash"></i>
            <h3>No Notifications</h3>
            <p>You're all caught up! We'll notify you when something new happens.</p>
        </div>

        <!-- Notifications will be loaded here dynamically -->
    </div>
</div>

<style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1e40af;
    --primary-light: #dbeafe;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --background-gray: #f9fafb;
}

.notifications-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--border-color);
}

.header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.header-content h1 i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.subtitle {
    color: var(--text-secondary);
    font-size: 1rem;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-outline {
    background: white;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.btn-outline:hover {
    background: var(--primary-light);
}

.btn-danger-outline {
    background: white;
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
}

.btn-danger-outline:hover {
    background: #fee2e2;
}

.notification-filters {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.filter-tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border: 1px solid var(--border-color);
    background: white;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-tab:hover {
    background: var(--background-gray);
}

.filter-tab.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.notification-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 3.5rem;
    height: 3.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    font-size: 1.5rem;
}

.stat-info h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stat-info p {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.notifications-list {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.notification-card {
    display: flex;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 0.75rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.notification-card:hover {
    background: var(--background-gray);
    border-color: var(--border-color);
}

.notification-card.unread {
    background: #eff6ff;
    border-color: var(--primary-light);
}

.notification-icon {
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.icon-booking { background: #dbeafe; color: #2563eb; }
.icon-payment { background: #d1fae5; color: #10b981; }
.icon-order { background: #fef3c7; color: #f59e0b; }
.icon-review { background: #e0e7ff; color: #6366f1; }
.icon-promotional { background: #fce7f3; color: #ec4899; }
.icon-system { background: #e5e7eb; color: #6b7280; }

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    font-size: 0.9375rem;
}

.notification-message {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    line-height: 1.5;
}

.notification-time {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.notification-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.unread-badge {
    width: 0.5rem;
    height: 0.5rem;
    background: var(--primary-color);
    border-radius: 50%;
}

.notification-delete {
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.notification-delete:hover {
    color: var(--danger-color);
    background: #fee2e2;
}

.loading-state, .empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
}

.spinner {
    width: 3rem;
    height: 3rem;
    border: 4px solid var(--border-color);
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }

    .header-actions {
        width: 100%;
        flex-direction: column;
    }

    .notification-stats {
        grid-template-columns: 1fr;
    }

    .filter-tabs {
        flex-direction: column;
    }

    .notification-card {
        flex-direction: column;
    }

    .notification-actions {
        flex-direction: row;
        justify-content: space-between;
        width: 100%;
    }
}
</style>

<script>
let allNotifications = [];
let currentFilter = 'all';

document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();

    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterNotifications();
        });
    });
});

async function loadNotifications() {
    try {
        const response = await fetch('/api/user/notifications');
        const data = await response.json();

        console.log('Notifications API response:', data);

        if (data.success) {
            allNotifications = data.notifications || [];
            updateStats(allNotifications);
            displayNotifications(allNotifications);
        } else {
            console.error('API returned error:', data.message);
            allNotifications = [];
            updateStats([]);
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
        allNotifications = [];
        updateStats([]);
        showEmptyState();
    }
}

function updateStats(notifications) {
    const today = new Date().setHours(0, 0, 0, 0);
    const stats = {
        total: notifications.length,
        unread: notifications.filter(n => !n.is_read).length,
        read: notifications.filter(n => n.is_read).length,
        today: notifications.filter(n => new Date(n.created_at).setHours(0, 0, 0, 0) === today).length
    };

    document.getElementById('total-notifications').textContent = stats.total;
    document.getElementById('unread-notifications').textContent = stats.unread;
    document.getElementById('read-notifications').textContent = stats.read;
    document.getElementById('today-notifications').textContent = stats.today;
}

function displayNotifications(notifications) {
    const container = document.getElementById('notifications-list');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');

    loadingState.style.display = 'none';

    if (notifications.length === 0) {
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';

    const notificationsHTML = notifications.map(notification => createNotificationCard(notification)).join('');
    const existingCards = container.querySelectorAll('.notification-card');
    existingCards.forEach(card => card.remove());

    container.insertAdjacentHTML('beforeend', notificationsHTML);
}

function createNotificationCard(notification) {
    const iconClass = `icon-${notification.type.replace('_', '-')}`;
    const iconMap = {
        'booking_confirmation': 'fa-calendar-check',
        'payment_success': 'fa-credit-card',
        'order_status': 'fa-shopping-bag',
        'review_request': 'fa-star',
        'promotional': 'fa-gift',
        'system': 'fa-info-circle'
    };

    return `
        <div class="notification-card ${!notification.is_read ? 'unread' : ''}"
             onclick="markAsRead(${notification.id})">
            <div class="notification-icon ${iconClass}">
                <i class="fas ${iconMap[notification.type] || 'fa-bell'}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${notification.title}</div>
                <div class="notification-message">${notification.message}</div>
                <div class="notification-time">${formatTimeAgo(notification.created_at)}</div>
            </div>
            <div class="notification-actions">
                ${!notification.is_read ? '<div class="unread-badge"></div>' : ''}
                <button class="notification-delete" onclick="deleteNotification(event, ${notification.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
}

function filterNotifications() {
    let filtered = allNotifications;

    if (currentFilter === 'unread') {
        filtered = filtered.filter(n => !n.is_read);
    } else if (currentFilter !== 'all') {
        filtered = filtered.filter(n => n.type === currentFilter);
    }

    displayNotifications(filtered);
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)} minutes ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)} hours ago`;
    if (seconds < 604800) return `${Math.floor(seconds / 86400)} days ago`;

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
    });
}

async function markAsRead(notificationId) {
    try {
        const response = await fetch(`/api/user/notifications/${notificationId}/read`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const notification = allNotifications.find(n => n.id === notificationId);
            if (notification) {
                notification.is_read = true;
                updateStats(allNotifications);
                filterNotifications();
            }
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch('/api/user/notifications/mark-all-read', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            allNotifications.forEach(n => n.is_read = true);
            updateStats(allNotifications);
            filterNotifications();
        }
    } catch (error) {
        console.error('Error marking all as read:', error);
        // Fallback for demonstration
        allNotifications.forEach(n => n.is_read = true);
        updateStats(allNotifications);
        filterNotifications();
    }
}

async function deleteNotification(event, notificationId) {
    event.stopPropagation();

    if (!confirm('Delete this notification?')) return;

    try {
        const response = await fetch(`/api/user/notifications/${notificationId}`, {
            method: 'DELETE'
        });

        if (response.ok) {
            allNotifications = allNotifications.filter(n => n.id !== notificationId);
            updateStats(allNotifications);
            filterNotifications();
        }
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
}

async function clearAllNotifications() {
    if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) return;

    try {
        const response = await fetch('/api/user/notifications/clear-all', {
            method: 'DELETE'
        });

        if (response.ok) {
            allNotifications = [];
            updateStats(allNotifications);
            showEmptyState();
        }
    } catch (error) {
        console.error('Error clearing notifications:', error);
    }
}

function showEmptyState() {
    document.getElementById('loading-state').style.display = 'none';
    document.getElementById('empty-state').style.display = 'block';
}
</script>
