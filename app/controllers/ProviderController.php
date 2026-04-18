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

    // Allowed file extensions and MIME types for uploads
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg', 'image/png', 'image/gif', 'application/pdf'
    ];
    private const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB

    /**
     * Show role selection page
     */
    public function join(Request $request): Response
    {
        $this->startSession();

        // Require login
        if (empty($_SESSION['user_id'])) {
            return $this->redirect('/login?redirect=/provider/join');
        }

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
        if (empty($_SESSION['user_id'])) {
            return $this->redirect('/login?redirect=/provider/apply/ground-owner');
        }
        return $this->viewWithoutLayout('provider/ground-owner-form');
    }

    /**
     * Show coach application form
     */
    public function applyCoach(Request $request): Response
    {
        $this->startSession();
        if (empty($_SESSION['user_id'])) {
            return $this->redirect('/login?redirect=/provider/apply/coach');
        }
        return $this->viewWithoutLayout('provider/coach-form');
    }

    /**
     * Show shop owner application form
     */
    public function applyShopOwner(Request $request): Response
    {
        $this->startSession();
        if (empty($_SESSION['user_id'])) {
            return $this->redirect('/login?redirect=/provider/apply/shop-owner');
        }
        return $this->viewWithoutLayout('provider/shop-owner-form');
    }

    /**
     * Submit provider application
     */
    public function submitApplication(Request $request): Response
    {
        $this->startSession();

        // Require login to submit applications
        if (empty($_SESSION['user_id'])) {
            return $this->json([
                'success' => false,
                'message' => 'You must be logged in to submit a provider application.'
            ], 401);
        }

        try {
            // Rate limiting: max 3 application submissions per hour per user
            $userId = $_SESSION['user_id'];
            $rateLimitKey = 'provider_app_attempts_' . $userId;
            if (!isset($_SESSION[$rateLimitKey])) {
                $_SESSION[$rateLimitKey] = ['count' => 0, 'window_start' => time()];
            }
            $rl = &$_SESSION[$rateLimitKey];
            if (time() - $rl['window_start'] > 3600) {
                $rl = ['count' => 0, 'window_start' => time()];
            }
            if ($rl['count'] >= 3) {
                return $this->json([
                    'success' => false,
                    'message' => 'Too many submissions. Please wait before trying again.'
                ], 429);
            }
            $rl['count']++;

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

            // Duplicate submission guard
            $userId = $_SESSION['user_id'] ?? null;
            $applicantEmail = $_POST['email'] ?? '';
            if ($userId || $applicantEmail) {
                $db = $this->getDatabase();
                $dupSql = "SELECT id FROM provider_applications WHERE status = 'pending' AND provider_type = ?";
                $dupParams = [$providerType];
                if ($userId) {
                    $dupSql .= " AND user_id = ?";
                    $dupParams[] = $userId;
                } else {
                    $dupSql .= " AND email = ?";
                    $dupParams[] = $applicantEmail;
                }
                $dupStmt = $db->prepare($dupSql);
                $dupStmt->execute($dupParams);
                if ($dupStmt->fetch()) {
                    return $this->json([
                        'success' => false,
                        'message' => 'You already have a pending application for this provider type. Please wait for it to be reviewed.'
                    ], 400);
                }
            }

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

                    // Cap multi-file uploads at 5 files max
                    $fileCount = count(array_filter($file['name']));
                    if ($fileCount > 5) {
                        return ['success' => false, 'message' => "Maximum 5 files allowed for '{$fieldName}'. You uploaded {$fileCount}."];
                    }

                    foreach ($file['name'] as $index => $fileName) {
                        if (empty($fileName)) continue;

                        // Check upload error code
                        if ($file['error'][$index] !== UPLOAD_ERR_OK) {
                            return ['success' => false, 'message' => $this->uploadErrorMessage($file['error'][$index], $fileName)];
                        }

                        // Validate file type, MIME, and size
                        $validationError = $this->validateUploadedFile($file['tmp_name'][$index], $fileName, $file['size'][$index]);
                        if ($validationError) {
                            return ['success' => false, 'message' => $validationError];
                        }

                        $newFileName = $this->sanitizeFileName($fileName);
                        $destination = $applicationPath . $newFileName;

                        if (move_uploaded_file($file['tmp_name'][$index], $destination)) {
                            $uploadedFiles[$fieldName][] = $applicationFolder . '/' . $newFileName;
                        } else {
                            return ['success' => false, 'message' => "Failed to save file '{$fileName}'. Please try again."];
                        }
                    }
                } else {
                    // Handle single file
                    if (empty($file['name'])) continue;

                    if ($file['error'] !== UPLOAD_ERR_OK) {
                        return ['success' => false, 'message' => $this->uploadErrorMessage($file['error'], $file['name'])];
                    }

                    $validationError = $this->validateUploadedFile($file['tmp_name'], $file['name'], $file['size']);
                    if ($validationError) {
                        return ['success' => false, 'message' => $validationError];
                    }

                    $newFileName = $this->sanitizeFileName($file['name']);
                    $destination = $applicationPath . $newFileName;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $uploadedFiles[$fieldName] = $applicationFolder . '/' . $newFileName;
                    } else {
                        return ['success' => false, 'message' => "Failed to save file '{$file['name']}'. Please try again."];
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
     * Validate a single uploaded file (extension, MIME, size)
     */
    private function validateUploadedFile(string $tmpName, string $originalName, int $size): ?string
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return "File '{$originalName}' has a disallowed type. Allowed: " . implode(', ', self::ALLOWED_EXTENSIONS);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);
        if (!in_array($mime, self::ALLOWED_MIME_TYPES, true)) {
            return "File '{$originalName}' has an invalid content type ({$mime}).";
        }

        if ($size > self::MAX_FILE_SIZE) {
            return "File '{$originalName}' exceeds the maximum size of 10 MB.";
        }

        return null; // valid
    }

    /**
     * Sanitize file name — only allows whitelisted extensions
     */
    private function sanitizeFileName(string $fileName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            $extension = 'bin'; // fallback — should never reach here after validation
        }
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
        try {
            $emailService = new \App\Services\EmailService();
            $typeName = ucwords(str_replace('_', ' ', $providerType));
            $subject = "New Provider Application: {$typeName} (#{$applicationId})";
            $message = "<h2>New Provider Application Received</h2>"
                     . "<p>A new <strong>{$typeName}</strong> application has been submitted.</p>"
                     . "<p><strong>Applicant:</strong> {$applicantEmail}</p>"
                     . "<p><strong>Application ID:</strong> #{$applicationId}</p>"
                     . "<p>Please review this application in the admin panel.</p>";

            // Send to admin email
            $adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'admin@goplay.lk';
            $emailService->sendNotification($adminEmail, 'GoPlay Admin', $subject, $message);
        } catch (\Exception $e) {
            error_log("Admin notification email failed: " . $e->getMessage());
        }
    }

    /**
     * Send confirmation to applicant
     */
    private function sendApplicantConfirmation(string $email, string $providerType): void
    {
        try {
            $emailService = new \App\Services\EmailService();
            $typeName = ucwords(str_replace('_', ' ', $providerType));
            $subject = "GoPlay - Application Received: {$typeName}";
            $message = "<h2>Application Received</h2>"
                     . "<p>Thank you for applying to become a <strong>{$typeName}</strong> on GoPlay.</p>"
                     . "<p>Your application is currently under review. Our team will evaluate your submission and get back to you within 3-5 business days.</p>"
                     . "<p>You will receive an email notification once your application has been reviewed.</p>"
                     . "<br><p>Best regards,<br><strong>The GoPlay Team</strong></p>";

            $emailService->sendNotification($email, $email, $subject, $message);
        } catch (\Exception $e) {
            error_log("Applicant confirmation email failed: " . $e->getMessage());
        }
    }

    /**
     * Get database connection
     */
    private function getDatabase(): \PDO
    {
        return \Core\Database::getInstance()->getConnection();
    }

    /**
     * Human-readable message for PHP upload error codes
     */
    private function uploadErrorMessage(int $code, string $fileName): string
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => "File '{$fileName}' exceeds the server upload size limit.",
            UPLOAD_ERR_FORM_SIZE  => "File '{$fileName}' exceeds the form upload size limit.",
            UPLOAD_ERR_PARTIAL    => "File '{$fileName}' was only partially uploaded.",
            UPLOAD_ERR_NO_FILE    => "No file was uploaded for '{$fileName}'.",
            UPLOAD_ERR_NO_TMP_DIR => "Server error: missing temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Server error: failed to write '{$fileName}' to disk.",
            UPLOAD_ERR_EXTENSION  => "A server extension stopped the upload of '{$fileName}'.",
        ];

        return $messages[$code] ?? "Unknown upload error for '{$fileName}'.";
    }
}
