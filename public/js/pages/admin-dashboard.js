document.addEventListener('DOMContentLoaded', function() {
    initializeAdminDashboard();
});

function initializeAdminDashboard() {
    // Initialize sidebar toggle
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.getElementById('adminSidebar');
    
    sidebarToggle.forEach(button => {
        button.addEventListener('click', toggleSidebar);
    });

    // Load all dashboard data
    loadDashboardStats();
    loadRevenueChart();
    loadRecentRegistrations();
    loadRecentContent();
    loadNotifications();
    loadAdminProfile();

    // Initialize time filter for revenue chart
    const timeFilter = document.querySelector('.time-filter');
    if (timeFilter) {
        timeFilter.addEventListener('change', function() {
            loadRevenueChart(this.value);
        });
    }

    // Set up auto-refresh every 30 seconds
    setInterval(() => {
        loadDashboardStats();
        loadNotifications();
    }, 30000);
}

function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    sidebar.classList.toggle('open');
}

/**
 * Load Dashboard Statistics
 */
async function loadDashboardStats() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/admin/api/stats');
        const data = await response.json();
        
        if (data.success && data.stats) {
            updateStatCard('users', data.stats.users);
            updateStatCard('revenue', data.stats.revenue);
            updateStatCard('grounds', data.stats.grounds);
            updateStatCard('coaches', data.stats.coaches);
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
        showToast('Failed to load statistics', 'error');
    }
}

function updateStatCard(type, stats) {
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        const title = card.querySelector('h3')?.textContent.toLowerCase();
        
        if (type === 'users' && title.includes('users')) {
            updateCardContent(card, stats.total, stats.label, stats.growth);
        } else if (type === 'revenue' && title.includes('revenue')) {
            updateCardContent(card, `Rs.${formatNumber(stats.total)}`, stats.label, stats.growth);
        } else if (type === 'grounds' && title.includes('grounds')) {
            updateCardContent(card, stats.total, stats.label, 0);
        } else if (type === 'coaches' && title.includes('coaches')) {
            updateCardContent(card, stats.total, stats.label, 0);
        }
    });
}

function updateCardContent(card, number, label, growth) {
    const statNumber = card.querySelector('.stat-number');
    const statChange = card.querySelector('.stat-change span');
    const changeIcon = card.querySelector('.stat-change i');
    const changeDiv = card.querySelector('.stat-change');
    
    if (statNumber) {
        // Animate number change
        animateValue(statNumber, number);
    }
    
    if (statChange && label) {
        statChange.textContent = label;
    }
    
    if (changeDiv && growth !== undefined) {
        if (growth >= 0) {
            changeDiv.classList.remove('negative');
            changeDiv.classList.add('positive');
            if (changeIcon) changeIcon.className = 'fas fa-arrow-up';
        } else {
            changeDiv.classList.remove('positive');
            changeDiv.classList.add('negative');
            if (changeIcon) changeIcon.className = 'fas fa-arrow-down';
        }
    }
}

function animateValue(element, newValue) {
    const currentText = element.textContent;
    const hasRupee = currentText.includes('Rs.');
    const currentValue = parseInt(currentText.replace(/[^0-9]/g, '')) || 0;
    const targetValue = typeof newValue === 'string' ? 
        parseInt(newValue.replace(/[^0-9]/g, '')) : newValue;
    
    if (currentValue === targetValue) return;
    
    const duration = 500;
    const steps = 20;
    const increment = (targetValue - currentValue) / steps;
    let current = currentValue;
    let step = 0;
    
    const timer = setInterval(() => {
        step++;
        current += increment;
        
        if (step >= steps) {
            clearInterval(timer);
            current = targetValue;
        }
        
        if (hasRupee) {
            element.textContent = `Rs.${formatNumber(Math.round(current))}`;
        } else {
            element.textContent = formatNumber(Math.round(current));
        }
    }, duration / steps);
}

/**
 * Load Revenue Chart
 */
