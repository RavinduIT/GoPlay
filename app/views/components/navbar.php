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
                <a href="/grounds" class="nav-link">Book Ground</a>
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
                        <span><?= $_SESSION['user']['name'] ?? 'User' ?></span>
                        <i class="dropdown-icon">▼</i>
                    </button>
                    <div class="dropdown-content">
                        <a href="/profile">Profile</a>
                        <a href="/my-bookings">My Bookings</a>
                        <a href="/cart">Cart</a>
                        <?php if (($_SESSION['user']['role'] ?? '') === 'admin'): ?>
                            <a href="/admin">Admin Panel</a>
                        <?php endif; ?>
                        <hr>
                        <a href="/logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="/login" class="btn btn-outline">Login</a>
                    <a href="/signup" class="btn btn-primary">Sign Up</a>
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
        <li><a href="/grounds">Book Ground</a></li>
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