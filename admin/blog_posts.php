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
        // Delete post tags first
        $stmt = $db->prepare("DELETE FROM blog_post_tags WHERE post_id = ?");
        $stmt->execute([$_GET['delete']]);
        
        // Delete the post
        $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        
        $_SESSION['success_message'] = "Blog post deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting post: " . $e->getMessage();
    }
    header("Location: blog_posts.php");
    exit();
}

if (isset($_GET['toggle_featured'])) {
    try {
        $stmt = $db->prepare("UPDATE blog_posts SET is_featured = NOT is_featured WHERE id = ?");
        $stmt->execute([$_GET['toggle_featured']]);
        $_SESSION['success_message'] = "Featured status updated!";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating post: " . $e->getMessage();
    }
    header("Location: blog_posts.php");
    exit();
}

// Get all blog posts
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$query = "SELECT bp.*, bc.name as category_name 
          FROM blog_posts bp 
          LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
          WHERE 1=1";
$params = [];
$types = '';

if ($search) {
    $query .= " AND (bp.title LIKE ? OR bp.excerpt LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($category_filter) {
    $query .= " AND bp.category_id = ?";
    $params[] = $category_filter;
    $types .= 'i';
}

if ($status_filter) {
    $query .= " AND bp.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$query .= " ORDER BY bp.created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $db->prepare($query);

// Bind parameters with proper types
if (!empty($params)) {
    foreach ($params as $key => $value) {
        $paramType = PDO::PARAM_STR;
        
        // Determine parameter type
        if ($key === count($params) - 2 || $key === count($params) - 1) {
            $paramType = PDO::PARAM_INT; // LIMIT and OFFSET are integers
        } elseif ($key < count($params) - 2 && $category_filter && $key === array_search($category_filter, $params)) {
            $paramType = PDO::PARAM_INT; // category_id is integer
        } elseif ($key < count($params) - 2 && is_numeric($value) && (string)(int)$value === $value) {
            $paramType = PDO::PARAM_INT; // Other numeric values
        }
        
        $stmt->bindValue($key + 1, $value, $paramType);
    }
}

$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$count_query = "SELECT COUNT(*) FROM blog_posts bp WHERE 1=1";
$count_params = [];
$count_types = '';

if ($search) {
    $count_query .= " AND (bp.title LIKE ? OR bp.excerpt LIKE ?)";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
    $count_types .= 'ss';
}

if ($category_filter) {
    $count_query .= " AND bp.category_id = ?";
    $count_params[] = $category_filter;
    $count_types .= 'i';
}

if ($status_filter) {
    $count_query .= " AND bp.status = ?";
    $count_params[] = $status_filter;
    $count_types .= 's';
}

$total_stmt = $db->prepare($count_query);

if (!empty($count_params)) {
    foreach ($count_params as $key => $value) {
        $paramType = PDO::PARAM_STR;
        
        if ($category_filter && $key === array_search($category_filter, $count_params)) {
            $paramType = PDO::PARAM_INT;
        } elseif (is_numeric($value) && (string)(int)$value === $value) {
            $paramType = PDO::PARAM_INT;
        }
        
        $total_stmt->bindValue($key + 1, $value, $paramType);
    }
}

$total_stmt->execute();
$total_posts = $total_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Get categories for filter
$categories = $db->query("SELECT * FROM blog_categories WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				    <h1 class="h2">Blog Posts</h1>
				    <a href="blog_post_edit.php" class="btn btn-primary">
				        <i class="fas fa-plus me-2"></i>Add New Post
				    </a>
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

                <!-- Filters -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>

                <!-- Posts Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">No blog posts found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                        <?php if ($post['featured_image']): ?>
                                            <br><small class="text-muted">Has featured image</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $post['status'] == 'published' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($post['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?toggle_featured=<?php echo $post['id']; ?>" class="btn btn-sm btn-<?php echo $post['is_featured'] ? 'warning' : 'outline-secondary'; ?>">
                                            <?php echo $post['is_featured'] ? 'Featured' : 'Feature'; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $post['published_at'] ? date('M j, Y', strtotime($post['published_at'])) : 'Not published'; ?>
                                    </td>
                                    <td>
                                        <a href="blog_post_edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>