// News Index JavaScript - Complete with Real-time Search and Filters
document.addEventListener('DOMContentLoaded', function() {
    initializeNewsIndex();
    initializeClickableCards();
    initializeRealTimeSearch();
});

let currentPage = 1;
let currentCategory = 'all';
let isLoading = false;
let hasMoreNews = true;
let searchTimeout = null;

function initializeNewsIndex() {
    // Get current category from URL
    const urlParams = new URLSearchParams(window.location.search);
    currentCategory = urlParams.get('category') || 'all';
    
    // Update active category button
    updateActiveCategoryButton();
    
    // Initialize infinite scroll
    initializeInfiniteScroll();
    
    // Add loading states to buttons
    addLoadingStates();
}

function updateActiveCategoryButton() {
    document.querySelectorAll('.category-btn').forEach(btn => {
        const btnCategory = btn.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
        if (btnCategory === currentCategory) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Make entire news cards clickable
function initializeClickableCards() {
    const clickableCards = document.querySelectorAll('.clickable-card');
    
    clickableCards.forEach(card => {
        card.style.cursor = 'pointer';
        
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on a link
            if (e.target.tagName === 'A' || e.target.closest('a')) {
                return;
            }
            
            const href = this.getAttribute('data-href');
            if (href) {
                if (e.ctrlKey || e.metaKey || e.button === 1) {
                    window.open(href, '_blank');
                } else {
                    window.location.href = href;
                }
            }
        });
        
        card.addEventListener('mousedown', function(e) {
            if (e.button === 1) {
                e.preventDefault();
                const href = this.getAttribute('data-href');
                if (href) {
                    window.open(href, '_blank');
                }
            }
        });
        
        // Keyboard accessibility
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'article');
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const href = this.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            }
        });
    });
}

// Real-time search implementation
function initializeRealTimeSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchForm = document.querySelector('.search-form');
    
    if (!searchInput) return;
    
    // Prevent form submission for real-time search
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query) {
            performSearch(query);
        }
    });
    
    // Real-time search as user types
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length === 0) {
            // If search cleared, reload current category
            searchTimeout = setTimeout(() => {
                filterByCategory(currentCategory, true);
            }, 300);
        } else if (query.length >= 2) {
            // Search after user stops typing (debounce)
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 500);
        }
    });
    
    // Show search indicator
    searchInput.addEventListener('keydown', function() {
        const searchBtn = document.querySelector('.search-btn');
        if (searchBtn) {
            searchBtn.textContent = 'Searching...';
            searchBtn.style.opacity = '0.7';
        }
    });
}

function performSearch(query) {
    if (isLoading) return;
    
    isLoading = true;
    const searchBtn = document.querySelector('.search-btn');
    
    if (searchBtn) {
        searchBtn.textContent = 'Searching...';
        searchBtn.disabled = true;
    }
    
    // Show loading state
    const newsGrid = document.getElementById('news-grid');
    if (newsGrid) {
        newsGrid.style.opacity = '0.5';
    }
    
    // Make AJAX request to live search endpoint
    fetch(`${window.BASE_URL||""}/api/news/live-search?q=${encodeURIComponent(query)}&category=${currentCategory}&limit=20`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNewsGrid(data.news);
            
            // Show result count
            showSearchResults(data.count, query);
        } else {
            showErrorMessage('Search failed. Please try again.');
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        showErrorMessage('Search failed. Please try again.');
    })
    .finally(() => {
        isLoading = false;
        if (searchBtn) {
            searchBtn.textContent = 'Search';
            searchBtn.disabled = false;
        }
        if (newsGrid) {
            newsGrid.style.opacity = '1';
        }
    });
}

