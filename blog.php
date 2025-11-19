<?php
// Blog page
include_once 'config/database.php';
include_once 'blog_helper.php';

$database = new Database();
$db = $database->getConnection();

// Get featured posts - PASS THE DATABASE CONNECTION
$featured_posts = getFeaturedPosts($db, 4);
// Get latest posts for the main articles section
$latest_posts = getBlogPosts($db, 6);

// Get settings
include_once 'settings_helper.php';
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<!-- Rest of your blog.php HTML -->
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Enugu Smart Bus System - Blog</title>
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
    .blog-image {
      object-fit: cover;
      width: 100%;
      height: 100%;
    }
    .clickable-card {
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .clickable-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
  </style>
</head>

<body class="font-inter text-gray-800 overflow-x-hidden relative">
 <?php include 'navbar.php'; ?>

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
        <h2 class="font-inter text-center font-semibold text-[36px] leading-[65px] tracking-[-0.02em] align-middle mb-5 hero-title">
          Insights, Updates & Smart Mobility Stories
        </h2>
        <p class="font-inter text-center font-light text-2xl leading-[39px] tracking-[-0.02em] align-middle mb-[50px] hero-subtitle">
          Follow the journey as Enugu Smart Bus builds a safer, smarter and
          more sustainable transport system for everyone in Enugu state.
        </p>
      </div>
    </div>
  </section>

  <!-- Featured Topics Section -->
  <section class="container mx-auto max-w-6xl p-10 flex flex-col gap-5">
    <p class="text-xl font-bold">Featured Topics</p>
    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-10">
      <?php if (!empty($featured_posts)): ?>
        <!-- Main featured post (left side) -->
        <div class="border rounded-2xl p-5 flex flex-col gap-4 clickable-card" onclick="window.location.href='blog-post.php?id=<?php echo $featured_posts[0]['id']; ?>'">
          <div class="h-64 overflow-hidden rounded-xl">
            <img src="<?php echo getImagePath($featured_posts[0]['featured_image'], 'assets/lead-post-img.png'); ?>" 
                 alt="<?php echo htmlspecialchars($featured_posts[0]['title']); ?>" 
                 class="blog-image w-full h-full" 
                 onerror="this.src='assets/lead-post-img.png'" />
          </div>
          <div class="flex flex-col gap-2">
            <div class="flex text-sm text-gray-400 gap-4">
              <span><?php echo date('M jS, Y', strtotime($featured_posts[0]['published_at'])); ?></span>
              <span><?php echo $featured_posts[0]['read_time'] ?? '5'; ?> mins read</span>
            </div>
            <p class="text-lg font-semibold">
              <?php echo htmlspecialchars($featured_posts[0]['title']); ?>
            </p>
            <p class="text-sm text-gray-600">
              <?php 
              $excerpt = $featured_posts[0]['excerpt'] ?? substr(strip_tags($featured_posts[0]['content'] ?? ''), 0, 150);
              echo htmlspecialchars($excerpt . (strlen($excerpt) >= 150 ? '...' : ''));
              ?>
            </p>
          </div>
        </div>

        <!-- Side featured posts (right side) -->
        <div class="flex flex-col gap-5">
          <?php for ($i = 1; $i < min(4, count($featured_posts)); $i++): ?>
            <div class="flex gap-5 items-center border rounded-2xl p-4 clickable-card" onclick="window.location.href='blog-post.php?id=<?php echo $featured_posts[$i]['id']; ?>'">
              <div class="flex-shrink-0 w-20 h-20 overflow-hidden rounded-lg">
                <img src="<?php echo getImagePath($featured_posts[$i]['featured_image'], 'assets/blog-img-' . $i . '.png'); ?>" 
                     alt="<?php echo htmlspecialchars($featured_posts[$i]['title']); ?>" 
                     class="blog-image w-full h-full"
                     onerror="this.src='assets/blog-img-<?php echo $i; ?>.png'" />
              </div>
              <div class="flex flex-col gap-2 flex-1">
                <div class="flex text-xs text-gray-400 gap-2">
                  <span><?php echo date('M jS, Y', strtotime($featured_posts[$i]['published_at'])); ?></span>
                  <span><?php echo $featured_posts[$i]['read_time'] ?? '5'; ?> mins read</span>
                </div>
                <p class="text-sm font-medium leading-tight">
                  <?php echo htmlspecialchars($featured_posts[$i]['title']); ?>
                </p>
              </div>
            </div>
          <?php endfor; ?>
          
          <!-- Fill empty slots if less than 3 featured posts -->
          <?php for ($i = count($featured_posts); $i < 4; $i++): ?>
            <div class="flex gap-5 items-center border rounded-2xl p-4 opacity-50">
              <div class="flex-shrink-0 w-20 h-20 overflow-hidden rounded-lg">
                <img src="assets/blog-img-<?php echo $i; ?>.png" alt="Coming soon" class="blog-image w-full h-full" />
              </div>
              <div class="flex flex-col gap-2 flex-1">
                <div class="flex text-xs text-gray-400 gap-2">
                  <span>Coming soon</span>
                  <span>0 mins read</span>
                </div>
                <p class="text-sm font-medium leading-tight">
                  More featured articles coming soon
                </p>
              </div>
            </div>
          <?php endfor; ?>
        </div>
      <?php else: ?>
        <!-- Default content when no featured posts -->
        <div class="border rounded-2xl p-5 flex flex-col gap-4">
          <div class="h-64 overflow-hidden rounded-xl">
            <img src="assets/lead-post-img.png" alt="Default featured post" class="blog-image w-full h-full" />
          </div>
          <div class="flex flex-col gap-2">
            <div class="flex text-sm text-gray-400 gap-4">
              <span>Jan 30th, 2025</span>
              <span>5 mins read</span>
            </div>
            <p class="text-lg font-semibold">
              Enugu residents rejoice as the Enugu Smart Bus Project Begins in Earnest
            </p>
            <p class="text-sm text-gray-600">
              Discover how the Enugu Smart Bus system is transforming transportation in the city with modern, efficient, and eco-friendly solutions.
            </p>
          </div>
        </div>

        <div class="flex flex-col gap-5">
          <?php 
          $default_posts = [
            [
              'title' => 'Empowering Students Through Real-World Learning. Inside the Enugu smart bus',
              'image' => 'blog-img-1.png'
            ],
            [
              'title' => 'Enugu Smart Bus Subsidizes rates for Enugu students',
              'image' => 'blog-img-2.png'
            ],
            [
              'title' => 'Get to work and be productive while you book Enugu smart bus',
              'image' => 'blog-img-3.png'
            ]
          ];
          ?>
          
          <?php foreach ($default_posts as $index => $post): ?>
            <div class="flex gap-5 items-center border rounded-2xl p-4">
              <div class="flex-shrink-0 w-20 h-20 overflow-hidden rounded-lg">
                <img src="assets/<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-image w-full h-full" />
              </div>
              <div class="flex flex-col gap-2 flex-1">
                <div class="flex text-xs text-gray-400 gap-2">
                  <span>Jan 30th, 2025</span>
                  <span>5 mins read</span>
                </div>
                <p class="text-sm font-medium leading-tight">
                  <?php echo htmlspecialchars($post['title']); ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- All Articles Section -->
  <section class="relative container mx-auto max-w-6xl p-10 flex flex-col gap-5">
    <div class="flex flex-wrap gap-4 items-center justify-between">
      <p class="text-xl font-bold">All Articles</p>
      <select class="rounded-full border p-3 bg-transparent">
        <option>Most Recent</option>
        <option>Most Popular</option>
        <option>Oldest First</option>
      </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php if (!empty($latest_posts)): ?>
        <?php foreach ($latest_posts as $index => $post): ?>
          <div class="flex flex-col gap-5 border rounded-xl p-4 clickable-card" onclick="window.location.href='blog-post.php?id=<?php echo $post['id']; ?>'">
            <div class="h-48 overflow-hidden rounded-xl">
              <img src="<?php echo getImagePath($post['featured_image'], 'assets/blog-img-' . ($index + 5) . '.png'); ?>" 
                   class="blog-image w-full h-full" 
                   alt="<?php echo htmlspecialchars($post['title']); ?>"
                   onerror="this.src='assets/blog-img-<?php echo ($index + 5); ?>.png'" />
            </div>
            <div class="flex flex-col gap-2 flex-1">
              <div class="flex text-xs text-gray-400 gap-4">
                <span><?php echo date('M jS, Y', strtotime($post['published_at'])); ?></span>
                <span><?php echo $post['read_time'] ?? '5'; ?> mins read</span>
              </div>
              <p class="text-sm font-semibold">
                <?php echo htmlspecialchars($post['title']); ?>
              </p>
              <p class="text-xs text-gray-600">
                <?php 
                $excerpt = $post['excerpt'] ?? substr(strip_tags($post['content'] ?? ''), 0, 100);
                echo htmlspecialchars($excerpt . (strlen($excerpt) >= 100 ? '...' : ''));
                ?>
              </p>
              <?php if ($post['is_featured']): ?>
                <span class="inline-block bg-primary-green text-white text-xs px-2 py-1 rounded-full w-fit mt-2">Featured</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Default articles when no posts -->
        <?php 
        $default_articles = [
          [
            'img' => 'blog-img-5.png',
            'title' => 'Environmental Impact: Smart Bus Fleet Reduces Carbon Emissions by 30% in Enugu',
            'date' => 'May 30th, 2025'
          ],
          [
            'img' => 'blog-img-6.png',
            'title' => 'Community Feedback Drives Improvements in Smart Bus Scheduling and Accessibility',
            'date' => 'May 30th, 2025'
          ],
          [
            'img' => 'blog-img-8.png',
            'title' => 'Smart Bus Mobile App Now Available for Real-Time Trip Planning and E-Ticketing',
            'date' => 'May 30th, 2025'
          ],
          [
            'img' => 'blog-img-9.png',
            'title' => 'Enugu Smart Bus Launches New Routes to Improve Citywide Connectivity',
            'date' => 'May 30th, 2025'
          ],
          [
            'img' => 'blog-img-10.png',
            'title' => 'Students and Workers Enjoy Discounted Fares with New Smart Bus Packages',
            'date' => 'May 30th, 2025'
          ],
          [
            'img' => 'blog-img-11.png',
            'title' => 'Enugu Smart Bus Partners with Tech Startups to Enhance Passenger Safety and Tracking',
            'date' => 'May 30th, 2025'
          ]
        ];
        ?>
        
        <?php foreach ($default_articles as $article): ?>
          <div class="flex flex-col gap-5">
            <div class="h-48 overflow-hidden rounded-xl">
              <img src="assets/<?php echo $article['img']; ?>" class="blog-image w-full h-full" alt="<?php echo htmlspecialchars($article['title']); ?>" />
            </div>
            <div class="flex flex-col gap-2">
              <div class="flex text-xs text-gray-400 gap-4">
                <span><?php echo $article['date']; ?></span>
                <span>5 mins read</span>
              </div>
              <p class="text-sm font-medium">
                <?php echo htmlspecialchars($article['title']); ?>
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <!-- Newsletter Section -->
  <section class="container mx-auto p-10 max-w-6xl">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-10">
      <div class="flex flex-col gap-5">
        <p class="text-3xl font-bold">
          Subscribe To Our Newsletter For News, Tips and Updates
        </p>
        <p class="text-sm">
          Subscribe to Enugu Smart Bus newsletter and stay up to date on our services, routes, and more.
        </p>
      </div>
      <div class="flex items-center">
        <div class="w-full border-2 border-primary-blue flex justify-between items-center rounded-2xl p-2">
          <input type="email" class="p-2 focus:outline-none flex-grow w-full bg-transparent" placeholder="Enter your email address" />
          <button class="flex-shrink w-[fit-content] p-3 bg-primary-blue rounded-xl text-white hover:bg-dark-blue transition-colors">
            Subscribe
          </button>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <script>
    // Add click handlers for all clickable cards
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.clickable-card');
      cards.forEach(card => {
        card.addEventListener('click', function() {
          window.location.href = this.getAttribute('onclick').match(/'(.*?)'/)[1];
        });
      });
    });
  </script>
</body>
</html>