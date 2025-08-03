// Fixed Authentication JavaScript - Main Auth System
class Auth {
    constructor() {
        this.currentUser = null;
        this.isInitialized = false;
        this.initPromise = null;
        this.init();
    }

    async init() {
        if (this.initPromise) {
            return this.initPromise;
        }

        this.initPromise = this._performInit();
        return this.initPromise;
    }

    async _performInit() {
        try {
            console.log('Initializing auth system...');
            await this.initializeDefaultUsers();
            this.loadCurrentUser();
            this.setupStorageListener();
            this.isInitialized = true;
            console.log('Auth system initialized successfully');
            
            // Dispatch ready event
            window.dispatchEvent(new CustomEvent('authReady'));
            return true;
        } catch (error) {
            console.error('Auth initialization failed:', error);
            return false;
        }
    }

    // Initialize default users if none exist
    async initializeDefaultUsers() {
        try {
            // Check if localStorage is available
            if (typeof Storage === "undefined") {
                console.warn('localStorage not available, using memory storage');
                return;
            }

            const users = JSON.parse(localStorage.getItem('users') || '[]');
            
            if (users.length === 0) {
                const defaultUsers = [
                    {
                        id: '1',
                        name: 'John Doe',
                        email: 'user@example.com',
                        password: 'password',
                        phone: '+1234567890',
                        location: 'New York, NY',
                        bio: 'Sports enthusiast and weekend warrior',
                        role: 'Player',
                        joinDate: '2024-01-15',
                        sports: ['Basketball', 'Tennis'],
                        avatar: '👨'
                    },
                    {
                        id: '2',
                        name: 'Sarah Wilson',
                        email: 'coach@example.com',
                        password: 'password',
                        phone: '+1234567891',
                        location: 'Los Angeles, CA',
                        bio: 'Professional tennis coach with 10 years experience',
                        role: 'Coach',
                        joinDate: '2024-01-10',
                        sports: ['Tennis'],
                        avatar: '👩'
                    },
                    {
                        id: '3',
                        name: 'Mike Johnson',
                        email: 'admin@example.com',
                        password: 'password',
                        phone: '+1234567892',
                        location: 'Chicago, IL',
                        bio: 'Sports equipment retailer',
                        role: 'Admin',
                        joinDate: '2024-01-05',
                        sports: ['Football', 'Basketball'],
                        avatar: '👨‍💼'
                    }
                ];
                
                localStorage.setItem('users', JSON.stringify(defaultUsers));
                console.log('Default users created');
            }
        } catch (error) {
            console.error('Error initializing default users:', error);
        }
    }

    loadCurrentUser() {
        try {
            if (typeof Storage === "undefined") {
                this.currentUser = null;
                return;
            }

            const userData = localStorage.getItem('currentUser');
            this.currentUser = userData ? JSON.parse(userData) : null;
            console.log('Current user loaded:', this.currentUser ? this.currentUser.email : 'None');
        } catch (error) {
            console.error('Error loading current user:', error);
            this.currentUser = null;
            if (typeof Storage !== "undefined") {
                localStorage.removeItem('currentUser');
            }
        }
    }

    setupStorageListener() {
        if (typeof window !== 'undefined' && window.addEventListener) {
            window.addEventListener('storage', (e) => {
                if (e.key === 'currentUser') {
                    this.loadCurrentUser();
                    this.updateAuthUI();
                }
            });
        }
    }

