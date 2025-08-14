<?php
/**
 * GoPlay Sports Platform Database Configuration
 * 
 * This file contains all the database settings for your GoPlay application.
 * Update the values according to your MySQL setup.
 */

// Database Configuration
define('DB_HOST', 'localhost');          // Database host (usually localhost)
define('DB_USERNAME', 'root');           // Database username
define('DB_PASSWORD', '');               // Database password (empty for XAMPP)
define('DB_NAME', 'goplay_sports_platform'); // Database name
define('DB_CHARSET', 'utf8mb4');         // Database charset
define('DB_COLLATION', 'utf8mb4_unicode_ci'); // Database collation

// PDO Database Connection Class
class Database {
    private $host = DB_HOST;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    private $charset = DB_CHARSET;
    public $connection;
    
    // Connect to database
    public function connect() {
        $this->connection = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->database . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            exit();
        }
        
        return $this->connection;
    }
    
    // Close database connection
    public function close() {
        $this->connection = null;
    }
}

// Database Helper Functions
class DatabaseHelper {
    private $db;
    private $connection;
    
    public function __construct() {
        $this->db = new Database();
        $this->connection = $this->db->connect();
    }
    
    // Execute a query and return results
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Database Query Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get all records
    public function getAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    // Get single record
    public function getOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : null;
    }
    
    // Insert record and return last insert ID
    public function insert($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $this->connection->lastInsertId() : false;
    }
    
    // Update record and return affected rows
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }
    
    // Delete record and return affected rows
    public function delete($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }
    
    // Check if record exists
    public function exists($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchColumn() > 0 : false;
    }
    
    // Begin transaction
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    // Commit transaction
    public function commit() {
        return $this->connection->commit();
    }
    
    // Rollback transaction
    public function rollback() {
        return $this->connection->rollback();
    }
}

// Test Database Connection
function testDatabaseConnection() {
    try {
        $db = new Database();
        $connection = $db->connect();
        
        if ($connection) {
            echo "✅ Database connection successful!<br>";
            
            // Test query
            $stmt = $connection->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '" . DB_NAME . "'");
            $result = $stmt->fetch();
            
            echo "📊 Database contains " . $result['table_count'] . " tables<br>";
            
            $db->close();
            return true;
        }
        
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        return false;
    }
}

// API Response Helper
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Error Response Helper
function sendErrorResponse($message, $statusCode = 400) {
    sendJsonResponse([
        'success' => false,
        'error' => $message
    ], $statusCode);
}

// Success Response Helper
function sendSuccessResponse($data = null, $message = 'Success') {
    $response = [
        'success' => true,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    sendJsonResponse($response);
}

?>