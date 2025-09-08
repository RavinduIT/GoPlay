// News Index JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeNewsIndex();
});

let currentPage = 1;
let currentCategory = 'all';
let isLoading = false;
let hasMoreNews = true;

function initializeNewsIndex() {
    // Get current category from URL or button state
    const activeCategoryBtn = document.querySelector('.category-btn.active');
    if (activeCategoryBtn) {
        currentCategory = activeCategoryBtn.onclick.toString().match(/'([^']+)'/)[1];
    }
    
    // Initialize infinite scroll
    initializeInfiniteScroll();
    
    // Initialize search functionality
    initializeSearch();
    
    // Add loading states to buttons
    addLoadingStates();
}

function filterByCategory(category) {
    if (isLoading) return;
    
    // Update active button
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    currentCategory = category;
    currentPage = 1;
    hasMoreNews = true;
    
    // Show loading state
    showLoadingState();
    
    // Load news for selected category
    loadCategoryNews(category);
}

function loadCategoryNews(category) {
    const url = category === 'all' ? '/news' : `/news?category=${category}`;
    
    // Redirect to filtered page
    window.location.href = url;
}

function loadMoreNews() {
    if (isLoading || !hasMoreNews) return;
    
    isLoading = true;
    const loadMoreBtn = document.getElementById('load-more-btn');
    const originalText = loadMoreBtn.textContent;
    loadMoreBtn.textContent = 'Loading...';
    loadMoreBtn.disabled = true;
    
    // Prepare request data
    const requestData = {
        page: currentPage + 1,
        category: currentCategory
    };
    
    // Build query string
    const queryString = new URLSearchParams(requestData).toString();
    
    // Make AJAX request
    fetch(`/news/load-more?${queryString}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.news && data.news.length > 0) {
            // Add new news cards to the grid
            appendNewsCards(data.news);
            currentPage++;
            hasMoreNews = data.hasMore;
            
            if (!hasMoreNews) {
                loadMoreBtn.style.display = 'none';
            }
        } else {
            hasMoreNews = false;
            loadMoreBtn.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error loading more news:', error);
        showErrorMessage('Failed to load more news. Please try again.');
    })
    .finally(() => {
        isLoading = false;
        loadMoreBtn.textContent = originalText;
        loadMoreBtn.disabled = false;
    });
}

function appendNewsCards(newsArray) {
    const newsGrid = document.getElementById('news-grid');
    
    newsArray.forEach(article => {
        const newsCard = createNewsCard(article);
        newsGrid.appendChild(newsCard);
    });
    
    // Add animation to new cards
    const newCards = newsGrid.querySelectorAll('.news-card:nth-last-child(-n+' + newsArray.length + ')');
    newCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s, transform 0.5s';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function createNewsCard(article) {
    const card = document.createElement('div');
    card.className = 'news-card';
    
    card.innerHTML = `
        <img src="${article.featured_image || '/public/assets/images/default-news.jpg'}" 
             alt="${escapeHtml(article.title)}" 
             class="news-image">
        <div class="news-content">
            <span class="news-category">${escapeHtml(article.category)}</span>
            <h3>
                <a href="/news/${article.slug}" class="news-title">
                    ${escapeHtml(article.title)}
                </a>
            </h3>
            <p class="news-excerpt">${escapeHtml(article.excerpt || '')}</p>
            <div class="news-meta">
                <span>${formatDate(article.published_at)}</span>
                <span>${article.views || 0} views</span>
            </div>
        </div>
    `;
    
    return card;
}

function initializeInfiniteScroll() {
    // Check if we should show load more button
    const newsGrid = document.getElementById('news-grid');
    const loadMoreBtn = document.getElementById('load-more-btn');
    
    if (newsGrid && newsGrid.children.length >= 9) {
        loadMoreBtn.style.display = 'block';
    }
    
    // Optional: Auto-load on scroll (uncomment if preferred)
    /*
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) clearTimeout(scrollTimeout);
        
        scrollTimeout = setTimeout(() => {
            if (!isLoading && hasMoreNews) {
                const scrollPosition = window.innerHeight + window.scrollY;
                const documentHeight = document.documentElement.offsetHeight;
                
                if (scrollPosition >= documentHeight - 1000) {
                    loadMoreNews();
                }
            }
        }, 150);
    });
    */
}

function initializeSearch() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = document.querySelector('.search-input');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput.value.trim();
            if (!query) {
                e.preventDefault();
                searchInput.focus();
                return;
            }
        });
    }
    
    // Add search suggestions (optional enhancement)
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 3) {
                searchTimeout = setTimeout(() => {
                    // Could implement search suggestions here
                    console.log('Could show search suggestions for:', query);
                }, 300);
            }
        });
    }
}

function addLoadingStates() {
    // Add loading state for category buttons
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('active')) {
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
            }
        });
    });
}

function showLoadingState() {
    const newsContainer = document.getElementById('news-container');
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading-state';
    loadingDiv.style.cssText = `
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
        font-size: 1.1em;
    `;
    loadingDiv.innerHTML = `
        <div style="display: inline-block; margin-bottom: 10px;">
            <div style="width: 40px; height: 40px; border: 4px solid #ecf0f1; border-left: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        </div>
        <div>Loading news...</div>
        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    `;
    
    // Insert loading state before news grid
    const newsGrid = document.getElementById('news-grid');
    if (newsGrid) {
        newsContainer.insertBefore(loadingDiv, newsGrid);
    }
}

function hideLoadingState() {
    const loadingState = document.getElementById('loading-state');
    if (loadingState) {
        loadingState.remove();
    }
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.style.cssText = `
        background: #e74c3c;
        color: white;
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
        text-align: center;
        animation: slideIn 0.3s ease;
    `;
    errorDiv.textContent = message;
    
    const container = document.querySelector('.container');
    container.insertBefore(errorDiv, container.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        errorDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => errorDiv.remove(), 300);
    }, 5000);
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === '/' && e.target.tagName !== 'INPUT') {
        e.preventDefault();
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.focus();
        }
    }
});

// Performance monitoring
function trackPerformance() {
    if ('performance' in window) {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                    console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
                }
            }, 0);
        });
    }
}

trackPerformance();