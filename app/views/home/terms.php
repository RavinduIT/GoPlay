<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Terms and Conditions - GoPlay';
?>

<div class="legal-page">
    <div class="legal-container">
        <h1>Terms and Conditions</h1>
        <p class="last-updated">Last updated: <?= date('F j, Y') ?></p>

        <section class="legal-section">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using the GoPlay Sports Platform ("Service"), you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our Service.</p>
        </section>

        <section class="legal-section">
            <h2>2. Description of Service</h2>
            <p>GoPlay is a sports booking and management platform that connects sports enthusiasts with facilities, coaches, and equipment providers. Our services include:</p>
            <ul>
                <li>Booking sports grounds and facilities</li>
                <li>Hiring professional coaches</li>
                <li>Purchasing sports equipment and merchandise</li>
                <li>Provider registration and management tools</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>3. User Accounts</h2>
            <p>You must register for an account to access certain features. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
        </section>

        <section class="legal-section">
            <h2>4. Provider Terms</h2>
            <p>Service providers (ground owners, coaches, shop owners) must submit an application and receive approval before offering services on the platform. Providers must ensure all information provided is accurate and up-to-date.</p>
        </section>

        <section class="legal-section">
            <h2>5. Booking & Payment</h2>
            <p>All bookings are subject to availability. Payments are processed securely through our platform. Cancellation and refund policies vary by provider and are outlined at the time of booking.</p>
        </section>

        <section class="legal-section">
            <h2>6. User Conduct</h2>
            <p>Users agree not to misuse the platform, submit false information, or engage in any activity that disrupts the service or violates applicable laws.</p>
        </section>

        <section class="legal-section">
            <h2>7. Limitation of Liability</h2>
            <p>GoPlay shall not be held liable for any indirect, incidental, or consequential damages arising from the use of the platform. We do not guarantee the quality of services provided by third-party providers.</p>
        </section>

        <section class="legal-section">
            <h2>8. Contact</h2>
            <p>For questions about these Terms, please contact us at <a href="<?= $_base ?>/contact">our contact page</a>.</p>
        </section>
    </div>
</div>

<style>
.legal-page { padding: 60px 20px; max-width: 800px; margin: 0 auto; }
.legal-container h1 { font-size: 2rem; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.last-updated { color: #64748b; font-size: 0.9rem; margin-bottom: 40px; }
.legal-section { margin-bottom: 32px; }
.legal-section h2 { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin-bottom: 12px; }
.legal-section p { color: #475569; line-height: 1.7; margin-bottom: 12px; }
.legal-section ul { padding-left: 24px; color: #475569; line-height: 1.8; }
.legal-section a { color: #2563eb; text-decoration: none; }
.legal-section a:hover { text-decoration: underline; }
</style>
