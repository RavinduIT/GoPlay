<?php
// Comprehensive debugging script
require_once 'bootstrap.php';

echo "<h1>GoPlay Database Debug Report</h1>";

// 1. Test basic PHP info
echo "<h2>1. PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . __DIR__ . "<br>";
echo "APP_DEBUG: " . ($_ENV['APP_DEBUG'] ?? 'not set') . "<br><br>";

// 2. Test database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'not set') . "<br>";
    echo "DB_PORT: " . ($_ENV['DB_PORT'] ?? 'not set') . "<br>";
    echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'not set') . "<br>";
    echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'not set') . "<br>";
    
    $db = Core\Database::getInstance();
    echo "✅ Database connection: SUCCESS<br><br>";
} catch (Exception $e) {
    echo "❌ Database connection: FAILED<br>";
    echo "Error: " . $e->getMessage() . "<br><br>";
    exit;
}

// 3. Test raw queries
echo "<h2>3. Raw Database Queries</h2>";
try {
    // Count records
    $result = $db->query("SELECT COUNT(*) as count FROM sports_facilities")->fetch();
    echo "Sports facilities count: " . $result['count'] . "<br>";
    
    $result = $db->query("SELECT COUNT(*) as count FROM sports_categories")->fetch();
    echo "Sports categories count: " . $result['count'] . "<br>";
    
    $result = $db->query("SELECT COUNT(*) as count FROM users")->fetch();
    echo "Users count: " . $result['count'] . "<br><br>";
} catch (Exception $e) {
    echo "❌ Raw queries failed: " . $e->getMessage() . "<br><br>";
}

// 4. Test models
echo "<h2>4. Model Tests</h2>";
try {
    $facilityModel = new App\Models\SportsFacility();
    echo "✅ SportsFacility model created successfully<br>";
    
    $categoryModel = new App\Models\SportsCategory();
    echo "✅ SportsCategory model created successfully<br>";
    
    // Test getAvailableFacilities
    $facilities = $facilityModel->getAvailableFacilities(['limit' => 3]);
    echo "Available facilities returned: " . count($facilities) . "<br>";
    
    // Test getAllActive
    $categories = $categoryModel->getAllActive();
    echo "Active categories returned: " . count($categories) . "<br><br>";
    
} catch (Exception $e) {
    echo "❌ Model test failed: " . $e->getMessage() . "<br><br>";
}

// 5. Test specific data
echo "<h2>5. Sample Data</h2>";
try {
    $facilities = $facilityModel->getAvailableFacilities(['limit' => 2]);
    
    if (empty($facilities)) {
        echo "❌ No facilities found<br>";
    } else {
        echo "✅ Found " . count($facilities) . " facilities:<br>";
        foreach ($facilities as $facility) {
            echo "- ID: {$facility['id']}, Name: {$facility['name']}, City: {$facility['city']}, Rate: LKR {$facility['hourly_rate']}<br>";
        }
    }
    echo "<br>";
    
} catch (Exception $e) {
    echo "❌ Sample data test failed: " . $e->getMessage() . "<br><br>";
}

// 6. Test controller
echo "<h2>6. Controller Test</h2>";
try {
    $request = new Core\Request();
    $controller = new App\Controllers\BookingController();
    
    echo "✅ BookingController created successfully<br>";
    echo "✅ Request object created successfully<br><br>";
    
} catch (Exception $e) {
    echo "❌ Controller test failed: " . $e->getMessage() . "<br><br>";
}

// 7. Show sample facility data in detail
echo "<h2>7. Detailed Sample Facility</h2>";
try {
    $facility = $facilityModel->getAvailableFacilities(['limit' => 1]);
    if (!empty($facility)) {
        echo "<pre>";
        print_r($facility[0]);
        echo "</pre>";
    } else {
        echo "No facility data to display<br>";
    }
} catch (Exception $e) {
    echo "❌ Detailed facility test failed: " . $e->getMessage() . "<br>";
}

echo "<h2>✅ Debug Complete</h2>";
?>