// Ground Owner - Earnings Analytics JavaScript
class EarningsManager {
    constructor() {
        this.charts = {};
        this.earningsData = {};
        this.currentTimeRange = '30';
        this.currentChartType = 'line';
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadEarningsData();
        this.initializeCharts();
    }

    bindEvents() {
        // Time range selector
        document.getElementById('timeRange')?.addEventListener('change', (e) => {
            this.currentTimeRange = e.target.value;
            if (e.target.value === 'custom') {
                this.showDateRangeModal();
            } else {
                this.loadEarningsData();
            }
        });

        // Chart type buttons
        document.querySelectorAll('.chart-type-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.currentChartType = e.target.dataset.type;
                this.updateChartTypeButtons();
                this.updateRevenueChart();
            });
        });

        // Performance metric selector
        document.getElementById('performanceMetric')?.addEventListener('change', (e) => {
            this.updatePerformanceChart(e.target.value);
        });

        // Breakdown period selector
        document.getElementById('breakdownPeriod')?.addEventListener('change', (e) => {
            this.updateEarningsBreakdown(e.target.value);
        });

        // Transaction filter
        document.getElementById('transactionFilter')?.addEventListener('change', (e) => {
            this.filterTransactions(e.target.value);
        });

        // Modal functions
        window.closeDateRangeModal = this.closeDateRangeModal.bind(this);
        window.applyDateRange = this.applyDateRange.bind(this);
        window.generateReport = this.generateReport.bind(this);
        window.exportFinancials = this.exportFinancials.bind(this);
        window.viewAllTransactions = this.viewAllTransactions.bind(this);
    }

    async loadEarningsData() {
        try {
            const response = await fetch(`/api/ground-owner/earnings?period=${this.currentTimeRange}`);
            if (response.ok) {
                const data = await response.json();
                this.earningsData = data;
                this.updateEarningsOverview();
                this.updateAllCharts();
                this.updateAnalytics();
                this.loadTransactions();
            }
        } catch (error) {
            console.error('Error loading earnings data:', error);
            this.showToast('Failed to load earnings data', 'error');
        }
    }

    updateEarningsOverview() {
        const { overview } = this.earningsData;
        if (!overview) return;

        // Update overview cards
        document.getElementById('totalRevenue').textContent = `LKR ${overview.totalRevenue?.toLocaleString() || 0}`;
        document.getElementById('monthlyEarnings').textContent = `LKR ${overview.monthlyEarnings?.toLocaleString() || 0}`;
        document.getElementById('averageBooking').textContent = `LKR ${overview.averageBooking?.toLocaleString() || 0}`;
        document.getElementById('pendingPayments').textContent = `LKR ${overview.pendingPayments?.toLocaleString() || 0}`;

        // Update change indicators
        this.updateChangeIndicator('revenueChange', overview.revenueChange);
        this.updateChangeIndicator('monthlyChange', overview.monthlyChange);
        this.updateChangeIndicator('avgChange', overview.avgChange);
        
        document.getElementById('pendingCount').textContent = overview.pendingCount || 0;
    }

    updateChangeIndicator(elementId, change) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const isPositive = change >= 0;
        element.textContent = `${isPositive ? '+' : ''}${change}%`;
        element.className = `earning-change ${isPositive ? 'positive' : 'negative'}`;
        element.innerHTML = `
            <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>
            <span>${isPositive ? '+' : ''}${change}%</span>
            <small>vs last period</small>
        `;
    }

    initializeCharts() {
        // Initialize Chart.js charts
        this.initRevenueChart();
        this.initPerformanceChart();
        this.initRevenueSourceChart();
    }

    initRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        this.charts.revenue = new Chart(ctx, {
            type: this.currentChartType,
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    data: [],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: this.currentChartType === 'area',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'LKR ' + value.toLocaleString();
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 6,
                        hoverRadius: 8
                    }
                }
            }
        });
    }

    initPerformanceChart() {
        const ctx = document.getElementById('performanceChart');
        if (!ctx) return;

        this.charts.performance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Performance',
                    data: [],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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

    initRevenueSourceChart() {
        const ctx = document.getElementById('revenueSourceChart');
        if (!ctx) return;

        this.charts.revenueSource = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#06b6d4'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '60%'
            }
        });
    }

    updateAllCharts() {
        this.updateRevenueChart();
        this.updatePerformanceChart('revenue');
        this.updateRevenueSourceChart();
    }

    updateRevenueChart() {
        if (!this.charts.revenue || !this.earningsData.trends) return;

        const { trends } = this.earningsData;
        
        this.charts.revenue.data.labels = trends.labels || [];
        this.charts.revenue.data.datasets[0].data = trends.revenue || [];
        
        // Update chart type if changed
        if (this.charts.revenue.config.type !== this.currentChartType) {
            this.charts.revenue.config.type = this.currentChartType;
            this.charts.revenue.data.datasets[0].fill = this.currentChartType === 'area';
        }
        
        this.charts.revenue.update();
    }

    updatePerformanceChart(metric = 'revenue') {
        if (!this.charts.performance || !this.earningsData.grounds) return;

        const { grounds } = this.earningsData;
        const labels = grounds.map(g => g.name) || [];
        const data = grounds.map(g => g[metric]) || [];

        this.charts.performance.data.labels = labels;
        this.charts.performance.data.datasets[0].data = data;
        this.charts.performance.update();
    }

    updateRevenueSourceChart() {
        if (!this.charts.revenueSource || !this.earningsData.sources) return;

        const { sources } = this.earningsData;
        
        this.charts.revenueSource.data.labels = sources.map(s => s.name) || [];
        this.charts.revenueSource.data.datasets[0].data = sources.map(s => s.value) || [];
        this.charts.revenueSource.update();

        this.updateRevenueLegend(sources);
    }

    updateRevenueLegend(sources) {
        const legend = document.getElementById('revenueLegend');
        if (!legend) return;

        legend.innerHTML = sources.map((source, index) => `
            <div class="legend-item">
                <div class="legend-color" style="background: ${this.getChartColor(index)}"></div>
                <span class="legend-label">${source.name}</span>
                <span class="legend-value">LKR ${source.value.toLocaleString()}</span>
            </div>
        `).join('');
    }

    updateChartTypeButtons() {
        document.querySelectorAll('.chart-type-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-type="${this.currentChartType}"]`).classList.add('active');
    }

    updateAnalytics() {
        this.updateEarningsBreakdown('monthly');
        this.updateTopGrounds();
        this.updatePaymentAnalytics();
        this.updateBookingPatterns();
        this.updateFinancialSummary();
    }

    updateEarningsBreakdown(period) {
        const breakdown = document.getElementById('earningsBreakdown');
        if (!breakdown || !this.earningsData.breakdown) return;

        const data = this.earningsData.breakdown[period] || [];
        
        breakdown.innerHTML = data.map(item => `
            <div class="breakdown-item">
                <div>
                    <div class="breakdown-date">${item.period}</div>
                    <div class="breakdown-change">${item.change}% vs previous</div>
                </div>
                <div class="breakdown-amount">LKR ${item.amount.toLocaleString()}</div>
            </div>
        `).join('');
    }

    updateTopGrounds() {
        const topGrounds = document.getElementById('topGrounds');
        if (!topGrounds || !this.earningsData.topGrounds) return;

        topGrounds.innerHTML = this.earningsData.topGrounds.map(ground => `
            <div class="ranking-item">
                <div class="ranking-position">${ground.rank}</div>
                <div class="ranking-info">
                    <div class="ranking-name">${ground.name}</div>
                    <div class="ranking-stats">
                        <span>${ground.bookings} bookings</span>
                        <span>${ground.occupancy}% occupancy</span>
                    </div>
                </div>
                <div class="ranking-revenue">LKR ${ground.revenue.toLocaleString()}</div>
            </div>
        `).join('');
    }

    updatePaymentAnalytics() {
        const { paymentAnalytics } = this.earningsData;
        if (!paymentAnalytics) return;

        document.getElementById('paymentSuccessRate').textContent = `${paymentAnalytics.successRate}%`;
        document.getElementById('paymentSuccessBar').style.width = `${paymentAnalytics.successRate}%`;
        document.getElementById('avgProcessingTime').textContent = `${paymentAnalytics.avgProcessingTime}s`;
        document.getElementById('failedPayments').textContent = paymentAnalytics.failedPayments;
        document.getElementById('refundsIssued').textContent = `LKR ${paymentAnalytics.refundsIssued.toLocaleString()}`;
    }

    updateBookingPatterns() {
        this.updatePeakHours();
        this.updateWeeklyTrends();
    }

    updatePeakHours() {
        const peakHours = document.getElementById('peakHours');
        if (!peakHours || !this.earningsData.patterns?.hours) return;

        peakHours.innerHTML = this.earningsData.patterns.hours.map(hour => `
            <div class="hour-item">
                <div class="hour-label">${hour.time}</div>
                <div class="hour-bar">
                    <div class="hour-fill" style="width: ${hour.percentage}%"></div>
                </div>
                <div class="hour-value">${hour.bookings}</div>
            </div>
        `).join('');
    }

    updateWeeklyTrends() {
        const weeklyTrends = document.getElementById('weeklyTrends');
        if (!weeklyTrends || !this.earningsData.patterns?.days) return;

        weeklyTrends.innerHTML = this.earningsData.patterns.days.map(day => `
            <div class="day-item">
                <div class="day-label">${day.name}</div>
                <div class="day-bar">
                    <div class="day-fill" style="width: ${day.percentage}%"></div>
                </div>
                <div class="day-value">LKR ${day.revenue.toLocaleString()}</div>
            </div>
        `).join('');
    }

    updateFinancialSummary() {
        const { financial } = this.earningsData;
        if (!financial) return;

        document.getElementById('grossRevenue').textContent = `LKR ${financial.grossRevenue.toLocaleString()}`;
        document.getElementById('platformCommission').textContent = `-LKR ${financial.platformCommission.toLocaleString()}`;
        document.getElementById('processingFees').textContent = `-LKR ${financial.processingFees.toLocaleString()}`;
        document.getElementById('taxes').textContent = `-LKR ${financial.taxes.toLocaleString()}`;
        document.getElementById('netEarnings').textContent = `LKR ${financial.netEarnings.toLocaleString()}`;
    }

    async loadTransactions() {
        try {
            const response = await fetch('/api/ground-owner/transactions');
            if (response.ok) {
                const data = await response.json();
                this.renderTransactions(data.transactions);
            }
        } catch (error) {
            console.error('Error loading transactions:', error);
        }
    }

    renderTransactions(transactions) {
        const tbody = document.getElementById('transactionsTableBody');
        if (!tbody) return;

        tbody.innerHTML = transactions.map(transaction => `
            <tr>
                <td>#${transaction.id}</td>
                <td>${this.formatDate(transaction.date)}</td>
                <td>${transaction.ground}</td>
                <td>${transaction.customer}</td>
                <td>LKR ${transaction.amount.toLocaleString()}</td>
                <td>LKR ${transaction.fee.toLocaleString()}</td>
                <td>LKR ${transaction.net.toLocaleString()}</td>
                <td><span class="status-badge ${transaction.status}">${transaction.status}</span></td>
            </tr>
        `).join('');
    }

    filterTransactions(status) {
        // This would filter the transactions table based on status
        this.loadTransactions();
    }

    showDateRangeModal() {
        document.getElementById('dateRangeModal').classList.add('active');
    }

    closeDateRangeModal() {
        document.getElementById('dateRangeModal').classList.remove('active');
        document.getElementById('timeRange').value = this.currentTimeRange;
    }

    applyDateRange() {
        const startDate = document.getElementById('customStartDate').value;
        const endDate = document.getElementById('customEndDate').value;
        
        if (!startDate || !endDate) {
            this.showToast('Please select both start and end dates', 'error');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            this.showToast('Start date must be before end date', 'error');
            return;
        }

        this.currentTimeRange = 'custom';
        this.customDateRange = { startDate, endDate };
        this.closeDateRangeModal();
        this.loadEarningsData();
    }

    async generateReport() {
        try {
            this.showToast('Generating earnings report...', 'info');
            
            const response = await fetch('/api/ground-owner/earnings/report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    period: this.currentTimeRange,
                    customRange: this.customDateRange
                })
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `earnings-report-${new Date().toISOString().split('T')[0]}.pdf`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                
                this.showToast('Report generated successfully', 'success');
            }
        } catch (error) {
            console.error('Error generating report:', error);
            this.showToast('Failed to generate report', 'error');
        }
    }

    exportFinancials() {
        const financial = this.earningsData.financial;
        if (!financial) return;

        const csvContent = [
            ['Item', 'Amount'],
            ['Gross Revenue', financial.grossRevenue],
            ['Platform Commission', -financial.platformCommission],
            ['Processing Fees', -financial.processingFees],
            ['Taxes', -financial.taxes],
            ['Net Earnings', financial.netEarnings]
        ].map(row => row.join(',')).join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `financial-summary-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }

    viewAllTransactions() {
        // Redirect to full transactions page or expand table
        window.location.href = '/ground-owner/transactions';
    }

    // Utility functions
    getChartColor(index) {
        const colors = [
            '#3b82f6',
            '#10b981',
            '#f59e0b',
            '#ef4444',
            '#8b5cf6',
            '#06b6d4'
        ];
        return colors[index % colors.length];
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString();
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
        
        toast.style.cssText = `
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 12px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        `;
        
        container.appendChild(toast);
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => container.removeChild(toast), 300);
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.earningsManager = new EarningsManager();
});

// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    sidebar.classList.toggle('collapsed');
}