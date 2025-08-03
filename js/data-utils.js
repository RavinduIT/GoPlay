// Fixed Data utility functions for SportHub application
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
            const response = await fetch('../data/data.json');
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

    // News functions
    async getNewsItems() {
        const data = await this.ensureDataLoaded();
        return data.newsItems || [];
    }

    async getNewsById(id) {
        const newsItems = await this.getNewsItems();
        return newsItems.find(news => news.id === parseInt(id));
    }

    async getLatestNews(limit = 6) {
        const newsItems = await this.getNewsItems();
        return newsItems
            .sort((a, b) => new Date(b.date) - new Date(a.date))
            .slice(0, limit);
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
                article.summary?.toLowerCase().includes(searchTerm)
            )
        };
    }

    // Get statistics for dashboard
    async getStats() {
        const [venues, coaches, products, users, groundBookings, coachBookings] = await Promise.all([
            this.getVenues(),
            this.getCoaches(),
            this.getProducts(),
            this.getUsers(),
            this.getGroundBookings(),
            this.getCoachBookings()
        ]);

        return {
            totalVenues: venues.length,
            totalCoaches: coaches.length,
            totalProducts: products.length,
            totalUsers: users.length,
            totalGroundBookings: groundBookings.length,
            totalCoachBookings: coachBookings.length,
            totalBookings: groundBookings.length + coachBookings.length
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