<?php 
$title = 'Login - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/login.css'];
$additionalJS = ['/public/js/pages/login.js'];
?>
<link rel="stylesheet" href="/public/css/pages/login.css">
<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Branding -->
        <div class="auth-brand">
            <div class="brand-content">
                <img src="/public/assets/images/logo.jpeg" alt="GoPlay" class="brand-logo">
                <h1 class="brand-title">GoPlay</h1>
                <p class="brand-subtitle">Your Premier Sports Booking Platform</p>
                <div class="brand-features">
                    <div class="feature-item">
                        <i class="fas fa-calendar-check"></i>
                        <span>Easy Booking</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users"></i>
                        <span>Expert Coaches</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-trophy"></i>
                        <span>Premium Facilities</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="auth-form-section">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h2 class="auth-title">Welcome Back</h2>
                    <p class="auth-subtitle">Sign in to your account to continue booking</p>
                </div>
            
                
                <form id="loginForm" class="auth-form">
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
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                class="form-input"
                                placeholder="Enter your password"
                                required
                            />
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" id="remember" class="checkbox">
                            <span class="checkbox-label">Remember me</span>
                        </label>
                        <a href="/forgot-password" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="btn-text">Sign In</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    <p class="auth-footer-text">
                        Don't have an account? 
                        <a href="/signup" class="auth-link">Create Account</a>
                    </p>
                </div>
                
                
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>
<script src="/public/js/pages/login.js"></script>
