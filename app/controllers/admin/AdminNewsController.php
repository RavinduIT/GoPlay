<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Response;

/**
 * Admin News Controller
 * 
 * Handles admin operations for news articles
 */
class AdminNewsController extends BaseController
{
    /**
     * Display news management page
     */
    public function index(Request $request): Response
    {
        return $this->view('admin/news');
    }

    /**
     * Create new news article
     */
    public function create(Request $request): Response
    {
        return $this->view('admin/news');
    }

    /**
     * Update news article
     */
    public function update(Request $request): Response
    {
        return $this->view('admin/news');
    }

    /**
     * Delete news article
     */
    public function delete(Request $request): Response
    {
        return $this->redirect('/admin/news');
    }
}