<?php
// admin/services.php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
include_once '../services_helper.php';

$database = new Database();
$db = $database->getConnection();

$message = '';
$message_type = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['create_service'])) {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'icon' => '',
            'features' => explode("\n", $_POST['features']),
            'display_order' => $_POST['display_order'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Handle file upload
        if (!empty($_FILES['icon']['name'])) {
            try {
                $data['icon'] = $database->uploadFile($_FILES['icon'], 'services');
            } catch (Exception $e) {
                $message = 'Error uploading icon: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
        
        if (createService($db, $data)) {
            $message = 'Service created successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error creating service';
            $message_type = 'error';
        }
    }
    
    if (isset($_POST['update_service'])) {
        $id = $_POST['id'];
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'icon' => $_POST['current_icon'],
            'features' => explode("\n", $_POST['features']),
            'display_order' => $_POST['display_order'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Handle file upload
        if (!empty($_FILES['icon']['name'])) {
            try {
                // Delete old icon
                if ($data['icon']) {
                    $database->deleteFile($data['icon']);
                }
                $data['icon'] = $database->uploadFile($_FILES['icon'], 'services');
            } catch (Exception $e) {
                $message = 'Error uploading icon: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
        
        if (updateService($db, $id, $data)) {
            $message = 'Service updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error updating service';
            $message_type = 'error';
        }
    }
    
    if (isset($_POST['delete_service'])) {
        $id = $_POST['id'];
        $service = getServiceById($db, $id);
        
        if ($service && $service['icon']) {
            $database->deleteFile($service['icon']);
        }
        
        if (deleteService($db, $id)) {
            $message = 'Service deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error deleting service';
            $message_type = 'error';
        }
    }
}

// Get all services for display
$services = getServices($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Enugu Smart Bus Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Manage Services</h1>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type == 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Add Service Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Add New Service</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Service Title</label>
                                <input type="text" class="form-control" name="title" required>
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
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Features (one per line)</label>
                        <textarea class="form-control" name="features" rows="4" placeholder="Enter each feature on a new line"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Icon</label>
                        <input type="file" class="form-control" name="icon" accept="image/*">
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                    
                    <button type="submit" name="create_service" class="btn btn-primary">Add Service</button>
                </form>
            </div>
        </div>

        <!-- Services List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Services</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): 
                                $features = json_decode($service['features'], true);
                            ?>
                            <tr>
                                <td>
                                    <?php if ($service['icon']): ?>
                                    <img src="<?php echo $database->getFileUrl($service['icon']); ?>" 
                                         alt="Icon" style="width: 40px; height: 40px; object-fit: contain;">
                                    <?php else: ?>
                                    <i class="fas fa-cog text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($service['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($service['description'], 0, 50)) . '...'; ?></td>
                                <td><?php echo $service['display_order']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $service['is_active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $service['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                            data-bs-target="#editServiceModal<?php echo $service['id']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" name="delete_service" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editServiceModal<?php echo $service['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Service</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                                <input type="hidden" name="current_icon" value="<?php echo $service['icon']; ?>">
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Service Title</label>
                                                            <input type="text" class="form-control" name="title" 
                                                                   value="<?php echo htmlspecialchars($service['title']); ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Display Order</label>
                                                            <input type="number" class="form-control" name="display_order" 
                                                                   value="<?php echo $service['display_order']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($service['description']); ?></textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Features (one per line)</label>
                                                    <textarea class="form-control" name="features" rows="4"><?php echo implode("\n", $features); ?></textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Icon</label>
                                                    <input type="file" class="form-control" name="icon" accept="image/*">
                                                    <?php if ($service['icon']): ?>
                                                    <small class="text-muted">Current: <?php echo $service['icon']; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" name="is_active" 
                                                           <?php echo $service['is_active'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Active</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="update_service" class="btn btn-primary">Update Service</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>