class NewsDetailPage {
    constructor() {
        this.currentArticle = null;
        this.relatedArticles = [];
        this.allNews = [];
        this.init();
    }

    async init() {
        try {
            // Get article ID from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const articleId = urlParams.get('id');
            const articleSlug = urlParams.get('slug');

            if (!articleId && !articleSlug) {
                this.showError('No article specified');
                return;
            }

            // Load news data
            await this.loadNewsData();

            // Find and display the article
            this.currentArticle = this.findArticle(articleId, articleSlug);
            
            if (!this.currentArticle) {
                this.showError('Article not found');
                return;
            }

            // Render the article
            this.renderArticle();
            this.loadRelatedArticles();
            this.updateMetaTags();
            this.incrementViews();
            this.setupEventListeners();
            this.hideLoading();

        } catch (error) {
            console.error('Error loading article:', error);
            this.showError('Failed to load article');
        }
    }

    async loadNewsData() {
        try {
            // Try to load from news.json first
            const response = await fetch('/data/news.json');
            if (response.ok) {
                const data = await response.json();
                this.allNews = data.news || [];
                return;
            }
        } catch (error) {
            console.warn('Could not load news.json:', error);
        }

        // Fallback to data.json
        try {
            const response = await fetch('/data/data.json');
            if (response.ok) {
                const data = await response.json();
                this.allNews = data.newsItems || [];
                return;
            }
        } catch (error) {
            console.warn('Could not load data.json:', error);
        }

        // Final fallback to empty array
        this.allNews = [];
    }

    findArticle(id, slug) {
        if (id) {
            return this.allNews.find(article => article.id === parseInt(id));
        }
        if (slug) {
            return this.allNews.find(article => article.slug === slug);
        }
        return null;
    }

    renderArticle() {
        if (!this.currentArticle) return;

        const article = this.currentArticle;

        // Update breadcrumb
        document.getElementById('breadcrumbCurrent').textContent = this.truncateText(article.title, 50);

        // Update meta information
        document.getElementById('articleCategory').innerHTML = `
            <i class="fas fa-tag"></i>
            <span>${article.category || 'Sports'}</span>
        `;

        const formattedDate = this.formatDate(article.date || article.publishedAt);
        document.getElementById('articleDate').innerHTML = `
            <i class="fas fa-calendar"></i>
            <time datetime="${article.date || article.publishedAt}">${formattedDate}</time>
        `;

        document.getElementById('articleReadTime').innerHTML = `
            <i class="fas fa-clock"></i>
            <span>${article.readTime || '5 min read'}</span>
        `;

        document.getElementById('articleViews').innerHTML = `
            <i class="fas fa-eye"></i>
            <span>${(article.views || 0).toLocaleString()} views</span>
        `;

        // Update title and summary
        document.getElementById('articleTitle').textContent = article.title;
        document.getElementById('articleSummary').textContent = article.summary;

        // Update author information
        document.getElementById('articleAuthor').innerHTML = `
            <div class="author-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="author-info">
                <p class="author-name">${article.author || 'SportHub Editor'}</p>
                <p class="author-title">Sports Journalist</p>
            </div>
        `;

        // Update featured image
        const imageContainer = document.getElementById('articleImageContainer');
        const articleImage = document.getElementById('articleImage');
        const imageCaption = document.getElementById('imageCaption');

        if (article.image || (article.images && article.images.length > 0)) {
            const imageSrc = article.image || article.images[0];
            articleImage.src = imageSrc;
            articleImage.alt = article.title;
            imageCaption.textContent = `Featured image: ${article.title}`;
            imageContainer.style.display = 'block';
        } else {
            imageContainer.style.display = 'none';
        }

        // Update content
        document.getElementById('articleContent').innerHTML = this.formatContent(article.content);

        // Update tags
        this.renderTags(article.tags || []);

        // Update page title
        document.title = `${article.title} - SportHub`;
    }

    formatContent(content) {
        if (!content) return '<p>Content not available for this article.</p>';

        // Convert line breaks to paragraphs
        const paragraphs = content.split('\n\n').filter(p => p.trim());
        return paragraphs.map(p => `<p>${p.trim()}</p>`).join('');
    }

    renderTags(tags) {
        const tagsContainer = document.getElementById('articleTags');
        const tagsList = tagsContainer.querySelector('.tags-list');

        if (tags.length === 0) {
            tagsContainer.style.display = 'none';
            return;
        }

        tagsList.innerHTML = tags.map(tag => `
            <a href="/pages/news.html?tag=${encodeURIComponent(tag)}" class="tag">
                ${tag}
            </a>
        `).join('');
    }

    loadRelatedArticles() {
        if (!this.currentArticle) return;

        // Find related articles based on category and tags
        const related = this.allNews
            .filter(article => article.id !== this.currentArticle.id)
            .filter(article => article.status === 'published' || !article.status)
            .map(article => ({
                ...article,
                relevanceScore: this.calculateRelevance(article, this.currentArticle)
            }))
            .sort((a, b) => b.relevanceScore - a.relevanceScore)
            .slice(0, 3);

        this.renderRelatedArticles(related);
    }

