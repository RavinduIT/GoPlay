<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Coach;
use App\Models\CoachBooking;

/**
 * Coach Controller
 * 
 * Handles coach-related operations
 */
class CoachController extends BaseController
{
    /**
     * Display all coaches
     */
    public function index(Request $request): Response
    {
        return $this->view('booking/book-coach');
    }

    /**
     * Display coach profile
     */
    public function profile(Request $request): Response
    {
        $id = $request->getParam('id');
        return $this->view('coach/profile', ['id' => $id]);
    }

    /**
     * Book a coach
     */
    public function book(Request $request): Response
    {
        return $this->view('booking/book-coach');
    }

    /**
     * Coach dashboard
     */
    public function dashboard(Request $request): Response
    {
        // Check if user is authenticated and is coach
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/dashboard');
    }

    /**
     * Coach profile page
     */
    public function profilePage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/profile');
    }

    /**
     * Coach sessions page
     */
    public function sessionsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/sessions');
    }

    /**
     * Coach clients page
     */
    public function clientsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/clients');
    }

    /**
     * Coach programs page
     */
    public function programsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/programs');
    }

    /**
     * Coach assessments page
     */
    public function assessmentsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/assessments');
    }

    /**
     * Coach earnings page
     */
    public function earningsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/earnings');
    }

    /**
     * Coach availability page
     */
    public function availabilityPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/availability');
    }

    /**
     * Coach reviews page
     */
    public function reviewsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/reviews');
    }

    /**
     * Coach resources page
     */
    public function resourcesPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/resources');
    }

    /**
     * Coach notifications page
     */
    public function notificationsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/notifications');
    }

    /**
     * Coach settings page
     */
    public function settingsPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/settings');
    }

    /**
     * Coach help page
     */
    public function helpPage(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }
        
        return $this->view('coach/help');
    }

    // API Methods

    /**
     * Get dashboard data
     */
    public function getDashboardData(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Mock data for demonstration
        $data = [
            'stats' => [
                'totalClients' => 45,
                'totalSessions' => 87,
                'monthlyEarnings' => 28450,
                'avgRating' => 4.9,
                'clientsGrowth' => 12,
                'sessionsGrowth' => 15,
                'earningsGrowth' => 8,
                'totalReviews' => 23
            ],
            'todaySchedule' => [
                [
                    'id' => 1,
                    'title' => 'Cricket Fundamentals',
                    'clientName' => 'Kavinda Ranasighe',
                    'time' => '10:00 AM',
                    'duration' => '1 hour',
                    'type' => 'individual',
                    'status' => 'upcoming',
                    'datetime' => date('Y-m-d H:i:s', strtotime('today 10:00'))
                ],
                [
                    'id' => 2,
                    'title' => 'Bowling Technique',
                    'clientName' => 'Group Session (5 students)',
                    'time' => '2:00 PM',
                    'duration' => '2 hours',
                    'type' => 'group',
                    'status' => 'upcoming',
                    'datetime' => date('Y-m-d H:i:s', strtotime('today 14:00'))
                ]
            ],
            'recentClients' => [
                [
                    'id' => 1,
                    'name' => 'Kavinda Ranasighe',
                    'avatar' => '/public/assets/images/student1.jpg',
                    'lastSession' => 'Yesterday',
                    'status' => 'active'
                ],
                [
                    'id' => 2,
                    'name' => 'Sanduni Rajapakse',
                    'avatar' => '/public/assets/images/student2.jpg',
                    'lastSession' => '2 days ago',
                    'status' => 'active'
                ]
            ],
            'upcomingSessions' => [
                [
                    'id' => 3,
                    'title' => 'Batting Practice',
                    'clientName' => 'Dilan Wijesinghe',
                    'date' => date('Y-m-d', strtotime('tomorrow')),
                    'time' => '9:00 AM',
                    'type' => 'individual'
                ]
            ],
            'notifications' => [
                [
                    'id' => 1,
                    'type' => 'session',
                    'message' => 'New session request from John Doe',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                    'read' => false
                ]
            ]
        ];

        return $this->json($data);
    }

    /**
     * Get coach profile
     */
    public function getProfile(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Mock profile data
        $profile = [
            'fullName' => 'Lasith Malinga',
            'email' => 'lasith.malinga@email.com',
            'phone' => '+94 71 123 4567',
            'location' => 'Colombo, Sri Lanka',
            'dateOfBirth' => 'August 28, 1983',
            'gender' => 'Male',
            'specialization' => 'Cricket Coach & Former International Player',
            'experience' => '5',
            'license' => 'Level 3 Certified',
            'languages' => 'English, Sinhala, Tamil',
            'hourlyRate' => '₹800 - ₹1,500',
            'bio' => 'Former international cricket player with over 15 years of professional experience.',
            'avatar' => '/public/assets/images/coach-avatar.jpg',
            'stats' => [
                'rating' => 4.9,
                'students' => 45,
                'years' => 5,
                'sessions' => 230
            ],
            'specializations' => ['Fast Bowling', 'Cricket Fundamentals', 'Match Strategy', 'Youth Development']
        ];

        return $this->json($profile);
    }

    /**
     * Update coach profile
     */
    public function updateProfile(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // In a real application, you would validate and save the profile data
        $data = $request->getBody();
        
        return $this->json(['message' => 'Profile updated successfully', 'data' => $data]);
    }

    /**
     * Get sidebar stats
     */
    public function getSidebarStats(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'activeClients' => 38,
            'monthSessions' => 12,
            'upcomingSchedule' => 5,
            'totalClients' => 45,
            'pendingReviews' => 3
        ];

        return $this->json($stats);
    }

    /**
     * Get notifications count
     */
    public function getNotificationsCount(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $counts = [
            'unread' => 5
        ];

        return $this->json($counts);
    }

    /**
     * Get sessions
     */
    public function getSessions(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Mock sessions data
        $sessions = [
            [
                'id' => 1,
                'title' => 'Cricket Fundamentals',
                'clientName' => 'Kavinda Ranasighe',
                'date' => date('Y-m-d'),
                'time' => '10:00 AM',
                'duration' => 60,
                'type' => 'individual',
                'status' => 'completed',
                'rate' => 800
            ]
        ];

        return $this->json($sessions);
    }

    /**
     * Create session
     */
    public function createSession(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->getBody();
        
        // Mock response
        $newSession = array_merge($data, ['id' => rand(1000, 9999)]);
        
        return $this->json($newSession, 201);
    }

    /**
     * Get clients
     */
    public function getClients(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Mock clients data
        $clients = [
            [
                'id' => 1,
                'name' => 'Kavinda Ranasighe',
                'email' => 'kavinda@email.com',
                'phone' => '+94 71 234 5678'
            ],
            [
                'id' => 2,
                'name' => 'Sanduni Rajapakse',
                'email' => 'sanduni@email.com',
                'phone' => '+94 71 345 6789'
            ]
        ];

        return $this->json($clients);
    }

    /**
     * Create client
     */
    public function createClient(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->getBody();
        
        // Mock response
        $newClient = array_merge($data, ['id' => rand(1000, 9999)]);
        
        return $this->json($newClient, 201);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request): Response
    {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        // Mock response for avatar upload
        return $this->json([
            'message' => 'Avatar uploaded successfully',
            'avatarUrl' => '/public/assets/images/coach-avatar-new.jpg'
        ]);
    }

    /**
     * Get all available coaches for booking
     */
    public function getCoachesForBooking(Request $request): Response
    {
        try {
            $coachModel = new Coach();
            
            // Get query parameters
            $searchQuery = $request->getQuery('search') ?? '';
            $sport = $request->getQuery('sport') ?? '';
            $experience = $request->getQuery('experience') ?? '';
            $price = $request->getQuery('price') ?? '';
            $sortBy = $request->getQuery('sort') ?? 'rating';
            
            // Prepare filters
            $filters = [];
            if (!empty($sport)) {
                $filters['sport'] = $sport;
            }
            if (!empty($experience)) {
                $filters['experience'] = $experience;
            }
            if (!empty($price)) {
                $filters['price'] = $price;
            }
            
            // Get coaches based on search and filters
            if (!empty($searchQuery) || !empty($filters)) {
                $coaches = $coachModel->search($searchQuery, $filters);
            } else {
                $coaches = $coachModel->getAvailable();
            }
            
            // Format coaches data for the UI
            $formattedCoaches = [];
            foreach ($coaches as $coach) {
                $formattedCoaches[] = [
                    'id' => $coach['id'],
                    'name' => $coach['first_name'] . ' ' . $coach['last_name'],
                    'sport' => $coach['sport_name'] ?? 'General',
                    'experience' => $coach['experience_years'] . ' years',
                    'rating' => round($coach['rating'], 1),
                    'reviews' => $coach['total_reviews'],
                    'price' => $coach['hourly_rate'],
                    'location' => $coach['location'],
                    'bio' => $coach['bio'],
                    'specialties' => !empty($coach['specializations']) ? explode(', ', $coach['specializations']) : [],
                    'certifications' => !empty($coach['certifications']) ? explode(', ', $coach['certifications']) : [],
                    'profile_picture' => $coach['profile_picture'] ?? null
                ];
            }
            
            return $this->json([
                'success' => true,
                'coaches' => $formattedCoaches,
                'total' => count($formattedCoaches)
            ]);
            
        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch coaches',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get sports categories for filter dropdown
     */
    public function getSportsCategories(Request $request): Response
    {
        try {
            $coachModel = new Coach();
            $categories = $coachModel->getSportsCategories();
            
            return $this->json([
                'success' => true,
                'categories' => $categories
            ]);
            
        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch sports categories',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get single coach details
     */
    public function getCoachDetails(Request $request): Response
    {
        try {
            $id = $request->getParam('id'); // This is correct for route params like /api/coaches/{id}
            if (!$id) {
                return $this->json(['error' => 'Coach ID is required'], 400);
            }
            
            $coachModel = new Coach();
            $coach = $coachModel->getWithDetails((int)$id);
            
            if (!$coach) {
                return $this->json(['error' => 'Coach not found'], 404);
            }
            
            // Get reviews for this coach
            $reviews = $coachModel->getReviews((int)$id);
            
            $formattedCoach = [
                'id' => $coach['id'],
                'name' => $coach['first_name'] . ' ' . $coach['last_name'],
                'email' => $coach['email'],
                'phone' => $coach['phone'],
                'sport' => $coach['sport_name'] ?? 'General',
                'experience' => $coach['experience_years'] . ' years',
                'rating' => round($coach['rating'], 1),
                'reviews' => $coach['total_reviews'],
                'price' => $coach['hourly_rate'],
                'location' => $coach['location'],
                'bio' => $coach['bio'],
                'specialties' => !empty($coach['specializations']) ? explode(', ', $coach['specializations']) : [],
                'certifications' => !empty($coach['certifications']) ? explode(', ', $coach['certifications']) : [],
                'profile_picture' => $coach['profile_picture'] ?? null,
                'total_sessions' => $coach['total_sessions'],
                'reviews_list' => array_map(function($review) {
                    return [
                        'rating' => $review['rating'],
                        'review_text' => $review['review_text'],
                        'reviewer_name' => $review['first_name'] . ' ' . $review['last_name'],
                        'created_at' => $review['created_at']
                    ];
                }, $reviews)
            ];
            
            return $this->json([
                'success' => true,
                'coach' => $formattedCoach
            ]);

        } catch (Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch coach details',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new coach booking (API endpoint)
     *
     * POST /api/coach-bookings
     */
    public function createCoachBooking(Request $request): Response
    {
        try {
            // Get user ID from session
            session_start();
            if (!isset($_SESSION['user_id'])) {
                return $this->json([
                    'success' => false,
                    'error' => 'You must be logged in to book a session'
                ], 401);
            }

            $userId = $_SESSION['user_id'];

            // Get booking data from request
            $data = $request->getBody();

            // Enhanced debug logging
            error_log("=== Coach Booking Request ===");
            error_log("Request Method: " . $request->getMethod());
            error_log("Content-Type: " . ($request->getHeader('content-type') ?? 'not set'));
            error_log("Request Body Data: " . json_encode($data));
            error_log("Data is array: " . (is_array($data) ? 'yes' : 'no'));
            error_log("Data count: " . count($data));

            // If data is empty, try alternative methods
            if (empty($data)) {
                error_log("Body is empty, trying getJsonBody()");
                $data = $request->getJsonBody();
                error_log("JsonBody Data: " . json_encode($data));
            }

            // Validate required fields
            $required = ['coach_id', 'booking_date', 'start_time', 'session_type', 'total_amount'];
            $missing = [];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $missing[] = $field;
                }
            }

            if (!empty($missing)) {
                error_log("Missing fields: " . implode(', ', $missing));
                error_log("Available fields: " . implode(', ', array_keys($data)));
                return $this->json([
                    'success' => false,
                    'error' => "Missing required fields: " . implode(', ', $missing),
                    'received_data' => array_keys($data),
                    'debug' => [
                        'content_type' => $request->getHeader('content-type'),
                        'method' => $request->getMethod(),
                        'body_count' => count($data)
                    ]
                ], 400);
            }

            // Calculate end time (default 1 hour)
            $duration = isset($data['duration']) ? (int)$data['duration'] : 60;
            $startTime = $data['start_time'];
            $endTime = date('H:i', strtotime($startTime) + ($duration * 60));

            // Check if time slot is available
            $coachBookingModel = new CoachBooking();
            $isAvailable = $coachBookingModel->isTimeSlotAvailable(
                (int)$data['coach_id'],
                $data['booking_date'],
                $startTime,
                $endTime
            );

            if (!$isAvailable) {
                return $this->json([
                    'success' => false,
                    'error' => 'This time slot is not available'
                ], 400);
            }

            // Create booking
            $bookingData = [
                'user_id' => $userId,
                'coach_id' => (int)$data['coach_id'],
                'booking_date' => $data['booking_date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'session_type' => $data['session_type'],
                'total_amount' => (float)$data['total_amount'],
                'special_requests' => $data['special_requests'] ?? ''
            ];

            $bookingId = $coachBookingModel->createBooking($bookingData);

            if ($bookingId) {
                return $this->json([
                    'success' => true,
                    'message' => 'Booking created successfully',
                    'booking_id' => $bookingId
                ], 201);
            } else {
                return $this->json([
                    'success' => false,
                    'error' => 'Failed to create booking'
                ], 500);
            }

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's coach bookings (API endpoint)
     *
     * GET /api/my-coach-bookings
     */
    public function getMyBookings(Request $request): Response
    {
        try {
            session_start();
            if (!isset($_SESSION['user_id'])) {
                return $this->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }

            $coachBookingModel = new CoachBooking();
            $bookings = $coachBookingModel->getBookingsByUser($_SESSION['user_id']);

            return $this->json([
                'success' => true,
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch bookings',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a coach booking (API endpoint)
     *
     * PUT /api/coach-bookings/{id}/cancel
     */
    public function cancelCoachBooking(Request $request): Response
    {
        try {
            session_start();
            if (!isset($_SESSION['user_id'])) {
                return $this->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 401);
            }

            $bookingId = $request->getParam('id');
            if (!$bookingId) {
                return $this->json([
                    'success' => false,
                    'error' => 'Booking ID is required'
                ], 400);
            }

            $coachBookingModel = new CoachBooking();

            // Verify booking belongs to user
            $booking = $coachBookingModel->find((int)$bookingId);
            if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
                return $this->json([
                    'success' => false,
                    'error' => 'Booking not found or unauthorized'
                ], 404);
            }

            // Cancel the booking
            $success = $coachBookingModel->cancelBooking((int)$bookingId);

            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Booking cancelled successfully'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'error' => 'Failed to cancel booking'
                ], 500);
            }

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get coach's own bookings (for coach dashboard)
     *
     * GET /api/coach/bookings
     */
    public function getCoachOwnBookings(Request $request): Response
    {
        try {
            session_start();
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
                return $this->json([
                    'success' => false,
                    'error' => 'Unauthorized - Coach access required'
                ], 401);
            }

            $userId = $_SESSION['user_id'];

            // Get coach record for this user
            $coachModel = new Coach();
            $coach = $coachModel->where(['user_id' => $userId]);

            if (empty($coach)) {
                return $this->json([
                    'success' => false,
                    'error' => 'Coach profile not found'
                ], 404);
            }

            $coachId = $coach[0]['id'];

            // Get bookings for this coach
            $coachBookingModel = new CoachBooking();
            $bookings = $coachBookingModel->getBookingsByCoach($coachId);

            // Get statistics
            $stats = $coachBookingModel->getCoachBookingStats($coachId);

            return $this->json([
                'success' => true,
                'bookings' => $bookings,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'Failed to fetch bookings',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark session as completed (coach only)
     *
     * PUT /api/coach/bookings/{id}/complete
     */
    public function markSessionCompleted(Request $request): Response
    {
        try {
            session_start();
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $bookingId = $request->getParam('id');
            $coachBookingModel = new CoachBooking();

            // Verify booking belongs to this coach
            $booking = $coachBookingModel->find((int)$bookingId);
            if (!$booking) {
                return $this->json(['success' => false, 'error' => 'Booking not found'], 404);
            }

            // Get coach ID
            $coachModel = new Coach();
            $coach = $coachModel->where(['user_id' => $_SESSION['user_id']]);
            if (empty($coach) || $booking['coach_id'] != $coach[0]['id']) {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $success = $coachBookingModel->updateStatus((int)$bookingId, 'completed');

            return $this->json([
                'success' => $success,
                'message' => $success ? 'Session marked as completed' : 'Failed to update'
            ]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancel session (coach only)
     *
     * PUT /api/coach/bookings/{id}/cancel
     */
    public function coachCancelSession(Request $request): Response
    {
        try {
            session_start();
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $bookingId = $request->getParam('id');
            $coachBookingModel = new CoachBooking();

            // Verify booking belongs to this coach
            $booking = $coachBookingModel->find((int)$bookingId);
            if (!$booking) {
                return $this->json(['success' => false, 'error' => 'Booking not found'], 404);
            }

            // Get coach ID
            $coachModel = new Coach();
            $coach = $coachModel->where(['user_id' => $_SESSION['user_id']]);
            if (empty($coach) || $booking['coach_id'] != $coach[0]['id']) {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $success = $coachBookingModel->cancelBooking((int)$bookingId);

            return $this->json([
                'success' => $success,
                'message' => $success ? 'Session cancelled' : 'Failed to cancel'
            ]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}