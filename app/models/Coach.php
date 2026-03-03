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

    // ── Reviews ───────────────────────────────────────────────

    /**
     * Aggregate stats: avg rating, total, per-star counts, this-month count.
     */
    public function getReviewStats(int $coachId): array
    {
        $row = $this->queryFirst(
            "SELECT
                COUNT(*)                                                      AS total,
                COALESCE(AVG(rating), 0)                                      AS avg_rating,
                SUM(CASE WHEN rating=5 THEN 1 ELSE 0 END)                    AS r5,
                SUM(CASE WHEN rating=4 THEN 1 ELSE 0 END)                    AS r4,
                SUM(CASE WHEN rating=3 THEN 1 ELSE 0 END)                    AS r3,
                SUM(CASE WHEN rating=2 THEN 1 ELSE 0 END)                    AS r2,
                SUM(CASE WHEN rating=1 THEN 1 ELSE 0 END)                    AS r1,
                SUM(CASE WHEN MONTH(created_at)=MONTH(NOW())
                           AND YEAR(created_at)=YEAR(NOW()) THEN 1 ELSE 0 END) AS this_month,
                SUM(CASE WHEN MONTH(created_at)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))
                           AND YEAR(created_at)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH)) THEN 1 ELSE 0 END)
                                                                              AS last_month
             FROM coach_reviews
             WHERE coach_id = ?",
            [$coachId]
        );
        return $row ?: [
            'total'=>0,'avg_rating'=>0,'r5'=>0,'r4'=>0,'r3'=>0,'r2'=>0,'r1'=>0,
            'this_month'=>0,'last_month'=>0,
        ];
    }

    /**
     * Paginated, filterable list of reviews with user info + coach reply.
     */
    public function getReviewsList(int $coachId, array $filters, int $page, int $limit): array
    {
        $where  = ['cr.coach_id = ?'];
        $params = [$coachId];

        if (!empty($filters['rating'])) {
            $where[]  = 'cr.rating = ?';
            $params[] = (int)$filters['rating'];
        }
        if (isset($filters['no_reply']) && $filters['no_reply']) {
            $where[] = 'crr.id IS NULL';
        }
        if (!empty($filters['search'])) {
            $where[]  = '(u.first_name LIKE ? OR u.last_name LIKE ? OR cr.review_text LIKE ?)';
            $s        = '%' . $filters['search'] . '%';
            $params   = array_merge($params, [$s, $s, $s]);
        }

        $whereSQL = implode(' AND ', $where);
        $offset   = ($page - 1) * $limit;

        $sort = match($filters['sort'] ?? 'newest') {
            'oldest'     => 'cr.created_at ASC',
            'highest'    => 'cr.rating DESC',
            'lowest'     => 'cr.rating ASC',
            default      => 'cr.created_at DESC',
        };

        $countRow = $this->queryFirst(
            "SELECT COUNT(*) AS n
             FROM coach_reviews cr
             JOIN users u ON cr.user_id = u.id
             LEFT JOIN coach_review_replies crr ON crr.review_id = cr.id AND crr.coach_id = ?
             WHERE $whereSQL",
            array_merge([$coachId], $params)
        );
        $total = (int)($countRow['n'] ?? 0);

        $rows = $this->query(
            "SELECT cr.id, cr.rating, cr.review_text, cr.created_at, cr.booking_id,
                    u.id AS user_id, u.first_name, u.last_name, u.profile_picture,
                    crr.id AS reply_id, crr.reply_text, crr.created_at AS reply_at, crr.updated_at AS reply_updated_at,
                    cb.session_type
             FROM coach_reviews cr
             JOIN users u ON cr.user_id = u.id
             LEFT JOIN coach_review_replies crr ON crr.review_id = cr.id AND crr.coach_id = ?
             LEFT JOIN coach_bookings cb ON cb.id = cr.booking_id
             WHERE $whereSQL
             ORDER BY $sort
             LIMIT ? OFFSET ?",
            array_merge([$coachId], $params, [$limit, $offset])
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return [
            'data'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $total > 0 ? (int)ceil($total / $limit) : 1,
        ];
    }

    /**
     * Insert or update a coach reply for a review.
     */
    public function replyToReview(int $reviewId, int $coachId, string $replyText): bool
    {
        $existing = $this->queryFirst(
            'SELECT id FROM coach_review_replies WHERE review_id = ? AND coach_id = ?',
            [$reviewId, $coachId]
        );
        if ($existing) {
            $this->query(
                'UPDATE coach_review_replies SET reply_text = ?, updated_at = NOW() WHERE id = ?',
                [$replyText, $existing['id']]
            );
        } else {
            $this->query(
                'INSERT INTO coach_review_replies (review_id, coach_id, reply_text) VALUES (?, ?, ?)',
                [$reviewId, $coachId, $replyText]
            );
        }
        return true;
    }

    /**
     * Delete a coach reply.
     */
    public function deleteReviewReply(int $reviewId, int $coachId): bool
    {
        $stmt = $this->query(
            'DELETE FROM coach_review_replies WHERE review_id = ? AND coach_id = ?',
            [$reviewId, $coachId]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Get all reviews for CSV export.
     */
    public function getAllReviewsForExport(int $coachId): array
    {
        return $this->query(
            "SELECT cr.id, cr.rating, cr.review_text, cr.created_at,
                    u.first_name, u.last_name, u.email,
                    crr.reply_text, crr.created_at AS reply_at,
                    cb.session_type
             FROM coach_reviews cr
             JOIN users u ON cr.user_id = u.id
             LEFT JOIN coach_review_replies crr ON crr.review_id = cr.id AND crr.coach_id = ?
             LEFT JOIN coach_bookings cb ON cb.id = cr.booking_id
             WHERE cr.coach_id = ?
             ORDER BY cr.created_at DESC",
            [$coachId, $coachId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
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

    // =====================================================================
    // COACH ↔ FACILITY ASSOCIATION
    // =====================================================================

    /**
     * Get approved facilities linked to a coach (public-facing).
     */
    public function getFacilities(int $coachId): array
    {
        return $this->query(
            "SELECT cf.id, cf.facility_id, cf.is_primary, cf.status AS link_status, cf.created_at,
                    sf.name, sf.address, sf.city, sf.hourly_rate, sf.status AS facility_status,
                    sc.name AS category_name, sc.icon AS category_icon
             FROM coach_facilities cf
             JOIN sports_facilities sf ON cf.facility_id = sf.id
             LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
             WHERE cf.coach_id = ?
               AND cf.status = 'approved'
               AND sf.status = 'active'
             ORDER BY cf.is_primary DESC, cf.created_at ASC",
            [$coachId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all facilities linked to a coach (all statuses — for coach's own dashboard).
     */
    public function getCoachFacilitiesWithStatus(int $coachId): array
    {
        return $this->query(
            "SELECT cf.id, cf.facility_id, cf.is_primary, cf.status AS link_status, cf.created_at,
                    sf.name, sf.address, sf.city, sf.hourly_rate,
                    sc.name AS category_name, sc.icon AS category_icon
             FROM coach_facilities cf
             JOIN sports_facilities sf ON cf.facility_id = sf.id
             LEFT JOIN sports_categories sc ON sf.sport_category_id = sc.id
             WHERE cf.coach_id = ?
               AND sf.status = 'active'
             ORDER BY cf.is_primary DESC, cf.created_at ASC",
            [$coachId]
        )->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all coaches linked to any facility owned by $ownerId,
     * optionally filtered by status.
     */
    public function getCoachesByOwner(int $ownerId, ?string $status = null): array
    {
        $sql = "SELECT cf.id AS link_id, cf.coach_id, cf.facility_id, cf.is_primary,
                       cf.status AS link_status, cf.created_at,
                       c.hourly_rate, c.rating, c.experience_years,
                       u.first_name, u.last_name, u.email, u.profile_picture,
                       sc.name AS sport_name, sc.icon AS sport_icon,
                       sf.name AS facility_name
                FROM coach_facilities cf
                JOIN coaches c          ON cf.coach_id    = c.id
                JOIN users u            ON c.user_id      = u.id
                LEFT JOIN sports_categories sc ON c.sport_category_id = sc.id
                JOIN sports_facilities sf  ON cf.facility_id = sf.id
                WHERE sf.owner_id = ?";
        $params = [$ownerId];

        if ($status !== null) {
            $sql .= " AND cf.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY cf.created_at DESC";
        return $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Approve a coach-facility link. Returns true on success.
     */
    public function approveLink(int $linkId, int $approvedByUserId): bool
    {
        $stmt = $this->query(
            "UPDATE coach_facilities
             SET status = 'approved', approved_by = ?, approved_at = NOW()
             WHERE id = ?",
            [$approvedByUserId, $linkId]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Reject a coach-facility link.
     */
    public function rejectLink(int $linkId, int $rejectedByUserId): bool
    {
        $stmt = $this->query(
            "UPDATE coach_facilities
             SET status = 'rejected', approved_by = ?, approved_at = NOW()
             WHERE id = ?",
            [$rejectedByUserId, $linkId]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Delete a coach_facilities row by its primary key.
     */
    public function deleteLink(int $linkId): bool
    {
        $stmt = $this->query("DELETE FROM coach_facilities WHERE id = ?", [$linkId]);
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Count completed coach_bookings for a coach.
     */
    public function getCompletedSessionCount(int $coachId): int
    {
        $row = $this->query(
            "SELECT COUNT(*) AS cnt FROM coach_bookings WHERE coach_id = ? AND status = 'completed'",
            [$coachId]
        )->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * Link a facility to a coach.
     * Returns the new row ID, or null if the pair already exists.
     */
    public function linkFacility(int $coachId, int $facilityId, bool $isPrimary = false): ?int
    {
        try {
            if ($isPrimary) {
                $this->query(
                    "UPDATE coach_facilities SET is_primary = 0 WHERE coach_id = ?",
                    [$coachId]
                );
            }
            $this->query(
                "INSERT INTO coach_facilities (coach_id, facility_id, is_primary) VALUES (?, ?, ?)",
                [$coachId, $facilityId, (int)$isPrimary]
            );
            return (int)$this->db->lastInsertId();
        } catch (\Exception $e) {
            // Duplicate key → already linked
            return null;
        }
    }

    /**
     * Unlink a facility from a coach.
     */
    public function unlinkFacility(int $coachId, int $facilityId): bool
    {
        $stmt = $this->query(
            "DELETE FROM coach_facilities WHERE coach_id = ? AND facility_id = ?",
            [$coachId, $facilityId]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Set a linked facility as primary (clears other primaries first).
     */
    public function setPrimaryFacility(int $coachId, int $facilityId): bool
    {
        $this->query(
            "UPDATE coach_facilities SET is_primary = 0 WHERE coach_id = ?",
            [$coachId]
        );
        $stmt = $this->query(
            "UPDATE coach_facilities SET is_primary = 1
             WHERE coach_id = ? AND facility_id = ?",
            [$coachId, $facilityId]
        );
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Return ALL linked facilities for a slot, each with 'available' (bool)
     * and 'unavailable_reason' (string) so the UI can show why a facility is blocked.
     */
    public function getFacilitiesForSlot(int $coachId, string $date, string $start, string $end): array
    {
        $facilities = $this->getFacilities($coachId);
        if (empty($facilities)) {
            return [];
        }

        $facilityModel = new \App\Models\SportsFacility();
        foreach ($facilities as &$fac) {
            $reason                    = $facilityModel->getAvailabilityReason((int)$fac['facility_id'], $date, $start, $end);
            $fac['available']          = ($reason === '');
            $fac['unavailable_reason'] = $reason;
        }
        unset($fac);
        return $facilities;
    }
}