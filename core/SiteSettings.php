<?php

namespace Core;

/**
 * SiteSettings - Global settings accessor
 * 
 * Loads all settings from the DB once per request and provides
 * a static interface so every controller, view, and service can
 * read admin-configured values instead of hardcoded defaults.
 */
class SiteSettings
{
    private static ?array $cache = null;

    /**
     * Load all settings from the database into a static cache.
     * Call this once during application bootstrap.
     */
    public static function load(): void
    {
        if (self::$cache !== null) {
            return; // Already loaded
        }

        try {
            $db = Database::getInstance();
            $rows = $db->query("SELECT key_name, value FROM settings")->fetchAll();
            self::$cache = [];
            foreach ($rows as $row) {
                self::$cache[$row['key_name']] = $row['value'];
            }
        } catch (\Exception $e) {
            error_log("SiteSettings::load() failed: " . $e->getMessage());
            self::$cache = [];
        }
    }

    /**
     * Get a setting value by key, with an optional default.
     */
    public static function get(string $key, string $default = ''): string
    {
        if (self::$cache === null) {
            self::load();
        }
        return self::$cache[$key] ?? $default;
    }

    /**
     * Get a boolean setting (stored as '1'/'0' or 'true'/'false').
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $val = self::get($key, $default ? '1' : '0');
        return $val === '1' || $val === 'true';
    }

    /**
     * Get a numeric setting.
     */
    public static function getInt(string $key, int $default = 0): int
    {
        $val = self::get($key, '');
        return $val !== '' ? (int)$val : $default;
    }

    /**
     * Get a float setting.
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        $val = self::get($key, '');
        return $val !== '' ? (float)$val : $default;
    }

    /**
     * Flush the cache (e.g. after admin saves settings).
     */
    public static function flush(): void
    {
        self::$cache = null;
    }
}
