/**
 * Coach Sessions Management
 * Handles session display, filtering, and interactions
 */

const coachSessions = {
    sessions: [],
    filteredSessions: [],
    currentView: 'list',
    currentMonth: new Date(),

    /**
     * Initialize the sessions page
     */
    init() {
        console.log('Initializing Coach Sessions...');
        
        // Show loading state
        this.showLoadingState();
        
        // Simulate loading delay for smooth experience
        setTimeout(() => {
            this.loadFakeSessions();
            this.setupEventListeners();
            this.renderSessions();
            this.updateStats();
            this.hideLoadingState();
        }, 500);
    },
    
    /**
     * Show loading state
     */
    showLoadingState() {
        const container = document.getElementById('sessionsList');
        if (container) {
            container.innerHTML = `
                <div class="loading-state">
                    <div class="loading-spinner"></div>
                    <p>Loading sessions...</p>
                </div>
            `;
        }
    },
    
    /**
     * Hide loading state
     */
    hideLoadingState() {
        // Loading is hidden when content is rendered
    },

    /**
     * Load fake sessions data
     */
    loadFakeSessions() {
        this.sessions = [
            // Upcoming Session
            {
                id: 1,
                title: 'Cricket Batting Fundamentals',
                type: 'individual',
                status: 'upcoming',
                clientName: 'Kamal Perera',
                clientAvatar: '/public/assets/images/avatar-1.jpg',
                date: '2025-10-23',
                startTime: '09:00',
                endTime: '10:00',
                duration: 60,
                location: 'Ground A - Cricket Field',
                rate: 3500,
                description: 'Focus on batting stance, grip, and basic strokes',
                equipment: 'Cricket bats, balls, stumps',
                notes: 'Client is a beginner, needs basic fundamentals'
            },
            
            // Completed Session
            {
                id: 2,
                title: 'Fast Bowling Masterclass',
                type: 'individual',
                status: 'completed',
                clientName: 'Thilina Bandara',
                clientAvatar: '/public/assets/images/avatar-8.jpg',
                date: '2025-10-20',
                startTime: '14:00',
                endTime: '15:30',
                duration: 90,
                location: 'Ground B - Practice Nets',
                rate: 4500,
                description: 'Advanced bowling techniques and pace development',
                equipment: 'Cricket balls, speed gun',
                completedAt: '2025-10-20 15:30:00',
                feedback: 'Excellent progress on pace and accuracy'
            },
            
            // Group Session
            {
                id: 3,
                title: 'Youth Group Training',
                type: 'group',
                status: 'completed',
                clientName: 'Junior Cricket Club',
                clientAvatar: '/public/assets/images/team-avatar.jpg',
                date: '2025-10-19',
                startTime: '16:00',
                endTime: '18:00',
                duration: 120,
                location: 'Ground A - Cricket Field',
                rate: 8000,
                groupSize: 15,
                description: 'Team practice session for young players',
                equipment: 'Full cricket gear',
                completedAt: '2025-10-19 18:00:00',
                feedback: 'Energetic session, kids showed great enthusiasm'
            },
            
            // Cancelled Session
            {
                id: 4,
                title: 'Cricket Practice Session',
                type: 'individual',
                status: 'cancelled',
                clientName: 'Sanath Kumar',
                clientAvatar: '/public/assets/images/avatar-13.jpg',
                date: '2025-10-22',
                startTime: '10:00',
                endTime: '11:00',
                duration: 60,
                location: 'Ground A - Cricket Field',
                rate: 3500,
                cancelledAt: '2025-10-21 18:30:00',
                cancelReason: 'Client illness'
            }
        ];

        this.filteredSessions = [...this.sessions];
        console.log('Loaded', this.sessions.length, 'sessions');
    },

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // View controls
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchView(e.target.closest('.view-btn').dataset.view);
            });
        });

        // Filters
        const statusFilter = document.getElementById('statusFilter');
        const typeFilter = document.getElementById('typeFilter');
        const dateFilter = document.getElementById('dateFilter');
        const searchInput = document.getElementById('sessionSearch');
        const clearFilters = document.getElementById('clearFilters');

        if (statusFilter) statusFilter.addEventListener('change', () => this.applyFilters());
        if (typeFilter) typeFilter.addEventListener('change', () => this.applyFilters());
        if (dateFilter) dateFilter.addEventListener('change', () => this.applyFilters());
        if (searchInput) searchInput.addEventListener('input', () => this.applyFilters());
        if (clearFilters) clearFilters.addEventListener('click', () => this.clearAllFilters());

        // Modal close buttons
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) this.closeModal(modal.id);
            });
        });

        // Close modals on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });

        // Export button
        const exportBtn = document.getElementById('exportSessionsBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => this.exportSessions());
        }
    },

    /**
     * Switch between different views
     */
    switchView(view) {
        this.currentView = view;

        // Update active button
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            }
        });

        // Update active view
        document.querySelectorAll('.sessions-view').forEach(viewEl => {
            viewEl.classList.remove('active');
        });

        const viewElement = document.getElementById(view + 'View');
        if (viewElement) {
            viewElement.classList.add('active');
        }

        // Render appropriate view
        if (view === 'list') {
            this.renderSessions();
        } else if (view === 'calendar') {
            this.renderCalendar();
        } else if (view === 'timeline') {
            this.renderTimeline();
        }
    },

    /**
     * Apply filters to sessions
     */
    applyFilters() {
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const typeFilter = document.getElementById('typeFilter')?.value || '';
        const dateFilter = document.getElementById('dateFilter')?.value || '';
        const searchQuery = document.getElementById('sessionSearch')?.value.toLowerCase() || '';

        this.filteredSessions = this.sessions.filter(session => {
            // Status filter
            if (statusFilter && session.status !== statusFilter) return false;

            // Type filter
            if (typeFilter && session.type !== typeFilter) return false;

            // Date filter
            if (dateFilter && session.date !== dateFilter) return false;

            // Search filter
            if (searchQuery) {
                const searchableText = `${session.title} ${session.clientName} ${session.description || ''}`.toLowerCase();
                if (!searchableText.includes(searchQuery)) return false;
            }

            return true;
        });

        this.renderSessions();
    },

    /**
     * Clear all filters
     */
    clearAllFilters() {
        const statusFilter = document.getElementById('statusFilter');
        const typeFilter = document.getElementById('typeFilter');
        const dateFilter = document.getElementById('dateFilter');
        const searchInput = document.getElementById('sessionSearch');

        if (statusFilter) statusFilter.value = '';
        if (typeFilter) typeFilter.value = '';
        if (dateFilter) dateFilter.value = '';
        if (searchInput) searchInput.value = '';

        this.filteredSessions = [...this.sessions];
        this.renderSessions();
    },

    /**
     * Render sessions in list view
     */
    renderSessions() {
        const container = document.getElementById('sessionsList');
        if (!container) return;

        if (this.filteredSessions.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Sessions Found</h3>
                    <p>Try adjusting your filters or create a new session</p>
                </div>
            `;
            return;
        }

        const sessionsHTML = this.filteredSessions.map((session, index) => {
            const card = this.createSessionCard(session);
            // Add staggered animation delay
            return card.replace('<div class="session-card', `<div class="session-card" style="animation-delay: ${index * 0.05}s"`);
        }).join('');
        
        container.innerHTML = sessionsHTML;

        // Add click listeners to session cards
        container.querySelectorAll('.session-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.session-actions')) {
                    const sessionId = parseInt(card.dataset.sessionId);
                    this.showSessionDetails(sessionId);
                }
            });
        });
    },

    /**
     * Create HTML for a session card
     */
    createSessionCard(session) {
        const statusClass = this.getStatusClass(session.status);
        const statusIcon = this.getStatusIcon(session.status);
        const typeIcon = this.getTypeIcon(session.type);

        return `
            <div class="session-card ${statusClass}" data-session-id="${session.id}">
                <div class="session-header">
                    <div class="session-info">
                        <div class="session-type">
                            <i class="${typeIcon}"></i>
                            <span>${this.formatType(session.type)}</span>
                        </div>
                        <h3>${session.title}</h3>
                        <div class="session-client">
                            <i class="fas fa-user"></i>
                            ${session.clientName}
                            ${session.groupSize ? `<span class="group-size">(${session.groupSize} participants)</span>` : ''}
                        </div>
                    </div>
                    <div class="session-status ${session.status}">
                        <i class="${statusIcon}"></i>
                        <span>${this.formatStatus(session.status)}</span>
                    </div>
                </div>
                
                <div class="session-details">
                    <div class="detail-item">
                        <i class="fas fa-calendar"></i>
                        <span>${this.formatDate(session.date)}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <span>${session.startTime} - ${session.endTime}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-hourglass-half"></i>
                        <span>${session.duration} mins</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${session.location}</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-rupee-sign"></i>
                        <span>₹${session.rate.toLocaleString()}</span>
                    </div>
                </div>

                ${session.description ? `<div class="session-description">${session.description}</div>` : ''}

                <div class="session-actions">
                    ${this.getSessionActions(session)}
                </div>
            </div>
        `;
    },

    /**
     * Get action buttons based on session status
     */
    getSessionActions(session) {
        if (session.status === 'upcoming') {
            return `
                <button class="btn btn-sm btn-primary" onclick="coachSessions.startSession(${session.id})">
                    <i class="fas fa-play"></i> Start
                </button>
                <button class="btn btn-sm btn-secondary" onclick="coachSessions.editSession(${session.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="coachSessions.cancelSession(${session.id})">
                    <i class="fas fa-times"></i> Cancel
                </button>
            `;
        } else if (session.status === 'completed') {
            return `
                <button class="btn btn-sm btn-secondary" onclick="coachSessions.viewReport(${session.id})">
                    <i class="fas fa-file-alt"></i> View Report
                </button>
            `;
        } else if (session.status === 'cancelled') {
            return `
                <span class="cancelled-note">Cancelled: ${session.cancelReason || 'No reason provided'}</span>
            `;
        }
        return '';
    },

    /**
     * Update statistics
     */
    updateStats() {
        const stats = {
            total: this.sessions.length,
            upcoming: this.sessions.filter(s => s.status === 'upcoming').length,
            completed: this.sessions.filter(s => s.status === 'completed').length,
            cancelled: this.sessions.filter(s => s.status === 'cancelled').length
        };

        const totalEl = document.getElementById('totalSessions');
        const upcomingEl = document.getElementById('upcomingSessions');
        const completedEl = document.getElementById('completedSessions');
        const cancelledEl = document.getElementById('cancelledSessions');

        if (totalEl) totalEl.textContent = stats.total;
        if (upcomingEl) upcomingEl.textContent = stats.upcoming;
        if (completedEl) completedEl.textContent = stats.completed;
        if (cancelledEl) cancelledEl.textContent = stats.cancelled;
    },

    /**
     * Show session details modal
     */
    showSessionDetails(sessionId) {
        const session = this.sessions.find(s => s.id === sessionId);
        if (!session) return;

        const modal = document.getElementById('sessionDetailsModal');
        const content = document.getElementById('sessionDetailsContent');

        if (!modal || !content) return;

        content.innerHTML = `
            <div class="session-detail-view">
                <div class="detail-section">
                    <h4>Session Information</h4>
                    <div class="detail-grid">
                        <div class="detail-row">
                            <span class="label">Title:</span>
                            <span class="value">${session.title}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Type:</span>
                            <span class="value">${this.formatType(session.type)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Status:</span>
                            <span class="value status-badge ${session.status}">${this.formatStatus(session.status)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Client:</span>
                            <span class="value">${session.clientName}</span>
                        </div>
                        ${session.groupSize ? `
                        <div class="detail-row">
                            <span class="label">Group Size:</span>
                            <span class="value">${session.groupSize} participants</span>
                        </div>
                        ` : ''}
                    </div>
                </div>

                <div class="detail-section">
                    <h4>Schedule</h4>
                    <div class="detail-grid">
                        <div class="detail-row">
                            <span class="label">Date:</span>
                            <span class="value">${this.formatDate(session.date)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Time:</span>
                            <span class="value">${session.startTime} - ${session.endTime}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Duration:</span>
                            <span class="value">${session.duration} minutes</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Location:</span>
                            <span class="value">${session.location}</span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <h4>Pricing</h4>
                    <div class="detail-row">
                        <span class="label">Session Rate:</span>
                        <span class="value">₹${session.rate.toLocaleString()}</span>
                    </div>
                </div>

                ${session.description ? `
                <div class="detail-section">
                    <h4>Description</h4>
                    <p>${session.description}</p>
                </div>
                ` : ''}

                ${session.equipment ? `
                <div class="detail-section">
                    <h4>Equipment</h4>
                    <p>${session.equipment}</p>
                </div>
                ` : ''}

                ${session.notes ? `
                <div class="detail-section">
                    <h4>Notes</h4>
                    <p>${session.notes}</p>
                </div>
                ` : ''}

                ${session.feedback ? `
                <div class="detail-section">
                    <h4>Session Feedback</h4>
                    <p>${session.feedback}</p>
                </div>
                ` : ''}

                ${session.cancelReason ? `
                <div class="detail-section alert-warning">
                    <h4>Cancellation Reason</h4>
                    <p>${session.cancelReason}</p>
                </div>
                ` : ''}
            </div>
        `;

        modal.style.display = 'flex';
    },

    /**
     * Close modal
     */
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    },

    /**
     * Start a session
     */
    startSession(sessionId) {
        if (confirm('Start this session now?')) {
            console.log('Starting session:', sessionId);
            alert('Session started! (This would redirect to session view in production)');
        }
    },

    /**
     * Edit a session
     */
    editSession(sessionId) {
        console.log('Editing session:', sessionId);
        alert('Edit session functionality (To be implemented)');
    },

    /**
     * Cancel a session
     */
    cancelSession(sessionId) {
        const reason = prompt('Please provide a reason for cancellation:');
        if (reason) {
            const session = this.sessions.find(s => s.id === sessionId);
            if (session) {
                session.status = 'cancelled';
                session.cancelReason = reason;
                session.cancelledAt = new Date().toISOString();
                this.renderSessions();
                this.updateStats();
                alert('Session cancelled successfully');
            }
        }
    },

    /**
     * View session report
     */
    viewReport(sessionId) {
        console.log('Viewing report for session:', sessionId);
        alert('View report functionality (To be implemented)');
    },

    /**
     * Export sessions
     */
    exportSessions() {
        console.log('Exporting sessions...');
        alert('Export functionality (To be implemented)');
    },

    /**
     * Render calendar view
     */
    renderCalendar() {
        const container = document.getElementById('sessionsCalendar');
        if (!container) return;

        container.innerHTML = `
            <div class="calendar-placeholder">
                <i class="fas fa-calendar-alt"></i>
                <p>Calendar view coming soon...</p>
            </div>
        `;
    },

    /**
     * Render timeline view
     */
    renderTimeline() {
        const container = document.getElementById('sessionsTimeline');
        if (!container) return;

        container.innerHTML = `
            <div class="timeline-placeholder">
                <i class="fas fa-stream"></i>
                <p>Timeline view coming soon...</p>
            </div>
        `;
    },

    // Utility functions
    getStatusClass(status) {
        return `session-${status}`;
    },

    getStatusIcon(status) {
        const icons = {
            upcoming: 'fas fa-clock',
            completed: 'fas fa-check-circle',
            cancelled: 'fas fa-times-circle',
            'in-progress': 'fas fa-spinner'
        };
        return icons[status] || 'fas fa-question-circle';
    },

    getTypeIcon(type) {
        const icons = {
            individual: 'fas fa-user',
            group: 'fas fa-users',
            assessment: 'fas fa-clipboard-check',
            consultation: 'fas fa-comment'
        };
        return icons[type] || 'fas fa-dumbbell';
    },

    formatType(type) {
        return type.charAt(0).toUpperCase() + type.slice(1).replace('-', ' ');
    },

    formatStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1).replace('-', ' ');
    },

    formatDate(dateString) {
        const date = new Date(dateString);
        const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    coachSessions.init();
});
