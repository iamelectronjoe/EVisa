-- Create the database
CREATE DATABASE IF NOT EXISTS visa_service;

-- Use the created database
USE visa_service;

-- Create the table for visa applications
CREATE TABLE IF NOT EXISTS visa_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullName VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    passportNumber VARCHAR(50) NOT NULL,
    passportExpiry DATE NOT NULL,
    nationality VARCHAR(100) NOT NULL,
    arrivalDate DATE NOT NULL,
    departureDate DATE NOT NULL,
    purpose TEXT NOT NULL,
    passportPhoto VARCHAR(255) NOT NULL,
    passportPage VARCHAR(255) NOT NULL,
    additionalDocs TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
