# Shop Owner Profile Fixes - Complete Guide

## Issues Identified & Fixed

### Issue 1: Profile Updates Not Saving ❌
**Problem:** When shop owners (IDs 16-20) filled business details, data wasn't updating in the database.

**Root Cause:**
- Missing error logging made it hard to debug
- Possible profile doesn't exist for some users
- Request data might not be reaching the model

**Fixes Applied:**
1. ✅ Added detailed error logging to `ShopOwnerProfile::updateProfile()`
2. ✅ Added error logging to `ShopOwnerController::updateProfile()`
3. ✅ Profile existence check with auto-creation if missing

### Issue 2: Images Not Appearing ❌
**Problem:** Uploaded logos and banners don't display even after successful upload.

**Root Cause:**
- Image paths in database: `uploads/shop-owners/{user_id}/logo.jpg`
- Image src in HTML was: `/uploads/...` (missing `/public/` prefix)
- Actual file location: `C:\wamp64\www\main\GoPlay\public\uploads\shop-owners\{user_id}\`

**Fixes Applied:**
1. ✅ Updated profile.php to include `/public/` prefix in image src attributes
2. ✅ Banner image: `<img src="/public/<?php echo $shopBanner; ?>">`
3. ✅ Logo image: `<img src="/public/<?php echo $shopLogo; ?>">`

---

## Step-by-Step Fix Process

### Step 1: Run Diagnostic Tool

1. **Access the diagnostic page:**
   ```
   http://localhost/diagnostic-shop-owners.php
   ```

2. **The tool will show:**
   - ✓ User types (should be 'shop_owner')
   - ✓ Profile existence
   - ✓ Upload directories
   - ✓ Current field values
   - ✓ Image file existence

3. **Fix any issues found:**
   - Click "Change to Shop Owner" if user_type is wrong
   - Click "Create Profile" if profile doesn't exist
   - Click "Create Directory" if upload folder is missing

### Step 2: Test Update Functionality

1. **On the diagnostic page, scroll to "Test Update Functionality"**
2. **Select a user ID (16-20)**
3. **Enter a test shop name**
4. **Click "Test Update"**
5. **Should see:** ✓ Update Successful!

If update fails:
- Check PHP error logs: `C:\wamp64\logs\php_error.log`
- Look for lines with "ShopOwnerProfile:" prefix
- Common issues:
  - Database connection
  - Table doesn't exist
  - Permission issues

### Step 3: Test in Browser

1. **Log in as shop owner (one of IDs 16-20)**

2. **Navigate to:**
   ```
   http://localhost/shop-owner/profile
   ```

3. **Test Banner Upload:**
   - Click camera icon on banner
   - Select image (JPG/PNG, max 5MB)
   - Should see immediate preview
   - Check "Shop Banner" field in database

4. **Test Logo Upload:**
   - Click camera icon on avatar/logo
   - Select image (JPG/PNG, max 2MB)
   - Should see immediate preview
   - Check "Shop Logo" field in database

5. **Test Business Info Update:**
   - Go to "Business Information" tab
   - Fill in shop name, business name, etc.
   - Click "Save Business Information"
   - Should see success message
   - Refresh page to verify changes saved

6. **Test Banking Details:**
   - Go to "Banking Details" tab
   - Fill in bank information
   - Click "Save Banking Details"
   - Verify success message

7. **Test Social Media:**
   - Go to "Social Media" tab
   - Add website, Facebook, Instagram
   - Click "Save Social Links"
   - Verify success

---

## How to Debug Issues

### Browser Console (JavaScript Errors)

1. **Open DevTools:** Press `F12`
2. **Go to Console tab**
3. **Try updating profile**
4. **Look for errors in red**

Common errors:
```javascript
// If you see this:
Failed to fetch
// Solution: Check if WAMP is running and API endpoints are accessible

// If you see this:
Unexpected token < in JSON
// Solution: PHP error occurred, check PHP logs
```

### Network Tab (API Requests)

1. **Open DevTools:** Press `F12`
2. **Go to Network tab**
3. **Filter by "Fetch/XHR"**
4. **Try updating profile**
5. **Click on the request (e.g., "update")**
6. **Check Response tab**

Expected successful response:
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "profile": { ... }
}
```

Error response example:
```json
{
  "success": false,
  "message": "Failed to update profile",
  "error": "..."
}
```

### PHP Error Logs

**Location:** `C:\wamp64\logs\php_error.log`

**View recent logs:**
```powershell
Get-Content "C:\wamp64\logs\php_error.log" -Tail 50
```

**Look for these patterns:**
```
ShopOwnerProfile: No profile found for user_id: 16
ShopOwnerProfile: Updating profile for user_id: 16
ShopOwnerProfile: Received data: {"shop_name":"..."}
ShopOwnerProfile: Filtered update data: {...}
ShopOwnerProfile: Update result: SUCCESS for profile ID: 10
ShopOwnerController: Update request from user_id: 16
```

---

## Verifying Fixes

### Database Verification

