<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;

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
}