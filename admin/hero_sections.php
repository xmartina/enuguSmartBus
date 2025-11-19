<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug: Log the actual POST data
        error_log("RAW POST DATA: " . print_r($_POST, true));
        
        if (isset($_POST['add_section'])) {
            error_log("ADD SECTION triggered");
            
            $banner_image = null;
            
            // Handle banner image upload with better error handling
            if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
                error_log("File upload started: " . $_FILES['banner_image']['name']);
                $banner_image = $database->uploadFile($_FILES['banner_image'], 'hero');
                error_log("File uploaded successfully: " . $banner_image);
            } else {
                $upload_error = $_FILES['banner_image']['error'] ?? 'No file';
                $error_messages = [
                    UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
                    UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
                    UPLOAD_ERR_PARTIAL => 'File upload incomplete',
                    UPLOAD_ERR_NO_FILE => 'No file selected',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
                    UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload'
                ];
                error_log("File upload error: " . ($error_messages[$upload_error] ?? "Unknown error ($upload_error)"));
            }
            
            // Prepare the INSERT query
            $query = "INSERT INTO hero_sections (welcome_text, main_title, description, button_text, button_link, download_link_text, download_link_url, banner_image, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['welcome_text'] ?? '',
                $_POST['main_title'] ?? '',
                $_POST['description'] ?? '',
                $_POST['button_text'] ?? '',
                $_POST['button_link'] ?? '',
                $_POST['download_link_text'] ?? '',
                $_POST['download_link_url'] ?? '',
                $banner_image,
                $_POST['display_order'] ?? 0
            ];
            
            error_log("Executing INSERT with params: " . print_r($params, true));
            
            // Execute the query
            $result = $stmt->execute($params);
            
            if ($result) {
                $new_id = $db->lastInsertId();
                $_SESSION['success_message'] = "Hero section added successfully! ID: " . $new_id;
                error_log("Hero section added successfully with ID: " . $new_id);
            } else {
                $error_info = $stmt->errorInfo();
                throw new Exception("Database error: " . $error_info[2]);
            }
            
        } elseif (isset($_POST['update_section'])) {
            error_log("UPDATE SECTION triggered for ID: " . ($_POST['id'] ?? 'unknown'));
            
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("No section ID provided for update");
            }
            
            // Get current data first
            $stmt = $db->prepare("SELECT banner_image FROM hero_sections WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $current_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$current_data) {
                throw new Exception("Hero section not found with ID: " . $_POST['id']);
            }
            
            $banner_image = $_POST['current_banner_image'] ?? null;
            
            // Handle new banner image upload
            if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
                error_log("New banner image upload attempted");
                
                // Delete old banner image if exists
                if (!empty($_POST['current_banner_image'])) {
                    $database->deleteFile($_POST['current_banner_image']);
                    error_log("Old banner image deleted: " . $_POST['current_banner_image']);
                }
                
                $banner_image = $database->uploadFile($_FILES['banner_image'], 'hero');
                error_log("New banner image uploaded: " . $banner_image);
            }
            
            // Handle banner image removal
            if (isset($_POST['remove_banner_image']) && $_POST['remove_banner_image'] == '1') {
                error_log("Banner image removal requested");
                if (!empty($_POST['current_banner_image'])) {
                    $database->deleteFile($_POST['current_banner_image']);
                    error_log("Banner image deleted: " . $_POST['current_banner_image']);
                }
                $banner_image = null;
            }
            
            // Prepare the UPDATE query
            $query = "UPDATE hero_sections SET 
                welcome_text = ?, 
                main_title = ?, 
                description = ?, 
                button_text = ?, 
                button_link = ?, 
                download_link_text = ?, 
                download_link_url = ?, 
                banner_image = ?, 
                display_order = ? 
                WHERE id = ?";
                
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['welcome_text'] ?? '',
                $_POST['main_title'] ?? '',
                $_POST['description'] ?? '',
                $_POST['button_text'] ?? '',
                $_POST['button_link'] ?? '',
                $_POST['download_link_text'] ?? '',
                $_POST['download_link_url'] ?? '',
                $banner_image,
                $_POST['display_order'] ?? 0,
                $_POST['id']
            ];
            
            error_log("Executing UPDATE with params: " . print_r($params, true));
            
            // Execute the query
            $result = $stmt->execute($params);
            
            if ($result) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success_message'] = "Hero section updated successfully!";
                error_log("Hero section updated successfully. Affected rows: " . $affected_rows);
            } else {
                $error_info = $stmt->errorInfo();
                throw new Exception("Database error: " . $error_info[2]);
            }
        } else {
            error_log("No valid action detected in POST data");
            $_SESSION['error_message'] = "No action specified. Please use the correct form buttons.";
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
        $_SESSION['error_message'] = $error_msg;
        error_log($error_msg);
    }
    
    // Redirect to prevent form resubmission
    header("Location: hero_sections.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    try {
        error_log("Delete requested for ID: " . $_GET['delete']);
        
        // Get the section to delete its banner image
        $stmt = $db->prepare("SELECT banner_image FROM hero_sections WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $section = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete banner image if exists
        if ($section && $section['banner_image']) {
            $database->deleteFile($section['banner_image']);
            error_log("Banner image deleted: " . $section['banner_image']);
        }
        
        // Delete the section
        $stmt = $db->prepare("DELETE FROM hero_sections WHERE id = ?");
        $result = $stmt->execute([$_GET['delete']]);
        
        if ($result) {
            $_SESSION['success_message'] = "Hero section deleted successfully!";
            error_log("Hero section deleted successfully");
        } else {
            throw new Exception("Failed to delete hero section");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting section: " . $e->getMessage();
        error_log("Delete error: " . $e->getMessage());
    }
    
    header("Location: hero_sections.php");
    exit();
}

// Handle toggle active
if (isset($_GET['toggle'])) {
    try {
        error_log("Toggle active requested for ID: " . $_GET['toggle']);
        
        $stmt = $db->prepare("UPDATE hero_sections SET is_active = NOT is_active WHERE id = ?");
        $result = $stmt->execute([$_GET['toggle']]);
        
        if ($result) {
            $_SESSION['success_message'] = "Hero section status updated!";
            error_log("Toggle successful for ID: " . $_GET['toggle']);
        } else {
            throw new Exception("Failed to toggle status");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating status: " . $e->getMessage();
        error_log("Toggle error: " . $e->getMessage());
    }
    
    header("Location: hero_sections.php");
    exit();
}

// Get all hero sections
try {
    $stmt = $db->query("SELECT * FROM hero_sections ORDER BY display_order, id");
    $hero_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Loaded " . count($hero_sections) . " hero sections from database");
} catch (PDOException $e) {
    $hero_sections = [];
    $error_msg = "Error loading hero sections: " . $e->getMessage();
    $_SESSION['error_message'] = $error_msg;
    error_log($error_msg);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Sections - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-preview {
            max-width: 200px;
            max-height: 100px;
            object-fit: cover;
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
        .debug-info {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 10px;
            margin: 10px 0;
            font-size: 12px;
            font-family: monospace;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
    <?php include 'sidebar.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Hero Sections Management</h1>
            <div>
                <!-- <a href="debug-hero-update.php" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-bug me-1"></i>Debug
                </a> -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHeroModal">
                    <i class="fas fa-plus me-2"></i>Add New Hero Section
                </button>
            </div>
        </div>

        <!-- Debug Information -->
        <!-- <div class="debug-info">
            <strong>Debug Info:</strong><br>
            PHP Version: <?php echo phpversion(); ?><br>
            POST Method: <?php echo $_SERVER['REQUEST_METHOD']; ?><br>
            Hero Sections Count: <?php echo count($hero_sections); ?><br>
            Last Error: <?php echo error_get_last()['message'] ?? 'None'; ?>
        </div>
 -->
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

        <?php if (empty($hero_sections)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No hero sections found. <a href="#" data-bs-toggle="modal" data-bs-target="#addHeroModal">Add your first hero section</a> to display on the homepage.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Preview</th>
                            <th>Welcome Text</th>
                            <th>Main Title</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hero_sections as $section): ?>
                        <tr>
                            <td><?php echo $section['id']; ?></td>
                            <td>
                                <?php if ($section['banner_image']): ?>
                                    <img src="<?php echo $database->getFileUrl($section['banner_image']); ?>" 
                                         alt="Banner Preview" 
                                         class="hero-preview">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($section['welcome_text']); ?></td>
                            <td><?php echo htmlspecialchars($section['main_title']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $section['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $section['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo $section['display_order']; ?></td>
                            <td>
                                <a href="?toggle=<?php echo $section['id']; ?>" 
                                   class="btn btn-sm btn-<?php echo $section['is_active'] ? 'warning' : 'success'; ?>">
                                    <?php echo $section['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                </a>
                                <button class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editHeroModal<?php echo $section['id']; ?>">
                                    Edit
                                </button>
                                <a href="?delete=<?php echo $section['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this hero section?')">
                                    Delete
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editHeroModal<?php echo $section['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Hero Section #<?php echo $section['id']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                                            <input type="hidden" name="current_banner_image" value="<?php echo $section['banner_image']; ?>">
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Welcome Text *</label>
                                                        <input type="text" class="form-control" name="welcome_text" value="<?php echo htmlspecialchars($section['welcome_text']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Display Order</label>
                                                        <input type="number" class="form-control" name="display_order" value="<?php echo $section['display_order']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Main Title *</label>
                                                <input type="text" class="form-control" name="main_title" value="<?php echo htmlspecialchars($section['main_title']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Description *</label>
                                                <textarea class="form-control" name="description" rows="3" required><?php echo htmlspecialchars($section['description']); ?></textarea>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Button Text</label>
                                                        <input type="text" class="form-control" name="button_text" value="<?php echo htmlspecialchars($section['button_text']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Button Link</label>
                                                        <input type="url" class="form-control" name="button_link" value="<?php echo htmlspecialchars($section['button_link']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Download Link Text</label>
                                                        <input type="text" class="form-control" name="download_link_text" value="<?php echo htmlspecialchars($section['download_link_text']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Download Link URL</label>
                                                        <input type="url" class="form-control" name="download_link_url" value="<?php echo htmlspecialchars($section['download_link_url']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Banner Image</label>
                                                <?php if ($section['banner_image']): ?>
                                                    <div class="mb-2">
                                                        <img src="<?php echo $database->getFileUrl($section['banner_image']); ?>" 
                                                             alt="Current Banner" 
                                                             class="img-thumbnail" 
                                                             style="max-height: 150px;">
                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="checkbox" name="remove_banner_image" value="1" id="removeBanner<?php echo $section['id']; ?>">
                                                            <label class="form-check-label text-danger" for="removeBanner<?php echo $section['id']; ?>">
                                                                Remove current banner image
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control" name="banner_image" accept="image/*">
                                                <div class="form-text">Upload a new banner image (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="update_section" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>Update Section
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <!-- Add Hero Modal -->
    <div class="modal fade" id="addHeroModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Hero Section</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Welcome Text *</label>
                                    <input type="text" class="form-control" name="welcome_text" value="Welcome to" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" class="form-control" name="display_order" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Main Title *</label>
                            <input type="text" class="form-control" name="main_title" value="Enugu Smart Bus" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea class="form-control" name="description" rows="3" required>Smart. Safe. Seamless Mobility for Everyone in Enugu State</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" class="form-control" name="button_text" value="Learn More">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Button Link</label>
                                    <input type="url" class="form-control" name="button_link" value="#">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Download Link Text</label>
                                    <input type="text" class="form-control" name="download_link_text" value="Download our mobile app">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Download Link URL</label>
                                    <input type="url" class="form-control" name="download_link_url" value="#">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Banner Image</label>
                            <input type="file" class="form-control" name="banner_image" accept="image/*">
                            <div class="form-text">Upload a banner image for this hero section (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_section" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let valid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            valid = false;
                            field.classList.add('is-invalid');
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!valid) {
                        e.preventDefault();
                        alert('Please fill in all required fields (marked with *)');
                    }
                });
            });
        });
    </script>
</body>
</html>