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

    // Initialize earnings chart
    initializeEarningsChart();

    // Initialize time filter
    initializeTimeFilter();

    // Initialize notifications
    initializeNotifications();

    // Initialize real-time updates
    startRealTimeUpdates();
}

function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    sidebar.classList.toggle('open');
}

function initializeEarningsChart() {
    const canvas = document.getElementById('earningsChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    
    // Sample earnings data for the last 30 days
    const earningsData = {
        labels: generateDateLabels(30),
        datasets: [{
            label: 'Daily Earnings (₹)',
            data: generateEarningsData(30),
            borderColor: '#059669',
            backgroundColor: 'rgba(5, 150, 105, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#059669',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    };

    drawEarningsChart(ctx, earningsData);
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

function updateDashboardStats() {
    // Simulate small changes in stats
    const statNumbers = document.querySelectorAll('.stat-number');
    
    statNumbers.forEach(stat => {
        const currentText = stat.textContent;
        
        if (currentText.includes('₹')) {
            // Update earnings
            const currentValue = parseInt(currentText.replace(/[^0-9]/g, ''));
            const change = Math.floor(Math.random() * 1000) - 500;
            const newValue = Math.max(0, currentValue + change);
            stat.textContent = `₹${newValue.toLocaleString()}`;
        } else if (currentText.includes('%')) {
            // Update percentages
            const currentValue = parseFloat(currentText.replace('%', ''));
            const change = (Math.random() - 0.5) * 2; // ±1%
            const newValue = Math.max(0, Math.min(100, currentValue + change));
            stat.textContent = `${newValue.toFixed(1)}%`;
        } else if (!currentText.includes('.')) {
            // Update counts
            const currentValue = parseInt(currentText.replace(/[^0-9]/g, ''));
            const change = Math.floor(Math.random() * 3) - 1; // ±1
            const newValue = Math.max(0, currentValue + change);
            stat.textContent = newValue.toString();
        }
    });
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
    
    // Redraw chart on resize
    setTimeout(() => {
        const filter = document.getElementById('earningsFilter');
        if (filter) {
            const days = parseInt(filter.value);
            updateEarningsChart(days);
        }
    }, 100);
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