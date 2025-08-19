<?php

namespace App\Core;

/**
 * Response Class
 * 
 * Represents an HTTP response with methods to set
 * status codes, headers, and content
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';
    private static array $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    /**
     * Set HTTP status code
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Get HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set response header
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Get response header
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * Get all headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set response content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get response content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Send JSON response
     */
    public function json(array $data, int $statusCode = 200): self
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $this;
    }

    /**
     * Send redirect response
     */
    public function redirect(string $url, int $statusCode = 302): self
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        return $this;
    }

    /**
     * Send file download response
     */
    public function download(string $filePath, string $fileName = null): self
    {
        if (!file_exists($filePath)) {
            return $this->setStatusCode(404)->setContent('File not found');
        }

        $fileName = $fileName ?: basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        $this->setHeader('Content-Type', $mimeType);
        $this->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->setHeader('Content-Length', (string)filesize($filePath));
        $this->setContent(file_get_contents($filePath));

        return $this;
    }

    /**
     * Set cookie
     */
    public function setCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = true
    ): self {
        setcookie($name, $value, $expires, $path, $domain, $secure, $httpOnly);
        return $this;
    }

    /**
     * Send the response
     */
    public function send(): void
    {
        // Send status code
        $statusText = self::$statusTexts[$this->statusCode] ?? 'Unknown Status';
        header("HTTP/1.1 {$this->statusCode} {$statusText}");

        // Send headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        // Send content
        echo $this->content;
    }

    /**
     * Create success response
     */
    public static function success($data = null, string $message = 'Success', int $statusCode = 200): self
    {
        $response = new self();
        return $response->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ], $statusCode);
    }

    /**
     * Create error response
     */
    public static function error(string $message = 'Error', int $statusCode = 400, $errors = null): self
    {
        $response = new self();
        $data = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('c')
        ];

        if ($errors !== null) {
            $data['errors'] = $errors;
        }

        return $response->json($data, $statusCode);
    }

    /**
     * Create validation error response
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): self
    {
        return self::error($message, 422, $errors);
    }

    /**
     * Create unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    /**
     * Create forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    /**
     * Create not found response
     */
    public static function notFound(string $message = 'Not found'): self
    {
        return self::error($message, 404);
    }

    /**
     * Create server error response
     */
    public static function serverError(string $message = 'Internal server error'): self
    {
        return self::error($message, 500);
    }

    /**
     * Check if response is successful
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Check if response is a redirect
     */
    public function isRedirect(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * Check if response is a client error
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if response is a server error
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }
}