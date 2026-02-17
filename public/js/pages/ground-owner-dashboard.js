document.addEventListener('DOMContentLoaded', function() {
    initializeGroundOwnerDashboard();
});

function initializeGroundOwnerDashboard() {
    // Initialize sidebar toggle
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    const sidebar = document.getElementById('dashboardSidebar');

    sidebarToggle.forEach(button => {
        button.addEventListener('click', toggleSidebar);
    });

    // Load dashboard data from API
    updateDashboardStats();

    // Initialize earnings chart
    initializeEarningsChart();

    // Initialize notifications
    initializeNotifications();

    // Initialize real-time updates
    startRealTimeUpdates();
}

function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    sidebar.classList.toggle('open');
}

async function initializeEarningsChart() {
    const canvas = document.getElementById('earningsChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // Load real earnings data from API
    await loadRealEarningsData(ctx);
}

async function loadRealEarningsData(ctx) {
    try {
        const response = await fetch('/api/ground-owner/earnings/trends?days=30');
        const data = await response.json();

        let chartData;
        let chartLabels;

        if (data.success !== false && data.trends) {
            // Use real data from API
            chartLabels = data.trends.labels || [];
            chartData = data.trends.revenue || [];
        } else {
            // Fallback to sample data if API fails
            chartLabels = generateDateLabels(30);
            chartData = generateEarningsData(30);
        }

        // Create professional Chart.js chart
        if (window.earningsChartInstance) {
            window.earningsChartInstance.destroy();
        }

        window.earningsChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Daily Earnings',
                    data: chartData,
                    borderColor: '#10b981',
                    backgroundColor: function(context) {
                        const ctx = context.chart.ctx;
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                        gradient.addColorStop(0.5, 'rgba(16, 185, 129, 0.15)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');
                        return gradient;
                    },
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#059669',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            boxHeight: 12,
                            padding: 15,
                            font: {
                                size: 13,
                                weight: '500',
                                family: "'Inter', 'Segoe UI', sans-serif"
                            },
                            color: '#374151',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#e5e7eb',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        boxWidth: 8,
                        boxHeight: 8,
                        boxPadding: 6,
                        titleFont: {
                            size: 13,
                            weight: '600',
                            family: "'Inter', 'Segoe UI', sans-serif"
                        },
                        bodyFont: {
                            size: 14,
                            weight: '500',
                            family: "'Inter', 'Segoe UI', sans-serif"
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'LKR ' + context.parsed.y.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                                return label;
                            },
                            title: function(context) {
                                return context[0].label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 0,
                            autoSkipPadding: 20
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)',
                            drawBorder: false,
                            lineWidth: 1
                        },
                        border: {
                            display: false,
                            dash: [5, 5]
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            padding: 10,
                            callback: function(value) {
                                return 'LKR ' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    }
                }
            }
        });

    } catch (error) {
        console.error('Error loading real earnings data:', error);
        // Show error state
        ctx.canvas.parentElement.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af;">Failed to load earnings data</div>';
    }
}

function generateDateLabels(days) {
    const labels = [];
    const today = new Date();
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date(today);
        date.setDate(today.getDate() - i);
        labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
    }
    
    return labels;
}

function generateEarningsData(days) {
    const data = [];
    const baseEarning = 1000;
    
    for (let i = 0; i < days; i++) {
        // Generate realistic earnings with some variation
        const variation = Math.random() * 1000 - 500;
        const dayOfWeek = (new Date().getDay() - i + 7) % 7;
        
        // Higher earnings on weekends
        const weekendBonus = (dayOfWeek === 0 || dayOfWeek === 6) ? 500 : 0;
        
        const earning = Math.max(0, baseEarning + variation + weekendBonus);
        data.push(Math.round(earning));
    }
    
    return data;
}

