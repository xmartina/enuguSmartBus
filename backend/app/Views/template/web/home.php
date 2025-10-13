<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="preload" as="image" href="<?php echo base_url('images/logo.png'); ?>" fetchPriority="high"/>
    <link rel="stylesheet" href="<?php echo base_url('_next/static/css/2016ee00fee71e0d.css'); ?>"/>
    <title><?php echo isset($title) ? esc($title) : 'Enugu Smart Bus Service'; ?></title>
    <meta name="description" content="ENUGU BUS SERVICE MANAGEMENT LTD - Smart transportation solutions"/>
    <link rel="icon" href="<?php echo base_url('favicon.ico'); ?>" type="image/x-icon" sizes="16x16"/>
</head>
<body class="__variable_069ab3 __variable_f367f3 __variable_51684b font-montserrat antialiased flex min-h-screen flex-col">
    <header class="sticky top-0 z-50 w-full border-b border-gray-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <a class="flex items-center space-x-3" href="<?php echo base_url('/'); ?>">
                    <div class="relative h-16 w-16">
                        <img alt="Enugu Smart Bus Service" fetchPriority="high" decoding="async" class="object-contain" style="position:absolute;height:100%;width:100%;left:0;top:0;right:0;bottom:0;color:transparent" src="<?php echo base_url('images/logo.png'); ?>"/>
                    </div>
                </a>
                <nav class="hidden lg:flex items-center space-x-8">
                    <a class="text-sm text-primary transition-colors hover:text-primary-80 font-bold" href="<?php echo base_url('/'); ?>">Home</a>
                    <a class="text-sm text-primary transition-colors hover:text-primary-80 font-bold" href="<?php echo base_url('/about'); ?>">About</a>
                    <a class="text-sm text-primary transition-colors hover:text-primary-80 font-bold" href="<?php echo base_url('/how-it-works'); ?>">How it works</a>
                    <a class="text-sm text-primary transition-colors hover:text-primary-80 font-bold" href="<?php echo base_url('/services'); ?>">Services</a>
                    <a class="text-sm text-primary transition-colors hover:text-primary-80 font-bold" href="<?php echo base_url('/blog'); ?>">Blog</a>
                    <a class="text-sm text-primary transition-colors hover:text-primary-80 font-bold" href="<?php echo base_url('/contact'); ?>">Contact Us</a>
                </nav>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?php echo base_url('login'); ?>" class="inline-flex items-center justify-center rounded-md font-medium transition-colors h-8 px-3 text-sm text-primary hover:bg-gray-50 shadow-xs border-none">Login</a>
                    <a href="<?php echo base_url('login'); ?>" class="inline-flex items-center justify-center rounded-md font-medium transition-colors h-8 px-3 text-sm bg-primary text-white hover:bg-primary-90">Sign Up</a>
                </div>
                <button class="lg:hidden p-2 text-primary" aria-label="Toggle mobile menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-6 w-6">
                        <path d="M4 5h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 19h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>
    
    <main class="flex-1">
        <section class="py-10 px-6 md:px-32 relative min-h-screen flex items-center">
            <div class="absolute inset-0 z-0">
                <img alt="Bus Depot Background" loading="lazy" decoding="async" class="object-cover blur-xs" style="position:absolute;height:100%;width:100%;left:0;top:0;right:0;bottom:0;color:transparent" src="<?php echo base_url(isset($hero_bg_image) ? $hero_bg_image : 'images/hero-bus.jpg'); ?>"/>
                <div class="absolute inset-0 bg-[#00131AC4]"></div>
            </div>
            <div class="relative z-10 flex flex-col lg:flex-row items-start gap-16 w-full max-w-full pt-12">
                <div class="flex-1 text-white">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 leading-tight">
                        <?php echo isset($hero_title) ? esc($hero_title) : 'Welcome to Enugu Smart Bus'; ?>
                    </h1>
                    <p class="text-lg md:text-xl mb-8 text-gray-200 leading-relaxed">
                        <?php echo isset($hero_subtitle) ? esc($hero_subtitle) : 'Smart. Safe. Seamless Mobility for Everyone in Enugu State.'; ?>
                    </p>
                    <div class="space-y-4">
                        <a href="#services" class="inline-flex items-center justify-center transition-colors h-12 bg-[#00B935] hover:bg-[#00B935]/90 text-white px-8 py-4 rounded-lg font-semibold text-lg">
                            <?php echo isset($hero_button_text) ? esc($hero_button_text) : 'Learn More'; ?>
                        </a>
                        <div class="text-white text-lg">Download our mobile app</div>
                        <div class="flex gap-4 mt-4">
                            <a href="#" class="inline-block">
                                <img src="<?php echo base_url('images/google-play.svg'); ?>" alt="Get it on Google Play" class="h-12">
                            </a>
                            <a href="#" class="inline-block">
                                <img src="<?php echo base_url('images/apple.svg'); ?>" alt="Download on App Store" class="h-12">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex-1 flex justify-center w-full lg:w-auto">
                    <div class="relative w-full max-w-lg">
                        <div class="rounded-2xl p-2">
                            <img alt="Enugu Smart Bus" loading="lazy" width="600" height="400" decoding="async" class="rounded-xl object-cover w-full h-full z-10" style="color:transparent" src="<?php echo base_url(isset($hero_image) ? $hero_image : 'images/hero-bus.png'); ?>"/>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-br from-[#00B935] to-[#195AFF] rounded-2xl p-1 left-10 -top-10 w-[95%] h-[95%] -z-10"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 md:py-32 px-6 md:px-32 relative">
            <div class="absolute inset-0 z-0">
                <img alt="Bus Background" loading="lazy" decoding="async" class="object-cover opacity-10" style="position:absolute;height:100%;width:100%;left:0;top:0;right:0;bottom:0;color:transparent" src="<?php echo base_url('images/blue.png'); ?>"/>
            </div>
            <div class="relative z-10 flex flex-col lg:flex-row items-center gap-10">
                <div class="flex-1 flex justify-center overflow-visible w-full lg:w-auto">
                    <div class="relative w-80 md:w-96 h-[400px] md:h-[500px]">
                        <div class="absolute -bottom-12 -left-12 right-10 bg-[#195AFFD9] text-white p-1 rounded-lg text-center z-10 w-64 md:w-80 h-full">
                            <div class="flex flex-col justify-end h-full">
                                <h3 class="text-xl">Governor Peter Mbah</h3>
                                <p class="text-sm opacity-90 font-semibold">Governor of Enugu State</p>
                            </div>
                        </div>
                        <div class="absolute top-0 left-0 w-64 md:w-80 h-full z-20">
                            <img alt="Governor" loading="lazy" width="320" height="500" decoding="async" class="rounded-lg object-cover w-full h-full" style="color:transparent" src="<?php echo base_url('images/peter.png'); ?>"/>
                        </div>
                        <div class="absolute -top-10 -right-10 bg-[#00B935] rounded-full p-1 w-28 h-28 z-0"></div>
                    </div>
                </div>
                <div class="flex-1 space-y-6">
                    <div class="inline-block px-4 py-2 bg-[#00B93533] text-[#00B935] rounded-full text-sm font-semibold">ABOUT US</div>
                    <h2 class="text-3xl md:text-4xl font-bold text-[#00131A]">
                        <?php echo isset($about_title) ? esc($about_title) : 'The Smart Way to Travel Across Enugu State'; ?>
                    </h2>
                    <p class="text-gray-700 text-lg leading-relaxed">
                        <?php echo isset($about_description) ? esc($about_description) : 'Enugu Smart Bus is revolutionizing public transportation in Enugu State. We provide safe, comfortable, and reliable bus services that connect communities and make travel easier for everyone.'; ?>
                    </p>
                    <div class="grid grid-cols-2 gap-6 mt-8">
                        <div class="space-y-2">
                            <div class="text-4xl font-bold text-[#00B935]">500+</div>
                            <p class="text-gray-600">Daily Trips</p>
                        </div>
                        <div class="space-y-2">
                            <div class="text-4xl font-bold text-[#195AFF]">50+</div>
                            <p class="text-gray-600">Bus Routes</p>
                        </div>
                        <div class="space-y-2">
                            <div class="text-4xl font-bold text-[#00B935]">10K+</div>
                            <p class="text-gray-600">Happy Customers</p>
                        </div>
                        <div class="space-y-2">
                            <div class="text-4xl font-bold text-[#195AFF]">24/7</div>
                            <p class="text-gray-600">Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 md:py-32 px-6 md:px-32 bg-[#F8F9FA]">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <div class="inline-block px-4 py-2 bg-[#195AFF33] text-[#195AFF] rounded-full text-sm font-semibold mb-4">HOW IT WORKS</div>
                    <h2 class="text-3xl md:text-4xl font-bold text-[#00131A] mb-4">
                        <?php echo isset($how_it_works_title) ? esc($how_it_works_title) : 'Book Your Trip in 4 Simple Steps'; ?>
                    </h2>
                    <p class="text-gray-600 text-lg">
                        <?php echo isset($how_it_works_subtitle) ? esc($how_it_works_subtitle) : 'Getting around Enugu State has never been easier'; ?>
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php 
                    $steps = isset($how_it_works_steps) ? $how_it_works_steps : [
                        ['image' => 'images/step1.png', 'title' => 'Choose Destination', 'description' => 'Select where you want to go'],
                        ['image' => 'images/step2.png', 'title' => 'Select Date & Time', 'description' => 'Pick your travel schedule'],
                        ['image' => 'images/step3.png', 'title' => 'Make Payment', 'description' => 'Secure online payment'],
                        ['image' => 'images/step4.png', 'title' => 'Get Your Ticket', 'description' => 'Receive ticket instantly']
                    ];
                    foreach ($steps as $index => $step): 
                    ?>
                    <div class="bg-white rounded-lg p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-20 h-20 mx-auto mb-4 bg-[#F0F4FF] rounded-full flex items-center justify-center">
                            <img src="<?php echo base_url($step['image']); ?>" alt="Step <?php echo $index + 1; ?>" class="w-12 h-12">
                        </div>
                        <h3 class="text-xl font-bold text-[#00131A] mb-2"><?php echo esc($step['title']); ?></h3>
                        <p class="text-gray-600"><?php echo esc($step['description']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section id="services" class="py-16 md:py-32 px-6 md:px-32">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <div class="inline-block px-4 py-2 bg-[#00B93533] text-[#00B935] rounded-full text-sm font-semibold mb-4">OUR SERVICES</div>
                    <h2 class="text-3xl md:text-4xl font-bold text-[#00131A]">Why Choose Enugu Smart Bus?</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-lg transition-shadow border border-gray-100">
                        <div class="w-16 h-16 bg-[#00B93533] rounded-full flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-[#00B935]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#00131A] mb-4">On-Time Service</h3>
                        <p class="text-gray-600">Reliable schedules that get you to your destination on time, every time.</p>
                    </div>
                    <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-lg transition-shadow border border-gray-100">
                        <div class="w-16 h-16 bg-[#195AFF33] rounded-full flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-[#195AFF]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#00131A] mb-4">Safe & Secure</h3>
                        <p class="text-gray-600">Your safety is our priority with trained drivers and well-maintained buses.</p>
                    </div>
                    <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-lg transition-shadow border border-gray-100">
                        <div class="w-16 h-16 bg-[#00B93533] rounded-full flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-[#00B935]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#00131A] mb-4">Affordable Fares</h3>
                        <p class="text-gray-600">Quality transportation at prices that won't break your budget.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 md:py-24 px-6 md:px-32 bg-gradient-to-r from-[#00B935] to-[#195AFF]">
            <div class="max-w-4xl mx-auto text-center text-white">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Start Your Journey?</h2>
                <p class="text-xl mb-8 opacity-90">Book your next trip with Enugu Smart Bus today and experience the difference.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo base_url('login'); ?>" class="inline-flex items-center justify-center bg-white text-[#00B935] hover:bg-gray-100 font-semibold px-8 py-4 rounded-lg text-lg transition-colors">
                        Book a Ticket Now
                    </a>
                    <a href="<?php echo base_url('/contact'); ?>" class="inline-flex items-center justify-center bg-transparent border-2 border-white text-white hover:bg-white hover:text-[#00B935] font-semibold px-8 py-4 rounded-lg text-lg transition-colors">
                        Contact Us
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-[#00131A] text-white py-12 px-6 md:px-32">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="<?php echo base_url('images/logo.png'); ?>" alt="Enugu Smart Bus" class="h-12 w-12">
                        <span class="text-xl font-bold">Enugu Smart Bus</span>
                    </div>
                    <p class="text-gray-400 mb-4">Smart. Safe. Seamless Mobility for Everyone in Enugu State.</p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="<?php echo base_url('/'); ?>" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="<?php echo base_url('/about'); ?>" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="<?php echo base_url('/services'); ?>" class="hover:text-white transition-colors">Services</a></li>
                        <li><a href="<?php echo base_url('/contact'); ?>" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Contact Info</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Enugu State, Nigeria</li>
                        <li>info@enugusmartbus.ng</li>
                        <li>+234 XXX XXX XXXX</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Enugu Smart Bus Service. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="<?php echo base_url('_next/static/chunks/polyfills-42372ed130431b0a.js'); ?>"></script>
    <script src="<?php echo base_url('_next/static/chunks/webpack-b7eb15e779c34889.js'); ?>"></script>
    <script src="<?php echo base_url('_next/static/chunks/main-app-08809f55eaeb8322.js'); ?>"></script>
</body>
</html>
