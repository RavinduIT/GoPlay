// Data utility functions for SportHub application
class DataManager {
    constructor() {
        this.data = null;
        this.init();
    }

    async init() {
        await this.loadData();
        this.initializeLocalStorage();
    }

    // Load data from JSON file
    async loadData() {
        try {
            const response = await fetch('../../data/data.json');
            if (!response.ok) {
                throw new Error('Failed to load data');
            }
            this.data = await response.json();
            return this.data;
        } catch (error) {
            console.error('Error loading data:', error);
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
            venue.type.toLowerCase().includes(type.toLowerCase())
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
            coach.sport.toLowerCase() === sport.toLowerCase()
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
            product.category.toLowerCase() === category.toLowerCase()
        );
    }

    // Category functions
    async getCategories() {
        const data = await this.ensureDataLoaded();
        return data.categories || [];
    }

    // User functions
    async getUsers() {
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
        const data = await this.ensureDataLoaded();
        return data.groundBookings || [];
    }

    async getCoachBookings() {
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
        
        // Initialize users if not exists
        if (!this.getFromLocalStorage('users')) {
            this.saveToLocalStorage('users', data.sampleUsers || []);
        }
        
        // Initialize venues if not exists
        if (!this.getFromLocalStorage('venues')) {
            this.saveToLocalStorage('venues', data.popularVenues || []);
        }
        
        // Initialize coaches if not exists
        if (!this.getFromLocalStorage('coaches')) {
            this.saveToLocalStorage('coaches', data.featuredCoaches || []);
        }
        
        // Initialize products if not exists
        if (!this.getFromLocalStorage('products')) {
            this.saveToLocalStorage('products', data.featuredProducts || []);
        }
        
        // Initialize bookings if not exists
        if (!this.getFromLocalStorage('groundBookings')) {
            this.saveToLocalStorage('groundBookings', data.groundBookings || []);
        }
        
        if (!this.getFromLocalStorage('coachBookings')) {
            this.saveToLocalStorage('coachBookings', data.coachBookings || []);
        }
    }

    // Authentication helpers
    async authenticateUser(email, password) {
        // First check localStorage for updated user data
        const storedUsers = this.getFromLocalStorage('users') || [];
        let user = storedUsers.find(u => u.email === email && u.password === password);
        
        // If not found in localStorage, check JSON data
        if (!user) {
            const users = await this.getUsers();
            user = users.find(u => u.email === email && u.password === password);
        }
        
        return user;
    }

    async registerUser(userData) {
        const storedUsers = this.getFromLocalStorage('users') || [];
        
        // Check if user already exists
        if (storedUsers.find(u => u.email === userData.email)) {
            return { success: false, message: 'User already exists' };
        }
        
        // Create new user
        const newUser = {
            id: Date.now(),
            ...userData,
            role: 'Player',
            joinDate: new Date().toISOString().split('T')[0],
            sports: [],
            avatar: '👤',
            bio: ''
        };
        
        storedUsers.push(newUser);
        this.saveToLocalStorage('users', storedUsers);
        
        return { success: true, user: newUser };
    }

    // Booking helpers
    async createGroundBooking(bookingData) {
        const storedBookings = this.getFromLocalStorage('groundBookings') || [];
        
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
        const storedBookings = this.getFromLocalStorage('coachBookings') || [];
        
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
}

// Create global instance
const dataManager = new DataManager();

// Export for use in other files
window.dataManager = dataManager;