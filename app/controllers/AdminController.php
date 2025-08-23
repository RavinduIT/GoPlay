<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;

class AdminController extends BaseController
{
    public function dashboard(Request $request): Response
    {
        // Check if user is authenticated and is admin
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->redirect('/login');
        }
        
        return $this->view('admin/dashboard');
    }
}