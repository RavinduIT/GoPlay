// AuthContext-like functionality
class AuthManager {
    constructor() {
        this.currentUser = this.getCurrentUser();
    }

    getCurrentUser() {
        const storedUser = localStorage.getItem('currentUser');
        return storedUser ? JSON.parse(storedUser) : null;
    }

    async signup(userData) {
        // Simulate API call delay
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        try {
            // Get existing users
            const users = JSON.parse(localStorage.getItem('users') || '[]');
            
            // Check if user already exists
            if (users.find(u => u.email === userData.email)) {
                return { success: false, error: 'User already exists' };
            }

            // Create new user (matching your AuthContext structure)
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
            
            return { success: true };
        } catch (error) {
            return { success: false, error: 'Registration failed' };
        }
    }

    logout() {
        this.currentUser = null;
        localStorage.removeItem('currentUser');
    }

    isAuthenticated() {
        return !!this.currentUser;
    }
}

// Initialize auth manager
const auth = new AuthManager();

// Form data management
class FormManager {
    constructor() {
        this.formData = {
            name: '',
            email: '',
            password: '',
            confirmPassword: '',
            phone: '',
            location: ''
        };
        this.isLoading = false;
    }

    updateField(field, value) {
        this.formData[field] = value;
        this.validateField(field, value);
    }

    validateField(field, value) {
        const input = document.getElementById(field);
        
        // Remove existing validation classes
        input.classList.remove('error', 'success');
        
        // Basic validation
        if (field === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value && !emailRegex.test(value)) {
                input.classList.add('error');
                return false;
            } else if (value) {
                input.classList.add('success');
            }
        }
        
        if (field === 'password') {
            this.updatePasswordStrength(value);
        }
        
        if (field === 'confirmPassword') {
            if (value && value !== this.formData.password) {
                input.classList.add('error');
                return false;
            } else if (value && value === this.formData.password) {
                input.classList.add('success');
            }
        }
        
        return true;
    }

    updatePasswordStrength(password) {
        // Remove existing password strength indicator
        const existingIndicator = document.querySelector('.password-strength');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        if (!password) return;
        
        const passwordContainer = document.getElementById('password').parentElement.parentElement;
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength';
        
        let strength = 0;
        let strengthText = '';
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        if (strength < 3) {
            strengthIndicator.classList.add('weak');
            strengthText = 'Weak password';
        } else if (strength < 4) {
            strengthIndicator.classList.add('medium');
            strengthText = 'Medium strength';
        } else {
            strengthIndicator.classList.add('strong');
            strengthText = 'Strong password';
        }
        
        strengthIndicator.textContent = strengthText;
        passwordContainer.appendChild(strengthIndicator);
    }

    validateForm() {
        const requiredFields = ['name', 'email', 'phone', 'location', 'password', 'confirmPassword'];
        let isValid = true;
        
        for (const field of requiredFields) {
            const value = this.formData[field];
            if (!value || !this.validateField(field, value)) {
                isValid = false;
            }
        }
        
        // Check password match
        if (this.formData.password !== this.formData.confirmPassword) {
            isValid = false;
        }
        
        return isValid;
    }

    reset() {
        this.formData = {
            name: '',
            email: '',
            password: '',
            confirmPassword: '',
            phone: '',
            location: ''
        };
        
        // Clear form inputs
        Object.keys(this.formData).forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.value = '';
                input.classList.remove('error', 'success');
            }
        });
        
        // Remove password strength indicator
        const strengthIndicator = document.querySelector('.password-strength');
        if (strengthIndicator) {
            strengthIndicator.remove();
        }
    }
}

// Initialize form manager
const formManager = new FormManager();

