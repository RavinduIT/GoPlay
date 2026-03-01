<?php
$title = 'Reviews - GoPlay';
$additionalCSS = [
    '/public/css/pages/ground-owner-dashboard.css',
    '/public/css/pages/ground-owner-reviews.css'
];
$additionalJS = ['/public/js/pages/ground-owner-reviews.js'];
include __DIR__ . '/layout-head.php';
?>

<div class="ground-owner-dashboard">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Reviews</h1>
            </div>
            <div class="header-right"></div>
        </header>

        <div class="dashboard-content">

            <!-- Stats Row -->
            <div class="rv-stats-row" id="rvStats">
                <div class="rv-stat-card">
                    <div class="rv-stat-icon rv-icon-star"><i class="fas fa-star"></i></div>
                    <div class="rv-stat-body">
                        <div class="rv-stat-value" id="rvAvgRating">—</div>
                        <div class="rv-stat-label">Average Rating</div>
                    </div>
                </div>
                <div class="rv-stat-card">
                    <div class="rv-stat-icon rv-icon-total"><i class="fas fa-comment-dots"></i></div>
                    <div class="rv-stat-body">
                        <div class="rv-stat-value" id="rvTotalReviews">—</div>
                        <div class="rv-stat-label">Total Reviews</div>
                    </div>
                </div>
                <div class="rv-stat-card">
                    <div class="rv-stat-icon rv-icon-pending"><i class="fas fa-clock"></i></div>
                    <div class="rv-stat-body">
                        <div class="rv-stat-value" id="rvPendingReplies">—</div>
                        <div class="rv-stat-label">Awaiting Reply</div>
                    </div>
                </div>
                <div class="rv-stat-card">
                    <div class="rv-stat-icon rv-icon-recent"><i class="fas fa-calendar-week"></i></div>
                    <div class="rv-stat-body">
                        <div class="rv-stat-value" id="rvRecentCount">—</div>
                        <div class="rv-stat-label">This Week</div>
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="rv-distribution-card" id="rvDistribution">
                <div class="rv-dist-header">
                    <h3>Rating Breakdown</h3>
                    <div class="rv-big-rating">
                        <span id="rvBigAvg">—</span>
                        <div class="rv-big-stars" id="rvBigStars"></div>
                    </div>
                </div>
                <div class="rv-bars" id="rvBars">
                    <?php foreach ([5,4,3,2,1] as $s): ?>
                    <div class="rv-bar-row" data-star="<?= $s ?>">
                        <span class="rv-bar-label"><?= $s ?> <i class="fas fa-star"></i></span>
                        <div class="rv-bar-track"><div class="rv-bar-fill" style="width:0%"></div></div>
                        <span class="rv-bar-count">0</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Controls -->
            <div class="rv-controls">
                <div class="rv-search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="rvSearch" placeholder="Search reviews…">
                </div>
                <div class="rv-filters">
                    <select id="rvFilterRating">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <select id="rvFilterReply">
                        <option value="">All Status</option>
                        <option value="replied">Replied</option>
                        <option value="pending">Not Replied</option>
                    </select>
                    <select id="rvSort">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="highest">Highest Rating</option>
                        <option value="lowest">Lowest Rating</option>
                    </select>
                </div>
            </div>

            <!-- Reviews List -->
            <div id="rvList" class="rv-list">
                <div class="rv-loading">
                    <div class="rv-spinner"></div>
                    <p>Loading reviews…</p>
                </div>
            </div>

        </div><!-- /dashboard-content -->
    </main>
</div>

<!-- Toast -->
<div id="rvToast" class="rv-toast" aria-live="polite"></div>
<?php include __DIR__ . '/layout-foot.php'; ?>
