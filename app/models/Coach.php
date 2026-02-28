<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Coach Model
 * 
 * Handles coach data and operations
 */
class Coach extends BaseModel
{
    protected string $table = 'coaches';
    
    protected array $fillable = [
        'user_id',
        'sport_category_id',
        'experience_years',
        'age',
        'date_of_birth',
        'hourly_rate',
        'bio',
        'specializations',
        'certifications',
        'rating',
        'total_reviews',
        'total_sessions',
        'availability_schedule',
        'location',
        'status'
    ];

    protected array $casts = [
        'experience_years' => 'int',
        'age' => 'int',
        'hourly_rate' => 'float',
        'availability_schedule' => 'array',
        'rating' => 'float',
        'total_reviews' => 'int',
        'total_sessions' => 'int'
    ];

    /**
     * Get available coaches with user and sport category details
     */
    public function getAvailable(): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active' 
                ORDER BY c.rating DESC, c.total_reviews DESC";
        
        $results = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get coaches by sport category
     */
    public function getBySport(string $sportName): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active' AND sc.name = ?
                ORDER BY c.rating DESC, c.total_reviews DESC";
        
        $results = $this->query($sql, [$sportName])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get top rated coaches
     */
    public function getTopRated(int $limit = 10): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active' 
                ORDER BY c.rating DESC, c.total_sessions DESC 
                LIMIT ?";
        
        $results = $this->query($sql, [$limit])->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Search coaches with filters
     */
    public function search(string $query = '', array $filters = []): array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.status = 'active'";
        
        $params = [];
        
