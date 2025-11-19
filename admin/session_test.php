<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Session Debug Info</h2>";

// Check session save path
$sessionPath = session_save_path();
echo "<p>Session save path: " . ($sessionPath ? $sessionPath : 'default') . "</p>";

// Check if writable
echo "<p>Session path writable: " . (is_writable($sessionPath) ? 'Yes' : 'No') . "</p>";

// Set custom session path if needed
if (empty($sessionPath) || !is_writable($sessionPath)) {
    ini_set('session.save_path', '/tmp');
    echo "<p>Changed session path to /tmp</p>";
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "<p>Session status: " . session_status() . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";

// Test session writing
$_SESSION['test_time'] = date('Y-m-d H:i:s');
echo "<p>Session test value set: " . $_SESSION['test_time'] . "</p>";

echo "<p><strong>If you see all this, sessions are working!</strong></p>";
?>