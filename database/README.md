# GoPlay Sports Platform - Database Setup Guide

## 📋 Prerequisites

Before setting up the database, ensure you have:
- MySQL Server installed (via XAMPP, WAMP, MAMP, or standalone)
- Web server with PHP support
- Access to MySQL command line or phpMyAdmin

---

## 🚀 Quick Setup (Using XAMPP)

### Step 1: Install XAMPP
1. Download XAMPP from https://www.apachefriends.org/
2. Install XAMPP on your computer
3. Start **Apache** and **MySQL** services from XAMPP Control Panel

### Step 2: Access phpMyAdmin
1. Open your browser
2. Navigate to `http://localhost/phpmyadmin`
3. Login with username: `root` (password is usually empty)

### Step 3: Create Database
1. In phpMyAdmin, click "SQL" tab
2. Copy and paste the content from `create_database.sql`
3. Click "Go" to execute the script
4. ✅ Your database structure is now created!

### Step 4: Add Sample Data (Optional)
1. In phpMyAdmin, select your `goplay_sports_platform` database
2. Click "SQL" tab
3. Copy and paste the content from `sample_data.sql`
4. Click "Go" to execute
5. ✅ Sample data is now loaded!

---

## 🔧 Manual Setup (Command Line)

### Step 1: Access MySQL
```bash
mysql -u root -p
```

### Step 2: Create Database
```sql
CREATE DATABASE goplay_sports_platform;
USE goplay_sports_platform;
SOURCE /path/to/your/project/database/create_database.sql;
SOURCE /path/to/your/project/database/sample_data.sql;
```

---

## 📊 Database Structure

### Core Tables Created:

#### **User Management**
- `users` - User accounts and profiles
- `user_addresses` - User addresses (billing/shipping)

#### **Sports & Categories** 
- `sports_categories` - Sports types (Basketball, Tennis, etc.)

#### **Coach System**
- `coaches` - Coach profiles and details
- `coach_reviews` - Coach ratings and reviews
- `coach_bookings` - Coach booking records

#### **Facility System**
- `sports_facilities` - Sports grounds/courts
- `facility_reviews` - Facility ratings and reviews  
- `facility_bookings` - Facility booking records

#### **E-commerce System**
- `product_categories` - Product categories
- `products` - Sports equipment and gear
- `product_reviews` - Product ratings and reviews
- `shopping_cart` - User shopping carts
- `orders` - Purchase orders
- `order_items` - Order line items

#### **Payment System**
- `payments` - Payment transactions and records

#### **Communication**
- `notifications` - User notifications

#### **System**
- `settings` - Application configuration

---

## 🔌 Database Connection Setup

### Update Configuration
Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');        // Your MySQL host
define('DB_USERNAME', 'root');         // Your MySQL username  
define('DB_PASSWORD', '');             // Your MySQL password
define('DB_NAME', 'goplay_sports_platform'); // Database name
```

### Test Connection
Create a test file `test_connection.php`:

```php
<?php
require_once 'config/database.php';

// Test the connection
if (testDatabaseConnection()) {
    echo "🎉 Database setup successful!";
} else {
    echo "❌ Database connection failed!";
}
?>
```

---

## 📝 API Endpoints

Once database is set up, you can use these API endpoints:

### Products API
- **GET** `/api/products.php?action=active` - Get active products
- **GET** `/api/products.php?id=1` - Get single product
- **POST** `/api/products.php` - Create new product
- **PUT** `/api/products.php?id=1` - Update product
- **DELETE** `/api/products.php?id=1` - Delete product

### Example API Usage:
```javascript
// Get all active products
fetch('/api/products.php?action=active')
    .then(response => response.json())
    .then(data => {
        console.log('Products:', data.data);
    });

// Create new product
fetch('/api/products.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        name: 'New Tennis Racket',
        category_id: 1,
        brand: 'Wilson',
        price: 150.00,
        stock_quantity: 10
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

---

## 🔍 Sample Data Included

The database comes pre-loaded with:
- **8 Sports Categories** (Basketball, Tennis, Football, etc.)
- **6 Sample Users** (Admin, Customers, Coaches, Facility Owner)
- **2 Professional Coaches** with experience and ratings
- **3 Sports Facilities** with different locations
- **8 Sports Products** matching your current JSON data
- **Sample Bookings** for coaches and facilities
- **Sample Orders** and purchase history
- **Reviews and Ratings** for products, coaches, facilities

---

## 🛠️ Maintenance Commands

### Backup Database
```bash
mysqldump -u root -p goplay_sports_platform > backup.sql
```

### Restore Database  
```bash
mysql -u root -p goplay_sports_platform < backup.sql
```

### Clear All Data (Keep Structure)
```sql
-- Run this to clear all data but keep tables
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE users;
TRUNCATE TABLE products;
-- ... repeat for all tables you want to clear
SET FOREIGN_KEY_CHECKS = 1;
```

---

## 🔐 Security Notes

### For Production:
1. **Change default passwords**
2. **Create specific database user** (don't use root)
3. **Enable SSL connections**
4. **Regularly backup your data**
5. **Update connection credentials** in `config/database.php`

### Create Production User:
```sql
CREATE USER 'goplay_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON goplay_sports_platform.* TO 'goplay_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 📞 Troubleshooting

### Common Issues:

**❌ Connection failed**
- Check if MySQL is running
- Verify username/password in config
- Ensure database exists

**❌ Permission denied**
- Check user has proper privileges
- Verify PHP has MySQL extension

**❌ Table doesn't exist**  
- Ensure you ran `create_database.sql`
- Check database name is correct

### Need Help?
1. Check MySQL error logs
2. Enable PHP error reporting
3. Verify all file paths are correct

---

## 🎉 You're All Set!

Your GoPlay Sports Platform database is now ready to use. The system supports:

✅ **User Management** - Registration, login, profiles  
✅ **Coach Booking** - Find and book professional coaches  
✅ **Facility Booking** - Reserve sports grounds and courts  
✅ **E-commerce** - Buy sports equipment and gear  
✅ **Payment Processing** - Handle transactions securely  
✅ **Reviews & Ratings** - User feedback system  
✅ **Admin Dashboard** - Manage all aspects of the platform  

Start building amazing sports experiences! 🏀⚽🎾