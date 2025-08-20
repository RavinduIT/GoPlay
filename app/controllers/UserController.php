<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * User Controller
 * 
 * Handles user profile and account operations
 */
class UserController extends BaseController
{
    /**
     * Display user profile
     */
    public function profile(Request $request): Response
    {
        return $this->view('user/profile');
    }

    /**
     * Show service provider registration
     */
    public function serviceProviderRegister(Request $request): Response
    {
        return $this->view('user/service-provider-register');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): Response
    {
        // Profile update logic
        return $this->redirect('/profile');
    }
}