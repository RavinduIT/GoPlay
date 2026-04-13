<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php
/**
 * News Search Results View
 */
$news = $data['news'] ?? [];
$query = $data['query'] ?? '';
$count = $data['count'] ?? 0;
$categories = $data['categories'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search: <?= htmlspecialchars($query) ?> - GoPlay Sports News</title>
    <meta name="description" content="Search results for '<?= htmlspecialchars($query) ?>' on GoPlay Sports News">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/news-index.css">
</head>
<body>
    <?php include APP_PATH . '/views/components/navbar.php'; ?>

    <div class="container" style="padding-top: 100px;">
        <div class="header">
            <a href="<?= $_base ?>/news" class="back-link" style="display: inline-flex; align-items: center; gap: 8px; color: #64748b; text-decoration: none; margin-bottom: 16px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to News
            </a>
            <h1>Search Results</h1>
            <p>
                <?php if ($count > 0): ?>
                    Found <strong><?= $count ?></strong> result<?= $count !== 1 ? 's' : '' ?> for "<strong><?= htmlspecialchars($query) ?></strong>"
                <?php else: ?>
                    No results found for "<strong><?= htmlspecialchars($query) ?></strong>"
                <?php endif; ?>
            </p>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <form class="search-form" method="GET" action="<?= $_base ?>/news/search">
                <input type="text" name="q" class="search-input" placeholder="Search news..." value="<?= htmlspecialchars($query) ?>" autocomplete="off">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <!-- Category Filters -->
        <?php if (!empty($categories)): ?>
        <div class="category-filters">
            <?php foreach ($categories as $key => $label): ?>
                <button class="category-btn" onclick="filterSearchByCategory('<?= htmlspecialchars($key) ?>')">
                    <?= htmlspecialchars($label) ?>
                </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($count > 0): ?>
        <!-- Results Grid -->
        <div class="news-grid">
            <?php foreach ($news as $article): ?>
            <article class="news-card clickable-card" data-href="<?= $_base ?>/news/<?= htmlspecialchars($article['slug']) ?>">
                <img src="<?= htmlspecialchars($article['featured_image'] ?? '/public/assets/images/default-news.jpg') ?>" 
                     alt="<?= htmlspecialchars($article['title']) ?>" 
                     class="news-image"
                     loading="lazy">
                <div class="news-content">
                    <span class="news-category"><?= htmlspecialchars($article['category']) ?></span>
                    <h3>
                        <a href="<?= $_base ?>/news/<?= htmlspecialchars($article['slug']) ?>" class="news-title">
                            <?= htmlspecialchars($article['title']) ?>
                        </a>
                    </h3>
                    <?php if (!empty($article['excerpt'])): ?>
                    <p class="news-excerpt"><?= htmlspecialchars($article['excerpt']) ?></p>
                    <?php endif; ?>
                    <div class="news-meta">
                        <time datetime="<?= date('c', strtotime($article['published_at'])) ?>">
                            <?= date('M j, Y', strtotime($article['published_at'])) ?>
                        </time>
                        <span><i class="fas fa-eye"></i> <?= number_format($article['views'] ?? 0) ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- No Results State -->
        <div style="text-align: center; padding: 80px 20px; color: #64748b;">
            <i class="fas fa-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 20px; display: block;"></i>
            <h2 style="color: #334155; margin-bottom: 12px;">No articles found</h2>
            <p style="max-width: 400px; margin: 0 auto 24px;">Try different keywords or browse all news articles.</p>
            <a href="<?= $_base ?>/news" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #3b82f6; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600;">
                <i class="fas fa-newspaper"></i> Browse All News
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php include APP_PATH . '/views/components/footer.php'; ?>

    <script>
        // Make cards clickable
        document.querySelectorAll('.clickable-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') return;
                const href = this.dataset.href;
                if (href) window.location.href = href;
            });
        });

        function filterSearchByCategory(category) {
            const query = '<?= addslashes($query) ?>';
            if (category === 'all') {
                window.location.href = '/news/search?q=' + encodeURIComponent(query);
            } else {
                window.location.href = '/news?category=' + encodeURIComponent(category);
            }
        }
    </script>
</body>
</html>
