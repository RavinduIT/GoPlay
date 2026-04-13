<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShopOwnerProfile;
use App\Models\User;

class ShopOwnerController extends BaseController
{
    private ?Product $productModel = null;
    private ?Category $categoryModel = null;
    private ?ProductReview $reviewModel = null;
    private ?Order $orderModel = null;
    private ?ShopOwnerProfile $profileModel = null;
    private ?User $userModel = null;
    
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
    
    private function getOrderModel(): Order
    {
        if ($this->orderModel === null) {
            $this->orderModel = new Order();
        }
        return $this->orderModel;
    }
    
    private function getProfileModel(): ShopOwnerProfile
    {
        if ($this->profileModel === null) {
            $this->profileModel = new ShopOwnerProfile();
        }
        return $this->profileModel;
    }
    
    private function getUserModel(): User
    {
        if ($this->userModel === null) {
            $this->userModel = new User();
        }
        return $this->userModel;
    }
    
    private function checkShopOwnerAuth(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("Auth check - User ID: " . ($_SESSION['user_id'] ?? 'none') . ", User Type: " . ($_SESSION['user_type'] ?? 'none'));
        
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
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            
            // Get product stats
            $productStats = $this->getProductModel()->getShopOwnerStats($shopOwnerId);
            
            // Get order stats
            $orderStats = $this->getOrderModel()->getShopOwnerOrderStats($shopOwnerId);
            
            // Get recent orders (last 5)
            $recentOrders = $this->getOrderModel()->getOrdersByShopOwner($shopOwnerId, [], 5);
            
            // Get top products
            $topProducts = $this->getProductModel()->getTopSellingProducts($shopOwnerId, 3);
            
            // Get low stock products
            $lowStockProducts = $this->getProductModel()->getLowStockProducts($shopOwnerId, 3);
            
            // Get recent reviews
            $recentReviews = $this->getReviewModel()->getShopOwnerReviews($shopOwnerId, 2);
            
            // Calculate total revenue from orders
            $totalRevenue = $orderStats['total_revenue'] ?? 0;
            $monthlyRevenue = $orderStats['monthly_revenue'] ?? 0;
            
            return $this->view('shop-owner/dashboard', [
                'stats' => [
                    'total_products' => $productStats['total_products'] ?? 0,
                    'active_products' => $productStats['active_products'] ?? 0,
                    'low_stock' => $productStats['low_stock'] ?? 0,
                    'total_orders' => $orderStats['total_orders'] ?? 0,
                    'pending_orders' => $orderStats['pending_orders'] ?? 0,
                    'processing_orders' => $orderStats['processing_orders'] ?? 0,
                    'shipped_orders' => $orderStats['shipped_orders'] ?? 0,
                    'delivered_orders' => $orderStats['delivered_orders'] ?? 0,
                    'total_revenue' => $totalRevenue,
                    'monthly_revenue' => $monthlyRevenue
                ],
                'recentOrders' => $recentOrders,
                'topProducts' => $topProducts,
                'lowStockProducts' => $lowStockProducts,
                'recentReviews' => $recentReviews
            ]);
            
        } catch (\Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Return view with empty data on error
            return $this->view('shop-owner/dashboard', [
                'stats' => [
                    'total_products' => 0,
                    'active_products' => 0,
                    'low_stock' => 0,
                    'total_orders' => 0,
                    'pending_orders' => 0,
                    'processing_orders' => 0,
                    'shipped_orders' => 0,
                    'delivered_orders' => 0,
                    'total_revenue' => 0,
                    'monthly_revenue' => 0
                ],
                'recentOrders' => [],
                'topProducts' => [],
                'lowStockProducts' => [],
                'recentReviews' => []
            ]);
        }
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
    
    public function profilePage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        // Get user and profile data
        $userId = $_SESSION['user_id'];
        $user = $this->getUserModel()->find($userId);
        $profile = $this->getProfileModel()->getByUserId($userId);
        
