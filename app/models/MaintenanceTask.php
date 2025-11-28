<?php

namespace App\Models;

use Core\BaseModel;
use PDO;

class MaintenanceTask extends BaseModel
{
    protected string $table = 'facility_maintenance_tasks';

    /**
     * Get all maintenance tasks for a specific owner
     */
    public function getByOwnerId($ownerId, $filters = [])
    {
        $sql = "SELECT
                    mt.*,
                    sf.name as facility_name,
                    sf.address as facility_address
                FROM {$this->table} mt
                INNER JOIN sports_facilities sf ON mt.facility_id = sf.id
                WHERE mt.owner_id = ?";

        $params = [$ownerId];

        // Apply filters
        if (!empty($filters['facility_id'])) {
            $sql .= " AND mt.facility_id = ?";
            $params[] = $filters['facility_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND mt.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND mt.priority = ?";
            $params[] = $filters['priority'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND mt.scheduled_date >= ?";
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND mt.scheduled_date <= ?";
            $params[] = $filters['end_date'];
        }

        $sql .= " ORDER BY
                    FIELD(mt.priority, 'urgent', 'high', 'medium', 'low'),
                    mt.scheduled_date ASC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active tasks (scheduled or in-progress)
     */
    public function getActiveTasks($ownerId)
    {
        $sql = "SELECT
                    mt.*,
                    sf.name as facility_name
                FROM {$this->table} mt
                INNER JOIN sports_facilities sf ON mt.facility_id = sf.id
                WHERE mt.owner_id = ?
                  AND mt.status IN ('scheduled', 'in-progress')
                ORDER BY
                    FIELD(mt.priority, 'urgent', 'high', 'medium', 'low'),
                    mt.scheduled_date ASC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get overdue tasks
     */
    public function getOverdueTasks($ownerId)
    {
        $sql = "SELECT
                    mt.*,
                    sf.name as facility_name
                FROM {$this->table} mt
                INNER JOIN sports_facilities sf ON mt.facility_id = sf.id
                WHERE mt.owner_id = ?
                  AND mt.status = 'scheduled'
                  AND mt.scheduled_date < CURDATE()
                ORDER BY mt.scheduled_date ASC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics for owner's maintenance tasks
     */
    public function getOwnerStats($ownerId)
    {
        // Active tasks count
        $sql = "SELECT
                    COUNT(*) as active_tasks,
                    SUM(CASE WHEN priority = 'high' OR priority = 'urgent' THEN 1 ELSE 0 END) as `high_priority`,
                    SUM(CASE WHEN priority = 'medium' THEN 1 ELSE 0 END) as `medium_priority`
                FROM {$this->table}
                WHERE owner_id = ?
                  AND status IN ('scheduled', 'in-progress')";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId]);
        $activeStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Overdue tasks count
        $sql = "SELECT COUNT(*) as overdue_count
                FROM {$this->table}
                WHERE owner_id = ?
                  AND status = 'scheduled'
                  AND scheduled_date < CURDATE()";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId]);
        $overdueStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Completed this month
        $sql = "SELECT
                    COUNT(*) as completed_count,
                    SUM(actual_cost) as total_cost
                FROM {$this->table}
                WHERE owner_id = ?
                  AND status = 'completed'
                  AND completed_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId]);
        $completedStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Monthly cost
        $sql = "SELECT
                    SUM(COALESCE(actual_cost, estimated_cost)) as monthly_cost
                FROM {$this->table}
                WHERE owner_id = ?
                  AND scheduled_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
                  AND scheduled_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId]);
        $costStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate completion rate
        $totalThisMonth = $activeStats['active_tasks'] + $completedStats['completed_count'];
        $completionRate = $totalThisMonth > 0
            ? round(($completedStats['completed_count'] / $totalThisMonth) * 100)
            : 0;

        return [
            'active_tasks' => (int)$activeStats['active_tasks'],
            'high_priority_tasks' => (int)$activeStats['high_priority'],
            'medium_priority_tasks' => (int)$activeStats['medium_priority'],
            'overdue_tasks' => (int)$overdueStats['overdue_count'],
            'completed_month' => (int)$completedStats['completed_count'],
            'completion_rate' => $completionRate,
            'monthly_cost' => (float)($costStats['monthly_cost'] ?? 0)
        ];
    }

    /**
     * Get tasks for calendar (grouped by date)
     */
    public function getCalendarTasks($ownerId, $startDate, $endDate)
    {
        $sql = "SELECT
                    mt.scheduled_date,
                    mt.status,
                    mt.priority,
                    mt.title,
                    mt.task_type,
                    sf.name as facility_name
                FROM {$this->table} mt
                INNER JOIN sports_facilities sf ON mt.facility_id = sf.id
                WHERE mt.owner_id = ?
                  AND mt.scheduled_date >= ?
                  AND mt.scheduled_date <= ?
                ORDER BY mt.scheduled_date ASC, mt.priority DESC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new maintenance task
     */
    public function createTask($data)
    {
        $sql = "INSERT INTO {$this->table} (
                    facility_id, owner_id, title, description, task_type, category,
                    priority, status, scheduled_date, estimated_duration, estimated_cost,
                    assigned_to, required_tools, block_bookings, send_notifications
                ) VALUES (
                    :facility_id, :owner_id, :title, :description, :task_type, :category,
                    :priority, :status, :scheduled_date, :estimated_duration, :estimated_cost,
                    :assigned_to, :required_tools, :block_bookings, :send_notifications
                )";

        $stmt = $this->db->getConnection()->prepare($sql);
        $success = $stmt->execute([
            'facility_id' => $data['facility_id'],
            'owner_id' => $data['owner_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'task_type' => $data['task_type'],
            'category' => $data['category'] ?? 'routine',
            'priority' => $data['priority'] ?? 'medium',
            'status' => $data['status'] ?? 'scheduled',
            'scheduled_date' => $data['scheduled_date'],
            'estimated_duration' => $data['estimated_duration'] ?? null,
            'estimated_cost' => $data['estimated_cost'] ?? 0,
            'assigned_to' => $data['assigned_to'] ?? null,
            'required_tools' => $data['required_tools'] ?? null,
            'block_bookings' => $data['block_bookings'] ?? false,
            'send_notifications' => $data['send_notifications'] ?? true
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    /**
     * Update task status
     */
    public function updateStatus($taskId, $status)
    {
        $updateData = ['status' => $status];

        if ($status === 'completed') {
            $updateData['completed_date'] = date('Y-m-d H:i:s');
        }

        return $this->update($taskId, $updateData);
    }

    /**
     * Update actual cost
     */
    public function updateCost($taskId, $actualCost)
    {
        return $this->update($taskId, ['actual_cost' => $actualCost]);
    }

    /**
     * Get task with progress updates
     */
    public function getTaskWithProgress($taskId)
    {
        $sql = "SELECT
                    mt.*,
                    sf.name as facility_name,
                    sf.address as facility_address
                FROM {$this->table} mt
                INNER JOIN sports_facilities sf ON mt.facility_id = sf.id
                WHERE mt.id = ?";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($task) {
            // Get progress updates
            $sql = "SELECT * FROM maintenance_progress_updates
                    WHERE task_id = ?
                    ORDER BY created_at DESC";

            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$taskId]);
            $task['progress_updates'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $task;
    }

    /**
     * Add progress update to a task
     */
    public function addProgressUpdate($taskId, $updateText, $progressPercentage = 0)
    {
        $sql = "INSERT INTO maintenance_progress_updates (task_id, update_text, progress_percentage)
                VALUES (?, ?, ?)";

        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([$taskId, $updateText, $progressPercentage]);
    }

    /**
     * Get cost trends (monthly breakdown)
     */
    public function getCostTrends($ownerId, $months = 6)
    {
        $sql = "SELECT
                    DATE_FORMAT(scheduled_date, '%Y-%m') as month,
                    SUM(COALESCE(actual_cost, estimated_cost)) as total_cost,
                    COUNT(*) as task_count,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
                FROM {$this->table}
                WHERE owner_id = ?
                  AND scheduled_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                GROUP BY month
                ORDER BY month ASC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId, $months]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a facility has blocking maintenance on a specific date
     */
    public function hasBlockingMaintenance($facilityId, $date)
    {
        $sql = "SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE facility_id = ?
                  AND scheduled_date = ?
                  AND block_bookings = 1
                  AND status IN ('scheduled', 'in-progress')";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$facilityId, $date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['count'] > 0;
    }
}
