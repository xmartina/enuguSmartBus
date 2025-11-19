<?php
// Get current settings
try {
    $stmt = $db->query("SELECT * FROM site_settings WHERE id=1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$settings) {
        throw new Exception("No settings found");
    }
} catch (Exception $e) {
    $settings = [];
    $_SESSION['error_message'] = "Error loading settings: " . $e->getMessage();
}

// Debug current logo
$logo_debug = "";
if ($settings['logo']) {
    $logo_path = $database->getFilePath($settings['logo']);
    $logo_url = $database->getFileUrl($settings['logo']);
    $logo_exists = file_exists($logo_path);
    
    $logo_debug = "Logo debug - Path: $logo_path, URL: $logo_url, Exists: " . ($logo_exists ? 'Yes' : 'No');
}
?>

<style>
    :root {
        --sidebar-width: 280px;
        --sidebar-collapsed: 80px;
        --primary-blue: #1f2b6c;
        --primary-green: #27c840;
        --dark-blue: #001447;
        --sidebar-transition: all 0.3s ease;
    }
    
    .sidebar-wrapper {
        width: var(--sidebar-width);
        min-height: 100vh;
        background: linear-gradient(180deg, var(--dark-blue) 0%, var(--primary-blue) 100%);
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        transition: var(--sidebar-transition);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    .sidebar-wrapper.collapsed {
        width: var(--sidebar-collapsed);
    }
    
    .sidebar-header {
        padding: 1.5rem 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
        transition: var(--sidebar-transition);
    }
    
    .sidebar-wrapper.collapsed .sidebar-header {
        padding: 1rem 0.5rem;
    }
    
    .sidebar-logo {
        max-height: 60px;
        transition: var(--sidebar-transition);
    }
    
    .sidebar-wrapper.collapsed .sidebar-logo {
        max-height: 40px;
    }
    
    .sidebar-title {
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0.75rem 0 0.25rem 0;
        transition: var(--sidebar-transition);
        opacity: 1;
    }
    
    .sidebar-wrapper.collapsed .sidebar-title {
        opacity: 0;
        height: 0;
        margin: 0;
        overflow: hidden;
    }
    
    .sidebar-subtitle {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.8rem;
        transition: var(--sidebar-transition);
        opacity: 1;
    }
    
    .sidebar-wrapper.collapsed .sidebar-subtitle {
        opacity: 0;
        height: 0;
        overflow: hidden;
    }
    
    .sidebar-content {
        padding: 1rem 0;
    }
    
    .nav-item {
        margin-bottom: 0.25rem;
    }
    
    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 0.875rem 1.25rem;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        position: relative;
    }
    
    .nav-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        border-left-color: var(--primary-green);
    }
    
    .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.15);
        border-left-color: var(--primary-green);
        font-weight: 500;
    }
    
    .nav-icon {
        width: 20px;
        text-align: center;
        margin-right: 0.75rem;
        font-size: 1.1rem;
        transition: var(--sidebar-transition);
    }
    
    .sidebar-wrapper.collapsed .nav-icon {
        margin-right: 0;
    }
    
    .nav-text {
        transition: var(--sidebar-transition);
        opacity: 1;
        white-space: nowrap;
    }
    
    .sidebar-wrapper.collapsed .nav-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }
    
    .nav-dropdown-toggle::after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        border: none;
        margin-left: auto;
        transition: transform 0.3s ease;
    }
    
    .nav-dropdown-toggle[aria-expanded="true"]::after {
        transform: rotate(180deg);
    }
    
    .sidebar-wrapper.collapsed .nav-dropdown-toggle::after {
        display: none;
    }
    
    .nav-dropdown-menu {
        background: rgba(0, 0, 0, 0.2);
        border: none;
        padding: 0;
        margin: 0;
    }
    
    .nav-dropdown-item {
        color: rgba(255, 255, 255, 0.7);
        padding: 0.75rem 1.25rem 0.75rem 3rem;
        text-decoration: none;
        display: block;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        font-size: 0.9rem;
    }
    
    .nav-dropdown-item:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
        border-left-color: var(--primary-green);
    }
    
    .nav-dropdown-item.active {
        color: white;
        background: rgba(255, 255, 255, 0.15);
        border-left-color: var(--primary-green);
        font-weight: 500;
    }
    
    .sidebar-wrapper.collapsed .nav-dropdown-menu {
        display: none !important;
    }
    
    .badge-sidebar {
        background: var(--primary-green);
        color: white;
        border-radius: 10px;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        margin-left: auto;
    }
    
    .sidebar-wrapper.collapsed .badge-sidebar {
        display: none;
    }
    
    .toggle-sidebar {
        position: absolute;
        top: 1rem;
        right: -12px;
        background: var(--primary-blue);
        border: 2px solid white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        cursor: pointer;
        z-index: 1001;
        transition: var(--sidebar-transition);
    }
    
    .toggle-sidebar:hover {
        background: var(--primary-green);
    }
    
    /* Main content adjustment */
    .main-content {
        margin-left: var(--sidebar-width);
        transition: var(--sidebar-transition);
    }
    
    .main-content.expanded {
        margin-left: var(--sidebar-collapsed);
    }
    
    /* Scrollbar styling */
    .sidebar-wrapper::-webkit-scrollbar {
        width: 4px;
    }
    
    .sidebar-wrapper::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }
    
    .sidebar-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .sidebar-wrapper {
            transform: translateX(-100%);
            width: var(--sidebar-width);
        }
        
        .sidebar-wrapper.mobile-open {
            transform: translateX(0);
        }
        
        .main-content {
            margin-left: 0;
        }
        
        .toggle-sidebar {
            display: none;
        }
    }
