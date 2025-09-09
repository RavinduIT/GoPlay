// Ground Owner - Bookings Management JavaScript
class BookingsManager {
    constructor() {
        this.currentView = 'list';
        this.bookings = [];
        this.filteredBookings = [];
        this.filters = {
            status: '',
            ground: '',
            sortBy: 'booking_date'
        };
        this.currentPage = 1;
        this.itemsPerPage = 10;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadBookings();
        this.updateStats();
        this.initDateFilters();
    }

    bindEvents() {
        // View toggles
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchView(e.target.dataset.view);
            });
        });

        // Filters
        document.getElementById('statusFilter')?.addEventListener('change', (e) => {
            this.filters.status = e.target.value;
            this.applyFilters();
        });

        document.getElementById('groundFilter')?.addEventListener('change', (e) => {
            this.filters.ground = e.target.value;
            this.applyFilters();
        });

        document.getElementById('sortBy')?.addEventListener('change', (e) => {
            this.filters.sortBy = e.target.value;
            this.applyFilters();
        });

        // Search
        document.getElementById('bookingSearch')?.addEventListener('input', (e) => {
            this.searchBookings(e.target.value);
        });

        // Date filters
        document.getElementById('startDate')?.addEventListener('change', () => {
            this.applyDateFilter();
        });

        document.getElementById('endDate')?.addEventListener('change', () => {
            this.applyDateFilter();
        });

        // Calendar navigation
        document.getElementById('prevMonth')?.addEventListener('click', () => {
            this.navigateCalendar('prev');
        });

        document.getElementById('nextMonth')?.addEventListener('click', () => {
            this.navigateCalendar('next');
        });

        // Export functionality
        window.exportBookings = this.exportBookings.bind(this);
        
        // Modal functions
        window.closeBookingModal = this.closeBookingModal.bind(this);
        window.confirmBooking = this.confirmBooking.bind(this);
        window.cancelBooking = this.cancelBooking.bind(this);
        window.contactCustomer = this.contactCustomer.bind(this);
    }

    async loadBookings() {
        try {
            const response = await fetch('/api/ground-owner/bookings');
            if (response.ok) {
                const data = await response.json();
                this.bookings = data.bookings || [];
                this.filteredBookings = [...this.bookings];
                this.renderCurrentView();
                this.populateGroundFilter();
            }
        } catch (error) {
            console.error('Error loading bookings:', error);
            this.showToast('Failed to load bookings', 'error');
        }
    }

    switchView(view) {
        this.currentView = view;
        
        // Update view buttons
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-view="${view}"]`).classList.add('active');

        // Show/hide view content
        document.querySelectorAll('.view-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(`${view}View`).classList.add('active');

        this.renderCurrentView();
    }

    renderCurrentView() {
        switch (this.currentView) {
            case 'list':
                this.renderListView();
                break;
            case 'calendar':
                this.renderCalendarView();
                break;
            case 'timeline':
                this.renderTimelineView();
                break;
        }
    }

    renderListView() {
        const tbody = document.getElementById('bookingsTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        if (this.filteredBookings.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center" style="padding: 2rem;">
                        <i class="fas fa-calendar-times" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                        <p>No bookings found</p>
                    </td>
                </tr>
            `;
            return;
        }

        this.filteredBookings.forEach(booking => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>#${booking.id}</td>
                <td>${booking.ground_name}</td>
                <td>${booking.customer_name}</td>
                <td>${this.formatDateTime(booking.booking_date, booking.start_time)}</td>
                <td>${this.calculateDuration(booking.start_time, booking.end_time)}</td>
                <td>₹${booking.amount}</td>
                <td><span class="status-badge ${booking.status}">${this.capitalizeFirst(booking.status)}</span></td>
                <td>
                    <button onclick="bookingsManager.viewBookingDetails(${booking.id})" class="btn-secondary" style="padding: 0.5rem;">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    renderCalendarView() {
        const calendarGrid = document.getElementById('calendarGrid');
        if (!calendarGrid) return;

        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Update month display
        document.getElementById('currentMonth').textContent = 
            currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        // Generate calendar grid
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startingDayOfWeek = firstDay.getDay();
        const daysInMonth = lastDay.getDate();

        calendarGrid.innerHTML = '';

        // Add day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            dayHeader.style.cssText = `
                padding: 1rem;
                text-align: center;
                font-weight: 600;
                background: var(--light-bg);
                border-bottom: 1px solid var(--border-color);
            `;
            calendarGrid.appendChild(dayHeader);
        });

        // Add empty cells for days before first day of month
        for (let i = 0; i < startingDayOfWeek; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            calendarGrid.appendChild(emptyDay);
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            
            const dayDate = new Date(year, month, day);
            const isToday = dayDate.toDateString() === new Date().toDateString();
            
            if (isToday) {
                dayElement.classList.add('today');
            }

            const dayBookings = this.getBookingsForDate(dayDate);
            
            dayElement.innerHTML = `
                <div class="day-number">${day}</div>
                <div class="day-bookings">
                    ${dayBookings.map(booking => `
                        <div class="calendar-booking ${booking.status}" onclick="bookingsManager.viewBookingDetails(${booking.id})">
                            <div class="booking-title">${booking.ground_name}</div>
                            <div class="booking-time">${booking.start_time} - ${booking.end_time}</div>
                        </div>
                    `).join('')}
                </div>
            `;

            calendarGrid.appendChild(dayElement);
        }

        calendarGrid.style.gridTemplateColumns = 'repeat(7, 1fr)';
    }

    renderTimelineView() {
        const timelineContainer = document.getElementById('timelineContainer');
        if (!timelineContainer) return;

        // Group bookings by date
        const groupedBookings = this.groupBookingsByDate();
        
        timelineContainer.innerHTML = '';

        Object.keys(groupedBookings).sort().forEach(date => {
            const timelineItem = document.createElement('div');
            timelineItem.className = 'timeline-item';
            
            timelineItem.innerHTML = `
                <div class="timeline-date">${this.formatDate(date)}</div>
                <div class="timeline-bookings">
                    ${groupedBookings[date].map(booking => `
                        <div class="timeline-booking" onclick="bookingsManager.viewBookingDetails(${booking.id})">
                            <div class="booking-info">
                                <h4>${booking.ground_name}</h4>
                                <p>${booking.customer_name}</p>
                                <span class="booking-details">${booking.start_time} - ${booking.end_time}</span>
                                <span class="booking-amount">₹${booking.amount}</span>
                            </div>
                            <span class="status-badge ${booking.status}">${this.capitalizeFirst(booking.status)}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            
            timelineContainer.appendChild(timelineItem);
        });
    }

    applyFilters() {
        this.filteredBookings = this.bookings.filter(booking => {
            let matches = true;
            
            if (this.filters.status && booking.status !== this.filters.status) {
                matches = false;
            }
            
            if (this.filters.ground && booking.ground_id !== parseInt(this.filters.ground)) {
                matches = false;
            }
            
            return matches;
        });

        this.sortBookings();
        this.renderCurrentView();
    }

    sortBookings() {
        this.filteredBookings.sort((a, b) => {
            switch (this.filters.sortBy) {
                case 'booking_date':
                    return new Date(b.booking_date) - new Date(a.booking_date);
                case 'created_at':
                    return new Date(b.created_at) - new Date(a.created_at);
                case 'amount':
                    return b.amount - a.amount;
                default:
                    return 0;
            }
        });
    }

    searchBookings(query) {
        if (!query.trim()) {
            this.filteredBookings = [...this.bookings];
        } else {
            this.filteredBookings = this.bookings.filter(booking => 
                booking.ground_name.toLowerCase().includes(query.toLowerCase()) ||
                booking.customer_name.toLowerCase().includes(query.toLowerCase()) ||
                booking.id.toString().includes(query)
            );
        }
        
        this.renderCurrentView();
    }

    async viewBookingDetails(bookingId) {
        try {
            const response = await fetch(`/api/ground-owner/bookings/${bookingId}`);
            if (response.ok) {
                const data = await response.json();
                this.showBookingModal(data.booking);
            }
        } catch (error) {
            console.error('Error loading booking details:', error);
            this.showToast('Failed to load booking details', 'error');
        }
    }

    showBookingModal(booking) {
        // Populate modal with booking data
        document.getElementById('bookingId').textContent = `#${booking.id}`;
        document.getElementById('bookingGround').textContent = booking.ground_name;
        document.getElementById('bookingDate').textContent = this.formatDate(booking.booking_date);
        document.getElementById('bookingTime').textContent = `${booking.start_time} - ${booking.end_time}`;
        document.getElementById('bookingDuration').textContent = this.calculateDuration(booking.start_time, booking.end_time);
        document.getElementById('bookingAmount').textContent = `₹${booking.amount}`;
        document.getElementById('bookingStatus').textContent = this.capitalizeFirst(booking.status);
        document.getElementById('bookingStatus').className = `status-badge ${booking.status}`;
        
        document.getElementById('customerName').textContent = booking.customer_name;
        document.getElementById('customerEmail').textContent = booking.customer_email;
        document.getElementById('customerPhone').textContent = booking.customer_phone || 'Not provided';
        document.getElementById('customerBookings').textContent = booking.customer_booking_count || '0';
        
        document.getElementById('bookingNotes').textContent = booking.notes || 'No special requests';

        // Show appropriate action buttons
        const confirmBtn = document.getElementById('confirmBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        
        if (booking.status === 'pending') {
            confirmBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
        } else {
            confirmBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
        }

        document.getElementById('bookingModal').classList.add('active');
    }

    closeBookingModal() {
        document.getElementById('bookingModal').classList.remove('active');
    }

    async confirmBooking() {
        const bookingId = document.getElementById('bookingId').textContent.replace('#', '');
        
        try {
            const response = await fetch(`/api/ground-owner/bookings/${bookingId}/confirm`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                this.showToast('Booking confirmed successfully', 'success');
                this.closeBookingModal();
                this.loadBookings();
            }
        } catch (error) {
            console.error('Error confirming booking:', error);
            this.showToast('Failed to confirm booking', 'error');
        }
    }

    async cancelBooking() {
        const bookingId = document.getElementById('bookingId').textContent.replace('#', '');
        
        if (!confirm('Are you sure you want to cancel this booking?')) {
            return;
        }

        try {
            const response = await fetch(`/api/ground-owner/bookings/${bookingId}/cancel`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                this.showToast('Booking cancelled successfully', 'success');
                this.closeBookingModal();
                this.loadBookings();
            }
        } catch (error) {
            console.error('Error cancelling booking:', error);
            this.showToast('Failed to cancel booking', 'error');
        }
    }

    contactCustomer() {
        const email = document.getElementById('customerEmail').textContent;
        const phone = document.getElementById('customerPhone').textContent;
        
        const message = `Choose contact method:\n\n1. Email: ${email}\n2. Phone: ${phone}`;
        
        if (confirm(message + '\n\nClick OK for Email, Cancel for Phone')) {
            window.location.href = `mailto:${email}`;
        } else if (phone !== 'Not provided') {
            window.location.href = `tel:${phone}`;
        }
    }

    updateStats() {
        // This would typically fetch stats from API
        const stats = {
            total: this.bookings.length,
            confirmed: this.bookings.filter(b => b.status === 'confirmed').length,
            pending: this.bookings.filter(b => b.status === 'pending').length,
            revenue: this.bookings.reduce((sum, b) => sum + (b.amount || 0), 0)
        };

        document.getElementById('totalBookings').textContent = stats.total;
        document.getElementById('confirmedBookings').textContent = stats.confirmed;
        document.getElementById('pendingBookings').textContent = stats.pending;
        document.getElementById('monthlyRevenue').textContent = `₹${stats.revenue.toLocaleString()}`;
    }

    populateGroundFilter() {
        const groundFilter = document.getElementById('groundFilter');
        if (!groundFilter) return;

        const grounds = [...new Set(this.bookings.map(b => ({ id: b.ground_id, name: b.ground_name })))];
        
        grounds.forEach(ground => {
            const option = document.createElement('option');
            option.value = ground.id;
            option.textContent = ground.name;
            groundFilter.appendChild(option);
        });
    }

    initDateFilters() {
        const today = new Date();
        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        
        document.getElementById('startDate').value = weekAgo.toISOString().split('T')[0];
        document.getElementById('endDate').value = today.toISOString().split('T')[0];
    }

    applyDateFilter() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (startDate && endDate) {
            this.filteredBookings = this.bookings.filter(booking => {
                const bookingDate = new Date(booking.booking_date);
                return bookingDate >= new Date(startDate) && bookingDate <= new Date(endDate);
            });
            
            this.renderCurrentView();
        }
    }

    navigateCalendar(direction) {
        const currentMonth = document.getElementById('currentMonth');
        const [monthName, year] = currentMonth.textContent.split(' ');
        const currentDate = new Date(`${monthName} 1, ${year}`);
        
        if (direction === 'prev') {
            currentDate.setMonth(currentDate.getMonth() - 1);
        } else {
            currentDate.setMonth(currentDate.getMonth() + 1);
        }
        
        currentMonth.textContent = currentDate.toLocaleDateString('en-US', { 
            month: 'long', 
            year: 'numeric' 
        });
        
        this.renderCalendarView();
    }

    exportBookings() {
        const csvContent = this.generateCSV();
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `bookings-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    }

    generateCSV() {
        const headers = ['Booking ID', 'Ground', 'Customer', 'Date', 'Time', 'Duration', 'Amount', 'Status'];
        const rows = this.filteredBookings.map(booking => [
            booking.id,
            booking.ground_name,
            booking.customer_name,
            booking.booking_date,
            `${booking.start_time} - ${booking.end_time}`,
            this.calculateDuration(booking.start_time, booking.end_time),
            booking.amount,
            booking.status
        ]);

        return [headers, ...rows].map(row => 
            row.map(field => `"${field}"`).join(',')
        ).join('\n');
    }

    // Utility functions
    formatDateTime(date, time) {
        const d = new Date(date);
        return `${d.toLocaleDateString()} ${time}`;
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    calculateDuration(startTime, endTime) {
        const start = new Date(`2000-01-01 ${startTime}`);
        const end = new Date(`2000-01-01 ${endTime}`);
        const diff = (end - start) / (1000 * 60 * 60);
        return `${diff} hour${diff !== 1 ? 's' : ''}`;
    }

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    getBookingsForDate(date) {
        const dateStr = date.toISOString().split('T')[0];
        return this.bookings.filter(booking => booking.booking_date === dateStr);
    }

    groupBookingsByDate() {
        const grouped = {};
        this.filteredBookings.forEach(booking => {
            const date = booking.booking_date;
            if (!grouped[date]) {
                grouped[date] = [];
            }
            grouped[date].push(booking);
        });
        return grouped;
    }

    showToast(message, type = 'info') {
        // Create toast notification
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
    window.bookingsManager = new BookingsManager();
});

// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('dashboardSidebar');
    sidebar.classList.toggle('collapsed');
}