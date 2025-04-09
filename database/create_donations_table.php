<?php
require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Read the SQL file
    $sql = file_get_contents('donations.sql');
    
    // Execute the SQL
    $conn->exec($sql);
    
    echo "Donations table created successfully!";
} catch(PDOException $e) {
    echo "Error creating donations table: " . $e->getMessage();
}
?> 