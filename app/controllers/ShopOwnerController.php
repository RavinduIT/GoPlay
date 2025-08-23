<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;

class ShopOwnerController extends BaseController
{
    public function dashboard(Request $request): Response
    {
        // Check if user is authenticated and is shop owner
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'shop_owner') {
            return $this->redirect('/login');
        }
        
        return $this->view('shop-owner/dashboard');
    }
}