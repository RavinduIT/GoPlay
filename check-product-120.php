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

// Get product 120
$stmt = $pdo->prepare("SELECT id, name, images, created_at FROM products WHERE id = 120");
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h2>Product 120 Details</h2>";
echo "<style>body { font-family: Arial; padding: 20px; }</style>";

if ($product) {
    echo "<p><strong>ID:</strong> {$product['id']}</p>";
    echo "<p><strong>Name:</strong> " . htmlspecialchars($product['name']) . "</p>";
    echo "<p><strong>Created:</strong> {$product['created_at']}</p>";
    echo "<p><strong>Images (raw):</strong> <code>" . htmlspecialchars($product['images']) . "</code></p>";
    
    $images = json_decode($product['images'], true);
    echo "<p><strong>Images (decoded):</strong></p>";
    echo "<pre>" . print_r($images, true) . "</pre>";
    
    if (!empty($images) && is_array($images)) {
        echo "<h3>Image Files:</h3>";
        foreach ($images as $imagePath) {
            $fullPath = __DIR__ . $imagePath;
            $fileExists = file_exists($fullPath);
            
            echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0; background: #f9f9f9;'>";
            echo "<p><strong>Path:</strong> <code>" . htmlspecialchars($imagePath) . "</code></p>";
            echo "<p><strong>Full Path:</strong> <code>" . htmlspecialchars($fullPath) . "</code></p>";
            echo "<p><strong>File Exists:</strong> " . ($fileExists ? "<span style='color:green;'>YES</span>" : "<span style='color:red;'>NO</span>") . "</p>";
            
            if ($fileExists) {
                echo "<p><strong>File Size:</strong> " . number_format(filesize($fullPath)) . " bytes</p>";
            }
            
            echo "<p><strong>Image Test:</strong></p>";
            echo "<img src='" . htmlspecialchars($imagePath) . "' style='max-width: 300px; border: 2px solid #333;' onerror=\"this.alt='FAILED TO LOAD'; this.style.borderColor='red';\" />";
            echo "</div>";
        }
    } else {
        echo "<p style='color: red;'>No images found or images is not an array</p>";
    }
} else {
    echo "<p style='color: red;'>Product 120 not found</p>";
}

// Also check the most recent products
echo "<hr><h2>Recent Products</h2>";
$stmt = $pdo->query("SELECT id, name, images FROM products ORDER BY id DESC LIMIT 5");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Has Images</th><th>Image Paths</th></tr>";
foreach ($products as $p) {
    $imgs = json_decode($p['images'], true);
    $hasImages = !empty($imgs) && is_array($imgs);
    echo "<tr>";
    echo "<td>{$p['id']}</td>";
    echo "<td>" . htmlspecialchars($p['name']) . "</td>";
    echo "<td>" . ($hasImages ? "<span style='color:green;'>YES</span>" : "<span style='color:red;'>NO</span>") . "</td>";
    echo "<td><code>" . htmlspecialchars($p['images']) . "</code></td>";
    echo "</tr>";
}
echo "</table>";
