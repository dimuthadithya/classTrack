-- Database Name: class_track

CREATE DATABASE IF NOT EXISTS class_track;
USE class_track;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses (Now Represents "Grades" or "Classes" e.g. Grade 11 Science)
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    thumbnail VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Course Months (NEW: Represents a specific month for a grade)
CREATE TABLE IF NOT EXISTS course_months (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(50) NOT NULL, -- e.g. "January"
    year INT NOT NULL, -- e.g. 2026
    live_link VARCHAR(255),
    fee DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Enrollments (Updated: Links to course_months)
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    month_id INT NOT NULL, -- Changed from course_id to month_id
    payment_status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (month_id) REFERENCES course_months(id) ON DELETE CASCADE
);

-- Resources (Updated: Links to course_months)
CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    month_id INT NOT NULL, -- Changed from course_id to month_id
    title VARCHAR(100) NOT NULL,
    type ENUM('recording', 'document') NOT NULL,
    url VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (month_id) REFERENCES course_months(id) ON DELETE CASCADE
);
