-- Migration: Add age field to coaches table
-- Created: 2025-10-20
-- Purpose: Enable age-based filtering for coaches to allow senior coaches visibility

-- Add age column to coaches table
ALTER TABLE coaches
ADD COLUMN age INT NULL AFTER experience_years,
ADD COLUMN date_of_birth DATE NULL AFTER age;

-- Add index for performance on age filtering
CREATE INDEX idx_coaches_age ON coaches(age);

-- Add comment to columns
ALTER TABLE coaches
MODIFY COLUMN age INT NULL COMMENT 'Coach age in years',
MODIFY COLUMN date_of_birth DATE NULL COMMENT 'Coach date of birth for age calculation';

-- Update existing coaches with realistic age data
-- Distribution: 3 Senior Coaches (50+), 1 in 40s, 3 in 30s, 1 in 20s
UPDATE coaches SET age = 52, date_of_birth = '1972-03-15' WHERE id = 1; -- 15 years exp, Senior Coach
UPDATE coaches SET age = 38, date_of_birth = '1986-07-22' WHERE id = 2; -- 8 years exp
UPDATE coaches SET age = 45, date_of_birth = '1979-11-08' WHERE id = 3; -- 10 years exp
UPDATE coaches SET age = 55, date_of_birth = '1969-05-30' WHERE id = 4; -- 12 years exp, Senior Coach
UPDATE coaches SET age = 35, date_of_birth = '1989-09-14' WHERE id = 5; -- 7 years exp
UPDATE coaches SET age = 28, date_of_birth = '1996-12-01' WHERE id = 6; -- 4 years exp
UPDATE coaches SET age = 32, date_of_birth = '1992-04-19' WHERE id = 7; -- 6 years exp
UPDATE coaches SET age = 58, date_of_birth = '1966-08-25' WHERE id = 8; -- 5 years exp, Senior Coach (late career change)
