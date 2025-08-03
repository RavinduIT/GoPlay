// Fixed Authentication JavaScript - Main Auth System
class Auth {
    constructor() {
        this.currentUser = null;
        this.isInitialized = false;
        this.init();
    }

    init() {
        this.initializeDefaultUsers();
        this.loadCurrentUser();
        this.setupStorageListener();
        this.isInitialized = true;
        console.log('Auth system initialized');
    }

    // Initialize default users if none exist
    initializeDefaultUsers() {
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
    }

    loadCurrentUser() {
        try {
            const userData = localStorage.getItem('currentUser');
            this.currentUser = userData ? JSON.parse(userData) : null;
            console.log('Current user loaded:', this.currentUser ? this.currentUser.email : 'None');
        } catch (error) {
            console.error('Error loading current user:', error);
            this.currentUser = null;
        }
    }

    setupStorageListener() {
        window.addEventListener('storage', (e) => {
            if (e.key === 'currentUser') {
                this.loadCurrentUser();
                this.updateAuthUI();
            }
        });
    }

    async login(email, password) {
        try {
            console.log('Attempting login for:', email);
            
            // Get users from localStorage
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            console.log('Found users:', users.length);
            
            const foundUser = users.find(u => u.email === email && u.password === password);
            
            if (foundUser) {
                console.log('User found, logging in:', foundUser.email);
                
                // Remove password from current user object
                const { password: _, ...userWithoutPassword } = foundUser;
                this.currentUser = userWithoutPassword;
                
                // Store current user
                localStorage.setItem('currentUser', JSON.stringify(userWithoutPassword));
                localStorage.setItem('sessionTimestamp', Date.now().toString());
                
                this.updateAuthUI();
                
                return { success: true, user: userWithoutPassword };
            }
            
            console.log('Invalid credentials');
            return { success: false, error: 'Invalid email or password' };
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: 'Login failed' };
        }
    }

    async signup(userData) {
        try {
            console.log('Attempting signup for:', userData.email);
            
            // Validate signup form
            const validation = this.validateSignupForm(userData);
            if (!validation.isValid) {
                return { success: false, error: validation.errors[0] };
            }

            // Get existing users
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            
            // Check if user already exists
            if (users.find(u => u.email === userData.email)) {
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
            localStorage.setItem('users', JSON.stringify(users));

            // Set current user (without password)
            this.currentUser = newUser;
            localStorage.setItem('currentUser', JSON.stringify(newUser));
            localStorage.setItem('sessionTimestamp', Date.now().toString());
            
            this.updateAuthUI();
            
            console.log('User registered successfully:', newUser.email);
            return { success: true, user: newUser };
        } catch (error) {
            console.error('Signup error:', error);
            return { success: false, error: 'Signup failed' };
        }
    }

    logout() {
        console.log('Logging out user');
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        localStorage.removeItem('sessionTimestamp');
        this.updateAuthUI();
        
        // Redirect to home page if on protected page
        const currentPath = window.location.pathname;
        const protectedPages = ['/pages/profile.html', '/pages/my-bookings.html'];
        if (protectedPages.some(page => currentPath.includes(page))) {
            window.location.href = '../index.html';
        }
    }

    updateUser(userData) {
        if (this.currentUser) {
            const updatedUser = { ...this.currentUser, ...userData };
            this.currentUser = updatedUser;
            localStorage.setItem('currentUser', JSON.stringify(updatedUser));
            
            // Update user in users array
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            const userIndex = users.findIndex(u => u.id === this.currentUser.id);
            if (userIndex !== -1) {
                users[userIndex] = { ...users[userIndex], ...userData };
                localStorage.setItem('users', JSON.stringify(users));
            }
            
            this.updateAuthUI();
            return { success: true, user: updatedUser };
        }
        
        return { success: false, error: 'No current user' };
    }

    updateAuthUI() {
        // Update navbar auth state
        if (window.navbar) {
            window.navbar.checkAuthState();
        }
        
        // Dispatch custom event for other components
        window.dispatchEvent(new CustomEvent('authStateChanged', {
            detail: { user: this.currentUser, isAuthenticated: !!this.currentUser }
        }));
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
        const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast ${variant === 'destructive' ? 'toast-destructive' : ''}`;
        
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-description">${description}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    createToastContainer() {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    }

    // Session management
    checkSessionExpiry() {
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
    }
}

// Global auth instance
let authInstance = null;

// Initialize auth when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('Initializing auth system...');
    authInstance = new Auth();
    
    // Make toast function globally available
    window.showGlobalToast = (title, description, variant) => {
        if (authInstance) {
            authInstance.showToast(title, description, variant);
        }
    };
    
    // Check session expiry every 5 minutes
    setInterval(() => {
        if (authInstance) {
            authInstance.checkSessionExpiry();
        }
    }, 5 * 60 * 1000);

    console.log('Auth system ready!');
    console.log('Test credentials:');
    console.log('User: user@example.com / password');
    console.log('Coach: coach@example.com / password');
    console.log('Admin: admin@example.com / password');
});

// Global functions for easy access
function login(email, password) {
    if (!authInstance) {
        console.error('Auth not initialized');
        return Promise.resolve({ success: false, error: 'Auth not initialized' });
    }
    return authInstance.login(email, password);
}

function signup(userData) {
    if (!authInstance) {
        console.error('Auth not initialized');
        return Promise.resolve({ success: false, error: 'Auth not initialized' });
    }
    return authInstance.signup(userData);
}

function logout() {
    if (authInstance) {
        authInstance.logout();
    }
}

function getCurrentUser() {
    return authInstance ? authInstance.getCurrentUser() : null;
}

function isAuthenticated() {
    return authInstance ? authInstance.isAuthenticated() : false;
}

function updateUser(userData) {
    return authInstance ? authInstance.updateUser(userData) : { success: false, error: 'Auth not initialized' };
}

function showToast(title, description, variant) {
    if (authInstance) {
        authInstance.showToast(title, description, variant);
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Auth;
}