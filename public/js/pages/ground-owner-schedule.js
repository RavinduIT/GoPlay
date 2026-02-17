/**
 * Ground Owner Schedule Manager
 * Displays real booking data from the API with conflict checking
 */
class ScheduleManager {
    constructor() {
        this.currentView = 'week';
        this.currentDate = new Date();
        this.bookings = [];
        this.facilities = [];
        this.selectedFacilityId = null;
        this.stats = {};
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeViews();
        this.loadSchedule();
    }

    initializeViews() {
        // Ensure week view is visible by default
        document.getElementById('weekView').style.display = 'block';
        document.getElementById('dayView').style.display = 'none';
        document.getElementById('monthView').style.display = 'none';
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
        const prevBtn = document.getElementById('prevWeek');
        const nextBtn = document.getElementById('nextWeek');
        if (prevBtn) prevBtn.addEventListener('click', () => this.navigatePeriod(-1));
        if (nextBtn) nextBtn.addEventListener('click', () => this.navigatePeriod(1));

        // Facility filter
        const groundSelector = document.getElementById('groundSelector');
        if (groundSelector) {
            groundSelector.addEventListener('change', (e) => {
                this.selectedFacilityId = e.target.value || null;
                this.loadSchedule();
            });
        }
    }

    switchView(view) {
        this.currentView = view;

        // Update active button
        document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
        const viewBtn = document.querySelector(`[data-view="${view}"]`);
        if (viewBtn) viewBtn.classList.add('active');

        // Show/hide the appropriate view container
        document.getElementById('weekView').style.display = view === 'week' ? 'block' : 'none';
        document.getElementById('dayView').style.display = view === 'day' ? 'block' : 'none';
        document.getElementById('monthView').style.display = view === 'month' ? 'block' : 'none';

        this.renderCurrentView();
    }

