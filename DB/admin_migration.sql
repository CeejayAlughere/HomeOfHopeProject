-- Admin Dashboard Migration Script
-- Run these queries to set up the admin system

-- Create admin_users table
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add columns to donations table
ALTER TABLE `donations` ADD COLUMN `status` varchar(20) NOT NULL DEFAULT 'pending' AFTER `amount`;
ALTER TABLE `donations` ADD COLUMN `notes` text AFTER `status`;

-- Add columns to volunteers table
ALTER TABLE `volunteers` ADD COLUMN `status` varchar(20) NOT NULL DEFAULT 'new' AFTER `reason`;
ALTER TABLE `volunteers` ADD COLUMN `notes` text AFTER `status`;

-- Add columns to contact_messages table
ALTER TABLE `contact_messages` ADD COLUMN `notes` text AFTER `message`;
ALTER TABLE `contact_messages` ADD COLUMN `response_sent_at` datetime AFTER `notes`;

-- Insert default admin user (username: admin, password: admin@123)
-- Note: Run this PHP snippet to generate the hash:
-- echo password_hash('admin@123', PASSWORD_DEFAULT);
-- Then manually insert with the generated hash, or use the SQL below with a pre-generated hash:

INSERT INTO `admin_users` (`username`, `password_hash`, `created_at`) VALUES 
('admin', '$2y$10$N7Tr2yx/sSZj1gLzA5lWzuz0JpqJ6Jq6sCxJvZ7qZ1HzK8J9mJ8fq', NOW());

-- This hash is for password: admin@123
