<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\SportsFacility;
use App\Models\SportsCategory;
use App\Models\GroundBooking;
use App\Models\Notification;
use App\Models\GroundOwnerNotification;
use App\Models\User;

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

            // Check against owner-set availability (blocked dates + operating hours)
            $availModel = new \App\Models\FacilityAvailability();
            if ($availModel->isDateBlocked($facilityId, $bookingDate, $startTime, $endTime)) {
                return $this->json([
                    'success' => false,
                    'message' => 'This ground is not available on the selected date'
                ], 409);
            }
            if (!$availModel->isWithinOperatingHours($facilityId, $bookingDate, $startTime, $endTime)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Selected time is outside of the ground\'s operating hours'
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

            // Get facility details for notifications
            $facility = $this->getFacilityModel()->find($facilityId);
            $facilityName = $facility['name'] ?? 'Ground';
            $ownerId = $facility['owner_id'] ?? null;

<<<<<<< HEAD
            // Create notifications for user and ground owner
=======
            // Create notification for user
            error_log("=== CREATING BOOKING NOTIFICATION ===");
            error_log("User ID: {$userId}, Facility ID: {$facilityId}, Facility: {$facilityName}, Date: {$bookingDate}");
            error_log("Facility data: " . json_encode($facility));
            error_log("Owner ID from facility: " . ($ownerId ?? 'NULL'));

>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
            $notificationId = null;
            $ownerNotificationId = null;

            try {
                $notificationModel = new Notification();
<<<<<<< HEAD
                $notificationId = $notificationModel->createBookingPendingNotification($userId, [
                    'booking_id'    => $bookingId,
                    'facility_id'   => $facilityId,
                    'facility_name' => $facilityName,
                    'booking_date'  => $bookingDate,
                    'start_time'    => $startTime,
                    'end_time'      => $endTime
                ]);

                // Notify ground owner — skip if the owner is the one who made the booking
                if ($ownerId && (int)$ownerId !== (int)$userId) {
=======
                $formattedDate = date('Y-m-d', strtotime($bookingDate));
                $formattedTime = "{$startTime} to {$endTime}";
                $notificationId = $notificationModel->createBookingNotification($userId, [
                    'title' => 'Booking Submitted',
                    'message' => "Your booking request for {$facilityName} on {$formattedDate} at {$formattedTime} has been submitted and is awaiting approval from the ground owner.",
                    'booking_id' => $bookingId,
                    'facility_id' => $facilityId,
                    'facility_name' => $facilityName,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ]);
                error_log("User notification result: " . ($notificationId ? "Created ID {$notificationId}" : "FAILED"));

                // Create notification for ground owner
                if ($ownerId) {
                    error_log("Creating notification for owner ID: {$ownerId}");
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
                    $userModel = new User();
                    $user = $userModel->find($userId);
                    $ownerNotificationModel = new GroundOwnerNotification();
                    $ownerNotificationId = $ownerNotificationModel->createBookingNotification($ownerId, [
<<<<<<< HEAD
                        'booking_id'    => $bookingId,
                        'facility_name' => $facilityName,
                        'first_name'    => $user['first_name'] ?? '',
                        'last_name'     => $user['last_name'] ?? '',
                        'booking_date'  => $bookingDate
                    ]);
                }
            } catch (\Exception $e) {
                error_log("Notification creation error: " . $e->getMessage());
=======
                        'booking_id' => $bookingId,
                        'facility_name' => $facilityName,
                        'first_name' => $user['first_name'] ?? '',
                        'last_name' => $user['last_name'] ?? '',
                        'booking_date' => $bookingDate
                    ]);
                    error_log("Owner notification result: " . ($ownerNotificationId ? "Created ID {$ownerNotificationId}" : "FAILED"));
                } else {
                    error_log("WARNING: No owner_id found for facility {$facilityId} - skipping owner notification");
                }
            } catch (\Exception $e) {
                // Log notification error but don't fail the booking
                error_log("Notification creation error: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
            }

            return $this->json([
                'success' => true,
                'message' => 'Booking submitted! Awaiting ground owner approval.',
                'booking' => $booking,
                'booking_id' => $bookingId,
                'notifications' => [
                    'user_notification_id' => $notificationId ?? null,
                    'owner_notification_id' => $ownerNotificationId ?? null,
                    'owner_id' => $ownerId
                ]
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

            // Use getAvailabilityReason which checks all blocking conditions
            $reason    = $this->getFacilityModel()->getAvailabilityReason($facilityId, $date, $startTime, $endTime);
            $available = $reason === '';

            return $this->json([
                'success'   => true,
                'available' => $available,
                'reason'    => $reason,
                'message'   => $available ? 'Time slot is available' : $reason,
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

    /**
     * GET /api/facility/{id}/coaches
     * Public: coaches linked to a facility.
     */
    public function getFacilityCoaches(Request $request): Response
    {
        try {
            $facilityId = (int)$request->getParam('id');
            if (!$facilityId) {
                return $this->json(['success' => false, 'error' => 'Facility ID required'], 400);
            }

            $coaches = $this->getFacilityModel()->getLinkedCoaches($facilityId);
            return $this->json(['success' => true, 'coaches' => $coaches]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/facility/{id}/slots?date=YYYY-MM-DD
     * Returns 1-hour time slots for the day with availability status.
     */
    public function getFacilitySlots(Request $request): Response
    {
        try {
            $facilityId = (int)$request->getParam('id');
            $date       = $request->getQuery('date');

            if (!$facilityId || !$date) {
                return $this->json(['success' => false, 'error' => 'facility id and date are required'], 400);
            }

            // Validate date
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $this->json(['success' => false, 'error' => 'Invalid date format'], 400);
            }

            // Reject past dates
            if ($date < date('Y-m-d')) {
                return $this->json(['success' => false, 'error' => 'Date is in the past'], 400);
            }

            $slots = $this->getFacilityModel()->getSlotsForDay($facilityId, $date);

            return $this->json([
                'success' => true,
                'date'    => $date,
                'slots'   => $slots,
            ]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}