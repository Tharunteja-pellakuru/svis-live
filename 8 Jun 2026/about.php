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
  <title>About Sadhu Vaswani International School | SVIS History & Mission</title>
  <meta name="description" content="Learn about the rich history, values, and educational mission of Sadhu Vaswani International School (SVIS), Hyderabad. Established in 2008 to impart value-based education.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>

    /* Navbar styles moved to shared.css */

    /* ============================================================
       ABOUT PAGE STYLES
    ============================================================ */
    .about-page { background: #fff; }
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: stretch; }
    .two-col img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; box-shadow: 0 8px 28px rgba(0,0,0,0.12); display: block; }
    .body-text p { color: var(--purple); font-family: 'Poppins', sans-serif; font-size: 15px; line-height: 1.9; text-align: justify; margin-bottom: 1rem; }
    .orange-head { color: var(--orange); font-weight: 700; font-size: 1.05rem; margin-bottom: 0.4rem; }

    .info-box { background: var(--bg-light); border-radius: 12px; padding: 1.75rem 2rem; margin-top: 2rem; }
    .info-box p { color: var(--purple); font-family: 'Poppins', sans-serif; font-size: 15px; line-height: 1.85; }

    /* New Activities & Clubs Styles */
    .activity-pill-wrap {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      justify-content: center;
    }
    .act-pill {
      background: #eff6ff;
      color: var(--blue);
      padding: 0.5rem 1.25rem;
      border-radius: 999px;
      font-size: 0.88rem;
      font-weight: 600;
      font-family: 'Poppins', sans-serif;
      border: 1.5px solid rgba(29, 78, 216, 0.1);
      transition: all 0.3s;
    }
    .act-pill:hover {
      background: var(--blue);
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
    }

    .club-grid-new {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 1.5rem;
    }
    .club-card-new {
      background: #fff;
      border-radius: 16px;
      padding: 1.5rem;
      border: 1px solid #eef2ff;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      transition: all 0.3s;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .club-card-new:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(29, 78, 216, 0.1);
      border-color: var(--blue-mid);
    }
    .club-icon {
      font-size: 2.2rem;
      margin-bottom: 1rem;
    }
    .club-card-new h4 {
      color: var(--blue-dark);
      font-size: 1.05rem;
      font-weight: 700;
      margin-bottom: 0.6rem;
      font-family: 'Poppins', sans-serif;
    }
    .club-card-new p {
      color: #6b7280;
      font-size: 0.85rem;
      line-height: 1.6;
      margin: 0;
    }

    .houses-grid {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 1.5rem !important;
      margin-top: 1.5rem;
    }
    .house-card-img-wrap {
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      border: 1px solid rgba(0,0,0,0.05);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      background: #fff;
      aspect-ratio: 1 / 1;
    }
    .house-card-img-wrap:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .house-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .house-desc {
      text-align: center;
      color: #6b7280;
      max-width: 680px;
      margin: 0 auto 2rem;
      font-size: 0.9rem;
      line-height: 1.7;
    }

    @media (max-width: 1300px) {
       .two-col { grid-template-columns: 1fr; gap: 2rem; align-items: start; }
       .two-col img { height: auto; max-height: 400px; }
       .policy-hero { padding: 9rem 1.5rem 3rem; }
     }
    @media (max-width: 1024px) {
      .houses-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
      }
    }
    @media (max-width: 600px) {
      .activity-pill-wrap { gap: 0.5rem; }
      .act-pill { padding: 0.4rem 1rem; font-size: 0.8rem; }
      .club-grid-new { grid-template-columns: 1fr; }
      .body-text p, .info-box p, .house-desc { text-align: justify; }
    }
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
      <a href="about.php"    class="nav-link active">About</a>
      <a href="founders.php" class="nav-link">Founders</a>
      <a href="gallery.php"  class="nav-link">Gallery</a>
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
  <a href="about.php"    class="nav-link active">About School</a>
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

