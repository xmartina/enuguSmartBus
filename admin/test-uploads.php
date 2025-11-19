<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h1>File Upload Test</h1>";

// Test upload directory
echo "<h2>Upload Directory Check</h2>";
echo "Upload directory: " . $database->upload_dir . "<br>";
echo "Directory exists: " . (is_dir($database->upload_dir) ? 'Yes' : 'No') . "<br>";
echo "Directory writable: " . (is_writable($database->upload_dir) ? 'Yes' : 'No') . "<br>";

// Test subdirectories
$subdirs = ['logos', 'hero', 'about', 'how-it-works', 'news', 'testimonials', 'app'];
echo "<h2>Subdirectories Check</h2>";
foreach ($subdirs as $dir) {
    $path = $database->upload_dir . $dir . '/';
    echo "$dir: " . (is_dir($path) ? 'Exists' : 'Missing') . " - " . (is_writable($path) ? 'Writable' : 'Not Writable') . "<br>";
}

// Test current logo
echo "<h2>Current Logo Check</h2>";
$stmt = $db->query("SELECT logo FROM site_settings WHERE id=1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if ($settings['logo']) {
    $logo_path = $database->getFilePath($settings['logo']);
    $logo_url = $database->getFileUrl($settings['logo']);
    
    echo "Logo filename: " . $settings['logo'] . "<br>";
    echo "Full path: " . $logo_path . "<br>";
    echo "File exists: " . (file_exists($logo_path) ? 'Yes' : 'No') . "<br>";
    echo "File URL: " . $logo_url . "<br>";
    echo "File size: " . (file_exists($logo_path) ? filesize($logo_path) : 'N/A') . " bytes<br>";
    
    if (file_exists($logo_path)) {
        echo "<img src='$logo_url' style='max-width: 200px; border: 1px solid #ccc;'><br>";
        echo "Direct link: <a href='$logo_url' target='_blank'>Open Logo</a><br>";
    }
} else {
    echo "No logo in database<br>";
}

// Test URL generation
echo "<h2>URL Generation Test</h2>";
$test_filename = "logos/test_image.jpg";
echo "Test filename: $test_filename<br>";
echo "Generated URL: " . $database->getFileUrl($test_filename) . "<br>";

echo "<h2>PHP Info</h2>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Base URL: http://" . $_SERVER['HTTP_HOST'] . "<br>";
?>