<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;
use App\Models\GroundBooking;
use App\Models\Order;

/**
 * User Controller
 * 
 * Handles user profile and account operations
 */
class UserController extends BaseController
{
    protected $userModel;
    protected $groundBookingModel;
    protected $orderModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->groundBookingModel = new GroundBooking();
        $this->orderModel = new Order();
    }

    /**
     * Check if user is authenticated
     */
    private function requireAuth(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        return $_SESSION;
    }

    /**
     * Display user profile
     */
    public function profile(Request $request): Response
    {
        $session = $this->requireAuth();
        
        // Get user details
        $user = $this->userModel->find($session['user_id']);
        if (!$user) {
            return $this->redirect('/login');
        }

        // Get user statistics
        $stats = $this->userModel->getStatistics($session['user_id']);

        // Use regular view() method with layout and pass additional CSS/JS
        return $this->view('user/profile', [
            'user' => $user,
            'stats' => $stats,
            'title' => 'My Profile - GoPlay Sports Platform',
            'additionalCSS' => [
                '/public/css/pages/user-profile.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
            ],
            'additionalJS' => [
                '/public/js/pages/user-profile.js'
            ]
        ]);
    }

    /**
     * Dashboard for regular users
     */
    public function dashboard(Request $request): Response
    {
        $session = $this->requireAuth();
        
        // Get user details
        $user = $this->userModel->find($session['user_id']);
        if (!$user) {
            return $this->redirect('/login');
        }

        // Get user statistics
        $stats = $this->userModel->getStatistics($session['user_id']);
        
        return $this->view('user/dashboard', [
            'user' => $user,
            'stats' => $stats,
            'title' => 'Dashboard - GoPlay Sports Platform',
            'additionalCSS' => [
                '/public/css/pages/user-dashboard.css'
            ],
            'additionalJS' => [
                '/public/js/pages/user-dashboard.js'
            ]
        ]);
    }

    /**
     * Get user profile data (API)
     */
    public function getProfile(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $user = $this->userModel->find($_SESSION['user_id']);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        // Remove sensitive data
        unset($user['password_hash']);

        return $this->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $data = $request->getJsonBody();
            $userId = $_SESSION['user_id'];

            // Validate input
            $allowedFields = [
                'first_name', 'last_name', 'phone', 'date_of_birth'
            ];

            $updateData = [];
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = htmlspecialchars(trim($data[$field]));
                }
            }

            if (empty($updateData)) {
                return $this->json(['error' => 'No valid fields to update'], 400);
            }

            // Update user
            $success = $this->userModel->update($userId, $updateData);
            
            if ($success) {
                // Update session data
                if (isset($updateData['first_name']) || isset($updateData['last_name'])) {
                    $user = $this->userModel->find($userId);
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user']['name'] = $_SESSION['user_name'];
                }

                return $this->json([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                return $this->json(['error' => 'Failed to update profile'], 500);
            }

        } catch (\Exception $e) {
            return $this->json(['error' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload profile picture
     */
    public function uploadAvatar(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                return $this->json(['error' => 'No file uploaded or upload error'], 400);
            }

            $file = $_FILES['avatar'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            // Validate file type
            if (!in_array($file['type'], $allowedTypes)) {
                return $this->json(['error' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed'], 400);
            }

            // Validate file size
            if ($file['size'] > $maxSize) {
                return $this->json(['error' => 'File too large. Maximum size is 5MB'], 400);
            }

            // Create upload directory if it doesn't exist
            $uploadDir = ROOT_PATH . '/public/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $filePath = $uploadDir . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                // Update user record
                $relativePath = '/public/uploads/avatars/' . $filename;
                $success = $this->userModel->update($_SESSION['user_id'], [
                    'profile_picture' => $relativePath
                ]);

                if ($success) {
                    // Update session
                    $_SESSION['user']['avatar'] = $relativePath;

                    return $this->json([
                        'success' => true,
                        'message' => 'Avatar uploaded successfully',
                        'avatar_url' => $relativePath
                    ]);
                } else {
                    // Remove uploaded file if database update failed
                    unlink($filePath);
                    return $this->json(['error' => 'Failed to update database'], 500);
                }
            } else {
                return $this->json(['error' => 'Failed to upload file'], 500);
            }

        } catch (\Exception $e) {
            return $this->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $data = $request->getJsonBody();
            
            if (!isset($data['current_password']) || !isset($data['new_password'])) {
                return $this->json(['error' => 'Current password and new password are required'], 400);
            }

            $userId = $_SESSION['user_id'];
            $user = $this->userModel->getByEmail($_SESSION['user_email']); // Use getByEmail to get password_hash
            
            // Verify current password
            if (!$this->userModel->verifyPassword($data['current_password'], $user['password_hash'])) {
                return $this->json(['error' => 'Current password is incorrect'], 400);
            }

            // Validate new password
            if (strlen($data['new_password']) < 8) {
                return $this->json(['error' => 'New password must be at least 8 characters long'], 400);
            }

            // Update password
            $hashedPassword = $this->userModel->hashPassword($data['new_password']);
            $success = $this->userModel->update($userId, ['password_hash' => $hashedPassword]);

            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Password changed successfully'
                ]);
            } else {
                return $this->json(['error' => 'Failed to update password'], 500);
            }

        } catch (\Exception $e) {
            return $this->json(['error' => 'Password change failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get user's bookings
     */
    public function getBookings(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $bookings = $this->userModel->getBookings($_SESSION['user_id']);
            
            return $this->json([
                'success' => true,
                'bookings' => $bookings
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to fetch bookings: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get user's orders
     */
    public function getOrders(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = $_SESSION['user_id'];
            
            // Get filter parameters
            $status = $request->get('status', '');
            $paymentStatus = $request->get('payment_status', '');
            $search = $request->get('search', '');
            $dateFrom = $request->get('date_from', '');
            $dateTo = $request->get('date_to', '');
            $sort = $request->get('sort', 'newest');
            
            $orders = $this->userModel->getOrders($userId);
            
            // Apply filters
            if ($status) {
                $orders = array_filter($orders, fn($o) => $o['status'] === $status);
            }
            if ($paymentStatus) {
                $orders = array_filter($orders, fn($o) => $o['payment_status'] === $paymentStatus);
            }
            if ($search) {
                $orders = array_filter($orders, fn($o) => 
                    stripos($o['order_number'], $search) !== false ||
                    stripos($o['items'], $search) !== false
                );
            }
            if ($dateFrom) {
                $orders = array_filter($orders, fn($o) => $o['created_at'] >= $dateFrom);
            }
            if ($dateTo) {
                $orders = array_filter($orders, fn($o) => $o['created_at'] <= $dateTo . ' 23:59:59');
            }
            
            // Apply sorting
            if ($sort === 'oldest') {
                usort($orders, fn($a, $b) => strtotime($a['created_at']) - strtotime($b['created_at']));
            } elseif ($sort === 'highest') {
                usort($orders, fn($a, $b) => $b['total_amount'] - $a['total_amount']);
            } elseif ($sort === 'lowest') {
                usort($orders, fn($a, $b) => $a['total_amount'] - $b['total_amount']);
            }
            // Default is newest (already sorted in model)
            
            return $this->json([
                'success' => true,
                'orders' => array_values($orders)
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to fetch orders: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Get ID from route parameter
            $orderId = (int)$request->getParam('id', 0);
            $userId = $_SESSION['user_id'];
            
            if ($orderId <= 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid order ID'
                ], 400);
            }
            
            $order = $this->orderModel->getOrderDetailsForUser($orderId, $userId);
            
            if (!$order) {
                return $this->json([
                    'success' => false,
                    'message' => 'Order not found or access denied'
                ], 404);
            }
            
            return $this->json([
                'success' => true,
                'order' => $order
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load order details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user order statistics
     */
    public function getOrderStats(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $stats = $this->orderModel->getUserOrderStats($_SESSION['user_id']);
            
            return $this->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to fetch stats: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            // Get ID from route parameter
            $orderId = (int)$request->getParam('id', 0);
            $userId = $_SESSION['user_id'];
            
            // Get reason from JSON body
            $body = $request->getJsonBody();
            $reason = $body['reason'] ?? '';
            
            if ($orderId <= 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid order ID'
                ], 400);
            }
            
            $result = $this->orderModel->cancelOrder($orderId, $userId, $reason);
            
            return $this->json($result, $result['success'] ? 200 : 400);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * My bookings page
     */
    public function myBookings(Request $request): Response
    {
        $session = $this->requireAuth();
        return $this->view('user/bookings', [
            'title' => 'My Bookings - GoPlay Sports Platform'
        ]);
    }

    /**
     * My orders page  
     */
    public function myOrders(Request $request): Response
    {
        $session = $this->requireAuth();
        return $this->view('user/my-orders', [
            'title' => 'My Orders - GoPlay Sports Platform'
        ]);
    }

    /**
     * Shopping cart page
     */
    public function cart(Request $request): Response
    {
        $session = $this->requireAuth();
        return $this->view('user/cart', [
            'title' => 'Shopping Cart - GoPlay Sports Platform'
        ]);
    }

    /**
     * Notifications page
     */
    public function notifications(Request $request): Response
    {
        $session = $this->requireAuth();
        return $this->view('user/notifications', [
            'title' => 'Notifications - GoPlay Sports Platform'
        ]);
    }

    /**
     * Settings page
     */
    public function settings(Request $request): Response
    {
        $session = $this->requireAuth();
        return $this->view('user/settings', [
            'title' => 'Settings - GoPlay Sports Platform'
        ]);
    }

    // ======================
    // GROUND BOOKING METHODS
    // ======================

    /**
     * Get user's ground bookings (API)
     */
    public function getGroundBookings(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId = $_SESSION['user_id'];
            $bookings = $this->groundBookingModel->getBookingsByUser($userId);

            // Get booking statistics
            $stats = $this->groundBookingModel->getUserBookingStats($userId);

            return $this->json([
                'success' => true,
                'bookings' => $bookings,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch ground bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new ground booking
     */
    public function createGroundBooking(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $data = $request->getJsonBody();
            $userId = $_SESSION['user_id'];

            // Validate required fields
            $required = ['facility_id', 'booking_date', 'start_time', 'end_time'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], 400);
                }
            }

            // Validate booking date (must be in the future)
            $bookingDate = $data['booking_date'];
            if (strtotime($bookingDate) < strtotime('today')) {
                return $this->json([
                    'success' => false,
                    'message' => 'Booking date must be today or in the future'
                ], 400);
            }

            // Validate time slot availability
            $facilityId = (int)$data['facility_id'];
            $startTime = $data['start_time'];
            $endTime = $data['end_time'];

            if (!$this->groundBookingModel->isTimeSlotAvailable($facilityId, $bookingDate, $startTime, $endTime)) {
                return $this->json([
                    'success' => false,
                    'message' => 'This time slot is not available'
                ], 409);
            }

            // Create booking data
            $bookingData = [
                'user_id' => $userId,
                'facility_id' => $facilityId,
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'special_requests' => $data['special_requests'] ?? ''
            ];

            $bookingId = $this->groundBookingModel->createBooking($bookingData);

            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create booking'
                ], 500);
            }

            // Get the created booking with details
            $booking = $this->groundBookingModel->find($bookingId);

            return $this->json([
                'success' => true,
                'message' => 'Ground booked successfully! No payment required.',
                'booking' => $booking
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to create booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a ground booking
     */
    public function cancelGroundBooking(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $bookingId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $userId = $_SESSION['user_id'];

            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid booking ID'
                ], 400);
            }

            // Verify booking belongs to user
            $booking = $this->groundBookingModel->find($bookingId);
            if (!$booking || $booking['user_id'] != $userId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Booking not found or unauthorized'
                ], 404);
            }

            // Check if booking can be cancelled (not already completed or cancelled)
            if (in_array($booking['status'], ['completed', 'cancelled'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Cannot cancel this booking'
                ], 400);
            }

            $reason = $data['reason'] ?? 'Cancelled by user';
            $success = $this->groundBookingModel->cancelBooking($bookingId, $userId, $reason);

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to cancel booking'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get ground booking details
     */
    public function getGroundBookingDetails(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $bookingId = (int)$request->getParam('id');
            $userId = $_SESSION['user_id'];

            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid booking ID'
                ], 400);
            }

            $booking = $this->groundBookingModel->find($bookingId);
            if (!$booking || $booking['user_id'] != $userId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Get booking with facility details
            $bookings = $this->groundBookingModel->getBookingsByUser($userId);
            $bookingDetails = array_filter($bookings, fn($b) => $b['id'] == $bookingId);
            $bookingDetails = array_values($bookingDetails)[0] ?? $booking;

            return $this->json([
                'success' => true,
                'booking' => $bookingDetails
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to fetch booking details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ground booking
     */
    public function updateGroundBooking(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $bookingId = (int)$request->getParam('id');
            $userId = $_SESSION['user_id'];

            // Try getBody first, fallback to getJsonBody
            $data = $request->getBody();
            if (empty($data)) {
                $data = $request->getJsonBody();
            }

            error_log("=== Update Ground Booking Request ===");
            error_log("Booking ID: " . $bookingId);
            error_log("User ID: " . $userId);
            error_log("Content-Type: " . ($request->getHeader('content-type') ?? 'not set'));
            error_log("Raw body count: " . count($data));
            error_log("Update Data: " . json_encode($data));

            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid booking ID'
                ], 400);
            }

            // Verify booking belongs to user
            $booking = $this->groundBookingModel->find($bookingId);
            if (!$booking) {
                return $this->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            if ($booking['user_id'] != $userId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Unauthorized - This booking does not belong to you'
                ], 403);
            }

            // Check if booking can be updated
            if (in_array($booking['status'], ['completed', 'cancelled'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Cannot update completed or cancelled bookings'
                ], 400);
            }

            // Prepare update data
            $updateData = [];
            if (isset($data['booking_date'])) $updateData['booking_date'] = $data['booking_date'];
            if (isset($data['start_time'])) $updateData['start_time'] = $data['start_time'];
            if (isset($data['end_time'])) $updateData['end_time'] = $data['end_time'];
            if (isset($data['special_requests'])) $updateData['special_requests'] = $data['special_requests'];

            if (empty($updateData)) {
                return $this->json([
                    'success' => false,
                    'message' => 'No update data provided'
                ], 400);
            }

            error_log("Update data prepared: " . json_encode($updateData));

            $success = $this->groundBookingModel->updateBooking($bookingId, $updateData);

            if (!$success) {
                error_log("Update failed - possibly time slot not available");
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update booking. The time slot may not be available.'
                ], 400);
            }

            error_log("Booking updated successfully");
            return $this->json([
                'success' => true,
                'message' => 'Booking updated successfully'
            ]);

        } catch (\Exception $e) {
            error_log("Error updating ground booking: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->json([
                'success' => false,
                'message' => 'Failed to update booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ground bookings dashboard page
     */
    public function groundBookingsDashboard(Request $request): Response
    {
        $session = $this->requireAuth();

        // Get user details
        $user = $this->userModel->find($session['user_id']);
        if (!$user) {
            return $this->redirect('/login');
        }

        return $this->view('user/my-bookings', [
            'user' => $user,
            'title' => 'My Bookings - GoPlay Sports Platform',
            'additionalCSS' => [
                '/public/css/pages/user-bookings.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
            ],
            'additionalJS' => [
                '/public/js/pages/user-bookings.js'
            ]
        ]);
    }

    // ======================
    // FACILITY REVIEWS
    // ======================

    /**
     * Submit a review for a facility (users only)
     */
    public function submitReview(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'user') {
            return $this->json([
                'success' => false,
                'message' => 'Only users can submit reviews'
            ], 403);
        }

        try {
            $userId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            // Validate required fields
            if (empty($data['facility_id']) || empty($data['rating'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Facility ID and rating are required'
                ], 400);
            }

            $facilityId = (int)$data['facility_id'];
            $rating = (int)$data['rating'];
            $reviewText = $data['review_text'] ?? '';
            $bookingId = !empty($data['booking_id']) ? (int)$data['booking_id'] : null;

            // Validate rating
            if ($rating < 1 || $rating > 5) {
                return $this->json([
                    'success' => false,
                    'message' => 'Rating must be between 1 and 5'
                ], 400);
            }

            $reviewModel = new \App\Models\FacilityReview();

            // Check if user has completed booking for this facility
            if (!$reviewModel->canUserReview($userId, $facilityId)) {
                return $this->json([
                    'success' => false,
                    'message' => 'You can only review facilities you have booked and completed'
                ], 403);
            }

            // Check if user already reviewed this facility
            if ($reviewModel->hasUserReviewed($userId, $facilityId, $bookingId)) {
                return $this->json([
                    'success' => false,
                    'message' => 'You have already reviewed this facility'
                ], 400);
            }

            // Create review
            $reviewId = $reviewModel->createReview([
                'facility_id' => $facilityId,
                'user_id' => $userId,
                'booking_id' => $bookingId,
                'rating' => $rating,
                'review_text' => $reviewText
            ]);

            if (!$reviewId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to submit review'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'review_id' => $reviewId
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error submitting review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's reviews
     */
    public function getMyReviews(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $userId = $_SESSION['user_id'];
            $reviewModel = new \App\Models\FacilityReview();

            $reviews = $reviewModel->getByUserId($userId);

            return $this->json([
                'success' => true,
                'reviews' => $reviews
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error loading reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's review
     */
    public function updateReview(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $reviewId = (int)$request->getParam('id');
            $userId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            if (!$reviewId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid review ID'
                ], 400);
            }

            $reviewModel = new \App\Models\FacilityReview();
            $success = $reviewModel->updateReview($reviewId, $userId, $data);

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update review or unauthorized'
                ], 403);
            }

            return $this->json([
                'success' => true,
                'message' => 'Review updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error updating review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user's review
     */
    public function deleteReview(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $reviewId = (int)$request->getParam('id');
            $userId = $_SESSION['user_id'];

            if (!$reviewId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid review ID'
                ], 400);
            }

            $reviewModel = new \App\Models\FacilityReview();
            $success = $reviewModel->deleteReview($reviewId, $userId);

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to delete review or unauthorized'
                ], 403);
            }

            return $this->json([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error deleting review',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}