<?php 
include('db_connect.php'); 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SVIS - Alumni Network</title>
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>

    /* ============================================================
       NAV — Logo Left | Links Center | Login Right
       Uses CSS Grid: [logo] [links] [login+hamburger]
    ============================================================ */
    /* Navbar styles moved to shared.css */

    /* ============================================================
       SECTION TITLE SPACING
       Clear breathing room after every section title & divider
    ============================================================ */
    .section-title {
      margin-bottom: 1.25rem;
    }
    .divider-img {
      margin-top:    0.25rem;
      margin-bottom: 2.5rem;
    }

    /* ============================================================
       INDEX PAGE — Royal Blue Theme
    ============================================================ */

    /* ---- HERO ---- */
    .hero-section {
      position: relative;
      height: 100vh;
      min-height: 480px;
      overflow: hidden;
      background: #0f172a;
    }
    #heroCarousel {
      display: flex;
      width: 100%;
      height: 100%;
      transition: transform 0.7s ease-in-out;
    }
    #heroCarousel img {
      width: 100%; height: 100%;
      flex-shrink: 0;
      object-fit: cover;
    }
    .hero-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 20;
      background: rgba(255,255,255,0.18);
      border: none;
      padding: 0.65rem;
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.2s, transform 0.2s;
    }
    .hero-arrow:hover { background: rgba(255,255,255,0.38); transform: translateY(-50%) scale(1.1); }
    .hero-arrow.left  { left: 2.5%; }
    .hero-arrow.right { right: 2.5%; }
    .hero-arrow svg   { width: 18px; height: 18px; display: block; }
    .hero-overlay {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 90%;
      max-width: 1200px;
      text-align: left;
      z-index: 15;
      padding: 0;
    }
    .hero-overlay > * {
      max-width: 650px;
    }
    .hero-section::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 65%;
      background: linear-gradient(to top, rgba(15,23,42,0.80), transparent);
      pointer-events: none;
      z-index: 5;
    }
    .hero-title {
      color: #fff;
      font-family: 'lato', serif;
      font-size: clamp(1.6rem, 4vw, 3rem);
      font-weight: 700;
      line-height: 1.25;
      margin-bottom: 0.6rem;
      text-shadow: 0 2px 16px rgba(0,0,0,0.5);
    }
    .hero-subtitle {
      color: rgba(255,255,255,0.82);
      font-size: clamp(0.88rem, 1.4vw, 1.05rem);
      margin-bottom: 1.75rem;
      font-family: 'Poppins', sans-serif;
    }
    .hero-btn {
      background: #1D4ED8;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 1rem;
      padding: 1rem 2.5rem;
      border-radius: 999px;
      border: 2px solid #fbbf24;
      box-shadow: 0 4px 24px rgba(29,78,216,0.45);
      transition: background 0.3s, transform 0.2s, box-shadow 0.3s;
    }
    .hero-btn:hover { background: #1741b0; transform: scale(1.05); box-shadow: 0 6px 30px rgba(29,78,216,0.55); }

    /* ---- NEWS TICKER ---- */
    .news-ticker {
      background: #1e3a8a;
      color: #fff;
      padding: 0.45rem 0;
      overflow: hidden;
      position: relative;
    }
    .ticker-inner { 
      display: flex; 
      align-items: center; 
      padding: 0 1.5rem; 
      position: relative;
    }
    .ticker-label {
      background: #fbbf24;
      color: #1e3a8a;
      font-weight: 700;
      font-size: 0.78rem;
      padding: 0.18rem 0.8rem;
      white-space: nowrap;
      flex-shrink: 0;
      margin-right: 1rem;
      border-radius: 3px;
      position: relative;
      z-index: 10;
      box-shadow: 4px 0 10px rgba(0,0,0,0.2);
    }
    @keyframes ticker { 0%{transform:translateX(100%)} 100%{transform:translateX(-100%)} }
    .ticker-text {
      white-space: nowrap;
      animation: ticker 30s linear infinite;
      font-size: 0.84rem;
      color: #BFDBFE;
      position: relative;
      z-index: 1;
    }

    /* ---- STATS BAR ---- */
    .stats-bar {
      background: linear-gradient(135deg, #1e3a8a 0%, #1D4ED8 100%);
      padding: 2.5rem 0;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    .stat-item { text-align: center; }
    .stat-num {
      font-size: 2rem;
      font-weight: 700;
      color: #fbbf24;
      font-family: 'lato', serif;
      text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .stat-lbl {
      font-size: 0.8rem;
      color: #BFDBFE;
      margin-top: 0.75rem;
      font-family: 'Poppins', sans-serif;
      letter-spacing: 0.03em;
    }

    /* ---- ABOUT ---- */
    .about-section {
      background: #fff;
      padding: 5rem 0;
    }
    .about-grid { display: flex; flex-wrap: wrap; gap: 2.5rem; align-items: stretch; }
    .about-text {
      flex: 1 1 55%;
      color: #1e3a8a;
      font-family: 'Poppins', sans-serif;
      font-size: clamp(14px, 1.05vw, 16px);
      line-height: 1.9;
      text-align: justify;
    }
    .about-text p + p { margin-top: 0.85rem; }
    .about-text strong { font-weight: 700; color: #1D4ED8; }
    .about-img {
      flex: 1 1 35%;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(29,78,216,0.18);
      min-height: 220px;
      border: 3px solid #BFDBFE;
    }
    .about-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .event-img img {
      width: 100%;
      height: 160px;
      object-fit: cover;
    }

    .event-content {
      padding: 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 0.6rem;
    }
    .read-more-link { color: #1D4ED8; font-weight: 700; }
    .read-more-link:hover { text-decoration: underline; }

    /* ---- HIGHLIGHTS ---- */
    .highlights-section { padding: 4rem 0; background: #f0f4ff; }
    .highlights-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.5rem;
    }
    .highlight-card {
      background: #fff;
      border-radius: 14px;
      padding: 2rem 1.4rem;
      text-align: center;
      box-shadow: 0 2px 12px rgba(29,78,216,0.08);
      border-bottom: 4px solid #1D4ED8;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .highlight-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(29,78,216,0.15); }
    .highlight-icon {
      width: 58px; height: 58px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1e3a8a, #1D4ED8);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1rem;
      font-size: 1.3rem;
      color: #fff;
      box-shadow: 0 4px 14px rgba(29,78,216,0.3);
    }
    .highlight-card h3 { font-size: 0.97rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.45rem; }
    .highlight-card p  { font-size: 0.83rem; color: #6b7280; line-height: 1.65; }

    /* ---- ALUMNI ---- */
    .alumni-section { padding: 4rem 0; background: #fff; }

    /* ---- VISION / MISSION ---- */
    .vision-section { padding: 4rem 0; background: #EFF6FF; }
    .vm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem; }
    .vm-card {
      background: #fff;
      border-radius: 14px;
      padding: 1.75rem;
      box-shadow: 0 2px 12px rgba(29,78,216,0.08);
      border-top: 4px solid #1D4ED8;
      transition: box-shadow 0.3s, transform 0.3s;
    }
    .vm-card:hover { box-shadow: 0 8px 28px rgba(29,78,216,0.14); transform: translateY(-2px); }
    .vm-card h3 { color: #1e3a8a; font-family: 'lato', serif; font-size: 1.1rem; margin-bottom: 0.7rem; }
    .vm-card p  { color: #374151; font-size: 0.88rem; line-height: 1.8; }

    /* ---- GALLERY ---- */
    .gallery-section { padding: 4rem 0; background: #fff; }
    .gallery-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 1.25rem; }
    .gallery-item {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(29,78,216,0.12);
      width: 100%;
      max-width: 340px;
      height: 230px;
      cursor: pointer;
      border: 2px solid #BFDBFE;
    }
    .gallery-item img.g-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
    .gallery-item:hover img.g-img { transform: scale(1.07); }
    .gallery-item .g-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0); transition: background 0.3s; }
    .gallery-item:hover .g-overlay { background: rgba(29,78,216,0.18); }
    .gallery-label {
      position: absolute; bottom: 0; left: 0; right: 0;
      padding: 0.55rem 0.9rem;
      background: linear-gradient(to top, rgba(15,23,42,0.82), transparent);
      color: #fff;
      font-size: 0.8rem;
      font-weight: 600;
    }
    .gallery-border-img { width: 100%; margin-top: 3rem; }

    /* Standardize all section widths on Home Page to match Navbar */
    .section-inner {
      max-width: none !important;
      width: 90% !important;
      margin: 0 auto;
    }

    /* Grids: 4 columns for Desktop */
    .highlights-grid,
    .alumni-section .alumni-grid,
    .events-section .events-grid,
    .gallery-section .gallery-grid {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 1.5rem !important;
      width: 100% !important;
      max-width: none !important;
    }

    .alumni-card, .event-card, .gallery-item {
      width: 100% !important;
      max-width: none !important;
    }

    .events-section .event-card {
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .events-section .event-img img {
      height: 140px;
      object-fit: cover;
    }
    .events-section .event-content {
      flex: 1;
    }

    @media (max-width: 1300px) {
      .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 3rem;
      }
      .alumni-section .alumni-grid,
      .events-section .events-grid,
      .gallery-section .gallery-grid,
      .highlights-grid {
        grid-template-columns: repeat(2, 1fr) !important;
      }
    }

    @media (max-width: 768px) {
      .hero-section {
        height: 100vh;
        min-height: 480px;
      }
      .hero-overlay {
        top: auto;
        bottom: 12%;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        width: 95%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }
      .hero-overlay > * {
        max-width: 100%;
        margin-left: auto;
        margin-right: auto;
      }
      .hero-title {
        font-size: clamp(1.8rem, 8vw, 2.8rem);
        line-height: 1.2;
        margin-bottom: 1rem;
      }
      .hero-subtitle {
        font-size: 1rem;
        margin-bottom: 2rem;
      }
      .about-grid {
        flex-direction: column;
        gap: 2rem;
      }
      .about-img {
        order: -1;
      }
      .about-text {
        text-align: justify;
        order: 0;
      }
      .vm-grid {
        grid-template-columns: 1fr;
      }
      .stats-grid {
        gap: 2rem;
      }
    }

    @media (max-width: 640px) {
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .hero-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
      }
      .hero-section {
        height: 100vh;
      }
    }

    @media (max-width: 480px) {
      .hero-section {
        height: 100vh;
      }
      .alumni-section .alumni-grid,
      .events-section .events-grid,
      .gallery-section .gallery-grid,
      .highlights-grid {
        grid-template-columns: 1fr !important;
      }
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        text-align: center;
      }
      .stats-grid .stat-item:last-child {
        grid-column: span 2;
      }
      .hero-title {
        font-size: 1.5rem;
      }
      .hero-btn {
        padding: 0.7rem 1.2rem;
        font-size: 0.85rem;
        width: 100%;
        max-width: 280px;
      }
      .gallery-item {
        height: 190px;
      }
    }
  </style>
</head>
<body>

<!-- ===== NAV ===== -->
<nav class="site-nav">
  <div class="nav-inner">

    <!-- LEFT: Logo -->
    <a href="index.php" class="nav-logo">
      <img src="Logo/Logo.png" alt="SVIS Logo"/>
    </a>

    <!-- CENTER: Links -->
    <div class="nav-links">
      <a href="index.php"    class="nav-link active">Home</a>
      <a href="directory.php" class="nav-link">Directory</a>
      <a href="event.php"   class="nav-link">Events</a>
      <a href="about.php"    class="nav-link">About</a>
      <a href="founders.php" class="nav-link">Founders</a>
      <a href="gallery.php"  class="nav-link">Gallery</a>
      <a href="videos.php"   class="nav-link">Videos</a>  
      <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
        <a href="profileedit.php" class="nav-link">Profile</a>
      <?php } ?>
    </div>

    <!-- RIGHT: Login + Hamburger -->
    <div class="nav-right">
      <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
        <a href="logout.php" class="nav-login-btn">Logout</a>
      <?php } else { ?>
        <button class="nav-login-btn" onclick="showModal('login')">Login</button>
      <?php } ?>
      <button class="hamburger-btn" id="hamburger-btn" aria-label="Menu">
        <svg id="hamburger-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

  </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobile-menu">
  <a href="index.php"    class="nav-link active">Home</a>
  <a href="directory.php" class="nav-link">Directory</a>
  <a href="event.php"   class="nav-link">Events</a>
  <a href="about.php"    class="nav-link">About</a>
  <a href="founders.php" class="nav-link">Founders</a>
  <a href="gallery.php"  class="nav-link">Gallery</a>
  <a href="videos.php"   class="nav-link">Videos</a>
  <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
    <a href="profileedit.php" class="nav-link">Profile</a>
    <a href="logout.php" class="nav-login-btn">Logout</a>
  <?php } else { ?>
    <button class="nav-login-btn" onclick="showModal('login')">Login</button>
  <?php } ?>
</div>



<!-- ===== HERO ===== -->
<section class="hero-section">
  <div id="heroCarousel">
    <?php
      $scrollImages = $conn->query("SELECT * FROM home_scroll_images ORDER BY order_no ASC, id DESC");
      if ($scrollImages && $scrollImages->num_rows > 0):
        while ($img = $scrollImages->fetch_assoc()):
          $imagePath = "uploads/home_scroll/" . $img['image_name'];
          if (empty($img['image_name']) || !file_exists($imagePath)) {
            $imagePath = "Banners/image.png";
          }
    ?>
    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($img['title'] ?: 'Alumni banner') ?>"/>
    <?php
        endwhile;
      else:
    ?>
    <img src="Banners/image.png" alt="Alumni banner"/>
    <?php endif; ?>
  </div>
  <button class="hero-arrow left"  onclick="prevSlide()" aria-label="Previous">
    <svg viewBox="0 0 15 27" fill="none"><path d="M14.5649 2.16413L12.3987 0L0.600139 11.7945C0.053 12.7034 0 12.9688 0 13.2369C0 13.5051 0.053 13.7705 0.156 14.018C0.259 14.266 0.41 14.49 0.6 14.679L12.3987 26.48L14.5629 24.316L3.489 13.24L14.5649 2.16413Z" fill="white"/></svg>
  </button>
  <button class="hero-arrow right" onclick="nextSlide()" aria-label="Next">
    <svg viewBox="0 0 15 27" fill="none"><path d="M0 2.16413L2.16619 0L13.9648 11.7945C14.512 12.7034 14.5649 12.9688 14.5649 13.2369C14.5649 13.5051 14.512 13.7705 14.409 14.018C14.306 14.266 14.155 14.49 13.965 14.679L2.16619 26.48L0.002 24.316L11.076 13.24L0 2.16413Z" fill="white"/></svg>
  </button>
  <div class="hero-overlay" style="z-index:20;">
    <h1 class="hero-title">Welcome to SVIS - Alumni Network</h1>
    <p class="hero-subtitle">Sadhu Vaswani International School, Kompally, Hyderabad Established 2008</p>
    <button class="hero-btn" onclick="showModal('register')">Join The Alumni Network</button>
  </div>
</section>

<!-- ===== STATS BAR ===== -->
<div class="stats-bar">
  <div class="stats-grid">
    <div class="stat-item"><div class="stat-num">2008</div><div class="stat-lbl">Year Established</div></div>
    <div class="stat-item"><div class="stat-num">15+</div><div class="stat-lbl">Years of Excellence</div></div>
    <div class="stat-item"><div class="stat-num">5000+</div><div class="stat-lbl">Alumni Members</div></div>
    <div class="stat-item"><div class="stat-num">CBSE</div><div class="stat-lbl">Affiliated Board</div></div>
    <div class="stat-item"><div class="stat-num">4</div><div class="stat-lbl">House Systems</div></div>
  </div>
</div>

<!-- ===== ABOUT ===== -->
<section class="about-section">
  <div class="section-inner">
    <h2 class="section-title">About Sadhu Vaswani International School</h2>
    <div class="about-grid">
      <div class="about-text">
        <p><strong>SVIS Alumni – Stay Connected Forever</strong></p>
        <p>Sadhu Vaswani International School (SVIS), Kompally, Hyderabad, established in 2008, is one of the many educational institutions which the Sadhu Vaswani Mission started all over India. It is a progressive school based on Indian thought, culture, tradition and the educational ideals of Sadhu Vaswani.</p>
        <p>The aim of our school is to impart integrated and comprehensive education which is formative and not merely informative. It lays emphasis on the full development of the student's character and personality which should normally lead them to self-fulfillment and dedication to the service of society.</p>
        <p>Students are placed in an atmosphere that enables them to develop reverence for all prophets, seers and sages, all heroes of humanity, all races and religions. Our school is a non-communal, non-sectarian institution which places emphasis on the permanent values of life.</p>
        <p>This Alumni Network is dedicated to all former students of SVIS — a platform to reconnect, share your journey, and relive cherished school memories. Join us and keep the SVIS spirit alive wherever life takes you!
          <a href="about.php" class="read-more-link">... Read More</a>
        </p>
      </div>
      <div class="about-img">
        <img src="https://www.svishyd.edu.in/wp-content/uploads/2022/08/Vision-Mission-img.jpg" alt="SVIS School"
          onerror="this.style.cssText='height:100%;background:linear-gradient(135deg,#1e3a8a,#1D4ED8)';this.removeAttribute('src')"/>
      </div>
    </div>
  </div>
</section>

<!-- ===== HIGHLIGHTS ===== -->
<section class="highlights-section">
  <div class="section-inner">
    <h2 class="section-title">What Makes Our Alumni Network Special</h2>
    <div class="highlights-grid">
      <div class="highlight-card">
        <div class="highlight-icon"><i class="fas fa-graduation-cap"></i></div>
        <h3>Alumni Directory</h3>
        <p>Explore profiles of SVIS Alumni graduates and reconnect with your classmates.</p>
      </div>
      <div class="highlight-card">
        <div class="highlight-icon"><i class="fas fa-calendar-alt"></i></div>
        <h3>Alumni Events</h3>
        <p>Stay updated on annual alumni meets, reunions, networking events and school celebrations.</p>
      </div>
      <div class="highlight-card">
        <div class="highlight-icon"><i class="fas fa-images"></i></div>
        <h3>Photo Gallery</h3>
        <p>Browse through event galleries, school memories, cultural programs and more.</p>
      </div>
      <div class="highlight-card">
        <div class="highlight-icon"><i class="fas fa-hands-helping"></i></div>
        <h3>Mentorship</h3>
        <p>Connect with senior alumni for career guidance, professional growth and mentorship.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== ALUMNI CARDS ===== -->
<section class="alumni-section">
  <div class="section-inner">
    <h2 class="section-title">Our Esteemed Alumni</h2>
    <div class="alumni-grid">
      <?php
        $result = $conn->query("SELECT * FROM alumni_register WHERE verified_status=1 ORDER BY id DESC LIMIT 4");
        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
            $name = trim($row['full_name'] ?? '');
            $initial = strtoupper(substr($name ?: 'A', 0, 1));
      ?>
      <div class="alumni-card">
        <div class="alumni-avatar">
          <?php if (!empty($row['user_image'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['user_image']) ?>" alt="<?= htmlspecialchars($name) ?>" />
          <?php else: ?>
            <div class="alumni-avatar-init"><?= htmlspecialchars($initial) ?></div>
          <?php endif; ?>
        </div>
        <h3 class="alumni-name"><?= htmlspecialchars(ucfirst($name)) ?></h3>
        <p class="alumni-batch"><?= htmlspecialchars($row['batch_year'] ?? '') ?> | Batch</p>
        <?php if (!empty($row['Profession'])): ?>
          <p class="alumni-prof"><?= htmlspecialchars(ucfirst($row['Profession'])) ?></p>
        <?php endif; ?>
      </div>
      <?php
          endwhile;
        else:
      ?>
      <p>No verified alumni found yet.</p>
      <?php endif; ?>
    </div>

    <div class="view-all-wrap"><a href="directory.php" class="view-all-btn">View All Alumni</a></div>
  </div>
</section>

<!-- ===== EVENTS ===== -->
<section class="events-section">
  <div class="section-inner">

    <h2 class="section-title">Upcoming Alumni Events</h2>

    <div class="events-grid">
      <?php
        $events = $conn->query("SELECT * FROM events WHERE start_time >= CURDATE() ORDER BY start_time ASC LIMIT 4");
        if ($events && $events->num_rows > 0):
          while ($event = $events->fetch_assoc()):
      ?>
      <div class="event-card">
        <div class="event-img">
          <?php if (!empty($event['event_image'])): ?>
            <img src="uploads/<?= htmlspecialchars($event['event_image']) ?>" alt="<?= htmlspecialchars($event['event_name']) ?>">
          <?php else: ?>
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#1e3a8a,#1D4ED8);"></div>
          <?php endif; ?>
        </div>
        <div class="event-content">
          <div class="event-header">
            <span class="event-date-text">Date: <?= date("M d, Y", strtotime($event['start_time'])) ?></span>
            <span class="event-status-badge">Upcoming</span>
          </div>
          <h3><?= htmlspecialchars($event['event_name']) ?></h3>
          <div class="event-meta-item">
            <i class="fas fa-clock"></i> 
            <?= date("h:i A", strtotime($event['start_time'])) ?> – <?= date("h:i A", strtotime($event['end_time'])) ?>
          </div>
          <div class="event-meta-item">
            <i class="fas fa-map-marker-alt"></i> 
            <?= htmlspecialchars($event['venue'] ?? '') ?>
          </div>
          <p class="event-description">
            <?= htmlspecialchars($event['description'] ?? '') ?>
          </p>
        </div>
      </div>
      <?php
          endwhile;
        else:
      ?>
      <p>No upcoming events scheduled at the moment.</p>
      <?php endif; ?>
    </div>

    <div class="view-all-wrap">
      <a href="event.php" class="view-all-btn">
        View All Events
      </a>
    </div>

  </div>
</section>

<!-- ===== GALLERY ===== -->
<section class="gallery-section">
  <div class="section-inner">
    <h2 class="section-title">A Walk Through Our Journey</h2>
    <div class="gallery-grid">
      <?php
        $gallery = $conn->query("SELECT * FROM gallery ORDER BY id DESC LIMIT 4");
        if ($gallery && $gallery->num_rows > 0):
          while ($photo = $gallery->fetch_assoc()):
            $galleryTitle = $photo['title'] ?? 'Gallery Image';
      ?>
      <div class="gallery-item">
        <img class="g-img" src="uploads/<?= htmlspecialchars($photo['image_name']) ?>" alt="<?= htmlspecialchars($galleryTitle) ?>"/>
        <div class="g-overlay"></div>
        <div class="gallery-label"><?= htmlspecialchars($galleryTitle) ?></div>
      </div>
      <?php
          endwhile;
        else:
      ?>
      <p>No images found.</p>
      <?php endif; ?>
    </div>
    <div class="view-all-wrap"><a href="gallery.php" class="view-all-btn">View All</a></div>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer>
  <div class="footer-grid">

    <!-- Quick Links -->
    <div class="footer-col">
      <h3>Quick Links</h3>
      <ul>
        <li><a  href="index.php"  class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Home</a></li>
        <li><a  href="about.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : '' ?>">About</a></li>
        <li><a  href="directory.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'directory.php') ? 'active' : '' ?>"> Directory</a></li>
        <li><a  href="event.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'event.php') ? 'active' : '' ?>">Events</a></li>
        <li><a  href="gallery.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'active' : '' ?>">Gallery</a></li>
        <li><a  href="videos.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'videos.php') ? 'active' : '' ?>">Videos</a></li>
        <li><a  href="founders.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'founders.php') ? 'active' : '' ?>">Founders</a></li>
        <li><a  href="privacy-policy.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'privacy-policy.php') ? 'active' : '' ?>">Privacy Policy</a></li>
        <li><a  href="terms_use.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'terms_use.php') ? 'active' : '' ?>">Terms & Conditions</a></li>
      </ul>
    </div>

    <!-- Contact Info -->
     <div class="footer-col">
      <h3>Contact Info</h3>

      <div class="footer-contact-item">
        <i class="fas fa-map-marker-alt"></i>
        <span>
          150-152 Jayabheri Park, Behind Cine Planet Multiplex,
          Kompally, Hyderabad – 500100, Telangana
        </span>
      </div>

      <div class="footer-contact-item">
        <i class="fas fa-phone"></i>
        <span>040-23005000</span>
      </div>

      <div class="footer-contact-item">
        <i class="fas fa-envelope"></i>
        <span>info@svishyd.edu.in</span>
      </div>

      <div class="footer-contact-item">
        <i class="fas fa-clock"></i>
        <span>
          Mon–Fri: 8:15 AM – 3:15 PM<br>
          Saturday: 8:15 AM – 12:30 PM
        </span>
      </div>
    </div>


    <!-- Location -->
    <div class="footer-col">
      <h3>Location</h3>

      <div class="footer-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15217.29501790504!2d78.478686!3d17.539766!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb855ec1fabca7%3A0x216c99b72461c6a0!2sSadhu%20Vaswani%20International%20School!5e0!3m2!1sen!2sin!4v1778574953962!5m2!1sen!2sin"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>

      <p style="margin-top:1rem;">
        Follow Us
      </p>

      <div class="footer-socials">
        <a href="https://www.facebook.com/svishydintsch" target="_blank">
          <i class="fab fa-facebook-f"></i>
        </a>

        <a href="https://www.instagram.com/svishyderabad" target="_blank">
          <i class="fab fa-instagram"></i>
        </a>

        <a href="https://www.youtube.com/c/SadhuVaswaniInternationalSchoolHyderabad" target="_blank">
          <i class="fab fa-youtube"></i>
        </a>
      </div>

    </div>

  </div>

  <!-- Bottom Bar -->
  <div class="footer-bottom">
    <p>
      ©2026 Sadhu Vaswani International School, Hyderabad.
      All Rights Reserved. | Concept & Design by eparivartan
    </p>
  </div>

</footer>

<!-- ===== LOGIN MODAL ===== -->
<div id="login-modal" class="modal-overlay modal-hidden">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-logos">
        <img src="Logo/Logo.png"  alt="SVIS Alumni" style="height:48px;" onerror="this.style.display='none'"/>
      </div>
      <h2>Welcome Back</h2>
      <p>Sign in to your SVIS Alumni account</p>
    </div>
    <button class="modal-close" onclick="hideModal('login')" aria-label="Close">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="modal-body">
      <form method="POST" action="login_code.php">
        <div class="form-group">
          <label>Email Address<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <input type="email" name="email" placeholder="Enter your email" required/>
          </div>
        </div>
        <div class="form-group">
          <label>Password<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
            <input type="password" id="login-password" name="password" placeholder="Enter your password" required/>
            <button type="button" class="pw-toggle" onclick="togglePassword('login-password',this)">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="form-submit">Login</button>
        <div class="form-divider"><span>New to SVIS Alumni Network?</span></div>
        <div class="form-switch">Don't have an account? <button type="button" onclick="showModal('register')">Register here</button></div>
      </form>
    </div>
  </div>
</div>

<!-- ===== REGISTER MODAL ===== -->
<?php include "register_modal_part.php"; ?>
        <div class="form-group">
          <label>Email Address<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <input type="email" name="email" placeholder="Enter your email" required/>
          </div>
        </div>
        <div class="form-group">
          <label>Passed-out Year<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            <select name="batch" required>
              <option value="">Select Year</option>
              <script>for(let y=2008;y<=new Date().getFullYear();y++){document.write('<option value="'+y+'">'+y+'</option>');}</script>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Gender<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            <select name="gender" required>
              <option value="">Select Gender</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Password<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
            <input type="password" id="register-password" name="password" placeholder="Create a password" required/>
            <button type="button" class="pw-toggle" onclick="togglePassword('register-password',this)">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
        </div>
        <div class="form-group">
          <div class="checkbox-wrap">
            <input type="checkbox" name="terms" required/>
            <span>I agree to the <button type="button">Terms &amp; Conditions</button></span>
          </div>
        </div>
        <button type="submit" class="form-submit">Register for Alumni</button>
        <div class="form-switch">Already have an account? <button type="button" onclick="showModal('login')">Login here</button></div>
      </form>
    </div>
  </div>
</div>

<script>
  /* Carousel */
  let slideIdx = 0;
  const carousel = document.getElementById('heroCarousel');
  function showSlide(n) {
    const total = carousel.children.length;
    slideIdx = (n + total) % total;
    carousel.style.transform = `translateX(-${slideIdx * 100}%)`;
  }
  function nextSlide() { showSlide(slideIdx + 1); }
  function prevSlide() { showSlide(slideIdx - 1); }
  setInterval(nextSlide, 5000);

  /* Mobile Nav */
  const menuBtn = document.getElementById('hamburger-btn');
  const mobileNav = document.getElementById('mobile-menu');
  const hamIcon = document.getElementById('hamburger-icon');
  menuBtn.addEventListener('click', () => {
    const o = mobileNav.classList.toggle('open');
    hamIcon.style.transform = o ? 'rotate(90deg)' : 'rotate(0)';
  });
  document.addEventListener('click', e => {
    if (!menuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
      mobileNav.classList.remove('open');
      hamIcon.style.transform = 'rotate(0)';
    }
  });

  /* Modals */
  const isLoggedIn = <?= (isset($_SESSION['alumni_id']) && !empty($_SESSION['alumni_id'])) ? 'true' : 'false' ?>;
  function openRegModal(name) {
    if (!isLoggedIn) {
      showToast("Please login to register for this event.", "error");
      setTimeout(() => {
        showModal('login');
      }, 500);
      return;
    }
    document.getElementById('regmod').classList.add('open');
    document.getElementById('reg-event-title').textContent = "Registering for: " + name;
    document.getElementById('reg-event-name-input').value = name;
    document.body.style.overflow = 'hidden';
  }
  function showModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.remove('modal-hidden'); m.classList.add('modal-visible');
    document.body.style.overflow = 'hidden';
    if (type === 'register') hideModal('login');
    if (type === 'login')    hideModal('register');
  }
  function hideModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.remove('modal-visible'); m.classList.add('modal-hidden');
    document.body.style.overflow = '';
  }
  document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('modal-visible');
      this.classList.add('modal-hidden');
      document.body.style.overflow = '';
    }
  }));

  /* Password Toggle */
  function togglePassword(id) {
    const i = document.getElementById(id);
    i.type = i.type === 'password' ? 'text' : 'password';
  }

  /* --- CUSTOM DROPDOWN SYSTEM --- */
  function initCustomSelects() {
    document.querySelectorAll('select').forEach(select => {
      if (select.closest('.cs-wrapper')) return;
      const wrapper = document.createElement('div');
      wrapper.className = 'cs-wrapper';
      if (select.closest('.input-wrap')) wrapper.classList.add('with-icon');
      
      const selected = document.createElement('div');
      selected.className = 'cs-selected';
      selected.textContent = select.options[select.selectedIndex]?.text || 'Select...';
      
      const menu = document.createElement('div');
      menu.className = 'cs-menu';
      
      Array.from(select.options).forEach((opt, idx) => {
        const option = document.createElement('div');
        option.className = 'cs-option';
        if (idx === select.selectedIndex) option.classList.add('active');
        option.textContent = opt.text;
        option.onclick = (e) => {
          e.stopPropagation();
          select.selectedIndex = idx;
          select.dispatchEvent(new Event('change'));
          selected.textContent = opt.text;
          menu.classList.remove('show');
          wrapper.classList.remove('open');
          menu.querySelectorAll('.cs-option').forEach(o => o.classList.remove('active'));
          option.classList.add('active');
        };
        menu.appendChild(option);
      });
      
      wrapper.appendChild(selected);
      wrapper.appendChild(menu);
      select.style.display = 'none';
      select.parentNode.insertBefore(wrapper, select.nextSibling);
      
      selected.onclick = (e) => {
        e.stopPropagation();
        const isOpen = menu.classList.contains('show');
        document.querySelectorAll('.cs-menu').forEach(m => m.classList.remove('show'));
        document.querySelectorAll('.cs-wrapper').forEach(w => w.classList.remove('open'));
        if (!isOpen) {
          menu.classList.add('show');
          wrapper.classList.add('open');
        }
      };
    });
  }

  document.addEventListener('click', () => {
    document.querySelectorAll('.cs-menu').forEach(m => m.classList.remove('show'));
    document.querySelectorAll('.cs-wrapper').forEach(w => w.classList.remove('open'));
  });

  /* --- TOAST SYSTEM --- */

  function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Add icon based on type
    const icon = type === 'success' 
      ? '<i class="fas fa-check-circle"></i>' 
      : '<i class="fas fa-exclamation-circle"></i>';
    
    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);
    
    // Remove from DOM after animation ends
    setTimeout(() => {
      toast.remove();
    }, 4200);
  }

  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();

    // Register Form AJAX
    const regForm = document.getElementById('register-form');
    if(regForm) {
      regForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        const formData = new FormData(this);
        fetch('insert_reg.php', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            showToast(data.message, 'success');
            regForm.reset();
            setTimeout(() => hideModal('register'), 2000);
          } else {
            showToast(data.message, 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('An error occurred. Please try again.', 'error');
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        });
      });
    }
  });
  
  function askLogin() {
    showToast("Please login to register for this event.", "error");
    setTimeout(() => {
      showModal('login');
    }, 500);
  }
</script>

<!-- Toast Container -->
<div id="toast-container"></div>

</body>
</html>
