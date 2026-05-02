CREATE DATABASE IF NOT EXISTS bridgeconnect;
USE bridgeconnect;

DROP TABLE IF EXISTS saved_resources;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    role ENUM('person_in_need', 'volunteer', 'shelter_provider', 'admin') NOT NULL
);

CREATE TABLE resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    type ENUM('shelter', 'food', 'medical', 'hygiene', 'job_support', 'other') NOT NULL,
    description TEXT,
    address VARCHAR(255),
    phone VARCHAR(50),
    available_beds INT DEFAULT 0,
    updated_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE saved_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    resource_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Demo User', 'user@test.com', '1234', 'person_in_need'),
('Shelter Provider', 'provider@test.com', '1234', 'shelter_provider'),
('Volunteer Worker', 'volunteer@test.com', '1234', 'volunteer'),
('Admin User', 'admin@test.com', '1234', 'admin');

INSERT INTO resources (name, type, description, address, phone, available_beds) VALUES
('Hope Shelter', 'shelter', 'Emergency overnight shelter with meals and case management support.', '123 Main St, Baltimore, MD', '555-111-2222', 12),
('Safe Night Housing', 'shelter', 'Temporary shelter for adults with evening intake.', '200 Shelter Ave, Baltimore, MD', '555-222-3333', 5),
('Community Food Pantry', 'food', 'Free groceries and hot meals available Monday through Friday.', '456 Oak Ave, Baltimore, MD', '555-333-4444', 0),
('Fresh Start Food Drive', 'food', 'Weekly food drive with meals, water, and hygiene kits.', '800 North Ave, Baltimore, MD', '555-444-5555', 0),
('Care Clinic', 'medical', 'Basic health services, checkups, and referrals.', '789 Pine Rd, Baltimore, MD', '555-555-6666', 0),
('Clean Hands Hygiene Center', 'hygiene', 'Showers, hygiene kits, and laundry support.', '300 Green St, Baltimore, MD', '555-777-8888', 0),
('Pathway Job Support', 'job_support', 'Resume help, job search assistance, and interview preparation.', '900 Career Blvd, Baltimore, MD', '555-999-1010', 0);
