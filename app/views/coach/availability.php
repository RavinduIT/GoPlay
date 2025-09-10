<?php 
$title = 'Availability Management - Coach Dashboard';
$additionalCSS = ['/public/css/coach/availability.css'];
$additionalJS = ['/public/js/pages/coach-availability.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Professional Availability Styles */
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #dbeafe;
        --secondary-color: #10b981;
        --accent-color: #f59e0b;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --success-color: #10b981;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-light: #9ca3af;
        --background-white: #ffffff;
        --background-light: #f9fafb;
        --background-gray: #f3f4f6;
        --border-color: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --border-radius: 8px;
        --border-radius-lg: 12px;
        --transition: all 0.3s ease;
    }

    .dashboard-container {
        display: flex;
        min-height: 100vh;
        background: var(--background-light);
    }

    .main-content {
        flex: 1;
        padding: 2rem;
        margin-left: 280px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin-top: 0.5rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--border-radius);
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--background-white);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--background-gray);
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    /* Calendar Layout */
    .availability-layout {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .calendar-section {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .calendar-header {
        padding: 1.5rem;
        background: var(--primary-color);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .calendar-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }

    .calendar-nav {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .nav-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
    }

    .nav-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .current-month {
        font-weight: 600;
        min-width: 150px;
        text-align: center;
    }

    /* Calendar Grid */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }

    .calendar-day-header {
        padding: 1rem 0.5rem;
        text-align: center;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--background-gray);
        font-size: 0.9rem;
    }

    .calendar-day {
        min-height: 100px;
        padding: 0.5rem;
        border: 1px solid var(--border-color);
        background: var(--background-white);
        cursor: pointer;
        transition: var(--transition);
        position: relative;
    }

    .calendar-day:hover {
        background: var(--background-light);
    }

    .calendar-day.other-month {
        background: var(--background-gray);
        color: var(--text-light);
    }

    .calendar-day.today {
        background: var(--primary-light);
        color: var(--primary-color);
        font-weight: 600;
    }

    .calendar-day.selected {
        background: var(--primary-color);
        color: white;
    }

    .day-number {
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .day-slots {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .time-slot {
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.7rem;
        text-align: center;
        font-weight: 500;
    }

    .slot-available {
        background: rgba(16, 185, 129, 0.2);
        color: var(--success-color);
    }

    .slot-booked {
        background: rgba(239, 68, 68, 0.2);
        color: var(--danger-color);
    }

    .slot-blocked {
        background: rgba(156, 163, 175, 0.2);
        color: var(--text-secondary);
    }

    /* Sidebar */
    .availability-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sidebar-card {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .card-header {
        padding: 1rem 1.5rem;
        background: var(--background-gray);
        border-bottom: 1px solid var(--border-color);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .card-content {
        padding: 1.5rem;
    }

    /* Quick Actions */
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: transparent;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        color: var(--text-primary);
        cursor: pointer;
        transition: var(--transition);
        text-align: left;
        width: 100%;
    }

    .action-btn:hover {
        background: var(--background-light);
        border-color: var(--primary-color);
    }

    .action-icon {
        width: 36px;
        height: 36px;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .action-text {
        flex: 1;
    }

    .action-title {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .action-description {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    /* Upcoming Sessions */
    .upcoming-sessions {
        max-height: 400px;
        overflow-y: auto;
    }

    .session-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }

    .session-item:last-child {
        border-bottom: none;
    }

    .session-time {
        text-align: center;
        min-width: 80px;
    }

    .session-date {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1;
    }

    .session-hour {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .session-details {
        flex: 1;
    }

    .session-client {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .session-type {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .session-status {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-confirmed {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning-color);
    }

    /* Availability Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        background: var(--background-light);
        border-radius: var(--border-radius);
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .page-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .availability-layout {
            grid-template-columns: 1fr;
        }

        .calendar-day {
            min-height: 80px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Availability Management</h1>
                <p class="page-subtitle">Manage your schedule and set availability for client bookings</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="exportSchedule()">
                    <i class="fas fa-download"></i>
                    Export Schedule
                </button>
                <button class="btn btn-primary" onclick="setRecurringAvailability()">
                    <i class="fas fa-repeat"></i>
                    Set Recurring
                </button>
            </div>
        </div>

        <!-- Calendar and Sidebar Layout -->
        <div class="availability-layout">
            <!-- Calendar Section -->
            <div class="calendar-section">
                <div class="calendar-header">
                    <h3 class="calendar-title">Availability Calendar</h3>
                    <div class="calendar-nav">
                        <button class="nav-btn" onclick="previousMonth()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="current-month" id="currentMonth">March 2024</span>
                        <button class="nav-btn" onclick="nextMonth()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                
                <div class="calendar-grid" id="calendarGrid">
                    <!-- Calendar will be generated by JavaScript -->
                </div>
            </div>

            <!-- Sidebar -->
            <div class="availability-sidebar">
                <!-- Quick Actions -->
                <div class="sidebar-card">
                    <div class="card-header">
                        <h4 class="card-title">Quick Actions</h4>
                    </div>
                    <div class="card-content">
                        <div class="quick-actions">
                            <button class="action-btn" onclick="blockTimeOff()">
                                <div class="action-icon">
                                    <i class="fas fa-ban"></i>
                                </div>
                                <div class="action-text">
                                    <div class="action-title">Block Time Off</div>
                                    <div class="action-description">Mark periods as unavailable</div>
                                </div>
                            </button>
                            <button class="action-btn" onclick="addAvailableSlots()">
                                <div class="action-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="action-text">
                                    <div class="action-title">Add Available Slots</div>
                                    <div class="action-description">Open new time slots for booking</div>
                                </div>
                            </button>
                            <button class="action-btn" onclick="copyWeekSchedule()">
                                <div class="action-icon">
                                    <i class="fas fa-copy"></i>
                                </div>
                                <div class="action-text">
                                    <div class="action-title">Copy Week Schedule</div>
                                    <div class="action-description">Duplicate to other weeks</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Availability Stats -->
                <div class="sidebar-card">
                    <div class="card-header">
                        <h4 class="card-title">This Week</h4>
                    </div>
                    <div class="card-content">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value">24</div>
                                <div class="stat-label">Available Hours</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">18</div>
                                <div class="stat-label">Booked Hours</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">75%</div>
                                <div class="stat-label">Utilization</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">6</div>
                                <div class="stat-label">Open Slots</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Sessions -->
                <div class="sidebar-card">
                    <div class="card-header">
                        <h4 class="card-title">Upcoming Sessions</h4>
                    </div>
                    <div class="card-content">
                        <div class="upcoming-sessions" id="upcomingSessions">
                            <!-- Sessions will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Sample data
const currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

// Sample availability data
const availabilityData = {
    '2024-03-11': [
        { time: '09:00', status: 'available' },
        { time: '10:00', status: 'booked' },
        { time: '11:00', status: 'available' },
        { time: '14:00', status: 'available' },
        { time: '15:00', status: 'available' }
    ],
    '2024-03-12': [
        { time: '08:00', status: 'available' },
        { time: '09:00', status: 'booked' },
        { time: '10:00', status: 'booked' },
        { time: '16:00', status: 'available' }
    ],
    '2024-03-13': [
        { time: '09:00', status: 'available' },
        { time: '10:00', status: 'available' },
        { time: '11:00', status: 'blocked' },
        { time: '14:00', status: 'available' }
    ],
    '2024-03-14': [
        { time: '08:00', status: 'available' },
        { time: '09:00', status: 'available' },
        { time: '15:00', status: 'booked' },
        { time: '16:00', status: 'booked' }
    ],
    '2024-03-15': [
        { time: '10:00', status: 'available' },
        { time: '11:00', status: 'available' },
        { time: '14:00', status: 'booked' }
    ]
};

// Sample upcoming sessions
const upcomingSessions = [
    {
        id: 1,
        client: "John Davidson",
        type: "Personal Training",
        date: "2024-03-11",
        time: "10:00",
        status: "confirmed"
    },
    {
        id: 2,
        client: "Sarah Mitchell",
        type: "Fitness Assessment",
        date: "2024-03-12",
        time: "09:00",
        status: "confirmed"
    },
    {
        id: 3,
        client: "Mike Rodriguez",
        type: "Group Training",
        date: "2024-03-12",
        time: "10:00",
        status: "pending"
    },
    {
        id: 4,
        client: "Emma Wilson",
        type: "Consultation",
        date: "2024-03-14",
        time: "15:00",
        status: "confirmed"
    },
    {
        id: 5,
        client: "David Parker",
        type: "Personal Training",
        date: "2024-03-14",
        time: "16:00",
        status: "confirmed"
    },
    {
        id: 6,
        client: "Lisa Chen",
        type: "Yoga Session",
        date: "2024-03-15",
        time: "14:00",
        status: "confirmed"
    }
];

// Generate calendar
function generateCalendar() {
    const grid = document.getElementById('calendarGrid');
    const monthElement = document.getElementById('currentMonth');
    
    monthElement.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    // Clear grid
    grid.innerHTML = '';
    
    // Add day headers
    dayNames.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        grid.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();
    
    // Add previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        const dayElement = createDayElement(day, true, currentMonth - 1);
        grid.appendChild(dayElement);
    }
    
    // Add current month days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = createDayElement(day, false, currentMonth);
        grid.appendChild(dayElement);
    }
    
    // Add next month days
    const totalCells = 42; // 6 rows × 7 days
    const cellsUsed = firstDay + daysInMonth;
    for (let day = 1; cellsUsed + day - 1 < totalCells; day++) {
        const dayElement = createDayElement(day, true, currentMonth + 1);
        grid.appendChild(dayElement);
    }
}

// Create day element
function createDayElement(day, otherMonth, month) {
    const dayElement = document.createElement('div');
    dayElement.className = 'calendar-day';
    
    if (otherMonth) {
        dayElement.classList.add('other-month');
    }
    
    // Check if today
    const today = new Date();
    if (!otherMonth && day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
        dayElement.classList.add('today');
    }
    
    const dayNumber = document.createElement('div');
    dayNumber.className = 'day-number';
    dayNumber.textContent = day;
    dayElement.appendChild(dayNumber);
    
    // Add time slots if available
    if (!otherMonth) {
        const dateString = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const dayAvailability = availabilityData[dateString];
        
        if (dayAvailability) {
            const slotsContainer = document.createElement('div');
            slotsContainer.className = 'day-slots';
            
            dayAvailability.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = `time-slot slot-${slot.status}`;
                slotElement.textContent = slot.time;
                slotsContainer.appendChild(slotElement);
            });
            
            dayElement.appendChild(slotsContainer);
        }
    }
    
    dayElement.addEventListener('click', () => selectDay(dayElement, day, otherMonth));
    
    return dayElement;
}

// Select day
function selectDay(element, day, otherMonth) {
    // Remove previous selection
    document.querySelectorAll('.calendar-day.selected').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selection to clicked day
    element.classList.add('selected');
    
    if (!otherMonth) {
        console.log(`Selected day: ${day}`);
        // Here you could show day details or open time slot management
    }
}

// Render upcoming sessions
function renderUpcomingSessions() {
    const container = document.getElementById('upcomingSessions');
    
    // Sort sessions by date and time
    const sortedSessions = upcomingSessions
        .sort((a, b) => new Date(a.date + ' ' + a.time) - new Date(b.date + ' ' + b.time))
        .slice(0, 5); // Show only next 5 sessions
    
    container.innerHTML = sortedSessions.map(session => `
        <div class="session-item">
            <div class="session-time">
                <div class="session-date">${formatDate(session.date)}</div>
                <div class="session-hour">${session.time}</div>
            </div>
            <div class="session-details">
                <div class="session-client">${session.client}</div>
                <div class="session-type">${session.type}</div>
            </div>
            <div class="session-status status-${session.status}">
                ${session.status.charAt(0).toUpperCase() + session.status.slice(1)}
            </div>
        </div>
    `).join('');
}

// Navigation functions
function previousMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar();
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    const month = date.toLocaleDateString('en-US', { month: 'short' });
    const day = date.getDate();
    return `${month} ${day}`;
}

// Action functions
function blockTimeOff() {
    alert('Block Time Off - This would open a modal to select dates and times to block');
}

function addAvailableSlots() {
    alert('Add Available Slots - This would open a modal to add new time slots');
}

function copyWeekSchedule() {
    alert('Copy Week Schedule - This would allow copying the current week to other weeks');
}

function setRecurringAvailability() {
    alert('Set Recurring Availability - This would open a modal to set weekly recurring patterns');
}

function exportSchedule() {
    alert('Export Schedule - This would generate a downloadable calendar file');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    generateCalendar();
    renderUpcomingSessions();
});
</script>