<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GoPlay Sports Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/login.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Branding -->
        <div class="auth-brand">
            <div class="brand-content">
                <img src="<?= $_base ?>/public/assets/images/logo.jpeg" alt="GoPlay" class="brand-logo">
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

                    

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="btn-text">Sign In</span>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>

                    <!-- Inline error message -->
                    <div id="loginError" style="display:none;margin-top:16px;padding:12px 16px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:10px;color:#ef4444;font-size:14px;text-align:center;animation:errorShake .4s ease">
                        <i class="fas fa-exclamation-circle" style="margin-right:8px"></i>
                        <span id="loginErrorText"></span>
                    </div>
                    <style>@keyframes errorShake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}</style>
                </form>

                <div class="auth-footer">
                    <p class="auth-footer-text">
                        Don't have an account? 
                        <a href="<?= $_base ?>/signup" class="auth-link">Create Account</a>
                    </p>
                </div>
                
                
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container"></div>
<script>window.BASE_URL = '<?= $_base ?>';</script>
<script src="<?= $_base ?>/public/js/pages/login.js?v=<?= time() ?>"></script>
</body>
</html>