function drawEarningsChart(ctx, data) {
    const canvas = ctx.canvas;
    const width = canvas.width;
    const height = canvas.height;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    const values = data.datasets[0].data;
    const labels = data.labels;
    const max = Math.max(...values);
    const min = Math.min(...values);
    const range = max - min || 1;
    
    // Chart dimensions
    const chartArea = {
        left: 40,
        right: width - 20,
        top: 20,
        bottom: height - 40
    };
    
    const chartWidth = chartArea.right - chartArea.left;
    const chartHeight = chartArea.bottom - chartArea.top;
    
    // Draw gradient background
    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.1)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0.02)');
    
    // Draw area fill
    ctx.beginPath();
    ctx.fillStyle = gradient;
    
    const step = chartWidth / (values.length - 1);
    
    values.forEach((value, index) => {
        const x = chartArea.left + (index * step);
        const y = chartArea.bottom - ((value - min) / range) * chartHeight;
        
        if (index === 0) {
            ctx.moveTo(x, chartArea.bottom);
            ctx.lineTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.lineTo(chartArea.right, chartArea.bottom);
    ctx.closePath();
    ctx.fill();
    
    // Draw line
    ctx.beginPath();
    ctx.strokeStyle = '#059669';
    ctx.lineWidth = 3;
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    
    values.forEach((value, index) => {
        const x = chartArea.left + (index * step);
        const y = chartArea.bottom - ((value - min) / range) * chartHeight;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw data points
    ctx.fillStyle = '#059669';
    values.forEach((value, index) => {
        const x = chartArea.left + (index * step);
        const y = chartArea.bottom - ((value - min) / range) * chartHeight;
        
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fillStyle = '#ffffff';
        ctx.fill();
        ctx.beginPath();
        ctx.arc(x, y, 3, 0, 2 * Math.PI);
        ctx.fillStyle = '#059669';
        ctx.fill();
    });
    
    // Draw Y-axis labels
    ctx.fillStyle = '#64748b';
    ctx.font = '11px Inter';
    ctx.textAlign = 'right';
    ctx.textBaseline = 'middle';
    
    const ySteps = 4;
    for (let i = 0; i <= ySteps; i++) {
        const value = min + (range * i / ySteps);
        const y = chartArea.bottom - (i / ySteps) * chartHeight;
        
        ctx.fillText('₹' + Math.round(value), chartArea.left - 5, y);
        
        // Draw grid lines
        if (i > 0) {
            ctx.beginPath();
            ctx.strokeStyle = '#f1f5f9';
            ctx.lineWidth = 1;
            ctx.moveTo(chartArea.left, y);
            ctx.lineTo(chartArea.right, y);
            ctx.stroke();
        }
    }
    
    // Draw X-axis labels (show every 3rd label to avoid crowding)
    ctx.fillStyle = '#64748b';
    ctx.font = '11px Inter';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'top';
    
    labels.forEach((label, index) => {
        if (index % 3 === 0 || index === labels.length - 1) {
            const x = chartArea.left + (index * step);
            ctx.fillText(label, x, chartArea.bottom + 5);
        }
    });
}

function initializeTimeFilter() {
    const filter = document.getElementById('earningsFilter');
    if (!filter) return;
    
    filter.addEventListener('change', function() {
        const days = parseInt(this.value);
        updateEarningsChart(days);
    });
}

function updateEarningsChart(days) {
    const canvas = document.getElementById('earningsChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    const earningsData = {
        labels: generateDateLabels(days),
        datasets: [{
            label: 'Daily Earnings (₹)',
            data: generateEarningsData(days),
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    };
    
    drawEarningsChart(ctx, earningsData);
}

function initializeNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    if (!notificationBtn) return;

    notificationBtn.addEventListener('click', function() {
        showNotificationsPanel();
    });
}

function showNotificationsPanel() {
    const notifications = [
        {
            title: 'New Booking Request',
            message: 'Kavinda Ranasighe wants to book Football Ground A for tomorrow',
            time: '5 minutes ago',
            type: 'booking',
            unread: true
        },
        {
            title: 'Payment Received',
            message: 'Payment of ₹1,800 received from Sanduni Rajapakse',
            time: '1 hour ago',
            type: 'payment',
            unread: true
        },
        {
            title: 'New Review',
            message: 'Dilan Wijesinghe left a 5-star review for Tennis Court 1',
            time: '2 hours ago',
            type: 'review',
            unread: false
        }
    ];
    
    showToast('You have ' + notifications.filter(n => n.unread).length + ' new notifications', 'info');
}

function startRealTimeUpdates() {
    // Update stats every 60 seconds
    setInterval(() => {
        updateDashboardStats();
    }, 60000);
    
    // Update booking status every 30 seconds
    setInterval(() => {
        updateBookingStatuses();
    }, 30000);
}

async function updateDashboardStats() {
    try {
        const response = await fetch('/api/ground-owner/dashboard-stats');

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        const data = await response.json();

        if (data.success) {
            const { stats, recent_bookings, ground_performance } = data;

            // Update earnings stats
            if (stats.earnings) {
                updateStatIfExists('#totalEarnings', `LKR ${stats.earnings.total_earnings.toLocaleString()}`);
                updateStatIfExists('#earningsChange', `${stats.earnings.earnings_change}% this month`);
            }

            // Update booking stats
            if (stats.bookings) {
                updateStatIfExists('#totalBookings', stats.bookings.total_bookings || 0);
                updateStatIfExists('#bookingsChange', `${stats.bookings.this_month_bookings || 0} this month`);
            }

            // Update occupancy stats
            if (stats.occupancy) {
                updateStatIfExists('#occupancyRate', `${stats.occupancy.rate}%`);
                updateStatIfExists('#occupancyChange', `${stats.occupancy.change}% this month`);
            }

            // Update rating stats
            if (stats.rating) {
                updateStatIfExists('#averageRating', stats.rating.average.toFixed(1));
                updateStatIfExists('#ratingInfo', `Based on ${stats.rating.total_reviews} reviews`);
            }

            // Update recent bookings
            if (recent_bookings && recent_bookings.length > 0) {
                updateRecentBookings(recent_bookings);
            }

            // Update ground performance
            if (ground_performance && ground_performance.length > 0) {
                updateGroundPerformance(ground_performance);
            }
        }
    } catch (error) {
        console.error('Error updating dashboard stats:', error);
    }
}

function updateStatIfExists(selector, value) {
    const element = document.querySelector(selector);
    if (element) {
        element.textContent = value;
    }
}

function updateRecentBookings(bookings) {
    const container = document.querySelector('.booking-list');
    if (!container) return;

    const html = bookings.slice(0, 3).map(booking => `
        <div class="booking-item">
            <div class="booking-info">
                <h4>${booking.facility_name || 'Facility'}</h4>
                <p>${booking.first_name || ''} ${booking.last_name || ''}</p>
                <span class="booking-details">${formatBookingDate(booking.booking_date)}, ${booking.start_time} - ${booking.end_time}</span>
                <span class="booking-amount">LKR ${parseFloat(booking.total_amount || 0).toLocaleString()}</span>
            </div>
            <span class="status-badge ${booking.status}">${booking.status}</span>
        </div>
    `).join('');

    container.innerHTML = html;
}

function formatBookingDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === tomorrow.toDateString()) {
        return 'Tomorrow';
    } else {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }
}

function updateGroundPerformance(grounds) {
    const container = document.querySelector('.performance-list');
    if (!container) return;

    // Take top 3 grounds by revenue
    const topGrounds = grounds.slice(0, 3);
    const maxRevenue = Math.max(...topGrounds.map(g => parseFloat(g.total_revenue)));

    const html = topGrounds.map(ground => {
        const revenue = parseFloat(ground.total_revenue);
        const percentage = maxRevenue > 0 ? Math.round((revenue / maxRevenue) * 100) : 0;

        return `
            <div class="performance-item">
                <div class="ground-info">
                    <h4>${ground.name}</h4>
                    <div class="performance-stats">
                        <span class="bookings">${ground.total_bookings} bookings</span>
                        <span class="earnings">LKR ${revenue.toLocaleString()}</span>
                    </div>
                </div>
                <div class="performance-chart">
                    <div class="chart-bar" style="width: ${percentage}%"></div>
                    <span class="performance-rate">${percentage}%</span>
                </div>
            </div>
        `;
    }).join('');

    container.innerHTML = html;
}

function updateBookingStatuses() {
    // Simulate booking status changes
    const bookingItems = document.querySelectorAll('.booking-item');
    
    bookingItems.forEach((item, index) => {
        const statusBadge = item.querySelector('.status-badge');
        if (statusBadge && statusBadge.textContent === 'Pending' && Math.random() > 0.8) {
            statusBadge.textContent = 'Confirmed';
            statusBadge.className = 'status-badge confirmed';
            
            // Show notification
            setTimeout(() => {
                showToast('Booking confirmed!', 'success');
            }, 1000);
        }
    });
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
        border-left: 4px solid ${type === 'success' ? '#059669' : type === 'error' ? '#ef4444' : '#3b82f6'};
    `;

    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    const color = type === 'success' ? '#059669' : type === 'error' ? '#ef4444' : '#3b82f6';

    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-${icon}" style="color: ${color}; font-size: 1.25rem;"></i>
            <span style="color: #0f172a; font-weight: 500;">${message}</span>
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
    const sidebar = document.getElementById('dashboardSidebar');
    if (window.innerWidth > 768) {
        sidebar.classList.remove('open');
    }
});

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('dashboardSidebar');
    const sidebarToggle = document.querySelectorAll('.sidebar-toggle');
    
    if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
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