<?php

namespace Core;

class Router
{
    private array $routes = [];
    
    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put(string $path, $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete(string $path, $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    private function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        foreach ($this->routes as $route) {
            $params = [];
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri, $params)) {
                // Set the route parameters in the request
                $request->setRouteParams($params);
                return $this->callHandler($route['handler'], $request);
            }
        }
        
        return $this->notFound();
    }
    
    private function matchPath(string $pattern, string $uri, array &$params = []): bool
    {
        // Extract parameter names from the pattern
        $paramNames = [];
        preg_match_all('/\{([^}]+)\}/', $pattern, $matches);
        $paramNames = $matches[1];
        
        // Convert pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        // Match the URI against the pattern
        if (preg_match($pattern, $uri, $matches)) {
            // Extract parameter values
            array_shift($matches); // Remove full match
            $params = array_combine($paramNames, $matches) ?: [];
            return true;
        }
        
        return false;
    }
    
    private function callHandler($handler, Request $request): Response
    {
        if (is_string($handler)) {
            $parts = explode('@', $handler);
            $controllerClass = 'App\\Controllers\\' . $parts[0];
            $method = $parts[1];
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller not found: {$controllerClass}");
            }
            
            $controller = new $controllerClass();
            
            if (!method_exists($controller, $method)) {
                throw new \Exception("Method not found: {$method}");
            }
            
            return $controller->$method($request);
        }
        
        if (is_callable($handler)) {
            return $handler($request);
        }
        
        throw new \Exception("Invalid route handler");
    }
    
    private function notFound(): Response
    {
        return new Response('404 Not Found pp', 404);
    }
}