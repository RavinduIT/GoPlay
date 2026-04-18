<?php
// Test bulk settings save
$ch = curl_init('http://localhost/GoPlay/api/admin/settings/bulk');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'settings' => [
        'support_phone' => '+94 77 999 8888',
        'contact_email' => 'test@goplay.lk'
    ]
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

// Verify it saved
require_once __DIR__ . '/../core/Database.php';
$db = \Core\Database::getInstance();
$result = $db->query("SELECT key_name, value FROM settings WHERE key_name IN ('support_phone', 'contact_email')")->fetchAll();
echo "\nVerification from DB:\n";
foreach ($result as $row) {
    echo "  {$row['key_name']} = {$row['value']}\n";
}
