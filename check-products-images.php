<?php
// Direct database connection
$host = 'localhost';
$dbname = 'goplay';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get products from the shop page (public products)
$stmt = $pdo->query("SELECT id, name, images, status FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 10");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Products in Database (Public Shop)</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Images (Raw)</th><th>Images (Decoded)</th><th>Image Preview</th></tr>";

foreach ($products as $product) {
    echo "<tr>";
    echo "<td>{$product['id']}</td>";
    echo "<td>{$product['name']}</td>";
    echo "<td><pre>" . htmlspecialchars($product['images']) . "</pre></td>";
    
    $images = json_decode($product['images'], true);
    echo "<td><pre>" . print_r($images, true) . "</pre></td>";
    
    echo "<td>";
    if (!empty($images) && is_array($images)) {
        foreach ($images as $img) {
            echo "<img src='" . htmlspecialchars($img) . "' style='max-width:100px; margin:5px;' onerror=\"this.alt='FAILED: {$img}'\" /><br>";
            echo "<small>" . htmlspecialchars($img) . "</small><br>";
        }
    } else {
        echo "No images";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
