<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
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
    <meta name="description" content="<?= htmlspecialchars($news['excerpt'] ?? substr(strip_tags($news['content']), 0, 160)) ?>">
    <meta name="keywords" content="sports news, <?= htmlspecialchars($news['category']) ?>, GoPlay">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($news['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($news['excerpt'] ?? '') ?>">
    <meta property="og:image" content="<?= imgUrl($news['featured_image'] ?? null, '/public/assets/images/default-news.jpg') ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
    <meta property="twitter:title" content="<?= htmlspecialchars($news['title']) ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($news['excerpt'] ?? '') ?>">
    <meta property="twitter:image" content="<?= imgUrl($news['featured_image'] ?? null, '/public/assets/images/default-news.jpg') ?>">

    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/news-detail.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Schema.org structured data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": "<?= htmlspecialchars($news['title']) ?>",
        "image": "<?= imgUrl($news['featured_image'] ?? null, '/public/assets/images/default-news.jpg') ?>",
        "datePublished": "<?= date('c', strtotime($news['published_at'])) ?>",
        "dateModified": "<?= date('c', strtotime($news['updated_at'] ?? $news['published_at'])) ?>",
        "author": {
            "@type": "Person",
            "name": "<?= $author ? htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) : 'GoPlay Sports' ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "GoPlay Sports",
            "logo": {
                "@type": "ImageObject",
                "url": "/public/assets/images/logo.jpeg"
            }
        },
        "description": "<?= htmlspecialchars($news['excerpt'] ?? '') ?>",
        "articleSection": "<?= htmlspecialchars($news['category']) ?>"
    }
    </script>
</head>
<body>
    <div class="news-detail-container">
        <a href="<?= $_base ?>/news" class="back-btn" aria-label="Back to News">← Back to News</a>
        
        <!-- Article Header -->
        <header class="article-header">
            <span class="article-category"><?= htmlspecialchars($news['category']) ?></span>
            <h1 class="article-title"><?= htmlspecialchars($news['title']) ?></h1>
            
            <?php if (!empty($news['excerpt'])): ?>
            <p class="article-excerpt"><?= htmlspecialchars($news['excerpt']) ?></p>
            <?php endif; ?>
            
            <div class="article-meta">
                <div class="author-info">
                    <?php if ($author): ?>
                    <img src="<?= imgUrl($author['profile_picture'] ?? null) ?>" 
                         alt="<?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?>" 
                         class="author-avatar"
                         loading="lazy">
                    <div class="author-details">
                        <h4><?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?></h4>
                        <div class="publish-date">
                            <time datetime="<?= date('c', strtotime($news['published_at'])) ?>">
                                <?= date('F j, Y', strtotime($news['published_at'])) ?>
                            </time>
                        </div>
                    </div>
                    <?php else: ?>
                    <img src="<?= $_base ?>/public/assets/images/default-avatar.png" 
                         alt="GoPlay Sports" 
                         class="author-avatar"
                         loading="lazy">
                    <div class="author-details">
                        <h4>GoPlay Sports</h4>
                        <div class="publish-date">
                            <time datetime="<?= date('c', strtotime($news['published_at'])) ?>">
                                <?= date('F j, Y', strtotime($news['published_at'])) ?>
                            </time>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="article-stats">
                    <span><i class="fa-solid fa-eye"></i> <?= number_format($news['views'] ?? 0) ?> views</span>
                    <span id="reading-time"><i class="fas fa-stopwatch"></i> <?= $news['reading_time'] ?? '5 min read' ?></span>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if (!empty($news['featured_image'])): ?>
        <img src="<?= imgUrl($news['featured_image'] ?? null, '/public/assets/images/default-news.jpg') ?>" 
             alt="<?= htmlspecialchars($news['title']) ?>" 
             class="featured-image"
             loading="lazy">
        <?php endif; ?>

        <!-- Article Content -->
        <main class="article-content">
            <div class="article-body">
                <?= nl2br(htmlspecialchars($news['content'])) ?>
            </div>
        </main>

        <!-- Share Buttons -->
        <div class="share-buttons" role="toolbar" aria-label="Share this article">
            <a href="#" class="share-btn share-facebook" onclick="shareOnFacebook(); return false;" title="Share on Facebook" aria-label="Share on Facebook">
                <i class="fa-brands fa-facebook"></i> Share on Facebook
            </a>
            <a href="#" class="share-btn share-twitter" onclick="shareOnTwitter(); return false;" title="Share on Twitter" aria-label="Share on Twitter">
                <i class="fa-brands fa-x-twitter"></i> Share on Twitter
            </a>
            <a href="#" class="share-btn share-linkedin" onclick="shareOnLinkedIn(); return false;" title="Share on LinkedIn" aria-label="Share on LinkedIn">
                <i class="fa-brands fa-linkedin"></i> Share on LinkedIn
            </a>
            <button class="share-btn share-copy" onclick="copyToClipboard()" title="Copy Link" aria-label="Copy article link">
                <i class="fa-solid fa-link"></i> Copy Link
            </button>
        </div>

        <!-- Related News -->
        <?php if (!empty($relatedNews)): ?>
        <section class="related-news" aria-labelledby="related-title">
            <h3 class="related-title" id="related-title">Related Articles</h3>
            <div class="related-grid">
                <?php foreach ($relatedNews as $related): ?>
                <article class="related-card">
                    <img src="<?= imgUrl($related['featured_image'] ?? null, '/public/assets/images/default-news.jpg') ?>" 
                         alt="<?= htmlspecialchars($related['title']) ?>" 
                         class="related-image"
                         loading="lazy">
                    <div class="related-content">
                        <h4>
                            <a href="<?= $_base ?>/news/<?= htmlspecialchars($related['slug']) ?>">
                                <?= htmlspecialchars($related['title']) ?>
                            </a>
                        </h4>
                        <div class="related-meta">
                            <time datetime="<?= date('c', strtotime($related['published_at'])) ?>">
                                <?= date('M j, Y', strtotime($related['published_at'])) ?>
                            </time>
                             • 
                            <span><?= number_format($related['views'] ?? 0) ?> views</span>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php else: ?>
        <!-- Debug: Show if no related news found -->
        <?php if (isset($_GET['debug'])): ?>
        <div style="background: #f0f0f0; padding: 10px; margin: 20px 0; border-radius: 5px;">
            <p><strong>Debug:</strong> No related articles found.</p>
            <p>Current article category: <?= htmlspecialchars($news['category']) ?></p>
            <p>Current article ID: <?= htmlspecialchars($news['id']) ?></p>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Article data for sharing
        const articleData = {
            title: <?= json_encode($news['title']) ?>,
            url: window.location.href,
            description: <?= json_encode($news['excerpt'] ?? '') ?>
        };

        function shareOnFacebook() {
            const url = encodeURIComponent(articleData.url);
            const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            openShareWindow(shareUrl);
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(articleData.url);
            const text = encodeURIComponent(articleData.title);
            const shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}&via=GoPlaySports`;
            openShareWindow(shareUrl);
        }

        function shareOnLinkedIn() {
            const url = encodeURIComponent(articleData.url);
            const title = encodeURIComponent(articleData.title);
            const summary = encodeURIComponent(articleData.description);
            const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}&summary=${summary}`;
            openShareWindow(shareUrl);
        }

        function copyToClipboard() {
            // Modern approach with Clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(articleData.url).then(() => {
                    showCopySuccess();
                }).catch(() => {
                    fallbackCopyToClipboard();
                });
            } else {
                fallbackCopyToClipboard();
            }
        }

        function fallbackCopyToClipboard() {
            const tempInput = document.createElement('input');
            tempInput.value = articleData.url;
            document.body.appendChild(tempInput);
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                document.execCommand('copy');
                showCopySuccess();
            } catch (err) {
                console.error('Failed to copy text: ', err);
                alert('Copy failed. Please copy the URL manually: ' + articleData.url);
            }
            
            document.body.removeChild(tempInput);
        }

        function showCopySuccess() {
            const copyBtn = document.querySelector('.share-copy');
            const originalText = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            copyBtn.style.background = 'linear-gradient(135deg, #27ae60 0%, #219a52 100%)';
            
            setTimeout(() => {
                copyBtn.innerHTML = originalText;
                copyBtn.style.background = 'linear-gradient(135deg, #4a7c23 0%, #2d5016 100%)';
            }, 2000);
        }

        function openShareWindow(url) {
            const width = 600;
            const height = 400;
            const left = (window.innerWidth - width) / 2;
            const top = (window.innerHeight - height) / 2;
            
            window.open(
                url, 
                'share-window', 
                `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
            );
        }

        // Calculate and update reading time
        function calculateReadingTime() {
            const content = document.querySelector('.article-body').textContent;
            const wordsPerMinute = 200;
            const wordCount = content.trim().split(/\s+/).length;
            const readingTime = Math.ceil(wordCount / wordsPerMinute);
            
            const readingTimeElement = document.getElementById('reading-time');
            if (readingTimeElement && !readingTimeElement.textContent.includes('min read')) {
                readingTimeElement.textContent = `<i class="fas fa-stopwatch"></i> ${readingTime} min read`;
            }
        }

        // Initialize reading time calculation
        document.addEventListener('DOMContentLoaded', calculateReadingTime);

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
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

        // Add print functionality
        function printArticle() {
            window.print();
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P for print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printArticle();
            }
            
            // Escape key to go back
            if (e.key === 'Escape') {
                window.history.back();
            }
        });
    </script>
</body>
</html>