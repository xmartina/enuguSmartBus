<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$post_id = $_GET['id'] ?? null;
$post = null;
$categories = [];
$tags = [];

// Get categories
try {
    $stmt = $db->query("SELECT * FROM blog_categories WHERE is_active = 1 ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error loading categories: " . $e->getMessage();
}

// Get existing tags
try {
    $stmt = $db->query("SELECT * FROM blog_tags ORDER BY name");
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Tags table might not exist yet, that's okay
}

// Load post if editing
if ($post_id) {
    try {
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            $_SESSION['error_message'] = "Blog post not found!";
            header("Location: blog_posts.php");
            exit();
        }
        
        // Get post tags
        $stmt = $db->prepare("SELECT t.id, t.name FROM blog_tags t 
                             INNER JOIN blog_post_tags pt ON t.id = pt.tag_id 
                             WHERE pt.post_id = ?");
        $stmt->execute([$post_id]);
        $post_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $post_tag_ids = array_column($post_tags, 'id');
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error loading post: " . $e->getMessage();
        header("Location: blog_posts.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']);
        $excerpt = trim($_POST['excerpt']);
        $content = trim($_POST['content']);
        $category_id = $_POST['category_id'] ?: null;
        $status = $_POST['status'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $read_time = intval($_POST['read_time']) ?: 5;
        $meta_description = trim($_POST['meta_description']);
        $meta_keywords = trim($_POST['meta_keywords']);
        $selected_tags = $_POST['tags'] ?? [];
        
        // Generate slug from title if empty
        if (empty($slug)) {
            $slug = generateSlug($title);
        } else {
            $slug = generateSlug($slug);
        }
        
        // Check if slug already exists (excluding current post)
        $slug_check_query = "SELECT id FROM blog_posts WHERE slug = ?";
        $slug_check_params = [$slug];
        
        if ($post_id) {
            $slug_check_query .= " AND id != ?";
            $slug_check_params[] = $post_id;
        }
        
        $stmt = $db->prepare($slug_check_query);
        $stmt->execute($slug_check_params);
        
        if ($stmt->fetch()) {
            $slug .= '-' . time(); // Append timestamp to make unique
        }
        
        // Handle featured image upload
        $featured_image = $post['featured_image'] ?? null;
        
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image if exists
            if ($featured_image && file_exists($database->getFilePath($featured_image))) {
                $database->deleteFile($featured_image);
            }
            
            // Upload new image
            $featured_image = $database->uploadFile($_FILES['featured_image'], 'blog');
        } elseif (isset($_POST['remove_featured_image']) && $_POST['remove_featured_image'] == '1') {
            // Remove featured image
            if ($featured_image && file_exists($database->getFilePath($featured_image))) {
                $database->deleteFile($featured_image);
            }
            $featured_image = null;
        }
        
        // Set published_at date
        $published_at = null;
        if ($status === 'published') {
            $published_at = date('Y-m-d H:i:s');
            if ($post && $post['published_at']) {
                $published_at = $post['published_at']; // Keep existing published date
            }
        }
        
        if ($post_id) {
            // Update existing post
            $query = "UPDATE blog_posts SET 
                     title = ?, slug = ?, excerpt = ?, content = ?, 
                     featured_image = ?, category_id = ?, status = ?, 
                     is_featured = ?, read_time = ?, meta_description = ?, 
                     meta_keywords = ?, published_at = ?, updated_at = NOW() 
                     WHERE id = ?";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                $title, $slug, $excerpt, $content, $featured_image, 
                $category_id, $status, $is_featured, $read_time, 
                $meta_description, $meta_keywords, $published_at, $post_id
            ]);
            
            $message = "Blog post updated successfully!";
        } else {
            // Create new post
            $query = "INSERT INTO blog_posts 
                     (title, slug, excerpt, content, featured_image, category_id, 
                      status, is_featured, read_time, meta_description, meta_keywords, published_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($query);
            $stmt->execute([
                $title, $slug, $excerpt, $content, $featured_image, 
                $category_id, $status, $is_featured, $read_time, 
                $meta_description, $meta_keywords, $published_at
            ]);
            
            $post_id = $db->lastInsertId();
            $message = "Blog post created successfully!";
        }
        
        // Handle tags
        if ($post_id) {
            // Remove existing tags
            $stmt = $db->prepare("DELETE FROM blog_post_tags WHERE post_id = ?");
            $stmt->execute([$post_id]);
            
            // Add new tags
            foreach ($selected_tags as $tag_id) {
                $stmt = $db->prepare("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$post_id, $tag_id]);
            }
            
            // Handle new tags
            $new_tags = $_POST['new_tags'] ?? '';
            if (!empty($new_tags)) {
                $new_tag_names = array_map('trim', explode(',', $new_tags));
                foreach ($new_tag_names as $tag_name) {
                    if (!empty($tag_name)) {
                        // Check if tag exists
                        $stmt = $db->prepare("SELECT id FROM blog_tags WHERE name = ?");
                        $stmt->execute([$tag_name]);
                        $existing_tag = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($existing_tag) {
                            $tag_id = $existing_tag['id'];
                        } else {
                            // Create new tag
                            $tag_slug = generateSlug($tag_name);
                            $stmt = $db->prepare("INSERT INTO blog_tags (name, slug) VALUES (?, ?)");
                            $stmt->execute([$tag_name, $tag_slug]);
                            $tag_id = $db->lastInsertId();
                        }
                        
                        // Link tag to post
                        $stmt = $db->prepare("INSERT IGNORE INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)");
                        $stmt->execute([$post_id, $tag_id]);
                    }
                }
            }
        }
        
        $_SESSION['success_message'] = $message;
        header("Location: blog_posts.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error saving post: " . $e->getMessage();
    }
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post_id ? 'Edit' : 'Add New'; ?> Blog Post - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <style>
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
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
        .tag-badge {
            margin: 2px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $post_id ? 'Edit Blog Post' : 'Add New Blog Post'; ?></h1>
                    <a href="blog_posts.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Posts
                    </a>
                </div>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Title -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Post Title</h5>
                            </div>
                            <div class="card-body">
                                <input type="text" name="title" class="form-control form-control-lg" 
                                       placeholder="Enter post title" required
                                       value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Post Content</h5>
                            </div>
                            <div class="card-body">
                                <textarea name="content" id="content" rows="15" class="form-control" 
                                          placeholder="Write your post content here..." required><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Excerpt -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Excerpt</h5>
                            </div>
                            <div class="card-body">
                                <textarea name="excerpt" rows="4" class="form-control" 
                                          placeholder="Brief description of the post (shown in listings)"><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                                <small class="text-muted">A short summary of your post. Usually 1-2 sentences.</small>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Featured Image</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($post && $post['featured_image']): ?>
                                    <?php $current_image_url = $database->getFileUrl($post['featured_image']); ?>
                                    <div class="mb-3">
                                        <img src="<?php echo $current_image_url; ?>" alt="Current featured image" class="image-preview">
                                        <div class="mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remove_featured_image" value="1" id="removeImage">
                                                <label class="form-check-label text-danger" for="removeImage">
                                                    Remove current image
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="upload-area mb-3" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2 text-muted"></i>
                                    <p class="mb-1">Click to upload or drag and drop</p>
                                    <small class="text-muted">Recommended: 1200x630px, JPG, PNG, or WebP</small>
                                    <input type="file" name="featured_image" id="featuredImage" accept="image/*" class="d-none">
                                </div>
                                <div id="imagePreview" class="mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Publish Settings -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Publish Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="draft" <?php echo ($post['status'] ?? 'draft') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        <option value="published" <?php echo ($post['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Published</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" 
                                               id="isFeatured" <?php echo ($post['is_featured'] ?? 0) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="isFeatured">
                                            Feature this post
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Read Time (minutes)</label>
                                    <input type="number" name="read_time" class="form-control" min="1" max="60" 
                                           value="<?php echo $post['read_time'] ?? 5; ?>">
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>
                                        <?php echo $post_id ? 'Update Post' : 'Publish Post'; ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Category</h5>
                            </div>
                            <div class="card-body">
                                <select name="category_id" class="form-select">
                                    <option value="">Uncategorized</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo ($post['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Tags</h5>
                            </div>
                            <div class="card-body">
                                <!-- Existing Tags -->
                                <?php if (!empty($tags)): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Select Existing Tags</label>
                                        <div style="max-height: 150px; overflow-y: auto;">
                                            <?php foreach ($tags as $tag): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="tags[]" 
                                                           value="<?php echo $tag['id']; ?>" 
                                                           id="tag_<?php echo $tag['id']; ?>"
                                                           <?php echo (isset($post_tag_ids) && in_array($tag['id'], $post_tag_ids)) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="tag_<?php echo $tag['id']; ?>">
                                                        <?php echo htmlspecialchars($tag['name']); ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- New Tags -->
                                <div class="mb-3">
                                    <label class="form-label">Add New Tags</label>
                                    <input type="text" name="new_tags" class="form-control" 
                                           placeholder="Separate tags with commas">
                                    <small class="text-muted">e.g., transportation, smart city, enugu</small>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">SEO Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Slug</label>
                                    <input type="text" name="slug" class="form-control" 
                                           value="<?php echo htmlspecialchars($post['slug'] ?? ''); ?>"
                                           placeholder="URL-friendly version of title">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <textarea name="meta_description" rows="3" class="form-control" 
                                              placeholder="Brief description for search engines"><?php echo htmlspecialchars($post['meta_description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control" 
                                           value="<?php echo htmlspecialchars($post['meta_keywords'] ?? ''); ?>"
                                           placeholder="Comma-separated keywords">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize CKEditor
        CKEDITOR.replace('content', {
            toolbar: [
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule'] },
                { name: 'styles', items: ['Styles', 'Format'] },
                { name: 'tools', items: ['Maximize', 'Source'] }
            ],
            height: 400
        });

        // Image upload handling
        const uploadArea = document.getElementById('uploadArea');
        const featuredImage = document.getElementById('featuredImage');
        const imagePreview = document.getElementById('imagePreview');

        uploadArea.addEventListener('click', () => {
            featuredImage.click();
        });

        featuredImage.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-image me-2"></i>
                            ${file.name} (${formatFileSize(file.size)})
                            <button type="button" class="btn-close float-end" onclick="clearImage()"></button>
                        </div>
                        <img src="${e.target.result}" class="image-preview mt-2" alt="Preview">
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

        function clearImage() {
            featuredImage.value = '';
            imagePreview.innerHTML = '';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Auto-generate slug from title
        document.querySelector('input[name="title"]').addEventListener('blur', function() {
            const slugField = document.querySelector('input[name="slug"]');
            if (!slugField.value) {
                const title = this.value;
                const slug = title.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
                slugField.value = slug;
            }
        });
    </script>
</body>
</html>