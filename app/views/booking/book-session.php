<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
use App\Models\Coach;

$title = 'Book Training Session - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];

// Get coach ID from URL parameter
$coach_id = $_GET['coach_id'] ?? null;

// Fetch coach data from database
$selected_coach = null;
$coaches = [];

try {
    $coachModel = new Coach();

    if ($coach_id) {
        // If coach_id is provided, load only that specific coach with user details
        $coachData = $coachModel->getWithDetails((int)$coach_id);

        if ($coachData) {
            $selected_coach = [
                'id' => $coachData['id'],
                'name' => $coachData['first_name'] . ' ' . $coachData['last_name'],
                'sport' => $coachData['sport_name'] ?? 'General',
                'experience' => $coachData['experience_years'] . ' Years',
                'rating' => round($coachData['rating'] ?? 4.5, 1),
                'reviews' => $coachData['total_reviews'] ?? 0,
                'price' => $coachData['hourly_rate'],
                'location' => $coachData['location'] ?? 'Sri Lanka',
                'bio' => $coachData['bio'] ?? 'Professional coach with extensive experience.',
                'profile_picture' => $coachData['profile_picture'] ?? null,
                'specialties' => !empty($coachData['specializations']) ? explode(', ', $coachData['specializations']) : ['Training', 'Development'],
                'available_times' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00']
            ];

            // Add to coaches array for JavaScript access
            $coaches[$coachData['id']] = $selected_coach;
        } else {
            error_log("Coach not found with ID: " . $coach_id);
        }
    } else {
        // If no coach_id provided, load all available coaches
        $allCoaches = $coachModel->getAvailable();

        foreach ($allCoaches as $coach) {
            $coaches[$coach['id']] = [
                'id' => $coach['id'],
                'name' => $coach['first_name'] . ' ' . $coach['last_name'],
                'sport' => $coach['sport_name'] ?? 'General',
                'experience' => $coach['experience_years'] . ' Years',
                'rating' => round($coach['rating'] ?? 4.5, 1),
                'reviews' => $coach['total_reviews'] ?? 0,
                'price' => $coach['hourly_rate'],
                'location' => $coach['location'] ?? 'Sri Lanka',
                'bio' => $coach['bio'] ?? 'Professional coach.',
                'profile_picture' => $coach['profile_picture'] ?? null,
                'specialties' => !empty($coach['specializations']) ? explode(', ', $coach['specializations']) : ['Training'],
                'available_times' => ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00']
            ];
        }
    }
} catch (Exception $e) {
    // Fallback to empty array if database connection fails
    $coaches = [];
    error_log("Error loading coaches: " . $e->getMessage());
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        #main-content {
            background: var(--background-light);
            min-height: 100vh;
            padding-top: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Professional Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: center;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.95;
            text-align: center;
        }

        /* Coach Header Banner */
        .coach-header-banner {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 2rem;
            border: 2px solid var(--primary-light);
        }

        .coach-header-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .coach-header-info {
            flex: 1;
        }

        .coach-header-name {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .verified-badge {
            background: var(--success-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .coach-header-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .coach-header-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .coach-header-meta-item i {
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .coach-header-meta-item strong {
            color: var(--text-primary);
        }

        .coach-header-price {
            background: var(--primary-light);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            text-align: center;
            min-width: 180px;
        }

        .coach-header-price-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .coach-header-price-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .coach-header-price-per {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .booking-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        .coach-selection, .booking-form {
            background: var(--background-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
            color: white !important;
            padding: 1.5rem;
            text-align: left;
            border-bottom: 3px solid var(--primary-dark);
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-header h2 i {
            font-size: 1.3rem;
        }

        .section-header p {
            opacity: 0.9;
            font-size: 0.95rem;
            margin-left: 2.1rem;
        }

        .section-content {
            padding: 2rem;
        }

        /* Coach Selection */
        .coach-grid {
            display: grid;
            gap: 1rem;
            max-height: 500px;
            overflow-y: auto;
        }

        .coach-card {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .coach-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .coach-card.selected {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .coach-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .coach-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .coach-details h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .coach-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 0.5rem 0;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .coach-meta span {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .coach-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-top: 0.5rem;
        }

        /* Booking Form */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            min-height: 80px;
        }

        .session-types {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
        }

        .session-type {
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--background-white);
        }

        .session-type:hover {
            border-color: var(--primary-color);
        }

        .session-type.selected {
            border-color: var(--primary-color);
            background: var(--primary-light);
        }

        .session-type i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            display: block;
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
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
            background: var(--background-light);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-color);
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
            margin-top: 1.5rem;
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

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        @media (max-width: 1024px) {
            .booking-container {
                grid-template-columns: 1fr;
            }

            .coach-header-banner {
                flex-direction: column;
                text-align: center;
            }

            .coach-header-info {
                text-align: center;
            }

            .coach-header-name {
                justify-content: center;
                flex-wrap: wrap;
            }

            .coach-header-meta {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .booking-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .container {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.75rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .coach-header-banner {
                padding: 1.5rem;
            }

            .coach-header-name {
                font-size: 1.5rem;
            }

            .coach-header-meta {
                flex-direction: column;
                gap: 0.75rem;
                align-items: center;
            }

            .coach-header-price-amount {
                font-size: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .session-types {
                grid-template-columns: 1fr;
            }

            .time-slots {
                grid-template-columns: repeat(3, 1fr);
            }
        }
</style>

<div class="container">
        <a href="<?= $_base ?>/book-coach" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Coach Selection
        </a>

        <div class="page-header">
            <h1><i class="fas fa-calendar-plus"></i> Book Training Session</h1>
            <p>Select a coach and schedule your training session</p>
        </div>

        <?php if ($selected_coach): ?>
        <!-- Coach Header Banner -->
        <div class="coach-header-banner">
            <div class="coach-header-avatar">
                <?= strtoupper(substr($selected_coach['name'], 0, 1)) ?>
            </div>
            <div class="coach-header-info">
                <div class="coach-header-name">
                    <?= htmlspecialchars($selected_coach['name']) ?>
                    <span class="verified-badge">
                        <i class="fas fa-check-circle"></i>
                        Verified
                    </span>
                </div>
                <div class="coach-header-meta">
                    <div class="coach-header-meta-item">
                        <i class="fas fa-trophy"></i>
                        <span><strong><?= htmlspecialchars($selected_coach['sport']) ?></strong> Coach</span>
                    </div>
                    <div class="coach-header-meta-item">
                        <i class="fas fa-award"></i>
                        <span><strong><?= htmlspecialchars($selected_coach['experience']) ?></strong> Experience</span>
                    </div>
                    <div class="coach-header-meta-item">
                        <i class="fas fa-star"></i>
                        <span><strong><?= $selected_coach['rating'] ?>/5.0</strong> (<?= $selected_coach['reviews'] ?> reviews)</span>
                    </div>
                    <div class="coach-header-meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($selected_coach['location']) ?></span>
                    </div>
                </div>
            </div>
            <div class="coach-header-price">
                <div class="coach-header-price-label">Session Rate</div>
                <div class="coach-header-price-amount">LKR <?= number_format($selected_coach['price']) ?></div>
                <div class="coach-header-price-per">per hour</div>
            </div>
        </div>
        <?php endif; ?>

        <div class="booking-container">
            <!-- Coach Selection -->
            <div class="coach-selection">
                <div class="section-header">
                    <h2><i class="fas fa-user-tie"></i> Select Coach</h2>
                    <p>Choose your preferred trainer</p>
                </div>
                <div class="section-content">
                    <div class="coach-grid" id="coachGrid">
                        <?php foreach ($coaches as $coach): ?>
                        <div class="coach-card <?= $selected_coach && $selected_coach['id'] == $coach['id'] ? 'selected' : '' ?>"
                             data-coach-id="<?= $coach['id'] ?>"
                             data-coach-name="<?= htmlspecialchars($coach['name']) ?>"
                             data-coach-price="<?= $coach['price'] ?>"
                             data-coach-sport="<?= htmlspecialchars($coach['sport']) ?>">
                            <div class="coach-info">
                                <div class="coach-avatar">
                                    <?= strtoupper(substr($coach['name'], 0, 1)) ?>
                                </div>
                                <div class="coach-details">
                                    <h3><?= htmlspecialchars($coach['name']) ?></h3>
                                    <div class="coach-meta">
                                        <span><i class="fas fa-trophy"></i> <?= htmlspecialchars($coach['sport']) ?></span>
                                        <span><i class="fas fa-star"></i> <?= $coach['rating'] ?> (<?= $coach['reviews'] ?>)</span>
                                    </div>
                                    <div class="coach-meta">
                                        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($coach['location']) ?></span>
                                        <span><i class="fas fa-clock"></i> <?= htmlspecialchars($coach['experience']) ?></span>
                                    </div>
                                    <div class="coach-price">LKR <?= number_format($coach['price']) ?>/session</div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="booking-form">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-check"></i> Book Session</h2>
                    <p>Complete your booking details</p>
                </div>
                <div class="section-content">
                    <div class="alert alert-info" id="selectCoachAlert">
                        <i class="fas fa-info-circle"></i>
                        Please select a coach to continue
                    </div>

                    <form id="bookingForm" style="display: none;">
                        <div class="form-group">
                            <label>Session Type <span class="required">*</span></label>
                            <div class="session-types">
                                <div class="session-type" data-type="individual">
                                    <i class="fas fa-user"></i>
                                    <div>Individual</div>
                                </div>
                                <div class="session-type" data-type="group">
                                    <i class="fas fa-users"></i>
                                    <div>Group</div>
                                </div>
                                <div class="session-type" data-type="assessment">
                                    <i class="fas fa-clipboard-check"></i>
                                    <div>Assessment</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="sessionDate">Session Date <span class="required">*</span></label>
                                <input type="date" id="sessionDate" required>
                            </div>
                            <div class="form-group">
                                <label for="duration">Duration</label>
                                <select id="duration">
                                    <option value="60">1 Hour</option>
                                    <option value="90">1.5 Hours</option>
                                    <option value="120">2 Hours</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Available Time Slots</label>
                            <div class="time-slots" id="timeSlots">
                                <!-- Time slots will be populated based on selected coach -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="specialRequests">Special Requests (Optional)</label>
                            <textarea id="specialRequests" placeholder="Any specific focus areas or requirements..."></textarea>
                        </div>

                        <div class="booking-summary" id="bookingSummary">
                            <div class="summary-row">
                                <span>Coach:</span>
                                <span id="summaryCoach">-</span>
                            </div>
                            <div class="summary-row">
                                <span>Session Type:</span>
                                <span id="summaryType">-</span>
                            </div>
                            <div class="summary-row">
                                <span>Date & Time:</span>
                                <span id="summaryDateTime">-</span>
                            </div>
                            <div class="summary-row">
                                <span>Duration:</span>
                                <span id="summaryDuration">1 Hour</span>
                            </div>
                            <div class="summary-row">
                                <span>Total Amount:</span>
                                <span id="summaryTotal">LKR 0</span>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" id="proceedToPayment" disabled>
                            <i class="fas fa-credit-card"></i>
                            Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
        class SessionBooking {
            constructor() {
                this.selectedCoach = null;
                this.selectedType = null;
                this.selectedTime = null;
                this.selectedDate = null;
                this.duration = 60;

                this.initializeEventListeners();
                this.setMinDate();

                // Auto-select coach if coach_id is provided
                <?php if ($selected_coach): ?>
                this.selectCoach(<?= $selected_coach['id'] ?>);
                <?php endif; ?>
            }

            initializeEventListeners() {
                // Coach selection
                document.querySelectorAll('.coach-card').forEach(card => {
                    card.addEventListener('click', () => {
                        const coachId = parseInt(card.dataset.coachId);
                        this.selectCoach(coachId);
                    });
                });

                // Session type selection
                document.querySelectorAll('.session-type').forEach(type => {
                    type.addEventListener('click', () => {
                        document.querySelectorAll('.session-type').forEach(t => t.classList.remove('selected'));
                        type.classList.add('selected');
                        this.selectedType = type.dataset.type;
                        this.updateSummary();
                    });
                });

                // Date selection
                document.getElementById('sessionDate').addEventListener('change', (e) => {
                    this.selectedDate = e.target.value;
                    this.updateTimeSlots();
                    this.updateSummary();
                });

                // Duration selection
                document.getElementById('duration').addEventListener('change', (e) => {
                    this.duration = parseInt(e.target.value);
                    this.updateSummary();
                });

                // Proceed to payment
                document.getElementById('proceedToPayment').addEventListener('click', () => {
                    this.proceedToPayment();
                });
            }

            selectCoach(coachId) {
                // Remove previous selection
                document.querySelectorAll('.coach-card').forEach(card => {
                    card.classList.remove('selected');
                });

                // Select new coach
                const coachCard = document.querySelector(`[data-coach-id="${coachId}"]`);
                if (coachCard) {
                    coachCard.classList.add('selected');
                    this.selectedCoach = {
                        id: coachId,
                        name: coachCard.dataset.coachName,
                        price: parseInt(coachCard.dataset.coachPrice),
                        sport: coachCard.dataset.coachSport
                    };

                    // Show booking form
                    document.getElementById('selectCoachAlert').style.display = 'none';
                    document.getElementById('bookingForm').style.display = 'block';

                    this.updateTimeSlots();
                    this.updateSummary();
                }
            }

            updateTimeSlots() {
                const timeSlotsContainer = document.getElementById('timeSlots');
                if (!this.selectedCoach) return;

                // Mock available times (you can replace with dynamic data)
                const availableTimes = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];

                timeSlotsContainer.innerHTML = '';
                availableTimes.forEach(time => {
                    const slot = document.createElement('div');
                    slot.className = 'time-slot';
                    slot.textContent = this.formatTime(time);
                    slot.dataset.time = time;

                    slot.addEventListener('click', () => {
                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                        slot.classList.add('selected');
                        this.selectedTime = time;
                        this.updateSummary();
                    });

                    timeSlotsContainer.appendChild(slot);
                });
            }

            formatTime(time24) {
                const [hours, minutes] = time24.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour % 12 || 12;
                return `${hour12}:${minutes} ${ampm}`;
            }

            updateSummary() {
                if (!this.selectedCoach) return;

                document.getElementById('summaryCoach').textContent = this.selectedCoach.name;
                document.getElementById('summaryType').textContent = this.selectedType ?
                    this.selectedType.charAt(0).toUpperCase() + this.selectedType.slice(1) : '-';

                if (this.selectedDate && this.selectedTime) {
                    const date = new Date(this.selectedDate).toLocaleDateString();
                    const time = this.formatTime(this.selectedTime);
                    document.getElementById('summaryDateTime').textContent = `${date} at ${time}`;
                } else {
                    document.getElementById('summaryDateTime').textContent = '-';
                }

                document.getElementById('summaryDuration').textContent = `${this.duration} minutes`;
                document.getElementById('summaryTotal').textContent = `LKR ${this.selectedCoach.price.toLocaleString()}`;

                // Enable/disable proceed button
                const canProceed = this.selectedCoach && this.selectedType && this.selectedDate && this.selectedTime;
                document.getElementById('proceedToPayment').disabled = !canProceed;
            }

            setMinDate() {
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);

                document.getElementById('sessionDate').min = tomorrow.toISOString().split('T')[0];
            }

            async proceedToPayment() {
                if (!this.selectedCoach || !this.selectedType || !this.selectedDate || !this.selectedTime) {
                    alert('Please complete all required fields');
                    return;
                }

                // Disable button during submission
                const btn = document.getElementById('proceedToPayment');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Booking...';

                // Create booking data
                const bookingData = {
                    coach_id: this.selectedCoach.id,
                    booking_date: this.selectedDate,
                    start_time: this.selectedTime,
                    session_type: this.selectedType,
                    duration: this.duration,
                    total_amount: this.selectedCoach.price,
                    special_requests: document.getElementById('specialRequests').value
                };

                try {
                    // Call API to create booking
                    const response = await fetch('/api/coach-bookings', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(bookingData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('Booking created successfully! Booking ID: ' + result.booking_id);
                        // Redirect to payment or bookings page
                        window.location.href=(window.BASE_URL||'')+'/my-bookings';
                    } else {
                        alert('Error: ' + result.error);
                        console.error('Booking error:', result);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-credit-card"></i> Proceed to Payment';
                    }
                } catch (error) {
                    console.error('Error creating booking:', error);
                    alert('Failed to create booking. Error: ' + error.message + '\nCheck browser console for details.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-credit-card"></i> Proceed to Payment';
                }
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new SessionBooking();
        });
    </script>
