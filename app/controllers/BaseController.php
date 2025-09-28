<?php

namespace App\Controllers;

use Core\Response;
use Core\Request;

abstract class BaseController
{
    /**
     * Start session safely (only if not already started)
     */
    protected function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    protected function view(string $view, array $data = []): Response
    {
        extract($data);
        
        ob_start();
        
        $viewFile = APP_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$viewFile}");
        }
        
        include $viewFile;
        $content = ob_get_clean();
        
        if (strpos($view, 'layouts/') === 0) {
            return new Response($content);
        }
        
        ob_start();
        include APP_PATH . '/views/layouts/main.php';
        $layout = ob_get_clean();
        
        return new Response($layout);
    }
    
    protected function json(array $data = [], int $status = 200): Response
    {
        $response = new Response(json_encode($data), $status);
        $response->setHeader('Content-Type', 'application/json');
        return $response;
    }
    
    protected function redirect(string $url): Response
    {
        $response = new Response('', 302);
        $response->setHeader('Location', $url);
        return $response;
    }
    
    protected function viewWithoutLayout(string $view, array $data = []): Response
    {
        extract($data);
        
        ob_start();
        
        $viewFile = APP_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$viewFile}");
        }
        
        include $viewFile;
        $content = ob_get_clean();
        
        return new Response($content);
    }
}