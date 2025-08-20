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
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                return $this->callHandler($route['handler'], $request);
            }
        }
        
        return $this->notFound();
    }
    
    private function matchPath(string $pattern, string $uri): bool
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return preg_match($pattern, $uri);
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