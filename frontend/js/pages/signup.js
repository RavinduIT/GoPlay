// Fixed Signup Page JavaScript - Error-Free Version
let signupFormInitialized = false;
let initRetryCount = 0;
const maxRetries = 10;

function initializeSignupPage() {
    if (signupFormInitialized) return;
    
    console.log('Initializing signup page...');
    
    const signupForm = document.getElementById('signupForm');
    const inputs = {
        name: document.getElementById('name'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        location: document.getElementById('location'),
        password: document.getElementById('password'),
        confirmPassword: document.getElementById('confirmPassword')
    };
    const submitBtn = document.getElementById('submitBtn');

    // Check if all elements exist
    if (!signupForm || !submitBtn) {
        console.warn('Required form elements not found, retrying...');
        initRetryCount++;
        if (initRetryCount < maxRetries) {
            setTimeout(initializeSignupPage, 200);
        } else {
            console.error('Failed to find form elements after maximum retries');
        }
        return;
    }

    // Check if all inputs exist
    for (const [key, input] of Object.entries(inputs)) {
        if (!input) {
            console.warn(`Input ${key} not found, retrying...`);
            initRetryCount++;
            if (initRetryCount < maxRetries) {
                setTimeout(initializeSignupPage, 200);
            } else {
                console.error(`Failed to find input ${key} after maximum retries`);
            }
            return;
        }
    }

    signupFormInitialized = true;
    console.log('Signup page elements found, setting up handlers');

    // Form submission handler with comprehensive error handling
    signupForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Signup form submitted');
        
        const formData = getFormData();
        console.log('Form data:', { ...formData, password: '[HIDDEN]', confirmPassword: '[HIDDEN]' });
        
        // Validation
        const validation = validateForm(formData);
        if (!validation.isValid) {
            console.log('Validation failed:', validation.errors);
            safeShowToast('Validation Error', validation.errors[0], 'destructive');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        try {
            console.log('Calling signup function...');
            
            // Ensure auth system is available with timeout
            const signupResult = await waitForAuthAndSignup(formData);
            console.log('Signup result:', signupResult);
            
            if (signupResult && signupResult.success) {
                safeShowToast('Account Created!', 'Welcome to SportHub! You can now explore all features.');
                
                // Clear form
                clearForm();
                
                // Redirect after a short delay
                setTimeout(() => {
                    redirectToHome();
                }, 2000);
            } else {
                const errorMessage = signupResult && signupResult.error ? signupResult.error : 'An account with this email already exists.';
                safeShowToast('Signup Failed', errorMessage, 'destructive');
            }
        } catch (error) {
            console.error('Signup error:', error);
            safeShowToast('Error', 'An unexpected error occurred. Please try again.', 'destructive');
        } finally {
            setLoadingState(false);
        }
    });

    // Real-time validation for each field with error handling
    Object.keys(inputs).forEach(fieldName => {
        const input = inputs[fieldName];
        
        input.addEventListener('blur', function() {
            try {
                validateField(fieldName, this.value.trim());
            } catch (error) {
                console.warn(`Validation error for ${fieldName}:`, error);
            }
        });

        input.addEventListener('input', function() {
            try {
                this.classList.remove('error', 'success');
                clearFieldError(this);
                
                // Special handling for password strength
                if (fieldName === 'password') {
                    updatePasswordStrength(this.value);
                }
                
                // Special handling for confirm password
                if (fieldName === 'confirmPassword') {
                    const password = inputs.password.value;
                    if (this.value && password && this.value === password) {
                        this.classList.add('success');
                    } else if (this.value && password && this.value !== password) {
                        this.classList.add('error');
                        showFieldError(this, 'Passwords do not match');
                    }
                }
            } catch (error) {
                console.warn(`Input handler error for ${fieldName}:`, error);
            }
        });
    });

    // Handle Enter key navigation with error handling
    const inputOrder = ['name', 'email', 'phone', 'location', 'password', 'confirmPassword'];
    inputOrder.forEach((fieldName, index) => {
        const input = inputs[fieldName];
        input.addEventListener('keypress', function(e) {
            try {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const nextField = inputOrder[index + 1];
                    if (nextField && inputs[nextField]) {
                        inputs[nextField].focus();
                    } else {
                        signupForm.dispatchEvent(new Event('submit'));
                    }
                }
            } catch (error) {
                console.warn('Enter key handler error:', error);
            }
        });
    });

    function getFormData() {
        try {
            return {
                name: inputs.name.value.trim(),
                email: inputs.email.value.trim(),
                phone: inputs.phone.value.trim(),
                location: inputs.location.value.trim(),
                password: inputs.password.value,
                confirmPassword: inputs.confirmPassword.value
            };
        } catch (error) {
            console.error('Form data collection error:', error);
            return {};
        }
    }

    function validateForm(data) {
        try {
            const errors = [];

            if (!data.name || data.name.length < 2) {
                errors.push('Name must be at least 2 characters long');
            }

            if (!validateEmail(data.email)) {
                errors.push('Please enter a valid email address');
            }

            if (!validatePhone(data.phone)) {
                errors.push('Please enter a valid phone number');
            }

            if (!data.location || data.location.length < 2) {
                errors.push('Location must be at least 2 characters long');
            }

            if (!data.password || data.password.length < 6) {
                errors.push('Password must be at least 6 characters long');
            }

            if (data.password !== data.confirmPassword) {
                errors.push('Passwords do not match');
            }

            return {
                isValid: errors.length === 0,
                errors: errors
            };
        } catch (error) {
            console.error('Form validation error:', error);
            return {
                isValid: false,
                errors: ['Validation error occurred']
            };
        }
    }

    function validateField(fieldName, value) {
        try {
            const input = inputs[fieldName];
            if (!input) return false;
            
            let isValid = true;
            let errorMessage = '';

            switch (fieldName) {
                case 'name':
                    isValid = value.length >= 2;
                    errorMessage = 'Name must be at least 2 characters long';
                    break;
                case 'email':
                    isValid = validateEmail(value);
                    errorMessage = 'Please enter a valid email address';
                    break;
                case 'phone':
                    isValid = validatePhone(value);
                    errorMessage = 'Please enter a valid phone number';
                    break;
                case 'location':
                    isValid = value.length >= 2;
                    errorMessage = 'Location must be at least 2 characters long';
                    break;
                case 'password':
                    isValid = value.length >= 6;
                    errorMessage = 'Password must be at least 6 characters long';
                    break;
                case 'confirmPassword':
                    isValid = value === inputs.password.value;
                    errorMessage = 'Passwords do not match';
                    break;
            }

            if (value) {
                if (isValid) {
                    input.classList.remove('error');
                    input.classList.add('success');
                    clearFieldError(input);
                } else {
                    input.classList.remove('success');
                    input.classList.add('error');
                    showFieldError(input, errorMessage);
                }
            }

            return isValid;
        } catch (error) {
            console.warn(`Field validation error for ${fieldName}:`, error);
            return false;
        }
    }

    function updatePasswordStrength(password) {
        try {
            // Remove existing indicator
            const existingIndicator = document.querySelector('.password-strength');
            if (existingIndicator) {
                existingIndicator.remove();
            }
            
            if (!password) return;
            
            const passwordContainer = inputs.password.parentElement.parentElement;
            if (!passwordContainer) return;
            
            const strengthIndicator = document.createElement('div');
            strengthIndicator.className = 'password-strength';
            
            let strength = 0;
            let strengthText = '';
            let color = '';
            
            if (password.length >= 6) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            if (strength < 3) {
                color = '#ef4444';
                strengthText = 'Weak password';
            } else if (strength < 4) {
                color = '#f59e0b';
                strengthText = 'Medium strength';
            } else {
                color = '#10b981';
                strengthText = 'Strong password';
            }
            
            strengthIndicator.style.cssText = `
                color: ${color};
                font-size: 12px;
                margin-top: 4px;
                font-weight: 500;
            `;
            
            strengthIndicator.textContent = strengthText;
            passwordContainer.appendChild(strengthIndicator);
        } catch (error) {
            console.warn('Password strength update error:', error);
        }
    }

    function setLoadingState(loading) {
        try {
            submitBtn.disabled = loading;
            if (loading) {
                submitBtn.innerHTML = '<div style="display: inline-block; width: 16px; height: 16px; border: 2px solid #ffffff; border-radius: 50%; border-top-color: transparent; animation: spin 1s linear infinite; margin-right: 8px;"></div>Creating account...';
                submitBtn.style.opacity = '0.8';
            } else {
                submitBtn.innerHTML = 'Create Account';
                submitBtn.style.opacity = '1';
            }
        } catch (error) {
            console.warn('Loading state error:', error);
        }
    }

    function clearForm() {
        try {
            Object.values(inputs).forEach(input => {
                if (input) {
                    input.value = '';
                    input.classList.remove('error', 'success');
                }
            });
            clearAllFieldErrors();
            
            const strengthIndicator = document.querySelector('.password-strength');
            if (strengthIndicator) {
                strengthIndicator.remove();
            }
        } catch (error) {
            console.warn('Clear form error:', error);
        }
    }

    function showFieldError(input, message) {
        try {
            clearFieldError(input);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = message;
            errorDiv.style.cssText = `
                color: #ef4444;
                font-size: 14px;
                margin-top: 4px;
                font-weight: 400;
            `;
            
            const container = input.parentElement && input.parentElement.parentElement;
            if (container) {
                container.appendChild(errorDiv);
            }
        } catch (error) {
            console.warn('Show field error failed:', error);
        }
    }

    function clearFieldError(input) {
        try {
            const container = input.parentElement && input.parentElement.parentElement;
            if (container) {
                const errorDiv = container.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        } catch (error) {
            console.warn('Clear field error failed:', error);
        }
    }

    function clearAllFieldErrors() {
        try {
            document.querySelectorAll('.field-error').forEach(error => error.remove());
        } catch (error) {
            console.warn('Clear all field errors failed:', error);
        }
    }

    function validateEmail(email) {
        try {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        } catch (error) {
            console.warn('Email validation error:', error);
            return false;
        }
    }

    function validatePhone(phone) {
        try {
            const phoneRegex = /^\+?[1-9]\d{1,14}$/;
            return phoneRegex.test(phone.replace(/[\s()-]/g, ''));
        } catch (error) {
            console.warn('Phone validation error:', error);
            return false;
        }
    }

    // Auto-focus first input
    try {
        inputs.name.focus();
    } catch (error) {
        console.warn('Focus error:', error);
    }

    console.log('Signup page initialized successfully!');
}

