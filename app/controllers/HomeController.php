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
        // Fetch the latest published news for the home page carousel
        $newsModel = new News();
        $latestNews = $newsModel->getRecent(4);

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

        // Fetch dynamic trust/stats from database
        $trustStats = ['venues' => 0, 'coaches' => 0, 'bookings' => 0, 'avg_rating' => '0.0'];
        try {
            $db = Database::getInstance()->getConnection();

            $venueCount = $db->query("SELECT COUNT(*) as c FROM sports_facilities WHERE status = 'active'")->fetch(\PDO::FETCH_ASSOC)['c'];
            $coachCount = $db->query("SELECT COUNT(*) as c FROM coaches WHERE status = 'active'")->fetch(\PDO::FETCH_ASSOC)['c'];

            // Combine all booking sources
            $bookingCount = 0;
            try {
                $bookingCount += (int)$db->query("SELECT COUNT(*) as c FROM facility_bookings WHERE status IN ('confirmed','completed')")->fetch(\PDO::FETCH_ASSOC)['c'];
            } catch (\Exception $e) { /* table may not exist */ }
            try {
                $bookingCount += (int)$db->query("SELECT COUNT(*) as c FROM coach_bookings WHERE status IN ('confirmed','completed')")->fetch(\PDO::FETCH_ASSOC)['c'];
            } catch (\Exception $e) { /* table may not exist */ }

            // Average rating from facility reviews
            $avgRating = '0.0';
            try {
                $row = $db->query("SELECT ROUND(AVG(rating), 1) as avg_r FROM facility_reviews")->fetch(\PDO::FETCH_ASSOC);
                if ($row && $row['avg_r']) {
                    $avgRating = $row['avg_r'];
                }
            } catch (\Exception $e) { /* table may not exist */ }

            $trustStats = [
                'venues' => (int)$venueCount,
                'coaches' => (int)$coachCount,
                'bookings' => (int)$bookingCount,
                'avg_rating' => $avgRating,
            ];
        } catch (\Exception $e) {
            error_log("Failed to load trust stats: " . $e->getMessage());
        }

        // Fetch testimonials from top-rated reviews
        $testimonials = [];
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query(
                "SELECT fr.review_text, fr.rating, u.first_name, u.last_name, u.profile_picture
                 FROM facility_reviews fr
                 JOIN users u ON fr.user_id = u.id
                 WHERE fr.rating >= 4 AND fr.review_text IS NOT NULL AND fr.review_text != ''
                 ORDER BY fr.rating DESC, fr.created_at DESC
                 LIMIT 3"
            );
            $testimonials = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Failed to load testimonials: " . $e->getMessage());
        }

        // Fetch distinct locations from active facilities
        $locations = [];
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query(
                "SELECT DISTINCT city FROM sports_facilities WHERE status = 'active' AND city IS NOT NULL AND city != '' ORDER BY city ASC"
            );
            $locations = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            error_log("Failed to load locations: " . $e->getMessage());
        }

        return $this->view('home/index', [
            'latestNews' => $latestNews,
            'promotions' => $promotions,
            'sportsCategories' => $sportsCategories,
            'trustStats' => $trustStats,
            'testimonials' => $testimonials,
            'locations' => $locations,
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

    /**
     * Display terms and conditions page
     */
    public function terms(Request $request): Response
    {
        return $this->view('home/terms');
    }

    /**
     * Display privacy policy page
     */
    public function privacy(Request $request): Response
    {
        return $this->view('home/privacy');
    }
}