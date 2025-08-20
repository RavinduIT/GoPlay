<?php 
$title = 'Sign Up - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/signup.css'];
$additionalJS = ['/public/js/pages/signup.js'];
?>

<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Image -->
        <div class="auth-image">
            <img src="/public/assets/images/hero-background.jpg" alt="Sports" class="auth-bg">
            <div class="auth-overlay">
                <h2>Join GoPlay!</h2>
                <p>Start your sports journey today</p>
            </div>
        </div>
        
        <!-- Right Side - Signup Form -->
        <div class="auth-form-container">
            <div class="auth-form">
                <!-- Logo -->
                <div class="auth-header">
                    <img src="/public/assets/images/logo.jpeg" alt="GoPlay" class="auth-logo">
                    <h1>Create Account</h1>
                    <p>Join the GoPlay community</p>
                </div>

                <!-- Signup Form -->
                <form id="signupForm" class="form" method="POST" action="/auth/register">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="first_name" required class="form-input">
                            <span class="form-error" id="firstName-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="last_name" required class="form-input">
                            <span class="form-error" id="lastName-error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required class="form-input">
                        <span class="form-error" id="email-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required class="form-input" placeholder="+94 XX XXX XXXX">
                        <span class="form-error" id="phone-error"></span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-input">
                                <input type="password" id="password" name="password" required class="form-input">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <span class="toggle-icon">👁️</span>
                                </button>
                            </div>
                            <span class="form-error" id="password-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password</label>
                            <div class="password-input">
                                <input type="password" id="confirmPassword" name="confirm_password" required class="form-input">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                    <span class="toggle-icon">👁️</span>
                                </button>
                            </div>
                            <span class="form-error" id="confirmPassword-error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="userType">I am a</label>
                        <select id="userType" name="user_type" required class="form-select">
                            <option value="">Select user type</option>
                            <option value="customer">Sports Enthusiast</option>
                            <option value="coach">Coach</option>
                            <option value="facility_owner">Facility Owner</option>
                        </select>
                        <span class="form-error" id="userType-error"></span>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" id="terms" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>
                        </label>
                        <span class="form-error" id="terms-error"></span>
                    </div>

                    <!-- Marketing Consent -->
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="marketing" id="marketing">
                            <span class="checkmark"></span>
                            Send me updates about new features and promotions
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Create Account</button>
                </form>

                <!-- Alternative Signup -->
                <div class="auth-divider">
                    <span>Or sign up with</span>
                </div>

                <div class="social-login">
                    <button class="btn btn-social btn-google">
                        <img src="/public/assets/icons/google.svg" alt="Google">
                        Google
                    </button>
                    <button class="btn btn-social btn-facebook">
                        <img src="/public/assets/icons/facebook.svg" alt="Facebook">
                        Facebook
                    </button>
                </div>

                <!-- Login Link -->
                <div class="auth-footer">
                    <p>Already have an account? <a href="/login" class="auth-link">Sign in here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('.toggle-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = '🙈';
    } else {
        input.type = 'password';
        icon.textContent = '👁️';
    }
}

// Form validation
document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Password validation
    if (password.length < 8) {
        alert('Password must be at least 8 characters long');
        return;
    }
    
    if (password !== confirmPassword) {
        alert('Passwords do not match');
        return;
    }
    
    // Submit form
    this.submit();
});

// Real-time password validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const errorElement = document.getElementById('password-error');
    
    if (password.length > 0 && password.length < 8) {
        errorElement.textContent = 'Password must be at least 8 characters';
        errorElement.style.display = 'block';
    } else {
        errorElement.style.display = 'none';
    }
});
</script>