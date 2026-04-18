<?php
/**
 * Database Auto-Migration
 * Ensures all required columns exist for product variants
 * This runs automatically on application startup
 */

function runAutoMigrations() {
    try {
        // Try to connect directly to database for migrations
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
        $db = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'goplay';
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';
        
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Define required columns
        $migrations = [
            'products' => [
                'available_sizes' => 'VARCHAR(500) NULL',
                'available_colors' => 'VARCHAR(500) NULL'
            ],
            'cart_items' => [
                'selected_size' => 'VARCHAR(100) NULL',
                'selected_color' => 'VARCHAR(100) NULL'
            ],
            'order_items' => [
                'selected_size' => 'VARCHAR(100) NULL',
                'selected_color' => 'VARCHAR(100) NULL'
            ]
        ];
        
        foreach ($migrations as $table => $columns) {
            foreach ($columns as $column => $type) {
                try {
                    // Try to add the column if it doesn't exist
                    $pdo->exec("ALTER TABLE $table ADD COLUMN IF NOT EXISTS $column $type");
                } catch (Exception $e) {
                    // Column might already exist, continue
                    @error_log("Auto-migration info for $table.$column: " . $e->getMessage());
                }
            }
        }
        
        return true;
    } catch (Exception $e) {
        @error_log("Auto-migration startup error: " . $e->getMessage());
        return false;
    }
}

// Run migrations on startup (suppress warnings during initialization)
@runAutoMigrations();
?>

