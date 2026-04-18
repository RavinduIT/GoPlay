<?php
/**
 * Stock Management Verification Test
 * Tests: 1. Stock reduction when order placed
 *        2. Stock restoration when order cancelled
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$productModel = new Product();
$orderModel = new Order();
$cartModel = new Cart();

// Test results storage
$tests = [];
$passCount = 0;
$failCount = 0;

// Helper function to add test result
function addTest(&$tests, &$passCount, &$failCount, $testName, $passed, $details = '') {
    $tests[] = [
        'name' => $testName,
        'passed' => $passed,
        'details' => $details
    ];
    if ($passed) {
        $passCount++;
    } else {
        $failCount++;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-card .label {
            font-size: 12px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .tests {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .test-item {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background: #f9f9f9;
            transition: all 0.3s ease;
        }
        .test-item.passed {
            border-color: #4caf50;
            background: #f1f8f4;
        }
        .test-item.failed {
            border-color: #f44336;
            background: #fef5f5;
        }
        .test-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        .test-icon {
            font-size: 24px;
            width: 30px;
            text-align: center;
        }
        .test-passed .test-icon {
            color: #4caf50;
        }
        .test-failed .test-icon {
            color: #f44336;
        }
        .test-name {
            font-weight: 600;
            font-size: 15px;
            flex: 1;
        }
        .test-details {
            margin-left: 45px;
            padding: 10px 0 0 0;
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 10px;
        }
        .code-block {
            background: #f5f5f5;
            border-left: 3px solid #667eea;
            padding: 10px 15px;
            margin: 8px 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
            border-radius: 4px;
        }
        .error {
            color: #f44336;
            font-weight: 500;
        }
        .success {
            color: #4caf50;
            font-weight: 500;
        }
        .info {
            color: #2196f3;
            font-weight: 500;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .footer {
            background: #f5f5f5;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Stock Management Test Suite</h1>
            <p>Verifies stock is reduced on order creation and restored on cancellation</p>
        </div>

        <div class="content">
            <?php
            
            // Find a test product with stock
            $db = new \Core\Database();
            $testProductResult = $db->query(
                "SELECT * FROM products WHERE stock_quantity > 0 LIMIT 1",
                []
            );
            $testProduct = $testProductResult ? $testProductResult->fetch(\PDO::FETCH_ASSOC) : null;
            
            if (!$testProduct) {
                echo '<div class="test-item failed">';
                echo '<div class="test-header"><div class="test-icon">❌</div><div class="test-name">Test Setup</div></div>';
                echo '<div class="test-details"><span class="error">ERROR:</span> No products with stock found. Please create a product first.</div>';
                echo '</div>';
            } else {
                $productId = $testProduct['id'];
                $shopOwnerId = $testProduct['shop_owner_id'];
                $initialStock = (int)$testProduct['stock_quantity'];
                $testQuantity = min(5, max(1, floor($initialStock / 2)));
                
                // TEST 1: Stock reduction on order creation
                $beforeStock = $productModel->find($productId)['stock_quantity'];
                
                $orderData = [
                    'order_number' => 'TEST-' . date('YmdHis'),
                    'user_id' => 1,
                    'order_type' => 'product',
                    'subtotal' => 1000,
                    'tax_amount' => 80,
                    'shipping_amount' => 500,
                    'discount_amount' => 0,
                    'total_amount' => 1580,
                    'currency' => 'LKR',
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => 'cash'
                ];
                
                $cartItems = [[
                    'product_id' => $productId,
                    'product_name' => $testProduct['name'],
                    'quantity' => $testQuantity,
                    'unit_price' => $testProduct['price'],
                    'total_price' => $testProduct['price'] * $testQuantity,
                    'selected_size' => null,
                    'selected_color' => null
                ]];
                
                $orderId = $orderModel->createOrder($orderData, $cartItems);
                
                $afterOrderStock = $productModel->find($productId)['stock_quantity'];
                
                $stockReduced = ($beforeStock - $afterOrderStock) == $testQuantity;
                addTest($tests, $passCount, $failCount, 
                    'Stock Reduced on Order Creation',
                    $stockReduced,
                    "Before: $beforeStock units | After: $afterOrderStock units | Reduced by: " . ($beforeStock - $afterOrderStock) . " units"
                );
                
                // TEST 2: Stock restoration on order cancellation
                if ($orderId) {
                    $beforeCancelStock = $productModel->find($productId)['stock_quantity'];
                    
                    // Cancel the order
                    $cancelResult = $orderModel->cancelOrder($orderId, 1, 'Testing stock restoration');
                    
                    $afterCancelStock = $productModel->find($productId)['stock_quantity'];
                    
                    $stockRestored = ($afterCancelStock - $beforeCancelStock) == $testQuantity;
                    addTest($tests, $passCount, $failCount,
                        'Stock Restored on Order Cancellation',
                        $stockRestored,
                        "Before cancel: $beforeCancelStock units | After cancel: $afterCancelStock units | Restored: " . ($afterCancelStock - $beforeCancelStock) . " units"
                    );
                    
                    // TEST 3: Stock returned to initial level
                    $finalStock = $productModel->find($productId)['stock_quantity'];
                    $stockReturned = $finalStock == $initialStock;
                    addTest($tests, $passCount, $failCount,
                        'Stock Returned to Initial Level',
                        $stockReturned,
                        "Initial: $initialStock | Final: $finalStock | Match: " . ($stockReturned ? 'YES ✓' : 'NO ✗')
                    );
                    
                    // TEST 4: Order status is cancelled
                    $cancelledOrder = $orderModel->find($orderId);
                    $orderCancelled = $cancelledOrder && $cancelledOrder['status'] == 'cancelled';
                    addTest($tests, $passCount, $failCount,
                        'Order Status Updated to Cancelled',
                        $orderCancelled,
                        "Order #" . $cancelledOrder['order_number'] . " Status: " . $cancelledOrder['status']
                    );
                    
                    // TEST 5: Verify multiple cancellations don't over-restock
                    $beforeDoubleCancel = $productModel->find($productId)['stock_quantity'];
                    $secondCancelResult = $orderModel->cancelOrder($orderId, 1, 'Testing duplicate cancellation');
                    $afterDoubleCancel = $productModel->find($productId)['stock_quantity'];
                    
                    $noDuplicateRestock = $beforeDoubleCancel == $afterDoubleCancel && !$secondCancelResult['success'];
                    addTest($tests, $passCount, $failCount,
                        'No Duplicate Restocking on Re-cancellation',
                        $noDuplicateRestock,
                        "Result: " . $secondCancelResult['message']
                    );
                }
            }
            
            ?>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="value"><?= $passCount ?></div>
                    <div class="label">Tests Passed</div>
                </div>
                <div class="stat-card">
                    <div class="value"><?= $failCount ?></div>
                    <div class="label">Tests Failed</div>
                </div>
                <div class="stat-card">
                    <div class="value"><?= count($tests) ?></div>
                    <div class="label">Total Tests</div>
                </div>
            </div>

            <div class="section-title">📋 Test Results</div>
            <div class="tests">
                <?php foreach ($tests as $test): ?>
                    <div class="test-item <?= $test['passed'] ? 'passed test-passed' : 'failed test-failed' ?>">
                        <div class="test-header">
                            <div class="test-icon"><?= $test['passed'] ? '✅' : '❌' ?></div>
                            <div class="test-name"><?= htmlspecialchars($test['name']) ?></div>
                        </div>
                        <?php if (!empty($test['details'])): ?>
                            <div class="test-details"><?= htmlspecialchars($test['details']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="section-title">🔧 How It Works</div>
            <div class="test-item">
                <div class="test-header">
                    <div class="test-icon">📊</div>
                    <div class="test-name">Stock Reduction Flow</div>
                </div>
                <div class="test-details">
                    <strong>When order is placed:</strong>
                    <div class="code-block">
                        1. PaymentController.processCODPayment() → creates order<br>
                        2. Order.createOrder() → loops through cart items<br>
                        3. For each item → updateProductStock() executes:<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;UPDATE products SET stock_quantity = stock_quantity - ?<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;WHERE id = ? AND stock_quantity >= ?<br>
                        4. Stock reduced by order quantity ✓
                    </div>
                </div>
            </div>

            <div class="test-item">
                <div class="test-header">
                    <div class="test-icon">🔄</div>
                    <div class="test-name">Stock Restoration Flow</div>
                </div>
                <div class="test-details">
                    <strong>When order is cancelled:</strong>
                    <div class="code-block">
                        1. UserController.cancelOrder() → calls Order.cancelOrder()<br>
                        2. Order.cancelOrder() → calls restockProducts()<br>
                        3. restockProducts() gets order items with shop_owner_id<br>
                        4. For each item → Product.addStock() executes:<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;UPDATE products SET stock_quantity = stock_quantity + ?<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;WHERE id = ? AND shop_owner_id = ?<br>
                        5. Stock restored by order quantity ✓<br>
                        6. Order status changed to 'cancelled' ✓
                    </div>
                </div>
            </div>

            <div class="test-item">
                <div class="test-header">
                    <div class="test-icon">🛡️</div>
                    <div class="test-name">Safety Features</div>
                </div>
                <div class="test-details">
                    ✓ Stock only reduced if sufficient quantity exists (WHERE stock_quantity >= ?)<br>
                    ✓ Orders can only be cancelled if status is 'pending' or 'processing'<br>
                    ✓ Duplicate cancellations prevented (status already 'cancelled')<br>
                    ✓ Shop owner validation on stock restoration<br>
                    ✓ All operations logged to error_log for audit trail
                </div>
            </div>

        </div>

        <div class="footer">
            <p>✨ Stock Management System - All operations atomic and validated</p>
            <p style="margin-top: 10px; opacity: 0.7;">Test Time: <?= date('Y-m-d H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