async function loadRevenueChart(period = '7') {
    try {
        const response = await fetch(`${window.BASE_URL||""}/admin/api/revenue-chart?period=${period}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            drawRevenueChart(result.data);
        }
    } catch (error) {
        console.error('Error loading revenue chart:', error);
        const canvas = document.getElementById('revenueChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#ef4444';
            ctx.font = '14px Inter';
            ctx.textAlign = 'center';
            ctx.fillText('Failed to load revenue data', canvas.width / 2, canvas.height / 2);
        }
    }
}

function drawRevenueChart(data) {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;
    
    // Scale canvas for crisp rendering
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    
    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);
    
    const width = rect.width;
    const height = rect.height;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    if (!data || data.length === 0) {
        ctx.fillStyle = '#64748b';
        ctx.font = '14px Inter';
        ctx.textAlign = 'center';
        ctx.fillText('No data available', width / 2, height / 2);
        return;
    }
    
    const values = data.map(d => d.revenue);
    const labels = data.map(d => d.label);
    const max = Math.max(...values, 100);
    const min = Math.min(...values, 0);
    const range = max - min || 1;
    
    // Margins
    const marginTop = 40;
    const marginBottom = 50;
    const marginLeft = 60;
    const marginRight = 30;
    
    const chartWidth = width - marginLeft - marginRight;
    const chartHeight = height - marginTop - marginBottom;
    
    // Draw gradient background
    const gradient = ctx.createLinearGradient(0, marginTop, 0, height - marginBottom);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.01)');
    
    // Draw the line path first for fill
    ctx.beginPath();
    const step = chartWidth / (values.length - 1 || 1);
    
    values.forEach((value, index) => {
        const x = marginLeft + (index * step);
        const y = marginTop + chartHeight - ((value - min) / range * chartHeight);
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    // Close path for fill
    ctx.lineTo(marginLeft + chartWidth, height - marginBottom);
    ctx.lineTo(marginLeft, height - marginBottom);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();
    
    // Draw the line
    ctx.beginPath();
    ctx.strokeStyle = '#3b82f6';
    ctx.lineWidth = 3;
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    
    values.forEach((value, index) => {
        const x = marginLeft + (index * step);
        const y = marginTop + chartHeight - ((value - min) / range * chartHeight);
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw data points and values
    ctx.fillStyle = '#3b82f6';
    ctx.font = 'bold 11px Inter';
    ctx.textAlign = 'center';
    
    values.forEach((value, index) => {
        const x = marginLeft + (index * step);
        const y = marginTop + chartHeight - ((value - min) / range * chartHeight);
        
        // Draw point
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fill();
        
        // Draw value above point
        ctx.fillStyle = '#0f172a';
        const formattedValue = value >= 1000 ? 'Rs.' + (value / 1000).toFixed(1) + 'k' : 'Rs.' + value.toFixed(0);
        ctx.fillText(formattedValue, x, y - 12);
        ctx.fillStyle = '#3b82f6';
    });
    
    // Draw labels
    ctx.fillStyle = '#64748b';
    ctx.font = '12px Inter';
    ctx.textAlign = 'center';
    
    labels.forEach((label, index) => {
        const x = marginLeft + (index * step);
        ctx.fillText(label, x, height - marginBottom + 20);
    });
    
    // Draw Y-axis labels
    ctx.textAlign = 'right';
    ctx.fillStyle = '#64748b';
    const ySteps = 5;
    for (let i = 0; i <= ySteps; i++) {
        const value = min + (range / ySteps) * i;
        const y = height - marginBottom - (chartHeight / ySteps) * i;
        const label = value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value.toFixed(0);
        ctx.fillText(label, marginLeft - 10, y + 4);
        
        // Draw grid line
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(marginLeft, y);
        ctx.lineTo(width - marginRight, y);
        ctx.stroke();
    }
}

/**
 * Load Recent Registrations
 */
async function loadRecentRegistrations() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/admin/api/recent-registrations?limit=5');
        const result = await response.json();
        
        if (result.success && result.data) {
            displayRecentRegistrations(result.data);
        }
    } catch (error) {
        console.error('Error loading recent registrations:', error);
        const container = document.querySelector('.booking-list');
        if (container) container.innerHTML = '<div class="empty-state" style="color:#ef4444">Failed to load registrations</div>';
    }
}

function displayRecentRegistrations(registrations) {
    const container = document.querySelector('.booking-list');
    if (!container) return;
    
    if (registrations.length === 0) {
        container.innerHTML = '<div class="empty-state">No recent registrations</div>';
        return;
    }
    
    container.innerHTML = registrations.map(reg => `
        <div class="booking-item">
            <div class="booking-info">
                <h4>${escapeHtml(reg.name)}</h4>
                <p>${escapeHtml(reg.email)}</p>
                <span class="booking-time">${escapeHtml(reg.timeAgo)}</span>
            </div>
            <span class="status-badge ${reg.typeClass}">${escapeHtml(reg.type)}</span>
        </div>
    `).join('');
}

/**
 * Load Recent Content
 */
async function loadRecentContent() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/admin/api/recent-content?limit=5');
        const result = await response.json();
        
        if (result.success && result.data) {
            displayRecentContent(result.data);
        }
    } catch (error) {
        console.error('Error loading recent content:', error);
        const container = document.querySelector('.order-list');
        if (container) container.innerHTML = '<div class="empty-state" style="color:#ef4444">Failed to load content</div>';
    }
}

function displayRecentContent(content) {
    const container = document.querySelector('.order-list');
    if (!container) return;
    
    if (content.length === 0) {
        container.innerHTML = '<div class="empty-state">No recent content</div>';
        return;
    }
    
    container.innerHTML = content.map(item => `
        <div class="order-item">
            <div class="order-info">
                <h4><i class="fas ${escapeHtml(item.icon)}"></i> ${escapeHtml(item.name)}</h4>
                <p>${escapeHtml(item.type)}</p>
                <span class="order-amount">${escapeHtml(item.timeAgo)}</span>
            </div>
            <span class="status-badge ${item.typeClass}">${escapeHtml(item.type)}</span>
        </div>
    `).join('');
}

/**
 * Load Notifications
 */
async function loadNotifications() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/admin/api/notifications');
        const result = await response.json();
        
        if (result.success) {
            updateNotificationBadge(result.count);
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-count');
    if (badge) {
        badge.textContent = count;
        if (count === 0) {
            badge.style.display = 'none';
        } else {
            badge.style.display = 'flex';
        }
    }
}

/**
 * Load Admin Profile
 */
async function loadAdminProfile() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/admin/api/profile');
        const result = await response.json();
        
        if (result.success && result.profile) {
            updateAdminProfile(result.profile);
        }
    } catch (error) {
        console.error('Error loading admin profile:', error);
    }
}

function updateAdminProfile(profile) {
    const nameElement = document.querySelector('.profile-name');
    const roleElement = document.querySelector('.profile-role');
    const avatarElement = document.querySelector('.profile-avatar');
    
    if (nameElement) nameElement.textContent = profile.name;
    if (roleElement) roleElement.textContent = profile.role;
    if (avatarElement) avatarElement.src = profile.avatar;
}

/**
 * Initialize Notification Click Handler
 */
function initializeNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    if (!notificationBtn) return;

    notificationBtn.addEventListener('click', function() {
        showToast('View all notifications in the notifications page', 'info');
    });
}

/**
 * Utility Functions
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            pointer-events: none;
        `;
        document.body.appendChild(toastContainer);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        min-width: 300px;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: auto;
        position: relative;
        border-left: 4px solid ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
    `;

    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    const color = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';

    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-${icon}" style="color: ${color}; font-size: 1.25rem;"></i>
            <span style="color: #0f172a; font-weight: 500;">${escapeHtml(message)}</span>
        </div>
    `;

    toastContainer.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 100);

    // Remove after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// Handle window resize
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('adminSidebar');
    if (window.innerWidth > 768) {
        sidebar.classList.remove('open');
    }
    
    // Redraw chart on resize
    const canvas = document.getElementById('revenueChart');
    if (canvas) {
        const timeFilter = document.querySelector('.time-filter');
        const period = timeFilter ? timeFilter.value : '7';
        loadRevenueChart(period);
    }
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    
    if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('open')) {
        let clickedToggle = false;
        sidebarToggle.forEach(toggle => {
            if (toggle.contains(e.target)) {
                clickedToggle = true;
            }
        });
        
        if (!sidebar.contains(e.target) && !clickedToggle) {
            sidebar.classList.remove('open');
        }
    }
});

