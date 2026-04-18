<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\Database;
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

        // Fetch active promotions for homepage banners
        $promotions = [];
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query(
                "SELECT * FROM promotions 
                 WHERE is_active = 1 
                 AND (starts_at IS NULL OR starts_at <= NOW()) 
                 AND (ends_at IS NULL OR ends_at >= NOW()) 
                 ORDER BY priority DESC, created_at DESC LIMIT 5"
            );
            $promotions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Failed to load promotions: " . $e->getMessage());
        }

        // Fetch active sports categories for dynamic display
        $sportsCategories = [];
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query(
                "SELECT sc.*, 
                    (SELECT COUNT(*) FROM sports_facilities sf WHERE sf.sport_category_id = sc.id AND sf.status = 'active') as venue_count
                 FROM sports_categories sc 
                 WHERE sc.is_active = 1 
                 ORDER BY sc.name ASC"
            );
            $sportsCategories = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Failed to load sports categories: " . $e->getMessage());
        }

        return $this->view('home/index', [
            'featuredNews' => $featuredNews,
            'recentNews' => $recentNews,
            'popularNews' => $popularNews,
            'promotions' => $promotions,
            'sportsCategories' => $sportsCategories,
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