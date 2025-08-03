// Enhanced Authentication JavaScript - Main Auth System
class Auth {
    constructor() {
        this.currentUser = null;
        this.init();
    }

    init() {
        this.loadCurrentUser();
        this.setupStorageListener();
    }

    loadCurrentUser() {
        try {
            const userData = localStorage.getItem('currentUser');
            this.currentUser = userData ? JSON.parse(userData) : null;
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
            // Get users from localStorage
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            const foundUser = users.find(u => u.email === email && u.password === password);
            
            if (foundUser) {
                const { password: _, ...userWithoutPassword } = foundUser;
                this.currentUser = userWithoutPassword;
                localStorage.setItem('currentUser', JSON.stringify(userWithoutPassword));
                this.updateAuthUI();
                this.extendSession(); // Track session
                return { success: true, user: userWithoutPassword };
            }
            
            return { success: false, error: 'Invalid email or password' };
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, error: 'Login failed' };
        }
    }

    async signup(userData) {
        try {
            // Validate signup form first
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
            this.updateAuthUI();
            this.extendSession(); // Track session
            
            return { success: true, user: newUser };
        } catch (error) {
            console.error('Signup error:', error);
            return { success: false, error: 'Signup failed' };
        }
    }

    logout() {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        localStorage.removeItem('sessionTimestamp');
        this.updateAuthUI();
        
        // Redirect to home page if on protected page
        const protectedPages = ['/pages/profile.html', '/pages/my-bookings.html'];
        if (protectedPages.some(page => window.location.pathname.includes(page))) {
            window.location.href = '../index.html'; // Fixed redirect path
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

    // Check if user has access to admin features
    canAccessAdmin() {
        return this.isAuthenticated() && ['Admin', 'Shop Owner', 'Complex Owner', 'Coach'].includes(this.currentUser.role);
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
        }
    }

    // Enhanced password validation
    validatePassword(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        const errors = [];
        
        if (password.length < minLength) {
            errors.push(`Password must be at least ${minLength} characters long`);
        }
        if (!hasUpperCase) {
            errors.push('Password must contain at least one uppercase letter');
        }
        if (!hasLowerCase) {
            errors.push('Password must contain at least one lowercase letter');
        }
        if (!hasNumbers) {
            errors.push('Password must contain at least one number');
        }
        if (!hasSpecialChar) {
            errors.push('Password must contain at least one special character');
        }

        return {
            isValid: errors.length === 0,
            errors: errors,
            strength: this.getPasswordStrength(password)
        };
    }

    // Get password strength score
    getPasswordStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[a-z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score++;
        
        if (score < 3) return { level: 'weak', text: 'Weak' };
        if (score < 4) return { level: 'medium', text: 'Medium' };
        return { level: 'strong', text: 'Strong' };
    }

    // Email validation
    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Phone validation
    validatePhone(phone) {
        const phoneRegex = /^\+?[1-9]\d{1,14}$/;
        return phoneRegex.test(phone.replace(/[\s()-]/g, ''));
    }

    // Enhanced form validation
    validateSignupForm(formData) {
        const errors = [];

        if (!formData.name || formData.name.trim().length < 2) {
            errors.push('Name must be at least 2 characters long');
        }

        if (!this.validateEmail(formData.email)) {
            errors.push('Please enter a valid email address');
        }

        const passwordValidation = this.validatePassword(formData.password);
        if (!passwordValidation.isValid) {
            errors.push(...passwordValidation.errors);
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

    // Session management
    extendSession() {
        if (this.currentUser) {
            const sessionData = {
                user: this.currentUser,
                timestamp: Date.now()
            };
            localStorage.setItem('currentUser', JSON.stringify(this.currentUser));
            localStorage.setItem('sessionTimestamp', sessionData.timestamp.toString());
        }
    }

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

    // Enhanced toast function for global use
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
        
        // Trigger animation
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

    // Create toast container if it doesn't exist
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

    // Forgot password (mock implementation)
    async forgotPassword(email) {
        const users = JSON.parse(localStorage.getItem('users') || '[]');
        const user = users.find(u => u.email === email);
        
        if (user) {
            // In a real app, this would send an email
            console.log(`Password reset email would be sent to ${email}`);
            return { success: true, message: 'Password reset email sent' };
        }
        
        return { success: false, error: 'Email not found' };
    }

    // Reset password (mock implementation)
    async resetPassword(token, newPassword) {
        // In a real app, this would validate the token
        const passwordValidation = this.validatePassword(newPassword);
        
        if (!passwordValidation.isValid) {
            return { success: false, errors: passwordValidation.errors };
        }
        
        // Mock successful reset
        return { success: true, message: 'Password reset successfully' };
    }

    // Change password for logged in user
    async changePassword(currentPassword, newPassword) {
        if (!this.currentUser) {
            return { success: false, error: 'Not logged in' };
        }

        // Verify current password
        const users = JSON.parse(localStorage.getItem('users') || '[]');
        const user = users.find(u => u.id === this.currentUser.id);
        
        if (!user || user.password !== currentPassword) {
            return { success: false, error: 'Current password is incorrect' };
        }

        // Validate new password
        const validation = this.validatePassword(newPassword);
        if (!validation.isValid) {
            return { success: false, error: validation.errors[0] };
        }

        // Update password
        const userIndex = users.findIndex(u => u.id === this.currentUser.id);
        users[userIndex].password = newPassword;
        localStorage.setItem('users', JSON.stringify(users));

        return { success: true, message: 'Password changed successfully' };
    }
}

// Global auth instance
let authInstance = null;

// Initialize auth when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    authInstance = new Auth();
    authInstance.initializeDefaultUsers();
    
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

    console.log('Auth system initialized successfully!');
    console.log('Test credentials:');
    console.log('User: user@example.com / password');
    console.log('Coach: coach@example.com / password');
    console.log('Admin: admin@example.com / password');
});

// Global functions for easy access
function login(email, password) {
    return authInstance ? authInstance.login(email, password) : Promise.resolve({ success: false, error: 'Auth not initialized' });
}

function signup(userData) {
    return authInstance ? authInstance.signup(userData) : Promise.resolve({ success: false, error: 'Auth not initialized' });
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