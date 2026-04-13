# GoPlay Sports Platform - Setup Guide

## Prerequisites

- **PHP 8.0+** with extensions: `pdo_mysql`, `mbstring`, `json`, `fileinfo`
- **MySQL 5.7+** or **MariaDB 10.3+**
- **Composer** (PHP package manager)
- **XAMPP / WAMP / MAMP** or standalone PHP + MySQL

---

## Quick Start (5 minutes)

### 1. Clone & Install Dependencies
```bash
git clone https://github.com/RavinduIT/GoPlay.git
cd GoPlay
composer install
```

### 2. Configure Environment
```bash
cp .env.example .env
```
Edit `.env` and set your database credentials:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=goplay_sports_platform
DB_USER=root
DB_PASS=
```

### 3. Create Database
Run the master setup SQL:
```bash
mysql -u root -p < database/goplay_master_setup.sql
```
Or import `database/goplay_master_setup.sql` via **phpMyAdmin**.

This single file creates:
- All 20+ tables
- Indexes for performance
- Sample users, coaches, facilities, news, promotions, and contact messages

### 4. Start the Server
```bash
php -S localhost:8000
```

### 5. Login
Open `http://localhost:8000` and login with any of these accounts:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin1@goplay.lk` | `password123` |
| **User** | `user1@goplay.lk` | `password123` |
| **Coach** | `coach1@goplay.lk` | `password123` |
| **Ground Owner** | `groundowner1@goplay.lk` | `password123` |
| **Shop Owner** | `shopowner1@goplay.lk` | `password123` |

---

## Admin Dashboard

After logging in as admin, navigate to `/admin/dashboard`.

### Admin Modules (CRUDs)

| Module | URL | Description |
|--------|-----|-------------|
| Dashboard | `/admin/dashboard` | Overview stats, charts, recent activity |
| User Management | `/admin/users` | View/edit/delete users, change roles |
| News Management | `/admin/news` | Create/edit/delete news articles |
| Sports Categories | `/admin/categories` | Manage sport types across the platform |
| Promotions | `/admin/promotions` | Create homepage banners & promotions |
| Contact Messages | `/admin/contacts` | View/reply/archive user inquiries |
| Provider Applications | `/admin/provider-applications` | Approve/reject provider registrations |
| Analytics | `/admin/analytics` | Platform usage analytics |

---

## Project Structure

```
GoPlay/
├── app/
│   ├── controllers/          # Route handlers
│   │   ├── admin/            # Admin-specific controllers
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── ...
│   ├── models/               # Database models
│   ├── services/             # Business logic (EmailService, etc.)
│   └── views/                # PHP templates
│       ├── admin/            # Admin panel pages
│       ├── components/       # Shared components (navbar, footer, sidebar)
│       ├── home/             # Public pages (index, contact, about)
│       ├── news/             # News pages
│       └── ...
├── core/                     # Framework core (Router, Database, Request, Response)
├── database/                 # SQL files
│   └── goplay_master_setup.sql  # ← Run this to set up everything
├── public/
│   ├── css/pages/            # Page-specific stylesheets
│   ├── js/pages/             # Page-specific JavaScript
│   ├── assets/images/        # Static images
│   └── uploads/              # User uploads (auto-created)
├── .env                      # Environment config (create from .env.example)
├── .env.example              # Environment template
└── index.php                 # Front controller & router
```

---

## Email Configuration (Optional)

To enable email notifications (provider approvals, contact replies), configure SMTP in `.env`:

### Gmail Setup
1. Go to Google Account → Security → 2-Step Verification → App Passwords
2. Generate an app password for "Mail"
3. Set in `.env`:
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
```

---

## Database Files Reference

| File | Purpose |
|------|---------|
| `goplay_master_setup.sql` | **Use this** - Complete setup with all tables + data |
| `schema.sql` | Original core tables only |
| `news.sql` | News system extensions |
| `promotions_contacts.sql` | Promotions + Contact tables only |
| Other `.sql` files | Individual module migrations (already included in master) |

---

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 error on any page | Check PHP error log, verify `.env` DB credentials |
| Login not working | Ensure `users` table has data, password hash is argon2id |
| Admin pages blank | Verify `user_type = 'admin'` in your user record |
| Email not sending | Check SMTP credentials in `.env`, enable "less secure apps" or use app password |
| Images not loading | Ensure `public/uploads/` directory exists and is writable |
| CSS/JS not loading | Make sure you're running from project root, not a subdirectory |
