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
        if (isset($_POST['add_news'])) {
            error_log("Adding new news article");
            
            $news_image = null;
            
            // Handle image upload
            if (isset($_FILES['news_image']) && $_FILES['news_image']['error'] === UPLOAD_ERR_OK) {
                error_log("News image upload attempted");
                $news_image = $database->uploadFile($_FILES['news_image'], 'news');
                error_log("News image uploaded: " . $news_image);
            } else {
                $upload_error = $_FILES['news_image']['error'] ?? 'No file';
                error_log("No news image uploaded or error: " . $upload_error);
            }
            
            $query = "INSERT INTO news_updates (title, content, image, excerpt, read_more_link, is_featured) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['title'] ?? '',
                $_POST['content'] ?? '',
                $news_image,
                $_POST['excerpt'] ?? '',
                $_POST['read_more_link'] ?? '',
                $_POST['is_featured'] ?? 0
            ];
            
            error_log("Executing INSERT with params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $new_id = $db->lastInsertId();
                $_SESSION['success_message'] = "News article added successfully!";
                error_log("News article added successfully with ID: " . $new_id);
            } else {
                throw new Exception("Failed to add news article");
            }
            
        } elseif (isset($_POST['update_news'])) {
            error_log("Updating news article ID: " . ($_POST['id'] ?? 'unknown'));
            
            // Get current data first
            $stmt = $db->prepare("SELECT image FROM news_updates WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $current_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $news_image = $_POST['current_image'] ?? null;
            
            // Handle new image upload
            if (isset($_FILES['news_image']) && $_FILES['news_image']['error'] === UPLOAD_ERR_OK) {
                error_log("New news image upload attempted");
                
                // Delete old image if exists
                if (!empty($_POST['current_image'])) {
                    $database->deleteFile($_POST['current_image']);
                    error_log("Old news image deleted: " . $_POST['current_image']);
                }
                
                $news_image = $database->uploadFile($_FILES['news_image'], 'news');
                error_log("New news image uploaded: " . $news_image);
            }
            
            // Handle image removal
            if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
                error_log("News image removal requested");
                if (!empty($_POST['current_image'])) {
                    $database->deleteFile($_POST['current_image']);
                    error_log("News image deleted: " . $_POST['current_image']);
                }
                $news_image = null;
            }
            
            $query = "UPDATE news_updates SET title = ?, content = ?, image = ?, excerpt = ?, read_more_link = ?, is_featured = ? WHERE id = ?";
            $stmt = $db->prepare($query);
            
            $params = [
                $_POST['title'] ?? '',
                $_POST['content'] ?? '',
                $news_image,
                $_POST['excerpt'] ?? '',
                $_POST['read_more_link'] ?? '',
                $_POST['is_featured'] ?? 0,
                $_POST['id']
            ];
            
            error_log("Executing UPDATE with params: " . print_r($params, true));
            
            $result = $stmt->execute($params);
            
            if ($result) {
                $affected_rows = $stmt->rowCount();
                $_SESSION['success_message'] = "News article updated successfully!";
                error_log("News article updated successfully. Affected rows: " . $affected_rows);
            } else {
                throw new Exception("Failed to update news article");
            }
        }
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
        $_SESSION['error_message'] = $error_msg;
        error_log($error_msg);
    }
    
    header("Location: news.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    try {
        error_log("Delete requested for news ID: " . $_GET['delete']);
        
        // Get the news article to delete its image
        $stmt = $db->prepare("SELECT image FROM news_updates WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $news = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete image if exists
        if ($news && $news['image']) {
            $database->deleteFile($news['image']);
            error_log("News image deleted: " . $news['image']);
        }
        
        // Delete the news article
        $stmt = $db->prepare("DELETE FROM news_updates WHERE id = ?");
        $result = $stmt->execute([$_GET['delete']]);
        
        if ($result) {
            $_SESSION['success_message'] = "News article deleted successfully!";
            error_log("News article deleted successfully");
        } else {
            throw new Exception("Failed to delete news article");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting news article: " . $e->getMessage();
        error_log("Delete error: " . $e->getMessage());
    }
    
    header("Location: news.php");
    exit();
}

// Handle toggle featured
if (isset($_GET['toggle_featured'])) {
    try {
        error_log("Toggle featured requested for ID: " . $_GET['toggle_featured']);
        
        $stmt = $db->prepare("UPDATE news_updates SET is_featured = NOT is_featured WHERE id = ?");
        $result = $stmt->execute([$_GET['toggle_featured']]);
        
        if ($result) {
            $_SESSION['success_message'] = "News article featured status updated!";
            error_log("Toggle featured successful for ID: " . $_GET['toggle_featured']);
        } else {
            throw new Exception("Failed to toggle featured status");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating featured status: " . $e->getMessage();
        error_log("Toggle featured error: " . $e->getMessage());
    }
    
    header("Location: news.php");
    exit();
}

// Get all news articles
try {
    $stmt = $db->query("SELECT * FROM news_updates ORDER BY created_at DESC");
    $news_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Loaded " . count($news_articles) . " news articles from database");
} catch (PDOException $e) {
    $news_articles = [];
    $error_msg = "Error loading news articles: " . $e->getMessage();
    $_SESSION['error_message'] = $error_msg;
    error_log($error_msg);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Updates - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .news-preview {
            max-width: 200px;
            max-height: 120px;
            object-fit: cover;
            border-radius: 8px;
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
        .news-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        .news-image-container {
            height: 180px;
            overflow: hidden;
            background: #f8f9fa;
        }
        .news-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .news-card:hover .news-image {
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
            <h1 class="h2">News & Updates Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                <i class="fas fa-plus me-2"></i>Add New Article
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

        <?php if (empty($news_articles)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No news articles found. <a href="#" data-bs-toggle="modal" data-bs-target="#addNewsModal">Add your first news article</a> to display on the homepage.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Featured</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news_articles as $news): ?>
                        <tr>
                            <td><?php echo $news['id']; ?></td>
                            <td>
                                <?php if ($news['image']): ?>
                                    <img src="<?php echo $database->getFileUrl($news['image']); ?>" 
                                         alt="News Image" 
                                         class="news-preview">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo substr(strip_tags($news['excerpt']), 0, 100); ?>...</small>
                            </td>
                            <td>
                                <a href="?toggle_featured=<?php echo $news['id']; ?>" 
                                   class="btn btn-sm btn-<?php echo $news['is_featured'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $news['is_featured'] ? 'Featured' : 'Normal'; ?>
                                </a>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($news['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editNewsModal<?php echo $news['id']; ?>">
                                    Edit
                                </button>
                                <a href="?delete=<?php echo $news['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this news article?')">
                                    Delete
                                </a>
                            </td>
                        </tr>

                        <!-- Edit News Modal -->
                        <div class="modal fade" id="editNewsModal<?php echo $news['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit News Article</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $news['id']; ?>">
                                            <input type="hidden" name="current_image" value="<?php echo $news['image']; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Title *</label>
                                                <input type="text" class="form-control" name="title" 
                                                       value="<?php echo htmlspecialchars($news['title']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Excerpt (Short Description) *</label>
                                                <textarea class="form-control" name="excerpt" rows="3" required><?php echo htmlspecialchars($news['excerpt']); ?></textarea>
                                                <div class="form-text">Brief summary displayed on the news card.</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Content *</label>
                                                <textarea class="form-control" name="content" rows="6" required><?php echo htmlspecialchars($news['content']); ?></textarea>
                                                <div class="form-text">Full article content.</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">News Image</label>
                                                
                                                <?php if ($news['image']): ?>
                                                    <div class="mb-3">
                                                        <img src="<?php echo $database->getFileUrl($news['image']); ?>" 
                                                             alt="Current News Image" 
                                                             class="img-thumbnail news-preview">
                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeImage<?php echo $news['id']; ?>">
                                                            <label class="form-check-label text-danger" for="removeImage<?php echo $news['id']; ?>">
                                                                Remove current image
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="upload-area mb-2" id="uploadArea<?php echo $news['id']; ?>">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i>
                                                    Click to upload or drag and drop
                                                    <input type="file" name="news_image" 
                                                           id="newsImageInput<?php echo $news['id']; ?>" 
                                                           accept="image/*" class="d-none">
                                                </div>
                                                <div id="fileInfo<?php echo $news['id']; ?>" class="d-none">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-file-image me-2"></i>
                                                        <span id="fileName<?php echo $news['id']; ?>"></span>
                                                        <button type="button" class="btn-close float-end" onclick="clearFile(<?php echo $news['id']; ?>)"></button>
                                                    </div>
                                                </div>
                                                <div class="form-text">
                                                    Upload a news image (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Read More Link</label>
                                                <input type="url" class="form-control" name="read_more_link" 
                                                       value="<?php echo htmlspecialchars($news['read_more_link']); ?>" 
                                                       placeholder="https://...">
                                                <div class="form-text">Link to full article page (optional).</div>
                                            </div>
                                            
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" name="is_featured" value="1" 
                                                       <?php echo $news['is_featured'] ? 'checked' : ''; ?> id="isFeatured<?php echo $news['id']; ?>">
                                                <label class="form-check-label" for="isFeatured<?php echo $news['id']; ?>">
                                                    Featured Article
                                                </label>
                                                <div class="form-text">Featured articles may be highlighted on the homepage.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="update_news" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>Update Article
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

    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New News Article</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" class="form-control" name="title" placeholder="Enter news title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Excerpt (Short Description) *</label>
                            <textarea class="form-control" name="excerpt" rows="3" placeholder="Brief summary of the article" required></textarea>
                            <div class="form-text">Brief summary displayed on the news card.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea class="form-control" name="content" rows="6" placeholder="Full article content" required></textarea>
                            <div class="form-text">Full article content.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">News Image</label>
                            <div class="upload-area mb-2" id="addNewsUploadArea">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Click to upload or drag and drop
                                <input type="file" name="news_image" id="addNewsImageInput" accept="image/*" class="d-none">
                            </div>
                            <div id="addNewsFileInfo" class="d-none">
                                <div class="alert alert-info">
                                    <i class="fas fa-file-image me-2"></i>
                                    <span id="addNewsFileName"></span>
                                    <button type="button" class="btn-close float-end" onclick="clearAddNewsFile()"></button>
                                </div>
                            </div>
                            <div class="form-text">
                                Upload a news image (optional). Max 5MB. Allowed: JPG, PNG, GIF, WebP
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Read More Link</label>
                            <input type="url" class="form-control" name="read_more_link" placeholder="https://...">
                            <div class="form-text">Link to full article page (optional).</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_featured" value="1" id="isFeatured">
                            <label class="form-check-label" for="isFeatured">
                                Featured Article
                            </label>
                            <div class="form-text">Featured articles may be highlighted on the homepage.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_news" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload functionality for edit modals
        function initializeFileUpload(newsId) {
            const uploadArea = document.getElementById('uploadArea' + newsId);
            const newsImageInput = document.getElementById('newsImageInput' + newsId);
            const fileInfo = document.getElementById('fileInfo' + newsId);
            const fileName = document.getElementById('fileName' + newsId);

            if (uploadArea && newsImageInput) {
                uploadArea.addEventListener('click', () => {
                    newsImageInput.click();
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
                        newsImageInput.files = e.dataTransfer.files;
                        updateFileInfo(newsId);
                    }
                });

                newsImageInput.addEventListener('change', () => {
                    updateFileInfo(newsId);
                });
            }
        }

        function updateFileInfo(newsId) {
            const newsImageInput = document.getElementById('newsImageInput' + newsId);
            const fileInfo = document.getElementById('fileInfo' + newsId);
            const fileName = document.getElementById('fileName' + newsId);

            if (newsImageInput.files.length > 0) {
                const file = newsImageInput.files[0];
                fileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                fileInfo.classList.remove('d-none');
            }
        }

        function clearFile(newsId) {
            const newsImageInput = document.getElementById('newsImageInput' + newsId);
            const fileInfo = document.getElementById('fileInfo' + newsId);
            newsImageInput.value = '';
            fileInfo.classList.add('d-none');
        }

        // File upload for add news modal
        const addNewsUploadArea = document.getElementById('addNewsUploadArea');
        const addNewsImageInput = document.getElementById('addNewsImageInput');
        const addNewsFileInfo = document.getElementById('addNewsFileInfo');
        const addNewsFileName = document.getElementById('addNewsFileName');

        if (addNewsUploadArea && addNewsImageInput) {
            addNewsUploadArea.addEventListener('click', () => {
                addNewsImageInput.click();
            });

            addNewsUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                addNewsUploadArea.classList.add('dragover');
            });

            addNewsUploadArea.addEventListener('dragleave', () => {
                addNewsUploadArea.classList.remove('dragover');
            });

            addNewsUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                addNewsUploadArea.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    addNewsImageInput.files = e.dataTransfer.files;
                    updateAddNewsFileInfo();
                }
            });

            addNewsImageInput.addEventListener('change', updateAddNewsFileInfo);
        }

        function updateAddNewsFileInfo() {
            if (addNewsImageInput.files.length > 0) {
                const file = addNewsImageInput.files[0];
                addNewsFileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                addNewsFileInfo.classList.remove('d-none');
            }
        }

        function clearAddNewsFile() {
            addNewsImageInput.value = '';
            addNewsFileInfo.classList.add('d-none');
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
        <?php foreach ($news_articles as $news): ?>
            initializeFileUpload(<?php echo $news['id']; ?>);
        <?php endforeach; ?>
    </script>
</body>
</html>