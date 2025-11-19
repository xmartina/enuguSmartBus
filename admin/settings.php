<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Debug: Check if uploads directory is accessible
$upload_test = is_writable($database->upload_dir);
if (!$upload_test) {
    $_SESSION['error_message'] = "Uploads directory is not writable. Please check permissions.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get current logo to delete if new one is uploaded
        $current_logo = null;
        $stmt = $db->prepare("SELECT logo FROM site_settings WHERE id=1");
        $stmt->execute();
        $current_settings = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_logo = $current_settings['logo'];

        $new_logo = $current_logo;

        // Handle file upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            // Delete old logo if exists
            if ($current_logo && file_exists($database->getFilePath($current_logo))) {
                $database->deleteFile($current_logo);
            }
            
            // Upload new logo
            $new_logo = $database->uploadFile($_FILES['logo'], 'logos');
            
            // Debug: Check if file was actually uploaded
            if ($new_logo && !file_exists($database->getFilePath($new_logo))) {
                throw new Exception("File upload failed - file not found after upload");
            }
            
        } elseif (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
            // Remove logo if requested
            if ($current_logo) {
                $database->deleteFile($current_logo);
            }
            $new_logo = null;
        }

        // Update settings in database
        $query = "UPDATE site_settings SET 
            logo=?, email1=?, email2=?, phone=?, business_hours=?, 
            office_address=?, facebook_url=?, twitter_url=?, 
            instagram_url=?, youtube_url=?, linkedin_url=?, copyright_text=? 
            WHERE id=1";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $new_logo,
            $_POST['email1'] ?? '',
            $_POST['email2'] ?? '',
            $_POST['phone'] ?? '',
            $_POST['business_hours'] ?? '',
            $_POST['office_address'] ?? '',
            $_POST['facebook_url'] ?? '',
            $_POST['twitter_url'] ?? '',
            $_POST['instagram_url'] ?? '',
            $_POST['youtube_url'] ?? '',
            $_POST['linkedin_url'] ?? '',
            $_POST['copyright_text'] ?? ''
        ]);
        
        $_SESSION['success_message'] = "Settings updated successfully!";
        
        // Debug info
        if ($new_logo) {
            $_SESSION['success_message'] .= " Logo uploaded: " . $new_logo . " - URL: " . $database->getFileUrl($new_logo);
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    
    header("Location: settings.php");
    exit();
}

// Get current settings
try {
    $stmt = $db->query("SELECT * FROM site_settings WHERE id=1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$settings) {
        throw new Exception("No settings found");
    }
} catch (Exception $e) {
    $settings = [];
    $_SESSION['error_message'] = "Error loading settings: " . $e->getMessage();
}

