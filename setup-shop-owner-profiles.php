<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Shop Owner Profiles</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #2c3e50; margin-bottom: 10px; }
        .subtitle { color: #7f8c8d; margin-bottom: 30px; font-size: 1.1rem; }
        .card { background: white; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card h2 { color: #3498db; margin-bottom: 15px; font-size: 1.3rem; }
        .status { display: inline-block; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin: 5px; }
        .status.success { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .status.warning { background: #fff3cd; color: #856404; }
        .status.info { background: #d1ecf1; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { background: #f8f9fa; font-weight: 600; color: #495057; }
        tr:hover { background: #f8f9fa; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px; border: none; cursor: pointer; font-weight: 600; font-size: 0.95rem; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-warning:hover { background: #e0a800; }
        .info-box { background: #e7f3ff; border-left: 4px solid #3498db; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .warning-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success-box { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .code { background: #f4f4f4; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; overflow-x: auto; margin: 10px 0; }
        form { display: inline; }
        .user-card { border: 2px solid #e9ecef; padding: 15px; margin: 10px 0; border-radius: 8px; }
        .user-card.has-profile { border-color: #28a745; }
        .user-card.no-profile { border-color: #ffc107; }
    </style>
</head>
<body>
<?php
session_start();
require_once __DIR__ . '/bootstrap.php';

use Core\Database;
use App\Models\ShopOwnerProfile;
use App\Models\User;

$db = Database::getInstance();
$profileModel = new ShopOwnerProfile();
$userModel = new User();

// Handle actions
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$userId = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
$message = '';
$messageType = '';

if ($action === 'create_profile' && $userId) {
    $user = $userModel->find($userId);
    if ($user) {
        // Check if profile already exists
        $existingProfile = $profileModel->getByUserId($userId);
        
        if ($existingProfile) {
            $message = "Profile already exists for user ID $userId";
            $messageType = 'warning';
        } else {
            $profileData = [
                'user_id' => $userId,
                'shop_name' => $user['username'] . "'s Shop",
                'business_name' => $user['first_name'] . ' ' . $user['last_name'] . ' Business',
                'business_email' => $user['email'],
                'business_phone' => $user['phone'] ?? '',
                'profile_completion_percentage' => 15,
                'status' => 'pending'
            ];
            
            $created = $profileModel->create($profileData);
            
            if ($created) {
                $message = "✓ Profile created successfully for user ID $userId";
                $messageType = 'success';
                
                // Create upload directory
                $uploadDir = __DIR__ . '/public/uploads/shop-owners/' . $userId . '/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
            } else {
                $message = "✗ Failed to create profile for user ID $userId";
                $messageType = 'error';
            }
        }
    }
}

if ($action === 'update_user_type' && $userId) {
    $db->query("UPDATE users SET user_type = 'shop_owner' WHERE id = ?", [$userId]);
    $message = "✓ User type updated to 'shop_owner' for user ID $userId";
    $messageType = 'success';
}

if ($action === 'create_all_profiles') {
    $shopOwnerIds = [16, 17, 18, 19, 20];
    $created = 0;
    $skipped = 0;
    
    foreach ($shopOwnerIds as $uid) {
        $user = $userModel->find($uid);
        if (!$user) continue;
        
        // Update user type if needed
        if ($user['user_type'] !== 'shop_owner') {
            $db->query("UPDATE users SET user_type = 'shop_owner' WHERE id = ?", [$uid]);
        }
        
        // Check if profile exists
        $existingProfile = $profileModel->getByUserId($uid);
        if ($existingProfile) {
            $skipped++;
            continue;
        }
        
        // Create profile
        $profileData = [
            'user_id' => $uid,
            'shop_name' => $user['username'] . "'s Shop",
            'business_name' => $user['first_name'] . ' ' . $user['last_name'] . ' Business',
            'business_email' => $user['email'],
            'business_phone' => $user['phone'] ?? '',
            'profile_completion_percentage' => 15,
            'status' => 'pending'
        ];
        
        if ($profileModel->create($profileData)) {
            $created++;
            
            // Create upload directory
            $uploadDir = __DIR__ . '/public/uploads/shop-owners/' . $uid . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }
    }
    
    $message = "✓ Created $created profiles, skipped $skipped existing profiles";
    $messageType = 'success';
}
?>

<div class="container">
    <h1>🔧 Shop Owner Profile Setup Tool</h1>
    <p class="subtitle">For shop owners created directly (without provider application)</p>
    
    <?php if ($message): ?>
        <div class="<?php echo $messageType; ?>-box">
            <strong><?php echo $message; ?></strong>
        </div>
    <?php endif; ?>
    
    <div class="warning-box">
        <strong>⚠️ Important Note:</strong><br>
        Shop owners with IDs 16-20 were created directly by admin, not through the provider application system.<br>
        They need profiles to be created manually so they can fill in their business details.
    </div>

    <div class="card">
        <h2>Quick Actions</h2>
        <form method="POST" style="display: inline;">
            <input type="hidden" name="action" value="create_all_profiles">
            <button type="submit" class="btn btn-success">
                ✓ Create Profiles for All Shop Owners (16-20)
            </button>
        </form>
        <a href="/shop-owner/profile" class="btn btn-primary">Go to Profile Page</a>
        <a href="/diagnostic-shop-owners.php" class="btn btn-warning">Open Diagnostic Tool</a>
    </div>

    <div class="card">
        <h2>Shop Owner Status (IDs 16-20)</h2>
        
        <?php
        $shopOwnerIds = [16, 17, 18, 19, 20];
        
        foreach ($shopOwnerIds as $uid) {
            $user = $userModel->find($uid);
            
            if (!$user) {
                echo "<div class='user-card'>";
                echo "<span class='status error'>✗ User ID $uid does not exist</span>";
                echo "</div>";
                continue;
            }
            
            $profile = $profileModel->getByUserId($uid);
            $hasProfile = $profile !== null;
            $uploadDir = __DIR__ . '/public/uploads/shop-owners/' . $uid . '/';
            $uploadDirExists = is_dir($uploadDir);
            
            echo "<div class='user-card " . ($hasProfile ? 'has-profile' : 'no-profile') . "'>";
            echo "<h3>User ID: $uid - {$user['username']}</h3>";
            echo "<p><strong>Name:</strong> {$user['first_name']} {$user['last_name']} | ";
            echo "<strong>Email:</strong> {$user['email']} | ";
            echo "<strong>Phone:</strong> " . ($user['phone'] ?? 'N/A') . "</p>";
            
            // User Type
            echo "<div style='margin: 10px 0;'>";
            if ($user['user_type'] === 'shop_owner') {
                echo "<span class='status success'>✓ Shop Owner</span>";
            } else {
                echo "<span class='status error'>✗ {$user['user_type']}</span> ";
                echo "<form method='POST'>";
                echo "<input type='hidden' name='action' value='update_user_type'>";
                echo "<input type='hidden' name='user_id' value='$uid'>";
                echo "<button type='submit' class='btn btn-success'>Fix User Type</button>";
                echo "</form>";
            }
            echo "</div>";
            
            // Profile Status
            echo "<div style='margin: 10px 0;'>";
            if ($hasProfile) {
                echo "<span class='status success'>✓ Profile Exists (ID: {$profile['id']})</span>";
                echo "<span class='status info'>{$profile['profile_completion_percentage']}% Complete</span>";
                echo "<span class='status " . ($profile['status'] === 'active' ? 'success' : 'warning') . "'>{$profile['status']}</span>";
                
                echo "<table style='margin-top:10px;'>";
                echo "<tr><th>Field</th><th>Value</th></tr>";
                echo "<tr><td>Shop Name</td><td>" . htmlspecialchars($profile['shop_name'] ?? 'Not set') . "</td></tr>";
                echo "<tr><td>Business Name</td><td>" . htmlspecialchars($profile['business_name'] ?? 'Not set') . "</td></tr>";
                echo "<tr><td>Business Email</td><td>" . htmlspecialchars($profile['business_email'] ?? 'Not set') . "</td></tr>";
                echo "<tr><td>Business Phone</td><td>" . htmlspecialchars($profile['business_phone'] ?? 'Not set') . "</td></tr>";
                echo "</table>";
            } else {
                echo "<span class='status error'>✗ No Profile</span> ";
                echo "<form method='POST'>";
                echo "<input type='hidden' name='action' value='create_profile'>";
                echo "<input type='hidden' name='user_id' value='$uid'>";
                echo "<button type='submit' class='btn btn-success'>Create Profile Now</button>";
                echo "</form>";
            }
            echo "</div>";
            
            // Upload Directory
            echo "<div style='margin: 10px 0;'>";
            if ($uploadDirExists) {
                echo "<span class='status success'>✓ Upload Directory Exists</span>";
            } else {
                echo "<span class='status warning'>⚠️ Upload Directory Missing</span>";
            }
            echo "</div>";
            
            echo "</div>";
        }
        ?>
    </div>
    
    <div class="card">
        <h2>📋 How This Works</h2>
        <ol style="line-height: 2;">
            <li><strong>Normal Flow:</strong> User applies → Admin approves → Profile auto-created from application data</li>
            <li><strong>Your Case:</strong> Users 16-20 were created directly → No application data → Need manual profile creation</li>
            <li><strong>Solution:</strong> This tool creates empty profiles with basic info from users table</li>
            <li><strong>Next Step:</strong> Shop owners log in and fill their business details via profile page</li>
        </ol>
    </div>
    
    <div class="card">
        <h2>🔍 What Gets Created</h2>
        <div class="code">
Profile Data (from users table):
- user_id: [from users.id]
- shop_name: "[username]'s Shop"
- business_name: "[first_name] [last_name] Business"
- business_email: [from users.email]
- business_phone: [from users.phone]
- profile_completion_percentage: 15%
- status: 'pending'

Upload Directory:
- Creates: public/uploads/shop-owners/{user_id}/
- Permissions: 0777 (writable)

User Type:
- Updates users.user_type to 'shop_owner' if needed
        </div>
    </div>
    
    <div class="card">
        <h2>✅ After Profile Creation</h2>
        <ol style="line-height: 2;">
            <li>Shop owner logs in to their account</li>
            <li>Navigates to <a href="/shop-owner/profile">/shop-owner/profile</a></li>
            <li>Fills in complete business information:
                <ul>
                    <li>Shop details (name, description, address)</li>
                    <li>Business registration info</li>
                    <li>Banking details</li>
                    <li>Social media links</li>
                    <li>Upload logo and banner</li>
                </ul>
            </li>
            <li>Profile completion percentage increases automatically</li>
            <li>Data saves to shop_owner_profiles table</li>
            <li>Profile becomes fully functional</li>
        </ol>
    </div>
    
    <div class="info-box">
        <strong>💡 Tip:</strong> After creating profiles, test by logging in as one of the shop owners (ID 16-20) and updating their profile at /shop-owner/profile
    </div>
</div>

</body>
</html>
