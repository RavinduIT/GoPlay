<?php
/**
 * Database Migration Runner
 * Access via: http://localhost:3000/run_migrations.php
 * 
 * This script adds the necessary columns for size/color variants to work
 */

// Don't show errors in HTML, just log them
error_reporting(E_ALL);
ini_set('display_errors', 0);

$success = false;
$message = '';
$columns_status = [];

try {
    // Get database credentials from environment or use defaults
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'goplay';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    
    // Try to connect
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // List of migrations to run
    $migrations = [
        [
            'table' => 'products',
            'columns' => [
                'available_sizes VARCHAR(500) NULL COMMENT "Comma-separated sizes"',
                'available_colors VARCHAR(500) NULL COMMENT "Comma-separated colors"'
            ]
        ],
        [
            'table' => 'cart_items',
            'columns' => [
                'selected_size VARCHAR(100) NULL COMMENT "Selected size for cart item"',
                'selected_color VARCHAR(100) NULL COMMENT "Selected color for cart item"'
            ]
        ],
        [
            'table' => 'order_items',
            'columns' => [
                'selected_size VARCHAR(100) NULL COMMENT "Selected size for order item"',
                'selected_color VARCHAR(100) NULL COMMENT "Selected color for order item"'
            ]
        ]
    ];
    
    // Run migrations
    foreach ($migrations as $migration) {
        $table = $migration['table'];
        $status = [];
        
        foreach ($migration['columns'] as $columnDef) {
            $columnName = explode(' ', $columnDef)[0];
            
            try {
                $pdo->exec("ALTER TABLE $table ADD COLUMN IF NOT EXISTS $columnDef");
                $status[$columnName] = ['done' => true, 'message' => 'Added'];
            } catch (Exception $e) {
                $status[$columnName] = ['done' => true, 'message' => 'Already exists'];
            }
        }
        
        $columns_status[$table] = $status;
    }
    
    $success = true;
    $message = 'All migrations completed successfully!';
    
} catch (PDOException $e) {
    $success = false;
    $message = 'Database Error: ' . $e->getMessage();
} catch (Exception $e) {
    $success = false;
    $message = 'Error: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Migration Runner</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .status-box {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .status-box.success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }
        .status-box.error {
            background: #ffebee;
            border-left: 4px solid #f44336;
        }
        .status-icon {
            font-size: 24px;
        }
        .status-text {
            flex: 1;
        }
        .status-text p {
            margin: 5px 0;
            line-height: 1.5;
        }
        .status-text .title {
            font-weight: 600;
            color: #333;
        }
        .status-text .detail {
            color: #666;
            font-size: 13px;
        }
        .columns-list {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.6;
        }
        .column-item {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .column-item:last-child {
            border-bottom: none;
        }
        .column-check {
            color: #4caf50;
            font-weight: bold;
        }
        .table-name {
            font-weight: 600;
            color: #333;
            margin-top: 15px;
            margin-bottom: 8px;
            font-family: inherit;
            font-size: 14px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        button {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #1565c0;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Database Migration Runner</h1>
        <div class="subtitle">Size/Color Product Variants</div>
        
        <div class="info-box">
            <strong>What this does:</strong>
            Adds necessary database columns to support product size and color variants in the shopping system.
        </div>

        <?php if ($success): ?>
            <div class="status-box success">
                <div class="status-icon">✓</div>
                <div class="status-text">
                    <p class="title">Migration Successful!</p>
                    <p class="detail"><?php echo htmlspecialchars($message); ?></p>
                </div>
            </div>
            
            <div class="columns-list">
                <?php foreach ($columns_status as $table => $columns): ?>
                    <div class="table-name">📊 <?php echo htmlspecialchars($table); ?></div>
                    <?php foreach ($columns as $col => $status): ?>
                        <div class="column-item">
                            <span class="column-check">✓</span>
                            <span><?php echo htmlspecialchars($col); ?></span>
                            <span style="color: #999; font-size: 11px;">(<?php echo htmlspecialchars($status['message']); ?>)</span>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
            
            <div style="background: #f0f7ff; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 13px; color: #1565c0;">
                <strong>✓ You're all set!</strong><br>
                The database columns have been successfully created. You can now:
                <ul style="margin: 10px 0 0 20px;">
                    <li>Create products with sizes and colors</li>
                    <li>Customers can select variants in the cart</li>
                    <li>Orders will track the selected variants</li>
                </ul>
            </div>
        <?php else: ?>
            <div class="status-box error">
                <div class="status-icon">✗</div>
                <div class="status-text">
                    <p class="title">Migration Failed</p>
                    <p class="detail"><?php echo htmlspecialchars($message); ?></p>
                </div>
            </div>
            
            <div style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-top: 20px; font-size: 13px; color: #e65100;">
                <strong>Troubleshooting:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <li>Ensure MySQL is running</li>
                    <li>Check database credentials in .env file</li>
                    <li>Try running the SQL manually in phpMyAdmin</li>
                </ul>
            </div>
        <?php endif; ?>

        <div class="button-group">
            <button class="btn-primary" onclick="location.reload()">↻ Refresh</button>
            <button class="btn-secondary" onclick="history.back()">← Back</button>
        </div>
    </div>
</body>
</html>