// Wait for auth system with timeout
async function waitForAuthAndSignup(formData, timeout = 5000) {
    return new Promise((resolve, reject) => {
        const startTime = Date.now();
        
        const checkAuth = async () => {
            try {
                // Check if signup function is available
                if (typeof signup === 'function') {
                    const result = await signup(formData);
                    resolve(result);
                    return;
                }
                
                // Check if auth instance is available
                if (window.authInstance && typeof window.authInstance.signup === 'function') {
                    const result = await window.authInstance.signup(formData);
                    resolve(result);
                    return;
                }
                
                // Check timeout
                if (Date.now() - startTime > timeout) {
                    reject(new Error('Auth system not available after timeout'));
                    return;
                }
                
                // Retry
                setTimeout(checkAuth, 100);
            } catch (error) {
                reject(error);
            }
        };
        
        checkAuth();
    });
}

function redirectToHome() {
    try {
        const currentPath = window.location.pathname;
        if (currentPath.includes('/pages/')) {
            window.location.href = '../index.html';
        } else {
            window.location.href = 'index.html';
        }
    } catch (error) {
        console.error('Redirect error:', error);
        // Fallback
        window.location.href = '/';
    }
}

// Enhanced toast function with multiple fallbacks
function safeShowToast(title, description, variant = 'default') {
    console.log('Showing toast:', title, description);
    
    try {
        // Try global showToast function first
        if (typeof window.showToast === 'function') {
            window.showToast(title, description, variant);
            return;
        }
        
        // Try auth instance directly
        if (window.authInstance && typeof window.authInstance.showToast === 'function') {
            window.authInstance.showToast(title, description, variant);
            return;
        }
        
        // Fallback: create simple toast
        createSimpleToast(title, description, variant);
    } catch (error) {
        console.warn('Toast error, using fallback:', error);
        createSimpleToast(title, description, variant);
    }
}

