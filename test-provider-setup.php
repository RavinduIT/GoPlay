<?php
/**
 * Provider Setup Test Script
 * Run this file to check if everything is set up correctly
 */

echo "=== GoPlay Provider Setup Test ===\n\n";

// Test 1: Check if files exist
echo "1. Checking if required files exist...\n";
$files = [
    'Controller' => __DIR__ . '/app/controllers/ProviderController.php',
    'Role Selection View' => __DIR__ . '/app/views/provider/role-selection.php',
    'Ground Owner Form' => __DIR__ . '/app/views/provider/ground-owner-form.php',
    'Coach Form' => __DIR__ . '/app/views/provider/coach-form.php',
    'Shop Owner Form' => __DIR__ . '/app/views/provider/shop-owner-form.php',
    'Application CSS' => __DIR__ . '/public/css/pages/provider-application.css',
    'Role Selection CSS' => __DIR__ . '/public/css/pages/provider-role-selection.css',
    'Application JS' => __DIR__ . '/public/js/provider-application.js',
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "   ✓ $name exists\n";
    } else {
        echo "   ✗ $name NOT FOUND at: $path\n";
    }
}

// Test 2: Check upload directory
echo "\n2. Checking upload directory...\n";
$uploadDir = __DIR__ . '/public/uploads/provider-applications';
if (file_exists($uploadDir)) {
    echo "   ✓ Upload directory exists\n";
    if (is_writable($uploadDir)) {
        echo "   ✓ Upload directory is writable\n";
    } else {
        echo "   ✗ Upload directory is NOT writable\n";
        echo "   → Run: chmod 755 $uploadDir\n";
    }
} else {
    echo "   ✗ Upload directory does not exist\n";
    echo "   → Creating directory...\n";
    if (mkdir($uploadDir, 0755, true)) {
        echo "   ✓ Directory created successfully\n";
    } else {
        echo "   ✗ Failed to create directory\n";
    }
}

// Test 3: Check database connection
echo "\n3. Checking database connection...\n";
require_once __DIR__ . '/bootstrap.php';

try {
    $db = (new App\Models\User())->getConnection();
    echo "   ✓ Database connection successful\n";

    // Test 4: Check if provider_applications table exists
    echo "\n4. Checking provider_applications table...\n";
    $stmt = $db->query("SHOW TABLES LIKE 'provider_applications'");
    if ($stmt->rowCount() > 0) {
        echo "   ✓ provider_applications table exists\n";

        // Get column count
        $stmt = $db->query("SELECT COUNT(*) as total FROM provider_applications");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   ℹ Current applications: " . $result['total'] . "\n";
    } else {
        echo "   ✗ provider_applications table does NOT exist\n";
        echo "   → You need to run the migration SQL:\n";
        echo "   → mysql -u username -p database_name < database/migrations/create_provider_applications_table.sql\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

// Test 5: Check routes in index.php
echo "\n5. Checking routes in index.php...\n";
$indexContent = file_get_contents(__DIR__ . '/index.php');
$routes = [
    '/provider/join' => 'ProviderController@join',
    '/provider/apply/ground-owner' => 'ProviderController@applyGroundOwner',
    '/provider/apply/coach' => 'ProviderController@applyCoach',
    '/provider/apply/shop-owner' => 'ProviderController@applyShopOwner',
    '/provider/submit-application' => 'ProviderController@submitApplication',
];

foreach ($routes as $route => $controller) {
    if (strpos($indexContent, $route) !== false && strpos($indexContent, $controller) !== false) {
        echo "   ✓ Route $route exists\n";
    } else {
        echo "   ✗ Route $route NOT FOUND\n";
    }
}

// Test 6: Check PHP syntax
echo "\n6. Checking PHP syntax...\n";
$phpFiles = [
    __DIR__ . '/app/controllers/ProviderController.php',
    __DIR__ . '/app/views/provider/role-selection.php',
    __DIR__ . '/app/views/provider/ground-owner-form.php',
];

foreach ($phpFiles as $file) {
    $output = [];
    $return = 0;
    exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return);
    $filename = basename($file);
    if ($return === 0) {
        echo "   ✓ $filename syntax OK\n";
    } else {
        echo "   ✗ $filename has syntax errors\n";
        echo "   → " . implode("\n", $output) . "\n";
    }
}

echo "\n=== Test Complete ===\n\n";
echo "Next steps:\n";
echo "1. If the database table doesn't exist, run the migration SQL\n";
echo "2. Make sure the upload directory exists and is writable\n";
echo "3. Access the provider page at: http://yoursite.com/provider/join\n";
echo "4. Check your web server error logs if pages don't load\n\n";
