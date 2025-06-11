-- Create the database
CREATE DATABASE IF NOT EXISTS cruzewheelz;
USE cruzewheelz;

-- Locations Table
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Insert sample locations
INSERT INTO locations (name) VALUES
('Bagalkot'), ('Ballari'), ('Belagavi'), ('Bengaluru Rural'),
('Bengaluru Urban'), ('Bidar'), ('Chikkamagaluru'), ('Davanagere'),
('Dharwad'), ('Gadag'), ('Haveri'), ('Mysuru'), ('Uttar Kannada');

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20) UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cars Table 
CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    category VARCHAR(50),
    seats INT,
    transmission VARCHAR(50),
    price DECIMAL(10,2),
    rating DECIMAL(2,1),
    featured TINYINT(1),
    image VARCHAR(255),
    description TEXT,
    features TEXT,
    specifications TEXT,
    location_1 VARCHAR(100), 
    location_2 VARCHAR(100)   
);

-- Review table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    review TEXT NOT NULL,
    stars INT NOT NULL CHECK (stars BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Car_Bookings Table with Enhanced Features
CREATE TABLE `car_bookings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `car_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `pickup_date` DATE NOT NULL,
  `return_date` DATE NOT NULL,
  `pickup_location_id` INT NOT NULL,
  `return_location_id` INT NOT NULL,
  `rental_days` INT NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` ENUM('cash', 'razorpay') DEFAULT NULL,
  `payment_status` ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
  `status` ENUM('pending', 'confirmed', 'ongoing', 'completed', 'cancelled') DEFAULT 'pending',
  `cancellation_reason` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`car_id`) REFERENCES cars(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES users(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`pickup_location_id`) REFERENCES locations(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`return_location_id`) REFERENCES locations(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_car_dates` (`car_id`, `pickup_date`, `return_date`),
  INDEX `idx_user_bookings` (`user_id`),
  INDEX `idx_booking_status` (`status`),
  CONSTRAINT `check_dates` CHECK (`return_date` >= `pickup_date`),
  CONSTRAINT `check_rental_days` CHECK (`rental_days` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin activity logs
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Insert default admin (password: admin123)
INSERT INTO admin_users (username, password, name, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'admin@cruzewheelz.com')
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    status = 'active';

-- Insert default admin (password: @SnehaPrem1)
INSERT INTO admin_users (username, password, name, email) VALUES 
('prembadiger9@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Prem Badiger', 'prembadiger9@gmail.com')
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    status = 'active';

-- Trigger to prevent double booking
DELIMITER //
CREATE TRIGGER before_booking_insert 
BEFORE INSERT ON car_bookings
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM car_bookings
        WHERE car_id = NEW.car_id
        AND status NOT IN ('completed', 'cancelled')
        AND (
            (NEW.pickup_date BETWEEN pickup_date AND return_date)
            OR (NEW.return_date BETWEEN pickup_date AND return_date)
            OR (pickup_date BETWEEN NEW.pickup_date AND NEW.return_date)
        )
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Car is already booked for these dates';
    END IF;
END//
DELIMITER ;
