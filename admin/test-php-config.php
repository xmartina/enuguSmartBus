<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Configuration Test - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1f2b6c 0%, #27c840 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .test-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 30px;
            margin-bottom: 20px;
        }
        .status-ok {
            color: #27c840;
            font-weight: bold;
        }
        .status-warning {
            color: #f59e0b;
            font-weight: bold;
        }
        .status-error {
            color: #dc2626;
            font-weight: bold;
        }
        .setting-row {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .setting-row:last-child {
            border-bottom: none;
        }
        h1 {
            color: #1f2b6c;
            margin-bottom: 20px;
        }
        h3 {
            color: #1f2b6c;
            margin-top: 20px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-card">
            <h1><i class="fas fa-cog"></i> PHP Configuration Test</h1>
            <p class="lead">This page verifies your PHP 8.3 configuration and compatibility.</p>
            
            <h3>PHP Version Information</h3>
            <div class="setting-row">
                <strong>PHP Version:</strong> 
                <span class="<?php echo version_compare(PHP_VERSION, '8.0.0', '>=') ? 'status-ok' : 'status-warning'; ?>">
                    <?php echo PHP_VERSION; ?>
                </span>
                <?php if (version_compare(PHP_VERSION, '8.0.0', '>=')): ?>
                    <span class="badge bg-success">Compatible</span>
                <?php else: ?>
                    <span class="badge bg-warning">Upgrade Recommended</span>
                <?php endif; ?>
            </div>
            
            <div class="setting-row">
                <strong>Server API:</strong> 
                <?php 
                $sapi = php_sapi_name();
                echo $sapi;
                if (strpos($sapi, 'cgi') !== false || strpos($sapi, 'fpm') !== false) {
                    echo ' <span class="status-ok">✓ FastCGI/FPM (Correct for .user.ini)</span>';
                } else {
                    echo ' <span class="status-warning">⚠ Not FastCGI (May need different config)</span>';
                }
                ?>
            </div>
            
            <h3>Upload & Execution Settings</h3>
            <div class="setting-row">
                <strong>upload_max_filesize:</strong> 
                <span class="<?php echo ini_get('upload_max_filesize') === '10M' ? 'status-ok' : 'status-warning'; ?>">
                    <?php echo ini_get('upload_max_filesize'); ?>
                </span>
                <?php if (ini_get('upload_max_filesize') === '10M'): ?>
                    <span class="badge bg-success">Configured ✓</span>
                <?php else: ?>
                    <span class="badge bg-warning">.user.ini may not be loaded yet (wait 5 min or restart PHP)</span>
                <?php endif; ?>
            </div>
            
            <div class="setting-row">
                <strong>post_max_size:</strong> 
                <span class="<?php echo ini_get('post_max_size') === '10M' ? 'status-ok' : 'status-warning'; ?>">
                    <?php echo ini_get('post_max_size'); ?>
                </span>
            </div>
            
            <div class="setting-row">
                <strong>max_file_uploads:</strong> 
                <span class="<?php echo ini_get('max_file_uploads') >= 20 ? 'status-ok' : 'status-warning'; ?>">
                    <?php echo ini_get('max_file_uploads'); ?>
                </span>
            </div>
            
            <div class="setting-row">
                <strong>max_execution_time:</strong> 
                <span class="<?php echo ini_get('max_execution_time') >= 300 ? 'status-ok' : 'status-warning'; ?>">
                    <?php echo ini_get('max_execution_time'); ?> seconds
                </span>
            </div>
            
            <div class="setting-row">
                <strong>memory_limit:</strong> 
                <span class="<?php echo ini_get('memory_limit') === '256M' ? 'status-ok' : 'status-warning'; ?>">
                    <?php echo ini_get('memory_limit'); ?>
                </span>
            </div>
            
            <h3>Database Connection Test</h3>
            <div class="setting-row">
                <?php
                try {
                    include_once '../config/database.php';
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    if ($db) {
                        echo '<span class="status-ok">✓ Database connection successful!</span>';
                        echo ' <span class="badge bg-success">PDO Connected</span>';
                    } else {
                        echo '<span class="status-error">✗ Database connection failed</span>';
                    }
                } catch (Exception $e) {
                    echo '<span class="status-error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</span>';
                }
                ?>
            </div>
            
            <h3>PHP Extensions</h3>
            <div class="setting-row">
                <strong>PDO:</strong> 
                <?php echo extension_loaded('pdo') ? '<span class="status-ok">✓ Loaded</span>' : '<span class="status-error">✗ Not Loaded</span>'; ?>
            </div>
            <div class="setting-row">
                <strong>PDO MySQL:</strong> 
                <?php echo extension_loaded('pdo_mysql') ? '<span class="status-ok">✓ Loaded</span>' : '<span class="status-error">✗ Not Loaded</span>'; ?>
            </div>
            <div class="setting-row">
                <strong>GD (Images):</strong> 
                <?php echo extension_loaded('gd') ? '<span class="status-ok">✓ Loaded</span>' : '<span class="status-warning">⚠ Not Loaded</span>'; ?>
            </div>
            <div class="setting-row">
                <strong>mbstring:</strong> 
                <?php echo extension_loaded('mbstring') ? '<span class="status-ok">✓ Loaded</span>' : '<span class="status-warning">⚠ Not Loaded</span>'; ?>
            </div>
            
            <h3>File System Permissions</h3>
            <div class="setting-row">
                <strong>Uploads Directory:</strong> 
                <?php 
                $uploads_dir = '../uploads/';
                if (is_dir($uploads_dir)) {
                    if (is_writable($uploads_dir)) {
                        echo '<span class="status-ok">✓ Exists and writable</span>';
                    } else {
                        echo '<span class="status-error">✗ Exists but not writable</span>';
                    }
                } else {
                    echo '<span class="status-warning">⚠ Directory does not exist</span>';
                }
                ?>
            </div>
            
            <div class="alert alert-info mt-4">
                <h5>Configuration Notes:</h5>
                <ul class="mb-0">
                    <li><strong>.user.ini files</strong> may take up to 5 minutes to take effect, or require PHP-FPM restart</li>
                    <li>If settings show default values, check that .user.ini files exist in the correct directories</li>
                    <li>For cPanel, you can also use <strong>MultiPHP INI Editor</strong> to adjust settings</li>
                    <li>All PHP 8.3 compatibility issues have been resolved</li>
                </ul>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary btn-lg">Go to Admin Dashboard</a>
                <a href="login.php" class="btn btn-secondary btn-lg">Go to Login</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</body>
</html>
