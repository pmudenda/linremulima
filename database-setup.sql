-- Linire Mulima & Company Website Database Setup
-- Create database and tables for contact form submissions

-- Create database
CREATE DATABASE IF NOT EXISTS linire_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE linire_website;

-- Create contact_submissions table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    service ENUM('corporate', 'commercial', 'construction', 'governance', 'regulatory', 'banking', 'property', 'employment', 'dispute', 'other') NOT NULL,
    message TEXT NOT NULL,
    consent BOOLEAN NOT NULL DEFAULT 0,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email(191)),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Create admin_users table for admin access
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, email, password_hash, full_name, role) 
VALUES ('admin', 'linire@liniremulima.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin')
ON DUPLICATE KEY UPDATE username = username;

-- Create settings table for configuration
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_email', 'linire@liniremulima.com', 'Main contact email address'),
('auto_reply_enabled', '1', 'Enable automatic reply to contact form submissions'),
('auto_reply_subject', 'Thank you for contacting Linire Mulima & Company', 'Auto-reply email subject'),
('notification_email', 'linire@liniremulima.com', 'Email to receive new submission notifications')
ON DUPLICATE KEY UPDATE setting_key = setting_key;
