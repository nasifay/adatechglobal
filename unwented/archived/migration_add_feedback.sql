-- Migration: add feedback table for About page client feedback
-- BACKUP INSTRUCTIONS
-- 1) Dump your existing database before running this migration:
--    mysqldump -u <db_user> -p <db_name> > backup_adatech_$(Get-Date -Format yyyyMMdd_HHmmss).sql
--    Example (PowerShell):
--    mysqldump -u root -p adatech_cms > "d:/backups/adatech_backup_$(Get-Date -Format yyyyMMdd_HHmmss).sql"
-- 2) Confirm the backup file exists and you can inspect it in your SQL editor or text viewer.
-- 3) Run this migration on the target DB. If using MySQL 8+ you can run this file directly.

-- Use your DB
USE `adatech_cms`;

-- Create feedback table (separate from testimonials)
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

-- Helpful indexes
CREATE INDEX IF NOT EXISTS idx_feedback_visible ON feedback(visible);

-- Optional: add attached references to images table if not yet present (MySQL 8+)
ALTER TABLE images ADD COLUMN IF NOT EXISTS attached_type VARCHAR(32) DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS attached_id INT DEFAULT NULL;

-- Optional: add image column to content if not yet present
ALTER TABLE content ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL;

-- End of migration
