<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Response;

/**
 * Admin Coach Controller
 * 
 * Handles admin operations for coaches
 */
class AdminCoachController extends BaseController
{
    /**
     * Display coach management page
     */
    public function index(Request $request): Response
    {
        return $this->view('admin/coaches');
    }

    /**
     * Create new coach
     */
    public function create(Request $request): Response
    {
        return $this->view('admin/coaches');
    }

    /**
     * Update coach
     */
    public function update(Request $request): Response
    {
        return $this->view('admin/coaches');
    }

    /**
     * Delete coach
     */
    public function delete(Request $request): Response
    {
        return $this->redirect('/admin/coaches');
    }
}