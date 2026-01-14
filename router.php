<?php

/**
 * PHP Built-in Server Router
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// All other requests go through index.php
require_once __DIR__ . '/public/index.php';
