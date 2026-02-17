<?php
/**
 * Test API Endpoints
 * Access this at: http://localhost:3000/test-api.php
 */

require_once __DIR__ . '/bootstrap.php';

// Start session
session_start();

// Check if user is logged in
echo "<h2>Session Check:</h2>";
echo "<pre>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "User Type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "\n";
echo "User Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";
echo "</pre>";

// Test database connection
echo "<h2>Database Connection:</h2>";
try {
    $db = \Core\Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✓ Database connected successfully</p>";

    // Check if provider_applications table exists
    $stmt = $db->query("SHOW TABLES LIKE 'provider_applications'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ provider_applications table exists</p>";

        // Get count
        $stmt = $db->query("SELECT COUNT(*) as total FROM provider_applications");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total applications: " . $result['total'] . "</p>";

        // Get statistics
        $stmt = $db->query("SELECT
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM provider_applications");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<h3>Statistics:</h3>";
        echo "<pre>";
        print_r($stats);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>✗ provider_applications table does NOT exist</p>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test routes
echo "<h2>Available Routes:</h2>";
echo "<ul>";
echo "<li><a href='/provider/join'>Provider Role Selection</a></li>";
echo "<li><a href='/provider/apply/ground-owner'>Ground Owner Form</a></li>";
echo "<li><a href='/provider/apply/coach'>Coach Form</a></li>";
echo "<li><a href='/provider/apply/shop-owner'>Shop Owner Form</a></li>";
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    echo "<li><a href='/admin/provider-applications'>Admin Provider Applications</a></li>";
    echo "<li><a href='/admin/provider-applications/statistics' target='_blank'>Statistics API</a></li>";
    echo "<li><a href='/admin/provider-applications/list?status=pending' target='_blank'>Applications List API</a></li>";
}
echo "</ul>";

// Test API directly
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    echo "<h2>Test Statistics API:</h2>";
    try {
        $db = \Core\Database::getInstance()->getConnection();
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    SUM(CASE WHEN status = 'approved' AND DATE(reviewed_at) = CURDATE() THEN 1 ELSE 0 END) as approved_today
                FROM provider_applications";

        $stmt = $db->query($sql);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<pre>";
        echo json_encode(['success' => true, 'stats' => $stats], JSON_PRETTY_PRINT);
        echo "</pre>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background: #f5f5f5;
    }
    h2 {
        color: #333;
        border-bottom: 2px solid #667eea;
        padding-bottom: 10px;
    }
    pre {
        background: white;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    a {
        color: #667eea;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
