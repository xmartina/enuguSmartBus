<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle export
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Email', 'Status', 'Subscribed Date', 'Verified']);
    
    $stmt = $db->query("SELECT * FROM newsletter_subscriptions ORDER BY subscribed_at DESC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['id'],
            $row['email'],
            $row['is_active'] ? 'Active' : 'Inactive',
            $row['subscribed_at'],
            $row['is_verified'] ? 'Yes' : 'No'
        ]);
    }
    fclose($output);
    exit();
}

// Handle toggle active
if (isset($_GET['toggle'])) {
    try {
        $stmt = $db->prepare("UPDATE newsletter_subscriptions SET is_active = NOT is_active WHERE id = ?");
        $result = $stmt->execute([$_GET['toggle']]);
        
        if ($result) {
            $_SESSION['success_message'] = "Subscriber status updated!";
        } else {
            throw new Exception("Failed to update subscriber status");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating status: " . $e->getMessage();
    }
    
    header("Location: newsletter.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    try {
        $stmt = $db->prepare("DELETE FROM newsletter_subscriptions WHERE id = ?");
        $result = $stmt->execute([$_GET['delete']]);
        
        if ($result) {
            $_SESSION['success_message'] = "Subscriber deleted successfully!";
        } else {
            throw new Exception("Failed to delete subscriber");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deleting subscriber: " . $e->getMessage();
    }
    
    header("Location: newsletter.php");
    exit();
}

// Handle bulk actions
if ($_POST && isset($_POST['bulk_action'])) {
    try {
        $selected_ids = $_POST['selected_subscribers'] ?? [];
        
        if (empty($selected_ids)) {
            throw new Exception("No subscribers selected");
        }
        
        $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
        
        switch ($_POST['bulk_action']) {
            case 'activate':
                $stmt = $db->prepare("UPDATE newsletter_subscriptions SET is_active = 1 WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                $_SESSION['success_message'] = count($selected_ids) . " subscribers activated!";
                break;
                
            case 'deactivate':
                $stmt = $db->prepare("UPDATE newsletter_subscriptions SET is_active = 0 WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                $_SESSION['success_message'] = count($selected_ids) . " subscribers deactivated!";
                break;
                
            case 'delete':
                $stmt = $db->prepare("DELETE FROM newsletter_subscriptions WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                $_SESSION['success_message'] = count($selected_ids) . " subscribers deleted!";
                break;
                
            default:
                throw new Exception("Invalid bulk action");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Bulk action error: " . $e->getMessage();
    }
    
    header("Location: newsletter.php");
    exit();
}

// Get all subscribers with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total count
$total_stmt = $db->query("SELECT COUNT(*) FROM newsletter_subscriptions");
$total_subscribers = $total_stmt->fetchColumn();
$total_pages = ceil($total_subscribers / $limit);

// Get subscribers for current page
$stmt = $db->prepare("SELECT * FROM newsletter_subscriptions ORDER BY subscribed_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$active_subscribers = $db->query("SELECT COUNT(*) FROM newsletter_subscriptions WHERE is_active=1")->fetchColumn();
$verified_subscribers = $db->query("SELECT COUNT(*) FROM newsletter_subscriptions WHERE is_verified=1")->fetchColumn();
$new_today = $db->query("SELECT COUNT(*) FROM newsletter_subscriptions WHERE DATE(subscribed_at) = CURDATE()")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stats-card {
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .bulk-actions {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .subscriber-row {
            transition: background-color 0.2s ease;
        }
        .subscriber-row:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
    
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Newsletter Subscribers</h1>
            <div>
                <a href="?export=1" class="btn btn-success">
                    <i class="fas fa-download me-2"></i>Export CSV
                </a>
            </div>
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

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $total_subscribers; ?></h4>
                                <p>Total Subscribers</p>
                            </div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $active_subscribers; ?></h4>
                                <p>Active Subscribers</p>
                            </div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $verified_subscribers; ?></h4>
                                <p>Verified Subscribers</p>
                            </div>
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $new_today; ?></h4>
                                <p>New Today</p>
                            </div>
                            <i class="fas fa-calendar-day fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <form method="POST" id="bulkForm">
            <div class="bulk-actions">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <select name="bulk_action" class="form-select" id="bulkAction">
                            <option value="">Bulk Actions</option>
                            <option value="activate">Activate Selected</option>
                            <option value="deactivate">Deactivate Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary" id="applyBulkAction">
                            <i class="fas fa-play me-1"></i>Apply
                        </button>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            Showing <?php echo count($subscribers); ?> of <?php echo $total_subscribers; ?> subscribers
                        </small>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Subscribed Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subscribers)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No subscribers found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subscribers as $subscriber): ?>
                            <tr class="subscriber-row">
                                <td>
                                    <input type="checkbox" name="selected_subscribers[]" value="<?php echo $subscriber['id']; ?>" class="subscriber-checkbox">
                                </td>
                                <td><?php echo $subscriber['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-muted me-2"></i>
                                        <?php echo htmlspecialchars($subscriber['email']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $subscriber['is_active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $subscriber['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($subscriber['is_verified']): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Verified
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo date('M j, Y g:i A', strtotime($subscriber['subscribed_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <a href="?toggle=<?php echo $subscriber['id']; ?>" 
                                       class="btn btn-sm btn-<?php echo $subscriber['is_active'] ? 'warning' : 'success'; ?>">
                                        <?php echo $subscriber['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                    <a href="?delete=<?php echo $subscriber['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this subscriber?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Subscriber pagination">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bulk actions functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkbox
            const selectAll = document.getElementById('selectAll');
            const subscriberCheckboxes = document.querySelectorAll('.subscriber-checkbox');
            const bulkAction = document.getElementById('bulkAction');
            const applyBulkAction = document.getElementById('applyBulkAction');
            const bulkForm = document.getElementById('bulkForm');

            // Select all functionality
            selectAll.addEventListener('change', function() {
                subscriberCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            });

            // Update select all when individual checkboxes change
            subscriberCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(subscriberCheckboxes).every(cb => cb.checked);
                    selectAll.checked = allChecked;
                });
            });

            // Bulk action form submission
            bulkForm.addEventListener('submit', function(e) {
                const selectedCount = document.querySelectorAll('.subscriber-checkbox:checked').length;
                
                if (selectedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one subscriber');
                    return false;
                }

                if (!bulkAction.value) {
                    e.preventDefault();
                    alert('Please select a bulk action');
                    return false;
                }

                if (bulkAction.value === 'delete') {
                    if (!confirm(`Are you sure you want to delete ${selectedCount} subscriber(s)? This action cannot be undone.`)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        });
    </script>
</body>
</html>