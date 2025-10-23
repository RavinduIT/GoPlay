# Quick Setup Guide - Provider Registration System

## ✅ What's Already Done

All code files have been created:
- ✓ Routes added to index.php
- ✓ ProviderController created
- ✓ AdminController updated
- ✓ All view files created
- ✓ CSS and JavaScript files created
- ✓ Upload directory created at `/public/uploads/provider-applications/`
- ✓ "Join as Provider" button added to navbar
- ✓ Admin sidebar link added

## 🔧 What You Need To Do

### Step 1: Create the Database Table

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin
2. Select your database (`goplay_sports_platform`)
3. Click on "SQL" tab
4. Copy and paste the contents of `CREATE_PROVIDER_TABLE.sql`
5. Click "Go" to execute

**Option B: Using MySQL Command Line**
```bash
mysql -u your_username -p your_database_name < CREATE_PROVIDER_TABLE.sql
```

**Option C: Using MySQL Workbench**
1. Open MySQL Workbench
2. Connect to your database
3. Open `CREATE_PROVIDER_TABLE.sql`
4. Execute the SQL

### Step 2: Verify Everything Works

1. **Test the role selection page:**
   - Go to: `http://localhost:3000/provider/join`
   - You should see three provider cards (Ground Owner, Coach, Shop Owner)

2. **Test the application forms:**
   - Click on any provider card
   - Fill out the multi-step form
   - Upload test documents
   - Submit the application

3. **Test the admin panel:**
   - Login as admin
   - Go to: `http://localhost:3000/admin/provider-applications`
   - You should see the applications dashboard

## 🐛 Troubleshooting

### If pages don't load:
1. Check PHP error logs
2. Make sure routes are in `index.php`
3. Verify ProviderController.php exists in `/app/controllers/`

### If "500 Internal Server Error":
- The database table doesn't exist yet - run the SQL file (Step 1 above)

### If JavaScript errors in console:
- Clear your browser cache
- Hard refresh with Ctrl+F5 (or Cmd+Shift+R on Mac)

### If file uploads don't work:
```bash
chmod 755 public/uploads/provider-applications
```

## 📁 Key Files

- **Routes**: `/index.php` (lines 243-256)
- **Controller**: `/app/controllers/ProviderController.php`
- **Admin Controller**: `/app/controllers/AdminController.php`
- **Views**: `/app/views/provider/`
- **Admin View**: `/app/views/admin/provider-applications.php`
- **CSS**: `/public/css/pages/provider-*.css`
- **JS**: `/public/js/provider-application.js` and `/public/js/admin-applications.js`

## 🔄 The Complete Workflow

1. User clicks "Join as Provider" in navbar
2. User selects role (Ground Owner / Coach / Shop Owner)
3. User fills out the role-specific form
4. Application is saved to `provider_applications` table
5. Admin views application in admin panel
6. Admin approves/rejects application
7. If approved: User's `user_type` in `users` table is updated
8. User can now access their provider dashboard

## 📧 Email Notifications (TODO)

The system has placeholders for email notifications. To enable:
1. Configure an email service (PHPMailer, SendGrid, etc.)
2. Update the email methods in `ProviderController.php`:
   - `sendAdminNotification()` (line 383)
   - `sendApplicantConfirmation()` (line 391)
3. Update the email methods in `AdminController.php`:
   - After approval (line 227)
   - After rejection (line 273)

## ✨ Features

- Multi-step forms with validation
- File upload support (NIC, certificates, photos)
- Real-time form validation
- Admin dashboard with filters
- Statistics tracking
- Approve/Reject functionality
- Automatic user role update on approval

## 🎯 Test Credentials

Make sure you have an admin account to test the admin panel:
- User type in database should be `admin`
- Login and access `/admin/provider-applications`

---

**Need Help?**
Check the main setup guide: `PROVIDER_SETUP_GUIDE.md`
