<?php 
$title = 'GoPlay - Premier Sports Booking Platform';
$description = 'Book sports facilities, hire professional coaches, and shop for equipment at GoPlay';
$additionalCSS = ['/public/css/pages/index.css'];
$additionalJS = ['/public/js/components/news-carousel.js', '/public/js/pages/index.js'];
$_base = defined('BASE_URL') ? BASE_URL : '';
?>


    <!-- Home Content (scoped) -->
    <div class="main-content home">

        <!-- Promotions Banner -->
        <?php if (!empty($promotions ?? [])): ?>
        <div class="promo-banner" id="promoBanner">
            <?php foreach ($promotions as $i => $promo): ?>
            <div class="promo-slide <?= $i === 0 ? 'active' : '' ?>" style="background: <?= htmlspecialchars($promo['bg_color'] ?? '#3b82f6') ?>; color: <?= htmlspecialchars($promo['text_color'] ?? '#fff') ?>;">
                <div class="promo-content">
                    <strong><?= htmlspecialchars($promo['title']) ?></strong>
                    <?php if (!empty($promo['subtitle'])): ?>
                        <span> - <?= htmlspecialchars($promo['subtitle']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($promo['link_url'])): ?>
                        <a href="<?= htmlspecialchars($promo['link_url']) ?>" style="color: inherit; margin-left: 12px; text-decoration: underline; font-weight: 600;"><?= htmlspecialchars($promo['link_text'] ?? 'Learn More') ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <style>
            .promo-banner { position: relative; text-align: center; font-size: 14px; overflow: hidden; height: 44px; }
            .promo-slide { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; padding: 8px 20px; opacity: 0; transition: opacity 0.5s; }
            .promo-slide.active { opacity: 1; }
        </style>
        <script>
            (function() {
                const slides = document.querySelectorAll('.promo-slide');
                if (slides.length <= 1) return;
                let idx = 0;
                setInterval(() => {
                    slides[idx].classList.remove('active');
                    idx = (idx + 1) % slides.length;
                    slides[idx].classList.add('active');
                }, 4000);
            })();
        </script>
        <?php endif; ?>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title">
                    Your Ultimate GOPLAY
                </h1>
                <p class="hero-description">
                    Book grounds, hire coaches, buy equipment, and stay updated with the latest sports news
                </p>
                <div class="hero-buttons">
                    <a href="<?= $_base ?>/book-ground" class="hero-btn primary">
                        <i class="fas fa-calendar"></i>
                        Book Now
                    </a>
                    <a href="<?= $_base ?>/shop" class="hero-btn secondary">
                        <i class="fas fa-shopping-cart"></i>
                        Shop Equipment
                    </a>
                </div>
                <!-- Hero Search Panel -->
                <div class="hero-search mt-4">
                    <form class="search-form" action="<?= $_base ?>/book-ground" method="get">
                        <div class="search-grid">
                            <div class="search-field">
                                <label for="sport">Sport</label>
                                <select id="sport" name="sport">
                                    <option value="">Any</option>
                                    <?php if (!empty($sportsCategories ?? [])): ?>
                                        <?php foreach ($sportsCategories as $cat): ?>
                                        <option value="<?= htmlspecialchars(strtolower($cat['name'])) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="football">Football</option>
                                        <option value="tennis">Tennis</option>
                                        <option value="basketball">Basketball</option>
                                        <option value="cricket">Cricket</option>
                                        <option value="badminton">Badminton</option>
                                        <option value="swimming">Swimming</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="location">Location</label>
                                <select id="location" name="location">
                                    <option value="">Anywhere</option>
                                    <option value="Colombo 03">Colombo 03</option>
                                    <option value="Colombo 05">Colombo 05</option>
                                    <option value="Colombo 07">Colombo 07</option>
                                    <option value="Kandy">Kandy</option>
                                    <option value="Galle">Galle</option>
                                    <option value="Negombo">Negombo</option>
                                </select>
                            </div>
                            <div class="search-field">
                                <label for="date">Date</label>
                                <input id="date" name="date" type="date" />
                            </div>
                            <div class="search-action">
                                <button class="search-btn" type="submit">
                                    <i class="fas fa-search"></i>
                                    Find Availability
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </section>

        <!-- Trust/Stats Bar -->
        <section class="trust-bar-section">
            <div class="container">
                <div class="trust-bar">
                    <div class="trust-item">
                        <div class="trust-value">500+</div>
                        <div class="trust-label">Venues</div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-value">200+</div>
                        <div class="trust-label">Certified Coaches</div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-value">60k+</div>
                        <div class="trust-label">Bookings</div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-value">4.9</div>
                        <div class="trust-label">Average Rating</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="features-background"></div>
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        Everything You Need for <span class="title-highlight">Sports</span>
                    </h2>
                    <p class="section-description">
                        From booking world-class facilities to finding expert coaches and premium equipment
                    </p>
                </div>

                <div class="features-grid">
                    <div class="feature-card" data-feature="booking">
                        <div class="feature-image">
                            <img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Book Sports Grounds" />
                            <div class="feature-overlay"></div>
                            <div class="feature-stats">500+ Venues</div>
                        </div>
                        <div class="feature-header">
                            <div class="feature-icon">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <h3 class="feature-title">Book Sports Grounds</h3>
                        </div>
                        <div class="feature-content">
                            <p class="feature-description">
                                Reserve courts, fields, and complexes for your games
                            </p>
                            <a href="<?= $_base ?>/book-ground" class="feature-btn">
                                Get Started
                            </a>
                        </div>
                    </div>

                    <div class="feature-card" data-feature="coaching">
                        <div class="feature-image">
                            <img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Hire Professional Coaches" />
                            <div class="feature-overlay"></div>
                            <div class="feature-stats">200+ Coaches</div>
                        </div>
                        <div class="feature-header">
                            <div class="feature-icon accent">
                                <i class="fas fa-user"></i>
                            </div>
                            <h3 class="feature-title">Hire Professional Coaches</h3>
                        </div>
                        <div class="feature-content">
                            <p class="feature-description">
                                Train with certified coaches for all sports
                            </p>
                            <a href="<?= $_base ?>/coaches" class="feature-btn">
                                Get Started
                            </a>
                        </div>
                    </div>

                    <div class="feature-card" data-feature="equipment">
                        <div class="feature-image">
                            <img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Sports Equipment Shop" />
                            <div class="feature-overlay"></div>
                            <div class="feature-stats">1000+ Products</div>
                        </div>
                        <div class="feature-header">
                            <div class="feature-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3 class="feature-title">Sports Equipment Shop</h3>
                        </div>
                        <div class="feature-content">
                            <p class="feature-description">
                                Buy quality gear from trusted brands
                            </p>
                            <a href="<?= $_base ?>/shop" class="feature-btn">
                                Get Started
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Explore Sports Categories -->
        <section class="categories-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Explore <span class="title-highlight">Sports</span></h2>
                    <p class="section-description">Quickly jump into what you love playing</p>
                </div>
                <div class="categories-grid">
                    <?php if (!empty($sportsCategories ?? [])): ?>
                        <?php foreach ($sportsCategories as $cat): ?>
                        <a class="category-card" href="<?= $_base ?>/book-ground?sport=<?= htmlspecialchars(strtolower($cat['name'])) ?>">
                            <img src="<?= $_base ?>/public/assets/images/football.jpg" alt="<?= htmlspecialchars($cat['name']) ?>" />
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a class="category-card" href="<?= $_base ?>/book-ground?sport=football"><img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Football" /><span>Football</span></a>
                        <a class="category-card" href="<?= $_base ?>/book-ground?sport=tennis"><img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Tennis" /><span>Tennis</span></a>
                        <a class="category-card" href="<?= $_base ?>/book-ground?sport=basketball"><img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Basketball" /><span>Basketball</span></a>
                        <a class="category-card" href="<?= $_base ?>/book-ground?sport=cricket"><img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Cricket" /><span>Cricket</span></a>
                        <a class="category-card" href="<?= $_base ?>/book-ground?sport=badminton"><img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Badminton" /><span>Badminton</span></a>
                        <a class="category-card" href="<?= $_base ?>/book-ground?sport=swimming"><img src="<?= $_base ?>/public/assets/images/football.jpg" alt="Swimming" /><span>Swimming</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="how-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">How It <span class="title-highlight">Works</span></h2>
                    <p class="section-description">Book in three simple steps</p>
                </div>
                <div class="how-grid">
                    <div class="how-card">
                        <div class="how-icon"><i class="fas fa-search"></i></div>
                        <h3>Discover</h3>
                        <p>Browse venues and coaches across Sri Lanka.</p>
                    </div>
                    <div class="how-card">
                        <div class="how-icon"><i class="fas fa-calendar-check"></i></div>
                        <h3>Schedule</h3>
                        <p>Pick your preferred date and time instantly.</p>
                    </div>
                    <div class="how-card">
                        <div class="how-icon"><i class="fas fa-credit-card"></i></div>
                        <h3>Play</h3>
                        <p>Confirm and pay securely. You're ready to go!</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section class="news-section">
            <div class="news-background"></div>
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        Latest <span class="title-highlight">Sports News</span>
                    </h2>
                    <p class="section-description">
                        Stay updated with the latest happenings in the sports world
                    </p>
                </div>
                <div class="news-container">
                    <div class="news-carousel" id="newsCarousel">
                        <?php if (!empty($featuredNews ?? [])): ?>
                            <?php foreach (($featuredNews ?? []) as $news): ?>
                                <div class="news-item" data-id="<?= (int)($news['id'] ?? 0) ?>">
                                    <div class="news-image">
                                        <img src="<?= htmlspecialchars($news['featured_image'] ?? $_base . '/public/assets/images/football.jpg') ?>" alt="<?= htmlspecialchars($news['title'] ?? 'News') ?>" loading="lazy"
                                             onerror="this.src='<?= $_base ?>/public/assets/images/football.jpg'">
                                        <?php if (!empty($news['category'])): ?>
                                            <div class="news-badge"><?= htmlspecialchars($news['category']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="news-content">
                                        <div class="news-date">
                                            <?= isset($news['published_at']) ? date('F j, Y', strtotime((string)$news['published_at'])) : '' ?>
                                        </div>
                                        <h3 class="news-title"><?= htmlspecialchars($news['title'] ?? '') ?></h3>
                                        <p class="news-excerpt"><?= htmlspecialchars($news['excerpt'] ?? '') ?></p>
                                        <a href="<?= $_base ?>/news/<?= (int)($news['id'] ?? 0) ?>" class="news-link">Read More</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="news-item">
                                <div class="news-image">
                                    <img src="<?= $_base ?>/public/assets/images/football.jpg" alt="News" />
                                </div>
                                <div class="news-content">
                                    <div class="news-date">No news</div>
                                    <h3 class="news-title">No news available</h3>
                                    <p class="news-excerpt">Please check back later for the latest updates.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="carousel-controls">
                        <button class="carousel-btn prev" id="prevBtn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="carousel-btn next" id="nextBtn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="testimonials-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Players <span class="title-highlight">Love</span> GoPlay</h2>
                    <p class="section-description">Real stories from our community</p>
                </div>
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-quote">"Seamless booking and top-notch venues. We organize all our weekend games here."</div>
                        <div class="testimonial-user">
                            <img src="<?= $_base ?>/public/assets/images/default-avatar.png" alt="User" />
                            <div>
                                <div class="user-name">Naveen</div>
                                <div class="user-role">Amateur Footballer</div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-quote">"Found a great tennis coach within minutes. My game has improved dramatically."</div>
                        <div class="testimonial-user">
                            <img src="<?= $_base ?>/public/assets/images/default-avatar.png" alt="User" />
                            <div>
                                <div class="user-name">Ishara</div>
                                <div class="user-role">Tennis Enthusiast</div>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="testimonial-quote">"Clean UI, fast checkout, and reliable schedules. Highly recommend for teams."</div>
                        <div class="testimonial-user">
                            <img src="<?= $_base ?>/public/assets/images/default-avatar.png" alt="User" />
                            <div>
                                <div class="user-name">Dinuka</div>
                                <div class="user-role">Cricket Captain</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Popular Venues -->
        <section class="venues-section" id="venues">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">
                        Popular <span class="title-highlight">Venues</span>
                    </h2>
                    <p class="section-description">
                        Book your favorite sports facilities at premium locations
                    </p>
                </div>

                <div class="venues-grid" id="venuesGrid">
                    <!-- Venues will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-card">
                    <h2>Ready to play?</h2>
                    <p>Join thousands of players and book your next session today.</p>
                    <div class="cta-actions">
                        <a class="hero-btn primary" href="<?= $_base ?>/book-ground"><i class="fas fa-calendar"></i> Book a Ground</a>
                        <a class="hero-btn primary" href="<?= $_base ?>/book-coach"><i class="fas fa-calendar"></i> Hire a Coach</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
