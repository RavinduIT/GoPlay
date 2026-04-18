<?php
/**
 * Size/Color Update Test Endpoint
 * Access via: http://localhost:3000/public/test-size-color-update.php
 * 
 * This page helps verify that size/color updates are working correctly
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    die('Not logged in');
}

// Only allow shop owners
if (($_SESSION['role'] ?? '') !== 'shop_owner') {
    die('Only shop owners can access this');
}

require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Product;

$result = [
    'success' => false,
    'message' => '',
    'product_before' => null,
    'product_after' => null,
    'test_product_id' => null
];

try {
    $productModel = new Product();
    $shopOwnerId = $_SESSION['user_id'];
    
    // Get first product owned by this shop owner
    $sql = "SELECT id FROM products WHERE shop_owner_id = ? LIMIT 1";
    $db = \Core\Database::getInstance();
    $stmt = $db->query($sql, [$shopOwnerId]);
    $product = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if (!$product) {
        $result['message'] = 'No products found. Please create a product first.';
        http_response_code(400);
    } else {
        $productId = $product['id'];
        $result['test_product_id'] = $productId;
        
        // Get before state
        $before = $productModel->find($productId);
        $result['product_before'] = [
            'id' => $before['id'],
            'name' => $before['name'],
            'available_sizes' => $before['available_sizes'],
            'available_colors' => $before['available_colors']
        ];
        
        // Test update with new values
        $testSizes = 'XS,S,M,L,XL,XXL_' . time();
        $testColors = 'Red,Blue,Green,Black_' . time();
        
        $updateData = [
            'available_sizes' => $testSizes,
            'available_colors' => $testColors
        ];
        
        $updated = $productModel->updateProductForShopOwner($productId, $updateData, $shopOwnerId);
        
        // Get after state
        $after = $productModel->find($productId);
        $result['product_after'] = [
            'id' => $after['id'],
            'name' => $after['name'],
            'available_sizes' => $after['available_sizes'],
            'available_colors' => $after['available_colors']
        ];
        
        if ($updated && $after['available_sizes'] === $testSizes && $after['available_colors'] === $testColors) {
            $result['success'] = true;
            $result['message'] = 'SUCCESS: Size/Color update is working correctly!';
            
            // Revert the changes
            $productModel->updateProductForShopOwner($productId, [
                'available_sizes' => $before['available_sizes'],
                'available_colors' => $before['available_colors']
            ], $shopOwnerId);
            $result['message'] .= ' (Test data has been reverted)';
        } else {
            $result['message'] = 'FAILED: Update did not save the size/color values';
            if (!$updated) {
                $result['message'] .= ' (Update method returned false)';
            }
            if ($after['available_sizes'] !== $testSizes) {
                $result['message'] .= ' (available_sizes: ' . $after['available_sizes'] . ' != ' . $testSizes . ')';
            }
            if ($after['available_colors'] !== $testColors) {
                $result['message'] .= ' (available_colors: ' . $after['available_colors'] . ' != ' . $testColors . ')';
            }
        }
    }
    
} catch (Exception $e) {
    $result['message'] = 'Error: ' . $e->getMessage();
    http_response_code(500);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Size/Color Update Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .status-box {
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .status-box.success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .status-box.error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .status-box.warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .data-box {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }
        .data-label {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .data-value {
            margin-left: 20px;
            color: #666;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Size/Color Update Test</h1>
        
        <div class="info-box">
            This test page verifies that the product update mechanism is working correctly for size and color fields.
        </div>
        
        <?php if ($result['success']): ?>
            <div class="status-box success">
                <strong>✓ TEST PASSED</strong><br>
                <?= htmlspecialchars($result['message']) ?>
            </div>
        <?php elseif ($result['test_product_id'] === null): ?>
            <div class="status-box warning">
                <strong>⚠ NO TEST PRODUCT</strong><br>
                <?= htmlspecialchars($result['message']) ?>
            </div>
        <?php else: ?>
            <div class="status-box error">
                <strong>✗ TEST FAILED</strong><br>
                <?= htmlspecialchars($result['message']) ?>
            </div>
        <?php endif; ?>
        
        <h2 style="font-size: 18px; margin: 20px 0 15px 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            Test Details
        </h2>
        
        <?php if ($result['test_product_id']): ?>
            <div class="data-box">
                <div class="data-label">Test Product ID:</div>
                <div class="data-value"><?= htmlspecialchars($result['test_product_id']) ?></div>
            </div>
            
            <div class="data-box">
                <div class="data-label">Before Update:</div>
                <div class="data-value">
                    ID: <?= htmlspecialchars($result['product_before']['id']) ?><br>
                    Name: <?= htmlspecialchars($result['product_before']['name']) ?><br>
                    Sizes: <?= htmlspecialchars($result['product_before']['available_sizes'] ?? '(empty)') ?><br>
                    Colors: <?= htmlspecialchars($result['product_before']['available_colors'] ?? '(empty)') ?>
                </div>
            </div>
            
            <div class="data-box">
                <div class="data-label">After Update Attempt:</div>
                <div class="data-value">
                    ID: <?= htmlspecialchars($result['product_after']['id']) ?><br>
                    Name: <?= htmlspecialchars($result['product_after']['name']) ?><br>
                    Sizes: <?= htmlspecialchars($result['product_after']['available_sizes'] ?? '(empty)') ?><br>
                    Colors: <?= htmlspecialchars($result['product_after']['available_colors'] ?? '(empty)') ?>
                </div>
            </div>
            
            <?php if ($result['success']): ?>
                <div style="padding: 15px; background: #d4edda; border-radius: 6px; margin-top: 20px; border-left: 4px solid #28a745;">
                    <strong>✓ System is working correctly!</strong><br>
                    You can now use the product edit form and size/color updates should save properly.
                </div>
            <?php else: ?>
                <div style="padding: 15px; background: #f8d7da; border-radius: 6px; margin-top: 20px; border-left: 4px solid #dc3545;">
                    <strong>✗ There's an issue with the update mechanism</strong><br>
                    Check the DEBUG_SIZE_COLOR_UPDATE.md file for troubleshooting steps.
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center;">
            <button onclick="history.back()">← Back</button>
            <button onclick="location.reload()">↻ Run Test Again</button>
        </div>
    </div>
</body>
</html>
