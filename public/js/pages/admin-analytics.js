// Admin Analytics JavaScript
let charts = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeAnalytics();
    setupEventListeners();
});

function setupEventListeners() {
    const timeRangeSelector = document.getElementById('timeRange');
    if (timeRangeSelector) {
        timeRangeSelector.addEventListener('change', function() {
            loadOverviewStats(this.value);
            loadAllCharts();
        });
    }
}

async function initializeAnalytics() {
    await loadOverviewStats(30);
    await loadAllCharts();
    await loadTopProducts();
    await loadRecentActivity();
}

// Load Overview Statistics
async function loadOverviewStats(days = 30) {
    try {
        const response = await fetch(`${window.BASE_URL||""}/api/admin/analytics/overview?range=${days}`);
        const data = await response.json();

        if (data.success) {
            const stats = data.data;

            // Update Total Users
            document.getElementById('totalUsers').textContent = stats.users.total.toLocaleString();
            updateGrowthIndicator('userGrowth', stats.users.growth);

            // Update Total Revenue
            document.getElementById('totalRevenue').textContent = `Rs.${stats.revenue.total.toLocaleString()}`;
            updateGrowthIndicator('revenueGrowth', stats.revenue.growth);

            // Update Total Bookings
            document.getElementById('totalBookings').textContent = stats.bookings.total.toLocaleString();
            updateGrowthIndicator('bookingGrowth', stats.bookings.growth);

            // Update Total Orders
            document.getElementById('totalOrders').textContent = stats.products.orders.toLocaleString();
            const orderGrowth = stats.products.orders > 0 ? 
                ((stats.products.periodOrders / stats.products.orders) * 100).toFixed(1) : 0;
            updateGrowthIndicator('orderGrowth', orderGrowth);
        }
    } catch (error) {
        console.error('Error loading overview stats:', error);
        showError('Failed to load statistics');
    }
}

