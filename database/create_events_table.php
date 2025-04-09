<?php
require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Read the SQL file
    $sql = file_get_contents('events.sql');
    
    // Execute the SQL
    $conn->exec($sql);
    
    echo "Events table created successfully!";
} catch(PDOException $e) {
    echo "Error creating events table: " . $e->getMessage();
}
?> 