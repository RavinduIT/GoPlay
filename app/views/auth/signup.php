<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - GoPlay Sports Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/pages/signup.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Branding -->
        <div class="auth-brand">
            <div class="brand-content">
                <img src="/public/assets/images/logo.jpeg" alt="GoPlay" class="brand-logo">
                <h1 class="brand-title">Join GoPlay</h1>
                <p class="brand-subtitle">Start your sports journey with us today</p>
                <div class="brand-features">
                    <div class="feature-item">
                        <i class="fas fa-user-plus"></i>
                        <span>Easy Registration</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-star"></i>
                        <span>Premium Access</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure Platform</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Signup Form -->
        <div class="auth-form-section">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h2 class="auth-title">Create Account</h2>
                    <p class="auth-subtitle">Sign up to start booking sports facilities</p>
                </div>
            
                
                <form id="signupForm" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName" class="form-label">First Name</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input
                                    type="text"
                                    id="firstName"
                                    class="form-input"
                                    placeholder="Enter your first name"
                                    required
                                />
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="lastName" class="form-label">Last Name</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input
                                    type="text"
                                    id="lastName"
                                    class="form-input"
                                    placeholder="Enter your last name"
                                    required
                                />
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input
                                type="email"
                                id="email"
                                class="form-input"
                                placeholder="Enter your email address"
                                required
                            />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input
                                type="tel"
                                id="phone"
                                class="form-input"
                                placeholder="Enter your phone number"
                                required
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="userType" class="form-label">I want to register as</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user-tag input-icon"></i>
                            <select
                                id="userType"
                                class="form-input"
                                required
                            >
                                <option value="">Select your role</option>
                                <option value="user">User </option>
                                <option value="coach">Coach </option>
                                <option value="ground_owner">Ground Owner </option>
                                <option value="shop_owner">Shop Owner </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                class="form-input"
                                placeholder="Create a strong password"
                                required
                            />
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength">
                            <div class="password-strength-bar" id="password-strength-bar"></div>
                        </div>
                        <div class="password-requirements" id="password-requirements">
                            <span class="requirement" data-requirement="length">8+ characters</span>
                            <span class="requirement" data-requirement="uppercase">Uppercase</span>
                            <span class="requirement" data-requirement="lowercase">Lowercase</span>
                            <span class="requirement" data-requirement="number">Number</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="confirmPassword"
                                class="form-input"
                                placeholder="Confirm your password"
                                required
                            />
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye" id="confirm-password-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="btn-text">Create Account</span>
                        <i class="fas fa-user-plus btn-icon"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    <p class="auth-footer-text">
                        Already have an account? 
                        <a href="/login" class="auth-link">Sign In</a>
                    </p>
                </div>
                
               
            </div>
        </div>
    </div>
</div>



<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<script>
// Password toggle functionality
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentNode.querySelector('.password-toggle i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength checker
function checkPasswordStrength(password) {
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password)
    };
    
    const score = Object.values(requirements).filter(Boolean).length;
    return { requirements, score };
}

// Update password requirements UI
function updatePasswordRequirements(requirements) {
    Object.keys(requirements).forEach(req => {
        const element = document.querySelector(`[data-requirement="${req}"]`);
        if (element) {
            element.classList.toggle('met', requirements[req]);
        }
    });
}

// Update password strength bar
function updatePasswordStrength(score) {
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthContainer = document.getElementById('password-strength');
    
    strengthContainer.classList.add('visible');
    
    if (score <= 1) {
        strengthBar.className = 'password-strength-bar weak';
    } else if (score <= 2) {
        strengthBar.className = 'password-strength-bar medium';
    } else {
        strengthBar.className = 'password-strength-bar strong';
    }
}

// Real-time password validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    
    if (password.length === 0) {
        document.getElementById('password-strength').classList.remove('visible');
        return;
    }
    
    const { requirements, score } = checkPasswordStrength(password);
    updatePasswordRequirements(requirements);
    updatePasswordStrength(score);
});

// Form validation and submission
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const userType = document.getElementById('userType').value;
    

    // Validation
    if (!firstName || !lastName || !email || !phone || !userType) {
        showToast('Please fill in all required fields', 'error');
        return;
    }

    const { score } = checkPasswordStrength(password);

    if (score < 3) {
        showToast('Please create a stronger password', 'error');
        return;
    }

    if (password !== confirmPassword) {
        showToast('Passwords do not match', 'error');
        return;
    }

    

    // Disable submit button
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';

    // Send registration request
    fetch('/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            first_name: firstName,
            last_name: lastName,
            email: email,
            phone: phone,
            password: password,
            user_type: userType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Account created successfully! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect || '/';
            }, 1500);
        } else {
            showToast(data.error || 'Registration failed', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="btn-text">Create Account</span><i class="fas fa-user-plus btn-icon"></i>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span class="btn-text">Create Account</span><i class="fas fa-user-plus btn-icon"></i>';
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    toastContainer.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
</body>
</html>