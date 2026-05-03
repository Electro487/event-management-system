CREATE DATABASE IF NOT EXISTS event_management_system;
USE event_management_system;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fullname` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'organizer', 'client') NOT NULL DEFAULT 'client',
    `is_verified` TINYINT(1) DEFAULT 0,
    `is_blocked` TINYINT(1) DEFAULT 0,
    `otp_code` VARCHAR(6) DEFAULT NULL,
    `otp_expires_at` DATETIME DEFAULT NULL,
    `profile_picture` VARCHAR(255) DEFAULT NULL,
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

CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT NOT NULL,
    `event_snapshot` LONGTEXT DEFAULT NULL,
    `client_id` INT NOT NULL,
    `package_tier` VARCHAR(50) NOT NULL,
    `package_snapshot` LONGTEXT DEFAULT NULL,
    `event_date` DATE NOT NULL,
    `guest_count` INT NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `checkin_time` VARCHAR(20) DEFAULT '10:00 AM',
    `total_amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `payment_status` ENUM('unpaid', 'partially_paid', 'paid') NOT NULL DEFAULT 'unpaid',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NOT NULL,
    `client_id` INT NOT NULL,
    `transaction_id` VARCHAR(100) UNIQUE NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'card',
    `status` ENUM('succeeded', 'failed') NOT NULL DEFAULT 'succeeded',
    `stripe_session_id` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) DEFAULT 'info',
  `related_id` INT DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `ticket_code` VARCHAR(50) UNIQUE NOT NULL,
  `status` ENUM('active', 'used', 'cancelled') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `feedbacks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `client_id` INT NOT NULL,
    `rating` INT NOT NULL,
    `comment` TEXT NOT NULL,
    `reply` TEXT DEFAULT NULL,
    `replied_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `feedback_replies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `feedback_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `reply_text` TEXT NOT NULL,
    `parent_reply_id` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`feedback_id`) REFERENCES `feedbacks`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_reply_id`) REFERENCES `feedback_replies`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
