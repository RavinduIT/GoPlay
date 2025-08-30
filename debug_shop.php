<?php
// Shop-specific debugging script
require_once 'bootstrap.php';

echo "<h1>Shop Debug Report</h1>";

// 1. Test database tables
echo "<h2>1. Database Tables Check</h2>";
try {
    $db = Core\Database::getInstance();
    
    // Check if tables exist
    $tables = $db->query("SHOW TABLES")->fetchAll();
    $tableNames = array_column($tables, array_values($tables[0])[0]);
    
    echo "Available tables: " . implode(', ', $tableNames) . "<br>";
    
    $requiredTables = ['products', 'categories', 'product_categories'];
    foreach ($requiredTables as $table) {
        if (in_array($table, $tableNames)) {
            echo "✅ Table '{$table}' exists<br>";
        } else {
            echo "❌ Table '{$table}' missing<br>";
        }
    }
    echo "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br><br>";
    exit;
}

// 2. Check data counts
echo "<h2>2. Data Counts</h2>";
try {
    $result = $db->query("SELECT COUNT(*) as count FROM products")->fetch();
    echo "Products: " . $result['count'] . "<br>";
    
    $result = $db->query("SELECT COUNT(*) as count FROM categories")->fetch();
    echo "Categories: " . $result['count'] . "<br>";
    
    // Check active products specifically
    $result = $db->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'")->fetch();
    echo "Active products: " . $result['count'] . "<br><br>";
    
} catch (Exception $e) {
    echo "❌ Count queries failed: " . $e->getMessage() . "<br><br>";
}

// 3. Test models
echo "<h2>3. Model Tests</h2>";
try {
    $productModel = new App\Models\Product();
    echo "✅ Product model created<br>";
    
    $categoryModel = new App\Models\Category();
    echo "✅ Category model created<br>";
    
    // Test getActiveProducts
    $products = $productModel->getActiveProducts(['limit' => 3]);
    echo "Active products returned: " . count($products) . "<br>";
    
    // Test categories
    $categories = $categoryModel->getCategoriesWithProductCounts();
    echo "Categories with counts: " . count($categories) . "<br><br>";
    
} catch (Exception $e) {
    echo "❌ Model test failed: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre><br>";
}

// 4. Test controller
echo "<h2>4. Controller Test</h2>";
try {
    $request = new Core\Request();
    $controller = new App\Controllers\ProductController();
    echo "✅ ProductController created<br>";
    
    // Try to call index method
    $response = $controller->index($request);
    echo "✅ ProductController->index() executed successfully<br><br>";
    
} catch (Exception $e) {
    echo "❌ Controller test failed: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre><br>";
}

// 5. Sample data
echo "<h2>5. Sample Products</h2>";
try {
    $products = $productModel->getActiveProducts(['limit' => 3]);
    
    if (empty($products)) {
        echo "❌ No products found<br>";
        
        // Check raw SQL
        echo "<h3>Raw SQL Debug</h3>";
        $result = $db->query("SELECT * FROM products LIMIT 3")->fetchAll();
        echo "Raw products count: " . count($result) . "<br>";
        if (!empty($result)) {
            echo "Sample raw product:<br>";
            echo "<pre>" . print_r($result[0], true) . "</pre>";
        }
        
    } else {
        echo "✅ Found " . count($products) . " products:<br>";
        foreach ($products as $product) {
            echo "- ID: {$product['id']}, Name: {$product['name']}, Price: LKR {$product['price']}<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Sample data failed: " . $e->getMessage() . "<br>";
}

echo "<h2>6. Quick Fix</h2>";
echo "If no products found, run this SQL to add test data:<br>";
echo "<textarea rows='10' cols='80'>";
echo "INSERT INTO categories (name, slug, description, icon) VALUES
('Sports Equipment', 'sports-equipment', 'Sports gear and equipment', 'fas fa-running'),
('Fitness', 'fitness', 'Fitness and gym equipment', 'fas fa-dumbbell'),
('Outdoor Sports', 'outdoor-sports', 'Outdoor sports gear', 'fas fa-mountain');

INSERT INTO products (name, description, category_id, price, stock_quantity, status, rating) VALUES
('Football', 'Professional football for matches', 1, 2500.00, 50, 'active', 4.5),
('Tennis Racket', 'High quality tennis racket', 1, 8500.00, 25, 'active', 4.7),
('Yoga Mat', 'Premium yoga mat for fitness', 2, 1500.00, 100, 'active', 4.3);";
echo "</textarea>";

echo "<h2>✅ Shop Debug Complete</h2>";
?>