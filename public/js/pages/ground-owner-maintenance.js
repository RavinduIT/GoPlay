/**
 * Ground Owner Maintenance Management
 * Connects to real backend APIs
 */
class MaintenanceManager {
    constructor() {
        this.currentMonth = new Date();
        this.tasks = [];
        this.inspections = [];
        this.facilities = [];
        this.stats = {};
        this.currentFilterStatus = '';
        this.currentFilterPriority = '';
        this.init();
    }

    async init() {
        try {
            await this.loadFacilities();
            await this.loadStats();
            await this.loadActiveTasks();
            await this.loadUpcomingInspections();
            await this.renderCalendar();
            this.bindEvents();
        } catch (error) {
            console.error('Initialization error:', error);
            this.showToast('Failed to initialize maintenance dashboard', 'error');
        }
    }

    bindEvents() {
        // Forms
        const maintenanceForm = document.getElementById('maintenanceForm');
        if (maintenanceForm) {
            maintenanceForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveMaintenanceTask();
            });
        }

        const inspectionForm = document.getElementById('inspectionForm');
        if (inspectionForm) {
            inspectionForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveInspection();
            });
        }

        // Filters
        const priorityFilter = document.getElementById('taskPriorityFilter');
        const statusFilter = document.getElementById('taskStatusFilter');
        const groundFilter = document.getElementById('groundFilter');

        if (priorityFilter) {
            priorityFilter.addEventListener('change', (e) => {
                this.currentFilterPriority = e.target.value;
                this.loadActiveTasks();
            });
        }

        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.currentFilterStatus = e.target.value;
                this.loadActiveTasks();
            });
        }

        if (groundFilter) {
            groundFilter.addEventListener('change', (e) => {
                this.selectedFacilityId = e.target.value;
                this.loadActiveTasks();
            });
        }

        // Calendar navigation
        const prevBtn = document.getElementById('prevCalendarMonth');
        const nextBtn = document.getElementById('nextCalendarMonth');

        if (prevBtn) prevBtn.addEventListener('click', () => this.navigateMonth(-1));
        if (nextBtn) nextBtn.addEventListener('click', () => this.navigateMonth(1));
    }

    async loadFacilities() {
        try {
            const response = await fetch('/api/ground-owner/facilities');
            const data = await response.json();

            if (data.success) {
                this.facilities = data.facilities || [];
                this.updateFacilityDropdowns();
            }
        } catch (error) {
            console.error('Error loading facilities:', error);
        }
    }

    async loadStats() {
        try {
            const response = await fetch('/api/ground-owner/maintenance/stats');
            const data = await response.json();

            if (data.success) {
                this.stats = data.stats;
                this.updateStatsDisplay();
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async loadActiveTasks() {
        try {
            let url = '/api/ground-owner/maintenance/tasks/active';
            const params = [];

            if (this.currentFilterPriority) params.push(`priority=${this.currentFilterPriority}`);
            if (this.currentFilterStatus) params.push(`status=${this.currentFilterStatus}`);
            if (this.selectedFacilityId) params.push(`facility_id=${this.selectedFacilityId}`);

            if (params.length > 0) {
                url = `/api/ground-owner/maintenance/tasks?${params.join('&')}`;
            }

            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                this.tasks = data.tasks || [];
                this.renderTasksList();
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
            this.showToast('Failed to load tasks', 'error');
        }
    }

    async loadUpcomingInspections() {
        try {
            const response = await fetch('/api/ground-owner/inspections/upcoming?limit=5');
            const data = await response.json();

            if (data.success) {
                this.inspections = data.inspections || [];
                this.renderInspectionsList();
            }
        } catch (error) {
            console.error('Error loading inspections:', error);
        }
    }

    updateStatsDisplay() {
        // Update stat cards
        const activeTasks = document.getElementById('activeTasks');
        const highPriority = document.getElementById('highPriorityTasks');
        const mediumPriority = document.getElementById('mediumPriorityTasks');
        const overdueTasks = document.getElementById('overdueTasks');
        const completedMonth = document.getElementById('completedMonth');
        const completionRate = document.getElementById('completionRate');
        const monthlyCost = document.getElementById('monthlyCost');

        if (activeTasks) activeTasks.textContent = this.stats.active_tasks || 0;
        if (highPriority) highPriority.textContent = this.stats.high_priority_tasks || 0;
        if (mediumPriority) mediumPriority.textContent = this.stats.medium_priority_tasks || 0;
        if (overdueTasks) overdueTasks.textContent = this.stats.overdue_tasks || 0;
        if (completedMonth) completedMonth.textContent = this.stats.completed_month || 0;
        if (completionRate) completionRate.textContent = `${this.stats.completion_rate || 0}%`;
        if (monthlyCost) monthlyCost.textContent = `LKR ${(this.stats.monthly_cost || 0).toLocaleString()}`;
    }

    updateFacilityDropdowns() {
        const taskGround = document.getElementById('taskGround');
        const inspectionGround = document.getElementById('inspectionGround');
        const groundFilter = document.getElementById('groundFilter');

        [taskGround, inspectionGround].forEach(select => {
            if (select) {
                select.innerHTML = '<option value="">Select Ground</option>';
                this.facilities.forEach(facility => {
                    const option = document.createElement('option');
                    option.value = facility.id;
                    option.textContent = facility.name;
                    select.appendChild(option);
                });
            }
        });

        if (groundFilter && this.facilities.length > 0) {
            groundFilter.innerHTML = '<option value="">All Grounds</option>';
            this.facilities.forEach(facility => {
                const option = document.createElement('option');
                option.value = facility.id;
                option.textContent = facility.name;
                groundFilter.appendChild(option);
            });
        }
    }

    renderTasksList() {
        const container = document.getElementById('activeTasksList');
        if (!container) return;

        if (this.tasks.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #6b7280;">
                    <i class="fas fa-tasks" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <h3>No Active Tasks</h3>
                    <p>All maintenance tasks are up to date!</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.tasks.map(task => `
            <div class="task-card" style="border-left: 4px solid ${this.getPriorityColor(task.priority)}; padding: 16px; margin-bottom: 12px; background: white; border-radius: 8px; cursor: pointer;" onclick="maintenanceManager.viewTaskDetails(${task.id})">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <h4 style="margin: 0; font-size: 16px;">${task.title}</h4>
                    <span class="priority-badge" style="padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; background: ${this.getPriorityColor(task.priority)}20; color: ${this.getPriorityColor(task.priority)}; text-transform: uppercase;">
                        ${task.priority}
                    </span>
                </div>
                <div style="color: #6b7280; font-size: 14px; margin-bottom: 8px;">
                    <i class="fas fa-map-marker-alt"></i> ${task.facility_name}
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                    <span><i class="fas fa-calendar"></i> ${this.formatDate(task.scheduled_date)}</span>
                    <span><i class="fas fa-tools"></i> ${task.task_type}</span>
                    <span style="color: ${this.getStatusColor(task.status)}; font-weight: 600;">
                        <i class="fas fa-circle" style="font-size: 8px;"></i> ${task.status}
                    </span>
                </div>
            </div>
        `).join('');
    }

    renderInspectionsList() {
        const container = document.getElementById('inspectionsList');
        if (!container) return;

        if (this.inspections.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #6b7280;">
                    <i class="fas fa-clipboard-check" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <h3>No Upcoming Inspections</h3>
                    <p>Schedule your next inspection</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.inspections.map(inspection => `
            <div class="inspection-card" style="padding: 16px; margin-bottom: 12px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <h4 style="margin: 0; font-size: 15px;">${inspection.facility_name}</h4>
                    <span style="padding: 4px 10px; background: #3b82f6; color: white; border-radius: 12px; font-size: 11px; font-weight: 600;">
                        ${inspection.inspection_type}
                    </span>
                </div>
                <div style="color: #6b7280; font-size: 13px;">
                    <div><i class="fas fa-calendar"></i> ${this.formatDate(inspection.inspection_date)} ${inspection.inspection_time ? `at ${inspection.inspection_time.substring(0, 5)}` : ''}</div>
                    ${inspection.inspector ? `<div><i class="fas fa-user"></i> ${inspection.inspector}</div>` : ''}
                </div>
            </div>
        `).join('');
    }

    async renderCalendar() {
        try {
            const startDate = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), 1);
            const endDate = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + 1, 0);

            const response = await fetch(`/api/ground-owner/maintenance/calendar?start_date=${startDate.toISOString().split('T')[0]}&end_date=${endDate.toISOString().split('T')[0]}`);
            const data = await response.json();

            if (!data.success) return;

            const calendarTasks = data.tasks || [];

            // Update month display
            const monthDisplay = document.getElementById('calendarMonth');
            if (monthDisplay) {
                monthDisplay.textContent = this.currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            }

            const container = document.getElementById('maintenanceCalendar');
            if (!container) return;

            // Generate calendar
            const firstDay = startDate.getDay();
            const daysInMonth = endDate.getDate();

            let html = '<div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;">';

            // Day headers
            ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
                html += `<div style="text-align: center; font-weight: 600; padding: 8px; background: #f3f4f6; border-radius: 4px;">${day}</div>`;
            });

            // Empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                html += '<div></div>';
            }

            // Calendar days
            for (let day = 1; day <= daysInMonth; day++) {
                const currentDate = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth(), day);
                const dateStr = currentDate.toISOString().split('T')[0];
                const dayTasks = calendarTasks.filter(t => t.scheduled_date === dateStr);
                const isToday = this.isToday(currentDate);

                html += `
                    <div style="min-height: 80px; padding: 8px; background: white; border: 2px solid ${isToday ? '#3b82f6' : '#e5e7eb'}; border-radius: 8px; ${isToday ? 'box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);' : ''}">
                        <div style="font-weight: bold; margin-bottom: 4px; ${isToday ? 'color: #3b82f6;' : ''}">${day}</div>
                        ${dayTasks.slice(0, 2).map(task => `
                            <div style="font-size: 10px; padding: 2px 4px; margin: 2px 0; background: ${this.getStatusColorLight(task.status)}; border-left: 3px solid ${this.getStatusColor(task.status)}; border-radius: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${task.title}">
                                ${task.title.substring(0, 15)}${task.title.length > 15 ? '...' : ''}
                            </div>
                        `).join('')}
                        ${dayTasks.length > 2 ? `<div style="font-size: 10px; color: #6b7280; margin-top: 2px;">+${dayTasks.length - 2} more</div>` : ''}
                    </div>
                `;
            }

            html += '</div>';
            container.innerHTML = html;

        } catch (error) {
            console.error('Error rendering calendar:', error);
        }
    }

    navigateMonth(direction) {
        this.currentMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + direction, 1);
        this.renderCalendar();
    }

    async saveMaintenanceTask() {
        const form = document.getElementById('maintenanceForm');
        const formData = new FormData(form);

        const data = {
            facility_id: formData.get('ground_id'),
            title: formData.get('title'),
            description: formData.get('description'),
            task_type: formData.get('task_type'),
            category: formData.get('category'),
            priority: formData.get('priority'),
            scheduled_date: formData.get('scheduled_date'),
            estimated_duration: formData.get('estimated_duration'),
            estimated_cost: formData.get('estimated_cost'),
            assigned_to: formData.get('assigned_to'),
            required_tools: formData.get('required_tools'),
            block_bookings: formData.get('block_bookings') === 'on',
            send_notifications: formData.get('send_notifications') === 'on'
        };

        try {
            const response = await fetch('/api/ground-owner/maintenance/tasks', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('Maintenance task created successfully', 'success');
                this.closeModal(document.getElementById('maintenanceModal'));
                await this.loadStats();
                await this.loadActiveTasks();
                await this.renderCalendar();
                form.reset();
            } else {
                this.showToast(result.message || 'Failed to create task', 'error');
            }
        } catch (error) {
            console.error('Error creating task:', error);
            this.showToast('Error creating maintenance task', 'error');
        }
    }

    async saveInspection() {
        const form = document.getElementById('inspectionForm');
        const formData = new FormData(form);

        const data = {
            facility_id: formData.get('ground_id'),
            inspection_type: formData.get('inspection_type'),
            inspector: formData.get('inspector'),
            inspection_date: formData.get('inspection_date'),
            inspection_time: formData.get('inspection_time'),
            notes: formData.get('notes')
        };

        try {
            const response = await fetch('/api/ground-owner/inspections', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('Inspection scheduled successfully', 'success');
                this.closeModal(document.getElementById('inspectionModal'));
                await this.loadUpcomingInspections();
                form.reset();
            } else {
                this.showToast(result.message || 'Failed to schedule inspection', 'error');
            }
        } catch (error) {
            console.error('Error scheduling inspection:', error);
            this.showToast('Error scheduling inspection', 'error');
        }
    }

    async viewTaskDetails(taskId) {
        try {
            const response = await fetch(`/api/ground-owner/maintenance/tasks/${taskId}`);
            const data = await response.json();

            if (data.success) {
                const task = data.task;

                // Populate modal
                document.getElementById('detailTaskTitle').textContent = task.title;
                document.getElementById('detailTaskGround').textContent = task.facility_name;
                document.getElementById('detailTaskType').textContent = task.task_type;
                document.getElementById('detailTaskPriority').textContent = task.priority;
                document.getElementById('detailTaskStatus').textContent = task.status;
                document.getElementById('detailScheduledDate').textContent = this.formatDate(task.scheduled_date);
                document.getElementById('detailDuration').textContent = `${task.estimated_duration || 0} hours`;
                document.getElementById('detailEstimatedCost').textContent = `LKR ${(task.estimated_cost || 0).toLocaleString()}`;
                document.getElementById('detailActualCost').textContent = task.actual_cost ? `LKR ${task.actual_cost.toLocaleString()}` : '-';
                document.getElementById('detailAssignedTo').textContent = task.assigned_to || '-';
                document.getElementById('detailTaskDescription').textContent = task.description || '-';
                document.getElementById('detailRequiredTools').textContent = task.required_tools || '-';

                // Show modal
                document.getElementById('taskDetailsModal').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading task details:', error);
            this.showToast('Failed to load task details', 'error');
        }
    }

    // Helper methods
    getPriorityColor(priority) {
        const colors = {
            urgent: '#ef4444',
            high: '#f59e0b',
            medium: '#3b82f6',
            low: '#10b981'
        };
        return colors[priority] || '#6b7280';
    }

    getStatusColor(status) {
        const colors = {
            scheduled: '#3b82f6',
            'in-progress': '#f59e0b',
            completed: '#10b981',
            cancelled: '#ef4444',
            overdue: '#dc2626'
        };
        return colors[status] || '#6b7280';
    }

    getStatusColorLight(status) {
        const colors = {
            scheduled: '#dbeafe',
            'in-progress': '#fef3c7',
            completed: '#d1fae5',
            cancelled: '#fee2e2',
            overdue: '#fecaca'
        };
        return colors[status] || '#f3f4f6';
    }

    formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    isToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
    }

    closeModal(modal) {
        if (modal) modal.style.display = 'none';
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 10000;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

// Global functions for HTML onclick handlers
function addMaintenanceTask() {
    document.getElementById('maintenanceModal').style.display = 'block';
}

function scheduleInspection() {
    document.getElementById('inspectionModal').style.display = 'block';
}

function closeMaintenanceModal() {
    document.getElementById('maintenanceModal').style.display = 'none';
}

function closeInspectionModal() {
    document.getElementById('inspectionModal').style.display = 'none';
}

function closeTaskDetailsModal() {
    document.getElementById('taskDetailsModal').style.display = 'none';
}

// Initialize when DOM is ready
let maintenanceManager;
document.addEventListener('DOMContentLoaded', () => {
    maintenanceManager = new MaintenanceManager();
});
