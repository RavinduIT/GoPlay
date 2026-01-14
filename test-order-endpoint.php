<?php
/**
 * Simple test script to verify the order endpoint is working
 * Access via: http://localhost/test-order-endpoint.php?order_id=1
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode([
        'error' => 'Not logged in',
        'session' => $_SESSION
    ]));
}

// Get order ID from query
$orderId = $_GET['order_id'] ?? 1;

echo "<h1>Testing Order Endpoint</h1>";
echo "<p>Session User ID: " . ($_SESSION['user_id'] ?? 'none') . "</p>";
echo "<p>Session User Type: " . ($_SESSION['user_type'] ?? 'none') . "</p>";
echo "<p>Testing Order ID: $orderId</p>";

echo "<h2>Test 1: Direct Endpoint Call</h2>";
$url = "/shop-owner/get-order?id=$orderId";
echo "<p>URL: <a href='$url' target='_blank'>$url</a></p>";

echo "<h2>Test 2: Using CURL</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . "/shop-owner/get-order?id=$orderId");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<pre>";
echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo htmlspecialchars($response);
echo "</pre>";

echo "<h2>Test 3: JavaScript Fetch</h2>";
?>
<div id="fetchResult"></div>
<script>
async function testFetch() {
    try {
        const response = await fetch('/shop-owner/get-order?id=<?php echo $orderId; ?>', {
            credentials: 'same-origin'
        });
        const data = await response.json();
        document.getElementById('fetchResult').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
    } catch (error) {
        document.getElementById('fetchResult').innerHTML = '<pre>Error: ' + error.message + '</pre>';
    }
}
testFetch();
</script>
