/**
 * Shop Owner Dashboard JavaScript
 * Handles dashboard interactions and API calls
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

function initializeDashboard() {
    loadDashboardStats();
    initializeCharts();
    setupEventListeners();
}

async function loadDashboardStats() {
    try {
        const response = await fetch('/api/shop-owner/dashboard');
        const data = await response.json();
        
        if (data.success) {
            updateStatsCards(data.stats);
        } else {
            console.error('Failed to load dashboard stats:', data.message);
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

function updateStatsCards(stats) {
    // Update total products
    const productCountElement = document.querySelector('.stat-card:nth-child(3) .stat-number');
    if (productCountElement && stats.total_products !== undefined) {
        productCountElement.textContent = stats.total_products;
    }
    
    // Update low stock warning
    const lowStockElement = document.querySelector('.stat-card:nth-child(3) .stat-change span');
    if (lowStockElement && stats.low_stock_products !== undefined) {
        lowStockElement.textContent = `${stats.low_stock_products} low stock`;
    }
    
    // Update other stats as they become available
    // Revenue, orders, customers - these can be updated when Order model is implemented
}

function initializeCharts() {
    const salesChartCanvas = document.getElementById('salesChart');
    if (!salesChartCanvas) return;
    
    // Initialize sales chart (placeholder data)
    const ctx = salesChartCanvas.getContext('2d');
    
    // Simple canvas-based chart (you can replace this with Chart.js if available)
    drawSalesChart(ctx);
}

function drawSalesChart(ctx) {
    const canvas = ctx.canvas;
    const width = canvas.width;
    const height = canvas.height;
    
    // Sample data for the chart
    const data = [2500, 3200, 2800, 4100, 3600, 4500, 5200];
    const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Set up chart styling
    ctx.strokeStyle = '#6366f1';
    ctx.fillStyle = 'rgba(99, 102, 241, 0.1)';
    ctx.lineWidth = 3;
    
    // Calculate positions
    const padding = 40;
    const chartWidth = width - (padding * 2);
    const chartHeight = height - (padding * 2);
    
    const maxValue = Math.max(...data);
    const stepX = chartWidth / (data.length - 1);
    
    // Draw chart line
    ctx.beginPath();
    data.forEach((value, index) => {
        const x = padding + (index * stepX);
        const y = padding + chartHeight - (value / maxValue * chartHeight);
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw data points
    ctx.fillStyle = '#6366f1';
    data.forEach((value, index) => {
        const x = padding + (index * stepX);
        const y = padding + chartHeight - (value / maxValue * chartHeight);
        
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, Math.PI * 2);
        ctx.fill();
    });
}

function setupEventListeners() {
    // Sales filter
    const salesFilter = document.getElementById('salesFilter');
    if (salesFilter) {
        salesFilter.addEventListener('change', function() {
            // Reload chart data based on selected period
            console.log('Sales filter changed to:', this.value);
        });
    }
    
    // Restock buttons
    const restockButtons = document.querySelectorAll('.btn-restock');
    restockButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Handle restock action
            const alertItem = this.closest('.alert-item');
            const productName = alertItem.querySelector('h4').textContent;
            
            if (confirm(`Restock ${productName}?`)) {
                // API call to update stock would go here
                console.log('Restocking:', productName);
                
                // For now, just remove the alert
                alertItem.style.opacity = '0.5';
                this.textContent = 'Restocking...';
                this.disabled = true;
            }
        });
    });
    
    // Profile dropdown toggle
    const profileDropdown = document.querySelector('.profile-dropdown');
    if (profileDropdown) {
        profileDropdown.addEventListener('click', function() {
            // Toggle dropdown menu
            console.log('Profile dropdown clicked');
        });
    }
    
    // Notification button
    const notificationBtn = document.querySelector('.notification-btn');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            // Show notifications panel
            console.log('Notifications clicked');
        });
    }
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-LK', {
        style: 'currency',
        currency: 'LKR'
    }).format(amount);
}

function formatNumber(number) {
    return new Intl.NumberFormat('en-LK').format(number);
}

// Export functions for external use
window.ShopOwnerDashboard = {
    toggleSidebar,
    loadDashboardStats,
    updateStatsCards
};