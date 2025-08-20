<?php 
$title = 'Login - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/login.css'];
$additionalJS = ['/public/js/pages/login.js'];
?>

<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Image -->
        <div class="auth-image">
            <img src="/public/assets/images/hero-background.jpg" alt="Sports" class="auth-bg">
            <div class="auth-overlay">
                <h2>Welcome Back!</h2>
                <p>Continue your sports journey with GoPlay</p>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="auth-form-container">
            <div class="auth-form">
                <!-- Logo -->
                <div class="auth-header">
                    <img src="/public/assets/images/logo.jpeg" alt="GoPlay" class="auth-logo">
                    <h1>Sign In</h1>
                    <p>Access your GoPlay account</p>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="form" method="POST" action="/auth/login">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required class="form-input">
                        <span class="form-error" id="email-error"></span>
                    </div>

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

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="/forgot-password" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Sign In</button>
                </form>

                <!-- Alternative Login -->
                <div class="auth-divider">
                    <span>Or continue with</span>
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

                <!-- Sign Up Link -->
                <div class="auth-footer">
                    <p>Don't have an account? <a href="/signup" class="auth-link">Sign up here</a></p>
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
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Basic validation
    if (!email || !password) {
        alert('Please fill in all fields');
        return;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        return;
    }
    
    // Submit form
    this.submit();
});
</script>