<?php
/**
 * Test Admin API Endpoints with Session
 * Access this at: http://localhost:3000/test-admin-api.php
 * Make sure you're logged in as admin first
 */

require_once __DIR__ . '/bootstrap.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<h1>Admin API Test</h1>";

// Session Check
echo "<h2>1. Session Check:</h2>";
echo "<pre>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "User Type: " . ($_SESSION['user_type'] ?? 'NOT SET') . "\n";
echo "User Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";
echo "</pre>";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo "<p style='color: red;'>❌ You must be logged in as admin. <a href='/login'>Login here</a></p>";
    exit;
}

echo "<p style='color: green;'>✓ Logged in as admin</p>";

// Test Database Connection
echo "<h2>2. Database Connection:</h2>";
try {
    $db = \Core\Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✓ Database connected</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
    exit;
}

// Check provider_applications table
echo "<h2>3. Provider Applications Table:</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) as total FROM provider_applications");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total applications: " . $result['total'] . "</p>";

    if ($result['total'] > 0) {
        // Get sample data
        $stmt = $db->query("SELECT * FROM provider_applications LIMIT 1");
        $app = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<h3>Sample Application:</h3>";
        echo "<pre>";
        print_r([
            'id' => $app['id'],
            'provider_type' => $app['provider_type'],
            'first_name' => $app['first_name'],
            'last_name' => $app['last_name'],
            'email' => $app['email'],
            'status' => $app['status'],
            'created_at' => $app['created_at']
        ]);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test Statistics Query
echo "<h2>4. Test Statistics Query:</h2>";
try {
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
    print_r($stats);
    echo "</pre>";

    echo "<h3>JSON Output (what API should return):</h3>";
    echo "<pre>";
    echo json_encode(['success' => true, 'stats' => $stats], JSON_PRETTY_PRINT);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test Applications List Query
echo "<h2>5. Test Applications List Query:</h2>";
try {
    $page = 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM provider_applications
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p>Found " . count($applications) . " applications</p>";

    if (count($applications) > 0) {
        echo "<h3>First Application:</h3>";
        echo "<pre>";
        print_r([
            'id' => $applications[0]['id'],
            'provider_type' => $applications[0]['provider_type'],
            'first_name' => $applications[0]['first_name'],
            'last_name' => $applications[0]['last_name'],
            'email' => $applications[0]['email'],
            'phone' => $applications[0]['phone'],
            'status' => $applications[0]['status'],
            'created_at' => $applications[0]['created_at']
        ]);
        echo "</pre>";
    }

    // Get total count
    $stmt = $db->query("SELECT COUNT(*) as total FROM provider_applications");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $pagination = [
        'current_page' => $page,
        'total_pages' => ceil($total / $limit),
        'total_items' => $total,
        'items_per_page' => $limit
    ];

    echo "<h3>JSON Output (what API should return):</h3>";
    echo "<pre>";
    echo json_encode([
        'success' => true,
        'applications' => array_map(function($app) {
            return [
                'id' => $app['id'],
                'provider_type' => $app['provider_type'],
                'first_name' => $app['first_name'],
                'last_name' => $app['last_name'],
                'email' => $app['email'],
                'phone' => $app['phone'],
                'status' => $app['status'],
                'created_at' => $app['created_at']
            ];
        }, $applications),
        'pagination' => $pagination
    ], JSON_PRETTY_PRINT);
    echo "</pre>";

} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Test Links
echo "<h2>6. API Endpoint Links:</h2>";
echo "<ul>";
echo "<li><a href='/admin/provider-applications' target='_blank'>Admin Applications Page</a></li>";
echo "<li><a href='/admin/provider-applications/statistics' target='_blank'>Statistics API</a></li>";
echo "<li><a href='/admin/provider-applications/list' target='_blank'>Applications List API</a></li>";
echo "</ul>";

echo "<h2>7. JavaScript Console Test:</h2>";
echo "<button onclick='testAPI()'>Test API Calls</button>";
echo "<div id='apiResults' style='margin-top: 20px;'></div>";
?>

<script>
async function testAPI() {
    const resultsDiv = document.getElementById('apiResults');
    resultsDiv.innerHTML = '<p>Testing APIs...</p>';

    try {
        // Test statistics
        console.log('Testing statistics API...');
        const statsResponse = await fetch('/admin/provider-applications/statistics');
        const statsData = await statsResponse.json();
        console.log('Statistics response:', statsData);

        // Test applications list
        console.log('Testing applications list API...');
        const listResponse = await fetch('/admin/provider-applications/list');
        const listData = await listResponse.json();
        console.log('Applications list response:', listData);

        resultsDiv.innerHTML = `
            <h3>Statistics API:</h3>
            <pre>${JSON.stringify(statsData, null, 2)}</pre>
            <h3>Applications List API:</h3>
            <pre>${JSON.stringify(listData, null, 2)}</pre>
        `;
    } catch (error) {
        console.error('Error:', error);
        resultsDiv.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
    }
}
</script>

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
    }
    a {
        color: #667eea;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    button {
        background: #667eea;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background: #5568d3;
    }
</style>
