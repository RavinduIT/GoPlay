// Fixed Login Page JavaScript - Error-Free Version
let loginFormInitialized = false;
let initRetryCount = 0;
const maxRetries = 10;

function initializeLoginPage() {
    if (loginFormInitialized) return;
    
    console.log('Initializing login page...');
    
    // Check for required elements
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');

    if (!loginForm || !emailInput || !passwordInput || !submitBtn) {
        console.warn('Required form elements not found, retrying...');
        initRetryCount++;
        if (initRetryCount < maxRetries) {
            setTimeout(initializeLoginPage, 200); // Retry with delay
        } else {
            console.error('Failed to find form elements after maximum retries');
        }
        return;
    }

    loginFormInitialized = true;
    console.log('Login page elements found, setting up handlers');

    // Form submission handler with comprehensive error handling
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Login form submitted');
        
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        
        console.log('Login attempt for email:', email);
        
        // Basic validation
        if (!email || !password) {
            safeShowToast('Validation Error', 'Please fill in all fields.', 'destructive');
            return;
        }

        if (!validateEmail(email)) {
            safeShowToast('Invalid Email', 'Please enter a valid email address.', 'destructive');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        try {
            console.log('Calling login function...');
            
            // Ensure auth system is available with timeout
            const loginResult = await waitForAuthAndLogin(email, password);
            console.log('Login result:', loginResult);
            
            if (loginResult && loginResult.success) {
                safeShowToast('Welcome back!', 'You have successfully logged in.');
                
                // Clear form
                emailInput.value = '';
                passwordInput.value = '';
                
                // Redirect after a short delay
                setTimeout(() => {
                    redirectToHome();
                }, 1500);
            } else {
                const errorMessage = loginResult && loginResult.error ? loginResult.error : 'Invalid email or password.';
                safeShowToast('Login Failed', errorMessage, 'destructive');
            }
        } catch (error) {
            console.error('Login error:', error);
            safeShowToast('Error', 'An unexpected error occurred. Please try again.', 'destructive');
        } finally {
            setLoadingState(false);
        }
    });

    // Real-time validation with error handling
    emailInput.addEventListener('blur', function() {
        try {
            const email = this.value.trim();
            if (email && !validateEmail(email)) {
                this.classList.add('error');
                showFieldError(this, 'Please enter a valid email address');
            } else {
                this.classList.remove('error');
                clearFieldError(this);
                if (email) this.classList.add('success');
            }
        } catch (error) {
            console.warn('Email validation error:', error);
        }
    });

    // Clear validation on input
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('input', function() {
            try {
                this.classList.remove('error', 'success');
                clearFieldError(this);
            } catch (error) {
                console.warn('Input clear error:', error);
            }
        });
    });

    // Handle Enter key navigation
    emailInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            passwordInput.focus();
        }
    });

    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            loginForm.dispatchEvent(new Event('submit'));
        }
    });

    // Auto-focus email input
    try {
        emailInput.focus();
    } catch (error) {
        console.warn('Focus error:', error);
    }

    console.log('Login page initialized successfully!');
}

// Wait for auth system with timeout
async function waitForAuthAndLogin(email, password, timeout = 5000) {
    return new Promise((resolve, reject) => {
        const startTime = Date.now();
        
        const checkAuth = async () => {
            try {
                // Check if login function is available
                if (typeof login === 'function') {
                    const result = await login(email, password);
                    resolve(result);
                    return;
                }
                
                // Check if auth instance is available
                if (window.authInstance && typeof window.authInstance.login === 'function') {
                    const result = await window.authInstance.login(email, password);
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

// Helper functions with error handling
function setLoadingState(loading) {
    try {
        const submitBtn = document.getElementById('submitBtn');
        if (!submitBtn) return;
        
        submitBtn.disabled = loading;
        if (loading) {
            submitBtn.innerHTML = '<div style="display: inline-block; width: 16px; height: 16px; border: 2px solid #ffffff; border-radius: 50%; border-top-color: transparent; animation: spin 1s linear infinite; margin-right: 8px;"></div>Signing in...';
            submitBtn.style.opacity = '0.8';
        } else {
            submitBtn.innerHTML = 'Login';
            submitBtn.style.opacity = '1';
        }
    } catch (error) {
        console.warn('Loading state error:', error);
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
            const hasAuth = typeof login === 'function' || (window.authInstance && typeof window.authInstance.login === 'function');
            const domReady = document.readyState !== 'loading';
            
            if (hasAuth && domReady) {
                console.log('Auth system ready, initializing login page');
                initializeLoginPage();
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
            initializeLoginPage();
        }
    });
} catch (error) {
    console.warn('Auth ready listener error:', error);
}

// Backup initialization with multiple attempts
let backupInitCount = 0;
function backupInit() {
    if (!loginFormInitialized && backupInitCount < 3) {
        console.log('Backup initialization triggered, attempt:', backupInitCount + 1);
        backupInitCount++;
        waitForAuthAndInit();
        setTimeout(backupInit, 2000);
    }
}

setTimeout(backupInit, 1000);

// Add CSS when script loads
addSpinnerCSS();