// Enhanced News Carousel Component for news.json
class NewsCarousel {
    constructor() {
        this.newsContainer = document.getElementById('newsCarousel');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.currentIndex = 0;
        this.newsItems = [];
        this.itemsPerPage = this.getItemsPerPage();
        this.autoSlideInterval = null;
        this.autoSlideDelay = 5000; // 5 seconds
        
        this.init();
    }

    async init() {
        try {
            await this.loadNewsData();
            this.render();
            this.setupEventListeners();
            this.startAutoSlide();
        } catch (error) {
            console.error('Error initializing news carousel:', error);
            this.showError();
        }
    }

    async loadNewsData() {
        try {
            // Use the enhanced DataUtils to load news
            if (window.dataUtils) {
                const latestNews = await window.dataUtils.getLatestNews(6);
                this.newsItems = latestNews;
                return;
            }

            // Fallback: directly fetch news.json
            const response = await fetch('/data/news.json');
            if (response.ok) {
                const data = await response.json();
                this.newsItems = (data.news || [])
                    .filter(news => news.status === 'published')
                    .sort((a, b) => new Date(b.publishedAt || b.date) - new Date(a.publishedAt || a.date))
                    .slice(0, 6);
                return;
            }
        } catch (error) {
            console.warn('Could not load news data:', error);
        }

        // Final fallback to empty array
        this.newsItems = [];
    }

    render() {
        if (!this.newsContainer) return;

        if (this.newsItems.length === 0) {
            this.newsContainer.innerHTML = `
                <div class="news-empty">
                    <i class="fas fa-newspaper"></i>
                    <p>No news available at the moment</p>
                </div>
            `;
            return;
        }

        this.newsContainer.innerHTML = this.newsItems.map((news, index) => `
            <div class="news-item" data-index="${index}" data-news-id="${news.id}">
                <div class="news-image">
                    <img 
                        src="${this.getNewsImage(news)}" 
                        alt="${news.title}"
                        onerror="this.src='https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400&h=300&fit=crop'"
                    />
                    <div class="news-overlay"></div>
                    <div class="news-category">
                        <i class="fas fa-tag"></i>
                        ${news.category || 'Sports'}
                    </div>
                    ${news.featured ? '<div class="news-featured-badge"><i class="fas fa-star"></i> Featured</div>' : ''}
                </div>
                <div class="news-content">
                    <div class="news-meta">
                        <span class="news-date">
                            <i class="fas fa-calendar"></i>
                            ${this.formatDate(news.publishedAt || news.date)}
                        </span>
                        <span class="news-read-time">
                            <i class="fas fa-clock"></i>
                            ${news.readTime || '5 min read'}
                        </span>
                    </div>
                    <h3 class="news-title">${news.title}</h3>
                    <p class="news-summary">${this.truncateText(news.summary, 100)}</p>
                    <div class="news-actions">
                        <a href="pages/news-details.html?id=${news.id}" class="news-read-more">
                            Read More
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <div class="news-stats">
                            <span class="news-views">
                                <i class="fas fa-eye"></i>
                                ${this.formatViews(news.views || 0)}
                            </span>
                            ${news.author ? `
                                <span class="news-author">
                                    <i class="fas fa-user"></i>
                                    ${news.author}
                                </span>
                            ` : ''}
                        </div>
                    </div>
                    ${news.tags && news.tags.length > 0 ? `
                        <div class="news-tags">
                            ${news.tags.slice(0, 2).map(tag => `<span class="news-tag">${tag}</span>`).join('')}
                        </div>
                    ` : ''}
                </div>
            </div>
        `).join('');

        this.updateCarouselPosition();
    }

    getNewsImage(news) {
        // Priority order: main image, first from images array, fallback
        if (news.image) return news.image;
        if (news.images && news.images.length > 0) return news.images[0];
        
        // Category-based fallback images
        const categoryImages = {
            'Football': 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?w=400&h=300&fit=crop',
            'Tennis': 'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=400&h=300&fit=crop',
            'Basketball': 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400&h=300&fit=crop',
            'Olympics': 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=300&fit=crop',
            'Technology': 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=400&h=300&fit=crop'
        };
        
        return categoryImages[news.category] || 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400&h=300&fit=crop';
    }

