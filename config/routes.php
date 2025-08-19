<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    | These routes are for web interface (HTML responses)
    */
    'web' => [
        // Home & Public Pages
        '/' => ['controller' => 'HomeController', 'action' => 'index'],
        '/about' => ['controller' => 'HomeController', 'action' => 'about'],
        '/contact' => ['controller' => 'HomeController', 'action' => 'contact'],
        
        // Authentication Routes
        '/login' => ['controller' => 'AuthController', 'action' => 'showLogin'],
        '/register' => ['controller' => 'AuthController', 'action' => 'showRegister'],
        '/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
        
        // Facilities Routes
        '/facilities' => ['controller' => 'FacilityController', 'action' => 'index'],
        '/facilities/{id}' => ['controller' => 'FacilityController', 'action' => 'show'],
        '/book-ground' => ['controller' => 'FacilityController', 'action' => 'index'],
        '/ground-details/{id}' => ['controller' => 'FacilityController', 'action' => 'show'],
        
        // Coaches Routes
        '/coaches' => ['controller' => 'CoachController', 'action' => 'index'],
        '/coaches/{id}' => ['controller' => 'CoachController', 'action' => 'show'],
        '/book-coach' => ['controller' => 'CoachController', 'action' => 'index'],
        '/coach-profile/{id}' => ['controller' => 'CoachController', 'action' => 'show'],
        
        // Shop Routes
        '/shop' => ['controller' => 'ProductController', 'action' => 'index'],
        '/shop/category/{id}' => ['controller' => 'ProductController', 'action' => 'category'],
        '/product/{id}' => ['controller' => 'ProductController', 'action' => 'show'],
        '/cart' => ['controller' => 'CartController', 'action' => 'show'],
        
        // News Routes
        '/news' => ['controller' => 'NewsController', 'action' => 'index'],
        '/news/{slug}' => ['controller' => 'NewsController', 'action' => 'show'],
        
        // User Dashboard Routes (Requires Authentication)
        '/dashboard' => ['controller' => 'UserController', 'action' => 'dashboard', 'middleware' => 'auth'],
        '/profile' => ['controller' => 'UserController', 'action' => 'profile', 'middleware' => 'auth'],
        '/my-bookings' => ['controller' => 'BookingController', 'action' => 'userBookings', 'middleware' => 'auth'],
        '/my-orders' => ['controller' => 'OrderController', 'action' => 'userOrders', 'middleware' => 'auth'],
        
        // Payment Routes
        '/payment' => ['controller' => 'PaymentController', 'action' => 'show', 'middleware' => 'auth'],
        '/payment/success' => ['controller' => 'PaymentController', 'action' => 'success', 'middleware' => 'auth'],
        '/payment/cancel' => ['controller' => 'PaymentController', 'action' => 'cancel', 'middleware' => 'auth'],
        
        // Service Provider Registration
        '/register/coach' => ['controller' => 'AuthController', 'action' => 'showCoachRegister'],
        '/register/facility' => ['controller' => 'AuthController', 'action' => 'showFacilityRegister'],
        
        // Admin Routes (Requires Admin Role)
        '/admin' => ['controller' => 'AdminController', 'action' => 'dashboard', 'middleware' => 'admin'],
        '/admin/users' => ['controller' => 'AdminController', 'action' => 'users', 'middleware' => 'admin'],
        '/admin/facilities' => ['controller' => 'AdminController', 'action' => 'facilities', 'middleware' => 'admin'],
        '/admin/coaches' => ['controller' => 'AdminController', 'action' => 'coaches', 'middleware' => 'admin'],
        '/admin/products' => ['controller' => 'AdminController', 'action' => 'products', 'middleware' => 'admin'],
        '/admin/orders' => ['controller' => 'AdminController', 'action' => 'orders', 'middleware' => 'admin'],
        '/admin/analytics' => ['controller' => 'AdminController', 'action' => 'analytics', 'middleware' => 'admin'],
        '/admin/settings' => ['controller' => 'AdminController', 'action' => 'settings', 'middleware' => 'admin'],
        
        // Error Pages
        '/404' => ['controller' => 'ErrorController', 'action' => 'notFound'],
        '/500' => ['controller' => 'ErrorController', 'action' => 'serverError'],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    | These routes are for API endpoints (JSON responses)
    */
    'api' => [
        // Authentication API
        'POST /api/auth/login' => ['controller' => 'AuthController', 'action' => 'apiLogin'],
        'POST /api/auth/register' => ['controller' => 'AuthController', 'action' => 'apiRegister'],
        'POST /api/auth/logout' => ['controller' => 'AuthController', 'action' => 'apiLogout', 'middleware' => 'auth'],
        'POST /api/auth/refresh' => ['controller' => 'AuthController', 'action' => 'refreshToken', 'middleware' => 'auth'],
        
        // Facilities API
        'GET /api/facilities' => ['controller' => 'FacilityController', 'action' => 'apiIndex'],
        'GET /api/facilities/{id}' => ['controller' => 'FacilityController', 'action' => 'apiShow'],
        'POST /api/facilities' => ['controller' => 'FacilityController', 'action' => 'apiStore', 'middleware' => 'auth'],
        'PUT /api/facilities/{id}' => ['controller' => 'FacilityController', 'action' => 'apiUpdate', 'middleware' => 'auth'],
        'DELETE /api/facilities/{id}' => ['controller' => 'FacilityController', 'action' => 'apiDestroy', 'middleware' => 'auth'],
        
        // Coaches API
        'GET /api/coaches' => ['controller' => 'CoachController', 'action' => 'apiIndex'],
        'GET /api/coaches/{id}' => ['controller' => 'CoachController', 'action' => 'apiShow'],
        'POST /api/coaches' => ['controller' => 'CoachController', 'action' => 'apiStore', 'middleware' => 'auth'],
        'PUT /api/coaches/{id}' => ['controller' => 'CoachController', 'action' => 'apiUpdate', 'middleware' => 'auth'],
        
        // Bookings API
        'GET /api/bookings' => ['controller' => 'BookingController', 'action' => 'apiIndex', 'middleware' => 'auth'],
        'POST /api/bookings/facility' => ['controller' => 'BookingController', 'action' => 'apiFacilityBooking', 'middleware' => 'auth'],
        'POST /api/bookings/coach' => ['controller' => 'BookingController', 'action' => 'apiCoachBooking', 'middleware' => 'auth'],
        'PUT /api/bookings/{id}' => ['controller' => 'BookingController', 'action' => 'apiUpdate', 'middleware' => 'auth'],
        'DELETE /api/bookings/{id}' => ['controller' => 'BookingController', 'action' => 'apiCancel', 'middleware' => 'auth'],
        
        // Products API
        'GET /api/products' => ['controller' => 'ProductController', 'action' => 'apiIndex'],
        'GET /api/products/{id}' => ['controller' => 'ProductController', 'action' => 'apiShow'],
        'POST /api/products' => ['controller' => 'ProductController', 'action' => 'apiStore', 'middleware' => 'auth'],
        
        // Cart API
        'GET /api/cart' => ['controller' => 'CartController', 'action' => 'apiShow', 'middleware' => 'auth'],
        'POST /api/cart/add' => ['controller' => 'CartController', 'action' => 'apiAdd', 'middleware' => 'auth'],
        'PUT /api/cart/update' => ['controller' => 'CartController', 'action' => 'apiUpdate', 'middleware' => 'auth'],
        'DELETE /api/cart/remove' => ['controller' => 'CartController', 'action' => 'apiRemove', 'middleware' => 'auth'],
        
        // Orders API
        'GET /api/orders' => ['controller' => 'OrderController', 'action' => 'apiIndex', 'middleware' => 'auth'],
        'POST /api/orders' => ['controller' => 'OrderController', 'action' => 'apiStore', 'middleware' => 'auth'],
        'GET /api/orders/{id}' => ['controller' => 'OrderController', 'action' => 'apiShow', 'middleware' => 'auth'],
        
        // Payment API
        'POST /api/payments/process' => ['controller' => 'PaymentController', 'action' => 'apiProcess', 'middleware' => 'auth'],
        'POST /api/payments/webhook' => ['controller' => 'PaymentController', 'action' => 'webhook'],
        
        // Reviews API
        'GET /api/reviews/{type}/{id}' => ['controller' => 'ReviewController', 'action' => 'apiIndex'],
        'POST /api/reviews' => ['controller' => 'ReviewController', 'action' => 'apiStore', 'middleware' => 'auth'],
        
        // User API
        'GET /api/user/profile' => ['controller' => 'UserController', 'action' => 'apiProfile', 'middleware' => 'auth'],
        'PUT /api/user/profile' => ['controller' => 'UserController', 'action' => 'apiUpdateProfile', 'middleware' => 'auth'],
        
        // Admin API
        'GET /api/admin/stats' => ['controller' => 'AdminController', 'action' => 'apiStats', 'middleware' => 'admin'],
        'GET /api/admin/users' => ['controller' => 'AdminController', 'action' => 'apiUsers', 'middleware' => 'admin'],
        
        // News API
        'GET /api/news' => ['controller' => 'NewsController', 'action' => 'apiIndex'],
        'GET /api/news/{slug}' => ['controller' => 'NewsController', 'action' => 'apiShow'],
        
        // Search API
        'GET /api/search' => ['controller' => 'SearchController', 'action' => 'apiSearch'],
        'GET /api/search/facilities' => ['controller' => 'SearchController', 'action' => 'apiFacilities'],
        'GET /api/search/coaches' => ['controller' => 'SearchController', 'action' => 'apiCoaches'],
        'GET /api/search/products' => ['controller' => 'SearchController', 'action' => 'apiProducts'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Middleware Groups
    |--------------------------------------------------------------------------
    */
    'middleware' => [
        'auth' => 'AuthMiddleware',
        'admin' => ['AuthMiddleware', 'AdminMiddleware'],
        'cors' => 'CorsMiddleware',
        'rate_limit' => 'RateLimitMiddleware',
        'log' => 'LoggerMiddleware',
        'validate' => 'ValidationMiddleware',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Patterns
    |--------------------------------------------------------------------------
    */
    'patterns' => [
        'id' => '[0-9]+',
        'slug' => '[a-z0-9-]+',
        'username' => '[a-zA-Z0-9_]+',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Controllers
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'controller' => 'HomeController',
        'action' => 'index',
        'middleware' => ['cors', 'log'],
    ],
];