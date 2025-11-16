<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\SportsFacility;
use App\Models\SportsCategory;
use App\Models\GroundBooking;

class GroundOwnerController extends BaseController
{
    private ?SportsFacility $facilityModel = null;
    private ?SportsCategory $categoryModel = null;
    private ?GroundBooking $bookingModel = null;
    
    private function getFacilityModel(): SportsFacility
    {
        if ($this->facilityModel === null) {
            $this->facilityModel = new SportsFacility();
        }
        return $this->facilityModel;
    }
    
    private function getCategoryModel(): SportsCategory
    {
        if ($this->categoryModel === null) {
            $this->categoryModel = new SportsCategory();
        }
        return $this->categoryModel;
    }

    private function getBookingModel(): GroundBooking
    {
        if ($this->bookingModel === null) {
            $this->bookingModel = new GroundBooking();
        }
        return $this->bookingModel;
    }
    
    private function checkGroundOwnerAuth(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'ground_owner';
    }
    
    private function getGroundOwnerResponse(): Response
    {
        return $this->json([
            'success' => false,
            'message' => 'Unauthorized. Ground owner access required.',
            'status' => 401
        ], 401);
    }

    public function dashboard(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/dashboard');
    }
    
    public function groundsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/grounds');
    }
    
    public function bookingsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->view('ground-owner/booking-dashboard');
    }
    
    public function earningsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/earnings');
    }
    
    public function reviewsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/reviews');
    }
    
    public function schedulePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/schedule');
    }
    
    public function maintenancePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/maintenance');
    }
    
    public function profilePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/profile');
    }
    
    public function settingsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/settings');
    }
    
    // API Methods for Ground Management
    public function getGrounds(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $ownerId = $_SESSION['user_id'];
            $facilities = $this->getFacilityModel()->getByOwnerId($ownerId);
            
            return $this->json([
                'success' => true,
                'grounds' => $facilities
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load grounds',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $groundId = (int)$request->getParam('id');
            $ownerId = $_SESSION['user_id'];
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid ground ID'
                ], 400);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            if (!$ground || $ground['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }
            
            return $this->json([
                'success' => true,
                'ground' => $ground
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function createGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];
            
            // Validate required fields
            $required = ['name', 'sport_category_id', 'address', 'city', 'hourly_rate'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], 400);
                }
            }
            
            // Add owner ID and defaults
            $data['owner_id'] = $ownerId;
            $data['status'] = $data['status'] ?? 'active';
            $data['country'] = 'Sri Lanka';
            
            // Handle amenities array
            if (isset($data['amenities']) && is_array($data['amenities'])) {
                $data['amenities'] = json_encode($data['amenities']);
            }
            
            $groundId = $this->getFacilityModel()->create($data);
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create ground'
                ], 500);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            return $this->json([
                'success' => true,
                'message' => 'Ground created successfully',
                'ground' => $ground
            ], 201);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to create ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $groundId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid ground ID'
                ], 400);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            if (!$ground || $ground['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }
            
            // Remove fields that shouldn't be updated
            unset($data['id'], $data['owner_id'], $data['created_at']);
            
            // Handle amenities array
            if (isset($data['amenities']) && is_array($data['amenities'])) {
                $data['amenities'] = json_encode($data['amenities']);
            }
            
            $success = $this->getFacilityModel()->update($groundId, $data);
            
            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update ground'
                ], 500);
            }
            
            $updatedGround = $this->getFacilityModel()->find($groundId);
            
            return $this->json([
                'success' => true,
                'message' => 'Ground updated successfully',
                'ground' => $updatedGround
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $groundId = (int)$request->getParam('id');
            $ownerId = $_SESSION['user_id'];
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid ground ID'
                ], 400);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            if (!$ground || $ground['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }
            
            // Soft delete by setting status to inactive
            $success = $this->getFacilityModel()->update($groundId, ['status' => 'inactive']);
            
            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to delete ground'
                ], 500);
            }
            
            return $this->json([
                'success' => true,
                'message' => 'Ground deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to delete ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getSportsCategories(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $categories = $this->getCategoryModel()->getAllActive();

            return $this->json([
                'success' => true,
                'categories' => $categories
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ======================
    // BOOKING MANAGEMENT METHODS
    // ======================

    /**
     * Get dashboard statistics including booking stats
     */
    public function getDashboardStats(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            // Get booking statistics
            $bookingStats = $this->getBookingModel()->getOwnerBookingStats($ownerId);

            // Get ground statistics
            $grounds = $this->getFacilityModel()->getByOwnerId($ownerId);
            $groundStats = [
                'total_grounds' => count($grounds),
                'active_grounds' => count(array_filter($grounds, fn($g) => $g['status'] === 'active'))
            ];

            // Get recent bookings
            $recentBookings = $this->getBookingModel()->getRecentBookings($ownerId, 5);

            // Get today's bookings
            $todayBookings = $this->getBookingModel()->getTodayBookings($ownerId);

            return $this->json([
                'success' => true,
                'stats' => [
                    'bookings' => $bookingStats,
                    'grounds' => $groundStats
                ],
                'recent_bookings' => $recentBookings,
                'today_bookings' => $todayBookings
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all bookings for ground owner
     */
    public function getBookings(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            // Get query parameters for filtering
            $filters = [
                'start_date' => $request->getQuery('start_date'),
                'end_date' => $request->getQuery('end_date'),
                'status' => $request->getQuery('status'),
                'facility_id' => $request->getQuery('facility_id'),
                'user_search' => $request->getQuery('user_search')
            ];

            // Remove empty filters
            $filters = array_filter($filters, fn($value) => !empty($value));

            if (empty($filters)) {
                $bookings = $this->getBookingModel()->getBookingsByOwner($ownerId);
            } else {
                $bookings = $this->getBookingModel()->searchBookings($ownerId, $filters);
            }

            return $this->json([
                'success' => true,
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a booking (ground owner action)
     */
    public function cancelBooking(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $bookingId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];

            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid booking ID'
                ], 400);
            }

            // Verify booking belongs to owner's facility
            $booking = $this->getBookingModel()->find($bookingId);
            if (!$booking) {
                return $this->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $facility = $this->getFacilityModel()->find($booking['facility_id']);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this booking'
                ], 403);
            }

            $reason = $data['reason'] ?? 'Cancelled by ground owner';
            $success = $this->getBookingModel()->cancelBooking($bookingId, $ownerId, $reason);

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
     * Get bookings for a specific facility
     */
    public function getFacilityBookings(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $facilityId = (int)$request->getParam('id');
            $ownerId = $_SESSION['user_id'];

            if (!$facilityId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid facility ID'
                ], 400);
            }

            // Verify facility belongs to owner
            $facility = $this->getFacilityModel()->find($facilityId);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Facility not found'
                ], 404);
            }

            $status = $request->getQuery('status');
            $bookings = $this->getBookingModel()->getBookingsByFacility($facilityId, $status);

            return $this->json([
                'success' => true,
                'facility' => $facility,
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load facility bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $bookingId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];

            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid booking ID'
                ], 400);
            }

            $newStatus = $data['status'] ?? null;
            $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];

            if (!in_array($newStatus, $validStatuses)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid status'
                ], 400);
            }

            // Verify booking belongs to owner's facility
            $booking = $this->getBookingModel()->find($bookingId);
            if (!$booking) {
                return $this->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $facility = $this->getFacilityModel()->find($booking['facility_id']);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this booking'
                ], 403);
            }

            $success = $this->getBookingModel()->update($bookingId, ['status' => $newStatus]);

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update booking status'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Booking status updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update booking status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ======================
    // PROFILE MANAGEMENT
    // ======================

    /**
     * Get ground owner profile with statistics
     */
    public function getProfile(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            // Get user data
            $userModel = new \App\Models\User();
            $user = $userModel->find($ownerId);

            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Get ground owner profile
            $profileModel = new \App\Models\GroundOwnerProfile();
            $ownerProfile = $profileModel->getByUserId($ownerId);

            // Get statistics
            $facilities = $this->getFacilityModel()->getByOwnerId($ownerId);
            $bookingStats = $this->getBookingModel()->getOwnerBookingStats($ownerId);

            // Calculate average rating across all facilities
            $totalRating = 0;
            $totalReviews = 0;
            foreach ($facilities as $facility) {
                $totalRating += ($facility['rating'] ?? 0) * ($facility['total_reviews'] ?? 0);
                $totalReviews += $facility['total_reviews'] ?? 0;
            }
            $averageRating = $totalReviews > 0 ? $totalRating / $totalReviews : 0;

            $stats = [
                'total_facilities' => count($facilities),
                'total_bookings' => $bookingStats['total_bookings'] ?? 0,
                'average_rating' => round($averageRating, 2)
            ];

            // Remove sensitive data
            unset($user['password_hash']);

            return $this->json([
                'success' => true,
                'profile' => [
                    'user' => $user,
                    'owner_profile' => $ownerProfile,
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update ground owner profile
     */
    public function updateProfile(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            $profileModel = new \App\Models\GroundOwnerProfile();
            $success = $profileModel->updateProfile($ownerId, $data);

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update profile'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}