    calculateRelevance(article, currentArticle) {
        let score = 0;

        // Same category gets high score
        if (article.category === currentArticle.category) {
            score += 10;
        }

        // Shared tags get medium score
        const currentTags = currentArticle.tags || [];
        const articleTags = article.tags || [];
        const sharedTags = currentTags.filter(tag => articleTags.includes(tag));
        score += sharedTags.length * 5;

        // Featured articles get bonus
        if (article.featured) {
            score += 3;
        }

        // Recent articles get slight bonus
        const articleDate = new Date(article.date || article.publishedAt);
        const daysDiff = (Date.now() - articleDate) / (1000 * 60 * 60 * 24);
        if (daysDiff < 7) score += 2;
        else if (daysDiff < 30) score += 1;

        return score;
    }

    renderRelatedArticles(articles) {
        const relatedGrid = document.getElementById('relatedGrid');

        if (articles.length === 0) {
            document.getElementById('relatedArticles').style.display = 'none';
            return;
        }

        relatedGrid.innerHTML = articles.map(article => `
            <a href="/pages/news-detail.html?id=${article.id}" class="related-article">
                <img 
                    class="related-article-image" 
                    src="${article.image || article.images?.[0] || 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400&h=200&fit=crop'}" 
                    alt="${article.title}"
                    onerror="this.src='https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400&h=200&fit=crop'"
                />
                <div class="related-article-content">
                    <div class="related-article-meta">
                        <span class="related-article-category">${article.category || 'Sports'}</span>
                        <span>${this.formatDate(article.date || article.publishedAt)}</span>
                    </div>
                    <h3 class="related-article-title">${article.title}</h3>
                    <p class="related-article-summary">${article.summary}</p>
                </div>
            </a>
        `).join('');
    }

    updateMetaTags() {
        if (!this.currentArticle) return;

        const article = this.currentArticle;

        // Update Open Graph tags
        this.updateMetaTag('property', 'og:title', article.title);
        this.updateMetaTag('property', 'og:description', article.summary);
        this.updateMetaTag('property', 'og:image', article.image || article.images?.[0]);
        this.updateMetaTag('property', 'og:url', window.location.href);

        // Update Twitter tags
        this.updateMetaTag('name', 'twitter:title', article.title);
        this.updateMetaTag('name', 'twitter:description', article.summary);
        this.updateMetaTag('name', 'twitter:image', article.image || article.images?.[0]);

        // Update meta description
        this.updateMetaTag('name', 'description', article.summary);
    }

    updateMetaTag(attribute, value, content) {
        if (!content) return;

        let tag = document.querySelector(`meta[${attribute}="${value}"]`);
        if (!tag) {
            tag = document.createElement('meta');
            tag.setAttribute(attribute, value);
            document.head.appendChild(tag);
        }
        tag.setAttribute('content', content);
    }

    incrementViews() {
        if (!this.currentArticle) return;

        // Increment view count (in a real app, this would be sent to a server)
        const viewsKey = `news_views_${this.currentArticle.id}`;
        const currentViews = parseInt(localStorage.getItem(viewsKey) || '0');
        const newViews = currentViews + 1;
        localStorage.setItem(viewsKey, newViews.toString());

        // Update the current article views for display
        this.currentArticle.views = (this.currentArticle.views || 0) + 1;
    }

    setupEventListeners() {
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });

        // Smooth scrolling for internal links
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="#"]')) {
                e.preventDefault();
                const targetId = e.target.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    showError(message) {
        const articleContainer = document.getElementById('articleContainer');
        articleContainer.innerHTML = `
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="error-title">Article Not Found</h1>
                <p class="error-message">${message}</p>
                <div class="error-actions">
                    <a href="/pages/news.html" class="error-btn primary">
                        <i class="fas fa-newspaper"></i>
                        Browse All News
                    </a>
                    <a href="/" class="error-btn secondary">
                        <i class="fas fa-home"></i>
                        Go Home
                    </a>
                </div>
            </div>
        `;
        this.hideLoading();
        
        // Hide related articles section
        document.getElementById('relatedArticles').style.display = 'none';
    }

    hideLoading() {
        const loading = document.getElementById('loadingOverlay');
        loading.classList.add('hidden');
        setTimeout(() => {
            loading.style.display = 'none';
        }, 300);
    }

    formatDate(dateString) {
        if (!dateString) return 'Unknown date';
        
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (error) {
            return dateString;
        }
    }

    truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
}

// Utility Functions
function shareArticle(platform) {
    const article = window.newsDetail?.currentArticle;
    if (!article) return;

    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(article.title);
    const summary = encodeURIComponent(article.summary);

    let shareUrl = '';

    switch (platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            break;
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            break;
        default:
            return;
    }

    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function copyArticleLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        showToast('Link copied to clipboard!');
    }).catch(() => {
        // Fallback for browsers that don't support clipboard API
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Link copied to clipboard!');
    });
}

function subscribeNewsletter(event) {
    event.preventDefault();
    const emailInput = event.target.querySelector('input[type="email"]');
    const email = emailInput.value;

    if (email) {
        // In a real app, this would send the email to a server
        showToast('Thank you for subscribing to our newsletter!');
        emailInput.value = '';
    }
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function showToast(message, duration = 3000) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    if (!toast || !toastMessage) return;

    toastMessage.textContent = message;
    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, duration);
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.newsDetail = new NewsDetailPage();
});

// Handle browser back/forward navigation
window.addEventListener('popstate', () => {
    location.reload();
});

// Error handling for images
document.addEventListener('error', (e) => {
    if (e.target.tagName === 'IMG') {
        e.target.src = 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800&h=400&fit=crop';
    }
}, true);