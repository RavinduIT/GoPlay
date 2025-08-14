<?php
/**
 * Base Model Class
 * Common functionality for all models
 */

class BaseModel {
    
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;
    
    public function __construct() {
        $this->db = new DatabaseHelper();
    }
    
    /**
     * Find record by ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->getOne($sql, [$id]);
    }
    
    /**
     * Find all records
     */
    public function findAll($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $key => $value) {
                $whereClause[] = "$key = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->getAll($sql, $params);
    }
    
    /**
     * Create new record
     */
    public function create($data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $columns = implode(',', array_keys($data));
        $placeholders = str_repeat('?,', count($data) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        
        $id = $this->db->insert($sql, array_values($data));
        
        if ($id) {
            return $this->find($id);
        }
        
        return false;
    }
    
    /**
     * Update record
     */
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "$column = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE {$this->primaryKey} = ?";
        $params = array_merge(array_values($data), [$id]);
        
        $affected = $this->db->update($sql, $params);
        
        if ($affected > 0) {
            return $this->find($id);
        }
        
        return false;
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->delete($sql, [$id]) > 0;
    }
    
    /**
     * Check if record exists
     */
    public function exists($conditions) {
        $whereClause = [];
        $params = [];
        
        foreach ($conditions as $key => $value) {
            $whereClause[] = "$key = ?";
            $params[] = $value;
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE " . implode(' AND ', $whereClause);
        $result = $this->db->getOne($sql, $params);
        
        return $result['count'] > 0;
    }
    
    /**
     * Filter data based on fillable fields
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
}