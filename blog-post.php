<?php
// Blog post page
include_once 'config/database.php';
include_once 'blog_helper.php';

$database = new Database();
$db = $database->getConnection();

// Get the post ID from URL
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the specific blog post
$post = getBlogPost($db, $post_id);

// If post not found or not published, redirect to blog page
if (!$post) {
    header("Location: blog.php");
    exit();
}

// Get related posts (posts from same category or latest posts)
$related_posts = getRelatedPosts($db, $post_id, $post['category_id'] ?? null, 3);

// Get settings
include_once 'settings_helper.php';
$settings = getSiteSettings();

// Update view count (optional - you can add this functionality later)
// updatePostViews($db, $post_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($post['title']); ?> - Enugu Smart Bus System</title>
  <meta name="description" content="<?php echo htmlspecialchars($post['meta_description'] ?? $post['excerpt'] ?? ''); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($post['meta_keywords'] ?? ''); ?>">
  
  <script src="https://cdn.tailwindcss.com/"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            "primary-blue": "#1f2b6c",
            "primary-green": "#27c840",
            "dark-green": "#22b038",
            "light-green": "#0f9918",
            "dark-blue": "#001447",
            overlay: "#00131ac4",
          },
          fontFamily: {
            inter: ["Inter", "sans-serif"],
            poppins: ["Poppins", "sans-serif"],
          },
        },
      },
    };
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="custom.css" />
  <style>
    .blog-content {
      font-family: 'Inter', sans-serif;
    }
    .blog-content h2 {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      color: #1f2937;
      margin-top: 2rem;
      margin-bottom: 1rem;
      font-size: 1.5rem;
    }
    .blog-content h3 {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      color: #374151;
      margin-top: 1.5rem;
      margin-bottom: 0.75rem;
      font-size: 1.25rem;
    }
    .blog-content p {
      margin-bottom: 1rem;
      line-height: 1.7;
      color: #4b5563;
    }
    .blog-content ul, .blog-content ol {
      margin-bottom: 1rem;
      padding-left: 1.5rem;
    }
    .blog-content li {
      margin-bottom: 0.5rem;
      line-height: 1.6;
    }
    .blog-content strong {
      font-weight: 600;
      color: #374151;
    }
    .blog-content em {
      font-style: italic;
      color: #6b7280;
    }
    .share-buttons a {
      transition: all 0.3s ease;
    }
    .share-buttons a:hover {
      transform: translateY(-2px);
    }
  </style>
</head>

