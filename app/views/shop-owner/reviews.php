<?php
$title = 'Customer Reviews – Shop Owner';

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage   = $_SESSION['error_message']   ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

$additionalCSS = [
    '/public/css/pages/shop-owner-dashboard.css',
    '/public/css/pages/shop-owner-reviews.css',
];
$additionalJS = [
    '/public/js/shop-owner-sidebar.js',
    '/public/js/pages/shop-owner-reviews.js',
];
?>
<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<link rel="stylesheet" href="/public/css/pages/shop-owner-reviews.css">

<div class="shop-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">

        <header class="sr-page-header">
            <h1 class="sr-page-title">Customer Reviews</h1>
        </header>

        <div class="sr-content">

            <?php if ($successMessage): ?>
            <div class="sr-alert sr-alert--ok">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($successMessage) ?>
            </div>
            <?php endif; ?>
            <?php if ($errorMessage): ?>
            <div class="sr-alert sr-alert--err">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($errorMessage) ?>
            </div>
            <?php endif; ?>

            <!-- Stats Row -->
            <div class="sr-stats-row" id="srStats">
                <div class="sr-stat-card">
                    <div class="sr-stat-icon sr-icon-star"><i class="fas fa-star"></i></div>
                    <div class="sr-stat-body">
                        <div class="sr-stat-value" id="srAvgRating">—</div>
                        <div class="sr-stat-label">Average Rating</div>
                    </div>
                </div>
                <div class="sr-stat-card">
                    <div class="sr-stat-icon sr-icon-total"><i class="fas fa-comment-dots"></i></div>
                    <div class="sr-stat-body">
                        <div class="sr-stat-value" id="srTotalReviews">—</div>
                        <div class="sr-stat-label">Total Reviews</div>
                    </div>
                </div>
                <div class="sr-stat-card">
                    <div class="sr-stat-icon sr-icon-pending"><i class="fas fa-clock"></i></div>
                    <div class="sr-stat-body">
                        <div class="sr-stat-value" id="srPendingReplies">—</div>
                        <div class="sr-stat-label">Awaiting Reply</div>
                    </div>
                </div>
                <div class="sr-stat-card">
                    <div class="sr-stat-icon sr-icon-recent"><i class="fas fa-calendar-week"></i></div>
                    <div class="sr-stat-body">
                        <div class="sr-stat-value" id="srRecentCount">—</div>
                        <div class="sr-stat-label">This Week</div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="sr-distribution-card" id="srDistribution">
                <div class="sr-dist-header">
                    <h3>Rating Breakdown</h3>
                    <div class="sr-big-rating">
                        <span id="srBigAvg">—</span>
                        <div class="sr-big-stars" id="srBigStars"></div>
                    </div>
                </div>
                <div class="sr-bars" id="srBars">
                    <?php foreach ([5,4,3,2,1] as $s): ?>
                    <div class="sr-bar-row" data-star="<?= $s ?>">
                        <span class="sr-bar-label"><?= $s ?> <i class="fas fa-star"></i></span>
                        <div class="sr-bar-track"><div class="sr-bar-fill" style="width:0%"></div></div>
                        <span class="sr-bar-count">0</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Controls -->
            <div class="sr-controls">
                <div class="sr-search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="srSearch" placeholder="Search by customer or product…">
                </div>
                <div class="sr-filters">
                    <select id="srFilterRating">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <select id="srFilterReply">
                        <option value="">All Status</option>
                        <option value="replied">Replied</option>
                        <option value="pending">Not Replied</option>
                    </select>
                    <select id="srSort">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest">Highest Rating</option>
                        <option value="lowest">Lowest Rating</option>
                    </select>
                </div>
            </div>

            <!-- Reviews List -->
            <div id="srList" class="sr-list">
                <div class="sr-loading">
                    <div class="sr-spinner"></div>
                    <p>Loading reviews…</p>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Toast -->
<div id="srToast" class="sr-toast" aria-live="polite"></div>

<script src="/public/js/shop-owner-sidebar.js"></script>
<script src="/public/js/pages/shop-owner-reviews.js"></script>