function createSimpleToast(title, description, variant) {
    try {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.simple-toast');
        existingToasts.forEach(toast => {
            try {
                toast.remove();
            } catch (e) {
                console.warn('Error removing toast:', e);
            }
        });

        const toast = document.createElement('div');
        toast.className = 'simple-toast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${variant === 'destructive' ? '#ef4444' : '#10b981'};
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            max-width: 400px;
            font-family: inherit;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        // Escape HTML to prevent XSS
        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };
        
        toast.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 4px; font-size: 16px;">${escapeHtml(title)}</div>
            <div style="font-size: 14px; opacity: 0.9;">${escapeHtml(description)}</div>
        `;
        
        document.body.appendChild(toast);
        
        // Show toast with animation
        setTimeout(() => {
            try {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            } catch (e) {
                console.warn('Toast animation error:', e);
            }
        }, 100);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            try {
                if (toast.parentNode) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        try {
                            if (toast.parentNode) {
                                toast.remove();
                            }
                        } catch (e) {
                            console.warn('Toast removal error:', e);
                        }
                    }, 300);
                }
            } catch (e) {
                console.warn('Toast timeout error:', e);
            }
        }, 4000);
    } catch (error) {
        console.error('Simple toast creation failed:', error);
        // Ultimate fallback - native alert
        alert(`${title}: ${description}`);
    }
}

// Add spinner animation CSS if not exists
function addSpinnerCSS() {
    try {
        if (!document.querySelector('#spinner-style')) {
            const style = document.createElement('style');
            style.id = 'spinner-style';
            style.textContent = `
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
    } catch (error) {
        console.warn('CSS addition error:', error);
    }
}