function showSearchResults(count, query) {
    // Remove existing result message
    const existingMsg = document.querySelector('.search-result-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    // Create result message
    const resultMsg = document.createElement('div');
    resultMsg.className = 'search-result-message';
    resultMsg.style.cssText = `
        text-align: center;
        padding: 1rem;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
        border-radius: 0.75rem;
        color: #1e40af;
        font-weight: 600;
    `;
    resultMsg.textContent = `Found ${count} article${count !== 1 ? 's' : ''} for "${query}"`;
    
    const newsContainer = document.getElementById('news-container');
    if (newsContainer) {
        newsContainer.insertBefore(resultMsg, newsContainer.firstChild);
    }
}

function updateNewsGrid(newsArray) {
    const newsGrid = document.getElementById('news-grid');
    if (!newsGrid) return;
    
    // Clear existing news
    newsGrid.innerHTML = '';
    
    if (newsArray.length === 0) {
        newsGrid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem 2rem;">
                <h3 style="color: #374151; font-size: 1.5rem; margin-bottom: 1rem;">No articles found</h3>
                <p style="color: #6b7280;">Try adjusting your search or filter criteria</p>
            </div>
        `;
        return;
    }
    
    // Add new news cards
    newsArray.forEach((article, index) => {
        const card = createNewsCard(article);
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        newsGrid.appendChild(card);
        
        // Animate in
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
    // Re-initialize clickable cards
    initializeClickableCards();
}

function filterByCategory(category, skipReload = false) {
    if (isLoading) return;
    
    // Update current category
    currentCategory = category;
    
    // Update active button
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
        const btnCategory = btn.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
        if (btnCategory === category) {
            btn.classList.add('active');
        }
    });
    
    // Clear search input
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Remove search result message
    const searchMsg = document.querySelector('.search-result-message');
    if (searchMsg) {
        searchMsg.remove();
    }
    
    if (skipReload) {
        // Just update URL without reload (for clearing search)
        const url = category === 'all' ? '/news' : `/news?category=${encodeURIComponent(category)}`;
        window.history.pushState({}, '', url);
        return;
    }
    
    // Show loading state
    const newsGrid = document.getElementById('news-grid');
    if (newsGrid) {
        newsGrid.style.opacity = '0.5';
    }
    
    // Navigate to filtered page
    const url = category === 'all' ? '/news' : `/news?category=${encodeURIComponent(category)}`;
    window.location.href = url;
}

function createNewsCard(article) {
    const card = document.createElement('article');
    card.className = 'news-card clickable-card';
    card.setAttribute('data-href', article.url);
    
    const excerpt = article.excerpt || '';
    const truncatedExcerpt = excerpt.length > 120 ? excerpt.substring(0, 120) + '...' : excerpt;
    
    card.innerHTML = `
        <img src="${escapeHtml(article.featured_image)}" 
             alt="${escapeHtml(article.title)}" 
             class="news-image"
             loading="lazy">
        <div class="news-content">
            <span class="news-category">${escapeHtml(article.category)}</span>
            <h3>
                <a href="${escapeHtml(article.url)}" class="news-title">
                    ${escapeHtml(article.title)}
                </a>
            </h3>
            ${truncatedExcerpt ? `<p class="news-excerpt">${escapeHtml(truncatedExcerpt)}</p>` : ''}
            <div class="news-meta">
                <span><i class="fa-solid fa-calendar"></i> ${escapeHtml(article.formatted_date)}</span>
                <span><i class="fa-solid fa-eye"></i> ${Number(article.views || 0).toLocaleString()} views</span>
            </div>
        </div>
    `;
    
    return card;
}

function loadMoreNews() {
    if (isLoading || !hasMoreNews) return;
    
    isLoading = true;
    const loadMoreBtn = document.getElementById('load-more-btn');
    const originalText = loadMoreBtn.textContent;
    loadMoreBtn.textContent = 'Loading...';
    loadMoreBtn.disabled = true;
    
    const requestData = {
        page: currentPage + 1,
        category: currentCategory
    };
    
    const queryString = new URLSearchParams(requestData).toString();
    
    fetch(`${window.BASE_URL||""}/news/load-more?${queryString}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.news && data.news.length > 0) {
            appendNewsCards(data.news);
            currentPage++;
            hasMoreNews = data.hasMore;
            
            initializeClickableCards();
            
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

function initializeInfiniteScroll() {
    const newsGrid = document.getElementById('news-grid');
    const loadMoreBtn = document.getElementById('load-more-btn');
    
    if (newsGrid && loadMoreBtn && newsGrid.children.length >= 9) {
        loadMoreBtn.style.display = 'block';
    }
}

function addLoadingStates() {
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('active')) {
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
            }
        });
    });
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.style.cssText = `
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        margin: 1rem 0;
        text-align: center;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        font-weight: 600;
    `;
    errorDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(errorDiv, container.firstChild);
        
        setTimeout(() => {
            errorDiv.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => errorDiv.remove(), 300);
        }, 5000);
    }
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === '/' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    if (e.key === 'Escape') {
        const searchInput = document.querySelector('.search-input');
        if (searchInput && document.activeElement === searchInput) {
            searchInput.blur();
        }
    }
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
`;
document.head.appendChild(style);

console.log('News index initialized with real-time search and filters');