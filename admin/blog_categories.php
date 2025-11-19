<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle actions
if (isset($_GET['delete'])) {
    try {
        // Check if category has posts
        $stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
        $stmt->execute([$_GET['delete']]);
        $post_count = $stmt->fetchColumn();
        
        if ($post_count > 0) {
            $_SESSION['error_message'] = "Cannot delete category that has blog posts. Please reassign posts first.";
        } else {
            $stmt = $db->prepare("DELETE FROM blog_categories WHERE id = ?");
            $stmt->execute([$_GET['delete']]);
            $_SESSION['success_message'] = "Category deleted successfully!";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting category: " . $e->getMessage();
    }
    header("Location: blog_categories.php");
    exit();
}

if (isset($_GET['toggle'])) {
    try {
        $stmt = $db->prepare("UPDATE blog_categories SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$_GET['toggle']]);
        $_SESSION['success_message'] = "Category status updated!";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating category: " . $e->getMessage();
    }
    header("Location: blog_categories.php");
    exit();
}

// Handle form submission for add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $description = trim($_POST['description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $category_id = $_POST['category_id'] ?? null;
        
        // Generate slug from name if empty
        if (empty($slug)) {
            $slug = generateSlug($name);
        } else {
            $slug = generateSlug($slug);
        }
        
        // Check if slug already exists (excluding current category)
        $slug_check_query = "SELECT id FROM blog_categories WHERE slug = ?";
        $slug_check_params = [$slug];
        
        if ($category_id) {
            $slug_check_query .= " AND id != ?";
            $slug_check_params[] = $category_id;
        }
        
        $stmt = $db->prepare($slug_check_query);
        $stmt->execute($slug_check_params);
        
        if ($stmt->fetch()) {
            $slug .= '-' . time(); // Append timestamp to make unique
        }
        
        if ($category_id) {
            // Update existing category
            $query = "UPDATE blog_categories SET 
                     name = ?, slug = ?, description = ?, is_active = ?, 
                     updated_at = NOW() WHERE id = ?";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$name, $slug, $description, $is_active, $category_id]);
            
            $_SESSION['success_message'] = "Category updated successfully!";
        } else {
            // Create new category
            $query = "INSERT INTO blog_categories (name, slug, description, is_active) 
                     VALUES (?, ?, ?, ?)";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$name, $slug, $description, $is_active]);
            
            $_SESSION['success_message'] = "Category created successfully!";
        }
        
        header("Location: blog_categories.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error saving category: " . $e->getMessage();
    }
}

// Get category for editing
$edit_category = null;
if (isset($_GET['edit'])) {
    try {
        $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$edit_category) {
            $_SESSION['error_message'] = "Category not found!";
            header("Location: blog_categories.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error loading category: " . $e->getMessage();
        header("Location: blog_categories.php");
        exit();
    }
}

// Get all categories with post counts
try {
    $query = "SELECT c.*, COUNT(p.id) as post_count 
              FROM blog_categories c 
              LEFT JOIN blog_posts p ON c.id = p.category_id AND p.status = 'published'
              GROUP BY c.id 
              ORDER BY c.name";
    $stmt = $db->query($query);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
    $_SESSION['error_message'] = "Error loading categories: " . $e->getMessage();
}

// Helper function to generate slug
function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

// Helper functions for statistics (replacing arrow functions)
function getActiveCategoriesCount($categories) {
    $count = 0;
    foreach ($categories as $category) {
        if ($category['is_active']) {
            $count++;
        }
    }
    return $count;
}

function getCategoriesWithPostsCount($categories) {
    $count = 0;
    foreach ($categories as $category) {
        if ($category['post_count'] > 0) {
            $count++;
        }
    }
    return $count;
}

function getTotalPostsCount($categories) {
    $total = 0;
    foreach ($categories as $category) {
        $total += $category['post_count'];
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Categories - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .category-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .category-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .post-count-badge {
            font-size: 0.75em;
        }
        .form-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Blog Categories</h1>
                    <div>
                        <a href="blog_posts.php" class="btn btn-outline-primary me-2">
                            <i class="fas fa-newspaper me-2"></i>Blog Posts
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                            <i class="fas fa-plus me-2"></i>Add New Category
                        </button>
                    </div>
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

                <!-- Categories Grid -->
                <div class="row g-4">
                    <?php if (empty($categories)): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No Categories Found</h4>
                                <p class="text-muted">Get started by creating your first blog category.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                    <i class="fas fa-plus me-2"></i>Create First Category
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card category-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($category['name']); ?></h5>
                                        <span class="badge bg-<?php echo $category['is_active'] ? 'success' : 'secondary'; ?> post-count-badge">
                                            <?php echo $category['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                    
                                    <?php if ($category['description']): ?>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars($category['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="text-muted small">
                                            <i class="fas fa-newspaper me-1"></i>
                                            <?php echo $category['post_count']; ?> posts
                                        </span>
                                        <div class="btn-group">
                                            <a href="?edit=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#categoryModal">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-<?php echo $category['is_active'] ? 'warning' : 'success'; ?>">
                                                <i class="fas fa-<?php echo $category['is_active'] ? 'pause' : 'play'; ?>"></i>
                                            </a>
                                            <a href="?delete=<?php echo $category['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this category? <?php echo $category['post_count'] > 0 ? 'It has ' . $category['post_count'] . ' posts that will become uncategorized.' : ''; ?>')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <?php if ($category['post_count'] > 0): ?>
                                        <div class="mt-2">
                                            <small class="text-info">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Contains <?php echo $category['post_count']; ?> post(s)
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Stats Cards -->
                <?php if (!empty($categories)): ?>
                <div class="row mt-5">
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo count($categories); ?></h4>
                                        <p>Total Categories</p>
                                    </div>
                                    <i class="fas fa-folder fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo getActiveCategoriesCount($categories); ?></h4>
                                        <p>Active Categories</p>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo getTotalPostsCount($categories); ?></h4>
                                        <p>Total Posts</p>
                                    </div>
                                    <i class="fas fa-newspaper fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?php echo getCategoriesWithPostsCount($categories); ?></h4>
                                        <p>Categories with Posts</p>
                                    </div>
                                    <i class="fas fa-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">
                        <?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <?php if ($edit_category): ?>
                            <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Category Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" 
                                       placeholder="e.g., Transportation, Technology" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="slug" class="form-label">Slug *</label>
                                <input type="text" class="form-control" id="slug" name="slug" 
                                       value="<?php echo htmlspecialchars($edit_category['slug'] ?? ''); ?>" 
                                       placeholder="URL-friendly version" required>
                                <small class="text-muted">Used in URLs. Auto-generated from name if empty.</small>
                            </div>
                            
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3" placeholder="Brief description of the category"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           <?php echo ($edit_category['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active Category
                                    </label>
                                </div>
                                <small class="text-muted">Inactive categories won't be shown on the website.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            <?php echo $edit_category ? 'Update Category' : 'Create Category'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('blur', function() {
            const slugField = document.getElementById('slug');
            if (!slugField.value) {
                const name = this.value;
                const slug = name.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                slugField.value = slug;
            }
        });

        // Clear form when modal is hidden (for new entries)
        const categoryModal = document.getElementById('categoryModal');
        categoryModal.addEventListener('hidden.bs.modal', function () {
            // Only clear if we're not editing
            if (!<?php echo $edit_category ? 'true' : 'false'; ?>) {
                document.querySelector('form').reset();
                // Remove any hidden category_id field
                const categoryIdField = document.querySelector('input[name="category_id"]');
                if (categoryIdField) {
                    categoryIdField.remove();
                }
            }
        });

        // If editing, show modal automatically
        <?php if ($edit_category): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
                modal.show();
            });
        <?php endif; ?>

        // Format slugs on input
        document.getElementById('slug').addEventListener('input', function() {
            this.value = this.value.toLowerCase()
                .replace(/[^\w-]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        });
    </script>
</body>
</html>