    async login(email, password) {
        try {
            console.log('Attempting login for:', email);
            
            if (!email || !password) {
                return { success: false, error: 'Email and password are required' };
            }

            // Ensure auth is initialized
            await this.init();

            // Get users from localStorage
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            console.log('Found users:', users.length);
            
            const foundUser = users.find(u => u.email.toLowerCase() === email.toLowerCase() && u.password === password);
            
            if (foundUser) {
                console.log('User found, logging in:', foundUser.email);
                
                // Remove password from current user object
                const { password: _, ...userWithoutPassword } = foundUser;
                this.currentUser = userWithoutPassword;
                
                // Store current user
                if (typeof Storage !== "undefined") {
                    localStorage.setItem('currentUser', JSON.stringify(userWithoutPassword));
                    localStorage.setItem('sessionTimestamp', Date.now().toString());
                }
                
                this.updateAuthUI();
                
                return { success: true, user: userWithoutPassword };
            }
            
            console.log('Invalid credentials for:', email);
            return { success: false, error: 'Invalid email or password' };
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: 'Login failed. Please try again.' };
        }
    }

    async signup(userData) {
        try {
            console.log('Attempting signup for:', userData.email);
            
            // Ensure auth is initialized
            await this.init();

            // Validate signup form
            const validation = this.validateSignupForm(userData);
            if (!validation.isValid) {
                return { success: false, error: validation.errors[0] };
            }

            // Get existing users
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            
            // Check if user already exists
            if (users.find(u => u.email.toLowerCase() === userData.email.toLowerCase())) {
                return { success: false, error: 'User already exists with this email' };
            }

            // Create new user
            const newUser = {
                id: Date.now().toString(),
                name: userData.name,
                email: userData.email,
                phone: userData.phone,
                location: userData.location,
                bio: '',
                role: 'Player',
                joinDate: new Date().toISOString().split('T')[0],
                sports: [],
                avatar: '👤'
            };

            // Store user with password for login
            const userWithPassword = { ...newUser, password: userData.password };
            users.push(userWithPassword);
            
            if (typeof Storage !== "undefined") {
                localStorage.setItem('users', JSON.stringify(users));
                // Set current user (without password)
                this.currentUser = newUser;
                localStorage.setItem('currentUser', JSON.stringify(newUser));
                localStorage.setItem('sessionTimestamp', Date.now().toString());
            } else {
                this.currentUser = newUser;
            }
            
            this.updateAuthUI();
            
            console.log('User registered successfully:', newUser.email);
            return { success: true, user: newUser };
        } catch (error) {
            console.error('Signup error:', error);
            return { success: false, error: 'Signup failed. Please try again.' };
        }
    }

    logout() {
        console.log('Logging out user');
        this.currentUser = null;
        
        if (typeof Storage !== "undefined") {
            localStorage.removeItem('currentUser');
            localStorage.removeItem('sessionTimestamp');
        }
        
        this.updateAuthUI();
        
        // Redirect to home page if on protected page
        if (typeof window !== 'undefined' && window.location) {
            const currentPath = window.location.pathname;
            const protectedPages = ['/pages/profile.html', '/pages/my-bookings.html'];
            if (protectedPages.some(page => currentPath.includes(page))) {
                window.location.href = currentPath.includes('/pages/') ? '../index.html' : 'index.html';
            }
        }
    }

    updateUser(userData) {
        if (this.currentUser) {
            const updatedUser = { ...this.currentUser, ...userData };
            this.currentUser = updatedUser;
            
            if (typeof Storage !== "undefined") {
                localStorage.setItem('currentUser', JSON.stringify(updatedUser));
                
                // Update user in users array
                const users = JSON.parse(localStorage.getItem('users') || '[]');
                const userIndex = users.findIndex(u => u.id === this.currentUser.id);
                if (userIndex !== -1) {
                    users[userIndex] = { ...users[userIndex], ...userData };
                    localStorage.setItem('users', JSON.stringify(users));
                }
            }
            
            this.updateAuthUI();
            return { success: true, user: updatedUser };
        }
        
        return { success: false, error: 'No current user' };
    }

    updateAuthUI() {
        try {
            // Update navbar auth state - check if navbar exists
            if (typeof window !== 'undefined' && window.navbar && typeof window.navbar.checkAuthState === 'function') {
                window.navbar.checkAuthState();
            }
            
            // Dispatch custom event for other components
            if (typeof window !== 'undefined' && window.dispatchEvent) {
                window.dispatchEvent(new CustomEvent('authStateChanged', {
                    detail: { user: this.currentUser, isAuthenticated: !!this.currentUser }
                }));
            }
        } catch (error) {
            console.warn('Error updating auth UI:', error);
        }
    }

    isAuthenticated() {
        return !!this.currentUser;
    }

    getCurrentUser() {
        return this.currentUser;
    }

    hasRole(role) {
        return this.currentUser && this.currentUser.role === role;
    }

    canAccessAdmin() {
        return this.isAuthenticated() && ['Admin', 'Shop Owner', 'Complex Owner', 'Coach'].includes(this.currentUser.role);
    }

    // Form validation
    validateSignupForm(formData) {
        const errors = [];

        if (!formData.name || formData.name.trim().length < 2) {
            errors.push('Name must be at least 2 characters long');
        }

        if (!this.validateEmail(formData.email)) {
            errors.push('Please enter a valid email address');
        }

        if (!formData.password || formData.password.length < 6) {
            errors.push('Password must be at least 6 characters long');
        }

        if (formData.password !== formData.confirmPassword) {
            errors.push('Passwords do not match');
        }

        if (!this.validatePhone(formData.phone)) {
            errors.push('Please enter a valid phone number');
        }

        if (!formData.location || formData.location.trim().length < 2) {
            errors.push('Location must be at least 2 characters long');
        }

        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    validatePhone(phone) {
        const phoneRegex = /^\+?[1-9]\d{1,14}$/;
        return phoneRegex.test(phone.replace(/[\s()-]/g, ''));
    }

    // Toast notification
    showToast(title, description, variant = 'default') {
        try {
            const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
            
            // Remove existing toasts
            const existingToasts = toastContainer.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());
            
            const toast = document.createElement('div');
            toast.className = `toast ${variant === 'destructive' ? 'toast-destructive' : ''}`;
            
            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-title">${this.escapeHtml(title)}</div>
                    <div class="toast-description">${this.escapeHtml(description)}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
            `;
            
            // Add styles
            toast.style.cssText = `
                position: relative;
                background: ${variant === 'destructive' ? '#fef2f2' : '#f0fdf4'};
                border: 1px solid ${variant === 'destructive' ? '#fecaca' : '#bbf7d0'};
                color: ${variant === 'destructive' ? '#dc2626' : '#16a34a'};
                padding: 16px;
                border-radius: 8px;
                margin-bottom: 8px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideIn 0.3s ease-out;
            `;
            
            const closeBtn = toast.querySelector('.toast-close');
            if (closeBtn) {
                closeBtn.style.cssText = `
                    background: none;
                    border: none;
                    font-size: 18px;
                    cursor: pointer;
                    color: currentColor;
                    opacity: 0.7;
                    margin-left: 12px;
                `;
            }
            
            const content = toast.querySelector('.toast-content');
            if (content) {
                content.style.cssText = 'flex: 1;';
            }
            
            const titleEl = toast.querySelector('.toast-title');
            if (titleEl) {
                titleEl.style.cssText = 'font-weight: 600; margin-bottom: 4px;';
            }
            
            const descEl = toast.querySelector('.toast-description');
            if (descEl) {
                descEl.style.cssText = 'font-size: 14px; opacity: 0.9;';
            }
            
            toastContainer.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }
            }, 5000);
        } catch (error) {
            console.error('Error showing toast:', error);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    createToastContainer() {
        try {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container';
                container.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 1000;
                    max-width: 400px;
                `;
                
                // Add animation styles
                if (!document.querySelector('#toast-animations')) {
                    const style = document.createElement('style');
                    style.id = 'toast-animations';
                    style.textContent = `
                        @keyframes slideIn {
                            from {
                                opacity: 0;
                                transform: translateX(100%);
                            }
                            to {
                                opacity: 1;
                                transform: translateX(0);
                            }
                        }
                    `;
                    document.head.appendChild(style);
                }
                
                document.body.appendChild(container);
            }
            return container;
        } catch (error) {
            console.error('Error creating toast container:', error);
            return null;
        }
    }

    // Session management
    checkSessionExpiry() {
        try {
            if (typeof Storage === "undefined") return true;
            
            const sessionTimestamp = localStorage.getItem('sessionTimestamp');
            if (sessionTimestamp) {
                const sessionAge = Date.now() - parseInt(sessionTimestamp);
                const maxAge = 24 * 60 * 60 * 1000; // 24 hours
                
                if (sessionAge > maxAge) {
                    this.logout();
                    return false;
                }
            }
            return true;
        } catch (error) {
            console.error('Error checking session expiry:', error);
            return true;
        }
    }
}

