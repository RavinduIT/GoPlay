<?php
// Extract data from controller
$news = $data['news'] ?? null;
$author = $data['author'] ?? null;
$relatedNews = $data['relatedNews'] ?? [];

if (!$news) {
    header('Location: /news');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - GoPlay Sports</title>
    <meta name="description" content="<?= htmlspecialchars($news['excerpt'] ?? '') ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Georgia, serif;
            line-height: 1.8;
            color: #333;
            background-color: #f9f9f9;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #ecf0f1;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
        }
        
        .back-btn:hover {
            background: #bdc3c7;
        }
        
        .article-header {
            background: white;
            border-radius: 10px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .article-category {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.9em;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
        }
        
        .article-title {
            color: #2c3e50;
            font-size: 2.5em;
            margin-bottom: 15px;
            line-height: 1.2;
        }
        
        .article-excerpt {
            color: #7f8c8d;
            font-size: 1.2em;
            margin-bottom: 25px;
            font-style: italic;
        }
        
        .article-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-top: 1px solid #ecf0f1;
            border-bottom: 1px solid #ecf0f1;
            font-family: Arial, sans-serif;
        }
        
        .author-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .author-details h4 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .publish-date {
            color: #95a5a6;
            font-size: 0.9em;
        }
        
        .article-stats {
            display: flex;
            gap: 20px;
            color: #95a5a6;
            font-size: 0.9em;
        }
        
        .featured-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .article-content {
            background: white;
            border-radius: 10px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .article-body {
            font-size: 1.1em;
            line-height: 1.8;
        }
        
        .article-body p {
            margin-bottom: 20px;
        }
        
        .article-body h2 {
            color: #2c3e50;
            margin: 30px 0 15px 0;
            font-size: 1.5em;
        }
        
        .article-body h3 {
            color: #34495e;
            margin: 25px 0 12px 0;
            font-size: 1.3em;
        }
        
        .article-body ul, .article-body ol {
            margin-bottom: 20px;
            padding-left: 30px;
        }
        
        .article-body blockquote {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            font-style: italic;
        }
        
        .article-tags {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .tags-title {
            color: #2c3e50;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
        }
        
        .tag {
            display: inline-block;
            background: #ecf0f1;
            color: #2c3e50;
            padding: 6px 12px;
            border-radius: 20px;
            margin: 5px 5px 5px 0;
            font-size: 0.9em;
            text-decoration: none;
            font-family: Arial, sans-serif;
        }
        
        .tag:hover {
            background: #bdc3c7;
        }
        
        .related-news {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .related-title {
            color: #2c3e50;
            margin-bottom: 25px;
            font-family: Arial, sans-serif;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .related-card {
            border: 1px solid #ecf0f1;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .related-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .related-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .related-content {
            padding: 15px;
        }
        
        .related-content h4 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1em;
        }
        
        .related-content h4 a {
            color: inherit;
            text-decoration: none;
        }
        
        .related-content h4 a:hover {
            color: #3498db;
        }
        
        .related-meta {
            color: #95a5a6;
            font-size: 0.8em;
            font-family: Arial, sans-serif;
        }
        
        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .share-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            font-family: Arial, sans-serif;
            transition: transform 0.2s;
        }
        
        .share-btn:hover {
            transform: scale(1.05);
        }
        
        .share-facebook {
            background: #3b5998;
            color: white;
        }
        
        .share-twitter {
            background: #1da1f2;
            color: white;
        }
        
        .share-linkedin {
            background: #0077b5;
            color: white;
        }
        
        .share-copy {
            background: #95a5a6;
            color: white;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .article-header, .article-content {
                padding: 25px;
            }
            
            .article-title {
                font-size: 1.8em;
            }
            
            .article-meta {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .related-grid {
                grid-template-columns: 1fr;
            }
            
            .share-buttons {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/news" class="back-btn">← Back to News</a>
        
        <!-- Article Header -->
        <div class="article-header">
            <span class="article-category"><?= htmlspecialchars($news['category']) ?></span>
            <h1 class="article-title"><?= htmlspecialchars($news['title']) ?></h1>
            
            <?php if ($news['excerpt']): ?>
            <p class="article-excerpt"><?= htmlspecialchars($news['excerpt']) ?></p>
            <?php endif; ?>
            
            <div class="article-meta">
                <div class="author-info">
                    <?php if ($author): ?>
                    <img src="<?= htmlspecialchars($author['profile_picture'] ?? '/public/assets/images/default-avatar.png') ?>" 
                         alt="<?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?>" 
                         class="author-avatar">
                    <div class="author-details">
                        <h4><?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?></h4>
                        <div class="publish-date"><?= date('F j, Y', strtotime($news['published_at'])) ?></div>
                    </div>
                    <?php else: ?>
                    <div class="author-details">
                        <h4>GoPlay Sports</h4>
                        <div class="publish-date"><?= date('F j, Y', strtotime($news['published_at'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="article-stats">
                    <span><?= $news['views'] ?? 0 ?> views</span>
                    <span id="reading-time"><?= $news['reading_time'] ?? '5 min read' ?></span>
                </div>
            </div>
        </div>

        <!-- Featured Image -->
        <?php if ($news['featured_image']): ?>
        <img src="<?= htmlspecialchars($news['featured_image']) ?>" 
             alt="<?= htmlspecialchars($news['title']) ?>" 
             class="featured-image">
        <?php endif; ?>

        <!-- Article Content -->
        <div class="article-content">
            <div class="article-body">
                <?= nl2br($news['content']) ?>
            </div>
        </div>

        <!-- Tags -->
        <?php if (!empty($news['tags'])): ?>
        <div class="article-tags">
            <h3 class="tags-title">Tags</h3>
            <?php 
            $tags = is_array($news['tags']) ? $news['tags'] : json_decode($news['tags'] ?? '[]', true);
            foreach ($tags as $tag): ?>
                <a href="/news/search?q=<?= urlencode($tag) ?>" class="tag"><?= htmlspecialchars($tag) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Share Buttons -->
        <div class="share-buttons">
            <a href="#" class="share-btn share-facebook" onclick="shareOnFacebook()" title="Share on Facebook">
                Share on Facebook
            </a>
            <a href="#" class="share-btn share-twitter" onclick="shareOnTwitter()" title="Share on Twitter">
                Share on Twitter
            </a>
            <a href="#" class="share-btn share-linkedin" onclick="shareOnLinkedIn()" title="Share on LinkedIn">
                Share on LinkedIn
            </a>
            <button class="share-btn share-copy" onclick="copyToClipboard()" title="Copy Link">
                Copy Link
            </button>
        </div>

        <!-- Related News -->
        <?php if (!empty($relatedNews)): ?>
        <div class="related-news">
            <h3 class="related-title">Related Articles</h3>
            <div class="related-grid">
                <?php foreach ($relatedNews as $related): ?>
                <div class="related-card">
                    <img src="<?= htmlspecialchars($related['featured_image'] ?? '/public/assets/images/default-news.jpg') ?>" 
                         alt="<?= htmlspecialchars($related['title']) ?>" 
                         class="related-image">
                    <div class="related-content">
                        <h4>
                            <a href="/news/<?= htmlspecialchars($related['slug']) ?>">
                                <?= htmlspecialchars($related['title']) ?>
                            </a>
                        </h4>
                        <div class="related-meta">
                            <?= date('M j, Y', strtotime($related['published_at'])) ?> • 
                            <?= $related['views'] ?? 0 ?> views
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Article data for sharing
        const articleData = {
            title: <?= json_encode($news['title']) ?>,
            url: window.location.href,
            description: <?= json_encode($news['excerpt'] ?? '') ?>
        };
    </script>
    <script src="/public/js/pages/news-detail.js"></script>
</body>
</html>