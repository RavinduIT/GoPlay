<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use Core\Database;

/**
 * Admin Sports Categories Controller
 * 
 * Manages sports categories (add, edit, deactivate)
 */
class AdminCategoryController extends BaseController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Check admin authentication
     */
    private function checkAdmin(): bool
    {
        $this->startSession();
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin';
    }

    /**
     * Display categories management page
     */
    public function index(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->redirect('/login');
        }

        $activePage = 'categories';
        return $this->view('admin/categories', compact('activePage'));
    }

    /**
     * API: Get all categories with usage counts
     */
    public function getCategories(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $sql = "SELECT 
                        sc.*,
                        (SELECT COUNT(*) FROM sports_facilities sf WHERE sf.sport_category_id = sc.id) as facilities_count,
                        (SELECT COUNT(*) FROM coaches c WHERE c.sport_category_id = sc.id) as coaches_count
                    FROM sports_categories sc
                    ORDER BY sc.name ASC";

            $categories = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

            // Stats
            $total = count($categories);
            $active = 0;
            $inactive = 0;
            foreach ($categories as $cat) {
                if ($cat['is_active']) $active++;
                else $inactive++;
            }

            return $this->json([
                'success' => true,
                'data' => $categories,
                'stats' => [
                    'total' => $total,
                    'active' => $active,
                    'inactive' => $inactive
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Get categories error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Create a new sport category
     */
    public function createCategory(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? 'fas fa-trophy');

            if (empty($name)) {
                return $this->json(['success' => false, 'message' => 'Category name is required'], 400);
            }

            // Check duplicate
            $existing = $this->db->query(
                "SELECT id FROM sports_categories WHERE name = ?",
                [$name]
            )->fetch();

            if ($existing) {
                return $this->json(['success' => false, 'message' => 'A category with this name already exists'], 400);
            }

            $this->db->query(
                "INSERT INTO sports_categories (name, description, icon, is_active) VALUES (?, ?, ?, 1)",
                [$name, $description, $icon]
            );

            return $this->json([
                'success' => true,
                'message' => 'Sports category created successfully'
            ], 201);
        } catch (\Exception $e) {
            error_log("Create category error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update a category
     */
    public function updateCategory(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $id = (int)$request->getParam('id');
            $data = $request->getJsonBody();

            if (!$id) {
                return $this->json(['success' => false, 'message' => 'Category ID is required'], 400);
            }

            // Check exists
            $existing = $this->db->query("SELECT id FROM sports_categories WHERE id = ?", [$id])->fetch();
            if (!$existing) {
                return $this->json(['success' => false, 'message' => 'Category not found'], 404);
            }

            $fields = [];
            $params = [];

            if (isset($data['name'])) {
                $fields[] = 'name = ?';
                $params[] = trim($data['name']);
            }
            if (isset($data['description'])) {
                $fields[] = 'description = ?';
                $params[] = trim($data['description']);
            }
            if (isset($data['icon'])) {
                $fields[] = 'icon = ?';
                $params[] = trim($data['icon']);
            }
            if (isset($data['is_active'])) {
                $fields[] = 'is_active = ?';
                $params[] = $data['is_active'] ? 1 : 0;
            }

            if (empty($fields)) {
                return $this->json(['success' => false, 'message' => 'No fields to update'], 400);
            }

            $params[] = $id;
            $sql = "UPDATE sports_categories SET " . implode(', ', $fields) . " WHERE id = ?";
            $this->db->query($sql, $params);

            return $this->json([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            error_log("Update category error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Delete (deactivate) a category
     */
    public function deleteCategory(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $id = (int)$request->getParam('id');

            if (!$id) {
                return $this->json(['success' => false, 'message' => 'Category ID is required'], 400);
            }

            // Soft-delete: set inactive to preserve FK relations
            $this->db->query("UPDATE sports_categories SET is_active = 0 WHERE id = ?", [$id]);

            return $this->json([
                'success' => true,
                'message' => 'Category deactivated successfully'
            ]);
        } catch (\Exception $e) {
            error_log("Delete category error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
