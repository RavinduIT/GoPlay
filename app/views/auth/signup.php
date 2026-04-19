<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - GoPlay Sports Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/signup.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Branding -->
        <div class="auth-brand">
            <div class="brand-content">
                <img src="<?= $_base ?>/public/assets/images/logo.jpeg" alt="GoPlay" class="brand-logo">
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
                        <a href="<?= $_base ?>/login" class="auth-link">Sign In</a>
                    </p>
                </div>
                
               
            </div>
        </div>
    </div>
</div>



<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<script>
// Password visibility toggle
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentNode.querySelector('.password-toggle i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Password strength checker
function checkPasswordStrength(password) {
    const requirements = {
        length:    password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number:    /\d/.test(password)
    };
    return { requirements, score: Object.values(requirements).filter(Boolean).length };
}

function updatePasswordRequirements(requirements) {
    Object.keys(requirements).forEach(req => {
        const el = document.querySelector(`[data-requirement="${req}"]`);
        if (el) el.classList.toggle('met', requirements[req]);
    });
}

function updatePasswordStrength(score) {
    const bar       = document.getElementById('password-strength-bar');
    const container = document.getElementById('password-strength');
    container.classList.add('visible');
    bar.className = 'password-strength-bar ' + (score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong');
}

document.getElementById('password').addEventListener('input', function () {
    if (!this.value.length) {
        document.getElementById('password-strength').classList.remove('visible');
        return;
    }
    const { requirements, score } = checkPasswordStrength(this.value);
    updatePasswordRequirements(requirements);
    updatePasswordStrength(score);
});

// Form submission
document.getElementById('signupForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const firstName       = document.getElementById('firstName').value.trim();
    const lastName        = document.getElementById('lastName').value.trim();
    const email           = document.getElementById('email').value.trim();
    const phone           = document.getElementById('phone').value.trim();
    const password        = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const submitBtn       = document.getElementById('submitBtn');

    // Client-side validation
    if (!firstName || !lastName || !email || !phone) {
        showToast('Please fill in all required fields before continuing.', 'warning', 'Fields Required');
        return;
    }

    if (!email.includes('@') || !email.includes('.')) {
        showToast('Please enter a valid email address.', 'error', 'Invalid Email');
        document.getElementById('email').focus();
        return;
    }

    const { score } = checkPasswordStrength(password);
    if (score < 3) {
        showToast('Use 8+ characters with uppercase, lowercase, and a number.', 'warning', 'Weak Password');
        document.getElementById('password').focus();
        return;
    }

    if (password !== confirmPassword) {
        showToast('Your passwords do not match. Please re-enter them.', 'error', 'Password Mismatch');
        document.getElementById('confirmPassword').value = '';
        document.getElementById('confirmPassword').focus();
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';

    try {
        const response = await fetch((window.BASE_URL || '') + '/auth/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                first_name: firstName,
                last_name:  lastName,
                email:      email,
                phone:      phone,
                password:   password,
                user_type:  'user'
            })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Your account has been created! Redirecting...', 'success', 'Account Created!');
            setTimeout(() => {
                window.location.href = data.redirect || (window.BASE_URL || '') + '/';
            }, 1600);
            return;
        }

        // Map server messages to specific toasts
        const msg = (data.message || '').toLowerCase();
        if (msg.includes('email already registered') || msg.includes('email already')) {
            showToast('This email is already registered. Try signing in instead.', 'error', 'Email Taken');
        } else if (msg.includes('username already')) {
            showToast('This username is already in use. Please choose another.', 'warning', 'Username Taken');
        } else if (msg.includes('password must be at least')) {
            showToast('Password must be at least 8 characters long.', 'warning', 'Weak Password');
        } else if (msg.includes('invalid email')) {
            showToast('Please enter a valid email address.', 'error', 'Invalid Email');
        } else if (msg.includes('failed to create')) {
            showToast('Failed to create your account. Please try again.', 'error', 'Registration Failed');
        } else {
            showToast(data.message || 'Registration failed. Please try again.', 'error', 'Registration Failed');
        }

    } catch (error) {
        console.error('Registration error:', error);
        showToast('Unable to connect. Please check your internet connection.', 'error', 'Connection Error');
    }

    submitBtn.disabled = false;
    submitBtn.innerHTML = '<span class="btn-text">Create Account</span><i class="fas fa-user-plus btn-icon"></i>';
});

// ── Toast system ─────────────────────────────────────────────────
function showToast(message, type = 'info', title = null) {
    const config = {
        success: { icon: 'fa-check-circle',        title: title || 'Success' },
        error:   { icon: 'fa-times-circle',         title: title || 'Error'   },
        warning: { icon: 'fa-exclamation-triangle', title: title || 'Warning' },
        info:    { icon: 'fa-info-circle',          title: title || 'Info'    }
    };
    const { icon, title: toastTitle } = config[type] || config.info;
    const duration = 4500;

    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.setProperty('--toast-duration', `${duration / 1000}s`);
    toast.innerHTML = `
        <i class="fas ${icon} toast-icon"></i>
        <div class="toast-body">
            <div class="toast-title">${toastTitle}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" aria-label="Dismiss"><i class="fas fa-times"></i></button>
        <div class="toast-progress"></div>
    `;

    toast.querySelector('.toast-close').addEventListener('click', () => dismissToast(toast));
    container.appendChild(toast);

    requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.add('show')));

    toast._timer = setTimeout(() => dismissToast(toast), duration);
}

function dismissToast(toast) {
    if (!toast || toast._dismissed) return;
    toast._dismissed = true;
    clearTimeout(toast._timer);
    toast.classList.add('hiding');
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 280);
}
</script>
</body>
</html>