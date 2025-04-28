CREATE DATABASE IF NOT EXISTS db_note;
USE db_note;

-- Create tb_users Table
CREATE TABLE IF NOT EXISTS tb_users (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `firstname` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_pass` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `activated` BIT(1) DEFAULT b'0',
  `email_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Inserted Data
INSERT INTO tb_users (firstname, lastname, email, username, user_pass, activated, email_token)
VALUES 
('Luciana', 'de Montefio', 'admin@gmail.com', 'lucy', '$2b$12$iGO.OUUL7q.Sm9QCcjGN2.xB5Xr9tIwCROV9mE4ZKLEMcBLPN9OdC', b'1',
 'b59f7decc4baf1a0f57e531cf6d5f4d778dc6ea0ef5a8a3db8043d45208e7db9'),
('Jane', 'Doe', 'jane.doe@gmail.com', 'jane_doe', '$2b$12$3WGl/7lUbdvbTXZT1.8BHevq4xXtEwkSEXsTq6lmO0uyAZQvpqOJm', b'1',
 'b2b8fbe249a4d25ed2c21ec7f7f6e21ed2d60a7f576c4d5199d84dd80b6e76b1');

