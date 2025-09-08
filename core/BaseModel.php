<?php

namespace Core;

abstract class BaseModel
{
    protected Database $db;
    protected string $table;
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $casts = [];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->queryFirst($sql, [$id]);
        return $result ? $this->castAttributes($result) : null;
    }
    
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $statement = $this->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }
    
    public function where(array $conditions, string $operator = '='): array
    {
        $wheres = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            $wheres[] = "{$field} {$operator} ?";
            $params[] = $value;
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $wheres);
        $statement = $this->query($sql, $params);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return array_map([$this, 'castAttributes'], $results);
    }
    
    public function create(array $data): ?int
    {
        $data = $this->filterFillable($data);
        $fields = array_keys($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
        
        if ($this->query($sql, array_values($data))) {
            return $this->db->lastInsertId();
        }
        
        return null;
    }
    
    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);
        $sets = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $sets[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(',', $sets) . " WHERE id = ?";
        
        $statement = $this->query($sql, $params);
        return $statement && $statement->rowCount() > 0;
    }
    
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $statement = $this->query($sql, [$id]);
        return $statement && $statement->rowCount() > 0;
    }
    
    protected function query(string $sql, array $params = [])
    {
        return $this->db->query($sql, $params);
    }
    
    protected function queryFirst(string $sql, array $params = []): ?array
    {
        $results = $this->db->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
        return $results[0] ?? null;
    }
    
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    protected function castAttributes(array $attributes): array
    {
        foreach ($this->casts as $field => $type) {
            if (isset($attributes[$field])) {
                $attributes[$field] = $this->castAttribute($attributes[$field], $type);
            }
        }
        
        foreach ($this->hidden as $field) {
            unset($attributes[$field]);
        }
        
        return $attributes;
    }
    
    protected function castAttribute($value, string $type)
    {
        if ($value === null) {
            return null;
        }
        
        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
            case 'double':
                return (float) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'array':
                return is_string($value) ? json_decode($value, true) : $value;
            case 'date':
                return date('Y-m-d', strtotime($value));
            case 'datetime':
                return date('Y-m-d H:i:s', strtotime($value));
            default:
                return $value;
        }
    }
}