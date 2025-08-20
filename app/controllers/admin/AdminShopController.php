<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Request;
use App\Core\Response;

/**
 * Admin Shop Controller
 * 
 * Handles admin operations for shop/products
 */
class AdminShopController extends BaseController
{
    /**
     * Display shop management page
     */
    public function index(Request $request): Response
    {
        return $this->view('admin/shop');
    }

    /**
     * Create new product
     */
    public function create(Request $request): Response
    {
        return $this->view('admin/shop');
    }

    /**
     * Update product
     */
    public function update(Request $request): Response
    {
        return $this->view('admin/shop');
    }

    /**
     * Delete product
     */
    public function delete(Request $request): Response
    {
        return $this->redirect('/admin/shop');
    }
}