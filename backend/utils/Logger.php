<?php
/**
 * Logger Utility Class
 * Handles application logging
 */

class Logger {
    
    const LOG_LEVELS = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 4
    ];
    
    private static $logDir = null;
    
    /**
     * Initialize logger
     */
    private static function init() {
        if (self::$logDir === null) {
            self::$logDir = __DIR__ . '/../logs/';
            if (!is_dir(self::$logDir)) {
                mkdir(self::$logDir, 0755, true);
            }
        }
    }
    
    /**
     * Write log entry
     */
    private static function writeLog($level, $message, $context = [], $filename = 'api.log') {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        
        $logEntry = sprintf(
            "[%s] %s: %s %s\n",
            $timestamp,
            $level,
            $message,
            $contextStr
        );
        
        file_put_contents(
            self::$logDir . $filename,
            $logEntry,
            FILE_APPEND | LOCK_EX
        );
    }
    
    /**
     * Log debug message
     */
    public static function debug($message, $context = []) {
        self::writeLog('DEBUG', $message, $context);
    }
    
    /**
     * Log info message
     */
    public static function info($message, $context = []) {
        self::writeLog('INFO', $message, $context);
    }
    
    /**
     * Log warning message
     */
    public static function warning($message, $context = []) {
        self::writeLog('WARNING', $message, $context);
    }
    
    /**
     * Log error message
     */
    public static function error($message, $context = []) {
        self::writeLog('ERROR', $message, $context, 'error.log');
    }
    
    /**
     * Log critical message
     */
    public static function critical($message, $context = []) {
        self::writeLog('CRITICAL', $message, $context, 'error.log');
    }
    
    /**
     * Log authentication events
     */
    public static function auth($message, $context = []) {
        self::writeLog('AUTH', $message, $context, 'auth.log');
    }
    
    /**
     * Log API requests
     */
    public static function apiRequest($method, $uri, $data = []) {
        $message = sprintf('%s %s', $method, $uri);
        self::writeLog('API_REQUEST', $message, $data);
    }
    
    /**
     * Log API responses
     */
    public static function apiResponse($statusCode, $responseTime = null) {
        $context = ['status_code' => $statusCode];
        if ($responseTime !== null) {
            $context['response_time'] = $responseTime . 'ms';
        }
        
        self::writeLog('API_RESPONSE', 'Response sent', $context);
    }
}