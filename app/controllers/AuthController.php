<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;
use App\Services\EmailService;

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
     * Handle registration - FIXED VERSION
     */
    public function handleRegister(Request $request): Response
    {
        $this->startSession();

        try {
            $data = $request->getJsonBody();

            // Validate required fields (username is now optional, will be auto-generated)
            $required = ['email', 'password', 'first_name', 'last_name', 'user_type'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'
                    ], 400);
                }
            }

            // Validate user_type
            $allowedTypes = ['user', 'coach', 'ground_owner', 'shop_owner'];
            if (!in_array($data['user_type'], $allowedTypes)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid user type'
                ], 400);
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

            // Generate username from email if not provided
            $username = $data['username'] ?? null;
            
            if (!$username) {
                // Extract username from email and make it unique
                $emailParts = explode('@', $data['email']);
                $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($emailParts[0]));
                $username = $baseUsername;
                
                // Check if username exists and add numbers if needed
                $counter = 1;
                while (true) {
                    $existingUsername = $this->userModel->where(['username' => $username]);
                    if (empty($existingUsername)) {
                        break;
                    }
                    $username = $baseUsername . $counter;
                    $counter++;
                    
                    // Prevent infinite loop
                    if ($counter > 1000) {
                        $username = $baseUsername . '_' . uniqid();
                        break;
                    }
                }
            } else {
                // If username was provided, check if it's already taken
                $existingUsername = $this->userModel->where(['username' => $username]);
                if (!empty($existingUsername)) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Username already taken'
                    ], 409);
                }
            }

            // Hash password
            $hashedPassword = $this->userModel->hashPassword($data['password']);

            // Create user with all required fields
            $userData = [
                'username' => $username,
                'email' => $data['email'],
                'password_hash' => $hashedPassword,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'user_type' => $data['user_type'], // Use the user_type from form
                'status' => 'active'
            ];

            $userId = $this->userModel->create($userData);

            if (!$userId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create account'
                ], 500);
            }

            // Send registration confirmation email
            try {
                $emailService = new EmailService();
                $fullName = $data['first_name'] . ' ' . $data['last_name'];
                $emailService->sendRegistrationConfirmation(
                    $data['email'],
                    $fullName,
                    $data['user_type']
                );
                error_log("Registration confirmation email sent to: {$data['email']}");
            } catch (\Exception $e) {
                // Log error but don't fail registration if email fails
                error_log("Failed to send registration email: " . $e->getMessage());
            }

            // Record initial login
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            $this->userModel->recordLogin($userId, $ipAddress, $userAgent);

            // Set session
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['user_type'] = $data['user_type'];
            $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'type' => $data['user_type'],
                'avatar' => null
            ];

            // Determine redirect based on user type
            $redirectUrl = $this->getDashboardUrl($data['user_type']);

            return $this->json([
                'success' => true,
                'message' => 'Account created successfully',
                'redirect' => $redirectUrl,
                'user' => [
                    'id' => $userId,
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'email' => $data['email'],
                    'type' => $data['user_type']
                ]
            ], 201);

        } catch (\Exception $e) {
            error_log("Registration Error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle logout - FIXED VERSION
     */
    public function logout(Request $request): Response
    {
        $this->startSession();
        
        // Log the logout activity before destroying session
        if (isset($_SESSION['user_id'])) {
            try {
                $this->userModel->logActivity(
                    $_SESSION['user_id'],
                    'logout',
                    'User logged out',
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                );
            } catch (\Exception $e) {
                error_log("Failed to log logout: " . $e->getMessage());
            }
        }

        // Properly clear and destroy session
        $_SESSION = array(); // Clear all session variables
        
        // Delete session cookie if it exists
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Start a new clean session to avoid issues
        session_start();

        // Handle both GET and POST requests
        $base = defined('BASE_URL') ? BASE_URL : '';
        if ($request->getMethod() === 'POST') {
            return $this->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'redirect' => $base . '/login'
            ]);
        } else {
            // For GET requests, redirect directly
            header('Location: ' . $base . '/login');
            exit();
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
        $base = defined('BASE_URL') ? BASE_URL : '';
        $dashboardMap = [
            'admin' => $base . '/admin/dashboard',
            'ground_owner' => $base . '/ground-owner/dashboard',
            'coach' => $base . '/coach/dashboard',
            'shop_owner' => $base . '/shop-owner/dashboard',
            'user' => $base . '/'
        ];

        return $dashboardMap[$userType] ?? $base . '/';
    }

    /**
     * Redirect to appropriate dashboard
     */
    private function redirectToDashboard(string $userType): Response
    {
        return $this->redirect($this->getDashboardUrl($userType));
    }
}