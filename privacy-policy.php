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
  <title>Privacy Policy | SVIS Alumni Network</title>
  <meta name="description" content="Read the SVIS Alumni Network privacy policy to understand how we protect your personal information and ensure data security for our members.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
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
  <h1>Privacy Policy</h1>
  <p>SVIS Alumni Network — Sadhu Vaswani International School, Hyderabad</p>
</div>

<!-- ===== CONTENT ===== -->
<div class="policy-page">
  <div class="policy-container">

    <!-- Dynamic Last Updated date from PHP -->
    <!-- <div class="effective-bar">
      <i class="fas fa-calendar-check"></i>
      <span><strong>Last Updated:</strong> <?php echo date('F d, Y'); ?></span>
    </div> -->

    <!-- TOC -->
    <div class="toc-card">
      <h2><i class="fas fa-list" style="margin-right:0.4rem;"></i> Table of Contents</h2>
      <ol>
        <li><a href="#sec1">Information We Collect</a></li>
        <li><a href="#sec2">How We Use Your Information</a></li>
        <li><a href="#sec3">Information Sharing</a></li>
        <li><a href="#sec4">Data Security</a></li>
        <li><a href="#sec5">Your Rights &amp; Choices</a></li>
        <li><a href="#sec6">Cookies &amp; Tracking</a></li>
        <li><a href="#sec7">Children's Privacy</a></li>
        <li><a href="#sec8">Third-Party Links</a></li>
        <li><a href="#sec9">Changes to This Policy</a></li>
        <li><a href="#sec10">Contact Us</a></li>
      </ol>
    </div>

    <!-- Intro -->
    <div class="policy-section">
      <p>Welcome to the <strong>SVIS Alumni Network</strong>, an official platform of Sadhu Vaswani International School (SVIS), Kompally, Hyderabad, Telangana. We are committed to protecting your personal information and your right to privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or register as an alumni member.</p>
      <div class="info-box">
        <i class="fas fa-info-circle"></i>
        By accessing or using the SVIS Alumni Network platform, you agree to the terms described in this Privacy Policy. If you do not agree, please discontinue use of our services.
      </div>
    </div>

    <!-- Section 1 -->
    <div class="policy-section" id="sec1">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-database"></i></div>
        <h2>1. Information We Collect</h2>
      </div>
      <p>We collect information you voluntarily provide when registering on our platform, as well as information collected automatically during your use of our services.</p>
      <p><strong>Personal Information you provide:</strong></p>
      <ul>
        <li>Full name and profile photograph</li>
        <li>Email address and contact number</li>
        <li>Year of graduation (batch) and gender</li>
        <li>Current profession, employer, or educational institution</li>
        <li>City/country of residence</li>
        <li>Password (stored in encrypted form)</li>
        <li>Any additional details you choose to add to your alumni profile</li>
      </ul>
      <p><strong>Information collected automatically:</strong></p>
      <ul>
        <li>IP address and browser type</li>
        <li>Pages visited and time spent on the platform</li>
        <li>Device type and operating system</li>
        <li>Referral URLs and general geographic location</li>
      </ul>
    </div>

    <!-- Section 2 -->
    <div class="policy-section" id="sec2">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-cogs"></i></div>
        <h2>2. How We Use Your Information</h2>
      </div>
      <p>We use the information we collect for the following purposes:</p>
      <ul>
        <li>To create and manage your alumni profile on our network</li>
        <li>To facilitate connections between SVIS alumni members</li>
        <li>To send event invitations, newsletters, and alumni announcements</li>
        <li>To maintain and improve the functionality of the platform</li>
        <li>To verify your identity and authenticate your login</li>
        <li>To respond to your queries and provide support</li>
        <li>To ensure platform security and prevent fraudulent activity</li>
        <li>To comply with applicable legal obligations</li>
      </ul>
      <div class="info-box">
        <i class="fas fa-envelope"></i>
        We will only send you communications relevant to the SVIS Alumni Network. You may opt out of non-essential communications at any time.
      </div>
    </div>

    <!-- Section 3 -->
    <div class="policy-section" id="sec3">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-share-alt"></i></div>
        <h2>3. Information Sharing &amp; Disclosure</h2>
      </div>
      <p>We respect your privacy and do <strong>not sell, rent, or trade</strong> your personal information to third parties. Your information may only be shared in the following limited circumstances:</p>
      <ul>
        <li><strong>Within the Alumni Network:</strong> Your profile information (name, batch year, profession, city) may be visible to other verified SVIS alumni members as part of the directory.</li>
        <li><strong>School Administration:</strong> SVIS school management may access aggregated alumni data for institutional planning and outreach.</li>
        <li><strong>Service Providers:</strong> Trusted technical partners who assist in hosting, email delivery, or platform maintenance — bound by strict confidentiality agreements.</li>
        <li><strong>Legal Obligations:</strong> If required by law, court order, or government authority.</li>
        <li><strong>Safety Concerns:</strong> To protect the rights, property, or safety of SVIS, its alumni, or the public.</li>
      </ul>
    </div>

    <!-- Section 4 -->
    <div class="policy-section" id="sec4">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-lock"></i></div>
        <h2>4. Data Security</h2>
      </div>
      <p>We take the security of your personal data seriously and implement appropriate technical and organisational measures to protect it from unauthorized access, alteration, disclosure, or destruction.</p>
      <ul>
        <li>Passwords are stored using industry-standard encryption</li>
        <li>Our platform uses HTTPS/SSL encryption for data transmission</li>
        <li>Access to your personal data is restricted to authorised personnel only</li>
        <li>We regularly review and update our security practices</li>
      </ul>
      <div class="info-box">
        <i class="fas fa-exclamation-triangle"></i>
        While we strive to use commercially acceptable means to protect your information, no method of transmission over the Internet is 100% secure. We encourage you to use a strong, unique password for your account.
      </div>
    </div>

    <!-- Section 5 -->
    <div class="policy-section" id="sec5">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-user-check"></i></div>
        <h2>5. Your Rights &amp; Choices</h2>
      </div>
      <p>As a registered member of the SVIS Alumni Network, you have the following rights regarding your personal data:</p>
      <ul>
        <li><strong>Access:</strong> Request a copy of the personal information we hold about you.</li>
        <li><strong>Correction:</strong> Update or correct inaccurate or incomplete information in your profile.</li>
        <li><strong>Deletion:</strong> Request removal of your account and associated data from our platform.</li>
        <li><strong>Opt-Out:</strong> Unsubscribe from marketing communications and event notifications at any time.</li>
        <li><strong>Visibility Control:</strong> Manage which profile details are visible to other alumni members.</li>
        <li><strong>Data Portability:</strong> Request your data in a portable format where technically feasible.</li>
      </ul>
      <p>To exercise any of these rights, please contact us at <a href="mailto:info@svishyd.edu.in">info@svishyd.edu.in</a>.</p>
    </div>

    <!-- Section 6 -->
    <div class="policy-section" id="sec6">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-cookie-bite"></i></div>
        <h2>6. Cookies &amp; Tracking Technologies</h2>
      </div>
      <p>Our website uses cookies and similar tracking technologies to enhance your browsing experience and analyse website traffic. Cookies are small text files stored on your device by your browser.</p>
      <p>We use the following types of cookies:</p>
      <ul>
        <li><strong>Essential Cookies:</strong> Required for the website to function correctly (e.g., login sessions).</li>
        <li><strong>Analytical Cookies:</strong> Help us understand how visitors interact with our website (e.g., Google Analytics).</li>
        <li><strong>Preference Cookies:</strong> Remember your settings and preferences for a better experience.</li>
      </ul>
      <p>You can manage or disable cookies through your browser settings. Note that disabling certain cookies may affect the functionality of the platform.</p>
    </div>

    <!-- Section 7 -->
    <div class="policy-section" id="sec7">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-child"></i></div>
        <h2>7. Children's Privacy</h2>
      </div>
      <p>The SVIS Alumni Network is intended for use by <strong>former students who have passed out from SVIS</strong>. Registration requires users to be at least 15 years of age or older. We do not knowingly collect personal information from children under the age of 13.</p>
      <p>If you are a parent or guardian and believe your child has provided us with personal information without your consent, please contact us immediately at <a href="mailto:info@svishyd.edu.in">info@svishyd.edu.in</a> and we will take steps to remove such information.</p>
    </div>

    <!-- Section 8 -->
    <div class="policy-section" id="sec8">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-external-link-alt"></i></div>
        <h2>8. Third-Party Links</h2>
      </div>
      <p>Our website may contain links to third-party websites, such as the official SVIS school website (<a href="https://www.svishyd.edu.in" target="_blank">svishyd.edu.in</a>), social media platforms (Facebook, Instagram, YouTube), or external resources.</p>
      <p>We are not responsible for the privacy practices or content of those third-party websites. We encourage you to review the privacy policies of any external sites you visit.</p>
    </div>

    <!-- Section 9 -->
    <div class="policy-section" id="sec9">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-sync-alt"></i></div>
        <h2>9. Changes to This Privacy Policy</h2>
      </div>
      <p>We may update this Privacy Policy from time to time to reflect changes in our practices, legal requirements, or platform features. When we make significant changes, we will:</p>
      <ul>
        <li>Update the "Last Updated" date at the top of this page</li>
        <li>Notify registered alumni via email where appropriate</li>
        <li>Post a notice on the SVIS Alumni Network homepage</li>
      </ul>
      <p>We encourage you to review this policy periodically. Your continued use of the platform after changes are posted constitutes your acceptance of the updated policy.</p>
    </div>

    <!-- Section 10 -->
    <div class="policy-section" id="sec10">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-envelope"></i></div>
        <h2>10. Contact Us</h2>
      </div>
      <p>If you have any questions, concerns, or requests regarding this Privacy Policy or your personal data, please reach out to us:</p>
      <ul>
        <li><strong>School:</strong> Sadhu Vaswani International School, Kompally, Hyderabad</li>
        <li><strong>Address:</strong> 150-152 Jayabheri Park, Behind Cine Planet Multiplex, Kompally, Hyderabad – 500100, Telangana</li>
        <li><strong>Phone:</strong> 040-23005000</li>
        <li><strong>Email:</strong> <a href="mailto:info@svishyd.edu.in">info@svishyd.edu.in</a></li>
        <li><strong>Office Hours:</strong> Mon–Fri: 8:15 AM – 3:15 PM | Saturday: 8:15 AM – 12:30 PM</li>
      </ul>
    </div>

    <!-- Contact CTA -->
    <!-- <div class="contact-policy-card">
      <h3>Still have questions?</h3>
      <p>Our team is here to help you with any privacy-related concerns.</p>
      <a href="mailto:info@svishyd.edu.in"><i class="fas fa-envelope" style="margin-right:0.4rem;"></i> Email Us</a>
    </div> -->

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
        <li><a  href="privacy-policy.php" class="footer-link active" class="<?= (basename($_SERVER['PHP_SELF']) == 'privacy-policy.php') ? 'active' : '' ?>">Privacy Policy</a></li>
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

  document.addEventListener('DOMContentLoaded', () => {
    initCustomSelects();
    handleLoginAJAX('login-form');
    handleRegisterAJAX('register-form');
  });

</script>

</body>
</html>