```sql
-- Check users are shop owners
SELECT id, username, user_type FROM users WHERE id IN (16,17,18,19,20);

-- Check profiles exist
SELECT user_id, shop_name, business_name, shop_logo, shop_banner, 
       profile_completion_percentage, status 
FROM shop_owner_profiles 
WHERE user_id IN (16,17,18,19,20);

-- Update test
UPDATE shop_owner_profiles 
SET shop_name = 'Test Shop Update' 
WHERE user_id = 16;

-- Verify update
SELECT user_id, shop_name FROM shop_owner_profiles WHERE user_id = 16;
```

### File System Verification

**Check upload directories exist:**
```powershell
ls "C:\wamp64\www\main\GoPlay\public\uploads\shop-owners"
```

**Should see folders:**
```
16/
17/
18/
19/
20/
```

**Check files in a user's folder:**
```powershell
ls "C:\wamp64\www\main\GoPlay\public\uploads\shop-owners\16"
```

**Should see files like:**
```
logo.jpg or logo.png
banner.jpg or banner.png
documents/
images/
```

---

## Image Display Issues

### Problem: Images uploaded but don't display

**Checklist:**

1. **✓ Database has correct path:**
   ```sql
   SELECT shop_logo, shop_banner FROM shop_owner_profiles WHERE user_id = 16;
   ```
   Should return: `uploads/shop-owners/16/logo.jpg`

2. **✓ File exists:**
   ```powershell
   Test-Path "C:\wamp64\www\main\GoPlay\public\uploads\shop-owners\16\logo.jpg"
   ```
   Should return: `True`

3. **✓ HTML has correct src:**
   View page source, should see:
   ```html
   <img src="/public/uploads/shop-owners/16/logo.jpg">
   ```

4. **✓ URL is accessible:**
   Try accessing directly in browser:
   ```
   http://localhost/public/uploads/shop-owners/16/logo.jpg
   ```
   Should display the image

5. **✓ .htaccess allows access:**
   Ensure no rules blocking /public/ or /uploads/

### Common Image Issues

**Issue:** 404 Not Found
- Check file path in database matches actual file location
- Verify file permissions (should be readable)

**Issue:** 403 Forbidden
- Check Apache configuration allows access to /public/uploads/
- Verify directory permissions (755)

**Issue:** Broken image icon
- Image may be corrupted during upload
- Check file size (logo max 2MB, banner max 5MB)
- Verify file type (only JPG, PNG, WEBP allowed)

---

## Update Flow

### How Profile Updates Work

1. **User fills form in one of the tabs**
2. **Clicks "Save" button**
3. **JavaScript catches form submit:**
   ```javascript
   form.addEventListener('submit', function(e) {
       e.preventDefault();
       // Collect form data
       // Send to API
   });
   ```

4. **AJAX POST to API endpoint:**
   ```javascript
   fetch('/api/shop-owner/profile/update', {
       method: 'POST',
       headers: {'Content-Type': 'application/json'},
       body: JSON.stringify(data)
   })
   ```

5. **Server processes request:**
   - `ShopOwnerController::updateProfile()` receives request
   - Logs: "ShopOwnerController: Update request from user_id: X"
   - Calls `ShopOwnerProfile::updateProfile()`

6. **Model updates database:**
   - Logs: "ShopOwnerProfile: Updating profile for user_id: X"
   - Logs: "ShopOwnerProfile: Received data: {...}"
   - Filters data to allowed fields
   - Logs: "ShopOwnerProfile: Filtered update data: {...}"
   - Calls `BaseModel::update()`
   - Logs: "ShopOwnerProfile: Update result: SUCCESS"

7. **Response sent back:**
   ```json
   {"success": true, "message": "Profile updated successfully"}
   ```

8. **JavaScript shows success message:**
   ```javascript
   showMessage('Profile updated successfully!', 'success');
   ```

---

## Quick Test Checklist

- [ ] User IDs 16-20 have user_type = 'shop_owner'
- [ ] All users have profiles in shop_owner_profiles table
- [ ] Upload directories exist for each user
- [ ] Can access /shop-owner/profile without errors
- [ ] Business Information form saves successfully
- [ ] Banking Details form saves successfully
- [ ] Social Media form saves successfully
- [ ] Logo upload works and displays
- [ ] Banner upload works and displays
- [ ] Profile completion percentage updates
- [ ] No JavaScript errors in console
- [ ] No PHP errors in logs
- [ ] Images accessible via direct URL

---

## Still Having Issues?

### Contact Information
Check these logs for detailed error information:

1. **PHP Error Log:**
   ```
   C:\wamp64\logs\php_error.log
   ```

2. **Apache Error Log:**
   ```
   C:\wamp64\logs\apache_error.log
   ```

3. **Browser Console:**
   Press F12 → Console tab

### Common Solutions

**"Profile not found"**
→ Run diagnostic tool and click "Create Profile"

**"Upload failed"**
→ Check directory permissions and available disk space

**"Database error"**
→ Verify WAMP MySQL service is running
→ Check database connection in config

**"Images don't display"**
→ Clear browser cache
→ Verify image path includes /public/ prefix
→ Check file exists in correct directory

---

## Success Indicators

✅ Diagnostic tool shows all green checkmarks
✅ Test update returns "Update Successful!"
✅ Forms submit and show success messages
✅ Page refresh shows updated data
✅ Images display after upload
✅ Profile completion percentage increases
✅ No errors in browser console
✅ No errors in PHP logs

**If all indicators are ✅, your shop owner profiles are working correctly!**
