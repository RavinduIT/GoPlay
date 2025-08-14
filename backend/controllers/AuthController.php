<?php
/**
 * Authentication Controller
 * Handles user authentication, registration, and session management
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Validator.php';

class AuthController {
    
    private $userModel;
    private $validator;
    
    public function __construct() {
        $this->userModel = new User();
        $this->validator = new Validator();
    }
    
    /**
     * User registration
     */
    public function register() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data');
            }
            
            // Validation rules
            $rules = [
                'username' => 'required|min:3|max:50|alpha_num',
                'email' => 'required|email|max:100',
                'password' => 'required|min:6|max:255',
                'password_confirmation' => 'required|same:password',
                'first_name' => 'required|min:2|max:50|alpha',
                'last_name' => 'required|min:2|max:50|alpha',
                'phone' => 'phone',
                'user_type' => 'in:customer,coach,facility_owner'
            ];
            
            $validation = $this->validator->validate($input, $rules);
            if (!$validation['valid']) {
                Response::validationError($validation['errors']);
            }
            
            // Check if user already exists
            if ($this->userModel->findByEmail($input['email'])) {
                Response::error('Email already registered', 409);
            }
            
            if ($this->userModel->findByUsername($input['username'])) {
                Response::error('Username already taken', 409);
            }
            
            // Create user
            $userData = [
                'username' => $input['username'],
                'email' => $input['email'],
                'password' => $input['password'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'phone' => $input['phone'] ?? null,
                'user_type' => $input['user_type'] ?? 'customer',
                'status' => 'active'
            ];
            
            $user = $this->userModel->createUser($userData);
            
            if ($user) {
                // Remove password hash from response
                unset($user['password_hash']);
                
                // Generate JWT token
                $token = JWT::encode([
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'user_type' => $user['user_type'],
                    'exp' => time() + (24 * 60 * 60) // 24 hours
                ]);
                
                Logger::auth('User registered successfully', ['user_id' => $user['id'], 'email' => $user['email']]);
                
                Response::success([
                    'user' => $user,
                    'token' => $token
                ], 'Registration successful');
            } else {
                Response::error('Registration failed');
            }
            
        } catch (Exception $e) {
            Logger::error('Registration error: ' . $e->getMessage());
            Response::serverError('Registration failed');
        }
    }
    
    /**
     * User login
     */
    public function login() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data');
            }
            
            // Validation rules
            $rules = [
                'email' => 'required',
                'password' => 'required'
            ];
            
            $validation = $this->validator->validate($input, $rules);
            if (!$validation['valid']) {
                Response::validationError($validation['errors']);
            }
            
            // Find user by email or username
            $user = $this->userModel->findByEmailOrUsername($input['email']);
            
            if (!$user) {
                Logger::auth('Login attempt with invalid credentials', ['identifier' => $input['email']]);
                Response::unauthorized('Invalid credentials');
            }
            
            // Check if user is active
            if ($user['status'] !== 'active') {
                Logger::auth('Login attempt with inactive account', ['user_id' => $user['id']]);
                Response::forbidden('Account is not active');
            }
            
            // Verify password
            if (!$this->userModel->verifyPassword($user, $input['password'])) {
                Logger::auth('Login attempt with wrong password', ['user_id' => $user['id']]);
                Response::unauthorized('Invalid credentials');
            }
            
            // Remove password hash from response
            unset($user['password_hash']);
            
            // Generate JWT token
            $token = JWT::encode([
                'user_id' => $user['id'],
                'email' => $user['email'],
                'user_type' => $user['user_type'],
                'exp' => time() + (24 * 60 * 60) // 24 hours
            ]);
            
            Logger::auth('User logged in successfully', ['user_id' => $user['id'], 'email' => $user['email']]);
            
            Response::success([
                'user' => $user,
                'token' => $token
            ], 'Login successful');
            
        } catch (Exception $e) {
            Logger::error('Login error: ' . $e->getMessage());
            Response::serverError('Login failed');
        }
    }
    
    /**
     * Get current user profile
     */
    public function profile() {
        try {
            $user = $this->getCurrentUser();
            
            if (!$user) {
                Response::unauthorized('Please login to access this resource');
            }
            
            // Get user with addresses
            $userProfile = $this->userModel->findWithAddresses($user['user_id']);
            unset($userProfile['password_hash']);
            
            Response::success($userProfile);
            
        } catch (Exception $e) {
            Logger::error('Profile fetch error: ' . $e->getMessage());
            Response::serverError('Failed to fetch profile');
        }
    }
    
    /**
     * Update user profile
     */
    public function updateProfile() {
        try {
            $user = $this->getCurrentUser();
            
            if (!$user) {
                Response::unauthorized('Please login to access this resource');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data');
            }
            
            // Validation rules
            $rules = [
                'first_name' => 'min:2|max:50|alpha',
                'last_name' => 'min:2|max:50|alpha',
                'phone' => 'phone',
                'date_of_birth' => 'date'
            ];
            
            $validation = $this->validator->validate($input, $rules);
            if (!$validation['valid']) {
                Response::validationError($validation['errors']);
            }
            
            // Update user
            $updatedUser = $this->userModel->update($user['user_id'], $input);
            
            if ($updatedUser) {
                unset($updatedUser['password_hash']);
                Logger::info('User profile updated', ['user_id' => $user['user_id']]);
                Response::success($updatedUser, 'Profile updated successfully');
            } else {
                Response::error('Failed to update profile');
            }
            
        } catch (Exception $e) {
            Logger::error('Profile update error: ' . $e->getMessage());
            Response::serverError('Failed to update profile');
        }
    }
    
    /**
     * Change user password
     */
    public function changePassword() {
        try {
            $user = $this->getCurrentUser();
            
            if (!$user) {
                Response::unauthorized('Please login to access this resource');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                Response::error('Invalid JSON data');
            }
            
            // Validation rules
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min:6|max:255',
                'new_password_confirmation' => 'required|same:new_password'
            ];
            
            $validation = $this->validator->validate($input, $rules);
            if (!$validation['valid']) {
                Response::validationError($validation['errors']);
            }
            
            // Get current user
            $currentUser = $this->userModel->find($user['user_id']);
            
            // Verify current password
            if (!$this->userModel->verifyPassword($currentUser, $input['current_password'])) {
                Response::unauthorized('Current password is incorrect');
            }
            
            // Update password
            $result = $this->userModel->updatePassword($user['user_id'], $input['new_password']);
            
            if ($result) {
                Logger::auth('Password changed successfully', ['user_id' => $user['user_id']]);
                Response::success(null, 'Password changed successfully');
            } else {
                Response::error('Failed to change password');
            }
            
        } catch (Exception $e) {
            Logger::error('Password change error: ' . $e->getMessage());
            Response::serverError('Failed to change password');
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        try {
            $user = $this->getCurrentUser();
            
            if ($user) {
                Logger::auth('User logged out', ['user_id' => $user['user_id']]);
            }
            
            Response::success(null, 'Logged out successfully');
            
        } catch (Exception $e) {
            Logger::error('Logout error: ' . $e->getMessage());
            Response::success(null, 'Logged out successfully');
        }
    }
    
    /**
     * Get current authenticated user from JWT token
     */
    private function getCurrentUser() {
        $headers = getallheaders();
        $token = null;
        
        // Check Authorization header
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }
        
        if (!$token) {
            return null;
        }
        
        try {
            $decoded = JWT::decode($token);
            return $decoded;
        } catch (Exception $e) {
            Logger::warning('Invalid JWT token: ' . $e->getMessage());
            return null;
        }
    }
}