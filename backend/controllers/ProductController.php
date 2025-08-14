<?php
/**
 * Product Controller
 * Handles product-related API endpoints
 */

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/ProductCategory.php';

class ProductController {
    
    private $productModel;
    private $categoryModel;
    private $validator;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new ProductCategory();
        $this->validator = new Validator();
    }
    
    /**
     * Get all products or single product
     * GET /api/products or /api/products/{id}
     */
    public function index() {
        try {
            $productId = $_GET['id'] ?? null;
            $category = $_GET['category'] ?? null;
            $search = $_GET['search'] ?? null;
            $brand = $_GET['brand'] ?? null;
            $minPrice = $_GET['min_price'] ?? null;
            $maxPrice = $_GET['max_price'] ?? null;
            $featured = $_GET['featured'] ?? null;
            $bestselling = $_GET['bestselling'] ?? null;
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 12;
            
            // Get single product
            if ($productId) {
                $product = $this->productModel->getWithCategory($productId);
                if (!$product) {
                    Response::notFound('Product not found');
                }
                
                // Process JSON fields
                $product = $this->processProductJsonFields($product);
                
                // Get related products
                $product['related_products'] = $this->productModel->getRelatedProducts(
                    $productId, 
                    $product['category_id'], 
                    4
                );
                
                // Get reviews
                $product['reviews'] = $this->productModel->getProductReviews($productId);
                $product['rating_summary'] = $this->productModel->getRatingSummary($productId);
                
                Response::success($product);
            }
            
            // Get featured products
            if ($featured) {
                $products = $this->productModel->getFeaturedProducts($limit);
                $products = array_map([$this, 'processProductJsonFields'], $products);
                Response::success($products);
            }
            
            // Get bestselling products
            if ($bestselling) {
                $products = $this->productModel->getBestSellingProducts($limit);
                $products = array_map([$this, 'processProductJsonFields'], $products);
                Response::success($products);
            }
            
            // Search/filter products
            if ($search || $category || $brand || $minPrice || $maxPrice) {
                $products = $this->productModel->searchProducts($search, $category, $minPrice, $maxPrice, $brand);
                $products = array_map([$this, 'processProductJsonFields'], $products);
                
                // Paginate results
                $total = count($products);
                $offset = ($page - 1) * $limit;
                $paginatedProducts = array_slice($products, $offset, $limit);
                
                Response::paginated($paginatedProducts, $page, ceil($total / $limit), $total, $limit);
            }
            
            // Get all active products with pagination
            $result = $this->productModel->paginate($page, $limit, ['status' => 'active'], 'created_at DESC');
            $result['data'] = array_map([$this, 'processProductJsonFields'], $result['data']);
            
            Response::paginated(
                $result['data'], 
                $result['pagination']['current_page'],
                $result['pagination']['total_pages'],
                $result['pagination']['total_records'],
                $result['pagination']['per_page']
            );
            
        } catch (Exception $e) {
            Logger::error('Product fetch error: ' . $e->getMessage());
            Response::serverError('Failed to fetch products');
        }
    }
    
    /**
     * Create new product
     * POST /api/products
     */
    public function store() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data');
            }
            
            // Validation rules
            $rules = [
                'name' => 'required|min:3|max:200',
                'category_id' => 'required|integer|positive',
                'brand' => 'required|min:2|max:100',
                'price' => 'required|decimal|positive',
                'stock_quantity' => 'required|integer',
                'description' => 'max:1000'
            ];
            
            $validation = $this->validator->validate($input, $rules);
            if (!$validation['valid']) {
                Response::validationError($validation['errors']);
            }
            
            // Check if category exists
            if (!$this->categoryModel->find($input['category_id'])) {
                Response::error('Category not found', 404);
            }
            
            // Generate SKU if not provided
            if (empty($input['sku'])) {
                $input['sku'] = $this->generateSKU($input['brand'], $input['name']);
            }
            
            // Process JSON fields
            if (isset($input['specifications']) && is_array($input['specifications'])) {
                $input['specifications'] = json_encode($input['specifications']);
            }
            if (isset($input['features']) && is_array($input['features'])) {
                $input['features'] = json_encode($input['features']);
            }
            if (isset($input['images']) && is_array($input['images'])) {
                $input['images'] = json_encode($input['images']);
            }
            if (isset($input['tags']) && is_array($input['tags'])) {
                $input['tags'] = json_encode($input['tags']);
            }
            
            $product = $this->productModel->create($input);
            
            if ($product) {
                $product = $this->processProductJsonFields($product);
                Logger::info('Product created', ['product_id' => $product['id']]);
                Response::success($product, 'Product created successfully');
            } else {
                Response::error('Failed to create product');
            }
            
        } catch (Exception $e) {
            Logger::error('Product creation error: ' . $e->getMessage());
            Response::serverError('Failed to create product');
        }
    }
    
    /**
     * Update product
     * PUT /api/products/{id}
     */
    public function update() {
        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                Response::error('Product ID is required');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data');
            }
            
            // Check if product exists
            if (!$this->productModel->find($productId)) {
                Response::notFound('Product not found');
            }
            
            // Validation rules (optional fields for update)
            $rules = [
                'name' => 'min:3|max:200',
                'category_id' => 'integer|positive',
                'brand' => 'min:2|max:100',
                'price' => 'decimal|positive',
                'stock_quantity' => 'integer',
                'description' => 'max:1000'
            ];
            
            $validation = $this->validator->validate($input, $rules);
            if (!$validation['valid']) {
                Response::validationError($validation['errors']);
            }
            
            // Process JSON fields
            if (isset($input['specifications']) && is_array($input['specifications'])) {
                $input['specifications'] = json_encode($input['specifications']);
            }
            if (isset($input['features']) && is_array($input['features'])) {
                $input['features'] = json_encode($input['features']);
            }
            if (isset($input['images']) && is_array($input['images'])) {
                $input['images'] = json_encode($input['images']);
            }
            if (isset($input['tags']) && is_array($input['tags'])) {
                $input['tags'] = json_encode($input['tags']);
            }
            
            $product = $this->productModel->update($productId, $input);
            
            if ($product) {
                $product = $this->processProductJsonFields($product);
                Logger::info('Product updated', ['product_id' => $productId]);
                Response::success($product, 'Product updated successfully');
            } else {
                Response::error('Failed to update product');
            }
            
        } catch (Exception $e) {
            Logger::error('Product update error: ' . $e->getMessage());
            Response::serverError('Failed to update product');
        }
    }
    
    /**
     * Delete product
     * DELETE /api/products/{id}
     */
    public function delete() {
        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                Response::error('Product ID is required');
            }
            
            // Check if product exists
            if (!$this->productModel->find($productId)) {
                Response::notFound('Product not found');
            }
            
            $deleted = $this->productModel->delete($productId);
            
            if ($deleted) {
                Logger::info('Product deleted', ['product_id' => $productId]);
                Response::success(null, 'Product deleted successfully');
            } else {
                Response::error('Failed to delete product');
            }
            
        } catch (Exception $e) {
            Logger::error('Product deletion error: ' . $e->getMessage());
            Response::serverError('Failed to delete product');
        }
    }
    
    /**
     * Get product categories
     * GET /api/products/categories
     */
    public function categories() {
        try {
            $categories = $this->categoryModel->findAll(['is_active' => true], 'name ASC');
            Response::success($categories);
            
        } catch (Exception $e) {
            Logger::error('Categories fetch error: ' . $e->getMessage());
            Response::serverError('Failed to fetch categories');
        }
    }
    
    /**
     * Get product brands
     * GET /api/products/brands
     */
    public function brands() {
        try {
            $brands = $this->productModel->getAllBrands();
            Response::success($brands);
            
        } catch (Exception $e) {
            Logger::error('Brands fetch error: ' . $e->getMessage());
            Response::serverError('Failed to fetch brands');
        }
    }
    
    /**
     * Get low stock products
     * GET /api/products/low-stock
     */
    public function lowStock() {
        try {
            $products = $this->productModel->getLowStockProducts();
            $products = array_map([$this, 'processProductJsonFields'], $products);
            Response::success($products);
            
        } catch (Exception $e) {
            Logger::error('Low stock fetch error: ' . $e->getMessage());
            Response::serverError('Failed to fetch low stock products');
        }
    }
    
    /**
     * Update product stock
     * PUT /api/products/{id}/stock
     */
    public function updateStock() {
        try {
            $productId = $_GET['id'] ?? null;
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$productId || !$input) {
                Response::error('Product ID and stock data are required');
            }
            
            $rules = [
                'quantity' => 'required|integer',
                'operation' => 'in:set,add,subtract'
            ];
            
            $validation = $this->validator->validate($input, $rules);
            if (!$validation['valid']) {
                Response::validationError($validation['errors']);
            }
            
            $updated = $this->productModel->updateStock(
                $productId, 
                $input['quantity'], 
                $input['operation'] ?? 'set'
            );
            
            if ($updated) {
                Logger::info('Product stock updated', ['product_id' => $productId]);
                Response::success(null, 'Stock updated successfully');
            } else {
                Response::error('Failed to update stock');
            }
            
        } catch (Exception $e) {
            Logger::error('Stock update error: ' . $e->getMessage());
            Response::serverError('Failed to update stock');
        }
    }
    
    /**
     * Get product statistics
     * GET /api/products/stats
     */
    public function stats() {
        try {
            $stats = $this->productModel->getProductStats();
            Response::success($stats);
            
        } catch (Exception $e) {
            Logger::error('Product stats error: ' . $e->getMessage());
            Response::serverError('Failed to fetch product statistics');
        }
    }
    
    /**
     * Process product JSON fields
     */
    private function processProductJsonFields($product) {
        $jsonFields = ['specifications', 'features', 'images', 'tags'];
        
        foreach ($jsonFields as $field) {
            if (isset($product[$field])) {
                $decoded = json_decode($product[$field], true);
                $product[$field] = $decoded ?: [];
            }
        }
        
        return $product;
    }
    
    /**
     * Generate product SKU
     */
    private function generateSKU($brand, $name) {
        $brandCode = strtoupper(substr($brand, 0, 3));
        $nameWords = explode(' ', $name);
        $nameCode = strtoupper(substr($nameWords[0], 0, 3));
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return $brandCode . '-' . $nameCode . '-' . $random;
    }
}