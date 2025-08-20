<?php

/**
 * Application Bootstrap
 * 
 * This file initializes the application core components:
 * - Autoloader registration
 * - Configuration loading
 * - Service container setup
 * - Error handlers
 * - Middleware registration
 */

// Register autoloader
require_once __DIR__ . '/autoload.php';

// Load core application classes
require_once __DIR__ . '/Core/Application.php';
require_once __DIR__ . '/Core/Router.php';
require_once __DIR__ . '/Core/Request.php';
require_once __DIR__ . '/Core/Response.php';
require_once __DIR__ . '/Core/View.php';
require_once __DIR__ . '/Core/Database.php';
require_once __DIR__ . '/Core/Container.php';

// Load configuration
$config = [];

// Load all configuration files
$configFiles = glob(CONFIG_PATH . '/*.php');
foreach ($configFiles as $configFile) {
    $configName = basename($configFile, '.php');
    $config[$configName] = require $configFile;
}

// Store configuration globally
define('APP_CONFIG', $config);

// Set up error and exception handlers
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($exception) {
    // Log the exception
    error_log("Uncaught exception: " . $exception->getMessage());
    
    // Show error page
    http_response_code(500);
    
    if (APP_CONFIG['app']['debug'] ?? false) {
        echo "<h1>Uncaught Exception</h1>";
        echo "<p>" . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    } else {
        echo "<h1>Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
    }
});

// Register shutdown function
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
        http_response_code(500);
        
        if (APP_CONFIG['app']['debug'] ?? false) {
            echo "<h1>Fatal Error</h1>";
            echo "<p>" . htmlspecialchars($error['message']) . "</p>";
            echo "<p>File: " . htmlspecialchars($error['file']) . " Line: " . $error['line'] . "</p>";
        } else {
            echo "<h1>Internal Server Error</h1>";
            echo "<p>Something went wrong. Please try again later.</p>";
        }
    }
});

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    $sessionConfig = APP_CONFIG['app']['session'] ?? [];
    
    if (isset($sessionConfig['cookie_name'])) {
        session_name($sessionConfig['cookie_name']);
    }
    
    if (isset($sessionConfig['lifetime'])) {
        ini_set('session.gc_maxlifetime', $sessionConfig['lifetime'] * 60);
    }
    
    session_start();
}

// Set up database connection
try {
    $dbConfig = APP_CONFIG['database']['connections'][APP_CONFIG['database']['default']] ?? [];
    
    if (!empty($dbConfig)) {
        $dsn = "{$dbConfig['driver']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
        
        $pdo = new PDO(
            $dsn,
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['options'] ?? []
        );
        
        // Store database connection globally
        define('DB_CONNECTION', $pdo);
    }
} catch (PDOException $e) {
    if (APP_CONFIG['app']['debug'] ?? false) {
        throw new Exception("Database connection failed: " . $e->getMessage());
    } else {
        throw new Exception("Database connection failed");
    }
}

// Load helper functions
if (file_exists(__DIR__ . '/helpers.php')) {
    require_once __DIR__ . '/helpers.php';
}