-- Blog Admin Panel Database Schema
-- Run this SQL file to create the necessary tables

-- Create database (uncomment if creating new database)
-- CREATE DATABASE IF NOT EXISTS techweblabs_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE techweblabs_blog;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Posts Table with SEO Fields
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `title` varchar(500) NOT NULL,
  `meta_title` varchar(500) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `seo_focus_keyword` varchar(255) DEFAULT NULL,
  `og_image` varchar(500) DEFAULT NULL,
  `schema_type` varchar(100) DEFAULT 'Article',
  `reading_time` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `author_id` (`author_id`),
  KEY `status` (`status`),
  KEY `published_at` (`published_at`),
  KEY `slug_status` (`slug`,`status`),
  FULLTEXT KEY `title_content` (`title`,`content`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `admin_users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Categories Table
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Post Categories (Many-to-Many Relationship)
CREATE TABLE IF NOT EXISTS `blog_post_categories` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `blog_post_categories_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_post_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Tags Table
CREATE TABLE IF NOT EXISTS `blog_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Post Tags (Many-to-Many Relationship)
CREATE TABLE IF NOT EXISTS `blog_post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `blog_post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (username: admin, password: admin123 - CHANGE THIS!)
-- Note: This hash might not work. Use setup-admin.php to create/update the admin user
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `full_name`, `role`, `status`) 
VALUES ('admin', 'admin@techweblabs.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'active')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Better approach: Run setup-admin.php after importing this SQL file to create the admin user with correct password hash

-- Insert default categories
INSERT INTO `blog_categories` (`name`, `slug`, `description`) VALUES
('Mobile App Development', 'mobile-app-development', 'Articles about mobile app development'),
('Web Development', 'web-development', 'Articles about web development'),
('Technology', 'technology', 'General technology articles')
ON DUPLICATE KEY UPDATE `name`=`name`;
