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

// Get the latest products with images
$stmt = $pdo->query("
    SELECT id, name, images, created_at 
    FROM products 
    WHERE images IS NOT NULL 
    AND images != '[]' 
    AND images != '' 
    ORDER BY created_at DESC 
    LIMIT 5
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Recent Products with Images</h2>";
echo "<style>
    body { font-family: Arial; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background: #4CAF50; color: white; }
    img { max-width: 150px; margin: 5px; border: 2px solid #ddd; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
</style>";

echo "<table>";
echo "<tr><th>ID</th><th>Name</th><th>Images JSON</th><th>Image Preview</th><th>Status</th></tr>";

foreach ($products as $product) {
    echo "<tr>";
    echo "<td>{$product['id']}</td>";
    echo "<td>" . htmlspecialchars($product['name']) . "</td>";
    echo "<td><code>" . htmlspecialchars($product['images']) . "</code></td>";
    
    $images = json_decode($product['images'], true);
    echo "<td>";
    
    if (!empty($images) && is_array($images)) {
        foreach ($images as $imagePath) {
            $fullPath = __DIR__ . '/public' . $imagePath;
            $fileExists = file_exists($fullPath);
            
            echo "<div style='margin: 10px 0; padding: 10px; background: #f5f5f5;'>";
            echo "<div>Path: <code>" . htmlspecialchars($imagePath) . "</code></div>";
            echo "<div>File exists: " . ($fileExists ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "</div>";
            echo "<div>Full path: <code>" . htmlspecialchars($fullPath) . "</code></div>";
            echo "<img src='" . htmlspecialchars($imagePath) . "' alt='Product Image' onerror=\"this.alt='FAILED TO LOAD'; this.style.border='3px solid red';\" />";
            echo "</div>";
        }
    } else {
        echo "No images or invalid JSON";
    }
    echo "</td>";
    
    $images = json_decode($product['images'], true);
    if (!empty($images) && is_array($images)) {
        $allExist = true;
        foreach ($images as $imagePath) {
            $fullPath = __DIR__ . '/public' . $imagePath;
            if (!file_exists($fullPath)) {
                $allExist = false;
                break;
            }
        }
        echo "<td class='" . ($allExist ? "success'>✓ All files exist" : "error'>✗ Some files missing") . "</td>";
    } else {
        echo "<td class='error'>No images</td>";
    }
    echo "</tr>";
}

echo "</table>";

echo "<h3>Test Direct Image Access</h3>";
echo "<p>Testing if images are accessible via web URLs:</p>";

// Get one image to test
if (!empty($products)) {
    $firstProduct = $products[0];
    $images = json_decode($firstProduct['images'], true);
    if (!empty($images)) {
        $testImage = $images[0];
        echo "<div style='padding: 20px; background: #f9f9f9; margin: 20px 0;'>";
        echo "<p>Test Image URL: <code>" . htmlspecialchars($testImage) . "</code></p>";
        echo "<img src='" . htmlspecialchars($testImage) . "' style='max-width: 300px; border: 3px solid #333;' onerror=\"this.alt='FAILED'; this.style.borderColor='red';\" />";
        echo "</div>";
    }
}
