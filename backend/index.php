<?php
/**
 * GoPlay Sports Platform API Entry Point
 * Main router for all API requests
 */

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('Asia/Colombo');

// Include autoloader
require_once __DIR__ . '/autoload.php';

// Include CORS handler
require_once __DIR__ . '/cors.php';

// Set content type
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Get request method and URI
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Remove /backend from URI if present
    $uri = str_replace('/backend', '', $uri);
    
    // Basic routing
    $routes = [
        'GET' => [
            '/api/test' => function() {
                Response::json(['message' => 'GoPlay API is running!', 'version' => '1.0.0']);
            }
        ],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    // Load route files
    $routeFiles = glob(__DIR__ . '/routes/*.php');
    foreach ($routeFiles as $file) {
        require_once $file;
    }

    // Simple router
    if (isset($routes[$method][$uri])) {
        $routes[$method][$uri]();
    } elseif (strpos($uri, '/api/') === 0) {
        // Dynamic routing for API endpoints
        $segments = explode('/', trim($uri, '/'));
        
        if (count($segments) >= 2 && $segments[0] === 'api') {
            $controller = ucfirst($segments[1]) . 'Controller';
            $controllerFile = __DIR__ . '/controllers/' . $controller . '.php';
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controller)) {
                    $controllerInstance = new $controller();
                    $action = isset($segments[2]) ? $segments[2] : 'index';
                    
                    if (method_exists($controllerInstance, $action)) {
                        $controllerInstance->$action();
                    } else {
                        Response::error('Method not found', 404);
                    }
                } else {
                    Response::error('Controller not found', 404);
                }
            } else {
                Response::error('Endpoint not found', 404);
            }
        } else {
            Response::error('Invalid API endpoint', 404);
        }
    } else {
        Response::error('Route not found', 404);
    }

} catch (Exception $e) {
    Logger::error('API Error: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    Response::error('Internal server error', 500);
}