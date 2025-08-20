<?php

namespace App\Core;

/**
 * Application Core Class
 * 
 * Main application class that handles request routing,
 * middleware processing, and response generation.
 */
class Application
{
    private Router $router;
    private Container $container;
    private array $config;
    private array $middleware = [];

    public function __construct()
    {
        $this->config = APP_CONFIG;
        $this->container = new Container();
        $this->router = new Router();
        
        $this->registerServices();
        $this->registerMiddleware();
        $this->registerRoutes();
    }

    /**
     * Handle incoming HTTP request
     */
    public function handleRequest(): Response
    {
        $request = Request::createFromGlobals();
        
        // Apply middleware
        foreach ($this->middleware as $middlewareName) {
            $middleware = $this->container->resolve($middlewareName);
            $request = $middleware->handle($request);
        }
        
        // Route the request
        return $this->router->dispatch($request);
    }

    /**
     * Register core services in the container
     */
    private function registerServices(): void
    {
        // Register database
        $this->container->singleton('database', function() {
            return DB_CONNECTION;
        });

        // Register view renderer
        $this->container->singleton('view', function() {
            return new View();
        });

        // Register response
        $this->container->bind('response', function() {
            return new Response();
        });

        // Register request
        $this->container->bind('request', function() {
            return Request::createFromGlobals();
        });
    }

    /**
     * Register middleware
     */
    private function registerMiddleware(): void
    {
        $middlewareConfig = $this->config['routes']['middleware'] ?? [];
        
        foreach ($middlewareConfig as $name => $class) {
            if (is_array($class)) {
                foreach ($class as $middlewareClass) {
                    $this->container->bind($middlewareClass, function() use ($middlewareClass) {
                        $fullClass = "App\\Middleware\\{$middlewareClass}";
                        return new $fullClass();
                    });
                }
            } else {
                $this->container->bind($class, function() use ($class) {
                    $fullClass = "App\\Middleware\\{$class}";
                    return new $fullClass();
                });
            }
        }

        // Register default middleware
        $this->middleware = $this->config['routes']['defaults']['middleware'] ?? [];
    }

    /**
     * Register application routes
     */
    private function registerRoutes(): void
    {
        $routes = $this->config['routes'];

        // Register web routes
        if (isset($routes['web'])) {
            foreach ($routes['web'] as $path => $route) {
                $this->router->addRoute('GET', $path, $route);
            }
        }

        // Register API routes
        if (isset($routes['api'])) {
            foreach ($routes['api'] as $route => $config) {
                [$method, $path] = explode(' ', $route, 2);
                $this->router->addRoute($method, $path, $config);
            }
        }
    }

    /**
     * Get container instance
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get configuration
     */
    public function getConfig(string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (isset($value[$segment])) {
                $value = $value[$segment];
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Set configuration value
     */
    public function setConfig(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $segment) {
            if (!isset($config[$segment])) {
                $config[$segment] = [];
            }
            $config = &$config[$segment];
        }

        $config = $value;
    }
}