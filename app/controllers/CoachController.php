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
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->redirect('/login');
        }

        return $this->viewWithoutLayout('coach/earnings');
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
     * Get coach profile (real DB data)
     */
    public function getProfile(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);

        if (!$coach) {
            return $this->json(['error' => 'Coach profile not found'], 404);
        }

        $profile = [
            'id'             => $coach['id'],
            'first_name'     => $coach['first_name'],
            'last_name'      => $coach['last_name'],
            'fullName'       => $coach['first_name'] . ' ' . $coach['last_name'],
            'email'          => $coach['email'],
            'phone'          => $coach['phone'] ?? '',
            'location'       => $coach['location'] ?? '',
            'bio'            => $coach['bio'] ?? '',
            'specializations'=> $coach['specializations'] ?? '',
            'experience'     => $coach['experience_years'] ?? 0,
            'hourlyRate'     => $coach['hourly_rate'] ?? 0,
            'sport'          => $coach['sport_name'] ?? '',
            'avatar'         => $coach['profile_picture'] ?? null,
            'stats' => [
                'rating'   => round((float)($coach['rating'] ?? 0), 1),
                'sessions' => (int)($coach['total_sessions'] ?? 0),
                'reviews'  => (int)($coach['total_reviews'] ?? 0),
                'years'    => (int)($coach['experience_years'] ?? 0),
            ],
        ];

        return $this->json(['success' => true, 'profile' => $profile]);
    }

    /**
     * Update coach profile (real DB update)
     */
    public function updateProfile(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->getJsonBody() ?: $request->getBody();

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach profile not found'], 404);
        }

        $allowed = [
            'first_name', 'last_name', 'email', 'phone',
            'bio', 'specializations', 'location', 'hourly_rate', 'experience_years'
        ];
        $filtered = array_intersect_key($data, array_flip($allowed));

        if (empty($filtered)) {
            return $this->json(['error' => 'No valid fields to update'], 400);
        }

        $coachModel->updateProfile((int)$coach['id'], (int)$_SESSION['user_id'], $filtered);

        return $this->json(['success' => true, 'message' => 'Profile updated successfully']);
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
     * Get clients derived from coach_bookings (real data).
     * GET /api/coach/clients
     */
    public function getClients(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $coachModel = new Coach();
            $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
            if (!$coach) {
                return $this->json(['error' => 'Coach profile not found'], 404);
            }

            $coachId = (int)$coach['id'];
            $clients = $coachModel->getClients($coachId);
            $stats   = $coachModel->getClientStats($coachId);

            // Enrich each client with a computed status
            $today = date('Y-m-d');
            foreach ($clients as &$c) {
                $daysSince = $c['last_session_date']
                    ? (int)floor((strtotime($today) - strtotime($c['last_session_date'])) / 86400)
                    : PHP_INT_MAX;
                $c['status'] = ($c['upcoming_sessions'] > 0 || $daysSince <= 30) ? 'active' : 'inactive';
                $c['days_since_last'] = $daysSince === PHP_INT_MAX ? null : $daysSince;
            }
            unset($c);

            return $this->json([
                'success' => true,
                'clients' => $clients,
                'stats'   => $stats,
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get booking history for a specific client of this coach.
     * GET /api/coach/clients/{id}
     */
    public function getClientDetail(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $userId     = (int)$request->getParam('id');
            $coachModel = new Coach();
            $coach      = $coachModel->getByUserId((int)$_SESSION['user_id']);
            if (!$coach) {
                return $this->json(['error' => 'Coach profile not found'], 404);
            }

            $coachId = (int)$coach['id'];
            $history = $coachModel->getClientBookingHistory($coachId, $userId);

            return $this->json(['success' => true, 'history' => $history]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload avatar (real file upload)
     */
    public function uploadAvatar(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        if (empty($_FILES['avatar'])) {
            return $this->json(['error' => 'No file uploaded'], 400);
        }

        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['error' => 'Upload error'], 400);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime    = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed)) {
            return $this->json(['error' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed'], 400);
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return $this->json(['error' => 'File too large (max 5 MB)'], 400);
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'coach_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $dir      = ROOT_PATH . '/public/assets/images/coaches/';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return $this->json(['error' => 'Failed to save file'], 500);
        }

        $path = '/public/assets/images/coaches/' . $filename;

        $coachModel = new Coach();
        $coachModel->updateAvatar((int)$_SESSION['user_id'], $path);

        return $this->json(['success' => true, 'avatarUrl' => $path]);
    }

    // =====================================================================
    // CERTIFICATES API
    // =====================================================================

    /** GET /api/coach/certificates */
    public function getCertificates(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $certs = $coachModel->getCertificates((int)$coach['id']);
        return $this->json(['success' => true, 'certificates' => $certs]);
    }

    /** POST /api/coach/certificates */
    public function addCertificate(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->getJsonBody() ?: $request->getBody();
        if (empty($data['title'])) {
            return $this->json(['error' => 'Title is required'], 400);
        }

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $id = $coachModel->addCertificate((int)$coach['id'], $data);
        return $this->json(['success' => true, 'id' => $id, 'message' => 'Certificate added'], 201);
    }

    /** PUT /api/coach/certificates/{id} */
    public function updateCertificate(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $certId = (int)$request->getParam('id');
        $data   = $request->getJsonBody() ?: $request->getBody();

        if (empty($data['title'])) {
            return $this->json(['error' => 'Title is required'], 400);
        }

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $ok = $coachModel->updateCertificate($certId, (int)$coach['id'], $data);
        return $this->json(['success' => $ok, 'message' => $ok ? 'Certificate updated' : 'Not found or unauthorized']);
    }

    /** DELETE /api/coach/certificates/{id} */
    public function deleteCertificate(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $certId = (int)$request->getParam('id');

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $ok = $coachModel->deleteCertificate($certId, (int)$coach['id']);
        return $this->json(['success' => $ok, 'message' => $ok ? 'Certificate deleted' : 'Not found or unauthorized']);
    }

    // =====================================================================
    // ACHIEVEMENTS API
    // =====================================================================

    /** GET /api/coach/achievements */
    public function getAchievements(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $achievements = $coachModel->getAchievements((int)$coach['id']);
        return $this->json(['success' => true, 'achievements' => $achievements]);
    }

    /** POST /api/coach/achievements */
    public function addAchievement(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->getJsonBody() ?: $request->getBody();
        if (empty($data['title'])) {
            return $this->json(['error' => 'Title is required'], 400);
        }

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $id = $coachModel->addAchievement((int)$coach['id'], $data);
        return $this->json(['success' => true, 'id' => $id, 'message' => 'Achievement added'], 201);
    }

    /** PUT /api/coach/achievements/{id} */
    public function updateAchievement(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $achId = (int)$request->getParam('id');
        $data  = $request->getJsonBody() ?: $request->getBody();

        if (empty($data['title'])) {
            return $this->json(['error' => 'Title is required'], 400);
        }

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $ok = $coachModel->updateAchievement($achId, (int)$coach['id'], $data);
        return $this->json(['success' => $ok, 'message' => $ok ? 'Achievement updated' : 'Not found or unauthorized']);
    }

    /** DELETE /api/coach/achievements/{id} */
    public function deleteAchievement(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $achId = (int)$request->getParam('id');

        $coachModel = new Coach();
        $coach = $coachModel->getByUserId((int)$_SESSION['user_id']);
        if (!$coach) {
            return $this->json(['error' => 'Coach not found'], 404);
        }

        $ok = $coachModel->deleteAchievement($achId, (int)$coach['id']);
        return $this->json(['success' => $ok, 'message' => $ok ? 'Achievement deleted' : 'Not found or unauthorized']);
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
            $age = $request->getQuery('age') ?? '';
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
            if (!empty($age)) {
                $filters['age'] = $age;
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
                    'age' => $coach['age'] ?? null,
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
            $this->startSession();
            if (!isset($_SESSION['user_id'])) {
                return $this->json([
                    'success' => false,
                    'error' => 'login_required'
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
            $this->startSession();
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
            $this->startSession();
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
            $this->startSession();
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $bookingId = $request->getParam('id');
            $coachBookingModel = new CoachBooking();

            $booking = $coachBookingModel->find((int)$bookingId);
            if (!$booking) {
                return $this->json(['success' => false, 'error' => 'Booking not found'], 404);
            }

            $coachModel = new Coach();
            $coach = $coachModel->where(['user_id' => $_SESSION['user_id']]);
            if (empty($coach) || $booking['coach_id'] != $coach[0]['id']) {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $success = $coachBookingModel->cancelBooking((int)$bookingId, 'coach');

            return $this->json([
                'success' => $success,
                'message' => $success ? 'Session cancelled' : 'Failed to cancel'
            ]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // =====================================================================
    // EARNINGS API
    // =====================================================================

    /**
     * Resolve coach ID from session user.
     * Returns the integer coach.id or null if not found.
     */
    private function resolveCoachId(): ?int
    {
        $coachModel = new Coach();
        $coach      = $coachModel->getByUserId((int)$_SESSION['user_id']);
        return $coach ? (int)$coach['id'] : null;
    }

    /**
     * GET /api/coach/earnings
     * Returns paginated earnings list + stats summary.
     */
    public function getEarnings(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $coachId = $this->resolveCoachId();
            if (!$coachId) return $this->json(['error' => 'Coach profile not found'], 404);

            $filters = [
                'dateRange'     => $request->getQuery('dateRange')     ?? 'month',
                'sessionType'   => $request->getQuery('sessionType')   ?? '',
                'paymentStatus' => $request->getQuery('paymentStatus') ?? '',
                'sortBy'        => $request->getQuery('sortBy')        ?? 'date_desc',
                'startDate'     => $request->getQuery('startDate')     ?? null,
                'endDate'       => $request->getQuery('endDate')       ?? null,
            ];
            $page  = max(1, (int)($request->getQuery('page')  ?? 1));
            $limit = max(1, min(50, (int)($request->getQuery('limit') ?? 10)));

            $bookingModel = new CoachBooking();
            $result       = $bookingModel->getEarningsList($coachId, $filters, $page, $limit);
            $stats        = $bookingModel->getEarningsStats($coachId, $filters);

            return $this->json([
                'success'     => true,
                'earnings'    => $result['data'],
                'stats'       => $stats,
                'total'       => $result['total'],
                'totalPages'  => $result['totalPages'],
                'currentPage' => $result['page'],
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/coach/earnings-trend?period=6months|12months|year
     */
    public function getEarningsTrend(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $coachId = $this->resolveCoachId();
            if (!$coachId) return $this->json(['error' => 'Coach profile not found'], 404);

            $period = $request->getQuery('period') ?? '6months';
            $data   = (new CoachBooking())->getEarningsTrend($coachId, $period);

            return $this->json(['success' => true] + $data);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/coach/session-breakdown
     */
    public function getSessionBreakdown(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $coachId = $this->resolveCoachId();
            if (!$coachId) return $this->json(['error' => 'Coach profile not found'], 404);

            $data = (new CoachBooking())->getSessionBreakdown($coachId);
            return $this->json(['success' => true] + $data);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/coach/earnings/{id}
     */
    public function getEarningDetail(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $coachId = $this->resolveCoachId();
            if (!$coachId) return $this->json(['error' => 'Coach profile not found'], 404);

            $id      = (int)$request->getParam('id');
            $session = (new CoachBooking())->getEarningById($id, $coachId);
            if (!$session) return $this->json(['error' => 'Session not found'], 404);

            return $this->json(['success' => true, 'session' => $session]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/coach/earnings/export  – CSV download
     */
    public function exportEarnings(Request $request): Response
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $coachId = $this->resolveCoachId();
            if (!$coachId) return $this->json(['error' => 'Coach profile not found'], 404);

            $filters = [
                'dateRange'     => $request->getQuery('dateRange')     ?? 'all',
                'sessionType'   => $request->getQuery('sessionType')   ?? '',
                'paymentStatus' => $request->getQuery('paymentStatus') ?? '',
                'sortBy'        => $request->getQuery('sortBy')        ?? 'date_desc',
                'startDate'     => $request->getQuery('startDate')     ?? null,
                'endDate'       => $request->getQuery('endDate')       ?? null,
            ];

            $result = (new CoachBooking())->getEarningsList($coachId, $filters, 1, 9999);
            $rows   = $result['data'];

            $csv  = "Date,Time,Client,Email,Session Type,Duration (hrs),Amount (LKR),Payment Status,Booking Status\n";
            foreach ($rows as $r) {
                $csv .= implode(',', [
                    '"' . $r['booking_date'] . '"',
                    '"' . $r['start_time'] . ' - ' . $r['end_time'] . '"',
                    '"' . addslashes($r['client_name'] ?? '') . '"',
                    '"' . ($r['client_email'] ?? '') . '"',
                    '"' . ucfirst($r['session_type'] ?? '') . '"',
                    $r['duration_hours'],
                    number_format((float)$r['total_amount'], 2, '.', ''),
                    '"' . ucfirst($r['payment_status'] ?? '') . '"',
                    '"' . ucfirst($r['status'] ?? '') . '"',
                ]) . "\n";
            }

            $response = new \Core\Response($csv, 200);
            $response->setHeader('Content-Type', 'text/csv');
            $response->setHeader('Content-Disposition', 'attachment; filename="coach-earnings-' . date('Y-m-d') . '.csv"');
            return $response;
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    // =====================================================================
    // PUBLIC-FACING COACH PROFILE PAGE
    // =====================================================================

    /**
     * Public coach profile page with booking form
     * GET /coach-profile/{id}
     */
    public function coachProfilePage(Request $request): Response
    {
        $id = $request->getParam('id');
        return $this->view('booking/coach-profile', [
            'title'       => 'Coach Profile - GoPlay Sports Platform',
            'coach_id'    => (int)$id,
        ]);
    }

    // =====================================================================
    // AVAILABILITY API
    // =====================================================================

    /**
     * Returns bookable time slots for a coach on a specific date.
     * GET /api/coaches/{id}/availability?date=YYYY-MM-DD
     */
    public function getCoachAvailability(Request $request): Response
    {
        try {
            $coachId = (int)$request->getParam('id');
            $date    = $request->getQuery('date');

            if (!$coachId || !$date) {
                return $this->json(['success' => false, 'error' => 'Coach ID and date required'], 400);
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $this->json(['success' => false, 'error' => 'Invalid date format (YYYY-MM-DD)'], 400);
            }

            // Past dates not allowed
            if ($date < date('Y-m-d')) {
                return $this->json(['success' => true, 'slots' => []]);
            }

            $bookingModel = new CoachBooking();
            $slots        = $bookingModel->getAvailableSlots($coachId, $date);

            return $this->json(['success' => true, 'slots' => $slots]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // =====================================================================
    // BOOKING API (user-facing)
    // =====================================================================

    /**
     * Update (reschedule) a coach booking by the booking user.
     * PUT /api/user/coach-bookings/{id}
     */
    public function updateCoachBooking(Request $request): Response
    {
        try {
            $this->startSession();
            if (!isset($_SESSION['user_id'])) {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $bookingId = (int)$request->getParam('id');
            $data      = $request->getJsonBody() ?: $request->getBody();

            $bookingModel = new CoachBooking();
            $booking      = $bookingModel->find($bookingId);

            if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
                return $this->json(['success' => false, 'error' => 'Booking not found'], 404);
            }

            if (!in_array($booking['status'], ['confirmed', 'pending'])) {
                return $this->json(['success' => false, 'error' => 'Only upcoming bookings can be rescheduled'], 400);
            }

            if (empty($data['booking_date']) || empty($data['start_time']) || empty($data['end_time'])) {
                return $this->json(['success' => false, 'error' => 'Date and time are required'], 400);
            }

            $success = $bookingModel->rescheduleBooking(
                $bookingId,
                $data['booking_date'],
                $data['start_time'],
                $data['end_time']
            );

            if (!$success) {
                return $this->json(['success' => false, 'error' => 'The selected time slot is not available'], 409);
            }

            return $this->json(['success' => true, 'message' => 'Session rescheduled successfully']);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * User cancels their own booking.
     * PUT /api/user/coach-bookings/{id}/cancel
     */
    public function userCancelCoachBooking(Request $request): Response
    {
        try {
            $this->startSession();
            if (!isset($_SESSION['user_id'])) {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $bookingId    = (int)$request->getParam('id');
            $bookingModel = new CoachBooking();
            $booking      = $bookingModel->find($bookingId);

            if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
                return $this->json(['success' => false, 'error' => 'Booking not found'], 404);
            }

            if (!in_array($booking['status'], ['confirmed', 'pending'])) {
                return $this->json(['success' => false, 'error' => 'Only upcoming bookings can be cancelled'], 400);
            }

            $data   = $request->getJsonBody() ?: [];
            $reason = $data['reason'] ?? '';

            $success = $bookingModel->cancelBooking($bookingId, 'user', $reason);
            return $this->json([
                'success' => $success,
                'message' => $success ? 'Booking cancelled successfully' : 'Cancellation failed',
            ]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user's own coach bookings list.
     * GET /api/user/coach-bookings
     */
    public function getUserCoachBookings(Request $request): Response
    {
        try {
            $this->startSession();
            if (!isset($_SESSION['user_id'])) {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $bookingModel = new CoachBooking();
            $bookings     = $bookingModel->getBookingsByUser((int)$_SESSION['user_id']);

            return $this->json(['success' => true, 'bookings' => $bookings]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // =====================================================================
    // COACH DASHBOARD SCHEDULE API
    // =====================================================================

    /**
     * Coach's own schedule (upcoming + today).
     * GET /api/coach/schedule
     */
    public function getCoachSchedule(Request $request): Response
    {
        try {
            $this->startSession();
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'coach') {
                return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $coachModel = new Coach();
            $coaches    = $coachModel->where(['user_id' => (int)$_SESSION['user_id']]);
            if (empty($coaches)) {
                return $this->json(['success' => false, 'error' => 'Coach profile not found'], 404);
            }
            $coachId = (int)$coaches[0]['id'];

            $bookingModel  = new CoachBooking();
            $today         = $bookingModel->getTodaySessions($coachId);
            $upcoming      = $bookingModel->getUpcomingBookings($coachId, 10);
            $weekStart     = date('Y-m-d', strtotime('Monday this week'));
            $weekSchedule  = $bookingModel->getWeekSchedule($coachId, $weekStart);
            $stats         = $bookingModel->getCoachBookingStats($coachId);

            return $this->json([
                'success'       => true,
                'today'         => $today,
                'upcoming'      => $upcoming,
                'week_schedule' => $weekSchedule,
                'stats'         => $stats,
            ]);

        } catch (\Exception $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}