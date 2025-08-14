<?php
/**
 * JWT (JSON Web Token) Utility Class
 * Simple JWT implementation for authentication
 */

class JWT {
    
    private static $secret = 'your-super-secret-key-change-this-in-production';
    private static $algorithm = 'HS256';
    
    /**
     * Encode payload to JWT
     */
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);
        $payload = json_encode($payload);
        
        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    /**
     * Decode JWT to payload
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Invalid token format');
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Verify signature
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secret, true);
        $expectedSignature = self::base64UrlEncode($signature);
        
        if (!hash_equals($expectedSignature, $signatureEncoded)) {
            throw new Exception('Invalid token signature');
        }
        
        // Decode payload
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        if (!$payload) {
            throw new Exception('Invalid token payload');
        }
        
        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token has expired');
        }
        
        return $payload;
    }
    
    /**
     * Base64 URL encode
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL decode
     */
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    /**
     * Set custom secret key
     */
    public static function setSecret($secret) {
        self::$secret = $secret;
    }
    
    /**
     * Generate a secure secret key
     */
    public static function generateSecret($length = 64) {
        return bin2hex(random_bytes($length));
    }
}