// Toast functionality (matching your useToast hook)
function showToast(title, description, variant = 'default') {
    const toastContainer = document.getElementById('toastContainer');
    
    const toast = document.createElement('div');
    toast.className = `toast ${variant === 'destructive' ? 'toast-destructive' : ''}`;
    
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-description">${description}</div>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Remove toast after 5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// Form submission handler (matching your React component logic)
async function handleSubmit(e) {
    e.preventDefault();
    
    // Password validation
    if (formManager.formData.password !== formManager.formData.confirmPassword) {
        showToast(
            'Password mismatch',
            'Passwords do not match. Please try again.',
            'destructive'
        );
        return;
    }
    
    // Form validation
    if (!formManager.validateForm()) {
        showToast(
            'Validation Error',
            'Please fill in all required fields correctly.',
            'destructive'
        );
        return;
    }
    
    const submitBtn = document.getElementById('submitBtn');
    
    // Show loading state
    formManager.isLoading = true;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner"></span>Creating account...';
    
    try {
        const result = await auth.signup({
            name: formManager.formData.name,
            email: formManager.formData.email,
            password: formManager.formData.password,
            phone: formManager.formData.phone,
            location: formManager.formData.location
        });
        
        if (result.success) {
            showToast(
                'Account created!',
                'Welcome to SportConnect! You can now update your profile.'
            );
            
            // Redirect after successful signup (matching your navigate('/profile'))
            setTimeout(() => {
                window.location.href = '/profile'; // Replace with your profile URL
            }, 2000);
        } else {
            showToast(
                'Signup failed',
                'An account with this email already exists.',
                'destructive'
            );
        }
    } catch (error) {
        showToast(
            'Error',
            'An error occurred. Please try again.',
            'destructive'
        );
    } finally {
        // Reset button state
        formManager.isLoading = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Create Account';
    }
}

// Input change handlers
function setupFormHandlers() {
    const form = document.getElementById('signupForm');
    const inputs = form.querySelectorAll('input');
    
    // Add input event listeners
    inputs.forEach(input => {
        input.addEventListener('input', (e) => {
            const field = e.target.id;
            const value = e.target.value;
            formManager.updateField(field, value);
        });
        
        // Add blur event for validation
        input.addEventListener('blur', (e) => {
            const field = e.target.id;
            const value = e.target.value;
            formManager.validateField(field, value);
        });
    });
    
    // Add form submit handler
    form.addEventListener('submit', handleSubmit);
}

// Real-time validation helpers
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/\s/g, ''));
}

function sanitizeInput(input) {
    return input.trim().replace(/[<>]/g, '');
}

// Password strength checker
function checkPasswordStrength(password) {
    let score = 0;
    const feedback = [];
    
    if (password.length >= 8) {
        score += 1;
    } else {
        feedback.push('Use at least 8 characters');
    }
    
    if (/[A-Z]/.test(password)) {
        score += 1;
    } else {
        feedback.push('Add uppercase letters');
    }
    
    if (/[a-z]/.test(password)) {
        score += 1;
    } else {
        feedback.push('Add lowercase letters');
    }
    
    if (/[0-9]/.test(password)) {
        score += 1;
    } else {
        feedback.push('Add numbers');
    }
    
    if (/[^A-Za-z0-9]/.test(password)) {
        score += 1;
    } else {
        feedback.push('Add special characters');
    }
    
    return { score, feedback };
}

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup form handlers
    setupFormHandlers();
    
    // Initialize sample users if none exist
    if (!localStorage.getItem('users')) {
        const sampleUsers = [
            {
                id: '1',
                name: 'John Doe',
                email: 'user@example.com',
                password: 'password',
                phone: '+1234567890',
                location: 'New York, NY',
                bio: 'Passionate sports enthusiast',
                role: 'Player',
                joinDate: '2024-01-15',
                sports: ['Basketball', 'Tennis'],
                avatar: '👤'
            }
        ];
        localStorage.setItem('users', JSON.stringify(sampleUsers));
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Enter key on form submission
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
            e.preventDefault();
            const form = document.getElementById('signupForm');
            const submitBtn = document.getElementById('submitBtn');
            if (!submitBtn.disabled) {
                handleSubmit(e);
            }
        }
        
        // Escape key to clear form
        if (e.key === 'Escape') {
            const confirmClear = confirm('Are you sure you want to clear the form?');
            if (confirmClear) {
                formManager.reset();
            }
        }
    });
    
    // Auto-focus first input
    const firstInput = document.getElementById('name');
    if (firstInput) {
        firstInput.focus();
    }
    
    console.log('Signup form initialized successfully!');
    console.log('Features available:');
    console.log('- Real-time validation');
    console.log('- Password strength checker');
    console.log('- Toast notifications');
    console.log('- Form persistence');
    console.log('- Keyboard shortcuts (Enter to submit, Escape to clear)');
});

// Utility functions for form enhancement
function debounce(func, wait) {
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

// Enhanced input validation with debouncing
const debouncedValidation = debounce((field, value) => {
    formManager.validateField(field, value);
}, 300);

// Form persistence (save form data to localStorage)
function saveFormData() {
    localStorage.setItem('signupFormData', JSON.stringify(formManager.formData));
}

function loadFormData() {
    const savedData = localStorage.getItem('signupFormData');
    if (savedData) {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach(field => {
            const input = document.getElementById(field);
            if (input && data[field]) {
                input.value = data[field];
                formManager.formData[field] = data[field];
            }
        });
    }
}

// Clear saved form data
function clearSavedFormData() {
    localStorage.removeItem('signupFormData');
}

// Export functions for potential use in other scripts
window.AuthManager = AuthManager;
window.FormManager = FormManager;
window.showToast = showToast;