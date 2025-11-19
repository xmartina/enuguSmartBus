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
        if (isset($_POST['add_step'])) {
            error_log("Adding new how it works step");
            
            $step_image = null;
            
            // Handle image upload
            if (isset($_FILES['step_image']) && $_FILES['step_image']['error'] === UPLOAD_ERR_OK) {
                error_log("Step image upload attempted");
                $step_image = $database->uploadFile($_FILES['step_image'], 'how-it-works');
                error_log("Step image uploaded: " . $step_image);
            } else {
                $upload_error = $_FILES['step_image']['error'] ?? 'No file';
                error_log("No step image uploaded or error: " . $upload_error);
            }
            
            $query = "INSERT INTO how_it_works (step_number, title, description, image, display_order) VALUES (?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['step_number'] ?? 1,
                $_POST['title'] ?? '',
                $_POST['description'] ?? '',
                $step_image,
                $_POST['display_order'] ?? 0
            ];
            
            error_log("Executing INSERT with params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $new_id = $db->lastInsertId();
                $_SESSION['success_message'] = "Step added successfully!";
                error_log("Step added successfully with ID: " . $new_id);
            } else {
                throw new Exception("Failed to add step");
            }
            
        } elseif (isset($_POST['update_step'])) {
            error_log("Updating step ID: " . ($_POST['id'] ?? 'unknown'));
            
            // Get current data first
            $stmt = $db->prepare("SELECT image FROM how_it_works WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $current_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $step_image = $_POST['current_image'] ?? null;
            
            // Handle new image upload
            if (isset($_FILES['step_image']) && $_FILES['step_image']['error'] === UPLOAD_ERR_OK) {
                error_log("New step image upload attempted");
                
                // Delete old image if exists
                if (!empty($_POST['current_image'])) {
                    $database->deleteFile($_POST['current_image']);
                    error_log("Old step image deleted: " . $_POST['current_image']);
                }
                
                $step_image = $database->uploadFile($_FILES['step_image'], 'how-it-works');
                error_log("New step image uploaded: " . $step_image);
            }
            
            // Handle image removal
            if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
                error_log("Step image removal requested");
                if (!empty($_POST['current_image'])) {
                    $database->deleteFile($_POST['current_image']);
                    error_log("Step image deleted: " . $_POST['current_image']);
                }
                $step_image = null;
            }
            
            $query = "UPDATE how_it_works SET step_number = ?, title = ?, description = ?, image = ?, display_order = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['step_number'] ?? 1,
                $_POST['title'] ?? '',
                $_POST['description'] ?? '',
                $step_image,
                $_POST['display_order'] ?? 0,
                $_POST['id']
            ];
            
            error_log("Executing UPDATE with params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success_message'] = "Step updated successfully!";
                error_log("Step updated successfully. Affected rows: " . $affected_rows);
            } else {
                throw new Exception("Failed to update step");
            }
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
        $_SESSION['error_message'] = $error_msg;
        error_log($error_msg);
    }
    
    header("Location: how_it_works.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    try {
        error_log("Delete requested for step ID: " . $_GET['delete']);
        
        // Get the step to delete its image
        $stmt = $db->prepare("SELECT image FROM how_it_works WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $step = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete image if exists
        if ($step && $step['image']) {
            $database->deleteFile($step['image']);
            error_log("Step image deleted: " . $step['image']);
        }
        
        // Delete the step
        $stmt = $db->prepare("DELETE FROM how_it_works WHERE id = ?");
        $result = $stmt->execute([$_GET['delete']]);
        
        if ($result) {
            $_SESSION['success_message'] = "Step deleted successfully!";
            error_log("Step deleted successfully");
        } else {
            throw new Exception("Failed to delete step");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting step: " . $e->getMessage();
        error_log("Delete error: " . $e->getMessage());
    }
    
    header("Location: how_it_works.php");
    exit();
}

// Get all steps
try {
    $stmt = $db->query("SELECT * FROM how_it_works ORDER BY display_order, step_number");
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Loaded " . count($steps) . " steps from database");
} catch (PDOException $e) {
    $steps = [];
    $error_msg = "Error loading steps: " . $e->getMessage();
    $_SESSION['error_message'] = $error_msg;
    error_log($error_msg);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .step-preview {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #dee2e6;
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
        .step-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            overflow: hidden;
        }
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .step-number {
            width: 50px;
            height: 50px;
            background: #1f2b6c;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
    <?php include 'sidebar.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">How It Works Steps Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStepModal">
                <i class="fas fa-plus me-2"></i>Add New Step
            </button>
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

        <?php if (empty($steps)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No steps found. <a href="#" data-bs-toggle="modal" data-bs-target="#addStepModal">Add your first step</a> to display on the homepage.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($steps as $step): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card step-card h-100">
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="step-number">
                                    <?php echo $step['step_number']; ?>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editStepModal<?php echo $step['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $step['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this step?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($step['image']): ?>
                                <div class="text-center mb-3">
                                    <img src="<?php echo $database->getFileUrl($step['image']); ?>" 
                                         alt="Step <?php echo $step['step_number']; ?>" 
                                         class="step-preview">
                                </div>
                            <?php endif; ?>
                            <h5 class="card-title"><?php echo htmlspecialchars($step['title']); ?></h5>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($step['description']); ?></p>
                            <small class="text-muted">Order: <?php echo $step['display_order']; ?></small>
                        </div>
                    </div>
                </div>

                <!-- Edit Step Modal -->
                <div class="modal fade" id="editStepModal<?php echo $step['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Step <?php echo $step['step_number']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $step['id']; ?>">
                                    <input type="hidden" name="current_image" value="<?php echo $step['image']; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Step Number *</label>
                                                <input type="number" class="form-control" name="step_number" 
                                                       value="<?php echo $step['step_number']; ?>" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Display Order</label>
                                                <input type="number" class="form-control" name="display_order" 
                                                       value="<?php echo $step['display_order']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Title *</label>
                                        <input type="text" class="form-control" name="title" 
                                               value="<?php echo htmlspecialchars($step['title']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Description *</label>
                                        <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($step['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Step Image</label>
                                        
                                        <?php if ($step['image']): ?>
                                            <div class="mb-3">
                                                <img src="<?php echo $database->getFileUrl($step['image']); ?>" 
                                                     alt="Current Step Image" 
                                                     class="step-preview d-block mx-auto">
                                                <div class="form-check mt-2 text-center">
                                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage<?php echo $step['id']; ?>">
                                                    <label class="form-check-label text-danger" for="removeImage<?php echo $step['id']; ?>">
                                                        Remove current image
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="upload-area mb-2" id="uploadArea<?php echo $step['id']; ?>">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>
                                            Click to upload or drag and drop
                                            <input type="file" name="step_image" 
                                                   id="stepImageInput<?php echo $step['id']; ?>" 
                                                   accept="image/*" class="d-none">
                                        </div>
                                        <div id="fileInfo<?php echo $step['id']; ?>" class="d-none">
                                            <div class="alert alert-info">
                                                <i class="fas fa-file-image me-2"></i>
                                                <span id="fileName<?php echo $step['id']; ?>"></span>
                                                <button type="button" class="btn-close float-end" onclick="clearFile(<?php echo $step['id']; ?>)"></button>
                                            </div>
                                        </div>
                                        <div class="form-text">
                                            Upload a step image (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_step" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Step
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Add Step Modal -->
    <div class="modal fade" id="addStepModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Step</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Step Number *</label>
                                    <input type="number" class="form-control" name="step_number" value="1" min="1" required>
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
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g., Register" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description *</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Enter step description" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Step Image</label>
                            <div class="upload-area mb-2" id="addStepUploadArea">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Click to upload or drag and drop
                                <input type="file" name="step_image" id="addStepImageInput" accept="image/*" class="d-none">
                            </div>
                            <div id="addStepFileInfo" class="d-none">
                                <div class="alert alert-info">
                                    <i class="fas fa-file-image me-2"></i>
                                    <span id="addStepFileName"></span>
                                    <button type="button" class="btn-close float-end" onclick="clearAddStepFile()"></button>
                                </div>
                            </div>
                            <div class="form-text">
                                Upload a step image (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_step" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Step
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload functionality for edit modals
        function initializeFileUpload(stepId) {
            const uploadArea = document.getElementById('uploadArea' + stepId);
            const stepImageInput = document.getElementById('stepImageInput' + stepId);
            const fileInfo = document.getElementById('fileInfo' + stepId);
            const fileName = document.getElementById('fileName' + stepId);

            if (uploadArea && stepImageInput) {
                uploadArea.addEventListener('click', () => {
                    stepImageInput.click();
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
                        stepImageInput.files = e.dataTransfer.files;
                        updateFileInfo(stepId);
                    }
                });

                stepImageInput.addEventListener('change', () => {
                    updateFileInfo(stepId);
                });
            }
        }

        function updateFileInfo(stepId) {
            const stepImageInput = document.getElementById('stepImageInput' + stepId);
            const fileInfo = document.getElementById('fileInfo' + stepId);
            const fileName = document.getElementById('fileName' + stepId);

            if (stepImageInput.files.length > 0) {
                const file = stepImageInput.files[0];
                fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                fileInfo.classList.remove('d-none');
            }
        }

        function clearFile(stepId) {
            const stepImageInput = document.getElementById('stepImageInput' + stepId);
            const fileInfo = document.getElementById('fileInfo' + stepId);
            stepImageInput.value = '';
            fileInfo.classList.add('d-none');
        }

        // File upload for add step modal
        const addStepUploadArea = document.getElementById('addStepUploadArea');
        const addStepImageInput = document.getElementById('addStepImageInput');
        const addStepFileInfo = document.getElementById('addStepFileInfo');
        const addStepFileName = document.getElementById('addStepFileName');

        if (addStepUploadArea && addStepImageInput) {
            addStepUploadArea.addEventListener('click', () => {
                addStepImageInput.click();
            });

            addStepUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                addStepUploadArea.classList.add('dragover');
            });

            addStepUploadArea.addEventListener('dragleave', () => {
                addStepUploadArea.classList.remove('dragover');
            });

            addStepUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                addStepUploadArea.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    addStepImageInput.files = e.dataTransfer.files;
                    updateAddStepFileInfo();
                }
            });

            addStepImageInput.addEventListener('change', updateAddStepFileInfo);
        }

        function updateAddStepFileInfo() {
            if (addStepImageInput.files.length > 0) {
                const file = addStepImageInput.files[0];
                addStepFileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                addStepFileInfo.classList.remove('d-none');
            }
        }

        function clearAddStepFile() {
            addStepImageInput.value = '';
            addStepFileInfo.classList.add('d-none');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const fileInputs = form.querySelectorAll('input[type="file"]');
                    fileInputs.forEach(input => {
                        if (input.files.length > 0) {
                            const file = input.files[0];
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
                });
            });
        });

        // Initialize file upload for existing edit modals
        <?php foreach ($steps as $step): ?>
            initializeFileUpload(<?php echo $step['id']; ?>);
        <?php endforeach; ?>
    </script>
</body>
</html>