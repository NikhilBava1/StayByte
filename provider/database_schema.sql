-- Complete Database Schema for StayByte Admin Panel

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
('student', 'Student user'),
('staff', 'Hotel staff member'),
('provider', 'Service provider');

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

-- Rooms table
CREATE TABLE IF NOT EXISTS rooms (
    room_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    room_image VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    bed_size VARCHAR(50),
    guest_capacity INT DEFAULT 2,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Room Images table
CREATE TABLE IF NOT EXISTS rooms_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    room_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- Room Amenities table
CREATE TABLE IF NOT EXISTS room_amenities (
    amenities_id INT PRIMARY KEY AUTO_INCREMENT,
    facility_name VARCHAR(100) NOT NULL,
    room_id INT DEFAULT NULL,
    KEY room_id (room_id),
    CONSTRAINT room_amenities_ibfk_1 FOREIGN KEY (room_id) REFERENCES rooms(room_id) ON DELETE CASCADE
);

-- Meals table
CREATE TABLE IF NOT EXISTS meals (
    meal_id INT PRIMARY KEY AUTO_INCREMENT,
    meal_title VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    meal_type ENUM('Veg', 'Non-Veg') DEFAULT 'Veg',
    items_included TEXT,
    meal_address TEXT,
    image_url VARCHAR(255),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    meal_provider_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Meal Images table
CREATE TABLE IF NOT EXISTS meals_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    meal_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meal_id) REFERENCES meals(meal_id) ON DELETE CASCADE
);

-- Enquiries table
CREATE TABLE IF NOT EXISTS enquiries (
    enquiry_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    category ENUM('room', 'meal', 'general') DEFAULT 'general',
    status ENUM('open', 'in-progress', 'resolved', 'closed') DEFAULT 'open',
    response TEXT,
    responded_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (responded_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    admin_id VARCHAR(20) UNIQUE NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

-- Insert sample meals
INSERT INTO meals (meal_title, description, price, rating, meal_type, items_included, meal_address, image_url) VALUES 
('Vegetarian Thali', 'Traditional Indian vegetarian meal with variety of dishes', 12.99, 4.7, 'Veg', 'Dal, Rice, Roti, Sabzi, Salad, Dessert', 'Main Dining Hall', 'uploads/meals/meal1.jpg'),
('Non-Veg Platter', 'Delicious non-vegetarian meal with chicken and mutton dishes', 15.99, 4.5, 'Non-Veg', 'Chicken Curry, Mutton Curry, Rice, Roti, Salad', 'Main Dining Hall', 'uploads/meals/meal2.jpg'),
('Continental Breakfast', 'Continental breakfast with bread, eggs, and beverages', 8.99, 4.3, 'Veg', 'Bread, Butter, Jam, Eggs, Coffee, Tea', 'Main Dining Hall', 'uploads/meals/meal3.jpg');

-- Insert sample admin
INSERT INTO admins (username, password, email, first_name, last_name, admin_id, role, phone) VALUES 
('admin1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin1@example.com', 'Admin', 'User', 'ADM001', 'super_admin', '5555555555');
