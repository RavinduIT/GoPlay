<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\Request;
use Core\Response;
use Core\Database;

/**
 * Admin Contact Messages Controller
 * 
 * Full CRUD for managing contact form submissions.
 */
class AdminContactController extends BaseController
{
    private function checkAdmin(): bool
    {
        $this->startSession();
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin';
    }

    private function getDatabase(): \PDO
    {
        return Database::getInstance()->getConnection();
    }

    /**
     * Display contact messages management page
     */
    public function index(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->redirect('/login');
        }
        $activePage = 'contacts';
        return $this->view('admin/contacts', compact('activePage'));
    }

    /**
     * API: Get all messages with filtering
     */
    public function getMessages(Request $request): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();
            $status = $_GET['status'] ?? 'all';
            $search = trim($_GET['search'] ?? '');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 15;
            $offset = ($page - 1) * $limit;

            $where = [];
            $params = [];

            if ($status !== 'all') {
                $where[] = "status = ?";
                $params[] = $status;
            }

            if (!empty($search)) {
                $where[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $whereSql = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

            // Count
            $countSql = "SELECT COUNT(*) as total FROM contact_messages {$whereSql}";
            $countStmt = $db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Data
            $sql = "SELECT cm.*, u.first_name as replied_by_name
                    FROM contact_messages cm
                    LEFT JOIN users u ON cm.replied_by = u.id
                    {$whereSql}
                    ORDER BY cm.created_at DESC
                    LIMIT ? OFFSET ?";

            $params[] = $limit;
            $params[] = $offset;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Stats
            $statsStmt = $db->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) as unread,
                SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as `read`,
                SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied,
                SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived
            FROM contact_messages");
            $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

            return $this->json([
                'success' => true,
                'data' => $messages,
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_items' => (int)$total,
                    'per_page' => $limit
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Get messages error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get single message
     */
    public function getMessage(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();
            $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            $message = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$message) {
                return $this->json(['success' => false, 'message' => 'Message not found'], 404);
            }

            // Auto-mark as read
            if ($message['status'] === 'unread') {
                $db->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$id]);
                $message['status'] = 'read';
            }

            return $this->json(['success' => true, 'data' => $message]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Reply to a message (sends email)
     */
    public function replyMessage(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $data = $request->getJsonBody();
            $reply = trim($data['reply'] ?? '');

            if (empty($reply)) {
                return $this->json(['success' => false, 'message' => 'Reply message is required'], 400);
            }

            $db = $this->getDatabase();

            // Get original message
            $stmt = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);
            $message = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$message) {
                return $this->json(['success' => false, 'message' => 'Message not found'], 404);
            }

            // Update message with reply
            $updateStmt = $db->prepare(
                "UPDATE contact_messages SET status = 'replied', admin_reply = ?, replied_by = ?, replied_at = NOW() WHERE id = ?"
            );
            $updateStmt->execute([$reply, $_SESSION['user_id'], $id]);

            // Send email reply
            try {
                $emailService = new \App\Services\EmailService();
                $subject = "Re: " . $message['subject'] . " - GoPlay Support";
                $htmlBody = "<h2>GoPlay Support Reply</h2>"
                    . "<p>Hi " . htmlspecialchars($message['name']) . ",</p>"
                    . "<p>Thank you for contacting GoPlay. Here is our response to your inquiry:</p>"
                    . "<blockquote style='border-left: 3px solid #3b82f6; padding: 12px 16px; margin: 16px 0; background: #f8fafc; border-radius: 4px;'>"
                    . nl2br(htmlspecialchars($reply))
                    . "</blockquote>"
                    . "<hr style='border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;'>"
                    . "<p style='color: #64748b; font-size: 13px;'><strong>Your original message:</strong></p>"
                    . "<p style='color: #64748b; font-size: 13px;'>" . nl2br(htmlspecialchars($message['message'])) . "</p>"
                    . "<br><p>Best regards,<br><strong>GoPlay Support Team</strong></p>";

                $emailService->sendNotification(
                    $message['email'],
                    $message['name'],
                    $subject,
                    $htmlBody
                );
            } catch (\Exception $emailErr) {
                error_log("Reply email failed: " . $emailErr->getMessage());
            }

            return $this->json(['success' => true, 'message' => 'Reply sent successfully']);
        } catch (\Exception $e) {
            error_log("Reply message error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Update message status
     */
    public function updateStatus(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $data = $request->getJsonBody();
            $status = $data['status'] ?? '';

            $validStatuses = ['unread', 'read', 'replied', 'archived'];
            if (!in_array($status, $validStatuses)) {
                return $this->json(['success' => false, 'message' => 'Invalid status'], 400);
            }

            $db = $this->getDatabase();
            $stmt = $db->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);

            return $this->json(['success' => true, 'message' => 'Status updated']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Delete message
     */
    public function deleteMessage(Request $request, int $id): Response
    {
        if (!$this->checkAdmin()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $db = $this->getDatabase();
            $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$id]);

            return $this->json(['success' => true, 'message' => 'Message deleted successfully']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Public: Handle contact form submission (not admin-only)
     */
    public function submitContact(Request $request): Response
    {
        try {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                return $this->json(['success' => false, 'message' => 'All required fields must be filled'], 400);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->json(['success' => false, 'message' => 'Invalid email address'], 400);
            }

            $db = $this->getDatabase();
            $stmt = $db->prepare(
                "INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, status) 
                 VALUES (?, ?, ?, ?, ?, ?, 'unread')"
            );
            $stmt->execute([
                $name, $email, $phone ?: null, $subject, $message,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Your message has been sent. We will get back to you soon!'
            ], 201);
        } catch (\Exception $e) {
            error_log("Contact form error: " . $e->getMessage());
            return $this->json(['success' => false, 'message' => 'Failed to send message. Please try again.'], 500);
        }
    }
}
