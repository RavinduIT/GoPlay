<?php
/**
 * Database Schema Auto-Migration
 * This file will automatically create missing columns on first access
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

$result = [
    'success' => false,
    'message' => '',
    'columns_created' => [],
    'columns_existing' => [],
    'errors' => []
];

try {
    // Connect to database
    $pdo = new PDO('mysql:host=localhost;dbname=goplay;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Add columns to products table
    $migrations = [
        'products' => [
            'available_sizes' => 'VARCHAR(500) NULL COMMENT "Comma-separated available sizes"',
            'available_colors' => 'VARCHAR(500) NULL COMMENT "Comma-separated available colors"'
        ],
        'cart_items' => [
            'selected_size' => 'VARCHAR(100) NULL COMMENT "Size selected for this cart item"',
            'selected_color' => 'VARCHAR(100) NULL COMMENT "Color selected for this cart item"'
        ],
        'order_items' => [
            'selected_size' => 'VARCHAR(100) NULL COMMENT "Size selected for this order item"',
            'selected_color' => 'VARCHAR(100) NULL COMMENT "Color selected for this order item"'
        ]
    ];
    
    foreach ($migrations as $table => $columns) {
        foreach ($columns as $column => $definition) {
            try {
                // Check if column exists
                $check = $pdo->query("SHOW COLUMNS FROM $table LIKE '$column'")->fetchAll();
                
                if (!empty($check)) {
                    $result['columns_existing'][] = "$table.$column";
                } else {
                    // Add column if it doesn't exist
                    $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
                    $result['columns_created'][] = "$table.$column";
                }
            } catch (PDOException $e) {
                $result['errors'][] = "Error with $table.$column: " . $e->getMessage();
            }
        }
    }
    
    if (empty($result['errors'])) {
        $result['success'] = true;
        if (!empty($result['columns_created'])) {
            $result['message'] = 'Successfully created ' . count($result['columns_created']) . ' new column(s)';
        }
        if (!empty($result['columns_existing'])) {
            if (!$result['message']) {
                $result['message'] = 'All columns already exist';
            } else {
                $result['message'] .= ' and ' . count($result['columns_existing']) . ' column(s) already exist';
            }
        }
    } else {
        $result['message'] = 'Some errors occurred during migration';
    }
    
} catch (PDOException $e) {
    $result['errors'][] = 'Database connection error: ' . $e->getMessage();
    $result['message'] = 'Could not connect to database';
}

// Log the results
error_log('Size/Color Migration: ' . json_encode($result));

// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
?>
