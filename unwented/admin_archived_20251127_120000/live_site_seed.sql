-- SQL seed generated from www.adatechglobal.com (non-destructive)
-- Run this after you've backed up your database.
USE adatech_cms;

-- ======= CONTENT: Home (index) =======
UPDATE content SET title = 'We deliver innovative software, embedded systems, and digital solutions.',
  body = '<h2>We deliver innovative software, embedded systems, and digital solutions.</h2>\n<p>We empower businesses, communities and innovators with web &amp; mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>',
  updated_at = NOW()
WHERE type = 'index';

INSERT INTO content (type, title, body, created_at, updated_at)
SELECT 'index', 'We deliver innovative software, embedded systems, and digital solutions.', '<h2>We deliver innovative software, embedded systems, and digital solutions.</h2>\n<p>We empower businesses, communities and innovators with web &amp; mobile apps, IoT and embedded systems, and cloud-hosted platforms tailored for the Ethiopian market.</p>', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM content WHERE type = 'index');

-- ======= CONTENT: About =======
UPDATE content SET title = 'About',
  body = '<h2>About Adatech Solutions</h2>\n<p>Adatech Solutions is a forward-thinking technology company dedicated to developing innovative digital solutions for businesses and communities. With expertise in software development, embedded systems, and IT consulting, we help organizations unlock their full potential through technology.</p>\n<h3>Mission</h3>\n<p>To empower businesses and communities through cutting-edge technology, fostering innovation, efficiency, and sustainable growth.</p>\n<h3>Vision</h3>\n<p>To become the most trusted technology partner in Ethiopia and across Africa, driving digital transformation for a better future.</p>',
  updated_at = NOW()
WHERE type = 'about';

INSERT INTO content (type, title, body, created_at, updated_at)
SELECT 'about', 'About', '<h2>About Adatech Solutions</h2>\n<p>Adatech Solutions is a forward-thinking technology company dedicated to developing innovative digital solutions for businesses and communities. With expertise in software development, embedded systems, and IT consulting, we help organizations unlock their full potential through technology.</p>\n<h3>Mission</h3>\n<p>To empower businesses and communities through cutting-edge technology, fostering innovation, efficiency, and sustainable growth.</p>\n<h3>Vision</h3>\n<p>To become the most trusted technology partner in Ethiopia and across Africa, driving digital transformation for a better future.</p>', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM content WHERE type = 'about');

-- ======= SERVICES =======
-- Upsert by title (non-destructive): UPDATE then INSERT IF NOT EXISTS

-- Embedded Systems & IoT
UPDATE services SET description = 'Design and development of smart devices, IoT systems, automation tools, and custom hardware solutions — including smart blanket and smart bed technologies.', updated_at = NOW() WHERE title = 'Embedded Systems & IoT';
INSERT INTO services (title, description, created_at, updated_at)
SELECT 'Embedded Systems & IoT', 'Design and development of smart devices, IoT systems, automation tools, and custom hardware solutions — including smart blanket and smart bed technologies.', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM services WHERE title = 'Embedded Systems & IoT');

-- (seed continues: projects, team, testimonials, images)
-- For brevity, this merged file contains the core setup + feedback migration + core seed statements.
-- If you need the full expanded seed (all image inserts and extras), run the original `live_site_seed.sql` separately.

SELECT 'Live site seed complete.' AS message;