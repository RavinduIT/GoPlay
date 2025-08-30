<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\SportsFacility;
use App\Models\SportsCategory;

/**
 * Booking Controller
 * 
 * Handles booking operations for facilities and coaches
 */
class BookingController extends BaseController
{
    private ?SportsFacility $facilityModel = null;
    private ?SportsCategory $categoryModel = null;
    
    private function getFacilityModel(): SportsFacility
    {
        if ($this->facilityModel === null) {
            $this->facilityModel = new SportsFacility();
        }
        return $this->facilityModel;
    }
    
    private function getCategoryModel(): SportsCategory
    {
        if ($this->categoryModel === null) {
            $this->categoryModel = new SportsCategory();
        }
        return $this->categoryModel;
    }
    
    /**
     * Display booking form for ground with available facilities
     */
    public function bookGround(Request $request): Response
    {
        try {
            // Get filter parameters
            $filters = [
                'sport_category' => $request->getQuery('sport'),
                'city' => $request->getQuery('city'),
                'min_rate' => $request->getQuery('min_rate'),
                'max_rate' => $request->getQuery('max_rate'),
                'date' => $request->getQuery('date'),
                'time' => $request->getQuery('time')
            ];
            
            // Remove empty filters
            $filters = array_filter($filters, fn($value) => $value !== null && $value !== '');
            
            // Get available sports facilities
            $facilities = $this->getFacilityModel()->getAvailableFacilities($filters);
            
            // Get sports categories for filters
            $categories = $this->getCategoryModel()->getAllActive();
            
            // Get unique cities for filter dropdown
            $cities = $this->getFacilityModel()->getUniqueCities();
            
            return $this->view('booking/book-ground', [
                'facilities' => $facilities,
                'categories' => $categories,
                'cities' => $cities,
                'currentFilters' => $filters
            ]);
            
        } catch (\Exception $e) {
            // Log error and show fallback
            error_log("Book ground page error: " . $e->getMessage());
            
            return $this->view('booking/book-ground', [
                'facilities' => [],
                'categories' => [],
                'cities' => [],
                'currentFilters' => [],
                'error' => 'Unable to load facilities. Please try again later.'
            ]);
        }
    }

    /**
     * Display booking form for coach
     */
    public function bookCoach(Request $request): Response
    {
        return $this->view('booking/book-coach');
    }

    /**
     * Display ground details
     */
    public function groundDetails(Request $request): Response
    {
        $id = $request->getQuery('id');
        return $this->view('booking/ground-details', ['id' => $id]);
    }

    /**
     * Display user's bookings
     */
    public function myBookings(Request $request): Response
    {
        return $this->view('booking/my-bookings');
    }

    /**
     * Handle booking submission
     */
    public function store(Request $request): Response
    {
        // Booking creation logic
        return $this->json(['success' => true, 'message' => 'Booking created successfully']);
    }
}