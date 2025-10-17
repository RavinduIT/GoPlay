// Shop Owner Sales Analytics JavaScript

// Wait for DOM and Chart.js to load
document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        // Try again after a short delay
        setTimeout(function() {
            if (typeof Chart !== 'undefined') {
                initializeCharts();
                setupEventListeners();
            }
        }, 1000);
    } else {
        initializeCharts();
        setupEventListeners();
    }
});

// Chart color palette
const colors = {
    primary: '#2563eb',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    purple: '#8b5cf6',
    pink: '#ec4899',
    gray: '#6b7280',
    lightGray: '#cbd5e1'
};

function initializeCharts() {
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [
                    {
                        label: 'Current Period',
                        data: [65000, 82000, 91000, 107780],
                        borderColor: colors.primary,
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Previous Period',
                        data: [58000, 71000, 68000, 86000],
                        borderColor: colors.lightGray,
                        backgroundColor: 'rgba(203, 213, 225, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderDash: [5, 5]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': LKR ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + (value / 1000) + 'k';
                            }
                        }
                    }
                }
            }
        });
    }

    // Category Sales Chart (Doughnut)
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Cricket', 'Football', 'Tennis', 'Others'],
                datasets: [{
                    data: [124560, 98230, 67450, 55540],
                    backgroundColor: [
                        '#ef4444',
                        '#3b82f6',
                        '#10b981',
                        '#8b5cf6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
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
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': LKR ' + value.toLocaleString() + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    }

    // Order Status Chart (Pie)
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx) {
        new Chart(orderStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Delivered', 'Shipped', 'Processing', 'Cancelled'],
                datasets: [{
                    data: [856, 234, 112, 45],
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
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
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' orders (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Customer Demographics Chart (Bar)
    const demographicsCtx = document.getElementById('demographicsChart');
    if (demographicsCtx) {
        new Chart(demographicsCtx, {
            type: 'bar',
            data: {
                labels: ['18-24', '25-34', '35-44', '45-54', '55+'],
                datasets: [{
                    label: 'Customers',
                    data: [145, 312, 287, 178, 125],
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(37, 99, 235, 0.8)',
                        'rgba(37, 99, 235, 0.8)'
                    ],
                    borderRadius: 8,
                    barThickness: 40
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
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' customers';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 50
                        }
                    }
                }
            }
        });
    }

    // Hourly Sales Chart
    const hourlyCtx = document.getElementById('hourlyChart');
    if (hourlyCtx) {
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: ['00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '21:00'],
                datasets: [{
                    label: 'Orders',
                    data: [12, 8, 15, 45, 67, 89, 124, 98],
                    backgroundColor: 'rgba(139, 92, 246, 0.8)',
                    borderRadius: 8,
                    barThickness: 50
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
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' orders';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentChart');
    if (paymentCtx) {
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Card', 'Cash on Delivery', 'Mobile Wallet'],
                datasets: [{
                    data: [58, 28, 14],
                    backgroundColor: [
                        '#2563eb',
                        '#10b981',
                        '#f59e0b'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
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
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
}

function setupEventListeners() {
    // Date range selector
    const dateRangeBtns = document.querySelectorAll('.date-range-btn');
    dateRangeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            dateRangeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const range = this.dataset.range;
            // Here you would typically fetch new data based on the selected range
            // updateChartsData(range);
        });
    });

    // Export button
    const exportBtn = document.querySelector('.btn-export');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            // Implement export functionality
            alert('Sales report export feature coming soon!');
        });
    }

    // Chart period selectors
    const periodSelects = document.querySelectorAll('.chart-period-select');
    periodSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Update specific chart based on selection
        });
    });
}

// Sidebar toggle function
function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
    }
}

// Helper function to update charts with new data (for future use)
function updateChartsData(dateRange) {
    // This would fetch new data from the server based on date range
    // and update all charts accordingly
}
