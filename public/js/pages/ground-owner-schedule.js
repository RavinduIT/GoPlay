class ScheduleManager {
    constructor() {
        this.currentView = 'week';
        this.currentDate = new Date();
        this.schedule = [];
        this.timeSlots = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadSchedule();
        this.renderCurrentView();
    }

    bindEvents() {
        // View switching
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const view = e.target.dataset.view;
                this.switchView(view);
            });
        });

        // Navigation
        document.getElementById('prevPeriod').addEventListener('click', () => this.navigatePeriod(-1));
        document.getElementById('nextPeriod').addEventListener('click', () => this.navigatePeriod(1));
        document.getElementById('todayBtn').addEventListener('click', () => this.goToToday());

        // Availability management
        document.getElementById('bulkAvailabilityBtn').addEventListener('click', () => this.openBulkAvailabilityModal());
        document.getElementById('blockTimeBtn').addEventListener('click', () => this.openBlockTimeModal());

        // Modal events
        document.getElementById('saveBulkAvailability').addEventListener('click', () => this.saveBulkAvailability());
        document.getElementById('saveBlockTime').addEventListener('click', () => this.saveBlockTime());

        // Close modals
        document.querySelectorAll('.modal-close, .btn-secondary').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) this.closeModal(modal);
            });
        });

        // Time slot generation
        document.getElementById('generateSlots').addEventListener('click', () => this.generateTimeSlots());
    }

    switchView(view) {
        this.currentView = view;
        
        // Update active button
        document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-view="${view}"]`).classList.add('active');
        
        this.renderCurrentView();
    }

    renderCurrentView() {
        switch (this.currentView) {
            case 'week':
                this.renderWeekView();
                break;
            case 'day':
                this.renderDayView();
                break;
            case 'month':
                this.renderMonthView();
                break;
        }
        this.updatePeriodDisplay();
    }

    renderWeekView() {
        const weekStart = this.getWeekStart(this.currentDate);
        const weekDays = [];
        
        for (let i = 0; i < 7; i++) {
            const day = new Date(weekStart);
            day.setDate(weekStart.getDate() + i);
            weekDays.push(day);
        }

        const calendarContainer = document.getElementById('calendarView');
        calendarContainer.innerHTML = `
            <div class="week-view">
                <div class="time-column">
                    <div class="time-header"></div>
                    ${this.generateTimeSlots().map(slot => `
                        <div class="time-slot-label">${slot}</div>
                    `).join('')}
                </div>
                ${weekDays.map(day => `
                    <div class="day-column" data-date="${day.toISOString().split('T')[0]}">
                        <div class="day-header">
                            <div class="day-name">${day.toLocaleDateString('en-US', { weekday: 'short' })}</div>
                            <div class="day-number">${day.getDate()}</div>
                        </div>
                        ${this.generateTimeSlots().map(slot => `
                            <div class="time-slot" data-time="${slot}" onclick="scheduleManager.selectTimeSlot(event)">
                                ${this.getSlotContent(day, slot)}
                            </div>
                        `).join('')}
                    </div>
                `).join('')}
            </div>
        `;
    }

    renderDayView() {
        const day = new Date(this.currentDate);
        const calendarContainer = document.getElementById('calendarView');
        
        calendarContainer.innerHTML = `
            <div class="day-view">
                <div class="day-header-large">
                    <h3>${day.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</h3>
                </div>
                <div class="day-schedule">
                    <div class="time-column">
                        ${this.generateTimeSlots().map(slot => `
                            <div class="time-slot-label">${slot}</div>
                        `).join('')}
                    </div>
                    <div class="slots-column">
                        ${this.generateTimeSlots().map(slot => `
                            <div class="time-slot large" data-time="${slot}" onclick="scheduleManager.selectTimeSlot(event)">
                                ${this.getSlotContent(day, slot)}
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    }

    renderMonthView() {
        const monthStart = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
        const monthEnd = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
        const calendarStart = this.getWeekStart(monthStart);
        
        const calendarContainer = document.getElementById('calendarView');
        let html = '<div class="month-view"><div class="month-grid">';
        
        // Day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            html += `<div class="day-header">${day}</div>`;
        });
        
        // Calendar days
        let currentDay = new Date(calendarStart);
        for (let week = 0; week < 6; week++) {
            for (let day = 0; day < 7; day++) {
                const isCurrentMonth = currentDay.getMonth() === this.currentDate.getMonth();
                const dayBookings = this.getDayBookings(currentDay);
                
                html += `
                    <div class="calendar-day ${!isCurrentMonth ? 'other-month' : ''}" 
                         data-date="${currentDay.toISOString().split('T')[0]}"
                         onclick="scheduleManager.selectDay(event)">
                        <div class="day-number">${currentDay.getDate()}</div>
                        <div class="day-bookings">
                            ${dayBookings.slice(0, 3).map(booking => `
                                <div class="booking-dot ${booking.status}"></div>
                            `).join('')}
                            ${dayBookings.length > 3 ? `<div class="booking-more">+${dayBookings.length - 3}</div>` : ''}
                        </div>
                    </div>
                `;
                currentDay.setDate(currentDay.getDate() + 1);
            }
        }
        
        html += '</div></div>';
        calendarContainer.innerHTML = html;
    }

    generateTimeSlots() {
        const slots = [];
        for (let hour = 6; hour <= 22; hour++) {
            slots.push(`${hour.toString().padStart(2, '0')}:00`);
            slots.push(`${hour.toString().padStart(2, '0')}:30`);
        }
        return slots;
    }

    getSlotContent(date, time) {
        const booking = this.getBookingForSlot(date, time);
        if (booking) {
            return `
                <div class="slot-booking ${booking.status}">
                    <div class="booking-title">${booking.customerName}</div>
                    <div class="booking-ground">${booking.groundName}</div>
                </div>
            `;
        }
        
        const availability = this.getAvailabilityForSlot(date, time);
        if (availability) {
            return `<div class="slot-available">Available - ₹${availability.rate}/hr</div>`;
        }
        
        return '<div class="slot-blocked">Blocked</div>';
    }

    getBookingForSlot(date, time) {
        return this.schedule.find(item => 
            item.date === date.toISOString().split('T')[0] && 
            item.time === time &&
            item.type === 'booking'
        );
    }

    getAvailabilityForSlot(date, time) {
        return this.schedule.find(item => 
            item.date === date.toISOString().split('T')[0] && 
            item.time === time &&
            item.type === 'available'
        );
    }

    getDayBookings(date) {
        return this.schedule.filter(item => 
            item.date === date.toISOString().split('T')[0] && 
            item.type === 'booking'
        );
    }

    selectTimeSlot(event) {
        const slot = event.currentTarget;
        const date = slot.closest('[data-date]').dataset.date;
        const time = slot.dataset.time;
        
        // Remove previous selection
        document.querySelectorAll('.time-slot.selected').forEach(s => s.classList.remove('selected'));
        slot.classList.add('selected');
        
        this.showSlotActions(date, time, slot);
    }

    selectDay(event) {
        const day = event.currentTarget;
        const date = day.dataset.date;
        this.currentDate = new Date(date);
        this.switchView('day');
    }

    showSlotActions(date, time, slotElement) {
        const existing = document.querySelector('.slot-actions');
        if (existing) existing.remove();
        
        const actions = document.createElement('div');
        actions.className = 'slot-actions';
        actions.innerHTML = `
            <button class="btn-action btn-primary" onclick="scheduleManager.setAvailable('${date}', '${time}')">
                <i class="fas fa-check"></i> Set Available
            </button>
            <button class="btn-action btn-secondary" onclick="scheduleManager.blockSlot('${date}', '${time}')">
                <i class="fas fa-ban"></i> Block
            </button>
            <button class="btn-action btn-delete" onclick="scheduleManager.removeSlot('${date}', '${time}')">
                <i class="fas fa-trash"></i> Remove
            </button>
        `;
        
        slotElement.appendChild(actions);
    }

    async setAvailable(date, time) {
        const rate = prompt('Enter hourly rate for this slot:');
        if (!rate) return;
        
        try {
            const response = await fetch('/api/schedule/availability', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date, time, rate: parseFloat(rate) })
            });
            
            if (response.ok) {
                this.// showToast('Time slot set as available', 'success');
                this.loadSchedule();
            } else {
                throw new Error('Failed to set availability');
            }
        } catch (error) {
            this.// showToast('Error setting availability', 'error');
        }
    }

    async blockSlot(date, time) {
        const reason = prompt('Reason for blocking (optional):');
        
        try {
            const response = await fetch('/api/schedule/block', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date, time, reason })
            });
            
            if (response.ok) {
                this.// showToast('Time slot blocked', 'success');
                this.loadSchedule();
            } else {
                throw new Error('Failed to block slot');
            }
        } catch (error) {
            this.// showToast('Error blocking slot', 'error');
        }
    }

    async removeSlot(date, time) {
        if (!confirm('Remove this time slot?')) return;
        
        try {
            const response = await fetch('/api/schedule/remove', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date, time })
            });
            
            if (response.ok) {
                this.// showToast('Time slot removed', 'success');
                this.loadSchedule();
            } else {
                throw new Error('Failed to remove slot');
            }
        } catch (error) {
            this.// showToast('Error removing slot', 'error');
        }
    }

    openBulkAvailabilityModal() {
        document.getElementById('bulkAvailabilityModal').style.display = 'flex';
    }

    openBlockTimeModal() {
        document.getElementById('blockTimeModal').style.display = 'flex';
    }

    async saveBulkAvailability() {
        const formData = new FormData(document.getElementById('bulkAvailabilityForm'));
        const data = Object.fromEntries(formData.entries());
        
        // Get selected days
        const selectedDays = Array.from(document.querySelectorAll('input[name="days[]"]:checked'))
            .map(cb => cb.value);
        
        // Get selected time slots
        const startTime = data.startTime;
        const endTime = data.endTime;
        const rate = parseFloat(data.rate);
        
        try {
            const response = await fetch('/api/schedule/bulk-availability', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    days: selectedDays,
                    startTime,
                    endTime,
                    rate,
                    startDate: data.startDate,
                    endDate: data.endDate
                })
            });
            
            if (response.ok) {
                this.// showToast('Bulk availability set successfully', 'success');
                this.closeModal(document.getElementById('bulkAvailabilityModal'));
                this.loadSchedule();
            } else {
                throw new Error('Failed to set bulk availability');
            }
        } catch (error) {
            this.// showToast('Error setting bulk availability', 'error');
        }
    }

    async saveBlockTime() {
        const formData = new FormData(document.getElementById('blockTimeForm'));
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await fetch('/api/schedule/bulk-block', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            if (response.ok) {
                this.// showToast('Time blocked successfully', 'success');
                this.closeModal(document.getElementById('blockTimeModal'));
                this.loadSchedule();
            } else {
                throw new Error('Failed to block time');
            }
        } catch (error) {
            this.// showToast('Error blocking time', 'error');
        }
    }

    navigatePeriod(direction) {
        const currentDate = new Date(this.currentDate);
        
        switch (this.currentView) {
            case 'day':
                currentDate.setDate(currentDate.getDate() + direction);
                break;
            case 'week':
                currentDate.setDate(currentDate.getDate() + (direction * 7));
                break;
            case 'month':
                currentDate.setMonth(currentDate.getMonth() + direction);
                break;
        }
        
        this.currentDate = currentDate;
        this.renderCurrentView();
    }

    goToToday() {
        this.currentDate = new Date();
        this.renderCurrentView();
    }

    updatePeriodDisplay() {
        const periodDisplay = document.getElementById('currentPeriod');
        let text = '';
        
        switch (this.currentView) {
            case 'day':
                text = this.currentDate.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                break;
            case 'week':
                const weekStart = this.getWeekStart(this.currentDate);
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6);
                text = `${weekStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
                break;
            case 'month':
                text = this.currentDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
                break;
        }
        
        periodDisplay.textContent = text;
    }

    getWeekStart(date) {
        const start = new Date(date);
        const day = start.getDay();
        const diff = start.getDate() - day;
        return new Date(start.setDate(diff));
    }

    async loadSchedule() {
        try {
            const response = await fetch('/api/schedule');
            this.schedule = await response.json();
            this.renderCurrentView();
        } catch (error) {
            console.error('Error loading schedule:', error);
            this.// showToast('Error loading schedule data', 'error');
        }
    }

    closeModal(modal) {
        modal.style.display = 'none';
        // Reset forms
        const forms = modal.querySelectorAll('form');
        forms.forEach(form => form.reset());
    }

    // showToast(message, type = 'info') {
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

// Initialize when DOM is loaded
let scheduleManager;
document.addEventListener('DOMContentLoaded', () => {
    scheduleManager = new ScheduleManager();
});