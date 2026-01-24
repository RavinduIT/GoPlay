<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;

/**
 * Product Controller
 * 
 * Handles shop/product operations
 */
class ProductController extends BaseController
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
    
    private function checkUserAuth(): bool
    {
        $this->startSession();
        return isset($_SESSION['user_id']);
    }
    
    private function getUserId(): ?int
    {
        $this->startSession();
        return $_SESSION['user_id'] ?? null;
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
                return new Response('Product ID is required', 400);
            }
            
            // Get product with shop owner details
            $product = $this->getProductModel()->getProductWithShopOwner($id);
            
            if (!$product) {
                return new Response('Product not found', 404);
            }
            
            // Get related products
            $relatedProducts = $this->getProductModel()->getRelatedProducts(
                $id, 
                $product['category_id'], 
                4
            );
            
            // Get product reviews
            $reviews = $this->getReviewModel()->getProductReviews($id);
            
            // Check if current user has reviewed this product
            $userReview = null;
            if ($this->checkUserAuth()) {
                $userReview = $this->getReviewModel()->getUserProductReview($this->getUserId(), $id);
            }
            
            return $this->view('shop/product-details', [
                'product' => $product,
                'relatedProducts' => $relatedProducts,
                'reviews' => $reviews,
                'userReview' => $userReview
            ]);
            
        } catch (\Exception $e) {
            error_log("Product details error: " . $e->getMessage());
            return new Response('Server Error: ' . $e->getMessage(), 500);
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

    /**
     * Submit a new review for a product
     */
    public function submitReview(Request $request): Response
    {
        // Check if user is logged in
        if (!$this->checkUserAuth()) {
            $this->startSession();
            $_SESSION['error_message'] = 'Please login to write a review';
            $productId = (int)$request->getParam('id');
            return $this->redirect("/product/{$productId}");
        }

        try {
            $productId = (int)$request->getParam('id');
            $userId = $this->getUserId();
            
            // Get form data
            $rating = (int)($_POST['rating'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $reviewText = trim($_POST['review_text'] ?? '');
            
            // Validation
            if ($rating < 1 || $rating > 5) {
                $this->startSession();
                $_SESSION['error_message'] = 'Please select a rating between 1 and 5 stars';
                return $this->redirect("/product/{$productId}");
            }
            
            // Check if user has already reviewed this product
            $existingReview = $this->getReviewModel()->getUserProductReview($userId, $productId);
            if ($existingReview) {
                $this->startSession();
                $_SESSION['error_message'] = 'You have already reviewed this product. You can edit your existing review.';
                return $this->redirect("/product/{$productId}");
            }
            
            // Create review data
            $reviewData = [
                'product_id' => $productId,
                'user_id' => $userId,
                'rating' => $rating,
                'title' => $title,
                'review_text' => $reviewText,
                'is_verified_purchase' => 0
            ];
            
            // Create the review
            $reviewId = $this->getReviewModel()->createReview($reviewData);
            
            if ($reviewId) {
                $this->startSession();
                $_SESSION['success_message'] = 'Thank you! Your review has been submitted successfully.';
            } else {
                $this->startSession();
                $_SESSION['error_message'] = 'Failed to submit review. Please try again.';
            }
            
            return $this->redirect("/product/{$productId}");
            
        } catch (\Exception $e) {
            error_log("Submit review error: " . $e->getMessage());
            $this->startSession();
            $_SESSION['error_message'] = 'An error occurred while submitting your review.';
            $productId = (int)$request->getParam('id');
            return $this->redirect("/product/{$productId}");
        }
    }

    /**
     * Update an existing review
     */
    public function updateReview(Request $request): Response
    {
        // Check if user is logged in
        if (!$this->checkUserAuth()) {
            $this->startSession();
            $_SESSION['error_message'] = 'Please login to edit your review';
            return $this->redirect('/login');
        }

        try {
            $reviewId = (int)($_POST['review_id'] ?? 0);
            $productId = (int)($_POST['product_id'] ?? 0);
            $userId = $this->getUserId();
            
            // Verify ownership
            if (!$this->getReviewModel()->isReviewOwner($reviewId, $userId)) {
                $this->startSession();
                $_SESSION['error_message'] = 'You can only edit your own reviews';
                return $this->redirect("/product/{$productId}");
            }
            
            // Get form data
            $rating = (int)($_POST['rating'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $reviewText = trim($_POST['review_text'] ?? '');
            
            // Validation
            if ($rating < 1 || $rating > 5) {
                $this->startSession();
                $_SESSION['error_message'] = 'Please select a rating between 1 and 5 stars';
                return $this->redirect("/product/{$productId}");
            }
            
            // Update review data
            $reviewData = [
                'rating' => $rating,
                'title' => $title,
                'review_text' => $reviewText
            ];
            
            // Update the review
            $success = $this->getReviewModel()->updateReview($reviewId, $reviewData);
            
            if ($success) {
                $this->startSession();
                $_SESSION['success_message'] = 'Your review has been updated successfully.';
            } else {
                $this->startSession();
                $_SESSION['error_message'] = 'Failed to update review. Please try again.';
            }
            
            return $this->redirect("/product/{$productId}");
            
        } catch (\Exception $e) {
            error_log("Update review error: " . $e->getMessage());
            $this->startSession();
            $_SESSION['error_message'] = 'An error occurred while updating your review.';
            $productId = (int)($_POST['product_id'] ?? 0);
            return $this->redirect("/product/{$productId}");
        }
    }

    /**
     * Delete a review
     */
    public function deleteReview(Request $request): Response
    {
        // Check if user is logged in
        if (!$this->checkUserAuth()) {
            $this->startSession();
            $_SESSION['error_message'] = 'Please login to delete your review';
            return $this->redirect('/login');
        }

        try {
            $reviewId = (int)($_POST['review_id'] ?? 0);
            $productId = (int)($_POST['product_id'] ?? 0);
            $userId = $this->getUserId();
            
            // Verify ownership
            if (!$this->getReviewModel()->isReviewOwner($reviewId, $userId)) {
                $this->startSession();
                $_SESSION['error_message'] = 'You can only delete your own reviews';
                return $this->redirect("/product/{$productId}");
            }
            
            // Delete the review
            $success = $this->getReviewModel()->deleteReview($reviewId);
            
            if ($success) {
                $this->startSession();
                $_SESSION['success_message'] = 'Your review has been deleted successfully.';
            } else {
                $this->startSession();
                $_SESSION['error_message'] = 'Failed to delete review. Please try again.';
            }
            
            return $this->redirect("/product/{$productId}");
            
        } catch (\Exception $e) {
            error_log("Delete review error: " . $e->getMessage());
            $this->startSession();
            $_SESSION['error_message'] = 'An error occurred while deleting your review.';
            $productId = (int)($_POST['product_id'] ?? 0);
            return $this->redirect("/product/{$productId}");
        }
    }
}