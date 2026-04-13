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
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $startsAt = !empty($_POST['starts_at']) ? $_POST['starts_at'] : null;
            $endsAt = !empty($_POST['ends_at']) ? $_POST['ends_at'] : null;

            if (empty($title)) {
                return $this->json(['success' => false, 'message' => 'Title is required'], 400);
            }

            // Handle image upload
            $imageUrl = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->handleImageUpload($_FILES['image']);
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
     * API: Update promotion
     */
    public function updatePromotion(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();
            $data = $request->getJsonBody();

            if (empty($data)) {
                // Try POST data (for form submissions)
                $data = $_POST;
            }

            $fields = [];
            $params = [];

            $allowedFields = ['title', 'subtitle', 'link_url', 'link_text', 'position', 'bg_color', 'text_color', 'priority', 'starts_at', 'ends_at'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }

            if (isset($data['is_active'])) {
                $fields[] = "is_active = ?";
                $params[] = $data['is_active'] ? 1 : 0;
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

            // Delete from database
            $stmt = $db->prepare("DELETE FROM promotions WHERE id = ?");
            $stmt->execute([$id]);

            // Delete image file if exists
            if ($promo && $promo['image_url'] && file_exists(ROOT_PATH . '/public' . $promo['image_url'])) {
                unlink(ROOT_PATH . '/public' . $promo['image_url']);
            }

            return $this->json(['success' => true, 'message' => 'Promotion deleted successfully']);
        } catch (\Exception $e) {
            error_log("Delete promotion error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $uploadDir = ROOT_PATH . '/public/uploads/promotions/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'promo_' . uniqid() . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return '/uploads/promotions/' . $filename;
        }

        return null;
    }
}
