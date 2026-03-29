<?php
// Start session to check user authentication (only if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to get profile URL based on user role
if (!function_exists('getProfileUrl')) {
    function getProfileUrl($userType) {
        switch($userType) {
            case 'admin':
                return '/admin/dashboard';
            case 'coach':
                return '/coach/profile';
            case 'ground_owner':
                return '/ground-owner/profile';
            case 'shop_owner':
                return '/shop-owner/profile';
            case 'user':
            default:
                return '/user/profile';
        }
    }
}

// Function to get dashboard URL based on user role
if (!function_exists('getDashboardUrl')) {
    function getDashboardUrl($userType) {
        switch($userType) {
            case 'admin':
                return '/admin/dashboard';
            case 'coach':
                return '/coach/dashboard';
            case 'ground_owner':
                return '/ground-owner/dashboard';
            case 'shop_owner':
                return '/shop-owner/dashboard';
            case 'user':
            default:
                return '/dashboard';
        }
    }
}

// Function to get user initials for default avatar
if (!function_exists('getUserInitials')) {
    function getUserInitials($name) {
        $words = explode(' ', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        return substr($initials, 0, 2); // Maximum 2 initials
    }
}

// Check if user has a profile picture
$hasProfilePicture = isset($_SESSION['user']['avatar']) && !empty($_SESSION['user']['avatar']);
$userInitials = isset($_SESSION['user_name']) ? getUserInitials($_SESSION['user_name']) : 'U';
?>

<nav class="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <div class="nav-logo">
            <a href="/">
                <img src="/public/assets/images/logo.jpeg" alt="GoPlay" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <span class="logo-fallback" style="display: none;">GP</span>
                <span class="logo-text">GOPLAY</span>
            </a>
        </div>
        
        <!-- Navigation Menu -->
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
                <a href="/book-ground" class="nav-link">Book Ground</a>
            </li>
            <li class="nav-item">
                <a href="/book-coach" class="nav-link">Book Coach</a>
            </li>
            <li class="nav-item">
                <a href="/shop" class="nav-link">Shop</a>
            </li>
            <li class="nav-item">
                <a href="/news" class="nav-link">News</a>
            </li>
            <?php if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? 'user') === 'user'): ?>
    <li class="nav-item">
        <a 
            href="<?= isset($_SESSION['user_id']) ? '/provider/join' : '/signup' ?>" 
            class="nav-link join-provider"
            onclick="<?php if (!isset($_SESSION['user_id'])) echo 'alert(\'Please sign up or log in before joining as a provider.\');'; ?>"
        >
            <i class="fas fa-briefcase"></i> Join as Provider
        </a>
    </li>
<?php endif; ?>

        </ul>
        
        <!-- User Menu -->
        <div class="nav-user">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <button class="user-btn">
                        <?php if ($hasProfilePicture): ?>
                            <img src="<?= $_SESSION['user']['avatar'] ?>" 
                                 alt="Profile" class="user-avatar"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="user-avatar-fallback" style="display: none;"><?= $userInitials ?></div>
                        <?php else: ?>
                            <div class="user-avatar-fallback"><?= $userInitials ?></div>
                        <?php endif; ?>
                        <span><?= $_SESSION['user_name'] ?? 'User' ?></span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="<?= getProfileUrl($_SESSION['user_type'] ?? 'user') ?>">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        
                        <a href="<?= getDashboardUrl($_SESSION['user_type'] ?? 'user') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        
                        <a href="/my-bookings"><i class="fas fa-calendar-alt"></i> My Bookings</a>
                        <a href="/cart"><i class="fas fa-shopping-cart"></i> Cart</a>
                        <a href="/my-orders"><i class="fas fa-receipt"></i> My Orders</a>
                        
                        <?php if (($_SESSION['user_type'] ?? '') === 'admin'): ?>
                            <hr>
                            <a href="/admin/dashboard"><i class="fas fa-cog"></i> Admin Panel</a>
                        <?php endif; ?>
                        
                        <hr>
                        <a href="/notifications"><i class="fas fa-bell"></i> Notifications</a>
                        <a href="/settings"><i class="fas fa-cog"></i> Settings</a>
                        <hr>
                        <a href="#" onclick="logout(); return false;" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="/login" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> <span>Login</span>
                    </a>
                    <a href="/signup" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> <span>Sign Up</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Mobile Menu Toggle -->
        <div class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu">
    <ul class="mobile-nav-menu">
        <li><a href="/">Home</a></li>
        <li><a href="/book-ground">Book Ground</a></li>
        <li><a href="/book-coach">Book Coach</a></li>
        <li><a href="/shop">Shop</a></li>
        <li><a href="/news">News</a></li>
        <?php if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? 'user') === 'user'): ?>
            <li><a href="/provider/join">Join as Provider</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?= getProfileUrl($_SESSION['user_type'] ?? 'user') ?>">Profile</a></li>
            <li><a href="<?= getDashboardUrl($_SESSION['user_type'] ?? 'user') ?>">Dashboard</a></li>
            <li><a href="/my-bookings">My Bookings</a></li>
            <li><a href="/cart">Cart</a></li>
            <li><a href="#" onclick="logout(); return false;">Logout</a></li>
        <?php else: ?>
            <li><a href="/login">Login</a></li>
            <li><a href="/signup">Sign Up</a></li>
        <?php endif; ?>
    </ul>
