<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        error_log("App section form submitted");
        
        $phone_image = $_POST['current_phone_image'] ?? null;
        
        // Handle phone image upload
        if (isset($_FILES['phone_image']) && $_FILES['phone_image']['error'] === UPLOAD_ERR_OK) {
            error_log("New phone image upload attempted");
            
            // Delete old image if exists
            if (!empty($_POST['current_phone_image'])) {
                $database->deleteFile($_POST['current_phone_image']);
                error_log("Old phone image deleted: " . $_POST['current_phone_image']);
            }
            
            // Upload new image
            $phone_image = $database->uploadFile($_FILES['phone_image'], 'app');
            error_log("New phone image uploaded: " . $phone_image);
        }
        
        // Handle image removal
        if (isset($_POST['remove_phone_image']) && $_POST['remove_phone_image'] == '1') {
            error_log("Phone image removal requested");
            if (!empty($_POST['current_phone_image'])) {
                $database->deleteFile($_POST['current_phone_image']);
                error_log("Phone image deleted: " . $_POST['current_phone_image']);
            }
            $phone_image = null;
        }
        
        // Check if app section exists
        $stmt = $db->query("SELECT COUNT(*) FROM app_section WHERE id = 1");
        $exists = $stmt->fetchColumn();
        
        if ($exists) {
            // Update existing app section
            $query = "UPDATE app_section SET title = ?, content = ?, app_store_link = ?, play_store_link = ?, phone_image = ? WHERE id = 1";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                $_POST['title'] ?? '',
                $_POST['content'] ?? '',
                $_POST['app_store_link'] ?? '',
                $_POST['play_store_link'] ?? '',
                $phone_image
            ]);
            
            if ($result) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success_message'] = "App section updated successfully!";
                error_log("App section updated. Affected rows: " . $affected_rows);
            } else {
                throw new Exception("Failed to update app section");
            }
        } else {
            // Insert new app section
            $query = "INSERT INTO app_section (id, title, content, app_store_link, play_store_link, phone_image) VALUES (1, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                $_POST['title'] ?? '',
                $_POST['content'] ?? '',
                $_POST['app_store_link'] ?? '',
                $_POST['play_store_link'] ?? '',
                $phone_image
            ]);
            
            if ($result) {
                $_SESSION['success_message'] = "App section created successfully!";
                error_log("App section created successfully");
            } else {
                throw new Exception("Failed to create app section");
            }
        }
        
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
        $_SESSION['error_message'] = $error_msg;
        error_log($error_msg);
    }
    
    header("Location: app_section.php");
    exit();
}

