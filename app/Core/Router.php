<?php

namespace App\Core;

/**
 * Router Class
 * 
 * Handles URL routing and dispatching requests to appropriate controllers
 */
class Router
{
    private array $routes = [];
    private array $patterns = [
        'id' => '[0-9]+',
        'slug' => '[a-z0-9-]+',
        'username' => '[a-zA-Z0-9_]+',
    ];

    /**
     * Add a route
     */
    public function addRoute(string $method, string $path, array $config): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $config['controller'],
            'action' => $config['action'],
            'middleware' => $config['middleware'] ?? [],
            'pattern' => $this->buildPattern($path)
        ];
    }

    /**
     * Dispatch request to appropriate controller
     */
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                return $this->handleRoute($route, $matches, $request);
            }
        }

        // No route found, return 404
        return $this->handleNotFound($request);
    }

    /**
     * Handle matched route
     */
    private function handleRoute(array $route, array $matches, Request $request): Response
    {
        try {
            // Extract parameters from URL
            $params = array_slice($matches, 1);
            $request->setParams($params);

            // Apply route-specific middleware
            foreach ($route['middleware'] as $middlewareName) {
                $middlewareClass = "App\\Middleware\\{$middlewareName}";
                if (class_exists($middlewareClass)) {
                    $middleware = new $middlewareClass();
                    $request = $middleware->handle($request);
                }
            }

            // Create controller instance
            $controllerClass = "App\\Controllers\\{$route['controller']}";
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }

            $controller = new $controllerClass();
            $action = $route['action'];

            if (!method_exists($controller, $action)) {
                throw new \Exception("Action {$action} not found in controller {$controllerClass}");
            }

            // Call controller action
            $response = $controller->$action($request);

            // Ensure we return a Response object
            if (!$response instanceof Response) {
                if (is_string($response)) {
                    $responseObj = new Response();
                    $responseObj->setContent($response);
                    return $responseObj;
                } elseif (is_array($response)) {
                    $responseObj = new Response();
                    $responseObj->setContent(json_encode($response));
                    $responseObj->setHeader('Content-Type', 'application/json');
                    return $responseObj;
                } else {
                    throw new \Exception("Controller action must return a Response object, string, or array");
                }
            }

            return $response;

        } catch (\Exception $e) {
            return $this->handleError($e, $request);
        }
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(Request $request): Response
    {
        $response = new Response();
        $response->setStatusCode(404);

        // Check if this is an API request
        if (strpos($request->getPath(), '/api/') === 0) {
            $response->setContent(json_encode([
                'error' => 'Not Found',
                'message' => 'The requested resource was not found',
                'status' => 404
            ]));
            $response->setHeader('Content-Type', 'application/json');
        } else {
            // Load 404 page template
            $view = new View();
            $content = $view->render('errors/404', [
                'title' => 'Page Not Found',
                'message' => 'The page you are looking for could not be found.'
            ]);
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Handle errors
     */
    private function handleError(\Exception $e, Request $request): Response
    {
        $response = new Response();
        $response->setStatusCode(500);

        // Log the error
        error_log("Router Error: " . $e->getMessage());

        // Check if this is an API request
        if (strpos($request->getPath(), '/api/') === 0) {
            $error = [
                'error' => 'Internal Server Error',
                'status' => 500
            ];

            if (APP_CONFIG['app']['debug'] ?? false) {
                $error['message'] = $e->getMessage();
                $error['file'] = $e->getFile();
                $error['line'] = $e->getLine();
                $error['trace'] = $e->getTraceAsString();
            } else {
                $error['message'] = 'Something went wrong. Please try again later.';
            }

            $response->setContent(json_encode($error));
            $response->setHeader('Content-Type', 'application/json');
        } else {
            if (APP_CONFIG['app']['debug'] ?? false) {
                $content = "<h1>Application Error</h1>";
                $content .= "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                $content .= "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
                $content .= "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
                $content .= "<h3>Stack Trace:</h3>";
                $content .= "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                $view = new View();
                $content = $view->render('errors/500', [
                    'title' => 'Internal Server Error',
                    'message' => 'Something went wrong. Please try again later.'
                ]);
            }
            
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Build regex pattern from route path
     */
    private function buildPattern(string $path): string
    {
        $pattern = preg_replace_callback('/\{(\w+)\}/', function($matches) {
            $param = $matches[1];
            return '(' . ($this->patterns[$param] ?? '[^/]+') . ')';
        }, $path);

        return '#^' . $pattern . '$#';
    }

    /**
     * Get all registered routes
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}