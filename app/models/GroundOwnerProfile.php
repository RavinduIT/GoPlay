<?php

namespace App\Models;

use Core\BaseModel;

/**
 * GroundOwnerProfile Model
 *
 * Handles ground owner profile information and business details
 */
class GroundOwnerProfile extends BaseModel
{
    protected string $table = 'ground_owner_profiles';

    protected array $fillable = [
        'user_id',
        'business_name',
        'business_registration_number',
        'business_address',
        'business_phone',
        'business_email',
        'bio',
        'years_in_business',
        'total_facilities',
        'website',
        'facebook',
        'instagram',
        'twitter',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'payment_methods',
        'profile_completion_percentage'
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

        return $result;
    }

    /**
     * Create default profile for new ground owner
     */
    private function createDefaultProfile(int $userId): void
    {
        $data = [
            'user_id' => $userId,
            'profile_completion_percentage' => 20
        ];

        $this->create($data);
    }

    /**
     * Update profile for a ground owner
     */
    public function updateProfile(int $userId, array $data): bool
    {
        // Get existing profile
        $profile = $this->getByUserId($userId);

        if (!$profile) {
            return false;
        }

        // Filter allowed fields
        $updateData = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field]) && $field !== 'user_id') {
                $updateData[$field] = $data[$field];
            }
        }

        // Handle JSON fields
        if (isset($updateData['payment_methods']) && is_array($updateData['payment_methods'])) {
            $updateData['payment_methods'] = json_encode($updateData['payment_methods']);
        }

        // Calculate profile completion percentage
        $updateData['profile_completion_percentage'] = $this->calculateCompletionPercentage(
            array_merge($profile, $updateData)
        );

        if (empty($updateData)) {
            return false;
        }

        return $this->update($profile['id'], $updateData);
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateCompletionPercentage(array $profile): int
    {
        $totalFields = 15;
        $completedFields = 0;

        $fieldsToCheck = [
            'business_name',
            'business_registration_number',
            'business_address',
            'business_phone',
            'business_email',
            'bio',
            'years_in_business',
            'website',
            'facebook',
            'instagram',
            'bank_name',
            'bank_account_number',
            'bank_account_holder',
            'payment_methods'
        ];

        foreach ($fieldsToCheck as $field) {
            if (!empty($profile[$field])) {
                $completedFields++;
            }
        }

        return (int)(($completedFields / $totalFields) * 100);
    }

    /**
     * Get profile with user details
     */
    public function getProfileWithUser(int $userId): ?array
    {
        $sql = "SELECT
                    gop.*,
                    u.username,
                    u.email,
                    u.first_name,
                    u.last_name,
                    u.phone,
                    u.profile_picture,
                    u.created_at as user_created_at
                FROM {$this->table} gop
                JOIN users u ON gop.user_id = u.id
                WHERE gop.user_id = ?
                LIMIT 1";

        return $this->queryFirst($sql, [$userId]);
    }

    /**
     * Update total facilities count
     */
    public function updateFacilitiesCount(int $userId, int $count): bool
    {
        $profile = $this->getByUserId($userId);

        if (!$profile) {
            return false;
        }

        return $this->update($profile['id'], ['total_facilities' => $count]);
    }

    /**
     * Get all active ground owner profiles
     */
    public function getAllActive(): array
    {
        $sql = "SELECT
                    gop.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.profile_picture,
                    u.status as user_status
                FROM {$this->table} gop
                JOIN users u ON gop.user_id = u.id
                WHERE u.status = 'active' AND u.user_type = 'ground_owner'
                ORDER BY gop.created_at DESC";

        return $this->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Search ground owner profiles
     */
    public function search(string $searchTerm): array
    {
        $sql = "SELECT
                    gop.*,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.profile_picture
                FROM {$this->table} gop
                JOIN users u ON gop.user_id = u.id
                WHERE (
                    gop.business_name LIKE ? OR
                    u.first_name LIKE ? OR
                    u.last_name LIKE ? OR
                    u.email LIKE ? OR
                    gop.business_address LIKE ?
                )
                AND u.status = 'active' AND u.user_type = 'ground_owner'
                ORDER BY gop.business_name ASC";

        $searchPattern = '%' . $searchTerm . '%';
        $params = array_fill(0, 5, $searchPattern);

        return $this->query($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
