// Coach Earnings Management
class CoachEarnings {
    constructor() {
        this.earnings = [];
        this.stats = {};
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.totalPages = 1;
        this.filters = {
            dateRange: 'month',
            sessionType: '',
            paymentStatus: '',
            sortBy: 'date_desc'
        };
        this.earningsTrendChart = null;
        this.sessionBreakdownChart = null;
        this.init();
    }

    async init() {
        await this.loadEarningsData();
        this.setupEventListeners();
        this.renderStats();
        this.renderCharts();
        this.renderEarnings();
    }

    setupEventListeners() {
        // Date range filter
        const dateRangeSelect = document.getElementById('dateRange');
        if (dateRangeSelect) {
            dateRangeSelect.addEventListener('change', (e) => {
                const customDateGroup = document.getElementById('customDateGroup');
                const customDateGroup2 = document.getElementById('customDateGroup2');
                if (e.target.value === 'custom') {
                    customDateGroup.style.display = 'block';
                    customDateGroup2.style.display = 'block';
                } else {
                    customDateGroup.style.display = 'none';
                    customDateGroup2.style.display = 'none';
                }
            });
        }

        // Sort change
        const sortSelect = document.getElementById('sortBy');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.filters.sortBy = e.target.value;
                this.applyFilters();
            });
        }

        // Period selector for chart
        const periodBtns = document.querySelectorAll('.period-btn');
        periodBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                periodBtns.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                const period = e.target.dataset.period;
                this.updateEarningsTrend(period);
            });
        });
    }

    async loadEarningsData() {
        try {
            const queryParams = new URLSearchParams(this.filters).toString();
            const response = await fetch(`/api/coach/earnings?${queryParams}&page=${this.currentPage}&limit=${this.itemsPerPage}`);
            const data = await response.json();

            if (data.success) {
                this.earnings = data.earnings || [];
                this.stats = data.stats || {};
                this.totalPages = data.totalPages || 1;
                this.currentPage = data.currentPage || 1;
            } else {
                console.error('Error loading earnings:', data.message);
            }
        } catch (error) {
            console.error('Error loading earnings data:', error);
        }
    }

    renderStats() {
        // Total earnings
        const totalEarningsEl = document.getElementById('totalEarnings');
        if (totalEarningsEl && this.stats.total_earnings !== undefined) {
            totalEarningsEl.textContent = `₹${this.formatCurrency(this.stats.total_earnings)}`;
        }

        // Earnings change
        const earningsChangeEl = document.getElementById('earningsChange');
        if (earningsChangeEl && this.stats.earnings_change !== undefined) {
            const change = this.stats.earnings_change;
            const changeIcon = change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            const changeClass = change >= 0 ? 'positive' : 'negative';
            earningsChangeEl.innerHTML = `<i class="fas ${changeIcon}"></i> ${Math.abs(change)}% from last month`;
            earningsChangeEl.className = `stat-change ${changeClass}`;
        }

        // Pending payments
        const pendingPaymentsEl = document.getElementById('pendingPayments');
        if (pendingPaymentsEl && this.stats.pending_payments !== undefined) {
            pendingPaymentsEl.textContent = `₹${this.formatCurrency(this.stats.pending_payments)}`;
        }

        // Pending count
        const pendingCountEl = document.getElementById('pendingCount');
        if (pendingCountEl && this.stats.pending_count !== undefined) {
            pendingCountEl.textContent = `${this.stats.pending_count} sessions`;
        }

        // Completed sessions
        const completedSessionsEl = document.getElementById('completedSessions');
        if (completedSessionsEl && this.stats.completed_sessions !== undefined) {
            completedSessionsEl.textContent = this.stats.completed_sessions;
        }

        // Completed change
        const completedChangeEl = document.getElementById('completedChange');
        if (completedChangeEl && this.stats.completed_change !== undefined) {
            const change = this.stats.completed_change;
            const changeIcon = change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            const changeClass = change >= 0 ? 'positive' : 'negative';
            completedChangeEl.innerHTML = `<i class="fas ${changeIcon}"></i> ${Math.abs(change)}% from last month`;
            completedChangeEl.className = `stat-change ${changeClass}`;
        }

        // Average rate
        const avgRateEl = document.getElementById('avgRate');
        if (avgRateEl && this.stats.avg_rate !== undefined) {
            avgRateEl.textContent = `₹${this.formatCurrency(this.stats.avg_rate)}/hr`;
        }

        // Rate change
        const rateChangeEl = document.getElementById('rateChange');
        if (rateChangeEl && this.stats.rate_change !== undefined) {
            const change = this.stats.rate_change;
            const changeIcon = change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            const changeClass = change >= 0 ? 'positive' : 'negative';
            rateChangeEl.innerHTML = `<i class="fas ${changeIcon}"></i> ${Math.abs(change)}% from last month`;
            rateChangeEl.className = `stat-change ${changeClass}`;
        }
    }

    renderCharts() {
        this.renderEarningsTrend();
        this.renderSessionBreakdown();
    }

    async renderEarningsTrend(period = '6months') {
        try {
            const response = await fetch(`/api/coach/earnings-trend?period=${period}`);
            const data = await response.json();

            if (!data.success) {
                console.error('Error loading trend data:', data.message);
                return;
            }

            const ctx = document.getElementById('earningsTrendChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.earningsTrendChart) {
                this.earningsTrendChart.destroy();
            }

            // Create gradient
            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(251, 191, 36, 0.5)');
            gradient.addColorStop(1, 'rgba(251, 191, 36, 0.05)');

            this.earningsTrendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        label: 'Earnings',
                        data: data.values || [],
                        borderColor: '#f59e0b',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#f59e0b',
                            borderWidth: 1,
                            callbacks: {
                                label: (context) => `Earnings: ₹${this.formatCurrency(context.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => `₹${this.formatCurrency(value)}`
                            },
                            grid: {
                                color: '#f1f5f9'
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
        } catch (error) {
            console.error('Error rendering earnings trend:', error);
        }
    }

    async renderSessionBreakdown() {
        try {
            const response = await fetch('/api/coach/session-breakdown');
            const data = await response.json();

            if (!data.success) {
                console.error('Error loading breakdown data:', data.message);
                return;
            }

            const ctx = document.getElementById('sessionBreakdownChart');
            if (!ctx) return;

            // Destroy existing chart
            if (this.sessionBreakdownChart) {
                this.sessionBreakdownChart.destroy();
            }

            const colors = {
                individual: '#3b82f6',
                group: '#10b981',
                assessment: '#8b5cf6'
            };

            this.sessionBreakdownChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        data: data.values || [],
                        backgroundColor: data.labels.map(label => colors[label.toLowerCase()] || '#94a3b8'),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            callbacks: {
                                label: (context) => `${context.label}: ₹${this.formatCurrency(context.parsed)}`
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Render legend
            this.renderBreakdownLegend(data.labels, data.values, colors);
        } catch (error) {
            console.error('Error rendering session breakdown:', error);
        }
    }

    renderBreakdownLegend(labels, values, colors) {
        const legendContainer = document.getElementById('breakdownLegend');
        if (!legendContainer) return;

        legendContainer.innerHTML = labels.map((label, index) => `
            <div class="legend-item">
                <div class="legend-label">
                    <div class="legend-color" style="background: ${colors[label.toLowerCase()] || '#94a3b8'}"></div>
                    <span class="legend-text">${label}</span>
                </div>
                <span class="legend-value">₹${this.formatCurrency(values[index])}</span>
            </div>
        `).join('');
    }

    renderEarnings() {
        const tbody = document.getElementById('earningsTableBody');
        const loadingMessage = document.getElementById('loadingMessage');
        const tableContainer = document.getElementById('earningsTableContainer');
        const noEarnings = document.getElementById('noEarnings');
        const pagination = document.getElementById('pagination');

        loadingMessage.style.display = 'none';

        if (this.earnings.length === 0) {
            tableContainer.style.display = 'none';
            noEarnings.style.display = 'block';
            pagination.style.display = 'none';
            return;
        }

        tableContainer.style.display = 'block';
        noEarnings.style.display = 'none';
        pagination.style.display = 'flex';

        tbody.innerHTML = this.earnings.map(earning => `
            <tr>
                <td>
                    <strong>${this.formatDate(earning.booking_date)}</strong><br>
                    <small>${earning.start_time} - ${earning.end_time}</small>
                </td>
                <td>
                    <div class="client-info">
                        <span class="client-name">${earning.client_name || 'N/A'}</span>
                        <small class="client-email">${earning.client_email || ''}</small>
                    </div>
                </td>
                <td><span class="session-badge ${earning.session_type || 'individual'}">${earning.session_type || 'Individual'}</span></td>
                <td>${earning.duration_hours || 1}h</td>
                <td class="amount-cell">₹${this.formatCurrency(earning.total_amount)}</td>
                <td><span class="payment-status ${earning.payment_status || 'pending'}">${earning.payment_status || 'Pending'}</span></td>
                <td>
                    <button class="action-btn btn-view" onclick="earningsManager.viewSessionDetails(${earning.id})">
                        <i class="fas fa-eye"></i> View
                    </button>
                </td>
            </tr>
        `).join('');

        // Update pagination
        this.updatePagination();
    }

    updatePagination() {
        const pageInfo = document.getElementById('pageInfo');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');

        if (pageInfo) {
            pageInfo.textContent = `Page ${this.currentPage} of ${this.totalPages}`;
        }

        if (prevBtn) {
            prevBtn.disabled = this.currentPage === 1;
        }

        if (nextBtn) {
            nextBtn.disabled = this.currentPage === this.totalPages;
        }
    }

    async applyFilters() {
        this.filters.dateRange = document.getElementById('dateRange').value;
        this.filters.sessionType = document.getElementById('sessionType').value;
        this.filters.paymentStatus = document.getElementById('paymentStatus').value;

        if (this.filters.dateRange === 'custom') {
            this.filters.startDate = document.getElementById('startDate').value;
            this.filters.endDate = document.getElementById('endDate').value;
        }

        this.currentPage = 1;
        await this.loadEarningsData();
        this.renderStats();
        this.renderEarnings();
    }

    async previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            await this.loadEarningsData();
            this.renderEarnings();
        }
    }

    async nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            await this.loadEarningsData();
            this.renderEarnings();
        }
    }

    async viewSessionDetails(sessionId) {
        try {
            const response = await fetch(`/api/coach/earnings/${sessionId}`);
            const data = await response.json();

            if (!data.success) {
                alert('Error loading session details');
                return;
            }

            const session = data.session;
            const modalContent = document.getElementById('sessionDetailsContent');

            modalContent.innerHTML = `
                <div class="detail-row">
                    <span class="detail-label">Session ID</span>
                    <span class="detail-value">#${session.id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Client Name</span>
                    <span class="detail-value">${session.client_name || 'N/A'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Client Email</span>
                    <span class="detail-value">${session.client_email || 'N/A'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value">${this.formatDate(session.booking_date)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time</span>
                    <span class="detail-value">${session.start_time} - ${session.end_time}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration</span>
                    <span class="detail-value">${session.duration_hours}h</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Session Type</span>
                    <span class="detail-value">${session.session_type || 'Individual'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount</span>
                    <span class="detail-value amount-cell">₹${this.formatCurrency(session.total_amount)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Status</span>
                    <span class="detail-value"><span class="payment-status ${session.payment_status}">${session.payment_status}</span></span>
                </div>
                ${session.payment_date ? `
                <div class="detail-row">
                    <span class="detail-label">Payment Date</span>
                    <span class="detail-value">${this.formatDate(session.payment_date)}</span>
                </div>
                ` : ''}
            `;

            this.showModal('sessionDetailsModal');
        } catch (error) {
            console.error('Error loading session details:', error);
            alert('Error loading session details');
        }
    }

    async exportEarnings() {
        try {
            const queryParams = new URLSearchParams(this.filters).toString();
            window.location.href = `/api/coach/earnings/export?${queryParams}`;
        } catch (error) {
            console.error('Error exporting earnings:', error);
            alert('Error exporting earnings');
        }
    }

    async updateEarningsTrend(period) {
        await this.renderEarningsTrend(period);
    }

    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }
    }

    formatCurrency(amount) {
        if (!amount) return '0';
        return parseFloat(amount).toLocaleString('en-IN', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }
}

// Initialize earnings manager
let earningsManager;
document.addEventListener('DOMContentLoaded', () => {
    earningsManager = new CoachEarnings();
});

// Close modal on outside click
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('show');
    }
});
