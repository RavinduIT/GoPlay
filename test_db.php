<?php
// Test database connection
require_once 'bootstrap.php';

try {
    $db = Core\Database::getInstance();
    echo "✅ Database connection successful!\n\n";
    
    // Test query
    $result = $db->query("SELECT COUNT(*) as count FROM sports_facilities")->fetch();
    echo "Sports facilities count: " . $result['count'] . "\n\n";
    
    // Test model
    $facilityModel = new App\Models\SportsFacility();
    $facilities = $facilityModel->getAvailableFacilities(['limit' => 5]);
    echo "Facilities from model: " . count($facilities) . "\n";
    
    foreach ($facilities as $facility) {
        echo "- {$facility['name']} in {$facility['city']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>