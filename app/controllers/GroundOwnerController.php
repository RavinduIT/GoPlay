<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\SportsFacility;
use App\Models\SportsCategory;

class GroundOwnerController extends BaseController
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
    
    private function checkGroundOwnerAuth(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'ground_owner';
    }
    
    private function getGroundOwnerResponse(): Response
    {
        return $this->json([
            'success' => false,
            'message' => 'Unauthorized. Ground owner access required.',
            'status' => 401
        ], 401);
    }

    public function dashboard(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/dashboard');
    }
    
    public function groundsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/grounds');
    }
    
    public function bookingsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/bookings');
    }
    
    public function earningsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/earnings');
    }
    
    public function reviewsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/reviews');
    }
    
    public function schedulePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/schedule');
    }
    
    public function maintenancePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/maintenance');
    }
    
    public function profilePage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/profile');
    }
    
    public function settingsPage(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/settings');
    }
    
    // API Methods for Ground Management
    public function getGrounds(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $ownerId = $_SESSION['user_id'];
            $facilities = $this->getFacilityModel()->getByOwnerId($ownerId);
            
            return $this->json([
                'success' => true,
                'grounds' => $facilities
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load grounds',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $groundId = (int)$request->getParam('id');
            $ownerId = $_SESSION['user_id'];
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid ground ID'
                ], 400);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            if (!$ground || $ground['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }
            
            return $this->json([
                'success' => true,
                'ground' => $ground
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function createGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];
            
            // Validate required fields
            $required = ['name', 'sport_category_id', 'address', 'city', 'hourly_rate'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => "Field '{$field}' is required"
                    ], 400);
                }
            }
            
            // Add owner ID and defaults
            $data['owner_id'] = $ownerId;
            $data['status'] = $data['status'] ?? 'active';
            $data['country'] = 'Sri Lanka';
            
            // Handle amenities array
            if (isset($data['amenities']) && is_array($data['amenities'])) {
                $data['amenities'] = json_encode($data['amenities']);
            }
            
            $groundId = $this->getFacilityModel()->create($data);
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create ground'
                ], 500);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            return $this->json([
                'success' => true,
                'message' => 'Ground created successfully',
                'ground' => $ground
            ], 201);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to create ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $groundId = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            $ownerId = $_SESSION['user_id'];
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid ground ID'
                ], 400);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            if (!$ground || $ground['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }
            
            // Remove fields that shouldn't be updated
            unset($data['id'], $data['owner_id'], $data['created_at']);
            
            // Handle amenities array
            if (isset($data['amenities']) && is_array($data['amenities'])) {
                $data['amenities'] = json_encode($data['amenities']);
            }
            
            $success = $this->getFacilityModel()->update($groundId, $data);
            
            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update ground'
                ], 500);
            }
            
            $updatedGround = $this->getFacilityModel()->find($groundId);
            
            return $this->json([
                'success' => true,
                'message' => 'Ground updated successfully',
                'ground' => $updatedGround
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteGround(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $groundId = (int)$request->getParam('id');
            $ownerId = $_SESSION['user_id'];
            
            if (!$groundId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid ground ID'
                ], 400);
            }
            
            $ground = $this->getFacilityModel()->find($groundId);
            
            if (!$ground || $ground['owner_id'] != $ownerId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ground not found'
                ], 404);
            }
            
            // Soft delete by setting status to inactive
            $success = $this->getFacilityModel()->update($groundId, ['status' => 'inactive']);
            
            if (!$success) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to delete ground'
                ], 500);
            }
            
            return $this->json([
                'success' => true,
                'message' => 'Ground deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to delete ground',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getSportsCategories(Request $request): Response
    {
        if (!$this->checkGroundOwnerAuth()) {
            return $this->getGroundOwnerResponse();
        }
        
        try {
            $categories = $this->getCategoryModel()->getAllActive();
            
            return $this->json([
                'success' => true,
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to load categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}