-- Create database to support Viet Nam Language
CREATE DATABASE IF NOT EXISTS db_note
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE db_note;

-- Create tb_users Table
CREATE TABLE IF NOT EXISTS tb_users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  lastname VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  email VARCHAR(255),
  username VARCHAR(100),
  user_pass VARCHAR(255),
  user_role VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
);

-- Inserted Data
INSERT INTO tb_users (firstname, lastname, email, username, user_pass, user_role)
VALUES 
('Luciana', 'de Montefio', 'admin@gmail.com', 'lucy', 'lucy123', 'Admin'),
('Jane', 'Doe', 'jane.doe@gmail.com', 'jane_doe', 'password123', 'User');