<!-- ===== MAIN ===== -->
    <!-- ===== HERO ===== -->
    <div class="policy-hero">
        <h1>About SVIS Alumni</h1>
        <p>Discover our journey, mission, and the legacy of Sadhu Vaswani International School</p>
    </div>

    <main class="hero-page-wrap about-page">
    <div class="policy-container">
      <h2 class="page-title" style="margin-bottom:2.5rem;">Our Legacy & Philosophy</h2>
      <div class="two-col">
        <div class="body-text">
          <p>Sadhu Vaswani International School (SVIS), Kompally, Hyderabad, established in 2008, is one of the many educational institutions which the Sadhu Vaswani Mission started all over India. It is a progressive school based on Indian thought, culture, tradition and the educational ideals of Sadhu Vaswani. The school is run by the Sadhu Vaswani Mission.</p>
          <p>The aim of our school is to impart integrated and comprehensive education which is formative and not merely informative. It lays emphasis on the full development of the student's character and personality which should normally lead them to self-fulfillment and dedication to the service of society.</p>
          <p>Students are placed in an atmosphere that enables them to develop reverence for all prophets, seers and sages, all heroes of humanity, all races and religions. The school environment teaches them to understand the love of God and the essential unity of life.</p>
          <p>Our school is a non-communal, non-sectarian institution which places emphasis on the permanent values of life as enunciated by ancient and modern thinkers.</p>
        </div>
      <div>
        <img src="images/School-image.JPG " alt="SVIS School"/>
      </div>
    </div>

    <!-- Scheme of Education (Full Width) -->
    <div class="info-box" style="margin-top:2.5rem;">
      <p><strong>Scheme of Education:</strong> The school is affiliated to the Central Board of Secondary Education (CBSE) under the 10+2 scheme of education. It prepares students for the All India Secondary School (Class X) and the All India Senior School Certificate (Class XII) Examinations.</p>
    </div>

    <!-- Vision & Mission -->
    <div style="margin-top:4rem;">
      <h2 class="page-title">Our Vision &amp; Mission</h2>
      
      <div class="info-box" style="margin-bottom:2.5rem;">
        <p style="font-style:normal;font-size:1rem;color:var(--purple);">The Sadhu Vaswani Mission (SVM) seeks to bear witness to the truth that there is but One Life flowing in all — Men, Birds, Animals, Things; Animate and Inanimate. The SVM believes that life must have a spiritual orientation and promotes the Practice of Kirtan (group chanting), prayer, meditation and above all, love for others. The others are not apart from us. We all are part of the One Whole.</p>
      </div>

      <div class="two-col" style="align-items:center;">
        <div>
          <img src="images/School-image.JPG " alt="Vision Mission"/>
        </div>
        <div class="body-text">
          <div style="margin-bottom:1.5rem;">
            <p class="orange-head"><i class="fas fa-bullseye" style="margin-right: 8px;"></i> School Vision Statement</p>
            <p>To enable students to imbibe the four C's of Education — <strong>Culture, Compassion, Character and Courage</strong>; to inculcate love for their country, to impart value based education, to revere all life, animate and inanimate, in keeping with the teachings of our revered Guruji, Sadhu Vaswani.</p>
          </div>
          <div style="margin-bottom:1.5rem;">
            <p class="orange-head"><i class="fas fa-lightbulb" style="margin-right: 8px;"></i> School Mission Statement</p>
            <p>The school believes that life must have a spiritual orientation and promotes the practice of prayer, meditation and above all, living for others; the others are not apart from us; we all are a part of the One Whole.</p>
          </div>
          <div>
            <p class="orange-head"><i class="fas fa-book-open" style="margin-right: 8px;"></i> Our Aim</p>
            <p>To impart integrated and comprehensive education which is formative and not merely informative — laying emphasis on the full development of the student's character and personality leading to self-fulfillment and service of society.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Activities & Clubs -->
    <div style="margin-top:4rem;">
      <h2 class="page-title">Activities &amp; Clubs</h2>
      
      <div style="margin-bottom:3rem;">
        <p class="orange-head" style="text-align:center; margin-bottom:1.5rem;"><i class="fas fa-running" style="margin-right: 8px;"></i> Co-Curricular Activities</p>
        <div class="activity-pill-wrap">
          <span class="act-pill">Chess</span>
          <span class="act-pill">Badminton</span>
          <span class="act-pill">Carom</span>
          <span class="act-pill">Dance</span>
          <span class="act-pill">Drama</span>
          <span class="act-pill">Art & Craft</span>
          <span class="act-pill">Gymnastics</span>
          <span class="act-pill">Music (Vocal & Instrumental)</span>
          <span class="act-pill">Roller Skating</span>
          <span class="act-pill">Public Speaking</span>
          <span class="act-pill">Martial Arts</span>
          <span class="act-pill">Yoga & Meditation</span>
          <span class="act-pill">Gardening</span>
          <span class="act-pill">Tennis</span>
          <span class="act-pill">Table Tennis</span>
          <span class="act-pill">Cricket</span>
          <span class="act-pill">Basketball</span>
          <span class="act-pill">Athletics</span>
        </div>
      </div>

      <div>
        <p class="orange-head" style="text-align:center; margin-bottom:1.5rem;"><i class="fas fa-users" style="margin-right: 8px;"></i> Student Clubs</p>
        <div class="club-grid-new">
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-masks-theater" style="color: #1D4ED8;"></i></div>
            <h4>Dramatics Club</h4>
            <p>Develop language, communication skills and emotional intelligence.</p>
          </div>
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-microscope" style="color: #1D4ED8;"></i></div>
            <h4>Science Club</h4>
            <p>Improve scientific attitude through hands-on experiments.</p>
          </div>
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-palette" style="color: #1D4ED8;"></i></div>
            <h4>Cultural Club</h4>
            <p>Discover talents in music, dance and art.</p>
          </div>
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-globe" style="color: #1D4ED8;"></i></div>
            <h4>General Awareness</h4>
            <p>Current affairs knowledge and awareness of surroundings.</p>
          </div>
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-handshake" style="color: #1D4ED8;"></i></div>
            <h4>Interact Club</h4>
            <p>Community service to help the poor and needy.</p>
          </div>
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-calculator" style="color: #1D4ED8;"></i></div>
            <h4>Math Club</h4>
            <p>Develop mathematical understanding through techniques.</p>
          </div>
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-monument" style="color: #1D4ED8;"></i></div>
            <h4>Heritage Club</h4>
            <p>Awareness about Arts and Cultural Heritage of India.</p>
          </div>
          <div class="club-card-new">
            <div class="club-icon"><i class="fas fa-tools" style="color: #1D4ED8;"></i></div>
            <h4>Skills & Craft</h4>
            <p>Enhance dexterity focusing on fine motor skills.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- House System -->
    <div style="margin-top:4rem;margin-bottom:2rem;">
      <h2 class="page-title">House System</h2>
      <p class="house-desc">The school students are divided into four houses. All competitions in games and co-curricular activities are held on a 'house' basis. The house system forges bonds of loyalty, develops better student-teacher relationships and encourages self-discipline.</p>
      <div class="houses-grid">
        <div class="house-card-img-wrap">
          <img src="images/House-4.png" alt="Rani Laxmi Bai House" class="house-img"/>
        </div>
        <div class="house-card-img-wrap">
          <img src="images/House-1.png" alt="Mirabai House" class="house-img"/>
        </div>
        <div class="house-card-img-wrap">
          <img src="images/House-3.png" alt="Rabindranath Tagore House" class="house-img"/>
        </div>
        <div class="house-card-img-wrap">
          <img src="images/House-2.png" alt="Mahatma Gandhi House" class="house-img"/>
        </div>
      </div>
    </div>

        </div>
    </main>


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
            <div class="input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <input type="email" name="email" placeholder="Enter your email" required/>
          </div>
        </div>
        <div class="form-group">
          <label>Password<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
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
        <span>150-152 Jayabheri Park, Behind Cine Planet Multiplex, Kompally, Hyderabad – 500100, Telangana</span>
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
    <p>&#169;2026 Sadhu Vaswani International School, Hyderabad. All Rights Reserved. | Concept &amp; Design by eparivartan</p>
  </div>
