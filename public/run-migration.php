<?php
/**
 * Web-based Migration Runner
 * Run this through your browser: http://localhost/main/GoPlay/public/run-migration.php
 */

require_once __DIR__ . '/../bootstrap.php';

use Core\Database;

// Set content type to plain text for better readability
header('Content-Type: text/plain; charset=utf-8');

echo "=========================================\n";
echo "RUNNING MIGRATION: shop_owner_id Column\n";
echo "=========================================\n\n";

try {
    $db = Database::getInstance();
    
    // Step 1: Check if column already exists
    echo "Step 1: Checking if shop_owner_id column exists...\n";
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'shop_owner_id'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✓ Column already exists! No migration needed.\n\n";
    } else {
        echo "✗ Column does not exist. Adding it now...\n\n";
        
        // Step 2: Add the column
        echo "Step 2: Adding shop_owner_id column...\n";
        $db->query("ALTER TABLE products ADD COLUMN shop_owner_id INT NULL DEFAULT NULL AFTER category_id");
        echo "✓ Column added successfully!\n\n";
        
        // Step 3: Add foreign key constraint
        echo "Step 3: Adding foreign key constraint...\n";
        try {
            $db->query("ALTER TABLE products 
                       ADD CONSTRAINT fk_products_shop_owner 
                       FOREIGN KEY (shop_owner_id) 
                       REFERENCES users(id) 
                       ON DELETE SET NULL 
                       ON UPDATE CASCADE");
            echo "✓ Foreign key constraint added successfully!\n\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                echo "✓ Foreign key constraint already exists (skipped)\n\n";
            } else {
                echo "⚠ Warning: Could not add foreign key - " . $e->getMessage() . "\n";
                echo "  (This is okay, you can continue)\n\n";
            }
        }
        
        // Step 4: Add index
        echo "Step 4: Adding index for performance...\n";
        try {
            $db->query("CREATE INDEX idx_products_shop_owner ON products(shop_owner_id)");
            echo "✓ Index added successfully!\n\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                echo "✓ Index already exists (skipped)\n\n";
            } else {
                echo "⚠ Warning: Could not add index - " . $e->getMessage() . "\n";
                echo "  (This is okay, you can continue)\n\n";
            }
        }
        
        // Step 5: Add compound index
        echo "Step 5: Adding compound index (shop_owner_id + status)...\n";
        try {
            $db->query("CREATE INDEX idx_products_owner_status ON products(shop_owner_id, status)");
            echo "✓ Compound index added successfully!\n\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                echo "✓ Compound index already exists (skipped)\n\n";
            } else {
                echo "⚠ Warning: Could not add compound index - " . $e->getMessage() . "\n";
                echo "  (This is okay, you can continue)\n\n";
            }
        }
    }
    
    // Step 6: Verify the migration
    echo "Step 6: Verifying migration...\n";
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'shop_owner_id'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✓ Migration successful! shop_owner_id column exists.\n";
        echo "  Column details:\n";
        echo "    - Type: {$result['Type']}\n";
        echo "    - Null: {$result['Null']}\n";
        echo "    - Default: " . ($result['Default'] ?? 'NULL') . "\n\n";
    } else {
        echo "✗ Migration failed! Column does not exist.\n\n";
        exit(1);
    }
    
    // Step 7: Check products status
    echo "Step 7: Checking products...\n";
    $stmt = $db->query("SELECT COUNT(*) as total, COUNT(shop_owner_id) as with_owner FROM products");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "  Total products: {$stats['total']}\n";
    echo "  Products with shop_owner_id: {$stats['with_owner']}\n";
    echo "  Products without shop_owner_id: " . ($stats['total'] - $stats['with_owner']) . "\n\n";
    
    if ($stats['with_owner'] == 0 && $stats['total'] > 0) {
        echo "⚠ IMPORTANT: You have {$stats['total']} products without shop_owner_id assigned!\n";
        echo "  Each product needs to be assigned to a shop owner.\n";
        echo "  Visit: http://localhost/main/GoPlay/public/assign-products.php\n\n";
    } else if ($stats['with_owner'] > 0) {
        echo "✓ Great! You have products assigned to shop owners.\n\n";
    }
    
    echo "=========================================\n";
    echo "MIGRATION COMPLETED SUCCESSFULLY!\n";
    echo "=========================================\n\n";
    
    echo "Next Steps:\n";
    echo "1. If you have unassigned products, visit: http://localhost/main/GoPlay/public/assign-products.php\n";
    echo "2. Refresh your shop owner orders page\n";
    echo "3. Each shop owner will now see only their own orders!\n\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR: Migration failed!\n";
    echo "Error message: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n\n";
}
