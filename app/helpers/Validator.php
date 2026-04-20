<?php

namespace App\Helpers;

/**
 * Centralized input validation utility for the GoPlay platform.
 * 
 * Provides static methods for validating common field types across
 * all controllers (User, Coach, GroundOwner, ShopOwner, Admin, etc.)
 */
class Validator
{
    /**
     * Validate a Sri Lankan phone number.
     * Accepts formats: 0771234567, +94771234567, 077 123 4567, etc.
     */
    public static function phone(?string $val): bool
    {
        if (empty($val)) return true; // Optional field
        return (bool) preg_match('/^[0-9+\s\-()]{7,15}$/', trim($val));
    }

    /**
     * Validate a positive numeric value (for prices, rates, amounts).
     */
    public static function price($val): bool
    {
        if ($val === null || $val === '') return true;
        return is_numeric($val) && (float) $val >= 0;
    }

    /**
     * Validate an email address.
     */
    public static function email(?string $val): bool
    {
        if (empty($val)) return true;
        return (bool) filter_var($val, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate a postal code (5-digit format for Sri Lanka).
     */
    public static function postalCode(?string $val): bool
    {
        if (empty($val)) return true;
        return (bool) preg_match('/^\d{4,6}$/', trim($val));
    }

    /**
     * Validate a year value (1900–current year).
     */
    public static function year($val): bool
    {
        if ($val === null || $val === '') return true;
        $y = (int) $val;
        return is_numeric($val) && $y >= 1900 && $y <= (int) date('Y');
    }

    /**
     * Validate string length.
     */
    public static function maxLength(?string $val, int $max): bool
    {
        if (empty($val)) return true;
        return strlen($val) <= $max;
    }

    /**
     * Validate a positive integer.
     */
    public static function positiveInt($val): bool
    {
        if ($val === null || $val === '') return true;
        return is_numeric($val) && (int) $val >= 0 && floor((float) $val) == (float) $val;
    }

    /**
     * Validate a URL.
     */
    public static function url(?string $val): bool
    {
        if (empty($val)) return true;
        // Block javascript: URLs (XSS)
        if (preg_match('/^\s*javascript\s*:/i', $val)) return false;
        return (bool) filter_var($val, FILTER_VALIDATE_URL);
    }

    /**
     * Validate a date in Y-m-d format.
     */
    public static function date(?string $val): bool
    {
        if (empty($val)) return true;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return false;
        $parts = explode('-', $val);
        return checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0]);
    }

    /**
     * Validate a bank account number (digits only, 5-20 chars).
     */
    public static function bankAccount(?string $val): bool
    {
        if (empty($val)) return true;
        return (bool) preg_match('/^\d{5,20}$/', trim($val));
    }

    /**
     * Validate required string field — not empty after trimming.
     */
    public static function required($val): bool
    {
        if (is_string($val)) return trim($val) !== '';
        return $val !== null && $val !== '';
    }

    /**
     * Run multiple validations and return the first error or null.
     * 
     * Usage:
     *   $error = Validator::check([
     *       ['phone', $phone, 'Invalid phone number'],
     *       ['email', $email, 'Invalid email address'],
     *       ['price', $amount, 'Invalid amount'],
     *   ]);
     *   if ($error) return json error response...
     */
    public static function check(array $rules): ?string
    {
        foreach ($rules as $rule) {
            [$method, $value] = $rule;
            $message = $rule[2] ?? "Invalid value for $method";
            $extra = $rule[3] ?? null;

            $valid = $extra !== null
                ? self::$method($value, $extra)
                : self::$method($value);

            if (!$valid) {
                return $message;
            }
        }
        return null;
    }
}
