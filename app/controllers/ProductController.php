<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * Product Controller
 * 
 * Handles shop/product operations
 */
class ProductController extends BaseController
{
    /**
     * Display shop page
     */
    public function index(Request $request): Response
    {
        return $this->view('shop/shop');
    }

    /**
     * Display product details
     */
    public function show(Request $request): Response
    {
        $id = $request->getParam('id');
        return $this->view('shop/product-details', ['id' => $id]);
    }

    /**
     * Display shopping cart
     */
    public function cart(Request $request): Response
    {
        return $this->view('shop/cart');
    }
}