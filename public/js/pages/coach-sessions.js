/**
 * Coach Sessions Management
 * Displays real booking data from database
 */

class CoachSessions {
    constructor() {
        this.sessions = [];
        this.stats = {};
        this.currentView = 'list';
        this.filters = {
            status: '',
            type: '',
            date: '',
            search: ''
        };
        this.init();
    }

    async init() {
        await this.loadSessions();
        this.renderStats();
        this.renderSessions();
        this.attachEventListeners();
    }

    async loadSessions() {
        try {
            const response = await fetch('/api/coach/bookings');

            if (response.status === 401) {
                alert('Please login as a coach to view sessions');
                window.location.href = '/login';
                return;
            }

            const data = await response.json();

            if (data.success) {
                this.sessions = data.bookings || [];
                this.stats = data.stats || {};
                console.log('Loaded sessions:', this.sessions.length);
            } else {
                console.error('Error loading sessions:', data.error);
                alert('Error loading sessions: ' + data.error);
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
            alert('Failed to load sessions. Please try again.');
        }
    }

    renderStats() {
        const stats = this.stats;

        document.getElementById('totalSessions').textContent = stats.total_bookings || 0;
        document.getElementById('upcomingSessions').textContent = (stats.pending_bookings || 0) + (stats.confirmed_bookings || 0);
        document.getElementById('completedSessions').textContent = stats.completed_bookings || 0;
        document.getElementById('cancelledSessions').textContent = stats.cancelled_bookings || 0;
    }

    renderSessions() {
        const container = document.getElementById('sessionsList');

        if (!container) return;

        // Apply filters
        const filteredSessions = this.getFilteredSessions();

        if (filteredSessions.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No sessions found</h3>
                    <p>No training sessions match your current filters</p>
                </div>
            `;
            return;
        }

        // Sort by date (newest first)
        filteredSessions.sort((a, b) => {
            const dateA = new Date(a.booking_date + ' ' + a.start_time);
            const dateB = new Date(b.booking_date + ' ' + b.start_time);
            return dateB - dateA;
        });

        container.innerHTML = filteredSessions.map(session => this.renderSessionCard(session)).join('');
    }

    renderSessionCard(session) {
        const clientName = `${session.first_name} ${session.last_name}`;
        const sessionDate = new Date(session.booking_date);
        const formattedDate = sessionDate.toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });

        // Determine status class and upcoming/past
        const now = new Date();
        const sessionDateTime = new Date(session.booking_date + ' ' + session.start_time);
        const isUpcoming = sessionDateTime > now;
        const statusClass = this.getStatusClass(session.status);

        return `
            <div class="session-card ${statusClass}" data-session-id="${session.id}">
                <div class="session-card-header">
                    <div class="session-info">
                        <div class="session-title">
                            <i class="fas fa-user"></i>
                            <h3>${clientName}</h3>
                        </div>
                        <div class="session-meta">
                            <span class="session-type ${session.session_type}">
                                ${this.getSessionTypeIcon(session.session_type)}
                                ${session.session_type.charAt(0).toUpperCase() + session.session_type.slice(1)}
                            </span>
                            <span class="session-status ${session.status}">
                                ${session.status.charAt(0).toUpperCase() + session.status.slice(1)}
                            </span>
                        </div>
                    </div>
                    <div class="session-actions-menu">
                        <button class="btn-icon" onclick="coachSessions.viewSessionDetails(${session.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="session-details">
                    <div class="detail-item">
                        <i class="fas fa-calendar"></i>
                        <div>
                            <strong>Date</strong>
                            <span>${formattedDate}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Time</strong>
                            <span>${session.start_time} - ${session.end_time}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-hourglass-half"></i>
                        <div>
                            <strong>Duration</strong>
                            <span>${session.duration_hours} hour${session.duration_hours > 1 ? 's' : ''}</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-dollar-sign"></i>
                        <div>
                            <strong>Fee</strong>
                            <span>LKR ${parseFloat(session.total_amount).toLocaleString()}</span>
                        </div>
                    </div>
                </div>

                ${session.special_requests ? `
                    <div class="session-notes">
                        <i class="fas fa-comment-dots"></i>
                        <span><strong>Client Notes:</strong> ${session.special_requests}</span>
                    </div>
                ` : ''}

                <div class="session-client-contact">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:${session.email}">${session.email}</a>
                    </div>
                    ${session.phone ? `
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="tel:${session.phone}">${session.phone}</a>
                        </div>
                    ` : ''}
                </div>

                <div class="session-card-footer">
                    ${isUpcoming && (session.status === 'pending' || session.status === 'confirmed') ? `
                        <button class="btn btn-sm btn-success" onclick="coachSessions.markAsCompleted(${session.id})">
                            <i class="fas fa-check"></i> Mark Complete
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="coachSessions.cancelSession(${session.id})">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline" onclick="coachSessions.contactClient('${session.email}')">
                        <i class="fas fa-envelope"></i> Contact Client
                    </button>
                </div>
            </div>
        `;
    }

    getSessionTypeIcon(type) {
        const icons = {
            'individual': '<i class="fas fa-user"></i>',
            'group': '<i class="fas fa-users"></i>',
            'assessment': '<i class="fas fa-clipboard-check"></i>'
        };
        return icons[type] || '<i class="fas fa-dumbbell"></i>';
    }

    getStatusClass(status) {
        const classes = {
            'pending': 'status-pending',
            'confirmed': 'status-confirmed',
            'completed': 'status-completed',
            'cancelled': 'status-cancelled'
        };
        return classes[status] || '';
    }

    getFilteredSessions() {
        return this.sessions.filter(session => {
            // Status filter
            if (this.filters.status) {
                if (this.filters.status === 'upcoming' &&
                    (session.status !== 'pending' && session.status !== 'confirmed')) {
                    return false;
                }
                if (this.filters.status !== 'upcoming' && session.status !== this.filters.status) {
                    return false;
                }
            }

            // Type filter
            if (this.filters.type && session.session_type !== this.filters.type) {
                return false;
            }

            // Date filter
            if (this.filters.date && session.booking_date !== this.filters.date) {
                return false;
            }

            // Search filter
            if (this.filters.search) {
                const searchLower = this.filters.search.toLowerCase();
                const clientName = `${session.first_name} ${session.last_name}`.toLowerCase();
                const email = session.email.toLowerCase();

                if (!clientName.includes(searchLower) && !email.includes(searchLower)) {
                    return false;
                }
            }

            return true;
        });
    }

    attachEventListeners() {
        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.filters.status = e.target.value;
                this.renderSessions();
            });
        }

        // Type filter
        const typeFilter = document.getElementById('typeFilter');
        if (typeFilter) {
            typeFilter.addEventListener('change', (e) => {
                this.filters.type = e.target.value;
                this.renderSessions();
            });
        }

        // Date filter
        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            dateFilter.addEventListener('change', (e) => {
                this.filters.date = e.target.value;
                this.renderSessions();
            });
        }

