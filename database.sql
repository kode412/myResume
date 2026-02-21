-- Database: progress
-- Database untuk Project Portfolio (progressku)

-- Create Database
CREATE DATABASE IF NOT EXISTS `progress`;
USE `progress`;

-- ============================================
-- Table: tags
-- ============================================
CREATE TABLE `tags` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `color` VARCHAR(7) NOT NULL DEFAULT '#3498db',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: cards
-- ============================================
CREATE TABLE `cards` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `project_type` ENUM('Web', 'Mobile', 'Arduino', 'Desktop', 'IoT') NOT NULL,
  `image_path` VARCHAR(500) NOT NULL,
  `progress` INT DEFAULT 0 CHECK (progress >= 0 AND progress <= 100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_project_type` (`project_type`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: card_tags (Junction Table)
-- ============================================
CREATE TABLE `card_tags` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `card_id` INT NOT NULL,
  `tag_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_card_tags_card_id` FOREIGN KEY (`card_id`) REFERENCES `cards`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_card_tags_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_card_tag` (`card_id`, `tag_id`),
  INDEX `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data: Tags (Tech Stack)
-- ============================================
INSERT INTO `tags` (`name`, `color`) VALUES
('HTML', '#E34C26'),
('CSS', '#1572B6'),
('JavaScript', '#F7DF1E'),
('PHP', '#777BB4'),
('Laravel', '#FF2D20'),
('Vue.js', '#4FC08D'),
('React', '#61DAFB'),
('Node.js', '#339933'),
('MongoDB', '#13AA52'),
('MySQL', '#005C84'),
('Flutter', '#02569B'),
('Dart', '#00B4AB'),
('Python', '#3776AB'),
('Arduino', '#00979D'),
('C++', '#00599C'),
('Java', '#007396'),
('Bootstrap', '#7952B3'),
('Tailwind CSS', '#06B6D4'),
('PostgreSQL', '#336791'),
('Firebase', '#FFA000'),
('Git', '#F1502F'),
('Docker', '#2496ED'),
('AWS', '#FF9900'),
('API REST', '#09D3AC'),
('GraphQL', '#E10098'),
('Webpack', '#8DD6F9'),
('Electron', '#9FEDF9'),
('Next.js', '#000000'),
('Nuxt.js', '#00DC82'),
('TypeScript', '#3178C6'),
('Kotlin', '#7F52FF'),
('Swift', '#FA7343'),
('Unity', '#000000'),
('Three.js', '#04F3DE'),
('D3.js', '#F9A825'),
('Figma', '#F24E1E'),
('Adobe XD', '#FF61F6'),
('Photoshop', '#31A8FF'),
('Illustrator', '#FF9A00');

-- ============================================
-- Optional: Sample Project Card (Example)
-- ============================================
-- Uncomment the following to add sample data:

-- INSERT INTO `cards` (`title`, `project_type`, `image_path`, `progress`) VALUES
-- ('Sistem Manajemen Inventory', 'Web', 'uploads/sample_1.jpg', 85),
-- ('Mobile Banking App', 'Mobile', 'uploads/sample_2.jpg', 70),
-- ('Smart Home Controller', 'Arduino', 'uploads/sample_3.jpg', 60),
-- ('Desktop POS System', 'Desktop', 'uploads/sample_4.jpg', 95),
-- ('IoT Weather Station', 'IoT', 'uploads/sample_5.jpg', 45);

-- ============================================
-- Sample card_tags associations (Example)
-- ============================================
-- Uncomment the following to add sample data:

-- INSERT INTO `card_tags` (`card_id`, `tag_id`) VALUES
-- (1, 1), (1, 2), (1, 3), (1, 5), (1, 10), (1, 24), -- HTML, CSS, JavaScript, Laravel, MySQL, API REST
-- (2, 11), (2, 12), (2, 20), -- Flutter, Dart, Firebase
-- (3, 14), (3, 15), -- Arduino, C++
-- (4, 6), (4, 5), (4, 10), (4, 18), -- Vue.js, Laravel, MySQL, Tailwind CSS
-- (5, 14), (5, 23); -- Arduino, AWS

-- ============================================
-- Table: pages (About / Services / Contact content)
-- ============================================
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('about','service','contact','project','resume','other') NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `pdf_file` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: contacts (messages submitted via contact form)
-- ============================================
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample content: About + Contact placeholder
INSERT INTO `pages` (`type`, `title`, `content`) VALUES
('about', 'Tentang Project Ku', '<p>Selamat datang di Project Ku. Di sini saya menyimpan portofolio proyek yang pernah saya kerjakan.</p>'),
('contact', 'Kontak', '<p>Silakan gunakan form untuk menghubungi saya. Saya akan merespon lewat email.</p>');

-- ============================================
-- Table: users (for admin login)
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','editor') DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: A default admin user will be created on first login attempt by the auth helper if the users table is empty.

-- ============================================
-- Table: education (Resume education history)
-- ============================================
CREATE TABLE IF NOT EXISTS `education` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `school` VARCHAR(255) NOT NULL,
  `degree` VARCHAR(100) NOT NULL,
  `field` VARCHAR(100) NOT NULL,
  `year_start` INT,
  `year_end` INT,
  `description` TEXT,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: skills (Resume skills)
-- ============================================
CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `proficiency` INT DEFAULT 50 CHECK (proficiency >= 0 AND proficiency <= 100),
  `category` VARCHAR(50) DEFAULT 'Technical',
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: experience (Resume work experience)
-- ============================================
CREATE TABLE IF NOT EXISTS `experience` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `company` VARCHAR(255) NOT NULL,
  `position` VARCHAR(100) NOT NULL,
  `year_start` INT,
  `year_end` INT,
  `description` TEXT,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: A default admin user will be created on first login attempt by the auth helper if the users table is empty.
