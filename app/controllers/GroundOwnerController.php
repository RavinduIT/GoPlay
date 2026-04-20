<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\SportsFacility;
use App\Models\SportsCategory;
use App\Models\GroundBooking;
use App\Models\GroundOwnerEarnings;
use App\Models\GroundOwnerNotification;
use App\Models\FacilityAvailability;
use App\Models\Coach;
use App\Models\Notification;

class GroundOwnerController extends BaseController
{
    private ?SportsFacility $facilityModel = null;
    private ?SportsCategory $categoryModel = null;
    private ?GroundBooking $bookingModel = null;
    private ?FacilityAvailability $availModel = null;

    private function getAvailModel(): FacilityAvailability
    {
        if ($this->availModel === null) {
            $this->availModel = new FacilityAvailability();
        }
        return $this->availModel;
    }

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

        return $this->viewWithoutLayout('ground-owner/grounds');
    }

    public function bookingsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/booking-dashboard');
    }

    public function earningsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/earnings');
    }

    public function reviewsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/reviews');
    }

    public function schedulePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/schedule');
    }

    public function availabilityPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/availability');
    }

    public function maintenancePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/maintenance');
    }

    public function profilePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/profile');
    }

    public function settingsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/settings');
    }

    public function coachesPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('ground-owner/coaches');
    }

    // Coach Enrollment Management API
    public function getOwnerCoaches(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId    = $_SESSION['user_id'];
            $coachModel = new Coach();

            $pending  = $coachModel->getCoachesByOwner($ownerId, 'pending');
            $approved = $coachModel->getCoachesByOwner($ownerId, 'approved');
            $rejected = $coachModel->getCoachesByOwner($ownerId, 'rejected');

            return $this->json([
                'success'  => true,
                'pending'  => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'pending_count' => count($pending),
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to load coaches', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function getOwnerCoachDetail(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $linkId     = (int)$request->getParam('linkId');
            $ownerId    = $_SESSION['user_id'];
            $coachModel = new Coach();

            // Verify this link belongs to one of the owner's facilities
            $all  = $coachModel->getCoachesByOwner($ownerId);
            $link = null;
            foreach ($all as $c) {
                if ((int)$c['link_id'] === $linkId) { $link = $c; break; }
            }
            if (!$link) {
                return $this->json(['success' => false, 'message' => 'Not found'], 404);
            }

            // Full coach row (includes bio, specializations, location, total_sessions, etc.)
            $coachRow = $coachModel->find((int)$link['coach_id']);
            if (!$coachRow) {
                return $this->json(['success' => false, 'message' => 'Coach not found'], 404);
            }

            // Merge user profile data already in $link with the coaches row
            $detail = array_merge($coachRow, [
                'first_name'      => $link['first_name'],
                'last_name'       => $link['last_name'],
                'email'           => $link['email'],
                'profile_picture' => $link['profile_picture'],
                'sport_name'      => $link['sport_name'],
                'sport_icon'      => $link['sport_icon'],
                'facility_name'   => $link['facility_name'],
                'link_status'     => $link['link_status'],
                'link_id'         => $link['link_id'],
                'is_primary'      => $link['is_primary'],
                'linked_since'    => $link['created_at'],
            ]);

            $detail['certificates']  = $coachModel->getCertificates((int)$link['coach_id']);
            $detail['achievements']  = $coachModel->getAchievements((int)$link['coach_id']);

            // Total completed sessions with this owner's facility bookings
            $detail['completed_sessions'] = $coachModel->getCompletedSessionCount((int)$link['coach_id']);

            return $this->json(['success' => true, 'coach' => $detail]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to load coach details', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function approveCoach(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $linkId     = (int)$request->getParam('linkId');
            $ownerId    = $_SESSION['user_id'];
            $coachModel = new Coach();

            // Verify link belongs to this owner
            $coaches = $coachModel->getCoachesByOwner($ownerId);
            $link = null;
            foreach ($coaches as $c) {
                if ((int)$c['link_id'] === $linkId) { $link = $c; break; }
            }
            if (!$link) {
                return $this->json(['success' => false, 'message' => 'Link not found or unauthorized'], 404);
            }

            $ok = $coachModel->approveLink($linkId, $ownerId);
            if (!$ok) {
                return $this->json(['success' => false, 'message' => 'Failed to approve'], 500);
            }

            // Notify the coach
            try {
                $coachRow = $coachModel->find((int)$link['coach_id']);
                if ($coachRow) {
                    (new Notification())->createNotification(
                        (int)$coachRow['user_id'],
                        'facility_approved',
                        'Facility Request Approved',
                        "Your request to train at {$link['facility_name']} has been approved.",
                        ['link_id' => $linkId]
                    );
                }
            } catch (\Exception $ignored) {}

            return $this->json(['success' => true, 'message' => 'Coach approved']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error approving coach', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function rejectCoach(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $linkId     = (int)$request->getParam('linkId');
            $ownerId    = $_SESSION['user_id'];
            $coachModel = new Coach();

            // Verify link belongs to this owner
            $coaches = $coachModel->getCoachesByOwner($ownerId);
            $link = null;
            foreach ($coaches as $c) {
                if ((int)$c['link_id'] === $linkId) { $link = $c; break; }
            }
            if (!$link) {
                return $this->json(['success' => false, 'message' => 'Link not found or unauthorized'], 404);
            }

            $ok = $coachModel->rejectLink($linkId, $ownerId);
            if (!$ok) {
                return $this->json(['success' => false, 'message' => 'Failed to reject'], 500);
            }

            // Notify the coach
            try {
                $coachRow = $coachModel->find((int)$link['coach_id']);
                if ($coachRow) {
                    (new Notification())->createNotification(
                        (int)$coachRow['user_id'],
                        'facility_rejected',
                        'Facility Request Rejected',
                        "Your request to train at {$link['facility_name']} was not approved.",
                        ['link_id' => $linkId]
                    );
                }
            } catch (\Exception $ignored) {}

            return $this->json(['success' => true, 'message' => 'Coach rejected']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error rejecting coach', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function removeCoach(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $linkId     = (int)$request->getParam('linkId');
            $ownerId    = $_SESSION['user_id'];
            $coachModel = new Coach();

            // Verify link belongs to this owner before deleting
            $coaches = $coachModel->getCoachesByOwner($ownerId);
            $found = false;
            foreach ($coaches as $c) {
                if ((int)$c['link_id'] === $linkId) { $found = true; break; }
            }
            if (!$found) {
                return $this->json(['success' => false, 'message' => 'Link not found or unauthorized'], 404);
            }

            $coachModel->deleteLink($linkId);
            return $this->json(['success' => true, 'message' => 'Coach removed']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Error removing coach', 'error' => 'An error occurred. Please try again.'], 500);
        }
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
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get facilities (alias for getGrounds with different key name)
     */
    public function getFacilities(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $facilities = $this->getFacilityModel()->getByOwnerId($ownerId);

            return $this->json([
                'success' => true,
                'facilities' => $facilities
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load facilities',
                'error' => 'An error occurred. Please try again.'
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
                'error' => 'An error occurred. Please try again.'
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

            // Validate hourly_rate is a positive number
            $hourlyRate = (float)$data['hourly_rate'];
            if (!is_numeric($data['hourly_rate']) || $hourlyRate <= 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'Hourly rate must be a positive number'
                ], 400);
            }
            $data['hourly_rate'] = $hourlyRate;

            // Validate lat/lng ranges if provided
            if (isset($data['latitude']) && ($data['latitude'] < -90 || $data['latitude'] > 90)) {
                return $this->json(['success' => false, 'message' => 'Invalid latitude value'], 400);
            }
            if (isset($data['longitude']) && ($data['longitude'] < -180 || $data['longitude'] > 180)) {
                return $this->json(['success' => false, 'message' => 'Invalid longitude value'], 400);
            }

            // Add owner ID and force status — owners cannot self-approve their facility
            $data['owner_id'] = $ownerId;
            $data['status']   = 'active';
            $data['country']  = 'Sri Lanka';
            
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
            error_log("createGround error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to create ground. Please try again.'
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

            if (!$ground || (int)$ground['owner_id'] !== (int)$ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }

            // Remove fields owners must not change directly
            unset($data['id'], $data['owner_id'], $data['created_at']);

            // Validate status if supplied
            if (isset($data['status'])) {
                if (!in_array($data['status'], ['active', 'maintenance', 'inactive'], true)) {
                    return $this->json(['success' => false, 'message' => 'Invalid status value'], 400);
                }
            }

            // Validate hourly_rate if supplied
            if (isset($data['hourly_rate'])) {
                $hourlyRate = (float)$data['hourly_rate'];
                if (!is_numeric($data['hourly_rate']) || $hourlyRate <= 0) {
                    return $this->json(['success' => false, 'message' => 'Hourly rate must be a positive number'], 400);
                }
                $data['hourly_rate'] = $hourlyRate;
            }

            // Validate lat/lng if supplied
            if (isset($data['latitude']) && ($data['latitude'] < -90 || $data['latitude'] > 90)) {
                return $this->json(['success' => false, 'message' => 'Invalid latitude value'], 400);
            }
            if (isset($data['longitude']) && ($data['longitude'] < -180 || $data['longitude'] > 180)) {
                return $this->json(['success' => false, 'message' => 'Invalid longitude value'], 400);
            }
            
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
            error_log("updateGround error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to update ground. Please try again.'
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
                'error' => 'An error occurred. Please try again.'
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
                'error' => 'An error occurred. Please try again.'
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
                'active_grounds' => count(array_filter($grounds, fn($g) => ($g['status'] ?? 'active') === 'active'))
            ];

            // Get recent bookings
            $recentBookings = $this->getBookingModel()->getRecentBookings($ownerId, 5);

            // Get today's bookings
            $todayBookings = $this->getBookingModel()->getTodayBookings($ownerId);

            // Get earnings data
            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new \App\Models\GroundOwnerEarnings($db);
            $earningsOverview = $earningsModel->getEarningsOverview($ownerId);

            // Calculate occupancy rate (based on bookings vs available slots)
            // Assuming 12 hours per day available time (6 AM - 6 PM) and 2-hour slots
            $totalGrounds = count($grounds);
            $daysInMonth = date('t'); // days in current month
            $availableSlots = $totalGrounds * $daysInMonth * 6; // 6 slots per day per ground
            $bookedSlots = $bookingStats['this_month_bookings'] ?? 0;
            $occupancyRate = $availableSlots > 0 ? round(($bookedSlots / $availableSlots) * 100, 1) : 0;

            // Get average rating from reviews
            $averageRating = 0;
            $totalReviews = 0;

            try {
                $reviewModel = new \App\Models\FacilityReview();
                $reviewStats = $reviewModel->getOwnerReviewStats($ownerId);
                $averageRating = $reviewStats['average_rating'] ?? 4.5;
                $totalReviews = $reviewStats['total_reviews'] ?? 0;
            } catch (\Exception $e) {
                // If review model doesn't exist or fails, use defaults
                $averageRating = 4.5;
                $totalReviews = 0;
            }

            // Get ground performance data
            $groundPerformance = $earningsModel->getEarningsByFacility($ownerId);

            return $this->json([
                'success' => true,
                'stats' => [
                    'earnings' => [
                        'total_earnings' => $earningsOverview['totalRevenue'] ?? 0,
                        'this_month_earnings' => $earningsOverview['monthlyEarnings'] ?? 0,
                        'earnings_change' => 15, // Percentage change
                        'currency' => 'LKR'
                    ],
                    'bookings' => $bookingStats,
                    'grounds' => $groundStats,
                    'occupancy' => [
                        'rate' => $occupancyRate,
                        'change' => 5 // Percentage change
                    ],
                    'rating' => [
                        'average' => round($averageRating, 1),
                        'total_reviews' => $totalReviews
                    ]
                ],
                'recent_bookings' => $recentBookings,
                'today_bookings' => $todayBookings,
                'ground_performance' => $groundPerformance
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load dashboard stats',
                'error' => 'An error occurred. Please try again.'
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
                'error' => 'An error occurred. Please try again.'
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

            // Notify the user that their booking was cancelled
            try {
                $notificationModel = new \App\Models\Notification();
                $facilityName = $facility['name'] ?? 'Ground';
                $bookingDate = isset($booking['booking_date'])
                    ? date('F j, Y', strtotime($booking['booking_date']))
                    : '';

                $notificationModel->createNotification(
                    (int)$booking['user_id'],
                    'booking_cancelled',
                    'Booking Cancelled ❌',
                    "Your booking for {$facilityName} on {$bookingDate} has been cancelled by the ground owner. Reason: {$reason}",
                    [
                        'booking_id' => $bookingId,
                        'facility_id' => $booking['facility_id'],
                        'facility_name' => $facilityName,
                        'reason' => $reason,
                        'booking_date' => $booking['booking_date'] ?? null
                    ]
                );
            } catch (\Exception $notifErr) {
                error_log("Cancel booking notification error: " . $notifErr->getMessage());
            }

            return $this->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to cancel booking',
                'error' => 'An error occurred. Please try again.'
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
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get schedule data for calendar view
     * Returns bookings organized by date and time for easy calendar rendering
     */
    public function getSchedule(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            // Get date range parameters (default to current week)
            $startDate = $request->getQuery('start_date') ?? date('Y-m-d');
            $endDate = $request->getQuery('end_date') ?? date('Y-m-d', strtotime('+7 days'));
            $facilityId = $request->getQuery('facility_id');

            // Build filters
            $filters = [
                'start_date' => $startDate,
                'end_date' => $endDate
            ];

            if ($facilityId) {
                // Verify facility belongs to owner
                $facility = $this->getFacilityModel()->find((int)$facilityId);
                if (!$facility || $facility['owner_id'] != $ownerId) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Facility not found'
                    ], 404);
                }
                $filters['facility_id'] = $facilityId;
            }

            // Get bookings
            $bookings = $this->getBookingModel()->searchBookings($ownerId, $filters);

            // Get all owner's facilities for the dropdown
            $facilities = $this->getFacilityModel()->getByOwnerId($ownerId);

            // Get blocked dates for the date range
            $facilityIds = array_column($facilities, 'id');
            if ($facilityId) {
                $facilityIds = [$facilityId];
            }
            $blockedDates = $this->getAvailModel()->getBlockedDatesForFacilities(
                $facilityIds, $startDate, $endDate
            );

            // Calculate statistics for the date range
            $totalBookings = count($bookings);
            $bookedSlots = count(array_filter($bookings, fn($b) =>
                in_array($b['status'], ['confirmed', 'pending'])));

            return $this->json([
                'success' => true,
                'schedule' => [
                    'bookings' => $bookings,
                    'facilities' => $facilities,
                    'blocked_dates' => $blockedDates,
                    'stats' => [
                        'total_bookings' => $totalBookings,
                        'booked_slots' => $bookedSlots,
                        'available_slots' => 0,
                        'occupancy_rate' => $bookedSlots > 0 ? round(($bookedSlots / max($totalBookings, 1)) * 100) : 0
                    ],
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load schedule',
                'error' => 'An error occurred. Please try again.'
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

            // Send notification to the user who made the booking
            try {
                $notificationModel = new \App\Models\Notification();
                $facilityName = $facility['name'] ?? 'Ground';
                $bookingDate = isset($booking['booking_date'])
                    ? date('F j, Y', strtotime($booking['booking_date']))
                    : '';

                $statusConfig = [
                    'confirmed' => [
                        'type' => 'booking_approved',
                        'title' => 'Booking Approved! ✅',
                        'message' => "Great news! Your booking for {$facilityName} on {$bookingDate} has been approved by the ground owner. See you there!"
                    ],
                    'cancelled' => [
                        'type' => 'booking_rejected',
                        'title' => 'Booking Declined ❌',
                        'message' => "Unfortunately, your booking for {$facilityName} on {$bookingDate} has been declined by the ground owner. Please try another time slot or facility."
                    ],
                    'completed' => [
                        'type' => 'booking_confirmation',
                        'title' => 'Booking Completed',
                        'message' => "Your booking for {$facilityName} on {$bookingDate} has been marked as completed. We hope you had a great time! Please leave a review."
                    ],
                    'no_show' => [
                        'type' => 'booking_cancelled',
                        'title' => 'Marked as No-Show',
                        'message' => "Your booking for {$facilityName} on {$bookingDate} was marked as no-show by the ground owner."
                    ]
                ];

                if (isset($statusConfig[$newStatus])) {
                    $config = $statusConfig[$newStatus];
                    $notificationModel->createNotification(
                        (int)$booking['user_id'],
                        $config['type'],
                        $config['title'],
                        $config['message'],
                        [
                            'booking_id' => $bookingId,
                            'facility_id' => $booking['facility_id'],
                            'facility_name' => $facilityName,
                            'new_status' => $newStatus,
                            'booking_date' => $booking['booking_date'] ?? null
                        ]
                    );
                }
            } catch (\Exception $notifErr) {
                error_log("Booking status notification error: " . $notifErr->getMessage());
                // Don't fail the status update if notification fails
            }

            return $this->json([
                'success' => true,
                'message' => 'Booking status updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update booking status',
                'error' => 'An error occurred. Please try again.'
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
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Update ground owner profile (users table + ground_owner_profiles table)
     */
    public function updateProfile(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $data    = $request->getJsonBody();

            // --- Update users table fields ---
            $userFields = [];
            foreach (['first_name', 'last_name', 'phone'] as $f) {
                if (array_key_exists($f, $data)) {
                    $userFields[$f] = trim($data[$f]);
                }
            }
            if (!empty($userFields)) {
                $userModel = new \App\Models\User();
                $userModel->update($ownerId, $userFields);
            }

            // --- Update ground_owner_profiles table ---
            $profileModel = new \App\Models\GroundOwnerProfile();
            $profileModel->updateProfile($ownerId, $data);

            return $this->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Deactivate the ground owner's own account.
     * POST /api/ground-owner/deactivate
     */
    public function deactivateAccount(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            $userModel = new \App\Models\User();
            $success   = $userModel->update($ownerId, ['status' => 'inactive']);

            if (!$success) {
                return $this->json(['success' => false, 'message' => 'Failed to deactivate account. Please try again.'], 500);
            }

            // Destroy session — owner is immediately logged out
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();

            return $this->json(['success' => true, 'message' => 'Account deactivated.']);

        } catch (\Exception $e) {
            error_log(__METHOD__ . ' error: ' . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to deactivate account. Please try again.'], 500);
        }
    }

    /**
     * Upload ground owner avatar / profile picture
     * POST /api/ground-owner/upload-avatar
     */
    public function uploadAvatar(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        if (empty($_FILES['avatar'])) {
            return $this->json(['success' => false, 'message' => 'No file uploaded'], 400);
        }

        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['success' => false, 'message' => 'Upload error'], 400);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime    = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed)) {
            return $this->json(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed'], 400);
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return $this->json(['success' => false, 'message' => 'File too large (max 5 MB)'], 400);
        }

        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'owner_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $dir      = ROOT_PATH . '/public/assets/images/owners/';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return $this->json(['success' => false, 'message' => 'Failed to save file'], 500);
        }

        $path      = '/public/assets/images/owners/' . $filename;
        $userModel = new \App\Models\User();
        $userModel->update((int)$_SESSION['user_id'], ['profile_picture' => $path]);

        return $this->json(['success' => true, 'avatar_url' => $path]);
    }

    // ======================
    // REVIEWS MANAGEMENT
    // ======================

    /**
     * Get all reviews for ground owner's facilities (includes reply data)
     */
    public function getReviews(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId     = $_SESSION['user_id'];
            $reviewModel = new \App\Models\FacilityReview();
            $reviews     = $reviewModel->getByOwnerIdWithReplies($ownerId);
            $stats       = $reviewModel->getOwnerReviewStatsDetailed($ownerId);

            return $this->json([
                'success' => true,
                'reviews' => $reviews,
                'stats'   => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load reviews',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get review statistics for owner's facilities
     */
    public function getReviewStats(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId     = $_SESSION['user_id'];
            $reviewModel = new \App\Models\FacilityReview();
            $stats       = $reviewModel->getOwnerReviewStatsDetailed($ownerId);

            return $this->json([
                'success' => true,
                'stats'   => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load review statistics',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * POST   /api/ground-owner/reviews/{id}/reply  → create / update reply
     * DELETE /api/ground-owner/reviews/{id}/reply  → delete reply
     */
    public function respondToReview(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $reviewId    = (int) $request->getParam('id');
        $ownerId     = $_SESSION['user_id'];
        $reviewModel = new \App\Models\FacilityReview();
        $method      = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'POST');

        try {
            if ($method === 'DELETE') {
                $ok = $reviewModel->deleteReply($reviewId, $ownerId);
                if (!$ok) {
                    return $this->json(['success' => false, 'message' => 'Reply not found or not authorised'], 404);
                }
                return $this->json(['success' => true, 'message' => 'Reply deleted']);
            }

            // POST — create or update
            $data      = $request->getJsonBody();
            $replyText = trim($data['reply_text'] ?? '');

            if (empty($replyText)) {
                return $this->json(['success' => false, 'message' => 'Reply text is required'], 400);
            }
            if (mb_strlen($replyText) > 1000) {
                return $this->json(['success' => false, 'message' => 'Reply cannot exceed 1000 characters'], 400);
            }

            $ok = $reviewModel->saveReply($reviewId, $ownerId, $replyText);
            if (!$ok) {
                return $this->json(['success' => false, 'message' => 'Review not found or not authorised'], 404);
            }

            return $this->json([
                'success'    => true,
                'message'    => 'Reply saved successfully',
                'reply_text' => $replyText
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to save reply',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * POST /api/ground-owner/reviews/{id}/report
     */
    public function reportReview(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $reviewId    = (int) $request->getParam('id');
        $ownerId     = $_SESSION['user_id'];
        $reviewModel = new \App\Models\FacilityReview();

        try {
            $data        = $request->getJsonBody();
            $reason      = trim($data['reason']      ?? '');
            $description = trim($data['description'] ?? '') ?: null;

            if (empty($reason)) {
                return $this->json(['success' => false, 'message' => 'Reason is required'], 400);
            }

            $ok = $reviewModel->reportReview($reviewId, $ownerId, $reason, $description);
            if (!$ok) {
                return $this->json(['success' => false, 'message' => 'Could not submit report (already reported or invalid reason)'], 400);
            }

            return $this->json(['success' => true, 'message' => 'Review reported to admin']);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to report review',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    // ======================
    // MAINTENANCE MANAGEMENT
    // ======================

    /**
     * Get maintenance dashboard statistics
     */
    public function getMaintenanceStats(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $maintenanceModel = new \App\Models\MaintenanceTask();

            $stats = $maintenanceModel->getOwnerStats($ownerId);

            return $this->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load maintenance statistics',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get all maintenance tasks
     */
    public function getMaintenanceTasks(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $maintenanceModel = new \App\Models\MaintenanceTask();

            // Get filter parameters
            $filters = [
                'facility_id' => $request->getQuery('facility_id'),
                'status' => $request->getQuery('status'),
                'priority' => $request->getQuery('priority'),
                'start_date' => $request->getQuery('start_date'),
                'end_date' => $request->getQuery('end_date')
            ];

            // Remove empty filters
            $filters = array_filter($filters, fn($value) => !empty($value));

            $tasks = $maintenanceModel->getByOwnerId($ownerId, $filters);

            return $this->json([
                'success' => true,
                'tasks' => $tasks
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load maintenance tasks',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get active maintenance tasks
     */
    public function getActiveTasks(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $maintenanceModel = new \App\Models\MaintenanceTask();

            $tasks = $maintenanceModel->getActiveTasks($ownerId);

            return $this->json([
                'success' => true,
                'tasks' => $tasks
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load active tasks',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get overdue maintenance tasks
     */
    public function getOverdueTasks(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $maintenanceModel = new \App\Models\MaintenanceTask();

            $tasks = $maintenanceModel->getOverdueTasks($ownerId);

            return $this->json([
                'success' => true,
                'tasks' => $tasks
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load overdue tasks',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get maintenance calendar data
     */
    public function getMaintenanceCalendar(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $maintenanceModel = new \App\Models\MaintenanceTask();

            $startDate = $request->getQuery('start_date') ?? date('Y-m-01');
            $endDate = $request->getQuery('end_date') ?? date('Y-m-t');

            $tasks = $maintenanceModel->getCalendarTasks($ownerId, $startDate, $endDate);

            return $this->json([
                'success' => true,
                'tasks' => $tasks,
                'date_range' => [
                    'start' => $startDate,
                    'end' => $endDate
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load calendar data',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Create a new maintenance task
     */
    public function createMaintenanceTask(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            // Validate required fields
            $required = ['facility_id', 'title', 'task_type', 'scheduled_date', 'priority'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], 400);
                }
            }

            // Whitelist priority values
            $allowedPriorities = ['low', 'medium', 'high', 'urgent'];
            if (!in_array($data['priority'], $allowedPriorities, true)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Priority must be one of: ' . implode(', ', $allowedPriorities)
                ], 400);
            }

            // Validate scheduled_date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['scheduled_date'])) {
                return $this->json(['success' => false, 'message' => 'scheduled_date must be in YYYY-MM-DD format'], 400);
            }

            // Validate cost if provided
            if (isset($data['cost']) && $data['cost'] !== '' && $data['cost'] !== null) {
                if (!is_numeric($data['cost']) || (float)$data['cost'] < 0) {
                    return $this->json(['success' => false, 'message' => 'Cost must be a non-negative number'], 400);
                }
                $data['cost'] = (float)$data['cost'];
            }

            // Verify facility belongs to owner
            $facility = $this->getFacilityModel()->find((int)$data['facility_id']);
            if (!$facility || (int)$facility['owner_id'] !== (int)$ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Facility not found'
                ], 404);
            }

            $maintenanceModel = new \App\Models\MaintenanceTask();

            // Add owner_id to data
            $data['owner_id'] = $ownerId;

            $taskId = $maintenanceModel->createTask($data);

            if (!$taskId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create maintenance task'
                ], 500);
            }

            $task = $maintenanceModel->find($taskId);

            return $this->json([
                'success' => true,
                'message' => 'Maintenance task created successfully',
                'task' => $task
            ], 201);

        } catch (\Exception $e) {
            error_log("createMaintenanceTask error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to create maintenance task. Please try again.'
            ], 500);
        }
    }

    /**
     * Update a maintenance task
     */
    public function updateMaintenanceTask(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $taskId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];

            if (!$taskId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid task ID'
                ], 400);
            }

            $maintenanceModel = new \App\Models\MaintenanceTask();
            $task = $maintenanceModel->find($taskId);

            if (!$task || $task['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            // Remove fields that shouldn't be updated
            unset($data['id'], $data['owner_id'], $data['facility_id'], $data['created_at']);

            $success = $maintenanceModel->update($taskId, $data);

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update task'
                ], 500);
            }

            $updatedTask = $maintenanceModel->find($taskId);

            return $this->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'task' => $updatedTask
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update task',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a maintenance task
     */
    public function deleteMaintenanceTask(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $taskId = (int)$request->getParam('id');
            $ownerId = $_SESSION['user_id'];

            if (!$taskId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid task ID'
                ], 400);
            }

            $maintenanceModel = new \App\Models\MaintenanceTask();
            $task = $maintenanceModel->find($taskId);

            if (!$task || $task['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            $success = $maintenanceModel->delete($taskId);

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to delete task'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to delete task',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get task details with progress updates
     */
    public function getMaintenanceTaskDetails(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $taskId = (int)$request->getParam('id');
            $ownerId = $_SESSION['user_id'];

            if (!$taskId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid task ID'
                ], 400);
            }

            $maintenanceModel = new \App\Models\MaintenanceTask();
            $task = $maintenanceModel->getTaskWithProgress($taskId);

            if (!$task || $task['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'task' => $task
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load task details',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Add progress update to a task
     */
    public function addTaskProgress(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $taskId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];

            if (!$taskId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid task ID'
                ], 400);
            }

            if (empty($data['update_text'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Update text is required'
                ], 400);
            }

            $maintenanceModel = new \App\Models\MaintenanceTask();
            $task = $maintenanceModel->find($taskId);

            if (!$task || $task['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            $success = $maintenanceModel->addProgressUpdate(
                $taskId,
                $data['update_text'],
                $data['progress_percentage'] ?? 0
            );

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to add progress update'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Progress update added successfully'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to add progress update',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Complete a maintenance task
     */
    public function completeMaintenanceTask(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $taskId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];

            if (!$taskId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid task ID'
                ], 400);
            }

            $maintenanceModel = new \App\Models\MaintenanceTask();
            $task = $maintenanceModel->find($taskId);

            if (!$task || $task['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            // Update status to completed
            $success = $maintenanceModel->updateStatus($taskId, 'completed');

            // Update actual cost if provided
            if (!empty($data['actual_cost'])) {
                $maintenanceModel->updateCost($taskId, $data['actual_cost']);
            }

            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to complete task'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Task marked as completed'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to complete task',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get cost trends
     */
    public function getMaintenanceCostTrends(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $months = (int)($request->getQuery('months') ?? 6);

            $maintenanceModel = new \App\Models\MaintenanceTask();
            $trends = $maintenanceModel->getCostTrends($ownerId, $months);

            return $this->json([
                'success' => true,
                'trends' => $trends
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load cost trends',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    // ======================
    // INSPECTION MANAGEMENT
    // ======================

    /**
     * Get all inspections
     */
    public function getInspections(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $inspectionModel = new \App\Models\FacilityInspection();

            // Get filter parameters
            $filters = [
                'facility_id' => $request->getQuery('facility_id'),
                'status' => $request->getQuery('status'),
                'inspection_type' => $request->getQuery('inspection_type')
            ];

            // Remove empty filters
            $filters = array_filter($filters, fn($value) => !empty($value));

            $inspections = $inspectionModel->getByOwnerId($ownerId, $filters);

            return $this->json([
                'success' => true,
                'inspections' => $inspections
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load inspections',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get upcoming inspections
     */
    public function getUpcomingInspections(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $limit = (int)($request->getQuery('limit') ?? 10);

            $inspectionModel = new \App\Models\FacilityInspection();
            $inspections = $inspectionModel->getUpcoming($ownerId, $limit);

            return $this->json([
                'success' => true,
                'inspections' => $inspections
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load upcoming inspections',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Create a new inspection
     */
    public function createInspection(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            // Validate required fields
            $required = ['facility_id', 'inspection_type', 'inspection_date'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], 400);
                }
            }

            // Verify facility belongs to owner
            $facility = $this->getFacilityModel()->find((int)$data['facility_id']);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Facility not found'
                ], 404);
            }

            $inspectionModel = new \App\Models\FacilityInspection();

            // Add owner_id to data
            $data['owner_id'] = $ownerId;

            $inspectionId = $inspectionModel->createInspection($data);

            if (!$inspectionId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create inspection'
                ], 500);
            }

            $inspection = $inspectionModel->find($inspectionId);

            return $this->json([
                'success' => true,
                'message' => 'Inspection scheduled successfully',
                'inspection' => $inspection
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to create inspection',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get facility health scores
     */
    public function getFacilityHealthScores(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $inspectionModel = new \App\Models\FacilityInspection();

            $scores = $inspectionModel->getFacilityHealthScores($ownerId);

            return $this->json([
                'success' => true,
                'health_scores' => $scores
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load health scores',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    // ======================
    // EARNINGS MANAGEMENT
    // ======================

    /**
     * Get all earnings data for dashboard
     */
    public function getEarnings(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            // Initialize earnings model
            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            // Get all earnings data
            $rawData = $earningsModel->getAllEarningsData($ownerId);

            // Format trends data for charts
            $trends = [
                'labels' => [],
                'revenue' => [],
                'bookings' => []
            ];

            if (!empty($rawData['daily_trends'])) {
                foreach ($rawData['daily_trends'] as $trend) {
                    $trends['labels'][] = date('M d', strtotime($trend['date']));
                    $trends['revenue'][] = floatval($trend['revenue']);
                    $trends['bookings'][] = intval($trend['bookings']);
                }
            }

            // Format ground performance data
            $grounds = [];
            if (!empty($rawData['ground_performance'])) {
                foreach ($rawData['ground_performance'] as $ground) {
                    $grounds[] = [
                        'name' => $ground['name'],
                        'revenue' => floatval($ground['total_revenue']),
                        'bookings' => intval($ground['total_bookings']),
                        'occupancy' => rand(60, 95) // Dummy occupancy rate
                    ];
                }
            }

            // Format earnings breakdown
            $breakdown = [];
            if (!empty($rawData['earnings_breakdown'])) {
                foreach ($rawData['earnings_breakdown'] as $item) {
                    $breakdown[] = [
                        'period' => $item['period'],
                        'amount' => floatval($item['revenue']),
                        'change' => rand(-5, 15) // Dummy change percentage
                    ];
                }
            }

            // Calculate payment analytics
            $paymentAnalytics = $rawData['payment_analytics'] ?? [
                'total_transactions' => 0,
                'completed' => 0,
                'pending' => 0,
                'success_rate' => 0
            ];

            // Revenue sources (dummy data based on totals)
            $totalRev = $rawData['overview']['totalRevenue'] ?? 0;
            $sources = [
                ['name' => 'Ground Bookings', 'value' => $totalRev * 0.85],
                ['name' => 'Equipment Rental', 'value' => $totalRev * 0.10],
                ['name' => 'Other Services', 'value' => $totalRev * 0.05]
            ];

            // Peak hours (dummy data)
            $peakHours = [
                ['hour' => '6 AM', 'bookings' => 5],
                ['hour' => '7 AM', 'bookings' => 8],
                ['hour' => '8 AM', 'bookings' => 12],
                ['hour' => '5 PM', 'bookings' => 15],
                ['hour' => '6 PM', 'bookings' => 18],
                ['hour' => '7 PM', 'bookings' => 14],
                ['hour' => '8 PM', 'bookings' => 10]
            ];

            // Weekly trends (dummy data)
            $weeklyTrends = [
                ['name' => 'Mon', 'revenue' => 15000],
                ['name' => 'Tue', 'revenue' => 18000],
                ['name' => 'Wed', 'revenue' => 22000],
                ['name' => 'Thu', 'revenue' => 20000],
                ['name' => 'Fri', 'revenue' => 25000],
                ['name' => 'Sat', 'revenue' => 30000],
                ['name' => 'Sun', 'revenue' => 28000]
            ];

            // Financial summary
            $grossRevenue = $rawData['overview']['totalRevenue'] ?? 0;
            $platformCommission = $grossRevenue * 0.10; // 10% commission
            $processingFees = $grossRevenue * 0.02; // 2% processing
            $taxes = $grossRevenue * 0.05; // 5% tax
            $netEarnings = $grossRevenue - $platformCommission - $processingFees - $taxes;

            $financial = [
                'grossRevenue' => $grossRevenue,
                'platformCommission' => $platformCommission,
                'processingFees' => $processingFees,
                'taxes' => $taxes,
                'netEarnings' => $netEarnings
            ];

            $earningsData = [
                'overview' => $rawData['overview'],
                'trends' => $trends,
                'grounds' => $grounds,
                'transactions' => $rawData['recent_transactions'] ?? [],
                'breakdown' => $breakdown,
                'sources' => $sources,
                'paymentAnalytics' => [
                    'successRate' => $paymentAnalytics['success_rate'],
                    'avgProcessingTime' => 2.5,
                    'failedPayments' => $paymentAnalytics['total_transactions'] - $paymentAnalytics['completed'],
                    'refundsIssued' => 0
                ],
                'peakHours' => $peakHours,
                'weeklyTrends' => $weeklyTrends,
                'financial' => $financial
            ];

            return $this->json($earningsData);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load earnings data',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get earnings overview (summary cards)
     */
    public function getEarningsOverview(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            $overview = $earningsModel->getEarningsOverview($ownerId);

            return $this->json([
                'success' => true,
                'overview' => $overview
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load earnings overview',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get revenue trends for charts
     */
    public function getEarningsTrends(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $days = (int)($request->getQuery('days') ?? 30);

            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            $rawTrends = $earningsModel->getDailyTrends($ownerId, $days);

            // Format trends data for charts
            $trends = [
                'labels' => [],
                'revenue' => [],
                'bookings' => []
            ];

            if (!empty($rawTrends)) {
                foreach ($rawTrends as $trend) {
                    $trends['labels'][] = date('M d', strtotime($trend['date']));
                    $trends['revenue'][] = floatval($trend['revenue']);
                    $trends['bookings'][] = intval($trend['bookings']);
                }
            }

            return $this->json([
                'success' => true,
                'trends' => $trends
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load earnings trends',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get earnings breakdown by period
     */
    public function getEarningsBreakdown(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $period = $request->getQuery('period') ?? 'daily';

            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            $breakdown = $earningsModel->getEarningsBreakdown($ownerId, $period);

            return $this->json([
                'success' => true,
                'breakdown' => $breakdown
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load earnings breakdown',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get top performing grounds
     */
    public function getTopPerformingGrounds(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            $grounds = $earningsModel->getEarningsByFacility($ownerId);

            return $this->json([
                'success' => true,
                'grounds' => $grounds
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load ground performance',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get earnings transactions
     */
    public function getEarningsTransactions(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $limit = (int)($request->getQuery('limit') ?? 10);

            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            $transactions = $earningsModel->getRecentTransactions($ownerId, $limit);

            return $this->json([
                'success' => true,
                'transactions' => $transactions
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load transactions',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get payment analytics
     */
    public function getEarningsAnalytics(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];

            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            $analytics = $earningsModel->getPaymentAnalytics($ownerId);

            return $this->json([
                'success' => true,
                'analytics' => $analytics
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load payment analytics',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Add a walk-in customer transaction
     */
    public function addWalkInTransaction(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        try {
            $ownerId = $_SESSION['user_id'];
            $data = $request->getJsonBody();

            // Validate required fields
            $required = ['facility_id', 'amount', 'earning_date', 'payment_method'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], 400);
                }
            }

            $amount = floatval($data['amount']);
            if ($amount <= 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'Amount must be greater than 0'
                ], 400);
            }

            // Verify the facility belongs to this owner
            $facility = $this->getFacilityModel()->find((int)$data['facility_id']);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found or does not belong to you'
                ], 403);
            }

            $db = \Core\Database::getInstance()->getConnection();
            $earningsModel = new GroundOwnerEarnings($db);

            $id = $earningsModel->addWalkIn($ownerId, [
                'facility_id'    => (int)$data['facility_id'],
                'amount'         => $amount,
                'earning_date'   => $data['earning_date'],
                'payment_method' => $data['payment_method'],
                'notes'          => $data['notes'] ?? null
            ]);

            if (!$id) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to save transaction'
                ], 500);
            }

            return $this->json([
                'success' => true,
                'message' => 'Walk-in transaction recorded successfully',
                'id'      => $id
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to record walk-in transaction',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    // =============================================
    // NOTIFICATION METHODS
    // =============================================

    /**
     * Get notifications for ground owner
     */
    public function getNotifications(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $ownerId = $_SESSION['user_id'];

        try {
            $notificationModel = new GroundOwnerNotification();

            $type = $request->getQuery('type');
            $unreadOnly = $request->getQuery('unread') === 'true';
            $limit = (int) ($request->getQuery('limit') ?? 50);

            $notifications = $notificationModel->getByOwner($ownerId, $type, $unreadOnly, $limit);
            $stats = $notificationModel->getStats($ownerId);

            return $this->json([
                'success' => true,
                'notifications' => $notifications,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            error_log("Error getting notifications: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Error loading notifications',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $ownerId = $_SESSION['user_id'];

        try {
            $notificationModel = new GroundOwnerNotification();
            $stats = $notificationModel->getStats($ownerId);

            return $this->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error loading notification stats',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request, $id): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $ownerId = $_SESSION['user_id'];

        try {
            $notificationModel = new GroundOwnerNotification();
            $success = $notificationModel->markAsRead($id, $ownerId);

            return $this->json([
                'success' => $success,
                'message' => $success ? 'Notification marked as read' : 'Notification not found'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error updating notification',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $ownerId = $_SESSION['user_id'];

        try {
            $notificationModel = new GroundOwnerNotification();
            $notificationModel->markAllAsRead($ownerId);

            return $this->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error updating notifications',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function deleteNotification(Request $request, $id): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $ownerId = $_SESSION['user_id'];

        try {
            $notificationModel = new GroundOwnerNotification();
            $success = $notificationModel->deleteNotification($id, $ownerId);

            return $this->json([
                'success' => $success,
                'message' => $success ? 'Notification deleted' : 'Notification not found'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error deleting notification',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Clear all notifications
     */
    public function clearAllNotifications(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }

        $ownerId = $_SESSION['user_id'];

        try {
            $notificationModel = new GroundOwnerNotification();
            $notificationModel->clearAll($ownerId);

            return $this->json([
                'success' => true,
                'message' => 'All notifications cleared'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error clearing notifications',
                'error' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    // ── Availability & Blocked Dates ──────────────────────────────

    /**
     * GET /api/ground-owner/availability?facility_id=
     * Returns the 7-day weekly schedule for a facility.
     */
    public function getAvailability(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        try {
            $ownerId    = $_SESSION['user_id'];
            $facilityId = (int)$request->getQuery('facility_id');

            if (!$facilityId) {
                return $this->json(['success' => false, 'message' => 'facility_id required'], 400);
            }

            $facility = $this->getFacilityModel()->find($facilityId);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json(['success' => false, 'message' => 'Facility not found'], 404);
            }

            $schedule = $this->getAvailModel()->getWeeklySchedule($facilityId);

            return $this->json(['success' => true, 'schedule' => $schedule]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to get availability', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    /**
     * PUT /api/ground-owner/availability
     * Body: { facility_id, schedule: [{day_of_week, is_available, opening_time, closing_time, slot_duration, buffer_time}] }
     */
    public function saveWeeklyAvailability(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        try {
            $ownerId = $_SESSION['user_id'];
            $data    = $request->getBody();

            $facilityId = (int)($data['facility_id'] ?? 0);
            if (!$facilityId) {
                return $this->json(['success' => false, 'message' => 'facility_id required'], 400);
            }

            $facility = $this->getFacilityModel()->find($facilityId);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json(['success' => false, 'message' => 'Facility not found'], 404);
            }

            $schedule = $data['schedule'] ?? [];
            if (empty($schedule)) {
                return $this->json(['success' => false, 'message' => 'Schedule data required'], 400);
            }

            $this->getAvailModel()->saveWeeklySchedule($facilityId, $schedule);

            return $this->json(['success' => true, 'message' => 'Schedule saved successfully']);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to save schedule', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    /**
     * GET /api/ground-owner/blocked-dates?facility_id=
     */
    public function getBlockedDates(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        try {
            $ownerId    = $_SESSION['user_id'];
            $facilityId = (int)$request->getQuery('facility_id');

            if (!$facilityId) {
                return $this->json(['success' => false, 'message' => 'facility_id required'], 400);
            }

            $facility = $this->getFacilityModel()->find($facilityId);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json(['success' => false, 'message' => 'Facility not found'], 404);
            }

            $blockedDates = $this->getAvailModel()->getAllBlockedDates($facilityId);

            return $this->json(['success' => true, 'blocked_dates' => $blockedDates]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to get blocked dates', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }

    /**
     * POST /api/ground-owner/blocked-dates
     * Body: { facility_id, start_date, end_date, start_time?, end_time?, reason, notes? }
     */
    public function addBlockedDate(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        try {
            $ownerId = $_SESSION['user_id'];
            $data    = $request->getBody();

            $facilityId = (int)($data['facility_id'] ?? 0);
            if (!$facilityId) {
                return $this->json(['success' => false, 'message' => 'facility_id required'], 400);
            }

            $facility = $this->getFacilityModel()->find($facilityId);
            if (!$facility || $facility['owner_id'] != $ownerId) {
                return $this->json(['success' => false, 'message' => 'Facility not found'], 404);
            }

            if (empty($data['start_date']) || empty($data['end_date'])) {
                return $this->json(['success' => false, 'message' => 'start_date and end_date required'], 400);
            }

            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['start_date']) ||
                !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['end_date'])) {
                return $this->json(['success' => false, 'message' => 'Dates must be in YYYY-MM-DD format'], 400);
            }

            if ($data['end_date'] < $data['start_date']) {
                return $this->json(['success' => false, 'message' => 'end_date must not be before start_date'], 400);
            }

            $blockId = $this->getAvailModel()->addBlockedDate($facilityId, $ownerId, $data);

            return $this->json(['success' => true, 'message' => 'Dates blocked successfully', 'id' => $blockId], 201);

        } catch (\Exception $e) {
            error_log("addBlockedDate error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to block dates. Please try again.'], 500);
        }
    }

    /**
     * DELETE /api/ground-owner/blocked-dates/{id}
     */
    public function removeBlockedDate(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        try {
            $ownerId = $_SESSION['user_id'];
            $blockId = (int)$request->getParam('id');

            if (!$blockId) {
                return $this->json(['success' => false, 'message' => 'Block ID required'], 400);
            }

            $removed = $this->getAvailModel()->removeBlockedDate($blockId, $ownerId);

            if (!$removed) {
                return $this->json(['success' => false, 'message' => 'Block not found or unauthorized'], 404);
            }

            return $this->json(['success' => true, 'message' => 'Blocked date removed']);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to remove blocked date', 'error' => 'An error occurred. Please try again.'], 500);
        }
    }
}