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
            <!-- Page Header -->
            <div class="page-header">
                <div class="header-content">
                    <h1><i class="fas fa-user-circle"></i> Professional Profile</h1>
                    <p class="header-subtitle">Manage your coaching credentials and showcase your expertise</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" onclick="window.print()">
                        <i class="fas fa-download"></i>
                        Export Profile
                    </button>
                    <button class="btn btn-primary" id="editProfileBtn">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- Profile Completion Progress -->
            <div class="profile-completion-card">
                <div class="completion-header">
                    <div>
                        <h3>Profile Completeness</h3>
                        <p>Complete your profile to attract more students</p>
                    </div>
                    <div class="completion-percentage">
                        <div class="circular-progress" data-percentage="85">
                            <svg width="80" height="80">
                                <circle cx="40" cy="40" r="35" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                                <circle cx="40" cy="40" r="35" stroke="#2563eb" stroke-width="6" fill="none"
                                        stroke-dasharray="220" stroke-dashoffset="33" stroke-linecap="round"
                                        transform="rotate(-90 40 40)"/>
                            </svg>
                            <span class="percentage-text">85%</span>
                        </div>
                    </div>
                </div>
                <div class="completion-tasks">
                    <div class="task-item completed">
                        <i class="fas fa-check-circle"></i>
                        <span>Basic information added</span>
                    </div>
                    <div class="task-item completed">
                        <i class="fas fa-check-circle"></i>
                        <span>Professional credentials uploaded</span>
                    </div>
                    <div class="task-item pending">
                        <i class="far fa-circle"></i>
                        <span>Add video introduction</span>
                    </div>
                </div>
            </div>

            <div class="profile-content">
                <!-- Hero Profile Card -->
                <div class="profile-hero-card">
                    <div class="hero-background"></div>
                    <div class="hero-content">
                        <div class="profile-avatar-section">
                            <div class="avatar-wrapper">
                                <img src="/public/assets/images/coaches/coach1.png" alt="Coach Avatar" class="profile-avatar" id="profileAvatar">
                                <div class="avatar-status online"></div>
                                <button class="avatar-edit-btn" onclick="coachProfile.changeAvatar()">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                            </div>
                        </div>

                        <div class="profile-header-info">
                            <div class="name-section">
                                <h2 class="coach-name">Lasith Malinga</h2>
                                <div class="verification-badges">
                                    <span class="badge verified">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                    <span class="badge premium">
                                        <i class="fas fa-crown"></i> Premium
                                    </span>
                                </div>
                            </div>
                            <p class="coach-title">Cricket Coach & Former International Player</p>
                            <div class="coach-meta">
                                <span><i class="fas fa-map-marker-alt"></i> Colombo, Sri Lanka</span>
                                <span><i class="fas fa-globe"></i> English, Sinhala, Tamil</span>
                                <span><i class="fas fa-calendar-alt"></i> Joined March 2020</span>
                            </div>
                        </div>

                        <div class="profile-stats-cards">
                            <div class="stat-card-hero">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-details">
                                    <h3>4.9</h3>
                                    <p>Rating</p>
                                    <small>Based on 156 reviews</small>
                                </div>
                            </div>
                            <div class="stat-card-hero">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-details">
                                    <h3>245</h3>
                                    <p>Students</p>
                                    <small>Total trained</small>
                                </div>
                            </div>
                            <div class="stat-card-hero">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-details">
                                    <h3>1,230</h3>
                                    <p>Hours</p>
                                    <small>Coaching time</small>
                                </div>
                            </div>
                            <div class="stat-card-hero">
                                <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="stat-details">
                                    <h3>15+</h3>
                                    <p>Years</p>
                                    <small>Experience</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-grid">
                    <!-- About Me Section -->
                    <div class="profile-card featured full-width">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-user-circle"></i>
                                <h3>About Me</h3>
                            </div>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('bio')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <p class="bio-text">
                                Former international cricket player with over 15 years of professional experience at the highest level.
                                I have represented Sri Lanka in more than 340 international matches, taking over 500 wickets across all formats.
                                My coaching journey began in 2019, and since then, I've helped numerous aspiring cricketers develop their skills
                                and achieve their dreams.
                            </p>
                            <p class="bio-text">
                                I specialize in fast bowling techniques, particularly yorkers and slower deliveries. My coaching philosophy
                                centers on building strong fundamentals while nurturing each player's unique talents. I believe in combining
                                technical excellence with mental strength to create well-rounded athletes.
                            </p>
                            <div class="bio-highlights">
                                <div class="highlight-item">
                                    <i class="fas fa-bullseye"></i>
                                    <div>
                                        <strong>Specialized Training</strong>
                                        <p>Fast bowling, yorkers, death overs, and match strategy</p>
                                    </div>
                                </div>
                                <div class="highlight-item">
                                    <i class="fas fa-graduation-cap"></i>
                                    <div>
                                        <strong>Teaching Approach</strong>
                                        <p>Personalized coaching plans based on individual strengths and goals</p>
                                    </div>
                                </div>
                                <div class="highlight-item">
                                    <i class="fas fa-chart-line"></i>
                                    <div>
                                        <strong>Success Rate</strong>
                                        <p>92% of students show significant improvement within 3 months</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expertise & Specializations -->
                    <div class="profile-card">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-certificate"></i>
                                <h3>Expertise & Specializations</h3>
                            </div>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('expertise')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="expertise-grid">
                                <div class="expertise-item">
                                    <div class="expertise-icon">
                                        <i class="fas fa-baseball-ball"></i>
                                    </div>
                                    <h4>Fast Bowling</h4>
                                    <div class="skill-level">
                                        <div class="skill-bar" style="width: 98%"></div>
                                    </div>
                                    <span class="level-badge expert">Expert</span>
                                </div>
                                <div class="expertise-item">
                                    <div class="expertise-icon">
                                        <i class="fas fa-crosshairs"></i>
                                    </div>
                                    <h4>Yorker Specialist</h4>
                                    <div class="skill-level">
                                        <div class="skill-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="level-badge expert">Expert</span>
                                </div>
                                <div class="expertise-item">
                                    <div class="expertise-icon">
                                        <i class="fas fa-chess"></i>
                                    </div>
                                    <h4>Match Strategy</h4>
                                    <div class="skill-level">
                                        <div class="skill-bar" style="width: 95%"></div>
                                    </div>
                                    <span class="level-badge expert">Expert</span>
                                </div>
                                <div class="expertise-item">
                                    <div class="expertise-icon">
                                        <i class="fas fa-child"></i>
                                    </div>
                                    <h4>Youth Development</h4>
                                    <div class="skill-level">
                                        <div class="skill-bar" style="width: 90%"></div>
                                    </div>
                                    <span class="level-badge advanced">Advanced</span>
                                </div>
                                <div class="expertise-item">
                                    <div class="expertise-icon">
                                        <i class="fas fa-brain"></i>
                                    </div>
                                    <h4>Mental Conditioning</h4>
                                    <div class="skill-level">
                                        <div class="skill-bar" style="width: 88%"></div>
                                    </div>
                                    <span class="level-badge advanced">Advanced</span>
                                </div>
                                <div class="expertise-item">
                                    <div class="expertise-icon">
                                        <i class="fas fa-running"></i>
                                    </div>
                                    <h4>Fitness Training</h4>
                                    <div class="skill-level">
                                        <div class="skill-bar" style="width: 85%"></div>
                                    </div>
                                    <span class="level-badge advanced">Advanced</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Packages -->
                    <div class="profile-card">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-tag"></i>
                                <h3>Pricing & Packages</h3>
                            </div>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('pricing')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="pricing-packages">
                                <div class="package-card">
                                    <div class="package-header">
                                        <h4>Individual Session</h4>
                                        <span class="package-badge popular">Most Popular</span>
                                    </div>
                                    <div class="package-price">
                                        <span class="currency">LKR</span>
                                        <span class="amount">1,500</span>
                                        <span class="period">/hour</span>
                                    </div>
                                    <ul class="package-features">
                                        <li><i class="fas fa-check"></i> One-on-one coaching</li>
                                        <li><i class="fas fa-check"></i> Personalized training plan</li>
                                        <li><i class="fas fa-check"></i> Video analysis</li>
                                        <li><i class="fas fa-check"></i> Progress tracking</li>
                                    </ul>
                                </div>
                                <div class="package-card">
                                    <div class="package-header">
                                        <h4>Group Session</h4>
                                    </div>
                                    <div class="package-price">
                                        <span class="currency">LKR</span>
                                        <span class="amount">800</span>
                                        <span class="period">/hour</span>
                                    </div>
                                    <ul class="package-features">
                                        <li><i class="fas fa-check"></i> 4-6 students per group</li>
                                        <li><i class="fas fa-check"></i> Team drills & exercises</li>
                                        <li><i class="fas fa-check"></i> Match simulations</li>
                                        <li><i class="fas fa-check"></i> Group discounts available</li>
                                    </ul>
                                </div>
                                <div class="package-card featured-package">
                                    <div class="package-header">
                                        <h4>Monthly Package</h4>
                                        <span class="package-badge save">Save 20%</span>
                                    </div>
                                    <div class="package-price">
                                        <span class="currency">LKR</span>
                                        <span class="amount">20,000</span>
                                        <span class="period">/month</span>
                                    </div>
                                    <ul class="package-features">
                                        <li><i class="fas fa-check"></i> 16 hours of training</li>
                                        <li><i class="fas fa-check"></i> Individual + group sessions</li>
                                        <li><i class="fas fa-check"></i> Fitness program included</li>
                                        <li><i class="fas fa-check"></i> Diet consultation</li>
                                        <li><i class="fas fa-check"></i> Priority booking</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Certifications & Achievements -->
                    <div class="profile-card full-width">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-award"></i>
                                <h3>Certifications & Achievements</h3>
                            </div>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('certifications')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="achievements-grid">
                                <div class="achievement-card">
                                    <div class="achievement-icon certification">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div class="achievement-details">
                                        <h4>Level 3 Cricket Coaching Certificate</h4>
                                        <p class="issuer">Sri Lanka Cricket Board</p>
                                        <p class="date"><i class="fas fa-calendar"></i> Issued: January 2019</p>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>
                                    </div>
                                </div>
                                <div class="achievement-card">
                                    <div class="achievement-icon award">
                                        <i class="fas fa-trophy"></i>
                                    </div>
                                    <div class="achievement-details">
                                        <h4>Best Youth Coach Award</h4>
                                        <p class="issuer">Provincial Cricket Association</p>
                                        <p class="date"><i class="fas fa-calendar"></i> Received: March 2021</p>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>
                                    </div>
                                </div>
                                <div class="achievement-card">
                                    <div class="achievement-icon medal">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                    <div class="achievement-details">
                                        <h4>International Playing Career</h4>
                                        <p class="issuer">Sri Lankan National Cricket Team</p>
                                        <p class="date"><i class="fas fa-calendar"></i> 2004 - 2019</p>
                                        <div class="career-stats">
                                            <span>340+ Matches</span>
                                            <span>500+ Wickets</span>
                                            <span>T20 World Cup Winner</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="achievement-card">
                                    <div class="achievement-icon education">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="achievement-details">
                                        <h4>Sports Psychology Certification</h4>
                                        <p class="issuer">International Sports Academy</p>
                                        <p class="date"><i class="fas fa-calendar"></i> Issued: June 2020</p>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Availability -->
                    <div class="profile-card">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-calendar-check"></i>
                                <h3>Availability Schedule</h3>
                            </div>
                            <a href="/coach/availability" class="edit-section-btn">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <div class="card-content">
                            <div class="schedule-list">
                                <div class="schedule-day available">
                                    <div class="day-header">
                                        <span class="day-name">Monday</span>
                                        <span class="availability-status available">Available</span>
                                    </div>
                                    <span class="time-slot"><i class="fas fa-clock"></i> 6:00 AM - 8:00 PM</span>
                                </div>
                                <div class="schedule-day available">
                                    <div class="day-header">
                                        <span class="day-name">Tuesday</span>
                                        <span class="availability-status available">Available</span>
                                    </div>
                                    <span class="time-slot"><i class="fas fa-clock"></i> 6:00 AM - 8:00 PM</span>
                                </div>
                                <div class="schedule-day available">
                                    <div class="day-header">
                                        <span class="day-name">Wednesday</span>
                                        <span class="availability-status available">Available</span>
                                    </div>
                                    <span class="time-slot"><i class="fas fa-clock"></i> 6:00 AM - 8:00 PM</span>
                                </div>
                                <div class="schedule-day available">
                                    <div class="day-header">
                                        <span class="day-name">Thursday</span>
                                        <span class="availability-status available">Available</span>
                                    </div>
                                    <span class="time-slot"><i class="fas fa-clock"></i> 6:00 AM - 8:00 PM</span>
                                </div>
                                <div class="schedule-day available">
                                    <div class="day-header">
                                        <span class="day-name">Friday</span>
                                        <span class="availability-status available">Available</span>
                                    </div>
                                    <span class="time-slot"><i class="fas fa-clock"></i> 6:00 AM - 8:00 PM</span>
                                </div>
                                <div class="schedule-day limited">
                                    <div class="day-header">
                                        <span class="day-name">Saturday</span>
                                        <span class="availability-status limited">Limited</span>
                                    </div>
                                    <span class="time-slot"><i class="fas fa-clock"></i> 6:00 AM - 12:00 PM</span>
                                </div>
                                <div class="schedule-day unavailable">
                                    <div class="day-header">
                                        <span class="day-name">Sunday</span>
                                        <span class="availability-status unavailable">Unavailable</span>
                                    </div>
                                    <span class="time-slot"><i class="fas fa-ban"></i> Rest Day</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="profile-card">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-address-card"></i>
                                <h3>Contact Information</h3>
                            </div>
                            <button class="edit-section-btn" onclick="coachProfile.editSection('contact')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div class="contact-info">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="contact-details">
                                        <label>Email Address</label>
                                        <span>lasith.malinga@goplay.lk</span>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="contact-details">
                                        <label>Phone Number</label>
                                        <span>+94 71 123 4567</span>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="contact-details">
                                        <label>Training Location</label>
                                        <span>Colombo Cricket Grounds, Sri Lanka</span>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div class="contact-details">
                                        <label>WhatsApp</label>
                                        <span>+94 71 123 4567</span>
                                    </div>
                                </div>
                            </div>
                            <div class="social-links">
                                <h4>Social Media</h4>
                                <div class="social-buttons">
                                    <a href="#" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                                    <a href="#" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                                    <a href="#" class="social-btn youtube"><i class="fab fa-youtube"></i></a>
                                    <a href="#" class="social-btn linkedin"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Reviews & Testimonials -->
                    <div class="profile-card full-width">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-star"></i>
                                <h3>Student Reviews & Testimonials</h3>
                            </div>
                            <a href="/coach/reviews" class="view-all-link">
                                View All <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="card-content">
                            <div class="reviews-summary">
                                <div class="rating-overview">
                                    <div class="overall-rating">
                                        <h2>4.9</h2>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <p>156 reviews</p>
                                    </div>
                                    <div class="rating-breakdown">
                                        <div class="rating-bar-item">
                                            <span>5 <i class="fas fa-star"></i></span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 85%"></div>
                                            </div>
                                            <span>132</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>4 <i class="fas fa-star"></i></span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 12%"></div>
                                            </div>
                                            <span>19</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>3 <i class="fas fa-star"></i></span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 2%"></div>
                                            </div>
                                            <span>3</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>2 <i class="fas fa-star"></i></span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 1%"></div>
                                            </div>
                                            <span>1</span>
                                        </div>
                                        <div class="rating-bar-item">
                                            <span>1 <i class="fas fa-star"></i></span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 0%"></div>
                                            </div>
                                            <span>1</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="testimonials-list">
                                <div class="testimonial-card">
                                    <div class="testimonial-header">
                                        <img src="/public/assets/images/default-avatar.png" alt="Student">
                                        <div class="student-info">
                                            <h4>Kavinda Ranasighe</h4>
                                            <div class="review-stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <span class="review-date">2 weeks ago</span>
                                        </div>
                                    </div>
                                    <p class="testimonial-text">
                                        "Coach Malinga's expertise and dedication are unmatched! His training methods are
                                        incredibly effective, and I've seen tremendous improvement in my bowling technique.
                                        Highly recommended for anyone serious about cricket!"
                                    </p>
                                </div>
                                <div class="testimonial-card">
                                    <div class="testimonial-header">
                                        <img src="/public/assets/images/default-avatar.png" alt="Student">
                                        <div class="student-info">
                                            <h4>Sanduni Rajapakse</h4>
                                            <div class="review-stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <span class="review-date">1 month ago</span>
                                        </div>
                                    </div>
                                    <p class="testimonial-text">
                                        "Best coaching experience I've ever had! The personalized training plan and
                                        constant feedback helped me get selected for the provincial team. Thank you, coach!"
                                    </p>
                                </div>
                                <div class="testimonial-card">
                                    <div class="testimonial-header">
                                        <img src="/public/assets/images/default-avatar.png" alt="Student">
                                        <div class="student-info">
                                            <h4>Dilan Wijesinghe</h4>
                                            <div class="review-stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <span class="review-date">2 months ago</span>
                                        </div>
                                    </div>
                                    <p class="testimonial-text">
                                        "Learning from a legend! Coach's insights on match strategy and mental preparation
                                        have been game-changing for me. Professional and inspiring!"
                                    </p>
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
                <button class="modal-close" onclick="coachProfile.closeModal('editProfileModal')">&times;</button>
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
                                <input type="email" name="email" value="lasith.malinga@goplay.lk" required>
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
                                <input type="number" name="experience" value="15" min="0">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Hourly Rate (LKR)</label>
                                <input type="number" name="hourlyRate" value="1500">
                            </div>
                            <div class="form-group">
                                <label>Languages</label>
                                <input type="text" name="languages" value="English, Sinhala, Tamil">
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>About Me</label>
                            <textarea name="bio" rows="4">Former international cricket player with over 15 years of professional experience. Specialized in fast bowling techniques and youth development.</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="coachProfile.closeModal('editProfileModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="coachProfile.saveProfile()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <script src="/public/js/pages/coach-profile.js"></script>
</body>
</html>
