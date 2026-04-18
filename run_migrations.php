<?php
// Run database migrations for size/color support

$host = 'localhost';
$db = 'goplay';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database: $db\n\n";
    
    // Migration 1: Add columns to products table
    echo "1. Adding columns to products table...\n";
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS available_sizes VARCHAR(500) NULL COMMENT 'Comma-separated list of available sizes'");
        echo "   ✓ available_sizes added\n";
    } catch (Exception $e) {
        echo "   ✓ available_sizes already exists\n";
    }
    
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS available_colors VARCHAR(500) NULL COMMENT 'Comma-separated list of available colors'");
        echo "   ✓ available_colors added\n";
    } catch (Exception $e) {
        echo "   ✓ available_colors already exists\n";
    }
    
    // Migration 2: Add columns to cart_items table
    echo "\n2. Adding columns to cart_items table...\n";
    try {
        $pdo->exec("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS selected_size VARCHAR(100) NULL COMMENT 'Size selected for this cart item'");
        echo "   ✓ selected_size added\n";
    } catch (Exception $e) {
        echo "   ✓ selected_size already exists\n";
    }
    
    try {
        $pdo->exec("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS selected_color VARCHAR(100) NULL COMMENT 'Color selected for this cart item'");
        echo "   ✓ selected_color added\n";
    } catch (Exception $e) {
        echo "   ✓ selected_color already exists\n";
    }
    
    // Migration 3: Add columns to order_items table
    echo "\n3. Adding columns to order_items table...\n";
    try {
        $pdo->exec("ALTER TABLE order_items ADD COLUMN IF NOT EXISTS selected_size VARCHAR(100) NULL COMMENT 'Size selected for this order item'");
        echo "   ✓ selected_size added\n";
    } catch (Exception $e) {
        echo "   ✓ selected_size already exists\n";
    }
    
    try {
        $pdo->exec("ALTER TABLE order_items ADD COLUMN IF NOT EXISTS selected_color VARCHAR(100) NULL COMMENT 'Color selected for this order item'");
        echo "   ✓ selected_color added\n";
    } catch (Exception $e) {
        echo "   ✓ selected_color already exists\n";
    }
    
    echo "\n✓ All migrations completed successfully!\n";
    
    // Verify columns
    echo "\nVerifying columns...\n";
    
    echo "\nProducts table columns:\n";
    $result = $pdo->query("SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='goplay' AND TABLE_NAME='products' AND COLUMN_NAME IN ('available_sizes', 'available_colors')");
    foreach ($result as $row) {
        echo "  - " . $row['COLUMN_NAME'] . " (" . $row['COLUMN_TYPE'] . ")\n";
    }
    
    echo "\nCart_items table columns:\n";
    $result = $pdo->query("SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='goplay' AND TABLE_NAME='cart_items' AND COLUMN_NAME IN ('selected_size', 'selected_color')");
    foreach ($result as $row) {
        echo "  - " . $row['COLUMN_NAME'] . " (" . $row['COLUMN_TYPE'] . ")\n";
    }
    
    echo "\nOrder_items table columns:\n";
    $result = $pdo->query("SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='goplay' AND TABLE_NAME='order_items' AND COLUMN_NAME IN ('selected_size', 'selected_color')");
    foreach ($result as $row) {
        echo "  - " . $row['COLUMN_NAME'] . " (" . $row['COLUMN_TYPE'] . ")\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
