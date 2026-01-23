<?php
/**
 * Quick Fix Script for Shop Owner Orders
 * 
 * This script will:
 * 1. Check if shop_owner_id column exists in products table
 * 2. Add it if missing
 * 3. Update existing products to assign them to shop owners
 */

require_once 'bootstrap.php';

use Core\Database;

$db = Database::getInstance();

echo "=========================================\n";
echo "SHOP OWNER ORDERS - QUICK FIX SCRIPT\n";
echo "=========================================\n\n";

// Step 1: Check if column exists
echo "Step 1: Checking if shop_owner_id column exists...\n";
try {
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'shop_owner_id'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✓ Column EXISTS\n\n";
        $needsColumnCreation = false;
    } else {
        echo "✗ Column DOES NOT EXIST\n";
        echo "  Creating column...\n";
        $needsColumnCreation = true;
        
        // Add the column
        $db->query("ALTER TABLE products ADD COLUMN shop_owner_id INT NULL DEFAULT NULL AFTER category_id");
        echo "✓ Column created successfully\n\n";
        
        // Add foreign key
        try {
            $db->query("ALTER TABLE products ADD CONSTRAINT fk_products_shop_owner 
                       FOREIGN KEY (shop_owner_id) REFERENCES users(id) ON DELETE SET NULL");
            echo "✓ Foreign key constraint added\n\n";
        } catch (Exception $e) {
            echo "  Note: Foreign key constraint may already exist or users table needs adjustment\n\n";
        }
        
        // Add index
        try {
            $db->query("CREATE INDEX idx_products_shop_owner ON products(shop_owner_id)");
            echo "✓ Index created\n\n";
        } catch (Exception $e) {
            echo "  Note: Index may already exist\n\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Check products without shop_owner_id
echo "Step 2: Checking products without shop_owner_id...\n";
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM products WHERE shop_owner_id IS NULL");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nullCount = $result['count'];
    
    echo "  Products without shop_owner_id: $nullCount\n";
    
    if ($nullCount > 0) {
        echo "\n  Do you want to assign these products to a shop owner? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim($line) === 'y' || trim($line) === 'Y') {
            // Get available shop owners
            $stmt = $db->query("SELECT id, username, email FROM users WHERE user_type = 'shop_owner'");
            $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($owners)) {
                echo "\n✗ No shop owners found in the system!\n";
                echo "  You need to create a shop owner account first.\n\n";
            } else {
                echo "\n  Available shop owners:\n";
                foreach ($owners as $owner) {
                    echo "    ID: {$owner['id']} - {$owner['username']} ({$owner['email']})\n";
                }
                
                echo "\n  Enter shop owner ID to assign products to: ";
                $handle = fopen("php://stdin", "r");
                $ownerId = trim(fgets($handle));
                fclose($handle);
                
                if (is_numeric($ownerId)) {
                    $stmt = $db->query("UPDATE products SET shop_owner_id = ? WHERE shop_owner_id IS NULL", [(int)$ownerId]);
                    echo "✓ Products updated successfully!\n\n";
                } else {
                    echo "✗ Invalid shop owner ID\n\n";
                }
            }
        }
    } else {
        echo "✓ All products have shop_owner_id assigned\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Step 3: Verify the fix
echo "Step 3: Verification\n";
try {
    $stmt = $db->query("SELECT COUNT(*) as total, COUNT(shop_owner_id) as with_owner FROM products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "  Total products: {$result['total']}\n";
    echo "  Products with shop_owner_id: {$result['with_owner']}\n";
    echo "  Products without shop_owner_id: " . ($result['total'] - $result['with_owner']) . "\n\n";
    
    if ($result['with_owner'] > 0) {
        echo "✓ Shop owner orders filtering should now work correctly!\n";
    } else {
        echo "⚠ Warning: No products have shop_owner_id assigned yet.\n";
        echo "  Each shop owner will see all orders until products are properly assigned.\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=========================================\n";
echo "FIX COMPLETE\n";
echo "=========================================\n";
