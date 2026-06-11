<?php
include('db_connect.php');
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SVIS Alumni Directory | Reconnect with Your Classmates</title>
    <meta name="description" content="Search the SVIS Alumni Directory to find former classmates, professional peers, and mentors. Build your network and stay connected with the SVIS community.">
    <link rel="icon" type="image/png" href="Logo/FavIcon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="shared.css"/>
    <style>
      #profile-bio {
        white-space: pre-wrap;
        max-height: 180px;
        overflow-y: auto;
      }
      .hidden { display: none !important; }

      .filter-bar {
        background: #fff;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
        margin-bottom: 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.25rem;
      }
      .filter-bar label {
        display: block;
        font-size: 0.85rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.4rem;
      }
      .filter-bar input,
      .filter-bar select {
        width: 100%;
        padding: 0.6rem 0.85rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.9rem;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-family: "Inter", sans-serif;
        background: #fff;
      }
      .filter-bar input:focus,
      .filter-bar select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
      }
      .results-count {
        color: #6b7280;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
      }
      .dir-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        align-items: stretch !important;
        gap: 2rem;
        margin-top: 1.5rem;
      }
      .dir-card {
        height: 100% !important;
      }
      .pm-hdr {
        height: 180px;
        background: linear-gradient(135deg, var(--blue), #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
      }
      .pm-av {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--blue);
      }
      .pm-av img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .pm-body {
        padding: 1.5rem;
      }
      .pm-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
      }
      .pm-batch {
        color: var(--blue);
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 1.25rem;
      }
      .pm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem 1.5rem;
        margin-bottom: 1.5rem;
      }
      .pm-field {
        font-size: 0.85rem;
        color: #374151;
      }
      .pm-field strong {
        color: #111827;
      }
      .pm-socials {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.25rem;
      }
      .pm-socials a {
        font-size: 1.3rem;
        transition: opacity 0.2s;
      }
      .pm-socials a:hover {
        opacity: 0.7;
      }
      .pm-bio-lbl {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
      }
      .pm-bio {
        color: #6b7280;
        font-size: 0.85rem;
        white-space: pre-wrap;
        max-height: 150px;
        overflow-y: auto;
        line-height: 1.6;
      }
      .modal-box.directory-profile { max-width: 600px; }

      @media (max-width: 1300px) {
        .nav-links { display: none; }
        .hamburger-btn { display: flex !important; }
      }

      @media (max-width: 768px) {
        .pm-hdr { height: 150px; }
        .pm-av { width: 90px; height: 90px; font-size: 2rem; }
        .pm-body { padding: 1.25rem; }
        .pm-grid { grid-template-columns: 1fr; gap: 0.5rem; }
        .dir-grid { grid-template-columns: repeat(2, 1fr) !important; }
      }

      @media (max-width: 480px) {
        .policy-hero { padding: 8rem 1rem 2.5rem; }
        .filter-bar { padding: 1rem; }
        .dir-grid { gap: 1rem; grid-template-columns: 1fr !important; }
        .page-btn {
          min-width: 34px;
          height: 34px;
          font-size: 0.8rem;
          padding: 0;
        }
        .page-btn.prev-next {
          padding: 0 0.75rem;
        }
      }
    </style>
    <!-- Shared JS -->
  <script src="shared.js"></script>
