<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get all dashboard stats
$news_count = $db->query("SELECT COUNT(*) FROM news_updates")->fetchColumn();
$active_testimonials = $db->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn();
$subscribers = $db->query("SELECT COUNT(*) FROM newsletter_subscriptions WHERE is_active=1")->fetchColumn();
$how_it_works_steps = $db->query("SELECT COUNT(*) FROM how_it_works")->fetchColumn();
$hero_sections = $db->query("SELECT COUNT(*) FROM hero_sections WHERE is_active=1")->fetchColumn();
$about_sections = $db->query("SELECT COUNT(*) FROM about_section")->fetchColumn();
$app_sections = $db->query("SELECT COUNT(*) FROM app_section")->fetchColumn();

// Get recent news
$recent_news = $db->query("SELECT title, created_at FROM news_updates ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get recent testimonials
$recent_testimonials = $db->query("SELECT customer_name, created_at FROM testimonials ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get popular routes (example data)
$popular_routes = [
    ['route' => 'Enugu - Nsukka', 'trips' => 245],
    ['route' => 'Enugu - Abakpa', 'trips' => 189],
    ['route' => 'Enugu - Emene', 'trips' => 167],
    ['route' => 'Enugu - 9th Mile', 'trips' => 142],
    ['route' => 'Enugu - Trans-Ekulu', 'trips' => 128]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Enugu Smart Bus CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-blue: #1f2b6c;
            --primary-green: #27c840;
            --dark-green: #22b038;
            --light-green: #0f9918;
            --dark-blue: #001447;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--dark-blue) 0%, var(--primary-blue) 100%);
            width: var(--sidebar-width);
            position: fixed;
            transition: all 0.3s;
            z-index: 100;
        }
        
        .sidebar .nav-link {
            color: #fff;
            padding: 12px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid var(--primary-green);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .stat-card {
            border-left: 4px solid var(--primary-blue);
        }
        
        .stat-card.green {
            border-left-color: var(--primary-green);
        }
        
        .stat-card.orange {
            border-left-color: #f59e0b;
        }
        
        .stat-card.purple {
            border-left-color: #8b5cf6;
        }
        
        .stat-card.cyan {
            border-left-color: #06b6d4;
        }
        
        .stat-card.pink {
            border-left-color: #ec4899;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ef4444;
        }
        
        .activity-item {
            border-left: 3px solid var(--primary-blue);
            padding-left: 15px;
            margin-bottom: 15px;
        }
        
        .activity-item.new {
            border-left-color: var(--primary-green);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar .nav-text {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
   <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            

            <!-- Main Content -->
            <div class="main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-custom">
                    <div class="container-fluid">
                        <button class="btn btn-link text-dark d-md-none" type="button" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        
                        <div class="d-flex align-items-center ms-auto">
                            <div class="dropdown me-3">
                                <a class="btn btn-light btn-sm dropdown-toggle" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    <span class="badge bg-danger">3</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                    <li><a class="dropdown-item" href="#">New subscriber registered</a></li>
                                    <li><a class="dropdown-item" href="#">Testimonial awaiting approval</a></li>
                                    <li><a class="dropdown-item" href="#">System update available</a></li>
                                </ul>
                            </div>
                            
                            <div class="dropdown">
                                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <span class="d-none d-md-inline">Admin User</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                    <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid py-4">
                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-1">Dashboard</h1>
                            <p class="text-muted mb-0">Welcome back, Admin! Here's what's happening with Enugu Smart Bus today.</p>
                        </div>
                        <div class="d-flex">
                            <button class="btn btn-primary me-2">
                                <i class="fas fa-plus me-1"></i> Add Content
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download me-1"></i> Export
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                    <li><a class="dropdown-item" href="#">PDF Report</a></li>
                                    <li><a class="dropdown-item" href="#">Excel Data</a></li>
                                    <li><a class="dropdown-item" href="#">CSV File</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dashboard-card stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="mb-2"><?php echo $news_count; ?></h4>
                                            <p class="text-muted mb-0">News Articles</p>
                                        </div>
                                        <div class="card-icon bg-blue-100 text-primary-blue">
                                            <i class="fas fa-newspaper"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i> 12% from last month
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dashboard-card stat-card green">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="mb-2"><?php echo $active_testimonials; ?></h4>
                                            <p class="text-muted mb-0">Active Testimonials</p>
                                        </div>
                                        <div class="card-icon bg-green-100 text-primary-green">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i> 5% from last month
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dashboard-card stat-card orange">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="mb-2"><?php echo $subscribers; ?></h4>
                                            <p class="text-muted mb-0">Subscribers</p>
                                        </div>
                                        <div class="card-icon bg-orange-100 text-warning">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i> 8% from last month
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dashboard-card stat-card purple">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="mb-2"><?php echo $how_it_works_steps; ?></h4>
                                            <p class="text-muted mb-0">How It Works Steps</p>
                                        </div>
                                        <div class="card-icon bg-purple-100 text-purple">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-minus me-1"></i> No change
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dashboard-card stat-card cyan">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="mb-2"><?php echo $hero_sections; ?></h4>
                                            <p class="text-muted mb-0">Active Hero Sections</p>
                                        </div>
                                        <div class="card-icon bg-cyan-100 text-info">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i> 1 new
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="dashboard-card stat-card pink">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="mb-2"><?php echo $about_sections; ?></h4>
                                            <p class="text-muted mb-0">About Sections</p>
                                        </div>
                                        <div class="card-icon bg-pink-100 text-pink">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-minus me-1"></i> No change
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Additional Content -->
                    <div class="row g-4">
                        <!-- Website Traffic Chart -->
                        <div class="col-lg-8">
                            <div class="dashboard-card">
                                <div class="card-header bg-white border-bottom-0 py-3">
                                    <h5 class="mb-0">Website Traffic Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="trafficChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Activities -->
                        <div class="col-lg-4">
                            <div class="dashboard-card h-100">
                                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Activities</h5>
                                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <div class="activity-list">
                                        <?php foreach($recent_news as $news): ?>
                                        <div class="activity-item new">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">New Article Published</h6>
                                                    <p class="mb-1 text-muted small"><?php echo $news['title']; ?></p>
                                                </div>
                                                <small class="text-muted"><?php echo date('M j', strtotime($news['created_at'])); ?></small>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <?php foreach($recent_testimonials as $testimonial): ?>
                                        <div class="activity-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">New Testimonial</h6>
                                                    <p class="mb-1 text-muted small">From <?php echo $testimonial['customer_name']; ?></p>
                                                </div>
                                                <small class="text-muted"><?php echo date('M j', strtotime($testimonial['created_at'])); ?></small>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Content Sections -->
                    <div class="row g-4 mt-2">
                        <!-- Recent News -->
                        <div class="col-lg-6">
                            <div class="dashboard-card">
                                <div class="card-header bg-white border-bottom-0 py-3 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent News Articles</h5>
                                    <a href="news.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($recent_news as $news): ?>
                                                <tr>
                                                    <td><?php echo substr($news['title'], 0, 40) . (strlen($news['title']) > 40 ? '...' : ''); ?></td>
                                                    <td><?php echo date('M j, Y', strtotime($news['created_at'])); ?></td>
                                                    <td><span class="badge bg-success">Published</span></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Popular Routes -->
                        <div class="col-lg-6">
                            <div class="dashboard-card">
                                <div class="card-header bg-white border-bottom-0 py-3">
                                    <h5 class="mb-0">Popular Bus Routes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Route</th>
                                                    <th>Trips This Month</th>
                                                    <th>Trend</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($popular_routes as $route): ?>
                                                <tr>
                                                    <td><?php echo $route['route']; ?></td>
                                                    <td><?php echo $route['trips']; ?></td>
                                                    <td>
                                                        <span class="text-success">
                                                            <i class="fas fa-arrow-up me-1"></i> 12%
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('d-none');
            document.querySelector('.main-content').classList.toggle('expanded');
        });
        
        // Initialize traffic chart
        const ctx = document.getElementById('trafficChart').getContext('2d');
        const trafficChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Website Visitors',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 30000, 40000, 38000, 45000],
                    borderColor: '#1f2b6c',
                    backgroundColor: 'rgba(31, 43, 108, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Mobile App Users',
                    data: [5000, 7000, 10000, 12000, 15000, 18000, 20000, 25000, 28000, 32000, 35000, 40000],
                    borderColor: '#27c840',
                    backgroundColor: 'rgba(39, 200, 64, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>