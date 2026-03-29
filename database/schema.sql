CREATE DATABASE IF NOT EXISTS event_management_system;
USE event_management_system;
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fullname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'organizer', 'client') NOT NULL DEFAULT 'client',
    `is_verified` TINYINT(1) DEFAULT 0,
    `otp_code` VARCHAR(6) DEFAULT NULL,
    `otp_expires_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `organizer_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `category` VARCHAR(100),
    `status` ENUM('active', 'inactive', 'draft') NOT NULL DEFAULT 'draft',
    `image_path` VARCHAR(255) DEFAULT NULL,
    `event_date` DATETIME DEFAULT NULL,
    `venue_name` VARCHAR(255) DEFAULT NULL,
    `venue_location` VARCHAR(255) DEFAULT NULL,
    `bookings_count` INT DEFAULT 0,
    `packages` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`organizer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;