        if (!empty($query)) {
            $sql .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR c.bio LIKE ? OR sc.name LIKE ? OR c.specializations LIKE ?)";
            $queryParam = "%{$query}%";
            $params = array_fill(0, 5, $queryParam);
        }
        
        if (isset($filters['sport']) && !empty($filters['sport'])) {
            $sql .= " AND sc.name = ?";
            $params[] = $filters['sport'];
        }
        
        if (isset($filters['experience']) && !empty($filters['experience'])) {
            if ($filters['experience'] === '1-3') {
                $sql .= " AND c.experience_years BETWEEN 1 AND 3";
            } elseif ($filters['experience'] === '3-5') {
                $sql .= " AND c.experience_years BETWEEN 3 AND 5";
            } elseif ($filters['experience'] === '5+') {
                $sql .= " AND c.experience_years >= 5";
            }
        }

        if (isset($filters['age']) && !empty($filters['age'])) {
            if ($filters['age'] === '20-30') {
                $sql .= " AND c.age BETWEEN 20 AND 30";
            } elseif ($filters['age'] === '30-40') {
                $sql .= " AND c.age BETWEEN 30 AND 40";
            } elseif ($filters['age'] === '40-50') {
                $sql .= " AND c.age BETWEEN 40 AND 50";
            } elseif ($filters['age'] === '50+') {
                $sql .= " AND c.age >= 50";
            }
        }

        if (isset($filters['price']) && !empty($filters['price'])) {
            if ($filters['price'] === '0-2000') {
                $sql .= " AND c.hourly_rate BETWEEN 0 AND 2000";
            } elseif ($filters['price'] === '2000-4000') {
                $sql .= " AND c.hourly_rate BETWEEN 2000 AND 4000";
            } elseif ($filters['price'] === '4000+') {
                $sql .= " AND c.hourly_rate >= 4000";
            }
        }
        
        $sql .= " ORDER BY c.rating DESC, c.total_reviews DESC";
        
        $results = $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }

    /**
     * Get coach with full details
     */
    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT 
                    c.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    sc.name as sport_name,
                    sc.icon as sport_icon
                FROM {$this->table} c 
                JOIN users u ON c.user_id = u.id 
                JOIN sports_categories sc ON c.sport_category_id = sc.id
                WHERE c.id = ?";
        
        $result = $this->queryFirst($sql, [$id]);
        return $result ? $this->castAttributes($result) : null;
    }

    /**
     * Get coach bookings
     */
    public function getBookings(int $coachId): array
    {
        $sql = "SELECT cb.*, u.first_name, u.last_name 
                FROM coach_bookings cb 
                JOIN users u ON cb.user_id = u.id 
                WHERE cb.coach_id = ? 
                ORDER BY cb.booking_date DESC, cb.start_time DESC";
        
        $results = $this->query($sql, [$coachId])->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get coach reviews
     */
    public function getReviews(int $coachId): array
    {
        $sql = "SELECT cr.*, u.first_name, u.last_name 
                FROM coach_reviews cr 
                JOIN users u ON cr.user_id = u.id 
                WHERE cr.coach_id = ? 
                ORDER BY cr.created_at DESC";
        
        $results = $this->query($sql, [$coachId])->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    /**
     * Update coach rating based on reviews
     */
    public function updateRating(int $coachId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET rating = (
                    SELECT COALESCE(AVG(rating), 0) 
                    FROM coach_reviews 
                    WHERE coach_id = ?
                ),
                total_reviews = (
                    SELECT COUNT(*) 
                    FROM coach_reviews 
                    WHERE coach_id = ?
                ),
                total_sessions = (
                    SELECT COUNT(*) 
                    FROM coach_bookings 
                    WHERE coach_id = ? AND status = 'completed'
                )
                WHERE id = ?";
        
        $statement = $this->query($sql, [$coachId, $coachId, $coachId, $coachId]);
        return $statement && $statement->rowCount() > 0;
    }

    /**
     * Get all sports categories
     */
    public function getSportsCategories(): array
    {
        $sql = "SELECT * FROM sports_categories WHERE is_active = 1 ORDER BY name";
        $results = $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        return $results;
    }

    // ── Profile management ────────────────────────────────────

    /**
     * Get coach record (with user + sport details) by user_id.
     */
    public function getByUserId(int $userId): ?array
    {
        return $this->queryFirst(
            "SELECT c.*, u.first_name, u.last_name, u.email, u.phone, u.profile_picture,
                    sc.name AS sport_name
             FROM coaches c
             JOIN users u ON c.user_id = u.id
             JOIN sports_categories sc ON c.sport_category_id = sc.id
             WHERE c.user_id = ?",
            [$userId]
        );
    }

    /**
     * Update coach profile fields (coaches + users tables).
     */
    public function updateProfile(int $coachId, int $userId, array $data): bool
    {
        $coachCols = ['bio', 'specializations', 'location', 'hourly_rate', 'experience_years'];
        $cF = $cP = [];
        foreach ($coachCols as $f) {
            if (array_key_exists($f, $data)) { $cF[] = "$f = ?"; $cP[] = $data[$f]; }
        }
        if ($cF) {
            $cP[] = $coachId;
            $this->query('UPDATE coaches SET ' . implode(', ', $cF) . ' WHERE id = ?', $cP);
        }

        $userCols = ['first_name', 'last_name', 'phone', 'email'];
        $uF = $uP = [];
        foreach ($userCols as $f) {
            if (array_key_exists($f, $data)) { $uF[] = "$f = ?"; $uP[] = $data[$f]; }
        }
        if ($uF) {
            $uP[] = $userId;
            $this->query('UPDATE users SET ' . implode(', ', $uF) . ' WHERE id = ?', $uP);
        }

        return true;
    }

    /**
     * Update profile picture path for a user.
     */
    public function updateAvatar(int $userId, string $path): bool
    {
        $stmt = $this->query('UPDATE users SET profile_picture = ? WHERE id = ?', [$path, $userId]);
        return (bool)$stmt;
    }

    // ── Certificates ──────────────────────────────────────────

    public function getCertificates(int $coachId): array
    {
        return $this->query(
            'SELECT * FROM coach_certificates WHERE coach_id = ? ORDER BY issue_date DESC, id DESC',
            [$coachId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function addCertificate(int $coachId, array $d): ?int
    {
        $this->query(
            'INSERT INTO coach_certificates (coach_id, title, issuing_organization, issue_date, expiry_date, credential_id)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $coachId,
                $d['title'],
                $d['issuing_organization'] ?? null,
                ($d['issue_date'] ?? '') ?: null,
                ($d['expiry_date'] ?? '') ?: null,
                $d['credential_id']        ?? null,
            ]
        );
        return $this->db->lastInsertId() ?: null;
    }

    public function updateCertificate(int $id, int $coachId, array $d): bool
    {
        $stmt = $this->query(
            'UPDATE coach_certificates
             SET title=?, issuing_organization=?, issue_date=?, expiry_date=?, credential_id=?
             WHERE id=? AND coach_id=?',
            [
                $d['title'],
                $d['issuing_organization'] ?? null,
                ($d['issue_date']   ?? '') ?: null,
                ($d['expiry_date']  ?? '') ?: null,
                $d['credential_id'] ?? null,
                $id, $coachId,
            ]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    public function deleteCertificate(int $id, int $coachId): bool
    {
        $stmt = $this->query(
            'DELETE FROM coach_certificates WHERE id = ? AND coach_id = ?',
            [$id, $coachId]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    // ── Achievements ──────────────────────────────────────────

    public function getAchievements(int $coachId): array
    {
        return $this->query(
            'SELECT * FROM coach_achievements WHERE coach_id = ? ORDER BY date_achieved DESC, id DESC',
            [$coachId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function addAchievement(int $coachId, array $d): ?int
    {
        $this->query(
            'INSERT INTO coach_achievements (coach_id, title, description, date_achieved, category)
             VALUES (?, ?, ?, ?, ?)',
            [
                $coachId,
                $d['title'],
                $d['description']   ?? null,
                ($d['date_achieved'] ?? '') ?: null,
                $d['category']      ?? 'other',
            ]
        );
        return $this->db->lastInsertId() ?: null;
    }

    public function updateAchievement(int $id, int $coachId, array $d): bool
    {
        $stmt = $this->query(
            'UPDATE coach_achievements
             SET title=?, description=?, date_achieved=?, category=?
             WHERE id=? AND coach_id=?',
            [
                $d['title'],
                $d['description']    ?? null,
                ($d['date_achieved'] ?? '') ?: null,
                $d['category']       ?? 'other',
                $id, $coachId,
            ]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    public function deleteAchievement(int $id, int $coachId): bool
    {
        $stmt = $this->query(
            'DELETE FROM coach_achievements WHERE id = ? AND coach_id = ?',
            [$id, $coachId]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    // ── Clients ───────────────────────────────────────────────

    /**
     * Get all unique clients (users who booked sessions) for a coach, with aggregated stats.
     */
    public function getClients(int $coachId): array
    {
        $sql = "SELECT
                    u.id            AS user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone,
                    u.profile_picture,
                    COUNT(cb.id)                                            AS total_sessions,
                    SUM(CASE WHEN cb.status = 'completed'  THEN 1 ELSE 0 END) AS completed_sessions,
                    SUM(CASE WHEN cb.status IN ('confirmed','pending') AND cb.booking_date >= CURDATE() THEN 1 ELSE 0 END) AS upcoming_sessions,
                    MAX(cb.booking_date)                                    AS last_session_date,
                    MIN(cb.booking_date)                                    AS first_session_date,
                    SUM(CASE WHEN cb.payment_status = 'paid' THEN cb.total_amount ELSE 0 END) AS total_paid,
                    (SELECT cb2.session_type
                     FROM coach_bookings cb2
                     WHERE cb2.coach_id = ? AND cb2.user_id = u.id
                       AND cb2.status != 'cancelled'
                     GROUP BY cb2.session_type
                     ORDER BY COUNT(*) DESC LIMIT 1)                        AS preferred_type
                FROM coach_bookings cb
                JOIN users u ON cb.user_id = u.id
                WHERE cb.coach_id = ?
                  AND cb.status != 'cancelled'
                GROUP BY u.id, u.first_name, u.last_name, u.email, u.phone, u.profile_picture
                ORDER BY last_session_date DESC";

        $results = $this->query($sql, [$coachId, $coachId])->fetchAll(\PDO::FETCH_ASSOC);
        return $results ?: [];
    }

    /**
     * Get complete booking history between a coach and a specific client (user).
     */
    public function getClientBookingHistory(int $coachId, int $userId): array
    {
        $sql = "SELECT cb.id, cb.booking_date, cb.start_time, cb.end_time,
                       cb.session_type, cb.status, cb.payment_status,
                       cb.total_amount, cb.special_requests, cb.coach_notes,
                       cb.created_at
                FROM coach_bookings cb
                WHERE cb.coach_id = ? AND cb.user_id = ?
                ORDER BY cb.booking_date DESC, cb.start_time DESC";

        return $this->query($sql, [$coachId, $userId])->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get overall client stats for a coach.
     */
    public function getClientStats(int $coachId): array
    {
        $sql = "SELECT
                    COUNT(DISTINCT u.id)                                  AS total_clients,
                    COUNT(DISTINCT CASE
                        WHEN cb.booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          AND cb.status != 'cancelled'
                        THEN u.id END)                                    AS active_clients,
                    COUNT(DISTINCT CASE
                        WHEN YEAR(cb.booking_date)  = YEAR(CURDATE())
                         AND MONTH(cb.booking_date) = MONTH(CURDATE())
                         AND cb.status != 'cancelled'
                        THEN u.id END)                                    AS this_month_clients
                FROM coach_bookings cb
                JOIN users u ON cb.user_id = u.id
                WHERE cb.coach_id = ?";

        $row = $this->queryFirst($sql, [$coachId]);
        return $row ?: ['total_clients' => 0, 'active_clients' => 0, 'this_month_clients' => 0];
    }

    // ── Availability ──────────────────────────────────────────

    /**
     * Get weekly schedule from coach_availability, filling all 7 days with defaults.
     */
    public function getWeeklySchedule(int $coachId): array
    {
        $allDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

        $rows = $this->query(
            "SELECT * FROM coach_availability WHERE coach_id = ?
             ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')",
            [$coachId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['day_of_week']] = $row;
        }

        $schedule = [];
        foreach ($allDays as $day) {
            $schedule[] = $indexed[$day] ?? [
                'day_of_week'   => $day,
                'start_time'    => '09:00:00',
                'end_time'      => '18:00:00',
                'slot_duration' => 60,
                'is_available'  => 0,
            ];
        }
        return $schedule;
    }

    /**
     * Upsert weekly schedule rows for a coach.
     * $days: array of ['day_of_week'=>string, 'start_time'=>string,
     *                   'end_time'=>string, 'slot_duration'=>int, 'is_available'=>int]
     */
    public function saveWeeklySchedule(int $coachId, array $days): bool
    {
        foreach ($days as $d) {
            $day = $d['day_of_week'] ?? null;
            if (!$day) continue;

            $existing = $this->queryFirst(
                "SELECT id FROM coach_availability WHERE coach_id = ? AND day_of_week = ?",
                [$coachId, $day]
            );

            $start    = $d['start_time']    ?? '09:00:00';
            $end      = $d['end_time']      ?? '18:00:00';
            $duration = (int)($d['slot_duration'] ?? 60);
            $avail    = (int)($d['is_available']  ?? 0);

            if ($existing) {
                $this->query(
                    "UPDATE coach_availability
                     SET start_time=?, end_time=?, slot_duration=?, is_available=?, updated_at=NOW()
                     WHERE id=?",
                    [$start, $end, $duration, $avail, $existing['id']]
                );
            } else {
                $this->query(
                    "INSERT INTO coach_availability
                     (coach_id, day_of_week, start_time, end_time, slot_duration, is_available)
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [$coachId, $day, $start, $end, $duration, $avail]
                );
            }
        }
        return true;
    }

    /**
     * Get upcoming confirmed/pending sessions for a coach.
     */
    public function getUpcomingSessions(int $coachId, int $limit = 15): array
    {
        return $this->query(
            "SELECT cb.id, cb.booking_date, cb.start_time, cb.end_time,
                    cb.session_type, cb.status, cb.duration, cb.total_amount,
                    u.first_name, u.last_name
             FROM coach_bookings cb
             JOIN users u ON cb.user_id = u.id
             WHERE cb.coach_id = ?
               AND cb.booking_date >= CURDATE()
               AND cb.status IN ('confirmed','pending')
             ORDER BY cb.booking_date ASC, cb.start_time ASC
             LIMIT ?",
            [$coachId, $limit]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get bookings grouped by date for a calendar month view.
     */
    public function getMonthBookings(int $coachId, int $year, int $month): array
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-t', strtotime($start));

        return $this->query(
            "SELECT booking_date,
                    COUNT(*) AS session_count,
                    SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
                    SUM(CASE WHEN status='pending'   THEN 1 ELSE 0 END) AS pending_count
             FROM coach_bookings
             WHERE coach_id = ?
               AND booking_date BETWEEN ? AND ?
               AND status IN ('confirmed','pending','completed')
             GROUP BY booking_date
             ORDER BY booking_date",
            [$coachId, $start, $end]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}