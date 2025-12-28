<?php
/**
 * Fix image paths in products table
 * Ensure all paths have /public/ prefix for web accessibility
 */

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

// Get all products with images
$stmt = $pdo->query("SELECT id, images FROM products WHERE images IS NOT NULL AND images != '[]' AND images != ''");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$fixed = 0;
$alreadyCorrect = 0;

foreach ($products as $product) {
    $images = json_decode($product['images'], true);
    
    if (!is_array($images) || empty($images)) {
        continue;
    }
    
    $needsUpdate = false;
    $fixedImages = [];
    
    foreach ($images as $imagePath) {
        // Check if path doesn't have /public/ prefix
        if (strpos($imagePath, '/public/') !== 0 && strpos($imagePath, '/assets/') === 0) {
            // Add /public/ prefix
            $fixedImages[] = '/public' . $imagePath;
            $needsUpdate = true;
        } else {
            $fixedImages[] = $imagePath;
        }
    }
    
    if ($needsUpdate) {
        $newImagesJson = json_encode($fixedImages);
        $updateStmt = $pdo->prepare("UPDATE products SET images = ? WHERE id = ?");
        $updateStmt->execute([$newImagesJson, $product['id']]);
        
        echo "Product {$product['id']}: Fixed<br>";
        echo "&nbsp;&nbsp;Old: {$product['images']}<br>";
        echo "&nbsp;&nbsp;New: {$newImagesJson}<br><br>";
        $fixed++;
    } else {
        echo "Product {$product['id']}: Already correct<br>";
        $alreadyCorrect++;
    }
}

echo "<br>==================<br>";
echo "Summary:<br>";
echo "&nbsp;&nbsp;Fixed: $fixed<br>";
echo "&nbsp;&nbsp;Already correct: $alreadyCorrect<br>";
echo "&nbsp;&nbsp;Total processed: " . count($products) . "<br>";

