// JavaScript for Benue State E-Government System

document.addEventListener("DOMContentLoaded", function () {
  // Debug: Check image loading and set up loading animations
  const images = document.querySelectorAll("img");
  images.forEach((img, index) => {
    // Check if image is already loaded (cached images)
    if (img.complete && img.naturalHeight !== 0) {
      // Image is already loaded, show it immediately
      img.style.opacity = "1";
      img.style.transition = "opacity 0.3s ease";
    } else {
      // Image not loaded yet, set up fade-in animation
      img.style.opacity = "0";
      img.style.transition = "opacity 0.3s ease";

      img.addEventListener("load", function () {
        this.style.opacity = "1";
      });
    }

    img.addEventListener("error", function () {
      this.style.opacity = "1";
    });
  });

  // Smooth scrolling for navigation links
  const anchorLinks = document.querySelectorAll('.nav-links a[href^="#"]');
  anchorLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");
      const targetSection = document.querySelector(targetId);
      if (targetSection) {
        targetSection.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }
    });
  });

  // Header scroll effect - Keep header transparent to show background image
  // Removed background color change on scroll to maintain transparency

  // Mobile dropdown toggle functionality
  const mobileDropdownToggles = document.querySelectorAll(
    "[data-dropdown-toggle]"
  );
  mobileDropdownToggles.forEach((toggle) => {
    toggle.addEventListener("click", function (e) {
      // Only handle on mobile devices (desktop uses hover)
      if (window.innerWidth < 1024) {
        e.preventDefault();
        e.stopPropagation();

        const dropdownItem = this.closest(".mobile-dropdown-item");
        if (dropdownItem) {
          // Toggle active class
          dropdownItem.classList.toggle("active");

          // Close other dropdowns if any
          document.querySelectorAll(".mobile-dropdown-item").forEach((item) => {
            if (item !== dropdownItem && item.classList.contains("active")) {
              item.classList.remove("active");
            }
          });
        }
      }
      // On desktop, allow normal link behavior (hover handles dropdown)
    });
  });

  // Close mobile menu when clicking on dropdown menu items
  const mobileDropdownLinks = document.querySelectorAll(
    ".mobile-dropdown-menu a"
  );
  mobileDropdownLinks.forEach((link) => {
    link.addEventListener("click", function () {
      if (window.innerWidth < 1024) {
        const topBarLinks = document.querySelector(".top-bar-links");
        const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
        if (topBarLinks && mobileMenuToggle) {
          topBarLinks.classList.remove("active");
          mobileMenuToggle.classList.remove("active");
          document.body.style.overflow = "";
        }
      }
    });
  });

  // Mobile menu toggle (for responsive design)
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const topBarLinks = document.querySelector(".top-bar-links");

  if (mobileMenuToggle && topBarLinks) {
    // Function to close menu
    function closeMenu() {
      topBarLinks.classList.remove("active");
      mobileMenuToggle.classList.remove("active");
      document.body.style.overflow = "";
    }

    // Function to open menu
    function openMenu() {
      topBarLinks.classList.add("active");
      mobileMenuToggle.classList.add("active");
      document.body.style.overflow = "hidden";
    }

    // Toggle menu on hamburger click
    mobileMenuToggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (topBarLinks.classList.contains("active")) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    // Close menu when clicking on a link (but not dropdown toggles)
    const navLinkItems = topBarLinks.querySelectorAll("a");
    navLinkItems.forEach((link) => {
      link.addEventListener("click", function (e) {
        // Don't close if it's a dropdown toggle
        if (this.hasAttribute("data-dropdown-toggle")) {
          return;
        }
        // Close menu when clicking on actual navigation links
        if (window.innerWidth < 1024) {
          closeMenu();
        }
      });
    });

    // Close menu when clicking on the overlay (dark background)
    topBarLinks.addEventListener("click", function (e) {
      if (e.target === topBarLinks) {
        closeMenu();
      }
    });

    // Close menu when clicking outside (on ul, but not on links)
    const topBarLinksUl = topBarLinks.querySelector(".mobile-nav-list");
    if (topBarLinksUl) {
      topBarLinksUl.addEventListener("click", function (e) {
        e.stopPropagation();
      });
    }

    // Close menu when clicking on mobile auth buttons
    const mobileAuthButtons = topBarLinks.querySelectorAll(
      ".mobile-auth-buttons button"
    );
    mobileAuthButtons.forEach((button) => {
      button.addEventListener("click", function () {
        if (window.innerWidth < 1024) {
          closeMenu();
        }
      });
    });

    // Close menu on window resize if desktop size (lg breakpoint = 1024px)
    window.addEventListener("resize", function () {
      if (window.innerWidth >= 1024) {
        closeMenu();
      }
    });
  }

  // Newsletter form submission
  const newsletterForm = document.querySelector(".newsletter-form");
  const newsletterInput = document.querySelector(".newsletter-input");
  const subscribeBtn = newsletterForm?.querySelector(".btn-subscribe");

  if (newsletterForm && newsletterInput && subscribeBtn) {
    newsletterForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const email = newsletterInput.value.trim();

      if (email && isValidEmail(email)) {
        // Simulate form submission
        subscribeBtn.textContent = "Subscribing...";
        subscribeBtn.disabled = true;

        setTimeout(() => {
          subscribeBtn.textContent = "Subscribed!";
          subscribeBtn.style.background = "#4CAF50";
          newsletterInput.value = "";

          setTimeout(() => {
            subscribeBtn.textContent = "Subscribe";
            subscribeBtn.disabled = false;
            subscribeBtn.style.background = "#4CAF50";
          }, 2000);
        }, 1000);
      } else {
        showNotification("Please enter a valid email address", "error");
      }
    });
  }

  // Email validation function
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Notification system
  function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === "error" ? "#f44336" : "#4CAF50"};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.transform = "translateX(0)";
    }, 100);

    setTimeout(() => {
      notification.style.transform = "translateX(100%)";
      setTimeout(() => {
        document.body.removeChild(notification);
      }, 300);
    }, 3000);
  }

  // Service cards hover effect enhancement
  const serviceCards = document.querySelectorAll(".service-card");
  serviceCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-10px) scale(1.02)";
    });

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0) scale(1)";
    });
  });

  // Solution cards hover effect enhancement
  const solutionCards = document.querySelectorAll(".solution-card");
  solutionCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-10px) scale(1.02)";
    });

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0) scale(1)";
    });
  });

  // News cards hover effect enhancement
  const newsCards = document.querySelectorAll(".news-card");
  newsCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-10px) scale(1.02)";
    });

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0) scale(1)";
    });
  });

  // Button click animations
  const buttons = document.querySelectorAll(
    ".btn-primary, .btn-view-more, .btn-login, .btn-register"
  );
  buttons.forEach((button) => {
    button.addEventListener("click", function (e) {
      // Create ripple effect
      const ripple = document.createElement("span");
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;

      ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;

      this.style.position = "relative";
      this.style.overflow = "hidden";
      this.appendChild(ripple);

      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });

  // Add ripple animation CSS
  const style = document.createElement("style");
  style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
  document.head.appendChild(style);

  // Intersection Observer for animations
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1";
        entry.target.style.transform = "translateY(0)";
      }
    });
  }, observerOptions);

  // Observe elements for animation
  const animatedElements = document.querySelectorAll(
    ".service-card, .solution-card, .news-card, .section-title"
  );
  animatedElements.forEach((el) => {
    el.style.opacity = "0";
    el.style.transform = "translateY(30px)";
    el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(el);
  });

  // Phone mockup interactions
  const phones = document.querySelectorAll(".phone");
  phones.forEach((phone) => {
    phone.addEventListener("mouseenter", function () {
      this.style.transform = "rotateY(5deg) rotateX(5deg)";
      this.style.transition = "transform 0.3s ease";
    });

    phone.addEventListener("mouseleave", function () {
      this.style.transform = "rotateY(0) rotateX(0)";
    });
  });

  // Download button interactions
  const downloadBtns = document.querySelectorAll(".download-btn");
  downloadBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      showNotification("Download link will be available soon!", "info");
    });
  });

  // View More button interactions
  const viewMoreBtns = document.querySelectorAll(".btn-view-more");
  viewMoreBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      showNotification("Service details will be available soon!", "info");
    });
  });

  // Read More link interactions
  const readMoreLinks = document.querySelectorAll(".read-more-link");
  readMoreLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      showNotification("More information will be available soon!", "info");
    });
  });

  // Language selector functionality
  const languageSelector = document.querySelector(".language-selector select");
  if (languageSelector) {
    languageSelector.addEventListener("change", function () {
      showNotification(
        `Language changed to ${this.value.toUpperCase()}`,
        "info"
      );
    });
  }

  // Social media link interactions
  const socialLinks = document.querySelectorAll(".social-icons a");
  socialLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const platform = this.querySelector("i").className.includes("facebook")
        ? "Facebook"
        : this.querySelector("i").className.includes("instagram")
        ? "Instagram"
        : "Twitter";
      showNotification(`${platform} page will open in a new tab`, "info");
    });
  });

  // Auth button interactions
  const authBtns = document.querySelectorAll(".btn-login, .btn-register");
  authBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      // If it's a link with href, allow normal navigation
      if (this.tagName === "A" && this.getAttribute("href")) {
        return; // Allow default link behavior
      }
      // Otherwise, it's a button, show toast
      e.preventDefault();
      const action = this.classList.contains("btn-login")
        ? "Login"
        : "Register";
      showNotification(`${action} form will be available soon!`, "info");
    });
  });

  if (newsletterInput) {
    newsletterInput.addEventListener("focus", function () {
      this.style.borderColor = "#4CAF50";
      this.style.boxShadow = "0 0 0 3px rgba(76, 175, 80, 0.1)";
    });

    newsletterInput.addEventListener("blur", function () {
      this.style.borderColor = "#ddd";
      this.style.boxShadow = "none";
    });
  }

  // Scroll to top functionality
  const scrollToTopBtn = document.createElement("button");
  scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
  scrollToTopBtn.className = "scroll-to-top";
  scrollToTopBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    `;

  document.body.appendChild(scrollToTopBtn);

  window.addEventListener("scroll", function () {
    if (window.pageYOffset > 300) {
      scrollToTopBtn.style.opacity = "1";
      scrollToTopBtn.style.visibility = "visible";
    } else {
      scrollToTopBtn.style.opacity = "0";
      scrollToTopBtn.style.visibility = "hidden";
    }
  });

  scrollToTopBtn.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

  // Image loading animation is handled above in the initial images setup

  // Form validation enhancement
  const forms = document.querySelectorAll("form");
  forms.forEach((form) => {
    const inputs = form.querySelectorAll(
      'input[type="email"], input[type="text"]'
    );
    inputs.forEach((input) => {
      input.addEventListener("blur", function () {
        if (this.type === "email" && this.value) {
          if (isValidEmail(this.value)) {
            this.style.borderColor = "#4CAF50";
          } else {
            this.style.borderColor = "#f44336";
          }
        }
      });
    });
  });

  // Keyboard navigation enhancement
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      // Close any open modals or menus
      const mobileMenu = document.querySelector(".nav-links");
      const mobileToggle = document.querySelector(".mobile-menu-toggle");
      if (mobileMenu && mobileMenu.classList.contains("active")) {
        mobileMenu.classList.remove("active");
        if (mobileToggle) mobileToggle.classList.remove("active");
      }
    }
  });

  // Performance optimization: Lazy loading for images
  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute("data-src");
            imageObserver.unobserve(img);
          }
        }
      });
    });

    const lazyImages = document.querySelectorAll("img[data-src]");
    lazyImages.forEach((img) => imageObserver.observe(img));
  }

  // Ask Bena chat widget interactions
  const askBenaBtn = document.querySelector(".ask-bena-button");
  const benaChat = document.querySelector(".bena-chat");
  const benaClose = document.querySelector(".bena-close");
  if (askBenaBtn && benaChat) {
    const toggleChat = () => benaChat.classList.toggle("open");
    const closeChat = () => benaChat.classList.remove("open");
    askBenaBtn.addEventListener("click", toggleChat);
    if (benaClose) benaClose.addEventListener("click", closeChat);
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeChat();
    });
  }
});
