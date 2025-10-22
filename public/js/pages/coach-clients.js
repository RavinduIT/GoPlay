/**
 * Coach Clients Management
 * Handles client display, filtering, and interactions
 */

const coachClients = {
    clients: [],
    filteredClients: [],
    currentView: 'grid',

    /**
     * Initialize the clients page
     */
    init() {
        console.log('Initializing Coach Clients...');
        this.loadFakeClients();
        this.setupEventListeners();
        this.renderClients();
        this.updateStats();
    },

    /**
     * Load fake clients data
     */
    loadFakeClients() {
        this.clients = [
            {
                id: 1,
                name: 'Kamal Perera',
                email: 'kamal.perera@email.com',
                phone: '+94 71 234 5678',
                age: 24,
                gender: 'male',
                avatar: '/public/assets/images/avatar-1.jpg',
                program: 'cricket-fundamentals',
                programName: 'Cricket Fundamentals',
                experienceLevel: 'beginner',
                sessionType: 'individual',
                goals: 'Improve batting technique and timing',
                status: 'active',
                progress: 65,
                totalSessions: 12,
                completedSessions: 8,
                joinedDate: '2024-09-15',
                lastSession: '2025-10-20',
                nextSession: '2025-10-24',
                medicalConditions: 'None',
                notes: 'Very dedicated student, shows great improvement',
                skills: {
                    batting: 7,
                    bowling: 5,
                    fielding: 6,
                    fitness: 8
                }
            },
            {
                id: 2,
                name: 'Nimal Silva',
                email: 'nimal.silva@email.com',
                phone: '+94 77 345 6789',
                age: 19,
                gender: 'male',
                avatar: '/public/assets/images/avatar-2.jpg',
                program: 'advanced-techniques',
                programName: 'Advanced Techniques',
                experienceLevel: 'intermediate',
                sessionType: 'individual',
                goals: 'Master fast bowling techniques',
                status: 'active',
                progress: 78,
                totalSessions: 20,
                completedSessions: 16,
                joinedDate: '2024-07-10',
                lastSession: '2025-10-19',
                nextSession: '2025-10-23',
                medicalConditions: 'Previous shoulder injury - fully recovered',
                notes: 'Natural talent, needs to work on consistency',
                skills: {
                    batting: 6,
                    bowling: 9,
                    fielding: 7,
                    fitness: 8
                }
            },
            {
                id: 3,
                name: 'Saman Fernando',
                email: 'saman.fernando@email.com',
                phone: '+94 76 456 7890',
                age: 16,
                gender: 'male',
                avatar: '/public/assets/images/avatar-3.jpg',
                program: 'youth-development',
                programName: 'Youth Development',
                experienceLevel: 'beginner',
                sessionType: 'group',
                goals: 'Build overall cricket skills',
                status: 'active',
                progress: 45,
                totalSessions: 8,
                completedSessions: 4,
                joinedDate: '2024-10-01',
                lastSession: '2025-10-18',
                nextSession: '2025-10-25',
                medicalConditions: 'Asthma - uses inhaler',
                notes: 'New student, learning basics, very enthusiastic',
                skills: {
                    batting: 4,
                    bowling: 5,
                    fielding: 4,
                    fitness: 5
                }
            },
            {
                id: 4,
                name: 'Chaminda Rajapaksa',
                email: 'chaminda.r@email.com',
                phone: '+94 75 567 8901',
                age: 28,
                gender: 'male',
                avatar: '/public/assets/images/avatar-4.jpg',
                program: 'professional-training',
                programName: 'Professional Training',
                experienceLevel: 'advanced',
                sessionType: 'individual',
                goals: 'Prepare for national team trials',
                status: 'active',
                progress: 85,
                totalSessions: 30,
                completedSessions: 26,
                joinedDate: '2024-05-20',
                lastSession: '2025-10-21',
                nextSession: '2025-10-26',
                medicalConditions: 'None',
                notes: 'Elite player, focused on match strategy',
                skills: {
                    batting: 9,
                    bowling: 7,
                    fielding: 9,
                    fitness: 9
                }
            },
            {
                id: 5,
                name: 'Dinesh Jayawardena',
                email: 'dinesh.j@email.com',
                phone: '+94 74 678 9012',
                age: 22,
                gender: 'male',
                avatar: '/public/assets/images/avatar-5.jpg',
                program: 'cricket-fundamentals',
                programName: 'Cricket Fundamentals',
                experienceLevel: 'intermediate',
                sessionType: 'individual',
                goals: 'Improve spin bowling variations',
                status: 'active',
                progress: 72,
                totalSessions: 15,
                completedSessions: 11,
                joinedDate: '2024-08-12',
                lastSession: '2025-10-17',
                nextSession: '2025-10-27',
                medicalConditions: 'None',
                notes: 'Good technique, needs mental game training',
                skills: {
                    batting: 5,
                    bowling: 8,
                    fielding: 6,
                    fitness: 7
                }
            },
            {
                id: 6,
                name: 'Ruwan Wijesinghe',
                email: 'ruwan.w@email.com',
                phone: '+94 73 789 0123',
                age: 17,
                gender: 'male',
                avatar: '/public/assets/images/avatar-6.jpg',
                program: 'youth-development',
                programName: 'Youth Development',
                experienceLevel: 'beginner',
                sessionType: 'individual',
                goals: 'Learn wicket keeping basics',
                status: 'trial',
                progress: 30,
                totalSessions: 4,
                completedSessions: 2,
                joinedDate: '2024-10-10',
                lastSession: '2025-10-15',
                nextSession: '2025-10-29',
                medicalConditions: 'None',
                notes: 'Trial period - deciding on commitment',
                skills: {
                    batting: 4,
                    bowling: 3,
                    fielding: 5,
                    fitness: 6
                }
            },
            {
                id: 7,
                name: 'Asanka Gunawardena',
                email: 'asanka.g@email.com',
                phone: '+94 72 890 1234',
                age: 26,
                gender: 'male',
                avatar: '/public/assets/images/avatar-7.jpg',
                program: 'advanced-techniques',
                programName: 'Advanced Techniques',
                experienceLevel: 'advanced',
                sessionType: 'individual',
                goals: 'Refine batting against spin',
                status: 'active',
                progress: 88,
                totalSessions: 25,
                completedSessions: 22,
                joinedDate: '2024-06-05',
                lastSession: '2025-10-21',
                nextSession: '2025-10-28',
                medicalConditions: 'Previous knee surgery - fully recovered',
                notes: 'Excellent progress, very consistent',
                skills: {
                    batting: 9,
                    bowling: 6,
                    fielding: 8,
                    fitness: 8
                }
            },
            {
                id: 8,
                name: 'Thilina Bandara',
                email: 'thilina.b@email.com',
                phone: '+94 71 901 2345',
                age: 20,
                gender: 'male',
                avatar: '/public/assets/images/avatar-8.jpg',
                program: 'cricket-fundamentals',
                programName: 'Cricket Fundamentals',
                experienceLevel: 'intermediate',
                sessionType: 'individual',
                goals: 'Increase bowling pace and accuracy',
                status: 'active',
                progress: 68,
                totalSessions: 18,
                completedSessions: 12,
                joinedDate: '2024-07-28',
                lastSession: '2025-10-20',
                nextSession: '2025-10-30',
                medicalConditions: 'None',
                notes: 'Fast learner, good work ethic',
                skills: {
                    batting: 5,
                    bowling: 8,
                    fielding: 6,
                    fitness: 7
                }
            }
        ];

        this.filteredClients = [...this.clients];
        console.log('Loaded', this.clients.length, 'clients');
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
        const programFilter = document.getElementById('programFilter');
        const sortFilter = document.getElementById('sortFilter');
        const searchInput = document.getElementById('clientSearch');

        if (statusFilter) statusFilter.addEventListener('change', () => this.applyFilters());
        if (programFilter) programFilter.addEventListener('change', () => this.applyFilters());
        if (sortFilter) sortFilter.addEventListener('change', () => this.applyFilters());
        if (searchInput) searchInput.addEventListener('input', () => this.applyFilters());

        // Add client button
        const addClientBtn = document.getElementById('addClientBtn');
        if (addClientBtn) {
            addClientBtn.addEventListener('click', () => this.openModal('addClientModal'));
        }

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
        document.querySelectorAll('.clients-view').forEach(viewEl => {
            viewEl.classList.remove('active');
        });

        const viewElement = document.getElementById(view + 'View');
        if (viewElement) {
            viewElement.classList.add('active');
        }

        // Render appropriate view
        this.renderClients();
    },

    /**
     * Apply filters to clients
     */
    applyFilters() {
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const programFilter = document.getElementById('programFilter')?.value || '';
        const sortFilter = document.getElementById('sortFilter')?.value || 'name';
        const searchQuery = document.getElementById('clientSearch')?.value.toLowerCase() || '';

        this.filteredClients = this.clients.filter(client => {
            // Status filter
            if (statusFilter && client.status !== statusFilter) return false;

            // Program filter
            if (programFilter && client.program !== programFilter) return false;

            // Search filter
            if (searchQuery) {
                const searchableText = `${client.name} ${client.email} ${client.programName}`.toLowerCase();
                if (!searchableText.includes(searchQuery)) return false;
            }

            return true;
        });

        // Apply sorting
        this.filteredClients.sort((a, b) => {
            switch (sortFilter) {
                case 'name':
                    return a.name.localeCompare(b.name);
                case 'joined':
                    return new Date(b.joinedDate) - new Date(a.joinedDate);
                case 'progress':
                    return b.progress - a.progress;
                case 'sessions':
                    return b.totalSessions - a.totalSessions;
                default:
                    return 0;
            }
        });

        this.renderClients();
    },

    /**
     * Render clients based on current view
     */
    renderClients() {
        if (this.currentView === 'grid') {
            this.renderGridView();
        } else {
            this.renderListView();
        }
    },

    /**
     * Render grid view
     */
    renderGridView() {
        const container = document.getElementById('clientsGrid');
        if (!container) return;

        if (this.filteredClients.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-user-friends"></i>
                    <h3>No Clients Found</h3>
                    <p>Try adjusting your filters or add a new client</p>
                </div>
            `;
            return;
        }

        const clientsHTML = this.filteredClients.map(client => this.createClientCard(client)).join('');
        container.innerHTML = clientsHTML;

        // Add click listeners
        container.querySelectorAll('.client-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.client-actions')) {
                    const clientId = parseInt(card.dataset.clientId);
                    this.showClientDetails(clientId);
                }
            });
        });
    },

    /**
     * Create HTML for a client card
     */
    createClientCard(client) {
        const statusClass = this.getStatusClass(client.status);
        const progressColor = this.getProgressColor(client.progress);

        return `
            <div class="client-card ${statusClass}" data-client-id="${client.id}">
                <div class="client-card-header">
                    <div class="client-avatar">
                        <img src="${client.avatar}" alt="${client.name}">
                        <span class="status-indicator ${client.status}"></span>
                    </div>
                    <div class="client-status-badge ${client.status}">
                        ${this.formatStatus(client.status)}
                    </div>
                </div>
                
                <div class="client-card-body">
                    <h3 class="client-name">${client.name}</h3>
                    <p class="client-program">
                        <i class="fas fa-graduation-cap"></i>
                        ${client.programName}
                    </p>
                    <p class="client-level">
                        <i class="fas fa-chart-line"></i>
                        ${this.formatExperienceLevel(client.experienceLevel)}
                    </p>
                    
                    <div class="client-progress">
                        <div class="progress-header">
                            <span>Progress</span>
                            <span class="progress-value">${client.progress}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${client.progress}%; background: ${progressColor};"></div>
                        </div>
                    </div>
                    
                    <div class="client-stats">
                        <div class="stat-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>${client.completedSessions}/${client.totalSessions}</span>
                            <small>Sessions</small>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <span>${this.formatDate(client.lastSession)}</span>
                            <small>Last Session</small>
                        </div>
                    </div>
                </div>
                
                <div class="client-actions">
                    <button class="btn btn-sm btn-primary" onclick="coachClients.viewClient(${client.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="coachClients.trackProgress(${client.id})">
                        <i class="fas fa-chart-bar"></i>
                    </button>
                    <button class="btn btn-sm btn-success" onclick="coachClients.scheduleSession(${client.id})">
                        <i class="fas fa-calendar-plus"></i>
                    </button>
                </div>
            </div>
        `;
    },

    /**
     * Render list view
     */
    renderListView() {
        const tbody = document.querySelector('#clientsTable tbody');
        if (!tbody) return;

        if (this.filteredClients.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state-row">
                        <i class="fas fa-user-friends"></i>
                        <p>No clients found</p>
                    </td>
                </tr>
            `;
            return;
        }

        const rowsHTML = this.filteredClients.map(client => this.createClientRow(client)).join('');
        tbody.innerHTML = rowsHTML;
    },

    /**
     * Create HTML for a table row
     */
    createClientRow(client) {
        const progressColor = this.getProgressColor(client.progress);

        return `
            <tr data-client-id="${client.id}" onclick="coachClients.showClientDetails(${client.id})">
                <td>
                    <div class="client-info">
                        <img src="${client.avatar}" alt="${client.name}" class="client-avatar-small">
                        <div>
                            <div class="client-name-small">${client.name}</div>
                            <div class="client-email-small">${client.email}</div>
                        </div>
                    </div>
                </td>
                <td>${client.programName}</td>
                <td>
                    <div class="progress-bar-small">
                        <div class="progress-fill" style="width: ${client.progress}%; background: ${progressColor};"></div>
                    </div>
                    <span class="progress-text">${client.progress}%</span>
                </td>
                <td>${client.completedSessions}/${client.totalSessions}</td>
                <td>${this.formatDate(client.lastSession)}</td>
                <td>
                    <span class="status-badge ${client.status}">
                        ${this.formatStatus(client.status)}
                    </span>
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); coachClients.viewClient(${client.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); coachClients.trackProgress(${client.id})">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    },

    /**
     * Show client details modal
     */
    showClientDetails(clientId) {
        const client = this.clients.find(c => c.id === clientId);
        if (!client) return;

        const modal = document.getElementById('clientDetailsModal');
        const content = document.getElementById('clientDetailsContent');

        if (!modal || !content) return;

        content.innerHTML = `
            <div class="client-details-view">
                <div class="client-details-header">
                    <img src="${client.avatar}" alt="${client.name}" class="client-avatar-large">
                    <div class="client-header-info">
                        <h2>${client.name}</h2>
                        <span class="status-badge ${client.status}">${this.formatStatus(client.status)}</span>
                        <p class="client-joined">Member since ${this.formatDate(client.joinedDate)}</p>
                    </div>
                </div>

                <div class="details-grid">
                    <div class="detail-section">
                        <h4><i class="fas fa-user"></i> Personal Information</h4>
                        <div class="detail-row">
                            <span class="label">Email:</span>
                            <span class="value">${client.email}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Phone:</span>
                            <span class="value">${client.phone}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Age:</span>
                            <span class="value">${client.age} years</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Gender:</span>
                            <span class="value">${this.formatGender(client.gender)}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4><i class="fas fa-graduation-cap"></i> Training Information</h4>
                        <div class="detail-row">
                            <span class="label">Program:</span>
                            <span class="value">${client.programName}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Experience Level:</span>
                            <span class="value">${this.formatExperienceLevel(client.experienceLevel)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Session Type:</span>
                            <span class="value">${this.formatSessionType(client.sessionType)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Goals:</span>
                            <span class="value">${client.goals}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4><i class="fas fa-chart-line"></i> Progress & Stats</h4>
                        <div class="detail-row">
                            <span class="label">Overall Progress:</span>
                            <span class="value">${client.progress}%</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Total Sessions:</span>
                            <span class="value">${client.totalSessions}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Completed:</span>
                            <span class="value">${client.completedSessions}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Last Session:</span>
                            <span class="value">${this.formatDate(client.lastSession)}</span>
                        </div>
                        ${client.nextSession ? `
                        <div class="detail-row">
                            <span class="label">Next Session:</span>
                            <span class="value">${this.formatDate(client.nextSession)}</span>
                        </div>
                        ` : ''}
                    </div>

                    <div class="detail-section">
                        <h4><i class="fas fa-dumbbell"></i> Skills Assessment</h4>
                        ${Object.entries(client.skills).map(([skill, value]) => `
                            <div class="skill-row">
                                <span class="skill-label">${this.formatSkillName(skill)}</span>
                                <div class="skill-bar">
                                    <div class="skill-fill" style="width: ${value * 10}%;"></div>
                                </div>
                                <span class="skill-score">${value}/10</span>
                            </div>
                        `).join('')}
                    </div>

                    ${client.medicalConditions !== 'None' ? `
                    <div class="detail-section alert-warning">
                        <h4><i class="fas fa-heartbeat"></i> Medical Information</h4>
                        <p>${client.medicalConditions}</p>
                    </div>
                    ` : ''}

                    ${client.notes ? `
                    <div class="detail-section">
                        <h4><i class="fas fa-sticky-note"></i> Notes</h4>
                        <p>${client.notes}</p>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;

        this.openModal('clientDetailsModal');
    },

    /**
     * View client (wrapper for showClientDetails)
     */
    viewClient(clientId) {
        this.showClientDetails(clientId);
    },

    /**
     * Track progress
     */
    trackProgress(clientId) {
        const client = this.clients.find(c => c.id === clientId);
        if (!client) return;

        console.log('Opening progress tracking for:', client.name);
        this.openModal('progressModal');
    },

    /**
     * Schedule session
     */
    scheduleSession(clientId) {
        const client = this.clients.find(c => c.id === clientId);
        if (!client) return;

        console.log('Scheduling session for:', client.name);
        alert(`Schedule session functionality for ${client.name}\n(To be implemented)`);
    },

    /**
     * Save new client
     */
    saveClient() {
        const form = document.getElementById('addClientForm');
        if (!form) return;

        console.log('Saving new client...');
        alert('Add client functionality (To be implemented)');
        this.closeModal('addClientModal');
    },

    /**
     * Save progress
     */
    saveProgress() {
        const form = document.getElementById('progressForm');
        if (!form) return;

        console.log('Saving progress...');
        alert('Save progress functionality (To be implemented)');
        this.closeModal('progressModal');
    },

    /**
     * Open modal
     */
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
        }
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
     * Update statistics
     */
    updateStats() {
        const stats = {
            total: this.clients.length,
            active: this.clients.filter(c => c.status === 'active').length,
            newThisMonth: this.clients.filter(c => {
                const joined = new Date(c.joinedDate);
                const now = new Date();
                return joined.getMonth() === now.getMonth() && joined.getFullYear() === now.getFullYear();
            }).length
        };

        const totalEl = document.getElementById('totalClients');
        const activeEl = document.getElementById('activeClients');
        const newEl = document.getElementById('newClients');

        if (totalEl) totalEl.textContent = stats.total;
        if (activeEl) activeEl.textContent = stats.active;
        if (newEl) newEl.textContent = stats.newThisMonth;
    },

    // Utility functions
    getStatusClass(status) {
        return `client-${status}`;
    },

    getProgressColor(progress) {
        if (progress >= 80) return '#10b981';
        if (progress >= 60) return '#3b82f6';
        if (progress >= 40) return '#f59e0b';
        return '#ef4444';
    },

    formatStatus(status) {
        const statuses = {
            active: 'Active',
            inactive: 'Inactive',
            trial: 'Trial',
            suspended: 'Suspended'
        };
        return statuses[status] || status;
    },

    formatExperienceLevel(level) {
        const levels = {
            beginner: 'Beginner',
            intermediate: 'Intermediate',
            advanced: 'Advanced',
            professional: 'Professional'
        };
        return levels[level] || level;
    },

    formatSessionType(type) {
        const types = {
            individual: 'Individual',
            group: 'Group',
            both: 'Both'
        };
        return types[type] || type;
    },

    formatGender(gender) {
        const genders = {
            male: 'Male',
            female: 'Female',
            other: 'Other'
        };
        return genders[gender] || gender;
    },

    formatSkillName(skill) {
        return skill.charAt(0).toUpperCase() + skill.slice(1);
    },

    formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    coachClients.init();
});
