<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;

class ShopOwnerController extends BaseController
{
    private ?Product $productModel = null;
    private ?Category $categoryModel = null;
    private ?ProductReview $reviewModel = null;
    
    private function getProductModel(): Product
    {
        if ($this->productModel === null) {
            $this->productModel = new Product();
        }
        return $this->productModel;
    }
    
    private function getCategoryModel(): Category
    {
        if ($this->categoryModel === null) {
            $this->categoryModel = new Category();
        }
        return $this->categoryModel;
    }
    
    private function getReviewModel(): ProductReview
    {
        if ($this->reviewModel === null) {
            $this->reviewModel = new ProductReview();
        }
        return $this->reviewModel;
    }
    
    private function checkShopOwnerAuth(): bool
    {
        session_start();
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'shop_owner';
    }
    
    private function getShopOwnerResponse(): Response
    {
        return $this->json([
            'success' => false,
            'message' => 'Unauthorized. Shop owner access required.',
            'status' => 401
        ], 401);
    }

    public function dashboard(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('shop-owner/dashboard');
    }
    
    public function productsPage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('shop-owner/products');
    }
    
    public function inventoryPage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('shop-owner/Inventory');
    }
    
    public function ordersPage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('shop-owner/orders');
    }
    
    public function reviewsPage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('shop-owner/reviews');
    }
    public function salesPage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->view('shop-owner/sales');
    }

    
    public function profilePage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('shop-owner/profile');
    }
    
    public function getDashboardStats(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            
            // Get basic stats
            $stats = $this->getProductModel()->getShopOwnerStats($shopOwnerId);
            $totalProducts = $stats['total_products'] ?? 0;
            $activeProducts = $stats['active_products'] ?? 0;
            $lowStockProducts = $stats['low_stock'] ?? 0;
            
            return $this->json([
                'success' => true,
                'stats' => [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'low_stock_products' => $lowStockProducts,
                    'total_orders' => 0, // Placeholder
                    'pending_orders' => 0, // Placeholder
                    'total_revenue' => 0 // Placeholder
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getProducts(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $page = max(1, (int)$request->getQuery('page', 1));
            $limit = min(100, max(10, (int)$request->getQuery('limit', 20)));
            $search = $request->getQuery('search', '');
            $status = $request->getQuery('status', '');
            $category = $request->getQuery('category', '');
            
            $filters = [
                'shop_owner_id' => $shopOwnerId,
                'limit' => $limit,
                'offset' => ($page - 1) * $limit
            ];
            
            if ($search) $filters['search'] = $search;
            if ($status) $filters['status'] = $status;
            if ($category) $filters['category'] = $category;
            
            $products = $this->getProductModel()->getActiveProducts($filters);
            $stats = $this->getProductModel()->getShopOwnerStats($shopOwnerId);
            $total = $stats['total_products'] ?? 0;
            
            return $this->json([
                'success' => true,
                'products' => $products,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_items' => $total,
                    'items_per_page' => $limit
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getProduct(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $productId = (int)$request->getParam('id');
            $shopOwnerId = $_SESSION['user_id'];
            
            if (!$productId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ], 400);
            }
            
            $product = $this->getProductModel()->find($productId);
            
            if (!$product || $product['shop_owner_id'] != $shopOwnerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            return $this->json([
                'success' => true,
                'product' => $product
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function createProduct(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $data = $request->getJsonBody();
            $shopOwnerId = $_SESSION['user_id'];
            
            // Validate required fields
            $required = ['name', 'price', 'category_id', 'description'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], 400);
                }
            }
            
            // Add shop owner ID and defaults
            $data['shop_owner_id'] = $shopOwnerId;
            $data['status'] = $data['status'] ?? 'active';
            $data['stock_quantity'] = $data['stock_quantity'] ?? 0;
            $data['sku'] = $data['sku'] ?? $this->generateSku();
            
            $productId = $this->getProductModel()->create($data);
            
            if (!$productId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create product'
                ], 500);
            }
            
            $product = $this->getProductModel()->find($productId);
            
            return $this->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product
            ], 201);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateProduct(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $productId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $shopOwnerId = $_SESSION['user_id'];
            
            if (!$productId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ], 400);
            }
            
            $product = $this->getProductModel()->find($productId);
            
            if (!$product || $product['shop_owner_id'] != $shopOwnerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            // Remove fields that shouldn't be updated
            unset($data['id'], $data['shop_owner_id'], $data['created_at']);
            
            $success = $this->getProductModel()->update($productId, $data);
            
            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update product'
                ], 500);
            }
            
            $updatedProduct = $this->getProductModel()->find($productId);
            
            return $this->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $updatedProduct
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteProduct(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $productId = (int)$request->getParam('id');
            $shopOwnerId = $_SESSION['user_id'];
            
            if (!$productId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ], 400);
            }
            
            $product = $this->getProductModel()->find($productId);
            
            if (!$product || $product['shop_owner_id'] != $shopOwnerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            // Soft delete by setting status to inactive
            $success = $this->getProductModel()->update($productId, ['status' => 'inactive']);
            
            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to delete product'
                ], 500);
            }
            
            return $this->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function uploadProductImages(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $productId = (int)$request->getParam('id');
            $shopOwnerId = $_SESSION['user_id'];
            
            if (!$productId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ], 400);
            }
            
            $product = $this->getProductModel()->find($productId);
            
            if (!$product || $product['shop_owner_id'] != $shopOwnerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            // Handle file upload (placeholder implementation)
            $uploadedImages = [];
            
            if (isset($_FILES['images'])) {
                // Process uploaded images
                // This would typically involve moving files to storage and generating URLs
                $uploadedImages = ['placeholder-image-url.jpg'];
            }
            
            // Update product with new images
            $currentImages = $product['images'] ?? [];
            $newImages = array_merge($currentImages, $uploadedImages);
            
            $this->getProductModel()->update($productId, ['images' => $newImages]);
            
            return $this->json([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'images' => $newImages
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to upload images',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getOrders(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        return $this->json([
            'success' => true,
            'orders' => [],
            'message' => 'Orders endpoint - to be implemented with Order model'
        ]);
    }
    
    public function getOrder(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        return $this->json([
            'success' => true,
            'order' => null,
            'message' => 'Order details endpoint - to be implemented with Order model'
        ]);
    }
    
    public function updateOrderStatus(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        return $this->json([
            'success' => true,
            'message' => 'Order status update endpoint - to be implemented with Order model'
        ]);
    }
    
    public function getInventory(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            
            // Get inventory with status for shop owner
            $inventory = $this->getProductModel()->getInventoryWithStatus($shopOwnerId);
            
            return $this->json([
                'success' => true,
                'inventory' => $inventory
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load inventory',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateStock(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $productId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $shopOwnerId = $_SESSION['user_id'];
            
            if (!$productId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid product ID'
                ], 400);
            }
            
            if (!isset($data['stock_quantity'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Stock quantity is required'
                ], 400);
            }
            
            $product = $this->getProductModel()->find($productId);
            
            if (!$product || $product['shop_owner_id'] != $shopOwnerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }
            
            $success = $this->getProductModel()->update($productId, [
                'stock_quantity' => (int)$data['stock_quantity']
            ]);
            
            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update stock'
                ], 500);
            }
            
            return $this->json([
                'success' => true,
                'message' => 'Stock updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getAnalytics(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        return $this->json([
            'success' => true,
            'analytics' => [
                'sales_data' => [],
                'top_products' => [],
                'revenue_trend' => []
            ],
            'message' => 'Analytics endpoint - to be implemented with Order and Sales models'
        ]);
    }
    
    public function getCategories(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $categories = $this->getCategoryModel()->getActiveCategories();
            
            return $this->json([
                'success' => true,
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function generateSku(): string
    {
        return 'SKU' . strtoupper(substr(uniqid(), -8));
    }

    public function handleCreateProduct(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            
            // Validate required fields
            $name = trim($_POST['name'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $price = floatval($_POST['price'] ?? 0);
            
            if (empty($name) || $categoryId <= 0 || $price <= 0) {
                $_SESSION['error'] = 'Please fill in all required fields';
                return $this->redirect('/shop-owner/products');
            }
            
            // Handle image uploads
            $imagePaths = [];
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $imagePaths = $this->handleMultipleImageUploads($_FILES['images']);
            }
            
            // Prepare product data
            $productData = [
                'name' => $name,
                'description' => $_POST['description'] ?? '',
                'category_id' => $categoryId,
                'price' => $price,
                'stock_quantity' => (int)($_POST['stock_quantity'] ?? 0),
                'images' => $imagePaths,
                'status' => $_POST['status'] ?? 'active',
                'brand' => $_POST['brand'] ?? '',
                'sku' => $_POST['sku'] ?? '',
                'min_stock_level' => (int)($_POST['min_stock_level'] ?? 10)
            ];
            
            // Create product
            $productModel = $this->getProductModel();
            $productId = $productModel->createProductForShopOwner($productData, $shopOwnerId);
            
            if ($productId) {
                $_SESSION['success'] = 'Product created successfully' . (count($imagePaths) > 0 ? ' with ' . count($imagePaths) . ' image(s)' : '');
            } else {
                $_SESSION['error'] = 'Failed to create product';
            }
            
            return $this->redirect('/shop-owner/products');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            return $this->redirect('/shop-owner/products');
        }
    }
    
    public function handleUpdateProduct(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $productId = (int)($_POST['product_id'] ?? 0);
            
            if ($productId <= 0) {
                $_SESSION['error'] = 'Invalid product ID';
                return $this->redirect('/shop-owner/products');
            }
            
            // Validate required fields
            $name = trim($_POST['name'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $price = floatval($_POST['price'] ?? 0);
            
            if (empty($name) || $categoryId <= 0 || $price <= 0) {
                $_SESSION['error'] = 'Please fill in all required fields';
                return $this->redirect('/shop-owner/products');
            }
            
            // Get existing product to check images
            $productModel = $this->getProductModel();
            $existingProduct = $productModel->find($productId);
            
            if (!$existingProduct) {
                $_SESSION['error'] = 'Product not found';
                return $this->redirect('/shop-owner/products');
            }
            
            // Handle image uploads
            $replaceImages = isset($_POST['replace_images']) && $_POST['replace_images'] == '1';
            $newImages = [];
            
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $newImages = $this->handleMultipleImageUploads($_FILES['images']);
            }
            
            // Determine final images based on replace flag
            if (!empty($newImages)) {
                if ($replaceImages) {
                    // Replace: use only new images and delete old ones
                    $allImages = $newImages;
                    
                    // Delete old image files
                    $oldImages = is_array($existingProduct['images']) 
                        ? $existingProduct['images'] 
                        : json_decode($existingProduct['images'] ?? '[]', true);
                    
                    foreach ($oldImages as $oldImagePath) {
                        $fullPath = __DIR__ . '/../../' . ltrim($oldImagePath, '/');
                        if (file_exists($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                } else {
                    // Merge: keep existing images and add new ones
                    $existingImages = is_array($existingProduct['images']) 
                        ? $existingProduct['images'] 
                        : json_decode($existingProduct['images'] ?? '[]', true);
                    $allImages = array_merge($existingImages, $newImages);
                }
            } else {
                // No new images uploaded, keep existing ones
                $allImages = is_array($existingProduct['images']) 
                    ? $existingProduct['images'] 
                    : json_decode($existingProduct['images'] ?? '[]', true);
            }
            
            // Prepare product data
            $productData = [
                'name' => $name,
                'description' => $_POST['description'] ?? '',
                'category_id' => $categoryId,
                'price' => $price,
                'stock_quantity' => (int)($_POST['stock_quantity'] ?? $existingProduct['stock_quantity']),
                'images' => $allImages,
                'status' => $_POST['status'] ?? $existingProduct['status']
            ];
            
            // Update product
            $success = $productModel->updateProductForShopOwner($productId, $productData, $shopOwnerId);
            
            if ($success) {
                $_SESSION['success'] = 'Product updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update product';
            }
            
            return $this->redirect('/shop-owner/products');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            return $this->redirect('/shop-owner/products');
        }
    }
    
    public function handleDeleteProduct(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $productId = (int)($_POST['product_id'] ?? 0);
            
            if ($productId <= 0) {
                $_SESSION['error'] = 'Invalid product ID';
                return $this->redirect('/shop-owner/products');
            }
            
            // Get product to delete images
            $productModel = $this->getProductModel();
            $product = $productModel->find($productId);
            
            if (!$product) {
                $_SESSION['error'] = 'Product not found';
                return $this->redirect('/shop-owner/products');
            }
            
            // Delete product from database
            $success = $productModel->deleteProductForShopOwner($productId, $shopOwnerId);
            
            if ($success) {
                // Delete image files
                $images = is_array($product['images']) 
                    ? $product['images'] 
                    : json_decode($product['images'] ?? '[]', true);
                
                foreach ($images as $imagePath) {
                    $fullPath = __DIR__ . '/../../public/' . ltrim($imagePath, '/');
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
                
                $_SESSION['success'] = 'Product deleted successfully';
            } else {
                $_SESSION['error'] = 'Failed to delete product';
            }
            
            return $this->redirect('/shop-owner/products');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            return $this->redirect('/shop-owner/products');
        }
    }
    
    private function handleMultipleImageUploads(array $files): array
    {
        $uploadedPaths = [];
        $uploadDir = __DIR__ . '/../../public/assets/images/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileCount = count($files['name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        for ($i = 0; $i < $fileCount; $i++) {
            if (empty($files['name'][$i]) || $files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            // Validate file type
            $fileType = mime_content_type($files['tmp_name'][$i]);
            if (!in_array($fileType, $allowedTypes)) {
                continue;
            }
            
            // Validate file size
            if ($files['size'][$i] > $maxSize) {
                continue;
            }
            
            // Generate unique filename
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('product_', true) . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                $uploadedPaths[] = '/public/assets/images/products/' . $filename;
            }
        }
        
        return $uploadedPaths;
    }

    // ============================================
    // INVENTORY MANAGEMENT METHODS
    // ============================================

    /**
     * Handle adding stock to a product
     */
    public function handleAddStock(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            
            if ($productId <= 0 || $quantity <= 0) {
                $_SESSION['error'] = 'Invalid product ID or quantity';
                return $this->redirect('/shop-owner/inventory');
            }
            
            $success = $this->getProductModel()->addStock($productId, $quantity, $shopOwnerId);
            
            if ($success) {
                $_SESSION['success'] = "Added {$quantity} units to stock successfully";
            } else {
                $_SESSION['error'] = 'Failed to update stock';
            }
            
            return $this->redirect('/shop-owner/inventory');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            return $this->redirect('/shop-owner/inventory');
        }
    }

    /**
     * Handle updating minimum stock level
     */
    public function handleUpdateMinStock(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $productId = (int)($_POST['product_id'] ?? 0);
            $minLevel = (int)($_POST['min_stock_level'] ?? 0);
            
            if ($productId <= 0 || $minLevel < 0) {
                $_SESSION['error'] = 'Invalid input';
                return $this->redirect('/shop-owner/inventory');
            }
            
            $success = $this->getProductModel()->updateMinStockLevel($productId, $minLevel, $shopOwnerId);
            
            if ($success) {
                $_SESSION['success'] = 'Reorder level updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update reorder level';
            }
            
            return $this->redirect('/shop-owner/inventory');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            return $this->redirect('/shop-owner/inventory');
        }
    }

    /**
     * Handle removing stock (damaged/expired)
     */
    public function handleRemoveStock(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            $reason = $_POST['reason'] ?? 'damaged';
            
            if ($productId <= 0 || $quantity <= 0) {
                $_SESSION['error'] = 'Invalid input';
                return $this->redirect('/shop-owner/inventory');
            }
            
            $success = $this->getProductModel()->removeStock($productId, $quantity, $reason, $shopOwnerId);
            
            if ($success) {
                $_SESSION['success'] = "Removed {$quantity} units from stock ({$reason})";
            } else {
                $_SESSION['error'] = 'Failed to remove stock';
            }
            
            return $this->redirect('/shop-owner/inventory');
            
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            return $this->redirect('/shop-owner/inventory');
        }
    }

    /**
     * Get all reviews for shop owner's products (API endpoint)
     */
    public function getReviews(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $shopOwnerId = $_SESSION['user_id'];
            
            // Get all reviews for shop owner's products
            $reviews = $this->getReviewModel()->getShopOwnerReviews($shopOwnerId);
            
            // Get review statistics
            $stats = $this->getReviewModel()->getShopOwnerReviewStats($shopOwnerId);
            
            return $this->json([
                'success' => true,
                'reviews' => $reviews,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a product review (shop owner can delete reviews on their products)
     */
    public function deleteProductReview(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            $_SESSION['error_message'] = 'Unauthorized access';
            return $this->redirect('/login');
        }

        try {
            $reviewId = (int)($_POST['review_id'] ?? 0);
            $shopOwnerId = $_SESSION['user_id'];
            
            if ($reviewId <= 0) {
                $_SESSION['error_message'] = 'Invalid review ID';
                return $this->redirect('/shop-owner/reviews');
            }
            
            // Get review with product info to verify ownership
            $review = $this->getReviewModel()->getReviewWithProduct($reviewId);
            
            if (!$review) {
                $_SESSION['error_message'] = 'Review not found';
                return $this->redirect('/shop-owner/reviews');
            }
            
            // Verify that the product belongs to this shop owner
            $product = $this->getProductModel()->find($review['product_id']);
            if (!$product || $product['shop_owner_id'] != $shopOwnerId) {
                $_SESSION['error_message'] = 'You can only delete reviews on your own products';
                return $this->redirect('/shop-owner/reviews');
            }
            
            // Delete the review
            $success = $this->getReviewModel()->deleteReview($reviewId);
            
            if ($success) {
                $_SESSION['success_message'] = 'Review deleted successfully';
            } else {
                $_SESSION['error_message'] = 'Failed to delete review';
            }
            
            return $this->redirect('/shop-owner/reviews');
            
        } catch (\Exception $e) {
            error_log("Delete review error: " . $e->getMessage());
            $_SESSION['error_message'] = 'An error occurred while deleting the review';
            return $this->redirect('/shop-owner/reviews');
        }
    }
}