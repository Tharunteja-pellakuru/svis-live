<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$current_page = basename($_SERVER['PHP_SELF']);
$current_page = str_replace('.php', '', $current_page);
?>

<!-- Font Awesome for Social Icons -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
/>

<nav
  class="fixed top-6 left-1/2 gap-6 lg:gap-8 transform -translate-x-1/2 z-50
         w-[90%]
         h-[80px] sm:h-[90px] lg:h-[90px] xl:h-[100px]
         rounded-[12px] lg:rounded-[16px]
         border border-white/20 bg-white shadow-lg"
>
  <div class="h-full px-3 sm:px-4 md:px-6 lg:px-8 xl:px-10">
    <div class="flex justify-between items-center h-full">
      <!-- Left Logo (smaller on lg, bigger on xl) -->
      <a class="flex items-center" href="index.php">
        <div
          class="h-10 sm:h-12 lg:h-12 xl:h-16
                 rounded-lg flex items-center justify-center
                 overflow-hidden relative"
        >
          <div class="h-full w-auto object-contain py-0">
            <img
              src="Logo/Logo.png"
              alt="SVIS Alumni School of Excellence Logo"
              class="h-7 sm:h-8 lg:h-9 xl:h-12 w-auto object-contain"
            />
          </div>
        </div>
      </a>

      <!-- Desktop Navigation (show at xl breakpoint 1280px+) -->
      <div class="hidden xl:flex items-center justify-center flex-1 gap-4 2xl:gap-8">
        <a
          href="index.php"
          class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base <?php echo $current_page=='index'?'active':'' ?>"
        >
          Home
        </a>
        <a
          href="directory.php"
          class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base <?php echo $current_page=='directory'?'active':'' ?>"
        >
          Directory
        </a>
        <a
          href="event.php"
          class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base <?php echo $current_page=='events'?'active':'' ?>"
        >
          Events
        </a>
        <a
          href="about.php"
          class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base whitespace-nowrap <?php echo $current_page=='about'?'active':''; ?>"
        >
          About School
        </a>
        <a
          href="founders.php"
          class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base <?php echo $current_page=='founders'?'active':''; ?>"
        >
          Founders
        </a>
        <a
          href="gallery.php"
          class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base <?php echo $current_page=='gallery'?'active':'' ?>"
        >
          Gallery
        </a>
        <a
          href="videos.php"
          class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base <?php echo $current_page=='videos'?'active':'' ?>"
        >
          Videos
        </a>

        <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
          <a
            href="profileedit.php"
            class="nav-link text-xs sm:text-sm lg:text-sm xl:text-base <?php echo $current_page=='profileedit'?'active':'' ?>"
          >
            Profile
          </a>
          <a
            href="logout.php"
            class="nav-link bg-[#1D4ED8] text-white border-2 border-[#fbbf24]
                   w-[120px] h-[40px]
                   flex items-center justify-center rounded-full
                   text-xs sm:text-sm lg:text-sm xl:text-base
                   hover:bg-[#1741b0] hover:scale-105 hover:shadow-[0_6px_30px_rgba(29,78,216,0.55)] transition-all duration-300"
          >
            Logout
          </a>
        <?php } else { ?>
          <a
            href="#"
            class="nav-link bg-[#1D4ED8] text-white border-2 border-[#fbbf24]
                   w-[120px] h-[40px]
                   flex items-center justify-center rounded-full
                   text-xs sm:text-sm lg:text-sm xl:text-base
                   hover:bg-[#1741b0] hover:scale-105 hover:shadow-[0_6px_30px_rgba(29,78,216,0.55)] transition-all duration-300"
            onclick="showModal('login')"
          >
            Login
          </a>
        <?php } ?>
      </div>



      <!-- Right Logo (same sizing logic as left) -->
      <a class="flex items-center" href="index.php">
        <div
          class="h-10 sm:h-12 lg:h-12 xl:h-16
                 rounded-lg flex items-center justify-center
                 overflow-hidden relative"
        >
          <div class="h-full w-auto object-contain py-1">
            <img
              src="Logo/Logo.png"
              alt="SVIS Alumni School of Excellence Logo"
              class="h-7 sm:h-8 lg:h-9 xl:h-12 w-auto object-contain"
            />
          </div>
        </div>
      </a>

            <!-- Mobile Hamburger Icon (centered between logos) -->
      <button
        id="hamburger-btn"
        class="xl:hidden text-gray-600 hover:text-school-blue
               p-1.5 sm:p-2 rounded-md transition-all duration-300"
        aria-label="Toggle Menu"
      >
        <svg
          id="hamburger-icon"
          class="w-6 h-6 sm:w-7 sm:h-7 transition-transform duration-300"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path id="top-line" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16"></path>
          <path id="middle-line" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12h16"></path>
          <path id="bottom-line" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18h16"></path>
        </svg>
      </button>
    </div>
  </div>
</nav>


<!-- Mobile Menu -->
<div id="mobile-menu" class="hidden xl:hidden fixed left-1/2 transform -translate-x-1/2 w-[95%] max-w-md bg-white shadow-2xl border border-white/20 rounded-2xl overflow-hidden transition-all duration-300 ease-in-out origin-top opacity-0 scale-y-0 z-40" style="top: 105px;">
  <div class="flex flex-col items-center justify-center space-y-2 sm:space-y-3 py-4 sm:py-6 text-gray-700 font-medium">
      <a href="index.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='index'?'active':''; ?>">Home</a>
      <a href="directory.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='directory'?'active':''; ?>">Directory</a>
      <a href="event.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='events'?'active':''; ?>">Events</a>
      <a href="gallery.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='gallery'?'active':''; ?>">Gallery</a>
      <a href="videos.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='videos'?'active':''; ?>">Videos</a>
      <a href="about.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='about'?'active':''; ?>">About School</a>
      <a href="founders.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='founders'?'active':''; ?>">Founders</a>
    <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
      <a href="profileedit.php" class="nav-link text-center w-full py-2 text-sm sm:text-base hover:bg-white/40 transition-colors  <?php echo $current_page=='profileedit'?'active':''; ?>">Profile</a>
      <a href="logout.php" class="block bg-[#1D4ED8] text-white border-2 border-[#fbbf24] w-[120px] h-[40px] flex items-center justify-center rounded-full hover:bg-[#1741b0] hover:scale-105 hover:shadow-[0_6px_30px_rgba(29,78,216,0.55)] transition-all duration-300 mt-2 mx-auto text-sm sm:text-base">Logout</a>
    <?php } else { ?>
      <a href="#" class="block bg-[#1D4ED8] text-white border-2 border-[#fbbf24] w-[120px] h-[40px] flex items-center justify-center rounded-full hover:bg-[#1741b0] hover:scale-105 hover:shadow-[0_6px_30px_rgba(29,78,216,0.55)] transition-all duration-300 mt-2 mx-auto text-sm sm:text-base" onclick="showModal('login')">Login</a>
    <?php } ?>
  </div>
