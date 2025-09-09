<?php $currentPage = 'profile'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Profile - GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="/public/css/pages/coach-profile.css">
</head>
<body>
    <div class="coach-dashboard">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <h1>My Profile</h1>
                    <p class="header-subtitle">Manage your coaching profile and credentials</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" id="editProfileBtn">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </button>
                </div>
            </div>

            <div class="profile-content">
                <!-- Profile Overview -->
                <div class="profile-card overview-card">
                    <div class="profile-header">
                        <div class="profile-avatar-section">
                            <div class="avatar-wrapper">
                                <img src="/public/assets/images/coach-avatar.jpg" alt="Coach Avatar" class="profile-avatar" id="profileAvatar">
                                <button class="avatar-edit-btn" onclick="coachProfile.changeAvatar()">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                            </div>
                        </div>
                        
                        <div class="profile-info">
                            <h2 class="coach-name">Lasith Malinga</h2>
                            <p class="coach-specialization">Cricket Coach & Former International Player</p>
                            <div class="profile-stats">
                                <div class="stat">
                                    <span class="stat-number">4.9</span>
                                    <span class="stat-label">Rating</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-number">45</span>
                                    <span class="stat-label">Students</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-number">5</span>
                                    <span class="stat-label">Years</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-number">230</span>
                                    <span class="stat-label">Sessions</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-badges">
                        <span class="badge verified">
                            <i class="fas fa-check-circle"></i>
                            Verified Coach
                        </span>
                        <span class="badge premium">
                            <i class="fas fa-star"></i>
                            Premium Coach
                        </span>
                        <span class="badge expert">
                            <i class="fas fa-award"></i>
                            Expert Level
                        </span>
                    </div>
                </div>

                <div class="profile-grid">
                    <!-- Personal Information -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Personal Information</h3>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('personal')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>Full Name</label>
                                    <span>Lasith Malinga</span>
                                </div>
                                <div class="info-item">
                                    <label>Email</label>
                                    <span>lasith.malinga@email.com</span>
                                </div>
                                <div class="info-item">
                                    <label>Phone</label>
                                    <span>+94 71 123 4567</span>
                                </div>
                                <div class="info-item">
                                    <label>Location</label>
                                    <span>Colombo, Sri Lanka</span>
                                </div>
                                <div class="info-item">
                                    <label>Date of Birth</label>
                                    <span>August 28, 1983</span>
                                </div>
                                <div class="info-item">
                                    <label>Gender</label>
                                    <span>Male</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Professional Information -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Professional Information</h3>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('professional')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="info-grid">
                                <div class="info-item full-width">
                                    <label>Specializations</label>
                                    <div class="specialization-tags">
                                        <span class="tag">Fast Bowling</span>
                                        <span class="tag">Cricket Fundamentals</span>
                                        <span class="tag">Match Strategy</span>
                                        <span class="tag">Youth Development</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <label>Experience</label>
                                    <span>5+ Years</span>
                                </div>
                                <div class="info-item">
                                    <label>Coaching License</label>
                                    <span>Level 3 Certified</span>
                                </div>
                                <div class="info-item">
                                    <label>Languages</label>
                                    <span>English, Sinhala, Tamil</span>
                                </div>
                                <div class="info-item">
                                    <label>Hourly Rate</label>
                                    <span>₹800 - ₹1,500</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bio & Description -->
                    <div class="profile-card full-width">
                        <div class="card-header">
                            <h3>About Me</h3>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('bio')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <p class="bio-text">
                                Former international cricket player with over 15 years of professional experience. 
                                Specialized in fast bowling techniques and youth development. I have coached players 
                                at various levels, from beginners to professional cricketers. My coaching philosophy 
                                focuses on building strong fundamentals while developing each player's unique strengths.
                            </p>
                        </div>
                    </div>

                    <!-- Certifications & Achievements -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Certifications & Achievements</h3>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('certifications')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="achievements-list">
                                <div class="achievement-item">
                                    <div class="achievement-icon">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div class="achievement-details">
                                        <h4>Level 3 Cricket Coaching Certificate</h4>
                                        <p>Sri Lanka Cricket Board - 2019</p>
                                    </div>
                                </div>
                                <div class="achievement-item">
                                    <div class="achievement-icon">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <div class="achievement-details">
                                        <h4>Best Youth Coach Award</h4>
                                        <p>Provincial Cricket Association - 2021</p>
                                    </div>
                                </div>
                                <div class="achievement-item">
                                    <div class="achievement-icon">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                    <div class="achievement-details">
                                        <h4>International Playing Career</h4>
                                        <p>Sri Lankan National Team - 2004-2019</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Availability Schedule -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h3>Weekly Availability</h3>
                            <a href="/coach/availability" class="edit-section-btn">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <div class="card-content">
                            <div class="schedule-grid">
                                <div class="schedule-day">
                                    <span class="day-name">Monday</span>
                                    <span class="time-slot">9:00 AM - 6:00 PM</span>
                                </div>
                                <div class="schedule-day">
                                    <span class="day-name">Tuesday</span>
                                    <span class="time-slot">9:00 AM - 6:00 PM</span>
                                </div>
                                <div class="schedule-day">
                                    <span class="day-name">Wednesday</span>
                                    <span class="time-slot">9:00 AM - 6:00 PM</span>
                                </div>
                                <div class="schedule-day">
                                    <span class="day-name">Thursday</span>
                                    <span class="time-slot">9:00 AM - 6:00 PM</span>
                                </div>
                                <div class="schedule-day">
                                    <span class="day-name">Friday</span>
                                    <span class="time-slot">9:00 AM - 6:00 PM</span>
                                </div>
                                <div class="schedule-day unavailable">
                                    <span class="day-name">Saturday</span>
                                    <span class="time-slot">Unavailable</span>
                                </div>
                                <div class="schedule-day unavailable">
                                    <span class="day-name">Sunday</span>
                                    <span class="time-slot">Unavailable</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Edit Profile</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="form-section">
                        <h4>Personal Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="fullName" value="Lasith Malinga" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="lasith.malinga@email.com" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" name="phone" value="+94 71 123 4567" required>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" name="location" value="Colombo, Sri Lanka" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Professional Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Specialization</label>
                                <input type="text" name="specialization" value="Cricket Coach & Former International Player">
                            </div>
                            <div class="form-group">
                                <label>Experience (Years)</label>
                                <input type="number" name="experience" value="5" min="0">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Hourly Rate Range</label>
                                <input type="text" name="hourlyRate" value="₹800 - ₹1,500">
                            </div>
                            <div class="form-group">
                                <label>Languages</label>
                                <input type="text" name="languages" value="English, Sinhala, Tamil">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>About Me</label>
                            <textarea name="bio" rows="4">Former international cricket player with over 15 years of professional experience. Specialized in fast bowling techniques and youth development.</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="coachProfile.closeModal('editProfileModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="coachProfile.saveProfile()">Save Changes</button>
            </div>
        </div>
    </div>

    <script src="/public/js/pages/coach-profile.js"></script>
</body>
</html>