<?php
require_once '../config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Read the SQL file
    $sql = file_get_contents('users.sql');
    
    // Execute the SQL
    $conn->exec($sql);
    
    echo "Users table created successfully!";
} catch(PDOException $e) {
    echo "Error creating users table: " . $e->getMessage();
}
?> 