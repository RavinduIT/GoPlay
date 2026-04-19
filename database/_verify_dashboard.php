<?php
$html = @file_get_contents('http://localhost/GoPlay/admin/dashboard');
if (!$html) { echo "FAILED TO LOAD PAGE\n"; exit(1); }

echo "Page loaded (" . strlen($html) . " bytes)\n\n";
echo "=== FIX VERIFICATION ===\n";
echo "1. Public navbar (Book Ground): " . (strpos($html, 'Book Ground') !== false ? 'STILL VISIBLE (BAD)' : 'HIDDEN (GOOD)') . "\n";
echo "2. Public footer: " . (strpos($html, 'footer-container') !== false ? 'STILL VISIBLE (BAD)' : 'HIDDEN (GOOD)') . "\n";
echo "3. Notification bell btn: " . (strpos($html, 'notification-btn') !== false ? 'PRESENT (GOOD)' : 'MISSING (BAD)') . "\n";
echo "4. Revenue chart canvas: " . (strpos($html, 'revenueChart') !== false ? 'PRESENT (GOOD)' : 'MISSING (BAD)') . "\n";
echo "5. Admin sidebar: " . (strpos($html, 'admin-sidebar') !== false ? 'PRESENT (GOOD)' : 'MISSING (BAD)') . "\n";
echo "6. Dashboard CSS loaded: " . (strpos($html, 'admin-dashboard.css') !== false ? 'YES (GOOD)' : 'NO (BAD)') . "\n";
echo "7. Dashboard JS loaded: " . (strpos($html, 'admin-dashboard.js') !== false ? 'YES (GOOD)' : 'NO (BAD)') . "\n";
