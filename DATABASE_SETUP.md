# GoPlay Shop Database Setup Guide

This guide will help you set up the database for the GoPlay shop feature with sample products.

## Prerequisites

- MySQL server running
- PHP PDO MySQL extension enabled
- Access to MySQL command line or phpMyAdmin

## Step 1: Create Database

First, create the database if it doesn't exist:

```sql
CREATE DATABASE IF NOT EXISTS goplay_db;
USE goplay_db;
```

## Step 2: Run the Migration Script

Execute the SQL script to create tables and insert sample data:

```bash
# Method 1: Using MySQL command line
mysql -u root -p goplay_db < database/create_shop_tables.sql

# Method 2: Using phpMyAdmin
# - Open phpMyAdmin
# - Select 'goplay_db' database
# - Go to 'Import' tab
# - Choose the file: database/create_shop_tables.sql
# - Click 'Go'
```

## Step 3: Verify Database Setup

Check if tables were created successfully:

```sql
USE goplay_db;

-- Check tables
SHOW TABLES;

-- Check sample data
SELECT COUNT(*) as total_products FROM products;
SELECT COUNT(*) as total_categories FROM categories;

-- View sample products
SELECT id, name, price, category_id, stock_quantity FROM products LIMIT 5;

-- View categories with product counts
SELECT c.name, COUNT(p.id) as product_count 
FROM categories c 
LEFT JOIN products p ON c.id = p.category_id 
GROUP BY c.id;
```

## Expected Results

After successful setup, you should have:

### Tables Created:
- `categories` - Product categories (8 categories)
- `products` - Product listings (15 sample products)
- `product_reviews` - Customer reviews
- `product_images` - Product image gallery

### Sample Data:
- **15 Products** across 8 categories
- **Categories**: Football, Tennis, Basketball, Cricket, Badminton, Swimming, Fitness, Running
- **Product Features**: Prices, ratings, stock levels, descriptions, features lists
- **Realistic Data**: Sri Lankan pricing in LKR, local brands and descriptions

## Step 4: Test the Integration

1. **Start your web server** and navigate to the shop page: `http://localhost/shop`

2. **Expected Features:**
   - Products load from database instead of hardcoded data
   - Categories display with product counts
   - Search and filtering work with database
   - Product cards show real data (prices, ratings, stock status)
   - Error handling for database connection issues

3. **Check Error Logs:**
   - Monitor your PHP error logs for any database connection issues
   - Check browser console for JavaScript errors

## Troubleshooting

### Database Connection Issues

If you see "Unable to load products" error:

1. **Check Database Credentials** in `.env` file:
   ```
   DB_HOST=localhost
   DB_NAME=goplay_db
   DB_USER=root
   DB_PASS=your_password
   ```

2. **Test Database Connection:**
   ```php
   <?php
   try {
       $pdo = new PDO('mysql:host=localhost;dbname=goplay_db', 'root', 'your_password');
       echo "Database connection successful!";
   } catch (PDOException $e) {
       echo "Connection failed: " . $e->getMessage();
   }
   ?>
   ```

3. **Check MySQL Service:**
   ```bash
   # Start MySQL service
   sudo service mysql start
   # OR
   brew services start mysql
   ```

### No Products Showing

1. **Verify tables exist and have data:**
   ```sql
   SELECT COUNT(*) FROM products WHERE is_active = 1;
   ```

2. **Check PHP errors:**
   - Enable error reporting in `.env`: `APP_DEBUG=true`
   - Check PHP error logs

3. **Clear any caches:**
   - Refresh the browser with Ctrl+F5
   - Clear browser cache

## Database Schema Details

### Products Table Key Fields:
- `id` - Primary key
- `name` - Product name
- `price` - Current price (DECIMAL)
- `original_price` - Original price for discount calculation
- `category_id` - Links to categories table
- `stock_quantity` - Available stock
- `rating` - Average rating (0-5)
- `review_count` - Number of reviews
- `is_featured` - Featured product flag
- `is_active` - Active/inactive status
- `features` - JSON array of product features

### Categories Table:
- `id` - Primary key
- `name` - Category name
- `slug` - URL-friendly name
- `icon` - Emoji icon for display

## Next Steps

After successful setup:

1. **Add More Products:** Insert additional products into the database
2. **Customize Categories:** Modify categories to match your needs
3. **Set Up Reviews:** Configure the review system
4. **Add Product Images:** Upload actual product images
5. **Configure Inventory:** Set up stock management

## API Endpoints Available

Once set up, these endpoints will work:

- `GET /api/products` - Get all products with filters
- `GET /api/categories` - Get all categories
- `GET /api/products/search?q=term` - Search products
- `GET /product/{id}` - Get single product details

## Support

If you encounter issues:

1. Check the error logs in your web server
2. Verify database permissions
3. Ensure all PHP extensions are installed
4. Test with a simple database connection script

The shop should now display real data from your database instead of hardcoded sample data!