-- Create database
CREATE DATABASE IF NOT EXISTS faucet_db;
USE faucet_db;

-- Create claims table
CREATE TABLE IF NOT EXISTS claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    amount DECIMAL(20,8) NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_address (address),
    INDEX idx_ip (ip_address),
    INDEX idx_claimed_at (claimed_at)
);

-- Create faucet settings table
CREATE TABLE IF NOT EXISTS faucet_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT IGNORE INTO faucet_settings (setting_key, setting_value) VALUES
('total_claims', '0'),
('total_paid', '0'),
('last_payout', NOW());

-- Create admin users table (optional)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create payout log table
CREATE TABLE IF NOT EXISTS payout_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    amount DECIMAL(20,8) NOT NULL,
    transaction_id VARCHAR(255),
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
