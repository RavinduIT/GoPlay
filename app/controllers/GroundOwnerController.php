<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;

class GroundOwnerController extends BaseController
{
    public function dashboard(Request $request): Response
    {
        // Check if user is authenticated and is ground owner
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'ground_owner') {
            return $this->redirect('/login');
        }
        
        return $this->view('ground-owner/dashboard');
    }
}