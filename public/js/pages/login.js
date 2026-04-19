document.addEventListener('DOMContentLoaded', function () {
    const loginForm    = document.getElementById('loginForm');
    const submitBtn    = document.getElementById('submitBtn');
    const emailInput   = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const email    = emailInput.value.trim();
        const password = passwordInput.value.trim();

        // Client-side field validation
        if (!email && !password) {
            showToast('Please enter your email and password to continue.', 'warning', 'Fields Required');
            emailInput.focus();
            return;
        }
        if (!email) {
            showToast('Please enter your email address.', 'warning', 'Email Required');
            emailInput.focus();
            return;
        }
        if (!password) {
            showToast('Please enter your password.', 'warning', 'Password Required');
            passwordInput.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';

        try {
            const response = await fetch((window.BASE_URL || '') + '/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (result.success) {
                showToast('Login successful. Redirecting you now...', 'success', 'Welcome Back!');
                localStorage.setItem('user', JSON.stringify(result.user));
                setTimeout(() => { window.location.href = result.redirect; }, 1600);
                return;
            }

            // Map HTTP status to specific toast
            if (response.status === 401) {
                showToast('Incorrect email or password. Please try again.', 'error', 'Login Failed');
                passwordInput.value = '';
                passwordInput.focus();
            } else if (response.status === 403) {
                const msg = (result.message || '').toLowerCase();
                if (msg.includes('suspended')) {
                    showToast('Your account has been suspended. Please contact support.', 'error', 'Account Suspended');
                } else if (msg.includes('inactive')) {
                    showToast('Your account is inactive. Please contact support.', 'warning', 'Account Inactive');
                } else {
                    showToast(result.message || 'Access denied. Please contact support.', 'error', 'Access Denied');
                }
            } else if (response.status === 400) {
                showToast('Email and password are required.', 'warning', 'Missing Fields');
            } else {
                showToast('Something went wrong on our end. Please try again later.', 'error', 'Server Error');
            }

        } catch (error) {
            console.error('Login error:', error);
            showToast('Unable to connect. Please check your internet connection.', 'error', 'Connection Error');
        }

        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span class="btn-text">Sign In</span><i class="fas fa-arrow-right btn-icon"></i>';
    });

    // Password visibility toggle
    window.togglePassword = function () {
        const eye = document.getElementById('password-eye');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eye.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            eye.className = 'fas fa-eye';
        }
    };

    // ── Toast system ────────────────────────────────────────────
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
});
