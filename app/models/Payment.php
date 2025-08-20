<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Payment Model
 * 
 * Handles payment data and transactions
 */
class Payment extends BaseModel
{
    protected string $table = 'payments';
    
    protected array $fillable = [
        'user_id',
        'booking_id',
        'order_id',
        'amount',
        'currency',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'gateway_response',
        'status',
        'processed_at',
        'notes'
    ];

    protected array $casts = [
        'amount' => 'float',
        'gateway_response' => 'array',
        'processed_at' => 'datetime'
    ];

    /**
     * Get payments by user
     */
    public function getByUser(int $userId): array
    {
        return $this->where(['user_id' => $userId]);
    }

    /**
     * Get payments by status
     */
    public function getByStatus(string $status): array
    {
        return $this->where(['status' => $status]);
    }

    /**
     * Get successful payments
     */
    public function getSuccessful(): array
    {
        return $this->getByStatus('completed');
    }

    /**
     * Get pending payments
     */
    public function getPending(): array
    {
        return $this->getByStatus('pending');
    }

    /**
     * Get failed payments
     */
    public function getFailed(): array
    {
        return $this->getByStatus('failed');
    }

    /**
     * Process payment
     */
    public function processPayment(int $id, array $gatewayResponse): bool
    {
        $status = $gatewayResponse['success'] ?? false ? 'completed' : 'failed';
        
        return $this->update($id, [
            'status' => $status,
            'gateway_response' => $gatewayResponse,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Refund payment
     */
    public function refund(int $id, string $reason = ''): bool
    {
        return $this->update($id, [
            'status' => 'refunded',
            'notes' => $reason
        ]);
    }

    /**
     * Get payment statistics
     */
    public function getStatistics(): array
    {
        $stats = [];
        
        // Total revenue
        $result = $this->queryFirst("SELECT SUM(amount) as total FROM {$this->table} WHERE status = 'completed'");
        $stats['total_revenue'] = $result['total'] ?? 0;
        
        // Monthly revenue
        $result = $this->queryFirst("SELECT SUM(amount) as total FROM {$this->table} WHERE status = 'completed' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
        $stats['monthly_revenue'] = $result['total'] ?? 0;
        
        // Payment counts by status
        $result = $this->query("SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status");
        foreach ($result as $row) {
            $stats['count_' . $row['status']] = $row['count'];
        }
        
        return $stats;
    }
}