// Debug current logo
$logo_debug = "";
if ($settings['logo']) {
    $logo_path = $database->getFilePath($settings['logo']);
    $logo_url = $database->getFileUrl($settings['logo']);
    $logo_exists = file_exists($logo_path);
    
    $logo_debug = "Logo debug - Path: $logo_path, URL: $logo_url, Exists: " . ($logo_exists ? 'Yes' : 'No');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #1f2b6c;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #2c3e8f;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #2c3e8f;
        }
        main {
            margin-left: 280px;
            padding: 20px;
        }
        .settings-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            object-fit: contain;
            border: 2px dashed #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            background: #e9ecef;
            border-color: #0056b3;
        }
        .upload-area.dragover {
            background: #007bff;
            border-color: #0056b3;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
        
            <?php include 'sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Site Settings</h1>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="settings-section">
                        <h4 class="mb-4"><i class="fas fa-building me-2"></i>Company Information</h4>
                        
                         <!-- Logo Upload Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Company Logo</h5>
                                <p class="text-muted">Upload your company logo. Recommended size: 200x80px</p>
                                
                                <div class="upload-area mb-3" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <p class="mb-1">Click to upload or drag and drop</p>
                                    <small class="text-muted">PNG, JPG, GIF, WebP up to 5MB</small>
                                    <input type="file" name="logo" id="logoInput" accept="image/*" class="d-none">
                                </div>
                                
                                <div id="fileInfo" class="d-none">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-image me-2"></i>
                                        <span id="fileName"></span>
                                        <button type="button" class="btn-close float-end" onclick="clearFile()"></button>
                                    </div>
                                </div>

                                <!-- Upload Directory Check -->
                                <div class="mt-3">
                                    <small class="text-muted">
                                        Upload directory: <?php echo $database->upload_dir; ?><br>
                                        Writable: <?php echo is_writable($database->upload_dir) ? 'Yes' : 'No'; ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Current Logo</h5>
                                <?php if ($settings['logo']): ?>
                                    <?php 
                                    $current_logo_url = $database->getFileUrl($settings['logo']);
                                    $current_logo_path = $database->getFilePath($settings['logo']);
                                    $current_logo_exists = file_exists($current_logo_path);
                                    ?>
                                    
                                    <?php if ($current_logo_exists): ?>
                                        <div class="mb-3">
                                            <img src="<?php echo $current_logo_url; ?>?t=<?php echo time(); ?>" 
                                                 alt="Current Logo" 
                                                 class="logo-preview"
                                                 onerror="this.style.display='none'; document.getElementById('logoError').style.display='block';">
                                            <div id="logoError" class="alert alert-warning mt-2" style="display: none;">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                Logo file exists but cannot be displayed. 
                                                <a href="<?php echo $current_logo_url; ?>" target="_blank">Open directly</a>
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                                            <label class="form-check-label text-danger" for="removeLogo">
                                                Remove current logo
                                            </label>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            File: <?php echo $settings['logo']; ?>
                                        </small>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Logo file not found: <?php echo $settings['logo']; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-muted">
                                        <i class="fas fa-image fa-2x mb-2"></i>
                                        <p>No logo uploaded</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Primary Email</label>
                                    <input type="email" class="form-control" name="email1" value="<?php echo htmlspecialchars($settings['email1'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Support Email</label>
                                    <input type="email" class="form-control" name="email2" value="<?php echo htmlspecialchars($settings['email2'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Business Hours</label>
                                    <textarea class="form-control" name="business_hours" rows="3" required><?php echo htmlspecialchars($settings['business_hours'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Office Address</label>
                                    <textarea class="form-control" name="office_address" rows="3" required><?php echo htmlspecialchars($settings['office_address'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Copyright Text</label>
                                    <input type="text" class="form-control" name="copyright_text" value="<?php echo htmlspecialchars($settings['copyright_text'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="settings-section">
                        <h4 class="mb-4"><i class="fas fa-share-alt me-2"></i>Social Media Links</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fab fa-facebook text-primary me-2"></i>Facebook URL
                                    </label>
                                    <input type="url" class="form-control" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fab fa-twitter text-info me-2"></i>Twitter/X URL
                                    </label>
                                    <input type="url" class="form-control" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fab fa-instagram text-danger me-2"></i>Instagram URL
                                    </label>
                                    <input type="url" class="form-control" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fab fa-youtube text-danger me-2"></i>YouTube URL
                                    </label>
                                    <input type="url" class="form-control" name="youtube_url" value="<?php echo htmlspecialchars($settings['youtube_url'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fab fa-linkedin text-primary me-2"></i>LinkedIn URL
                                    </label>
                                    <input type="url" class="form-control" name="linkedin_url" value="<?php echo htmlspecialchars($settings['linkedin_url'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                        <a href="index.php" class="btn btn-secondary btn-lg ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload functionality
        const uploadArea = document.getElementById('uploadArea');
        const logoInput = document.getElementById('logoInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');

        uploadArea.addEventListener('click', () => {
            logoInput.click();
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                logoInput.files = e.dataTransfer.files;
                updateFileInfo();
            }
        });

        logoInput.addEventListener('change', updateFileInfo);

        function updateFileInfo() {
            if (logoInput.files.length > 0) {
                const file = logoInput.files[0];
                fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                fileInfo.classList.remove('d-none');
            }
        }

        function clearFile() {
            logoInput.value = '';
            fileInfo.classList.add('d-none');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('logoInput');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('File size must be less than 5MB');
                    return false;
                }
                
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Please select a valid image file (JPEG, PNG, GIF, WebP)');
                    return false;
                }
            }
        });
    </script>
</body>
</html>