<?php
// newsletter-subscribe.php
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for debugging, 0 for production

if ($_POST && isset($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        include_once 'config/database.php';
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Check if email already exists
            $stmt = $db->prepare("SELECT id, is_active FROM newsletter_subscriptions WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                if ($existing['is_active']) {
                    echo json_encode([
                        'success' => true,
                        'message' => "You're already subscribed to our newsletter!"
                    ]);
                } else {
                    // Reactivate existing subscription
                    $stmt = $db->prepare("UPDATE newsletter_subscriptions SET is_active = 1, unsubscribed_at = NULL WHERE email = ?");
                    $stmt->execute([$email]);
                    echo json_encode([
                        'success' => true,
                        'message' => "Welcome back! You've been resubscribed to our newsletter."
                    ]);
                }
            } else {
                // Create new subscription
                $stmt = $db->prepare("INSERT INTO newsletter_subscriptions (email, is_verified) VALUES (?, 1)");
                $stmt->execute([$email]);
                echo json_encode([
                    'success' => true,
                    'message' => "Thank you for subscribing to our newsletter!"
                ]);
            }
        } catch (PDOException $e) {
            error_log("Newsletter subscription error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => "Sorry, there was an error processing your subscription. Please try again."
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Please enter a valid email address."
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "Invalid request."
    ]);
}
?>