        return $this->view('shop-owner/profile', [
            'user' => $user,
            'profile' => $profile
        ]);
    }
    
    public function getProfileData(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $user = $this->getUserModel()->find($userId);
            $profile = $this->getProfileModel()->getByUserId($userId);
            
            return $this->json([
                'success' => true,
                'user' => $user,
                'profile' => $profile
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load profile data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateProfile(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $data = $request->getBody();
            
            error_log("ShopOwnerController: Update request from user_id: " . $userId);
            error_log("ShopOwnerController: Request body: " . json_encode($data));
            
            // Update shop owner profile
            $updated = $this->getProfileModel()->updateProfile($userId, $data);
            
            if ($updated) {
                return $this->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'profile' => $this->getProfileModel()->getByUserId($userId)
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update profile'
                ], 400);
            }
            
        } catch (\Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while updating profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function uploadShopLogo(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $userId = $_SESSION['user_id'];
            
            if (!isset($_FILES['shop_logo']) || $_FILES['shop_logo']['error'] !== UPLOAD_ERR_OK) {
                return $this->json([
                    'success' => false,
                    'message' => 'No file uploaded or upload error'
                ], 400);
            }
            
            $logoPath = $this->getProfileModel()->uploadShopLogo($userId, $_FILES['shop_logo']);
            
            if ($logoPath) {
                return $this->json([
                    'success' => true,
                    'message' => 'Shop logo uploaded successfully',
                    'logo_url' => '/' . $logoPath
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to upload logo. Please check file type and size.'
                ], 400);
            }
            
        } catch (\Exception $e) {
            error_log("Logo upload error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while uploading logo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function uploadShopBanner(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $userId = $_SESSION['user_id'];
            
            if (!isset($_FILES['shop_banner']) || $_FILES['shop_banner']['error'] !== UPLOAD_ERR_OK) {
                return $this->json([
                    'success' => false,
                    'message' => 'No file uploaded or upload error'
                ], 400);
            }
            
            $bannerPath = $this->getProfileModel()->uploadShopBanner($userId, $_FILES['shop_banner']);
            
            if ($bannerPath) {
                return $this->json([
                    'success' => true,
                    'message' => 'Shop banner uploaded successfully',
                    'banner_url' => '/' . $bannerPath
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to upload banner. Please check file type and size.'
                ], 400);
            }
            
        } catch (\Exception $e) {
            error_log("Banner upload error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while uploading banner',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function uploadDocument(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $userId = $_SESSION['user_id'];
            $documentType = $request->getQuery('type', 'license'); // 'license' or 'tax'
            
            $fileKey = $documentType === 'license' ? 'business_license' : 'tax_document';
            
            if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
                return $this->json([
                    'success' => false,
                    'message' => 'No file uploaded or upload error'
                ], 400);
            }
            
            $documentPath = $this->getProfileModel()->uploadDocument($userId, $_FILES[$fileKey], $documentType);
            
            if ($documentPath) {
                return $this->json([
                    'success' => true,
                    'message' => 'Document uploaded successfully',
                    'document_url' => '/' . $documentPath
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to upload document. Please check file type and size.'
                ], 400);
            }
            
        } catch (\Exception $e) {
            error_log("Document upload error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while uploading document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function uploadShopImages(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }
        
        try {
            $userId = $_SESSION['user_id'];
            
            if (!isset($_FILES['shop_images']) || empty($_FILES['shop_images']['tmp_name'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'No files uploaded'
                ], 400);
            }
            
            $uploadedImages = $this->getProfileModel()->uploadShopImages($userId, $_FILES['shop_images']);
            
            if (!empty($uploadedImages)) {
                return $this->json([
                    'success' => true,
                    'message' => count($uploadedImages) . ' image(s) uploaded successfully',
                    'images' => array_map(function($path) { return '/' . $path; }, $uploadedImages)
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to upload images. Please check file types and sizes.'
                ], 400);
            }
            
        } catch (\Exception $e) {
            error_log("Shop images upload error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while uploading images',
                'error' => $e->getMessage()
            ], 500);
        }
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
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            
            // Get filters from request
            $filters = [
                'search' => $request->get('search', ''),
                'status' => $request->get('status', ''),
                'payment_method' => $request->get('payment_method', ''),
                'payment_status' => $request->get('payment_status', ''),
                'date_from' => $request->get('date_from', ''),
                'date_to' => $request->get('date_to', '')
            ];
            
            // Remove empty filters
            $filters = array_filter($filters);
            
            $orders = $this->getOrderModel()->getOrdersByShopOwner($shopOwnerId, $filters);
            $stats = $this->getOrderModel()->getShopOwnerOrderStats($shopOwnerId);
            
            return $this->json([
                'success' => true,
                'orders' => $orders,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getOrder(Request $request): Response
    {
        // Clear any output buffering to prevent header issues
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("=== GET ORDER REQUEST ===");
        error_log("Session status: " . session_status());
        error_log("Session ID: " . session_id());
        error_log("User ID: " . ($_SESSION['user_id'] ?? 'not set'));
        error_log("User type: " . ($_SESSION['user_type'] ?? 'not set'));
        error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
        error_log("GET params: " . json_encode($_GET));
        
        if (!$this->checkShopOwnerAuth()) {
            error_log("Auth check failed - returning unauthorized");
            return $this->getShopOwnerResponse();
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $orderId = (int)$request->get('id', 0);
            
            error_log("Getting order - Shop Owner ID: $shopOwnerId, Order ID: $orderId");
            
            if ($orderId <= 0) {
                error_log("Invalid order ID: $orderId");
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid order ID'
                ], 400);
            }
            
            $order = $this->getOrderModel()->getOrderDetailsForShopOwner($orderId, $shopOwnerId);
            
            error_log("Order result: " . ($order ? json_encode($order) : "Not found"));
            
            if (!$order) {
                return $this->json([
                    'success' => false,
                    'message' => 'Order not found or access denied'
                ], 404);
            }
            
            error_log("Returning order successfully");
            return $this->json([
                'success' => true,
                'order' => $order
            ]);
            
        } catch (\Exception $e) {
            error_log("Error in getOrder: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->json([
                'success' => false,
                'message' => 'Failed to load order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateOrderStatus(Request $request): Response
    {
        // Clear any output buffering to prevent header issues
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("=== UPDATE ORDER STATUS REQUEST ===");
        error_log("Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("URI: " . $_SERVER['REQUEST_URI']);
        
        if (!$this->checkShopOwnerAuth()) {
            error_log("Auth check failed");
            return $this->getShopOwnerResponse();
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $rawInput = file_get_contents('php://input');
            error_log("Raw input: " . $rawInput);
            
            $data = json_decode($rawInput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error: " . json_last_error_msg());
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid JSON data'
                ], 400);
            }
            
            error_log("Parsed data: " . json_encode($data));
            
            $orderId = (int)($data['order_id'] ?? 0);
            $newStatus = $data['status'] ?? '';
            $paymentStatus = $data['payment_status'] ?? '';
            
            error_log("Order ID: $orderId, New Status: $newStatus, Payment Status: $paymentStatus");
            
            if ($orderId <= 0) {
                error_log("Invalid order ID");
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid order ID'
                ], 400);
            }
            
            $messages = [];
            $hasUpdates = false;
            
            // Update order status if provided
            if (!empty($newStatus)) {
                error_log("Updating order status to: $newStatus");
                $result = $this->getOrderModel()->updateOrderStatusWithValidation($orderId, $newStatus, $shopOwnerId);
                error_log("Update result: " . json_encode($result));
                if (!$result['success']) {
                    error_log("Order status update failed: " . $result['message']);
                    return $this->json($result, 400);
                }
                $messages[] = $result['message'];
                $hasUpdates = true;
            }
            
            // Update payment status if provided
            if (!empty($paymentStatus)) {
                error_log("Updating payment status to: $paymentStatus");
                $result = $this->getOrderModel()->updatePaymentStatusForOrder($orderId, $paymentStatus, $shopOwnerId);
                error_log("Payment update result: " . json_encode($result));
                if (!$result['success']) {
                    error_log("Payment status update failed: " . $result['message']);
                    return $this->json($result, 400);
                }
                $messages[] = $result['message'];
                $hasUpdates = true;
            }
            
            if (!$hasUpdates) {
                error_log("No updates provided");
                return $this->json([
                    'success' => false,
                    'message' => 'No updates provided'
                ], 400);
            }
            
            $finalMessage = !empty($messages) ? implode('. ', $messages) : 'Order updated successfully';
            
            $response = [
                'success' => true,
                'message' => $finalMessage
            ];
            
            error_log("Returning success response: " . json_encode($response));
            return $this->json($response);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteOrder(Request $request): Response
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("=== DELETE ORDER REQUEST ===");
        
        if (!$this->checkShopOwnerAuth()) {
            error_log("Auth check failed");
            return $this->getShopOwnerResponse();
        }
        
        try {
            $shopOwnerId = $_SESSION['user_id'];
            $data = json_decode(file_get_contents('php://input'), true);
            
            error_log("Delete request data: " . json_encode($data));
            
            $orderId = (int)($data['order_id'] ?? 0);
            
            error_log("Deleting order ID: $orderId for shop owner: $shopOwnerId");
            
            if ($orderId <= 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid order ID'
                ], 400);
            }
            
            $result = $this->getOrderModel()->deleteOrderWithValidation($orderId, $shopOwnerId);
            
            error_log("Delete result: " . json_encode($result));
            
            return $this->json($result, $result['success'] ? 200 : 400);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to delete order',
                'error' => $e->getMessage()
            ], 500);
        }
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

    // =========================================================
    // EARNINGS & PAYOUT METHODS
    // =========================================================

    /**
     * Render earnings & payouts page
     */
    public function earningsPage(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->redirect('/login');
        }
        return $this->view('shop-owner/earnings');
    }

    /**
     * API: Get earnings summary, transaction history, and payout status
     */
    public function getEarningsData(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }

        try {
            $shopOwnerId = $_SESSION['user_id'];
            $page = max(1, (int)$request->getQuery('page', 1));

            $balanceModel = new ShopOwnerBalance();
            $payoutModel = new \App\Models\ShopOwnerPayout();

            $summary = $balanceModel->getEarningsSummary($shopOwnerId);
            $transactions = $balanceModel->getTransactionHistory($shopOwnerId, $page, 15);
            $payouts = $payoutModel->getPayoutsByOwner($shopOwnerId, 1, 10);
            $canWithdraw = $balanceModel->canWithdraw($shopOwnerId);

            // Get payout details from profile
            $profile = $this->getProfileModel()->getByUserId($shopOwnerId);

            return $this->json([
                'success' => true,
                'summary' => $summary,
                'transactions' => $transactions,
                'payouts' => $payouts,
                'can_withdraw' => $canWithdraw,
                'payout_details' => [
                    'bank_name' => $profile['payout_bank_name'] ?? '',
                    'account_number' => $profile['payout_account_number'] ?? '',
                    'account_holder' => $profile['payout_account_holder'] ?? '',
                    'branch_name' => $profile['payout_branch_name'] ?? ''
                ]
            ]);

        } catch (\Exception $e) {
            error_log("Earnings data error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to load earnings data'
            ], 500);
        }
    }

    /**
     * API: Submit payout withdrawal request
     */
    public function requestPayout(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }

        try {
            $shopOwnerId = $_SESSION['user_id'];
            $data = $request->getJsonBody();
            
            // Get payout details from profile (or from request body)
            $profile = $this->getProfileModel()->getByUserId($shopOwnerId);
            
            $payoutDetails = [
                'amount' => $data['amount'] ?? 0,
                'bank_name' => $data['bank_name'] ?? $profile['payout_bank_name'] ?? '',
                'account_number' => $data['account_number'] ?? $profile['payout_account_number'] ?? '',
                'account_holder' => $data['account_holder'] ?? $profile['payout_account_holder'] ?? '',
                'branch_name' => $data['branch_name'] ?? $profile['payout_branch_name'] ?? ''
            ];

            $payoutModel = new \App\Models\ShopOwnerPayout();
            $result = $payoutModel->createRequest($shopOwnerId, $payoutDetails);

            return $this->json($result, $result['success'] ? 200 : 400);

        } catch (\Exception $e) {
            error_log("Payout request error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to submit payout request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Update shop owner's payout bank details
     */
    public function updatePayoutDetails(Request $request): Response
    {
        if (!$this->checkShopOwnerAuth()) {
            return $this->getShopOwnerResponse();
        }

        try {
            $shopOwnerId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            // Validate
            if (empty($data['bank_name']) || empty($data['account_number']) || empty($data['account_holder'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Bank name, account number, and account holder are required'
                ], 400);
            }

            // Update profile payout columns
            $profileModel = $this->getProfileModel();
            $profile = $profileModel->getByUserId($shopOwnerId);
            
            if (!$profile) {
                return $this->json(['success' => false, 'message' => 'Profile not found'], 404);
            }

            $sql = "UPDATE shop_owner_profiles 
                    SET payout_bank_name = ?, 
                        payout_account_number = ?, 
                        payout_account_holder = ?, 
                        payout_branch_name = ?
                    WHERE user_id = ?";
            
            $balanceModel = new ShopOwnerBalance();
            $balanceModel->runQuery($sql, [
                $data['bank_name'],
                $data['account_number'],
                $data['account_holder'],
                $data['branch_name'] ?? '',
                $shopOwnerId
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Payout details updated successfully'
            ]);

        } catch (\Exception $e) {
            error_log("Update payout details error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to update payout details'
            ], 500);
        }
    }
}