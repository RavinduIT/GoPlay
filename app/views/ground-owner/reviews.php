<?php 
$title = 'Reviews Management - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-reviews.css'
];
$additionalJS = ['/public/js/pages/ground-owner-reviews.js'];
?>

<div class="ground-owner-dashboard">
    <!-- Include Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Reviews Management</h1>
            </div>
            <div class="header-right">
                <div class="header-actions">
                    <button class="btn-secondary" onclick="exportReviews()">
                        <i class="fas fa-download"></i>
                        Export Reviews
                    </button>
                    <select id="timeFilter" class="filter-select">
                        <option value="all">All Time</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
                <div class="header-notifications">
                    <button class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-count">3</span>
                    </button>
                </div>
                <div class="owner-profile">
                    <div class="profile-info">
                        <span class="profile-name">Rajesh Perera</span>
                        <span class="profile-role">Ground Owner</span>
                    </div>
                    <img src="/public/assets/images/owner-avatar.jpg" alt="Owner" class="profile-avatar">
                    <button class="profile-dropdown">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Reviews Content -->
        <div class="dashboard-content">
            <!-- Reviews Overview -->
            <div class="reviews-overview">
                <div class="overview-card overall-rating">
                    <div class="card-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="card-content">
                        <h3>Overall Rating</h3>
                        <div class="rating-display">
                            <span class="rating-number" id="overallRating">4.8</span>
                            <div class="rating-stars" id="overallStars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <span class="rating-count" id="totalReviews">Based on 0 reviews</span>
                    </div>
                </div>

                <div class="overview-card total-reviews">
                    <div class="card-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="card-content">
                        <h3>Total Reviews</h3>
                        <p class="stat-number" id="reviewsCount">0</p>
                        <div class="stat-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span id="reviewsGrowth">0%</span>
                            <small>this month</small>
                        </div>
                    </div>
                </div>

                <div class="overview-card pending-responses">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <h3>Pending Responses</h3>
                        <p class="stat-number" id="pendingResponses">0</p>
                        <span class="urgency-indicator">Needs attention</span>
                    </div>
                </div>

                <div class="overview-card recent-reviews">
                    <div class="card-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="card-content">
                        <h3>Recent Reviews</h3>
                        <p class="stat-number" id="recentCount">0</p>
                        <span class="period-indicator">Last 7 days</span>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="rating-distribution-section">
                <div class="section-header">
                    <h3>Rating Distribution</h3>
                </div>
                <div class="rating-bars">
                    <div class="rating-bar">
                        <span class="rating-label">5 <i class="fas fa-star"></i></span>
                        <div class="bar-container">
                            <div class="bar-fill" id="rating5Bar" style="width: 0%"></div>
                        </div>
                        <span class="rating-count" id="rating5Count">0</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">4 <i class="fas fa-star"></i></span>
                        <div class="bar-container">
                            <div class="bar-fill" id="rating4Bar" style="width: 0%"></div>
                        </div>
                        <span class="rating-count" id="rating4Count">0</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">3 <i class="fas fa-star"></i></span>
                        <div class="bar-container">
                            <div class="bar-fill" id="rating3Bar" style="width: 0%"></div>
                        </div>
                        <span class="rating-count" id="rating3Count">0</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">2 <i class="fas fa-star"></i></span>
                        <div class="bar-container">
                            <div class="bar-fill" id="rating2Bar" style="width: 0%"></div>
                        </div>
                        <span class="rating-count" id="rating2Count">0</span>
                    </div>
                    <div class="rating-bar">
                        <span class="rating-label">1 <i class="fas fa-star"></i></span>
                        <div class="bar-container">
                            <div class="bar-fill" id="rating1Bar" style="width: 0%"></div>
                        </div>
                        <span class="rating-count" id="rating1Count">0</span>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="reviews-filters">
                <div class="filter-group">
                    <select id="ratingFilter" class="filter-select">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <select id="groundFilter" class="filter-select">
                        <option value="">All Grounds</option>
                    </select>
                    <select id="statusFilter" class="filter-select">
                        <option value="">All Reviews</option>
                        <option value="responded">Responded</option>
                        <option value="pending">Pending Response</option>
                        <option value="flagged">Flagged</option>
                    </select>
                    <select id="sortBy" class="filter-select">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest">Highest Rating</option>
                        <option value="lowest">Lowest Rating</option>
                    </select>
                </div>
                <div class="search-group">
                    <input type="text" id="reviewSearch" placeholder="Search reviews..." class="search-input">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="reviews-list" id="reviewsList">
                <!-- Dynamic content will be loaded here -->
            </div>

            <!-- Load More Button -->
            <div class="load-more-section">
                <button class="btn-secondary" id="loadMoreBtn" onclick="loadMoreReviews()">
                    <i class="fas fa-plus"></i>
                    Load More Reviews
                </button>
            </div>
        </div>
    </main>

    <!-- Review Response Modal -->
    <div id="responseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Respond to Review</h3>
                <button class="modal-close" onclick="closeResponseModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="original-review">
                    <h4>Original Review</h4>
                    <div class="review-card">
                        <div class="reviewer-info">
                            <img id="modalReviewerAvatar" src="" alt="Reviewer" class="reviewer-avatar">
                            <div class="reviewer-details">
                                <h5 id="modalReviewerName"></h5>
                                <div class="review-rating" id="modalReviewRating"></div>
                                <span class="review-date" id="modalReviewDate"></span>
                            </div>
                        </div>
                        <p class="review-text" id="modalReviewText"></p>
                    </div>
                </div>
                <div class="response-form">
                    <h4>Your Response</h4>
                    <textarea id="responseText" placeholder="Write your response..." rows="4" maxlength="500"></textarea>
                    <div class="character-count">
                        <span id="charCount">0</span>/500 characters
                    </div>
                    <div class="response-tips">
                        <h5>Tips for responding:</h5>
                        <ul>
                            <li>Thank the customer for their feedback</li>
                            <li>Address specific concerns mentioned</li>
                            <li>Offer solutions or improvements</li>
                            <li>Keep it professional and constructive</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeResponseModal()">Cancel</button>
                <button type="button" class="btn-primary" onclick="submitResponse()">Send Response</button>
            </div>
        </div>
    </div>

    <!-- Report Review Modal -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Report Review</h3>
                <button class="modal-close" onclick="closeReportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Why are you reporting this review?</p>
                <div class="report-reasons">
                    <label class="reason-option">
                        <input type="radio" name="reportReason" value="inappropriate">
                        <span>Inappropriate content</span>
                    </label>
                    <label class="reason-option">
                        <input type="radio" name="reportReason" value="spam">
                        <span>Spam or fake review</span>
                    </label>
                    <label class="reason-option">
                        <input type="radio" name="reportReason" value="offensive">
                        <span>Offensive language</span>
                    </label>
                    <label class="reason-option">
                        <input type="radio" name="reportReason" value="misleading">
                        <span>Misleading information</span>
                    </label>
                    <label class="reason-option">
                        <input type="radio" name="reportReason" value="other">
                        <span>Other</span>
                    </label>
                </div>
                <textarea id="reportDetails" placeholder="Additional details (optional)..." rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeReportModal()">Cancel</button>
                <button type="button" class="btn-danger" onclick="submitReport()">Report Review</button>
            </div>
        </div>
    </div>
</div>