<body class="font-inter text-gray-800 overflow-x-hidden relative">
 <?php include 'navbar.php'; ?>

  <!-- Blog Post Hero Section -->
  <section class="relative min-h-[50vh] flex items-center mt-0 py-[60px] overflow-hidden z-[1] bg-primary-blue">
    <div class="absolute top-0 left-0 right-0 bottom-0 w-full h-full bg-[#00131ac4]"></div>
    <div class="max-w-[1200px] mx-auto px-5 w-full relative z-[1]">
      <div class="max-w-4xl mx-auto text-center text-white">
        <!-- Breadcrumb -->
        <nav class="flex justify-center mb-6" aria-label="Breadcrumb">
          <ol class="flex items-center space-x-2 text-sm">
            <li><a href="index.php" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
            <li class="flex items-center">
              <i class="fas fa-chevron-right text-xs text-gray-400 mx-2"></i>
              <a href="blog.php" class="text-gray-300 hover:text-white transition-colors">Blog</a>
            </li>
            <li class="flex items-center">
              <i class="fas fa-chevron-right text-xs text-gray-400 mx-2"></i>
              <span class="text-white">Article</span>
            </li>
          </ol>
        </nav>
        
        <h1 class="font-inter text-center font-bold text-[42px] leading-[1.2] tracking-[-0.02em] mb-6">
          <?php echo htmlspecialchars($post['title']); ?>
        </h1>
        
        <div class="flex flex-wrap items-center justify-center gap-6 text-lg">
          <div class="flex items-center gap-2">
            <i class="far fa-calendar text-primary-green"></i>
            <span><?php echo date('F j, Y', strtotime($post['published_at'])); ?></span>
          </div>
          <div class="flex items-center gap-2">
            <i class="far fa-clock text-primary-green"></i>
            <span><?php echo $post['read_time'] ?? '5'; ?> min read</span>
          </div>
          <?php if ($post['is_featured']): ?>
            <div class="flex items-center gap-2">
              <i class="fas fa-star text-primary-green"></i>
              <span class="bg-primary-green text-white px-3 py-1 rounded-full text-sm font-medium">Featured</span>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog Content Section -->
  <section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-5">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <!-- Main Content -->
        <div class="lg:col-span-8">
          <!-- Featured Image -->
          <div class="mb-8 rounded-2xl overflow-hidden shadow-lg">
            <img src="<?php echo getImagePath($post['featured_image'], 'assets/lead-post-img.png'); ?>" 
                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                 class="w-full h-auto object-cover"
                 onerror="this.src='assets/lead-post-img.png'">
          </div>

          <!-- Article Content -->
          <article class="blog-content prose prose-lg max-w-none">
            <?php 
            // Output the content - it's already HTML from your admin
            echo $post['content']; 
            ?>
          </article>

          <!-- Tags and Share -->
          <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-4">
              <!-- Tags -->
              <?php if ($post['meta_keywords']): ?>
                <div class="flex items-center gap-2">
                  <span class="text-gray-600 font-medium"><i class="fas fa-tags mr-2"></i>Tags:</span>
                  <div class="flex flex-wrap gap-2">
                    <?php 
                    $keywords = explode(',', $post['meta_keywords']);
                    foreach ($keywords as $keyword): 
                      if (trim($keyword)):
                    ?>
                      <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm"><?php echo htmlspecialchars(trim($keyword)); ?></span>
                    <?php 
                      endif;
                    endforeach; 
                    ?>
                  </div>
                </div>
              <?php endif; ?>

              <!-- Share Buttons -->
              <div class="share-buttons flex items-center gap-3">
                <span class="text-gray-600 font-medium">Share:</span>
                <?php
                $share_url = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                $share_title = urlencode($post['title']);
                ?>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" 
                   target="_blank" 
                   class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>" 
                   target="_blank" 
                   class="w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" 
                   target="_blank" 
                   class="w-10 h-10 bg-blue-800 text-white rounded-full flex items-center justify-center hover:bg-blue-900">
                  <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="whatsapp://send?text=<?php echo $share_title . ' ' . $share_url; ?>" 
                   class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600">
                  <i class="fab fa-whatsapp"></i>
                </a>
              </div>
            </div>
          </div>

          <!-- Author Bio (Optional - you can expand this later) -->
          <!-- <div class="mt-8 p-6 bg-gray-50 rounded-2xl">
            <div class="flex items-center gap-4">
              <img src="assets/author-placeholder.jpg" alt="Author" class="w-16 h-16 rounded-full">
              <div>
                <h4 class="font-semibold text-lg">Author Name</h4>
                <p class="text-gray-600">Short bio about the author goes here. This can be expanded with user profiles later.</p>
              </div>
            </div>
          </div> -->
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4">
          <!-- Related Posts -->
          <?php if (!empty($related_posts)): ?>
            <div class="bg-gray-50 rounded-2xl p-6 mb-8">
              <h3 class="font-poppins font-semibold text-xl text-gray-800 mb-4">Related Articles</h3>
              <div class="space-y-4">
                <?php foreach ($related_posts as $related_post): ?>
                  <a href="blog-post.php?id=<?php echo $related_post['id']; ?>" 
                     class="block p-4 bg-white rounded-xl hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-start gap-3">
                      <img src="<?php echo getImagePath($related_post['featured_image'], 'assets/default-blog.png'); ?>" 
                           alt="<?php echo htmlspecialchars($related_post['title']); ?>" 
                           class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                      <div class="flex-1 min-w-0">
                        <h4 class="font-medium text-sm text-gray-800 line-clamp-2 mb-1">
                          <?php echo htmlspecialchars($related_post['title']); ?>
                        </h4>
                        <div class="flex items-center text-xs text-gray-500">
                          <span><?php echo date('M j, Y', strtotime($related_post['published_at'])); ?></span>
                        </div>
                      </div>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Newsletter Signup -->
          <div class="bg-primary-blue text-white rounded-2xl p-6">
            <h3 class="font-poppins font-semibold text-xl mb-3">Stay Updated</h3>
            <p class="text-blue-100 mb-4">Get the latest news and updates from Enugu Smart Bus directly in your inbox.</p>
            <form class="space-y-3">
              <input type="email" 
                     placeholder="Enter your email" 
                     class="w-full px-4 py-3 rounded-lg bg-white text-gray-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-green">
              <button type="submit" 
                      class="w-full bg-primary-green text-white font-medium py-3 rounded-lg hover:bg-dark-green transition-colors duration-300">
                Subscribe
              </button>
            </form>
          </div>

          <!-- Quick Links -->
          <div class="mt-8 bg-white border border-gray-200 rounded-2xl p-6">
            <h3 class="font-poppins font-semibold text-xl text-gray-800 mb-4">Quick Links</h3>
            <div class="space-y-3">
              <a href="blog.php" class="flex items-center gap-3 text-gray-600 hover:text-primary-blue transition-colors">
                <i class="fas fa-newspaper text-primary-green w-5"></i>
                <span>All Articles</span>
              </a>
              <a href="index.php#routes" class="flex items-center gap-3 text-gray-600 hover:text-primary-blue transition-colors">
                <i class="fas fa-route text-primary-green w-5"></i>
                <span>Bus Routes</span>
              </a>
              <a href="index.php#fares" class="flex items-center gap-3 text-gray-600 hover:text-primary-blue transition-colors">
                <i class="fas fa-tag text-primary-green w-5"></i>
                <span>Fare Information</span>
              </a>
              <a href="contact.php" class="flex items-center gap-3 text-gray-600 hover:text-primary-blue transition-colors">
                <i class="fas fa-headset text-primary-green w-5"></i>
                <span>Contact Support</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- More Articles Section -->
  <?php if (!empty($related_posts)): ?>
  <section class="py-16 bg-gray-50">
    <div class="max-w-6xl mx-auto px-5">
      <div class="text-center mb-12">
        <h2 class="font-inter font-bold text-[32px] text-gray-800 mb-4">You Might Also Like</h2>
        <p class="font-inter text-lg text-gray-600 max-w-2xl mx-auto">Discover more insightful articles from Enugu Smart Bus</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($related_posts as $related_post): ?>
          <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
            <img src="<?php echo getImagePath($related_post['featured_image'], 'assets/default-blog.png'); ?>" 
                 alt="<?php echo htmlspecialchars($related_post['title']); ?>" 
                 class="w-full h-48 object-cover">
            <div class="p-6">
              <div class="flex items-center text-sm text-gray-500 mb-3">
                <span><?php echo date('M j, Y', strtotime($related_post['published_at'])); ?></span>
                <span class="mx-2">•</span>
                <span><?php echo $related_post['read_time'] ?? '5'; ?> min read</span>
              </div>
              <h3 class="font-poppins font-semibold text-xl text-gray-800 mb-3 line-clamp-2">
                <?php echo htmlspecialchars($related_post['title']); ?>
              </h3>
              <p class="font-inter text-gray-600 mb-4 line-clamp-3">
                <?php 
                $excerpt = $related_post['excerpt'] ?? substr(strip_tags($related_post['content'] ?? ''), 0, 100);
                echo htmlspecialchars($excerpt . (strlen($excerpt) >= 100 ? '...' : ''));
                ?>
              </p>
              <a href="blog-post.php?id=<?php echo $related_post['id']; ?>" 
                 class="text-primary-blue font-semibold inline-flex items-center gap-2 hover:gap-3 transition-all duration-300">
                Read More <i class="fas fa-arrow-right text-xs"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php include 'footer.php'; ?>

  <script>
    // Add smooth scrolling for anchor links within the article
    document.addEventListener('DOMContentLoaded', function() {
      const articleLinks = document.querySelectorAll('.blog-content a[href^="#"]');
      articleLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          const targetId = this.getAttribute('href').substring(1);
          const targetElement = document.getElementById(targetId);
          if (targetElement) {
            targetElement.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });

      // Add reading progress indicator
      const progressBar = document.createElement('div');
      progressBar.className = 'fixed top-0 left-0 w-0 h-1 bg-primary-green z-50 transition-all duration-100';
      document.body.appendChild(progressBar);

      window.addEventListener('scroll', function() {
        const winHeight = window.innerHeight;
        const docHeight = document.documentElement.scrollHeight;
        const scrollTop = window.pageYOffset;
        const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
        progressBar.style.width = scrollPercent + '%';
      });
    });
  </script>
</body>
</html>