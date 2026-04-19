<?php

namespace App\Services;

/**
 * PayHere Payment Gateway Service
 *
 * Shared utility for building PayHere payment payloads from any controller.
 */
class PayHereService
{
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/payhere.php';
    }

    /**
     * Build the full PayHere payment payload ready to submit to the gateway.
     *
     * @param string $orderNumber  Unique order/reference number (already stored in DB)
     * @param float  $amount       Payment amount
     * @param string $itemLabel    Short description shown on PayHere checkout
     * @param array  $customer     Keys: first_name, last_name, email, phone, address, city
     * @return array
     */
    public function buildPayload(
        string $orderNumber,
        float  $amount,
        string $itemLabel,
        array  $customer
    ): array {
        $merchantId     = $this->config['merchant_id'];
        $merchantSecret = $this->config['merchant_secret'];
        $currency       = $this->config['currency'];
        $amountFormatted = number_format($amount, 2, '.', '');

        $hash = strtoupper(md5(
            $merchantId .
            $orderNumber .
            $amountFormatted .
            $currency .
            strtoupper(md5($merchantSecret))
        ));

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443)
            ? 'https' : 'http';
        $baseUrl = $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return [
            'merchant_id' => $merchantId,
            'return_url'  => $baseUrl . $this->config['return_url'],
            'cancel_url'  => $baseUrl . $this->config['cancel_url'],
            'notify_url'  => $baseUrl . $this->config['notify_url'],
            'order_id'    => $orderNumber,
            'items'       => $itemLabel,
            'currency'    => $currency,
            'amount'      => $amountFormatted,
            'first_name'  => $customer['first_name'] ?? '',
            'last_name'   => $customer['last_name']  ?? '',
            'email'       => $customer['email']       ?? '',
            'phone'       => $customer['phone']       ?? '',
            'address'     => $customer['address']     ?? '',
            'city'        => $customer['city']        ?? '',
            'country'     => 'Sri Lanka',
            'hash'        => $hash,
            'sandbox'     => $this->config['mode'] === 'sandbox',
        ];
    }

    /**
     * Verify a PayHere MD5 signature.
     */
    public function verifyHash(
        string $merchantId,
        string $orderId,
        string $amount,
        string $currency,
        string $statusCode,
        string $md5sig
    ): bool {
        $local = strtoupper(md5(
            $merchantId . $orderId . $amount . $currency . $statusCode .
            strtoupper(md5($this->config['merchant_secret']))
        ));
        return $local === $md5sig;
    }

    public function isSandbox(): bool
    {
        return $this->config['mode'] === 'sandbox';
    }
}
