USE event_management_system;

-- 1. Create Tickets Table
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `ticket_code` VARCHAR(50) UNIQUE NOT NULL,
  `status` ENUM('active', 'used', 'cancelled') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 2. Create Feedbacks Table
CREATE TABLE IF NOT EXISTS `feedbacks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `client_id` INT NOT NULL,
    `rating` INT NOT NULL,
    `comment` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Create Feedback Replies Table
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

-- 4. Update bookings status enum to include 'completed' and ensure snapshots are LONGTEXT
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
MODIFY COLUMN `event_snapshot` LONGTEXT DEFAULT NULL,
MODIFY COLUMN `package_snapshot` LONGTEXT DEFAULT NULL;
