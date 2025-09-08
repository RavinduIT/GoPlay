<?php
// Extract data from controller
$news = $data['news'] ?? [];
$featured = $data['featured'] ?? null;
$categories = $data['categories'] ?? [];
$currentCategory = $data['currentCategory'] ?? 'all';
$search = $data['search'] ?? '';
$pagination = $data['pagination'] ?? [];
$total = $data['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports News - GoPlay Platform</title>
    <meta name="description" content="Stay updated with the latest sports news and updates from GoPlay Platform">
    <link rel="stylesheet" href="/public/css/news-index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sports News</h1>
            <p>Stay updated with the latest sports news and updates from around the world</p>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <form class="search-form" method="GET" action="/news/search">
                <input type="text" name="q" class="search-input" placeholder="Search news..." value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <!-- Category Filters -->
        <?php if (!empty($categories)): ?>
        <div class="category-filters">
            <?php foreach ($categories as $key => $label): ?>
                <button class="category-btn <?= $currentCategory === $key ? 'active' : '' ?>" 
                        onclick="filterByCategory('<?= $key ?>')">
                    <?= htmlspecialchars($label) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Featured News -->
        <?php if ($featured): ?>
        <div class="featured-section">
            <div class="featured-card">
                <img src="<?= htmlspecialchars($featured['featured_image'] ?? '/public/assets/images/default-news.jpg') ?>" 
                     alt="<?= htmlspecialchars($featured['title']) ?>" 
                     class="featured-image"
                     loading="lazy">
                <div class="featured-content">
                    <h2>
                        <a href="/news/<?= htmlspecialchars($featured['slug']) ?>" class="news-title">
                            <?= htmlspecialchars($featured['title']) ?>
                        </a>
                    </h2>
                    <?php if (!empty($featured['excerpt'])): ?>
                    <p class="featured-excerpt"><?= htmlspecialchars($featured['excerpt']) ?></p>
                    <?php endif; ?>
                    <div class="featured-meta">
                        <span>📂 <?= htmlspecialchars($featured['category']) ?></span>
                        <span>📅 <?= date('M j, Y', strtotime($featured['published_at'])) ?></span>
                        <span>👁️ <?= number_format($featured['views'] ?? 0) ?> views</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- News Grid -->
        <div id="news-container">
            <?php if (!empty($news)): ?>
            <div class="news-grid" id="news-grid">
                <?php foreach ($news as $article): ?>
                <article class="news-card">
                    <img src="<?= htmlspecialchars($article['featured_image'] ?? '/public/assets/images/default-news.jpg') ?>" 
                         alt="<?= htmlspecialchars($article['title']) ?>" 
                         class="news-image"
                         loading="lazy">
                    <div class="news-content">
                        <span class="news-category"><?= htmlspecialchars($article['category']) ?></span>
                        <h3>
                            <a href="/news/<?= htmlspecialchars($article['slug']) ?>" class="news-title">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                        </h3>
                        <?php if (!empty($article['excerpt'])): ?>
                        <p class="news-excerpt"><?= htmlspecialchars($article['excerpt']) ?></p>
                        <?php endif; ?>
                        <div class="news-meta">
                            <span>📅 <?= date('M j, Y', strtotime($article['published_at'])) ?></span>
                            <span>👁️ <?= number_format($article['views'] ?? 0) ?> views</span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            
            <!-- Load More Button (for AJAX loading) -->
            <button id="load-more-btn" class="load-more-btn" style="display: none;" onclick="loadMoreNews()">
                Load More News
            </button>
            <?php else: ?>
            <div class="no-results">
                <h3>No news articles found</h3>
                <p>Try adjusting your search or category filter to find what you're looking for.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <nav class="pagination" aria-label="News pagination">
            <?php if ($pagination['has_prev']): ?>
                <a href="?page=<?= $pagination['prev_page'] ?><?= $currentCategory !== 'all' ? '&category=' . urlencode($currentCategory) : '' ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="page-btn" aria-label="Previous page">
                    « Previous
                </a>
            <?php endif; ?>

            <?php 
            $start = max(1, $pagination['current_page'] - 2);
            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
            
            if ($start > 1): ?>
                <a href="?page=1<?= $currentCategory !== 'all' ? '&category=' . urlencode($currentCategory) : '' ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="page-btn">1</a>
                <?php if ($start > 2): ?>
                    <span class="page-btn disabled">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?= $i ?><?= $currentCategory !== 'all' ? '&category=' . urlencode($currentCategory) : '' ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" 
                   class="page-btn <?= $i === $pagination['current_page'] ? 'active' : '' ?>"
                   <?= $i === $pagination['current_page'] ? 'aria-current="page"' : '' ?>>
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($end < $pagination['total_pages']): ?>
                <?php if ($end < $pagination['total_pages'] - 1): ?>
                    <span class="page-btn disabled">...</span>
                <?php endif; ?>
                <a href="?page=<?= $pagination['total_pages'] ?><?= $currentCategory !== 'all' ? '&category=' . urlencode($currentCategory) : '' ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="page-btn"><?= $pagination['total_pages'] ?></a>
            <?php endif; ?>

            <?php if ($pagination['has_next']): ?>
                <a href="?page=<?= $pagination['next_page'] ?><?= $currentCategory !== 'all' ? '&category=' . urlencode($currentCategory) : '' ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="page-btn" aria-label="Next page">
                    Next »
                </a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </div>

    <script>
        // Category filtering function
        function filterByCategory(category) {
            const currentUrl = new URL(window.location);
            if (category === 'all') {
                currentUrl.searchParams.delete('category');
            } else {
                currentUrl.searchParams.set('category', category);
            }
            currentUrl.searchParams.delete('page'); // Reset to first page
            window.location.href = currentUrl.toString();
        }

        // Load more news function (for AJAX implementation)
        function loadMoreNews() {
            // This function would be implemented with AJAX
            // For now, it's just a placeholder
            console.log('Load more news functionality would be implemented here');
        }

        // Search form enhancement
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            const searchInput = this.querySelector('.search-input');
            if (searchInput.value.trim() === '') {
                e.preventDefault();
                searchInput.focus();
            }
        });

        // Add loading state to category buttons
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Add loading state
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
            });
        });

        // Lazy loading for images (fallback for older browsers)
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
</body>
</html>