// Get current app section
try {
    $stmt = $db->query("SELECT * FROM app_section WHERE id = 1");
    $app_section = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$app_section) {
        // Initialize with default values if no app section exists
        $app_section = [
            'title' => 'Download Our Mobile App',
            'content' => 'Experience comfort and convenience with the Enugu Smart Bus app — your all-in-one platform for smart, safe, and cashless travel.',
            'app_store_link' => '#',
            'play_store_link' => '#',
            'phone_image' => null
        ];
    }
} catch (Exception $e) {
    $app_section = [
        'title' => 'Download Our Mobile App',
        'content' => 'Experience comfort and convenience with the Enugu Smart Bus app — your all-in-one platform for smart, safe, and cashless travel.',
        'app_store_link' => '#',
        'play_store_link' => '#',
        'phone_image' => null
    ];
    $_SESSION['error_message'] = "Error loading app section: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Section - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .phone-preview {
            max-width: 300px;
            max-height: 500px;
            object-fit: contain;
        }
        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            background: #e9ecef;
            border-color: #0056b3;
        }
        .settings-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .store-badge {
            height: 50px;
            width: auto;
            transition: transform 0.3s ease;
        }
        .store-badge:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
    <?php include 'sidebar.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">App Download Section</h1>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="settings-section">
                <h4 class="mb-4"><i class="fas fa-mobile-alt me-2"></i>App Download Section Content</h4>
                
                <input type="hidden" name="current_phone_image" value="<?php echo htmlspecialchars($app_section['phone_image'] ?? ''); ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?php echo htmlspecialchars($app_section['title']); ?>" 
                                   placeholder="Enter app section title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea class="form-control" name="content" rows="5" 
                                      placeholder="Enter app section description" required><?php echo htmlspecialchars($app_section['content']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">App Store Link</label>
                            <input type="url" class="form-control" name="app_store_link" 
                                   value="<?php echo htmlspecialchars($app_section['app_store_link']); ?>" 
                                   placeholder="https://apps.apple.com/...">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Google Play Store Link</label>
                            <input type="url" class="form-control" name="play_store_link" 
                                   value="<?php echo htmlspecialchars($app_section['play_store_link']); ?>" 
                                   placeholder="https://play.google.com/...">
                        </div>
                        
                        <!-- Store Badges Preview -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="mb-0">Store Badges Preview</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex gap-3 flex-wrap">
                                    <?php if ($app_section['app_store_link'] && $app_section['app_store_link'] != '#'): ?>
                                        <a href="<?php echo htmlspecialchars($app_section['app_store_link']); ?>" target="_blank" class="download-badge">
                                            <img src="../assets/app-store-badge.png" alt="Download on the App Store" class="store-badge">
                                        </a>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <img src="../assets/app-store-badge.png" alt="App Store" class="store-badge opacity-50">
                                            <small class="text-muted d-block mt-1">Add App Store link to activate</small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($app_section['play_store_link'] && $app_section['play_store_link'] != '#'): ?>
                                        <a href="<?php echo htmlspecialchars($app_section['play_store_link']); ?>" target="_blank" class="download-badge">
                                            <img src="../assets/google-play-badge.png" alt="Get it on Google Play" class="store-badge">
                                        </a>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <img src="../assets/google-play-badge.png" alt="Google Play" class="store-badge opacity-50">
                                            <small class="text-muted d-block mt-1">Add Play Store link to activate</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label">Phone Mockup Image</label>
                            
                            <?php if ($app_section['phone_image']): ?>
                                <div class="mb-3 text-center">
                                    <img src="<?php echo $database->getFileUrl($app_section['phone_image']); ?>" 
                                         alt="Phone Mockup" 
                                         class="phone-preview img-thumbnail"
                                         onerror="this.style.display='none'; document.getElementById('phoneImageError').style.display='block';">
                                    <div id="phoneImageError" class="alert alert-warning mt-2" style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Image cannot be displayed but exists in database.
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_phone_image" value="1" id="removePhoneImage">
                                        <label class="form-check-label text-danger" for="removePhoneImage">
                                            Remove current phone image
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        Current file: <?php echo $app_section['phone_image']; ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="upload-area mb-3" id="uploadArea">
                                <i class="fas fa-mobile-alt fa-3x mb-3 text-primary"></i>
                                <h5>Upload Phone Mockup Image</h5>
                                <p class="mb-2">Click to upload or drag and drop</p>
                                <small class="text-muted">PNG, JPG, GIF, WebP up to 5MB<br>Recommended: Transparent PNG with phone mockup</small>
                                <input type="file" name="phone_image" id="phoneImageInput" accept="image/*" class="d-none">
                            </div>
                            
                            <div id="fileInfo" class="d-none">
                                <div class="alert alert-info">
                                    <i class="fas fa-file-image me-2"></i>
                                    <span id="fileName"></span>
                                    <button type="button" class="btn-close float-end" onclick="clearFile()"></button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Requirements -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    Image Requirements
                                </h6>
                                <ul class="small mb-0">
                                    <li>Format: PNG (recommended), JPG, WebP</li>
                                    <li>Size: Max 5MB</li>
                                    <li>Dimensions: 400x800px or similar aspect ratio</li>
                                    <li>Transparent background recommended</li>
                                    <li>Show phone mockup with app screens</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Save App Section
                    </button>
                    <a href="index.php" class="btn btn-secondary btn-lg ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </form>
        
        <!-- Preview Section -->
        <div class="settings-section">
            <h4 class="mb-4"><i class="fas fa-eye me-2"></i>Live Preview</h4>
            <div class="border rounded p-4 bg-light">
                <h5>How it will appear on the frontend:</h5>
                <hr>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="text-center">
                            <?php if ($app_section['phone_image']): ?>
                                <img src="<?php echo $database->getFileUrl($app_section['phone_image']); ?>" 
                                     alt="App Preview" 
                                     class="img-fluid"
                                     style="max-height: 400px;">
                            <?php else: ?>
                                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" 
                                     style="height: 400px;">
                                    <div class="text-center">
                                        <i class="fas fa-mobile-alt fa-4x mb-3"></i>
                                        <p>Phone mockup image will appear here</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3 class="mb-3"><?php echo htmlspecialchars($app_section['title']); ?></h3>
                        <p class="mb-4"><?php echo htmlspecialchars($app_section['content']); ?></p>
                        
                        <div class="d-flex gap-3 flex-wrap">
                            <?php if ($app_section['app_store_link'] && $app_section['app_store_link'] != '#'): ?>
                                <a href="<?php echo htmlspecialchars($app_section['app_store_link']); ?>" class="download-badge">
                                    <img src="../assets/app-store-badge.png" alt="Download on the App Store" class="store-badge">
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($app_section['play_store_link'] && $app_section['play_store_link'] != '#'): ?>
                                <a href="<?php echo htmlspecialchars($app_section['play_store_link']); ?>" class="download-badge">
                                    <img src="../assets/google-play-badge.png" alt="Get it on Google Play" class="store-badge">
                                </a>
                            <?php endif; ?>
                            
                            <?php if (($app_section['app_store_link'] == '#' || !$app_section['app_store_link']) && ($app_section['play_store_link'] == '#' || !$app_section['play_store_link'])): ?>
                                <div class="alert alert-warning small">
                                    Add App Store and/or Play Store links to display download badges
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload functionality
        const uploadArea = document.getElementById('uploadArea');
        const phoneImageInput = document.getElementById('phoneImageInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');

        uploadArea.addEventListener('click', () => {
            phoneImageInput.click();
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
                phoneImageInput.files = e.dataTransfer.files;
                updateFileInfo();
            }
        });

        phoneImageInput.addEventListener('change', updateFileInfo);

        function updateFileInfo() {
            if (phoneImageInput.files.length > 0) {
                const file = phoneImageInput.files[0];
                fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                fileInfo.classList.remove('d-none');
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.phone-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                }
                reader.readAsDataURL(file);
            }
        }

        function clearFile() {
            phoneImageInput.value = '';
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
            const fileInput = document.getElementById('phoneImageInput');
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
            
            // Check required fields
            const title = document.querySelector('input[name="title"]');
            const content = document.querySelector('textarea[name="content"]');
            
            if (!title.value.trim() || !content.value.trim()) {
                e.preventDefault();
                alert('Please fill in all required fields (Title and Content)');
                return false;
            }
        });
    </script>
</body>
</html>