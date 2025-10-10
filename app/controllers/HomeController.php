<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\News;

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
        // Fetch key homepage data (news, etc.)
        $newsModel = new News();

        $featuredNews = $newsModel->getFeatured(8);
        $recentNews = $newsModel->getRecent(12);
        $popularNews = $newsModel->getPopular(6);

        return $this->view('home/index', [
            'featuredNews' => $featuredNews,
            'recentNews' => $recentNews,
            'popularNews' => $popularNews,
        ]);
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