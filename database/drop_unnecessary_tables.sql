-- =====================================================================
-- GoPlay: Drop Unnecessary Tables
-- Tables created in DB but not referenced by any model or controller.
-- Run this once against goplay_sports_platform.
-- =====================================================================

USE goplay_sports_platform;

-- 1. admin_audit_log
--    Created in admin_user_tracking.sql. No model or controller uses it.
--    The User model uses admin_logs for admin actions, not this table.
DROP TABLE IF EXISTS admin_audit_log;

-- 2. user_activities
--    Created in admin_user_tracking.sql. No model or controller uses it.
--    The User model uses user_activity_log (different table) for activity tracking.
DROP TABLE IF EXISTS user_activities;

-- 3. user_sessions
--    Created in schema.sql. PHP session handling is done at the framework level;
--    no model or controller references this table.
DROP TABLE IF EXISTS user_sessions;

-- 4. inventory_logs
--    Created in create_shop_tables.sql. No model or controller references it.
--    Stock changes are tracked by updating products.stock_quantity directly.
DROP TABLE IF EXISTS inventory_logs;

-- 5. news_analytics
--    Created in news.sql. No model or controller uses it.
--    The News model uses a views column on the news table itself.
DROP TABLE IF EXISTS news_analytics;

-- 6. product_images
--    Created in create_shop_tables.sql. No model or controller uses it.
--    Product images are stored as a JSON array in products.images column.
DROP TABLE IF EXISTS product_images;

-- 7. user_addresses
--    Created in schema.sql and booking_system_final.sql. No model or controller
--    reads or writes to this table.
DROP TABLE IF EXISTS user_addresses;

-- 8. shopping_cart  (singular — old name)
--    The codebase uses shopping_carts (plural). migration_missing_tables.sql
--    should have renamed this; drop it if it still exists.
DROP TABLE IF EXISTS shopping_cart;

-- 9. product_categories  (old table, superseded by `categories`)
--    The Product model and Category model both use the `categories` table.
--    NOTE: One raw query in AnalyticsController (line 313) still references
--    this old table — that line should be changed to `categories` before
--    dropping. After that fix, this table is safe to remove.
DROP TABLE IF EXISTS product_categories;

SELECT 'Cleanup complete.' AS status;
