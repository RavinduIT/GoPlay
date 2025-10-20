<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Profile - GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/pages/public-coach-profile.css">
</head>
<body>
    <!-- Include Navigation -->



    <div class="coach-profile-container">
        <!-- Hero Section -->
        <section class="profile-hero">
            <div class="hero-background"></div>
            <div class="hero-content container">
                <div class="profile-header">
                    <div class="coach-avatar-section">
                        <img src="/public/assets/images/coaches/coach1.png" alt="Coach" class="coach-avatar" id="coachAvatar">
                        <div class="online-status"></div>
                    </div>

                    <div class="coach-info">
                        <div class="name-badges">
                            <h1 class="coach-name" id="coachName">Lasith Malinga</h1>
                            <div class="badges">
                                <span class="badge verified"><i class="fas fa-check-circle"></i> Verified</span>
                                <span class="badge premium"><i class="fas fa-crown"></i> Premium</span>
                            </div>
                        </div>
                        <p class="coach-title" id="coachTitle">Cricket Coach & Former International Player</p>
                        <div class="coach-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <span id="coachLocation">Colombo, Sri Lanka</span></span>
                            <span><i class="fas fa-briefcase"></i> <span id="coachExperience">15+</span> Years Experience</span>
                            <span><i class="fas fa-language"></i> <span id="coachLanguages">English, Sinhala, Tamil</span></span>
                        </div>

                        <div class="profile-actions">
                            <button class="btn btn-primary btn-large" onclick="booksession()">
                                <i class="fas fa-calendar-check"></i> Book Session
                            </button>
                            <button class="btn btn-secondary" onclick="shareProfile()">
                                <i class="fas fa-share-alt"></i> Share
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="coachRating">4.9</h3>
                            <p>Rating</p>
                            <small id="totalReviews">156 reviews</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalStudents">245</h3>
                            <p>Students</p>
                            <small>Total trained</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalSessions">1,230</h3>
                            <p>Sessions</p>
                            <small>Completed</small>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="hourlyRate">LKR 1,500</h3>
                            <p>Starting at</p>
                            <small>Per hour</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="container main-content">
            <div class="content-grid">
                <!-- Left Column -->
                <div class="left-column">
                    <!-- About Section -->
                    <section class="content-card">
                        <h2><i class="fas fa-user-circle"></i> About Me</h2>
                        <div class="about-content" id="coachBio">
                            <p>
                                Former international cricket player with over 15 years of professional experience at the highest level.
                                I have represented Sri Lanka in more than 340 international matches, taking over 500 wickets across all formats.
                                My coaching journey began in 2019, and since then, I've helped numerous aspiring cricketers develop their skills
                                and achieve their dreams.
                            </p>
                            <p>
                                I specialize in fast bowling techniques, particularly yorkers and slower deliveries. My coaching philosophy
                                centers on building strong fundamentals while nurturing each player's unique talents. I believe in combining
                                technical excellence with mental strength to create well-rounded athletes.
                            </p>
                        </div>

                        <div class="highlights">
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
                    </section>

                    <!-- Expertise Section -->
                    <section class="content-card">
                        <h2><i class="fas fa-certificate"></i> Expertise & Specializations</h2>
                        <div class="expertise-tags" id="coachSpecializations">
                            <span class="tag">Fast Bowling</span>
                            <span class="tag">Yorker Specialist</span>
                            <span class="tag">Match Strategy</span>
                            <span class="tag">Youth Development</span>
                            <span class="tag">Mental Conditioning</span>
                            <span class="tag">Fitness Training</span>
                        </div>
                    </section>

                    <!-- Certifications Section -->
                    <section class="content-card">
                        <h2><i class="fas fa-award"></i> Certifications & Achievements</h2>
                        <div class="achievements-list" id="coachCertifications">
                            <div class="achievement">
                                <div class="achievement-icon certification">
                                    <i class="fas fa-certificate"></i>
                                </div>
                                <div class="achievement-info">
                                    <h4>Level 3 Cricket Coaching Certificate</h4>
                                    <p>Sri Lanka Cricket Board • 2019</p>
                                </div>
                            </div>
                            <div class="achievement">
                                <div class="achievement-icon award">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="achievement-info">
                                    <h4>Best Youth Coach Award</h4>
                                    <p>Provincial Cricket Association • 2021</p>
                                </div>
                            </div>
                            <div class="achievement">
                                <div class="achievement-icon medal">
                                    <i class="fas fa-medal"></i>
                                </div>
                                <div class="achievement-info">
                                    <h4>International Playing Career</h4>
                                    <p>Sri Lankan National Team • 2004-2019</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Reviews Section -->
                    <section class="content-card">
                        <h2><i class="fas fa-star"></i> Student Reviews</h2>

                        <div class="rating-summary">
                            <div class="overall-rating">
                                <div class="rating-number">4.9</div>
                                <div class="stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p>Based on 156 reviews</p>
                            </div>

                            <div class="rating-bars">
                                <div class="rating-bar">
                                    <span>5 <i class="fas fa-star"></i></span>
                                    <div class="bar"><div class="fill" style="width: 85%"></div></div>
                                    <span>132</span>
                                </div>
                                <div class="rating-bar">
                                    <span>4 <i class="fas fa-star"></i></span>
                                    <div class="bar"><div class="fill" style="width: 12%"></div></div>
                                    <span>19</span>
                                </div>
                                <div class="rating-bar">
                                    <span>3 <i class="fas fa-star"></i></span>
                                    <div class="bar"><div class="fill" style="width: 2%"></div></div>
                                    <span>3</span>
                                </div>
                                <div class="rating-bar">
                                    <span>2 <i class="fas fa-star"></i></span>
                                    <div class="bar"><div class="fill" style="width: 1%"></div></div>
                                    <span>1</span>
                                </div>
                                <div class="rating-bar">
                                    <span>1 <i class="fas fa-star"></i></span>
                                    <div class="bar"><div class="fill" style="width: 1%"></div></div>
                                    <span>1</span>
                                </div>
                            </div>
                        </div>

                        <div class="reviews-list" id="coachReviews">
                            <div class="review">
                                <div class="review-header">
                                    <img src="/public/assets/images/default-avatar.png" alt="Student">
                                    <div class="review-info">
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
                                <p class="review-text">
                                    "Coach Malinga's expertise and dedication are unmatched! His training methods are
                                    incredibly effective, and I've seen tremendous improvement in my bowling technique.
                                    Highly recommended for anyone serious about cricket!"
                                </p>
                            </div>

                            <div class="review">
                                <div class="review-header">
                                    <img src="/public/assets/images/default-avatar.png" alt="Student">
                                    <div class="review-info">
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
                                <p class="review-text">
                                    "Best coaching experience I've ever had! The personalized training plan and
                                    constant feedback helped me get selected for the provincial team. Thank you, coach!"
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right Column (Sidebar) -->
                <div class="right-column">
                    <!-- Pricing Card -->
                    <div class="sidebar-card sticky">
                        <h3><i class="fas fa-tag"></i> Pricing Packages</h3>

                        <div class="pricing-option featured">
                            <div class="price-header">
                                <h4>Individual Session</h4>
                                <span class="popular-badge">Most Popular</span>
                            </div>
                            <div class="price">
                                <span class="amount">LKR 1,500</span>
                                <span class="period">/hour</span>
                            </div>
                            <ul class="features">
                                <li><i class="fas fa-check"></i> One-on-one coaching</li>
                                <li><i class="fas fa-check"></i> Personalized training plan</li>
                                <li><i class="fas fa-check"></i> Video analysis</li>
                                <li><i class="fas fa-check"></i> Progress tracking</li>
                            </ul>
                            <button class="btn btn-primary btn-block" onclick="bookCoach()">
                                Book Now
                            </button>
                        </div>

                        <div class="pricing-option">
                            <div class="price-header">
                                <h4>Group Session</h4>
                            </div>
                            <div class="price">
                                <span class="amount">LKR 800</span>
                                <span class="period">/hour</span>
                            </div>
                            <ul class="features">
                                <li><i class="fas fa-check"></i> 4-6 students per group</li>
                                <li><i class="fas fa-check"></i> Team drills & exercises</li>
                                <li><i class="fas fa-check"></i> Match simulations</li>
                            </ul>
                            <button class="btn btn-outline btn-block" onclick="bookCoach()">
                                Book Now
                            </button>
                        </div>

                        <div class="pricing-option highlight">
                            <div class="price-header">
                                <h4>Monthly Package</h4>
                                <span class="save-badge">Save 20%</span>
                            </div>
                            <div class="price">
                                <span class="amount">LKR 20,000</span>
                                <span class="period">/month</span>
                            </div>
                            <ul class="features">
                                <li><i class="fas fa-check"></i> 16 hours of training</li>
                                <li><i class="fas fa-check"></i> Individual + group sessions</li>
                                <li><i class="fas fa-check"></i> Fitness program included</li>
                                <li><i class="fas fa-check"></i> Diet consultation</li>
                            </ul>
                            <button class="btn btn-primary btn-block" onclick="bookCoach()">
                                Book Now
                            </button>
                        </div>
                    </div>

                    <!-- Availability Card -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-calendar-alt"></i> Availability</h3>
                        <div class="availability-schedule" id="coachAvailability">
                            <div class="day available">
                                <span class="day-name">Monday</span>
                                <span class="time">6:00 AM - 8:00 PM</span>
                            </div>
                            <div class="day available">
                                <span class="day-name">Tuesday</span>
                                <span class="time">6:00 AM - 8:00 PM</span>
                            </div>
                            <div class="day available">
                                <span class="day-name">Wednesday</span>
                                <span class="time">6:00 AM - 8:00 PM</span>
                            </div>
                            <div class="day available">
                                <span class="day-name">Thursday</span>
                                <span class="time">6:00 AM - 8:00 PM</span>
                            </div>
                            <div class="day available">
                                <span class="day-name">Friday</span>
                                <span class="time">6:00 AM - 8:00 PM</span>
                            </div>
                            <div class="day limited">
                                <span class="day-name">Saturday</span>
                                <span class="time">6:00 AM - 12:00 PM</span>
                            </div>
                            <div class="day unavailable">
                                <span class="day-name">Sunday</span>
                                <span class="time">Unavailable</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Card -->
                    <div class="sidebar-card">
                        <h3><i class="fas fa-envelope"></i> Contact</h3>
                        <div class="contact-info">
                            <a href="mailto:lasith.malinga@goplay.lk" class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span id="coachEmail">lasith.malinga@goplay.lk</span>
                            </a>
                            <a href="tel:+94711234567" class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span id="coachPhone">+94 71 123 4567</span>
                            </a>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span id="trainingLocation">Colombo Cricket Grounds</span>
                            </div>
                        </div>

                        <div class="social-links">
                            <a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon youtube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->


    <script src="/public/js/pages/public-coach-profile.js"></script>
</body>
</html>