function updateGrowthIndicator(elementId, growth) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const isPositive = growth >= 0;
    element.className = `stat-change ${isPositive ? 'positive' : 'negative'}`;
    
    const icon = isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
    element.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${Math.abs(growth)}% ${isPositive ? 'increase' : 'decrease'}</span>
    `;
}

// Load All Charts
async function loadAllCharts() {
    await loadRevenueChart();
    await loadUserDistributionChart();
    await loadBookingChart();
}

// Revenue Chart
async function loadRevenueChart() {
    try {
        const days = document.getElementById('timeRange').value;
        const response = await fetch(`${window.BASE_URL||""}/api/admin/analytics/revenue?days=${days}`);
        const data = await response.json();

        if (data.success && data.data.revenueOverTime) {
            const chartData = data.data.revenueOverTime;
            const labels = chartData.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
            const revenues = chartData.map(item => parseFloat(item.revenue) || 0);

            if (charts.revenue) {
                charts.revenue.destroy();
            }

            const ctx = document.getElementById('revenueChart');
            if (ctx) {
                charts.revenue = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Revenue (Rs.)',
                            data: revenues,
                            borderColor: 'rgb(0, 82, 204)',
                            backgroundColor: 'rgba(0, 82, 204, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: 'rgb(0, 82, 204)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: function(context) {
                                        return 'Revenue: Rs.' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rs.' + value.toLocaleString();
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }
    } catch (error) {
        console.error('Error loading revenue chart:', error);
    }
}

// User Distribution Chart
async function loadUserDistributionChart() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/analytics/users');
        const data = await response.json();

        if (data.success && data.data.distribution) {
            const distribution = data.data.distribution;
            const labels = distribution.map(item => formatUserType(item.user_type));
            const counts = distribution.map(item => parseInt(item.count));
            const colors = ['#0052cc', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444'];

            if (charts.userDistribution) {
                charts.userDistribution.destroy();
            }

            const ctx = document.getElementById('userDistributionChart');
            if (ctx) {
                charts.userDistribution = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: counts,
                            backgroundColor: colors,
                            borderWidth: 0,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12, weight: '600' },
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    } catch (error) {
        console.error('Error loading user distribution chart:', error);
    }
}

// Booking Chart
async function loadBookingChart() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/analytics/bookings');
        const data = await response.json();

        if (data.success) {
            const groundBookings = data.data.groundBookingsBySport || [];
            const coachBookings = data.data.coachBookingsBySport || [];

            // Combine and aggregate data
            const sportsMap = new Map();
            
            groundBookings.forEach(item => {
                sportsMap.set(item.sport, {
                    ground: parseInt(item.count),
                    coach: 0
                });
            });

            coachBookings.forEach(item => {
                if (sportsMap.has(item.sport)) {
                    sportsMap.get(item.sport).coach = parseInt(item.count);
                } else {
                    sportsMap.set(item.sport, {
                        ground: 0,
                        coach: parseInt(item.count)
                    });
                }
            });

            const labels = Array.from(sportsMap.keys());
            const groundData = labels.map(sport => sportsMap.get(sport).ground);
            const coachData = labels.map(sport => sportsMap.get(sport).coach);

            if (charts.bookings) {
                charts.bookings.destroy();
            }

            const ctx = document.getElementById('bookingChart');
            if (ctx) {
                charts.bookings = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Ground Bookings',
                                data: groundData,
                                backgroundColor: 'rgba(0, 82, 204, 0.8)',
                                borderColor: 'rgb(0, 82, 204)',
                                borderWidth: 2,
                                borderRadius: 8
                            },
                            {
                                label: 'Coach Bookings',
                                data: coachData,
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 2,
                                borderRadius: 8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: { size: 12, weight: '600' },
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y + ' bookings';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }
    } catch (error) {
        console.error('Error loading booking chart:', error);
    }
}

// Load Top Products
async function loadTopProducts() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/analytics/products');
        const data = await response.json();

        const tbody = document.querySelector('#topProductsTable tbody');
        if (!tbody) return;

        if (data.success && data.data.topProducts && data.data.topProducts.length > 0) {
            tbody.innerHTML = data.data.topProducts.slice(0, 5).map(product => `
                <tr>
                    <td><strong>${escapeHtml(product.name)}</strong></td>
                    <td>${parseInt(product.total_sold)}</td>
                    <td>Rs.${parseFloat(product.revenue).toLocaleString()}</td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="loading-cell">No product data available</td></tr>';
        }
    } catch (error) {
        console.error('Error loading top products:', error);
        const tbody = document.querySelector('#topProductsTable tbody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="3" class="loading-cell">Error loading products</td></tr>';
        }
    }
}