        // Search
        const searchInput = document.getElementById('sessionSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filters.search = e.target.value;
                this.renderSessions();
            });
        }

        // Clear filters
        const clearBtn = document.getElementById('clearFilters');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.filters = { status: '', type: '', date: '', search: '' };
                if (statusFilter) statusFilter.value = '';
                if (typeFilter) typeFilter.value = '';
                if (dateFilter) dateFilter.value = '';
                if (searchInput) searchInput.value = '';
                this.renderSessions();
            });
        }

        // Modal close buttons
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.modal').style.display = 'none';
            });
        });
    }

    viewSessionDetails(sessionId) {
        const session = this.sessions.find(s => s.id === sessionId);
        if (!session) return;

        const clientName = `${session.first_name} ${session.last_name}`;
        const details = `
            <div class="session-details-view">
                <h4>Client: ${clientName}</h4>
                <p><strong>Email:</strong> ${session.email}</p>
                ${session.phone ? `<p><strong>Phone:</strong> ${session.phone}</p>` : ''}
                <p><strong>Date:</strong> ${new Date(session.booking_date).toLocaleDateString()}</p>
                <p><strong>Time:</strong> ${session.start_time} - ${session.end_time}</p>
                <p><strong>Duration:</strong> ${session.duration_hours} hour(s)</p>
                <p><strong>Type:</strong> ${session.session_type}</p>
                <p><strong>Fee:</strong> LKR ${parseFloat(session.total_amount).toLocaleString()}</p>
                <p><strong>Status:</strong> ${session.status}</p>
                ${session.special_requests ? `<p><strong>Notes:</strong> ${session.special_requests}</p>` : ''}
                <p><strong>Booking ID:</strong> #${session.id}</p>
                <p><strong>Booked on:</strong> ${new Date(session.created_at).toLocaleString()}</p>
            </div>
        `;

        const modal = document.getElementById('sessionDetailsModal');
        const content = document.getElementById('sessionDetailsContent');
        if (modal && content) {
            content.innerHTML = details;
            modal.style.display = 'flex';
        } else {
            alert(details.replace(/<[^>]*>/g, '\n'));
        }
    }

    async markAsCompleted(sessionId) {
        if (!confirm('Mark this session as completed?')) return;

        try {
            const response = await fetch(`/api/coach/bookings/${sessionId}/complete`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' }
            });

            const data = await response.json();

            if (data.success) {
                alert('Session marked as completed');
                await this.loadSessions();
                this.renderStats();
                this.renderSessions();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to update session');
        }
    }

    async cancelSession(sessionId) {
        if (!confirm('Are you sure you want to cancel this session?')) return;

        try {
            const response = await fetch(`/api/coach/bookings/${sessionId}/cancel`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' }
            });

            const data = await response.json();

            if (data.success) {
                alert('Session cancelled');
                await this.loadSessions();
                this.renderStats();
                this.renderSessions();
            } else {
                alert('Error: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to cancel session');
        }
    }

    contactClient(email) {
        window.location.href = `mailto:${email}`;
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }
}

// Initialize when DOM is ready
let coachSessions;
document.addEventListener('DOMContentLoaded', () => {
    coachSessions = new CoachSessions();
});
