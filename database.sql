-- ==========================================
-- DENTRACARE DENTAL MANAGEMENT SYSTEM
-- DATABASE SCHEMA
-- ==========================================

-- Create database
CREATE DATABASE IF NOT EXISTS dentracare;
USE dentracare;

-- ==========================================
-- PATIENTS TABLE
-- ==========================================
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    date_of_birth DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- DENTISTS TABLE
-- ==========================================
CREATE TABLE dentists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    specialization VARCHAR(100),
    license_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- ADMINS TABLE
-- ==========================================
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- APPOINTMENTS TABLE
-- ==========================================
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    dentist_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    status ENUM('Pending', 'Confirmed', 'Cancelled', 'Completed') DEFAULT 'Pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (dentist_id) REFERENCES dentists(id) ON DELETE CASCADE
);

-- ==========================================
-- MEDICAL HISTORY TABLE
-- ==========================================
CREATE TABLE medical_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    details TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- ==========================================
-- DIAGNOSES TABLE
-- ==========================================
CREATE TABLE diagnoses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    dentist_id INT NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment_plan TEXT,
    prescription TEXT,
    diagnosis_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (dentist_id) REFERENCES dentists(id) ON DELETE CASCADE
);

-- ==========================================
-- ACTIVITY LOGS TABLE
-- ==========================================
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    user_type ENUM('patient', 'dentist', 'admin') NOT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- INSERT SAMPLE DATA
-- ==========================================

-- Insert sample admin
INSERT INTO admins (name, email, password) VALUES 
('System Administrator', 'admin@dentracare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample dentists
INSERT INTO dentists (name, email, password, specialization, license_number) VALUES 
('Dr. Mark Macharia', 'markmacharia48@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'General Dentistry', 'DENT001'),
('Dr. Sarah Johnson', 'sarah.johnson@dentracare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Orthodontics', 'DENT002');

-- Insert sample patients
INSERT INTO patients (name, email, password, phone) VALUES 
('John Doe', 'john.doe@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+254700000001'),
('Jane Smith', 'jane.smith@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+254700000002');

-- Insert sample appointments
INSERT INTO appointments (patient_id, dentist_id, appointment_date, appointment_time, reason, status) VALUES 
(1, 1, '2024-01-15', '10:00:00', 'Regular checkup', 'Confirmed'),
(2, 1, '2024-01-16', '14:00:00', 'Dental cleaning', 'Pending');

-- Insert sample medical history
INSERT INTO medical_history (patient_id, details) VALUES 
(1, 'Patient has no known allergies. Previous treatments include regular cleanings and one filling in 2023.'),
(2, 'Patient has mild sensitivity to cold. No major dental procedures in the past.');

-- Insert sample diagnoses
INSERT INTO diagnoses (patient_id, dentist_id, diagnosis, treatment_plan, diagnosis_date) VALUES 
(1, 1, 'Healthy teeth and gums', 'Continue regular brushing and flossing. Schedule next checkup in 6 months.', '2024-01-15'),
(2, 1, 'Mild gingivitis', 'Professional cleaning recommended. Improved oral hygiene routine advised.', '2024-01-16');

-- Insert sample activity logs
INSERT INTO activity_logs (user_id, user_type, action, description) VALUES 
(1, 'admin', 'LOGIN', 'System administrator logged in'),
(1, 'dentist', 'APPOINTMENT_CREATED', 'New appointment scheduled for John Doe'),
(1, 'patient', 'PROFILE_UPDATED', 'Patient updated their profile information'); 