<?php
// In your homepage index.php

include_once 'config/database.php';
include_once 'blog_helper.php';

$database = new Database();
$db = $database->getConnection();

// Get latest posts - PASS THE DATABASE CONNECTION
$latest_news = getBlogPosts($db, 3); // Get 3 latest posts

// Get settings
$stmt = $db->query("SELECT * FROM site_settings WHERE id=1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Get active hero sections
try {
    $stmt = $db->query("SELECT * FROM hero_sections WHERE is_active = 1 ORDER BY display_order, id LIMIT 1");
    $hero_section = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $hero_section = null;
}

include_once 'settings_helper.php';
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">
<!-- Rest of your homepage HTML -->
  <meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Enugu Smart Bus System</title>
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
    <link
      href="cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/custom.css" />
  </head>

  <!-- body section -->


  <style type="text/css">
    /* Mobile Responsive Fixes */
@media (max-width: 768px) {
    /* Hero Section Mobile Fixes */
    .hero-section {
        min-height: 70vh !important;
        padding: 40px 0 !important;
    }
    
    .hero-content h1 {
        font-size: 28px !important;
        line-height: 1.3 !important;
    }
    
    .hero-content .hero-title {
        font-size: 32px !important;
        line-height: 1.2 !important;
    }
    
    .hero-subtitle {
        font-size: 18px !important;
        line-height: 1.5 !important;
        margin-bottom: 30px !important;
    }
    
    .hero-button {
        width: 200px !important;
        height: 50px !important;
        font-size: 18px !important;
    }
    
    /* About Section Mobile Fixes */
    .how-it-works-section {
        height: auto !important;
        padding: 60px 0 !important;
    }
    
    .how-it-works-content {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    
    .how-it-works-right {
        padding-left: 0 !important;
        text-align: center !important;
    }
    
    .how-it-works-title {
        font-size: 28px !important;
        line-height: 1.3 !important;
        text-align: center !important;
    }
    
    .how-it-works-text {
        font-size: 14px !important;
        line-height: 1.6 !important;
        text-align: center !important;
    }
    
    /* Services/How It Works Mobile */
    .services .grid {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    .service-card {
        min-height: auto !important;
    }
    
    /* App Section Mobile */
    .app-section .grid {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    
    .phone {
        width: 300px !important;
        height: 300px !important;
    }
    
    /* News Section Mobile */
    .news .grid {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    /* Testimonials Mobile */
    .testimonials .grid {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    /* Newsletter Mobile */
    .newsletter-form .flex {
        flex-direction: column !important;
        gap: 10px !important;
    }
    
    .btn-subscribe {
        width: 100% !important;
        margin-top: 10px !important;
    }
    
    /* Footer Mobile */
    .footer .grid {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    
    /* General spacing fixes */
    .py-20 {
        padding-top: 60px !important;
        padding-bottom: 60px !important;
    }
    
    .mb-20 {
        margin-bottom: 40px !important;
    }
}

@media (max-width: 480px) {
    .hero-section {
        min-height: 60vh !important;
        padding: 30px 0 !important;
    }
    
    .hero-content h1 {
        font-size: 24px !important;
    }
    
    .hero-content .hero-title {
        font-size: 28px !important;
    }
    
    .hero-subtitle {
        font-size: 16px !important;
    }
    
    .px-5 {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
}

/* Remove extra margins and padding */
body {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}

/* Ensure footer sticks to bottom */
html, body {
    height: 100%;
}

/* Fix for specific sections */
.how-it-works-section {
    margin-bottom: 0 !important;
}

.news-section {
    margin-bottom: 0 !important;
}

.testimonials {
    margin-bottom: 0 !important;
}

.newsletter {
    margin-bottom: 0 !important;
}

/* Mobile App Section Phone Mockup Fixes */
.app-section .phone {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.app-section .phone img {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Desktop styles */
@media (min-width: 1025px) {
    .app-section .phone {
        display: flex !important;
        justify-content: flex-end !important; /* Align to right on desktop */
        margin-left: auto !important;
    }
    
    .app-section .grid {
        grid-template-columns: 1fr 1fr !important; /* Equal columns on desktop */
    }
}

/* Tablet styles */
@media (max-width: 1024px) and (min-width: 769px) {
    .app-section .phone {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        margin: 0 auto !important;
    }
    
    .app-section .phone img {
        width: 100% !important;
        max-width: 400px !important;
        height: auto !important;
        display: block !important;
    }
}

/* Mobile styles */
@media (max-width: 768px) {
    .app-section {
        padding: 60px 0 !important;
    }
    
    .app-section .grid {
        grid-template-columns: 1fr !important;
        text-align: center !important;
    }
    
    .app-section .phone {
        display: flex !important;
        width: 100% !important;
        max-width: 280px !important;
        height: 280px !important;
        margin: 30px auto 0 auto !important;
        order: 2 !important; /* Phone comes after text on mobile */
    }
    
    .app-section .phone img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        display: block !important;
    }
    
    .app-section .flex.gap-\\[30px\\] {
        flex-direction: column !important;
        gap: 20px !important;
    }
    
    .app-section .lg\\:ml-5 {
        margin-left: 0 !important;
    }
}

@media (max-width: 480px) {
    .app-section .phone {
        max-width: 250px !important;
        height: 250px !important;
    }
    
    .app-section .download-badge {
        display: inline-block !important;
        margin: 5px !important;
    }
    
    .app-section .flex-row {
        justify-content: center !important;
        flex-wrap: wrap !important;
    }
}

/* Override any hidden classes that might affect display */
.app-section .hidden {
    display: flex !important; /* Override hidden class for phone */
}

.app-section .lg\:flex {
    display: flex !important;
}

/* Ensure the grid layout works properly */
.app-section .grid {
    display: grid !important;
}

/* Make sure content is properly aligned */
.app-section .text-center.lg\:text-left {
    text-align: center;
}

@media (min-width: 1024px) {
    .app-section .text-center.lg\:text-left {
        text-align: left;
    }
}

/* Aggressive space removal */
body > *:last-child:not(footer) {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

/* Ensure no element creates space after footer */
body:after {
    display: none !important;
    content: none !important;
}

/* Remove any pseudo-element spacing */
footer:after,
footer:before {
    display: none !important;
    content: none !important;
}

/* Force footer to stick to bottom */
footer {
    position: relative;
    bottom: 0;
    left: 0;
    width: 100%;
}

/* Remove any potential overflow */
body {
    overflow-y: auto;
    overflow-x: hidden;
}

/* Kill all margins on the last section before footer */
section:last-of-type {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
  </style>

  <body class="font-inter text-gray-800 overflow-x-hidden relative">

 <?php include 'navbar.php'; ?>



    <!-- Hero Section -->
<section class="relative min-h-[90vh] max-h-screen flex items-center mt-0 py-[60px] overflow-hidden z-[1] hero-section" >
    <?php
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    // Get active hero sections
    try {
        $stmt = $db->query("SELECT * FROM hero_sections WHERE is_active = 1 ORDER BY display_order, id LIMIT 1");
        $hero_section = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $hero_section = null;
    }

    if ($hero_section) { ?>
        <!-- Hero Background -->
        <div class="absolute top-0 left-0 right-0 bottom-0 w-full h-full -z-[1] hero-background">

          <img
          src="assets/hero-banner.png"
          alt="Hero Background"
          class="w-full h-full object-cover hero-bg-image"
        />
           <!--  <?php if ($hero_section['banner_image']) { ?>
                <img src="<?php echo $database->getFileUrl($hero_section['banner_image']); ?>" 
                     alt="Hero Background" 
                     class="w-full h-full object-cover hero-bg-image">
            <?php } ?> -->
            <div class="absolute top-0 left-0 right-0 bottom-0 w-full h-full bg-[#00131ac4] hero-overlay"></div>
        </div>

        <div class="max-w-[1200px] mx-auto px-5 w-full relative z-[1]">
            <div class="flex items-center gap-10 justify-between flex-col lg:flex-row">
                <div class="text-left text-white flex-1 relative z-[1] w-full lg:w-auto text-center lg:text-left hero-content">
                    <h1 class="font-inter font-semibold text-[36px] leading-[65px] tracking-[-0.02em] align-middle mb-5 hero-title">
                        <?php echo htmlspecialchars($hero_section['welcome_text']); ?><br>
                        <a class="font-inter font-bold text-[52px] leading-[65px] tracking-[-0.02em] align-middle">
                            <b><?php echo htmlspecialchars($hero_section['main_title']); ?></b>
                        </a>
                    </h1>
                    <p class="font-inter font-light text-2xl leading-[39px] tracking-[-0.02em] align-middle mb-[50px] hero-subtitle">
                        <?php echo htmlspecialchars($hero_section['description']); ?>
                    </p>
                    <div class="flex justify-center lg:justify-start">
                        <?php if ($hero_section['button_text'] && $hero_section['button_link']) { ?>
                            <a href="<?php echo htmlspecialchars($hero_section['button_link']); ?>" 
                               class="btn-primary bg-primary-green text-white border-none rounded-[10px] font-inter font-bold text-[22px] leading-[100%] cursor-pointer transition-all duration-300 inline-block no-underline w-[228px] h-[58px] py-[14.17px] px-[34px] hover:bg-dark-green hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(39,200,64,0.4)] hero-button text-center">
                                <?php echo htmlspecialchars($hero_section['button_text']); ?>
                            </a>
                        <?php } ?>
                    </div>
                    <?php if ($hero_section['download_link_text'] && $hero_section['download_link_url']) { ?>
                        <p class="mt-5 font-medium text-[19.03px] leading-[28.55px] tracking-[0.19px] font-inter text-center lg:text-left hero-link">
                            <a href="<?php echo htmlspecialchars($hero_section['download_link_url']); ?>" 
                               class="text-white font-inter font-light text-[21px] leading-[39px] tracking-[-0.02em] no-underline">
                                <?php echo htmlspecialchars($hero_section['download_link_text']); ?>
                            </a>
                        </p>
                    <?php } ?>
                </div>
                <?php if ($hero_section['banner_image']) { ?>
                    <div class="relative flex-1 max-w-[80%] z-[1] flex items-center justify-center overflow-hidden hidden lg:flex">
                        <img src="<?php echo $database->getFileUrl($hero_section['banner_image']); ?>" 
                             alt="Hero Image" 
                             class="w-full h-auto max-w-full object-contain">
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } else { ?>
        <!-- Fallback hero section if no active sections -->
        <div class="absolute top-0 left-0 right-0 bottom-0 w-full h-full -z-[1] hero-background">
            <div class="w-full h-full bg-gray-800"></div>
            <div class="absolute top-0 left-0 right-0 bottom-0 w-full h-full bg-[#00131ac4] hero-overlay"></div>
        </div>
        <div class="max-w-[1200px] mx-auto px-5 w-full relative z-[1] text-center text-white">
            <h1 class="text-4xl font-bold mb-4">Enugu Smart Bus</h1>
            <p class="text-xl mb-8">Smart. Safe. Seamless Mobility for Everyone in Enugu State</p>
            <a href="#" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors">
                Learn More
            </a>
            <p class="mt-4">
                <a href="#" class="text-white underline">Download our mobile app</a>
            </p>
        </div>
    <?php } ?>
</section>
























    <!-- About Us Section -->
 <section
      class="py-20 h-[700px] relative z-[2] bg-white overflow-hidden how-it-works-section"
    >
    <?php
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    // Get about section data
    try {
        $stmt = $db->query("SELECT * FROM about_section WHERE id = 1");
        $about_section = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $about_section = null;
    }

    if ($about_section):
    ?>
        <div class="max-w-[1200px] mx-auto px-5">
            <div
                class="grid grid-cols-1 lg:grid-cols-[1fr_1.8fr] gap-[60px] items-center relative max-w-[1200px] max-h-[569px] my-[90px] mx-auto justify-center z-[2] how-it-works-content"
              >
                  <div
            class="relative flex items-center max-h-[592px] how-it-works-left"
          >
            <div class="text-center relative z-[2] w-full how-it-works-image">
              
                    <?php if ($about_section['image']): ?>
                        <div class="relative">
                            <img src="<?php echo $database->getFileUrl($about_section['image']); ?>" 
                                 alt="About Enugu Smart Bus" 
                                 class="w-full max-w-md rounded-2xl shadow-2xl">
                            <!-- <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-primary-green rounded-full flex items-center justify-center">
                                <i class="fas fa-bus text-white text-2xl"></i>
                            </div> -->
                        </div>
                    <?php else: ?>
                        <div class="w-full max-w-md h-64 bg-gray-200 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-6xl"></i>
                        </div>
                    <?php endif; ?> <br><br>
                </div>
                </div>

                <!-- About Content -->
               
          <div
            class="pl-0 lg:pl-[60px] items-center self-center -mt-[50px] lg:mt-0 how-it-works-right"
          >
                    <h2 class="font-inter font-semibold text-[35px] leading-[55px] tracking-[0%] text-primary-blue mb-5 how-it-works-title break-words">
                        <?php echo htmlspecialchars($about_section['title']); ?>
                    </h2>

                    <p class="font-inter font-medium text-[15px] leading-[35px] tracking-[0.2px] mb-8 lg:mb-10 text-dark-blue how-it-works-text break-words">
             <?php 
                        // Convert line breaks to paragraphs for better formatting
                        $content = htmlspecialchars($about_section['content']);
                        $content = nl2br($content);
                        echo $content;
                        ?>
            </p>
                    
                   

                    <?php if ($about_section['button_text'] && $about_section['button_link']): ?>


                      <button
                    class="btn-primary bg-primary-green text-white border-none rounded-[10px] font-inter font-bold text-[22px] leading-[100%] cursor-pointer transition-all duration-300 inline-block no-underline w-[228px] h-[58px] py-[14.17px] px-[34px] hover:bg-dark-green hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(39,200,64,0.4)] hoverw-it-works-button"
                  ><a href="<?php echo htmlspecialchars($about_section['button_link']); ?>">
                  <?php echo htmlspecialchars($about_section['button_text']); ?> </a>  
                </button>
                         
                    <?php endif; ?>

                    <!-- Stats Section -->
                    <!-- <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 mt-12 pt-8 border-t border-gray-200">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-blue mb-2">50+</div>
                            <div class="text-sm text-gray-600">Modern Buses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-blue mb-2">24/7</div>
                            <div class="text-sm text-gray-600">Service</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-blue mb-2">1000+</div>
                            <div class="text-sm text-gray-6 00">Happy Commuters</div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
         <div class="absolute top-0 left-0 right-0 h-full z-0">
        <img
          src="assets/how-it-works-background-image.png"
          alt="how it works background"
          class="w-full h-full object-cover relative z-[1]"
        />
      </div><br><br><br><br><br><br><br><br><br><br>
    <?php else: ?>
        <p>None</p>

       
    <?php endif; ?>

</section>























   <!-- How It Works Section -->
<section class="services py-20 bg-white relative overflow-x-visible overflow-y-visible">
    <?php
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    // Get how it works steps
    try {
        $stmt = $db->query("SELECT * FROM how_it_works ORDER BY display_order, step_number");
        $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $steps = [];
    }
    ?>

    <div class="max-w-[1200px] mx-auto px-5">
        <div class="text-center mb-20 max-w-[800px] mx-auto relative z-[1]">
            <h2 class="font-inter font-bold text-[39px] leading-[47.35px] tracking-[-0.02em] text-center align-middle text-primary-blue m-0">
                How Enugu Smart Bus Works
            </h2>
            <p class="font-inter font-normal text-lg leading-7 tracking-[0%] text-center align-middle text-primary-blue mt-3">
                Simple steps to enjoy a smarter way of moving around Enugu.
            </p>
        </div>

        <?php if (!empty($steps)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[30px] relative z-[1]">
                <?php foreach ($steps as $step): ?>
                <div class="service-card bg-white min-h-[336px] rounded-xl shadow-[0_5px_20px_5px_rgba(0,0,0,0.1)] text-center transition-all duration-300 border border-[#f0f0f0] relative flex flex-col overflow-hidden z-[1] hover:bg-[#d7ffdd] hover:-translate-y-[5px] hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)]">
                    <div class="h-[145px] overflow-hidden bg-gray-100">
                        <?php if ($step['image']): ?>
                            <img src="<?php echo $database->getFileUrl($step['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($step['title']); ?>" 
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-primary-blue">
                                <i class="fas fa-step-forward text-white text-4xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-inter font-semibold text-lg leading-5 tracking-[0%] text-primary-blue text-left mb-0">
                            Step <?php echo $step['step_number']; ?>:
                        </h3>
                        <h2 class="font-inter font-semibold text-xl leading-[22.06px] tracking-[0%] text-black text-left mt-2.5 mb-1">
                            <?php echo htmlspecialchars($step['title']); ?>
                        </h2>
                        <p class="font-inter font-normal text-[11px] leading-[18px] tracking-[0%] text-[#130072] text-left mt-2 flex-1">
                            <?php echo htmlspecialchars($step['description']); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Fallback steps if no data -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[30px] relative z-[1]">
                <?php
                $fallback_steps = [
                    [
                        'step_number' => 1,
                        'title' => 'Register',
                        'description' => 'Sign up on the Enugu Smart Bus Web Portal or download the ESB Mobile App from Google Play or App Store to create your account.',
                        'image' => null
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Fund Your Account',
                        'description' => 'Top up your Smart Bus Wallet easily using debit card, bank transfer, or authorized vendors.',
                        'image' => null
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Get Your Smart Card',
                        'description' => 'Visit any authorized ESB outlet or terminal to collect your Enugu Smart Bus Smart Card.',
                        'image' => null
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Tap and Board',
                        'description' => 'Simply tap your Smart Card or scan your mobile QR code to pay automatically and board the bus.',
                        'image' => null
                    ]
                ];
                
                foreach ($fallback_steps as $step):
                ?>
                <div class="service-card bg-white min-h-[336px] rounded-xl shadow-[0_5px_20px_5px_rgba(0,0,0,0.1)] text-center transition-all duration-300 border border-[#f0f0f0] relative flex flex-col overflow-hidden z-[1] hover:bg-[#d7ffdd] hover:-translate-y-[5px] hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)]">
                    <div class="h-[145px] overflow-hidden bg-primary-blue flex items-center justify-center">
                        <i class="fas fa-step-forward text-white text-4xl"></i>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-inter font-semibold text-lg leading-5 tracking-[0%] text-primary-blue text-left mb-0">
                            Step <?php echo $step['step_number']; ?>:
                        </h3>
                        <h2 class="font-inter font-semibold text-xl leading-[22.06px] tracking-[0%] text-black text-left mt-2.5 mb-1">
                            <?php echo $step['title']; ?>
                        </h2>
                        <p class="font-inter font-normal text-[11px] leading-[18px] tracking-[0%] text-[#130072] text-left mt-2 flex-1">
                            <?php echo $step['description']; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <img
        src="assets/Vector%209.png"
        alt="Decoration"
        class="vector-9 absolute w-full h-full -translate-y-[35%] object-contain z-0 pointer-events-none top-[555.83px] left-[99.17px] hidden lg:block"
      />
      <img
        src="assets/Vector%207.png"
        alt="Decoration"
        class="vector-7 absolute w-[555.8px] h-[564.9px] -top-[12.5px] left-[9.17px] object-contain z-0 pointer-events-none hidden lg:block"
      />
</section>































































 <!-- Mobile App Section -->
<section class="app-section items-center justify-center py-20 bg-white relative overflow-x-visible overflow-y-visible">
    <?php
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    // Get app section data
    try {
        $stmt = $db->query("SELECT * FROM app_section WHERE id = 1");
        $app_section = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $app_section = null;
    }
    ?>

    <!-- Background decorations -->
    <img src="assets/Vector%209.png" alt="Decoration" class="vector-9 absolute w-full h-full -translate-y-[55%] object-contain pointer-events-none top-[555.83px] left-[99.17px] z-0 hidden lg:block">
    <img src="assets/Vector%207.png" alt="Decoration" class="vector-7 absolute w-[555.8px] h-[564.9px] -top-[12.5px] left-[9.17px] z-0 hidden lg:block">
    
    <div class="max-w-[1200px] mx-auto px-5">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-0 items-center relative z-[3] max-w-[1101px] mx-auto">
            <!-- App Content - Left side on desktop, First on mobile -->
            <div class="px-2.5 order-1 lg:order-1 text-center lg:text-left">
                <?php if ($app_section): ?>
                    <h2 class="font-inter font-bold text-2xl lg:text-[35px] leading-[1.3] lg:leading-[43.34px] tracking-[-0.12px] text-[#1d1d1f] mb-4 lg:mb-2.5">
                        <?php echo htmlspecialchars($app_section['title']); ?>
                    </h2>
                    <p class="font-inter font-normal text-base lg:text-lg leading-[1.6] lg:leading-10 tracking-[0%] text-black mb-8 lg:mb-20">
                        <?php echo htmlspecialchars($app_section['content']); ?>
                    </p>
                    
                    <div class="flex flex-row gap-4 flex-nowrap justify-center lg:justify-start items-center">
                        <?php if ($app_section['app_store_link'] && $app_section['app_store_link'] != '#'): ?>
                            <a href="<?php echo htmlspecialchars($app_section['app_store_link']); ?>" 
                               class="download-badge inline-block no-underline transition-all duration-300 flex-shrink-0 hover:-translate-y-0.5 hover:opacity-90"
                               target="_blank" rel="noopener noreferrer">
                                <img src="assets/app-store-badge.png" 
                                     alt="Download on the App Store" 
                                     class="h-10 lg:h-12 w-auto block rounded-lg">
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($app_section['play_store_link'] && $app_section['play_store_link'] != '#'): ?>
                            <a href="<?php echo htmlspecialchars($app_section['play_store_link']); ?>" 
                               class="download-badge inline-block no-underline transition-all duration-300 flex-shrink-0 hover:-translate-y-0.5 hover:opacity-90"
                               target="_blank" rel="noopener noreferrer">
                                <img src="assets/google-play-badge.png" 
                                     alt="Get it on Google Play" 
                                     class="h-10 lg:h-12 w-auto block rounded-lg">
                            </a>
                        <?php endif; ?>
                        
                        <?php if (($app_section['app_store_link'] == '#' || !$app_section['app_store_link']) && ($app_section['play_store_link'] == '#' || !$app_section['play_store_link'])): ?>
                            <div class="bg-yellow-100 border border-yellow-400 rounded-lg p-4 text-center">
                                <p class="text-yellow-800 text-sm mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    App download links will appear here once configured in the admin panel.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Fallback content -->
                    <h2 class="font-inter font-bold text-2xl lg:text-[35px] leading-[1.3] lg:leading-[43.34px] tracking-[-0.12px] text-[#1d1d1f] mb-4 lg:mb-2.5">
                        Download Our Mobile App
                    </h2>
                    <p class="font-inter font-normal text-base lg:text-lg leading-[1.6] lg:leading-10 tracking-[0%] text-black mb-8 lg:mb-20">
                        Experience comfort and convenience with the Enugu Smart Bus app — your all-in-one platform for smart, safe, and cashless travel.
                    </p>
                    <div class="flex flex-row gap-4 flex-nowrap justify-center lg:justify-start items-center">
                        <div class="bg-gray-100 rounded-lg p-4 text-center">
                            <p class="text-gray-600 text-sm mb-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                Configure app section in admin panel to display download links.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Phone Image - Right side on desktop, Second on mobile -->
            <div class="lg:ml-5 flex justify-center order-2 lg:order-2">
                <div class="flex justify-center items-center">
                    <div class="phone w-full max-w-[300px] lg:max-w-[492px] h-[300px] lg:h-[492px] relative flex items-center justify-center">
                        <?php if ($app_section && $app_section['phone_image']): ?>
                            <img src="<?php echo $database->getFileUrl($app_section['phone_image']); ?>" 
                                 alt="Enugu Smart Bus App" 
                                 class="w-full h-full object-contain block">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-100 rounded-3xl flex items-center justify-center border-2 border-dashed border-gray-300">
                                <div class="text-center p-4">
                                    <i class="fas fa-mobile-alt text-gray-400 text-4xl lg:text-6xl mb-4"></i>
                                    <p class="text-gray-500 text-sm lg:text-base">Phone mockup image</p>
                                    <small class="text-gray-400 text-xs">Upload in admin panel</small>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




































 <!-- Latest News Section -->
  <section class="news-section py-20 bg-gray-50">
    <div class="max-w-[1200px] mx-auto px-5">
        <div class="text-center mb-16"> <br><br><br>
            <h2 class="font-inter font-bold text-[35px] leading-9 text-[#111827] mb-4">
                Latest News & Updates
            </h2>
            <p class="font-inter font-normal text-lg text-[#4b5563] max-w-2xl mx-auto">
                Stay informed with the latest developments, announcements, and stories from Enugu Smart Bus
            </p>
        </div>

        <?php if (!empty($latest_news)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($latest_news as $post): ?>
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                    <?php if ($post['featured_image']): ?>
                        <img src="<?php echo getImagePath($post['featured_image']); ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>" 
                             class="w-full h-48 object-cover">
                    <?php else: ?>
                        <div class="w-full h-48 bg-gradient-to-r from-primary-blue to-primary-green flex items-center justify-center">
                            <i class="fas fa-newspaper text-white text-4xl"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-3">
                            <span><?php echo date('M j, Y', strtotime($post['published_at'])); ?></span>
                            <span class="mx-2">•</span>
                            <span><?php echo ($post['read_time'] ?? '5'); ?> min read</span>
                        </div>
                        
                        <h3 class="font-poppins font-semibold text-xl text-black mb-3 line-clamp-2">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h3>
                        
                        <p class="font-inter font-normal text-base text-[#4b5563] mb-4 line-clamp-3">
                            <?php 
                            $excerpt = $post['excerpt'] ?? substr(strip_tags($post['content'] ?? ''), 0, 120);
                            echo htmlspecialchars($excerpt . (strlen($excerpt) >= 120 ? '...' : ''));
                            ?>
                        </p>
                        
                        <a href="blog-post.php?id=<?php echo $post['id']; ?>" 
                           class="text-primary-blue font-semibold inline-flex items-center gap-2 hover:gap-3 transition-all duration-300">
                            Read More <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>



            

            <div class="text-center mt-12">
                 <button
            class="btn-primary bg-primary-green text-white border-none rounded-[10px] font-inter font-bold text-[22px] leading-[100%] cursor-pointer transition-all duration-300 inline-block no-underline w-[228px] h-[58px] py-[14.17px] px-[34px] mx-auto block hover:bg-dark-green hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(39,200,64,0.4)]"
          ><a href="blog.php">View All News</a>
           
          </button>
            </div>
        <?php else: ?>
            <!-- Fallback content when no posts exist -->
            <div class="text-center py-12">
                <i class="fas fa-newspaper text-gray-400 text-5xl mb-4"></i>
                <h3 class="font-inter font-semibold text-xl text-gray-600 mb-2">No News Yet</h3>
                <p class="font-inter text-gray-500 mb-6">Check back later for the latest updates from Enugu Smart Bus.</p>
                <a href="blog.php" class="text-primary-blue font-semibold hover:underline">
                    Visit Our Blog
                </a>
            </div>
        <?php endif; ?>
    </div>
  </section>


    <!-- Customer Testimonials Section -->
<section class="testimonials py-20 bg-transparent relative overflow-hidden">
    <?php
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    // Get active testimonials
    try {
        $stmt = $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY display_order, created_at DESC LIMIT 3");
        $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $testimonials = [];
    }
    ?>

    <div class="max-w-[1200px] mx-auto px-5 relative z-[1]">
        <div class="text-center mb-20">
            <h2 class="font-inter font-bold text-[35px] leading-9 tracking-[0%] text-center text-[#111827]">
                Customer Testimonials
            </h2>
            <p class="font-inter font-normal text-lg leading-5 tracking-[0%] text-center text-[#4b5563] mt-3">
                What our customers have to say about our fleet and services.
            </p>
        </div>

        <?php if (!empty($testimonials)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[30px] mb-10">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="news-card bg-white rounded-xl overflow-hidden shadow-[0_5px_20px_rgba(0,0,0,0.1)] transition-all duration-300 hover:-translate-y-[5px] hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)]">
                    <div class="h-auto overflow-visible py-[30px] px-5 flex justify-center items-center">
                        <?php if ($testimonial['customer_image']): ?>
                            <img src="<?php echo $database->getFileUrl($testimonial['customer_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($testimonial['customer_name']); ?>" 
                                 class="w-[120px] h-[120px] rounded-full object-cover block mx-auto border-4 border-white shadow-lg">
                        <?php else: ?>
                            <div class="w-[120px] h-[120px] rounded-full bg-gradient-to-r from-primary-blue to-primary-green flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                <?php echo strtoupper(substr($testimonial['customer_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="px-6 pb-6 text-center">
                        <h3 class="font-poppins font-bold text-xl leading-7 text-black mb-2">
                            <?php echo htmlspecialchars($testimonial['customer_name']); ?>
                        </h3>
                        <span class="testimonial-role font-poppins font-normal italic text-sm leading-5 text-[#6b7280] block mb-4">
                            <?php echo htmlspecialchars($testimonial['customer_role']); ?>
                        </span>
                        <p class="font-inter font-normal text-base leading-6 text-[#4b5563]">
                            "<?php echo htmlspecialchars($testimonial['testimonial']); ?>"
                        </p>
                        
                        <!-- Rating Stars -->
                        <div class="flex justify-center items-center mt-4">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Fallback testimonials -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[30px] mb-10">
                <?php
                $fallback_testimonials = [
                    [
                        'customer_name' => 'Chika Okeke',
                        'customer_role' => 'Business Owner, Ogbete Main Market',
                        'testimonial' => 'With Enugu Smart Bus, moving my goods and staff around the city will finally be easier and more reliable.',
                        'image' => null
                    ],
                    [
                        'customer_name' => 'Uchenna Nwodo',
                        'customer_role' => 'Student, ESUT',
                        'testimonial' => "I can't wait to use the Smart Bus app — no more long waits or confusion about which bus to take!",
                        'image' => null
                    ],
                    [
                        'customer_name' => 'Ngozi Eze',
                        'customer_role' => 'Civil Servant',
                        'testimonial' => 'This project shows real progress. Enugu is truly becoming a modern, connected city.',
                        'image' => null
                    ]
                ];
                
                foreach ($fallback_testimonials as $testimonial):
                ?>
                <div class="news-card bg-white rounded-xl overflow-hidden shadow-[0_5px_20px_rgba(0,0,0,0.1)] transition-all duration-300 hover:-translate-y-[5px] hover:shadow-[0_10px_30px_rgba(0,0,0,0.15)]">
                    <div class="h-auto overflow-visible py-[30px] px-5 flex justify-center items-center">
                        <div class="w-[120px] h-[120px] rounded-full bg-gradient-to-r from-primary-blue to-primary-green flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                            <?php echo strtoupper(substr($testimonial['customer_name'], 0, 1)); ?>
                        </div>
                    </div>
                    
                    <div class="px-6 pb-6 text-center">
                        <h3 class="font-poppins font-bold text-xl leading-7 text-black mb-2">
                            <?php echo htmlspecialchars($testimonial['customer_name']); ?>
                        </h3>
                        <span class="testimonial-role font-poppins font-normal italic text-sm leading-5 text-[#6b7280] block mb-4">
                            <?php echo htmlspecialchars($testimonial['customer_role']); ?>
                        </span>
                        <p class="font-inter font-normal text-base leading-6 text-[#4b5563]">
                            "<?php echo htmlspecialchars($testimonial['testimonial']); ?>"
                        </p>
                        
                        <!-- Rating Stars -->
                        <div class="flex justify-center items-center mt-4">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- CTA Button -->
        <div class="text-center mt-[70px] p-0">

          <button
            class="btn-primary bg-primary-green text-white border-none rounded-[10px] font-inter font-bold text-[22px] leading-[100%] cursor-pointer transition-all duration-300 inline-block no-underline w-[228px] h-[58px] py-[14.17px] px-[34px] mx-auto block hover:bg-dark-green hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(39,200,64,0.4)]"
          ><a href="testimonials.php">View All</a>
           
          </button>
           <!--  <a href="testimonials.php" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-blue hover:bg-blue-700 transition-colors duration-300 md:py-4 md:text-lg md:px-10">
                
            </a> -->
        </div>
    </div>

     <img
        src="assets/news-update-vector.png"
        alt="Decoration"
        class="news-ellipse absolute w-full max-w-[2123.56px] h-[1646.14px] opacity-100 top-[190px] left-0 -z-[1] object-cover"
      />
</section>
    



















<!-- Newsletter Section -->
<section class="newsletter py-20 bg-[#f0fff4] relative overflow-hidden mb-0">
    <div class="max-w-[1200px] mx-auto px-5 relative z-[1]">
        <div class="flex items-center justify-between gap-2.5 relative ml-0 lg:ml-[50px] mr-0 lg:mr-[50px] flex-col lg:flex-row">
            <!-- Newsletter Content -->
            <div class="flex-1 text-left lg:text-left text-center">
                <h2 class="font-poppins font-semibold text-[30px] leading-12 tracking-[0%] capitalize text-[#031e2d] mb-3">
                    Subscribe to Our Newsletter
                </h2>
                <p class="font-poppins font-normal text-xl leading-6 tracking-[1%] text-[#131313] mt-2">
                    Get the latest news, tips, and updates about Enugu Smart Bus delivered to your inbox.
                </p><br><br><br>
                
                <!-- Dynamic Message Container -->
                <div id="subscriptionMessage" class="mt-4"></div>
            </div>
            
            <!-- Newsletter Form -->
            <div class="flex-1 w-full lg:w-auto mt-8 lg:mt-0">
                <form id="newsletterForm" method="POST" class="newsletter-form">
                    <div class="flex items-center bg-white rounded-[20px] p-2 pl-[15px] max-w-[577px] w-full shadow-lg border border-green-200">
                        <i class="fas fa-envelope text-[#131313] ml-[15px] text-sm font-black leading-[100%]"></i>
                        <input type="email" 
                               name="email" 
                               id="subscribe_email"
                               placeholder="Enter your email address" 
                               class="newsletter-input flex-1 py-3 px-2.5 border-none bg-transparent text-base outline-none text-gray-800 placeholder:text-gray-500 placeholder:text-base placeholder:tracking-[1%] placeholder:leading-6 placeholder:font-inter placeholder:font-normal w-full"
                               required>
                        <button type="submit" 
                                class="btn-subscribe bg-[#00b935] text-white border-none py-4 px-6 rounded-[15px] text-[22px] cursor-pointer transition-all duration-300 font-montserrat font-bold tracking-[1%] leading-6 shadow-[0px_4px_4px_0px_#195aff] hover:bg-[#008c16] hover:-translate-y-0.5">
                            Subscribe
                        </button>
                    </div>
                </form><br><br>
            </div><br><br><br>
        </div>
    </div>

    <!-- Background decoration -->
    <img src="assets/newsletter-vector.png" alt="Decoration" class="newsletter-mask absolute w-full max-w-full h-[400px] opacity-100 -bottom-[200px] left-0 z-0 pointer-events-none object-cover">
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterForm');
    const subscriptionMessage = document.getElementById('subscriptionMessage');
    
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const emailInput = document.getElementById('subscribe_email');
        const email = emailInput.value.trim();
        
        // Basic email validation
        if (!email || !isValidEmail(email)) {
            showMessage('Please enter a valid email address.', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = newsletterForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subscribing...';
        submitBtn.disabled = true;
        
        // Send AJAX request
        const formData = new FormData();
        formData.append('email', email);
        
        fetch('newsletter-subscribe.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                newsletterForm.reset();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Sorry, there was an error processing your subscription. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    function showMessage(message, type) {
        const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-800' : 'bg-red-100 border-red-400 text-red-800';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        subscriptionMessage.innerHTML = `
            <div class="p-4 rounded-lg border ${bgColor}">
                <i class="fas ${icon} me-2"></i>
                ${message}
            </div>
        `;
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                subscriptionMessage.innerHTML = '';
            }, 5000);
        }
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
</script>



<?php include 'footer.php'; ?>