// Fixed Login Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Login page loading...');
    
    // Wait for auth system to be ready
    const waitForAuth = () => {
        if (typeof authInstance !== 'undefined' && authInstance && authInstance.isInitialized) {
            console.log('Auth system ready, initializing login page');
            initializeLoginPage();
        } else {
            console.log('Waiting for auth system...');
            setTimeout(waitForAuth, 100);
        }
    };
    
    // Start waiting for auth
    waitForAuth();
});

function initializeLoginPage() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');

    if (!loginForm || !emailInput || !passwordInput || !submitBtn) {
        console.error('Required form elements not found');
        return;
    }

    console.log('Login page elements found, setting up handlers');

    // Form submission handler
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Login form submitted');
        
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        
        console.log('Login attempt for email:', email);
        
        // Basic validation
        if (!email || !password) {
            showToast('Validation Error', 'Please fill in all fields.', 'destructive');
            return;
        }

        if (!validateEmail(email)) {
            showToast('Invalid Email', 'Please enter a valid email address.', 'destructive');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        try {
            console.log('Calling login function...');
            const result = await login(email, password);
            console.log('Login result:', result);
            
            if (result.success) {
                showToast('Welcome back!', 'You have successfully logged in.');
                
                // Clear form
                emailInput.value = '';
                passwordInput.value = '';
                
                // Redirect after a short delay
                setTimeout(() => {
                    // Check if we're in a subdirectory
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('/pages/')) {
                        window.location.href = '../index.html';
                    } else {
                        window.location.href = 'index.html';
                    }
                }, 1500);
            } else {
                showToast('Login Failed', result.error || 'Invalid email or password.', 'destructive');
            }
        } catch (error) {
            console.error('Login error:', error);
            showToast('Error', 'An unexpected error occurred. Please try again.', 'destructive');
        } finally {
            setLoadingState(false);
        }
    });

    // Real-time validation
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        if (email && !validateEmail(email)) {
            this.classList.add('error');
            showFieldError(this, 'Please enter a valid email address');
        } else {
            this.classList.remove('error');
            clearFieldError(this);
            if (email) this.classList.add('success');
        }
    });

    // Clear validation on input
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('error', 'success');
            clearFieldError(this);
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

    // Helper functions
    function setLoadingState(loading) {
        submitBtn.disabled = loading;
        if (loading) {
            submitBtn.innerHTML = '<div class="spinner"></div>Signing in...';
            submitBtn.style.opacity = '0.8';
        } else {
            submitBtn.innerHTML = 'Login';
            submitBtn.style.opacity = '1';
        }
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showFieldError(input, message) {
        clearFieldError(input);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.color = '#ef4444';
        errorDiv.style.fontSize = '14px';
        errorDiv.style.marginTop = '4px';
        input.parentElement.parentElement.appendChild(errorDiv);
    }

    function clearFieldError(input) {
        const errorDiv = input.parentElement.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Toast function with fallback
    function showToast(title, description, variant = 'default') {
        console.log('Showing toast:', title, description);
        
        // Try to use the global auth system's toast first
        if (typeof window.showGlobalToast === 'function') {
            window.showGlobalToast(title, description, variant);
            return;
        }

        // Try auth instance directly
        if (authInstance && authInstance.showToast) {
            authInstance.showToast(title, description, variant);
            return;
        }

        // Fallback: simple alert
        if (variant === 'destructive') {
            alert('Error: ' + description);
        } else {
            alert(title + ': ' + description);
        }
        
        // Try to create a simple toast
        createSimpleToast(title, description, variant);
    }

    function createSimpleToast(title, description, variant) {
        // Remove existing toasts
        const existingToasts = document.querySelectorAll('.simple-toast');
        existingToasts.forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = 'simple-toast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${variant === 'destructive' ? '#ef4444' : '#10b981'};
            color: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            max-width: 400px;
            font-family: inherit;
        `;
        
        toast.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 4px;">${title}</div>
            <div style="font-size: 14px; opacity: 0.9;">${description}</div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 4000);
    }

    // Auto-focus email input
    emailInput.focus();

    console.log('Login page initialized successfully!');
}