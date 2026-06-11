<?php 
include('db_connect.php'); 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$sql = "SELECT id, shortname, name, phonecode FROM countries";
$countries_res = $conn->query($sql);
$countries = [];
if ($countries_res) {
    while ($row = $countries_res->fetch_assoc()) {
        $countries[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SVIS Alumni Portal | Connect, Engage & Relive Memories</title>
  <meta name="description" content="Welcome to the official SVIS Alumni Portal. Reconnect with classmates, stay updated on school events, and join a thriving professional network of Sadhu Vaswani International School graduates.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;500;700;900&display=swap" rel="stylesheet">
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
    .heroCarousel {
      display: flex;
      width: 100%;
      height: 100%;
      transition: transform 0.7s ease-in-out;
    }
    .heroCarousel > * {
      width: 100%; height: 100%;
      flex-shrink: 0;
    }
    .heroCarousel img {
      width: 100%; height: 100%;
      object-fit: cover;
      display: block;
    }
    .carousel-container {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
    }
    .mobile-carousel { display: none; }
    @media (max-width: 768px) {
      .desktop-carousel { display: none; }
      .mobile-carousel { display: block; }
    }
    .hero-dots {
      position: absolute;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 0.6rem;
      z-index: 25;
    }
    .hero-dot {
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.2);
      cursor: pointer;
      padding: 0;
      transition: all 0.3s ease;
    }
    .hero-dot:hover {
      background: rgba(255, 255, 255, 0.8);
    }
    .hero-dot.active {
      background: #fbbf24;
      border-color: #fbbf24;
      transform: scale(1.3);
      box-shadow: 0 0 10px rgba(251, 191, 36, 0.4);
    }
    .hero-overlay {
      position: absolute;
      top: 55%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 90%;
      text-align: left;
      z-index: 15;
      padding: 0 1.5rem;
      pointer-events: none;
    }
    .hero-overlay > * {
      max-width: 700px;
      pointer-events: auto;
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
    .hero-top-heading {
      color: #fbbf24;
      font-weight: 600;
      font-size: clamp(0.9rem, 1.3vw, 1.1rem);
      margin-bottom: 0.8rem;
      font-family: 'Poppins', sans-serif;
      animation-delay: 0.1s;
    }
    .hero-title {
      color: #fff;
      font-family: 'lato', serif;
      font-size: clamp(1.4rem, 3.2vw, 2.4rem);
      font-weight: 700;
      line-height: 1.1;
      margin-bottom: 1.2rem;
      text-shadow: 0 2px 16px rgba(0,0,0,0.5);
      animation-delay: 0.2s;
    }
    .hero-divider {
      width: 60px;
      height: 4px;
      background: #fbbf24;
      margin-bottom: 1.5rem;
      border-radius: 2px;
      animation-delay: 0.3s;
    }
    .hero-brief {
      color: rgba(255,255,255,0.95);
      font-size: clamp(0.95rem, 1.3vw, 1.15rem);
      line-height: 1.6;
      margin-bottom: 2.5rem;
      font-family: 'Poppins', sans-serif;
      text-align: justify;
      animation-delay: 0.4s;
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
      animation-delay: 0.5s;
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
    .about-subheading { 
      display: block;
      font-size: 1.25rem; 
      color: #1D4ED8; 
      margin-bottom: 0.75rem;
      font-family: 'Lato', sans-serif;
    }
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
    .gallery-section .gallery-grid,
    .videos-section .videos-grid {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 1.5rem !important;
      width: 100% !important;
      max-width: none !important;
    }

    .alumni-card, .dir-card, .event-card, .gallery-item {
      width: 100% !important;
      height: 100% !important;
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
      .highlights-grid,
      .videos-section .videos-grid {
        grid-template-columns: repeat(2, 1fr) !important;
      }
    }

    @media (max-width: 1200px) {
      .hero-overlay > * { max-width: 600px; }
    }

    @media (max-width: 1024px) {
      .hero-overlay > * { max-width: 550px; }
    }

    @media (max-width: 768px) {
      .hero-dots {
        bottom: 1.5rem;
      }
      .hero-section {
        height: 100vh;
        min-height: 480px;
      }
      .hero-overlay {
        top: auto;
        bottom: 8%;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        width: 90%;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      .hero-overlay > * {
        max-width: 100%;
      }
      .hero-title {
        font-size: clamp(1.1rem, 5.5vw, 1.5rem);
        line-height: 1.2;
        margin-bottom: 0.6rem;
      }
      .hero-brief {
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
        line-height: 1.5;
      }
      .hero-top-heading {
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
      }
      .hero-divider {
        margin-bottom: 1rem;
        width: 40px;
        height: 3px;
      }
      .hero-btn {
        padding: 0.6rem 1.5rem;
        font-size: 0.8rem;
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
      .highlights-grid,
      .videos-section .videos-grid {
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

    /* Video Section Specific Styles */
    .videos-section {
      position: relative;
    }
    .video-card { background:#fff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.04); transition:all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); cursor:pointer; border:1px solid #f1f5f9; display:flex; flex-direction:column; height:100%; text-align: left; }
    .video-card:hover { box-shadow:0 25px 50px rgba(30,58,138,0.12); transform:translateY(-10px); border-color:rgba(30,58,138,0.15); }
    .video-thumb { position:relative; height:180px; overflow:hidden; background:#0f172a; }
    .video-thumb img { width:100%; height:100%; object-fit:cover; transition:transform 0.6s ease; filter:brightness(0.94); }
    .video-card:hover .video-thumb img { transform:scale(1.1); filter:brightness(1); }
    .play-btn-wrap { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(15,23,42,0); transition:background 0.3s; }
    .video-card:hover .play-btn-wrap { background:rgba(15,23,42,0.2); }
    .play-btn { width:50px; height:50px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 8px 25px rgba(0,0,0,0.2); transition:all 0.3s; opacity:0.9; }
    .video-card:hover .play-btn { transform:scale(1.2); background:#1D4ED8; opacity:1; }
    .play-btn i { color:#1D4ED8; font-size:1.1rem; margin-left:3px; transition:color 0.3s; }
    .video-card:hover .play-btn i { color:#fff }
    .video-badge { position:absolute; top:12px; left:12px; background:rgba(255,255,255,0.92); backdrop-filter:blur(4px); color:#1D4ED8; font-size:0.65rem; padding:0.3rem 0.7rem; border-radius:30px; font-weight:700; letter-spacing:0.03em; text-transform:uppercase; box-shadow:0 4px 12px rgba(0,0,0,0.1); z-index:10; }
    .video-info { padding:1.25rem; flex:1; display:flex; flex-direction:column; }
    .video-title { font-size:1.05rem; font-weight:700; color:#1e293b; margin-bottom:0.5rem; line-height:1.4; transition:color 0.3s; display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2; overflow:hidden; }
    .video-card:hover .video-title { color:#1D4ED8; }
    .video-desc { font-size:0.83rem; color:#64748b; line-height:1.6; margin-bottom:1rem; display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2; overflow:hidden; flex:1; }
    .video-date { font-size:0.75rem; color:#94a3b8; display:flex; align-items:center; gap:6px; padding-top:1rem; border-top:1px solid #f1f5f9; margin-top:auto; }
    .video-date i { font-size: 0.8rem; color: #1D4ED8; opacity: 0.6; }

    /* Video Lightbox Styles */
    .vm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92); z-index:9999; align-items:center; justify-content:center; padding:1.5rem; }
    .vm-overlay.open { display:flex; animation: fadeIn 0.3s ease; }
    .vm-inner { width:100%; max-width:960px; }
    .vm-topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
    .vm-topbar h3 { color:#fff; font-size:1.15rem; font-weight:600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 1rem; }
    .vm-ctrls { display:flex; gap:0.75rem; align-items:center; flex-shrink: 0; }
    .vm-ctrl { background:rgba(255,255,255,0.1); border:none; color:#fff; cursor:pointer; transition:all 0.2s; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .vm-ctrl:hover { background:rgba(255,255,255,0.25); color:#fbbf24; }
    .vm-ctrl svg { width:22px; height:22px; }
    .vm-frame-wrap { background:#000; border-radius:12px; overflow:hidden; aspect-ratio:16/9; box-shadow:0 20px 60px rgba(0,0,0,.6); }
    .vm-frame-wrap iframe { width:100%; height:100%; border:0; }
    .vm-hint { text-align:center; color:#9ca3af; font-size:.8rem; margin-top:1rem; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  </style>
  <script src="shared.js"></script>
</head>
<body>

<!-- ===== NAV ===== -->
<nav class="site-nav">
  <div class="nav-inner">

    <!-- LEFT: Logo -->
    <a href="index.php" class="nav-logo">
      <img src="Logo/Logo.svg" alt="SVIS Logo"/>
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
  <?php
      $scrollImagesRes = $conn->query("SELECT * FROM home_scroll_images ORDER BY order_no ASC, id DESC");
      $largeSlides = [];
      $smallSlides = [];

      if ($scrollImagesRes && $scrollImagesRes->num_rows > 0) {
          while ($row = $scrollImagesRes->fetch_assoc()) {
              if (!empty($row['banner_type']) && strcasecmp($row['banner_type'], 'All') === 0) {
                  $smallSlides[] = $row;
                  $largeSlides[] = $row;
              } else {
                  $type = 'large';
                  if (!empty($row['banner_type'])) {
                      if (stripos($row['banner_type'], 'Small') !== false) {
                          $type = 'small';
                      }
                  } else {
                      if (stripos($row['title'], 'small_banner') !== false || stripos($row['image_name'], 'mobile') !== false) {
                          $type = 'small';
                      }
                  }

                  if ($type === 'small') {
                      $smallSlides[] = $row;
                  } else {
                      $largeSlides[] = $row;
                  }
              }
          }
      }

      $hasSmall = count($smallSlides) > 0;
      $mobileSlides = $hasSmall ? $smallSlides : $largeSlides;
  ?>

  <!-- DESKTOP CAROUSEL -->
  <div class="carousel-container desktop-carousel">
    <div class="heroCarousel" id="heroCarouselDesktop">
      <?php if (count($largeSlides) > 0): ?>
        <?php foreach ($largeSlides as $slide): ?>
          <img src="uploads/home_scroll/<?= rawurlencode($slide['image_name']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>" onerror="this.src='Banners/image.png'"/>
        <?php endforeach; ?>
      <?php else: ?>
          <img src="Banners/image.png" alt="Alumni banner"/>
      <?php endif; ?>
    </div>
    <?php if (count($largeSlides) > 1): ?>
    <div class="hero-dots" id="heroDotsDesktop">
      <?php for ($i = 0; $i < count($largeSlides); $i++): ?>
        <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlideDesktop(<?= $i ?>)" aria-label="Go to slide <?= $i + 1 ?>"></button>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- MOBILE CAROUSEL -->
  <div class="carousel-container mobile-carousel">
    <div class="heroCarousel" id="heroCarouselMobile">
      <?php if (count($mobileSlides) > 0): ?>
        <?php foreach ($mobileSlides as $slide): ?>
          <img src="uploads/home_scroll/<?= rawurlencode($slide['image_name']) ?>" alt="<?= htmlspecialchars($slide['title']) ?>" onerror="this.src='Banners/image.png'"/>
        <?php endforeach; ?>
      <?php else: ?>
          <img src="Banners/image.png" alt="Alumni banner"/>
      <?php endif; ?>
    </div>
    <?php if (count($mobileSlides) > 1): ?>
    <div class="hero-dots" id="heroDotsMobile">
      <?php for ($i = 0; $i < count($mobileSlides); $i++): ?>
        <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" onclick="goToSlideMobile(<?= $i ?>)" aria-label="Go to slide <?= $i + 1 ?>"></button>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
  <div class="hero-overlay" style="z-index:20;">
    <p class="hero-top-heading">Welcome to SVIS Alumni Network</p>
    <h1 class="hero-title">Connecting Generations of SVIS Alumni Worldwide</h1>
    <div class="hero-divider"></div>
    <p class="hero-brief">The SVIS Alumni Network is a dedicated community that brings together former students to reconnect, share experiences, celebrate achievements, and stay engaged with the SVIS family through events, networking, and lifelong friendships.</p>
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
        <p class="about-subheading"><strong>SVIS Alumni – Stay Connected Forever</strong></p>
        <p>Sadhu Vaswani International School (SVIS), Kompally, Hyderabad, established in 2008, is one of the many educational institutions which the Sadhu Vaswani Mission started all over India. It is a progressive school based on Indian thought, culture, tradition and the educational ideals of Sadhu Vaswani.</p>
        <p>The aim of our school is to impart integrated and comprehensive education which is formative and not merely informative. It lays emphasis on the full development of the student's character and personality which should normally lead them to self-fulfillment and dedication to the service of society.</p>
        <p>Students are placed in an atmosphere that enables them to develop reverence for all prophets, seers and sages, all heroes of humanity, all races and religions. Our school is a non-communal, non-sectarian institution which places emphasis on the permanent values of life.</p>
        <p>This Alumni Network is dedicated to all former students of SVIS — a platform to reconnect, share your journey, and relive cherished school memories. Join us and keep the SVIS spirit alive wherever life takes you!
          <a href="about.php" class="read-more-link">... Read More</a>
        </p>
      </div>
      <div class="about-img">
        <img src="images/School-image.JPG " alt="SVIS School"
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
    <h2 class="section-title">Spotlight</h2>
    <div class="alumni-grid">
      <?php
        $result = $conn->query("SELECT * FROM alumni_register WHERE verified_status=1 ORDER BY CASE WHEN showcase_order > 0 THEN 0 ELSE 1 END, showcase_order ASC, id DESC LIMIT 4");
        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
            $name = trim($row['full_name'] ?? '');
            $initial = strtoupper(substr($name ?: 'A', 0, 1));
      ?>
      <div class="dir-card">
        <!-- Header with wave + decorations -->
        <div class="dir-card-hdr">
          <div class="dot-pattern">
            <?php for($d=0;$d<24;$d++) echo '<span></span>'; ?>
          </div>
          <div class="circle-deco"></div>
          <svg class="wave-divider" viewBox="0 0 500 40" preserveAspectRatio="none">
            <path d="M0,20 C125,45 175,0 250,18 C325,36 375,-5 500,20 L500,40 L0,40 Z" fill="#fff"/>
          </svg>
        </div>

        <!-- Avatar overlapping header -->
        <div class="dir-card-av">
          <?php if (!empty($row['user_image'])): ?>
            <img src="uploads/<?= rawurlencode($row['user_image']) ?>"
                 alt="<?= htmlspecialchars($name) ?>"
                 loading="lazy" />
          <?php else: ?>
            <span class="dir-card-init"><?= htmlspecialchars($initial) ?></span>
          <?php endif; ?>
        </div>

        <!-- Card Body -->
        <div class="dir-card-body">
          <h3 class="dir-card-name"><?= htmlspecialchars(ucfirst($name)) ?></h3>
          <div class="dir-card-meta dir-card-batch">
            <i class="fas fa-graduation-cap"></i>
            Batch <?= htmlspecialchars($row['batch_year'] ?? '') ?>
          </div>
          <?php if (!empty($row['Profession'])): ?>
            <div class="dir-card-meta dir-card-prof">
              <i class="fas fa-briefcase"></i>
              <?= htmlspecialchars(ucfirst($row['Profession'])) ?>
            </div>
          <?php endif; ?>
          <div class="dir-card-divider"></div>
        </div>

        <!-- Footer Trust Badges -->
        <div class="dir-card-footer">
          <div class="trust-badge">
            <i class="fas fa-check-circle"></i>
            <span>Verified<br>Alumni</span>
          </div>
          <div class="trust-badge">
            <i class="fas fa-users"></i>
            <span>Connected<br>Community</span>
          </div>
          <div class="trust-badge">
            <i class="fas fa-star"></i>
            <span>Trusted<br>Network</span>
          </div>
        </div>
      </div>
      <?php
          endwhile;
        else:
      ?>
      <div class="empty-state-card">
        <i class="fas fa-users-slash"></i>
        <h3>No Alumni Found</h3>
        <p>There are no verified alumni profiles to showcase at the moment. Check back later!</p>
      </div>
      <?php endif; ?>
    </div>

    <div class="view-all-wrap"><a href="directory.php" class="view-all-btn">View All</a></div>
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
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($event['event_name']) ?>&background=random&color=fff&size=600&font-size=0.25" alt="<?= htmlspecialchars($event['event_name']) ?>">
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
      <div class="empty-state-card">
        <i class="far fa-calendar-times"></i>
        <h3>No Upcoming Events</h3>
        <p>There are no events scheduled at the moment. Stay tuned for exciting updates!</p>
      </div>
      <?php endif; ?>
    </div>

    <div class="view-all-wrap">
      <a href="event.php" class="view-all-btn">
        View All
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
      <div class="empty-state-card">
        <i class="fas fa-images"></i>
        <h3>No Images Yet</h3>
        <p>Our gallery is currently empty. Memories will be shared here soon!</p>
      </div>
      <?php endif; ?>
    </div>
    <div class="view-all-wrap"><a href="gallery.php" class="view-all-btn">View All</a></div>
  </div>
</section>

<!-- ===== VIDEOS ===== -->
<section class="videos-section" style="padding: 4rem 0; background: #f0f4ff;">
  <div class="section-inner">
    <h2 class="section-title">Latest Videos & Memories</h2>
    <div class="videos-grid">
      <?php
        $videos_res = $conn->query("SELECT * FROM videos ORDER BY id DESC LIMIT 4");
        if ($videos_res && $videos_res->num_rows > 0):
          while ($row = $videos_res->fetch_assoc()):
            $video_url  = $row['video_url'];
            $youtube_id = '';
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $match)) {
                $youtube_id = $match[1];
            }
            $thumb    = "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg";
            $title    = htmlspecialchars($row['title']);
            $date     = date("M d, Y", strtotime($row['created_at']));
            $safe_url = htmlspecialchars($video_url);
      ?>
      <div class="video-card" onclick="openVm('<?= $safe_url ?>', '<?= $title ?>')">
        <div class="video-thumb">
          <img src="<?= $thumb ?>" alt="<?= $title ?>" onerror="this.style.display='none'"/>
          <div class="play-btn-wrap">
            <div class="play-btn"><i class="fas fa-play"></i></div>
          </div>
          <span class="video-badge"><i class="fas fa-video"></i> Video</span>
        </div>
        <div class="video-info">
          <h3 class="video-title"><?= $title ?></h3>
          <?php if(!empty($row['video_description'])): ?>
            <p class="video-desc"><?= htmlspecialchars(mb_strimwidth($row['video_description'], 0, 100, "...")) ?></p>
          <?php endif; ?>
          <p class="video-date"><i class="far fa-calendar-alt"></i> <?= $date ?></p>
        </div>
      </div>
      <?php
          endwhile;
        else:
      ?>
      <div class="empty-state-card">
        <i class="fas fa-video-slash"></i>
        <h3>No Videos Yet</h3>
        <p>No video highlights have been shared yet. Check back soon!</p>
      </div>
      <?php endif; ?>
    </div>
    <div class="view-all-wrap"><a href="videos.php" class="view-all-btn">View All</a></div>
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
        <img src="Logo/Logo.svg"  alt="SVIS Alumni" style="height:48px;" onerror="this.style.display='none'"/>
      </div>
      <h2>Welcome</h2>
      <p>Sign in to your SVIS Alumni account</p>
    </div>
    <button class="modal-close" onclick="hideModal('login')" aria-label="Close">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="modal-body">
      <form id="login-form" method="POST" action="login_code.php">
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

<?php include "register_modal_part.php"; ?>

<!-- ===== VIDEO LIGHTBOX ===== -->
<div class="vm-overlay" id="vm-overlay" onclick="if(event.target===this)closeVm()">
  <div class="vm-inner" onclick="event.stopPropagation()">
    <div class="vm-topbar">
      <h3 id="vm-title"></h3>
      <div class="vm-ctrls">
        <button class="vm-ctrl" onclick="toggleFs()" title="Fullscreen">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
        </button>
        <button class="vm-ctrl" onclick="closeVm()" title="Close">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </div>
    <div class="vm-frame-wrap" id="vm-container">
      <iframe id="vm-iframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
    </div>
    <p class="vm-hint"><i class="fas fa-expand"></i> F = fullscreen &nbsp;•&nbsp; <i class="fas fa-times"></i> ESC = close</p>
  </div>
</div>

<script>
  /* Carousel */
  function setupCarousel(carouselId, dotsId) {
    let slideIdx = 0;
    const carousel = document.getElementById(carouselId);
    if (!carousel) return function(){};
    
    function showSlide(n) {
      const total = carousel.children.length;
      if (total === 0) return;
      
      slideIdx = (n + total) % total;
      carousel.style.transform = `translateX(-${slideIdx * 100}%)`;
      
      const dotsContainer = document.getElementById(dotsId);
      if (dotsContainer) {
        const dots = dotsContainer.querySelectorAll('.hero-dot');
        dots.forEach((dot, index) => {
          dot.classList.toggle('active', index === slideIdx);
        });
      }
    }
    
    setInterval(() => showSlide(slideIdx + 1), 5000);
    return function goToSlide(n) { showSlide(n); };
  }

  const goToSlideDesktop = setupCarousel('heroCarouselDesktop', 'heroDotsDesktop');
  const goToSlideMobile = setupCarousel('heroCarouselMobile', 'heroDotsMobile');
</script>

<!-- ===== JAVASCRIPT ===== -->
<script>
  /* Video lightbox */
  function openVm(videoUrl, title) {
    let embedUrl = videoUrl;
    if (videoUrl.includes('youtube.com/watch')) {
      const videoId = videoUrl.split('v=')[1]?.split('&')[0];
      embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    } else if (videoUrl.includes('youtu.be/')) {
      const videoId = videoUrl.split('youtu.be/')[1]?.split('?')[0];
      embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
    }
    document.getElementById('vm-iframe').src = embedUrl;
    document.getElementById('vm-title').textContent = title;
    document.getElementById('vm-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeVm() {
    document.getElementById('vm-iframe').src = '';
    if (document.fullscreenElement) document.exitFullscreen();
    document.getElementById('vm-overlay').classList.remove('open');
    document.body.style.overflow = '';
  }
  function toggleFs() {
    const vc = document.getElementById('vm-container');
    if (!document.fullscreenElement) (vc.requestFullscreen || vc.webkitRequestFullscreen).call(vc);
    else (document.exitFullscreen || document.webkitExitFullscreen).call(document);
  }
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeVm();
    if ((e.key === 'f' || e.key === 'F') && document.getElementById('vm-overlay').classList.contains('open')) {
      e.preventDefault(); toggleFs();
    }
  });

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

  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    handleLoginAJAX('login-form');
    handleRegisterAJAX('register-form');
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
