<?php

namespace App\Core;

/**
 * Request Class
 * 
 * Represents an HTTP request with methods to access
 * request data, headers, and parameters
 */
class Request
{
    private string $method;
    private string $path;
    private array $query;
    private array $body;
    private array $headers;
    private array $params = [];
    private array $files;
    private array $server;

    public function __construct(
        string $method,
        string $path,
        array $query = [],
        array $body = [],
        array $headers = [],
        array $files = [],
        array $server = []
    ) {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->query = $query;
        $this->body = $body;
        $this->headers = $headers;
        $this->files = $files;
        $this->server = $server;
    }

    /**
     * Create request from PHP globals
     */
    public static function createFromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $query = $_GET;
        $body = $_POST;
        $files = $_FILES;
        $server = $_SERVER;

        // Parse request body for JSON requests
        if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $jsonBody = json_decode(file_get_contents('php://input'), true);
            if ($jsonBody !== null) {
                $body = $jsonBody;
            }
        }

        // Extract headers
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$headerName] = $value;
            }
        }

        return new self($method, $path, $query, $body, $headers, $files, $server);
    }

    /**
     * Get HTTP method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get request path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get query parameter
     */
    public function query(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    /**
     * Get all query parameters
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Get request body parameter
     */
    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * Get all request body data
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Get header value
     */
    public function header(string $key, $default = null)
    {
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get all headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get uploaded file
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Get all uploaded files
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Get server variable
     */
    public function server(string $key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    /**
     * Set route parameters
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Get route parameter by index
     */
    public function param(int $index, $default = null)
    {
        return $this->params[$index] ?? $default;
    }

    /**
     * Get all route parameters
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Check if request is API request
     */
    public function isApi(): bool
    {
        return strpos($this->path, '/api/') === 0 || 
               strpos($this->header('Accept', ''), 'application/json') !== false;
    }

    /**
     * Check if request is JSON
     */
    public function isJson(): bool
    {
        return strpos($this->header('Content-Type', ''), 'application/json') !== false;
    }

    /**
     * Get client IP address
     */
    public function getClientIp(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($this->server[$key])) {
                $ip = $this->server[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }

        return '0.0.0.0';
    }

    /**
     * Get user agent
     */
    public function getUserAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Validate request data
     */
    public function validate(array $rules): array
    {
        $errors = [];
        $data = array_merge($this->query, $this->body);

        foreach ($rules as $field => $rule) {
            $ruleList = is_string($rule) ? explode('|', $rule) : $rule;
            $value = $data[$field] ?? null;

            foreach ($ruleList as $singleRule) {
                [$ruleName, $ruleValue] = explode(':', $singleRule . ':');

                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = "{$field} is required";
                        }
                        break;

                    case 'email':
                        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "{$field} must be a valid email";
                        }
                        break;

                    case 'min':
                        if ($value && strlen($value) < (int)$ruleValue) {
                            $errors[$field][] = "{$field} must be at least {$ruleValue} characters";
                        }
                        break;

                    case 'max':
                        if ($value && strlen($value) > (int)$ruleValue) {
                            $errors[$field][] = "{$field} must not exceed {$ruleValue} characters";
                        }
                        break;

                    case 'numeric':
                        if ($value && !is_numeric($value)) {
                            $errors[$field][] = "{$field} must be numeric";
                        }
                        break;
                }
            }
        }

        return $errors;
    }
}