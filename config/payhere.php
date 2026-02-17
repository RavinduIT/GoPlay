<?php

/**
 * PayHere Payment Gateway Configuration
 * 
 * This file contains PayHere payment gateway settings.
 * Update these values with your actual PayHere credentials.
 * 
 * Get your credentials from: https://www.payhere.lk/merchant/
 */

return [
    // Sandbox or Live mode
    'mode' => 'sandbox', // 'sandbox' or 'live'
    
    // Merchant credentials
    'merchant_id' => '1234040', // Default sandbox merchant ID - Replace with your actual merchant ID
    'merchant_secret' => 'MjYxNDAxMTA4MzE2ODgyOTU5MDk0ODU0Njc1NTAyNjYwNzY5NDY0', // Replace with your actual merchant secret
    
    // Currency
    'currency' => 'LKR',
    
    // Notification URLs (PayHere will call these)
    'notify_url' => '/api/payment/payhere/notify',
    'return_url' => '/api/payment/payhere/return',
    'cancel_url' => '/checkout/payment-method',
    
    // PayHere API endpoints
    'sandbox_url' => 'https://sandbox.payhere.lk/pay/checkout',
    'live_url' => 'https://www.payhere.lk/pay/checkout',
    
    // Order prefix (helps identify orders in PayHere dashboard)
    'order_prefix' => 'GOPLAY-',
    
    // Auto redirect after payment (in seconds)
    'auto_redirect_time' => 3,
];
