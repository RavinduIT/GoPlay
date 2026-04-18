<?php
require 'bootstrap.php';
$db = new \Core\Database();
$result = $db->query('DESCRIBE products', []);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
echo "Checking products table columns:\n";
foreach ($rows as $row) {
    if (strpos($row['Field'], 'available_') === 0 || strpos($row['Field'], 'size') === 0 || strpos($row['Field'], 'color') === 0) {
        echo $row['Field'] . ' | ' . $row['Type'] . "\n";
    }
}

echo "\nChecking cart_items table columns:\n";
$result = $db->query('DESCRIBE cart_items', []);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    if (strpos($row['Field'], 'selected_') === 0 || strpos($row['Field'], 'size') === 0 || strpos($row['Field'], 'color') === 0) {
        echo $row['Field'] . ' | ' . $row['Type'] . "\n";
    }
}

echo "\nChecking order_items table columns:\n";
$result = $db->query('DESCRIBE order_items', []);
$rows = $result->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    if (strpos($row['Field'], 'selected_') === 0 || strpos($row['Field'], 'size') === 0 || strpos($row['Field'], 'color') === 0) {
        echo $row['Field'] . ' | ' . $row['Type'] . "\n";
    }
}
?>
