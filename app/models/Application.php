<?php

namespace App\Models;

/**
 * Application Model
 * 
 * Handles application/booking applications data
 */
class Application extends BaseModel
{
    protected string $table = 'applications';
    
    protected array $fillable = [
        'user_id',
        'type',
        'facility_id',
        'coach_id',
        'start_date',
        'end_date',
        'duration',
        'participants',
        'requirements',
        'status',
        'approved_by',
        'approved_at',
        'notes'
    ];

    protected array $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'participants' => 'int'
    ];

    /**
     * Get applications by status
     */
    public function getByStatus(string $status): array
    {
        return $this->where(['status' => $status]);
    }

    /**
     * Get pending applications
     */
    public function getPending(): array
    {
        return $this->getByStatus('pending');
    }

    /**
     * Get approved applications
     */
    public function getApproved(): array
    {
        return $this->getByStatus('approved');
    }

    /**
     * Approve application
     */
    public function approve(int $id, int $approvedBy): bool
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject application
     */
    public function reject(int $id, string $reason = ''): bool
    {
        return $this->update($id, [
            'status' => 'rejected',
            'notes' => $reason
        ]);
    }
}