// Fixed Login Page JavaScript - Uses main auth.js system
document.addEventListener('DOMContentLoaded', function() {
    // Wait for auth system to be ready
    const waitForAuth = () => {
        if (typeof authInstance !== 'undefined' && authInstance) {
            initializeLoginPage();
        } else {
            setTimeout(waitForAuth, 100);
        }
    };
    waitForAuth();
});

function initializeLoginPage() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');

    // Form submission handler
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        
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
            const result = await authInstance.login(email, password);
            
            if (result.success) {
                showToast('Welcome back!', 'You have successfully logged in.');
                // Redirect to home page after successful login
                setTimeout(() => {
                    window.location.href = '../index.html';
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

    // Handle Enter key
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loginForm.dispatchEvent(new Event('submit'));
            }
        });
    });

    function setLoadingState(loading) {
        submitBtn.disabled = loading;
        if (loading) {
            submitBtn.innerHTML = '<span class="spinner"></span>Signing in...';
        } else {
            submitBtn.innerHTML = 'Login';
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
        input.parentElement.parentElement.appendChild(errorDiv);
    }

    function clearFieldError(input) {
        const errorDiv = input.parentElement.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Toast function (simplified - uses the auth system's toast if available)
    function showToast(title, description, variant = 'default') {
        // Try to use the main auth system's toast first
        if (typeof window.showGlobalToast === 'function') {
            window.showGlobalToast(title, description, variant);
            return;
        }

        // Fallback toast implementation
        const toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) return;
        
        const toast = document.createElement('div');
        toast.className = `toast ${variant === 'destructive' ? 'toast-destructive' : ''}`;
        
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-description">${description}</div>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        setTimeout(() => toast.classList.add('show'), 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 4000);
    }

    // Auto-focus email input
    emailInput.focus();

    console.log('Login page initialized successfully!');
}