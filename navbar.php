
    <!-- Header -->
    <header class="relative z-[1000] bg-transparent">
      <div class="bg-white relative z-[2]">
        <div class="max-w-[1200px] mx-auto px-5">
          <div class="flex justify-between items-center relative py-3">
            <!-- Logo of ESB -->
            <div class="flex items-center gap-4 flex-shrink-0">
              <div
                class="w-[77px] h-[79px] flex items-center justify-center overflow-hidden"
              >

              <?php if ($settings['logo']): ?>
                  <img src="<?php echo $database->getFileUrl($settings['logo']); ?>"  class="w-full h-full object-contain" alt="Enugu Smart Bus Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <?php else: ?>
                  <div class="text-logo">Enugu Smart Bus</div>
              <?php endif; ?>
                
                <div
                  class="hidden w-[50px] h-[50px] bg-white rounded-full items-center justify-center text-[#2e7d32] text-2xl"
                >
                  <i class="fas fa-shield-alt"></i>
                </div>
              </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center justify-center flex-1">
              <ul class="flex list-none gap-6 m-0 p-0 items-center">
                <li class="m-0">
                  <a
                    href="index.php"
                    class="text-primary-blue no-underline transition-opacity duration-300 font-inter font-medium text-base leading-5 hover:opacity-70"
                    >Home</a
                  >
                </li>
                <li class="m-0 relative group">
                  <a
                    href="about.php"
                    class="text-primary-blue no-underline transition-opacity duration-300 font-inter font-medium text-base leading-5 hover:opacity-70 flex items-center gap-1"
                    >About
                    <i
                      class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"
                    ></i>
                  </a>
                  <ul
                    class="dropdown-menu absolute top-full left-0 mt-2 bg-white shadow-lg rounded-lg py-2 min-w-[200px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50"
                  >
                    <li class="m-0">
                      <a
                        href="about"
                        class="block px-4 py-2 text-primary-blue no-underline font-inter font-medium text-sm leading-5 hover:bg-[#f0f0f0] transition-colors duration-200"
                        >About ESB</a
                      >
                    </li>
                    <li class="m-0">
                      <a
                        href="blue/index.html"
                        class="block px-4 py-2 text-primary-blue no-underline font-inter font-medium text-sm leading-5 hover:bg-[#f0f0f0] transition-colors duration-200"
                        >About BNML</a
                      >
                    </li>
                    <li class="m-0">
                      <a
                        href="team"
                        class="block px-4 py-2 text-primary-blue no-underline font-inter font-medium text-sm leading-5 hover:bg-[#f0f0f0] transition-colors duration-200"
                        >Our Team</a
                      >
                    </li>
                  </ul>
                </li>
                <li class="m-0">
                  <a
                    href="how-it-works/index.html"
                    class="text-primary-blue no-underline transition-opacity duration-300 font-inter font-medium text-base leading-5 hover:opacity-70"
                    >How it works</a
                  >
                </li>
                <li class="m-0">
                  <a
                    href="services.php"
                    class="text-primary-blue no-underline transition-opacity duration-300 font-inter font-medium text-base leading-5 hover:opacity-70"
                    >Services</a
                  >
                </li>
                <li class="m-0">
                  <a
                    href="blog.php"
                    class="text-primary-blue no-underline transition-opacity duration-300 font-inter font-medium text-base leading-5 hover:opacity-70"
                    >Blog</a
                  >
                </li>
                <li class="m-0">
                  <a
                    href="contact"
                    class="text-primary-blue no-underline transition-opacity duration-300 font-inter font-medium text-base leading-5 hover:opacity-70"
                    >Contact Us</a
                  >
                </li>
              </ul>
            </nav>

            <!-- Right Side: Buttons and Mobile Menu Toggle -->
            <div class="flex items-center gap-3 lg:gap-4 flex-shrink-0">
              <!-- Auth Buttons -->
              <div class="hidden md:flex gap-2 lg:gap-3">
                <button
                  onclick="window.location.href='signup/signup.html'"
                  class="btn-register px-3 py-2 border border-primary-blue rounded-lg font-medium cursor-pointer transition-all duration-300 text-sm h-9 font-inter leading-5 bg-primary-blue text-white hover:bg-[#4d5baa] hover:border-[#4d5baa] hover:-translate-y-0.5 whitespace-nowrap"
                >
                  Register/Sign up
                </button>
                <button
                  onclick="window.location.href='download-our-app/download-our-app.html'"
                  class="btn-download px-3 py-2 border border-primary-blue rounded-lg font-medium cursor-pointer transition-all duration-300 text-sm h-9 font-inter leading-5 bg-transparent text-primary-blue hover:bg-[#4d5baa] hover:border-[#4d5baa] hover:text-white hover:-translate-y-0.5 whitespace-nowrap"
                >
                  Download our app
                </button>
              </div>

              <!-- Mobile Menu Toggle -->
              <button
                class="mobile-menu-toggle lg:hidden flex flex-col gap-1.5 cursor-pointer p-2 z-[1001] relative outline-none items-center justify-center flex-shrink-0 min-w-[40px] bg-transparent border-none"
                aria-label="Toggle mobile menu"
              >
                <span
                  class="hamburger-line w-[25px] h-[3px] bg-primary-blue transition-all duration-300 rounded-sm block"
                ></span>
                <span
                  class="hamburger-line w-[25px] h-[3px] bg-primary-blue transition-all duration-300 rounded-sm block"
                ></span>
                <span
                  class="hamburger-line w-[25px] h-[3px] bg-primary-blue transition-all duration-300 rounded-sm block"
                ></span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div class="top-bar-links lg:hidden">
        <ul class="mobile-nav-list">
          <li>
            <a
              href="index.php"
              class="text-primary-blue no-underline transition-colors duration-300 font-inter font-medium text-base leading-5 hover:bg-[#f0f0f0]"
              >Home</a
            >
          </li>
          <li class="mobile-dropdown-item">
            <a
              href="about"
              class="text-primary-blue no-underline transition-colors duration-300 font-inter font-medium text-base leading-5 hover:bg-[#f0f0f0] flex items-center justify-between"
              data-dropdown-toggle
              >About
              <i
                class="fas fa-chevron-down text-xs transition-transform duration-300"
              ></i>
            </a>
            <ul class="mobile-dropdown-menu">
              <li>
                <a
                  href="about"
                  class="block px-4 py-2 text-primary-blue no-underline font-inter font-medium text-sm leading-5 hover:bg-[#f0f0f0] transition-colors duration-200 pl-8"
                  >About ESB</a
                >
              </li>
              <li>
                <a
                  href="blue/index.html"
                  class="block px-4 py-2 text-primary-blue no-underline font-inter font-medium text-sm leading-5 hover:bg-[#f0f0f0] transition-colors duration-200 pl-8"
                  >About BNML</a
                >
              </li>
              <li>
                <a
                  href="team"
                  class="block px-4 py-2 text-primary-blue no-underline font-inter font-medium text-sm leading-5 hover:bg-[#f0f0f0] transition-colors duration-200 pl-8"
                  >Our Team</a
                >
              </li>
            </ul>
          </li>
          <li>
            <a
              href="how-it-works"
              class="text-primary-blue no-underline transition-colors duration-300 font-inter font-medium text-base leading-5 hover:bg-[#f0f0f0]"
              >How it works</a
            >
          </li>
          <li>
            <a
              href="services"
              class="text-primary-blue no-underline transition-colors duration-300 font-inter font-medium text-base leading-5 hover:bg-[#f0f0f0]"
              >Services</a
            >
          </li>
          <li>
            <a
              href="blogs"
              class="text-primary-blue no-underline transition-colors duration-300 font-inter font-medium text-base leading-5 hover:bg-[#f0f0f0]"
              >Blog</a
            >
          </li>
          <li>
            <a
              href="contact"
              class="text-primary-blue no-underline transition-colors duration-300 font-inter font-medium text-base leading-5 hover:bg-[#f0f0f0]"
              >Contact Us</a
            >
          </li>
          <!-- Mobile Auth Buttons -->
          <li class="mobile-auth-buttons">
            <button
              onclick="window.location.href='signup/signup.html'"
              class="btn-register w-full px-4 py-3 border border-primary-blue rounded-lg font-medium cursor-pointer transition-all duration-300 text-sm font-inter leading-5 bg-primary-blue text-white hover:bg-[#4d5baa] mb-2"
            >
              Register/Sign up
            </button>
            <button
              onclick="window.location.href='download-our-app/download-our-app.html'"
              class="btn-download w-full px-4 py-3 border border-primary-blue rounded-lg font-medium cursor-pointer transition-all duration-300 text-sm font-inter leading-5 bg-transparent text-primary-blue hover:bg-[#4d5baa] hover:text-white"
            >
              Download our app
            </button>
          </li>
        </ul>
      </div>
    </header>