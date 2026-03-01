<?php

namespace App\Models;

use Core\BaseModel;

/**
 * FacilityReview Model
 * Handles facility reviews and ratings
 */
class FacilityReview extends BaseModel
{
    protected string $table = 'facility_reviews';

    protected array $fillable = [
        'facility_id',
        'user_id',
        'booking_id',
        'rating',
        'review_text'
    ];

    /**
     * Get all reviews for a facility
     */
    public function getByFacilityId(int $facilityId, ?int $limit = null): array
    {
        $sql = "SELECT
                    fr.*,
                    u.first_name,
                    u.last_name,
                    u.profile_picture,
                    fb.booking_date
                FROM {$this->table} fr
                JOIN users u ON fr.user_id = u.id
                LEFT JOIN facility_bookings fb ON fr.booking_id = fb.id
                WHERE fr.facility_id = ?
                ORDER BY fr.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            $params = [$facilityId, $limit];
        } else {
            $params = [$facilityId];
        }

        return $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get reviews for all facilities owned by a ground owner
     */
    public function getByOwnerId(int $ownerId, ?int $limit = null): array
    {
        $sql = "SELECT
                    fr.*,
                    u.first_name,
                    u.last_name,
                    u.profile_picture,
                    sf.name as facility_name,
                    sf.id as facility_id,
                    fb.booking_date
                FROM {$this->table} fr
                JOIN users u ON fr.user_id = u.id
                JOIN sports_facilities sf ON fr.facility_id = sf.id
                LEFT JOIN facility_bookings fb ON fr.booking_id = fb.id
                WHERE sf.owner_id = ?
                ORDER BY fr.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            $params = [$ownerId, $limit];
        } else {
            $params = [$ownerId];
        }

        return $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get reviews by user
     */
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT
                    fr.*,
                    sf.name as facility_name,
                    sf.id as facility_id,
                    fb.booking_date
                FROM {$this->table} fr
                JOIN sports_facilities sf ON fr.facility_id = sf.id
                LEFT JOIN facility_bookings fb ON fr.booking_id = fb.id
                WHERE fr.user_id = ?
                ORDER BY fr.created_at DESC";

        return $this->query($sql, [$userId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Check if user has already reviewed a facility
     */
    public function hasUserReviewed(int $userId, int $facilityId, ?int $bookingId = null): bool
    {
        if ($bookingId) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}
                    WHERE user_id = ? AND facility_id = ? AND booking_id = ?";
            $params = [$userId, $facilityId, $bookingId];
        } else {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}
                    WHERE user_id = ? AND facility_id = ?";
            $params = [$userId, $facilityId];
        }

