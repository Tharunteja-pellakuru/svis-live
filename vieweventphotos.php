<?php 
include('db_connect.php'); 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['cat_id'])) { 
    echo "<script>alert('Invalid Category'); window.location='gallery.php';</script>"; 
    exit(); 
}

$cat_id = intval($_GET['cat_id']);
$catStmt = $conn->prepare("SELECT name FROM gallery_category WHERE id = ?");
$catStmt->bind_param("i", $cat_id); 
$catStmt->execute(); 
$catResult = $catStmt->get_result();

if ($catResult->num_rows == 0) { 
    echo "<script>alert('Category not found'); window.location='gallery.php';</script>"; 
    exit(); 
}

$category = $catResult->fetch_assoc();
$imgStmt = $conn->prepare("SELECT * FROM gallery WHERE category_id = ? ORDER BY id DESC");
$imgStmt->bind_param("i", $cat_id); 
$imgStmt->execute(); 
$images = $imgStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($category['name']) ?> - Event Photos | SVIS Alumni</title>
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Lato:wght@400;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    /* Hero override or local adjustments */


    /* Photo Grid Styles matching existing design language */
    .photo-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1.5rem;
    }
    .photo-item {
      position: relative;
      border-radius: 14px;
      overflow: hidden;
      aspect-ratio: 4/3;
      cursor: zoom-in;
      background: #eee;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      transition: transform 0.4s, box-shadow 0.4s;
    }
    .photo-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 24px rgba(29,78,216,0.15);
    }
    .photo-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    .photo-item:hover img { transform: scale(1.08); }
    
    .photo-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.45);
      backdrop-filter: blur(2px);
      -webkit-backdrop-filter: blur(2px);
      opacity: 0;
      transition: opacity 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .photo-item:hover .photo-overlay { opacity: 1; }
    .photo-overlay i {
      color: #fff;
      font-size: 1.25rem;
      background: linear-gradient(to right, var(--purple), var(--blue));
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      border: 1px solid #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

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

    /* Lightbox - matches project theme */
    .lb-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.92);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      backdrop-filter: blur(8px);
    }
    .lb-overlay.open { display: flex; }
    .lb-content {
      position: relative;
      max-width: 900px;
      width: 100%;
      text-align: center;
    }
    .lb-content img {
      max-width: 100%;
      max-height: 80vh;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.5);
    }
    .lb-close {
      position: absolute;
      top: -2.5rem;
      right: 0;
      background: none;
      border: none;
      color: #fff;
      font-size: 2rem;
      cursor: pointer;
      line-height: 1;
    }
    .lb-caption {
      color: #fff;
      margin-top: 1rem;
      font-size: 1rem;
      font-family: 'Poppins', sans-serif;
    }

    .back-btn {
        margin-top: 1.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        background: var(--blue);
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: transform 0.2s, background 0.2s;
        border: none;
    }
    .back-btn:hover { transform: translateX(-4px); background: var(--blue-dark); }
  </style>
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

<div class="policy-hero">
    <h1><?= htmlspecialchars($category['name']) ?></h1>
    <p>Captured moments and beautiful memories from the <?= htmlspecialchars($category['name']) ?> collection.</p>

    <div class="breadcrumbs">
      <a href="index.php">Home</a>
      <i class="fas fa-chevron-right"></i>
      <a href="gallery.php">Gallery</a>
      <i class="fas fa-chevron-right"></i>
      <span><?= htmlspecialchars($category['name']) ?></span>
    </div>
</div>

<main class="hero-page-wrap">
  <div class="policy-container">
    
    <div class="photo-grid">
      <?php if ($images->num_rows > 0): 
        while ($img = $images->fetch_assoc()): 
          $imgPath = "uploads/" . $img['image_name']; 
          if (!file_exists($imgPath)) { $imgPath = "https://via.placeholder.com/800x600?text=Image+Not+Found"; }
          $caption = !empty($img['caption']) ? $img['caption'] : $category['name'];
      ?>
        <div class="photo-item" onclick="openLightbox('<?= $imgPath ?>', '<?= addslashes(htmlspecialchars($caption)) ?>')">
          <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($caption) ?>" loading="lazy"/>
          <div class="photo-overlay">
            <i class="fas fa-search-plus"></i>
          </div>
        </div>
      <?php endwhile; else: ?>
        <div class="empty-state" style="grid-column: 1 / -1;">
          <i class="far fa-images"></i>
          <h2>No Photos Found</h2>
          <p>We haven't uploaded any photos to this category yet. Please check back later!</p>
          <a href="gallery.php" class="view-all-btn" style="margin-top: 1.5rem;">Explore Other Categories</a>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

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

<!-- ===== LIGHTBOX ===== -->
<div class="lb-overlay" id="lb-overlay" onclick="closeLightbox()">
  <div class="lb-content" onclick="event.stopPropagation()">
    <button class="lb-close" onclick="closeLightbox()">&times;</button>
    <img id="lb-img" src="" alt="Enlarged Photo"/>
    <div class="lb-caption" id="lb-caption"></div>
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

<script>
  // Mobile Nav 
  const menuBtn = document.getElementById('hamburger-btn');
  const mobileNav = document.getElementById('mobile-menu');
  const hamIcon = document.getElementById('hamburger-icon');
  if (menuBtn) {
    menuBtn.addEventListener('click', () => {
      const o = mobileNav.classList.toggle('open');
      if (hamIcon) hamIcon.style.transform = o ? 'rotate(90deg)' : 'rotate(0)';
    });
  }

  // Modals 
  function showModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.remove('modal-hidden'); m.classList.add('modal-visible');
    document.body.style.overflow = 'hidden';
  }
  function hideModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.remove('modal-visible'); m.classList.add('modal-hidden');
    document.body.style.overflow = '';
  }

  // Password Toggle 
  function togglePassword(id) {
    const i = document.getElementById(id);
    if (i) i.type = i.type === 'password' ? 'text' : 'password';
  }

  function openLightbox(src, caption) {
    const lb = document.getElementById('lb-overlay');
    const img = document.getElementById('lb-img');
    const cap = document.getElementById('lb-caption');
    
    img.src = src;
    cap.textContent = caption;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
  }

  function closeLightbox() {
    const lb = document.getElementById('lb-overlay');
    lb.classList.remove('open');
    document.body.style.overflow = '';
  }

  // Keyboard support for Lightbox
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightbox();
  });
</script>

</body>
</html>
