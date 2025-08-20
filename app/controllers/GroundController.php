<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * Ground Controller
 * 
 * Handles ground/facility related operations
 */
class GroundController extends BaseController
{
    /**
     * Display all grounds
     */
    public function index(Request $request): Response
    {
        // Logic to display all grounds
        return $this->view('booking/book-ground');
    }

    /**
     * Display specific ground details
     */
    public function show(Request $request): Response
    {
        $id = $request->getParam('id');
        // Logic to show ground details
        return $this->view('booking/ground-details', ['id' => $id]);
    }

    /**
     * Show booking form for ground
     */
    public function book(Request $request): Response
    {
        return $this->view('booking/book-ground');
    }
}