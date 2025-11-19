<?php
// services/index.php
include_once 'config/database.php';
include_once 'services_helper.php';

$database = new Database();
$db = $database->getConnection();

// Get all active services
$services = getServices($db);

// Get hero section data for services page
try {
    $stmt = $db->query("SELECT * FROM hero_sections WHERE page='services' AND is_active=1 ORDER BY display_order LIMIT 1");
    $hero_section = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $hero_section = null;
}

// Get site settings for contact info
try {
    $stmt = $db->query("SELECT * FROM site_settings WHERE id=1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $settings = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Services - Enugu Smart Bus System</title>
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
    <link rel="stylesheet" href="../css/custom.css" />
    <style>
        .service-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #1f2b6c;
        }
        
        .feature-list li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }
        
        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #27c840;
            font-weight: bold;
        }
        
        .hero-section {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="font-inter text-gray-800 overflow-x-hidden relative">
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="relative min-h-[60vh] flex items-center mt-0 py-[60px] overflow-hidden z-[1] hero-section" 
             style="background: linear-gradient(rgba(0, 19, 26, 0.8), rgba(0, 19, 26, 0.8)), 
                    url('<?php echo $hero_section && $hero_section['banner_image'] ? $database->getFileUrl($hero_section['banner_image']) : 'assets/hero-banner.png'; ?>');">
        <div class="max-w-[1200px] mx-auto px-5 w-full relative z-[1]">
            <div class="flex items-center justify-center text-white flex-col text-center">
                <h2 class="font-inter font-semibold text-[36px] md:text-[42px] leading-[1.2] tracking-[-0.02em] mb-5 hero-title">
                    <?php echo $hero_section ? htmlspecialchars($hero_section['main_title']) : 'Our Services'; ?>
                </h2>
                <p class="font-inter font-light text-xl md:text-2xl leading-[1.4] tracking-[-0.02em] mb-[50px] max-w-3xl hero-subtitle">
                    <?php echo $hero_section ? htmlspecialchars($hero_section['description']) : 'Smart, Safe and Seamless Mobility for Everyone'; ?>
                </p>
                <?php if ($hero_section && $hero_section['button_text']): ?>
                <div class="flex justify-center">
                    <a href="<?php echo htmlspecialchars($hero_section['button_link']); ?>" 
                       class="btn-primary bg-primary-green text-white border-none rounded-[10px] font-inter font-bold text-[18px] md:text-[22px] leading-[100%] cursor-pointer transition-all duration-300 inline-block no-underline px-8 py-4 hover:bg-dark-green hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(39,200,64,0.4)] hero-button">
                        <?php echo htmlspecialchars($hero_section['button_text']); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Services Grid Section -->
    <section class="py-20 relative z-[2] bg-white" style="background-color: #F4F6FF;">
        <div class="max-w-6xl mx-auto px-5 z-[10]">
            <?php if (!empty($services)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                <?php foreach ($services as $service): 
                    $features = json_decode($service['features'], true) ?: [];
                ?>
                <div class="service-card bg-white rounded-3xl flex flex-col gap-6 p-8 shadow-lg z-[2] hover:shadow-xl">
                    <div class="flex items-start gap-4">
                        <?php if ($service['icon']): ?>
                        <img src="<?php echo $database->getFileUrl($service['icon']); ?>" 
                             alt="<?php echo htmlspecialchars($service['title']); ?>" 
                             class="w-16 h-16 object-contain flex-shrink-0" 
                             onerror="this.src='assets/bus-icon.png'"/>
                        <?php else: ?>
                        <div class="w-16 h-16 bg-primary-blue rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-cog text-white text-2xl"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex-1">
                            <h3 class="font-inter font-bold text-xl text-primary-blue mb-2">
                                <?php echo htmlspecialchars($service['title']); ?>
                            </h3>
                            
                            <?php if ($service['description']): ?>
                            <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                <?php echo htmlspecialchars($service['description']); ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($features)): ?>
                            <ul class="feature-list text-sm text-gray-700">
                                <?php foreach ($features as $feature): ?>
                                <li><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- Fallback content when no services exist -->
            <div class="text-center py-12">
                <div class="bg-white rounded-3xl p-12 shadow-lg max-w-2xl mx-auto">
                    <i class="fas fa-cogs text-gray-400 text-5xl mb-4"></i>
                    <h3 class="font-inter font-semibold text-xl text-gray-600 mb-2">Services Coming Soon</h3>
                    <p class="font-inter text-gray-500 mb-6">We're working hard to bring you the best transportation services. Check back soon!</p>
                    <a href="contact" class="text-primary-blue font-semibold hover:underline">
                        Contact Us for More Information
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Background Decoration -->
        <div class="absolute top-[10%] left-[40%] h-full opacity-10 pointer-events-none hidden md:block">
            <img src="assets/footer-logo.png" alt="background decoration" class="w-76 object-cover" />
        </div>
    </section>

    <!-- Help Section -->
    <section class="bg-gray-50 py-16">
        <div class="max-w-4xl mx-auto px-5">
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h3 class="font-inter font-bold text-2xl text-primary-blue mb-8 text-center">Need Help?</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-lg">
                        <div class="flex-shrink-0 w-10 h-10 bg-primary-blue rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-white"></i>
                        </div>
                        <div>
                            <p class="font-inter font-semibold text-primary-blue mb-2">Lost an item?</p>
                            <p class="text-sm text-gray-600 leading-relaxed">Open the app → Help → Lost & Found and select your trip.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4 p-4 bg-green-50 rounded-lg">
                        <div class="flex-shrink-0 w-10 h-10 bg-primary-green rounded-full flex items-center justify-center">
                            <i class="fas fa-mobile-alt text-white"></i>
                        </div>
                        <div>
                            <p class="font-inter font-semibold text-primary-green mb-2">Get the App & Start</p>
                            <p class="text-sm text-gray-600 leading-relaxed">Download App: Google Play / App Store → Create Account → Fund Wallet → Track → Tap → Ride.</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-8 pt-6 border-t border-gray-200">
                    <p class="text-gray-600 mb-4">Ready to experience smart mobility?</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="" 
                           class="bg-primary-blue text-white px-6 py-3 rounded-lg font-inter font-medium hover:bg-dark-blue transition-colors duration-300">
                            Register Now
                        </a>
                        <a href="" 
                           class="border border-primary-blue text-primary-blue px-6 py-3 rounded-lg font-inter font-medium hover:bg-primary-blue hover:text-white transition-colors duration-300">
                            Download App
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="js/script.js"></script>
</body>
</html>