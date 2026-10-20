CREATE DATABASE IF NOT EXISTS bus_ticket_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bus_ticket_db;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE buses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  bus_name VARCHAR(120) NOT NULL,
  bus_type VARCHAR(40) NOT NULL DEFAULT 'AC',
  from_city VARCHAR(100) NOT NULL,
  to_city VARCHAR(100) NOT NULL,
  travel_date DATE NOT NULL,
  departure_time TIME NOT NULL,
  fare DECIMAL(10,2) NOT NULL,
  total_seats SMALLINT UNSIGNED NOT NULL,
  booked_seats SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_bus_search (from_city,to_city,travel_date,status),
  CONSTRAINT chk_fare_positive CHECK (fare > 0),
  CONSTRAINT chk_seats CHECK (total_seats > 0 AND booked_seats <= total_seats)
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_ref VARCHAR(20) NOT NULL UNIQUE,
  user_id BIGINT UNSIGNED NOT NULL,
  bus_id BIGINT UNSIGNED NOT NULL,
  seats SMALLINT UNSIGNED NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_booking_bus FOREIGN KEY (bus_id) REFERENCES buses(id),
  INDEX idx_booking_user (user_id,created_at)
) ENGINE=InnoDB;

-- Optional sample trips: update dates as needed before importing.
INSERT INTO buses (bus_name,bus_type,from_city,to_city,travel_date,departure_time,fare,total_seats,booked_seats,status)
VALUES
('GJ-01 Express','AC','Ahmedabad','Vadodara', DATE_ADD(CURDATE(), INTERVAL 2 DAY),'08:30:00',350.00,40,0,'active'),
('Saurashtra Travels','Sleeper','Ahmedabad','Rajkot', DATE_ADD(CURDATE(), INTERVAL 3 DAY),'22:00:00',650.00,36,0,'active'),
('CityLink 204','Non-AC','Vadodara','Surat', DATE_ADD(CURDATE(), INTERVAL 1 DAY),'10:15:00',280.00,45,0,'active');
