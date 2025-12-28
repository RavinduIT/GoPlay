-- Migration: Add shop_owner_id column to products table
-- Purpose: Associate products with shop owners for multi-vendor functionality

-- Add shop_owner_id column if it doesn't exist
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS shop_owner_id INT NULL DEFAULT NULL
COMMENT 'ID of the shop owner. NULL means unowned product (claimable by any shop owner)';

-- Add foreign key constraint to users table
ALTER TABLE products
ADD CONSTRAINT fk_products_shop_owner 
FOREIGN KEY (shop_owner_id) 
REFERENCES users(id) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Add index for performance on shop owner queries
CREATE INDEX IF NOT EXISTS idx_products_shop_owner_id 
ON products(shop_owner_id);

-- Add compound index for shop owner + status queries
CREATE INDEX IF NOT EXISTS idx_products_owner_status 
ON products(shop_owner_id, status);

-- Update existing products (if needed)
-- Uncomment the following line to assign all existing products to a specific shop owner
-- UPDATE products SET shop_owner_id = 1 WHERE shop_owner_id IS NULL;

-- Verify the changes
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT,
    COLUMN_KEY
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'products'
  AND COLUMN_NAME = 'shop_owner_id';