</div>

<script>
    const menuBtn = document.getElementById("hamburger-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    const hamburgerIcon = document.getElementById("hamburger-icon");

    if(menuBtn && mobileMenu){
        menuBtn.addEventListener("click", () => {
            if (mobileMenu.classList.contains("hidden")) {
                // Open menu
                mobileMenu.classList.remove("hidden");
                setTimeout(() => {
                    mobileMenu.classList.remove("scale-y-0", "opacity-0");
                }, 10);
                // Animate hamburger to X
                if(hamburgerIcon) {
                    hamburgerIcon.style.transform = "rotate(90deg)";
                }
            } else {
                // Close menu
                mobileMenu.classList.add("scale-y-0", "opacity-0");
                setTimeout(() => {
                    mobileMenu.classList.add("hidden");
                }, 300);
                // Animate X back to hamburger
                if(hamburgerIcon) {
                    hamburgerIcon.style.transform = "rotate(0deg)";
                }
            }
        });

        // Close menu when clicking outside
        document.addEventListener("click", (e) => {
            if (!menuBtn.contains(e.target) && !mobileMenu.contains(e.target) && !mobileMenu.classList.contains("hidden")) {
                mobileMenu.classList.add("scale-y-0", "opacity-0");
                setTimeout(() => {
                    mobileMenu.classList.add("hidden");
                }, 300);
                if(hamburgerIcon) {
                    hamburgerIcon.style.transform = "rotate(0deg)";
                }
            }
        });
    }
</script>
