<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;

/**
 * Home Controller
 * 
 * Handles home page and main landing pages
 */
class HomeController extends BaseController
{
    /**
     * Display home page
     */
    public function index(Request $request): Response
    {
        return $this->view('home/index');
    }

    /**
     * Display about page
     */
    public function about(Request $request): Response
    {
        return $this->view('home/about');
    }

    /**
     * Display contact page
     */
    public function contact(Request $request): Response
    {
        return $this->view('home/contact');
    }
}