<?php
/**
 * Debug API Response
 * This script simulates the exact API call and shows the raw response
 */

require_once __DIR__ . '/bootstrap.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>API Response Debug</h1>";

// Show session
echo "<h2>Current Session:</h2>";
echo "<pre>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "User Type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "\n";
echo "Session ID: " . session_id() . "\n";
echo "</pre>";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "<p style='color: red;'>❌ Not logged in as admin. <a href='/login'>Login here</a></p>";
    echo "<p>Note: You must login as admin first, then come back to this page.</p>";
    exit;
}

echo "<h2>Raw API Response Test:</h2>";

// Simulate the exact API call
$_GET['page'] = 1;
$_GET['limit'] = 10;
$_GET['status'] = '';
$_GET['type'] = '';
$_GET['search'] = '';

try {
    // Create request object
    $request = new \Core\Request();

    // Create controller
    $controller = new \App\Controllers\AdminController();

    // Call the method
    echo "<h3>Calling getApplicationsList()...</h3>";
    $response = $controller->getApplicationsList($request);

    echo "<h3>Response Headers:</h3>";
    echo "<pre>";
    foreach ($response->getHeaders() as $header => $value) {
        echo "$header: $value\n";
    }
    echo "</pre>";

    echo "<h3>Response Body:</h3>";
    echo "<pre>";
    $body = $response->getBody();
    echo htmlspecialchars($body);
    echo "</pre>";

    echo "<h3>Decoded JSON:</h3>";
    echo "<pre>";
    $decoded = json_decode($body, true);
    if ($decoded) {
        print_r($decoded);
    } else {
        echo "ERROR: Not valid JSON!\n";
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
    echo "</pre>";

} catch (\Exception $e) {
    echo "<h3 style='color: red;'>Exception Caught:</h3>";
    echo "<pre>";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}

// Also test statistics
echo "<hr>";
echo "<h2>Statistics API Test:</h2>";
try {
    $request = new \Core\Request();
    $controller = new \App\Controllers\AdminController();

    echo "<h3>Calling getApplicationsStatistics()...</h3>";
    $response = $controller->getApplicationsStatistics($request);

    echo "<h3>Response Body:</h3>";
    echo "<pre>";
    $body = $response->getBody();
    echo htmlspecialchars($body);
    echo "</pre>";

    echo "<h3>Decoded JSON:</h3>";
    echo "<pre>";
    $decoded = json_decode($body, true);
    if ($decoded) {
        print_r($decoded);
    } else {
        echo "ERROR: Not valid JSON!\n";
    }
    echo "</pre>";

} catch (\Exception $e) {
    echo "<h3 style='color: red;'>Exception Caught:</h3>";
    echo "<pre>";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background: #f5f5f5;
    }
    h1, h2, h3 {
        color: #333;
    }
    h2 {
        border-bottom: 2px solid #667eea;
        padding-bottom: 10px;
        margin-top: 30px;
    }
    pre {
        background: white;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
        overflow-x: auto;
        max-height: 500px;
    }
    a {
        color: #667eea;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
