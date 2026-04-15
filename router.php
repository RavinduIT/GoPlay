<?php

/**
 * PHP Built-in Server Router
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly.
// Support both /public/... URLs and root-relative /... URLs.
$staticFile = null;

if ($uri !== '/') {
    if (strpos($uri, '/public/') === 0) {
        $staticFile = __DIR__ . $uri;
    } else {
        $staticFile = __DIR__ . '/public' . $uri;
    }
}

if ($staticFile && is_file($staticFile)) {
    return false;
}

// All other requests go through index.php
require_once __DIR__ . '/public/index.php';
