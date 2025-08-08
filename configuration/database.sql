CREATE DATABASE IF NOT EXISTS fitzone1;
USE fitzone1;

CREATE TABLE IF NOT EXISTS sign_up (
 
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bmi VARCHAR(10),
    role ENUM('customer', 'staff', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL
);


CREATE TABLE IF NOT EXISTS add_member (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    membership_type ENUM('basic', 'standard', 'premium') NOT NULL,
    notes TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS add_class (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_type ENUM('yoga', 'cardio', 'strength', 'hiit', 'pilates') NOT NULL,
    trainer_id INT,
    class_date DATE NOT NULL,
    class_time TIME NOT NULL,
    duration INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL
);



CREATE TABLE IF NOT EXISTS instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    specialty VARCHAR(100),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO trainers (name, email, specialty) VALUES
('Anuja Rajakaruna', 'anuja@fitzone.com', 'Yoga'),
('Maneesha Perera', 'maneesha@fitzone.com', 'Cardio'),
('Nethmi Perera', 'nethmi@fitzone.com', 'Strength Training'),
('Yuvindu Mihijaya', 'yuvindu@fitzone.com', 'HIIT'),
('Helindu Vitiyala', 'helindu@fitzone.com', 'Pilates');
