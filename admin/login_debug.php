<?php
echo "1. Starting PHP...<br>";
flush();

error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "2. Error reporting set...<br>";
flush();

ini_set('session.save_path', '/tmp');
echo "3. Session path set...<br>";
flush();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "4. Session started...<br>";
flush();

echo "5. Script completed successfully!<br>";
?>