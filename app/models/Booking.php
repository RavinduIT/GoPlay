<?php

namespace App\Models;

/**
 * Booking Model
 * 
 * Handles booking data for facilities and coaches
 */
class Booking extends BaseModel
{
    protected string $table = 'bookings';
    
    protected array $fillable = [
        'user_id',
        'type',
        'facility_id',
        'coach_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration',
        'participants',
        'total_amount',
        'payment_status',
        'booking_status',
        'special_requirements',
        'notes'
    ];

    protected array $casts = [
        'booking_date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'participants' => 'int',
        'total_amount' => 'float'
    ];

    /**
     * Get bookings by user
     */
    public function getByUser(int $userId): array
    {
        return $this->where(['user_id' => $userId]);
    }

    /**
     * Get bookings by facility
     */
    public function getByFacility(int $facilityId): array
    {
        return $this->where(['facility_id' => $facilityId]);
    }

    /**
     * Get bookings by coach
     */
    public function getByCoach(int $coachId): array
    {
        return $this->where(['coach_id' => $coachId]);
    }

    /**
     * Get bookings by date
     */
    public function getByDate(string $date): array
    {
        return $this->where(['booking_date' => $date]);
    }

    /**
     * Check availability
     */
    public function isAvailable(int $resourceId, string $type, string $date, string $startTime, string $endTime): bool
    {
        $column = $type === 'facility' ? 'facility_id' : 'coach_id';
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE {$column} = ? 
                AND booking_date = ? 
                AND booking_status != 'cancelled'
                AND (
                    (start_time <= ? AND end_time > ?) OR
                    (start_time < ? AND end_time >= ?) OR
                    (start_time >= ? AND end_time <= ?)
                )";
        
        $result = $this->queryFirst($sql, [
            $resourceId, $date, $startTime, $startTime, 
            $endTime, $endTime, $startTime, $endTime
        ]);
        
        return ($result['count'] ?? 0) == 0;
    }

    /**
     * Cancel booking
     */
    public function cancel(int $id, string $reason = ''): bool
    {
        return $this->update($id, [
            'booking_status' => 'cancelled',
            'notes' => $reason
        ]);
    }
}