<?php

namespace Core;

class Application
{
    private Router $router;
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->router = new Router();
        
        $this->initializeConstants();
        $this->loadConfig();
    }
    
    public function getRouter(): Router
    {
        return $this->router;
    }
    
    public function run(): void
    {
        try {
            $request = new Request();
            $response = $this->router->dispatch($request);
            $response->send();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function initializeConstants(): void
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__));
        }
        
        if (!defined('APP_PATH')) {
            define('APP_PATH', ROOT_PATH . '/app');
        }
        
        if (!defined('CORE_PATH')) {
            define('CORE_PATH', ROOT_PATH . '/core');
        }
        
        if (!defined('PUBLIC_PATH')) {
            define('PUBLIC_PATH', ROOT_PATH . '/public');
        }
    }
    
    private function loadConfig(): void
    {
        
    }
    
    private function handleException(\Exception $e): void
    {
        http_response_code(500);

        // Check if this is an API request
        $isApiRequest = strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/provider-applications') !== false ||
                       (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($isApiRequest) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] ? $e->getMessage() : null,
                'file' => isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] ? $e->getFile() . ':' . $e->getLine() : null
            ]);
        } else {
            if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG']) {
                echo '<pre>';
                echo 'Error: ' . $e->getMessage() . "\n";
                echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
                echo 'Stack trace:' . "\n" . $e->getTraceAsString();
                echo '</pre>';
            } else {
                echo 'Internal Server Error';
            }
        }
    }
}