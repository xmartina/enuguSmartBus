<?php
// debug-newsletter.php
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Test database connection and table
try {
    echo "<h3>Newsletter System Debug</h3>";
    
    // Test connection
    echo "<p>Database connection: ✅ Success</p>";
    
    // Test table access
    $stmt = $db->query("SELECT COUNT(*) as count FROM newsletter_subscriptions");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p>Total subscribers: " . $result['count'] . "</p>";
    
    // Test insert
    $test_email = "test_" . time() . "@example.com";
    $stmt = $db->prepare("INSERT INTO newsletter_subscriptions (email, is_verified) VALUES (?, 1)");
    $stmt->execute([$test_email]);
    echo "<p>Test insert: ✅ Success (added: $test_email)</p>";
    
    // Clean up test
    $stmt = $db->prepare("DELETE FROM newsletter_subscriptions WHERE email = ?");
    $stmt->execute([$test_email]);
    echo "<p>Test cleanup: ✅ Success</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>