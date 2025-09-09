class MaintenanceManager {
    constructor() {
        this.currentView = 'tasks';
        this.maintenanceTasks = [];
        this.inspections = [];
        this.maintenanceRecords = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadMaintenanceData();
        this.renderCurrentView();
        this.updateSummaryStats();
    }

    bindEvents() {
        // View switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.target.dataset.tab;
                this.switchView(view);
            });
        });

        // Action buttons
        document.getElementById('addTaskBtn').addEventListener('click', () => this.openAddTaskModal());
        document.getElementById('scheduleInspectionBtn').addEventListener('click', () => this.openScheduleInspectionModal());
        document.getElementById('exportReportBtn').addEventListener('click', () => this.exportMaintenanceReport());

        // Filter and search
        document.getElementById('statusFilter').addEventListener('change', () => this.filterTasks());
        document.getElementById('priorityFilter').addEventListener('change', () => this.filterTasks());
        document.getElementById('maintenanceSearch').addEventListener('input', () => this.filterTasks());

        // Modal events
        document.getElementById('saveTask').addEventListener('click', () => this.saveMaintenanceTask());
        document.getElementById('saveInspection').addEventListener('click', () => this.saveInspection());

        // Close modals
        document.querySelectorAll('.modal-close, .btn-secondary').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) this.closeModal(modal);
            });
        });

        // Calendar navigation
        document.getElementById('prevMonth').addEventListener('click', () => this.navigateMonth(-1));
        document.getElementById('nextMonth').addEventListener('click', () => this.navigateMonth(1));
    }

    switchView(view) {
        this.currentView = view;
        
        // Update active tab
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-tab="${view}"]`).classList.add('active');
        
        // Show/hide content sections
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.getElementById(`${view}Tab`).classList.add('active');
        
        this.renderCurrentView();
    }

    renderCurrentView() {
        switch (this.currentView) {
            case 'tasks':
                this.renderTasksView();
                break;
            case 'calendar':
                this.renderCalendarView();
                break;
            case 'costs':
                this.renderCostsView();
                break;
            case 'health':
                this.renderHealthView();
                break;
        }
    }

    renderTasksView() {
        const container = document.getElementById('tasksContainer');
        const filteredTasks = this.getFilteredTasks();
        
        if (filteredTasks.length === 0) {
            container.innerHTML = `
                <div class="no-data">
                    <i class="fas fa-tasks"></i>
                    <h3>No maintenance tasks</h3>
                    <p>Create your first maintenance task to get started.</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = filteredTasks.map(task => `
            <div class="task-card ${task.status}" data-task-id="${task.id}">
                <div class="task-header">
                    <div class="task-info">
                        <h4>${task.title}</h4>
                        <div class="task-meta">
                            <span class="task-ground">
                                <i class="fas fa-map-marker-alt"></i>
                                ${task.groundName}
                            </span>
                            <span class="task-priority priority-${task.priority}">
                                <i class="fas fa-flag"></i>
                                ${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}
                            </span>
                        </div>
                    </div>
                    <div class="task-status">
                        <span class="status-badge status-${task.status}">${this.getStatusLabel(task.status)}</span>
                        <div class="task-actions">
                            <button class="btn-action" onclick="maintenanceManager.editTask(${task.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action" onclick="maintenanceManager.deleteTask(${task.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="task-content">
                    <p class="task-description">${task.description}</p>
                    
                    <div class="task-details">
                        <div class="detail-item">
                            <strong>Due Date:</strong>
                            <span class="due-date ${this.isDueDate(task.dueDate) ? 'overdue' : ''}">${task.dueDate}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Assigned To:</strong>
                            <span>${task.assignedTo || 'Unassigned'}</span>
                        </div>
                        <div class="detail-item">
                            <strong>Estimated Cost:</strong>
                            <span>₹${task.estimatedCost}</span>
                        </div>
                    </div>
                    
                    ${task.status === 'pending' ? `
                        <div class="task-quick-actions">
                            <button class="btn btn-primary" onclick="maintenanceManager.startTask(${task.id})">
                                <i class="fas fa-play"></i> Start Task
                            </button>
                            <button class="btn btn-secondary" onclick="maintenanceManager.postponeTask(${task.id})">
                                <i class="fas fa-clock"></i> Postpone
                            </button>
                        </div>
                    ` : ''}
                    
                    ${task.status === 'in_progress' ? `
                        <div class="task-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${task.progress || 0}%"></div>
                            </div>
                            <span class="progress-text">${task.progress || 0}% Complete</span>
                            <button class="btn btn-success" onclick="maintenanceManager.completeTask(${task.id})">
                                <i class="fas fa-check"></i> Complete
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    renderCalendarView() {
        const now = new Date();
        const currentMonth = now.getMonth();
        const currentYear = now.getFullYear();
        
        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        const container = document.getElementById('maintenanceCalendar');
        let html = '<div class="calendar-grid">';
        
        // Day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });
        
        // Calendar days
        let currentDate = new Date(startDate);
        for (let week = 0; week < 6; week++) {
            for (let day = 0; day < 7; day++) {
                const isCurrentMonth = currentDate.getMonth() === currentMonth;
                const dayTasks = this.getTasksForDate(currentDate);
                const dayInspections = this.getInspectionsForDate(currentDate);
                
                html += `
                    <div class="calendar-day ${!isCurrentMonth ? 'other-month' : ''}" 
                         data-date="${currentDate.toISOString().split('T')[0]}">
                        <div class="day-number">${currentDate.getDate()}</div>
                        <div class="day-events">
                            ${dayTasks.slice(0, 2).map(task => `
                                <div class="event-item task-event priority-${task.priority}" 
                                     onclick="maintenanceManager.showTaskDetails(${task.id})">
                                    ${task.title}
                                </div>
                            `).join('')}
                            ${dayInspections.slice(0, 1).map(inspection => `
                                <div class="event-item inspection-event"
                                     onclick="maintenanceManager.showInspectionDetails(${inspection.id})">
                                    <i class="fas fa-search"></i> ${inspection.type}
                                </div>
                            `).join('')}
                            ${(dayTasks.length + dayInspections.length) > 3 ? `
                                <div class="more-events">+${(dayTasks.length + dayInspections.length) - 3} more</div>
                            ` : ''}
                        </div>
                    </div>
                `;
                currentDate.setDate(currentDate.getDate() + 1);
            }
        }
        
        html += '</div>';
        container.innerHTML = html;
        
        document.getElementById('currentMonthYear').textContent = 
            now.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    }

    renderCostsView() {
        const container = document.getElementById('costsAnalysis');
        const monthlyData = this.calculateMonthlyCosts();
        const categoryData = this.calculateCategoryCosts();
        
        container.innerHTML = `
            <div class="costs-summary">
                <div class="cost-card">
                    <h4>This Month</h4>
                    <div class="cost-amount">₹${monthlyData.current.toLocaleString()}</div>
                    <div class="cost-change ${monthlyData.change >= 0 ? 'positive' : 'negative'}">
                        <i class="fas fa-arrow-${monthlyData.change >= 0 ? 'up' : 'down'}"></i>
                        ${Math.abs(monthlyData.change)}% from last month
                    </div>
                </div>
                
                <div class="cost-card">
                    <h4>Year to Date</h4>
                    <div class="cost-amount">₹${monthlyData.yearTotal.toLocaleString()}</div>
                    <div class="cost-subtitle">Total maintenance spending</div>
                </div>
                
                <div class="cost-card">
                    <h4>Average per Ground</h4>
                    <div class="cost-amount">₹${monthlyData.avgPerGround.toLocaleString()}</div>
                    <div class="cost-subtitle">Monthly average</div>
                </div>
            </div>
            
            <div class="costs-charts">
                <div class="chart-container">
                    <h4>Monthly Costs Trend</h4>
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
                
                <div class="chart-container">
                    <h4>Costs by Category</h4>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            
            <div class="cost-breakdown">
                <h4>Recent Expenses</h4>
                <div class="expenses-list">
                    ${this.maintenanceRecords.slice(0, 10).map(record => `
                        <div class="expense-item">
                            <div class="expense-info">
                                <div class="expense-title">${record.description}</div>
                                <div class="expense-details">
                                    <span>${record.groundName}</span> • 
                                    <span>${record.date}</span> • 
                                    <span class="expense-category">${record.category}</span>
                                </div>
                            </div>
                            <div class="expense-amount">₹${record.amount.toLocaleString()}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        // Initialize charts
        setTimeout(() => this.initializeCostCharts(), 100);
    }

    renderHealthView() {
        const container = document.getElementById('healthAnalysis');
        const healthData = this.calculateGroundHealth();
        
        container.innerHTML = `
            <div class="health-overview">
                <div class="health-card">
                    <h4>Overall Health Score</h4>
                    <div class="health-score ${this.getHealthClass(healthData.overall)}">
                        ${healthData.overall}%
                    </div>
                    <div class="health-status">${this.getHealthStatus(healthData.overall)}</div>
                </div>
                
                <div class="health-metrics">
                    <div class="metric">
                        <span class="metric-label">Maintenance Compliance</span>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: ${healthData.compliance}%"></div>
                        </div>
                        <span class="metric-value">${healthData.compliance}%</span>
                    </div>
                    
                    <div class="metric">
                        <span class="metric-label">Safety Rating</span>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: ${healthData.safety}%"></div>
                        </div>
                        <span class="metric-value">${healthData.safety}%</span>
                    </div>
                    
                    <div class="metric">
                        <span class="metric-label">Equipment Condition</span>
                        <div class="metric-bar">
                            <div class="metric-fill" style="width: ${healthData.equipment}%"></div>
                        </div>
                        <span class="metric-value">${healthData.equipment}%</span>
                    </div>
                </div>
            </div>
            
            <div class="grounds-health-list">
                <h4>Ground Health Details</h4>
                ${healthData.grounds.map(ground => `
                    <div class="ground-health-card">
                        <div class="ground-info">
                            <h5>${ground.name}</h5>
                            <div class="ground-meta">
                                <span>${ground.sport}</span> • 
                                <span>Last inspection: ${ground.lastInspection}</span>
                            </div>
                        </div>
                        
                        <div class="health-indicators">
                            <div class="indicator">
                                <span class="indicator-label">Surface</span>
                                <div class="indicator-score ${this.getHealthClass(ground.surface)}">${ground.surface}%</div>
                            </div>
                            <div class="indicator">
                                <span class="indicator-label">Facilities</span>
                                <div class="indicator-score ${this.getHealthClass(ground.facilities)}">${ground.facilities}%</div>
                            </div>
                            <div class="indicator">
                                <span class="indicator-label">Safety</span>
                                <div class="indicator-score ${this.getHealthClass(ground.safety)}">${ground.safety}%</div>
                            </div>
                        </div>
                        
                        <div class="ground-actions">
                            <button class="btn btn-primary" onclick="maintenanceManager.scheduleInspection(${ground.id})">
                                <i class="fas fa-calendar-plus"></i> Schedule Inspection
                            </button>
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <div class="health-recommendations">
                <h4>Maintenance Recommendations</h4>
                <div class="recommendations-list">
                    ${this.generateRecommendations().map(rec => `
                        <div class="recommendation-item priority-${rec.priority}">
                            <div class="rec-header">
                                <div class="rec-title">${rec.title}</div>
                                <div class="rec-priority">${rec.priority.toUpperCase()}</div>
                            </div>
                            <div class="rec-description">${rec.description}</div>
                            <div class="rec-actions">
                                <button class="btn btn-sm btn-primary" onclick="maintenanceManager.createTaskFromRecommendation('${rec.id}')">
                                    Create Task
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    async saveMaintenanceTask() {
        const formData = new FormData(document.getElementById('addTaskForm'));
        const taskData = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('/api/maintenance/tasks', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(taskData)
            });
            
            if (response.ok) {
                const newTask = await response.json();
                this.maintenanceTasks.push(newTask);
                this.showToast('Maintenance task created successfully', 'success');
                this.closeModal(document.getElementById('addTaskModal'));
                this.renderCurrentView();
            } else {
                throw new Error('Failed to create task');
            }
        } catch (error) {
            this.showToast('Error creating maintenance task', 'error');
        }
    }

    async startTask(taskId) {
        try {
            const response = await fetch(`/api/maintenance/tasks/${taskId}/start`, {
                method: 'POST'
            });
            
            if (response.ok) {
                const task = this.maintenanceTasks.find(t => t.id === taskId);
                if (task) task.status = 'in_progress';
                this.showToast('Task started', 'success');
                this.renderCurrentView();
            }
        } catch (error) {
            this.showToast('Error starting task', 'error');
        }
    }

    async completeTask(taskId) {
        const cost = prompt('Enter actual cost for this task:');
        if (!cost) return;
        
        try {
            const response = await fetch(`/api/maintenance/tasks/${taskId}/complete`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ actualCost: parseFloat(cost) })
            });
            
            if (response.ok) {
                const task = this.maintenanceTasks.find(t => t.id === taskId);
                if (task) {
                    task.status = 'completed';
                    task.actualCost = parseFloat(cost);
                }
                this.showToast('Task completed successfully', 'success');
                this.renderCurrentView();
            }
        } catch (error) {
            this.showToast('Error completing task', 'error');
        }
    }

    getFilteredTasks() {
        const statusFilter = document.getElementById('statusFilter').value;
        const priorityFilter = document.getElementById('priorityFilter').value;
        const searchTerm = document.getElementById('maintenanceSearch').value.toLowerCase();
        
        return this.maintenanceTasks.filter(task => {
            const matchesStatus = !statusFilter || task.status === statusFilter;
            const matchesPriority = !priorityFilter || task.priority === priorityFilter;
            const matchesSearch = !searchTerm || 
                task.title.toLowerCase().includes(searchTerm) ||
                task.description.toLowerCase().includes(searchTerm) ||
                task.groundName.toLowerCase().includes(searchTerm);
            
            return matchesStatus && matchesPriority && matchesSearch;
        });
    }

    async loadMaintenanceData() {
        try {
            const [tasksResponse, inspectionsResponse, recordsResponse] = await Promise.all([
                fetch('/api/maintenance/tasks'),
                fetch('/api/maintenance/inspections'),
                fetch('/api/maintenance/records')
            ]);
            
            this.maintenanceTasks = await tasksResponse.json();
            this.inspections = await inspectionsResponse.json();
            this.maintenanceRecords = await recordsResponse.json();
            
            this.renderCurrentView();
        } catch (error) {
            console.error('Error loading maintenance data:', error);
            this.showToast('Error loading maintenance data', 'error');
        }
    }

    updateSummaryStats() {
        const stats = {
            totalTasks: this.maintenanceTasks.length,
            pendingTasks: this.maintenanceTasks.filter(t => t.status === 'pending').length,
            completedTasks: this.maintenanceTasks.filter(t => t.status === 'completed').length,
            totalCosts: this.maintenanceRecords.reduce((sum, r) => sum + r.amount, 0)
        };
        
        document.getElementById('totalTasks').textContent = stats.totalTasks;
        document.getElementById('pendingTasks').textContent = stats.pendingTasks;
        document.getElementById('completedTasks').textContent = stats.completedTasks;
        document.getElementById('totalCosts').textContent = `₹${stats.totalCosts.toLocaleString()}`;
    }

    // Helper methods
    getStatusLabel(status) {
        const labels = {
            'pending': 'Pending',
            'in_progress': 'In Progress',
            'completed': 'Completed',
            'cancelled': 'Cancelled'
        };
        return labels[status] || status;
    }

    isDueDate(dateString) {
        return new Date(dateString) < new Date();
    }

    getHealthClass(score) {
        if (score >= 80) return 'excellent';
        if (score >= 60) return 'good';
        if (score >= 40) return 'fair';
        return 'poor';
    }

    getHealthStatus(score) {
        if (score >= 80) return 'Excellent';
        if (score >= 60) return 'Good';
        if (score >= 40) return 'Needs Attention';
        return 'Critical';
    }

    exportMaintenanceReport() {
        const data = this.maintenanceTasks.map(task => ({
            'Task': task.title,
            'Ground': task.groundName,
            'Status': task.status,
            'Priority': task.priority,
            'Due Date': task.dueDate,
            'Estimated Cost': task.estimatedCost,
            'Actual Cost': task.actualCost || '',
            'Assigned To': task.assignedTo || ''
        }));
        
        this.downloadCSV(data, 'maintenance-report.csv');
    }

    downloadCSV(data, filename) {
        const csv = this.arrayToCSV(data);
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        
        window.URL.revokeObjectURL(url);
    }

    arrayToCSV(data) {
        if (!data.length) return '';
        
        const headers = Object.keys(data[0]);
        const csvContent = [
            headers.join(','),
            ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
        ].join('\n');
        
        return csvContent;
    }

    openAddTaskModal() {
        document.getElementById('addTaskModal').style.display = 'flex';
    }

    openScheduleInspectionModal() {
        document.getElementById('scheduleInspectionModal').style.display = 'flex';
    }

    closeModal(modal) {
        modal.style.display = 'none';
        const forms = modal.querySelectorAll('form');
        forms.forEach(form => form.reset());
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Placeholder methods for complex calculations
    calculateMonthlyCosts() {
        return {
            current: 45000,
            change: 12,
            yearTotal: 540000,
            avgPerGround: 15000
        };
    }

    calculateCategoryCosts() {
        return [
            { category: 'Surface Maintenance', amount: 20000 },
            { category: 'Equipment Repair', amount: 15000 },
            { category: 'Facility Upkeep', amount: 10000 }
        ];
    }

    calculateGroundHealth() {
        return {
            overall: 78,
            compliance: 85,
            safety: 92,
            equipment: 67,
            grounds: [
                {
                    id: 1,
                    name: 'Football Ground A',
                    sport: 'Football',
                    lastInspection: '2024-01-15',
                    surface: 85,
                    facilities: 78,
                    safety: 92
                }
            ]
        };
    }

    generateRecommendations() {
        return [
            {
                id: 'rec_1',
                title: 'Field Surface Inspection',
                description: 'Regular field surface inspection needed for optimal playing conditions',
                priority: 'high'
            }
        ];
    }

    getTasksForDate(date) {
        const dateStr = date.toISOString().split('T')[0];
        return this.maintenanceTasks.filter(task => task.dueDate === dateStr);
    }

    getInspectionsForDate(date) {
        const dateStr = date.toISOString().split('T')[0];
        return this.inspections.filter(inspection => inspection.date === dateStr);
    }
}

// Initialize when DOM is loaded
let maintenanceManager;
document.addEventListener('DOMContentLoaded', () => {
    maintenanceManager = new MaintenanceManager();
});