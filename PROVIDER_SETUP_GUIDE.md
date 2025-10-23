# Provider Registration System - Setup Guide

This guide provides instructions for setting up the "Join as Provider" feature in the GoPlay application.

## Overview

The provider registration system allows users to apply as one of three provider types:
- **Ground Owner**: Manage sports facilities
- **Coach**: Offer professional coaching services
- **Shop Owner**: Sell sports equipment and apparel

## Files Created

### 1. Views

#### Provider Views
- `/app/views/provider/role-selection.php` - Role selection page
- `/app/views/provider/ground-owner-form.php` - Ground owner application form
- `/app/views/provider/coach-form.php` - Coach application form
- `/app/views/provider/shop-owner-form.php` - Shop owner application form

#### Admin Views
- `/app/views/admin/provider-applications.php` - Admin dashboard for managing applications

### 2. Controllers
- `/app/controllers/ProviderController.php` - Handles provider application submissions

### 3. CSS Files
- `/public/css/pages/provider-role-selection.css` - Styles for role selection page
- `/public/css/pages/provider-application.css` - Styles for application forms
- `/public/css/pages/admin-applications.css` - Styles for admin applications dashboard

### 4. JavaScript Files
- `/public/js/provider-application.js` - Form validation and step management
- `/public/js/admin-applications.js` - Admin panel application management

### 5. Database Migration
- `/database/migrations/create_provider_applications_table.sql` - Database schema

### 6. Updated Files
- `/app/views/components/navbar.php` - Added "Join as Provider" button

## Setup Instructions

### Step 1: Run Database Migration

Execute the SQL migration file to create the required database table:

```bash
mysql -u your_username -p your_database < database/migrations/create_provider_applications_table.sql
```

Or execute the SQL directly in your MySQL client.

### Step 2: Add Routes

Add the following routes to your routing configuration file:

```php
// Provider Routes
$router->get('/provider/join', 'ProviderController@join');
$router->get('/provider/apply/ground-owner', 'ProviderController@applyGroundOwner');
$router->get('/provider/apply/coach', 'ProviderController@applyCoach');
$router->get('/provider/apply/shop-owner', 'ProviderController@applyShopOwner');
$router->post('/provider/submit-application', 'ProviderController@submitApplication');

// Admin Routes (require admin authentication)
$router->get('/admin/provider-applications', 'AdminController@providerApplications');
$router->get('/admin/provider-applications/list', 'AdminController@getApplicationsList');
$router->get('/admin/provider-applications/statistics', 'AdminController@getApplicationsStatistics');
$router->get('/admin/provider-applications/details/{id}', 'AdminController@getApplicationDetails');
$router->post('/admin/provider-applications/approve/{id}', 'AdminController@approveApplication');
$router->post('/admin/provider-applications/reject/{id}', 'AdminController@rejectApplication');
```

### Step 3: Create Upload Directory

Create the directory for storing uploaded documents:

```bash
mkdir -p public/uploads/provider-applications
chmod 755 public/uploads/provider-applications
```

### Step 4: Add Admin Controller Methods

Add the following methods to your `AdminController.php`:

