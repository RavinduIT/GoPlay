<?php

namespace App\Models;

use Core\BaseModel;

/**
 * ShopOwnerProfile Model
 *
 * Handles shop owner profile information and business details
 */
class ShopOwnerProfile extends BaseModel
{
    protected string $table = 'shop_owner_profiles';

    protected array $fillable = [
        'user_id',
        'shop_name',
        'business_name',
        'business_registration_number',
        'tax_identification_number',
        'business_type',
        'year_established',
        'number_of_employees',
        'business_phone',
        'business_email',
        'shop_address',
        'shop_city',
        'shop_state',
        'shop_postal_code',
        'business_description',
        'product_categories',
        'brand_names',
        'website_url',
        'facebook',
        'instagram',
        'twitter',
        'shop_logo',
        'shop_banner',
        'business_license_document',
        'tax_document',
        'shop_images',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'bank_branch',
        'bank_swift_code',
        'delivery_options',
        'operating_hours',
        'return_policy',
        'warranty_policy',
        'profile_completion_percentage',
        'status'
    ];

    /**
     * Get profile by user ID
     */
    public function getByUserId(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1";
        $result = $this->queryFirst($sql, [$userId]);

        if (!$result) {
            // Create a default profile if it doesn't exist
            $this->createDefaultProfile($userId);
            $result = $this->queryFirst($sql, [$userId]);
        }

        // Decode JSON fields
        if ($result) {
            $result = $this->decodeJsonFields($result);
        }

        return $result;
    }

    /**
     * Create default profile for new shop owner
     */
    private function createDefaultProfile(int $userId): void
    {
        $data = [
            'user_id' => $userId,
            'profile_completion_percentage' => 10
        ];

        $this->create($data);
    }

