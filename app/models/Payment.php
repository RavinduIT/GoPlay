<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Payment Model
 * 
 * Handles payment data and transactions for orders
 */
class Payment extends BaseModel
{
    protected string $table = 'payments';
    
    protected array $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'processed_at'
    ];

    protected array $casts = [
        'order_id' => 'int',
        'amount' => 'float',
        'gateway_response' => 'array'
    ];

    /**
     * Create payment record
     */
    public function createPayment(array $paymentData): ?int
    {
        // Set default currency if not provided
        if (!isset($paymentData['currency'])) {
            $paymentData['currency'] = 'LKR';
        }

        return $this->create($paymentData);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(int $paymentId, string $status, ?array $gatewayResponse = null): bool
    {
        $data = [
            'status' => $status,
            'processed_at' => date('Y-m-d H:i:s')
        ];

        if ($gatewayResponse) {
            $data['gateway_response'] = json_encode($gatewayResponse);
        }

        return $this->update($paymentId, $data);
    }

    /**
     * Find payment by order ID
     */
    public function findByOrderId(int $orderId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ?";
        return $this->queryFirst($sql, [$orderId]);
    }

    /**
     * Find payment by transaction ID
     */
    public function findByTransactionId(string $transactionId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE transaction_id = ?";
        return $this->queryFirst($sql, [$transactionId]);
    }

    /**
     * Process card payment using payment gateway
     * This is a simplified version - integrate with actual payment gateway
     */
    public function processCardPayment(array $paymentData): array
    {
        try {
            // In production, use actual payment gateway SDK (Stripe, PayPal, etc.)
            // Example with Stripe:
            // \Stripe\Stripe::setApiKey('your_secret_key');
            // $intent = \Stripe\PaymentIntent::create([...]);
            
            // For testing, simulate payment processing
            // In real implementation, call the payment gateway API here
            
            // Simulate success (90% success rate for testing)
            $isSuccess = mt_rand(1, 10) <= 9;
            
            if ($isSuccess) {
                return [
                    'success' => true,
                    'transaction_id' => 'TXN-' . time() . '-' . mt_rand(1000, 9999),
                    'status' => 'completed',
                    'message' => 'Payment processed successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'status' => 'failed',
                    'message' => 'Payment declined by bank'
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get payments by user
     */
    public function getPaymentsByUser(int $userId): array
    {
        $sql = "SELECT p.*, o.order_number 
                FROM {$this->table} p
                JOIN orders o ON p.order_id = o.id
                WHERE o.user_id = ?
                ORDER BY p.created_at DESC";
        
        return $this->query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get payments by status
     */
    public function getByStatus(string $status): array
    {
        return $this->where(['status' => $status]);
    }

    /**
     * Verify payment webhook signature (for security)
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
