# Testing Provider Applications Feature

## Overview
This document explains how to test the "Join as Provider" feature and the admin dashboard for reviewing provider applications.

## Prerequisites

### 1. Database Setup
✅ The `provider_applications` table has been created
✅ Test data exists (1 coach application in the database)

### 2. Required User Accounts

You need TWO accounts to test this feature:

#### Regular User Account (for applying)
- Can register at: `http://localhost:3000/signup`
- All new signups are created as `user_type = 'user'`

#### Admin Account (for reviewing applications)
- Must have `user_type = 'admin'` in the database
- You need to manually set this in the database:

```sql
-- Update an existing user to admin
UPDATE users SET user_type = 'admin' WHERE id = YOUR_USER_ID;

-- Or create a new admin account
INSERT INTO users (first_name, last_name, email, phone, password, user_type, created_at)
VALUES ('Admin', 'User', 'admin@goplay.com', '0771234567',
        '$2y$10$YOUR_HASHED_PASSWORD', 'admin', NOW());
```

## Testing Steps

### Part 1: Submit a Provider Application (as Regular User)

1. **Login as a regular user**
   - Go to: `http://localhost:3000/login`
   - Login with a user account (not admin)

2. **Access the provider registration**
   - Click "Join as Provider" in the navbar
   - You'll be redirected to: `http://localhost:3000/provider/join`

3. **Choose a role**
   - Select one of: Ground Owner, Coach, or Shop Owner
   - Click "Apply Now"

4. **Fill out the application form**
   - Complete Step 1 (Personal Information)
   - Click "Next" to proceed to Step 2
   - Complete Step 2 (Role-specific details)
   - Click "Next" to proceed to Step 3
   - Upload required documents
   - Accept terms and conditions
   - Click "Submit Application"

5. **Verify submission**
   - You should see a success message
   - The application should be saved to the database with `status = 'pending'`

### Part 2: Review Applications (as Admin)

1. **Login as admin**
   - Logout from the regular user account
   - Login with an admin account
   - **IMPORTANT:** Your account MUST have `user_type = 'admin'` in the database

2. **Access the admin dashboard**
   - Go to: `http://localhost:3000/admin/provider-applications`
   - If you see a blank page or "Unauthorized access", your account is not set as admin

3. **View statistics**
   - At the top of the page, you should see 4 stat cards:
     - Pending Applications
     - Approved Today
     - Rejected
     - Total
   - These should load automatically via JavaScript

4. **View applications list**
   - Below the stats, you should see a table with all applications
   - If the table says "No applications found", check the browser console for errors

5. **Filter and search**
   - Use the status filter (All, Pending, Approved, Rejected)
   - Use the type filter (All, Ground Owner, Coach, Shop Owner)
   - Use the search box to search by name or email

6. **View application details**
   - Click the "View" button on any application
   - A modal should open showing all the details

7. **Approve an application**
   - Click the green checkmark (✓) button
   - Confirm the approval
   - The application status should change to "approved"
   - **IMPORTANT:** The user's `user_type` in the `users` table should be updated to the provider type

8. **Reject an application**
   - Click the red X button
   - Enter a rejection reason
   - Click "Submit"
   - The application status should change to "rejected"

## Troubleshooting

### Issue: Admin page shows "Unauthorized access"
**Solution:** Make sure you're logged in as admin. Check the database:
```sql
SELECT id, email, user_type FROM users WHERE email = 'your@email.com';
```
If `user_type` is not 'admin', update it:
```sql
UPDATE users SET user_type = 'admin' WHERE email = 'your@email.com';
```

### Issue: Statistics not loading
**Check:**
1. Open browser console (F12)
2. Look for errors in the Console tab
3. Check the Network tab for failed requests
4. Visit the debug page: `http://localhost:3000/test-admin-api.php`

### Issue: Applications list not loading
**Check:**
1. Are you logged in as admin?
2. Check browser console for JavaScript errors
3. Check Network tab for API response
4. Visit: `http://localhost:3000/admin/provider-applications/list`
   - If you see `{"success":false,"message":"Unauthorized access"}`, you're not logged in as admin
   - If you see a 500 error, check the PHP error logs

### Issue: Database errors
**Solution:** Verify the table exists:
```sql
SHOW TABLES LIKE 'provider_applications';
SELECT COUNT(*) FROM provider_applications;
```

## Debug Tools

### 1. Test API Script
Visit: `http://localhost:3000/test-admin-api.php`
- Shows your current session status
- Tests database queries
- Shows sample data
- Has a button to test API calls from JavaScript

### 2. Browser Console
Open Developer Tools (F12) and check:
- **Console tab:** JavaScript errors
- **Network tab:** API requests and responses
- Look for failed requests (red text)
- Click on a request to see the response

### 3. PHP Error Logs
Check your PHP error logs for server-side errors:
```bash
# On Mac/Linux
tail -f /var/log/php_errors.log

# Or check your PHP configuration
php -i | grep error_log
```

## API Endpoints

The admin dashboard uses these API endpoints:

1. **Get Statistics**
   - URL: `/admin/provider-applications/statistics`
   - Method: GET
   - Returns: `{ success: true, stats: {...} }`

2. **Get Applications List**
   - URL: `/admin/provider-applications/list`
   - Method: GET
   - Query params: `page`, `limit`, `status`, `type`, `search`
   - Returns: `{ success: true, applications: [...], pagination: {...} }`

3. **Get Application Details**
   - URL: `/admin/provider-applications/details/{id}`
   - Method: GET
   - Returns: `{ success: true, application: {...} }`

4. **Approve Application**
   - URL: `/admin/provider-applications/approve/{id}`
   - Method: POST
   - Returns: `{ success: true, message: "..." }`
   - **Side effect:** Updates user's `user_type` in database

5. **Reject Application**
   - URL: `/admin/provider-applications/reject/{id}`
   - Method: POST
   - Body: `{ reason: "..." }`
   - Returns: `{ success: true, message: "..." }`

## Expected Behavior

### After Approval:
1. Application status changes to 'approved'
2. User's `user_type` in `users` table is updated to the provider type
3. User can now access their provider dashboard
4. Application is no longer shown in "Pending" filter

### After Rejection:
1. Application status changes to 'rejected'
2. Rejection reason is saved
3. User's account remains as 'user' type
4. Application is shown in "Rejected" filter

## File Locations

### Views:
- Provider role selection: `/app/views/provider/role-selection.php`
- Application forms: `/app/views/provider/{type}-form.php`
- Admin dashboard: `/app/views/admin/provider-applications.php`

### Controllers:
- Provider applications: `/app/controllers/ProviderController.php`
- Admin management: `/app/controllers/AdminController.php`

### JavaScript:
- Provider forms: `/public/js/provider-application.js`
- Admin dashboard: `/public/js/admin-applications.js`

### CSS:
- Provider forms: `/public/css/pages/provider-application.css`
- Admin dashboard: `/public/css/pages/admin-applications.css`

### Database:
- SQL file: `/database/migrations/create_provider_applications_table.sql`

## Need Help?

1. First, check the debug page: `http://localhost:3000/test-admin-api.php`
2. Check browser console for JavaScript errors
3. Check PHP error logs for server errors
4. Verify your user account has `user_type = 'admin'`
5. Make sure the database table exists and has data
