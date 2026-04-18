<?php
require_once __DIR__ . '/../core/Database.php';
$db = \Core\Database::getInstance();
$db->query("UPDATE settings SET value = '0' WHERE key_name = 'maintenance_mode'");
echo "Maintenance mode disabled\n";

// Verify
$val = $db->query("SELECT value FROM settings WHERE key_name = 'maintenance_mode'")->fetch();
echo "maintenance_mode = " . $val['value'] . "\n";
