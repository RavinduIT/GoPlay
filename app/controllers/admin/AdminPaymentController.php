<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use App\Models\PlatformEarnings;
use App\Models\WithdrawalRequest;
use App\Models\Settings;

/**
 * Admin Payment & Earnings Controller
 * 
 * Manages platform earnings, service fees, and withdrawals
 */
class AdminPaymentController extends BaseController
{
    private ?PlatformEarnings $earningsModel = null;
    private ?WithdrawalRequest $withdrawalModel = null;
    
    private function getEarningsModel(): PlatformEarnings
    {
        if ($this->earningsModel === null) {
            $this->earningsModel = new PlatformEarnings();
        }
        return $this->earningsModel;
    }
    
    private function getWithdrawalModel(): WithdrawalRequest
    {
        if ($this->withdrawalModel === null) {
            $this->withdrawalModel = new WithdrawalRequest();
        }
        return $this->withdrawalModel;
    }
    
    /**
     * Check if user is admin
     */
    private function checkAdminAuth(): bool
    {
        $this->startSession();
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin';
    }
    
    /**
     * Display payments & earnings page
     */
    public function index(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->redirect('/login');
        }
        
        return $this->view('admin/payments', [
            'activePage' => 'payments'
        ]);
    }
    
    /**
     * API: Get earnings overview statistics
     */
    public function getOverviewStats(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $earningsModel = $this->getEarningsModel();
            $withdrawalModel = $this->getWithdrawalModel();
            
            // Get earnings statistics
            $earningsStats = $earningsModel->getStatistics();
            
            // Get withdrawal statistics
            $withdrawalStats = $withdrawalModel->getStatistics();
            
            // Get service fee percentage
            $sql = "SELECT value FROM settings WHERE key_name = 'service_fee_percentage' LIMIT 1";
            $result = $earningsModel->queryFirst($sql);
            $serviceFeePercentage = $result ? (float)$result['value'] : 5.00;
            
            // Combine all statistics
            $stats = array_merge($earningsStats, $withdrawalStats, [
                'service_fee_percentage' => $serviceFeePercentage
            ]);
            
            return $this->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Get earnings history with pagination
     */
    public function getEarningsHistory(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $page = (int)($request->getQuery('page') ?? 1);
            $perPage = (int)($request->getQuery('per_page') ?? 20);
            
            // Build filters
            $filters = [];
            if ($status = $request->getQuery('status')) {
                $filters['status'] = $status;
            }
            if ($bookingType = $request->getQuery('booking_type')) {
                $filters['booking_type'] = $bookingType;
            }
            if ($dateFrom = $request->getQuery('date_from')) {
                $filters['date_from'] = $dateFrom;
            }
            if ($dateTo = $request->getQuery('date_to')) {
                $filters['date_to'] = $dateTo;
            }
            if ($search = $request->getQuery('search')) {
                $filters['search'] = $search;
            }
            
            $earningsModel = $this->getEarningsModel();
            $result = $earningsModel->getEarningsHistory($page, $perPage, $filters);
            
            return $this->json([
                'success' => true,
                'data' => $result['data'],
                'pagination' => $result['pagination']
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Get earnings breakdown by type
     */
    public function getEarningsBreakdown(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $filters = [];
            if ($dateFrom = $request->getQuery('date_from')) {
                $filters['date_from'] = $dateFrom;
            }
            if ($dateTo = $request->getQuery('date_to')) {
                $filters['date_to'] = $dateTo;
            }
            
            $earningsModel = $this->getEarningsModel();
            $breakdown = $earningsModel->getEarningsBreakdown($filters);
            
            return $this->json([
                'success' => true,
                'data' => $breakdown
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Get daily earnings chart data
     */
    public function getDailyEarnings(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $days = (int)($request->getQuery('days') ?? 30);
            
            $earningsModel = $this->getEarningsModel();
            $dailyData = $earningsModel->getDailyEarnings($days);
            
            return $this->json([
                'success' => true,
                'data' => $dailyData
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Update service fee percentage
     */
    public function updateServiceFee(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $data = $request->getJsonBody();
            
            if (!isset($data['percentage'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Service fee percentage is required'
                ], 400);
            }
            
            $percentage = (float)$data['percentage'];
            
            if ($percentage < 0 || $percentage > 100) {
                return $this->json([
                    'success' => false,
                    'message' => 'Service fee percentage must be between 0 and 100'
                ], 400);
            }
            
            // Update setting
            $sql = "UPDATE settings SET value = ? WHERE key_name = 'service_fee_percentage'";
            $this->getEarningsModel()->query($sql, [$percentage]);
            
            return $this->json([
                'success' => true,
                'message' => 'Service fee percentage updated successfully',
                'percentage' => $percentage
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Get withdrawal requests
     */
    public function getWithdrawalRequests(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $page = (int)($request->getQuery('page') ?? 1);
            $perPage = (int)($request->getQuery('per_page') ?? 20);
            
            // Build filters
            $filters = [];
            if ($status = $request->getQuery('status')) {
                $filters['status'] = $status;
            }
            if ($dateFrom = $request->getQuery('date_from')) {
                $filters['date_from'] = $dateFrom;
            }
            if ($dateTo = $request->getQuery('date_to')) {
                $filters['date_to'] = $dateTo;
            }
            
            $withdrawalModel = $this->getWithdrawalModel();
            $result = $withdrawalModel->getRequests($page, $perPage, $filters);
            
            return $this->json([
                'success' => true,
                'data' => $result['data'],
                'pagination' => $result['pagination']
            ]);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Create withdrawal request
     */
    public function createWithdrawal(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $data = $request->getJsonBody();
            
            // Validate required fields
            if (!isset($data['amount']) || !isset($data['withdrawal_method'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Amount and withdrawal method are required'
                ], 400);
            }
            
            // Validate withdrawal method specific fields
            if ($data['withdrawal_method'] === 'bank_transfer') {
                if (!isset($data['bank_name']) || !isset($data['account_number']) || !isset($data['account_holder'])) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Bank details are required for bank transfer'
                    ], 400);
                }
            } elseif ($data['withdrawal_method'] === 'paypal') {
                if (!isset($data['paypal_email'])) {
                    return $this->json([
                        'success' => false,
                        'message' => 'PayPal email is required'
                    ], 400);
                }
            }
            
            $withdrawalModel = $this->getWithdrawalModel();
            $withdrawalId = $withdrawalModel->createRequest($_SESSION['user_id'], $data);
            
            if ($withdrawalId) {
                $withdrawal = $withdrawalModel->find($withdrawalId);
                
                return $this->json([
                    'success' => true,
                    'message' => 'Withdrawal request created successfully',
                    'data' => $withdrawal
                ], 201);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create withdrawal request'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * API: Process withdrawal request
     */
    public function processWithdrawal(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $id = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            
            if (!isset($data['transaction_reference'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Transaction reference is required'
                ], 400);
            }
            
            $withdrawalModel = $this->getWithdrawalModel();
            $success = $withdrawalModel->processRequest(
                $id, 
                $_SESSION['user_id'], 
                $data['transaction_reference']
            );
            
            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Withdrawal processed successfully'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to process withdrawal'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Reject withdrawal request
     */
    public function rejectWithdrawal(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $id = (int)$request->getParam('id');
            $data = $request->getJsonBody();
            
            if (!isset($data['reason'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Rejection reason is required'
                ], 400);
            }
            
            $withdrawalModel = $this->getWithdrawalModel();
            $success = $withdrawalModel->rejectRequest(
                $id, 
                $_SESSION['user_id'], 
                $data['reason']
            );
            
            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Withdrawal rejected'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to reject withdrawal'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Cancel withdrawal request
     */
    public function cancelWithdrawal(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $id = (int)$request->getParam('id');
            
            $withdrawalModel = $this->getWithdrawalModel();
            $success = $withdrawalModel->cancelRequest($id);
            
            if ($success) {
                return $this->json([
                    'success' => true,
                    'message' => 'Withdrawal cancelled successfully'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to cancel withdrawal or withdrawal cannot be cancelled'
                ], 400);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * API: Export earnings data
     */
    public function exportEarnings(Request $request): Response
    {
        if (!$this->checkAdminAuth()) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $format = $request->getQuery('format') ?? 'csv';
            
            // Build filters
            $filters = [];
            if ($dateFrom = $request->getQuery('date_from')) {
                $filters['date_from'] = $dateFrom;
            }
            if ($dateTo = $request->getQuery('date_to')) {
                $filters['date_to'] = $dateTo;
            }
            
            $earningsModel = $this->getEarningsModel();
            $result = $earningsModel->getEarningsHistory(1, 10000, $filters);
            
            if ($format === 'csv') {
                $csv = $this->generateCsv($result['data']);
                
                $response = new Response($csv);
                $response->setHeader('Content-Type', 'text/csv');
                $response->setHeader('Content-Disposition', 'attachment; filename="earnings_' . date('Y-m-d') . '.csv"');
                return $response;
            } else {
                return $this->json([
                    'success' => true,
                    'data' => $result['data']
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate CSV from earnings data
     */
    private function generateCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Headers
        fputcsv($output, [
            'ID',
            'Date',
            'Reference',
            'Type',
            'Transaction Amount',
            'Service Fee %',
            'Service Fee Amount',
            'Merchant Amount',
            'Merchant',
            'Status'
        ]);
        
        // Data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['created_at'],
                $row['reference_number'] ?? 'N/A',
                $row['booking_type'] ?? 'N/A',
                $row['transaction_amount'],
                $row['service_fee_percentage'] . '%',
                $row['service_fee_amount'],
                $row['merchant_amount'],
                ($row['merchant_first_name'] ?? '') . ' ' . ($row['merchant_last_name'] ?? ''),
                $row['status']
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}