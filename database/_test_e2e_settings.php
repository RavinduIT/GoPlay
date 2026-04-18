<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/SiteSettings.php';

// 1. Set a test value
$db = \Core\Database::getInstance();
$db->query("UPDATE settings SET value = 'GoPlay Test Site Name' WHERE key_name = 'site_name'");
$db->query("UPDATE settings SET value = '+94 77 888 9999' WHERE key_name = 'support_phone'");
$db->query("UPDATE settings SET value = 'support@goplay.lk' WHERE key_name = 'contact_email'");
echo "1. Updated settings in DB\n";

// 2. Clear cache
\Core\SiteSettings::flush();
\Core\SiteSettings::load();

// 3. Verify SiteSettings reads them
echo "2. SiteSettings::get('site_name') = " . \Core\SiteSettings::get('site_name') . "\n";
echo "   SiteSettings::get('support_phone') = " . \Core\SiteSettings::get('support_phone') . "\n";
echo "   SiteSettings::get('contact_email') = " . \Core\SiteSettings::get('contact_email') . "\n";
echo "   SiteSettings::get('smtp_host') = " . \Core\SiteSettings::get('smtp_host', 'NOT SET') . "\n";
echo "   SiteSettings::getBool('maintenance_mode') = " . (\Core\SiteSettings::getBool('maintenance_mode') ? 'true' : 'false') . "\n";
echo "   SiteSettings::getInt('booking_advance_days') = " . \Core\SiteSettings::getInt('booking_advance_days') . "\n";

// 4. Verify it shows in HTML output  
echo "\n3. Fetching homepage title...\n";
$html = @file_get_contents('http://localhost/GoPlay/');
if (preg_match('/<title>(.*?)<\/title>/', $html, $matches)) {
    echo "   Page <title> = " . $matches[1] . "\n";
}
if (strpos($html, '+94 77 888 9999') !== false) {
    echo "   Footer phone: FOUND +94 77 888 9999 ✓\n";
} else {
    echo "   Footer phone: NOT FOUND ✗\n";
}
if (strpos($html, 'support@goplay.lk') !== false) {
    echo "   Footer email: FOUND support@goplay.lk ✓\n";
} else {
    echo "   Footer email: NOT FOUND ✗\n";
}

// 5. Reset to original values
$db->query("UPDATE settings SET value = 'GoPlay Sports Platform' WHERE key_name = 'site_name'");
$db->query("UPDATE settings SET value = '+94 77 123 4567' WHERE key_name = 'support_phone'");
$db->query("UPDATE settings SET value = 'contact@goplay.lk' WHERE key_name = 'contact_email'");
echo "\n4. Reset settings to defaults\n";
