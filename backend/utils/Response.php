<?php
/**
 * Response Utility Class
 * Handles API responses in a consistent format
 */

class Response {
    
    /**
     * Send JSON response
     */
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit();
    }
    
    /**
     * Send success response
     */
    public static function success($data = null, $message = 'Success') {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        self::json($response, 200);
    }
    
    /**
     * Send error response
     */
    public static function error($message = 'Error occurred', $statusCode = 400, $errors = null) {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        self::json($response, $statusCode);
    }
    
    /**
     * Send validation error response
     */
    public static function validationError($errors, $message = 'Validation failed') {
        self::error($message, 422, $errors);
    }
    
    /**
     * Send unauthorized response
     */
    public static function unauthorized($message = 'Unauthorized access') {
        self::error($message, 401);
    }
    
    /**
     * Send forbidden response
     */
    public static function forbidden($message = 'Access forbidden') {
        self::error($message, 403);
    }
    
    /**
     * Send not found response
     */
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    /**
     * Send server error response
     */
    public static function serverError($message = 'Internal server error') {
        self::error($message, 500);
    }
    
    /**
     * Send paginated response
     */
    public static function paginated($data, $currentPage, $totalPages, $totalRecords, $perPage = 10) {
        $response = [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => (int) $currentPage,
                'total_pages' => (int) $totalPages,
                'total_records' => (int) $totalRecords,
                'per_page' => (int) $perPage,
                'has_next' => $currentPage < $totalPages,
                'has_prev' => $currentPage > 1
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        self::json($response, 200);
    }
}