</style>

<!-- Modern Sidebar -->
<div class="sidebar-wrapper" id="sidebar">
    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-chevron-left"></i>
    </button>
    
    <div class="sidebar-header">
        <?php if ($settings['logo']): ?>
            <img src="<?php echo $database->getFileUrl($settings['logo']); ?>" alt="Enugu Smart Bus" class="sidebar-logo">
        <?php else: ?>
            <div class="bg-light rounded p-2 d-inline-flex align-items-center justify-content-center">
                <i class="fas fa-bus fa-lg text-primary"></i>
            </div>
        <?php endif; ?>
        <h5 class="sidebar-title">Enugu Smart Bus CMS</h5>
        <p class="sidebar-subtitle">Welcome, <?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></p>
    </div>
    
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- Site Settings -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="nav-text">Site Settings</span>
                </a>
            </li>

            <!-- Home Page Sections with Dropdown -->
            <li class="nav-item">
                <a class="nav-link nav-dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['hero_sections.php', 'about_section.php', 'how_it_works.php', 'app_section.php', 'testimonials.php', 'newsletter.php']) ? 'active' : ''; ?>" 
                   data-bs-toggle="collapse" href="#homePageCollapse" role="button">
                    <i class="fas fa-home nav-icon"></i>
                    <span class="nav-text">Home Page Sections</span>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['hero_sections.php', 'about_section.php', 'how_it_works.php', 'app_section.php', 'testimonials.php', 'newsletter.php']) ? 'show' : ''; ?>" id="homePageCollapse">
                    <div class="nav-dropdown-menu">
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'hero_sections.php' ? 'active' : ''; ?>" href="hero_sections.php">
                            Hero Section
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'about_section.php' ? 'active' : ''; ?>" href="about_section.php">
                            About Us Section
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'how_it_works.php' ? 'active' : ''; ?>" href="how_it_works.php">
                            How It Works Section
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'app_section.php' ? 'active' : ''; ?>" href="app_section.php">
                            Download App Section
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'testimonials.php' ? 'active' : ''; ?>" href="testimonials.php">
                            Customer Testimonials
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? 'active' : ''; ?>" href="newsletter.php">
                            Newsletter Section
                        </a>
                    </div>
                </div>
            </li>

            <!-- About Page -->
            <li class="nav-item">
                <a class="nav-link nav-dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['about.php', 'team.php', 'mission.php', 'history.php', 'values.php']) ? 'active' : ''; ?>" 
                   data-bs-toggle="collapse" href="#aboutPageCollapse" role="button">
                    <i class="fas fa-info-circle nav-icon"></i>
                    <span class="nav-text">About Page</span>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['about.php', 'team.php', 'mission.php', 'history.php', 'values.php']) ? 'show' : ''; ?>" id="aboutPageCollapse">
                    <div class="nav-dropdown-menu">
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">
                            About Content
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'team.php' ? 'active' : ''; ?>" href="team.php">
                            Our Team
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'mission.php' ? 'active' : ''; ?>" href="mission.php">
                            Mission & Vision
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>" href="history.php">
                            Our History
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'values.php' ? 'active' : ''; ?>" href="values.php">
                            Our Values
                        </a>
                    </div>
                </div>
            </li>

            <!-- Services Page -->
            <li class="nav-item">
                <a class="nav-link nav-dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['services.php', 'routes.php', 'fares.php', 'schedule.php', 'benefits.php']) ? 'active' : ''; ?>" 
                   data-bs-toggle="collapse" href="#servicesPageCollapse" role="button">
                    <i class="fas fa-concierge-bell nav-icon"></i>
                    <span class="nav-text">Services Page</span>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['services.php', 'routes.php', 'fares.php', 'schedule.php', 'benefits.php']) ? 'show' : ''; ?>" id="servicesPageCollapse">
                    <div class="nav-dropdown-menu">
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" href="services.php">
                            Services Overview
                        </a>
                        <!-- <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'routes.php' ? 'active' : ''; ?>" href="routes.php">
                            Bus Routes
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'fares.php' ? 'active' : ''; ?>" href="fares.php">
                            Fares & Pricing
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'schedule.php' ? 'active' : ''; ?>" href="schedule.php">
                            Schedule & Timings
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'benefits.php' ? 'active' : ''; ?>" href="benefits.php">
                            Benefits & Features
                        </a> -->
                    </div>
                </div>
            </li>

            <!-- Blog Page -->
            <li class="nav-item">
                <a class="nav-link nav-dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['blog_posts.php', 'blog_categories.php', 'tags.php', 'comments.php']) ? 'active' : ''; ?>" 
                   data-bs-toggle="collapse" href="#blogPageCollapse" role="button">
                    <i class="fas fa-newspaper nav-icon"></i>
                    <span class="nav-text">Blog Page</span>
                    <span class="badge-sidebar"><?php echo $news_count ?? 0; ?></span>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['blog_posts.php', 'blog_categories.php', 'tags.php', 'comments.php']) ? 'show' : ''; ?>" id="blogPageCollapse">
                    <div class="nav-dropdown-menu">
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'blog_posts.php' ? 'active' : ''; ?>" href="blog_posts.php">
                            Blog Posts
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'blog_categories.php' ? 'active' : ''; ?>" href="blog_categories.php">
                            Categories
                        </a>
                       <!--  <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'tags.php' ? 'active' : ''; ?>" href="tags.php">
                            Tags
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'comments.php' ? 'active' : ''; ?>" href="comments.php">
                            Comments
                        </a> -->
                    </div>
                </div>
            </li>

            <!-- Contact Page -->
            <li class="nav-item">
                <a class="nav-link nav-dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['contact_info.php', 'contact_form.php', 'locations.php']) ? 'active' : ''; ?>" 
                   data-bs-toggle="collapse" href="#contactPageCollapse" role="button">
                    <i class="fas fa-address-book nav-icon"></i>
                    <span class="nav-text">Contact Page</span>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['contact_info.php', 'contact_form.php', 'locations.php']) ? 'show' : ''; ?>" id="contactPageCollapse">
                    <div class="nav-dropdown-menu">
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'contact_info.php' ? 'active' : ''; ?>" href="contact_info.php">
                            Contact Information
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'contact_form.php' ? 'active' : ''; ?>" href="contact_form.php">
                            Contact Form
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'locations.php' ? 'active' : ''; ?>" href="locations.php">
                            Office Locations
                        </a>
                    </div>
                </div>
            </li>

            <!-- Marketing -->
           <!--  <li class="nav-item">
                <a class="nav-link nav-dropdown-toggle <?php echo in_array(basename($_SERVER['PHP_SELF']), ['newsletter.php', 'promotions.php', 'subscribers.php']) ? 'active' : ''; ?>" 
                   data-bs-toggle="collapse" href="#marketingCollapse" role="button">
                    <i class="fas fa-bullhorn nav-icon"></i>
                    <span class="nav-text">Marketing</span>
                    <span class="badge-sidebar"><?php echo $subscribers ?? 0; ?></span>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['newsletter.php', 'promotions.php', 'subscribers.php']) ? 'show' : ''; ?>" id="marketingCollapse">
                    <div class="nav-dropdown-menu">
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? 'active' : ''; ?>" href="newsletter.php">
                            Newsletter Subscribers
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'promotions.php' ? 'active' : ''; ?>" href="promotions.php">
                            Promotions & Offers
                        </a>
                        <a class="nav-dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'subscribers.php' ? 'active' : ''; ?>" href="subscribers.php">
                            Subscriber Management
                        </a>
                    </div>
                </div>
            </li> -->

            <!-- Logout -->
            <li class="nav-item mt-4">
                <a class="nav-link text-warning" href="logout.php">
                    <i class="fas fa-sign-out-alt nav-icon"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('toggleSidebar');
    const mainContent = document.querySelector('.main-content');
    
    // Toggle sidebar
    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        // Rotate the toggle icon
        const icon = this.querySelector('i');
        if (sidebar.classList.contains('collapsed')) {
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
        } else {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
        }
        
        // Save state to localStorage
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Load saved state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        toggleButton.querySelector('i').classList.replace('fa-chevron-left', 'fa-chevron-right');
    }
    
    // Auto-collapse on mobile
    if (window.innerWidth < 768) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
    }
    
    // Handle dropdown active states
    const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            // Close other dropdowns when one is opened
            if (!this.classList.contains('active')) {
                dropdownToggles.forEach(otherToggle => {
                    if (otherToggle !== this) {
                        otherToggle.classList.remove('active');
                        const targetId = otherToggle.getAttribute('href');
                        const target = document.querySelector(targetId);
                        if (target) {
                            target.classList.remove('show');
                        }
                    }
                });
            }
            
            this.classList.toggle('active');
        });
    });
});
</script>