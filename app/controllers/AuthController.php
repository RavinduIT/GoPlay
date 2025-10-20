<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show login page
     */
    public function login(Request $request): Response
    {
        $this->startSession();
        
        // Redirect if already logged in
        if (isset($_SESSION['user_id'])) {
            return $this->redirectToDashboard($_SESSION['user_type']);
        }

        return $this->view('auth/login', [
            'title' => 'Login - GoPlay Sports Platform'
        ]);
    }

    /**
     * Handle login
     */
    public function handleLogin(Request $request): Response
    {
        $this->startSession();

        try {
            $data = $request->getJsonBody();

            // Validate input
            if (empty($data['email']) || empty($data['password'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Email and password are required'
                ], 400);
            }

            // Get user by email
            $user = $this->userModel->getByEmail($data['email']);

            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Check if account is active
            if ($user['status'] !== 'active') {
                return $this->json([
                    'success' => false,
                    'message' => 'Account is ' . $user['status'] . '. Please contact support.'
                ], 403);
            }

            // Verify password
            if (!$this->userModel->verifyPassword($data['password'], $user['password_hash'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Record login
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $this->userModel->recordLogin($user['id'], $ipAddress, $userAgent);

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $user['email'],
                'type' => $user['user_type'],
                'avatar' => $user['profile_picture'] ?? null
            ];

            // Determine redirect URL
            $redirectUrl = $this->getDashboardUrl($user['user_type']);

            return $this->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email'],
                    'type' => $user['user_type']
                ]
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show signup page
     */
    public function signup(Request $request): Response
    {
        $this->startSession();
        
        // Redirect if already logged in
        if (isset($_SESSION['user_id'])) {
            return $this->redirectToDashboard($_SESSION['user_type']);
        }

        return $this->view('auth/signup', [
            'title' => 'Sign Up - GoPlay Sports Platform'
        ]);
    }

    /**
     * Handle registration
     */
    public function handleRegister(Request $request): Response
    {
        $this->startSession();

        try {
            $data = $request->getJsonBody();

            // Validate required fields
            $required = ['username', 'email', 'password', 'first_name', 'last_name'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => ucfirst($field) . ' is required'
                    ], 400);
                }
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid email format'
                ], 400);
            }

            // Validate password strength
            if (strlen($data['password']) < 8) {
                return $this->json([
                    'success' => false,
                    'message' => 'Password must be at least 8 characters long'
                ], 400);
            }

            // Check if email already exists
            $existingUser = $this->userModel->getByEmail($data['email']);
            if ($existingUser) {
                return $this->json([
                    'success' => false,
                    'message' => 'Email already registered'
                ], 409);
            }

            // Check if username already exists
            $sql = "SELECT id FROM users WHERE username = ?";
            $stmt = $this->userModel->query($sql, [$data['username']]);
            if ($stmt->fetch()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Username already taken'
                ], 409);
            }

            // Hash password
            $hashedPassword = $this->userModel->hashPassword($data['password']);

            // Create user
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => $hashedPassword,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'user_type' => 'user',
                'status' => 'active'
            ];

            $userId = $this->userModel->create($userData);

            if (!$userId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create account'
                ], 500);
            }

            // Record initial login
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $this->userModel->recordLogin($userId, $ipAddress, $userAgent);

            // Set session
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['user_type'] = 'user';
            $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'type' => 'user',
                'avatar' => null
            ];

            return $this->json([
                'success' => true,
                'message' => 'Account created successfully',
                'redirect' => '/',
                'user' => [
                    'id' => $userId,
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'email' => $data['email'],
                    'type' => 'user'
                ]
            ], 201);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): Response
    {
        $this->startSession();
        
        // Log the logout activity
        if (isset($_SESSION['user_id'])) {
            try {
                $sql = "INSERT INTO user_activity_log (user_id, activity_type, activity_description, ip_address) 
                        VALUES (?, 'logout', 'User logged out', ?)";
                $this->userModel->query($sql, [
                    $_SESSION['user_id'],
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
            } catch (\Exception $e) {
                error_log("Failed to log logout: " . $e->getMessage());
            }
        }

        // Clear session
        session_destroy();

        // Handle both GET and POST requests
        if ($request->getMethod() === 'POST') {
            return $this->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect' => '/login'
            ]);
        } else {
            return $this->redirect('/login');
        }
    }

    /**
     * Check authentication status (API)
     */
    public function checkAuth(Request $request): Response
    {
        $this->startSession();

        if (isset($_SESSION['user_id'])) {
            return $this->json([
                'authenticated' => true,
                'user' => $_SESSION['user'] ?? null
            ]);
        }

        return $this->json([
            'authenticated' => false
        ]);
    }

    /**
     * Get dashboard URL based on user type
     */
    private function getDashboardUrl(string $userType): string
    {
        $dashboardMap = [
            'admin' => '/admin/dashboard',
            'ground_owner' => '/ground-owner/dashboard',
            'coach' => '/coach/dashboard',
            'shop_owner' => '/shop-owner/dashboard',
            'user' => '/'
        ];

        return $dashboardMap[$userType] ?? '/';
    }

    /**
     * Redirect to appropriate dashboard
     */
    private function redirectToDashboard(string $userType): Response
    {
        return $this->redirect($this->getDashboardUrl($userType));
    }
}