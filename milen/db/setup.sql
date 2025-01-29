CREATE TABLE `user` (
    `name` VARCHAR(255) PRIMARY KEY,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `coin_collection` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `user_name` VARCHAR(255) NOT NULL,
    `access` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`user_name`) REFERENCES `user`(`name`) ON DELETE CASCADE
);

CREATE TABLE `coin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `cost` DECIMAL(10, 2) NOT NULL,
    `value` DECIMAL(10, 2) NOT NULL,
    `currency` VARCHAR(50) NOT NULL,
    `front_path` VARCHAR(255) NOT NULL,
    `back_path` VARCHAR(255) NOT NULL,
    `country` VARCHAR(100) NOT NULL,
    `year` INT NOT NULL,
    `coin_collection_id` INT NOT NULL,
    FOREIGN KEY (`coin_collection_id`) REFERENCES `coin_collection`(`id`) ON DELETE CASCADE
);

CREATE TABLE `access_control` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_name` VARCHAR(255) NOT NULL,
    `collection_id` INT NOT NULL,
    FOREIGN KEY (`user_name`) REFERENCES `user`(`name`) ON DELETE CASCADE,
    FOREIGN KEY (`collection_id`) REFERENCES `coin_collection`(`id`) ON DELETE CASCADE
);

CREATE TABLE `collection_tag` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `collection_id` INT NOT NULL,
    FOREIGN KEY (`collection_id`) REFERENCES `coin_collection`(`id`) ON DELETE CASCADE
);

CREATE TABLE `period` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `country` VARCHAR(100) NOT NULL,
    `from` YEAR NOT NULL,
    `to` YEAR NOT NULL,
    `name` VARCHAR(255) NOT NULL
);

CREATE TABLE `coin_period` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `coin_id` INT NOT NULL,
    `period_id` INT NOT NULL,
    FOREIGN KEY (`coin_id`) REFERENCES `coin`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`period_id`) REFERENCES `period`(`id`) ON DELETE CASCADE
);

CREATE TABLE `trade` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `seller_name` VARCHAR(255) NOT NULL,
    `buyer_name` VARCHAR(255) NOT NULL,
    `status` VARCHAR(255) NOT NULL,
    `coin_id` INT NOT NULL,
    FOREIGN KEY (`seller_name`) REFERENCES `user`(`name`) ON DELETE CASCADE,
    FOREIGN KEY (`buyer_name`) REFERENCES `user`(`name`) ON DELETE CASCADE,
    FOREIGN KEY (`coin_id`) REFERENCES `coin`(`id`) ON DELETE CASCADE
);
