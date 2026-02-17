<?php
/**
 * Web-based Product Assignment Tool
 * Assign products to shop owners
 */

require_once __DIR__ . '/../bootstrap.php';

use Core\Database;

$db = Database::getInstance();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shop_owner_id'])) {
    $shopOwnerId = (int)$_POST['shop_owner_id'];
    $assignAll = isset($_POST['assign_all']);
    
    try {
        if ($assignAll) {
            $db->query("UPDATE products SET shop_owner_id = ? WHERE shop_owner_id IS NULL", [$shopOwnerId]);
            $message = "✓ All unassigned products have been assigned to shop owner!";
            $messageType = "success";
        } else {
            $productIds = $_POST['product_ids'] ?? [];
            if (!empty($productIds)) {
                $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
                $params = array_merge([$shopOwnerId], $productIds);
                $db->query("UPDATE products SET shop_owner_id = ? WHERE id IN ($placeholders)", $params);
                $message = "✓ Selected products have been assigned!";
                $messageType = "success";
            }
        }
    } catch (Exception $e) {
        $message = "✗ Error: " . $e->getMessage();
        $messageType = "error";
    }
}

// Get shop owners
$stmt = $db->query("SELECT id, username, email FROM users WHERE user_type = 'shop_owner'");
$shopOwners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get products without shop_owner_id
$stmt = $db->query("SELECT id, name, price, status FROM products WHERE shop_owner_id IS NULL");
$unassignedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$stmt = $db->query("SELECT COUNT(*) as total, COUNT(shop_owner_id) as with_owner FROM products");
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Products to Shop Owners</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { flex: 1; padding: 20px; background: #f8f9fa; border-radius: 6px; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 8px; }
        .stat-card .value { font-size: 32px; font-weight: bold; color: #007bff; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-section { margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn { padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; color: #333; }
        tr:hover { background: #f8f9fa; }
        .no-products { text-align: center; padding: 40px; color: #666; }
        .checkbox { width: 20px; height: 20px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Assign Products to Shop Owners</h1>
        <p class="subtitle">Manage product ownership for multi-vendor functionality</p>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Products</h3>
                <div class="value"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Assigned Products</h3>
                <div class="value"><?= $stats['with_owner'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Unassigned Products</h3>
                <div class="value"><?= count($unassignedProducts) ?></div>
            </div>
        </div>
        
        <?php if (isset($message)): ?>
            <div class="message <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($shopOwners)): ?>
            <div class="message error">
                ✗ No shop owners found! Please create a shop owner account first.
            </div>
        <?php elseif (count($unassignedProducts) === 0): ?>
            <div class="message success">
                ✓ All products are assigned to shop owners! You're all set.
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-section">
                    <div class="form-group">
                        <label>Select Shop Owner:</label>
                        <select name="shop_owner_id" required>
                            <option value="">-- Choose a shop owner --</option>
                            <?php foreach ($shopOwners as $owner): ?>
                                <option value="<?= $owner['id'] ?>">
                                    <?= htmlspecialchars($owner['username']) ?> (<?= htmlspecialchars($owner['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="assign_all" value="1" class="checkbox">
                            Assign ALL unassigned products to this shop owner
                        </label>
                    </div>
                    
                    <button type="submit" class="btn">Assign Products</button>
                </div>
                
                <h3>Unassigned Products (<?= count($unassignedProducts) ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th width="50">Select</th>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unassignedProducts as $product): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="product_ids[]" value="<?= $product['id'] ?>" class="checkbox">
                                </td>
                                <td><?= $product['id'] ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td><?= htmlspecialchars($product['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">
            <a href="/main/GoPlay/shop-owner/orders" style="color: #007bff; text-decoration: none;">← Back to Orders</a>
        </div>
    </div>
</body>
</html>
