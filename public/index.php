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
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Load environment configuration
require_once ROOT_PATH . '/vendor/autoload.php';

// Load environment variables
if (file_exists(ROOT_PATH . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();
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
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Colombo');

// Include the application bootstrap
require_once APP_PATH . '/bootstrap.php';

try {
    // Initialize the application
    $app = new App\Core\Application();
    
    // Handle the request
    $response = $app->handleRequest();
    
    // Send the response
    $response->send();
    
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
            // Load error page template
            $errorPage = file_exists(ROOT_PATH . '/resources/views/errors/500.php') 
                ? ROOT_PATH . '/resources/views/errors/500.php'
                : __DIR__ . '/500.html';
            
            if (file_exists($errorPage)) {
                include $errorPage;
            } else {
                echo "<h1>Internal Server Error</h1>";
                echo "<p>Something went wrong. Please try again later.</p>";
            }
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