<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * Booking Controller
 * 
 * Handles booking operations for facilities and coaches
 */
class BookingController extends BaseController
{
    /**
     * Display booking form for ground
     */
    public function bookGround(Request $request): Response
    {
        return $this->view('booking/book-ground');
    }

    /**
     * Display booking form for coach
     */
    public function bookCoach(Request $request): Response
    {
        return $this->view('booking/book-coach');
    }

    /**
     * Display ground details
     */
    public function groundDetails(Request $request): Response
    {
        $id = $request->getQuery('id');
        return $this->view('booking/ground-details', ['id' => $id]);
    }

    /**
     * Display user's bookings
     */
    public function myBookings(Request $request): Response
    {
        return $this->view('booking/my-bookings');
    }

    /**
     * Handle booking submission
     */
    public function store(Request $request): Response
    {
        // Booking creation logic
        return $this->json(['success' => true, 'message' => 'Booking created successfully']);
    }
}