-- Merged SQL: setup + feedback migration + live site seed
-- Run this file after backing up your database.
-- Example (PowerShell):
-- mysqldump -u root -p adatech_cms > "d:/backups/adatech_backup_$(Get-Date -Format yyyyMMdd_HHmmss).sql"
-- Then import:
-- mysql -u root -p adatech_cms < "d:/Xampp/htdocs/UpConstruction-1.0.0/admin/merged_migrations.sql"

-- ==========================
-- Part 1: setup.sql
-- ==========================
CREATE DATABASE IF NOT EXISTS adatech_cms DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE adatech_cms;

-- Content (landing, about, contact)
CREATE TABLE IF NOT EXISTS content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(32) NOT NULL,
  title VARCHAR(255),
  body TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Team
CREATE TABLE IF NOT EXISTS team (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  role VARCHAR(128),
  bio TEXT,
  image VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Testimonials
CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  role VARCHAR(128),
  message TEXT,
  image VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services
CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(128) NOT NULL,
  description TEXT,
  icon VARCHAR(64),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Projects
CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(128) NOT NULL,
  description TEXT,
  image VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Images (logo, etc.)
CREATE TABLE IF NOT EXISTS images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(32) NOT NULL,
  filename VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blog posts
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE,
  excerpt TEXT,
  body TEXT,
  image VARCHAR(255),
  author VARCHAR(128),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Extended schema (ALTERs using MySQL 8+ syntax)
ALTER TABLE content ADD COLUMN IF NOT EXISTS slug VARCHAR(255) DEFAULT NULL;
ALTER TABLE content ADD COLUMN IF NOT EXISTS meta_title VARCHAR(255) DEFAULT NULL;
ALTER TABLE content ADD COLUMN IF NOT EXISTS meta_description VARCHAR(255) DEFAULT NULL;
ALTER TABLE content ADD COLUMN IF NOT EXISTS status ENUM('published','draft') DEFAULT 'published';
ALTER TABLE content ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE images ADD COLUMN IF NOT EXISTS path VARCHAR(255) DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS alt VARCHAR(255) DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS caption TEXT DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS folder VARCHAR(128) DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS width INT DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS height INT DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS filesize BIGINT DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS registered TINYINT(1) DEFAULT 1;
ALTER TABLE images ADD COLUMN IF NOT EXISTS uploaded_by VARCHAR(128) DEFAULT NULL;

ALTER TABLE team ADD COLUMN IF NOT EXISTS image_alt VARCHAR(255) DEFAULT NULL;

ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS author VARCHAR(128) DEFAULT NULL;
ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS company VARCHAR(128) DEFAULT NULL;
ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS quote TEXT DEFAULT NULL;
ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS visible TINYINT(1) DEFAULT 1;

ALTER TABLE projects ADD COLUMN IF NOT EXISTS excerpt TEXT DEFAULT NULL;
ALTER TABLE projects ADD COLUMN IF NOT EXISTS image_alt VARCHAR(255) DEFAULT NULL;
ALTER TABLE projects ADD COLUMN IF NOT EXISTS category VARCHAR(128) DEFAULT NULL;

ALTER TABLE services ADD COLUMN IF NOT EXISTS excerpt TEXT DEFAULT NULL;
ALTER TABLE services ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL;
ALTER TABLE services ADD COLUMN IF NOT EXISTS image_alt VARCHAR(255) DEFAULT NULL;

ALTER TABLE posts ADD COLUMN IF NOT EXISTS status ENUM('published','draft') DEFAULT 'published';
ALTER TABLE posts ADD COLUMN IF NOT EXISTS published_at TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE images ADD COLUMN IF NOT EXISTS attached_type VARCHAR(32) DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS attached_id INT DEFAULT NULL;
ALTER TABLE content ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL;

-- ==========================
-- Part 2: migration_add_feedback.sql
-- ==========================
USE `adatech_cms`;

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

ALTER TABLE images ADD COLUMN IF NOT EXISTS attached_type VARCHAR(32) DEFAULT NULL;
ALTER TABLE images ADD COLUMN IF NOT EXISTS attached_id INT DEFAULT NULL;

ALTER TABLE content ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL;

-- ==========================
-- Part 3: live_site_seed.sql
-- ==========================
USE adatech_cms;

-- Home content
UPDATE content SET title = 'We deliver innovative software, embedded systems, and digital solutions.',
  body = '<h2>We deliver innovative software, embedded systems, and digital solutions.</h2>\n<p>We empower businesses, communities and innovators with web &amp; mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>',
  updated_at = NOW()
WHERE type = 'index';

INSERT INTO content (type, title, body, created_at, updated_at)
SELECT 'index', 'We deliver innovative software, embedded systems, and digital solutions.', '<h2>We deliver innovative software, embedded systems, and digital solutions.</h2>\n<p>We empower businesses, communities and innovators with web &amp; mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM content WHERE type = 'index');

-- About
UPDATE content SET title = 'About',
  body = '<h2>About Adatech Solutions</h2>\n<p>Adatech Solutions is a forward-thinking technology company dedicated to developing innovative digital solutions for businesses and communities. With expertise in software development, embedded systems, and IT consulting, we help organizations unlock their full potential through technology.</p>\n<h3>Mission</h3>\n<p>To empower businesses and communities through cutting-edge technology, fostering innovation, efficiency, and sustainable growth.</p>\n<h3>Vision</h3>\n<p>To become the most trusted technology partner in Ethiopia and across Africa, driving digital transformation for a better future.</p>',
  updated_at = NOW()
WHERE type = 'about';

INSERT INTO content (type, title, body, created_at, updated_at)
SELECT 'about', 'About', '<h2>About Adatech Solutions</h2>\n<p>Adatech Solutions is a forward-thinking technology company dedicated to developing innovative digital solutions for businesses and communities. With expertise in software development, embedded systems, and IT consulting, we help organizations unlock their full potential through technology.</p>\n<h3>Mission</h3>\n<p>To empower businesses and communities through cutting-edge technology, fostering innovation, efficiency, and sustainable growth.</p>\n<h3>Vision</h3>\n<p>To become the most trusted technology partner in Ethiopia and across Africa, driving digital transformation for a better future.</p>', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM content WHERE type = 'about');

-- Services (examples)
UPDATE services SET description = 'Design and development of smart devices, IoT systems, automation tools, and custom hardware solutions — including smart blanket and smart bed technologies.', updated_at = NOW() WHERE title = 'Embedded Systems & IoT';
INSERT INTO services (title, description, created_at, updated_at)
SELECT 'Embedded Systems & IoT', 'Design and development of smart devices, IoT systems, automation tools, and custom hardware solutions — including smart blanket and smart bed technologies.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM services WHERE title = 'Embedded Systems & IoT');

-- (seed continues: projects, team, testimonials, images)
-- For brevity, this merged file contains the core setup + feedback migration + core seed statements.
-- If you need the full expanded seed (all image inserts and extras), run the original `live_site_seed.sql` separately.

SELECT 'Merged SQL ready.' AS message;
