<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Privacy Policy - GoPlay';
?>

<div class="legal-page">
    <div class="legal-container">
        <h1>Privacy Policy</h1>
        <p class="last-updated">Last updated: <?= date('F j, Y') ?></p>

        <section class="legal-section">
            <h2>1. Information We Collect</h2>
            <p>We collect information you provide directly, including:</p>
            <ul>
                <li>Account details (name, email, phone number)</li>
                <li>Profile information and preferences</li>
                <li>Booking and transaction history</li>
                <li>Provider application documents (NIC, certifications)</li>
                <li>Communications with support or other users</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>2. How We Use Your Information</h2>
            <p>We use your information to:</p>
            <ul>
                <li>Provide and improve our services</li>
                <li>Process bookings and payments</li>
                <li>Verify provider applications</li>
                <li>Send important service notifications</li>
                <li>Ensure platform security and prevent fraud</li>
            </ul>
        </section>

        <section class="legal-section">
            <h2>3. Data Protection</h2>
            <p>We implement industry-standard security measures to protect your personal data. All sensitive information is encrypted in transit and at rest. File uploads are validated and stored securely.</p>
        </section>

        <section class="legal-section">
            <h2>4. Data Sharing</h2>
            <p>We do not sell your personal information. We may share limited data with service providers on the platform as necessary to fulfill bookings and deliver services.</p>
        </section>

        <section class="legal-section">
            <h2>5. Cookies</h2>
            <p>We use session cookies to maintain your login state and preferences. These are essential for the platform to function correctly.</p>
        </section>

        <section class="legal-section">
            <h2>6. Your Rights</h2>
            <p>You have the right to access, correct, or delete your personal data. You may also request a copy of your data or withdraw consent for optional data processing.</p>
        </section>

        <section class="legal-section">
            <h2>7. Data Retention</h2>
            <p>We retain your data for as long as your account is active or as needed to provide services. Provider application documents are retained for verification purposes.</p>
        </section>

        <section class="legal-section">
            <h2>8. Contact</h2>
            <p>For privacy concerns, please contact us at <a href="<?= $_base ?>/contact">our contact page</a>.</p>
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
