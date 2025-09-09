<link rel="stylesheet" href="/public/css/pages/components/navbar.css">
<nav class="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <div class="nav-logo">
            <a href="/">
                <img src="/public/assets/images/logo.jpeg" alt="GoPlay" class="logo-img">
                <span class="logo-text">GoPlay</span>
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
        </ul>
        
        <!-- User Menu -->
        <div class="nav-user">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-dropdown">
                    <button class="user-btn">
                        <img src="<?= $_SESSION['user']['avatar'] ?? '/public/assets/images/default-avatar.png' ?>" 
                             alt="Profile" class="user-avatar">
                        <span><?= $_SESSION['user']['name'] ?? 'User' ?></span>
                        <i class="fas fa-chevron-down dropdown-icon"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="/profile"><i class="fas fa-user"></i> Profile</a>
                        <a href="/my-bookings"><i class="fas fa-calendar-alt"></i> My Bookings</a>
                        <a href="/cart"><i class="fas fa-shopping-cart"></i> Cart</a>
                        <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                            <a href="/admin"><i class="fas fa-cog"></i> Admin Panel</a>
                        <?php endif; ?>
                        <hr>
                        <a href="/logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="/login" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="/signup" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Sign Up
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
        <?php if (isset($_SESSION['user'])): ?>
            <li><a href="/profile">Profile</a></li>
            <li><a href="/my-bookings">My Bookings</a></li>
            <li><a href="/cart">Cart</a></li>
            <li><a href="/logout">Logout</a></li>
        <?php else: ?>
            <li><a href="/login">Login</a></li>
            <li><a href="/signup">Sign Up</a></li>
        <?php endif; ?>
    </ul>
</div>

<script>
// Mobile menu toggle
document.querySelector('.nav-toggle').addEventListener('click', function() {
    document.querySelector('.mobile-menu').classList.toggle('active');
});

// User dropdown toggle
const userBtn = document.querySelector('.user-btn');
if (userBtn) {
    userBtn.addEventListener('click', function() {
        this.nextElementSibling.classList.toggle('show');
    });
}
</script>
<style>
    :root {
    --primary-color: #2563eb;
    --primary-dark: #64748b;
    --primary-light: #f1f5f9;
    --text-primary: #2c3e50;
    --text-secondary: #6c757d;
    --text-light: #adb5bd;
    --background-white: #ffffff;
    --border-color: #e9ecef;
    --shadow-light: 0 2px 8px rgba(0,0,0,0.08);
    --shadow-medium: 0 4px 16px rgba(0,0,0,0.12);
    --border-radius: 8px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Navbar */
.navbar {
    background: var(--background-white);
    box-shadow: var(--shadow-light);
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid var(--border-color);
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 70px;
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
    transform: scale(1.02);
}

.logo-img {
    width: 40px;
    height: 40px;
    margin-right: 0.5rem;
    border-radius: 50%;
    object-fit: cover;
}

.logo-text {
    font-weight: 700;
    font-size: 1.5rem;
}

/* Navigation Menu */
.nav-menu {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 2rem;
}

.nav-item {
    position: relative;
}

.nav-link {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 500;
    padding: 0.75rem 1rem;
    border-radius: var(--border-radius);
    transition: var(--transition);
    position: relative;
}

.nav-link:hover {
    color: var(--primary-color);
    background: var(--primary-light);
}

.nav-link.active {
    color: var(--primary-color);
    background: var(--primary-light);
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary-color);
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
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.btn-outline {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-outline:hover {
    background: var(--primary-color);
    color: white;
}

/* User Dropdown */
.user-dropdown {
    position: relative;
    display: inline-block;
}

.user-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--background-white);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: var(--transition);
    font-weight: 500;
    color: var(--text-primary);
}

.user-btn:hover {
    border-color: var(--primary-color);
    box-shadow: var(--shadow-light);
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.dropdown-icon {
    font-size: 0.8rem;
    transition: var(--transition);
}

.user-btn:hover .dropdown-icon {
    transform: rotate(180deg);
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 0.5rem;
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
    gap: 0.75rem;
    color: var(--text-primary);
    padding: 0.875rem 1.25rem;
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
}

.dropdown-content a:hover {
    background: var(--primary-light);
    color: var(--primary-color);
}

.dropdown-content a i {
    width: 16px;
    text-align: center;
    font-size: 0.9rem;
}

.dropdown-content hr {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 0.5rem 0;
}

.logout-btn:hover {
    background: #fee !important;
    color: #dc3545 !important;
}

/* Mobile Toggle */
.nav-toggle {
    display: none;
    flex-direction: column;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.nav-toggle:hover {
    background: var(--primary-light);
}

.nav-toggle span {
    width: 25px;
    height: 3px;
    background: var(--text-primary);
    margin: 3px 0;
    transition: var(--transition);
    border-radius: 2px;
}

.nav-toggle.active span:nth-child(1) {
    transform: rotate(-45deg) translate(-5px, 6px);
}

.nav-toggle.active span:nth-child(2) {
    opacity: 0;
}

.nav-toggle.active span:nth-child(3) {
    transform: rotate(45deg) translate(-5px, -6px);
}

/* Mobile Menu */
.mobile-menu {
    display: none;
    position: fixed;
    top: 70px;
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

.mobile-nav-menu li {
    margin: 0;
}

.mobile-nav-menu a {
    display: block;
    color: var(--text-primary);
    text-decoration: none;
    padding: 1rem 2rem;
    font-weight: 500;
    transition: var(--transition);
    border-bottom: 1px solid var(--border-color);
}

.mobile-nav-menu a:hover {
    background: var(--primary-light);
    color: var(--primary-color);
    padding-left: 2.5rem;
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
    
    .auth-buttons {
        gap: 0.5rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
    
    .logo-text {
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .nav-container {
        height: 60px;
    }
    
    .mobile-menu {
        top: 60px;
    }
    
    .logo-img {
        width: 32px;
        height: 32px;
    }
    
    .logo-text {
        font-size: 1.1rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
    }
    
    .btn span {
        display: none;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Active page highlighting */
.nav-link[href="/book-ground"].active,
.nav-link[href="/coaches"].active,
.nav-link[href="/shop"].active,
.nav-link[href="/news"].active {
    color: var(--primary-color);
    background: var(--primary-light);
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Focus styles for accessibility */
.nav-link:focus,
.btn:focus,
.user-btn:focus,
.nav-toggle:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>