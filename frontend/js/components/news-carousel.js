// Enhanced News Carousel Component
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
            // Try loading from news.json first
            const response = await fetch('/data/news.json');
            if (response.ok) {
                const data = await response.json();
                this.newsItems = (data.news || [])
                    .filter(news => news.status === 'published' || !news.status)
                    .sort((a, b) => new Date(b.date || b.publishedAt) - new Date(a.date || a.publishedAt))
                    .slice(0, 6); // Get latest 6 news items
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
                this.newsItems = (data.newsItems || [])
                    .sort((a, b) => new Date(b.date) - new Date(a.date))
                    .slice(0, 6);
                return;
            }
        } catch (error) {
            console.warn('Could not load data.json:', error);
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
            <div class="news-item" data-index="${index}">
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
                </div>
                <div class="news-content">
                    <div class="news-meta">
                        <span class="news-date">
                            <i class="fas fa-calendar"></i>
                            ${this.formatDate(news.date || news.publishedAt)}
                        </span>
                        <span class="news-read-time">
                            <i class="fas fa-clock"></i>
                            ${news.readTime || '5 min read'}
                        </span>
                    </div>
                    <h3 class="news-title">${news.title}</h3>
                    <p class="news-summary">${this.truncateText(news.summary, 100)}</p>
                    <div class="news-actions">
                        <a href="/pages/news-detail.html?id=${news.id}" class="news-read-more">
                            Read More
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <div class="news-stats">
                            <span class="news-views">
                                <i class="fas fa-eye"></i>
                                ${this.formatViews(news.views || 0)}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        this.updateCarouselPosition();
    }

    getNewsImage(news) {
        if (news.image) return news.image;
        if (news.images && news.images.length > 0) return news.images[0];
        return 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400&h=300&fit=crop';
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
                    const index = parseInt(newsItem.dataset.index);
                    const news = this.newsItems[index];
                    if (news) {
                        window.location.href = `/pages/news-detail.html?id=${news.id}`;
                    }
                }
            });
        }
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

    getItemsPerPage() {
        const width = window.innerWidth;
        if (width >= 1200) return 3;
        if (width >= 768) return 2;
        return 1;
    }

    startAutoSlide() {
        this.stopAutoSlide();
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
                <p>Unable to load news at the moment</p>
                <button onclick="location.reload()" class="retry-btn">
                    <i class="fas fa-redo"></i>
                    Retry
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

    refresh() {
        this.init();
    }

    destroy() {
        this.stopAutoSlide();
        if (this.prevBtn) this.prevBtn.removeEventListener('click', this.prev);
        if (this.nextBtn) this.nextBtn.removeEventListener('click', this.next);
        window.removeEventListener('resize', this.updateCarouselPosition);
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