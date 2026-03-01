<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Ground Owner Notification Model
 *
 * Handles notifications for ground owners (bookings, payments, reviews, maintenance, etc.)
 */
class GroundOwnerNotification extends BaseModel
{
    protected string $table = 'ground_owner_notifications';

    protected array $fillable = [
        'owner_id',
        'title',
        'message',
        'notification_type',
        'reference_id',
        'reference_type',
        'is_read',
        'priority'
    ];

    protected array $casts = [
        'is_read' => 'boolean'
    ];

    /**
     * Create a notification for ground owner
     */
    public function createNotification(
        int $ownerId,
        string $title,
        string $message,
        string $type = 'system',
        ?int $referenceId = null,
        ?string $referenceType = null,
        string $priority = 'medium'
    ): ?int {
        $data = [
            'owner_id' => $ownerId,
            'title' => $title,
            'message' => $message,
            'notification_type' => $type,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'is_read' => 0,  // Use integer instead of boolean
            'priority' => $priority
        ];

        try {
            $result = $this->create($data);
            error_log("Ground owner notification created for owner {$ownerId}: " . ($result ? "ID {$result}" : "FAILED"));
            return $result;
        } catch (\Exception $e) {
            error_log("Failed to create ground owner notification: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create notification for new booking
     */
    public function createBookingNotification(int $ownerId, array $bookingDetails): ?int
    {
        $customerName = ($bookingDetails['first_name'] ?? '') . ' ' . ($bookingDetails['last_name'] ?? '');
        $facilityName = $bookingDetails['facility_name'] ?? 'your facility';
        $bookingDate = isset($bookingDetails['booking_date'])
            ? date('F j, Y', strtotime($bookingDetails['booking_date']))
            : '';

        $title = 'New Booking Received';
        $message = "You have received a new booking from {$customerName} for {$facilityName} on {$bookingDate}.";

        return $this->createNotification(
            $ownerId,
            $title,
            $message,
            'booking',
            $bookingDetails['booking_id'] ?? null,
            'facility_booking',
            'high'
        );
    }

    /**
     * Create notification for booking cancellation
     */
    public function createCancellationNotification(int $ownerId, array $bookingDetails): ?int
    {
        $customerName = ($bookingDetails['first_name'] ?? '') . ' ' . ($bookingDetails['last_name'] ?? '');
        $facilityName = $bookingDetails['facility_name'] ?? 'your facility';

        $title = 'Booking Cancelled';
        $message = "{$customerName} has cancelled their booking for {$facilityName}.";

        return $this->createNotification(
            $ownerId,
            $title,
            $message,
            'booking',
            $bookingDetails['booking_id'] ?? null,
            'facility_booking',
            'medium'
        );
    }

    /**
     * Create notification for new review
     */
    public function createReviewNotification(int $ownerId, array $reviewDetails): ?int
    {
        $reviewerName = $reviewDetails['reviewer_name'] ?? 'A customer';
        $rating = $reviewDetails['rating'] ?? 5;
        $facilityName = $reviewDetails['facility_name'] ?? 'your facility';

        $title = 'New Review Posted';
        $message = "{$reviewerName} left a {$rating}-star review for {$facilityName}.";

        return $this->createNotification(
            $ownerId,
            $title,
            $message,
            'review',
            $reviewDetails['review_id'] ?? null,
            'facility_review',
            'medium'
        );
    }

    /**
     * Create notification for payment received
     */
    public function createPaymentNotification(int $ownerId, float $amount, ?int $bookingId = null): ?int
    {
        $formattedAmount = number_format($amount, 2);

        $title = 'Payment Received';
        $message = "Payment of LKR {$formattedAmount} has been received for booking #{$bookingId}.";

        return $this->createNotification(
            $ownerId,
            $title,
            $message,
            'payment',
            $bookingId,
            'facility_booking',
            'medium'
        );
    }

    /**
     * Create maintenance reminder notification
     */
    public function createMaintenanceNotification(int $ownerId, array $maintenanceDetails): ?int
    {
        $taskTitle = $maintenanceDetails['title'] ?? 'Maintenance task';
        $dueDate = isset($maintenanceDetails['due_date'])
            ? date('F j, Y', strtotime($maintenanceDetails['due_date']))
            : 'soon';

        $title = 'Maintenance Reminder';
        $message = "Scheduled maintenance '{$taskTitle}' is due {$dueDate}.";

        return $this->createNotification(
            $ownerId,
            $title,
            $message,
            'maintenance',
            $maintenanceDetails['task_id'] ?? null,
            'maintenance_task',
            'medium'
        );
    }

    /**
     * Get notifications for owner
     */
    public function getByOwner(int $ownerId, ?string $type = null, ?bool $unreadOnly = false, int $limit = 50): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE owner_id = ?";
        $params = [$ownerId];

        if ($type && $type !== 'all') {
            $sql .= " AND notification_type = ?";
            $params[] = $type;
        }

        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        $results = $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['is_read'] = (bool) $row['is_read'];
        }

        return $results;
    }

    /**
     * Get notification statistics
     */
    public function getStats(int $ownerId): array
    {
        $stats = [
            'total' => 0,
            'unread' => 0,
            'today' => 0,
            'by_type' => [
                'booking' => 0,
                'payment' => 0,
                'review' => 0,
                'maintenance' => 0,
                'system' => 0
            ]
        ];

        try {
            // Total
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE owner_id = ?";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['total'] = $result['count'] ?? 0;

            // Unread
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE owner_id = ? AND is_read = 0";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['unread'] = $result['count'] ?? 0;

            // Today
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE owner_id = ? AND DATE(created_at) = CURDATE()";
            $result = $this->queryFirst($sql, [$ownerId]);
            $stats['today'] = $result['count'] ?? 0;

            // By type
            $sql = "SELECT notification_type, COUNT(*) as count FROM {$this->table} WHERE owner_id = ? GROUP BY notification_type";
            $results = $this->query($sql, [$ownerId])->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($results as $row) {
                $type = $row['notification_type'];
                if (isset($stats['by_type'][$type])) {
                    $stats['by_type'][$type] = (int) $row['count'];
                }
            }

        } catch (\Exception $e) {
            error_log("Error getting notification stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $ownerId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1, read_at = NOW() WHERE id = ? AND owner_id = ?";
        return $this->query($sql, [$notificationId, $ownerId])->rowCount() > 0;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(int $ownerId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1, read_at = NOW() WHERE owner_id = ? AND is_read = 0";
        $this->query($sql, [$ownerId]);
        return true;
    }

    /**
     * Delete a notification
     */
    public function deleteNotification(int $notificationId, int $ownerId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ? AND owner_id = ?";
        return $this->query($sql, [$notificationId, $ownerId])->rowCount() > 0;
    }

    /**
     * Clear all notifications
     */
    public function clearAll(int $ownerId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE owner_id = ?";
        $this->query($sql, [$ownerId]);
        return true;
    }

    /**
     * Get unread count
     */
    public function getUnreadCount(int $ownerId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE owner_id = ? AND is_read = 0";
        $result = $this->queryFirst($sql, [$ownerId]);
        return $result['count'] ?? 0;
    }

    /**
     * Get recent notifications for dashboard
     */
    public function getRecentNotifications(int $ownerId, int $limit = 5): array
    {
        return $this->getByOwner($ownerId, null, false, $limit);
    }
}
