-- ============================================
-- Size and Color Product Variants
-- ============================================

-- Add size and color fields to products table
ALTER TABLE products ADD COLUMN IF NOT EXISTS available_sizes VARCHAR(500) NULL COMMENT 'Comma-separated list of available sizes (e.g., S,M,L,XL)';
ALTER TABLE products ADD COLUMN IF NOT EXISTS available_colors VARCHAR(500) NULL COMMENT 'Comma-separated list of available colors (e.g., Red,Blue,Green)';

-- Add size and color fields to cart_items table (if not exists)
ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS selected_size VARCHAR(100) NULL COMMENT 'Size selected for this cart item';
ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS selected_color VARCHAR(100) NULL COMMENT 'Color selected for this cart item';

-- Add size and color fields to order_items table to track what was ordered
ALTER TABLE order_items ADD COLUMN IF NOT EXISTS selected_size VARCHAR(100) NULL COMMENT 'Size selected for this order item';
ALTER TABLE order_items ADD COLUMN IF NOT EXISTS selected_color VARCHAR(100) NULL COMMENT 'Color selected for this order item';

