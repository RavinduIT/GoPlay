<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Join as Provider - GoPlay';
$additionalCSS = ['/public/css/pages/provider-role-selection.css'];
$additionalJS = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Include Navbar -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="provider-selection-container">
        <div class="selection-header">
            <div class="header-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <h1>Join as a Provider</h1>
            <p class="subtitle">Choose your role and start your journey with GoPlay</p>
        </div>

        <div class="roles-grid">
            <!-- Ground Owner Card -->
            <div class="role-card" data-role="ground-owner">
                <div class="role-icon">
                    <i class="fas fa-landmark"></i>
                </div>
                <h3>Ground Owner</h3>
                <p class="role-description">
                    List and manage your sports facilities, set pricing, manage bookings, and earn revenue
                </p>
                <ul class="role-features">
                    <li><i class="fas fa-check"></i> List multiple grounds</li>
                    <li><i class="fas fa-check"></i> Flexible pricing options</li>
                    <li><i class="fas fa-check"></i> Booking management</li>
                    <li><i class="fas fa-check"></i> Analytics dashboard</li>
                    <li><i class="fas fa-check"></i> Direct payments</li>
                </ul>
                <a href="<?= $_base ?>/provider/apply/ground-owner" class="btn-select-role">
                    Get Started <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Coach Card -->
            <div class="role-card" data-role="coach">
                <div class="role-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Coach</h3>
                <p class="role-description">
                    Offer professional coaching services, create training programs, and grow your client base
                </p>
                <ul class="role-features">
                    <li><i class="fas fa-check"></i> Create training programs</li>
                    <li><i class="fas fa-check"></i> Schedule management</li>
                    <li><i class="fas fa-check"></i> Client tracking</li>
                    <li><i class="fas fa-check"></i> Performance analytics</li>
                    <li><i class="fas fa-check"></i> Secure payments</li>
                </ul>
                <a href="<?= $_base ?>/provider/apply/coach" class="btn-select-role">
                    Get Started <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Shop Owner Card -->
            <div class="role-card" data-role="shop-owner">
                <div class="role-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3>Shop Owner</h3>
                <p class="role-description">
                    Sell sports equipment and apparel, manage inventory, and reach thousands of customers
                </p>
                <ul class="role-features">
                    <li><i class="fas fa-check"></i> Product listings</li>
                    <li><i class="fas fa-check"></i> Inventory management</li>
                    <li><i class="fas fa-check"></i> Order processing</li>
                    <li><i class="fas fa-check"></i> Sales analytics</li>
                    <li><i class="fas fa-check"></i> Customer reviews</li>
                </ul>
                <a href="<?= $_base ?>/provider/apply/shop-owner" class="btn-select-role">
                    Get Started <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="info-section">
            <h2>Why Join GoPlay?</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <i class="fas fa-users"></i>
                    <h4>Large Customer Base</h4>
                    <p>Access thousands of active users looking for sports services</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-chart-line"></i>
                    <h4>Grow Your Business</h4>
                    <p>Tools and analytics to help you expand your reach</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Secure Platform</h4>
                    <p>Safe and reliable payment processing</p>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-headset"></i>
                    <h4>Dedicated Support</h4>
                    <p>24/7 customer support for all providers</p>
                </div>
            </div>
        </div>

        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>How long does the approval process take?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Most applications are reviewed within 24-48 hours. You'll receive an email notification once your application is processed.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>Are there any fees to join?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Joining GoPlay is completely free. We only take a small commission on successful bookings or sales.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h4>Can I have multiple provider roles?</h4>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Currently, each account can hold one provider role at a time. However, you can apply for a role change if needed.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        // FAQ Toggle
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', function() {
                const faqItem = this.parentElement;
                const isActive = faqItem.classList.contains('active');

                // Close all FAQ items
                document.querySelectorAll('.faq-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Open clicked item if it wasn't already active
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });

        // Role card hover effect
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
