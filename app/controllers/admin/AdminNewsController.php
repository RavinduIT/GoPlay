<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use App\Models\News;

/**
 * Admin News Controller
 * 
 * Handles CRUD operations for news articles in admin panel
 */
class AdminNewsController extends BaseController
{
    private News $newsModel;

    public function __construct()
    {
        $this->newsModel = new News();
    }

    /**
     * Check if user is admin
     */
    private function checkAdmin(): bool
    {
        $this->startSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * Display news management page
     */
    public function index(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->redirect('/login');
        }

        try {
            // Get all news articles (including drafts)
            $sql = "SELECT n.*, u.first_name, u.last_name 
                    FROM news n 
                    LEFT JOIN users u ON n.author_id = u.id 
                    ORDER BY n.created_at DESC";
            
            $db = \Core\Database::getInstance();
            $news = $db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

            // Get statistics
            $stats = $this->newsModel->getStatistics();

            return $this->view('admin.news.index', [
                'news' => $news,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            error_log("Admin news index error: " . $e->getMessage());
            return $this->view('errors.500', [
                'error' => 'Unable to load news articles'
            ]);
        }
    }

    /**
     * Show create news form
     */
    public function create(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->redirect('/login');
        }

        return $this->view('admin.news.create');
    }

    /**
     * Store new news article
     */
    public function store(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $this->startSession();
            
            // Get data from POST (FormData sends via $_POST and $_FILES)
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $category = trim($_POST['category'] ?? 'General Sports');
            $status = $_POST['status'] ?? 'draft';
            
            // Validate required fields
            if (empty($title)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Title is required'
                ], 400);
            }

            if (empty($content)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Content is required'
                ], 400);
            }

            // Generate slug from title
            $slug = $this->generateSlug($title);
            
            // Handle file upload
            $featuredImage = null;
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $featuredImage = $this->uploadImage($_FILES['featured_image']);
                } catch (\Exception $e) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Image upload failed: ' . $e->getMessage()
                    ], 400);
                }
            }

            // Auto-generate excerpt if not provided
            if (empty($excerpt)) {
                $excerpt = substr(strip_tags($content), 0, 200);
            }

            // Prepare data
            $data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'featured_image' => $featuredImage,
                'category' => $category,
                'status' => $status,
                'author_id' => $_SESSION['user_id'],
                'published_at' => $status === 'published' ? date('Y-m-d H:i:s') : null
            ];

            // Create news article
            $newsId = $this->newsModel->create($data);

            if ($newsId) {
                return $this->json([
                    'success' => true,
                    'message' => 'News article created successfully',
                    'id' => $newsId
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create news article'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log("Store news error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show edit news form
     */
    public function edit(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->redirect('/login');
        }

        try {
            $id = $request->getParam('id');
            $news = $this->newsModel->find($id);

            if (!$news) {
                return $this->view('errors.404', [
                    'message' => 'News article not found'
                ]);
            }

            return $this->view('admin.news.edit', [
                'news' => $news
            ]);

        } catch (\Exception $e) {
            error_log("Edit news error: " . $e->getMessage());
            return $this->view('errors.500', [
                'error' => 'Unable to load news article'
            ]);
        }
    }

    /**
     * Update news article
     */
    public function update(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Get ID from URL parameter or POST data
            $id = $request->getParam('id') ?? $_POST['id'] ?? null;
            
            if (!$id) {
                return $this->json([
                    'success' => false,
                    'message' => 'News ID is required'
                ], 400);
            }

            $news = $this->newsModel->find($id);

            if (!$news) {
                return $this->json([
                    'success' => false,
                    'message' => 'News article not found'
                ], 404);
            }

            // Get data from POST
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $category = trim($_POST['category'] ?? 'General Sports');
            $status = $_POST['status'] ?? 'draft';

            // Validate required fields
            if (empty($title)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Title is required'
                ], 400);
            }

            if (empty($content)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Content is required'
                ], 400);
            }

            // Generate new slug if title changed
            $slug = $news['slug'];
            if ($title !== $news['title']) {
                $slug = $this->generateSlug($title, $id);
            }

            // Handle file upload
            $featuredImage = $news['featured_image'];
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                // Delete old image
                if ($featuredImage && file_exists(ROOT_PATH . $featuredImage)) {
                    @unlink(ROOT_PATH . $featuredImage);
                }
                try {
                    $featuredImage = $this->uploadImage($_FILES['featured_image']);
                } catch (\Exception $e) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Image upload failed: ' . $e->getMessage()
                    ], 400);
                }
            }

            // Auto-generate excerpt if not provided
            if (empty($excerpt)) {
                $excerpt = substr(strip_tags($content), 0, 200);
            }

            // Prepare data
            $data = [
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'excerpt' => $excerpt,
                'featured_image' => $featuredImage,
                'category' => $category,
                'status' => $status
            ];

            // Set published_at if changing from draft to published
            if ($status === 'published' && $news['status'] !== 'published') {
                $data['published_at'] = date('Y-m-d H:i:s');
            }

            // Update news article
            $updated = $this->newsModel->update($id, $data);

            if ($updated) {
                return $this->json([
                    'success' => true,
                    'message' => 'News article updated successfully'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to update news article'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log("Update news error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete news article
     */
    public function delete(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $id = $request->getParam('id');
            $news = $this->newsModel->find($id);

            if (!$news) {
                return $this->json([
                    'success' => false,
                    'message' => 'News article not found'
                ], 404);
            }

            // Delete featured image
            if ($news['featured_image'] && file_exists(ROOT_PATH . $news['featured_image'])) {
                @unlink(ROOT_PATH . $news['featured_image']);
            }

            // Delete news article
            $deleted = $this->newsModel->delete($id);

            if ($deleted) {
                return $this->json([
                    'success' => true,
                    'message' => 'News article deleted successfully'
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to delete news article'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log("Delete news error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique slug from title
     */
    private function generateSlug(string $title, ?int $excludeId = null): string
    {
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Ensure slug is not empty
        if (empty($slug)) {
            $slug = 'news-' . time();
        }

        // Check if slug exists
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $sql = "SELECT id FROM news WHERE slug = ?";
            $params = [$slug];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $db = \Core\Database::getInstance();
            $result = $db->query($sql, $params)->fetch(\PDO::FETCH_ASSOC);
            
            if (!$result) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Upload image
     */
    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            throw new \Exception('File too large. Maximum size is 5MB.');
        }

        // Create upload directory if it doesn't exist
        $uploadDir = ROOT_PATH . '/public/assets/images/news/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception('Failed to create upload directory.');
            }
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'news-' . time() . '-' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return '/public/assets/images/news/' . $filename;
        }

        throw new \Exception('Failed to upload image.');
    }
}