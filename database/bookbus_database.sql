-- ============================================
-- BookBus - Base de données complète
-- Système de réservation de bus inter-villes
-- Inspiré de marKoub.ma
-- ============================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS bookbus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bookbus;

-- ============================================
-- SUPPRESSION DES TABLES (si elles existent)
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS seats;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS routes;
DROP TABLE IF EXISTS buses;
DROP TABLE IF EXISTS bus_companies;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- CRÉATION DES TABLES
-- ============================================

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('client', 'admin') DEFAULT 'client',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bus_companies
CREATE TABLE bus_companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo VARCHAR(255) NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: buses
CREATE TABLE buses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bus_company_id INT NOT NULL,
    registration_number VARCHAR(50) NOT NULL UNIQUE,
    model VARCHAR(100) NOT NULL,
    total_seats INT NOT NULL,
    seat_layout JSON NULL,
    amenities JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_company_id) REFERENCES bus_companies(id) ON DELETE CASCADE,
    INDEX idx_company (bus_company_id),
    INDEX idx_registration (registration_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: routes
CREATE TABLE routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    departure_city VARCHAR(100) NOT NULL,
    arrival_city VARCHAR(100) NOT NULL,
    distance_km DECIMAL(8,2) NOT NULL,
    duration_minutes INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cities (departure_city, arrival_city)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: trips
CREATE TABLE trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    bus_id INT NOT NULL,
    departure_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    available_seats INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES routes(id) ON DELETE CASCADE,
    FOREIGN KEY (bus_id) REFERENCES buses(id) ON DELETE CASCADE,
    INDEX idx_route (route_id),
    INDEX idx_bus (bus_id),
    INDEX idx_departure (departure_time),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bookings
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    trip_id INT NOT NULL,
    booking_reference VARCHAR(20) NOT NULL UNIQUE,
    passenger_name VARCHAR(255) NOT NULL,
    passenger_phone VARCHAR(20) NOT NULL,
    number_of_seats INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_trip (trip_id),
    INDEX idx_reference (booking_reference),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: payments
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'mobile_money') NOT NULL,
    transaction_id VARCHAR(100) NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id),
    INDEX idx_status (status),
    INDEX idx_transaction (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: seats
CREATE TABLE seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    seat_number VARCHAR(10) NOT NULL,
    seat_type ENUM('standard', 'vip') DEFAULT 'standard',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERTION DES DONNÉES
-- ============================================

-- ============================================
-- 1. UTILISATEURS
-- ============================================

INSERT INTO users (name, email, password, phone, role, email_verified_at) VALUES
-- Administrateurs
('Admin BookBus', 'admin@bookbus.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0612345678', 'admin', NOW()),
('Youssef El Amrani', 'youssef.admin@bookbus.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0623456789', 'admin', NOW()),

-- Clients
('Mohammed Alami', 'mohammed.alami@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0661234567', 'client', NOW()),
('Fatima Zahra Bennani', 'fatima.bennani@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0662345678', 'client', NOW()),
('Karim Idrissi', 'karim.idrissi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0663456789', 'client', NOW()),
('Amina Chakir', 'amina.chakir@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0664567890', 'client', NOW()),
('Hassan Tazi', 'hassan.tazi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0665678901', 'client', NOW()),
('Salma Berrada', 'salma.berrada@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0666789012', 'client', NOW()),
('Omar Fassi', 'omar.fassi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0667890123', 'client', NOW()),
('Nadia Lahlou', 'nadia.lahlou@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0668901234', 'client', NOW());

-- ============================================
-- 2. COMPAGNIES DE BUS (Inspirées de marKoub.ma)
-- ============================================

INSERT INTO bus_companies (name, logo, phone, email, address) VALUES
('CTM', 'ctm_logo.png', '0522541010', 'contact@ctm.ma', '23 Rue Léon l\'Africain, Casablanca'),
('Supratours', 'supratours_logo.png', '0522431313', 'info@supratours.ma', 'Gare Routière Casa Voyageurs, Casablanca'),
('SATAS', 'satas_logo.png', '0522268080', 'contact@satas.ma', 'Boulevard Mohammed V, Casablanca'),
('Pullman du Sud', 'pullman_logo.png', '0528841234', 'info@pullmandusud.ma', 'Avenue Hassan II, Agadir'),
('Ghazala', 'ghazala_logo.png', '0539941234', 'contact@ghazala.ma', 'Gare Routière, Tanger'),
('Trans Ghazala', 'transghazala_logo.png', '0535621234', 'info@transghazala.ma', 'Place Al Adarissa, Fès'),
('Nejme Chamal', 'nejme_logo.png', '0539371234', 'contact@nejmechamal.ma', 'Route de Tétouan, Tanger'),
('Stareo', 'stareo_logo.png', '0524431234', 'info@stareo.ma', 'Avenue Mohammed VI, Marrakech');

-- ============================================
-- 3. BUS
-- ============================================

INSERT INTO buses (bus_company_id, registration_number, model, total_seats, seat_layout, amenities) VALUES
-- CTM (Compagnie 1)
(1, 'A-12345-CTM', 'Mercedes-Benz Tourismo', 45, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "USB Charging", "Reclining Seats"]'),
(1, 'A-12346-CTM', 'Volvo 9700', 50, '{"rows": 13, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "USB Charging", "Entertainment"]'),
(1, 'A-12347-CTM', 'Mercedes-Benz Travego', 42, '{"rows": 11, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "USB Charging", "Premium Seats"]'),

-- Supratours (Compagnie 2)
(2, 'B-23456-SUP', 'Scania Touring', 48, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "Snack Service"]'),
(2, 'B-23457-SUP', 'Mercedes-Benz Tourismo', 45, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "USB Charging"]'),
(2, 'B-23458-SUP', 'Volvo 9900', 52, '{"rows": 13, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "Entertainment", "Premium"]'),

-- SATAS (Compagnie 3)
(3, 'C-34567-SAT', 'Irisbus Magelys', 46, '{"rows": 12, "seatsPerRow": 4}', '["AC", "WC", "Reclining Seats"]'),
(3, 'C-34568-SAT', 'Mercedes-Benz Tourismo', 45, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC"]'),

-- Pullman du Sud (Compagnie 4)
(4, 'D-45678-PDS', 'Scania Touring', 48, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "USB Charging"]'),
(4, 'D-45679-PDS', 'Volvo 9700', 50, '{"rows": 13, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "Entertainment"]'),

-- Ghazala (Compagnie 5)
(5, 'E-56789-GHZ', 'Mercedes-Benz Travego', 42, '{"rows": 11, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "Premium"]'),
(5, 'E-56790-GHZ', 'Scania Touring', 48, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC"]'),

-- Trans Ghazala (Compagnie 6)
(6, 'F-67890-TGZ', 'Volvo 9700', 50, '{"rows": 13, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "USB Charging"]'),

-- Nejme Chamal (Compagnie 7)
(7, 'G-78901-NJM', 'Mercedes-Benz Tourismo', 45, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC"]'),

-- Stareo (Compagnie 8)
(8, 'H-89012-STR', 'Scania Touring', 48, '{"rows": 12, "seatsPerRow": 4}', '["WiFi", "AC", "WC", "Entertainment"]');

-- ============================================
-- 4. TRAJETS (Routes principales du Maroc)
-- ============================================

INSERT INTO routes (departure_city, arrival_city, distance_km, duration_minutes) VALUES
-- Casablanca - Autres villes
('Casablanca', 'Rabat', 87.5, 75),
('Casablanca', 'Marrakech', 241.0, 180),
('Casablanca', 'Fès', 298.0, 240),
('Casablanca', 'Tanger', 338.0, 300),
('Casablanca', 'Agadir', 508.0, 420),
('Casablanca', 'Essaouira', 372.0, 300),

-- Rabat - Autres villes
('Rabat', 'Casablanca', 87.5, 75),
('Rabat', 'Fès', 213.0, 180),
('Rabat', 'Tanger', 251.0, 210),
('Rabat', 'Marrakech', 325.0, 240),

-- Marrakech - Autres villes
('Marrakech', 'Casablanca', 241.0, 180),
('Marrakech', 'Agadir', 267.0, 210),
('Marrakech', 'Essaouira', 178.0, 150),
('Marrakech', 'Ouarzazate', 204.0, 180),

-- Fès - Autres villes
('Fès', 'Casablanca', 298.0, 240),
('Fès', 'Rabat', 213.0, 180),
('Fès', 'Tanger', 285.0, 240),
('Fès', 'Meknès', 60.0, 45),

-- Tanger - Autres villes
('Tanger', 'Casablanca', 338.0, 300),
('Tanger', 'Rabat', 251.0, 210),
('Tanger', 'Fès', 285.0, 240),
('Tanger', 'Tétouan', 60.0, 60),

-- Agadir - Autres villes
('Agadir', 'Casablanca', 508.0, 420),
('Agadir', 'Marrakech', 267.0, 210),
('Agadir', 'Essaouira', 178.0, 150),

-- Autres trajets
('Meknès', 'Fès', 60.0, 45),
('Tétouan', 'Tanger', 60.0, 60),
('Essaouira', 'Marrakech', 178.0, 150),
('Ouarzazate', 'Marrakech', 204.0, 180);

-- ============================================
-- 5. VOYAGES (Trips avec tarifs réalistes)
-- ============================================

-- Voyages pour les 7 prochains jours
INSERT INTO trips (route_id, bus_id, departure_time, arrival_time, price, status, available_seats) VALUES

-- JOUR 1 (Aujourd'hui + 1 jour)
-- Casablanca → Rabat
(1, 1, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 6 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 7 HOUR + INTERVAL 15 MINUTE, 45.00, 'scheduled', 45),
(1, 2, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 9 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 10 HOUR + INTERVAL 15 MINUTE, 45.00, 'scheduled', 50),
(1, 3, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 14 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 15 HOUR + INTERVAL 15 MINUTE, 50.00, 'scheduled', 42),

-- Casablanca → Marrakech
(2, 1, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 10 HOUR, 80.00, 'scheduled', 45),
(2, 4, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 10 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 13 HOUR, 75.00, 'scheduled', 48),
(2, 5, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 15 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 18 HOUR, 85.00, 'scheduled', 45),

-- Casablanca → Fès
(3, 2, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 12 HOUR, 100.00, 'scheduled', 50),
(3, 6, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 16 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 20 HOUR, 95.00, 'scheduled', 52),

-- Casablanca → Tanger
(4, 3, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 7 HOUR + INTERVAL 30 MINUTE, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 12 HOUR + INTERVAL 30 MINUTE, 120.00, 'scheduled', 42),
(4, 11, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 22 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 3 HOUR, 110.00, 'scheduled', 42),

-- Casablanca → Agadir
(5, 9, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 15 HOUR, 150.00, 'scheduled', 48),
(5, 10, DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 21 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 4 HOUR, 140.00, 'scheduled', 50),

-- JOUR 2
-- Rabat → Casablanca
(7, 1, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 8 HOUR + INTERVAL 15 MINUTE, 45.00, 'scheduled', 45),
(7, 4, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 14 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 15 HOUR + INTERVAL 15 MINUTE, 45.00, 'scheduled', 48),

-- Marrakech → Casablanca
(11, 5, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 11 HOUR, 80.00, 'scheduled', 45),
(11, 14, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 16 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 19 HOUR, 75.00, 'scheduled', 48),

-- Marrakech → Agadir
(12, 9, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 9 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 12 HOUR + INTERVAL 30 MINUTE, 90.00, 'scheduled', 48),

-- Fès → Casablanca
(15, 2, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 11 HOUR, 100.00, 'scheduled', 50),
(15, 12, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 15 HOUR, DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 19 HOUR, 95.00, 'scheduled', 50),

-- JOUR 3
-- Tanger → Casablanca
(19, 11, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 13 HOUR, 120.00, 'scheduled', 42),
(19, 13, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 22 HOUR, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 3 HOUR, 110.00, 'scheduled', 45),

-- Agadir → Casablanca
(23, 9, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 14 HOUR, 150.00, 'scheduled', 48),

-- Casablanca → Essaouira
(6, 7, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 9 HOUR, DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 14 HOUR, 110.00, 'scheduled', 46),

-- JOUR 4
-- Rabat → Fès
(8, 12, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 11 HOUR, 85.00, 'scheduled', 50),

-- Rabat → Tanger
(9, 11, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 9 HOUR, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 12 HOUR + INTERVAL 30 MINUTE, 95.00, 'scheduled', 42),

-- Marrakech → Essaouira
(13, 14, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 10 HOUR, DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 12 HOUR + INTERVAL 30 MINUTE, 70.00, 'scheduled', 48),

-- JOUR 5
-- Fès → Rabat
(16, 2, DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 10 HOUR, 85.00, 'scheduled', 50),

-- Fès → Tanger
(17, 11, DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 14 HOUR, DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 18 HOUR, 100.00, 'scheduled', 42),

-- Tanger → Rabat
(20, 13, DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 5 DAY) + INTERVAL 11 HOUR + INTERVAL 30 MINUTE, 95.00, 'scheduled', 45),

-- JOUR 6
-- Agadir → Marrakech
(24, 10, DATE_ADD(NOW(), INTERVAL 6 DAY) + INTERVAL 9 HOUR, DATE_ADD(NOW(), INTERVAL 6 DAY) + INTERVAL 12 HOUR + INTERVAL 30 MINUTE, 90.00, 'scheduled', 50),

-- Essaouira → Marrakech
(28, 7, DATE_ADD(NOW(), INTERVAL 6 DAY) + INTERVAL 10 HOUR, DATE_ADD(NOW(), INTERVAL 6 DAY) + INTERVAL 12 HOUR + INTERVAL 30 MINUTE, 70.00, 'scheduled', 46),

-- JOUR 7
-- Casablanca → Marrakech
(2, 1, DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 7 HOUR, DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 10 HOUR, 80.00, 'scheduled', 45),
(2, 14, DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 15 HOUR, DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 18 HOUR, 85.00, 'scheduled', 48),

-- Casablanca → Fès
(3, 2, DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 8 HOUR, DATE_ADD(NOW(), INTERVAL 7 DAY) + INTERVAL 12 HOUR, 100.00, 'scheduled', 50);

-- ============================================
-- 6. RÉSERVATIONS (Exemples)
-- ============================================

INSERT INTO bookings (user_id, trip_id, booking_reference, passenger_name, passenger_phone, number_of_seats, total_price, status) VALUES
(3, 1, 'BB-2026-001', 'Mohammed Alami', '0661234567', 1, 45.00, 'confirmed'),
(4, 4, 'BB-2026-002', 'Fatima Zahra Bennani', '0662345678', 2, 160.00, 'confirmed'),
(5, 7, 'BB-2026-003', 'Karim Idrissi', '0663456789', 1, 100.00, 'confirmed'),
(6, 2, 'BB-2026-004', 'Amina Chakir', '0664567890', 1, 80.00, 'pending'),
(7, 9, 'BB-2026-005', 'Hassan Tazi', '0665678901', 3, 360.00, 'confirmed'),
(8, 5, 'BB-2026-006', 'Salma Berrada', '0666789012', 1, 75.00, 'confirmed'),
(9, 11, 'BB-2026-007', 'Omar Fassi', '0667890123', 2, 180.00, 'confirmed'),
(10, 6, 'BB-2026-008', 'Nadia Lahlou', '0668901234', 1, 85.00, 'pending');

-- ============================================
-- 7. PAIEMENTS
-- ============================================

INSERT INTO payments (booking_id, amount, payment_method, transaction_id, status, paid_at) VALUES
(1, 45.00, 'card', 'TXN-2026-001-CARD', 'completed', NOW()),
(2, 160.00, 'card', 'TXN-2026-002-CARD', 'completed', NOW()),
(3, 100.00, 'mobile_money', 'TXN-2026-003-MM', 'completed', NOW()),
(4, 80.00, 'cash', NULL, 'pending', NULL),
(5, 360.00, 'card', 'TXN-2026-005-CARD', 'completed', NOW()),
(6, 75.00, 'card', 'TXN-2026-006-CARD', 'completed', NOW()),
(7, 180.00, 'mobile_money', 'TXN-2026-007-MM', 'completed', NOW()),
(8, 85.00, 'cash', NULL, 'pending', NULL);

-- ============================================
-- 8. SIÈGES RÉSERVÉS
-- ============================================

INSERT INTO seats (booking_id, seat_number, seat_type) VALUES
-- Réservation 1 (1 siège)
(1, 'A12', 'standard'),

-- Réservation 2 (2 sièges)
(2, 'B5', 'standard'),
(2, 'B6', 'standard'),

-- Réservation 3 (1 siège)
(3, 'C8', 'standard'),

-- Réservation 4 (1 siège)
(4, 'A3', 'standard'),

-- Réservation 5 (3 sièges)
(5, 'D1', 'vip'),
(5, 'D2', 'vip'),
(5, 'D3', 'vip'),

-- Réservation 6 (1 siège)
(6, 'B10', 'standard'),

-- Réservation 7 (2 sièges)
(7, 'C15', 'standard'),
(7, 'C16', 'standard'),

-- Réservation 8 (1 siège)
(8, 'A7', 'standard');

-- ============================================
-- MISE À JOUR DES SIÈGES DISPONIBLES
-- ============================================

UPDATE trips SET available_seats = available_seats - 1 WHERE id = 1;
UPDATE trips SET available_seats = available_seats - 2 WHERE id = 4;
UPDATE trips SET available_seats = available_seats - 1 WHERE id = 7;
UPDATE trips SET available_seats = available_seats - 1 WHERE id = 2;
UPDATE trips SET available_seats = available_seats - 3 WHERE id = 9;
UPDATE trips SET available_seats = available_seats - 1 WHERE id = 5;
UPDATE trips SET available_seats = available_seats - 2 WHERE id = 11;
UPDATE trips SET available_seats = available_seats - 1 WHERE id = 6;

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue: Voyages disponibles avec détails complets
CREATE OR REPLACE VIEW available_trips_view AS
SELECT 
    t.id AS trip_id,
    r.departure_city,
    r.arrival_city,
    r.distance_km,
    r.duration_minutes,
    t.departure_time,
    t.arrival_time,
    t.price,
    t.available_seats,
    b.registration_number AS bus_number,
    b.model AS bus_model,
    b.total_seats,
    bc.name AS company_name,
    bc.logo AS company_logo,
    t.status
FROM trips t
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
WHERE t.status = 'scheduled' AND t.available_seats > 0
ORDER BY t.departure_time;

-- Vue: Réservations avec détails
CREATE OR REPLACE VIEW bookings_details_view AS
SELECT 
    bk.id AS booking_id,
    bk.booking_reference,
    u.name AS user_name,
    u.email AS user_email,
    bk.passenger_name,
    bk.passenger_phone,
    r.departure_city,
    r.arrival_city,
    t.departure_time,
    t.arrival_time,
    bc.name AS company_name,
    bk.number_of_seats,
    bk.total_price,
    bk.status AS booking_status,
    p.payment_method,
    p.status AS payment_status,
    p.paid_at
FROM bookings bk
JOIN users u ON bk.user_id = u.id
JOIN trips t ON bk.trip_id = t.id
JOIN routes r ON t.route_id = r.id
JOIN buses b ON t.bus_id = b.id
JOIN bus_companies bc ON b.bus_company_id = bc.id
LEFT JOIN payments p ON bk.id = p.booking_id
ORDER BY bk.created_at DESC;

-- ============================================
-- REQUÊTES UTILES POUR TESTER
-- ============================================

-- Afficher tous les voyages disponibles
-- SELECT * FROM available_trips_view;

-- Rechercher des voyages Casablanca → Marrakech
-- SELECT * FROM available_trips_view 
-- WHERE departure_city = 'Casablanca' AND arrival_city = 'Marrakech';

-- Afficher toutes les réservations
-- SELECT * FROM bookings_details_view;

-- Statistiques par compagnie
-- SELECT bc.name, COUNT(t.id) AS total_trips, SUM(b.total_seats) AS total_capacity
-- FROM bus_companies bc
-- JOIN buses b ON bc.id = b.bus_company_id
-- JOIN trips t ON b.id = t.bus_id
-- GROUP BY bc.name;

-- ============================================
-- FIN DU SCRIPT
-- ============================================

SELECT 'Base de données BookBus créée avec succès!' AS Message;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_companies FROM bus_companies;
SELECT COUNT(*) AS total_buses FROM buses;
SELECT COUNT(*) AS total_routes FROM routes;
SELECT COUNT(*) AS total_trips FROM trips;
SELECT COUNT(*) AS total_bookings FROM bookings;
