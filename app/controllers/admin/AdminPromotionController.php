<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use Core\Database;

/**
 * Admin Promotions/Banners Controller
 * 
 * Full CRUD for managing promotional banners displayed on the platform.
 */
class AdminPromotionController extends BaseController
{
    /**
     * Check admin authentication
     */
    private function checkAdmin(): bool
    {
        $this->startSession();
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin';
    }

    /**
     * Get database connection
     */
    private function getDatabase(): \PDO
    {
        return Database::getInstance()->getConnection();
    }

    /**
     * Display promotions management page
     */
    public function index(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->redirect('/login');
        }
        $activePage = 'promotions';
        return $this->view('admin/promotions', compact('activePage'));
    }

    /**
     * API: Get all promotions
     */
    public function getPromotions(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();
            $status = $_GET['status'] ?? 'all';

            $sql = "SELECT * FROM promotions";
            $params = [];

            if ($status === 'active') {
                $sql .= " WHERE is_active = 1";
            } elseif ($status === 'inactive') {
                $sql .= " WHERE is_active = 0";
            }

            $sql .= " ORDER BY priority DESC, created_at DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $promotions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Resolve image URLs for frontend
            foreach ($promotions as &$p) {
                if (!empty($p['image_url'])) {
                    $p['image_url_full'] = imgUrl('/public' . $p['image_url']);
                }
            }
            unset($p);

            // Stats
            $totalStmt = $db->query("SELECT COUNT(*) as total, SUM(is_active) as active FROM promotions");
            $stats = $totalStmt->fetch(\PDO::FETCH_ASSOC);

            return $this->json([
                'success' => true,
                'data' => $promotions,
                'stats' => [
                    'total' => (int)($stats['total'] ?? 0),
                    'active' => (int)($stats['active'] ?? 0),
                    'inactive' => (int)($stats['total'] ?? 0) - (int)($stats['active'] ?? 0)
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Get promotions error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get single promotion
     */
    public function getPromotion(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();
            $stmt = $db->prepare("SELECT * FROM promotions WHERE id = ?");
            $stmt->execute([$id]);
            $promotion = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$promotion) {
                return $this->json(['success' => false, 'message' => 'Promotion not found'], 404);
            }

            if (!empty($promotion['image_url'])) {
                $promotion['image_url_full'] = imgUrl('/public' . $promotion['image_url']);
            }

            return $this->json(['success' => true, 'data' => $promotion]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Create promotion
     */
    public function createPromotion(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $title = trim($_POST['title'] ?? '');
            $subtitle = trim($_POST['subtitle'] ?? '');
            $linkUrl = trim($_POST['link_url'] ?? '');
            $linkText = trim($_POST['link_text'] ?? 'Learn More');
            $position = $_POST['position'] ?? 'hero';
            $bgColor = $_POST['bg_color'] ?? '#3b82f6';
            $textColor = $_POST['text_color'] ?? '#ffffff';
            $priority = (int)($_POST['priority'] ?? 0);
            $isActive = (int)($_POST['is_active'] ?? 0);
            $startsAt = !empty($_POST['starts_at']) ? $_POST['starts_at'] : null;
            $endsAt = !empty($_POST['ends_at']) ? $_POST['ends_at'] : null;

            if (empty($title)) {
                return $this->json(['success' => false, 'message' => 'Title is required'], 400);
            }

            // Validate position
            $validPositions = ['hero', 'sidebar', 'footer', 'popup'];
            if (!in_array($position, $validPositions)) {
                $position = 'hero';
            }

            // Handle image upload
            $imageUrl = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload($_FILES['image']);
                if ($imageUrl === null) {
                    return $this->json(['success' => false, 'message' => 'Invalid image file. Only JPG, PNG, WebP, GIF allowed (max 5MB).'], 400);
                }
            }

            $db = $this->getDatabase();
            $sql = "INSERT INTO promotions (title, subtitle, image_url, link_url, link_text, position, bg_color, text_color, priority, is_active, starts_at, ends_at, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);
            $stmt->execute([
                $title, $subtitle, $imageUrl, $linkUrl, $linkText,
                $position, $bgColor, $textColor, $priority, $isActive,
                $startsAt, $endsAt, $_SESSION['user_id']
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Promotion created successfully',
                'id' => $db->lastInsertId()
            ], 201);
        } catch (\Exception $e) {
            error_log("Create promotion error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update promotion (supports both JSON and FormData)
     */
    public function updatePromotion(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();

            // Check promotion exists
            $checkStmt = $db->prepare("SELECT id, image_url FROM promotions WHERE id = ?");
            $checkStmt->execute([$id]);
            $existing = $checkStmt->fetch(\PDO::FETCH_ASSOC);
            if (!$existing) {
                return $this->json(['success' => false, 'message' => 'Promotion not found'], 404);
            }

            // Detect content type — support both JSON and FormData
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
            } else {
                $data = $_POST;
            }

            $fields = [];
            $params = [];

            $allowedFields = ['title', 'subtitle', 'link_url', 'link_text', 'position', 'bg_color', 'text_color', 'priority', 'starts_at', 'ends_at'];

            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $fields[] = "$field = ?";
                    $val = $data[$field];
                    // Handle empty datetime fields
                    if (in_array($field, ['starts_at', 'ends_at']) && empty($val)) {
                        $val = null;
                    }
                    $params[] = $val;
                }
            }

            // Handle is_active (could be "1", "0", true, false)
            if (array_key_exists('is_active', $data)) {
                $fields[] = "is_active = ?";
                $params[] = (int)(bool)$data['is_active'];
            }

            // Handle image upload for edit
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload($_FILES['image']);
                if ($imageUrl !== null) {
                    // Delete old image
                    if (!empty($existing['image_url'])) {
                        $oldPath = ROOT_PATH . '/public' . $existing['image_url'];
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $fields[] = "image_url = ?";
                    $params[] = $imageUrl;
                }
            }

            // Handle explicit image removal
            if (isset($data['remove_image']) && $data['remove_image']) {
                if (!empty($existing['image_url'])) {
                    $oldPath = ROOT_PATH . '/public' . $existing['image_url'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $fields[] = "image_url = ?";
                $params[] = null;
            }

            if (empty($fields)) {
                return $this->json(['success' => false, 'message' => 'No fields to update'], 400);
            }

            $params[] = $id;
            $sql = "UPDATE promotions SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            return $this->json(['success' => true, 'message' => 'Promotion updated successfully']);
        } catch (\Exception $e) {
            error_log("Update promotion error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Delete promotion
     */
    public function deletePromotion(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();

            // Get image path before deletion
            $stmt = $db->prepare("SELECT image_url FROM promotions WHERE id = ?");
            $stmt->execute([$id]);
            $promo = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$promo) {
                return $this->json(['success' => false, 'message' => 'Promotion not found'], 404);
            }

            // Delete from database
            $stmt = $db->prepare("DELETE FROM promotions WHERE id = ?");
            $stmt->execute([$id]);

            // Delete image file if exists (stored as /uploads/promotions/file.jpg)
            if (!empty($promo['image_url'])) {
                $filePath = ROOT_PATH . '/public' . $promo['image_url'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            return $this->json(['success' => true, 'message' => 'Promotion deleted successfully']);
        } catch (\Exception $e) {
            error_log("Delete promotion error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle image upload with proper MIME validation
     */
    private function handleImageUpload(array $file): ?string
    {
        // Use finfo for reliable MIME detection (don't trust $file['type'])
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowedTypes)) {
            return null;
        }

        // Max 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $uploadDir = ROOT_PATH . '/public/uploads/promotions/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'promo_' . uniqid() . '.' . strtolower($ext);
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return '/uploads/promotions/' . $filename;
        }

        return null;
    }
}
