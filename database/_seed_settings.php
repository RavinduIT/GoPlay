<?php
require_once __DIR__ . '/../core/Database.php';

$db = \Core\Database::getInstance();
$conn = $db->getConnection();

$settings = [
    ['contact_email', 'contact@goplay.lk', 'Primary contact email', 'string'],
    ['support_phone', '+94 77 123 4567', 'Support phone number', 'string'],
    ['timezone', 'Asia/Colombo', 'Default timezone', 'string'],
    ['maintenance_mode', '0', 'Maintenance mode flag', 'boolean'],
    ['email_from_name', 'GoPlay Sports', 'Email from name', 'string'],
    ['email_from_address', 'noreply@goplay.lk', 'Email from address', 'string'],
    ['smtp_host', 'smtp.gmail.com', 'SMTP host', 'string'],
    ['smtp_port', '587', 'SMTP port', 'number'],
    ['smtp_encryption', 'tls', 'SMTP encryption type', 'string'],
    ['smtp_username', '', 'SMTP username', 'string'],
    ['smtp_password', '', 'SMTP password', 'string'],
    ['enable_stripe', '0', 'Enable Stripe payments', 'boolean'],
    ['stripe_public_key', '', 'Stripe public key', 'string'],
    ['stripe_secret_key', '', 'Stripe secret key', 'string'],
    ['enable_paypal', '0', 'Enable PayPal payments', 'boolean'],
    ['paypal_client_id', '', 'PayPal client ID', 'string'],
    ['paypal_secret', '', 'PayPal secret', 'string'],
    ['enable_cash', '1', 'Enable cash on delivery', 'boolean'],
    ['min_booking_duration', '1', 'Min booking hours', 'number'],
    ['cancellation_hours', '24', 'Cancellation notice hours', 'number'],
    ['allow_same_day_booking', '1', 'Allow same day booking', 'boolean'],
    ['require_payment_upfront', '0', 'Require payment before booking', 'boolean'],
    ['auto_approve_bookings', '1', 'Auto-approve bookings', 'boolean'],
    ['enable_2fa', '0', 'Two-factor authentication', 'boolean'],
    ['session_timeout', '60', 'Session timeout minutes', 'number'],
    ['password_min_length', '8', 'Min password length', 'number'],
    ['require_strong_password', '1', 'Require strong passwords', 'boolean'],
    ['max_login_attempts', '5', 'Max login attempts before lockout', 'number'],
    ['lockout_duration', '30', 'Lockout duration minutes', 'number'],
    ['enable_activity_logs', '1', 'Enable activity logging', 'boolean'],
];

$stmt = $conn->prepare('INSERT IGNORE INTO settings (key_name, value, description, type) VALUES (?, ?, ?, ?)');
$count = 0;
foreach ($settings as $s) {
    $stmt->execute($s);
    $count += $stmt->rowCount();
}

echo "Inserted {$count} new settings rows\n";

// Verify
$all = $conn->query("SELECT key_name FROM settings ORDER BY key_name")->fetchAll();
echo "Total settings in DB: " . count($all) . "\n";
