document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    if (!loginForm) return;

    // Handle form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        
        if (!email || !password) {
            showError('Please fill in all fields');
            return;
        }

        // Hide any previous error
        hideError();

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        
        try {
            const response = await fetch((window.BASE_URL||'')+'/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (result.success) {
                showToast('Login successful! Redirecting...', 'success');
                hideError();
                
                // Store user info in localStorage
                localStorage.setItem('user', JSON.stringify(result.user));
                
                // Redirect based on role
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1500);
            } else {
                const msg = result.message || 'Invalid email or password. Please try again.';
                showError(msg);
                showToast(msg, 'error');
            }
        } catch (error) {
            console.error('Login error:', error);
            showError('Connection error. Please check your internet connection and try again.');
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

    // === Error message functions ===
    function showError(message) {
        var errorDiv = document.getElementById('loginError');
        var errorText = document.getElementById('loginErrorText');
        if (errorDiv && errorText) {
            errorText.textContent = message;
            errorDiv.style.display = 'block';
            // Re-trigger shake animation
            errorDiv.style.animation = 'none';
            void errorDiv.offsetHeight; // Force reflow
            errorDiv.style.animation = 'errorShake .4s ease';
        }
    }

    function hideError() {
        var errorDiv = document.getElementById('loginError');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    // === Toast notification ===
    function showToast(message, type) {
        type = type || 'info';
        var toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.innerHTML = '<div class="toast-content"><i class="fas fa-' + 
            (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + 
            '"></i><span>' + message + '</span></div>';

        var container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;max-width:400px;';
            document.body.appendChild(container);
        }
        
        toast.style.cssText = 'background:' + (type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff') +
            ';color:white;padding:12px 20px;margin-bottom:10px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);' +
            'transform:translateX(100%);transition:transform 0.3s ease;display:flex;align-items:center;gap:10px;';
        
        container.appendChild(toast);

        setTimeout(function() { toast.style.transform = 'translateX(0)'; }, 100);
        setTimeout(function() {
            toast.style.transform = 'translateX(100%)';
            setTimeout(function() { if (container.contains(toast)) container.removeChild(toast); }, 300);
        }, 5000);
    }
});