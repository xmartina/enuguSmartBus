<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing database connection...<br>";

try {
    require_once __DIR__ . '/config/database.php';

    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ Database connection successful!<br>";
        
        // Test if tables exist
        $stmt = $db->query("SHOW TABLES LIKE 'site_settings'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Site settings table exists!<br>";
        } else {
            echo "❌ Site settings table missing!<br>";
        }
    } else {
        echo "❌ Database connection failed!<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>