</head>
  <body>
    <nav class="site-nav">
      <div class="nav-inner">
        <a href="index.php" class="nav-logo">
          <img src="Logo/Logo.svg" alt="SVIS Alumni Alumni Logo"/>
        </a>
        <div class="nav-links">
          <a href="index.php" class="nav-link">Home</a>
          <a href="directory.php" class="nav-link active">Directory</a>
          <a href="event.php" class="nav-link">Events</a>
          <a href="about.php" class="nav-link">About</a>
          <a href="founders.php" class="nav-link">Founders</a>
          <a href="gallery.php" class="nav-link">Gallery</a>
          <a href="videos.php" class="nav-link">Videos</a>
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

    <div class="mobile-menu" id="mobile-menu">
      <a href="index.php" class="nav-link">Home</a>
      <a href="directory.php" class="nav-link active">Directory</a>
      <a href="event.php" class="nav-link">Events</a>
      <a href="about.php" class="nav-link">About</a>
      <a href="founders.php" class="nav-link">Founders</a>
      <a href="gallery.php" class="nav-link">Gallery</a>
      <a href="videos.php" class="nav-link">Videos</a>
      <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
        <a href="profileedit.php" class="nav-link">Profile</a>
        <a href="logout.php" class="nav-login-btn">Logout</a>
      <?php } else { ?>
        <button class="nav-login-btn" onclick="showModal('login')">Login</button>
      <?php } ?>
    </div>

    <!-- ===== HERO ===== -->
    <div class="policy-hero">
        <h1>Alumni Directory</h1>
        <p>Connect with fellow graduates and expand your professional network</p>
    </div>

    <main class="hero-page-wrap">
        <div class="policy-container">

            <!-- Filters -->
            <div class="filter-bar">
                <div>
                  <label>Search by Name or Profession</label>
                  <input
                    id="alumni-search"
                    placeholder="Type to search..."
                    type="text"
                  />
                </div>
                <div>
                  <label>Filter by Batch Year</label>
                  <select
                    id="alumni-batch-filter"
                  >
                    <option value="">All Batches</option>
                    <?php 
                      for ($year = 2000; $year <= date("Y"); $year++) {
                        echo "<option value='$year'>Batch $year</option>";
                      }
                    ?>
                  </select>
                </div>
            </div>

            <?php
            //   $result = $conn->query("SELECT ar.*, c.phonecode FROM alumni_register ar LEFT JOIN countries c ON ar.country = c.id ORDER BY ar.id DESC");
            $result = $conn->query("
    SELECT ar.*, c.phonecode 
    FROM alumni_register ar 
    LEFT JOIN countries c ON ar.country = c.id 
    WHERE ar.verified_status = 1
    ORDER BY ar.id DESC
");
            ?>

            <p id="alumni-results-count" class="results-count">
              Showing <?php echo $result->num_rows; ?> alumni members
            </p>

            <!-- Alumni Cards -->
            <div id="alumni-list-container" class="dir-grid">
              <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                  $fullPhone = ($row['phonecode'] ? '+' . ltrim($row['phonecode'], '+') . ' ' : '') . $row['phone'];
                  $alumnusData = [
                    "name" => $row['full_name'],
                    "email" => $row['email'],
                    "phone" => $fullPhone,
                    "gender" => $row['gender'],
                    "batch" => $row['batch_year'],
                    "profession" => $row['Industry'],
                    "company" => $row['Company / Organization Name'],
                    "city" => $row['City'],
                    "bio" => $row['bio'],
                    "image" => $row['user_image'],
                    "dob" => $row['dob'],
                    "role" => $row['Current Occupation'],
                    "designation" => $row['Designation'],
                    "experience" => $row['Work Experience'],
                    "linkedin" => $row['linkedin'],
                    "instagram" => $row['instagram']
                  ];
                ?>
                <div
                  class="dir-card"
                  data-name="<?php echo strtolower(htmlspecialchars($row['full_name'])); ?>"
                  data-profession="<?php echo strtolower(htmlspecialchars($row['Industry'])); ?>"
                  data-batch="<?php echo htmlspecialchars($row['batch_year']); ?>"
                >
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
                      <img src="uploads/<?php echo rawurlencode($row['user_image']); ?>"
                           alt="<?php echo htmlspecialchars($row['full_name']); ?>"
                           loading="lazy" />
                    <?php else: ?>
                      <span class="dir-card-init"><?php echo strtoupper(substr($row['full_name'], 0, 1)); ?></span>
                    <?php endif; ?>
                  </div>

                  <!-- Card Body -->
                  <div class="dir-card-body">
                    <h3 class="dir-card-name"><?php echo htmlspecialchars($row['full_name']); ?></h3>
                    <div class="dir-card-meta dir-card-batch">
                      <i class="fas fa-graduation-cap"></i>
                      Batch <?php echo htmlspecialchars($row['batch_year']); ?>
                    </div>
                    <?php if (!empty($row['Industry'])): ?>
                      <div class="dir-card-meta dir-card-prof">
                        <i class="fas fa-briefcase"></i>
                        <?php echo htmlspecialchars($row['Industry']); ?>
                      </div>
                    <?php endif; ?>
                    <div class="dir-card-divider"></div>

                    <?php if(isset($_SESSION['alumni_id']) && $_SESSION['alumni_id']!=""){ ?>
                      <button
                        class="view-profile-btn vp-btn"
                        data-alumnus='<?php echo json_encode($alumnusData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>'>
                        <i class="fas fa-user-circle btn-icon"></i>
                        View Profile
                        <i class="fas fa-chevron-right btn-arrow"></i>
                      </button>
                    <?php } ?>
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
              <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div id="pagination-container" class="pagination-wrap"></div>
        </div>
        </div>
    </main>

      <!-- Profile Modal -->
      <div
        id="profile-modal"
       class="modal-overlay modal-hidden"
        onclick="if(event.target===this)hideModal('profile')">
        <div
         class="modal-box directory-profile"
          onclick="event.stopPropagation()">
          <div class="dir-card-hdr" style="border-radius: 16px 16px 0 0; position: relative; overflow: hidden; height: 160px;">
            <div class="dot-pattern">
              <span></span><span></span><span></span><span></span><span></span><span></span>
              <span></span><span></span><span></span><span></span><span></span><span></span>
              <span></span><span></span><span></span><span></span><span></span><span></span>
              <span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
            <div class="circle-deco"></div>
            <svg class="wave-divider" viewBox="0 0 500 40" preserveAspectRatio="none" style="position: absolute; bottom: -1px; left: 0; width: 100%; height: 40px; display: block;">
              <path d="M0,20 C125,45 175,0 250,18 C325,36 375,-5 500,20 L500,40 L0,40 Z" fill="#fff"></path>
            </svg>
            <button class="modal-close" style="top:1rem;right:1rem;z-index:10;color:#fff;" onclick="hideModal('profile')" aria-label="Close">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="pm-body" style="padding-top: 0; text-align: center;">
            <div style="display: flex; justify-content: center; position: relative; z-index: 5;">
              <div id="profile-initial" class="dir-card-av pm-av" style="margin-top: -65px;"></div>
            </div>
            
            <h2 id="profile-name" class="dir-card-name" style="font-size: 1.6rem; margin-bottom: 0.5rem; -webkit-line-clamp: unset; overflow: visible;"></h2>
            <div class="dir-card-meta dir-card-batch" style="display: inline-block; margin-bottom: 1.5rem;">
              <i class="fas fa-graduation-cap"></i> <span id="profile-batch"></span>
            </div>
            
            <div class="dir-card-divider" style="margin: 0 auto 1.5rem auto; width: 100%;"></div>
            
            <div class="pm-grid" style="text-align: left;">
                 <p class="pm-field"><strong>Email:</strong> <span id="profile-email"></span></p>
                 <p class="pm-field"><strong>Phone:</strong> <span id="profile-phone"></span></p>
                 <p class="pm-field"><strong>Gender:</strong> <span id="profile-gender"></span></p>
                 <p class="pm-field"><strong>Date of Birth:</strong> <span id="profile-dob"></span></p>
                 <p class="pm-field"><strong>Current Occupation:</strong> <span id="profile-role"></span></p>
                 <p class="pm-field"><strong>Designation:</strong> <span id="profile-designation"></span></p>
                 <p class="pm-field"><strong>Company / Organization Name:</strong> <span id="profile-company"></span></p>
                 <p class="pm-field"><strong>Industry:</strong> <span id="profile-profession"></span></p>
                 <p class="pm-field"><strong>Work Experience:</strong> <span id="profile-experience"></span> yrs</p>
                 <p class="pm-field"><strong>City:</strong> <span id="profile-city"></span></p>
            </div>

            <!-- Social Links -->
            <div class="pm-socials" id="social-links-container" style="justify-content: center; margin-top: 1rem;">
            </div>

            <div style="text-align: left;">
              <p class="pm-bio-lbl">Bio</p>
              <p id="profile-bio" class="pm-bio"></p>
            </div>
          </div>
        </div>
      </div>

<!-- ===== FOOTER ===== -->
<footer>
  <div class="footer-grid">

    <!-- Quick Links -->
    <div class="footer-col">
      <h3>Quick Links</h3>
      <ul>
        <li><a  href="index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Home</a></li>
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
      <p>Sign in to your SVIS Alumni Alumni account</p>
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
        <div class="form-divider"><span>New to SVIS Alumni Alumni Network?</span></div>
        <div class="form-switch">Don't have an account? <button type="button" onclick="showModal('register')">Register here</button></div>
      </form>
    </div>
  </div>
</div>

<?php include "register_modal_part.php"; ?>

    <script>
      // Pagination State
      let currentPage = 1;
      const itemsPerPage = 12;
      let matchedCards = [];

      // DOM ready
      document.addEventListener("DOMContentLoaded", () => {
        // Filter listeners
        const search = document.getElementById("alumni-search");
        const batch = document.getElementById("alumni-batch-filter");
        if (search) search.addEventListener("keyup", () => { currentPage = 1; filterAlumni(); });
        if (batch) batch.addEventListener("change", () => { currentPage = 1; filterAlumni(); });
        
        filterAlumni();

        // Profile buttons (using event delegation for pagination support)
        document.getElementById("alumni-list-container").addEventListener("click", (e) => {
          const btn = e.target.closest(".view-profile-btn");
          if (btn) {
            const alumnus = JSON.parse(btn.dataset.alumnus);
            showProfile(alumnus);
          }
        });

        const menuBtn = document.getElementById("hamburger-btn");
        const mobileNav = document.getElementById("mobile-menu");
        const hamIcon = document.getElementById("hamburger-icon");
        if (menuBtn && mobileNav && hamIcon) {
          menuBtn.addEventListener("click", (event) => {
            event.stopPropagation();
            const open = mobileNav.classList.toggle("open");
            hamIcon.style.transform = open ? "rotate(90deg)" : "rotate(0)";
          });
          document.addEventListener("click", (event) => {
            if (!menuBtn.contains(event.target) && !mobileNav.contains(event.target)) {
              mobileNav.classList.remove("open");
              hamIcon.style.transform = "rotate(0)";
            }
          });
        }

        document.querySelectorAll(".modal-overlay").forEach((overlay) => {
          overlay.addEventListener("click", (event) => {
            if (event.target === overlay) {
              overlay.classList.remove("modal-visible");
              overlay.classList.add("modal-hidden");
            }
          });
        });

        // Initialize Shared Login AJAX
        handleLoginAJAX('login-form');
        handleRegisterAJAX('register-form');

        // Initialize Custom Selects
        if (typeof initCustomSelects === 'function') {
          initCustomSelects();
        }
      });

      // Modal functions
      function showProfile(alumnus) {
        const container = document.getElementById("profile-initial");
        container.innerHTML = "";

        if (alumnus.image && alumnus.image.trim() !== "") {
          const img = document.createElement("img");
          img.src = "uploads/" + alumnus.image;
          img.alt = alumnus.name + " photo";
          container.appendChild(img);
        } else {
          container.textContent = alumnus.name.charAt(0).toUpperCase();
        }

        document.getElementById("profile-name").textContent = alumnus.name;
        document.getElementById("profile-batch").textContent = "Batch " + alumnus.batch;
        document.getElementById("profile-email").textContent = alumnus.email || "—";
        document.getElementById("profile-phone").textContent = alumnus.phone || "—";
        document.getElementById("profile-gender").textContent = alumnus.gender || "—";
        document.getElementById("profile-dob").textContent = alumnus.dob || "—";
        
        document.getElementById("profile-role").textContent = alumnus.role || "—";
        document.getElementById("profile-designation").textContent = alumnus.designation || "—";
        document.getElementById("profile-experience").textContent = alumnus.experience || "0";
        
        document.getElementById("profile-profession").textContent = alumnus.profession || "—";
        document.getElementById("profile-company").textContent = alumnus.company || "—";
        document.getElementById("profile-city").textContent = alumnus.city || "—";
        document.getElementById("profile-bio").textContent =
          alumnus.bio && alumnus.bio.trim() ? alumnus.bio : "No bio available.";

        // Social Links
        const socialContainer = document.getElementById("social-links-container");
        socialContainer.innerHTML = "";
        
        if (alumnus.linkedin) {
            const a = document.createElement('a');
            a.href = alumnus.linkedin;
            a.target = "_blank";
            a.style.color = "#0077b5";
            a.innerHTML = '<i class="fab fa-linkedin"></i>';
            socialContainer.appendChild(a);
        }

        if (alumnus.instagram) {
            const a = document.createElement('a');
            a.href = alumnus.instagram;
            a.target = "_blank";
            a.style.color = "#E1306C";
            a.innerHTML = '<i class="fab fa-instagram"></i>';
            socialContainer.appendChild(a);
        }

        showModal("profile");
      }

      function showModal(type) {
        const modal = document.getElementById(type + "-modal");
        if (!modal) return;
        modal.classList.remove("modal-hidden");
        modal.classList.add("modal-visible");
        document.body.style.overflow = "hidden";
        if (type === "register") hideModal("login");
        if (type === "login") hideModal("register");
      }

      function hideModal(type) {
        const modal = document.getElementById(type + "-modal");
        if (!modal) return;
        modal.classList.remove("modal-visible");
        modal.classList.add("modal-hidden");
        if (!document.querySelector(".modal-visible")) {
          document.body.style.overflow = "";
        }
      }

      function togglePassword(id) {
        const input = document.getElementById(id);
        if (!input) return;
        input.type = input.type === "password" ? "text" : "password";
      }

      function filterAlumni() {
        const search = (document.getElementById("alumni-search")?.value || "").toLowerCase();
        const batch = document.getElementById("alumni-batch-filter")?.value || "";
        const cards = document.querySelectorAll("#alumni-list-container .dir-card");
        const resultsCount = document.getElementById("alumni-results-count");

        matchedCards = [];
        cards.forEach((card) => {
          const name = card.dataset.name || "";
          const profession = card.dataset.profession || "";
          const batchYear = card.dataset.batch || "";
          const matchesSearch = name.includes(search) || profession.includes(search);
          const matchesBatch = batch === "" || batchYear === batch;

          if (matchesSearch && matchesBatch) {
            matchedCards.push(card);
          } else {
            card.style.display = "none";
          }
        });

        const totalMatched = matchedCards.length;
        const totalPages = Math.ceil(totalMatched / itemsPerPage);
        
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * itemsPerPage;
        const endIdx = startIdx + itemsPerPage;

        matchedCards.forEach((card, idx) => {
          if (idx >= startIdx && idx < endIdx) {
            card.style.display = "";
          } else {
            card.style.display = "none";
          }
        });

        resultsCount.textContent =
          totalMatched === 1 ? "Showing 1 alumni member" : `Showing ${totalMatched} alumni members`;
          
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
        prevBtn.onclick = () => { currentPage--; filterAlumni(); window.scrollTo({top: 400, behavior: 'smooth'}); };
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
          btn.onclick = () => { currentPage = i; filterAlumni(); window.scrollTo({top: 400, behavior: 'smooth'}); };
          container.appendChild(btn);
        }

        // Next Button
        const nextBtn = document.createElement("button");
        nextBtn.className = "page-btn prev-next";
        nextBtn.innerHTML = isMobile ? '<i class="fas fa-chevron-right"></i>' : 'Next <i class="fas fa-chevron-right"></i>';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => { currentPage++; filterAlumni(); window.scrollTo({top: 400, behavior: 'smooth'}); };
        container.appendChild(nextBtn);
      }
    </script>
  </body>
</html>
