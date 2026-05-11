USE event_management_system;

CREATE TABLE IF NOT EXISTS `custom_event_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `group_event_id` INT NOT NULL,
    `client_id` INT NOT NULL,
    `organizer_id` INT NOT NULL,
    `base_package_tier` VARCHAR(50) NOT NULL,
    `custom_packages` JSON DEFAULT NULL,
    `proposed_price` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'negotiating', 'approved', 'rejected', 'booked') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`group_event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`client_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`organizer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `request_id` INT DEFAULT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`request_id`) REFERENCES `custom_event_requests`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
