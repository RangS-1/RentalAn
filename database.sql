-- Database: rental_elektronik
-- Jalankan file ini di phpMyAdmin atau MySQL CLI

CREATE DATABASE IF NOT EXISTS rentalan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rentalan;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel items (barang)
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price_per_day DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel rentals
CREATE TABLE IF NOT EXISTS rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_id INT NOT NULL,
    borrower_name VARCHAR(100) NOT NULL,
    borrow_location VARCHAR(255) NOT NULL,
    duration_days INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    status ENUM('pending_borrow','active','pending_return','returned','rejected') DEFAULT 'pending_borrow',
    borrow_confirmed_at DATETIME NULL,
    return_deadline DATETIME NULL,
    return_admin_id INT NULL,
    money_paid DECIMAL(12,2) NULL,
    money_change DECIMAL(12,2) NULL,
    return_location VARCHAR(255) NULL,
    return_requested_at DATETIME NULL,
    return_confirmed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (return_admin_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin (username: admin, password: admin123)
INSERT INTO users (username, password, full_name, role)
VALUES ('admin', '$2b$10$wz3j8yjNYLlF4BsrQuNUeeoNfjoJNWeOPYitjRPvwzUKDInu03Bgu', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Catatan: hash di atas adalah bcrypt dari 'admin123'.
-- Jika login gagal, jalankan: UPDATE users SET password='<hash baru>' WHERE username='admin';
-- atau register dengan username 'admin' (sistem akan otomatis set role admin).

-- Sample items
INSERT INTO items (name, description, price_per_day, stock, image) VALUES
('Kamera DSLR Canon EOS 90D', 'Kamera DSLR profesional 32.5MP, ideal untuk fotografi dan videografi.', 150000, 3, 'assets/images/sample-camera.jpg'),
('Drone DJI Mavic Air 2', 'Drone 4K dengan jangkauan 10km dan battery life 34 menit.', 250000, 2, 'assets/images/sample-drone.jpg'),
('Laptop Gaming ASUS ROG', 'Laptop gaming Ryzen 7, RTX 3060, 16GB RAM.', 200000, 4, 'assets/images/sample-laptop.jpg'),
('Speaker JBL PartyBox 310', 'Speaker bluetooth 240W untuk acara outdoor.', 175000, 2, 'assets/images/sample-speaker.jpg'),
('Proyektor Epson EB-X06', 'Proyektor XGA 3600 lumens untuk presentasi & nonton.', 120000, 5, 'assets/images/sample-projector.jpg');
