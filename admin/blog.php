<?php
include_once 'helpers/blog_helper.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_slug = $_GET['category'] ?? '';
$limit = 9;
$offset = ($page - 1) * $limit;

// Get featured posts for the hero section
$featured_posts = getBlogPosts(1, null, true);
$latest_posts = getBlogPosts(4);

// Get posts for main listing
if ($category_slug) {
    $all_posts = getBlogPosts(null, $category_slug);
} else {
    $all_posts = getBlogPosts();
}

$total_posts = count($all_posts);
$total_pages = ceil($total_posts / $limit);
$posts = array_slice($all_posts, $offset, $limit);

$categories = getBlogCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content -->
    <title>Blog - Enugu Smart Bus</title>
</head>
<body class="font-inter text-gray-800 overflow-x-hidden relative">
    <!-- Header (include your existing header) -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center mt-0 py-[60px] overflow-hidden z-[1] hero-section" style="
        background-image: url('assets/hero-banner.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
      ">
        <div class="absolute top-0 left-0 right-0 bottom-0 w-full h-full bg-[#00131ac4] hero-overlay"></div>
        <div class="max-w-[1200px] mx-auto px-5 w-full relative z-[1]">
            <div class="flex items-center justify-center text-white flex-col">
                <?php if (!empty($featured_posts)): ?>
                    <h2 class="font-inter text-center font-semibold text-[36px] leading-[65px] tracking-[-0.02em] align-middle mb-5 hero-title">
                        <?php echo htmlspecialchars($featured_posts[0]['title']); ?>
                    </h2>
                    <p class="font-inter text-center font-light text-2xl leading-[39px] tracking-[-0.02em] align-middle mb-[50px] hero-subtitle">
                        <?php echo htmlspecialchars($featured_posts[0]['excerpt']); ?>
                    </p>
                    <a href="blog-single.php?slug=<?php echo $featured_posts[0]['slug']; ?>" class="btn btn-primary">
                        Read More
                    </a>
                <?php else: ?>
                    <h2 class="font-inter text-center font-semibold text-[36px] leading-[65px] tracking-[-0.02em] align-middle mb-5 hero-title">
                        Insights, Updates & Smart Mobility Stories
                    </h2>
                    <p class="font-inter text-center font-light text-2xl leading-[39px] tracking-[-0.02em] align-middle mb-[50px] hero-subtitle">
                        Follow the journey as Enugu Smart Bus builds a safer, smarter and more sustainable transport system.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Topics Section -->
    <section class="container mx-auto max-w-6xl p-10 flex flex-col gap-5">
        <p class="text-xl font-bold">Featured Topics</p>
        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Main Featured Post -->
            <?php if (!empty($featured_posts)): ?>
            <div class="border rounded-2xl p-5 flex flex-col gap-4">
                <div class="">
                    <?php if ($featured_posts[0]['featured_image']): ?>
                        <img src="<?php echo $database->getFileUrl($featured_posts[0]['featured_image']); ?>" alt="<?php echo htmlspecialchars($featured_posts[0]['title']); ?>" class="w-full h-64 object-cover rounded-lg" />
                    <?php else: ?>
                        <img src="assets/lead-post-img.png" alt="Featured Post" class="w-full h-64 object-cover rounded-lg" />
                    <?php endif; ?>
                </div>
                <div class="flex flex-col gap-2">
                    <div class="flex text-sm text-gray-400">
                        <span><?php echo date('M jS, Y', strtotime($featured_posts[0]['published_at'])); ?></span>
                        <span>•</span>
                        <span><?php echo formatReadTime($featured_posts[0]['read_time']); ?></span>
                    </div>
                    <p class="text-lg font-semibold">
                        <?php echo htmlspecialchars($featured_posts[0]['title']); ?>
                    </p>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($featured_posts[0]['excerpt']); ?>
                    </p>
                    <a href="blog-single.php?slug=<?php echo $featured_posts[0]['slug']; ?>" class="text-primary-blue font-semibold mt-2">
                        Read More →
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Latest Posts Sidebar -->
            <div class="flex flex-col gap-5">
                <?php foreach ($latest_posts as $index => $post): ?>
                <div class="flex gap-5 items-center border rounded-2xl p-4 hover:shadow-lg transition-shadow">
                    <div class="flex-shrink-0">
                        <?php if ($post['featured_image']): ?>
                            <img src="<?php echo $database->getFileUrl($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-20 h-20 object-cover rounded-lg" />
                        <?php else: ?>
                            <img src="assets/blog-img-<?php echo $index + 1; ?>.png" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-20 h-20 object-cover rounded-lg" />
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col gap-2 flex-grow">
                        <div class="flex text-xs text-gray-400">
                            <span><?php echo date('M j, Y', strtotime($post['published_at'])); ?></span>
                            <span>•</span>
                            <span><?php echo formatReadTime($post['read_time']); ?></span>
                        </div>
                        <p class="text-sm font-medium">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </p>
                        <a href="blog-single.php?slug=<?php echo $post['slug']; ?>" class="text-primary-blue text-xs font-semibold mt-1">
                            Read More →
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Articles Section -->
    <section class="relative container mx-auto max-w-6xl p-10 flex flex-col gap-5">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <p class="text-xl font-bold">
                <?php echo $category_slug ? 'Category: ' . htmlspecialchars($category_slug) : 'All Articles'; ?>
            </p>
            
            <!-- Category Filter -->
            <div class="flex gap-4 items-center">
                <select class="rounded-full border p-3 bg-transparent" onchange="window.location.href=this.value">
                    <option value="blog.php">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="blog.php?category=<?php echo $category['slug']; ?>" <?php echo $category_slug == $category['slug'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($posts)): ?>
                <div class="col-span-3 text-center py-10">
                    <p class="text-gray-500 text-lg">No articles found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <div class="flex flex-col gap-5 bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow p-4">
                    <?php if ($post['featured_image']): ?>
                        <img src="<?php echo $database->getFileUrl($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-48 object-cover rounded-lg" />
                    <?php else: ?>
                        <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-newspaper text-gray-400 text-4xl"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex flex-col gap-3 flex-grow">
                        <div class="flex text-xs text-gray-400">
                            <span><?php echo date('M j, Y', strtotime($post['published_at'])); ?></span>
                            <span>•</span>
                            <span><?php echo formatReadTime($post['read_time']); ?></span>
                            <?php if ($post['category_name']): ?>
                                <span>•</span>
                                <span class="text-primary-blue"><?php echo htmlspecialchars($post['category_name']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-800 leading-tight">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h3>
                        
                        <p class="text-gray-600 text-sm leading-relaxed flex-grow">
                            <?php echo htmlspecialchars($post['excerpt']); ?>
                        </p>
                        
                        <a href="blog-single.php?slug=<?php echo $post['slug']; ?>" class="text-primary-blue font-semibold text-sm mt-2 inline-flex items-center gap-2">
                            Read More <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="flex justify-center mt-8">
            <nav class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="blog.php?page=<?php echo $page - 1; ?><?php echo $category_slug ? '&category=' . urlencode($category_slug) : ''; ?>" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="blog.php?page=<?php echo $i; ?><?php echo $category_slug ? '&category=' . urlencode($category_slug) : ''; ?>" class="px-4 py-2 border rounded-lg <?php echo $i == $page ? 'bg-primary-blue text-white' : 'hover:bg-gray-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="blog.php?page=<?php echo $page + 1; ?><?php echo $category_slug ? '&category=' . urlencode($category_slug) : ''; ?>" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Next
                    </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </section>

    <!-- Newsletter Section (keep your existing newsletter) -->
    
    <!-- Footer (include your existing footer) -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>