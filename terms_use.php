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
  <title>Terms of Use | SVIS Alumni Portal</title>
  <meta name="description" content="The terms and conditions for using the SVIS Alumni Network. Guidelines for membership, conduct, and platform usage.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    :root {
      --blue:       #1D4ED8;
      --blue-dark:  #1e3a8a;
      --blue-light: #EFF6FF;
      --blue-mid:   #BFDBFE;
      --gold:       #fbbf24;
      --text:       #1f2937;
      --muted:      #6b7280;
    }

    /* Styles moved to shared.css */

    /* ── AGREEMENT BOX ── */
    .agreement-box {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue));
      border-radius: 14px; padding: 1.5rem 2rem; margin-bottom: 2rem;
      display: flex; align-items: flex-start; gap: 1rem; color: #fff;
    }
    .agreement-box .ag-icon { font-size: 1.5rem; color: var(--gold); flex-shrink: 0; margin-top: 0.1rem; }
    .agreement-box p { font-family: 'Poppins', sans-serif; font-size: 0.855rem; line-height: 1.75; color: var(--blue-mid); }
    .agreement-box strong { color: #fff; }

    /* ── TOC ── */
    .toc-card {
      background: #fff; border-radius: 14px;
      border: 1px solid var(--blue-mid); border-left: 5px solid var(--blue);
      padding: 1.5rem 2rem; margin-bottom: 2.5rem;
      box-shadow: 0 2px 12px rgba(29,78,216,0.07);
    }
    .toc-card h2 { font-family: 'Poppins', sans-serif; font-size: 0.85rem; font-weight: 700; color: var(--blue-dark); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.85rem; }
    .toc-card ol { padding-left: 1.2rem; columns: 2; column-gap: 2rem; }
    @media (max-width: 560px) { .toc-card ol { columns: 1; } }
    .toc-card li { margin-bottom: 0.35rem; }
    .toc-card li a { font-family: 'Poppins', sans-serif; font-size: 0.84rem; color: var(--blue); text-decoration: none; transition: color 0.2s; }
    .toc-card li a:hover { color: var(--blue-dark); text-decoration: underline; }

    /* ── SECTION BLOCKS ── */
    .policy-section {
      background: #fff; border-radius: 14px;
      border: 1px solid #e5eeff; padding: 2rem 2.2rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 10px rgba(29,78,216,0.05);
      scroll-margin-top: 90px;
    }
    .policy-section-header { display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1.1rem; }
    .sec-icon {
      width: 40px; height: 40px; flex-shrink: 0; border-radius: 10px;
      background: linear-gradient(135deg, var(--blue-dark), var(--blue));
      display: flex; align-items: center; justify-content: center;
      font-size: 0.95rem; color: #fff;
    }
    .policy-section h2 { font-family: 'Lato', serif; font-size: 1.15rem; color: var(--blue-dark); font-weight: 700; }
    .policy-section p, .policy-section li { font-family: 'Poppins', sans-serif; font-size: 0.875rem; color: var(--text); line-height: 1.85; }
    .policy-section p + p { margin-top: 0.7rem; }
    .policy-section ul, .policy-section ol { padding-left: 1.4rem; margin-top: 0.5rem; margin-bottom: 0.5rem; }
    .policy-section li { margin-bottom: 0.4rem; }
    .policy-section strong { color: var(--blue-dark); font-weight: 600; }
    .policy-section a { color: var(--blue); font-weight: 600; }
    .policy-section a:hover { text-decoration: underline; }

    .warn-box {
      background: #fff7ed; border-left: 4px solid #f59e0b;
      border-radius: 0 8px 8px 0; padding: 0.85rem 1.1rem; margin: 0.85rem 0;
      font-family: 'Poppins', sans-serif; font-size: 0.85rem; color: #92400e; line-height: 1.7;
    }
    .warn-box i { margin-right: 0.45rem; color: #f59e0b; }

    .info-box {
      background: var(--blue-light); border-left: 4px solid var(--blue);
      border-radius: 0 8px 8px 0; padding: 0.85rem 1.1rem; margin: 0.85rem 0;
      font-family: 'Poppins', sans-serif; font-size: 0.85rem; color: var(--blue-dark); line-height: 1.7;
    }
    .info-box i { margin-right: 0.45rem; color: var(--blue); }

    .rule-grid { display: grid; gap: 0.85rem; margin-top: 0.75rem; }
    .rule-item { display: flex; align-items: flex-start; gap: 0.8rem; background: var(--blue-light); border-radius: 10px; padding: 0.85rem 1rem; }
    .rule-num {
      width: 28px; height: 28px; flex-shrink: 0;
      background: var(--blue); color: #fff; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Poppins', sans-serif; font-size: 0.78rem; font-weight: 700;
    }
    .rule-item p { font-family: 'Poppins', sans-serif; font-size: 0.855rem; color: var(--blue-dark); line-height: 1.7; margin: 0; }

    .contact-policy-card {
      background: linear-gradient(135deg, var(--blue-dark), var(--blue));
      border-radius: 14px; padding: 2rem 2.2rem; margin-top: 2rem;
      color: #fff; text-align: center;
    }
    .contact-policy-card h3 { font-family: 'Lato', serif; font-size: 1.2rem; margin-bottom: 0.5rem; }
    .contact-policy-card p { font-family: 'Poppins', sans-serif; font-size: 0.85rem; color: var(--blue-mid); margin-bottom: 1rem; }
    .contact-policy-card a {
      display: inline-block; background: var(--gold); color: var(--blue-dark);
      font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 0.88rem;
      padding: 0.6rem 1.8rem; border-radius: 999px; text-decoration: none;
      transition: opacity 0.2s, transform 0.2s;
    }
    .contact-policy-card a:hover { opacity: 0.9; transform: scale(1.03); }

    .effective-bar {
      background: var(--blue-light); border: 1px solid var(--blue-mid); border-radius: 10px;
      padding: 0.7rem 1.2rem; display: flex; align-items: center; gap: 0.6rem;
      margin-bottom: 2rem; font-family: 'Poppins', sans-serif; font-size: 0.82rem; color: var(--blue-dark);
    }
    .effective-bar i { color: var(--blue); }

    .related-strip {
      background: #fff; border: 1px solid var(--blue-mid); border-radius: 12px;
      padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem;
    }
    .related-strip span { font-family: 'Poppins', sans-serif; font-size: 0.85rem; color: var(--muted); }
    .related-strip a {
      background: var(--blue-light); color: var(--blue);
      font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 0.84rem;
      padding: 0.45rem 1.1rem; border-radius: 999px; text-decoration: none;
      border: 1px solid var(--blue-mid); transition: background 0.2s;
    }
    .related-strip a:hover { background: var(--blue-mid); }
  </style>
</head>
<body>

<!-- ===== NAV ===== -->
<nav class="site-nav">
  <div class="nav-inner">
    <a href="index.php" class="nav-logo"><img src="Logo/Logo.svg" alt="SVIS Logo"/></a>
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
  <h1>Terms &amp; Conditions</h1>
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

    <!-- Agreement box -->
    <div class="agreement-box">
      <div class="ag-icon"><i class="fas fa-handshake"></i></div>
      <p><strong>Please read these Terms &amp; Conditions carefully before using the SVIS Alumni Network.</strong> By registering, logging in, or using any part of this platform, you confirm that you have read, understood, and agree to be bound by these terms. If you do not agree, please do not use this platform.</p>
    </div>

    <!-- TOC -->
    <div class="toc-card">
      <h2><i class="fas fa-list" style="margin-right:0.4rem;"></i> Table of Contents</h2>
      <ol>
        <li><a href="#t1">Acceptance of Terms</a></li>
        <li><a href="#t2">Eligibility</a></li>
        <li><a href="#t3">Account Registration</a></li>
        <li><a href="#t4">Acceptable Use Policy</a></li>
        <li><a href="#t5">Content &amp; Intellectual Property</a></li>
        <li><a href="#t6">Privacy</a></li>
        <li><a href="#t7">Alumni Directory</a></li>
        <li><a href="#t8">Events &amp; Activities</a></li>
        <li><a href="#t9">Disclaimers</a></li>
        <li><a href="#t10">Limitation of Liability</a></li>
        <li><a href="#t11">Account Termination</a></li>
        <li><a href="#t12">Governing Law</a></li>
        <li><a href="#t13">Changes to Terms</a></li>
        <li><a href="#t14">Contact Us</a></li>
      </ol>
    </div>

    <!-- Section 1 -->
    <div class="policy-section" id="t1">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-check-circle"></i></div>
        <h2>1. Acceptance of Terms</h2>
      </div>
      <p>These Terms &amp; Conditions ("Terms") govern your access to and use of the SVIS Alumni Network ("Platform"), operated by Sadhu Vaswani International School ("SVIS", "we", "us", "our"), located at 150-152 Jayabheri Park, Kompally, Hyderabad – 500100, Telangana, India.</p>
      <p>By accessing this Platform, you acknowledge that you have read, understood, and agree to be legally bound by these Terms and our <a href="privacy-policy.php">Privacy Policy</a>. These Terms constitute a binding agreement between you and SVIS.</p>
    </div>

    <!-- Section 2 -->
    <div class="policy-section" id="t2">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-id-badge"></i></div>
        <h2>2. Eligibility</h2>
      </div>
      <p>To register and use the SVIS Alumni Network, you must meet the following eligibility criteria:</p>
      <div class="rule-grid">
        <div class="rule-item"><div class="rule-num">1</div><p>You must be a <strong>former student of Sadhu Vaswani International School</strong>, Kompally, Hyderabad, who has completed their studies at SVIS.</p></div>
        <div class="rule-item"><div class="rule-num">2</div><p>You must be at least <strong>15 years of age</strong> at the time of registration.</p></div>
        <div class="rule-item"><div class="rule-num">3</div><p>You must provide <strong>accurate and truthful information</strong> during the registration process.</p></div>
        <div class="rule-item"><div class="rule-num">4</div><p>You must <strong>not have been previously banned</strong> or removed from the Platform for violations.</p></div>
      </div>
      <div class="info-box" style="margin-top:1rem;">
        <i class="fas fa-info-circle"></i>
        SVIS reserves the right to verify your alumni status and may request additional information for verification purposes. Unverified or fraudulent accounts may be removed without notice.
      </div>
    </div>

    <!-- Section 3 -->
    <div class="policy-section" id="t3">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-user-plus"></i></div>
        <h2>3. Account Registration &amp; Security</h2>
      </div>
      <p>When you create an account on the SVIS Alumni Network, you agree to:</p>
      <ul>
        <li>Provide accurate, complete, and up-to-date information during registration</li>
        <li>Maintain the confidentiality of your account password at all times</li>
        <li>Take full responsibility for all activity that occurs under your account</li>
        <li>Notify us immediately at <a href="mailto:info@svishyd.edu.in">info@svishyd.edu.in</a> if you suspect any unauthorised use of your account</li>
        <li>Not share your account credentials with any other person</li>
        <li>Use only one account per person — multiple accounts are not permitted</li>
      </ul>
      <p>SVIS will not be liable for any loss or damage resulting from your failure to maintain the security of your account credentials.</p>
    </div>

    <!-- Section 4 -->
    <div class="policy-section" id="t4">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-gavel"></i></div>
        <h2>4. Acceptable Use Policy</h2>
      </div>
      <p>The SVIS Alumni Network is a community platform dedicated to connecting former students of SVIS in a respectful and constructive manner. You agree <strong>not to</strong> use this Platform to:</p>
      <ul>
        <li>Post false, misleading, defamatory, or fraudulent content about any individual or organisation</li>
        <li>Harass, bully, threaten, or intimidate any other member of the alumni community</li>
        <li>Share obscene, offensive, or sexually explicit material</li>
        <li>Promote any political agenda, religious extremism, or hate speech</li>
        <li>Engage in unsolicited commercial advertising or spam</li>
        <li>Impersonate any person, including SVIS staff or other alumni</li>
        <li>Attempt to gain unauthorised access to any part of the Platform or its systems</li>
        <li>Upload or distribute viruses, malware, or any harmful code</li>
        <li>Violate any applicable local, national, or international law or regulation</li>
        <li>Use the alumni directory or member information for commercial solicitation without authorisation</li>
      </ul>
      <div class="warn-box">
        <i class="fas fa-exclamation-triangle"></i>
        Violation of the Acceptable Use Policy may result in immediate suspension or permanent termination of your account, and in serious cases, may be reported to the relevant authorities.
      </div>
    </div>

    <!-- Section 5 -->
    <div class="policy-section" id="t5">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-copyright"></i></div>
        <h2>5. Content &amp; Intellectual Property</h2>
      </div>
      <p><strong>Platform Content:</strong> All content on the SVIS Alumni Network — including text, logos, photographs, graphics, event information, and design elements — is the intellectual property of SVIS or its licensors. You may not reproduce, distribute, or commercially exploit any platform content without prior written permission from SVIS.</p>
      <p><strong>User-Submitted Content:</strong> When you upload photographs, profile information, or any other content to the Platform, you grant SVIS a non-exclusive, royalty-free licence to display, share, and use that content within the SVIS Alumni Network for community purposes.</p>
      <p><strong>Your Responsibility:</strong> You are solely responsible for the content you submit. You warrant that you own the rights to any content you upload and that it does not infringe the intellectual property rights of any third party.</p>
    </div>

    <!-- Section 6 -->
    <div class="policy-section" id="t6">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-shield-alt"></i></div>
        <h2>6. Privacy</h2>
      </div>
      <p>Your use of the SVIS Alumni Network is also governed by our <a href="privacy-policy.php">Privacy Policy</a>, which is incorporated into these Terms by reference. By using this Platform, you consent to the collection, use, and storage of your personal information as described in our Privacy Policy.</p>
      <p>We are committed to protecting your personal data and will not sell or share it with third parties for commercial purposes. Please review our Privacy Policy for full details on how your information is handled.</p>
    </div>

    <!-- Section 7 -->
    <div class="policy-section" id="t7">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-address-book"></i></div>
        <h2>7. Alumni Directory</h2>
      </div>
      <p>The Alumni Directory is a feature that allows SVIS graduates to connect with one another. By registering and creating a profile, you understand and agree that:</p>
      <ul>
        <li>Certain profile information (such as your name, batch year, profession, and city) may be visible to other verified SVIS alumni members</li>
        <li>The directory is intended <strong>solely for alumni networking and reconnection</strong> purposes</li>
        <li>You must not use contact information from the directory for spam, unsolicited marketing, or any commercial purpose without the explicit consent of the individual</li>
        <li>You can manage the visibility of your profile details through your account settings</li>
        <li>SVIS reserves the right to remove or restrict directory profiles that violate these Terms</li>
      </ul>
    </div>

    <!-- Section 8 -->
    <div class="policy-section" id="t8">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-calendar-alt"></i></div>
        <h2>8. Events &amp; Activities</h2>
      </div>
      <p>The SVIS Alumni Network may organise and promote alumni events, reunions, mentorship sessions, and other activities. By participating in such events:</p>
      <ul>
        <li>You agree to conduct yourself in a respectful and dignified manner that reflects the values of SVIS</li>
        <li>You consent to photographs or videos being taken at events for use on the Platform and SVIS's social media channels</li>
        <li>Event details, dates, and schedules are subject to change — SVIS will endeavour to notify registered participants in advance</li>
        <li>SVIS is not responsible for any personal loss, injury, or damages incurred during alumni events</li>
      </ul>
    </div>

    <!-- Section 9 -->
    <div class="policy-section" id="t9">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-exclamation-circle"></i></div>
        <h2>9. Disclaimers</h2>
      </div>
      <p>The SVIS Alumni Network Platform is provided on an <strong>"as is" and "as available" basis</strong>. SVIS makes no warranties, express or implied, regarding:</p>
      <ul>
        <li>The accuracy, completeness, or reliability of any content on the Platform</li>
        <li>The uninterrupted or error-free operation of the Platform</li>
        <li>The accuracy of alumni profiles or information submitted by members</li>
        <li>The suitability of the Platform for any particular purpose</li>
      </ul>
      <p>SVIS does not endorse or verify the accuracy of information provided by alumni members in their profiles or communications.</p>
    </div>

    <!-- Section 10 -->
    <div class="policy-section" id="t10">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-balance-scale"></i></div>
        <h2>10. Limitation of Liability</h2>
      </div>
      <p>To the fullest extent permitted by applicable law, SVIS and its administrators, staff, and partners shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising out of or related to your use of the Platform, including but not limited to:</p>
      <ul>
        <li>Loss of data or personal information</li>
        <li>Unauthorised access to your account</li>
        <li>Any actions or communications of other alumni members</li>
        <li>Technical failures, downtime, or interruptions to the Platform</li>
        <li>Reliance on information provided by other users on the Platform</li>
      </ul>
    </div>

    <!-- Section 11 -->
    <div class="policy-section" id="t11">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-user-slash"></i></div>
        <h2>11. Account Suspension &amp; Termination</h2>
      </div>
      <p>SVIS reserves the right to suspend, restrict, or permanently terminate your account at any time, with or without notice, for the following reasons:</p>
      <ul>
        <li>Violation of these Terms &amp; Conditions or the Acceptable Use Policy</li>
        <li>Providing false or misleading registration information</li>
        <li>Conduct that is harmful, offensive, or disruptive to the alumni community</li>
        <li>Inactivity over an extended period, subject to prior notice</li>
        <li>Any other reason that SVIS deems to be in the best interests of the platform and its members</li>
      </ul>
      <p>You may also request deletion of your own account at any time by contacting us at <a href="mailto:info@svishyd.edu.in">info@svishyd.edu.in</a>.</p>
      <div class="info-box">
        <i class="fas fa-info-circle"></i>
        Upon account termination, your profile will be removed from the alumni directory. Some records may be retained for a limited period to comply with legal obligations.
      </div>
    </div>

    <!-- Section 12 -->
    <div class="policy-section" id="t12">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-landmark"></i></div>
        <h2>12. Governing Law &amp; Jurisdiction</h2>
      </div>
      <p>These Terms &amp; Conditions shall be governed by and construed in accordance with the laws of <strong>India</strong>, specifically the laws applicable in the state of <strong>Telangana</strong>.</p>
      <p>Any disputes arising out of or in connection with these Terms shall be subject to the exclusive jurisdiction of the courts located in <strong>Hyderabad, Telangana, India</strong>. By using this Platform, you consent to the personal jurisdiction of such courts.</p>
    </div>

    <!-- Section 13 -->
    <div class="policy-section" id="t13">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-sync-alt"></i></div>
        <h2>13. Changes to These Terms</h2>
      </div>
      <p>SVIS reserves the right to modify or update these Terms &amp; Conditions at any time to reflect changes in the Platform, legal requirements, or our policies. When changes are made, we will:</p>
      <ul>
        <li>Update the "Last Updated" date at the top of this page</li>
        <li>Notify registered alumni via email for significant changes</li>
        <li>Post a notice on the Platform homepage</li>
      </ul>
      <p>Your continued use of the Platform after updated Terms are posted constitutes your acceptance of those changes. We encourage you to review this page periodically.</p>
    </div>

    <!-- Section 14 -->
    <div class="policy-section" id="t14">
      <div class="policy-section-header">
        <div class="sec-icon"><i class="fas fa-envelope"></i></div>
        <h2>14. Contact Us</h2>
      </div>
      <p>If you have any questions, concerns, or feedback about these Terms &amp; Conditions, please contact us:</p>
      <ul>
        <li><strong>School:</strong> Sadhu Vaswani International School, Kompally, Hyderabad</li>
        <li><strong>Address:</strong> 150-152 Jayabheri Park, Behind Cine Planet Multiplex, Kompally, Hyderabad – 500100, Telangana</li>
        <li><strong>Phone:</strong> 040-23005000</li>
        <li><strong>Email:</strong> <a href="mailto:info@svishyd.edu.in">info@svishyd.edu.in</a></li>
        <li><strong>Office Hours:</strong> Mon–Fri: 8:15 AM – 3:15 PM | Saturday: 8:15 AM – 12:30 PM</li>
      </ul>
    </div>

    <!-- Related link -->
    <div class="related-strip">
      <span><i class="fas fa-link" style="margin-right:0.4rem;"></i> Also read our Privacy Policy</span>
      <a href="privacy-policy.php"><i class="fas fa-shield-alt" style="margin-right:0.4rem;"></i> Privacy Policy</a>
    </div>

    <!-- CTA -->
    <!-- <div class="contact-policy-card">
      <h3>Questions about our Terms?</h3>
      <p>Reach out to the SVIS Alumni team — we're happy to help.</p>
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