// Global auth instance
let authInstance = null;

// Initialize auth immediately
async function initializeAuth() {
    if (!authInstance) {
        console.log('Creating auth instance...');
        authInstance = new Auth();
        
        // Wait for initialization to complete
        const initialized = await authInstance.init();
        
        if (initialized) {
            // Make functions globally available
            window.authInstance = authInstance;
            window.login = login;
            window.signup = signup;
            window.logout = logout;
            window.getCurrentUser = getCurrentUser;
            window.isAuthenticated = isAuthenticated;
            window.updateUser = updateUser;
            window.showToast = showToast;
            
            console.log('Auth system ready!');
            console.log('Test credentials:');
            console.log('User: user@example.com / password');
            console.log('Coach: coach@example.com / password');
            console.log('Admin: admin@example.com / password');
        } else {
            console.error('Auth system failed to initialize');
        }
    }
    return authInstance;
}

// Global functions for easy access
async function login(email, password) {
    const auth = authInstance || await initializeAuth();
    return auth.login(email, password);
}

async function signup(userData) {
    const auth = authInstance || await initializeAuth();
    return auth.signup(userData);
}

function logout() {
    const auth = authInstance || initializeAuth();
    auth.logout();
}

function getCurrentUser() {
    const auth = authInstance || initializeAuth();
    return auth.getCurrentUser();
}

function isAuthenticated() {
    const auth = authInstance || initializeAuth();
    return auth.isAuthenticated();
}

function updateUser(userData) {
    const auth = authInstance || initializeAuth();
    return auth.updateUser(userData);
}

function showToast(title, description, variant) {
    const auth = authInstance || initializeAuth();
    auth.showToast(title, description, variant);
}

// Initialize immediately when script loads
(async function() {
    try {
        await initializeAuth();
    } catch (error) {
        console.error('Failed to initialize auth on script load:', error);
    }
})();

// Also initialize on DOM ready as backup
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAuth);
    } else {
        initializeAuth();
    }
}

// Check session expiry every 5 minutes
if (typeof setInterval !== 'undefined') {
    setInterval(() => {
        if (authInstance) {
            authInstance.checkSessionExpiry();
        }
    }, 5 * 60 * 1000);
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Auth;
}