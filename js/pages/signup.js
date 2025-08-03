// Fixed Signup Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Signup page loading...');
    
    // Wait for auth system to be ready
    const waitForAuth = () => {
        if (typeof authInstance !== 'undefined' && authInstance && authInstance.isInitialized) {
            console.log('Auth system ready, initializing signup page');
            initializeSignupPage();
        } else {
            console.log('Waiting for auth system...');
            setTimeout(waitForAuth, 100);
        }
    };
    
    // Start waiting for auth
    waitForAuth();
});

function initializeSignupPage() {
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
        console.error('Required form elements not found');
        return;
    }

    // Check if all inputs exist
    for (const [key, input] of Object.entries(inputs)) {
        if (!input) {
            console.error(`Input ${key} not found`);
            return;
        }
    }

    console.log('Signup page elements found, setting up handlers');

    // Form submission handler
    signupForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Signup form submitted');
        
        const formData = getFormData();
        console.log('Form data:', { ...formData, password: '[HIDDEN]', confirmPassword: '[HIDDEN]' });
        
        // Validation
        const validation = validateForm(formData);
        if (!validation.isValid) {
            console.log('Validation failed:', validation.errors);
            showToast('Validation Error', validation.errors[0], 'destructive');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        try {
            console.log('Calling signup function...');
            const result = await signup(formData);
            console.log('Signup result:', result);
            
            if (result.success) {
                showToast('Account Created!', 'Welcome to SportHub! You can now explore all features.');
                
                // Clear form
                clearForm();
                
                // Redirect after a short delay
                setTimeout(() => {
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('/pages/')) {
                        window.location.href = '../index.html';
                    } else {
                        window.location.href = 'index.html';
                    }
                }, 2000);
            } else {
                showToast('Signup Failed', result.error || 'An account with this email already exists.', 'destructive');
            }
        } catch (error) {
            console.error('Signup error:', error);
            showToast('Error', 'An unexpected error occurred. Please try again.', 'destructive');
        } finally {
            setLoadingState(false);
        }
    });

    // Real-time validation for each field
    Object.keys(inputs).forEach(fieldName => {
        const input = inputs[fieldName];
        
        input.addEventListener('blur', function() {
            validateField(fieldName, this.value.trim());
        });

        input.addEventListener('input', function() {
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
        });
    });

    // Handle Enter key navigation
    const inputOrder = ['name', 'email', 'phone', 'location', 'password', 'confirmPassword'];
    inputOrder.forEach((fieldName, index) => {
        const input = inputs[fieldName];
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const nextField = inputOrder[index + 1];
                if (nextField && inputs[nextField]) {
                    inputs[nextField].focus();
                } else {
                    signupForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    });

    function getFormData() {
        return {
            name: inputs.name.value.trim(),
            email: inputs.email.value.trim(),
            phone: inputs.phone.value.trim(),
            location: inputs.location.value.trim(),
            password: inputs.password.value,
            confirmPassword: inputs.confirmPassword.value
        };
    }

    function validateForm(data) {
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
    }

    function validateField(fieldName, value) {
        const input = inputs[fieldName];
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
    }

    function updatePasswordStrength(password) {
        // Remove existing indicator
        const existingIndicator = document.querySelector('.password-strength');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        if (!password) return;
        
        const passwordContainer = inputs.password.parentElement.parentElement;
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
    }

    function setLoadingState(loading) {
        submitBtn.disabled = loading;
        if (loading) {
            submitBtn.innerHTML = '<div class="spinner"></div>Creating account...';
            submitBtn.style.opacity = '0.8';
        } else {
            submitBtn.innerHTML = 'Create Account';
            submitBtn.style.opacity = '1';
        }
    }

    function clearForm() {
        Object.values(inputs).forEach(input => {
            input.value = '';
            input.classList.remove('error', 'success');
        });
        clearAllFieldErrors();
        
        const strengthIndicator = document.querySelector('.password-strength');
        if (strengthIndicator) {
            strengthIndicator.remove();
        }
    }

    function showFieldError(input, message) {
        clearFieldError(input);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            color: #ef4444;
            font-size: 14px;
            margin-top: 4px;
        `;
        input.parentElement.parentElement.appendChild(errorDiv);
    }

    function clearFieldError(input) {
        const errorDiv = input.parentElement.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    function clearAllFieldErrors() {
        document.querySelectorAll('.field-error').forEach(error => error.remove());
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validatePhone(phone) {
        const phoneRegex = /^\+?[1-9]\d{1,14}$/;
        return phoneRegex.test(phone.replace(/[\s()-]/g, ''));
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
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        toast.innerHTML = `
            <div style="font-weight: 600; margin-bottom: 4px;">${title}</div>
            <div style="font-size: 14px; opacity: 0.9;">${description}</div>
        `;
        
        document.body.appendChild(toast);
        
        // Show toast with animation
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        }, 100);
        
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

    // Auto-focus first input
    inputs.name.focus();

    console.log('Signup page initialized successfully!');
}