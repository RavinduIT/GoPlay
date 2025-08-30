<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Product;
use App\Models\Category;

class ShopOwnerController extends BaseController
{
    private ?Product $productModel = null;
    private ?Category $categoryModel = null;
    
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
            $totalProducts = $this->getProductModel()->count(['shop_owner_id' => $shopOwnerId]);
            $activeProducts = $this->getProductModel()->count(['shop_owner_id' => $shopOwnerId, 'status' => 'active']);
            $lowStockProducts = count($this->getProductModel()->getLowStockProducts());
            
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
            $total = $this->getProductModel()->count(['shop_owner_id' => $shopOwnerId]);
            
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
            
            // Get low stock products
            $lowStockProducts = $this->getProductModel()->getLowStockProducts();
            
            return $this->json([
                'success' => true,
                'inventory' => [
                    'low_stock_products' => $lowStockProducts,
                    'total_low_stock' => count($lowStockProducts)
                ]
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
            $categories = $this->getCategoryModel()->getAllActive();
            
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
}