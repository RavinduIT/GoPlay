<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\User;

class ProviderController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show role selection page
     */
    public function join(Request $request): Response
    {
        $this->startSession();

        // Check if user is already a provider
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] !== 'user') {
            return $this->redirect('/');
        }

        return $this->viewWithoutLayout('provider/role-selection');
    }

    /**
     * Show ground owner application form
     */
    public function applyGroundOwner(Request $request): Response
    {
        $this->startSession();
        return $this->viewWithoutLayout('provider/ground-owner-form');
    }

    /**
     * Show coach application form
     */
    public function applyCoach(Request $request): Response
    {
        $this->startSession();
        return $this->viewWithoutLayout('provider/coach-form');
    }

    /**
     * Show shop owner application form
     */
    public function applyShopOwner(Request $request): Response
    {
        $this->startSession();
        return $this->viewWithoutLayout('provider/shop-owner-form');
    }

    /**
     * Submit provider application
     */
    public function submitApplication(Request $request): Response
    {
        $this->startSession();

        try {
            // Get form data
            $providerType = $_POST['provider_type'] ?? '';

            if (empty($providerType)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Provider type is required'
                ], 400);
            }

            // Validate required fields based on provider type
            $validationResult = $this->validateApplicationData($providerType, $_POST, $_FILES);

            if (!$validationResult['valid']) {
                return $this->json([
                    'success' => false,
                    'message' => $validationResult['message']
                ], 400);
            }

            // Handle file uploads
            $uploadedFiles = $this->handleFileUploads($_FILES, $providerType);

            if (!$uploadedFiles['success']) {
                return $this->json([
                    'success' => false,
                    'message' => $uploadedFiles['message']
                ], 400);
            }

            // Prepare application data
            $applicationData = $this->prepareApplicationData($providerType, $_POST, $uploadedFiles['files']);

            // Save to database
            $applicationId = $this->saveApplication($applicationData);

            if ($applicationId) {
                // Send notification email to admin
                $this->sendAdminNotification($providerType, $_POST['email'], $applicationId);

                // Send confirmation email to applicant
                $this->sendApplicantConfirmation($_POST['email'], $providerType);

                return $this->json([
                    'success' => true,
                    'message' => 'Application submitted successfully! You will receive an email once reviewed.',
                    'application_id' => $applicationId
                ]);
            } else {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to save application. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            error_log("Application submission error: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'An error occurred while processing your application.'
            ], 500);
        }
    }

    /**
     * Validate application data
     */
    private function validateApplicationData(string $providerType, array $postData, array $files): array
    {
        // Common required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city'];

        // Add provider-specific required fields
        switch ($providerType) {
            case 'ground_owner':
                $requiredFields = array_merge($requiredFields, [
                    'facility_name', 'facility_address', 'facility_city',
                    'number_of_courts', 'proposed_hourly_rate', 'facility_description'
                ]);
                break;

            case 'coach':
                $requiredFields = array_merge($requiredFields, [
                    'sport_specialization', 'experience_years', 'session_rate',
                    'qualifications', 'bio', 'previous_experience', 'date_of_birth'
                ]);
                break;

            case 'shop_owner':
                $requiredFields = array_merge($requiredFields, [
                    'shop_name', 'business_registration_number', 'shop_address',
                    'shop_city', 'business_type', 'year_established', 'business_description'
                ]);
                break;

            default:
                return ['valid' => false, 'message' => 'Invalid provider type'];
        }

        // Check required fields
        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                return ['valid' => false, 'message' => "Field '{$field}' is required"];
            }
        }

        // Validate email
        if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Invalid email address'];
        }

        // Validate phone (10 digits)
        $phone = preg_replace('/\D/', '', $postData['phone']);
        if (strlen($phone) !== 10) {
            return ['valid' => false, 'message' => 'Phone number must be 10 digits'];
        }

        // Check required files
        if (empty($files['nic_document']['name'])) {
            return ['valid' => false, 'message' => 'NIC document is required'];
        }

        return ['valid' => true];
    }

    /**
     * Handle file uploads
     */
    private function handleFileUploads(array $files, string $providerType): array
    {
        $uploadedFiles = [];
        $uploadDir = __DIR__ . '/../../public/uploads/provider-applications/';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique folder for this application
        $applicationFolder = uniqid('app_') . '_' . time();
        $applicationPath = $uploadDir . $applicationFolder . '/';
        mkdir($applicationPath, 0755, true);

        try {
            foreach ($files as $fieldName => $file) {
                // Handle multiple files
                if (is_array($file['name'])) {
                    $uploadedFiles[$fieldName] = [];

                    foreach ($file['name'] as $index => $fileName) {
                        if (!empty($fileName) && $file['error'][$index] === UPLOAD_ERR_OK) {
                            $newFileName = $this->sanitizeFileName($fileName);
                            $destination = $applicationPath . $newFileName;

                            if (move_uploaded_file($file['tmp_name'][$index], $destination)) {
                                $uploadedFiles[$fieldName][] = $applicationFolder . '/' . $newFileName;
                            }
                        }
                    }
                } else {
                    // Handle single file
                    if (!empty($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
                        $newFileName = $this->sanitizeFileName($file['name']);
                        $destination = $applicationPath . $newFileName;

                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            $uploadedFiles[$fieldName] = $applicationFolder . '/' . $newFileName;
                        }
                    }
                }
            }

            return [
                'success' => true,
                'files' => $uploadedFiles
            ];

        } catch (\Exception $e) {
            error_log("File upload error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to upload files'
            ];
        }
    }

    /**
     * Sanitize file name
     */
    private function sanitizeFileName(string $fileName): string
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        return $baseName . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Prepare application data for database
     */
    private function prepareApplicationData(string $providerType, array $postData, array $files): array
    {
        $commonData = [
            'provider_type' => $providerType,
            'first_name' => $postData['first_name'],
            'last_name' => $postData['last_name'],
            'email' => $postData['email'],
            'phone' => $postData['phone'],
            'address' => $postData['address'],
            'city' => $postData['city'],
            'postal_code' => $postData['postal_code'] ?? '',
            'nic_document' => $files['nic_document'] ?? '',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['user_id'] ?? null
        ];

        // Add provider-specific data
        $specificData = [];

        switch ($providerType) {
            case 'ground_owner':
                $specificData = [
                    'facility_name' => $postData['facility_name'],
                    'facility_address' => $postData['facility_address'],
                    'facility_city' => $postData['facility_city'],
                    'facility_postal' => $postData['facility_postal'] ?? '',
                    'sport_types' => json_encode($postData['sport_types'] ?? []),
                    'number_of_courts' => $postData['number_of_courts'],
                    'proposed_hourly_rate' => $postData['proposed_hourly_rate'],
                    'amenities' => json_encode($postData['amenities'] ?? []),
                    'facility_description' => $postData['facility_description'],
                    'business_registration' => $files['business_registration'] ?? '',
                    'ownership_proof' => $files['ownership_proof'] ?? '',
                    'facility_images' => json_encode($files['facility_images'] ?? [])
                ];
                break;

            case 'coach':
                $specificData = [
                    'sport_specialization' => $postData['sport_specialization'],
                    'experience_years' => $postData['experience_years'],
                    'session_rate' => $postData['session_rate'],
                    'qualifications' => $postData['qualifications'],
                    'specialties' => json_encode($postData['specialties'] ?? []),
                    'bio' => $postData['bio'],
                    'previous_experience' => $postData['previous_experience'],
                    'availability' => json_encode($postData['availability'] ?? []),
                    'date_of_birth' => $postData['date_of_birth'],
                    'certifications' => json_encode($files['certifications'] ?? []),
                    'profile_photo' => $files['profile_photo'] ?? '',
                    'additional_documents' => json_encode($files['additional_documents'] ?? [])
                ];
                break;

            case 'shop_owner':
                $specificData = [
                    'shop_name' => $postData['shop_name'],
                    'business_registration_number' => $postData['business_registration_number'],
                    'shop_address' => $postData['shop_address'],
                    'shop_city' => $postData['shop_city'],
                    'shop_postal' => $postData['shop_postal'] ?? '',
                    'product_categories' => json_encode($postData['product_categories'] ?? []),
                    'business_type' => $postData['business_type'],
                    'year_established' => $postData['year_established'],
                    'number_of_employees' => $postData['number_of_employees'] ?? 0,
                    'business_description' => $postData['business_description'],
                    'brand_names' => $postData['brand_names'] ?? '',
                    'website_url' => $postData['website_url'] ?? '',
                    'social_media' => $postData['social_media'] ?? '',
                    'delivery_options' => json_encode($postData['delivery_options'] ?? []),
                    'business_registration' => $files['business_registration'] ?? '',
                    'tax_document' => $files['tax_document'] ?? '',
                    'shop_images' => json_encode($files['shop_images'] ?? []),
                    'additional_documents' => json_encode($files['additional_documents'] ?? [])
                ];
                break;
        }

        return array_merge($commonData, $specificData);
    }

    /**
     * Save application to database
     */
    private function saveApplication(array $data): ?int
    {
        try {
            $db = $this->getDatabase();

            $columns = array_keys($data);
            $placeholders = array_fill(0, count($data), '?');
            $values = array_values($data);

            $sql = "INSERT INTO provider_applications (" . implode(', ', $columns) . ")
                    VALUES (" . implode(', ', $placeholders) . ")";

            $stmt = $db->prepare($sql);
            $stmt->execute($values);

            return $db->lastInsertId();

        } catch (\Exception $e) {
            error_log("Database error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send notification to admin
     */
    private function sendAdminNotification(string $providerType, string $applicantEmail, int $applicationId): void
    {
        // TODO: Implement email notification to admin
        // This would typically use a mail service
        error_log("New {$providerType} application from {$applicantEmail} (ID: {$applicationId})");
    }

    /**
     * Send confirmation to applicant
     */
    private function sendApplicantConfirmation(string $email, string $providerType): void
    {
        // TODO: Implement email confirmation to applicant
        // This would typically use a mail service
        error_log("Confirmation email sent to {$email} for {$providerType} application");
    }

    /**
     * Get database connection
     */
    private function getDatabase(): \PDO
    {
        return \Core\Database::getInstance()->getConnection();
    }
}
