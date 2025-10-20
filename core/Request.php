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
        $this->uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
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
            return $decoded ?: [];
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

        // Add Content-Type separately (it's in CONTENT_TYPE, not HTTP_CONTENT_TYPE)
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
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

    public function all(): array
    {
        return array_merge($this->query, $this->body, $this->routeParams);
    }
}