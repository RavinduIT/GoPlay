<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

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
}