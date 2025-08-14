<?php
/**
 * Validator Utility Class
 * Handles data validation with rules
 */

class Validator {
    
    private $errors = [];
    
    /**
     * Validate data against rules
     */
    public function validate($data, $rules) {
        $this->errors = [];
        
        foreach ($rules as $field => $rule) {
            $this->validateField($field, $data[$field] ?? null, $rule, $data);
        }
        
        return [
            'valid' => empty($this->errors),
            'errors' => $this->errors
        ];
    }
    
    /**
     * Validate individual field
     */
    private function validateField($field, $value, $rule, $allData) {
        $rules = explode('|', $rule);
        
        foreach ($rules as $singleRule) {
            $this->applyRule($field, $value, $singleRule, $allData);
        }
    }
    
    /**
     * Apply single validation rule
     */
    private function applyRule($field, $value, $rule, $allData) {
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $parameter = $parts[1] ?? null;
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, 'The ' . $field . ' field is required.');
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'The ' . $field . ' must be a valid email address.');
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $parameter) {
                    $this->addError($field, 'The ' . $field . ' must be at least ' . $parameter . ' characters.');
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $parameter) {
                    $this->addError($field, 'The ' . $field . ' must not exceed ' . $parameter . ' characters.');
                }
                break;
                
            case 'alpha':
                if (!empty($value) && !preg_match('/^[a-zA-Z\s]+$/', $value)) {
                    $this->addError($field, 'The ' . $field . ' must contain only letters.');
                }
                break;
                
            case 'alpha_num':
                if (!empty($value) && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    $this->addError($field, 'The ' . $field . ' must contain only letters and numbers.');
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, 'The ' . $field . ' must be numeric.');
                }
                break;
                
            case 'phone':
                if (!empty($value) && !preg_match('/^\+?[0-9\s\-\(\)]+$/', $value)) {
                    $this->addError($field, 'The ' . $field . ' must be a valid phone number.');
                }
                break;
                
            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, 'The ' . $field . ' must be a valid date.');
                }
                break;
                
            case 'same':
                $otherField = $parameter;
                if ($value !== ($allData[$otherField] ?? null)) {
                    $this->addError($field, 'The ' . $field . ' must match ' . $otherField . '.');
                }
                break;
                
            case 'in':
                $allowedValues = explode(',', $parameter);
                if (!empty($value) && !in_array($value, $allowedValues)) {
                    $this->addError($field, 'The ' . $field . ' must be one of: ' . implode(', ', $allowedValues) . '.');
                }
                break;
                
            case 'decimal':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, 'The ' . $field . ' must be a decimal number.');
                }
                break;
                
            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, 'The ' . $field . ' must be an integer.');
                }
                break;
                
            case 'positive':
                if (!empty($value) && (!is_numeric($value) || $value <= 0)) {
                    $this->addError($field, 'The ' . $field . ' must be a positive number.');
                }
                break;
                
            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, 'The ' . $field . ' must be a valid URL.');
                }
                break;
                
            case 'json':
                if (!empty($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $this->addError($field, 'The ' . $field . ' must be valid JSON.');
                    }
                }
                break;
                
            case 'array':
                if (!empty($value) && !is_array($value)) {
                    $this->addError($field, 'The ' . $field . ' must be an array.');
                }
                break;
                
            case 'boolean':
                if (!empty($value) && !is_bool($value) && !in_array($value, [0, 1, '0', '1', 'true', 'false'])) {
                    $this->addError($field, 'The ' . $field . ' must be true or false.');
                }
                break;
        }
    }
    
    /**
     * Add validation error
     */
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Check if validation passed
     */
    public function passes() {
        return empty($this->errors);
    }
    
    /**
     * Check if validation failed
     */
    public function fails() {
        return !empty($this->errors);
    }
    
    /**
     * Get first error for a field
     */
    public function getFirstError($field) {
        return $this->errors[$field][0] ?? null;
    }
    
    /**
     * Custom validation rule
     */
    public function addCustomRule($field, $callback, $message) {
        if (!$callback()) {
            $this->addError($field, $message);
        }
    }
    
    /**
     * Validate file upload
     */
    public function validateFile($field, $file, $maxSize = 5242880, $allowedTypes = []) {
        if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, 'The ' . $field . ' upload failed.');
            return;
        }
        
        // Check file size (default 5MB)
        if ($file['size'] > $maxSize) {
            $this->addError($field, 'The ' . $field . ' file is too large. Maximum size is ' . ($maxSize / 1024 / 1024) . 'MB.');
        }
        
        // Check file type
        if (!empty($allowedTypes)) {
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                $this->addError($field, 'The ' . $field . ' file type is not allowed.');
            }
        }
    }
}