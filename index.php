<?php

/**
 * GoPlay Sports Platform
 * Main Entry Point
 * 
 * This file serves as the front controller for all HTTP requests.
 * It handles routing, middleware, and response generation.
 */

// Define application constants
define('APP_START', microtime(true));
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load environment variables (simple implementation)
if (file_exists(ROOT_PATH . '/.env')) {
    $env = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Set up error reporting based on environment
if ($_ENV['APP_DEBUG'] ?? false) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Include the application bootstrap
require_once ROOT_PATH . '/bootstrap.php';

try {
    // Initialize the application
    $app = new Core\Application();
    
    // Set up basic routes
    $router = $app->getRouter();
    
    // Define routes
    $router->get('/', 'HomeController@index');
    $router->get('/login', 'AuthController@login');
    $router->post('/auth/login', 'AuthController@handleLogin');
    $router->get('/signup', 'AuthController@signup');
    $router->post('/auth/register', 'AuthController@handleRegister');
    $router->post('/auth/logout', 'AuthController@logout');
    $router->get('/auth/check', 'AuthController@checkAuth');
    
    // Dashboard routes
    $router->get('/admin/dashboard', 'AdminController@dashboard');
    $router->get('/ground-owner/dashboard', 'GroundOwnerController@dashboard');
    $router->get('/coach/dashboard', 'CoachController@dashboard');
    $router->get('/shop-owner/dashboard', 'ShopOwnerController@dashboard');
    
    // Coach page routes
    $router->get('/coach/profile', 'CoachController@profilePage');
    $router->get('/coach/sessions', 'CoachController@sessionsPage');
    $router->get('/coach/clients', 'CoachController@clientsPage');
    $router->get('/coach/programs', 'CoachController@programsPage');
    $router->get('/coach/assessments', 'CoachController@assessmentsPage');
    $router->get('/coach/earnings', 'CoachController@earningsPage');
    $router->get('/coach/availability', 'CoachController@availabilityPage');
    $router->get('/coach/reviews', 'CoachController@reviewsPage');
    $router->get('/coach/resources', 'CoachController@resourcesPage');
    $router->get('/coach/notifications', 'CoachController@notificationsPage');
    $router->get('/coach/settings', 'CoachController@settingsPage');
    $router->get('/coach/help', 'CoachController@helpPage');
    
    // Coach API routes
    $router->get('/api/coach/dashboard', 'CoachController@getDashboardData');
    $router->get('/api/coach/profile', 'CoachController@getProfile');
    $router->put('/api/coach/profile', 'CoachController@updateProfile');
    $router->post('/api/coach/profile/avatar', 'CoachController@uploadAvatar');
    $router->get('/api/coach/sessions', 'CoachController@getSessions');
    $router->post('/api/coach/sessions', 'CoachController@createSession');
    $router->get('/api/coach/clients', 'CoachController@getClients');
    $router->post('/api/coach/clients', 'CoachController@createClient');
    $router->get('/api/coach/sidebar-stats', 'CoachController@getSidebarStats');
    $router->get('/api/coach/notifications/count', 'CoachController@getNotificationsCount');
    
    // Public Coach API routes (for booking)
    $router->get('/api/coaches', 'CoachController@getCoachesForBooking');
    $router->get('/api/coaches/{id}', 'CoachController@getCoachDetails');
    $router->get('/api/sports-categories', 'CoachController@getSportsCategories');
    
    // Public Grounds API routes (for booking)
    $router->get('/api/grounds', 'BookingController@getGrounds');
    //$router->get('/api/grounds/{id}', 'BookingController@getGroundDetails');

    
    // Cart API routes (CRUD operations)
    $router->get('/api/cart', 'CartController@getCart');
    $router->get('/api/cart/count', 'CartController@getCartCount');
    $router->post('/api/cart/add', 'CartController@addToCart');
    $router->put('/api/cart/update', 'CartController@updateCartItem');
    $router->delete('/api/cart/remove', 'CartController@removeFromCart');
    $router->delete('/api/cart/clear', 'CartController@clearCart');
    $router->post('/api/cart/merge', 'CartController@mergeGuestCart');

    $router->get('/api/ground/{id}', 'BookingController@getGroundById');

    
    // Ground Owner page routes
    $router->get('/ground-owner/grounds', 'GroundOwnerController@groundsPage');
    $router->get('/ground-owner/bookings', 'GroundOwnerController@bookingsPage');
    $router->get('/ground-owner/earnings', 'GroundOwnerController@earningsPage');
    $router->get('/ground-owner/reviews', 'GroundOwnerController@reviewsPage');
    $router->get('/ground-owner/schedule', 'GroundOwnerController@schedulePage');
    $router->get('/ground-owner/maintenance', 'GroundOwnerController@maintenancePage');
    $router->get('/ground-owner/profile', 'GroundOwnerController@profilePage');
    $router->get('/ground-owner/settings', 'GroundOwnerController@settingsPage');
    
    // Ground Owner API routes
    $router->get('/api/ground-owner/grounds', 'GroundOwnerController@getGrounds');
    $router->post('/api/ground-owner/grounds', 'GroundOwnerController@createGround');
    $router->get('/api/ground-owner/grounds/{id}', 'GroundOwnerController@getGround');
    $router->put('/api/ground-owner/grounds/{id}', 'GroundOwnerController@updateGround');
    $router->delete('/api/ground-owner/grounds/{id}', 'GroundOwnerController@deleteGround');
    $router->get('/api/ground-owner/categories', 'GroundOwnerController@getSportsCategories');

    // Ground Owner Booking Management API routes
    $router->get('/api/ground-owner/dashboard-stats', 'GroundOwnerController@getDashboardStats');
    $router->get('/api/ground-owner/bookings', 'GroundOwnerController@getBookings');
    $router->put('/api/ground-owner/bookings/{id}/cancel', 'GroundOwnerController@cancelBooking');
    $router->get('/api/ground-owner/facilities/{id}/bookings', 'GroundOwnerController@getFacilityBookings');
    $router->put('/api/ground-owner/bookings/{id}/status', 'GroundOwnerController@updateBookingStatus');
    $router->get('/book-ground', 'BookingController@bookGround');
    $router->get('/ground-details', 'BookingController@groundDetails');
    $router->get('/book/{id}', 'BookingController@redirectToGroundDetails');
    $router->get('/book-coach', 'BookingController@bookCoach');

    // Public booking API routes
    $router->post('/api/booking/ground', 'BookingController@createGroundBooking');
    $router->get('/api/booking/availability', 'BookingController@checkAvailability');
    $router->get('/coaches', 'CoachController@index');
    $router->get('/shop', 'ProductController@index');
    $router->get('/api/products', 'ProductController@getProducts');
    $router->get('/api/categories', 'ProductController@getCategories');
    $router->get('/api/products/search', 'ProductController@search');
    $router->get('/product/{id}', 'ProductController@show');
    $router->get('/news', 'NewsController@index');
    $router->get('/payment', 'PaymentController@payment');
    $router->get('/payment/success', 'PaymentController@success');
    
    
    // Shop Owner page routes
    $router->get('/shop-owner/products', 'ShopOwnerController@productsPage');
    $router->get('/shop-owner/inventory', 'ShopOwnerController@inventoryPage');
    $router->get('/shop-owner/orders', 'ShopOwnerController@ordersPage');
    $router->get('/shop-owner/reviews', 'ShopOwnerController@reviewsPage');
    $router->get('/shop-owner/profile', 'ShopOwnerController@profilePage');
    $router->get('/shop-owner/sales', 'ShopOwnerController@salesPage');

    // Redirect old .php URLs to new routes
    $router->get('/shop-owner/products.php', 'ShopOwnerController@productsPage');
    $router->get('/shop-owner/inventory.php', 'ShopOwnerController@inventoryPage');
    $router->get('/shop-owner/Inventory.php', 'ShopOwnerController@inventoryPage');
    $router->get('/shop-owner/orders.php', 'ShopOwnerController@ordersPage');
    $router->get('/shop-owner/reviews.php', 'ShopOwnerController@reviewsPage');
    $router->get('/shop-owner/profile.php', 'ShopOwnerController@profilePage');
    $router->get('/shop-owner/dashboard.php', 'ShopOwnerController@dashboard');
    
    // Shop Owner API endpoints
    $router->get('/api/shop-owner/dashboard', 'ShopOwnerController@getDashboardStats');
    $router->get('/api/shop-owner/products', 'ShopOwnerController@getProducts');
    $router->post('/api/shop-owner/products', 'ShopOwnerController@createProduct');
    $router->get('/api/shop-owner/products/{id}', 'ShopOwnerController@getProduct');
    $router->put('/api/shop-owner/products/{id}', 'ShopOwnerController@updateProduct');
    $router->delete('/api/shop-owner/products/{id}', 'ShopOwnerController@deleteProduct');
    $router->post('/api/shop-owner/products/{id}/upload-images', 'ShopOwnerController@uploadProductImages');
    $router->get('/api/shop-owner/orders', 'ShopOwnerController@getOrders');
    $router->get('/api/shop-owner/orders/{id}', 'ShopOwnerController@getOrder');
    $router->put('/api/shop-owner/orders/{id}/status', 'ShopOwnerController@updateOrderStatus');
    $router->get('/api/shop-owner/inventory', 'ShopOwnerController@getInventory');
    $router->put('/api/shop-owner/inventory/{id}', 'ShopOwnerController@updateStock');
    $router->get('/api/shop-owner/analytics', 'ShopOwnerController@getAnalytics');
    $router->get('/api/shop-owner/categories', 'ShopOwnerController@getCategories');

    // News Routes - Updated
$router->get('/news', 'NewsController@index');
$router->get('/news/search', 'NewsController@search');
$router->get('/news/load-more', 'NewsController@loadMore');
$router->get('/api/news/live-search', 'NewsController@liveSearch'); // NEW!
$router->get('/news/{slug}', 'NewsController@show');

    // USER PROFILE ROUTES - Add these to your existing routes in index.php
    
    // User profile routes (ESSENTIAL - ADD THESE)
    $router->get('/user/profile', 'UserController@profile');
    $router->get('/profile', 'UserController@profile'); // Alternative route
    $router->get('/dashboard', 'UserController@dashboard'); // User dashboard
    
    // User API routes
    $router->get('/api/user/profile', 'UserController@getProfile');
    $router->put('/api/user/profile', 'UserController@updateProfile');
    $router->post('/api/user/avatar', 'UserController@uploadAvatar');
    $router->post('/api/user/change-password', 'UserController@changePassword');
    $router->get('/api/user/bookings', 'UserController@getBookings');
    $router->get('/api/user/orders', 'UserController@getOrders');

    // User Ground Booking API routes
    $router->get('/api/user/ground-bookings', 'UserController@getGroundBookings');
    $router->post('/api/user/ground-bookings', 'UserController@createGroundBooking');
    $router->get('/api/user/ground-bookings/{id}', 'UserController@getGroundBookingDetails');
    $router->put('/api/user/ground-bookings/{id}/cancel', 'UserController@cancelGroundBooking');
    
    // Additional user routes
    $router->get('/my-bookings', 'UserController@groundBookingsDashboard');
    $router->get('/my-ground-bookings', 'UserController@groundBookingsDashboard');
    $router->get('/my-orders', 'UserController@myOrders');
    $router->get('/cart', 'UserController@cart');
    $router->get('/notifications', 'UserController@notifications');
    $router->get('/settings', 'UserController@settings');
    
    // LOGOUT ROUTE FIX - Make sure this exists (should already be there)
    $router->post('/auth/logout', 'AuthController@logout');
    $router->get('/logout', 'AuthController@logout'); // Add GET version as fallback

    // Admin News Management Routes
$router->get('/admin/news', 'Admin\AdminNewsController@index');
$router->get('/admin/news/create', 'Admin\AdminNewsController@create');
$router->post('/admin/news/store', 'Admin\AdminNewsController@store');
$router->get('/admin/news/edit/{id}', 'Admin\AdminNewsController@edit');
$router->post('/admin/news/update/{id}', 'Admin\AdminNewsController@update');
$router->delete('/admin/news/delete/{id}', 'Admin\AdminNewsController@delete');

    
// Admin User Management Page
$router->get('/admin/users', 'Admin\AdminUserController@index');

// Admin User Management API Routes
$router->get('/api/admin/users', 'Admin\AdminUserController@getUsers');
$router->get('/api/admin/users/statistics', 'Admin\AdminUserController@getStatistics');
$router->get('/api/admin/users/{id}', 'Admin\AdminUserController@getUser');
$router->put('/api/admin/users/{id}/role', 'Admin\AdminUserController@updateRole');
$router->put('/api/admin/users/{id}/status', 'Admin\AdminUserController@updateStatus');
$router->post('/api/admin/users/{id}/reset-password', 'Admin\AdminUserController@resetPassword');
$router->delete('/api/admin/users/{id}', 'Admin\AdminUserController@deleteUser');
    
// Admin Dashboard API Routes
$router->get('/admin/api/stats', 'AdminController@getStats');
$router->get('/admin/api/revenue-chart', 'AdminController@getRevenueChart');
$router->get('/admin/api/recent-registrations', 'AdminController@getRecentRegistrations');
$router->get('/admin/api/recent-content', 'AdminController@getRecentContent');
$router->get('/admin/api/user-breakdown', 'AdminController@getUserBreakdown');
$router->get('/admin/api/notifications', 'AdminController@getNotifications');
$router->get('/admin/api/profile', 'AdminController@getProfile');


// Admin Analytics Routes
$router->get('/admin/analytics', 'Admin\AnalyticsController@index');
$router->get('/api/admin/analytics/overview', 'Admin\AnalyticsController@getOverviewStats');
$router->get('/api/admin/analytics/users', 'Admin\AnalyticsController@getUserAnalytics');
$router->get('/api/admin/analytics/revenue', 'Admin\AnalyticsController@getRevenueAnalytics');
$router->get('/api/admin/analytics/bookings', 'Admin\AnalyticsController@getBookingAnalytics');
$router->get('/api/admin/analytics/products', 'Admin\AnalyticsController@getProductAnalytics');
$router->get('/api/admin/analytics/export', 'Admin\AnalyticsController@exportData');
$router->get('/api/admin/analytics/activity', 'Admin\AnalyticsController@getActivityLogs');

// Admin Settings Routes
$router->get('/admin/settings', 'Admin\AdminSettingsController@index');
$router->get('/api/admin/settings', 'Admin\AdminSettingsController@getSettings');
$router->post('/api/admin/settings/update', 'Admin\AdminSettingsController@updateSetting');
$router->post('/api/admin/settings/bulk', 'Admin\AdminSettingsController@updateBulk');
$router->get('/api/admin/settings/system-info', 'Admin\AdminSettingsController@getSystemInfo');
$router->post('/api/admin/settings/clear-cache', 'Admin\AdminSettingsController@clearCache');
$router->post('/api/admin/settings/test-email', 'Admin\AdminSettingsController@testEmail');
$router->post('/api/admin/settings/backup-database', 'Admin\AdminSettingsController@backupDatabase');
$router->get('/api/admin/settings/activity-logs', 'Admin\AdminSettingsController@getActivityLogs');


    /* Debug routes
    $router->get('/debug', function() {
        require_once 'debug.php';
        return new Core\Response('');
    });
    $router->get('/debug-shop', function() {
        require_once 'debug_shop.php';
        return new Core\Response('');
    });*/
    
    // Run the application
    $app->run();
    
} catch (Exception $e) {
    // Handle uncaught exceptions
    if ($_ENV['APP_DEBUG'] ?? false) {
        // Show detailed error in debug mode
        echo "<h1>Application Error</h1>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
        echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        // Show generic error in production
        http_response_code(500);
        
        // Check if this is an API request
        $isApiRequest = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0 || 
                       (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
        
        if ($isApiRequest) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => 'Something went wrong. Please try again later.',
                'status' => 500
            ]);
        } else {
            echo "<h1>Internal Server Error</h1>";
            echo "<p>Something went wrong. Please try again later.</p>";
        }
    }
    
    // Log the error
    if (function_exists('error_log')) {
        error_log("Application Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    }
}

// Calculate and log execution time in debug mode
if ($_ENV['APP_DEBUG'] ?? false) {
    $executionTime = microtime(true) - APP_START;
    if (isset($_GET['debug'])) {
        echo "<!-- Execution time: " . number_format($executionTime * 1000, 2) . "ms -->";
    }
}