
      <div
        id="login-modal"
        class="modal-hidden fixed inset-0 bg-black/40 z-[60] flex items-center justify-center p-4"
        style="backdrop-filter: blur(8px)"
      >
        <div
          class="bg-white rounded-2xl shadow-2xl relative w-full max-w-md overflow-hidden"
        >
        
          <div
            class="px-6 py-8 text-center"
            style="background-color: #F4E3DF;"
          >
                      <div
              class="rounded-xl flex items-center justify-center mx-auto mb-4 p-5 gap-6"
            >
                  <img
                    src="Logo/Logo.png"
                    alt="SVIS Alumni Logo"
                    class="h-12 w-auto"
                  />
                  <img
                    src="Logo/Logo.png"
                    alt="SVIS Logo"
                    class="h-12 w-auto"
                  />
            </div>
          
            <h2 class="text-2xl font-bold text-[#41164B] mb-1">Welcome</h2>
            <p class="text-[#41164B]/70 text-sm">Sign in to continue</p>
          </div>
          <button
            class="absolute right-4 top-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 hover:bg-white text-gray-600 hover:text-gray-900 transition-all duration-200 shadow-md hover:shadow-lg"
            aria-label="Close"
            onclick="hideModal('login')"
          >
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2.5"
                d="M6 18L18 6M6 6l12 12"
              ></path>
            </svg>
          </button>
          <div class="p-6 md:p-8">
            <form  method="POST" action="login_code.php">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2"
                  >Email Address<span class="text-red-500 ml-1">*</span></label
                >
                <div class="relative">
                  <div
                    class="absolute left-3 top-1/2 transform -translate-y-1/2"
                  >
                    <svg
                      class="w-5 h-5 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                      ></path>
                    </svg>
                  </div>
                  <input
                    placeholder="Enter your email"
                    required=""
                    class="w-full pl-10 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-school-blue focus:border-transparent transition-all duration-200 border-gray-300 focus:border-school-blue"
                    type="email"
                    name="email"
                  />
                </div>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2"
                  >Password<span class="text-red-500 ml-1">*</span></label
                >
                <div class="relative">
                  <div
                    class="absolute left-3 top-1/2 transform -translate-y-1/2"
                  >
                    <svg
                      class="w-5 h-5 text-gray-400"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                      ></path>
                    </svg>
                  </div>
                  <input
                    id="login-password"
                    placeholder="Enter your password"
                    required=""
                    class="w-full pl-10 pr-10 py-3 border rounded-lg focus:ring-2 focus:ring-school-blue focus:border-transparent transition-all duration-200 border-gray-300 focus:border-school-blue"
                    type="password"
                    name="password"
                  />
                  <button
                    type="button"
                    id="login-toggle-password"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                    aria-label="Show Password"
                    onclick="togglePassword('login-password', 'login-toggle-password')"
                  >
                    <svg
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                      ></path>
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                      ></path>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <!-- <label class="flex items-center cursor-pointer"
                  >
                  <input
                    class="w-4 h-4 text-school-blue border-gray-300 rounded focus:ring-school-blue focus:ring-2"
                    type="checkbox"
                    name="rememberMe"
                  /><span class="ml-2 text-sm text-gray-700"
                    >Remember Me</span
                  ></label -->
                
                <!-- <button
                  type="button"
                  class="text-sm text-school-blue hover:text-blue-700 font-medium transition-colors"
                >
                  Forgot Password?
                </button> -->
              </div>
              <!-- <button
                type="submit"
                class="px-6 py-3 rounded-lg font-semibold text-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-gradient-to-r from-school-blue to-blue-600 text-white hover:from-blue-700 hover:to-blue-800 focus:ring-school-blue shadow-lg hover:shadow-xl w-full mt-6"
              >
                Login
              </button> -->

            <button type="submit" class="px-6 py-3 rounded-full font-semibold text-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-[#1D4ED8] text-white border-2 border-[#fbbf24] shadow-lg hover:bg-[#1741b0] hover:scale-105 hover:shadow-[0_6px_30px_rgba(29,78,216,0.55)] w-full mt-6">Login</button>

              <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-4 bg-white text-gray-500"
                    >Or continue with</span
                  >
                </div>
              </div>
              <div class="text-center pt-2">
                <p class="text-gray-600 text-sm">
                  Don't have an account?
                  <button
                    type="button"
                    class="text-school-orange hover:text-orange-600 font-semibold transition-colors"
                    onclick="showModal('register')"
                  >
                    Register here
                  </button>
                </p>
              </div>
            </form>
          </div>
        </div>
      </div>



    </div>



          <footer class="bg-[#41164B] text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
              <h3 class="text-[#EA9856] mb-4 text-[21px] italic font-normal leading-normal font-['Playfair_Display']">
                Quick Links
              </h3>
              <ul class="space-y-4 text-gray-300 text-sm">
                <li>
                  <a
                    class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] <?= ($current_page=='index') ? 'active' : '' ?>"
                    href="index.php"
                    >Home</a
                  >
                </li>
                <li>
                  <a
                    href="directory.php"
                    class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] <?= ($current_page=='directory') ? 'active' : '' ?>"
                    >Directory</a
                  >
                </li>
                <li>
                  <a
                    href="event.php"
                    class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] <?= ($current_page=='events') ? 'active' : '' ?>"
                    >Events</a
                  >
                </li>
                <li>
                  <a
                    class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] <?= ($current_page=='gallery') ? 'active' : '' ?>"
                    href="gallery.php"
                    >Gallery</a
                  >
                </li>
                  <li>
                  <a
                    class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] <?= ($current_page=='videos') ? 'active' : '' ?>"
                    href="videos.php"
                    >Videos</a
                  >
                </li>
                <li>
                  <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
                    <a
                      class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] <?= ($current_page=='logout') ? 'active' : '' ?>"
                      href="logout.php"
                      > Logout </a
                    >
                  <?php } else { ?>
                    <a
                      class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] cursor-pointer"
                      onclick="showModal('login')"
                      > Login </a
                    >
                  <?php } ?>
                </li>
                <li>
                  <a
                    href="privacy-policy.php"
                    class="text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins] <?= ($current_page=='privacy-policy') ? 'active' : '' ?>"
                    >Privacy Policy</a
                  >
                </li>
                <li>
                  <a
                    href="terms_use.php"
                    class="<?= ($current_page=='terms_use') ? 'text-white font-bold' : 'text-[#E0B4B2] font-normal' ?> text-[14px] leading-normal font-[Poppins]"
                    >Terms & Use</a
                  >
                </li>
              </ul>
            </div>
            <div>
              <h3 class="text-[#EA9856] text-[21px] mb-4 italic font-normal leading-normal font-['Playfair_Display']">
                Contact Info
              </h3>
              <div class="space-y-2 text-sm">
                <div class="flex items-start gap-3 text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins]">
                  <div class="mt-1 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="14" viewBox="0 0 11 14" fill="none">
                      <path d="M10 5.5C10 4.30653 9.52589 3.16193 8.68198 2.31802C7.83807 1.47411 6.69347 1 5.5 1C4.30653 1 3.16193 1.47411 2.31802 2.31802C1.47411 3.16193 1 4.30653 1 5.5C1 7.346 2.477 9.752 5.5 12.634C8.523 9.752 10 7.346 10 5.5ZM5.5 14C1.833 10.667 0 7.833 0 5.5C0 4.04131 0.579463 2.64236 1.61091 1.61091C2.64236 0.579463 4.04131 0 5.5 0C6.95869 0 8.35764 0.579463 9.38909 1.61091C10.4205 2.64236 11 4.04131 11 5.5C11 7.833 9.167 10.667 5.5 14Z" fill="#EA9856"/>
                      <path d="M5.5 7C5.89782 7 6.27936 6.84196 6.56066 6.56066C6.84196 6.27936 7 5.89782 7 5.5C7 5.10218 6.84196 4.72064 6.56066 4.43934C6.27936 4.15804 5.89782 4 5.5 4C5.10218 4 4.72064 4.15804 4.43934 4.43934C4.15804 4.72064 4 5.10218 4 5.5C4 5.89782 4.15804 6.27936 4.43934 6.56066C4.72064 6.84196 5.10218 7 5.5 7ZM5.5 8C4.83696 8 4.20107 7.73661 3.73223 7.26777C3.26339 6.79893 3 6.16304 3 5.5C3 4.83696 3.26339 4.20107 3.73223 3.73223C4.20107 3.26339 4.83696 3 5.5 3C6.16304 3 6.79893 3.26339 7.26777 3.73223C7.73661 4.20107 8 4.83696 8 5.5C8 6.16304 7.73661 6.79893 7.26777 7.26777C6.79893 7.73661 6.16304 8 5.5 8Z" fill="#EA9856"/>
                    </svg>
                  </div>
                  <div>
                    
                    150-152 Jayabheri Park, Behind Cine Planet Multiplex, Kompally, Hyderabad – 500100, Telangana
                  </div>
                </div>
                <div class="flex items-center gap-3 text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins]">
                  <div class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                      <path d="M3.46622 8.42624C2.18752 7.14505 1.20414 5.59998 0.584875 3.89909C0.24418 2.96916 0.556335 1.94767 1.25675 1.24725L1.6902 0.814395C1.80673 0.697629 1.94515 0.604992 2.09753 0.541786C2.2499 0.478579 2.41325 0.446045 2.57821 0.446045C2.74318 0.446045 2.90652 0.478579 3.0589 0.541786C3.21128 0.604992 3.34969 0.697629 3.46622 0.814395L4.48117 1.82935C4.59794 1.94588 4.69057 2.08429 4.75378 2.23667C4.81699 2.38904 4.84952 2.55239 4.84952 2.71736C4.84952 2.88232 4.81699 3.04567 4.75378 3.19804C4.69057 3.35042 4.59794 3.48883 4.48117 3.60536L4.23145 3.85509C4.13149 3.95502 4.0522 4.07367 3.9981 4.20426C3.944 4.33484 3.91616 4.47481 3.91616 4.61615C3.91616 4.7575 3.944 4.89746 3.9981 5.02805C4.0522 5.15863 4.13149 5.27728 4.23145 5.37722L6.51464 7.66101C6.61458 7.76097 6.73323 7.84026 6.86381 7.89436C6.9944 7.94846 7.13436 7.9763 7.27571 7.9763C7.41706 7.9763 7.55702 7.94846 7.6876 7.89436C7.81819 7.84026 7.93684 7.76097 8.03677 7.66101L8.28709 7.41128C8.40362 7.29452 8.54204 7.20188 8.69441 7.13867C8.84679 7.07547 9.01014 7.04293 9.1751 7.04293C9.34007 7.04293 9.50341 7.07547 9.65579 7.13867C9.80816 7.20188 9.94658 7.29452 10.0631 7.41128L11.0781 8.42624C11.1948 8.54277 11.2875 8.68118 11.3507 8.83356C11.4139 8.98593 11.4464 9.14928 11.4464 9.31424C11.4464 9.47921 11.4139 9.64256 11.3507 9.79493C11.2875 9.94731 11.1948 10.0857 11.0781 10.2023L10.6452 10.6351C9.94479 11.3361 8.9233 11.6483 7.99337 11.3076C6.29248 10.6883 4.74741 9.70494 3.46622 8.42624Z" stroke="#EA9856" stroke-width="0.891874" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div>
                    
