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
  `otp_code` VARCHAR(10) COLLATE utf8_unicode_ci DEFAULT NULL, 
  `otp_expiry` VARCHAR(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_avatar` VARCHAR(100) DEFAULT 'default.webp',
  `email_token` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create tb_notes Table
CREATE TABLE IF NOT EXISTS tb_notes (
  `note_id` INT AUTO_INCREMENT PRIMARY KEY,
  `note_title` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `note_desc` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
  `note_date` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` INT,
  `label_id` INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create tb_labels Table
CREATE TABLE IF NOT EXISTS tb_labels (
  `label_id` INT AUTO_INCREMENT PRIMARY KEY,
  `label_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create tb_preferences Table
CREATE TABLE IF NOT EXISTS tb_preferences (
  `user_id` INT PRIMARY KEY,
  `view` VARCHAR(100) COLLATE utf8_unicode_ci DEFAULT 'grid',
  `theme` VARCHAR(100) COLLATE utf8_unicode_ci DEFAULT 'light'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Inserted Data
INSERT INTO tb_users (firstname, lastname, email, username, user_pass, activated, otp_code, otp_expiry, email_token)
VALUES 
('Luciana', 'de Montefio', 'admin@gmail.com', 'lucy', '$2b$12$iGO.OUUL7q.Sm9QCcjGN2.xB5Xr9tIwCROV9mE4ZKLEMcBLPN9OdC', b'1',
 '123456', '2025-05-09 15:00:00', 'tok_admin_abc123'),
('Jane', 'Doe', 'jane.doe@gmail.com', 'jane_doe', '$2b$12$3WGl/7lUbdvbTXZT1.8BHevq4xXtEwkSEXsTq6lmO0uyAZQvpqOJm', b'1',
 '654321', '2025-05-09 15:00:00', 'tok_jane_xyz789');

 INSERT INTO tb_labels (label_name) VALUES
('Work'),
('Study'),  
('Business'),
('Personal');

INSERT INTO tb_notes (note_title, note_desc, note_date, user_id, label_id) VALUES
('Daily Missions', 'Complete all daily missions to earn Trailblaze EXP and credits.', 'April 25, 2025', 1, 1),
('Forgotten Hall', 'Clear stages 6-10 for rewards and Stellar Jade.', 'April 26, 2025', 1, 2),
('Light Cone Farming', 'Farm “Cavern of Corrosion: Path of Drifting” for Bronya.', 'April 24, 2025', 2, 3),
('Planar Ornaments', 'Use Simulated Universe to collect Planar Ornaments set for Seele.', 'April 23, 2025', 2, 4),
('Character Ascension', 'Ascend Kafka to phase 4 - need Lightning Crown and EXP materials.', 'April 27, 2025', 1, 1),
('Trailblaze Power Use', 'Spend 180 Trailblaze Power efficiently today.', 'April 26, 2025', 2, 2);

INSERT INTO tb_preferences (user_id, view, theme) VALUES
(1, 'grid', 'light'),
(2, 'list', 'light');


