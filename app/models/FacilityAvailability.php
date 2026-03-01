<?php

namespace App\Models;

use Core\BaseModel;

/**
 * FacilityAvailability Model
 *
 * Manages two kinds of availability data:
 *   1. Weekly recurring schedule  (facility_availability table)
 *   2. Specific date blocks        (facility_blocked_dates table)
 */
class FacilityAvailability extends BaseModel
{
    protected string $table = 'facility_availability';

    // ── Weekly recurring schedule ─────────────────────────────

    /**
     * Get the 7-day schedule for a facility, filling missing days with defaults.
     */
    public function getWeeklySchedule(int $facilityId): array
    {
        $allDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

        $rows = $this->query(
            "SELECT * FROM facility_availability WHERE facility_id = ?
             ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')",
            [$facilityId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['day_of_week']] = $row;
        }

        $schedule = [];
        foreach ($allDays as $day) {
            $schedule[] = $indexed[$day] ?? [
                'facility_id'   => $facilityId,
                'day_of_week'   => $day,
                'opening_time'  => '08:00:00',
                'closing_time'  => '22:00:00',
                'slot_duration' => 60,
                'buffer_time'   => 0,
                'is_available'  => 0,
            ];
        }

        return $schedule;
    }

    /**
     * Upsert the weekly schedule for a facility.
     * Each element: ['day_of_week', 'opening_time', 'closing_time',
     *                'slot_duration', 'buffer_time', 'is_available']
     */
    public function saveWeeklySchedule(int $facilityId, array $days): bool
    {
        foreach ($days as $d) {
            $day = $d['day_of_week'] ?? null;
            if (!$day) continue;

            $existing = $this->queryFirst(
                "SELECT id FROM facility_availability WHERE facility_id = ? AND day_of_week = ?",
                [$facilityId, $day]
            );

            $open   = $d['opening_time']  ?? '08:00:00';
            $close  = $d['closing_time']  ?? '22:00:00';
            $slot   = (int)($d['slot_duration'] ?? 60);
            $buf    = (int)($d['buffer_time']   ?? 0);
            $avail  = (int)($d['is_available']  ?? 0);

            if ($existing) {
                $this->query(
                    "UPDATE facility_availability
                     SET opening_time=?, closing_time=?, slot_duration=?, buffer_time=?, is_available=?, updated_at=NOW()
                     WHERE id=?",
                    [$open, $close, $slot, $buf, $avail, $existing['id']]
                );
            } else {
                $this->query(
                    "INSERT INTO facility_availability
                     (facility_id, day_of_week, opening_time, closing_time, slot_duration, buffer_time, is_available)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$facilityId, $day, $open, $close, $slot, $buf, $avail]
                );
            }
        }
        return true;
    }

    /**
     * Check whether a specific date + time range falls within operating hours.
     * Returns true if the ground is open and the time fits within opening hours.
     */
    public function isWithinOperatingHours(int $facilityId, string $date, string $startTime, string $endTime): bool
    {
        // Map PHP date('w') (0=Sun) to day_of_week string
        $dowMap = [0=>'Sunday',1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'];
        $dow    = $dowMap[(int)date('w', strtotime($date))] ?? null;
        if (!$dow) return true; // Can't determine day — allow

        $row = $this->queryFirst(
            "SELECT opening_time, closing_time, is_available
             FROM facility_availability
             WHERE facility_id = ? AND day_of_week = ?",
            [$facilityId, $dow]
        );

        if (!$row) return true;  // No schedule set — allow by default
        if (!(int)$row['is_available']) return false; // Day marked closed

        // Compare times as strings (HH:MM:SS format is lexicographically comparable)
        $open  = substr($row['opening_time'],  0, 5); // HH:MM
        $close = substr($row['closing_time'],  0, 5);
        $start = substr($startTime, 0, 5);
        $end   = substr($endTime,   0, 5);

        return ($start >= $open && $end <= $close);
    }

    // ── Blocked dates ─────────────────────────────────────────

    /**
     * Get blocked date ranges for a facility within a date range.
     */
    public function getBlockedDates(int $facilityId, string $startDate, string $endDate): array
    {
        return $this->query(
            "SELECT fbd.*, u.first_name, u.last_name
             FROM facility_blocked_dates fbd
             JOIN users u ON u.id = fbd.created_by
             WHERE fbd.facility_id = ?
               AND fbd.start_date <= ?
               AND fbd.end_date   >= ?
             ORDER BY fbd.start_date ASC, fbd.start_time ASC",
            [$facilityId, $endDate, $startDate]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all blocked dates for a facility (future ones first, then past).
     */
    public function getAllBlockedDates(int $facilityId): array
    {
        return $this->query(
            "SELECT fbd.*, u.first_name, u.last_name
             FROM facility_blocked_dates fbd
             JOIN users u ON u.id = fbd.created_by
             WHERE fbd.facility_id = ?
             ORDER BY fbd.start_date DESC",
            [$facilityId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Create a new date block. Returns the new row ID.
     */
    public function addBlockedDate(int $facilityId, int $createdBy, array $data): int
    {
        $stmt = $this->query(
            "INSERT INTO facility_blocked_dates
             (facility_id, start_date, end_date, start_time, end_time, reason, notes, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $facilityId,
                $data['start_date'],
                $data['end_date'],
                $data['start_time'] ?: null,
                $data['end_time']   ?: null,
                $data['reason']     ?? 'other',
                $data['notes']      ?? null,
                $createdBy,
            ]
        );

        return (int)$this->db->lastInsertId();
    }

    /**
     * Remove a blocked date. Verifies the block belongs to a facility owned by $ownerId.
     */
    public function removeBlockedDate(int $blockId, int $ownerId): bool
    {
        // Verify ownership via JOIN
        $row = $this->queryFirst(
            "SELECT fbd.id FROM facility_blocked_dates fbd
             JOIN sports_facilities sf ON sf.id = fbd.facility_id
             WHERE fbd.id = ? AND sf.owner_id = ?",
            [$blockId, $ownerId]
        );

        if (!$row) return false;

        $this->query("DELETE FROM facility_blocked_dates WHERE id = ?", [$blockId]);
        return true;
    }

    /**
     * Check if a specific date (+ optional time range) is blocked for a facility.
     * All-day blocks have NULL start_time/end_time.
     *
     * A booking is blocked if:
     *  - There is an all-day block covering that date, OR
     *  - There is a time-specific block that overlaps with [startTime, endTime]
     */
    public function isDateBlocked(int $facilityId, string $date, ?string $startTime = null, ?string $endTime = null): bool
    {
        // All-day blocks
        $allDay = $this->queryFirst(
            "SELECT id FROM facility_blocked_dates
             WHERE facility_id = ?
               AND ? BETWEEN start_date AND end_date
               AND start_time IS NULL",
            [$facilityId, $date]
        );

        if ($allDay) return true;

        // Time-specific blocks (only if booking times provided)
        if ($startTime && $endTime) {
            $timeBlock = $this->queryFirst(
                "SELECT id FROM facility_blocked_dates
                 WHERE facility_id = ?
                   AND ? BETWEEN start_date AND end_date
                   AND start_time IS NOT NULL
                   AND start_time < ?
                   AND end_time   > ?",
                [$facilityId, $date, $endTime, $startTime]
            );

            if ($timeBlock) return true;
        }

        return false;
    }

    /**
     * Get blocked dates for multiple facilities (used in schedule API).
     * Returns keyed by facility_id.
     */
    public function getBlockedDatesForFacilities(array $facilityIds, string $startDate, string $endDate): array
    {
        if (empty($facilityIds)) return [];

        $placeholders = implode(',', array_fill(0, count($facilityIds), '?'));

        $rows = $this->query(
            "SELECT * FROM facility_blocked_dates
             WHERE facility_id IN ($placeholders)
               AND start_date <= ?
               AND end_date   >= ?
             ORDER BY start_date ASC",
            array_merge($facilityIds, [$endDate, $startDate])
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return $rows;
    }
}
