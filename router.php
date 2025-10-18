<?php
// Router script for PHP development server
// This handles URL rewriting for the development server

$requestedPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if the request is for an actual file (assets, etc.)
$filePath = __DIR__ . '/public' . $requestedPath;

// If it's a request for a static file and the file exists, serve it
if ($requestedPath !== '/' && is_file($filePath)) {
    return false; // Let PHP development server handle static files
}

// For all other requests, route through index.php
$_SERVER['REQUEST_URI'] = $requestedPath;
require_once __DIR__ . '/index.php';