<?php 
include('db_connect.php'); 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SVIS Alumni Video Gallery | Highlights & Event Videos</title>
  <meta name="description" content="Watch highlights from SVIS alumni meets, school events, and student testimonials. Experience the vibrant life at Sadhu Vaswani International School.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    .videos-subtitle{text-align:center;color:#6b7280;max-width:540px;margin:0 auto 3rem;font-size:.95rem}
    .videos-grid { margin-top:1.5rem; display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:2rem; }
    .video-card { background:#fff; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.04); transition:all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); cursor:pointer; border:1px solid #f1f5f9; display:flex; flex-direction:column; height:100%; }
    .video-card:hover { box-shadow:0 25px 50px rgba(30,58,138,0.12); transform:translateY(-10px); border-color:rgba(30,58,138,0.15); }
    
    .video-thumb { position:relative; height:220px; overflow:hidden; background:#0f172a; }
    .video-thumb img { width:100%; height:100%; object-fit:cover; transition:transform 0.6s ease; filter:brightness(0.94); }
    .video-card:hover .video-thumb img { transform:scale(1.1); filter:brightness(1); }
    
    .play-btn-wrap { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(15,23,42,0); transition:background 0.3s; }
    .video-card:hover .play-btn-wrap { background:rgba(15,23,42,0.2); }
    
    .play-btn { width:60px; height:60px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 8px 25px rgba(0,0,0,0.2); transition:all 0.3s; opacity:0.9; }
    .video-card:hover .play-btn { transform:scale(1.2); background:var(--blue); opacity:1; }
    .play-btn i { color:var(--blue); font-size:1.25rem; margin-left:4px; transition:color 0.3s; }
    .video-card:hover .play-btn i { color:#fff }
    
    .video-badge { position:absolute; top:15px; left:15px; background:rgba(255,255,255,0.92); backdrop-filter:blur(4px); color:var(--blue); font-size:0.7rem; padding:0.35rem 0.8rem; border-radius:30px; font-weight:700; letter-spacing:0.03em; text-transform:uppercase; box-shadow:0 4px 12px rgba(0,0,0,0.1); z-index:10; }
    
    .video-info { padding:1.75rem; flex:1; display:flex; flex-direction:column; }
    .video-title { font-size:1.15rem; font-weight:700; color:#1e293b; margin-bottom:0.8rem; line-height:1.45; transition:color 0.3s; display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2; overflow:hidden; }
    .video-card:hover .video-title { color:var(--blue); }
    
    .video-desc { font-size:0.9rem; color:#64748b; line-height:1.65; margin-bottom:1.5rem; display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2; overflow:hidden; flex:1; }
    
    .video-date { font-size:0.8rem; color:#94a3b8; display:flex; align-items:center; gap:8px; padding-top:1.25rem; border-top:1px solid #f1f5f9; margin-top:auto; }
    .video-date i { font-size: 0.9rem; color: var(--blue); opacity: 0.6; }

    /* Pagination */
    .pagination-wrap { display: flex; justify-content: center; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin: 3rem 0; padding: 0 1rem; }
    .page-btn { min-width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #fff; border: 2px solid #1D4ED8; border-radius: 999px; color: #1D4ED8; font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: 'Poppins', sans-serif; font-size: 0.9rem; padding: 0 0.5rem; white-space: nowrap; }
    .page-btn:hover:not(:disabled) { background: #1741b0; color: #fff; border-color: var(--gold); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(29,78,216,0.2); }
    .page-btn.active { background: #1D4ED8; color: #fff; border-color: var(--gold); box-shadow: 0 3px 12px rgba(29,78,216,0.3); }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; border-color: #d1d5db; color: #9ca3af; }
    .page-btn.prev-next { padding: 0 1.5rem; }

    /* Lightbox */
    .vm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92); z-index:70; align-items:center; justify-content:center; padding:1.5rem; }
    .vm-overlay.open { display:flex; animation: fadeIn 0.3s ease; }
    .vm-inner { width:100%; max-width:960px; }
    .vm-topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
    .vm-topbar h3 { color:#fff; font-size:1.15rem; font-weight:600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 1rem; }
    .vm-ctrls { display:flex; gap:0.75rem; align-items:center; flex-shrink: 0; }
    .vm-ctrl { background:rgba(255,255,255,0.1); border:none; color:#fff; cursor:pointer; transition:all 0.2s; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .vm-ctrl:hover { background:rgba(255,255,255,0.25); color:var(--gold); }
    .vm-ctrl svg { width:22px; height:22px; }
    .vm-frame-wrap { background:#000; border-radius:12px; overflow:hidden; aspect-ratio:16/9; box-shadow:0 20px 60px rgba(0,0,0,.6); }
    .vm-frame-wrap iframe { width:100%; height:100%; border:0; }
    .vm-hint { text-align:center; color:#9ca3af; font-size:.8rem; margin-top:1rem; }

    /* Search Bar */
    .video-search-wrap { margin-bottom: 3rem; display: flex; justify-content: center; padding: 0 1rem; }
    .video-search-inner { position: relative; width: 100%; max-width: 540px; }
    .video-search-inner input { width: 100%; padding: 0.9rem 1.25rem 0.9rem 3.5rem; border-radius: 999px; border: 2px solid #e5e7eb; outline: none; font-family: 'Poppins', sans-serif; font-size: 1rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    .video-search-inner input:focus { border-color: var(--blue); box-shadow: 0 8px 24px rgba(30,58,138,0.1); }
    .video-search-inner i { position: absolute; left: 1.4rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 1.2rem; transition: color 0.2s; }
    .video-search-inner input:focus + i { color: var(--blue); }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    @media (max-width: 1300px) {
      .policy-hero { padding: 9rem 1.5rem 3rem; }
      .videos-grid { gap: 1.5rem; }
    }

    @media (max-width: 768px) {
      .video-thumb { height: 200px; }
      .video-info { padding: 1.25rem; }
      .video-title { font-size: 1.05rem; }
      .vm-inner { max-width: 100%; }
      .vm-topbar h3 { font-size: 1rem; }
      .vm-ctrl { width: 36px; height: 36px; }
    }

    @media (max-width: 600px) {
      .videos-grid { grid-template-columns: 1fr; gap: 1.25rem; }
      .video-thumb { height: 180px; }
      .page-btn.prev-next { padding: 0 1rem; font-size: 0.8rem; }
      .video-search-inner input { font-size: 0.9rem; padding-left: 3rem; }
      .video-search-inner i { left: 1.1rem; font-size: 1rem; }
      .vm-overlay { padding: 1rem; }
      .vm-topbar h3 { font-size: 0.9rem; }
      .vm-hint { display: none; }
    }
    /* Navbar styles moved to shared.css */
  </style>
  <!-- Shared JS -->
  <script src="shared.js"></script>
</head>
<body>

<!-- ===== NAV ===== -->
<nav class="site-nav">
  <div class="nav-inner">
    <a href="index.php" class="nav-logo">
      <img src="Logo/Logo.svg" alt="SVIS Logo"/>
    </a>
    <div class="nav-links">
      <a href="index.php"    class="nav-link">Home</a>
      <a href="directory.php" class="nav-link">Directory</a>
      <a href="event.php"   class="nav-link">Events</a>
      <a href="about.php"    class="nav-link">About</a>
      <a href="founders.php" class="nav-link">Founders</a>
      <a href="gallery.php"   class="nav-link">Gallery</a>
      <a href="videos.php"    class="nav-link active">Videos</a>
      <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
        <a href="profileedit.php" class="nav-link">Profile</a>
      <?php } ?>
    </div>
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
  <a href="index.php"    class="nav-link">Home</a>
  <a href="directory.php" class="nav-link">Directory</a>
  <a href="event.php"   class="nav-link">Events</a>
  <a href="about.php"    class="nav-link">About</a>
  <a href="founders.php" class="nav-link">Founders</a>
  <a href="gallery.php"   class="nav-link">Gallery</a>
  <a href="videos.php"    class="nav-link active">Videos</a>
  <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
    <a href="profileedit.php" class="nav-link">Profile</a>
    <a href="logout.php" class="nav-login-btn">Logout</a>
  <?php } else { ?>
    <button class="nav-login-btn" onclick="showModal('login')">Login</button>
  <?php } ?>
</div>

<!-- ===== MAIN ===== -->
    <!-- ===== HERO ===== -->
    <div class="policy-hero">
        <h1>Video Gallery</h1>
        <p>Watch memorable moments, events, and highlights from Sadhu Vaswani International School</p>
    </div>

    <main class="hero-page-wrap">
        <div class="policy-container">
          
          <div class="video-search-wrap">
            <div class="video-search-inner">
              <input type="text" id="videoSearch" placeholder="Search by video title..." autocomplete="off">
              <i class="fas fa-search"></i>
            </div>
          </div>

    <!-- Video Grid — PHP rendered -->
    <div id="video-list-container" class="videos-grid">

      <?php
        $result = $conn->query("SELECT * FROM videos ORDER BY id DESC");
        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
            $video_url  = $row['video_url'];
            $youtube_id = '';
            // Robust regex for various YouTube URL formats (watch, embed, shorts, youtu.be)
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $match)) {
                $youtube_id = $match[1];
            }
            $thumb    = "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg";
            $title    = htmlspecialchars($row['title']);
            $date     = date("M d, Y", strtotime($row['created_at']));
            $safe_url = htmlspecialchars($video_url);
      ?>

      <div class="video-card" onclick="openVm('<?= $safe_url ?>', '<?= $title ?>')" data-title="<?= strtolower($title) ?>" data-desc="<?= strtolower(htmlspecialchars($row['video_description'] ?? '')) ?>">
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
            <p class="video-desc"><?= htmlspecialchars(mb_strimwidth($row['video_description'], 0, 150, "...")) ?></p>
          <?php endif; ?>
          <p class="video-date"><i class="far fa-calendar-alt"></i> <?= $date ?></p>
        </div>
      </div>

      <?php endwhile; ?>
      <?php endif; ?>

    </div>

    <!-- Pagination Controls -->
    <div id="pagination-container" class="pagination-wrap"></div>

        </div>
    </main>

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

<!-- ===== LOGIN MODAL ===== -->
<div id="login-modal" class="modal-overlay modal-hidden">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-logos">
        <img src="Logo/Logo.svg" alt="SVIS" style="height:48px;" onerror="this.style.display='none'"/>
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
            <button type="button" class="pw-toggle" onclick="togglePassword('login-password')">
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

<!-- ===== FOOTER ===== -->
<footer>
  <div class="footer-grid">
    <div class="footer-col">
      <h3>Quick Links</h3>
      <ul>
        <li><a  href="index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Home</a></li>
        <li><a  href="about.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : '' ?>">About</a></li>
        <li><a  href="directory.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'directory.php') ? 'active' : '' ?>">Directory</a></li>
        <li><a  href="event.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'event.php') ? 'active' : '' ?>">Events</a></li>
        <li><a  href="gallery.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'active' : '' ?>">Gallery</a></li>
        <li><a  href="videos.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'videos.php') ? 'active' : '' ?>">Videos</a></li>
        <li><a  href="founders.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'founders.php') ? 'active' : '' ?>">Founders</a></li>
        <li><a  href="privacy-policy.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'privacy-policy.php') ? 'active' : '' ?>">Privacy Policy</a></li>
        <li><a  href="terms_use.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'terms_use.php') ? 'active' : '' ?>">Terms &amp; Conditions</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h3>Contact Info</h3>
      <div class="footer-contact-item">
        <i class="fas fa-map-marker-alt"></i>
        <span>150-152 Jayabheri Park, Behind Cine Planet Multiplex,
          Kompally, Hyderabad – 500100, Telangana</span>
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
        <span>Mon–Fri: 8:15 AM – 3:15 PM<br>Saturday: 8:15 AM – 12:30 PM</span>
      </div>
    </div>
    <div class="footer-col">
      <h3>Location</h3>
      <div class="footer-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15217.29501790504!2d78.478686!3d17.539766!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb855ec1fabca7%3A0x216c99b72461c6a0!2sSadhu%20Vaswani%20International%20School!5e0!3m2!1sen!2sin!4v1778574953962!5m2!1sen!2sin"
          allowfullscreen loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
      <p style="margin-top:1rem;">Follow Us</p>
      <div class="footer-socials">
        <a href="https://www.facebook.com/svishydintsch" target="_blank"><i class="fab fa-facebook-f"></i></a>
        <a href="https://www.instagram.com/svishyderabad" target="_blank"><i class="fab fa-instagram"></i></a>
        <a href="https://www.youtube.com/c/SadhuVaswaniInternationalSchoolHyderabad" target="_blank"><i class="fab fa-youtube"></i></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>©2026 Sadhu Vaswani International School, Hyderabad. All Rights Reserved. | Concept &amp; Design by eparivartan</p>
  </div>
</footer>

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
  const menuBtn  = document.getElementById('hamburger-btn');
  const mobileNav = document.getElementById('mobile-menu');
  const hamIcon  = document.getElementById('hamburger-icon');
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

  // Pagination State
  let currentPage = 1;
  const itemsPerPage = 6;
  let matchedVideos = [];

  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();

    /* Initial Filter/Pagination call */
    filterVideos();

    /* Search Logic */
    const searchInput = document.getElementById('videoSearch');
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        currentPage = 1;
        filterVideos();
      });
    }

    // Initialize Shared Login AJAX
    handleLoginAJAX('login-form');
  });

  function filterVideos() {
    const search = (document.getElementById("videoSearch")?.value || "").toLowerCase().trim();
    const cards = document.querySelectorAll("#video-list-container .video-card");
    
    matchedVideos = [];
    cards.forEach((card) => {
      const title = card.dataset.title || "";
      const desc = card.dataset.desc || "";
      
      if (title.includes(search) || desc.includes(search)) {
        matchedVideos.push(card);
      } else {
        card.style.display = "none";
      }
    });

    const totalMatched = matchedVideos.length;
    const totalPages = Math.ceil(totalMatched / itemsPerPage);
    
    if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = (currentPage - 1) * itemsPerPage;
    const endIdx = startIdx + itemsPerPage;

    matchedVideos.forEach((card, idx) => {
      if (idx >= startIdx && idx < endIdx) {
        card.style.display = "";
      } else {
        card.style.display = "none";
      }
    });

    renderPagination(totalPages);
  }

  function renderPagination(totalPages) {
    const container = document.getElementById("pagination-container");
    if (!container) return;
    container.innerHTML = "";

    if (totalPages <= 1) return;

    const isMobile = window.innerWidth < 600;

    // Previous Button
    const prevBtn = document.createElement("button");
    prevBtn.className = "page-btn prev-next";
    prevBtn.innerHTML = isMobile ? '<i class="fas fa-chevron-left"></i>' : '<i class="fas fa-chevron-left"></i> Previous';
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => { currentPage--; filterVideos(); window.scrollTo({top: 400, behavior: 'smooth'}); };
    container.appendChild(prevBtn);

    // Page Numbers
    const delta = isMobile ? 1 : 2;
    let startPage = Math.max(1, currentPage - delta);
    let endPage = Math.min(totalPages, startPage + (delta * 2));
    if (endPage - startPage < (delta * 2)) startPage = Math.max(1, endPage - (delta * 2));

    for (let i = startPage; i <= endPage; i++) {
      const btn = document.createElement("button");
      btn.className = `page-btn ${i === currentPage ? "active" : ""}`;
      btn.textContent = i;
      btn.onclick = () => { currentPage = i; filterVideos(); window.scrollTo({top: 400, behavior: 'smooth'}); };
      container.appendChild(btn);
    }

    // Next Button
    const nextBtn = document.createElement("button");
    nextBtn.className = "page-btn prev-next";
    nextBtn.innerHTML = isMobile ? '<i class="fas fa-chevron-right"></i>' : 'Next <i class="fas fa-chevron-right"></i>';
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.onclick = () => { currentPage++; filterVideos(); window.scrollTo({top: 400, behavior: 'smooth'}); };
    container.appendChild(nextBtn);
  }
</script>

<!-- Toast Container -->
<div id="toast-container"></div>

</body>
</html>