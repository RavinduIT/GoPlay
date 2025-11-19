<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\SportsFacility;
use App\Models\SportsCategory;
use App\Models\GroundBooking;

/**
 * Booking Controller
 * 
 * Handles booking operations for facilities and coaches
 */
class BookingController extends BaseController
{
    private ?SportsFacility $facilityModel = null;
    private ?SportsCategory $categoryModel = null;
    private ?GroundBooking $groundBookingModel = null;
    
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

    private function getGroundBookingModel(): GroundBooking
    {
        if ($this->groundBookingModel === null) {
            $this->groundBookingModel = new GroundBooking();
        }
        return $this->groundBookingModel;
    }
    
    /**
     * Display booking form for ground with available facilities
     */
    public function bookGround(Request $request): Response
    {
        try {
            // Get filter parameters
            $filters = [
                'sport_category' => $request->getQuery('sport'),
                'city' => $request->getQuery('city'),
                'min_rate' => $request->getQuery('min_rate'),
                'max_rate' => $request->getQuery('max_rate'),
                'date' => $request->getQuery('date'),
                'time' => $request->getQuery('time')
            ];
            
            // Remove empty filters
            $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');
            
            // Get available sports facilities
            $facilities = $this->getFacilityModel()->getAvailableFacilities($filters);
            
            // Get sports categories for filters
            $categories = $this->getCategoryModel()->getAllActive();
            
            // Get unique cities for filter dropdown
            $cities = $this->getFacilityModel()->getUniqueCities();
            
            return $this->view('booking/book-ground', [
                'facilities' => $facilities,
                'categories' => $categories,
                'cities' => $cities,
                'currentFilters' => $filters
            ]);
            
        } catch (\Exception $e) {
            // Log error and show fallback
            error_log("Book ground page error: " . $e->getMessage());
            
            return $this->view('booking/book-ground', [
                'facilities' => [],
                'categories' => [],
                'cities' => [],
                'currentFilters' => [],
                'error' => 'Unable to load facilities. Please try again later.'
            ]);
        }
    }

   

    /**
     * Display ground details
     */
    public function groundDetails(Request $request): Response
    {
        $id = $request->getQuery('id');
        return $this->view('booking/ground-details', ['id' => $id]);
    }

    /**
     * Display user's bookings
     */
    public function myBookings(Request $request): Response
    {
        return $this->view('booking/my-bookings');
    }

    /**
     * Handle ground booking submission
     */
    public function createGroundBooking(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return $this->json([
                'success' => false,
                'message' => 'Please login to make a booking'
            ], 401);
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

            // Validate booking date (must be today or in the future)
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

            if (!$this->getGroundBookingModel()->isTimeSlotAvailable($facilityId, $bookingDate, $startTime, $endTime)) {
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

            $bookingId = $this->getGroundBookingModel()->createBooking($bookingData);

            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create booking'
                ], 500);
            }

            // Get the created booking with details
            $booking = $this->getGroundBookingModel()->find($bookingId);

            return $this->json([
                'success' => true,
                'message' => 'Ground booked successfully! No payment required.',
                'booking' => $booking,
                'booking_id' => $bookingId
            ], 201);

        } catch (\Exception $e) {
            error_log("Ground booking error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check time slot availability for a facility
     */
    public function checkAvailability(Request $request): Response
    {
        try {
            $facilityId = (int)$request->getQuery('facility_id');
            $date = $request->getQuery('date');
            $startTime = $request->getQuery('start_time');
            $endTime = $request->getQuery('end_time');

            if (!$facilityId || !$date || !$startTime || !$endTime) {
                return $this->json([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ], 400);
            }

            $available = $this->getGroundBookingModel()->isTimeSlotAvailable($facilityId, $date, $startTime, $endTime);

            return $this->json([
                'success' => true,
                'available' => $available,
                'message' => $available ? 'Time slot is available' : 'Time slot is not available'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error checking availability'
            ], 500);
        }
    }

    /**
     * API: Get all available grounds
     */
    public function getGrounds(Request $request): Response
    {
        try {
            // Get filter parameters
            $filters = [
                'sport_category' => $request->getQuery('sport'),
                'city' => $request->getQuery('city'),
                'min_rate' => $request->getQuery('min_rate'),
                'max_rate' => $request->getQuery('max_rate'),
                'search' => $request->getQuery('search')
            ];
            
            // Remove empty filters
            $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');
            
            // Get available sports facilities
            $facilities = $this->getFacilityModel()->getAvailableFacilities($filters);
            
            return $this->json([
                'success' => true,
                'data' => $facilities
            ]);
            
        } catch (\Exception $e) {
            error_log("Get grounds API error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Unable to load facilities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get specific ground details by ID
     */
    public function getGroundDetails(Request $request): Response
    {
        try {
            $id = $request->getParam('id');
            if (!$id) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground ID is required'
                ], 400);
            }

            // Get facility details with reviews and bookings
            $facility = $this->getFacilityModel()->getDetailedFacility($id);
            
            if (!$facility) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }

            return $this->json([
                'success' => true,
                'data' => $facility
            ]);
            
        } catch (\Exception $e) {
            error_log("Get ground details API error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Unable to load ground details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Alternative endpoint for ground by ID (for compatibility)
     */
    public function getGroundById(Request $request): Response
    {
        return $this->getGroundDetails($request);
    }

    /**
     * Redirect /book/{id} to ground details page
     */
    public function redirectToGroundDetails(Request $request): Response
    {
        $id = $request->getParam('id');
        if (!$id) {
            return $this->redirect('/book-ground');
        }

        return $this->redirect('/ground-details?id=' . $id);
    }

    /**
     * Get reviews for a facility (public endpoint)
     */
    public function getFacilityReviews(Request $request): Response
    {
        try {
            $facilityId = $request->getQuery('facility_id');

            if (!$facilityId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Facility ID is required'
                ], 400);
            }

            $reviewModel = new \App\Models\FacilityReview();

            $reviews = $reviewModel->getByFacilityId((int)$facilityId);
            $stats = $reviewModel->getReviewStats((int)$facilityId);

            return $this->json([
                'success' => true,
                'reviews' => $reviews,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error loading reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}