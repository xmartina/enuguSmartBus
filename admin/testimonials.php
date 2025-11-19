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
        if (isset($_POST['add_testimonial'])) {
            error_log("Adding new testimonial");
            
            $customer_image = null;
            
            // Handle customer image upload
            if (isset($_FILES['customer_image']) && $_FILES['customer_image']['error'] === UPLOAD_ERR_OK) {
                error_log("Customer image upload attempted");
                $customer_image = $database->uploadFile($_FILES['customer_image'], 'testimonials');
                error_log("Customer image uploaded: " . $customer_image);
            } else {
                $upload_error = $_FILES['customer_image']['error'] ?? 'No file';
                error_log("No customer image uploaded or error: " . $upload_error);
            }
            
            $query = "INSERT INTO testimonials (customer_name, customer_role, testimonial, customer_image, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['customer_name'] ?? '',
                $_POST['customer_role'] ?? '',
                $_POST['testimonial'] ?? '',
                $customer_image,
                $_POST['display_order'] ?? 0,
                $_POST['is_active'] ?? 1
            ];
            
            error_log("Executing INSERT with params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $new_id = $db->lastInsertId();
                $_SESSION['success_message'] = "Testimonial added successfully!";
                error_log("Testimonial added successfully with ID: " . $new_id);
            } else {
                throw new Exception("Failed to add testimonial");
            }
            
        } elseif (isset($_POST['update_testimonial'])) {
            error_log("Updating testimonial ID: " . ($_POST['id'] ?? 'unknown'));
            
            // Get current data first
            $stmt = $db->prepare("SELECT customer_image FROM testimonials WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $current_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $customer_image = $_POST['current_image'] ?? null;
            
            // Handle new image upload
            if (isset($_FILES['customer_image']) && $_FILES['customer_image']['error'] === UPLOAD_ERR_OK) {
                error_log("New customer image upload attempted");
                
                // Delete old image if exists
                if (!empty($_POST['current_image'])) {
                    $database->deleteFile($_POST['current_image']);
                    error_log("Old customer image deleted: " . $_POST['current_image']);
                }
                
                $customer_image = $database->uploadFile($_FILES['customer_image'], 'testimonials');
                error_log("New customer image uploaded: " . $customer_image);
            }
            
            // Handle image removal
            if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
                error_log("Customer image removal requested");
                if (!empty($_POST['current_image'])) {
                    $database->deleteFile($_POST['current_image']);
                    error_log("Customer image deleted: " . $_POST['current_image']);
                }
                $customer_image = null;
            }
            
            $query = "UPDATE testimonials SET customer_name = ?, customer_role = ?, testimonial = ?, customer_image = ?, display_order = ?, is_active = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['customer_name'] ?? '',
                $_POST['customer_role'] ?? '',
                $_POST['testimonial'] ?? '',
                $customer_image,
                $_POST['display_order'] ?? 0,
                $_POST['is_active'] ?? 1,
                $_POST['id']
            ];
            
            error_log("Executing UPDATE with params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success_message'] = "Testimonial updated successfully!";
                error_log("Testimonial updated successfully. Affected rows: " . $affected_rows);
            } else {
                throw new Exception("Failed to update testimonial");
            }
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
        $_SESSION['error_message'] = $error_msg;
        error_log($error_msg);
    }
    
    header("Location: testimonials.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    try {
        error_log("Delete requested for testimonial ID: " . $_GET['delete']);
        
        // Get the testimonial to delete its image
        $stmt = $db->prepare("SELECT customer_image FROM testimonials WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $testimonial = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete image if exists
        if ($testimonial && $testimonial['customer_image']) {
            $database->deleteFile($testimonial['customer_image']);
            error_log("Customer image deleted: " . $testimonial['customer_image']);
        }
        
        // Delete the testimonial
        $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
        $result = $stmt->execute([$_GET['delete']]);
        
        if ($result) {
            $_SESSION['success_message'] = "Testimonial deleted successfully!";
            error_log("Testimonial deleted successfully");
        } else {
            throw new Exception("Failed to delete testimonial");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting testimonial: " . $e->getMessage();
        error_log("Delete error: " . $e->getMessage());
    }
    
    header("Location: testimonials.php");
    exit();
}

// Handle toggle active
if (isset($_GET['toggle'])) {
    try {
        error_log("Toggle active requested for ID: " . $_GET['toggle']);
        
        $stmt = $db->prepare("UPDATE testimonials SET is_active = NOT is_active WHERE id = ?");
        $result = $stmt->execute([$_GET['toggle']]);
        
        if ($result) {
            $_SESSION['success_message'] = "Testimonial status updated!";
            error_log("Toggle successful for ID: " . $_GET['toggle']);
        } else {
            throw new Exception("Failed to toggle status");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating status: " . $e->getMessage();
        error_log("Toggle error: " . $e->getMessage());
    }
    
    header("Location: testimonials.php");
    exit();
}

// Get all testimonials
try {
    $stmt = $db->query("SELECT * FROM testimonials ORDER BY display_order, created_at DESC");
    $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Loaded " . count($testimonials) . " testimonials from database");
} catch (PDOException $e) {
    $testimonials = [];
    $error_msg = "Error loading testimonials: " . $e->getMessage();
    $_SESSION['error_message'] = $error_msg;
    error_log($error_msg);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimonials - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .testimonial-preview {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e9ecef;
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
        .testimonial-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            overflow: hidden;
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .avatar-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
    <?php include 'sidebar.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Customer Testimonials Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                <i class="fas fa-plus me-2"></i>Add New Testimonial
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

        <?php if (empty($testimonials)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No testimonials found. <a href="#" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">Add your first testimonial</a> to display on the homepage.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card testimonial-card h-100">
                        <div class="card-body text-center">
                            <!-- Customer Image -->
                            <div class="mb-3">
                                <?php if ($testimonial['customer_image']): ?>
                                    <img src="<?php echo $database->getFileUrl($testimonial['customer_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($testimonial['customer_name']); ?>" 
                                         class="testimonial-preview">
                                <?php else: ?>
                                    <div class="avatar-placeholder mx-auto">
                                        <?php echo strtoupper(substr($testimonial['customer_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Customer Info -->
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($testimonial['customer_name']); ?></h5>
                            <h6 class="card-subtitle mb-3 text-muted"><?php echo htmlspecialchars($testimonial['customer_role']); ?></h6>
                            
                            <!-- Testimonial Text -->
                            <p class="card-text text-muted">
                                "<?php echo htmlspecialchars($testimonial['testimonial']); ?>"
                            </p>
                            
                            <!-- Status and Actions -->
                            <div class="mt-3">
                                <span class="badge bg-<?php echo $testimonial['is_active'] ? 'success' : 'secondary'; ?> me-2">
                                    <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                                <small class="text-muted">Order: <?php echo $testimonial['display_order']; ?></small>
                            </div>
                            
                            <div class="mt-3">
                                <a href="?toggle=<?php echo $testimonial['id']; ?>" 
                                   class="btn btn-sm btn-<?php echo $testimonial['is_active'] ? 'warning' : 'success'; ?>">
                                    <?php echo $testimonial['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                </a>
                                <button class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editTestimonialModal<?php echo $testimonial['id']; ?>">
                                    Edit
                                </button>
                                <a href="?delete=<?php echo $testimonial['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this testimonial?')">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Testimonial Modal -->
                <div class="modal fade" id="editTestimonialModal<?php echo $testimonial['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Testimonial</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                    <input type="hidden" name="current_image" value="<?php echo $testimonial['customer_image']; ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Customer Name *</label>
                                                <input type="text" class="form-control" name="customer_name" 
                                                       value="<?php echo htmlspecialchars($testimonial['customer_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Customer Role/Title *</label>
                                                <input type="text" class="form-control" name="customer_role" 
                                                       value="<?php echo htmlspecialchars($testimonial['customer_role']); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Testimonial *</label>
                                        <textarea class="form-control" name="testimonial" rows="4" required><?php echo htmlspecialchars($testimonial['testimonial']); ?></textarea>
                                        <div class="form-text">Customer's quote or review.</div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Display Order</label>
                                                <input type="number" class="form-control" name="display_order" 
                                                       value="<?php echo $testimonial['display_order']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Status</label>
                                                <select class="form-select" name="is_active">
                                                    <option value="1" <?php echo $testimonial['is_active'] ? 'selected' : ''; ?>>Active</option>
                                                    <option value="0" <?php echo !$testimonial['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Customer Photo</label>
                                        
                                        <?php if ($testimonial['customer_image']): ?>
                                            <div class="mb-3 text-center">
                                                <img src="<?php echo $database->getFileUrl($testimonial['customer_image']); ?>" 
                                                     alt="Current Customer Photo" 
                                                     class="testimonial-preview">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage<?php echo $testimonial['id']; ?>">
                                                    <label class="form-check-label text-danger" for="removeImage<?php echo $testimonial['id']; ?>">
                                                        Remove current photo
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="upload-area mb-2" id="uploadArea<?php echo $testimonial['id']; ?>">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>
                                            Click to upload customer photo
                                            <input type="file" name="customer_image" 
                                                   id="customerImageInput<?php echo $testimonial['id']; ?>" 
                                                   accept="image/*" class="d-none">
                                        </div>
                                        <div id="fileInfo<?php echo $testimonial['id']; ?>" class="d-none">
                                            <div class="alert alert-info">
                                                <i class="fas fa-file-image me-2"></i>
                                                <span id="fileName<?php echo $testimonial['id']; ?>"></span>
                                                <button type="button" class="btn-close float-end" onclick="clearFile(<?php echo $testimonial['id']; ?>)"></button>
                                            </div>
                                        </div>
                                        <div class="form-text">
                                            Upload a customer photo (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="update_testimonial" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Testimonial
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

    <!-- Add Testimonial Modal -->
    <div class="modal fade" id="addTestimonialModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Testimonial</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Customer Name *</label>
                                    <input type="text" class="form-control" name="customer_name" placeholder="e.g., John Doe" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Customer Role/Title *</label>
                                    <input type="text" class="form-control" name="customer_role" placeholder="e.g., Business Owner" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Testimonial *</label>
                            <textarea class="form-control" name="testimonial" rows="4" placeholder="Customer's quote or review" required></textarea>
                            <div class="form-text">Customer's quote or review.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" class="form-control" name="display_order" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="is_active">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Customer Photo</label>
                            <div class="upload-area mb-2" id="addTestimonialUploadArea">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Click to upload customer photo
                                <input type="file" name="customer_image" id="addTestimonialImageInput" accept="image/*" class="d-none">
                            </div>
                            <div id="addTestimonialFileInfo" class="d-none">
                                <div class="alert alert-info">
                                    <i class="fas fa-file-image me-2"></i>
                                    <span id="addTestimonialFileName"></span>
                                    <button type="button" class="btn-close float-end" onclick="clearAddTestimonialFile()"></button>
                                </div>
                            </div>
                            <div class="form-text">
                                Upload a customer photo (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_testimonial" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Testimonial
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload functionality for edit modals
        function initializeFileUpload(testimonialId) {
            const uploadArea = document.getElementById('uploadArea' + testimonialId);
            const customerImageInput = document.getElementById('customerImageInput' + testimonialId);
            const fileInfo = document.getElementById('fileInfo' + testimonialId);
            const fileName = document.getElementById('fileName' + testimonialId);

            if (uploadArea && customerImageInput) {
                uploadArea.addEventListener('click', () => {
                    customerImageInput.click();
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
                        customerImageInput.files = e.dataTransfer.files;
                        updateFileInfo(testimonialId);
                    }
                });

                customerImageInput.addEventListener('change', () => {
                    updateFileInfo(testimonialId);
                });
            }
        }

        function updateFileInfo(testimonialId) {
            const customerImageInput = document.getElementById('customerImageInput' + testimonialId);
            const fileInfo = document.getElementById('fileInfo' + testimonialId);
            const fileName = document.getElementById('fileName' + testimonialId);

            if (customerImageInput.files.length > 0) {
                const file = customerImageInput.files[0];
                fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                fileInfo.classList.remove('d-none');
            }
        }

        function clearFile(testimonialId) {
            const customerImageInput = document.getElementById('customerImageInput' + testimonialId);
            const fileInfo = document.getElementById('fileInfo' + testimonialId);
            customerImageInput.value = '';
            fileInfo.classList.add('d-none');
        }

        // File upload for add testimonial modal
        const addTestimonialUploadArea = document.getElementById('addTestimonialUploadArea');
        const addTestimonialImageInput = document.getElementById('addTestimonialImageInput');
        const addTestimonialFileInfo = document.getElementById('addTestimonialFileInfo');
        const addTestimonialFileName = document.getElementById('addTestimonialFileName');

        if (addTestimonialUploadArea && addTestimonialImageInput) {
            addTestimonialUploadArea.addEventListener('click', () => {
                addTestimonialImageInput.click();
            });

            addTestimonialUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                addTestimonialUploadArea.classList.add('dragover');
            });

            addTestimonialUploadArea.addEventListener('dragleave', () => {
                addTestimonialUploadArea.classList.remove('dragover');
            });

            addTestimonialUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                addTestimonialUploadArea.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    addTestimonialImageInput.files = e.dataTransfer.files;
                    updateAddTestimonialFileInfo();
                }
            });

            addTestimonialImageInput.addEventListener('change', updateAddTestimonialFileInfo);
        }

        function updateAddTestimonialFileInfo() {
            if (addTestimonialImageInput.files.length > 0) {
                const file = addTestimonialImageInput.files[0];
                addTestimonialFileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                addTestimonialFileInfo.classList.remove('d-none');
            }
        }

        function clearAddTestimonialFile() {
            addTestimonialImageInput.value = '';
            addTestimonialFileInfo.classList.add('d-none');
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
        <?php foreach ($testimonials as $testimonial): ?>
            initializeFileUpload(<?php echo $testimonial['id']; ?>);
        <?php endforeach; ?>
    </script>
</body>
</html>