</footer>


<!-- Toast Container -->
<div id="toast-container"></div>


<!-- ===== JAVASCRIPT ===== -->
<script src="shared.js"></script>
<script>

  /* ---- Mobile Nav ---- */
  const menuBtn   = document.getElementById('hamburger-btn');
  const mobileNav = document.getElementById('mobile-menu');
  const hamIcon   = document.getElementById('hamburger-icon');

  menuBtn.addEventListener('click', () => {
    const open = mobileNav.classList.toggle('open');
    hamIcon.style.transform = open ? 'rotate(90deg)' : 'rotate(0)';
  });
  document.addEventListener('click', e => {
    if (!menuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
      mobileNav.classList.remove('open');
      hamIcon.style.transform = 'rotate(0)';
    }
  });

  /* ---- Login / Register Modals ---- */
  function showModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.remove('modal-hidden');
    m.classList.add('modal-visible');
    document.body.style.overflow = 'hidden';
    if (type === 'register') hideModal('login');
    if (type === 'login')    hideModal('register');
  }
  function hideModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.remove('modal-visible');
    m.classList.add('modal-hidden');
    document.body.style.overflow = '';
  }
  document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
      if (e.target === this) {
        this.classList.remove('modal-visible');
        this.classList.add('modal-hidden');
        document.body.style.overflow = '';
      }
    });
  });

  /* ---- Password Toggle ---- */
  function togglePassword(id) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
  }

  /* ---- Toast System ---- */
  function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icon = type === 'success'
      ? '<i class="fas fa-check-circle"></i>'
      : '<i class="fas fa-exclamation-circle"></i>';
    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 4200);
  }

  /* ---- DOMContentLoaded Init ---- */
  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    handleLoginAJAX('login-form');
    handleRegisterAJAX('register-form');
  });

</script>

</body>
</html>