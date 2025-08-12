-- Database setup for StayByte Hotel Management System

-- Users table for login
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User roles table
CREATE TABLE IF NOT EXISTS user_roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    role_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default roles
INSERT INTO user_roles (role_name, role_description) VALUES 
('admin', 'Administrator with full access'),
('student', 'Student user with booking access'),
('staff', 'Hotel staff member'),
('provider', 'Service provider with booking management');

-- Students table (extends users)
CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    student_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Rooms table (if not exists)
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    room_image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    bed_size VARCHAR(50),
    guest_capacity INT DEFAULT 2,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    room_id INT,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- Insert sample data for testing
INSERT INTO users (username, email, password, role_id) VALUES 
('admin', 'admin@staybyte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1), -- password: password
('student1', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2), -- password: password
('staff1', 'staff1@staybyte.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3); -- password: password

INSERT INTO students (user_id, name, phone, student_number) VALUES 
(2, 'John Doe', '+1234567890', 'STU001');

-- Insert sample rooms
INSERT INTO rooms (title, description, room_image, price, rating, bed_size, guest_capacity) VALUES 
('Deluxe Room', 'Spacious room with city view', 'uploads/rooms/room1.jpg', 150.00, 4.5, 'King Size', 2),
('Standard Room', 'Comfortable room for single occupancy', 'uploads/rooms/room2.jpg', 100.00, 4.2, 'Queen Size', 1),
('Suite', 'Luxury suite with separate living area', 'uploads/rooms/room3.jpg', 250.00, 4.8, 'King Size', 4);

-- Insert sample booking
INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date, total_amount, status) VALUES 
(2, 1, '2024-01-15', '2024-01-20', 750.00, 'active'); 