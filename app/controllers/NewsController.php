<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * News Controller
 * 
 * Handles news and articles
 */
class NewsController extends BaseController
{
    /**
     * Display all news
     */
    public function index(Request $request): Response
    {
        return $this->view('news/index');
    }

    /**
     * Display specific news article
     */
    public function show(Request $request): Response
    {
        $id = $request->getParam('id');
        return $this->view('news/detail', ['id' => $id]);
    }
}