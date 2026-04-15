<?php
$_base = defined('BASE_URL') ? BASE_URL : ''; $currentPage = 'sessions'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Training Session - GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/coach/sidebar.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #0891b2;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --background-light: #f9fafb;
            --background-white: #ffffff;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --transition: all 0.2s ease-in-out;
        }

        .main-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .booking-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .booking-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .booking-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .booking-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        .booking-form {
            background: var(--background-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .form-section {
            padding: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        label {
            display: block;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .required {
            color: var(--danger-color);
        }

        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--background-white);
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            z-index: 1;
        }

        .input-group input {
            padding-left: 2.5rem;
        }

        .session-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .session-type-card {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--background-white);
        }

        .session-type-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .session-type-card.selected {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .session-type-card i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .session-type-card h4 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .session-type-card p {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 0.5rem;
        }

        .time-slot {
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--background-white);
            font-weight: 500;
        }

        .time-slot:hover {
            border-color: var(--primary-color);
        }

        .time-slot.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .time-slot.unavailable {
            background: var(--background-light);
            color: var(--text-light);
            cursor: not-allowed;
        }

        .booking-summary {
            background: var(--background-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }

        .summary-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .summary-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .summary-content {
            padding: 1.5rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-color);
        }

        .summary-item label {
            margin: 0;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .summary-item span {
            font-weight: 600;
            color: var(--text-primary);
        }

        .action-buttons {
            padding: 1.5rem;
            background: var(--background-light);
            border-radius: 0 0 var(--border-radius) var(--border-radius);
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 1rem;
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

        .btn-primary:disabled {
            background: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }

        .equipment-checklist {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
        }

        .equipment-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            background: var(--background-light);
        }

        .equipment-item input[type="checkbox"] {
            width: auto;
        }

        .duration-pills {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .duration-pill {
            padding: 0.5rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 2rem;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            background: var(--background-white);
        }

        .duration-pill:hover {
            border-color: var(--primary-color);
        }

        .duration-pill.selected {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        @media (max-width: 768px) {
            .booking-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .main-content {
                padding: 1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .session-type-grid {
                grid-template-columns: 1fr;
            }

            .time-slots {
                grid-template-columns: repeat(3, 1fr);
            }

            .booking-summary {
                position: static;
            }
        }
    </style>
</head>
<body>
    <div class="coach-dashboard">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="booking-header">
                <h1><i class="fas fa-calendar-plus"></i> Book Training Session</h1>
                <p>Schedule a professional training session with your clients</p>
            </div>

            <div class="booking-container">
                <div class="booking-form">
                    <form id="sessionBookingForm">
                        <!-- Session Type Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-dumbbell"></i>
                                Session Type
                            </h3>
                            <div class="session-type-grid">
                                <div class="session-type-card" data-type="individual">
                                    <i class="fas fa-user"></i>
                                    <h4>Individual Training</h4>
                                    <p>One-on-one personalized coaching</p>
                                </div>
                                <div class="session-type-card" data-type="group">
                                    <i class="fas fa-users"></i>
                                    <h4>Group Session</h4>
                                    <p>Train multiple clients together</p>
                                </div>
                                <div class="session-type-card" data-type="assessment">
                                    <i class="fas fa-clipboard-check"></i>
                                    <h4>Skills Assessment</h4>
                                    <p>Evaluate client's current level</p>
                                </div>
                            </div>
                        </div>

                        <!-- Client Information Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-user-friends"></i>
                                Client Information
                            </h3>
                            <div class="form-group">
                                <label for="clientSelect">Select Client <span class="required">*</span></label>
                                <select id="clientSelect" required>
                                    <option value="">Choose a client...</option>
                                    <option value="1">John Smith - Cricket</option>
                                    <option value="2">Sarah Johnson - Tennis</option>
                                    <option value="3">Mike Wilson - Football</option>
                                </select>
                            </div>
                            <div class="form-group" id="groupSizeGroup" style="display: none;">
                                <label for="groupSize">Number of Participants</label>
                                <input type="number" id="groupSize" min="2" max="10" placeholder="Enter group size">
                            </div>
                        </div>

                        <!-- Schedule Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-clock"></i>
                                Schedule & Duration
                            </h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sessionDate">Session Date <span class="required">*</span></label>
                                    <input type="date" id="sessionDate" required>
                                </div>
                                <div class="form-group">
                                    <label>Duration</label>
                                    <div class="duration-pills">
                                        <div class="duration-pill" data-duration="30">30 min</div>
                                        <div class="duration-pill selected" data-duration="60">1 hour</div>
                                        <div class="duration-pill" data-duration="90">1.5 hours</div>
                                        <div class="duration-pill" data-duration="120">2 hours</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Available Time Slots</label>
                                <div class="time-slots" id="timeSlots">
                                    <div class="time-slot" data-time="08:00">08:00 AM</div>
                                    <div class="time-slot" data-time="09:00">09:00 AM</div>
                                    <div class="time-slot" data-time="10:00">10:00 AM</div>
                                    <div class="time-slot" data-time="11:00">11:00 AM</div>
                                    <div class="time-slot" data-time="14:00">02:00 PM</div>
                                    <div class="time-slot" data-time="15:00">03:00 PM</div>
                                    <div class="time-slot" data-time="16:00">04:00 PM</div>
                                    <div class="time-slot" data-time="17:00">05:00 PM</div>
                                </div>
                            </div>
                        </div>

                        <!-- Session Details Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Session Details
                            </h3>
                            <div class="form-group">
                                <label for="sessionTitle">Session Title <span class="required">*</span></label>
                                <input type="text" id="sessionTitle" placeholder="e.g., Cricket Batting Fundamentals" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sessionLocation">Location <span class="required">*</span></label>
                                    <select id="sessionLocation" required>
                                        <option value="">Select location...</option>
                                        <option value="ground-a">Main Cricket Ground</option>
                                        <option value="ground-b">Practice Nets</option>
                                        <option value="indoor">Indoor Training Hall</option>
                                        <option value="client-location">Client's Location</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sessionRate">Session Rate (₹) <span class="required">*</span></label>
                                    <div class="input-group">
                                        <i class="fas fa-rupee-sign"></i>
                                        <input type="number" id="sessionRate" placeholder="Enter your rate" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="sessionObjectives">Session Objectives</label>
                                <textarea id="sessionObjectives" placeholder="Describe the goals and focus areas for this session..."></textarea>
                            </div>
                        </div>

                        <!-- Equipment Section -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-toolbox"></i>
                                Equipment & Requirements
                            </h3>
                            <div class="form-group">
                                <label>Equipment Needed</label>
                                <div class="equipment-checklist">
                                    <div class="equipment-item">
                                        <input type="checkbox" id="bats" value="bats">
                                        <label for="bats">Cricket Bats</label>
                                    </div>
                                    <div class="equipment-item">
                                        <input type="checkbox" id="balls" value="balls">
                                        <label for="balls">Cricket Balls</label>
                                    </div>
                                    <div class="equipment-item">
                                        <input type="checkbox" id="pads" value="pads">
                                        <label for="pads">Batting Pads</label>
                                    </div>
                                    <div class="equipment-item">
                                        <input type="checkbox" id="gloves" value="gloves">
                                        <label for="gloves">Gloves</label>
                                    </div>
                                    <div class="equipment-item">
                                        <input type="checkbox" id="cones" value="cones">
                                        <label for="cones">Training Cones</label>
                                    </div>
                                    <div class="equipment-item">
                                        <input type="checkbox" id="stumps" value="stumps">
                                        <label for="stumps">Stumps</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="specialRequirements">Special Requirements</label>
                                <textarea id="specialRequirements" placeholder="Any special requirements or notes for this session..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Booking Summary -->
                <div class="booking-summary">
                    <div class="summary-header">
                        <h3><i class="fas fa-receipt"></i> Booking Summary</h3>
                    </div>
                    <div class="summary-content">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Please fill in the session details to see the summary
                        </div>

                        <div id="summaryDetails" style="display: none;">
                            <div class="summary-item">
                                <label>Session Type:</label>
                                <span id="summaryType">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Client:</label>
                                <span id="summaryClient">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Date:</label>
                                <span id="summaryDate">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Time:</label>
                                <span id="summaryTime">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Duration:</label>
                                <span id="summaryDuration">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Location:</label>
                                <span id="summaryLocation">-</span>
                            </div>
                            <div class="summary-item">
                                <label>Total Amount:</label>
                                <span id="summaryTotal">₹ 0</span>
                            </div>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-primary" id="bookSessionBtn" disabled>
                            <i class="fas fa-calendar-check"></i>
                            Confirm Booking
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Session booking functionality
        class SessionBooking {
            constructor() {
                this.selectedType = null;
                this.selectedTime = null;
                this.selectedDuration = 60;
                this.selectedClient = null;
                this.selectedLocation = null;
                this.sessionRate = 0;

                this.initializeEventListeners();
                this.updateSummary();
            }

            initializeEventListeners() {
                // Session type selection
                document.querySelectorAll('.session-type-card').forEach(card => {
                    card.addEventListener('click', () => {
                        document.querySelectorAll('.session-type-card').forEach(c => c.classList.remove('selected'));
                        card.classList.add('selected');
                        this.selectedType = card.dataset.type;
                        this.toggleGroupSize(this.selectedType === 'group');
                        this.updateSummary();
                    });
                });

                // Duration selection
                document.querySelectorAll('.duration-pill').forEach(pill => {
                    pill.addEventListener('click', () => {
                        document.querySelectorAll('.duration-pill').forEach(p => p.classList.remove('selected'));
                        pill.classList.add('selected');
                        this.selectedDuration = parseInt(pill.dataset.duration);
                        this.updateSummary();
                    });
                });

                // Time slot selection
                document.querySelectorAll('.time-slot').forEach(slot => {
                    if (!slot.classList.contains('unavailable')) {
                        slot.addEventListener('click', () => {
                            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                            slot.classList.add('selected');
                            this.selectedTime = slot.dataset.time;
                            this.updateSummary();
                        });
                    }
                });

                // Form inputs
                document.getElementById('clientSelect').addEventListener('change', (e) => {
                    this.selectedClient = e.target.value;
                    this.updateSummary();
                });

                document.getElementById('sessionLocation').addEventListener('change', (e) => {
                    this.selectedLocation = e.target.value;
                    this.updateSummary();
                });

                document.getElementById('sessionRate').addEventListener('input', (e) => {
                    this.sessionRate = parseFloat(e.target.value) || 0;
                    this.updateSummary();
                });

                document.getElementById('sessionDate').addEventListener('change', () => {
                    this.updateSummary();
                });

                // Book session button
                document.getElementById('bookSessionBtn').addEventListener('click', () => {
                    this.bookSession();
                });
            }

            toggleGroupSize(show) {
                const groupSizeGroup = document.getElementById('groupSizeGroup');
                if (show) {
                    groupSizeGroup.style.display = 'block';
                } else {
                    groupSizeGroup.style.display = 'none';
                }
            }

            updateSummary() {
                const summaryDetails = document.getElementById('summaryDetails');
                const bookBtn = document.getElementById('bookSessionBtn');
                const clientSelect = document.getElementById('clientSelect');
                const sessionDate = document.getElementById('sessionDate');

                // Check if all required fields are filled
                const isComplete = this.selectedType && this.selectedClient && this.selectedTime &&
                                 this.selectedLocation && this.sessionRate > 0 && sessionDate.value;

                if (isComplete) {
                    summaryDetails.style.display = 'block';
                    document.querySelector('.alert-info').style.display = 'none';
                    bookBtn.disabled = false;

                    // Update summary values
                    document.getElementById('summaryType').textContent = this.getTypeDisplayName(this.selectedType);
                    document.getElementById('summaryClient').textContent = clientSelect.options[clientSelect.selectedIndex].text;
                    document.getElementById('summaryDate').textContent = new Date(sessionDate.value).toLocaleDateString();
                    document.getElementById('summaryTime').textContent = this.formatTime(this.selectedTime);
                    document.getElementById('summaryDuration').textContent = `${this.selectedDuration} minutes`;
                    document.getElementById('summaryLocation').textContent =
                        document.getElementById('sessionLocation').options[document.getElementById('sessionLocation').selectedIndex].text;
                    document.getElementById('summaryTotal').textContent = `₹ ${this.sessionRate.toLocaleString()}`;
                } else {
                    summaryDetails.style.display = 'none';
                    document.querySelector('.alert-info').style.display = 'flex';
                    bookBtn.disabled = true;
                }
            }

            getTypeDisplayName(type) {
                const types = {
                    'individual': 'Individual Training',
                    'group': 'Group Session',
                    'assessment': 'Skills Assessment'
                };
                return types[type] || type;
            }

            formatTime(time24) {
                const [hours, minutes] = time24.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour % 12 || 12;
                return `${hour12}:${minutes} ${ampm}`;
            }

            async bookSession() {
                const formData = {
                    type: this.selectedType,
                    clientId: this.selectedClient,
                    date: document.getElementById('sessionDate').value,
                    time: this.selectedTime,
                    duration: this.selectedDuration,
                    location: this.selectedLocation,
                    rate: this.sessionRate,
                    title: document.getElementById('sessionTitle').value,
                    objectives: document.getElementById('sessionObjectives').value,
                    equipment: this.getSelectedEquipment(),
                    specialRequirements: document.getElementById('specialRequirements').value,
                    groupSize: document.getElementById('groupSize').value
                };

                try {
                    // Show loading state
                    const bookBtn = document.getElementById('bookSessionBtn');
                    const originalText = bookBtn.innerHTML;
                    bookBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';
                    bookBtn.disabled = true;

                    // Simulate API call
                    await new Promise(resolve => setTimeout(resolve, 2000));

                    // Success feedback
                    bookBtn.innerHTML = '<i class="fas fa-check"></i> Booked Successfully!';
                    bookBtn.style.background = 'var(--success-color)';

                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success';
                    alert.innerHTML = '<i class="fas fa-check-circle"></i> Session booked successfully! You and your client will receive confirmation emails.';
                    document.querySelector('.summary-content').prepend(alert);

                    // Reset form after delay
                    setTimeout(() => {
                        window.location.href=(window.BASE_URL||'')+'/coach/sessions';
                    }, 3000);

                } catch (error) {
                    console.error('Booking error:', error);

                    // Show error message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-warning';
                    alert.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Failed to book session. Please try again.';
                    document.querySelector('.summary-content').prepend(alert);

                    // Reset button
                    const bookBtn = document.getElementById('bookSessionBtn');
                    bookBtn.innerHTML = originalText;
                    bookBtn.disabled = false;
                }
            }

            getSelectedEquipment() {
                const equipment = [];
                document.querySelectorAll('.equipment-item input:checked').forEach(checkbox => {
                    equipment.push(checkbox.value);
                });
                return equipment;
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new SessionBooking();

            // Set minimum date to today
            document.getElementById('sessionDate').min = new Date().toISOString().split('T')[0];
        });
    </script>
</body>
</html>