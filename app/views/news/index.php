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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .search-container {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .search-form {
            display: inline-flex;
            gap: 10px;
        }
        
        .search-input {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            width: 300px;
        }
        
        .search-btn {
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .search-btn:hover {
            background: #2980b9;
        }
        
        .category-filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .category-btn {
            padding: 8px 16px;
            background: #ecf0f1;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category-btn.active {
            background: #3498db;
            color: white;
        }
        
        .category-btn:hover {
            background: #bdc3c7;
        }
        
        .category-btn.active:hover {
            background: #2980b9;
        }
        
        .featured-section {
            margin-bottom: 40px;
        }
        
        .featured-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
        }
        
        .featured-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .featured-content h2 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 2em;
        }
        
        .featured-excerpt {
            color: #7f8c8d;
            margin-bottom: 15px;
            font-size: 1.1em;
        }
        
        .featured-meta {
            display: flex;
            gap: 20px;
            color: #95a5a6;
            font-size: 0.9em;
        }
        
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .news-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .news-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .news-content {
            padding: 20px;
        }
        
        .news-category {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-bottom: 10px;
        }
        
        .news-title {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.3em;
            text-decoration: none;
        }
        
        .news-title:hover {
            color: #3498db;
        }
        
        .news-excerpt {
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .news-meta {
            display: flex;
            justify-content: space-between;
            color: #95a5a6;
            font-size: 0.9em;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        
        .page-btn {
            padding: 8px 16px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }
        
        .page-btn.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .page-btn:hover:not(.active) {
            background: #ecf0f1;
        }
        
        .load-more-btn {
            display: block;
            margin: 40px auto;
            padding: 12px 30px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        
        .load-more-btn:hover {
            background: #2980b9;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        @media (max-width: 768px) {
            .featured-card {
                grid-template-columns: 1fr;
            }
            
            .news-grid {
                grid-template-columns: 1fr;
            }
            
            .search-form {
                flex-direction: column;
                align-items: center;
            }
            
            .search-input {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sports News</h1>
            <p>Stay updated with the latest sports news and updates</p>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <form class="search-form" method="GET" action="/news/search">
                <input type="text" name="q" class="search-input" placeholder="Search news..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <!-- Category Filters -->
        <div class="category-filters">
            <?php foreach ($categories as $key => $label): ?>
                <button class="category-btn <?= $currentCategory === $key ? 'active' : '' ?>" 
                        onclick="filterByCategory('<?= $key ?>')">
                    <?= htmlspecialchars($label) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Featured News -->
        <?php if ($featured): ?>
        <div class="featured-section">
            <div class="featured-card">
                <img src="<?= htmlspecialchars($featured['featured_image'] ?? '/public/assets/images/default-news.jpg') ?>" 
                     alt="<?= htmlspecialchars($featured['title']) ?>" 
                     class="featured-image">
                <div class="featured-content">
                    <h2>
                        <a href="/news/<?= htmlspecialchars($featured['slug']) ?>" class="news-title">
                            <?= htmlspecialchars($featured['title']) ?>
                        </a>
                    </h2>
                    <p class="featured-excerpt"><?= htmlspecialchars($featured['excerpt'] ?? '') ?></p>
                    <div class="featured-meta">
                        <span><?= htmlspecialchars($featured['category']) ?></span>
                        <span><?= date('M j, Y', strtotime($featured['published_at'])) ?></span>
                        <span><?= $featured['views'] ?? 0 ?> views</span>
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
                <div class="news-card">
                    <img src="<?= htmlspecialchars($article['featured_image'] ?? '/public/assets/images/default-news.jpg') ?>" 
                         alt="<?= htmlspecialchars($article['title']) ?>" 
                         class="news-image">
                    <div class="news-content">
                        <span class="news-category"><?= htmlspecialchars($article['category']) ?></span>
                        <h3>
                            <a href="/news/<?= htmlspecialchars($article['slug']) ?>" class="news-title">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                        </h3>
                        <p class="news-excerpt"><?= htmlspecialchars($article['excerpt'] ?? '') ?></p>
                        <div class="news-meta">
                            <span><?= date('M j, Y', strtotime($article['published_at'])) ?></span>
                            <span><?= $article['views'] ?? 0 ?> views</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Load More Button (for AJAX loading) -->
            <button id="load-more-btn" class="load-more-btn" style="display: none;" onclick="loadMoreNews()">
                Load More News
            </button>
            <?php else: ?>
            <div class="no-results">
                <h3>No news articles found</h3>
                <p>Try adjusting your search or category filter.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['has_prev']): ?>
                <a href="?page=<?= $pagination['prev_page'] ?><?= $currentCategory !== 'all' ? '&category=' . $currentCategory : '' ?>" class="page-btn">
                    « Previous
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <a href="?page=<?= $i ?><?= $currentCategory !== 'all' ? '&category=' . $currentCategory : '' ?>" 
                   class="page-btn <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($pagination['has_next']): ?>
                <a href="?page=<?= $pagination['next_page'] ?><?= $currentCategory !== 'all' ? '&category=' . $currentCategory : '' ?>" class="page-btn">
                    Next »
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="/public/js/pages/news-index.js"></script>
</body>
</html>