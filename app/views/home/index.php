<?php 
$title = 'GoPlay - Premier Sports Booking Platform';
$description = 'Book sports facilities, hire professional coaches, and shop for equipment at GoPlay';
$additionalCSS = ['/public/css/pages/index.css'];
$additionalJS = ['/public/js/pages/index.js'];
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-background">
        <img src="/public/assets/images/hero-background.jpg" alt="Sports Background" class="hero-bg-img">
        <div class="hero-overlay"></div>
    </div>
    <div class="hero-content">
        <div class="container">
            <h1 class="hero-title">Welcome to GoPlay</h1>
            <p class="hero-subtitle">Your Premier Sports Booking Platform</p>
            <p class="hero-description">
                Book sports facilities, hire professional coaches, and shop for quality equipment - all in one place.
            </p>
            <div class="hero-buttons">
                <a href="/grounds" class="btn btn-primary btn-large">Book Facilities</a>
                <a href="/coaches" class="btn btn-outline btn-large">Find Coaches</a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">Why Choose GoPlay?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="/public/assets/images/feature-booking.jpg" alt="Easy Booking">
                </div>
                <h3>Easy Booking</h3>
                <p>Book sports facilities with just a few clicks. Real-time availability and instant confirmation.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="/public/assets/images/feature-coaching.jpg" alt="Professional Coaches">
                </div>
                <h3>Professional Coaches</h3>
                <p>Connect with certified and experienced coaches for personalized training sessions.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="/public/assets/images/feature-equipment.jpg" alt="Quality Equipment">
                </div>
                <h3>Quality Equipment</h3>
                <p>Shop for premium sports equipment and gear from trusted brands.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <div class="services-grid">
            <div class="service-item">
                <div class="service-content">
                    <h3>Facility Booking</h3>
                    <p>Reserve courts, fields, and sports complexes for your games and training sessions.</p>
                    <ul>
                        <li>Real-time availability</li>
                        <li>Flexible timing</li>
                        <li>Competitive rates</li>
                        <li>Easy cancellation</li>
                    </ul>
                    <a href="/grounds" class="btn btn-primary">Book Now</a>
                </div>
            </div>
            <div class="service-item">
                <div class="service-content">
                    <h3>Coach Hiring</h3>
                    <p>Find and hire qualified coaches for individual or group training sessions.</p>
                    <ul>
                        <li>Certified professionals</li>
                        <li>Various sports disciplines</li>
                        <li>Flexible scheduling</li>
                        <li>Skill-based matching</li>
                    </ul>
                    <a href="/coaches" class="btn btn-primary">Find Coach</a>
                </div>
            </div>
            <div class="service-item">
                <div class="service-content">
                    <h3>Equipment Shop</h3>
                    <p>Purchase high-quality sports equipment, apparel, and accessories.</p>
                    <ul>
                        <li>Premium brands</li>
                        <li>Competitive prices</li>
                        <li>Fast delivery</li>
                        <li>Quality guarantee</li>
                    </ul>
                    <a href="/shop" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Sports Facilities</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">200+</div>
                <div class="stat-label">Professional Coaches</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">10,000+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50,000+</div>
                <div class="stat-label">Successful Bookings</div>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Section -->
<section class="latest-news">
    <div class="container">
        <h2 class="section-title">Latest News</h2>
        <div class="news-grid">
            <div class="news-card">
                <img src="/public/assets/images/news-1.jpg" alt="News 1" class="news-image">
                <div class="news-content">
                    <div class="news-date">March 15, 2024</div>
                    <h3 class="news-title">New Sports Complex Opens in Colombo</h3>
                    <p class="news-excerpt">State-of-the-art facility with multiple courts and modern amenities now available for booking.</p>
                    <a href="/news/1" class="news-link">Read More</a>
                </div>
            </div>
            <div class="news-card">
                <img src="/public/assets/images/news-2.jpg" alt="News 2" class="news-image">
                <div class="news-content">
                    <div class="news-date">March 12, 2024</div>
                    <h3 class="news-title">Professional Tennis Coach Joins GoPlay</h3>
                    <p class="news-excerpt">Former national player now offering training sessions through our platform.</p>
                    <a href="/news/2" class="news-link">Read More</a>
                </div>
            </div>
            <div class="news-card">
                <img src="/public/assets/images/news-3.jpg" alt="News 3" class="news-image">
                <div class="news-content">
                    <div class="news-date">March 10, 2024</div>
                    <h3 class="news-title">Spring Sports Equipment Sale</h3>
                    <p class="news-excerpt">Get up to 30% off on selected sports equipment and apparel this month.</p>
                    <a href="/news/3" class="news-link">Read More</a>
                </div>
            </div>
        </div>
        <div class="news-cta">
            <a href="/news" class="btn btn-outline">View All News</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of sports enthusiasts who trust GoPlay for their sporting needs.</p>
            <div class="cta-buttons">
                <a href="/signup" class="btn btn-primary btn-large">Join Now</a>
                <a href="/contact" class="btn btn-outline btn-large">Contact Us</a>
            </div>
        </div>
    </div>
</section>