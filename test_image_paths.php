<?php
/**
 * Image Path Testing Script
 * Tests the auto-detection of project paths and URL generation
 * 
 * Usage: Place this file in your project root and access via browser
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Image Path Test - Enugu Smart Bus CMS</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; }
        .success { color: #27c840; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { background: #f0f8ff; padding: 15px; border-left: 4px solid #1f2b6c; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #1f2b6c; color: white; }
        tr:hover { background-color: #f5f5f5; }
        .code { background: #f4f4f4; padding: 2px 6px; font-family: monospace; border-radius: 3px; }
        h2 { color: #1f2b6c; border-bottom: 2px solid #27c840; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🖼️ Image Path Test</h1>
";

// Test 1: Load required files
echo "<h2>1. File Loading Test</h2>";
try {
    if (!file_exists('config/database.php')) {
        throw new Exception("config/database.php not found");
    }
    if (!file_exists('config/url_helper.php')) {
        throw new Exception("config/url_helper.php not found");
    }
    
    include_once 'config/database.php';
    include_once 'config/url_helper.php';
    
    echo "<p class='success'>✓ All required files loaded successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    echo "</body></html>";
    exit;
}

// Test 2: Path Detection
echo "<h2>2. Path Detection Test</h2>";
echo "<table>";
echo "<tr><th>Variable</th><th>Value</th></tr>";
echo "<tr><td>__FILE__</td><td class='code'>" . __FILE__ . "</td></tr>";
echo "<tr><td>\$_SERVER['DOCUMENT_ROOT']</td><td class='code'>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "</td></tr>";
echo "<tr><td>\$_SERVER['SCRIPT_NAME']</td><td class='code'>" . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</td></tr>";
echo "<tr><td>\$_SERVER['REQUEST_URI']</td><td class='code'>" . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</td></tr>";
echo "<tr><td>\$_SERVER['HTTP_HOST']</td><td class='code'>" . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "</td></tr>";

$project_path = UrlHelper::getProjectPath();
echo "<tr><td><strong>Detected Project Path</strong></td><td class='code'><strong>" . ($project_path ?: '(root level)') . "</strong></td></tr>";
echo "</table>";

// Test 3: URL Generation
echo "<h2>3. URL Generation Test</h2>";

$test_filenames = [
    'logos/logo.png',
    'hero/banner.jpg',
    'about/about-image.png',
    'testimonials/customer.jpg',
    'app/phone-mockup.png'
];

echo "<table>";
echo "<tr><th>Filename in Database</th><th>Generated URL</th><th>Expected Behavior</th></tr>";

foreach ($test_filenames as $filename) {
    $url = UrlHelper::getUploadUrl($filename);
    
    // Determine expected behavior
    $expected = $project_path ? "/{$project_path}/uploads/{$filename}" : "/uploads/{$filename}";
    $status = ($url === $expected) ? "✓" : "✗";
    
    echo "<tr>";
    echo "<td class='code'>" . htmlspecialchars($filename) . "</td>";
    echo "<td class='code'>" . htmlspecialchars($url) . "</td>";
    echo "<td>" . $status . " " . htmlspecialchars($expected) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test 4: Database Class Integration
echo "<h2>4. Database Class Integration Test</h2>";

try {
    $database = new Database();
    
    echo "<table>";
    echo "<tr><th>Method</th><th>Input</th><th>Output</th></tr>";
    
    foreach ($test_filenames as $filename) {
        $url = $database->getFileUrl($filename);
        echo "<tr>";
        echo "<td class='code'>getFileUrl()</td>";
        echo "<td class='code'>" . htmlspecialchars($filename) . "</td>";
        echo "<td class='code'>" . htmlspecialchars($url) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p class='success'>✓ Database class integration working</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
    echo "<p class='info'><strong>Note:</strong> Database connection error is normal if you haven't configured database credentials yet. The URL generation should still work.</p>";
}

// Test 5: Deployment Context
echo "<h2>5. Deployment Context Analysis</h2>";

$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
$current_file = __FILE__;
$current_dir = dirname($current_file);

echo "<table>";
echo "<tr><th>Path Analysis</th><th>Value</th></tr>";
echo "<tr><td>Document Root</td><td class='code'>" . htmlspecialchars($doc_root) . "</td></tr>";
echo "<tr><td>Current File</td><td class='code'>" . htmlspecialchars($current_file) . "</td></tr>";
echo "<tr><td>Current Directory</td><td class='code'>" . htmlspecialchars($current_dir) . "</td></tr>";

$normalized_doc = rtrim(str_replace('\\', '/', realpath($doc_root)), '/');
$normalized_current = rtrim(str_replace('\\', '/', realpath($current_dir)), '/');

echo "<tr><td>Normalized Document Root</td><td class='code'>" . htmlspecialchars($normalized_doc) . "</td></tr>";
echo "<tr><td>Normalized Current Dir</td><td class='code'>" . htmlspecialchars($normalized_current) . "</td></tr>";

$deployment_type = ($normalized_current === $normalized_doc) ? 
    "<span class='success'>ROOT LEVEL DEPLOYMENT</span>" : 
    "<span class='info'>SUBFOLDER DEPLOYMENT</span>";

echo "<tr><td><strong>Deployment Type</strong></td><td><strong>" . $deployment_type . "</strong></td></tr>";
echo "</table>";

// Test 6: Full URL Examples
echo "<h2>6. Full URL Examples</h2>";

$base_url = UrlHelper::getBaseUrl();
echo "<p><strong>Base URL:</strong> <span class='code'>" . htmlspecialchars($base_url) . "</span></p>";

echo "<div class='info'>";
echo "<p><strong>Sample Image URLs that will be generated:</strong></p>";
echo "<ul>";
echo "<li><strong>Logo:</strong> <span class='code'>" . htmlspecialchars($base_url . '/uploads/logos/logo.png') . "</span></li>";
echo "<li><strong>Hero Banner:</strong> <span class='code'>" . htmlspecialchars($base_url . '/uploads/hero/banner.jpg') . "</span></li>";
echo "<li><strong>About Image:</strong> <span class='code'>" . htmlspecialchars($base_url . '/uploads/about/about.png') . "</span></li>";
echo "</ul>";
echo "</div>";

// Summary
echo "<h2>✅ Summary</h2>";
echo "<div class='info'>";
echo "<p><strong>Deployment Configuration:</strong></p>";
if ($project_path) {
    echo "<p>✓ Site is deployed in a <strong>subfolder</strong>: <span class='code'>/{$project_path}/</span></p>";
    echo "<p>✓ All image URLs will include the project path: <span class='code'>/{$project_path}/uploads/...</span></p>";
} else {
    echo "<p>✓ Site is deployed at <strong>root level</strong></p>";
    echo "<p>✓ All image URLs will be: <span class='code'>/uploads/...</span></p>";
}
echo "<p><strong>This configuration is automatically detected. No manual changes needed.</strong></p>";
echo "</div>";

echo "<div class='info'>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Upload an image through the admin panel (Admin → Settings → Logo)</li>";
echo "<li>Check if it displays correctly in the admin preview</li>";
echo "<li>Visit the frontend homepage and verify the image loads</li>";
echo "<li>Check browser console (F12) for any 404 errors</li>";
echo "<li>If all images load correctly, delete this test file</li>";
echo "</ol>";
echo "</div>";

echo "
</body>
</html>";
?>