</div>

<script>
//console.log('Navbar script loaded');

// Mobile menu toggle
const navToggle = document.querySelector('.nav-toggle');
const mobileMenu = document.querySelector('.mobile-menu');

if (navToggle && mobileMenu) {
    navToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
        this.classList.toggle('active');
    });
}

// User dropdown toggle
const userBtn = document.querySelector('.user-btn');
if (userBtn) {
    console.log('User button found');
    userBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        e.preventDefault();
        const dropdown = this.nextElementSibling;
        dropdown.classList.toggle('show');
        console.log('Dropdown toggled');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.dropdown-content');
        if (dropdown && !userBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
}

// Logout function - Fixed to use correct endpoint
function logout() {
    console.log('Logout function called');
    
    if (confirm('Are you sure you want to logout?')) {
        // Try multiple possible logout endpoints
        const logoutEndpoints = ['/auth/logout', '/logout'];
        
        // Use form submission as fallback for logout
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/auth/logout';
        form.style.display = 'none';
        document.body.appendChild(form);
        
        // Add CSRF token if available
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = metaToken.content;
            form.appendChild(tokenInput);
        }
        
        // Try fetch first
        fetch('/auth/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Logout response:', response);
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok');
        })
        .then(data => {
            console.log('Logout success:', data);
            if (data.success) {
                window.location.href = '/';
            } else {
                throw new Error(data.message || 'Logout failed');
            }
        })
        .catch(error => {
            console.log('Fetch failed, trying form submission:', error);
            // Fallback to form submission
            form.submit();
        });
    }
    
    return false;
}

// Debug session info
console.log('Session check - User ID:', <?= json_encode($_SESSION['user_id'] ?? null) ?>);
console.log('Session check - User Type:', <?= json_encode($_SESSION['user_type'] ?? null) ?>);
console.log('Session check - User Name:', <?= json_encode($_SESSION['user_name'] ?? null) ?>);
</script>

<style>
:root {
    --primary-color: #2563eb;
    --primary-dark: #1e40af;
    --primary-light: #dbeafe;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --text-light: #9ca3af;
    --background-white: #ffffff;
    --border-color: #e5e7eb;
    --shadow-light: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-medium: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --border-radius: 6px;
    --transition: all 0.15s ease-in-out;
}

/* Navbar */
.navbar {
    background: var(--background-white);
    box-shadow: var(--shadow-light);
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid var(--border-color);
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
}

/* Logo */
.nav-logo a {
    display: flex;
    align-items: center;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.5rem;
    color: var(--primary-color);
    transition: var(--transition);
}

.nav-logo a:hover {
    transform: scale(1.05);
}

.logo-img {
    width: 36px;
    height: 36px;
    margin-right: 8px;
    border-radius: 50%;
    object-fit: cover;
}

