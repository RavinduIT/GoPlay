<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;

/**
 * Auth Controller
 * 
 * Handles authentication operations
 */
class AuthController extends BaseController
{
    /**
     * Show login form
     */
    public function login(Request $request): Response
    {
        return $this->view('auth/login');
    }

    /**
     * Show signup form
     */
    public function signup(Request $request): Response
    {
        return $this->view('auth/signup');
    }

    /**
     * Handle login
     */
    public function handleLogin(Request $request): Response
    {
        // Login logic here
        return $this->redirect('/dashboard');
    }

    /**
     * Handle registration
     */
    public function handleRegister(Request $request): Response
    {
        // Registration logic here
        return $this->redirect('/login');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request): Response
    {
        // Logout logic here
        return $this->redirect('/');
    }
}