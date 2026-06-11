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
  <title>Our Founders | The Vision Behind SVIS Hyderabad</title>
  <meta name="description" content="Meet the visionary founders of Sadhu Vaswani International School and learn about the philosophy of Sadhu Vaswani and Dada J.P. Vaswani.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    .founders-page { background:#fff; }
    .founder-block { margin-bottom:5rem; }
    .founder-grid { display:grid; gap:3rem; align-items:stretch; }
    .right-img { grid-template-columns:1.2fr 0.8fr; }
    .left-img  { grid-template-columns:0.8fr 1.2fr; }
    .founder-grid img { width:100%; height:100%; object-fit:cover; border-radius:14px; box-shadow:0 8px 30px rgba(0,0,0,0.14); display:block; }
    .founder-name  { font-family:'lato',serif; font-size:1.85rem; font-weight:800; color:#EA9856; margin-bottom:0.25rem; }
    .founder-role  { color:var(--orange); font-style:normal; font-weight:700; font-size:1.05rem; margin-bottom:1.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .founder-text p{ color:var(--purple); font-family:'Poppins',sans-serif; font-size:15px; line-height:1.9; text-align:justify; margin-bottom:1rem; }
    .founder-text p.quote { font-style:normal; font-weight:600; color:var(--blue); border-left:4px solid var(--orange); padding-left:1.25rem; margin:1.5rem 0; }
    
    .team-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:1.5rem; margin-top:2rem; }
    .team-card { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 18px rgba(0,0,0,0.08); text-align:center; transition:transform 0.3s,box-shadow 0.3s; height: 100%; display: flex; flex-direction: column; width: 100%; }
    .team-card:hover { transform:translateY(-8px); box-shadow:0 15px 40px rgba(0,0,0,0.12); }
    .team-card-img { width:100%; height:280px; object-fit:cover; object-position:top; }
    .team-card-init { width:100%; height:280px; background:linear-gradient(135deg,var(--purple),var(--orange)); display:flex; align-items:center; justify-content:center; font-size:4rem; color:#fff; font-weight:700; }
    .team-card-body { padding:1.75rem 1.25rem; flex: 1; display: flex; flex-direction: column; }
    .team-card-name { font-family:'lato',serif; font-size:1.2rem; font-weight:700; color:var(--purple); margin-bottom:0.4rem; }
    .team-card-role { color:var(--orange); font-size:0.85rem; font-weight:700; text-transform: uppercase; letter-spacing: 0.8px; min-height: 2.5rem; display: flex; align-items: center; justify-content: center; }
    .team-card-desc { color:#6b7280; font-size:0.88rem; line-height:1.6; margin-top:1rem; flex: 1; }

    @media (max-width: 1300px) {
      .policy-hero { padding: 9rem 1.5rem 3rem; }
      .founder-grid { gap: 2rem; align-items: start; }
      .right-img, .left-img { grid-template-columns: 1fr; }
      .founder-grid img { order: -1; max-width: 400px; height: auto; margin: 0 auto; }
      .founder-name { text-align: center; font-size: 1.6rem; }
      .founder-role { text-align: center; }
    }

    @media (max-width: 1000px) {
      .team-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
      .founder-text p { text-align: justify; font-size: 14px; }
      .founder-block { margin-bottom: 3.5rem; }
      .team-grid { grid-template-columns: 1fr; }
      .founder-grid img { max-width: 100%; }
      .vp-msg-box { padding: 1.5rem !important; }
    }

    /* Navbar styles moved to shared.css */
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
      <a href="founders.php" class="nav-link active">Founders</a>
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
  <a href="about.php"    class="nav-link">About</a>
  <a href="founders.php" class="nav-link active">Founders</a>
  <a href="gallery.php"  class="nav-link">Gallery</a>
  <a href="videos.php"   class="nav-link">Videos</a>
  <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
    <a href="profileedit.php" class="nav-link">Profile</a>
    <a href="logout.php" class="nav-login-btn">Logout</a>
  <?php } else { ?>
    <button class="nav-login-btn" onclick="showModal('login')">Login</button>
  <?php } ?>
</div>

<!-- ===== MAIN CONTENT ===== -->
    <!-- ===== HERO ===== -->
    <div class="policy-hero">
        <h1>Founders &amp; Our Team</h1>
        <p>Honoring the visionaries and leaders who shaped our institution's excellence</p>
    </div>

    <main class="hero-page-wrap founders-page">
        <div class="policy-container">

    <!-- Chairman -->
    <div class="founder-block">
      <div class="founder-grid right-img">
        <div class="founder-text">
          <h2 class="founder-name">Dr. Harish Mirchandani</h2>
          <p class="founder-role">Chairman, Sadhu Vaswani International School</p>
          <p>Warm Greetings from Sadhu Vaswani International School. The year 2017, is a year to rejoice — it is the decennial year of SVIS. This has been possible due to God's grace, the co-operation of our esteemed parents and of course the hard work pumped in by our staff-members.</p>
          <p>When I rewind my thoughts back to 2008, we just started with 72 students which now are around 3000. We at SVIS have never craved for the quantity but for the quality of education imparted to our students. We always strive to provide the best to our students; not only academically; but for their spiritual growth also.</p>
          <p>Dada J.P. Vaswani believes that the modern education has sharpened the minds and brains of the children but has blunted their hearts. Wherever we go there is hatred, jealousy, apathy and unhealthy competition. It is the schools and colleges who must change this.</p>
          <p class="quote">"New India will not be made in the Rajya Sabha or Lok Sabha but in the schools and colleges." — Sadhu Vaswani</p>
          <p>At SVIS we strive to impart value based education based on <strong>4Cs — Character, Culture, Compassion and Courage</strong>. It is our endeavor to provide the society with top-quality professionals but what we strive for most is to give to the society decent and honest human beings. This is the need of the hour in today's modern world.</p>
        </div>
        <img src="images/MAHI6677.JPG" alt="Dr. Harish Mirchandani" onerror="this.style.cssText='height:320px;background:linear-gradient(135deg,#41164B,#EA9856);border-radius:14px;';this.removeAttribute('src')"/>
      </div>
    </div>

    <!-- Principal -->
    <div class="founder-block">
      <div class="founder-grid left-img">
        <img src="images/MAHI3055.jpg" alt="Ms. G. Arpitha" onerror="this.style.cssText='height:380px;background:linear-gradient(135deg,#004aad,#41164B);border-radius:14px;';this.removeAttribute('src')"/>
        <div class="founder-text">
          <h2 class="founder-name">Ms. G. Arpitha</h2>
          <p class="founder-role">Principal, Sadhu Vaswani International School</p>
          <p class="quote">"Education is a shared commitment among motivated students, dedicated teachers and enthusiastic parents with high expectations."</p>
          <p>We have started yet another glorious session. I can say it's the beginning of a new era, when we all have learnt to equip ourselves with the new normal. This journey can be rewarding and full of accomplishments if we strive to remove barriers of effectiveness.</p>
          <p>We affirm that education begins at birth and continues throughout life. Our complete focus is to provide a support system to our children so they continue learning and developing towards becoming whole and healthy individuals. It is not enough to make children literate and academically intelligent — what is more important is to make them wise.</p>
          <p>At SVIS, we believe that true wisdom is the ability to listen to your heart and that each child's self-esteem, dignity, physical and emotional well-being must be cultivated with the same importance as that given to academic achievement.</p>
          <p>We create a passionate schooling experience recognized for its warmth, energy and excellence. We foster a positive spirit and believe in partnership between students, parents and teachers striving to create a milieu that sustains excellence.</p>
        </div>
      </div>
    </div>

    <!-- Our Team Section -->
    <h2 class="page-title" style="margin-top:2rem;">Our Leadership Team</h2>
    <div class="team-grid">
      <div class="team-card">
        <img class="team-card-img" src="images/MAHI6677.JPG" alt="Dr. Harish Mirchandani" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
        <div class="team-card-init" style="display:none;">H</div>
        <div class="team-card-body">
          <h3 class="team-card-name">Dr. Harish Mirchandani</h3>
          <p class="team-card-role">Chairman</p>
          <p class="team-card-desc">Founder and visionary leader of SVIS, committed to value-based education grounded in the 4Cs.</p>
        </div>
      </div>
      <div class="team-card">
        <img class="team-card-img" src="images/MAHI3055.jpg" alt="Ms. G. Arpitha" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
        <div class="team-card-init" style="display:none;">A</div>
        <div class="team-card-body">
          <h3 class="team-card-name">Ms. G. Arpitha</h3>
          <p class="team-card-role">Principal</p>
          <p class="team-card-desc">Dedicated to creating a passionate schooling experience recognized for warmth, energy and excellence.</p>
        </div>
      </div>
      <div class="team-card">
        <img class="team-card-img" src="https://www.svishyd.edu.in/wp-content/uploads/2022/11/Ms.-Nihita-Ranjan-Vice-Principal.jpg" alt="Ms. Nihita Ranjan" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
        <div class="team-card-init" style="display:none;">N</div>
        <div class="team-card-body">
          <h3 class="team-card-name">Ms. Nihita Ranjan</h3>
          <p class="team-card-role">Vice Principal</p>
          <p class="team-card-desc">Aligned with the vision of true education — teaching students to think intensively and critically with character.</p>
        </div>
      </div>
    </div>

    <!-- Vice Principal Message -->
    <div class="vp-msg-box" style="margin-top:4rem;background:var(--bg-light);border-radius:16px;padding:2.5rem;border-left:5px solid var(--orange);">
      <h3 style="font-family:'lato',serif;color:var(--purple);font-size:1.3rem;margin-bottom:0.5rem;">Ms. Nihita Ranjan — Vice Principal's Message</h3>
      <p style="font-style:normal;color:var(--orange);font-weight:600;margin-bottom:1rem;">"The function of education is to teach one to think intensively and critically. Intelligence plus character — that is the goal of true education." — Martin Luther King</p>
      <p style="color:var(--purple);font-family:'Poppins',sans-serif;font-size:15px;line-height:1.85;text-align:justify;">At SVIS, we are aligned with the thoughts of Martin Luther King. The likelihood of achieving this goal of education is strengthened by the fact that SVIS offers an academic program which includes the breadth and depth of learning, tailored to individual needs. We teach students to always aim high and cultivate core values like creativity, respecting elders, loyalty to the nation and having a balanced understanding of the prevailing global situation. By including art and sports as a pedagogical tool for experiential and joyful learning, we have strengthened the belief that learning can happen in many forms.</p>
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
    <p>©2026 Sadhu Vaswani International School, Hyderabad. All Rights Reserved. | Concept &amp; Design by eparivartan</p>
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
    const o = mobileNav.classList.toggle('open');
    hamIcon.style.transform = o ? 'rotate(90deg)' : 'rotate(0)';
  });
  document.addEventListener('click', e => {
    if (!menuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
      mobileNav.classList.remove('open');
      hamIcon.style.transform = 'rotate(0)';
    }
  });

  /* ---- Login / Register Modals ---- */
  /* ---- DOMContentLoaded Init ---- */
  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    handleLoginAJAX('login-form');
    handleRegisterAJAX('register-form');
  });



</script>

</body>
</html>