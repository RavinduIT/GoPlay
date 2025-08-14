<?php
/**
 * Products API Endpoint
 * Handles CRUD operations for products
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once '../config/database.php';

// Initialize database
$dbHelper = new DatabaseHelper();

// Get HTTP method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    switch ($method) {
        case 'GET':
            handleGetRequest($dbHelper, $action, $id);
            break;
            
        case 'POST':
            handlePostRequest($dbHelper, $action);
            break;
            
        case 'PUT':
            handlePutRequest($dbHelper, $id);
            break;
            
        case 'DELETE':
            handleDeleteRequest($dbHelper, $id);
            break;
            
        default:
            sendErrorResponse('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    error_log('Products API Error: ' . $e->getMessage());
    sendErrorResponse('Internal server error', 500);
}

// Handle GET requests
function handleGetRequest($dbHelper, $action, $id) {
    switch ($action) {
        case 'all':
            getAllProducts($dbHelper);
            break;
            
        case 'active':
            getActiveProducts($dbHelper);
            break;
            
        case 'categories':
            getProductCategories($dbHelper);
            break;
            
        case 'search':
            searchProducts($dbHelper);
            break;
            
        default:
            if ($id > 0) {
                getProductById($dbHelper, $id);
            } else {
                getActiveProducts($dbHelper);
            }
    }
}

// Get all products
function getAllProducts($dbHelper) {
    $sql = "SELECT p.*, pc.name as category_name 
            FROM products p 
            LEFT JOIN product_categories pc ON p.category_id = pc.id 
            ORDER BY p.created_at DESC";
    
    $products = $dbHelper->getAll($sql);
    
    // Process JSON fields
    foreach ($products as &$product) {
        $product['specifications'] = json_decode($product['specifications'], true) ?: [];
        $product['features'] = json_decode($product['features'], true) ?: [];
        $product['images'] = json_decode($product['images'], true) ?: [];
        $product['tags'] = json_decode($product['tags'], true) ?: [];
        $product['inStock'] = $product['stock_quantity'] > 0;
    }
    
    sendSuccessResponse($products);
}

// Get active products only
function getActiveProducts($dbHelper) {
    $sql = "SELECT p.*, pc.name as category_name 
            FROM products p 
            LEFT JOIN product_categories pc ON p.category_id = pc.id 
            WHERE p.status = 'active' 
            ORDER BY p.created_at DESC";
    
    $products = $dbHelper->getAll($sql);
    
    // Process JSON fields
    foreach ($products as &$product) {
        $product['specifications'] = json_decode($product['specifications'], true) ?: [];
        $product['features'] = json_decode($product['features'], true) ?: [];
        $product['images'] = json_decode($product['images'], true) ?: [];
        $product['tags'] = json_decode($product['tags'], true) ?: [];
        $product['inStock'] = $product['stock_quantity'] > 0;
    }
    
    sendSuccessResponse($products);
}

// Get single product by ID
function getProductById($dbHelper, $id) {
    $sql = "SELECT p.*, pc.name as category_name 
            FROM products p 
            LEFT JOIN product_categories pc ON p.category_id = pc.id 
            WHERE p.id = ?";
    
    $product = $dbHelper->getOne($sql, [$id]);
    
    if (!$product) {
        sendErrorResponse('Product not found', 404);
    }
    
    // Process JSON fields
    $product['specifications'] = json_decode($product['specifications'], true) ?: [];
    $product['features'] = json_decode($product['features'], true) ?: [];
    $product['images'] = json_decode($product['images'], true) ?: [];
    $product['tags'] = json_decode($product['tags'], true) ?: [];
    $product['inStock'] = $product['stock_quantity'] > 0;
    
    sendSuccessResponse($product);
}

// Get product categories
function getProductCategories($dbHelper) {
    $sql = "SELECT * FROM product_categories WHERE is_active = 1 ORDER BY name";
    $categories = $dbHelper->getAll($sql);
    sendSuccessResponse($categories);
}

// Search products
function searchProducts($dbHelper) {
    $search = isset($_GET['q']) ? $_GET['q'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $brand = isset($_GET['brand']) ? $_GET['brand'] : '';
    $minPrice = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $maxPrice = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 999999;
    
    $sql = "SELECT p.*, pc.name as category_name 
            FROM products p 
            LEFT JOIN product_categories pc ON p.category_id = pc.id 
            WHERE p.status = 'active'";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($category)) {
        $sql .= " AND pc.name = ?";
        $params[] = $category;
    }
    
    if (!empty($brand)) {
        $sql .= " AND p.brand = ?";
        $params[] = $brand;
    }
    
    if ($minPrice > 0 || $maxPrice < 999999) {
        $sql .= " AND p.price BETWEEN ? AND ?";
        $params[] = $minPrice;
        $params[] = $maxPrice;
    }
    
    $sql .= " ORDER BY p.name ASC";
    
    $products = $dbHelper->getAll($sql, $params);
    
    // Process JSON fields
    foreach ($products as &$product) {
        $product['specifications'] = json_decode($product['specifications'], true) ?: [];
        $product['features'] = json_decode($product['features'], true) ?: [];
        $product['images'] = json_decode($product['images'], true) ?: [];
        $product['tags'] = json_decode($product['tags'], true) ?: [];
        $product['inStock'] = $product['stock_quantity'] > 0;
    }
    
    sendSuccessResponse($products);
}

// Handle POST requests (Create new product)
function handlePostRequest($dbHelper, $action) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendErrorResponse('Invalid JSON data');
    }
    
    // Validate required fields
    $required = ['name', 'category_id', 'brand', 'price'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            sendErrorResponse("Field '$field' is required");
        }
    }
    
    // Generate SKU if not provided
    if (!isset($input['sku']) || empty($input['sku'])) {
        $input['sku'] = generateSKU($input['brand'], $input['name']);
    }
    
    $sql = "INSERT INTO products (name, sku, description, category_id, brand, price, stock_quantity, 
            specifications, features, images, tags, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $input['name'],
        $input['sku'],
        $input['description'] ?? '',
        $input['category_id'],
        $input['brand'],
        $input['price'],
        $input['stock_quantity'] ?? 0,
        json_encode($input['specifications'] ?? []),
        json_encode($input['features'] ?? []),
        json_encode($input['images'] ?? []),
        json_encode($input['tags'] ?? []),
        $input['status'] ?? 'active'
    ];
    
    $productId = $dbHelper->insert($sql, $params);
    
    if ($productId) {
        sendSuccessResponse(['id' => $productId], 'Product created successfully');
    } else {
        sendErrorResponse('Failed to create product');
    }
}

// Handle PUT requests (Update product)
function handlePutRequest($dbHelper, $id) {
    if ($id <= 0) {
        sendErrorResponse('Invalid product ID');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendErrorResponse('Invalid JSON data');
    }
    
    // Check if product exists
    if (!$dbHelper->exists("SELECT id FROM products WHERE id = ?", [$id])) {
        sendErrorResponse('Product not found', 404);
    }
    
    $sql = "UPDATE products SET 
            name = ?, description = ?, category_id = ?, brand = ?, price = ?, 
            stock_quantity = ?, specifications = ?, features = ?, images = ?, 
            tags = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?";
    
    $params = [
        $input['name'],
        $input['description'] ?? '',
        $input['category_id'],
        $input['brand'],
        $input['price'],
        $input['stock_quantity'] ?? 0,
        json_encode($input['specifications'] ?? []),
        json_encode($input['features'] ?? []),
        json_encode($input['images'] ?? []),
        json_encode($input['tags'] ?? []),
        $input['status'] ?? 'active',
        $id
    ];
    
    $affected = $dbHelper->update($sql, $params);
    
    if ($affected > 0) {
        sendSuccessResponse(null, 'Product updated successfully');
    } else {
        sendErrorResponse('Failed to update product');
    }
}

// Handle DELETE requests
function handleDeleteRequest($dbHelper, $id) {
    if ($id <= 0) {
        sendErrorResponse('Invalid product ID');
    }
    
    // Check if product exists
    if (!$dbHelper->exists("SELECT id FROM products WHERE id = ?", [$id])) {
        sendErrorResponse('Product not found', 404);
    }
    
    $sql = "DELETE FROM products WHERE id = ?";
    $affected = $dbHelper->delete($sql, [$id]);
    
    if ($affected > 0) {
        sendSuccessResponse(null, 'Product deleted successfully');
    } else {
        sendErrorResponse('Failed to delete product');
    }
}

// Generate SKU
function generateSKU($brand, $name) {
    $brandCode = strtoupper(substr($brand, 0, 3));
    $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));
    $number = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    return $brandCode . '-' . $nameCode . '-' . $number;
}

?>