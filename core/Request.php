<?php

namespace Core;

class Request
{
    private string $method;
    private string $uri;
    private array $query;
    private array $body;
    private array $headers;
    private array $routeParams = [];
    private static ?string $rawInput = null;
    
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Parse the URI and strip subdirectory base path (e.g. /GoPlay/) for XAMPP support
        $rawUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        if ($scriptDir !== '/' && $scriptDir !== '.' && strpos($rawUri, $scriptDir) === 0) {
            $rawUri = substr($rawUri, strlen($scriptDir));
        }
        $this->uri = '/' . ltrim($rawUri, '/');
        
        $this->query = $_GET;
        
        // Cache raw input on first read
        if (self::$rawInput === null) {
            self::$rawInput = file_get_contents('php://input');
        }
        
        $this->body = $this->parseBody();
        $this->headers = $this->parseHeaders();
    }
    
    public function getMethod(): string
    {
        return $this->method;
    }
    
    public function getUri(): string
    {
        return $this->uri;
    }
    
    public function getQuery(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        
        return $this->query[$key] ?? $default;
    }
    
    /**
     * Get value from query params or body (unified method)
     */
    public function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return array_merge($this->query, $this->body);
        }
        
        // Check query first, then body
        return $this->query[$key] ?? $this->body[$key] ?? $default;
    }
    
    /**
     * Get value from query parameters only ($_GET)
     */
    public function query(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        
        return $this->query[$key] ?? $default;
    }
    
    public function getBody(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->body;
        }
        
        return $this->body[$key] ?? $default;
    }
    
    public function getHeader(string $name): ?string
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? null;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    private function parseBody(): array
    {
        if ($this->method === 'GET') {
            return [];
        }

        $contentType = $this->getHeader('content-type') ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $decoded = json_decode(self::$rawInput, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        // If no content-type or not JSON, try to detect JSON anyway
        if (!empty(self::$rawInput) && (self::$rawInput[0] === '{' || self::$rawInput[0] === '[')) {
            $decoded = json_decode(self::$rawInput, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $_POST;
    }
    
    private function parseHeaders(): array
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = $value;
            }
        }
        
        // Add Content-Type if exists (doesn't have HTTP_ prefix)
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }
        
        // Add Content-Length if exists
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
        }
        
        return $headers;
    }
    
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }
    
    public function getParam(string $name, $default = null)
    {
        return $this->routeParams[$name] ?? $default;
    }
    
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }
    
    public function getJsonBody(): array
    {
        $contentType = $this->getHeader('content-type') ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $decoded = json_decode(self::$rawInput, true);
            return $decoded ?: [];
        }
        
        return [];
    }
}