        $result = $this->queryFirst($sql, $params);
        return $result && $result['count'] > 0;
    }

    /**
     * Check if user has completed booking for this facility
     */
    public function canUserReview(int $userId, int $facilityId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM facility_bookings
                WHERE user_id = ? AND facility_id = ? AND status = 'completed'";

        $result = $this->queryFirst($sql, [$userId, $facilityId]);
        return $result && $result['count'] > 0;
    }

    /**
     * Create a review
     */
    public function createReview(array $data): int
    {
        // Validate rating
        if (!isset($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            throw new \Exception('Rating must be between 1 and 5');
        }

        // Create the review
        $reviewId = $this->create($data);

        // Update facility rating
        if ($reviewId) {
            $this->updateFacilityRating($data['facility_id']);
        }

        return $reviewId;
    }

    /**
     * Update facility average rating and total reviews
     */
    private function updateFacilityRating(int $facilityId): void
    {
        $sql = "UPDATE sports_facilities
                SET rating = (
                    SELECT COALESCE(AVG(rating), 0)
                    FROM {$this->table}
                    WHERE facility_id = ?
                ),
                total_reviews = (
                    SELECT COUNT(*)
                    FROM {$this->table}
                    WHERE facility_id = ?
                )
                WHERE id = ?";

        $this->query($sql, [$facilityId, $facilityId, $facilityId]);
    }

    /**
     * Get review statistics for a facility
     */
    public function getReviewStats(int $facilityId): array
    {
        $sql = "SELECT
                    COUNT(*) as total_reviews,
                    COALESCE(AVG(rating), 0) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                FROM {$this->table}
                WHERE facility_id = ?";

        return $this->queryFirst($sql, [$facilityId]) ?: [];
    }

    /**
     * Get review statistics for owner's facilities
     */
    public function getOwnerReviewStats(int $ownerId): array
    {
        $sql = "SELECT
                    COUNT(*) as total_reviews,
                    COALESCE(AVG(fr.rating), 0) as average_rating,
                    COUNT(DISTINCT fr.facility_id) as facilities_with_reviews
                FROM {$this->table} fr
                JOIN sports_facilities sf ON fr.facility_id = sf.id
                WHERE sf.owner_id = ?";

        return $this->queryFirst($sql, [$ownerId]) ?: [];
    }

    /**
     * Delete a review (only by admin or the user who created it)
     */
    public function deleteReview(int $reviewId, int $userId): bool
    {
        // Get review to check ownership
        $review = $this->find($reviewId);

        if (!$review || $review['user_id'] != $userId) {
            return false;
        }

        $facilityId = $review['facility_id'];

        // Delete the review
        $success = $this->delete($reviewId);

        // Update facility rating
        if ($success) {
            $this->updateFacilityRating($facilityId);
        }

        return $success;
    }

    /**
     * Update a review
     */
    public function updateReview(int $reviewId, int $userId, array $data): bool
    {
        // Get review to check ownership
        $review = $this->find($reviewId);

        if (!$review || $review['user_id'] != $userId) {
            return false;
        }

        // Validate rating if provided
        if (isset($data['rating']) && ($data['rating'] < 1 || $data['rating'] > 5)) {
            throw new \Exception('Rating must be between 1 and 5');
        }

        $facilityId = $review['facility_id'];

        // Update the review
        $updateData = [];
        if (isset($data['rating'])) {
            $updateData['rating'] = $data['rating'];
        }
        if (isset($data['review_text'])) {
            $updateData['review_text'] = $data['review_text'];
        }

        $success = $this->update($reviewId, $updateData);

        // Update facility rating
        if ($success) {
            $this->updateFacilityRating($facilityId);
        }

        return $success;
    }

    /**
     * Get recent reviews (for dashboard)
     */
    public function getRecentReviews(int $ownerId, int $limit = 5): array
    {
        return $this->getByOwnerId($ownerId, $limit);
    }

    /**
     * Get all reviews for owner's facilities — includes reply data
     */
    public function getByOwnerIdWithReplies(int $ownerId): array
    {
        $sql = "SELECT
                    fr.*,
                    u.first_name,
                    u.last_name,
                    u.profile_picture,
                    sf.name  AS facility_name,
                    sf.id    AS facility_id,
                    fb.booking_date,
                    frr.id         AS reply_id,
                    frr.reply_text AS reply_text,
                    frr.created_at AS replied_at,
                    frr.updated_at AS reply_updated_at
                FROM {$this->table} fr
                JOIN users u             ON fr.user_id     = u.id
                JOIN sports_facilities sf ON fr.facility_id = sf.id
                LEFT JOIN facility_bookings fb       ON fr.booking_id  = fb.id
                LEFT JOIN facility_review_replies frr ON fr.id          = frr.review_id
                WHERE sf.owner_id = ?
                ORDER BY fr.created_at DESC";

        return $this->query($sql, [$ownerId])->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get detailed stats for owner: rating breakdown, pending replies, recent count
     */
    public function getOwnerReviewStatsDetailed(int $ownerId): array
    {
        $sql = "SELECT
                    COUNT(fr.id)                                                        AS total_reviews,
                    COALESCE(ROUND(AVG(fr.rating), 1), 0)                              AS average_rating,
                    SUM(CASE WHEN fr.rating = 5 THEN 1 ELSE 0 END)                     AS five_star,
                    SUM(CASE WHEN fr.rating = 4 THEN 1 ELSE 0 END)                     AS four_star,
                    SUM(CASE WHEN fr.rating = 3 THEN 1 ELSE 0 END)                     AS three_star,
                    SUM(CASE WHEN fr.rating = 2 THEN 1 ELSE 0 END)                     AS two_star,
                    SUM(CASE WHEN fr.rating = 1 THEN 1 ELSE 0 END)                     AS one_star,
                    COUNT(DISTINCT fr.facility_id)                                      AS facilities_reviewed,
                    SUM(CASE WHEN frr.id IS NULL THEN 1 ELSE 0 END)                    AS pending_replies,
                    SUM(CASE WHEN fr.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                              THEN 1 ELSE 0 END)                                        AS recent_count
                FROM {$this->table} fr
                JOIN sports_facilities sf     ON fr.facility_id = sf.id
                LEFT JOIN facility_review_replies frr ON fr.id  = frr.review_id
                WHERE sf.owner_id = ?";

        return $this->queryFirst($sql, [$ownerId]) ?: [];
    }

    /**
     * Upsert (create or update) an owner reply to a review
     */
    public function saveReply(int $reviewId, int $ownerId, string $replyText): bool
    {
        // Verify the review belongs to one of the owner's facilities
        $check = "SELECT fr.id FROM {$this->table} fr
                  JOIN sports_facilities sf ON fr.facility_id = sf.id
                  WHERE fr.id = ? AND sf.owner_id = ?";
        $row = $this->queryFirst($check, [$reviewId, $ownerId]);
        if (!$row) return false;

        // Upsert
        $sql = "INSERT INTO facility_review_replies (review_id, owner_id, reply_text)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    reply_text = VALUES(reply_text),
                    updated_at = CURRENT_TIMESTAMP";

        $this->query($sql, [$reviewId, $ownerId, $replyText]);
        return true;
    }

    /**
     * Delete an owner reply
     */
    public function deleteReply(int $reviewId, int $ownerId): bool
    {
        $sql = "DELETE frr FROM facility_review_replies frr
                JOIN {$this->table} fr ON frr.review_id = fr.id
                JOIN sports_facilities sf ON fr.facility_id = sf.id
                WHERE frr.review_id = ? AND sf.owner_id = ?";

        $stmt = $this->query($sql, [$reviewId, $ownerId]);
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Report a review to admin
     */
    public function reportReview(int $reviewId, int $reporterId, string $reason, ?string $description = null): bool
    {
        // Only valid reasons accepted
        $valid = ['spam', 'offensive', 'inappropriate', 'fake', 'other'];
        if (!in_array($reason, $valid)) return false;

        $sql = "INSERT IGNORE INTO facility_review_reports
                    (review_id, reporter_id, reason, description)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->query($sql, [$reviewId, $reporterId, $reason, $description]);
        return $stmt && $stmt->rowCount() > 0;
    }
}
