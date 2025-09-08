<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;

/**
 * Auth Controller
 * 
 * Handles authentication operations
 */
class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show login form
     */
    public function login(Request $request): Response
    {
        return $this->viewWithoutLayout('auth/login');
    }

    /**
     * Show signup form
     */
    public function signup(Request $request): Response
    {
        return $this->viewWithoutLayout('auth/signup');
    }

    /**
     * Handle login with role-based redirection
     */
    public function handleLogin(Request $request): Response
    {
        try {
            $data = $request->getBody();
            
            // Use JSON body if regular body is empty (for JSON requests)
            if (empty($data) && $request->getHeader('content-type') && strpos($request->getHeader('content-type'), 'application/json') !== false) {
                $data = $request->getJsonBody();
            }
            
            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json(['error' => 'Email and password are required'], 400);
            }

            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $password = $data['password'];

            $user = $this->userModel->getByEmail($email);

            if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
                return $this->json(['error' => 'Invalid credentials'], 401);
            }

            if ($user['status'] !== 'active') {
                return $this->json(['error' => 'Account is inactive'], 403);
            }

            // Update last login
            $this->userModel->updateLastLogin($user['id']);

            // Start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

            // Role-based redirect
            $redirectUrl = $this->getRedirectUrlByRole($user['user_type']);

            return $this->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'role' => $user['user_type']
                ],
                'redirect' => $redirectUrl
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Login failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrlByRole(string $role): string
    {
        switch ($role) {
            case 'admin':
                return '/admin/dashboard';
            case 'ground_owner':
                return '/ground-owner/dashboard';
            case 'coach':
                return '/coach/dashboard';
            case 'shop_owner':
                return '/shop-owner/dashboard';
            case 'user':
            default:
                return '/dashboard';
        }
    }

    /**
     * Handle registration
     */
    public function handleRegister(Request $request): Response
    {
        try {
            $data = $request->getBody();
            
            if (!isset($data['email'], $data['password'], $data['first_name'], $data['last_name'])) {
                return $this->json(['error' => 'All required fields must be provided'], 400);
            }

            // Check if user already exists
            $existingUser = $this->userModel->getByEmail($data['email']);
            if ($existingUser) {
                return $this->json(['error' => 'User already exists'], 409);
            }

            // Create user
            $userData = [
                'username' => explode('@', $data['email'])[0] . '_' . rand(1000, 9999),
                'email' => filter_var($data['email'], FILTER_SANITIZE_EMAIL),
                'password_hash' => $this->userModel->hashPassword($data['password']),
                'first_name' => htmlspecialchars($data['first_name']),
                'last_name' => htmlspecialchars($data['last_name']),
                'phone' => $data['phone'] ?? null,
                'user_type' => $data['user_type'] ?? 'user',
                'status' => 'active'
            ];

            $userId = $this->userModel->create($userData);

            return $this->json([
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): Response
    {
        session_start();
        session_destroy();
        
        return $this->json(['success' => true, 'message' => 'Logout successful']);
    }

    /**
     * Check if user is authenticated
     */
    public function checkAuth(Request $request): Response
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return $this->json(['authenticated' => false], 401);
        }

        return $this->json([
            'authenticated' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['user_email'],
                'name' => $_SESSION['user_name'],
                'role' => $_SESSION['user_type']
            ]
        ]);
    }
}