// Enhanced Data utilities for loading JSON data files with news support
class DataUtils {
    constructor() {
        this.cache = new Map();
        this.cacheDuration = 5 * 60 * 1000; // 5 minutes
    }

    // Generic fetch with error handling and caching
    async fetchJSON(url) {
        try {
            // Check cache first
            const cached = this.cache.get(url);
            if (cached && Date.now() - cached.timestamp < this.cacheDuration) {
                console.log(`Using cached data for ${url}`);
                return cached.data;
            }

            console.log(`Fetching data from ${url}`);
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} for ${url}`);
            }
            
            const data = await response.json();
            
            // Cache the data
            this.cache.set(url, {
                data: data,
                timestamp: Date.now()
            });
            
            return data;
        } catch (error) {
            console.error(`Error loading ${url}:`, error);
            throw error;
        }
    }

    // Load coaches data
    async loadCoaches() {
        return await this.fetchJSON('/data/coaches.json');
    }

    // Load grounds data
    async loadGrounds() {
        const data = await this.fetchJSON('/data/grounds.json');
        // Handle both formats: direct array or { grounds: [] }
        return Array.isArray(data) ? data : data.grounds || [];
    }

    // Load products data
    async loadProducts() {
        return await this.fetchJSON('/data/products.json');
    }

    // Load users data
    async loadUsers() {
        return await this.fetchJSON('/data/users.json');
    }

    // Load applications data
    async loadApplications() {
        return await this.fetchJSON('/data/applications.json');
    }

    // Load activity data
    async loadActivity() {
        return await this.fetchJSON('/data/activity.json');
    }

    // Load news data - NEW
    async loadNews() {
        try {
            const data = await this.fetchJSON('/data/news.json');
            // Handle both formats: direct array or { news: [] }
            return Array.isArray(data) ? data : data.news || [];
        } catch (error) {
            console.warn('Could not load news.json, trying fallback to localStorage');
            // Fallback to localStorage for admin-created news
            const localNews = localStorage.getItem('newsData');
            if (localNews) {
                const parsed = JSON.parse(localNews);
                return parsed.news || [];
            }
            return [];
        }
    }

    // Load all data concurrently
    async loadAllData() {
        try {
            console.log('Loading all data...');
            
            const results = await Promise.allSettled([
                this.loadCoaches(),
                this.loadGrounds(), 
                this.loadProducts(),
                this.loadUsers(),
                this.loadApplications(),
                this.loadActivity(),
                this.loadNews() // Added news loading
            ]);

            const [coaches, grounds, products, users, applications, activity, news] = results.map(result => {
                if (result.status === 'fulfilled') {
                    return result.value;
                } else {
                    console.warn('Failed to load data:', result.reason);
                    return [];
                }
            });

            console.log('Data loaded:', {
                coaches: coaches.length,
                grounds: grounds.length,
                products: products.length,
                users: users.length,
                applications: applications.length,
                activity: activity.length,
                news: news.length
            });

            return {
                coaches,
                grounds,
                products,
                users,
                applications,
                activity,
                news
            };
        } catch (error) {
            console.error('Error loading data:', error);
            throw error;
        }
    }

    // Clear cache
    clearCache() {
        this.cache.clear();
        console.log('Data cache cleared');
    }

    // Refresh specific data
    async refreshData(dataType) {
        const url = `/data/${dataType}.json`;
        this.cache.delete(url);
        
        switch(dataType) {
            case 'coaches':
                return await this.loadCoaches();
            case 'grounds':
                return await this.loadGrounds();
            case 'products':
                return await this.loadProducts();
            case 'users':
                return await this.loadUsers();
            case 'applications':
                return await this.loadApplications();
            case 'activity':
                return await this.loadActivity();
            case 'news':
                return await this.loadNews();
            default:
                throw new Error(`Unknown data type: ${dataType}`);
        }
    }
}

// Export for use in other files
window.DataUtils = DataUtils;

// Enhanced Data Manager with news support
class DataManager {
    constructor() {
        this.data = null;
        this.init();
    }

    async init() {
        await this.loadData();
        this.initializeLocalStorage();
    }

    // Load data from JSON file - FIXED PATH
    async loadData() {
        try {
            // Fixed path: from js/ folder, go up one level to reach data/
            const response = await fetch('/data/data.json');
            if (!response.ok) {
                throw new Error(`Failed to load data: ${response.status}`);
            }
            this.data = await response.json();
            return this.data;
        } catch (error) {
            console.warn('Could not load data.json, using fallback data:', error.message);
            // Fallback to empty data structure
            this.data = {
                newsItems: [],
                popularVenues: [],
                featuredCoaches: [],
                featuredProducts: [],
                categories: [],
                sampleUsers: [],
                groundBookings: [],
                coachBookings: []
            };
            return this.data;
        }
    }

    // Ensure data is loaded before accessing
    async ensureDataLoaded() {
        if (!this.data) {
            await this.loadData();
        }
        return this.data;
    }

    // News functions - ENHANCED
    async getNewsItems() {
        try {
            // First try to load from news.json
            const response = await fetch('/data/news.json');
            if (response.ok) {
                const data = await response.json();
                const newsFromFile = Array.isArray(data) ? data : data.news || [];
                
                // Also check localStorage for admin-added news
                const localNews = localStorage.getItem('newsData');
                if (localNews) {
                    const parsed = JSON.parse(localNews);
                    const newsFromStorage = parsed.news || [];
                    
                    // Merge both sources, with localStorage taking priority for duplicates
                    const allNews = [...newsFromStorage];
                    newsFromFile.forEach(fileNews => {
                        if (!allNews.find(localNews => localNews.id === fileNews.id)) {
                            allNews.push(fileNews);
                        }
                    });
                    
                    return allNews.sort((a, b) => new Date(b.date || b.publishedAt) - new Date(a.date || a.publishedAt));
                }
                
                return newsFromFile;
            }
        } catch (error) {
            console.warn('Could not load news.json:', error);
        }

        // Fallback to original data.json or localStorage
        const localNews = localStorage.getItem('newsData');
        if (localNews) {
            const parsed = JSON.parse(localNews);
            return parsed.news || [];
        }

        const data = await this.ensureDataLoaded();
        return data.newsItems || [];
    }

    async getNewsById(id) {
        const newsItems = await this.getNewsItems();
        return newsItems.find(news => news.id.toString() === id.toString());
    }

    async getLatestNews(limit = 6) {
        const newsItems = await this.getNewsItems();
        return newsItems
            .filter(news => news.status === 'published' || !news.status)
            .sort((a, b) => new Date(b.date || b.publishedAt) - new Date(a.date || a.publishedAt))
            .slice(0, limit);
    }

    async getNewsByCategory(category) {
        const newsItems = await this.getNewsItems();
        return newsItems.filter(news => 
            news.category && news.category.toLowerCase() === category.toLowerCase()
        );
    }

    async getFeaturedNews() {
        const newsItems = await this.getNewsItems();
        return newsItems.filter(news => news.featured && (news.status === 'published' || !news.status));
    }

    // Venue functions
    async getVenues() {
        const data = await this.ensureDataLoaded();
        return data.popularVenues || [];
    }

    async getVenueById(id) {
        const venues = await this.getVenues();
        return venues.find(venue => venue.id === parseInt(id));
    }

    async getVenuesByType(type) {
        const venues = await this.getVenues();
        return venues.filter(venue => 
            venue.type && venue.type.toLowerCase().includes(type.toLowerCase())
        );
    }

    // Coach functions
    async getCoaches() {
        const data = await this.ensureDataLoaded();
        return data.featuredCoaches || [];
    }

    async getCoachById(id) {
        const coaches = await this.getCoaches();
        return coaches.find(coach => coach.id === parseInt(id));
    }

    async getCoachesBySport(sport) {
        const coaches = await this.getCoaches();
        return coaches.filter(coach => 
            coach.sport && coach.sport.toLowerCase() === sport.toLowerCase()
        );
    }

    // Product functions
    async getProducts() {
        const data = await this.ensureDataLoaded();
        return data.featuredProducts || [];
    }

    async getProductById(id) {
        const products = await this.getProducts();
        return products.find(product => product.id === parseInt(id));
    }

    async getProductsByCategory(category) {
        const products = await this.getProducts();
        return products.filter(product => 
            product.category && product.category.toLowerCase() === category.toLowerCase()
        );
    }

    // Category functions
    async getCategories() {
        const data = await this.ensureDataLoaded();
        return data.categories || [];
    }

    // User functions - REMOVED DUPLICATION
    // These now work with the main auth.js system instead of duplicating logic
    async getUsers() {
        // Check localStorage first (managed by auth.js)
        const storedUsers = this.getFromLocalStorage('users');
        if (storedUsers && storedUsers.length > 0) {
            return storedUsers;
        }

        // Fallback to JSON data
        const data = await this.ensureDataLoaded();
        return data.sampleUsers || [];
    }

    async getUserById(id) {
        const users = await this.getUsers();
        return users.find(user => user.id === parseInt(id));
    }

    async getUserByEmail(email) {
        const users = await this.getUsers();
        return users.find(user => user.email === email);
    }

    // Booking functions
    async getGroundBookings() {
        // Check localStorage first
        const storedBookings = this.getFromLocalStorage('groundBookings');
        if (storedBookings) {
            return storedBookings;
        }

        // Fallback to JSON data
        const data = await this.ensureDataLoaded();
        return data.groundBookings || [];
    }

    async getCoachBookings() {
        // Check localStorage first
        const storedBookings = this.getFromLocalStorage('coachBookings');
        if (storedBookings) {
            return storedBookings;
        }

        // Fallback to JSON data
        const data = await this.ensureDataLoaded();
        return data.coachBookings || [];
    }

    async getUserBookings(userId) {
        const groundBookings = await this.getGroundBookings();
        const coachBookings = await this.getCoachBookings();
        
        const userGroundBookings = groundBookings.filter(booking => 
            booking.userId === parseInt(userId)
        );
        const userCoachBookings = coachBookings.filter(booking => 
            booking.userId === parseInt(userId)
        );
        
        return { 
            groundBookings: userGroundBookings, 
            coachBookings: userCoachBookings 
        };
    }

    // Local storage functions
    saveToLocalStorage(key, data) {
        try {
            localStorage.setItem(key, JSON.stringify(data));
            return true;
        } catch (error) {
            console.error('Error saving to localStorage:', error);
            return false;
        }
    }

    getFromLocalStorage(key) {
        try {
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : null;
        } catch (error) {
            console.error('Error reading from localStorage:', error);
            return null;
        }
    }

    // Initialize local storage with data from JSON
    async initializeLocalStorage() {
        const data = await this.ensureDataLoaded();
        
        // Only initialize if data doesn't already exist
        // This prevents overwriting user-generated data
        
        if (!this.getFromLocalStorage('venues')) {
            this.saveToLocalStorage('venues', data.popularVenues || []);
        }
        
        if (!this.getFromLocalStorage('coaches')) {
            this.saveToLocalStorage('coaches', data.featuredCoaches || []);
        }
        
        if (!this.getFromLocalStorage('products')) {
            this.saveToLocalStorage('products', data.featuredProducts || []);
        }
        
        if (!this.getFromLocalStorage('groundBookings')) {
            this.saveToLocalStorage('groundBookings', data.groundBookings || []);
        }
        
        if (!this.getFromLocalStorage('coachBookings')) {
            this.saveToLocalStorage('coachBookings', data.coachBookings || []);
        }

        // For users, let auth.js handle initialization
        // Don't override if users already exist (managed by auth.js)
        if (!this.getFromLocalStorage('users') && data.sampleUsers) {
            this.saveToLocalStorage('users', data.sampleUsers);
        }

        // Initialize news data if not exists
        if (!this.getFromLocalStorage('newsData')) {
            try {
                const newsItems = await this.getNewsItems();
                if (newsItems.length > 0) {
                    this.saveToLocalStorage('newsData', { news: newsItems });
                }
            } catch (error) {
                console.warn('Could not initialize news data:', error);
            }
        }
    }

    // REMOVED DUPLICATE AUTH FUNCTIONS
    // Authentication is now handled by auth.js exclusively
    // This removes the duplicate authenticateUser and registerUser methods

    // Booking helpers
    async createGroundBooking(bookingData) {
        const storedBookings = await this.getGroundBookings();
        
        const newBooking = {
            id: Date.now(),
            ...bookingData,
            bookingDate: new Date().toISOString().split('T')[0],
            status: 'Pending',
            paymentStatus: 'Pending'
        };
        
        storedBookings.push(newBooking);
        this.saveToLocalStorage('groundBookings', storedBookings);
        
        return newBooking;
    }

    async createCoachBooking(bookingData) {
        const storedBookings = await this.getCoachBookings();
        
        const newBooking = {
            id: Date.now(),
            ...bookingData,
            bookingDate: new Date().toISOString().split('T')[0],
            status: 'Pending',
            paymentStatus: 'Pending'
        };
        
        storedBookings.push(newBooking);
        this.saveToLocalStorage('coachBookings', storedBookings);
        
        return newBooking;
    }

    // News management functions - NEW
    async createNews(newsData) {
        const existingNews = await this.getNewsItems();
        
        const newNews = {
            id: Date.now(),
            ...newsData,
            date: new Date().toISOString().split('T')[0],
            publishedAt: new Date().toISOString(),
            views: 0
        };
        
        existingNews.unshift(newNews);
        this.saveToLocalStorage('newsData', { news: existingNews });
        
        return newNews;
    }

    async updateNews(newsId, updatedData) {
        const existingNews = await this.getNewsItems();
        const index = existingNews.findIndex(news => news.id.toString() === newsId.toString());
        
        if (index !== -1) {
            existingNews[index] = { ...existingNews[index], ...updatedData };
            this.saveToLocalStorage('newsData', { news: existingNews });
            return existingNews[index];
        }
        
        throw new Error('News item not found');
    }

    async deleteNews(newsId) {
        const existingNews = await this.getNewsItems();
        const filteredNews = existingNews.filter(news => news.id.toString() !== newsId.toString());
        
        this.saveToLocalStorage('newsData', { news: filteredNews });
        return true;
    }

    // Utility functions
    async searchAll(query) {
        if (!query || query.trim().length < 2) {
            return { venues: [], coaches: [], products: [], news: [] };
        }

        const searchTerm = query.toLowerCase().trim();
        
        const [venues, coaches, products, news] = await Promise.all([
            this.getVenues(),
            this.getCoaches(),
            this.getProducts(),
            this.getNewsItems()
        ]);

        return {
            venues: venues.filter(venue => 
                venue.name?.toLowerCase().includes(searchTerm) ||
                venue.type?.toLowerCase().includes(searchTerm) ||
                venue.location?.toLowerCase().includes(searchTerm)
            ),
            coaches: coaches.filter(coach =>
                coach.name?.toLowerCase().includes(searchTerm) ||
                coach.sport?.toLowerCase().includes(searchTerm) ||
                coach.specialization?.toLowerCase().includes(searchTerm)
            ),
            products: products.filter(product =>
                product.name?.toLowerCase().includes(searchTerm) ||
                product.category?.toLowerCase().includes(searchTerm) ||
                product.brand?.toLowerCase().includes(searchTerm)
            ),
            news: news.filter(article =>
                article.title?.toLowerCase().includes(searchTerm) ||
                article.summary?.toLowerCase().includes(searchTerm) ||
                article.content?.toLowerCase().includes(searchTerm)
            )
        };
    }

    // Get statistics for dashboard
    async getStats() {
        const [venues, coaches, products, users, groundBookings, coachBookings, news] = await Promise.all([
            this.getVenues(),
            this.getCoaches(),
            this.getProducts(),
            this.getUsers(),
            this.getGroundBookings(),
            this.getCoachBookings(),
            this.getNewsItems()
        ]);

        return {
            totalVenues: venues.length,
            totalCoaches: coaches.length,
            totalProducts: products.length,
            totalUsers: users.length,
            totalGroundBookings: groundBookings.length,
            totalCoachBookings: coachBookings.length,
            totalBookings: groundBookings.length + coachBookings.length,
            totalNews: news.length,
            publishedNews: news.filter(n => n.status === 'published' || !n.status).length,
            draftNews: news.filter(n => n.status === 'draft').length,
            totalNewsViews: news.reduce((sum, item) => sum + (item.views || 0), 0)
        };
    }
}

// Create global instance
const dataManager = new DataManager();

// Export for use in other files
window.dataManager = dataManager;

// Also export individual functions for convenience
window.getVenues = () => dataManager.getVenues();
window.getCoaches = () => dataManager.getCoaches();
window.getProducts = () => dataManager.getProducts();
window.getNewsItems = () => dataManager.getNewsItems();