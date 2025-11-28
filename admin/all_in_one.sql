-- All-in-one migration and seed for Adatech CMS
-- Fixed for shared hosting / cPanel
-- Make sure the user has all privileges

USE `adatecmu_adatech_cms`;  -- Your actual database name

-- ==========================
-- Core tables
-- ==========================
CREATE TABLE IF NOT EXISTS content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(32) NOT NULL,
  title VARCHAR(255),
  body TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS team (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  role VARCHAR(128),
  bio TEXT,
  image VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(128) NOT NULL,
  role VARCHAR(128),
  message TEXT,
  image VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(128) NOT NULL,
  description TEXT,
  icon VARCHAR(64),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(128) NOT NULL,
  description TEXT,
  image VARCHAR(255),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(32) NOT NULL,
  filename VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

-- ==========================
-- Optional columns/extensions (MySQL 5.7+)
-- ==========================
DELIMITER $$
DROP PROCEDURE IF EXISTS ensure_columns$$
CREATE PROCEDURE ensure_columns()
BEGIN
  DECLARE cnt INT DEFAULT 0;

  -- content optional columns
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='content' AND COLUMN_NAME='image';
  IF cnt = 0 THEN ALTER TABLE content ADD COLUMN image VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='content' AND COLUMN_NAME='slug';
  IF cnt = 0 THEN ALTER TABLE content ADD COLUMN slug VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='content' AND COLUMN_NAME='meta_title';
  IF cnt = 0 THEN ALTER TABLE content ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='content' AND COLUMN_NAME='meta_description';
  IF cnt = 0 THEN ALTER TABLE content ADD COLUMN meta_description VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='content' AND COLUMN_NAME='status';
  IF cnt = 0 THEN ALTER TABLE content ADD COLUMN status ENUM('published','draft') DEFAULT 'published'; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='content' AND COLUMN_NAME='created_at';
  IF cnt = 0 THEN ALTER TABLE content ADD COLUMN created_at TIMESTAMP NULL DEFAULT NULL; END IF;

  -- images extended
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='path';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN path VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='alt';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN alt VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='caption';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN caption TEXT DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='folder';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN folder VARCHAR(128) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='width';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN width INT DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='height';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN height INT DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='filesize';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN filesize BIGINT DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='registered';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN registered TINYINT(1) DEFAULT 1; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='uploaded_by';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN uploaded_by VARCHAR(128) DEFAULT NULL; END IF;

  -- team extras
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='team' AND COLUMN_NAME='image_alt';
  IF cnt = 0 THEN ALTER TABLE team ADD COLUMN image_alt VARCHAR(255) DEFAULT NULL; END IF;

  -- testimonials extras
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='testimonials' AND COLUMN_NAME='author';
  IF cnt = 0 THEN ALTER TABLE testimonials ADD COLUMN author VARCHAR(128) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='testimonials' AND COLUMN_NAME='company';
  IF cnt = 0 THEN ALTER TABLE testimonials ADD COLUMN company VARCHAR(128) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='testimonials' AND COLUMN_NAME='quote';
  IF cnt = 0 THEN ALTER TABLE testimonials ADD COLUMN quote TEXT DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='testimonials' AND COLUMN_NAME='visible';
  IF cnt = 0 THEN ALTER TABLE testimonials ADD COLUMN visible TINYINT(1) DEFAULT 1; END IF;

  -- projects extras
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='projects' AND COLUMN_NAME='excerpt';
  IF cnt = 0 THEN ALTER TABLE projects ADD COLUMN excerpt TEXT DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='projects' AND COLUMN_NAME='image_alt';
  IF cnt = 0 THEN ALTER TABLE projects ADD COLUMN image_alt VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='projects' AND COLUMN_NAME='category';
  IF cnt = 0 THEN ALTER TABLE projects ADD COLUMN category VARCHAR(128) DEFAULT NULL; END IF;

  -- services extras
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='services' AND COLUMN_NAME='excerpt';
  IF cnt = 0 THEN ALTER TABLE services ADD COLUMN excerpt TEXT DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='services' AND COLUMN_NAME='image';
  IF cnt = 0 THEN ALTER TABLE services ADD COLUMN image VARCHAR(255) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='services' AND COLUMN_NAME='image_alt';
  IF cnt = 0 THEN ALTER TABLE services ADD COLUMN image_alt VARCHAR(255) DEFAULT NULL; END IF;

  -- posts extras
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='posts' AND COLUMN_NAME='status';
  IF cnt = 0 THEN ALTER TABLE posts ADD COLUMN status ENUM('published','draft') DEFAULT 'published'; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='posts' AND COLUMN_NAME='published_at';
  IF cnt = 0 THEN ALTER TABLE posts ADD COLUMN published_at TIMESTAMP NULL DEFAULT NULL; END IF;

  -- images attached refs
  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='attached_type';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN attached_type VARCHAR(32) DEFAULT NULL; END IF;

  SELECT COUNT(*) INTO cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='images' AND COLUMN_NAME='attached_id';
  IF cnt = 0 THEN ALTER TABLE images ADD COLUMN attached_id INT DEFAULT NULL; END IF;

END$$
CALL ensure_columns()$$
DROP PROCEDURE IF EXISTS ensure_columns$$
DELIMITER ;

-- ==========================
-- Feedback table
-- ==========================
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

CREATE INDEX IF NOT EXISTS idx_feedback_visible ON feedback(visible);

-- ==========================
-- Seed data (Home, About, Services, Testimonials)
-- ==========================
-- Home content
UPDATE content SET title = 'We deliver innovative software, embedded systems, and digital solutions.',
  body = '<h2>We deliver innovative software, embedded systems, and digital solutions.</h2><p>We empower businesses, communities and innovators with web & mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>',
  updated_at = NOW()
WHERE type = 'index';

INSERT INTO content (type, title, body, created_at, updated_at)
SELECT 'index', 'We deliver innovative software, embedded systems, and digital solutions.', '<h2>We deliver innovative software, embedded systems, and digital solutions.</h2><p>We empower businesses, communities and innovators with web & mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM content WHERE type = 'index');

-- You can continue adding your About, Services, Testimonials seed data here...
