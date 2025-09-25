-- Migration: Remove age column from users table
-- Date: 2025-09-25
-- Purpose: Replace age field with calculated age from date_of_birth for better data integrity

-- Add the column if it doesn't exist (safety measure)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL;

-- Remove the age column if it exists
ALTER TABLE users 
DROP COLUMN IF EXISTS age;

-- Update any existing null date_of_birth values based on current age (if needed)
-- Note: This is optional and should only be run if you have existing age data you want to preserve
-- UPDATE users SET date_of_birth = DATE_SUB(CURRENT_DATE, INTERVAL age YEAR) WHERE date_of_birth IS NULL AND age IS NOT NULL;