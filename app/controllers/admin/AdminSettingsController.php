<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use Core\Database;

/**
 * Admin Settings Controller
 * 
 * Handles system settings and configurations
 */
class AdminSettingsController extends BaseController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Display settings page
     */
    public function index(Request $request): Response
    {
        // Check if user is authenticated and is admin
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return $this->redirect('/login');
        }

        $activePage = 'settings';
        return $this->view('admin/settings', compact('activePage'));
    }

    /**
     * Get all settings
     */
    public function getSettings(Request $request): Response
    {
        try {
            $settings = $this->db->query("SELECT * FROM settings ORDER BY key_name")->fetchAll();
            
            // Group settings by category
            $grouped = [];
            foreach ($settings as $setting) {
                $category = $this->getCategoryFromKey($setting['key_name']);
                if (!isset($grouped[$category])) {
                    $grouped[$category] = [];
                }
                $grouped[$category][] = $setting;
            }

            return $this->json([
                'success' => true,
                'data' => $grouped
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a setting
     */
    public function updateSetting(Request $request): Response
    {
        try {
            $key = $request->post('key');
            $value = $request->post('value');

            if (!$key) {
                return $this->json(['success' => false, 'message' => 'Setting key is required'], 400);
            }

            // Check if setting exists
            $existing = $this->db->query(
                "SELECT id FROM settings WHERE key_name = ?",
                [$key]
            )->fetch();

            if ($existing) {
                // Update existing setting
                $this->db->query(
                    "UPDATE settings SET value = ?, updated_at = NOW() WHERE key_name = ?",
                    [$value, $key]
                );
            } else {
                // Create new setting
                $this->db->query(
                    "INSERT INTO settings (key_name, value, type) VALUES (?, ?, 'string')",
                    [$key, $value]
                );
            }

            return $this->json([
                'success' => true,
                'message' => 'Setting updated successfully'
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update multiple settings at once
     */
    public function updateBulk(Request $request): Response
    {
        try {
            $settings = $request->post('settings');

            if (!is_array($settings)) {
                return $this->json(['success' => false, 'message' => 'Invalid settings data'], 400);
            }

            $this->db->beginTransaction();

            foreach ($settings as $key => $value) {
                $existing = $this->db->query(
                    "SELECT id FROM settings WHERE key_name = ?",
                    [$key]
                )->fetch();

                if ($existing) {
                    $this->db->query(
                        "UPDATE settings SET value = ?, updated_at = NOW() WHERE key_name = ?",
                        [$value, $key]
                    );
                } else {
                    $this->db->query(
                        "INSERT INTO settings (key_name, value, type) VALUES (?, ?, 'string')",
                        [$key, $value]
                    );
                }
            }

            $this->db->commit();

            return $this->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);
        } catch (\Exception $e) {
            $this->db->rollback();
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get system information
     */
    public function getSystemInfo(Request $request): Response
    {
        try {
            $info = [
                'php_version' => phpversion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'database_version' => $this->db->query("SELECT VERSION() as version")->fetch()['version'],
                'max_upload_size' => ini_get('upload_max_filesize'),
                'max_post_size' => ini_get('post_max_size'),
                'memory_limit' => ini_get('memory_limit'),
                'timezone' => date_default_timezone_get(),
                'disk_space' => [
                    'total' => disk_total_space('.'),
                    'free' => disk_free_space('.')
                ]
            ];

            return $this->json([
                'success' => true,
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache(Request $request): Response
    {
        try {
            // Clear various caches (implement based on your caching strategy)
            $cleared = [];

            // Clear file cache if exists
            if (is_dir(ROOT_PATH . '/storage/cache')) {
                $files = glob(ROOT_PATH . '/storage/cache/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $cleared[] = basename($file);
                    }
                }
            }

            // Clear session cache
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            session_regenerate_id(true);

            return $this->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
                'cleared' => $cleared
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request): Response
    {
        try {
            $to = $request->post('email');

            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                return $this->json(['success' => false, 'message' => 'Invalid email address'], 400);
            }

            $subject = 'GoPlay - Email Test';
            $message = 'This is a test email from your GoPlay Sports Platform admin panel.';
            $headers = 'From: noreply@goplay.lk' . "\r\n" .
                      'Reply-To: support@goplay.lk' . "\r\n" .
                      'X-Mailer: PHP/' . phpversion();

            $sent = mail($to, $subject, $message, $headers);

            return $this->json([
                'success' => $sent,
                'message' => $sent ? 'Test email sent successfully' : 'Failed to send test email'
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Backup database
     */
    public function backupDatabase(Request $request): Response
    {
        try {
            $backupDir = ROOT_PATH . '/storage/backups';
            
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupDir . '/' . $filename;

            // Get database credentials
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $dbname = $_ENV['DB_NAME'] ?? 'goplay_sports_platform';
            $username = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASS'] ?? '';

            // Create backup using mysqldump
            $command = sprintf(
                'mysqldump -h%s -u%s %s %s > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                $password ? '-p' . escapeshellarg($password) : '',
                escapeshellarg($dbname),
                escapeshellarg($filepath)
            );

            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($filepath)) {
                return $this->json([
                    'success' => true,
                    'message' => 'Database backup created successfully',
                    'filename' => $filename,
                    'size' => filesize($filepath)
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Failed to create database backup'
            ], 500);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get activity logs
     */
    public function getActivityLogs(Request $request): Response
    {
        try {
            $limit = (int)$request->get('limit', 50);
            $offset = (int)$request->get('offset', 0);

            // If you have an activity_logs table, query it
            // For now, we'll return recent user activities from various tables
            $logs = $this->db->query(
                "SELECT 
                    'User Registration' as action,
                    CONCAT(first_name, ' ', last_name) as user,
                    created_at as timestamp,
                    'success' as status
                FROM users
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?",
                [$limit, $offset]
            )->fetchAll();

            return $this->json([
                'success' => true,
                'data' => $logs
            ]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper method to categorize settings
     */
    private function getCategoryFromKey(string $key): string
    {
        if (strpos($key, 'site_') === 0) return 'general';
        if (strpos($key, 'email_') === 0) return 'email';
        if (strpos($key, 'payment_') === 0) return 'payment';
        if (strpos($key, 'booking_') === 0) return 'booking';
        if (strpos($key, 'tax_') === 0 || strpos($key, 'service_fee') === 0) return 'financial';
        return 'other';
    }
}