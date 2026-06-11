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
  <title>SVIS Alumni Gallery | Relive School Memories in Photos</title>
  <meta name="description" content="Browse through our alumni photo gallery. Explore memories from SVIS school days, annual meets, and special cultural celebrations.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    .gallery-subtitle{text-align:center;color:#6b7280;max-width:560px;margin:0 auto 2rem;font-size:.95rem}
    .cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-top: 1rem; }
    .cat-card { position:relative; border-radius:24px; overflow:hidden; height:260px; cursor:pointer; box-shadow:0 4px 16px rgba(0,0,0,.1); background: #eee; }
    .cat-card img { width:100%; height:100%; object-fit:cover; transition:transform .5s }
    .cat-card:hover img { transform:scale(1.08) }
    .cat-overlay { position:absolute; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(2px); -webkit-backdrop-filter:blur(2px); opacity:0; transition:opacity .3s; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1.5rem; text-align:center; z-index:2; }
    .cat-card:hover .cat-overlay { opacity:1 }
    .cat-overlay h3 { color:#fff; font-size:1.1rem; font-weight:700; margin-bottom:.4rem }
    .cat-overlay p { color:rgba(255,255,255,.85); font-size:.8rem; margin-bottom:.9rem }
    .cat-overlay .vbtn { padding:.45rem 1.25rem; background:linear-gradient(to right,var(--purple),var(--orange)); color:#fff; font-size:.82rem; font-weight:700; border-radius:6px; border:1px solid #fff; transition:transform .2s }
    .cat-overlay .vbtn:hover { transform:scale(1.05) }
    .cat-label { position:absolute; bottom:0; left:0; right:0; padding:1.2rem 1.2rem 1rem 1.2rem; background:linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 50%, transparent 100%); color:#fff; font-size:1.1rem; font-weight:700; text-shadow:0 2px 4px rgba(0,0,0,0.3); letter-spacing:0.02em; z-index:1; }
    
    /* Lightbox */
    .lb-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.92); z-index:70; align-items:center; justify-content:center; padding:1rem }
    .lb-overlay.open { display:flex }
    .lb-inner { position:relative; max-width:860px; width:100%; text-align:center }
    .lb-inner img { max-width:100%; max-height:80vh; border-radius:10px; box-shadow:0 10px 40px rgba(0,0,0,.5) }
    .lb-close { position:absolute; top:-2.5rem; right:0; background:none; border:none; color:#fff; font-size:1.8rem; cursor:pointer }

    .empty-state {
      text-align: center;
      padding: 5rem 2rem;
      background: #fff;
      border-radius: 14px;
      border: 2px dashed #BFDBFE;
    }
    .empty-state i { font-size: 3.5rem; color: #BFDBFE; margin-bottom: 1.25rem; }
    .empty-state h2 { font-size: 1.3rem; color: var(--purple); margin-bottom: 0.5rem; }
    .empty-state p { color: #6b7280; font-size: 0.9rem; }

    /* Pagination */
    .pagination-wrap { display: flex; justify-content: center; margin-top: 4rem; padding-bottom: 2rem; }
    .pagination { display: flex; gap: 0.6rem; list-style: none; }
    .page-link { 
      display: inline-flex; 
      align-items: center; 
      justify-content: center; 
      min-width: 42px; 
      height: 42px; 
      border-radius: 999px; 
      background: #fff; 
      border: 2px solid #1D4ED8; 
      color: #1D4ED8; 
      font-weight: 600; 
      text-decoration: none; 
      transition: all 0.3s; 
      font-size: 0.9rem; 
      font-family: 'Poppins', sans-serif;
      padding: 0 0.5rem;
    }
    .page-link:hover { 
      background: #1741b0; 
      color: #fff; 
      border-color: var(--gold); 
      transform: translateY(-2px); 
      box-shadow: 0 4px 12px rgba(29,78,216,0.2); 
    }
    .page-link.active { 
      background: #1D4ED8; 
      color: #fff; 
      border-color: var(--gold); 
      box-shadow: 0 3px 12px rgba(29,78,216,0.3); 
    }
    .page-link.prev, .page-link.next { padding: 0 1.25rem; }

    @media (max-width: 1300px) {
      .policy-hero { padding: 9rem 1.5rem 3rem; }
      .cat-grid { gap: 1.5rem; }
    }

    @media (max-width: 600px) {
      .cat-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem; }
      .cat-card { height: 200px; border-radius: 16px; }
      .pagination { gap: 0.4rem; }
      .page-link { min-width: 38px; height: 38px; font-size: 0.85rem; }
      .page-link.prev, .page-link.next { padding: 0 0.8rem; }
    }
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
      <a href="gallery.php"   class="nav-link active">Gallery</a>
      <a href="videos.php"   class="nav-link">Videos</a>
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
  <a href="gallery.php"   class="nav-link active">Gallery</a>
  <a href="videos.php"   class="nav-link">Videos</a>
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
        <h1>Event Gallery</h1>
        <p>Relive the memorable moments from SVIS events, celebrations, and alumni gatherings</p>
    </div>

    <main class="hero-page-wrap">
        <div class="policy-container">

    <!-- Dynamic Category Grid from DB -->
    <div class="cat-grid">

      <?php
        $limit = 8; // Categories per page
        $page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $total_res = $conn->query("SELECT COUNT(*) as count FROM gallery_category");
        $total_count = $total_res->fetch_assoc()['count'];
        $total_pages = ceil($total_count / $limit);

        $categories = $conn->query("SELECT * FROM gallery_category ORDER BY id DESC LIMIT $limit OFFSET $offset");
        if ($categories->num_rows > 0):
          while ($item = $categories->fetch_assoc()):
            $imgPath = "uploads/category/" . $item['image'];
            if (empty($item['image']) || !file_exists($imgPath)) {
              $imgPath = "https://via.placeholder.com/300x300?text=No+Image";
            }
      ?>

      <div class="cat-card" onclick="window.location='vieweventphotos.php?cat_id=<?= (int)$item['id'] ?>'">
        <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($item['name']) ?>"/>
        <div class="cat-label"><?= htmlspecialchars($item['name']) ?></div>
        <div class="cat-overlay">
          <h3><?= htmlspecialchars($item['name']) ?></h3>
          <p>Explore beautiful memories and photo moments captured in this category.</p>
          <a href="vieweventphotos.php?cat_id=<?= (int)$item['id'] ?>" class="vbtn" onclick="event.stopPropagation()">View Photos</a>
        </div>
      </div>

      <?php endwhile; else: ?>
        <div class="empty-state" style="grid-column: 1 / -1;">
          <i class="far fa-images"></i>
          <h2>No Categories Found</h2>
          <p>We haven't uploaded any image categories yet. Please check back later!</p>
        </div>
      <?php endif; ?>

    </div>

    <!-- Pagination Controls -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination-wrap">
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>" class="page-link prev"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php 
          $start = max(1, $page - 2);
          $end   = min($total_pages, $page + 2);
          for ($i = $start; $i <= $end; $i++): 
        ?>
          <a href="?page=<?= $i ?>" class="page-link <?= ($i === $page) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
          <a href="?page=<?= $page + 1 ?>" class="page-link next"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

        </div>
    </main>

<!-- ===== LIGHTBOX ===== -->
<div class="lb-overlay" id="lb-overlay" onclick="closeLb()">
  <div class="lb-inner" onclick="event.stopPropagation()">
    <button class="lb-close" onclick="closeLb()">&times;</button>
    <img id="lb-img" src="" alt=""/>
    <p class="lb-caption" id="lb-caption"></p>
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
          allowfullscreen="" loading="lazy"
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

  /* Toast System */

  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    // Initialize Shared Login AJAX
    handleLoginAJAX('login-form');
  });

  /* Lightbox */
  function openLb(src, cap) {
    document.getElementById('lb-img').src = src;
    document.getElementById('lb-caption').textContent = cap;
    document.getElementById('lb-overlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeLb() {
    document.getElementById('lb-overlay').classList.remove('open');
    document.body.style.overflow = '';
  }
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLb(); });
</script>

<!-- Toast Container -->
<div id="toast-container"></div>

</body>
</html>