    setupEventListeners() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                this.stopAutoSlide();
                this.prev();
                this.startAutoSlide();
            });
        }

        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                this.stopAutoSlide();
                this.next();
                this.startAutoSlide();
            });
        }

        // Pause auto-slide on hover
        if (this.newsContainer) {
            this.newsContainer.addEventListener('mouseenter', () => {
                this.stopAutoSlide();
            });

            this.newsContainer.addEventListener('mouseleave', () => {
                this.startAutoSlide();
            });
        }

        // Handle window resize
        window.addEventListener('resize', this.debounce(() => {
            this.itemsPerPage = this.getItemsPerPage();
            this.updateCarouselPosition();
        }, 250));

        // Handle click events on news items
        if (this.newsContainer) {
            this.newsContainer.addEventListener('click', (e) => {
                const newsItem = e.target.closest('.news-item');
                if (newsItem && !e.target.closest('.news-read-more')) {
                    const newsId = newsItem.dataset.newsId;
                    if (newsId) {
                        window.location.href = `pages/news-details.html?id=${newsId}`;
                    }
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (document.activeElement && document.activeElement.closest('.news-carousel')) {
                switch (e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.prev();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        this.next();
                        break;
                }
            }
        });
    }

    next() {
        const maxIndex = Math.max(0, this.newsItems.length - this.itemsPerPage);
        this.currentIndex = Math.min(this.currentIndex + 1, maxIndex);
        this.updateCarouselPosition();
    }

    prev() {
        this.currentIndex = Math.max(0, this.currentIndex - 1);
        this.updateCarouselPosition();
    }

    updateCarouselPosition() {
        if (!this.newsContainer) return;

        const items = this.newsContainer.querySelectorAll('.news-item');
        if (items.length === 0) return;

        const itemWidth = items[0].offsetWidth + 32; // Including gap
        const translateX = -this.currentIndex * itemWidth;
        
        this.newsContainer.style.transform = `translateX(${translateX}px)`;
        this.updateNavigationButtons();
        this.updateProgressIndicator();
    }

    updateNavigationButtons() {
        const maxIndex = Math.max(0, this.newsItems.length - this.itemsPerPage);
        
        if (this.prevBtn) {
            this.prevBtn.disabled = this.currentIndex === 0;
            this.prevBtn.classList.toggle('disabled', this.currentIndex === 0);
        }

        if (this.nextBtn) {
            this.nextBtn.disabled = this.currentIndex >= maxIndex;
            this.nextBtn.classList.toggle('disabled', this.currentIndex >= maxIndex);
        }
    }

    updateProgressIndicator() {
        // Add progress dots if container exists
        let progressContainer = document.querySelector('.carousel-progress');
        if (!progressContainer) return;

        const totalSlides = Math.ceil(this.newsItems.length / this.itemsPerPage);
        const currentSlide = this.currentIndex;

        progressContainer.innerHTML = '';
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('button');
            dot.className = `progress-dot ${i === currentSlide ? 'active' : ''}`;
            dot.addEventListener('click', () => {
                this.currentIndex = i;
                this.updateCarouselPosition();
            });
            progressContainer.appendChild(dot);
        }
    }

    getItemsPerPage() {
        const width = window.innerWidth;
        if (width >= 1200) return 3;
        if (width >= 768) return 2;
        return 1;
    }

    startAutoSlide() {
        this.stopAutoSlide();
        if (this.newsItems.length <= this.itemsPerPage) return; // Don't auto-slide if all items fit

        this.autoSlideInterval = setInterval(() => {
            const maxIndex = Math.max(0, this.newsItems.length - this.itemsPerPage);
            if (this.currentIndex >= maxIndex) {
                this.currentIndex = 0;
            } else {
                this.currentIndex++;
            }
            this.updateCarouselPosition();
        }, this.autoSlideDelay);
    }

    stopAutoSlide() {
        if (this.autoSlideInterval) {
            clearInterval(this.autoSlideInterval);
            this.autoSlideInterval = null;
        }
    }

    showError() {
        if (!this.newsContainer) return;
        
        this.newsContainer.innerHTML = `
            <div class="news-error">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Unable to Load News</h3>
                <p>We're having trouble loading the latest news at the moment.</p>
                <button onclick="window.newsCarousel?.refresh()" class="retry-btn">
                    <i class="fas fa-redo"></i>
                    Try Again
                </button>
            </div>
        `;
    }

    // Utility methods
    formatDate(dateString) {
        if (!dateString) return 'Unknown date';
        
        try {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 1) return 'Yesterday';
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
            
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        } catch (error) {
            console.warn('Error formatting date:', error);
            return dateString;
        }
    }

    formatViews(views) {
        if (views >= 1000000) return `${(views / 1000000).toFixed(1)}M`;
        if (views >= 1000) return `${(views / 1000).toFixed(1)}K`;
        return views.toString();
    }

    truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength).trim() + '...';
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Public methods for external control
    goToSlide(index) {
        const maxIndex = Math.max(0, this.newsItems.length - this.itemsPerPage);
        this.currentIndex = Math.max(0, Math.min(index, maxIndex));
        this.updateCarouselPosition();
    }

    async refresh() {
        try {
            await this.loadNewsData();
            this.render();
            this.currentIndex = 0;
            this.updateCarouselPosition();
            this.startAutoSlide();
        } catch (error) {
            console.error('Error refreshing news carousel:', error);
            this.showError();
        }
    }

    destroy() {
        this.stopAutoSlide();
        if (this.prevBtn) this.prevBtn.removeEventListener('click', this.prev);
        if (this.nextBtn) this.nextBtn.removeEventListener('click', this.next);
        window.removeEventListener('resize', this.updateCarouselPosition);
    }

    // Analytics method (for tracking user interactions)
    trackNewsClick(newsId, newsTitle) {
        // In a real application, you would send this data to your analytics service
        console.log('News clicked:', { id: newsId, title: newsTitle, timestamp: new Date() });
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('newsCarousel')) {
        window.newsCarousel = new NewsCarousel();
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NewsCarousel;
}