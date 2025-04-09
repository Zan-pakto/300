-- Create the database
CREATE DATABASE IF NOT EXISTS volunteer_management;
USE volunteer_management;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('admin', 'volunteer', 'donor') NOT NULL DEFAULT 'volunteer',
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Volunteers Table
CREATE TABLE IF NOT EXISTS volunteers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    skills TEXT,
    availability JSON,
    status ENUM('active', 'inactive') DEFAULT 'active',
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Events Table
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    location TEXT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    required_volunteers INT,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Event Volunteers Table
CREATE TABLE IF NOT EXISTS event_volunteers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    volunteer_id INT NOT NULL,
    status ENUM('registered', 'attended', 'no_show') DEFAULT 'registered',
    hours_worked DECIMAL(5,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE
);

-- Donations Table
CREATE TABLE IF NOT EXISTS donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    donation_type ENUM('one-time', 'recurring') DEFAULT 'one-time',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    campaign_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id),
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
);

-- Campaigns Table
CREATE TABLE IF NOT EXISTS campaigns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    goal_amount DECIMAL(10,2),
    current_amount DECIMAL(10,2) DEFAULT 0,
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Volunteer Hours Table
CREATE TABLE IF NOT EXISTS volunteer_hours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    volunteer_id INT NOT NULL,
    event_id INT,
    hours DECIMAL(5,2) NOT NULL,
    date DATE NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- Create indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_volunteers_user_id ON volunteers(user_id);
CREATE INDEX idx_events_status ON events(status);
CREATE INDEX idx_donations_donor_id ON donations(donor_id);
CREATE INDEX idx_donations_status ON donations(status);
CREATE INDEX idx_campaigns_status ON campaigns(status);
CREATE INDEX idx_volunteer_hours_volunteer_id ON volunteer_hours(volunteer_id);
CREATE INDEX idx_volunteer_hours_date ON volunteer_hours(date); 