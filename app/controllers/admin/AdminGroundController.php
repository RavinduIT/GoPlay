<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Response;

/**
 * Admin Ground Controller
 * 
 * Handles admin operations for grounds/facilities
 */
class AdminGroundController extends BaseController
{
    /**
     * Display ground management page
     */
    public function index(Request $request): Response
    {
        return $this->view('admin/grounds');
    }

    /**
     * Create new ground
     */
    public function create(Request $request): Response
    {
        return $this->view('admin/grounds');
    }

    /**
     * Update ground
     */
    public function update(Request $request): Response
    {
        return $this->view('admin/grounds');
    }

    /**
     * Delete ground
     */
    public function delete(Request $request): Response
    {
        return $this->redirect('/admin/grounds');
    }
}