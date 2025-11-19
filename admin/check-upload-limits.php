<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

echo "<h1>PHP Upload Configuration Check</h1>";

$settings = [
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_file_uploads' => ini_get('max_file_uploads'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'file_uploads' => ini_get('file_uploads'),
    'upload_tmp_dir' => ini_get('upload_tmp_dir'),
];

echo "<table border='1' cellpadding='10'>";
foreach ($settings as $key => $value) {
    echo "<tr>";
    echo "<td><strong>$key</strong></td>";
    echo "<td>$value</td>";
    echo "</tr>";
}
echo "</table>";

// Test file upload directory
echo "<h2>Upload Directory Test</h2>";
$upload_dir = "../uploads/";
echo "Upload directory: $upload_dir<br>";
echo "Exists: " . (is_dir($upload_dir) ? 'Yes' : 'No') . "<br>";
echo "Writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "<br>";

// Test form with different file sizes
echo "<h2>Test Different File Sizes</h2>";
?>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="test_upload" value="1">
    <div class="mb-3">
        <label>Small file test:</label>
        <input type="file" name="small_file" accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary">Test Upload</button>
</form>

<?php
if (isset($_POST['test_upload'])) {
    echo "<h3>Upload Test Results:</h3>";
    if (isset($_FILES['small_file'])) {
        $file = $_FILES['small_file'];
        echo "File name: " . $file['name'] . "<br>";
        echo "File size: " . $file['size'] . " bytes<br>";
        echo "File error: " . $file['error'] . "<br>";
        echo "Temp name: " . $file['tmp_name'] . "<br>";
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            echo "<span style='color: green;'>✓ Upload successful!</span><br>";
        } else {
            $error_messages = [
                1 => 'UPLOAD_ERR_INI_SIZE - File too large (php.ini limit)',
                2 => 'UPLOAD_ERR_FORM_SIZE - File too large (HTML form limit)',
                3 => 'UPLOAD_ERR_PARTIAL - File upload incomplete',
                4 => 'UPLOAD_ERR_NO_FILE - No file selected',
                6 => 'UPLOAD_ERR_NO_TMP_DIR - Missing temporary folder',
                7 => 'UPLOAD_ERR_CANT_WRITE - Failed to write file',
                8 => 'UPLOAD_ERR_EXTENSION - PHP extension stopped upload'
            ];
            echo "<span style='color: red;'>✗ Upload failed: " . ($error_messages[$file['error']] ?? "Unknown error ({$file['error']})") . "</span><br>";
        }
    }
}
?>