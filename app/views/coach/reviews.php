<?php 
$title = 'Client Reviews - Coach Dashboard';
$additionalCSS = ['/public/css/coach/reviews.css'];
$additionalJS = ['/public/js/pages/coach-reviews.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Professional Reviews Styles */
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
        --star-color: #fbbf24;
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

    /* Review Stats */
    .review-stats {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .overall-rating {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        padding: 2rem;
        text-align: center;
    }

    .rating-score {
        font-size: 4rem;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .rating-stars {
        display: flex;
        justify-content: center;
        gap: 0.25rem;
        margin-bottom: 1rem;
    }

    .star {
        font-size: 1.5rem;
        color: var(--star-color);
    }

    .star.empty {
        color: var(--border-color);
    }

    .rating-summary {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }

    .rating-trend {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--success-color);
        font-weight: 600;
    }

    .rating-breakdown {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        padding: 2rem;
    }

    .breakdown-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
    }

    .rating-bar {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .bar-label {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        min-width: 60px;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .bar-container {
        flex: 1;
        height: 8px;
        background: var(--background-gray);
        border-radius: 4px;
        overflow: hidden;
    }

    .bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--star-color), var(--accent-color));
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .bar-count {
        min-width: 40px;
        text-align: right;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    /* Filter and Search */
    .reviews-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        gap: 1rem;
    }

    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        background: var(--background-white);
        padding: 0.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
    }

    .filter-tab {
        padding: 0.75rem 1.5rem;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        font-weight: 500;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
    }

    .filter-tab.active {
        background: var(--primary-color);
        color: white;
    }

    .filter-tab:not(.active):hover {
        background: var(--background-gray);
        color: var(--text-primary);
    }

    .search-box {
        position: relative;
        max-width: 300px;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        background: var(--background-white);
        font-size: 0.9rem;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
    }

    /* Review Cards */
    .reviews-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .review-card {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        transition: var(--transition);
    }

    .review-card:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .reviewer-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .reviewer-details {
        flex: 1;
    }

    .reviewer-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .review-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .review-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .review-stars {
        display: flex;
        gap: 2px;
    }

    .review-stars .star {
        font-size: 1rem;
    }

    .review-date {
        color: var(--text-light);
    }

    .review-content {
        margin-bottom: 1rem;
    }

    .review-text {
        color: var(--text-primary);
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .review-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .review-tag {
        padding: 0.25rem 0.75rem;
        background: var(--background-light);
        color: var(--text-secondary);
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .review-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }

    .review-helpful {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline:hover {
        background: var(--primary-color);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .empty-description {
        margin-bottom: 2rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
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

        .review-stats {
            grid-template-columns: 1fr;
        }

        .reviews-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-tabs {
            order: 2;
        }

        .search-box {
            order: 1;
            max-width: none;
        }

        .review-header {
            flex-direction: column;
            gap: 1rem;
        }

        .review-actions {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }
    }
</style>

<div class="dashboard-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Client Reviews</h1>
                <p class="page-subtitle">View and manage feedback from your clients</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="exportReviews()">
                    <i class="fas fa-download"></i>
                    Export Reviews
                </button>
                <button class="btn btn-primary" onclick="requestReview()">
                    <i class="fas fa-paper-plane"></i>
                    Request Review
                </button>
            </div>
        </div>

        <!-- Review Stats -->
        <div class="review-stats">
            <div class="overall-rating">
                <div class="rating-score">4.8</div>
                <div class="rating-stars">
                    <i class="fas fa-star star"></i>
                    <i class="fas fa-star star"></i>
                    <i class="fas fa-star star"></i>
                    <i class="fas fa-star star"></i>
                    <i class="fas fa-star star"></i>
                </div>
                <div class="rating-summary">Based on 127 reviews</div>
                <div class="rating-trend">
                    <i class="fas fa-trending-up"></i>
                    <span>+0.3 this month</span>
                </div>
            </div>

            <div class="rating-breakdown">
                <h3 class="breakdown-title">Rating Breakdown</h3>
                <div class="rating-bar">
                    <div class="bar-label">
                        <span>5</span>
                        <i class="fas fa-star star"></i>
                    </div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: 78%"></div>
                    </div>
                    <div class="bar-count">99</div>
                </div>
                <div class="rating-bar">
                    <div class="bar-label">
                        <span>4</span>
                        <i class="fas fa-star star"></i>
                    </div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: 15%"></div>
                    </div>
                    <div class="bar-count">19</div>
                </div>
                <div class="rating-bar">
                    <div class="bar-label">
                        <span>3</span>
                        <i class="fas fa-star star"></i>
                    </div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: 5%"></div>
                    </div>
                    <div class="bar-count">6</div>
                </div>
                <div class="rating-bar">
                    <div class="bar-label">
                        <span>2</span>
                        <i class="fas fa-star star"></i>
                    </div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: 2%"></div>
                    </div>
                    <div class="bar-count">2</div>
                </div>
                <div class="rating-bar">
                    <div class="bar-label">
                        <span>1</span>
                        <i class="fas fa-star star"></i>
                    </div>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: 1%"></div>
                    </div>
                    <div class="bar-count">1</div>
                </div>
            </div>
        </div>

        <!-- Reviews Controls -->
        <div class="reviews-controls">
            <div class="filter-tabs">
                <button class="filter-tab active" onclick="filterReviews('all')">All Reviews</button>
                <button class="filter-tab" onclick="filterReviews('5')">5 Stars</button>
                <button class="filter-tab" onclick="filterReviews('4')">4 Stars</button>
                <button class="filter-tab" onclick="filterReviews('3')">3 Stars</button>
                <button class="filter-tab" onclick="filterReviews('recent')">Recent</button>
            </div>
            
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search reviews..." onkeyup="searchReviews(event)">
            </div>
        </div>

        <!-- Reviews List -->
        <div class="reviews-list" id="reviewsList">
            <!-- Reviews will be populated by JavaScript -->
        </div>

        <!-- Empty State (Hidden by default) -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-star"></i>
            </div>
            <h3 class="empty-title">No Reviews Found</h3>
            <p class="empty-description">
                No reviews match your current filter criteria. Try adjusting your filters or search terms.
            </p>
        </div>
    </main>
</div>

<script>
// Sample reviews data
const sampleReviews = [
    {
        id: 1,
        client: "Sarah Mitchell",
        avatar: "SM",
        rating: 5,
        date: "2024-03-10",
        helpful: 8,
        text: "Absolutely incredible trainer! The personalized workout plans and constant motivation have helped me achieve goals I never thought possible. The attention to detail and professional approach is outstanding.",
        tags: ["Motivational", "Professional", "Results-driven"],
        verified: true
    },
    {
        id: 2,
        client: "John Davidson",
        avatar: "JD",
        rating: 5,
        date: "2024-03-08",
        helpful: 12,
        text: "Been training for 6 months now and the transformation has been amazing. Great understanding of nutrition and exercise science. Always punctual and well-prepared for sessions.",
        tags: ["Knowledgeable", "Punctual", "Transformation"],
        verified: true
    },
    {
        id: 3,
        client: "Mike Rodriguez",
        avatar: "MR",
        rating: 4,
        date: "2024-03-05",
        helpful: 5,
        text: "Really good trainer with solid technical knowledge. Helped me recover from a shoulder injury with careful rehabilitation exercises. Would recommend for injury recovery.",
        tags: ["Technical", "Rehabilitation", "Careful"],
        verified: true
    },
    {
        id: 4,
        client: "Emma Wilson",
        avatar: "EW",
        rating: 5,
        date: "2024-03-03",
        helpful: 15,
        text: "Best investment I've made for my health! The training sessions are challenging but fun, and the progress tracking keeps me motivated. Excellent communication and flexibility with scheduling.",
        tags: ["Fun", "Progress Tracking", "Flexible"],
        verified: true
    },
    {
        id: 5,
        client: "David Parker",
        avatar: "DP",
        rating: 5,
        date: "2024-02-28",
        helpful: 9,
        text: "Outstanding coach who really cares about client success. The workout variety keeps things interesting and the nutritional guidance has been a game-changer for my energy levels.",
        tags: ["Caring", "Variety", "Nutrition"],
        verified: true
    },
    {
        id: 6,
        client: "Lisa Chen",
        avatar: "LC",
        rating: 4,
        date: "2024-02-25",
        helpful: 6,
        text: "Great experience overall. Very patient and encouraging, especially for someone new to fitness like me. The only minor issue is sometimes sessions run a bit over time.",
        tags: ["Patient", "Encouraging", "Beginner-friendly"],
        verified: true
    },
    {
        id: 7,
        client: "Alex Thompson",
        avatar: "AT",
        rating: 5,
        date: "2024-02-20",
        helpful: 11,
        text: "Phenomenal trainer! Helped me prepare for my first marathon with a structured training plan. The mental coaching aspect was just as important as the physical training.",
        tags: ["Structured", "Marathon Training", "Mental Coaching"],
        verified: true
    },
    {
        id: 8,
        client: "Grace Lopez",
        avatar: "GL",
        rating: 5,
        date: "2024-02-18",
        helpful: 7,
        text: "Love the positive energy and expertise! The functional fitness approach has improved my daily activities significantly. Always learns about new techniques and shares them.",
        tags: ["Positive Energy", "Functional Fitness", "Continuous Learning"],
        verified: true
    },
    {
        id: 9,
        client: "Robert Kim",
        avatar: "RK",
        rating: 4,
        date: "2024-02-15",
        helpful: 4,
        text: "Solid trainer with good knowledge of strength training. Helped me break through plateaus with new techniques. Would like to see more variety in cardio workouts.",
        tags: ["Strength Training", "Plateau Breaking", "Techniques"],
        verified: true
    },
    {
        id: 10,
        client: "Maria Garcia",
        avatar: "MG",
        rating: 5,
        date: "2024-02-12",
        helpful: 13,
        text: "Exceptional trainer who goes above and beyond! The personalized approach and constant check-ins make all the difference. Highly recommend to anyone serious about fitness.",
        tags: ["Personalized", "Check-ins", "Above and Beyond"],
        verified: true
    }
];

let currentFilter = 'all';
let searchTerm = '';

// Render reviews
function renderReviews() {
    const container = document.getElementById('reviewsList');
    const emptyState = document.getElementById('emptyState');

    let filteredReviews = sampleReviews;

    // Apply rating filter
    if (currentFilter !== 'all' && currentFilter !== 'recent') {
        const targetRating = parseInt(currentFilter);
        filteredReviews = filteredReviews.filter(review => review.rating === targetRating);
    }

    // Apply search filter
    if (searchTerm) {
        filteredReviews = filteredReviews.filter(review => 
            review.client.toLowerCase().includes(searchTerm.toLowerCase()) ||
            review.text.toLowerCase().includes(searchTerm.toLowerCase()) ||
            review.tags.some(tag => tag.toLowerCase().includes(searchTerm.toLowerCase()))
        );
    }

    // Sort by recent if selected
    if (currentFilter === 'recent') {
        filteredReviews.sort((a, b) => new Date(b.date) - new Date(a.date));
    }

    if (filteredReviews.length === 0) {
        container.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    container.style.display = 'flex';
    emptyState.style.display = 'none';

    container.innerHTML = filteredReviews.map(review => `
        <div class="review-card">
            <div class="review-header">
                <div class="reviewer-info">
                    <div class="reviewer-avatar">${review.avatar}</div>
                    <div class="reviewer-details">
                        <div class="reviewer-name">
                            ${review.client}
                            ${review.verified ? '<i class="fas fa-check-circle" style="color: var(--success-color); margin-left: 0.5rem;" title="Verified Client"></i>' : ''}
                        </div>
                        <div class="review-meta">
                            <div class="review-rating">
                                <div class="review-stars">
                                    ${generateStars(review.rating)}
                                </div>
                                <span>${review.rating}/5</span>
                            </div>
                            <span class="review-date">${formatDate(review.date)}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="review-content">
                <p class="review-text">${review.text}</p>
                <div class="review-tags">
                    ${review.tags.map(tag => `<span class="review-tag">${tag}</span>`).join('')}
                </div>
            </div>

            <div class="review-actions">
                <div class="review-helpful">
                    <i class="fas fa-thumbs-up"></i>
                    <span>${review.helpful} found helpful</span>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-outline btn-sm" onclick="replyToReview(${review.id})">
                        <i class="fas fa-reply"></i>
                        Reply
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="shareReview(${review.id})">
                        <i class="fas fa-share"></i>
                        Share
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Generate star rating
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star star"></i>';
        } else {
            stars += '<i class="fas fa-star star empty"></i>';
        }
    }
    return stars;
}

// Filter functionality
function filterReviews(filter) {
    currentFilter = filter;
    
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    renderReviews();
}

// Search functionality
function searchReviews(event) {
    searchTerm = event.target.value;
    renderReviews();
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

function replyToReview(reviewId) {
    console.log('Reply to review:', reviewId);
    alert(`Reply to Review ${reviewId} - This would open a modal to compose a reply`);
}

function shareReview(reviewId) {
    console.log('Share review:', reviewId);
    alert(`Share Review ${reviewId} - This would provide sharing options for social media`);
}

function requestReview() {
    alert('Request Review - This would open a modal to select clients and send review requests');
}

function exportReviews() {
    alert('Export Reviews - This would generate a comprehensive report of all reviews');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    renderReviews();
});
</script>