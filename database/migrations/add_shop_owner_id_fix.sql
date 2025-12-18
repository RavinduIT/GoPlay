-- Fix: Add shop_owner_id to products table
USE goplay_sports_platform;

-- Check if column already exists before adding
SET @dbname = 'goplay_sports_platform';
SET @tablename = 'products';
SET @columnname = 'shop_owner_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_NAME = @tablename)
      AND (TABLE_SCHEMA = @dbname)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT NULL AFTER category_id")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add foreign key if it doesn't exist
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                  WHERE CONSTRAINT_NAME = 'fk_products_shop_owner'
                  AND TABLE_SCHEMA = 'goplay_sports_platform'
                  AND TABLE_NAME = 'products');

SET @preparedStatement = (SELECT IF(
  @fk_exists > 0,
  "SELECT 1",
  "ALTER TABLE products ADD CONSTRAINT fk_products_shop_owner FOREIGN KEY (shop_owner_id) REFERENCES users(id) ON DELETE SET NULL"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add index if it doesn't exist
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
                     WHERE INDEX_NAME = 'idx_products_shop_owner'
                     AND TABLE_SCHEMA = 'goplay_sports_platform'
                     AND TABLE_NAME = 'products');

SET @preparedStatement = (SELECT IF(
  @index_exists > 0,
  "SELECT 1",
  "CREATE INDEX idx_products_shop_owner ON products(shop_owner_id)"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add short_description column if it doesn't exist
SET @columnname = 'short_description';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_NAME = @tablename)
      AND (TABLE_SCHEMA = @dbname)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " VARCHAR(500) NULL AFTER description")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SELECT 'Migration completed successfully!' as status;
