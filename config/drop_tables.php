<?php
require_once 'database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Disable foreign key checks temporarily
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Drop tables in correct order (child tables first)
    $tables = [
        'event_registrations', // References both users and events
        'donations',          // References users
        'events',            // No foreign keys
        'users'              // Parent table
    ];
    
    foreach ($tables as $table) {
        $conn->exec("DROP TABLE IF EXISTS `$table`");
        echo "Dropped table: $table<br>";
    }
    
    // Re-enable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<div style='color: green; padding: 20px; border: 1px solid green; margin: 20px;'>
            <h2>All tables dropped successfully!</h2>
            <p>You can now run database.sql to create fresh tables.</p>
          </div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 20px; border: 1px solid red; margin: 20px;'>
            <h2>Error dropping tables!</h2>
            <p>Error: " . $e->getMessage() . "</p>
          </div>";
}
?> 