// ==============================
// Notification Bell & Dropdown
// ==============================

function loadNotifications() {
    const base = window.BASE_URL || '';
    fetch(base + '/admin/api/notifications')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const countEl = document.getElementById('notifCount');
            if (countEl) {
                if (data.unread_count > 0) {
                    countEl.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    countEl.style.display = 'flex';
                } else {
                    countEl.style.display = 'none';
                }
            }
            renderNotifList(data.notifications || []);
        })
        .catch(err => console.error('Notifications load error:', err));
}

function renderNotifList(items) {
    const list = document.getElementById('notifList');
    if (!list) return;
    if (!items.length) {
        list.innerHTML = '<div style="padding:32px 20px;text-align:center;color:#9ca3af;font-size:14px;"><i class="fas fa-bell-slash" style="font-size:28px;margin-bottom:8px;display:block;"></i>No notifications</div>';
        return;
    }
    list.innerHTML = items.map(n => {
        const isUnread = !parseInt(n.is_read);
        const iconMap = {
            'system': 'fa-cog',
            'booking_confirmation': 'fa-calendar-check',
            'payment_success': 'fa-credit-card',
            'order_status': 'fa-box',
            'review_request': 'fa-star',
            'promotional': 'fa-bullhorn'
        };
        const icon = iconMap[n.type] || 'fa-bell';
        const ago = timeAgo(n.created_at);
        return `<div style="padding:12px 20px;border-bottom:1px solid #f3f4f6;background:${isUnread ? '#f0f7ff' : '#fff'};cursor:pointer;transition:background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='${isUnread ? '#f0f7ff' : '#fff'}'">
            <div style="display:flex;gap:12px;align-items:flex-start;">
                <div style="width:36px;height:36px;border-radius:50%;background:${isUnread ? '#3b82f6' : '#e5e7eb'};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas ${icon}" style="color:${isUnread ? '#fff' : '#6b7280'};font-size:14px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:${isUnread ? '600' : '400'};color:#1f2937;margin-bottom:2px;">${escapeNotifHtml(n.title)}</div>
                    <div style="font-size:12px;color:#6b7280;line-height:1.4;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">${escapeNotifHtml(n.message)}</div>
                    <div style="font-size:11px;color:#9ca3af;margin-top:4px;">${ago}</div>
                </div>
                ${isUnread ? '<div style="width:8px;height:8px;border-radius:50%;background:#3b82f6;flex-shrink:0;margin-top:6px;"></div>' : ''}
            </div>
        </div>`;
    }).join('');
}

function escapeNotifHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

function timeAgo(dateStr) {
    const now = new Date();
    const d = new Date(dateStr);
    const secs = Math.floor((now - d) / 1000);
    if (secs < 60) return 'Just now';
    if (secs < 3600) return Math.floor(secs / 60) + 'm ago';
    if (secs < 86400) return Math.floor(secs / 3600) + 'h ago';
    if (secs < 604800) return Math.floor(secs / 86400) + 'd ago';
    return d.toLocaleDateString();
}

// Toggle dropdown
document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notifBellBtn');
    const dropdown = document.getElementById('notifDropdown');
    const markAllBtn = document.getElementById('markAllReadBtn');

    if (bell && dropdown) {
        bell.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdown.style.display !== 'none';
            dropdown.style.display = isOpen ? 'none' : 'block';
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== bell && !bell.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            const base = window.BASE_URL || '';
            fetch(base + '/admin/api/notifications/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            })
            .then(r => r.json())
            .then(() => loadNotifications())
            .catch(err => console.error('Mark read error:', err));
        });
    }

    // Auto-refresh notifications every 30s
    setInterval(loadNotifications, 30000);
});