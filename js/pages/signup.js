// Fixed Signup Page JavaScript - Uses main auth.js system
document.addEventListener('DOMContentLoaded', function() {
    // Wait for auth system to be ready
    const waitForAuth = () => {
        if (typeof authInstance !== 'undefined' && authInstance) {
            initializeSignupPage();
        } else {
            setTimeout(waitForAuth, 100);
        }
    };
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

    // Form submission handler
    signupForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = getFormData();
        
        // Validation
        const validation = validateForm(formData);
        if (!validation.isValid) {
            showToast('Validation Error', validation.errors[0], 'destructive');
            return;
        }
        
        // Show loading state
        setLoadingState(true);
        
        try {
            const result = await authInstance.signup(formData);
            
            if (result.success) {
                showToast('Account Created!', 'Welcome to SportHub! You can now explore all features.');
                // Clear form
                clearForm();
                // Redirect to home page
                setTimeout(() => {
                    window.location.href = '../index.html';
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
                }
            }
        });
    });

    // Handle Enter key navigation
    Object.values(inputs).forEach((input, index) => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const nextInput = Object.values(inputs)[index + 1];
                if (nextInput) {
                    nextInput.focus();
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

        // Use the main auth system's validation if available
        if (authInstance && typeof authInstance.validateSignupForm === 'function') {
            return authInstance.validateSignupForm(data);
        }

        // Fallback validation
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

        if (!data.password || data.password.length < 8) {
            errors.push('Password must be at least 8 characters long');
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
                isValid = value.length >= 8;
                errorMessage = 'Password must be at least 8 characters long';
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
        let strengthClass = '';
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        if (strength < 3) {
            strengthClass = 'weak';
            strengthText = 'Weak password';
        } else if (strength < 4) {
            strengthClass = 'medium';
            strengthText = 'Medium strength';
        } else {
            strengthClass = 'strong';
            strengthText = 'Strong password';
        }
        
        strengthIndicator.classList.add(strengthClass);
        strengthIndicator.textContent = strengthText;
        passwordContainer.appendChild(strengthIndicator);
    }

    function setLoadingState(loading) {
        submitBtn.disabled = loading;
        if (loading) {
            submitBtn.innerHTML = '<span class="spinner"></span>Creating account...';
        } else {
            submitBtn.innerHTML = 'Create Account';
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

    // Auto-focus first input
    inputs.name.focus();

    console.log('Signup page initialized successfully!');
}