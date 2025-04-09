CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('Monetary', 'In-Kind', 'Equipment', 'Other') NOT NULL,
    description TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
); 