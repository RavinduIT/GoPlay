// News Carousel Component
class NewsCarousel {
    constructor() {
        this.carousel = document.getElementById('newsCarousel');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.currentIndex = 0;
        this.newsData = [];
        this.init();
    }

    async init() {
        await this.loadNewsData();
        this.renderNews();
        this.setupControls();
        this.setupAutoScroll();
    }

    async loadNewsData() {
        // Sample news data - in a real app, this would come from an API or data file
        this.newsData = [
            {
                id: 1,
                title: "Local Football Championship Finals This Weekend",
                excerpt: "The most anticipated match of the season brings together two rival teams...",
                image: "assets/images/news-1.jpg",
                date: "2024-03-15",
                author: "Sports Desk"
            },
            {
                id: 2,
                title: "New Tennis Academy Opens in Downtown",
                excerpt: "State-of-the-art facilities and professional coaching now available...",
                image: "assets/images/news-2.jpg",
                date: "2024-03-14",
                author: "Sarah Johnson"
            },
            {
                id: 3,
                title: "Basketball League Registration Now Open",
                excerpt: "Join the summer basketball league with teams for all skill levels...",
                image: "assets/images/news-3.jpg",
                date: "2024-03-13",
                author: "Mike Chen"
            },
            {
                id: 4,
                title: "Swimming Pool Renovation Complete",
                excerpt: "The community pool reopens with upgraded facilities and new programs...",
                image: "assets/images/news-4.jpg",
                date: "2024-03-12",
                author: "Lisa Wang"
            },
            {
                id: 5,
                title: "Youth Soccer Camp Registration Open",
                excerpt: "Professional coaches will train young athletes this summer...",
                image: "assets/images/news-5.jpg",
                date: "2024-03-11",
                author: "David Rodriguez"
            },
            {
                id: 6,
                title: "Marathon Training Program Starts Next Month",
                excerpt: "Prepare for the city marathon with our comprehensive training program...",
                image: "assets/images/news-6.jpg",
                date: "2024-03-10",
                author: "Emma Thompson"
            }
        ];

        // Try to load from data file if it exists
        try {
            const response = await fetch('data/data.json');
            if (response.ok) {
                const data = await response.json();
                if (data.news && data.news.length > 0) {
                    this.newsData = data.news;
                }
            }
        } catch (error) {
            console.log('Using default news data');
        }
    }

    renderNews() {
        if (!this.carousel) return;

        this.carousel.innerHTML = '';
        
        this.newsData.forEach((news, index) => {
            const newsItem = this.createNewsItem(news, index);
            this.carousel.appendChild(newsItem);
        });
    }

    createNewsItem(news, index) {
        const newsItem = document.createElement('div');
        newsItem.className = 'news-item';
        newsItem.dataset.index = index;

        const formattedDate = this.formatDate(news.date);

        newsItem.innerHTML = `
            <div class="news-image">
                <img src="${news.image}" alt="${news.title}" onerror="this.src='assets/images/placeholder.jpg'">
            </div>
            <div class="news-content">
                <h3 class="news-title">${news.title}</h3>
                <p class="news-excerpt">${news.excerpt}</p>
                <div class="news-meta">
                    <span class="news-date">${formattedDate}</span>
                    <span class="news-author">By ${news.author}</span>
                </div>
            </div>
        `;

        // Add click event to navigate to news details
        newsItem.addEventListener('click', () => {
            this.openNewsDetail(news.id);
        });

        return newsItem;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }

    setupControls() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                this.scrollPrev();
            });
        }

        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                this.scrollNext();
            });
        }

        // Add touch/swipe support for mobile
        this.setupTouchControls();
        
        // Add keyboard navigation
        this.setupKeyboardControls();
    }

    setupTouchControls() {
        if (!this.carousel) return;

        let startX = 0;
        let scrollLeft = 0;
        let isDown = false;

        this.carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - this.carousel.offsetLeft;
            scrollLeft = this.carousel.scrollLeft;
            this.carousel.style.cursor = 'grabbing';
        });

        this.carousel.addEventListener('mouseleave', () => {
            isDown = false;
            this.carousel.style.cursor = 'grab';
        });

        this.carousel.addEventListener('mouseup', () => {
            isDown = false;
            this.carousel.style.cursor = 'grab';
        });

        this.carousel.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - this.carousel.offsetLeft;
            const walk = (x - startX) * 2;
            this.carousel.scrollLeft = scrollLeft - walk;
        });

        // Touch events for mobile
        this.carousel.addEventListener('touchstart', (e) => {
            startX = e.touches[0].pageX - this.carousel.offsetLeft;
            scrollLeft = this.carousel.scrollLeft;
        });

        this.carousel.addEventListener('touchmove', (e) => {
            const x = e.touches[0].pageX - this.carousel.offsetLeft;
            const walk = (x - startX) * 2;
            this.carousel.scrollLeft = scrollLeft - walk;
        });
    }

    setupKeyboardControls() {
        document.addEventListener('keydown', (e) => {
            if (this.isCarouselInView()) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    this.scrollPrev();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    this.scrollNext();
                }
            }
        });
    }

    isCarouselInView() {
        if (!this.carousel) return false;
        
        const rect = this.carousel.getBoundingClientRect();
        return rect.top < window.innerHeight && rect.bottom > 0;
    }

    scrollPrev() {
        if (!this.carousel) return;
        
        const itemWidth = this.carousel.querySelector('.news-item').offsetWidth + 24; // 24px gap
        this.carousel.scrollBy({
            left: -itemWidth,
            behavior: 'smooth'
        });
    }

    scrollNext() {
        if (!this.carousel) return;
        
        const itemWidth = this.carousel.querySelector('.news-item').offsetWidth + 24; // 24px gap
        this.carousel.scrollBy({
            left: itemWidth,
            behavior: 'smooth'
        });
    }

    setupAutoScroll() {
        // Auto-scroll every 5 seconds
        setInterval(() => {
            if (this.isCarouselInView()) {
                this.scrollNext();
                
                // Reset to beginning if at end
                setTimeout(() => {
                    if (this.carousel.scrollLeft >= this.carousel.scrollWidth - this.carousel.clientWidth) {
                        this.carousel.scrollTo({
                            left: 0,
                            behavior: 'smooth'
                        });
                    }
                }, 500);
            }
        }, 5000);
    }

    openNewsDetail(newsId) {
        // Navigate to news detail page
        window.location.href = `pages/news-details.html?id=${newsId}`;
    }

    // Public method to add news item
    addNewsItem(newsItem) {
        this.newsData.unshift(newsItem);
        this.renderNews();
    }

    // Public method to refresh news
    async refreshNews() {
        await this.loadNewsData();
        this.renderNews();
    }
}

// Initialize news carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('newsCarousel')) {
        window.newsCarousel = new NewsCarousel();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NewsCarousel;
}