-- MySQL 5.7 compatible migration: add content.image, images.path/attached columns and feedback table
-- Run this file with the mysql client or phpMyAdmin against the `adatech_cms` database.
-- Example (PowerShell):
-- mysql -u root -p < 'd:/Xampp/htdocs/UpConstruction-1.0.0/admin/mysql57_migration.sql'

USE `adatech_cms`;

-- Add image column to content (if it already exists you'll see an error which can be ignored)
ALTER TABLE content ADD COLUMN image VARCHAR(255) DEFAULT NULL;

-- Add path and metadata columns to images table
ALTER TABLE images ADD COLUMN path VARCHAR(255) DEFAULT NULL;
ALTER TABLE images ADD COLUMN alt VARCHAR(255) DEFAULT NULL;
ALTER TABLE images ADD COLUMN caption TEXT DEFAULT NULL;
ALTER TABLE images ADD COLUMN folder VARCHAR(128) DEFAULT NULL;
ALTER TABLE images ADD COLUMN width INT DEFAULT NULL;
ALTER TABLE images ADD COLUMN height INT DEFAULT NULL;
ALTER TABLE images ADD COLUMN filesize BIGINT DEFAULT NULL;
ALTER TABLE images ADD COLUMN registered TINYINT(1) DEFAULT 1;
ALTER TABLE images ADD COLUMN uploaded_by VARCHAR(128) DEFAULT NULL;

-- Add attached reference columns (if not present)
ALTER TABLE images ADD COLUMN attached_type VARCHAR(32) DEFAULT NULL;
ALTER TABLE images ADD COLUMN attached_id INT DEFAULT NULL;

-- Create feedback table if missing
CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  email VARCHAR(255) DEFAULT NULL,
  company VARCHAR(128) DEFAULT NULL,
  message TEXT NOT NULL,
  visible TINYINT(1) DEFAULT 1,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Helpful index
CREATE INDEX idx_feedback_visible ON feedback(visible);

-- Note: MySQL 5.7 does not support 'ADD COLUMN IF NOT EXISTS' in ALTER statements.
-- If any ALTER fails because the column already exists, you can safely ignore that error.
-- If an ALTER fails for permissions or other reasons, run the statements individually using a DB client with sufficient privileges.