// Load Recent Activity
async function loadRecentActivity() {
    try {
        const response = await fetch((window.BASE_URL||'')+'/api/admin/analytics/activity?limit=10');
        const data = await response.json();

        const activityList = document.getElementById('activityList');
        if (!activityList) return;

        if (data.success && data.data && data.data.length > 0) {
            activityList.innerHTML = data.data.map(activity => {
                const icon = getActivityIcon(activity.action);
                const time = formatTimeAgo(activity.timestamp);
                return `
                    <div class="activity-item">
                        <div class="activity-icon ${icon.color}">
                            <i class="${icon.class}"></i>
                        </div>
                        <div class="activity-content">
                            <h4>${escapeHtml(activity.action)}</h4>
                            <p>${escapeHtml(activity.user)} • ${time}</p>
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            activityList.innerHTML = '<div class="loading-cell">No recent activity</div>';
        }
    } catch (error) {
        console.error('Error loading recent activity:', error);
        const activityList = document.getElementById('activityList');
        if (activityList) {
            activityList.innerHTML = '<div class="loading-cell">Error loading activity</div>';
        }
    }
}

// Refresh individual chart
async function refreshChart(type) {
    const button = event.target.closest('.icon-btn');
    if (button) {
        button.style.transform = 'rotate(360deg)';
        setTimeout(() => button.style.transform = '', 300);
    }

    switch(type) {
        case 'revenue':
            await loadRevenueChart();
            break;
        case 'users':
            await loadUserDistributionChart();
            break;
        case 'bookings':
            await loadBookingChart();
            break;
    }
}

// Refresh activity
async function refreshActivity() {
    await loadRecentActivity();
}

// Export Data
async function exportData() {
    const modal = document.createElement('div');
    modal.className = 'export-modal';
    modal.innerHTML = `
        <div class="modal-overlay" onclick="this.parentElement.remove()"></div>
        <div class="modal-content">
            <h3>Export Report</h3>
            <p>Select the report type to export:</p>
            <div class="export-options">
                <button onclick="downloadReport('users')" class="export-option">
                    <i class="fas fa-users"></i>
                    <span>Users Report</span>
                </button>
                <button onclick="downloadReport('bookings')" class="export-option">
                    <i class="fas fa-calendar"></i>
                    <span>Bookings Report</span>
                </button>
                <button onclick="downloadReport('revenue')" class="export-option">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Revenue Report</span>
                </button>
            </div>
            <button onclick="this.closest('.export-modal').remove()" class="close-btn">Close</button>
        </div>
    `;
    
    const style = document.createElement('style');
    style.textContent = `
        .export-modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; }
        .modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
        .modal-content { position: relative; background: white; padding: 2rem; border-radius: 16px; max-width: 500px; width: 90%; }
        .modal-content h3 { margin-bottom: 0.5rem; font-size: 1.5rem; color: #0f172a; }
        .modal-content p { color: #64748b; margin-bottom: 1.5rem; }
        .export-options { display: grid; gap: 1rem; margin-bottom: 1.5rem; }
        .export-option { padding: 1rem; background: linear-gradient(135deg, rgba(0,82,204,0.05), rgba(14,165,233,0.05)); border: 1.5px solid rgba(0,82,204,0.15); border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 1rem; transition: all 0.3s; }
        .export-option:hover { background: linear-gradient(135deg, #0052cc, #0ea5e9); color: white; border-color: transparent; }
        .export-option i { font-size: 1.5rem; }
        .close-btn { width: 100%; padding: 0.75rem; background: #e2e8f0; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .close-btn:hover { background: #cbd5e1; }
    `;
    document.head.appendChild(style);
    document.body.appendChild(modal);
}

// Download Report
async function downloadReport(type) {
    try {
        showNotification('Generating report...', 'info');
        
        const response = await fetch(`${window.BASE_URL||""}/api/admin/analytics/export?type=csv&report=${type}`);
        
        if (!response.ok) throw new Error('Export failed');
        
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${type}_report_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showNotification('Report downloaded successfully!', 'success');
        document.querySelector('.export-modal')?.remove();
    } catch (error) {
        console.error('Error downloading report:', error);
        showNotification('Failed to download report', 'error');
    }
}

// Generate Report
function generateReport(type) {
    downloadReport(type);
}

// Helper Functions
function formatUserType(type) {
    const types = {
        'user': 'Regular Users',
        'admin': 'Admins',
        'ground_owner': 'Ground Owners',
        'coach': 'Coaches',
        'shop_owner': 'Shop Owners'
    };
    return types[type] || type;
}

function getActivityIcon(action) {
    if (action.includes('Registration')) return { class: 'fas fa-user-plus', color: 'blue' };
    if (action.includes('Booking')) return { class: 'fas fa-calendar-check', color: 'green' };
    if (action.includes('Payment')) return { class: 'fas fa-dollar-sign', color: 'purple' };
    return { class: 'fas fa-circle', color: 'blue' };
}

function formatTimeAgo(timestamp) {
    const now = new Date();
    const then = new Date(timestamp);
    const diff = Math.floor((now - then) / 1000);

    if (diff < 60) return 'Just now';
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;
    return then.toLocaleDateString();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#0052cc'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function showError(message) {
    showNotification(message, 'error');
}

// Add animations
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(styleSheet);