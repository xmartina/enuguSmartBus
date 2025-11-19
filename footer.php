
   <!-- Footer -->
<footer class="footer bg-[#eef2f7] text-[#131313] p-0 relative overflow-hidden mt-0">
    <div class="footer-background-overlay absolute top-0 left-0 right-0 bottom-0 bg-[url('assets/footer-background.png')] bg-[length:100%] bg-center bg-no-repeat opacity-[0.15] grayscale-[30%] brightness-[1.1] sepia-[20%] hue-rotate-[180deg] saturate-[80%] z-0"></div>
    
    <div class="max-w-[1200px] mx-auto px-5 relative z-[1]">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 py-10 mx-auto">
            <!-- Column 1: Company Information -->
            <div class="flex flex-col">
                <?php if ($settings['logo_url']): ?>
                <div class="flex items-start">
                    <img src="<?php echo $settings['logo_url']; ?>" alt="Enugu Smart Bus Logo" class="w-[121px] h-auto object-contain" />
                </div>
                <?php endif; ?>

                <p class="font-inter font-normal text-[12.42px] leading-[21.29px] tracking-[0%] text-black mt-4">
                    Enugu Smart Bus is an innovative public transport company under Blue Noble Motors Limited, delivering safe, cashless, and smart mobility solutions across Enugu State. Empowering citizens through modern transport, technology, and sustainability — driving the Smart Enugu vision forward.
                </p>
                <div class="flex gap-2.5 flex-wrap mt-2.5">
                    <a href="#" class="inline-block h-8">
                        <img src="assets/app-store-badge.png" alt="Download on the App Store" class="h-full w-auto block" />
                    </a>
                    <a href="#" class="inline-block h-8">
                        <img src="assets/google-play-badge.png" alt="GET IT ON Google Play" class="h-full w-auto block" />
                    </a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="flex flex-col">
                <h3 class="font-inter font-bold text-[17.74px] text-[#002621] leading-[28.39px] tracking-[0%] mb-3">Quick Links</h3>
                <ul class="list-none p-0 m-0 space-y-2">
                    <li><a href="index-2.html" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">Home</a></li>
                    <li><a href="about" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">About Enugu Smart Bus</a></li>
                    <li><a href="blue/index.html" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">About BNML</a></li>
                    <li><a href="how-it-works" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">How It Works</a></li>
                    <li><a href="services" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">Our Services</a></li>
                    <li><a href="team" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">Our Team</a></li>
                    <li><a href="blog.php" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">Our Blog</a></li>
                    <li><a href="contact" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc] block">Contact Us</a></li>
                </ul>
            </div>

            <!-- Column 3: Support and Contact -->
            <div class="flex flex-col">
                <h3 class="font-inter font-bold text-[17.74px] text-[#002621] leading-[28.39px] tracking-[0%] mb-3">Support and Contact</h3>
                <div class="space-y-4">
                    <div>
                        <strong class="font-inter text-sm font-bold leading-5 text-black">Office Address:</strong>
                        <?php if ($settings['office_address']): ?>
                        <p class="font-inter text-sm font-normal leading-5 text-black mt-1"><?php echo htmlspecialchars($settings['office_address']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong class="font-inter text-sm font-bold leading-5 text-black">Phone:</strong>
                        <?php if ($settings['phone']): ?>
                        <p class="font-inter text-sm font-normal leading-5 text-black mt-1"><?php echo htmlspecialchars($settings['phone']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong class="font-inter text-sm font-bold leading-5 text-black">Email:</strong>
                        <?php if ($settings['email1']): ?>
                        <p class="font-inter text-sm font-normal leading-5 text-black mt-1"><?php echo htmlspecialchars($settings['email1']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong class="font-inter text-sm font-bold leading-5 text-black">Business Hours:</strong>
                        <?php if ($settings['business_hours']): ?>
                        <p class="font-inter text-sm font-normal leading-5 text-black mt-1"><?php echo htmlspecialchars($settings['business_hours']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Column 4: Connect With Us -->
            <div class="flex flex-col">
                <h3 class="font-inter font-bold text-[17.74px] text-[#002621] leading-[28.39px] tracking-[0%] mb-3">Connect With Us</h3>
                <p class="font-inter text-[14.19px] text-black leading-[21.29px] tracking-[0%] mb-3">Follow us for updates, route news, and community highlights:</p>
                <ul class="list-none p-0 m-0 space-y-3">
                    <li class="flex items-center gap-2.5">
                        <img src="assets/facebook.png" alt="Facebook" class="w-[18px] h-[18px] object-contain flex-shrink-0" />
                        <a href="<?php echo $settings['facebook_url']; ?>" target="_blank" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc]">Enugu Smart Bus</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <img src="assets/x.png" alt="Twitter" class="w-[18px] h-[18px] object-contain flex-shrink-0" />
                        <a href="<?php echo $settings['twitter_url']; ?>" target="_blank" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc]">@EnuguSmartBus</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <img src="assets/instagram.png" alt="Instagram" class="w-[18px] h-[18px] object-contain flex-shrink-0" />
                        <a href="<?php echo $settings['instagram_url']; ?>" target="_blank" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc]">@EnuguSmartBus</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <img src="assets/youtube.png" alt="Youtube" class="w-[18px] h-[18px] object-contain flex-shrink-0" />
                        <a href="<?php echo $settings['youtube_url']; ?>" target="_blank" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc]">Enugu Smart Bus TV</a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <img src="assets/linkedin.png" alt="Linkedin" class="w-[18px] h-[18px] object-contain flex-shrink-0" />
                        <a href="<?php echo $settings['linkedin_url']; ?>" target="_blank" class="font-inter font-normal text-[14.19px] text-black leading-[21.29px] tracking-[0%] no-underline hover:text-[#0066cc]">Enugu Smart Bus</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Separator Line -->
    <div class="w-full h-px bg-[#d1d5db] m-0 max-w-[1200px] mx-auto"></div>

    <!-- Bottom Bar -->
    <div class="bg-transparent py-5 w-full">
        <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row justify-center items-center gap-4 md:gap-[180px] text-center">
            <div class="font-montserrat font-bold text-sm text-black leading-[100%] tracking-[0%]">
                <span>© 2025 Enugu Smart Bus. All rights reserved.</span>
            </div>
            <div class="flex items-center gap-2 text-black font-montserrat font-bold text-sm leading-[100%] tracking-[0%]">
                <span>Powered By:</span>
                <div class="flex items-center gap-2">
                    <img src="assets/blue-noble-icon.jpg" alt="Blue Noble Motors Limited" class="h-[46px] w-auto block" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                    <div class="hidden items-center gap-2 bg-[#0066cc] py-1.5 px-3 rounded">
                        <div class="text-base filter brightness-0 invert">🏠</div>
                        <span class="font-poppins text-[10px] font-bold text-white whitespace-nowrap">BLUE NOBLE MOTORS LIMITED</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

    <script src="js/script.js"></script>

    <!-- Ask Bena Button -->
    <div
      class="ask-bena-button fixed bottom-5 right-5 bg-primary-green text-white py-3 px-5 rounded-[25px] flex items-center gap-2 cursor-pointer shadow-[0_4px_12px_rgba(39,200,64,0.3)] transition-all duration-300 z-[1000] font-montserrat font-semibold text-sm hover:bg-dark-green hover:-translate-y-0.5 hover:shadow-[0_6px_16px_rgba(39,200,64,0.4)]"
    >
      <img src="assets/bubble-chat.png" alt="bena icon" class="w-5 h-5" />
      <span>Ask Ijeoma</span>
    </div>

    <!-- Ask Bena Chat Widget -->
    <div
      class="bena-chat fixed right-5 bottom-[90px] w-[420px] h-[510px] bg-white rounded-[14.2px] shadow-[0_16px_40px_rgba(0,0,0,0.18)] hidden flex-col overflow-hidden z-[1001]"
      aria-hidden="true"
    >
      <div
        class="flex h-[74px] items-center justify-between py-[5px] px-[17px] m-[15px] bg-[#e5e5e5] border-b border-[#eef2f3] gap-2.5 shadow-[0_4px_12px_4px_rgba(0,0,0,0.349)] rounded-t-[10px]"
      >
        <div class="flex items-center gap-2.5">
          <img
            src="assets/footer-logo.png"
            alt="Benue State Logo"
            class="bena-logo w-[57px] h-[57px] rounded-full object-contain"
          />
          <div class="flex flex-col leading-[1.1] gap-2">
            <div
              class="bena-title font-poppins font-semibold text-[17px] leading-[30px] tracking-[0%] text-black"
            >
              Ask Ijeoma
            </div>
            <div
              class="bena-status text-xs text-[#0b1b12] opacity-70 flex items-center gap-1.5"
            >
              <span
                class="bena-status-dot w-2 h-2 bg-[#20c05c] rounded-full inline-block shadow-[0_0_0_3px_rgba(32,192,92,0.15)]"
              ></span
              >Online
            </div>
          </div>
        </div>
        <button
          class="bena-close appearance-none bg-transparent border-none text-[22px] leading-[1] cursor-pointer text-primary-green hover:text-[#1e9e33]"
          aria-label="Close chat"
        >
          ×
        </button>
      </div>
      <div class="bena-chat-body p-4 flex-1 bg-white overflow-y-auto">
        <div
          class="bena-welcome bg-white border border-[#ecf2ee] text-black rounded-[18px] py-3 px-[14px] text-xs font-normal font-poppins leading-[17.6px] mb-[14px]"
        >
          Hi! I'm Ijeoma, your AI agent meant to help you in your activities
          with the Enugu Smart bus.
        </div>
        <div class="bena-quick-actions flex flex-col gap-2.5">
          <button
            class="bena-chip appearance-none bg-white border border-black text-black py-2.5 px-3 rounded-full text-left cursor-pointer text-sm font-normal font-poppins leading-[17.6px] hover:bg-[#f7faf8]"
          >
            Register on Enugu Smart Bus Social Register?
          </button>
          <button
            class="bena-chip appearance-none bg-white border border-black text-black py-2.5 px-3 rounded-full text-left cursor-pointer text-sm font-normal font-poppins leading-[17.6px] hover:bg-[#f7faf8]"
          >
            Make payment to Smart Bus account
          </button>
          <button
            class="bena-chip appearance-none bg-white border border-black text-black py-2.5 px-3 rounded-full text-left cursor-pointer text-sm font-normal font-poppins leading-[17.6px] hover:bg-[#f7faf8]"
          >
            Register your new account
          </button>
        </div>
      </div>
      <div
        class="bena-chat-footer flex gap-2 p-4 m-[15px] border-t border-[#eef2f3] bg-[#d9d9d9]"
      >
        <input
          type="text"
          class="bena-input flex-1 border border-black rounded px-3 py-3 outline-none text-sm placeholder:text-[#9aa7a0]"
          placeholder="Type your question here..."
        />
        <button
          class="bena-send bg-primary-green text-white border-none rounded-lg py-0 px-[14px] cursor-pointer"
          aria-label="Send"
        >
          <i class="fas fa-paper-plane pointer-events-none"></i>
        </button>
      </div>
    </div>
  </body>

</html>
