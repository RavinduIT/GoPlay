<?php
/**
 * GoPlay Sports Platform Autoloader
 * Automatically loads classes based on file structure
 */

spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/';
    
    // Map class types to directories
    $classMap = [
        'Controller' => 'controllers/',
        'Model' => 'models/',
        'Service' => 'services/',
        'Middleware' => 'middleware/',
        'Util' => 'utils/',
        'Exception' => 'exceptions/',
        'Validator' => 'validators/'
    ];
    
    // Try to find the class in appropriate directory
    foreach ($classMap as $suffix => $directory) {
        if (strpos($className, $suffix) !== false) {
            $file = $baseDir . $directory . $className . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
    
    // Try utils directory for common classes
    $utilClasses = ['Response', 'Logger', 'JWT', 'Hash', 'Validator', 'DatabaseHelper'];
    if (in_array($className, $utilClasses)) {
        $file = $baseDir . 'utils/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Try models directory for model classes
    $file = $baseDir . 'models/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    
    // Try controllers directory
    $file = $baseDir . 'controllers/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    
    // Try services directory
    $file = $baseDir . 'services/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

// Include essential files
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/utils/Logger.php';
require_once __DIR__ . '/utils/DatabaseHelper.php';