    /**
     * Update profile for a shop owner
     */
    public function updateProfile(int $userId, array $data): bool
    {
        // Get existing profile
        $profile = $this->getByUserId($userId);

        if (!$profile) {
            error_log("ShopOwnerProfile: No profile found for user_id: " . $userId);
            return false;
        }

        error_log("ShopOwnerProfile: Updating profile for user_id: " . $userId);
        error_log("ShopOwnerProfile: Received data: " . json_encode($data));

        // Filter allowed fields
        $updateData = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field]) && $field !== 'user_id') {
                $updateData[$field] = $data[$field];
            }
        }
        
        error_log("ShopOwnerProfile: Filtered update data: " . json_encode($updateData));

        // Handle JSON fields
        if (isset($updateData['product_categories']) && is_array($updateData['product_categories'])) {
            $updateData['product_categories'] = json_encode($updateData['product_categories']);
        }

        if (isset($updateData['shop_images']) && is_array($updateData['shop_images'])) {
            $updateData['shop_images'] = json_encode($updateData['shop_images']);
        }

        if (isset($updateData['delivery_options']) && is_array($updateData['delivery_options'])) {
            $updateData['delivery_options'] = json_encode($updateData['delivery_options']);
        }

        if (isset($updateData['operating_hours']) && is_array($updateData['operating_hours'])) {
            $updateData['operating_hours'] = json_encode($updateData['operating_hours']);
        }

        // Calculate profile completion percentage
        $updateData['profile_completion_percentage'] = $this->calculateCompletionPercentage(
            array_merge($profile, $updateData)
        );
        
        // Convert empty strings to NULL for numeric fields
        $numericFields = ['year_established', 'number_of_employees'];
        foreach ($numericFields as $field) {
            if (isset($updateData[$field]) && $updateData[$field] === '') {
                $updateData[$field] = null;
            }
        }

        if (empty($updateData)) {
            error_log("ShopOwnerProfile: No data to update");
            return false;
        }

        // Update using BaseModel method
        $result = $this->update($profile['id'], $updateData);
        error_log("ShopOwnerProfile: Update result: " . ($result ? 'SUCCESS' : 'FAILED') . " for profile ID: " . $profile['id']);
        
        return $result;
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateCompletionPercentage(array $profile): int
    {
        $totalFields = 25;
        $completedFields = 0;

        // Essential fields (higher weight)
        $essentialFields = [
            'shop_name', 'business_name', 'business_phone', 'business_email',
            'shop_address', 'shop_city', 'business_description'
        ];

        // Important fields
        $importantFields = [
            'business_registration_number', 'tax_identification_number',
            'year_established', 'product_categories', 'shop_logo',
            'bank_name', 'bank_account_number', 'bank_account_holder'
        ];

        // Optional fields
        $optionalFields = [
            'website_url', 'facebook', 'instagram', 'business_license_document',
            'tax_document', 'shop_images', 'delivery_options', 'operating_hours',
            'return_policy', 'warranty_policy'
        ];

        // Count completed essential fields (weight: 3)
        foreach ($essentialFields as $field) {
            if (!empty($profile[$field])) {
                $completedFields += 3;
            }
        }

        // Count completed important fields (weight: 2)
        foreach ($importantFields as $field) {
            if (!empty($profile[$field])) {
                $completedFields += 2;
            }
        }

        // Count completed optional fields (weight: 1)
        foreach ($optionalFields as $field) {
            if (!empty($profile[$field])) {
                $completedFields += 1;
            }
        }

        // Calculate percentage (max weight: 21 + 16 + 10 = 47)
        $maxWeight = 47;
        $percentage = round(($completedFields / $maxWeight) * 100);

        return min(100, max(10, $percentage));
    }

    /**
     * Upload shop logo
     */
    public function uploadShopLogo(int $userId, array $file): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/shop-owners/' . $userId . '/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'logo.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $relativePath = 'uploads/shop-owners/' . $userId . '/' . $filename;
            
            // Update profile with logo path
            $this->updateProfile($userId, ['shop_logo' => $relativePath]);
            
            return $relativePath;
        }

        return null;
    }
    
    /**
     * Upload shop banner
     */
    public function uploadShopBanner(int $userId, array $file): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/shop-owners/' . $userId . '/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'banner.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $relativePath = 'uploads/shop-owners/' . $userId . '/' . $filename;
            
            // Update profile with banner path
            $this->updateProfile($userId, ['shop_banner' => $relativePath]);
            
            return $relativePath;
        }

        return null;
    }

    /**
     * Upload business documents
     */
    public function uploadDocument(int $userId, array $file, string $documentType): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/shop-owners/' . $userId . '/documents/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $documentType . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $relativePath = 'uploads/shop-owners/' . $userId . '/documents/' . $filename;
            
            // Update profile with document path
            $updateField = $documentType === 'license' ? 'business_license_document' : 'tax_document';
            $this->updateProfile($userId, [$updateField => $relativePath]);
            
            return $relativePath;
        }

        return null;
    }

    /**
     * Upload shop images
     */
    public function uploadShopImages(int $userId, array $files): array
    {
        $uploadDir = __DIR__ . '/../../public/uploads/shop-owners/' . $userId . '/images/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadedImages = [];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                // Validate file type
                if (!in_array($files['type'][$key], $allowedTypes)) {
                    continue;
                }

                // Generate unique filename
                $extension = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
                $filename = 'shop_' . time() . '_' . $key . '.' . $extension;
                $filepath = $uploadDir . $filename;

                // Move uploaded file
                if (move_uploaded_file($tmpName, $filepath)) {
                    $uploadedImages[] = 'uploads/shop-owners/' . $userId . '/images/' . $filename;
                }
            }
        }

        // Get existing shop images
        $profile = $this->getByUserId($userId);
        $existingImages = $profile['shop_images'] ?? [];
        
        // Merge with new images
        $allImages = array_merge($existingImages, $uploadedImages);
        
        // Update profile
        if (!empty($uploadedImages)) {
            $this->updateProfile($userId, ['shop_images' => $allImages]);
        }

        return $uploadedImages;
    }

    /**
     * Get bank details for a shop owner
     */
    public function getBankDetails(int $userId): ?array
    {
        $profile = $this->getByUserId($userId);
        
        if (!$profile) {
            return null;
        }

        return [
            'bank_name' => $profile['bank_name'] ?? '',
            'bank_account_number' => $profile['bank_account_number'] ?? '',
            'bank_account_holder' => $profile['bank_account_holder'] ?? '',
            'bank_branch' => $profile['bank_branch'] ?? '',
            'bank_swift_code' => $profile['bank_swift_code'] ?? ''
        ];
    }

    /**
     * Update statistics
     */
    public function updateStatistics(int $userId, array $stats): bool
    {
        $profile = $this->getByUserId($userId);
        
        if (!$profile) {
            return false;
        }

        $updateData = [];
        
        if (isset($stats['total_products'])) {
            $updateData['total_products'] = $stats['total_products'];
        }
        
        if (isset($stats['total_sales'])) {
            $updateData['total_sales'] = $stats['total_sales'];
        }
        
        if (isset($stats['total_revenue'])) {
            $updateData['total_revenue'] = $stats['total_revenue'];
        }
        
        if (isset($stats['average_rating'])) {
            $updateData['average_rating'] = $stats['average_rating'];
        }
        
        if (isset($stats['total_reviews'])) {
            $updateData['total_reviews'] = $stats['total_reviews'];
        }

        if (empty($updateData)) {
            return false;
        }

        return $this->update($profile['id'], $updateData);
    }

    /**
     * Decode JSON fields in profile data
     */
    private function decodeJsonFields(array $profile): array
    {
        $jsonFields = ['product_categories', 'shop_images', 'delivery_options', 'operating_hours'];
        
        foreach ($jsonFields as $field) {
            if (isset($profile[$field]) && is_string($profile[$field])) {
                $decoded = json_decode($profile[$field], true);
                $profile[$field] = $decoded ?? [];
            }
        }

        return $profile;
    }

    /**
     * Get all shop owners with profiles
     */
    public function getAllShopOwners(): array
    {
        $sql = "SELECT 
                    sop.*,
                    u.username,
                    u.email as user_email,
                    u.first_name,
                    u.last_name,
                    u.status as user_status
                FROM {$this->table} sop
                INNER JOIN users u ON sop.user_id = u.id
                ORDER BY sop.created_at DESC";
        
        $statement = $this->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map([$this, 'decodeJsonFields'], $results);
    }

    /**
     * Get active shop owners
     */
    public function getActiveShopOwners(): array
    {
        $sql = "SELECT 
                    sop.*,
                    u.username,
                    u.email as user_email,
                    u.first_name,
                    u.last_name
                FROM {$this->table} sop
                INNER JOIN users u ON sop.user_id = u.id
                WHERE sop.status = 'active' AND u.status = 'active'
                ORDER BY sop.total_sales DESC";
        
        $statement = $this->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        return array_map([$this, 'decodeJsonFields'], $results);
    }
}