.logo-fallback {
    width: 36px;
    height: 36px;
    margin-right: 8px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

/* Navigation Menu */
.nav-menu {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 0.5rem;
}

.nav-link {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.nav-link:hover {
    color: var(--primary-color);
    background: var(--primary-light);
}

/* User Menu */
.nav-user {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Auth Buttons */
.auth-buttons {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 8px 16px;
    border: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
}

.btn-outline {
    background: transparent;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.btn-outline:hover {
    background: var(--primary-color);
    color: white;
}

/* User Dropdown */
.user-dropdown {
    position: relative;
}

.user-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--background-white);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 6px 12px;
    cursor: pointer;
    transition: var(--transition);
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.user-btn:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-light);
}

.user-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
}

.user-avatar-fallback {
    width: 28px;
    height: 28px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
    flex-shrink: 0;
}

.dropdown-icon {
    font-size: 0.75rem;
    transition: var(--transition);
}

.user-btn:hover .dropdown-icon {
    transform: rotate(180deg);
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    background: var(--background-white);
    min-width: 200px;
    box-shadow: var(--shadow-medium);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    overflow: hidden;
    z-index: 1001;
}

.dropdown-content.show {
    display: block;
    animation: fadeInUp 0.2s ease-out;
}

.dropdown-content a {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--text-primary);
    padding: 12px 16px;
    text-decoration: none;
    transition: var(--transition);
    font-size: 0.875rem;
    font-weight: 500;
}

.dropdown-content a:hover {
    background: var(--primary-light);
    color: var(--primary-color);
}

.dropdown-content a i {
    width: 16px;
    text-align: center;
    font-size: 0.875rem;
}

.dropdown-content hr {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 8px 0;
}

.logout-btn:hover {
    background: #fef2f2 !important;
    color: #dc2626 !important;
}

.join-provider {
    background: linear-gradient(135deg, #495eb9ff 0%, #535b7cff 100%);
    color: white !important;
    border-radius: var(--border-radius);
    font-weight: 600;
    padding: 8px 16px !important;
}

.join-provider:hover {
    background: linear-gradient(135deg, #37418fff 0%, #3d4f9fff 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* Mobile Toggle */
.nav-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 8px;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.nav-toggle:hover {
    background: var(--primary-light);
}

.nav-toggle span {
    width: 20px;
    height: 2px;
    background: var(--text-primary);
    margin: 2px 0;
    transition: var(--transition);
    border-radius: 1px;
}

.nav-toggle.active span:nth-child(1) {
    transform: rotate(-45deg) translate(-4px, 5px);
}

.nav-toggle.active span:nth-child(2) {
    opacity: 0;
}

.nav-toggle.active span:nth-child(3) {
    transform: rotate(45deg) translate(-4px, -5px);
}

/* Mobile Menu */
.mobile-menu {
    display: none;
    position: fixed;
    top: 64px;
    left: 0;
    width: 100%;
    background: var(--background-white);
    box-shadow: var(--shadow-medium);
    border-top: 1px solid var(--border-color);
    z-index: 999;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
}

.mobile-menu.active {
    max-height: 500px;
}

.mobile-nav-menu {
    list-style: none;
    padding: 1rem 0;
    margin: 0;
}

.mobile-nav-menu a {
    display: block;
    color: var(--text-primary);
    text-decoration: none;
    padding: 12px 24px;
    font-weight: 500;
    transition: var(--transition);
    border-bottom: 1px solid var(--border-color);
}

.mobile-nav-menu a:hover {
    background: var(--primary-light);
    color: var(--primary-color);
    padding-left: 32px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .nav-container {
        padding: 0 1rem;
    }
    
    .nav-menu {
        display: none;
    }
    
    .nav-toggle {
        display: flex;
    }
    
    .mobile-menu {
        display: block;
    }
}

@media (max-width: 480px) {
    .nav-container {
        height: 56px;
    }
    
    .mobile-menu {
        top: 56px;
    }
    
    .logo-img, .logo-fallback {
        width: 32px;
        height: 32px;
    }
    
    .logo-text {
        font-size: 1.25rem;
    }
    
    .btn {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
    
    .btn span {
        display: none;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>