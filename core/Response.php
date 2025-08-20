<?php

namespace Core;

class Response
{
    private string $content;
    private int $status;
    private array $headers;
    
    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }
    
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    public function getStatus(): int
    {
        return $this->status;
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function send(): void
    {
        http_response_code($this->status);
        
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        
        echo $this->content;
    }
}