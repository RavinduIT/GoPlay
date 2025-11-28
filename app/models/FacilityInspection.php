<?php

namespace App\Models;

use Core\BaseModel;
use PDO;

class FacilityInspection extends BaseModel
{
    protected string $table = 'facility_inspections';

    /**
     * Get all inspections for a specific owner
     */
    public function getByOwnerId($ownerId, $filters = [])
    {
        $sql = "SELECT
                    fi.*,
                    sf.name as facility_name
                FROM {$this->table} fi
                INNER JOIN sports_facilities sf ON fi.facility_id = sf.id
                WHERE fi.owner_id = ?";

        $params = [$ownerId];

        // Apply filters
        if (!empty($filters['facility_id'])) {
            $sql .= " AND fi.facility_id = ?";
            $params[] = $filters['facility_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND fi.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['inspection_type'])) {
            $sql .= " AND fi.inspection_type = ?";
            $params[] = $filters['inspection_type'];
        }

        $sql .= " ORDER BY fi.inspection_date ASC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get upcoming inspections
     */
    public function getUpcoming($ownerId, $limit = 10)
    {
        $sql = "SELECT
                    fi.*,
                    sf.name as facility_name
                FROM {$this->table} fi
                INNER JOIN sports_facilities sf ON fi.facility_id = sf.id
                WHERE fi.owner_id = ?
                  AND fi.status = 'scheduled'
                  AND fi.inspection_date >= CURDATE()
                ORDER BY fi.inspection_date ASC
                LIMIT ?";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new inspection
     */
    public function createInspection($data)
    {
        $sql = "INSERT INTO {$this->table} (
                    facility_id, owner_id, inspection_type, inspector,
                    inspection_date, inspection_time, status, notes
                ) VALUES (
                    :facility_id, :owner_id, :inspection_type, :inspector,
                    :inspection_date, :inspection_time, :status, :notes
                )";

        $stmt = $this->db->getConnection()->prepare($sql);
        $success = $stmt->execute([
            'facility_id' => $data['facility_id'],
            'owner_id' => $data['owner_id'],
            'inspection_type' => $data['inspection_type'],
            'inspector' => $data['inspector'] ?? null,
            'inspection_date' => $data['inspection_date'],
            'inspection_time' => $data['inspection_time'] ?? null,
            'status' => $data['status'] ?? 'scheduled',
            'notes' => $data['notes'] ?? null
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    /**
     * Complete an inspection with results
     */
    public function completeInspection($inspectionId, $data)
    {
        $sql = "UPDATE {$this->table}
                SET status = 'completed',
                    pass_status = :pass_status,
                    score = :score,
                    findings = :findings,
                    recommendations = :recommendations,
                    completed_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->getConnection()->prepare($sql);
        return $stmt->execute([
            'id' => $inspectionId,
            'pass_status' => $data['pass_status'] ?? 'pending',
            'score' => $data['score'] ?? null,
            'findings' => $data['findings'] ?? null,
            'recommendations' => $data['recommendations'] ?? null
        ]);
    }

    /**
     * Get facility health scores
     */
    public function getFacilityHealthScores($ownerId)
    {
        $sql = "SELECT
                    fi.facility_id,
                    sf.name as facility_name,
                    AVG(fi.score) as avg_score,
                    COUNT(*) as inspection_count,
                    SUM(CASE WHEN fi.pass_status = 'passed' THEN 1 ELSE 0 END) as passed_count,
                    MAX(fi.inspection_date) as last_inspection
                FROM {$this->table} fi
                INNER JOIN sports_facilities sf ON fi.facility_id = sf.id
                WHERE fi.owner_id = ?
                  AND fi.status = 'completed'
                  AND fi.score IS NOT NULL
                GROUP BY fi.facility_id, sf.name
                ORDER BY avg_score DESC";

        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