    async loadSchedule() {
        try {
            console.log('Loading schedule...');

            // Calculate date range based on current view
            let startDate, endDate;

            if (this.currentView === 'week') {
                const weekStart = this.getWeekStart(this.currentDate);
                startDate = weekStart.toISOString().split('T')[0];
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekStart.getDate() + 6);
                endDate = weekEnd.toISOString().split('T')[0];
            } else if (this.currentView === 'month') {
                const monthStart = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
                const monthEnd = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
                startDate = monthStart.toISOString().split('T')[0];
                endDate = monthEnd.toISOString().split('T')[0];
            } else { // day view
                startDate = this.currentDate.toISOString().split('T')[0];
                endDate = startDate;
            }

            // Build query params
            const params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate
            });

            if (this.selectedFacilityId) {
                params.append('facility_id', this.selectedFacilityId);
            }

            console.log(`Fetching schedule: ${startDate} to ${endDate}`, this.selectedFacilityId ? `for facility ${this.selectedFacilityId}` : 'all facilities');

            const response = await fetch(`/api/ground-owner/schedule?${params.toString()}`);
            const data = await response.json();

            console.log('Schedule API response:', data);

            if (data.success) {
                this.bookings = data.schedule.bookings || [];
                this.facilities = data.schedule.facilities || [];
                this.stats = data.schedule.stats || {};

                console.log(`Loaded ${this.bookings.length} bookings, ${this.facilities.length} facilities`);

                // Update stats display
                this.updateStats();

                // Update facility dropdown
                this.updateFacilityDropdown();

                // Render the schedule
                this.renderCurrentView();

                if (this.bookings.length === 0) {
                    console.log('No bookings found for this date range');
                }
            } else {
                throw new Error(data.message || 'Failed to load schedule');
            }

        } catch (error) {
            console.error('Error loading schedule:', error);
            this.showToast('Error loading schedule data. Please check console for details.', 'error');
        }
    }

    updateStats() {
        // Update stat cards with meaningful data
        const bookedSlotsEl = document.getElementById('bookedSlots');
        const availableSlotsEl = document.getElementById('availableSlots');
        const occupancyRateEl = document.getElementById('occupancyRate');

        // Booked slots = confirmed and pending bookings
        const bookedCount = this.bookings.filter(b =>
            b.status === 'confirmed' || b.status === 'pending'
        ).length;

        // Total bookings in the period (including cancelled)
        const totalBookings = this.bookings.length;

        // Calculate occupancy rate
        const occupancyRate = totalBookings > 0 ?
            Math.round((bookedCount / totalBookings) * 100) : 0;

        if (bookedSlotsEl) bookedSlotsEl.textContent = bookedCount;
        if (availableSlotsEl) availableSlotsEl.textContent = totalBookings;
        if (occupancyRateEl) occupancyRateEl.textContent = `${occupancyRate}%`;
    }

    updateFacilityDropdown() {
        const groundSelector = document.getElementById('groundSelector');
        if (!groundSelector || this.facilities.length === 0) return;

        // Clear existing options (except "All Grounds")
        groundSelector.innerHTML = '<option value="">All Grounds</option>';

        // Add facility options
        this.facilities.forEach(facility => {
            const option = document.createElement('option');
            option.value = facility.id;
            option.textContent = facility.name;
            if (this.selectedFacilityId == facility.id) {
                option.selected = true;
            }
            groundSelector.appendChild(option);
        });
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

        const container = document.getElementById('weekCalendarBody');
        if (!container) {
            console.error('Week calendar body container not found');
            return;
        }

        const timeSlots = this.generateTimeSlots();

        // Create a wrapper for the grid layout
        let html = '<div class="week-grid" style="display: flex; min-height: 600px;">';

        // Time column
        html += '<div class="time-column" style="width: 80px; border-right: 1px solid #e5e7eb;">';
        html += '<div style="height: 60px;"></div>'; // Spacer for header
        timeSlots.forEach(slot => {
            html += `<div class="time-slot-label" style="height: 60px; padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 12px;">${slot}</div>`;
        });
        html += '</div>';

        // Day columns
        html += '<div class="days-container" style="display: flex; flex: 1;">';
        weekDays.forEach(day => {
            const isToday = this.isToday(day);
            html += `<div class="day-column" data-date="${day.toISOString().split('T')[0]}" style="flex: 1; border-right: 1px solid #e5e7eb;">`;

            // Day header
            html += `<div class="day-header" style="height: 60px; padding: 8px; border-bottom: 2px solid ${isToday ? '#3b82f6' : '#e5e7eb'}; text-align: center; ${isToday ? 'background: #eff6ff;' : ''}">
                        <div class="day-name" style="font-size: 12px; color: #6b7280;">${day.toLocaleDateString('en-US', { weekday: 'short' })}</div>
                        <div class="day-number" style="font-size: 18px; font-weight: bold; ${isToday ? 'color: #3b82f6;' : ''}">${day.getDate()}</div>
                     </div>`;

            // Time slots for this day
            timeSlots.forEach(slot => {
                const booking = this.getBookingForSlot(day, slot);
                const slotClass = booking ? `time-slot has-booking status-${booking.status}` : 'time-slot';

                let bgColor = '#ffffff';
                if (booking) {
                    bgColor = booking.status === 'confirmed' ? '#dcfce7' :
                              booking.status === 'pending' ? '#fef3c7' :
                              booking.status === 'cancelled' ? '#fee2e2' : '#f3f4f6';
                }

                html += `<div class="${slotClass}" data-time="${slot}" style="height: 60px; padding: 4px; border-bottom: 1px solid #e5e7eb; font-size: 11px; background: ${bgColor}; overflow: hidden; cursor: pointer;" title="${booking ? `${booking.facility_name} - ${booking.first_name} ${booking.last_name}` : 'Available'}">`;

                if (booking) {
                    html += `
                        <div class="booking-info" style="height: 100%; overflow: hidden;">
                            <div style="font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${booking.facility_name}</div>
                            <div style="font-size: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${booking.first_name} ${booking.last_name}</div>
                        </div>
                    `;
                }

                html += '</div>';
            });

            html += '</div>';
        });
        html += '</div>'; // Close days-container
        html += '</div>'; // Close week-grid

        container.innerHTML = html;
        console.log(`Rendered week view with ${this.bookings.length} bookings`);
    }

    renderDayView() {
        const day = new Date(this.currentDate);
        const container = document.getElementById('daySchedule');
        if (!container) {
            console.error('Day schedule container not found');
            return;
        }

        const dayBookings = this.getDayBookings(day);
        const timeSlots = this.generateTimeSlots();

        // Professional layout with summary card
        let html = `
            <div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
                <!-- Day Summary Card -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 24px; margin-bottom: 24px; color: white; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h2 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 700;">${day.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}</h2>
                            <p style="margin: 0; opacity: 0.9; font-size: 16px;">${dayBookings.length} booking${dayBookings.length !== 1 ? 's' : ''} scheduled</p>
                        </div>
                        <div style="text-align: right;">
                            <div style="display: flex; gap: 16px;">
                                <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 24px; font-weight: 700;">${dayBookings.filter(b => b.status === 'confirmed').length}</div>
                                    <div style="font-size: 12px; opacity: 0.9;">Confirmed</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                                    <div style="font-size: 24px; font-weight: 700;">${dayBookings.filter(b => b.status === 'pending').length}</div>
                                    <div style="font-size: 12px; opacity: 0.9;">Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline View -->
                <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        `;

        if (dayBookings.length === 0) {
            html += `
                <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
                    <div style="font-size: 64px; margin-bottom: 16px;">📅</div>
                    <h3 style="margin: 0 0 8px 0; font-size: 20px; color: #6b7280;">No Bookings Today</h3>
                    <p style="margin: 0; font-size: 14px;">All time slots are available</p>
                </div>
            `;
        } else {
            timeSlots.forEach(slot => {
                const booking = this.getBookingForSlot(day, slot);

                if (booking) {
                    const statusConfig = {
                        'confirmed': { color: '#10b981', bg: '#d1fae5', icon: '✓', label: 'Confirmed' },
                        'pending': { color: '#f59e0b', bg: '#fef3c7', icon: '⏱', label: 'Pending' },
                        'cancelled': { color: '#ef4444', bg: '#fee2e2', icon: '✕', label: 'Cancelled' },
                        'completed': { color: '#6b7280', bg: '#f3f4f6', icon: '✓', label: 'Completed' }
                    };

                    const config = statusConfig[booking.status] || statusConfig['pending'];

                    html += `
                        <div style="display: flex; gap: 20px; margin-bottom: 20px; padding: 20px; background: ${config.bg}; border-radius: 12px; border-left: 4px solid ${config.color}; transition: all 0.3s ease; cursor: pointer;"
                             onmouseover="this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'"
                             onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='none'">

                            <!-- Time Column -->
                            <div style="min-width: 100px; text-align: center;">
                                <div style="font-size: 24px; font-weight: 700; color: ${config.color}; margin-bottom: 4px;">${booking.start_time.substring(0, 5)}</div>
                                <div style="font-size: 12px; color: #6b7280;">to</div>
                                <div style="font-size: 18px; font-weight: 600; color: #6b7280;">${booking.end_time.substring(0, 5)}</div>
                                <div style="margin-top: 8px; padding: 4px 8px; background: white; border-radius: 6px; font-size: 11px; font-weight: 600; color: ${config.color};">
                                    ${booking.duration_hours}h
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                    <h3 style="margin: 0; font-size: 20px; font-weight: 700; color: #111827;">
                                        <i class="fas fa-map-marker-alt" style="color: ${config.color}; margin-right: 8px;"></i>
                                        ${booking.facility_name}
                                    </h3>
                                    <span style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; background: ${config.color}; color: white; display: flex; align-items: center; gap: 6px;">
                                        <span>${config.icon}</span>
                                        ${config.label}
                                    </span>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user" style="color: ${config.color}; width: 20px;"></i>
                                        <span style="font-size: 14px; color: #374151;"><strong>${booking.first_name} ${booking.last_name}</strong></span>
                                    </div>
                                    ${booking.phone ? `
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-phone" style="color: ${config.color}; width: 20px;"></i>
                                            <a href="tel:${booking.phone}" style="font-size: 14px; color: #374151; text-decoration: none;">${booking.phone}</a>
                                        </div>
                                    ` : ''}
                                    ${booking.email ? `
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-envelope" style="color: ${config.color}; width: 20px;"></i>
                                            <a href="mailto:${booking.email}" style="font-size: 14px; color: #374151; text-decoration: none;">${booking.email}</a>
                                        </div>
                                    ` : ''}
                                </div>

                                ${booking.special_requests ? `
                                    <div style="margin-top: 12px; padding: 12px; background: white; border-radius: 8px; border-left: 3px solid ${config.color};">
                                        <div style="font-size: 12px; font-weight: 600; color: #6b7280; margin-bottom: 4px; text-transform: uppercase;">Special Requests</div>
                                        <div style="font-size: 14px; color: #374151;">${booking.special_requests}</div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }
            });
        }

        html += `
                </div>
            </div>
        `;

        container.innerHTML = html;
        console.log(`Rendered professional day view for ${day.toDateString()} with ${dayBookings.length} bookings`);
    }

    renderMonthView() {
        const monthStart = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
        const monthEnd = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
        const calendarStart = this.getWeekStart(monthStart);

        const container = document.getElementById('monthCalendar');
        if (!container) {
            console.error('Month calendar container not found');
            return;
        }

        // Calculate month statistics
        const monthBookings = this.bookings.filter(b => {
            const bookingDate = new Date(b.booking_date);
            return bookingDate.getMonth() === this.currentDate.getMonth() &&
                   bookingDate.getFullYear() === this.currentDate.getFullYear();
        });

        const confirmedCount = monthBookings.filter(b => b.status === 'confirmed').length;
        const pendingCount = monthBookings.filter(b => b.status === 'pending').length;

        let html = `
            <div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
                <!-- Month Summary Card -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 24px; margin-bottom: 24px; color: white; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                        <div>
                            <h2 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 700;">${this.currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</h2>
                            <p style="margin: 0; opacity: 0.9; font-size: 16px;">${monthBookings.length} total booking${monthBookings.length !== 1 ? 's' : ''} this month</p>
                        </div>
                        <div style="display: flex; gap: 16px;">
                            <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 12px; backdrop-filter: blur(10px); text-align: center;">
                                <div style="font-size: 28px; font-weight: 700;">${confirmedCount}</div>
                                <div style="font-size: 12px; opacity: 0.9;">Confirmed</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 12px; backdrop-filter: blur(10px); text-align: center;">
                                <div style="font-size: 28px; font-weight: 700;">${pendingCount}</div>
                                <div style="font-size: 12px; opacity: 0.9;">Pending</div>
                            </div>
                            <div style="background: rgba(255,255,255,0.2); padding: 12px 20px; border-radius: 12px; backdrop-filter: blur(10px); text-align: center;">
                                <div style="font-size: 28px; font-weight: 700;">${this.facilities.length}</div>
                                <div style="font-size: 12px; opacity: 0.9;">Facilities</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                    <div class="month-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;">
        `;

        // Day headers with icons
        const dayHeaders = [
            { name: 'Sunday', short: 'Sun', icon: '☀️' },
            { name: 'Monday', short: 'Mon', icon: '💼' },
            { name: 'Tuesday', short: 'Tue', icon: '💼' },
            { name: 'Wednesday', short: 'Wed', icon: '💼' },
            { name: 'Thursday', short: 'Thu', icon: '💼' },
            { name: 'Friday', short: 'Fri', icon: '💼' },
            { name: 'Saturday', short: 'Sat', icon: '🎉' }
        ];

        dayHeaders.forEach(day => {
            html += `
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 16px 8px; text-align: center; font-weight: 700; font-size: 14px; color: white; border-radius: 8px;">
                    <div>${day.icon}</div>
                    <div style="margin-top: 4px;">${day.short}</div>
                </div>
            `;
        });

        // Calendar days
        let currentDay = new Date(calendarStart);
        for (let week = 0; week < 6; week++) {
            for (let day = 0; day < 7; day++) {
                const isCurrentMonth = currentDay.getMonth() === this.currentDate.getMonth();
                const dayBookings = this.getDayBookings(currentDay);
                const isToday = this.isToday(currentDay);

                let bgColor = '#ffffff';
                let borderColor = '#e5e7eb';
                let textColor = '#111827';

                if (!isCurrentMonth) {
                    bgColor = '#fafafa';
                    textColor = '#d1d5db';
                }

                if (isToday) {
                    bgColor = '#eff6ff';
                    borderColor = '#3b82f6';
                }

                html += `
                    <div class="calendar-day"
                         data-date="${currentDay.toISOString().split('T')[0]}"
                         onclick="scheduleManager.goToDate('${currentDay.toISOString().split('T')[0]}')"
                         style="background: ${bgColor}; padding: 12px; min-height: 120px; border: 2px solid ${borderColor}; border-radius: 12px; cursor: pointer; transition: all 0.2s ease;"
                         onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'"
                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div style="font-weight: 700; font-size: 18px; color: ${textColor}; ${isToday ? 'background: #3b82f6; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;' : ''}">${currentDay.getDate()}</div>
                            ${dayBookings.length > 0 ? `<div style="background: #3b82f6; color: white; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 12px;">${dayBookings.length}</div>` : ''}
                        </div>

                        <div class="day-bookings" style="font-size: 11px; display: flex; flex-direction: column; gap: 4px;">
                `;

                // Show up to 2 bookings with better styling
                dayBookings.slice(0, 2).forEach(booking => {
                    const statusConfig = {
                        'confirmed': { color: '#10b981', icon: '✓' },
                        'pending': { color: '#f59e0b', icon: '⏱' },
                        'cancelled': { color: '#ef4444', icon: '✕' },
                        'completed': { color: '#6b7280', icon: '✓' }
                    };

                    const config = statusConfig[booking.status] || statusConfig['pending'];

                    html += `
                        <div title="${booking.facility_name} - ${booking.first_name} ${booking.last_name}"
                             style="padding: 6px 8px; background: ${config.color}15; border-left: 3px solid ${config.color}; border-radius: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: all 0.2s ease;"
                             onmouseover="this.style.background='${config.color}25'"
                             onmouseout="this.style.background='${config.color}15'">
                            <div style="display: flex; align-items: center; gap: 4px; color: ${config.color}; font-weight: 600; font-size: 10px;">
                                <span>${config.icon}</span>
                                <span>${booking.start_time.substring(0, 5)}</span>
                            </div>
                            <div style="font-size: 10px; color: #374151; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${booking.facility_name}
                            </div>
                        </div>
                    `;
                });

                if (dayBookings.length > 2) {
                    html += `
                        <div style="font-size: 10px; color: #6b7280; font-weight: 600; text-align: center; padding: 4px; background: #f3f4f6; border-radius: 4px;">
                            +${dayBookings.length - 2} more
                        </div>
                    `;
                }

                html += `
                        </div>
                    </div>
                `;
                currentDay.setDate(currentDay.getDate() + 1);
            }
        }

        html += `
                    </div>
                </div>

                <!-- Legend -->
                <div style="display: flex; justify-content: center; gap: 24px; margin-top: 20px; padding: 16px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 16px; height: 16px; background: #10b98120; border-left: 3px solid #10b981; border-radius: 3px;"></div>
                        <span style="font-size: 13px; color: #374151;">Confirmed</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 16px; height: 16px; background: #f59e0b20; border-left: 3px solid #f59e0b; border-radius: 3px;"></div>
                        <span style="font-size: 13px; color: #374151;">Pending</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 16px; height: 16px; background: #ef444420; border-left: 3px solid #ef4444; border-radius: 3px;"></div>
                        <span style="font-size: 13px; color: #374151;">Cancelled</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 24px; height: 24px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">15</div>
                        <span style="font-size: 13px; color: #374151;">Today</span>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = html;
        console.log(`Rendered professional month view with ${monthBookings.length} bookings`);
    }

    goToDate(dateString) {
        this.currentDate = new Date(dateString);
        this.switchView('day');
    }

    generateTimeSlots() {
        const slots = [];
        for (let hour = 6; hour <= 22; hour++) {
            slots.push(`${hour.toString().padStart(2, '0')}:00`);
        }
        return slots;
    }

    getBookingForSlot(date, time) {
        const dateStr = date.toISOString().split('T')[0];

        return this.bookings.find(booking => {
            if (booking.booking_date !== dateStr) return false;

            // Parse times (format: HH:MM:SS)
            const bookingStart = booking.start_time.substring(0, 5); // Get HH:MM
            const bookingEnd = booking.end_time.substring(0, 5);
            const slotTime = time;

            // Check if slot time falls within booking time range
            return slotTime >= bookingStart && slotTime < bookingEnd;
        });
    }

    getDayBookings(date) {
        const dateStr = date.toISOString().split('T')[0];
        return this.bookings.filter(booking => booking.booking_date === dateStr);
    }

    isToday(date) {
        const today = new Date();
        return date.toDateString() === today.toDateString();
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
        this.loadSchedule();
    }

    updatePeriodDisplay() {
        const periodDisplay = document.getElementById('currentWeek');
        if (!periodDisplay) return;

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

    showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
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
            animation: slideIn 0.3s ease;
        `;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize when DOM is loaded
let scheduleManager;
document.addEventListener('DOMContentLoaded', () => {
    scheduleManager = new ScheduleManager();
});
