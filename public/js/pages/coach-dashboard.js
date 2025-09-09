class CoachDashboard {
    constructor() {
        this.stats = {
            totalClients: 0,
            totalSessions: 0,
            monthlyEarnings: 0,
            avgRating: 0
        };
        this.todaySchedule = [];
        this.recentClients = [];
        this.upcomingSessions = [];
        this.notifications = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadDashboardData();
        this.updateCurrentDate();
        this.initializeCharts();
    }

    bindEvents() {
        // Quick session button
        document.getElementById('quickSessionBtn').addEventListener('click', () => {
            this.openModal('quickSessionModal');
        });

        // Quick actions
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const action = e.currentTarget.getAttribute('onclick');
                if (action) {
                    eval(action);
                }
            });
        });

        // Modal events
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) this.closeModal(modal.id);
            });
        });

        // Analytics timeframe change
        document.getElementById('analyticsTimeframe').addEventListener('change', (e) => {
            this.updateAnalyticsChart(e.target.value);
        });

        // Session type change in modal
        const sessionTypeSelect = document.querySelector('select[name="sessionType"]');
        if (sessionTypeSelect) {
            sessionTypeSelect.addEventListener('change', (e) => {
                this.handleSessionTypeChange(e.target.value);
            });
        }

        // Form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.id === 'quickSessionForm') {
                e.preventDefault();
                this.saveQuickSession();
            }
        });

        // Refresh actions
        document.addEventListener('click', (e) => {
            if (e.target.matches('.refresh-btn, .refresh-btn *')) {
                this.refreshProgress();
            }
        });
    }

    async loadDashboardData() {
        try {
            const response = await fetch('/api/coach/dashboard');
            const data = await response.json();
            
            this.stats = data.stats;
            this.todaySchedule = data.todaySchedule || [];
            this.recentClients = data.recentClients || [];
            this.upcomingSessions = data.upcomingSessions || [];
            this.notifications = data.notifications || [];
            
            this.updateStats();
            this.renderTodaySchedule();
            this.renderRecentClients();
            this.renderProgressHighlights();
            this.renderUpcomingSessions();
            this.renderNotifications();
            this.updateGoalsProgress();
            
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            this.showToast('Error loading dashboard data', 'error');
        }
    }

    updateStats() {
        // Update stat numbers with animation
        this.animateValue('totalClients', 0, this.stats.totalClients, 1500);
        this.animateValue('totalSessions', 0, this.stats.totalSessions, 1500);
        this.animateValue('monthlyEarnings', 0, this.stats.monthlyEarnings, 1500);
        this.animateValue('avgRating', 0, this.stats.avgRating, 1500, 1);

        // Update growth indicators
        document.getElementById('clientsGrowth').textContent = this.stats.clientsGrowth || 0;
        document.getElementById('sessionsGrowth').textContent = this.stats.sessionsGrowth || 0;
        document.getElementById('earningsGrowth').textContent = this.stats.earningsGrowth || 0;
        document.getElementById('totalReviews').textContent = this.stats.totalReviews || 0;
    }

    animateValue(elementId, start, end, duration, decimals = 0) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const startTimestamp = performance.now();
        
        const step = (timestamp) => {
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const current = start + (end - start) * this.easeOutQuart(progress);
            
            element.textContent = decimals > 0 ? current.toFixed(decimals) : Math.floor(current);
            
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };
        
        requestAnimationFrame(step);
    }

    easeOutQuart(t) {
        return 1 - (--t) * t * t * t;
    }

    renderTodaySchedule() {
        const container = document.getElementById('todaySchedule');
        if (!container) return;
        
        if (this.todaySchedule.length === 0) {
            container.innerHTML = `
                <div class="no-schedule">
                    <i class="fas fa-calendar-check"></i>
                    <p>No sessions scheduled for today</p>
                    <button class="btn btn-primary" onclick="coachDashboard.scheduleSession()">
                        <i class="fas fa-plus"></i>
                        Schedule Session
                    </button>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.todaySchedule.map(session => `
            <div class="schedule-item ${session.status}" data-session-id="${session.id}">
                <div class="schedule-time">
                    <span class="time">${session.time}</span>
                    <span class="duration">${session.duration}</span>
                </div>
                <div class="schedule-info">
                    <h4>${session.title}</h4>
                    <p>${session.clientName}</p>
                    <span class="session-type ${session.type}">${this.getSessionTypeLabel(session.type)}</span>
                </div>
                <div class="schedule-actions">
                    ${this.getScheduleActions(session)}
                </div>
            </div>
        `).join('');
    }

    getScheduleActions(session) {
        const now = new Date();
        const sessionTime = new Date(session.datetime);
        const timeDiff = sessionTime - now;
        
        if (session.status === 'completed') {
            return '<span class="status completed">Completed</span>';
        }
        
        if (session.status === 'cancelled') {
            return '<span class="status cancelled">Cancelled</span>';
        }
        
        if (timeDiff <= 0 && timeDiff > -3600000) { // Within 1 hour
            return '<button class="btn-start" onclick="coachDashboard.startSession(' + session.id + ')">Start Session</button>';
        }
        
        if (timeDiff <= 1800000) { // Within 30 minutes
            return '<button class="btn-prepare" onclick="coachDashboard.prepareSession(' + session.id + ')">Prepare</button>';
        }
        
        return '<button class="btn btn-outline" onclick="coachDashboard.viewSession(' + session.id + ')">View Details</button>';
    }

    getSessionTypeLabel(type) {
        const labels = {
            'individual': 'Individual',
            'group': 'Group',
            'assessment': 'Assessment',
            'consultation': 'Consultation'
        };
        return labels[type] || type;
    }

    renderRecentClients() {
        const container = document.getElementById('recentClients');
        if (!container) return;
        
        if (this.recentClients.length === 0) {
            container.innerHTML = `
                <div class="no-clients">
                    <i class="fas fa-users"></i>
                    <p>No recent clients</p>
                    <button class="btn btn-primary" onclick="coachDashboard.addNewClient()">
                        <i class="fas fa-user-plus"></i>
                        Add Client
                    </button>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.recentClients.map(client => `
            <div class="client-item" onclick="coachDashboard.viewClient(${client.id})">
                <div class="client-avatar">
                    <img src="${client.avatar || '/public/assets/images/default-avatar.png'}" alt="${client.name}">
                </div>
                <div class="client-info">
                    <h4>${client.name}</h4>
                    <p>${client.lastSession || 'No sessions yet'}</p>
                </div>
                <div class="client-status ${client.status}">${this.getClientStatus(client.status)}</div>
            </div>
        `).join('');
    }

    getClientStatus(status) {
        const statuses = {
            'active': 'Active',
            'inactive': 'Inactive',
            'trial': 'Trial',
            'suspended': 'Suspended'
        };
        return statuses[status] || status;
    }

    renderProgressHighlights() {
        const container = document.getElementById('progressHighlights');
        if (!container) return;
        
        // Mock progress data - replace with actual API call
        const progressData = [
            {
                id: 1,
                name: 'Kavinda Ranasighe',
                avatar: '/public/assets/images/student1.jpg',
                sessions: 12,
                progress: 85,
                improvement: 15
            },
            {
                id: 2,
                name: 'Sanduni Rajapakse',
                avatar: '/public/assets/images/student2.jpg',
                sessions: 8,
                progress: 72,
                improvement: 20
            },
            {
                id: 3,
                name: 'Dilan Wijesinghe',
                avatar: '/public/assets/images/student3.jpg',
                sessions: 15,
                progress: 92,
                improvement: 8
            }
        ];
        
        container.innerHTML = progressData.map(student => `
            <div class="progress-item" onclick="coachDashboard.viewStudentProgress(${student.id})">
                <div class="student-info">
                    <div class="student-avatar">
                        <img src="${student.avatar}" alt="${student.name}">
                    </div>
                    <div class="student-details">
                        <h4>${student.name}</h4>
                        <p>${student.sessions} sessions completed</p>
                    </div>
                </div>
                <div class="progress-chart">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${student.progress}%"></div>
                    </div>
                    <span class="progress-score">${student.progress}%</span>
                </div>
            </div>
        `).join('');
    }

    renderUpcomingSessions() {
        const container = document.getElementById('upcomingSessions');
        const countElement = document.getElementById('upcomingCount');
        
        if (!container) return;
        
        if (countElement) {
            countElement.textContent = `${this.upcomingSessions.length} sessions`;
        }
        
        if (this.upcomingSessions.length === 0) {
            container.innerHTML = `
                <div class="no-sessions">
                    <i class="fas fa-calendar-times"></i>
                    <p>No upcoming sessions</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.upcomingSessions.map(session => `
            <div class="session-timeline-item">
                <div class="session-time">
                    <span class="date">${this.formatDate(session.date)}</span>
                    <span class="time">${session.time}</span>
                </div>
                <div class="session-details">
                    <h4>${session.title}</h4>
                    <p>${session.clientName}</p>
                    <span class="session-type ${session.type}">${this.getSessionTypeLabel(session.type)}</span>
                </div>
            </div>
        `).join('');
    }

    renderNotifications() {
        const container = document.getElementById('recentNotifications');
        if (!container) return;
        
        if (this.notifications.length === 0) {
            container.innerHTML = `
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>No recent notifications</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.notifications.slice(0, 5).map(notification => `
            <div class="notification-item ${notification.read ? 'read' : 'unread'}">
                <div class="notification-icon">
                    <i class="fas ${this.getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-content">
                    <p>${notification.message}</p>
                    <span class="notification-time">${this.timeAgo(notification.created_at)}</span>
                </div>
            </div>
        `).join('');
    }

    getNotificationIcon(type) {
        const icons = {
            'session': 'fa-calendar',
            'payment': 'fa-credit-card',
            'review': 'fa-star',
            'message': 'fa-envelope',
            'system': 'fa-info-circle'
        };
        return icons[type] || 'fa-bell';
    }

    updateGoalsProgress() {
        // Mock goals data - replace with actual API call
        const goals = {
            newClients: { current: 3, target: 10 },
            sessions: { current: 87, target: 100 },
            revenue: { current: 28450, target: 50000 },
            satisfaction: { current: 4.5, target: 5.0 }
        };
        
        // Update new clients
        document.getElementById('newClientsProgress').textContent = `${goals.newClients.current}/${goals.newClients.target}`;
        document.getElementById('newClientsBar').style.width = `${(goals.newClients.current / goals.newClients.target) * 100}%`;
        
        // Update sessions
        document.getElementById('sessionsProgress').textContent = `${goals.sessions.current}/${goals.sessions.target}`;
        document.getElementById('sessionsBar').style.width = `${(goals.sessions.current / goals.sessions.target) * 100}%`;
        
        // Update revenue
        document.getElementById('revenueProgress').textContent = goals.revenue.current.toLocaleString();
        document.getElementById('revenueBar').style.width = `${(goals.revenue.current / goals.revenue.target) * 100}%`;
        
        // Update satisfaction
        document.getElementById('satisfactionProgress').textContent = `${goals.satisfaction.current}/${goals.satisfaction.target}`;
        document.getElementById('satisfactionBar').style.width = `${(goals.satisfaction.current / goals.satisfaction.target) * 100}%`;
    }

    initializeCharts() {
        // Sessions Chart
        const ctx = document.getElementById('sessionsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Sessions',
                        data: [12, 15, 8, 18, 22, 14, 10],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
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
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Update metrics
        document.getElementById('avgSessionDuration').textContent = '65';
        document.getElementById('clientRetentionRate').textContent = '92%';
        document.getElementById('completionRate').textContent = '88%';
    }

    updateAnalyticsChart(timeframe) {
        // Update chart based on selected timeframe
        // This would typically make an API call to get new data
        console.log('Updating analytics for timeframe:', timeframe);
    }

    // Quick Actions
    addNewClient() {
        window.location.href = '/coach/clients?action=add';
    }

    scheduleSession() {
        this.openModal('quickSessionModal');
    }

    createProgram() {
        window.location.href = '/coach/programs?action=create';
    }

    viewProgress() {
        window.location.href = '/coach/assessments';
    }

    // Session Actions
    startSession(sessionId) {
        if (confirm('Start this session now?')) {
            this.updateSessionStatus(sessionId, 'in-progress');
        }
    }

    prepareSession(sessionId) {
        window.location.href = `/coach/sessions/${sessionId}/prepare`;
    }

    viewSession(sessionId) {
        window.location.href = `/coach/sessions/${sessionId}`;
    }

    async updateSessionStatus(sessionId, status) {
        try {
            const response = await fetch(`/api/coach/sessions/${sessionId}/status`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            });
            
            if (response.ok) {
                this.showToast('Session status updated', 'success');
                this.loadDashboardData();
            } else {
                throw new Error('Failed to update session status');
            }
        } catch (error) {
            this.showToast('Error updating session status', 'error');
        }
    }

    // Client Actions
    viewClient(clientId) {
        window.location.href = `/coach/clients/${clientId}`;
    }

    viewStudentProgress(studentId) {
        window.location.href = `/coach/clients/${studentId}/progress`;
    }

    // Quick Session Modal
    handleSessionTypeChange(type) {
        const groupSizeGroup = document.getElementById('groupSizeGroup');
        if (groupSizeGroup) {
            groupSizeGroup.style.display = type === 'group' ? 'block' : 'none';
        }
    }

    async saveQuickSession() {
        const form = document.getElementById('quickSessionForm');
        const formData = new FormData(form);
        const sessionData = Object.fromEntries(formData.entries());
        
        if (!this.validateSessionForm(sessionData)) {
            return;
        }
        
        try {
            const response = await fetch('/api/coach/sessions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(sessionData)
            });
            
            if (response.ok) {
                const newSession = await response.json();
                this.showToast('Session scheduled successfully', 'success');
                this.closeModal('quickSessionModal');
                this.loadDashboardData();
            } else {
                const error = await response.json();
                throw new Error(error.message || 'Failed to schedule session');
            }
        } catch (error) {
            this.showToast('Error scheduling session: ' + error.message, 'error');
        }
    }

    validateSessionForm(data) {
        const required = ['clientId', 'sessionType', 'sessionDate', 'sessionTime'];
        for (let field of required) {
            if (!data[field]) {
                this.showToast(`Please fill in the ${field.replace(/([A-Z])/g, ' $1').toLowerCase()}`, 'error');
                return false;
            }
        }
        
        // Validate date is not in the past
        const sessionDateTime = new Date(data.sessionDate + 'T' + data.sessionTime);
        if (sessionDateTime < new Date()) {
            this.showToast('Session date and time cannot be in the past', 'error');
            return false;
        }
        
        return true;
    }

    // Utility Methods
    updateCurrentDate() {
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            const now = new Date();
            dateElement.textContent = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    }

    formatDate(dateString) {
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

    timeAgo(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diffInSeconds = Math.floor((now - time) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }

    // Modal Management
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Load clients for quick session modal
            if (modalId === 'quickSessionModal') {
                this.loadClientsForModal();
            }
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            
            // Reset forms
            const forms = modal.querySelectorAll('form');
            forms.forEach(form => form.reset());
        }
    }

    async loadClientsForModal() {
        try {
            const response = await fetch('/api/coach/clients');
            const clients = await response.json();
            
            const select = document.querySelector('select[name="clientId"]');
            if (select) {
                select.innerHTML = '<option value="">Select Client</option>' +
                    clients.map(client => `<option value="${client.id}">${client.name}</option>`).join('');
            }
        } catch (error) {
            console.error('Error loading clients:', error);
        }
    }

    // Other Actions
    refreshProgress() {
        this.loadDashboardData();
        this.showToast('Progress data refreshed', 'success');
    }

    setGoals() {
        window.location.href = '/coach/settings?tab=goals';
    }

    markAllRead() {
        // Mark all notifications as read
        this.notifications.forEach(notification => notification.read = true);
        this.renderNotifications();
        this.showToast('All notifications marked as read', 'success');
    }

    // Toast Notifications
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize dashboard when DOM is loaded
let coachDashboard;
document.addEventListener('DOMContentLoaded', () => {
    coachDashboard = new CoachDashboard();
});