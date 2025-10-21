<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;
use App\Models\GroundBooking;

/**
 * User Controller
 * 
 * Handles user profile and account operations
 */
class UserController extends BaseController
{
    protected $userModel;
    protected $groundBookingModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->groundBookingModel = new GroundBooking();
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
            $orders = $this->userModel->getOrders($_SESSION['user_id']);
            
            return $this->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to fetch orders: ' . $e->getMessage()], 500);
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
        return $this->view('user/orders', [
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

        return $this->view('user/ground-bookings', [
            'user' => $user,
            'title' => 'My Ground Bookings - GoPlay Sports Platform',
            'additionalCSS' => [
                '/public/css/pages/user-bookings.css',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css'
            ],
            'additionalJS' => [
                '/public/js/pages/user-bookings.js'
            ]
        ]);
    }
}