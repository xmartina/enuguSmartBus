<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

echo "<h1>Hero Section Update Debug</h1>";

// Test POST data
echo "<h2>POST Data</h2>";
echo "<pre>";
print_r($_POST);
print_r($_FILES);
echo "</pre>";

// Test database connection and data
echo "<h2>Database Test</h2>";
try {
    $stmt = $db->query("SELECT * FROM hero_sections LIMIT 1");
    $test_data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Database connection: OK<br>";
    echo "Sample data: " . ($test_data ? "Exists" : "No data") . "<br>";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test file upload
echo "<h2>File Upload Test</h2>";
echo "Upload directory: " . $database->upload_dir . "<br>";
echo "Directory writable: " . (is_writable($database->upload_dir) ? 'Yes' : 'No') . "<br>";

// Test form submission simulation
echo "<h2>Test Form Submission</h2>";
?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="test" value="1">
    <input type="text" name="welcome_text" value="Test Welcome" required>
    <input type="text" name="main_title" value="Test Title" required>
    <textarea name="description">Test Description</textarea>
    <input type="file" name="banner_image">
    <button type="submit" name="add">Test Add</button>
</form>

<?php
if (isset($_POST['test'])) {
    echo "<h3>Form Submission Result:</h3>";
    echo "<pre>";
    print_r($_POST);
    print_r($_FILES);
    echo "</pre>";
}
?>