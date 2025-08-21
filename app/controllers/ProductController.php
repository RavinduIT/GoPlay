<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Product;
use App\Models\Category;

/**
 * Product Controller
 * 
 * Handles shop/product operations
 */
class ProductController extends BaseController
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

    /**
     * Display shop page
     */
    public function index(Request $request): Response
    {
        try {
            // Get filter parameters
            $filters = [
                'category' => $request->getQuery('category'),
                'search' => $request->getQuery('search'),
                'sort' => $request->getQuery('sort', 'featured'),
                'min_price' => $request->getQuery('min_price'),
                'max_price' => $request->getQuery('max_price')
            ];
            
            // Remove empty filters
            $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');
            
            // Get products from database
            $products = $this->getProductModel()->getActiveProducts($filters);
            
            // Get categories for filters
            $categories = $this->getCategoryModel()->getCategoriesWithProductCounts();
            
            // Get featured products for homepage section
            $featuredProducts = $this->getProductModel()->getFeaturedProducts(6);
            
            return $this->view('shop/shop', [
                'products' => $products,
                'categories' => $categories,
                'featuredProducts' => $featuredProducts,
                'currentFilters' => $filters
            ]);
            
        } catch (\Exception $e) {
            // Log error and show fallback
            error_log("Shop page error: " . $e->getMessage());
            
            return $this->view('shop/shop', [
                'products' => [],
                'categories' => [],
                'featuredProducts' => [],
                'currentFilters' => [],
                'error' => 'Unable to load products. Please try again later.'
            ]);
        }
    }

    /**
     * Get products via AJAX
     */
    public function getProducts(Request $request): Response
    {
        try {
            $filters = [
                'category' => $request->getQuery('category'),
                'search' => $request->getQuery('search'),
                'sort' => $request->getQuery('sort', 'featured'),
                'min_price' => $request->getQuery('min_price'),
                'max_price' => $request->getQuery('max_price'),
                'limit' => $request->getQuery('limit', 50)
            ];
            
            // Remove empty filters
            $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');
            
            $products = $this->getProductModel()->getActiveProducts($filters);
            
            return $this->json([
                'success' => true,
                'products' => $products,
                'total' => count($products)
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories via AJAX
     */
    public function getCategories(Request $request): Response
    {
        try {
            $categories = $this->getCategoryModel()->getCategoriesWithProductCounts();
            
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

    /**
     * Display product details
     */
    public function show(Request $request): Response
    {
        try {
            $id = (int)$request->getParam('id');
            
            if (!$id) {
                return $this->view('errors/404');
            }
            
            $product = $this->getProductModel()->getProductWithCategory($id);
            
            if (!$product) {
                return $this->view('errors/404');
            }
            
            // Get related products
            $relatedProducts = $this->getProductModel()->getRelatedProducts(
                $id, 
                $product['category_id'], 
                4
            );
            
            // Get product reviews
            $reviews = $this->getProductModel()->getProductReviews($id);
            
            return $this->view('shop/product-details', [
                'product' => $product,
                'relatedProducts' => $relatedProducts,
                'reviews' => $reviews
            ]);
            
        } catch (\Exception $e) {
            error_log("Product details error: " . $e->getMessage());
            return $this->view('errors/500');
        }
    }

    /**
     * Display shopping cart
     */
    public function cart(Request $request): Response
    {
        return $this->view('shop/cart');
    }

    /**
     * Search products via AJAX
     */
    public function search(Request $request): Response
    {
        try {
            $query = $request->getQuery('q', '');
            
            if (strlen($query) < 2) {
                return $this->json([
                    'success' => false,
                    'message' => 'Search query must be at least 2 characters'
                ]);
            }
            
            $filters = [
                'search' => $query,
                'limit' => $request->getQuery('limit', 20)
            ];
            
            $products = $this->getProductModel()->searchProducts($query, $filters);
            
            return $this->json([
                'success' => true,
                'products' => $products,
                'query' => $query,
                'total' => count($products)
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}