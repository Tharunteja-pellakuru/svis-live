<?php
include('db_connect.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$status = 'error'; // default status
$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists
    $stmt = $conn->prepare("SELECT * FROM alumni_register WHERE verify_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Activate user account: set verified_status = 2 (email verified, pending admin approval), and clear token
        $update = $conn->prepare("UPDATE alumni_register SET verified_status = 2, verify_token = NULL WHERE verify_token = ?");
        $update->bind_param("s", $token);
        
        if ($update->execute()) {
            $status = 'success';
            $message = "Your email has been verified successfully. Your registration details have been sent to the school administration for review.<br><br>Please allow up to <strong>24 hours</strong> for admin approval. Once approved, you will receive a confirmation email and will be able to log in.";
        } else {
            $status = 'error';
            $message = "An error occurred while activating your account. Please contact the administrator at <a href='mailto:info@svishyd.edu.in'>info@svishyd.edu.in</a>.";
        }
    } else {
        $status = 'error';
        $message = "Invalid or expired verification link. If you have already verified your email, please try logging in or contact support.";
    }
} else {
    $status = 'error';
    $message = "No token provided. A valid verification token is required to verify your email address.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Email Verification | SVIS Alumni Network</title>
  <meta name="description" content="Verify your SVIS Alumni Network account to connect with former classmates and stay updated on school events.">
  <link rel="icon" type="image/png" href="Logo/Logo.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    /* Custom Styling for the verification card */
    .verification-card {
      background: #fff;
      border-radius: 16px;
      border: 1.5px solid rgba(29, 78, 216, 0.15);
      padding: 4rem 2.5rem;
      margin: 4rem auto;
      max-width: 600px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(29, 78, 216, 0.08);
      position: relative;
      overflow: hidden;
    }
    
    .verification-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: <?php echo $status === 'success' ? 'linear-gradient(90deg, #10B981, #059669)' : 'linear-gradient(90deg, #EF4444, #DC2626)'; ?>;
    }

    .verification-icon {
      font-size: 4.5rem;
      color: <?php echo $status === 'success' ? '#10B981' : '#EF4444'; ?>;
      margin-bottom: 1.5rem;
      animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    .verification-title {
      font-family: 'Lato', serif;
      font-size: 1.8rem;
      color: #1e3a8a;
      font-weight: 700;
      margin-bottom: 1.2rem;
    }

    .verification-desc {
      font-family: 'Poppins', sans-serif;
      color: #4b5563;
      font-size: 1.05rem;
      line-height: 1.7;
      margin-bottom: 2.5rem;
    }

    .redirect-text {
      font-size: 0.9rem;
      color: #9ca3af;
      margin-top: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    @keyframes popIn {
      from { transform: scale(0); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }
  </style>
  <?php if ($status === 'success'): ?>
  <script>
    setTimeout(() => {
      window.location = 'index.php';
    }, 10000);
  </script>
  <?php endif; ?>
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
      <a href="videos.php"    class="nav-link">Videos</a>
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
  <a href="videos.php"    class="nav-link">Videos</a>
  <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
    <a href="profileedit.php" class="nav-link">Profile</a>
    <a href="logout.php" class="nav-login-btn">Logout</a>
  <?php } else { ?>
    <button class="nav-login-btn" onclick="showModal('login')">Login</button>
  <?php } ?>
</div>

<!-- ===== HERO ===== -->
<div class="policy-hero">
  <h1>Email Verification</h1>
  <p>Alumni Registration Activation — SVIS Alumni Network</p>
</div>

<!-- ===== CONTENT ===== -->
<div class="policy-page">
  <div class="policy-container">
    <div class="verification-card">
      <?php if ($status === 'success'): ?>
        <div class="verification-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="verification-title">Verification Successful!</h2>
        <p class="verification-desc">
          <?php echo $message; ?>
        </p>
        <a href="index.php" class="nav-login-btn" style="min-width: 180px; margin: 0 auto; display: inline-flex;">Go to Homepage</a>
        <p class="redirect-text">
          <i class="fas fa-spinner fa-spin"></i> 
          You will be automatically redirected to the homepage in 10 seconds.
        </p>
      <?php else: ?>
        <div class="verification-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <h2 class="verification-title">Verification Failed</h2>
        <p class="verification-desc">
          <?php echo $message; ?>
        </p>
        <a href="index.php" class="nav-login-btn" style="min-width: 180px; margin: 0 auto; display: inline-flex;">Back to Home</a>
      <?php endif; ?>
    </div>
  </div>
</div>

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
      <div class="footer-contact-item"><i class="fas fa-map-marker-alt"></i><span>150-152 Jayabheri Park, Behind Cine Planet Multiplex, Kompally, Hyderabad – 500100, Telangana</span></div>
      <div class="footer-contact-item"><i class="fas fa-phone"></i><span>040-23005000</span></div>
      <div class="footer-contact-item"><i class="fas fa-envelope"></i><span>info@svishyd.edu.in</span></div>
      <div class="footer-contact-item"><i class="fas fa-clock"></i><span>Mon–Fri: 8:15 AM – 3:15 PM<br>Saturday: 8:15 AM – 12:30 PM</span></div>
    </div>
    <div class="footer-col">
      <h3>Location</h3>
      <div class="footer-map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15217.29501790504!2d78.478686!3d17.539766!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb855ec1fabca7%3A0x216c99b72461c6a0!2sSadhu%20Vaswani%20International%20School!5e0!3m2!1sen!2sin!4v1778574953962!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

<div id="toast-container"></div>

<!-- ===== JAVASCRIPT ===== -->
<script src="shared.js"></script>
<script>
  /* Mobile Nav */
  const menuBtn  = document.getElementById('hamburger-btn');
  const mobileNav = document.getElementById('mobile-menu');
  const hamIcon  = document.getElementById('hamburger-icon');
  if(menuBtn && mobileNav && hamIcon) {
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
  }

  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    handleLoginAJAX('login-form');
    handleRegisterAJAX('register-form');
  });
</script>

</body>
</html>
