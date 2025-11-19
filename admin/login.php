<?php
// Enhanced login.php with better error handling
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


ini_set('display_errors', 1);

// Session setup for cPanel
ini_set('session.save_path', '/tmp');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple authentication - FALLBACK if database fails
$valid_username = "admin";
$valid_password = "admin123";

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

// Process login
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // First try database authentication
    try {
        include_once '../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            // Database connection successful - you can implement DB auth here later
            // For now, use the simple auth
            if ($username === $valid_username && $password === $valid_password) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            // Fallback to simple auth if database fails
            if ($username === $valid_username && $password === $valid_password) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password! (Database connection failed)";
            }
        }
    } catch (Exception $e) {
        // Fallback to simple auth
        if ($username === $valid_username && $password === $valid_password) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password! (System error)";
        }
    }
}
?>