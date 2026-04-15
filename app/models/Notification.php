<?php

namespace App\Models;

use Core\BaseModel;

/**
 * Notification Model
 *
 * Handles user notifications for bookings, payments, orders, etc.
 */
class Notification extends BaseModel
{
    protected string $table = 'notifications';

    protected array $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read'
    ];

    protected array $casts = [
        'is_read' => 'boolean',
        'data' => 'json'
    ];

    /**
     * Create a new notification
     */
    public function createNotification(int $userId, string $type, string $title, string $message, ?array $data = null): ?int
    {
        $notificationData = [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
            'is_read' => 0  // Use integer instead of boolean
        ];

        try {
            $result = $this->create($notificationData);
            error_log("Notification created for user {$userId}: " . ($result ? "ID {$result}" : "FAILED"));
            return $result;
        } catch (\Exception $e) {
            error_log("Failed to create notification: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Notify user their booking is pending ground owner approval
     */
    public function createBookingPendingNotification(int $userId, array $bookingDetails): ?int
    {
        $facilityName  = $bookingDetails['facility_name'] ?? 'Ground';
        $bookingDate   = $bookingDetails['booking_date'] ?? '';
        $startTime     = $bookingDetails['start_time'] ?? '';
        $endTime       = $bookingDetails['end_time'] ?? '';

        $formattedDate = date('F j, Y', strtotime($bookingDate));
        $formattedTime = date('g:i A', strtotime($startTime)) . ' – ' . date('g:i A', strtotime($endTime));

        return $this->createNotification($userId, 'booking_confirmation',
            'Booking Submitted',
            "Your booking request for {$facilityName} on {$formattedDate} at {$formattedTime} has been submitted and is awaiting approval from the ground owner.",
            [
                'booking_id'    => $bookingDetails['booking_id'] ?? null,
                'facility_id'   => $bookingDetails['facility_id'] ?? null,
                'facility_name' => $facilityName,
                'booking_date'  => $bookingDate,
                'start_time'    => $startTime,
                'end_time'      => $endTime,
                'status'        => 'pending',
            ]
        );
    }

    /**
     * Create booking confirmation notification (used when ground owner approves)
     */
    public function createBookingNotification(int $userId, array $bookingDetails): ?int
    {
        $facilityName = $bookingDetails['facility_name'] ?? 'Ground';
        $bookingDate = $bookingDetails['booking_date'] ?? '';
        $startTime = $bookingDetails['start_time'] ?? '';

        // Format the date nicely
        $formattedDate = date('F j, Y', strtotime($bookingDate));
        $formattedTime = date('g:i A', strtotime($startTime));

        $title = 'Booking Confirmed';
        $message = "Your booking for {$facilityName} has been confirmed for {$formattedDate} at {$formattedTime}.";

        return $this->createNotification($userId, 'booking_confirmation', $title, $message, [
            'booking_id' => $bookingDetails['booking_id'] ?? null,
            'facility_id' => $bookingDetails['facility_id'] ?? null,
            'facility_name' => $facilityName,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $bookingDetails['end_time'] ?? null
        ]);
    }

    /**
     * Create booking cancellation notification
     */
    public function createCancellationNotification(int $userId, array $bookingDetails): ?int
    {
        $facilityName = $bookingDetails['facility_name'] ?? 'Ground';
        $bookingDate = $bookingDetails['booking_date'] ?? '';

        $formattedDate = date('F j, Y', strtotime($bookingDate));

        $title = 'Booking Cancelled';
        $message = "Your booking for {$facilityName} on {$formattedDate} has been cancelled.";

        return $this->createNotification($userId, 'booking_cancelled', $title, $message, [
            'booking_id' => $bookingDetails['booking_id'] ?? null,
            'status' => 'cancelled'
        ]);
    }

    /**
     * Create payment success notification
     */
    public function createPaymentNotification(int $userId, float $amount, string $orderNumber): ?int
    {
        $formattedAmount = number_format($amount, 2);

        $title = 'Payment Successful';
        $message = "Your payment of LKR {$formattedAmount} for order #{$orderNumber} has been processed successfully.";

        return $this->createNotification($userId, 'payment_success', $title, $message, [
            'amount' => $amount,
            'order_number' => $orderNumber
        ]);
    }

    /**
     * Create order status notification
     */
    public function createOrderNotification(int $userId, string $orderNumber, string $status): ?int
    {
        $statusMessages = [
            'processing' => 'Your order #%s is being processed.',
            'shipped' => 'Your order #%s has been shipped and will arrive soon.',
            'delivered' => 'Your order #%s has been delivered.',
            'cancelled' => 'Your order #%s has been cancelled.'
        ];

        $title = 'Order Update';
        $message = sprintf($statusMessages[$status] ?? 'Your order #%s status has been updated.', $orderNumber);

        return $this->createNotification($userId, 'order_status', $title, $message, [
            'order_number' => $orderNumber,
            'status' => $status
        ]);
    }

    /**
     * Get notifications for a user
     */
    public function getByUser(int $userId, ?string $type = null, ?bool $unreadOnly = false): array
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
            $params = [$userId];

            if ($type && $type !== 'all') {
                $sql .= " AND type = ?";
                $params[] = $type;
            }

            if ($unreadOnly) {
                $sql .= " AND is_read = 0";
            }

            $sql .= " ORDER BY created_at DESC";

            $results = $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);

            // Parse JSON data field
            foreach ($results as &$row) {
                if (isset($row['data']) && is_string($row['data'])) {
                    $row['data'] = json_decode($row['data'], true);
                }
                $row['is_read'] = (bool) $row['is_read'];
            }

            return $results;
        } catch (\Exception $e) {
            // Table might not exist - log error and return empty array
            error_log("Notification::getByUser error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get notification statistics for a user
     */
    public function getStats(int $userId): array
    {
        $stats = [
            'total' => 0,
            'unread' => 0,
            'read' => 0,
            'today' => 0
        ];

        try {
            // Total
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
            $result = $this->queryFirst($sql, [$userId]);
            $stats['total'] = $result['count'] ?? 0;

            // Unread
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND is_read = 0";
            $result = $this->queryFirst($sql, [$userId]);
            $stats['unread'] = $result['count'] ?? 0;

            // Read
            $stats['read'] = $stats['total'] - $stats['unread'];

            // Today
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND DATE(created_at) = CURDATE()";
            $result = $this->queryFirst($sql, [$userId]);
            $stats['today'] = $result['count'] ?? 0;

        } catch (\Exception $e) {
            error_log("Error getting notification stats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = ? AND user_id = ?";
        return $this->query($sql, [$notificationId, $userId])->rowCount() > 0;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = ? AND is_read = 0";
        $this->query($sql, [$userId]);
        return true;
    }

    /**
     * Delete a notification
     */
    public function deleteNotification(int $notificationId, int $userId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?";
        return $this->query($sql, [$notificationId, $userId])->rowCount() > 0;
    }

    /**
     * Clear all notifications for a user
     */
    public function clearAll(int $userId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        $this->query($sql, [$userId]);
        return true;
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND is_read = 0";
        $result = $this->queryFirst($sql, [$userId]);
        return $result['count'] ?? 0;
    }

    /**
     * Notify the user who booked that their coach session is confirmed
     */
    public function createCoachBookingNotification(int $userId, array $bookingDetails): ?int
    {
        $coachName   = $bookingDetails['coach_name'] ?? 'your coach';
        $bookingDate = $bookingDetails['booking_date'] ?? '';
        $startTime   = $bookingDetails['start_time'] ?? '';
        $sessionType = $bookingDetails['session_type'] ?? 'session';

        $formattedDate = date('F j, Y', strtotime($bookingDate));
        $formattedTime = date('g:i A', strtotime($startTime));

        return $this->createNotification($userId, 'coach_booking',
            'Coach Session Confirmed',
            "Your {$sessionType} session with {$coachName} is confirmed for {$formattedDate} at {$formattedTime}.",
            [
                'booking_id'   => $bookingDetails['booking_id'] ?? null,
                'coach_id'     => $bookingDetails['coach_id'] ?? null,
                'coach_name'   => $coachName,
                'booking_date' => $bookingDate,
                'start_time'   => $startTime,
                'end_time'     => $bookingDetails['end_time'] ?? null,
                'session_type' => $sessionType,
            ]
        );
    }

    /**
     * Notify the coach that a new client booked a session
     */
    public function createCoachNewClientNotification(int $coachUserId, array $bookingDetails): ?int
    {
        $userName    = $bookingDetails['user_name'] ?? 'A client';
        $bookingDate = $bookingDetails['booking_date'] ?? '';
        $startTime   = $bookingDetails['start_time'] ?? '';
        $sessionType = $bookingDetails['session_type'] ?? 'session';

        $formattedDate = date('F j, Y', strtotime($bookingDate));
        $formattedTime = date('g:i A', strtotime($startTime));

        return $this->createNotification($coachUserId, 'coach_booking',
            'New Session Booked',
            "{$userName} has booked a {$sessionType} session with you on {$formattedDate} at {$formattedTime}.",
            [
                'booking_id'   => $bookingDetails['booking_id'] ?? null,
                'user_name'    => $userName,
                'booking_date' => $bookingDate,
                'start_time'   => $startTime,
                'end_time'     => $bookingDetails['end_time'] ?? null,
                'session_type' => $sessionType,
            ]
        );
    }

    /**
     * Notify the user that their coach session was cancelled
     */
    public function createCoachCancellationNotification(int $userId, array $bookingDetails): ?int
    {
        $coachName     = $bookingDetails['coach_name'] ?? 'your coach';
        $bookingDate   = $bookingDetails['booking_date'] ?? '';
        $formattedDate = date('F j, Y', strtotime($bookingDate));

        return $this->createNotification($userId, 'coach_booking',
            'Coach Session Cancelled',
            "Your session with {$coachName} on {$formattedDate} has been cancelled.",
            [
                'booking_id' => $bookingDetails['booking_id'] ?? null,
                'status'     => 'cancelled',
            ]
        );
    }

    /**
     * Notify the coach that a client cancelled their session
     */
    public function createCoachClientCancelledNotification(int $coachUserId, array $bookingDetails): ?int
    {
        $userName      = $bookingDetails['user_name'] ?? 'A client';
        $bookingDate   = $bookingDetails['booking_date'] ?? '';
        $formattedDate = date('F j, Y', strtotime($bookingDate));

        return $this->createNotification($coachUserId, 'coach_booking',
            'Session Cancelled by Client',
            "{$userName} has cancelled their session on {$formattedDate}.",
            [
                'booking_id' => $bookingDetails['booking_id'] ?? null,
                'status'     => 'cancelled',
            ]
        );
    }
}