// Wait for auth system and DOM with robust checking
function waitForAuthAndInit() {
    const checkAuth = () => {
        try {
            const hasAuth = typeof signup === 'function' || (window.authInstance && typeof window.authInstance.signup === 'function');
            const domReady = document.readyState !== 'loading';
            
            if (hasAuth && domReady) {
                console.log('Auth system ready, initializing signup page');
                initializeSignupPage();
            } else {
                console.log('Waiting for auth system or DOM...');
                setTimeout(checkAuth, 100);
            }
        } catch (error) {
            console.error('Auth check error:', error);
            setTimeout(checkAuth, 200);
        }
    };
    checkAuth();
}

// Multiple initialization strategies with error handling
try {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', waitForAuthAndInit);
    } else {
        waitForAuthAndInit();
    }
} catch (error) {
    console.error('Event listener error:', error);
    setTimeout(waitForAuthAndInit, 500);
}

// Listen for auth ready event
try {
    window.addEventListener('authReady', () => {
        console.log('Auth ready event received');
        if (document.readyState !== 'loading') {
            initializeSignupPage();
        }
    });
} catch (error) {
    console.warn('Auth ready listener error:', error);
}

// Backup initialization with multiple attempts
let backupInitCount = 0;
function backupInit() {
    if (!signupFormInitialized && backupInitCount < 3) {
        console.log('Backup initialization triggered, attempt:', backupInitCount + 1);
        backupInitCount++;
        waitForAuthAndInit();
        setTimeout(backupInit, 2000);
    }
}

setTimeout(backupInit, 1000);

// Add CSS when script loads
addSpinnerCSS();