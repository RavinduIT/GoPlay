<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Owner Profile Diagnostic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #2c3e50; margin-bottom: 30px; }
        .card { background: white; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card h2 { color: #3498db; margin-bottom: 15px; font-size: 1.3rem; }
        .status { display: inline-block; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; }
        .status.success { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .status.warning { background: #fff3cd; color: #856404; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { background: #f8f9fa; font-weight: 600; color: #495057; }
        tr:hover { background: #f8f9fa; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px; border: none; cursor: pointer; font-weight: 600; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .info-box { background: #e7f3ff; border-left: 4px solid #3498db; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .code { background: #f4f4f4; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; overflow-x: auto; margin: 10px 0; }
    </style>
</head>
<body>
<?php
require_once __DIR__ . '/bootstrap.php';

use Core\Database;
use App\Models\ShopOwnerProfile;
use App\Models\User;

$db = Database::getInstance();
$profileModel = new ShopOwnerProfile();
$userModel = new User();

// Get action if submitted
$action = $_GET['action'] ?? '';
$userId = $_GET['user_id'] ?? 0;

if ($action === 'create_profile' && $userId) {
    $user = $userModel->find($userId);
    if ($user) {
        $profileData = [
            'user_id' => $userId,
            'shop_name' => $user['username'] . "'s Shop",
            'business_name' => $user['username'] . " Business",
            'business_email' => $user['email'],
            'business_phone' => $user['phone'] ?? '',
            'profile_completion_percentage' => 10,
            'status' => 'pending'
        ];
        $profileModel->create($profileData);
        header("Location: diagnostic-shop-owners.php");
        exit;
    }
}

if ($action === 'update_user_type' && $userId) {
    $db->query("UPDATE users SET user_type = 'shop_owner' WHERE id = ?", [$userId]);
    header("Location: diagnostic-shop-owners.php");
    exit;
}

if ($action === 'create_upload_dir' && $userId) {
    $uploadDir = __DIR__ . '/public/uploads/shop-owners/' . $userId . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    header("Location: diagnostic-shop-owners.php");
    exit;
}
?>

<div class="container">
    <h1>🔧 Shop Owner Profile Diagnostic Tool</h1>
    
    <div class="info-box">
        <strong>Purpose:</strong> This tool helps diagnose and fix issues with shop owner profiles (IDs 16-20).
        <br>It checks: user types, profile existence, upload directories, and image paths.
    </div>

    <?php
    $shopOwnerIds = [16, 17, 18, 19, 20];
    
    foreach ($shopOwnerIds as $uid) {
        $user = $userModel->find($uid);
        
        if (!$user) {
            continue;
        }
        
        $profile = $profileModel->getByUserId($uid);
        $uploadDir = __DIR__ . '/public/uploads/shop-owners/' . $uid . '/';
        $uploadDirExists = is_dir($uploadDir);
        
        echo "<div class='card'>";
        echo "<h2>User ID: $uid - {$user['username']}</h2>";
        
        // User Type Check
        echo "<div style='margin-bottom: 15px;'>";
        echo "<strong>User Type:</strong> ";
        if ($user['user_type'] === 'shop_owner') {
            echo "<span class='status success'>✓ Shop Owner</span>";
        } else {
            echo "<span class='status error'>✗ {$user['user_type']}</span> ";
            echo "<a href='?action=update_user_type&user_id=$uid' class='btn btn-success'>Change to Shop Owner</a>";
        }
        echo "</div>";
        
        // Profile Check
        echo "<div style='margin-bottom: 15px;'>";
        echo "<strong>Profile Status:</strong> ";
        if ($profile) {
            echo "<span class='status success'>✓ Profile Exists (ID: {$profile['id']})</span>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Value</th><th>Status</th></tr>";
            
            $fields = [
                'shop_name' => 'Shop Name',
                'business_name' => 'Business Name',
                'business_email' => 'Business Email',
                'business_phone' => 'Business Phone',
                'shop_address' => 'Shop Address',
                'shop_city' => 'City',
                'business_description' => 'Description',
                'shop_logo' => 'Shop Logo',
                'shop_banner' => 'Shop Banner',
                'profile_completion_percentage' => 'Completion %',
                'status' => 'Status'
            ];
            
            foreach ($fields as $key => $label) {
                $value = $profile[$key] ?? '';
                $statusClass = empty($value) ? 'warning' : 'success';
                $statusIcon = empty($value) ? '⚠️' : '✓';
                
                echo "<tr>";
                echo "<td><strong>$label</strong></td>";
                echo "<td>" . htmlspecialchars(substr($value, 0, 100)) . "</td>";
                echo "<td><span class='status $statusClass'>$statusIcon</span></td>";
                echo "</tr>";
                
                // Check if image files exist
                if (($key === 'shop_logo' || $key === 'shop_banner') && !empty($value)) {
                    $filePath = __DIR__ . '/public/' . $value;
                    if (!file_exists($filePath)) {
                        echo "<tr><td colspan='3' style='background: #fff3cd; color: #856404; padding: 8px;'>";
                        echo "⚠️ Image file not found at: $filePath</td></tr>";
                    }
                }
            }
            echo "</table>";
        } else {
            echo "<span class='status error'>✗ No Profile</span> ";
            echo "<a href='?action=create_profile&user_id=$uid' class='btn btn-success'>Create Profile</a>";
        }
        echo "</div>";
        
        // Upload Directory Check
        echo "<div style='margin-bottom: 15px;'>";
        echo "<strong>Upload Directory:</strong> ";
        if ($uploadDirExists) {
            echo "<span class='status success'>✓ Exists</span>";
            echo "<div class='code'>$uploadDir</div>";
            
            // List files in directory
            $files = glob($uploadDir . '*');
            if (!empty($files)) {
                echo "<strong>Files:</strong><ul>";
                foreach ($files as $file) {
                    $filename = basename($file);
                    $size = filesize($file);
                    echo "<li>$filename (" . round($size/1024, 2) . " KB)</li>";
                }
                echo "</ul>";
            } else {
                echo "<em>No files uploaded yet</em>";
            }
        } else {
            echo "<span class='status error'>✗ Not Found</span> ";
            echo "<a href='?action=create_upload_dir&user_id=$uid' class='btn btn-success'>Create Directory</a>";
            echo "<div class='code'>$uploadDir</div>";
        }
        echo "</div>";
        
        echo "</div>";
    }
    ?>
    
    <div class="card">
        <h2>🧪 Test Update Functionality</h2>
        <p>Test if profile updates are working properly:</p>
        
        <?php
        if (isset($_POST['test_update'])) {
            $testUserId = (int)$_POST['test_user_id'];
            $testShopName = $_POST['test_shop_name'];
            
            $result = $profileModel->updateProfile($testUserId, [
                'shop_name' => $testShopName
            ]);
            
            if ($result) {
                echo "<div class='status success' style='display: block; margin: 15px 0;'>✓ Update Successful!</div>";
                $updatedProfile = $profileModel->getByUserId($testUserId);
                echo "<div class='code'>Updated shop_name: {$updatedProfile['shop_name']}</div>";
            } else {
                echo "<div class='status error' style='display: block; margin: 15px 0;'>✗ Update Failed!</div>";
                echo "<p>Check PHP error logs for details.</p>";
            }
        }
        ?>
        
        <form method="POST" style="margin-top: 15px;">
            <label><strong>Select User:</strong></label>
            <select name="test_user_id" style="padding: 8px; margin: 0 10px;">
                <?php foreach ($shopOwnerIds as $uid): ?>
                    <option value="<?php echo $uid; ?>">User ID <?php echo $uid; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label><strong>New Shop Name:</strong></label>
            <input type="text" name="test_shop_name" value="Test Shop <?php echo date('H:i:s'); ?>" style="padding: 8px; margin: 0 10px; width: 250px;">
            
            <button type="submit" name="test_update" class="btn btn-success">Test Update</button>
        </form>
    </div>
    
    <div class="card">
        <h2>📋 Next Steps</h2>
        <ol style="line-height: 2;">
            <li>Ensure all users have user_type = 'shop_owner'</li>
            <li>Create profiles for users without them</li>
            <li>Create upload directories</li>
            <li>Log in as shop owner and visit: <a href="/shop-owner/profile">/shop-owner/profile</a></li>
            <li>Try updating business details and uploading images</li>
            <li>Check browser console (F12) for JavaScript errors</li>
            <li>Check PHP error logs for server-side issues</li>
        </ol>
    </div>
    
    <div class="card">
        <h2>🔍 Debugging Tips</h2>
        <div class="info-box">
            <strong>If updates don't work:</strong><br>
            1. Open browser console (F12) and check for errors when submitting forms<br>
            2. Check Network tab to see if API requests are successful<br>
            3. View PHP error logs at: <code>C:\wamp64\logs\php_error.log</code><br>
            4. Added error logging will show in PHP logs with prefix "ShopOwnerProfile:"
        </div>
        
        <div class="info-box">
            <strong>If images don't appear:</strong><br>
            1. Check image paths in database (should be: uploads/shop-owners/{user_id}/logo.jpg)<br>
            2. Verify files exist in: <code>C:\wamp64\www\main\GoPlay\public\uploads\shop-owners\{user_id}\</code><br>
            3. Check file permissions (should be readable)<br>
            4. Image src should include /public/ prefix: <code>/public/uploads/shop-owners/{user_id}/logo.jpg</code>
        </div>
    </div>
</div>

</body>
</html>
