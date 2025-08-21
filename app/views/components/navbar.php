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
                <a href="/app/views/booking/book-ground.php" class="nav-link">Book Ground</a>
            </li>
            <li class="nav-item">
                <a href="/coaches" class="nav-link">Book Coach</a>
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
        <li><a href="/coaches">Book Coach</a></li>
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