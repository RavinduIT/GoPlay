<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'goplay_sports_platform'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],

        'testing' => [
            'driver' => 'mysql',
            'host' => env('DB_TEST_HOST', 'localhost'),
            'port' => env('DB_TEST_PORT', '3306'),
            'database' => env('DB_TEST_DATABASE', 'goplay_test'),
            'username' => env('DB_TEST_USERNAME', 'root'),
            'password' => env('DB_TEST_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */
    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Connection Pool Settings
    |--------------------------------------------------------------------------
    */
    'pool' => [
        'min_connections' => 5,
        'max_connections' => 20,
        'connection_timeout' => 30,
        'idle_timeout' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Logging
    |--------------------------------------------------------------------------
    */
    'log_queries' => env('DB_LOG_QUERIES', false),
    'slow_query_threshold' => 1000, // milliseconds

    /*
    |--------------------------------------------------------------------------
    | Database Backup Settings
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => env('DB_BACKUP_ENABLED', false),
        'schedule' => 'daily',
        'retention_days' => 30,
        'path' => storage_path('backups'),
    ],
];