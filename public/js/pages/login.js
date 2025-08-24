document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Add demo credentials helper
    addDemoCredentialsHelper();

    // Handle form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        
        if (!email || !password) {
            showToast('Please fill in all fields', 'error');
            return;
        }

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        
        try {
            const response = await fetch('/simple_auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (result.success) {
                showToast('Login successful! Redirecting...', 'success');
                
                // Store user info in localStorage
                localStorage.setItem('user', JSON.stringify(result.user));
                
                // Redirect based on role
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1500);
            } else {
                showToast(result.error || 'Login failed', 'error');
            }
        } catch (error) {
            console.error('Login error:', error);
            showToast('Connection error. Please try again.', 'error');
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="btn-text">Sign In</span><i class="fas fa-arrow-right btn-icon"></i>';
        }
    });

    // Toggle password visibility
    window.togglePassword = function() {
        const passwordInput = document.getElementById('password');
        const passwordEye = document.getElementById('password-eye');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordEye.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            passwordEye.className = 'fas fa-eye';
        }
    };

   

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        const container = document.getElementById('toastContainer');
        container.appendChild(toast);

        // Trigger animation
        setTimeout(() => toast.classList.add('show'), 100);

        // Remove after 5 seconds
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => container.removeChild(toast), 300);
        }, 5000);
    }
});