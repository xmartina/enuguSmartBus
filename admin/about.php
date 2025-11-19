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
        error_log("About section form submitted");
        
        $about_image = $_POST['current_image'] ?? null;
        
        // Handle image upload
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            error_log("New about image upload attempted");
            
            // Delete old image if exists
            if (!empty($_POST['current_image'])) {
                $database->deleteFile($_POST['current_image']);
                error_log("Old about image deleted: " . $_POST['current_image']);
            }
            
            // Upload new image
            $about_image = $database->uploadFile($_FILES['about_image'], 'about');
            error_log("New about image uploaded: " . $about_image);
        }
        
        // Handle image removal
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            error_log("About image removal requested");
            if (!empty($_POST['current_image'])) {
                $database->deleteFile($_POST['current_image']);
                error_log("About image deleted: " . $_POST['current_image']);
            }
            $about_image = null;
        }
        
        // Check if about section exists
        $stmt = $db->query("SELECT COUNT(*) FROM about_section WHERE id = 1");
        $exists = $stmt->fetchColumn();
        
        if ($exists) {
            // Update existing about section
            $query = "UPDATE about_section SET image = ?, title = ?, content = ?, button_text = ?, button_link = ? WHERE id = 1";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                $about_image,
                $_POST['title'] ?? '',
                $_POST['content'] ?? '',
                $_POST['button_text'] ?? '',
                $_POST['button_link'] ?? ''
            ]);
            
            if ($result) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success_message'] = "About section updated successfully!";
                error_log("About section updated. Affected rows: " . $affected_rows);
            } else {
                throw new Exception("Failed to update about section");
            }
        } else {
            // Insert new about section
            $query = "INSERT INTO about_section (id, image, title, content, button_text, button_link) VALUES (1, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                $about_image,
                $_POST['title'] ?? '',
                $_POST['content'] ?? '',
                $_POST['button_text'] ?? '',
                $_POST['button_link'] ?? ''
            ]);
            
            if ($result) {
                $_SESSION['success_message'] = "About section created successfully!";
                error_log("About section created successfully");
            } else {
                throw new Exception("Failed to create about section");
            }
        }
        
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
        $_SESSION['error_message'] = $error_msg;
        error_log($error_msg);
    }
    
    header("Location: about.php");
    exit();
}

// Get current about section
try {
    $stmt = $db->query("SELECT * FROM about_section WHERE id = 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$about) {
        // Initialize with default values if no about section exists
        $about = [
            'image' => null,
            'title' => 'About Enugu Smart Bus',
            'content' => 'Enugu Smart Bus is a modern public transport system that combines comfort, safety, and technology to transform the way people move across Enugu State. Our eco-friendly CNG and hybrid buses are equipped with real-time GPS tracking, AI-powered route optimization, on-board Wi-Fi, digital ticketing, and intelligent safety systems — ensuring a smarter, greener, and more convenient journey for all.',
            'button_text' => 'Read More',
            'button_link' => '#'
        ];
    }
} catch (Exception $e) {
    $about = [
        'image' => null,
        'title' => 'About Enugu Smart Bus',
        'content' => 'Enugu Smart Bus is a modern public transport system that combines comfort, safety, and technology to transform the way people move across Enugu State. Our eco-friendly CNG and hybrid buses are equipped with real-time GPS tracking, AI-powered route optimization, on-board Wi-Fi, digital ticketing, and intelligent safety systems — ensuring a smarter, greener, and more convenient journey for all.',
        'button_text' => 'Read More',
        'button_link' => '#'
    ];
    $_SESSION['error_message'] = "Error loading about section: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Section - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .image-preview {
            max-width: 400px;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #dee2e6;
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
    <?php include 'sidebar.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">About Section Management</h1>
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
                <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i>About Section Content</h4>
                
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($about['image'] ?? ''); ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label">About Image</label>
                            
                            <?php if ($about['image']): ?>
                                <div class="mb-3">
                                    <img src="<?php echo $database->getFileUrl($about['image']); ?>" 
                                         alt="About Image" 
                                         class="image-preview"
                                         onerror="this.style.display='none'; document.getElementById('imageError').style.display='block';">
                                    <div id="imageError" class="alert alert-warning mt-2" style="display: none;">
                                        <i class="fas fa-exclamation-triangle"></i> 
                                        Image cannot be displayed but exists in database.
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage">
                                        <label class="form-check-label text-danger" for="removeImage">
                                            Remove current image
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        Current file: <?php echo $about['image']; ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="upload-area mb-3" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-3 text-primary"></i>
                                <h5>Upload About Image</h5>
                                <p class="mb-2">Click to upload or drag and drop</p>
                                <small class="text-muted">PNG, JPG, GIF, WebP up to 5MB</small>
                                <input type="file" name="about_image" id="aboutImageInput" accept="image/*" class="d-none">
                            </div>
                            
                            <div id="fileInfo" class="d-none">
                                <div class="alert alert-info">
                                    <i class="fas fa-file-image me-2"></i>
                                    <span id="fileName"></span>
                                    <button type="button" class="btn-close float-end" onclick="clearFile()"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" 
                                   value="<?php echo htmlspecialchars($about['title']); ?>" 
                                   placeholder="Enter about section title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea class="form-control" name="content" rows="10" 
                                      placeholder="Enter about section content" required><?php echo htmlspecialchars($about['content']); ?></textarea>
                            <div class="form-text">You can use HTML tags for formatting if needed.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" class="form-control" name="button_text" 
                                           value="<?php echo htmlspecialchars($about['button_text']); ?>" 
                                           placeholder="e.g., Read More">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Button Link</label>
                                    <input type="url" class="form-control" name="button_link" 
                                           value="<?php echo htmlspecialchars($about['button_link']); ?>" 
                                           placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Save About Section
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
                <div class="row">
                    <div class="col-md-6">
                        <?php if ($about['image']): ?>
                            <img src="<?php echo $database->getFileUrl($about['image']); ?>" 
                                 alt="About Preview" 
                                 class="img-fluid rounded"
                                 style="max-height: 300px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" 
                                 style="height: 300px;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                            <small class="text-muted">No image uploaded</small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h3><?php echo htmlspecialchars($about['title']); ?></h3>
                        <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($about['content']); ?></p>
                        <?php if ($about['button_text']): ?>
                            <a href="<?php echo htmlspecialchars($about['button_link']); ?>" 
                               class="btn btn-primary">
                                <?php echo htmlspecialchars($about['button_text']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload functionality
        const uploadArea = document.getElementById('uploadArea');
        const aboutImageInput = document.getElementById('aboutImageInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');

        uploadArea.addEventListener('click', () => {
            aboutImageInput.click();
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
                aboutImageInput.files = e.dataTransfer.files;
                updateFileInfo();
            }
        });

        aboutImageInput.addEventListener('change', updateFileInfo);

        function updateFileInfo() {
            if (aboutImageInput.files.length > 0) {
                const file = aboutImageInput.files[0];
                fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                fileInfo.classList.remove('d-none');
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.image-preview');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                }
                reader.readAsDataURL(file);
            }
        }

        function clearFile() {
            aboutImageInput.value = '';
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
            const fileInput = document.getElementById('aboutImageInput');
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