040-23005000
                  </div>
                </div>
                <div class="flex items-center gap-3 text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins]">
                  <div class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 14 12" fill="none">
                      <path d="M2.43749 2.47655L6.90299 6.30505C6.94849 6.34405 7.02649 6.34405 7.07199 6.30505L11.5375 2.47655M1.26749 0.487549H12.7075C13.1365 0.487549 13.4875 0.838549 13.4875 1.26755V9.32755C13.4875 10.1855 12.7855 10.8875 11.9275 10.8875H2.04749C1.18949 10.8875 0.487488 10.1855 0.487488 9.32755V1.26755C0.487488 0.838549 0.838488 0.487549 1.26749 0.487549Z" stroke="#EA9856" stroke-width="0.975" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <div>
                    info@svishyd.edu.in
                  </div>
                </div>
              </div>
            </div>
            <div>
              <h3 class="text-[#EA9856] text-[21px]  mb-4 italic font-normal leading-normal font-['Playfair_Display']">
                Location
              </h3>
              <div class="w-full h-48 bg-gray-700 rounded-lg overflow-hidden">
<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15217.29501790504!2d78.478686!3d17.539766!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb855ec1fabca7%3A0x216c99b72461c6a0!2sSadhu%20Vaswani%20International%20School!5e0!3m2!1sen!2sin!4v1778574953962!5m2!1sen!2sin" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="SVIS Location Map"></iframe>
              </div>
            </div>
          </div>
          <div class="mt-8 pt-8 border-t border-[#E0B4B2] flex flex-col lg:flex-row justify-between items-center">
            <p class="text-center text-[#E0B4B2] text-[14px] font-normal leading-normal font-[Poppins]">
              ©2025 SVIS.   |    Concept & Design by eparivartan
            </p>
            <div class="flex justify-center items-center gap-6">
  <!-- Facebook -->
            <a href="https://www.facebook.com/svishydintsch" target="_blank" class="text-[#EA9856] transition duration-300">
              <i class="fab fa-facebook-f text-xl"></i>
              </a>

            <!-- Instagram -->
            <a href="#" class="text-[#EA9856] transition duration-300">
              <i class="fab fa-instagram text-xl"></i>
            </a>

            <!-- YouTube -->
            <a href="#" class="text-[#EA9856] transition duration-300">
              <i class="fab fa-linkedin text-xl"></i>
            </a>
            </div>

          </div>
        </div>
      </footer>

    <script>
      let currentStoryIndex = 0;
      let storySlides;
      let storyDots;
      let autoScrollTimer; // 💡 NEW: Variable to hold the timer interval

      
      // --- Timer Controls ---
      function startAutoScroll() {
        // Clear any existing timer before starting a new one
        clearInterval(autoScrollTimer);
        // Set a new interval to cycle the stories every 5000 milliseconds (5 seconds)
        autoScrollTimer = setInterval(() => {
          cycleStories(1, false); // Cycle to the next story
        }, 5000);
      }

      // Function to reset the timer on user interaction
      function resetAutoScroll() {
        startAutoScroll();
      }

      // Initialize carousel elements once the DOM is loaded
      function initializeStoryCarousel() {
        storySlides = document.querySelectorAll(".story-slide");
        storyDots = document.querySelectorAll(".story-dot");
        if (storySlides.length > 0) {
          updateStory(0);
          startAutoScroll(); // 🚀 NEW: Start auto-scroll on load
        }
      }

      // Function to show a specific slide
      function updateStory(index) {
        if (!storySlides || storySlides.length === 0) return;

        // Ensure index wraps around
        currentStoryIndex = (index + storySlides.length) % storySlides.length;

        storySlides.forEach((slide, i) => {
          if (i === currentStoryIndex) {
            // Show and set opacity to 1
            slide.classList.remove("hidden", "opacity-0");
            slide.classList.add("opacity-100");

            // Update dots
            storyDots[i].classList.remove("bg-gray-300");
            storyDots[i].classList.add("bg-school-orange");
          } else {
            // Hide and set opacity to 0
            slide.classList.remove("opacity-100");
            slide.classList.add("hidden", "opacity-0");

            // Update dots
            storyDots[i].classList.remove("bg-school-orange");
            storyDots[i].classList.add("bg-gray-300");
          }
        });
      }

      // Function to cycle the stories (called by arrow buttons and dots)
      function cycleStories(stepOrIndex, direct = false) {
        if (direct) {
          updateStory(stepOrIndex);
        } else {
          updateStory(currentStoryIndex + stepOrIndex);
        }
        resetAutoScroll(); // 🔄 NEW: Reset the timer after manual click
      }

      // --- Main DOM Initialization (Adjusted to use initializeStoryCarousel) ---
      document.addEventListener("DOMContentLoaded", () => {
        // Initialize story carousel and start auto-scroll
        initializeStoryCarousel();
      });

      // --- Modal Logic ---
      function showModal(modalType, eventTitle = null) {
        const modalId = modalType + "-modal";
        const modal = document.getElementById(modalId);
        if (modal) {
          modal.classList.remove("modal-hidden");
          modal.classList.add("modal-visible");

          if (modalType === "register" && eventTitle) {
            document.getElementById("register-modal-title").textContent =
              "Register for Event";
            document.getElementById("register-modal-subtitle").textContent =
              eventTitle;
          } else if (modalType === "register") {
            document.getElementById("register-modal-title").textContent =
              "Alumni Registration";
            document.getElementById("register-modal-subtitle").textContent =
              "Join the SVIS Alumni Network";
          }

          if (modalType === "register") {
            hideModal("login");
          }
          if (modalType === "login") {
            hideModal("register");
          }
        }
      }

      function hideModal(modalType) {
        const modalId = modalType + "-modal";
        const modal = document.getElementById(modalId);
        if (modal) {
          modal.classList.remove("modal-visible");
          modal.classList.add("modal-hidden");
        }
      }

      // Toggle Password Visibility
      function togglePassword(inputId, buttonId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = document.getElementById(buttonId);
        
        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          toggleButton.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            </svg>`;
        } else {
          passwordInput.type = "password";
          toggleButton.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>`;
        }
      }
    </script>