```php
/**
 * Show provider applications page
 */
public function providerApplications(Request $request): Response
{
    $this->startSession();
    $this->requireAdmin();
    return $this->view('admin/provider-applications');
}

/**
 * Get applications list with filters
 */
public function getApplicationsList(Request $request): Response
{
    $this->startSession();
    $this->requireAdmin();

    $page = (int)($request->query('page') ?? 1);
    $limit = (int)($request->query('limit') ?? 10);
    $status = $request->query('status') ?? '';
    $type = $request->query('type') ?? '';
    $search = $request->query('search') ?? '';

    $offset = ($page - 1) * $limit;

    // Build query
    $where = [];
    $params = [];

    if ($status) {
        $where[] = "status = ?";
        $params[] = $status;
    }

    if ($type) {
        $where[] = "provider_type = ?";
        $params[] = $type;
    }

    if ($search) {
        $where[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    // Get total count
    $db = $this->getDatabase();
    $countSql = "SELECT COUNT(*) as total FROM provider_applications {$whereClause}";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

    // Get applications
    $sql = "SELECT * FROM provider_applications {$whereClause}
            ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $applications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    return $this->json([
        'success' => true,
        'applications' => $applications,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_items' => $total,
            'per_page' => $limit
        ]
    ]);
}

/**
 * Get applications statistics
 */
public function getApplicationsStatistics(Request $request): Response
{
    $this->startSession();
    $this->requireAdmin();

    $db = $this->getDatabase();

    $sql = "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = 'approved' AND DATE(reviewed_at) = CURDATE() THEN 1 ELSE 0 END) as approved_today
            FROM provider_applications";

    $stmt = $db->query($sql);
    $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $this->json([
        'success' => true,
        'stats' => $stats
    ]);
}

/**
 * Get application details
 */
public function getApplicationDetails(Request $request, int $id): Response
{
    $this->startSession();
    $this->requireAdmin();

    $db = $this->getDatabase();
    $sql = "SELECT * FROM provider_applications WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $application = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$application) {
        return $this->json([
            'success' => false,
            'message' => 'Application not found'
        ], 404);
    }

    return $this->json([
        'success' => true,
        'application' => $application
    ]);
}

/**
 * Approve application
 */
public function approveApplication(Request $request, int $id): Response
{
    $this->startSession();
    $this->requireAdmin();

    $db = $this->getDatabase();

    // Get application
    $sql = "SELECT * FROM provider_applications WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $application = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$application) {
        return $this->json([
            'success' => false,
            'message' => 'Application not found'
        ], 404);
    }

    // Update application status
    $updateSql = "UPDATE provider_applications
                  SET status = 'approved',
                      reviewed_by = ?,
                      reviewed_at = NOW()
                  WHERE id = ?";
    $updateStmt = $db->prepare($updateSql);
    $updateStmt->execute([$_SESSION['user_id'], $id]);

    // Update user type if user_id exists
    if ($application['user_id']) {
        $userUpdateSql = "UPDATE users SET user_type = ? WHERE id = ?";
        $userUpdateStmt = $db->prepare($userUpdateSql);
        $userUpdateStmt->execute([$application['provider_type'], $application['user_id']]);
    }

    // TODO: Send approval email to applicant

    return $this->json([
        'success' => true,
        'message' => 'Application approved successfully'
    ]);
}

/**
 * Reject application
 */
public function rejectApplication(Request $request, int $id): Response
{
    $this->startSession();
    $this->requireAdmin();

    $data = $request->getJsonBody();
    $reason = $data['reason'] ?? '';

    if (empty($reason)) {
        return $this->json([
            'success' => false,
            'message' => 'Rejection reason is required'
        ], 400);
    }

    $db = $this->getDatabase();

    // Update application status
    $sql = "UPDATE provider_applications
            SET status = 'rejected',
                reviewed_by = ?,
                reviewed_at = NOW(),
                rejection_reason = ?
            WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$_SESSION['user_id'], $reason, $id]);

    // TODO: Send rejection email to applicant

    return $this->json([
        'success' => true,
        'message' => 'Application rejected'
    ]);
}

/**
 * Get database connection
 */
private function getDatabase(): \PDO
{
    return (new \App\Models\User())->getConnection();
}

/**
 * Require admin authentication
 */
private function requireAdmin(): void
{
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        header('Location: /');
        exit;
    }
}
```

### Step 5: Add Admin Sidebar Link

Add a link to the provider applications page in your admin sidebar:

```php
<a href="/admin/provider-applications" class="sidebar-link">
    <i class="fas fa-user-plus"></i>
    <span>Provider Applications</span>
    <?php if (isset($pendingApplicationsCount) && $pendingApplicationsCount > 0): ?>
        <span class="badge"><?= $pendingApplicationsCount ?></span>
    <?php endif; ?>
</a>
```

## Features

### For Users
1. **Role Selection**: Choose between Ground Owner, Coach, or Shop Owner
2. **Multi-Step Forms**: Easy-to-complete application forms with validation
3. **File Uploads**: Upload required documents (NIC, certifications, photos)
4. **Real-time Validation**: Instant feedback on form fields
5. **Email Notifications**: Receive updates on application status

### For Admins
1. **Applications Dashboard**: View all applications with filters
2. **Statistics**: Track pending, approved, and rejected applications
3. **Application Review**: View detailed information for each application
4. **Approve/Reject**: Process applications with admin notes
5. **Auto-Update User Type**: Approved users automatically get provider role

## Workflow

1. User clicks "Join as Provider" in navbar
2. User selects a provider role (Ground Owner, Coach, or Shop Owner)
3. User fills out role-specific application form
4. Application is submitted and saved to database
5. Admin receives notification of new application
6. Admin reviews application in admin dashboard
7. Admin approves or rejects application
8. If approved, user's `user_type` is updated in database
9. User receives email notification of decision
10. Approved users can now access their provider dashboard

## Database Schema

The `provider_applications` table stores all application data with the following key fields:
- Common fields: personal info, contact details, documents
- Role-specific fields: facility details, qualifications, business info
- Status tracking: pending, approved, rejected
- Admin review: reviewer ID, review date, notes

## Next Steps

1. Configure email service for notifications
2. Add email templates for application confirmation, approval, and rejection
3. Test the complete workflow
4. Add additional validation as needed
5. Customize form fields based on your requirements

## Notes

- All file uploads are stored in `/public/uploads/provider-applications/`
- Maximum file size: 5MB per file
- Accepted formats: PDF, JPG, PNG
- Applications can only be submitted by logged-in users or new users
- Admin approval is required before users can access provider features
