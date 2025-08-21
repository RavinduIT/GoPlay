<?php 
$title = 'Sign Up - GoPlay Sports Platform';
$additionalCSS = ['/public/css/pages/signup.css'];
$additionalJS = ['/public/js/pages/signup.js'];
?>

<link rel="stylesheet" href="/public/css/pages/signup.css">

    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <div class="icon-container">
                    <div class="icon-wrapper">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                </div>
                <h1 class="card-title">Welcome Back</h1>
                <p class="card-description">Sign in to your account to continue</p>
            </div>
            
            <div class="card-content">
                <form id="loginForm" class="form">
                    <div class="form-group">
                        <label for="email" class="label">Email</label>
                        <div class="input-container">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <input
                                type="email"
                                id="email"
                                class="input"
                                placeholder="Enter your email"
                                required
                            />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="label">Password</label>
                        <div class="input-container">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input
                                type="password"
                                id="password"
                                class="input"
                                placeholder="Enter your password"
                                required
                            />
                        </div>
                    </div>

                    <button type="submit" class="button" id="submitBtn">
                        Login
                    </button>
                </form>

                <div class="footer-text">
                    <p>
                        Don't have an account? 
                        <a href="signup.html" class="link">Sign up</a>
                    </p>
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