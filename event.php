<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db_connect.php'); 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$alumni_details = null;
if (isset($_SESSION['alumni_id'])) {
    $alumni_id = $_SESSION['alumni_id'];
    $stmt = $conn->prepare("SELECT id, full_name, email, phone, batch_year FROM alumni_register WHERE id = ?");
    $stmt->bind_param("i", $alumni_id);
    $stmt->execute();
    $alumni_details = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<?php
// ─── Handle Form POSTS ──────────────────────────────────────────────────────
$toast_message = '';
$toast_type    = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {

    // 1. EVENT REQUEST (Proposing a new event)
    if ($_POST['form_type'] === 'event_request') {
        $full_name       = $conn->real_escape_string(trim($_POST['full_name']));
        $phone           = $conn->real_escape_string(trim($_POST['phone']));
        $venue           = isset($_POST['venue']) ? $conn->real_escape_string(trim($_POST['venue'])) : '';
        
        $preferred_date  = $_POST['preferred_date'];
        $preferred_time  = $_POST['preferred_time'];
        $start_datetime  = $conn->real_escape_string($preferred_date . ' ' . $preferred_time);
        $end_datetime    = $start_datetime;
        
        $alumni_id = isset($_POST['alumni_id']) ? $conn->real_escape_string(trim($_POST['alumni_id'])) : '';
        $batch_year = isset($_POST['batch_year']) ? $conn->real_escape_string(trim($_POST['batch_year'])) : '';
        $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
        $event_title = isset($_POST['event_title']) ? $conn->real_escape_string(trim($_POST['event_title'])) : '';
        $event_category = isset($_POST['event_category']) ? $conn->real_escape_string(trim($_POST['event_category'])) : '';
        $event_description = isset($_POST['event_description']) ? $conn->real_escape_string(trim($_POST['event_description'])) : '';
        $purpose = isset($_POST['purpose']) ? $conn->real_escape_string(trim($_POST['purpose'])) : '';
        $alternate_date = !empty($_POST['alternate_date']) ? $conn->real_escape_string(trim($_POST['alternate_date'])) : '';
        $event_duration = isset($_POST['event_duration']) ? $conn->real_escape_string(trim($_POST['event_duration'])) : '';
        $event_mode = isset($_POST['event_mode']) ? $conn->real_escape_string(trim($_POST['event_mode'])) : '';
        $online_platform = isset($_POST['online_platform']) ? $conn->real_escape_string(trim($_POST['online_platform'])) : '';
        $expected_participants = isset($_POST['expected_participants']) ? $conn->real_escape_string(trim($_POST['expected_participants'])) : '';

        $request_message = $event_description;

        if (!empty($preferred_date) && !empty($preferred_time)) {
            $alternate_date_val = !empty($alternate_date) ? "'$alternate_date'" : "NULL";
            $insertQuery = "INSERT INTO event_requests 
                            (full_name, phone, venue, start_datetime, end_datetime, request_message, alumni_id, batch_year, email, event_title, event_category, event_description, purpose, alternate_date, event_duration, event_mode, online_platform, expected_participants)
                            VALUES ('$full_name', '$phone', '$venue', '$start_datetime', '$end_datetime', '$request_message', '$alumni_id', '$batch_year', '$email', '$event_title', '$event_category', '$event_description', '$purpose', $alternate_date_val, '$event_duration', '$event_mode', '$online_platform', '$expected_participants')";

            if (mysqli_query($conn, $insertQuery)) {
                $toast_message = 'Event request submitted successfully!';
                $toast_type    = 'success';
            } else {
                $toast_message = 'Database error: ' . mysqli_error($conn);
                $toast_type    = 'error';
            }
        } else {
            $toast_message = 'Please fill in Preferred Date & Time fields.';
            $toast_type    = 'error';
        }
    }

    // 2. EVENT REGISTRATION (Attending an existing event)
    if ($_POST['form_type'] === 'event_registration') {
        $alumni_id        = !empty($_POST['alumni_id']) ? intval($_POST['alumni_id']) : null;
        $full_name        = trim($_POST['full_name']);
        $batch_year       = trim($_POST['batch']);
        $email            = trim($_POST['email']);
        $phone            = trim($_POST['phone']);

        // --- EVENT IDENTIFIERS ---
        $event_id         = !empty($_POST['event_id'])   ? intval($_POST['event_id'])   : null;
        $event_name       = trim($_POST['event_name']);

        // --- EVENT DATE & TIME ---
                // --- INPUT EXTRACTION ---
        $event_date_raw   = trim($_POST['event_date']);
        // Use today's date if the field is empty
        $event_date       = $event_date_raw !== '' ? date('Y-m-d', strtotime($event_date_raw))
                                                  : date('Y-m-d');

        $event_time_raw   = trim($_POST['event_time']);
        // Convert to MySQL TIME format (HH:MM:SS)
        $event_time       = date('H:i:s', strtotime($event_time_raw));

        $event_venue      = trim($_POST['event_venue']);
        $event_category   = trim($_POST['event_category']);
        $attendance       = trim($_POST['attendance']);

        $fee_str = str_ireplace(['free', 'rs.', 'inr', '$', ' '], '', $_POST['registration_fee']);
        $registration_fee = (!empty($fee_str) && is_numeric($fee_str)) ? floatval($fee_str) : 0.00;

        $terms_agree      = isset($_POST['terms_agree']) ? 1 : 0;

        // --- CHECK FOR EXISTING REGISTRATION ---
        $check_stmt = $conn->prepare("SELECT id FROM event_registrations WHERE alumni_id = ? AND event_id = ?");
        $check_stmt->bind_param("ii", $alumni_id, $event_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            // Already registered
            $toast_message = 'You are already registered for this event.';
            $toast_type    = 'error';
        } else {
            // --- INSERT NEW REGISTRATION ---
            $stmt = $conn->prepare("INSERT INTO event_registrations 
                (alumni_id, event_id, event_category, attendance, registration_fee, terms_agreed)
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissdi", $alumni_id, $event_id, $event_category, $attendance, $registration_fee, $terms_agree);
            if ($stmt->execute()) {
                $toast_message = 'Successfully registered for ' . htmlspecialchars($event_name) . '!';
                $toast_type    = 'success';
            } else {
                $toast_message = 'Registration error: ' . $stmt->error;
                $toast_type    = 'error';
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// ─── Fetch Events ──────────────────────────────────────────────────────────
$upcoming_result = $conn->query("SELECT * FROM events WHERE start_time >= CURDATE() ORDER BY start_time ASC");
$past_result     = $conn->query("SELECT * FROM events WHERE start_time < CURDATE() ORDER BY start_time DESC");

$my_registrations = null;
if (isset($_SESSION['alumni_id'])) {
    $alumni_id = $_SESSION['alumni_id'];
    $my_registrations = $conn->query("SELECT r.*, e.event_name, e.start_time, e.end_time, e.venue, e.event_image, e.description, e.event_category, e.has_registration_fee, e.registration_fee_amount FROM event_registrations r JOIN events e ON r.event_id = e.id WHERE r.alumni_id = $alumni_id ORDER BY e.start_time DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SVIS Alumni Events | Reunions, Meets & Celebrations</title>
  <meta name="description" content="Stay updated on the latest alumni events, reunions, and school meets at SVIS Hyderabad. Don't miss out on opportunities to reconnect and celebrate.">
  <link rel="icon" type="image/png" href="Logo/FavIcon.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="shared.css"/>
  <style>
    /* Navbar styles moved to shared.css */


    .tab-wrap { display: flex; justify-content: center; margin-bottom: 2.5rem; width: 100%; padding: 0 1rem; }
    .tab-grp { background: #fff; border-radius: 999px; box-shadow: 0 4px 20px rgba(0,0,0,.08); padding: .35rem; display: flex; position: relative; width: 100%; max-width: 440px; }
    .tab-btn { flex: 1; padding: .7rem 0.5rem; border-radius: 999px; border: none; font-weight: 600; font-family: 'Poppins', sans-serif; font-size: .9rem; transition: color .4s ease; background: transparent; color: #374151; cursor: pointer; position: relative; z-index: 2; white-space: nowrap; min-width: 0; }
    .tab-btn.active { color: #fff; }
    
    .tab-slider {
      position: absolute;
      top: .35rem;
      left: .35rem;
      bottom: .35rem;
      width: calc(50% - .35rem);
      background: var(--blue);
      border-radius: 999px;
      transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
      z-index: 1;
    }

    .events-grid { display: grid !important; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)) !important; gap: 1.5rem !important; margin-top: 1rem !important; justify-content: center !important; align-items: stretch !important; }
    .ev-card { background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; display: flex !important; flex-direction: column !important; transition: box-shadow .3s; height: 100% !important; width: 100% !important; max-width: 480px !important; }
    .ev-card:hover { box-shadow: 0 10px 30px rgba(0,0,0,.13); }
    .ev-img { width: 100%; height: 200px; background: linear-gradient(to right, #fff, #1841B0); overflow: hidden; display: flex; align-items: center; justify-content: center; }
    .ev-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
    .ev-card:hover .ev-img img { transform: scale(1.04); }
    .ev-img-placeholder { color: #fff; font-size: 3rem; opacity: .5; }
    .ev-body { padding: 1.25rem; flex: 1 1 auto !important; display: flex !important; flex-direction: column !important; }
    .ev-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: .5rem; flex-wrap: wrap; gap: .4rem; }
    .ev-date { color: #f97316; font-size: .82rem; font-weight: 600; }
    .ev-badge { background: #eff6ff; color: var(--blue); font-size: .72rem; padding: .2rem .65rem; border-radius: 999px; font-weight: 600; }
    .ev-badge.past { background: #f9fafb; color: #9ca3af; }
    .ev-badge.category { background: #f0fdf4; color: #16a34a; }
    .ev-title { font-size: 1.05rem; font-weight: 700; color: #111827; margin-bottom: .6rem; line-height: 1.3; }
    .ev-detail { display: flex; align-items: center; gap: .4rem; font-size: .82rem; color: #6b7280; margin-bottom: .3rem; }
    .ev-detail svg, .ev-detail i { width: 16px; text-align: center; color: #6b7280; font-size: 0.85rem; flex-shrink: 0; }
    .ev-desc { font-size: .83rem; color: #6b7280; line-height: 1.6; margin-top: .6rem; overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }

    .ev-reg-btn { 
      display: inline-flex; 
      align-items: center; 
      justify-content: center;
      gap: 0.5rem;
      padding: .6rem 1.5rem; 
      background: transparent; 
      color: var(--blue); 
      border: 2px solid var(--blue); 
      border-radius: 999px; 
      font-weight: 700; 
      font-size: .85rem; 
      cursor: pointer; 
      transition: all .3s; 
      text-align: center; 
      width: fit-content; 
      align-self: center; 
      margin-top: auto !important; 
    }
    .ev-reg-btn:hover { background: var(--blue); color: #fff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(29,78,216,0.2); }
    .ev-reg-btn i { font-size: 0.8rem; transition: transform 0.3s; }
    .ev-reg-btn:hover i { transform: translateX(4px); }

    .ev-request-btn { padding: .6rem 1.25rem; background: transparent; color: var(--blue); border: 2px solid transparent; border-radius: 999px; font-weight: 700; font-size: .85rem; cursor: pointer; transition: all 0.3s; text-align: center; display: inline-flex; align-items: center; gap: 0.5rem; }
    .ev-request-btn:hover { border-color: var(--blue); transform: translateY(-2px); }
    .ev-request-btn i { font-size: 0.8rem; transition: transform 0.3s; }
    .ev-request-btn:hover i { transform: translateX(4px); }

    .form-sec-title {
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--blue);
      border-bottom: 2px solid #e5e7eb;
      padding-bottom: 5px;
      margin: 1.5rem 0 1rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    @media (max-width: 600px) {
      .tab-btn { padding: 0.6rem 0.5rem; font-size: 0.8rem; }
      .policy-hero { padding: 8rem 1rem 2.5rem; }
      .ev-title { font-size: 1rem; }
      .ev-img { height: 180px; }
    }

    @media (max-width: 480px) {
      .ev-request-btn { width: 100%; justify-content: center; margin-top: 0.5rem; border-color: rgba(29,78,216,0.2); }
      .tab-btn { font-size: 0.72rem; }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .fade-in-up {
      animation: fadeInUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
    }
  </style>
  <!-- Shared JS -->
  <script src="shared.js"></script>
</head>
<body>

<?php if (!empty($toast_message)): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    showToast(<?= json_encode($toast_message) ?>, <?= json_encode($toast_type) ?>);
  });
</script>
<?php endif; ?>

<!-- ===== NAV ===== -->
<nav class="site-nav">
  <div class="nav-inner">
    <a href="index.php" class="nav-logo">
      <img src="Logo/Logo.svg" alt="SVIS Logo"/>
    </a>
    <div class="nav-links">
      <a href="index.php"     class="nav-link">Home</a>
      <a href="directory.php" class="nav-link">Directory</a>
      <a href="event.php"    class="nav-link active">Events</a>
      <a href="about.php"     class="nav-link">About</a>
      <a href="founders.php"  class="nav-link">Founders</a>
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
  <a href="event.php"   class="nav-link active">Events</a>
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

<div class="policy-hero">
    <h1>Alumni Events</h1>
    <p>Stay updated with the latest reunions, workshops, and networking gatherings</p>
</div>

<main class="hero-page-wrap">
    <div class="policy-container">



    <!-- Tabs -->
    <div class="tab-wrap">
      <div class="tab-grp" <?= isset($_SESSION['alumni_id']) ? 'style="max-width: 600px;"' : '' ?>>
        <div class="tab-slider" id="tab-slider" <?= isset($_SESSION['alumni_id']) ? 'style="width: calc(33.333% - .35rem);"' : '' ?>></div>
        <button class="tab-btn active" id="upcoming-btn" onclick="toggleEvents('upcoming')">Upcoming Events</button>
        <button class="tab-btn"        id="past-btn"     onclick="toggleEvents('past')">Past Events</button>
        <?php if (isset($_SESSION['alumni_id'])): ?>
          <button class="tab-btn"      id="my-btn"       onclick="toggleEvents('my')">My Registrations</button>
        <?php endif; ?>
      </div>
    </div>

    <!-- ===== UPCOMING EVENTS ===== -->
    <div id="upcoming-events-list" class="fade-in-up">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size:1.4rem;font-weight:600;color:#111827;margin:0;">Upcoming Events</h2>
        <?php if (isset($_SESSION['alumni_id'])): ?>
          <button class="ev-request-btn" onclick="openReqModal()">Event Request <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14m-7-7l7 7-7 7"/></svg></button>
        <?php endif; ?>
      </div>
      <div class="events-grid">
        <?php if ($upcoming_result && $upcoming_result->num_rows > 0): ?>
          <?php while ($row = $upcoming_result->fetch_assoc()): ?>
          <div class="ev-card">
            <div class="ev-img">
              <?php if (!empty($row['event_image'])): ?>
                <img src="uploads/<?= htmlspecialchars($row['event_image']) ?>" alt="<?= htmlspecialchars($row['event_name']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['event_name']) ?>&background=random&color=fff&size=600&font-size=0.25'"/>
              <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['event_name']) ?>&background=random&color=fff&size=600&font-size=0.25" alt="<?= htmlspecialchars($row['event_name']) ?>"/>
              <?php endif; ?>
            </div>
            <div class="ev-body">
              <div class="ev-meta">
                <?php
                  $start_date = date("M d, Y", strtotime($row['start_time']));
                  $end_date = date("M d, Y", strtotime($row['end_time']));
                  $date_display = ($start_date === $end_date) ? $start_date : $start_date . " - " . $end_date;
                ?>
                <span class="ev-date"><i class="far fa-calendar-alt" style="margin-right: 4px;"></i><?= $date_display ?></span>
                <div style="display: flex; gap: 4px;">
                  <span class="ev-badge category"><?= htmlspecialchars(ucwords(strtolower($row['event_category'] ?? 'General'))) ?></span>
                  <span class="ev-badge">Upcoming</span>
                </div>
              </div>
              <h3 class="ev-title"><?= htmlspecialchars(ucfirst($row['event_name'])) ?></h3>
              <div class="ev-detail">
                <i class="far fa-clock"></i>
                <span><?= date("M d, Y, h:i A", strtotime($row['start_time'])) ?> – <?= date("M d, Y, h:i A", strtotime($row['end_time'])) ?></span>
              </div>
              <div class="ev-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span><?= htmlspecialchars(ucfirst($row['venue'])) ?></span>
              </div>
              <div class="ev-detail">
                <i class="fas fa-ticket-alt"></i>
                <span>Fee: <strong><?= ($row['has_registration_fee'] === 'Yes') ? '₹' . number_format($row['registration_fee_amount'], 2) : 'Free' ?></strong></span>
              </div>
              <?php if (!empty($row['description'])): ?>
                <p class="ev-desc"><?= htmlspecialchars(ucfirst($row['description'])) ?></p>
              <?php endif; ?>
              <button class="ev-reg-btn" 
                      data-id="<?= $row['id'] ?>"
                      data-name="<?= htmlspecialchars($row['event_name']) ?>"
                      data-date="<?= date("Y-m-d", strtotime($row['start_time'])) ?>"
                      data-time="<?= date("h:i A", strtotime($row['start_time'])) ?>"
                      data-end-date="<?= date("Y-m-d", strtotime($row['end_time'])) ?>"
                      data-end-time="<?= date("h:i A", strtotime($row['end_time'])) ?>"
                      data-venue="<?= htmlspecialchars($row['venue']) ?>"
                      data-description="<?= htmlspecialchars($row['description'] ?? '') ?>"
                      data-category="<?= htmlspecialchars($row['event_category'] ?? '') ?>"
                      data-fee="<?= ($row['has_registration_fee'] === 'Yes') ? htmlspecialchars($row['registration_fee_amount']) : '0.00 (Free)' ?>"
                      onclick="openRegModal(this)">
                Register Now <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14m-7-7l7 7-7 7"/></svg>
              </button>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="color:#6b7280;grid-column:1/-1;">No upcoming events at the moment. Check back soon!</p>
        <?php endif; ?>
      </div>
      <!-- Pagination for Upcoming -->
      <div id="upcoming-pagination" class="pagination-wrap"></div>
    </div>

    <!-- ===== PAST EVENTS ===== -->
    <div id="past-events-list" style="display:none;" class="fade-in-up">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size:1.4rem;font-weight:600;color:#111827;margin:0;">Past Events</h2>
        <?php if (isset($_SESSION['alumni_id'])): ?>
          <button class="ev-request-btn" onclick="openReqModal()">Event Request <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14m-7-7l7 7-7 7"/></svg></button>
        <?php endif; ?>
      </div>
      <div class="events-grid">
        <?php if ($past_result && $past_result->num_rows > 0): ?>
          <?php while ($row = $past_result->fetch_assoc()): ?>
          <div class="ev-card">
            <div class="ev-img">
              <?php if (!empty($row['event_image'])): ?>
                <img src="uploads/<?= htmlspecialchars($row['event_image']) ?>" alt="<?= htmlspecialchars($row['event_name']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['event_name']) ?>&background=random&color=fff&size=600&font-size=0.25'"/>
              <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['event_name']) ?>&background=random&color=fff&size=600&font-size=0.25" alt="<?= htmlspecialchars($row['event_name']) ?>"/>
              <?php endif; ?>
            </div>
            <div class="ev-body">
              <div class="ev-meta">
                <?php
                  $start_date = date("M d, Y", strtotime($row['start_time']));
                  $end_date = date("M d, Y", strtotime($row['end_time']));
                  $date_display = ($start_date === $end_date) ? $start_date : $start_date . " - " . $end_date;
                ?>
                <span class="ev-date"><i class="far fa-calendar-alt" style="margin-right: 4px;"></i><?= $date_display ?></span>
                <div style="display: flex; gap: 4px;">
                  <span class="ev-badge category"><?= htmlspecialchars(ucwords(strtolower($row['event_category'] ?? 'General'))) ?></span>
                  <span class="ev-badge past">Past Event</span>
                </div>
              </div>
              <h3 class="ev-title"><?= htmlspecialchars(ucfirst($row['event_name'])) ?></h3>
              <div class="ev-detail">
                <i class="far fa-clock"></i>
                <span><?= date("M d, Y, h:i A", strtotime($row['start_time'])) ?> – <?= date("M d, Y, h:i A", strtotime($row['end_time'])) ?></span>
              </div>
              <div class="ev-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span><?= htmlspecialchars(ucfirst($row['venue'])) ?></span>
              </div>
              <div class="ev-detail">
                <i class="fas fa-ticket-alt"></i>
                <span>Fee: <strong><?= ($row['has_registration_fee'] === 'Yes') ? '₹' . number_format($row['registration_fee_amount'], 2) : 'Free' ?></strong></span>
              </div>
              <?php if (!empty($row['description'])): ?>
                <p class="ev-desc"><?= htmlspecialchars(ucfirst($row['description'])) ?></p>
              <?php endif; ?>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="color:#6b7280;grid-column:1/-1;">No past events found.</p>
        <?php endif; ?>
      </div>
      <!-- Pagination for Past -->
      <div id="past-pagination" class="pagination-wrap"></div>
    </div>

    <!-- ===== MY REGISTRATIONS ===== -->
    <?php if (isset($_SESSION['alumni_id'])): ?>
    <div id="my-events-list" style="display:none;" class="fade-in-up">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size:1.4rem;font-weight:600;color:#111827;margin:0;">My Registered Events</h2>
      </div>
      <div class="events-grid">
        <?php if ($my_registrations && $my_registrations->num_rows > 0): ?>
          <?php while ($row = $my_registrations->fetch_assoc()): ?>
          <div class="ev-card">
            <div class="ev-img">
              <?php if (!empty($row['event_image'])): ?>
                <img src="uploads/<?= htmlspecialchars($row['event_image']) ?>" alt="<?= htmlspecialchars($row['event_name']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['event_name']) ?>&background=random&color=fff&size=600&font-size=0.25'"/>
              <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['event_name']) ?>&background=random&color=fff&size=600&font-size=0.25" alt="<?= htmlspecialchars($row['event_name']) ?>"/>
              <?php endif; ?>
            </div>
            <div class="ev-body">
              <div class="ev-meta">
                <?php
                  $start_date = date("M d, Y", strtotime($row['start_time']));
                  $end_date = date("M d, Y", strtotime($row['end_time']));
                  $date_display = ($start_date === $end_date) ? $start_date : $start_date . " - " . $end_date;
                ?>
                <span class="ev-date"><i class="far fa-calendar-alt" style="margin-right: 4px;"></i><?= $date_display ?></span>
                <div style="display: flex; gap: 4px;">
                  <span class="ev-badge category"><?= htmlspecialchars(ucwords(strtolower($row['event_category'] ?? 'General'))) ?></span>
                  <?php if(strtotime($row['start_time']) >= time()): ?>
                    <span class="ev-badge">Upcoming</span>
                  <?php else: ?>
                    <span class="ev-badge past">Past Event</span>
                  <?php endif; ?>
                </div>
              </div>
              <h3 class="ev-title"><?= htmlspecialchars(ucfirst($row['event_name'])) ?></h3>
              <div class="ev-detail">
                <i class="far fa-clock"></i>
                <span><?= date("M d, Y, h:i A", strtotime($row['start_time'])) ?> – <?= date("M d, Y, h:i A", strtotime($row['end_time'])) ?></span>
              </div>
              <div class="ev-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span><?= htmlspecialchars(ucfirst($row['venue'])) ?></span>
              </div>
              <div class="ev-detail">
                <i class="fas fa-ticket-alt"></i>
                <span>Fee: <strong><?= ($row['has_registration_fee'] === 'Yes') ? '₹' . number_format($row['registration_fee_amount'], 2) : 'Free' ?></strong></span>
              </div>
              <p class="ev-desc" style="margin-top:0.8rem; background:#f9fafb; padding:10px; border-radius:8px; font-size:0.85rem; color:#374151;">
                <strong>Attendance:</strong> <?= htmlspecialchars($row['attendance']) ?><br>
                <strong>Fee Paid:</strong> <?= $row['registration_fee'] > 0 ? '₹' . $row['registration_fee'] : 'Free' ?>
              </p>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="color:#6b7280;grid-column:1/-1;">You haven't registered for any events yet.</p>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    </div>
</main>

<!-- ===== EVENT REQUEST MODAL (Propose New Event) ===== -->
<div class="modal-overlay modal-hidden" id="req-modal">
  <div class="modal-box" style="max-width:550px;">
    <div class="modal-header">
      <div class="modal-logos"><img src="Logo/Logo.svg" alt="SVIS" style="height:44px;"/></div>
      <h2>Event Request</h2>
      <p>Submit your event proposal to SVIS Alumni</p>
    </div>
    <button class="modal-close" onclick="hideModal('req')" aria-label="Close">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto; overflow-x: hidden;">
      <form method="POST" action="event.php">
        <input type="hidden" name="form_type" value="event_request"/>
        
        <!-- SECTION 1: Alumni Information -->
        <h4 class="form-sec-title"><i class="fas fa-user-graduate"></i> 1. Alumni Information</h4>
        <div>
          <div class="form-group">
            <label>Full Name<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
              <input type="text" name="full_name" value="<?= htmlspecialchars($alumni_details['full_name'] ?? '') ?>" placeholder="Enter your full name" required <?= isset($alumni_details['full_name']) ? 'readonly class="readonly-input"' : '' ?>/>
            </div>
          </div>
          <div class="form-group">
            <label>Alumni ID</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg></div>
              <input type="text" name="alumni_id" value="<?= htmlspecialchars($alumni_details['id'] ?? '') ?>" placeholder="Alumni ID" <?= isset($alumni_details['id']) ? 'readonly class="readonly-input"' : '' ?>/>
            </div>
          </div>
        </div>
        
        <div>
          <div class="form-group">
            <label>Batch / Passing Year</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
              <input type="text" name="batch_year" value="<?= htmlspecialchars($alumni_details['batch_year'] ?? '') ?>" placeholder="e.g. 2015" <?= isset($alumni_details['batch_year']) ? 'readonly class="readonly-input"' : '' ?>/>
            </div>
          </div>
          <div class="form-group">
            <label>Mobile Number<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5.5a3 3 0 013-3h1.5a1 1 0 01.9.55l1.65 3.3a1 1 0 01-.1 1.05L8.9 8.9a13.04 13.04 0 005.4 5.4l1.5-1.1a1 1 0 011.05-.1l3.3 1.65a1 1 0 01.55.9V18a3 3 0 01-3 3h-1.5C10.1 21 3 13.9 3 5.5z"/></svg></div>
              <input type="tel" name="phone" value="<?= htmlspecialchars($alumni_details['phone'] ?? '') ?>" placeholder="Enter phone number" required <?= isset($alumni_details['phone']) ? 'readonly class="readonly-input"' : '' ?>/>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <input type="email" name="email" value="<?= htmlspecialchars($alumni_details['email'] ?? '') ?>" placeholder="Enter email address" <?= isset($alumni_details['email']) ? 'readonly class="readonly-input"' : '' ?>/>
          </div>
        </div>

        <!-- SECTION 2: Event Basic Details -->
        <h4 class="form-sec-title"><i class="fas fa-info-circle"></i> 2. Event Basic Details</h4>
        <div class="form-group">
          <label>Event Title<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
            <input type="text" name="event_title" placeholder="Enter event title" required/>
          </div>
        </div>
        
        <div class="form-group">
          <label>Event Category<span class="req">*</span></label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg></div>
            <select name="event_category" required>
              <option value="">Select Category</option>
              <option value="Reunion">Reunion</option>
              <option value="Webinar">Webinar</option>
              <option value="Workshop">Workshop</option>
              <option value="Networking">Networking</option>
              <option value="Career Guidance">Career Guidance</option>
              <option value="Cultural">Cultural</option>
              <option value="Sports">Sports</option>
              <option value="Fundraiser">Fundraiser</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Event Description<span class="req">*</span></label>
          <div class="input-wrap">
            <textarea name="event_description" rows="3" placeholder="Describe the event..." required></textarea>
          </div>
        </div>

        <div class="form-group">
          <label>Purpose / Objective of the Event</label>
          <div class="input-wrap">
            <textarea name="purpose" rows="2" placeholder="What is the objective of this event?"></textarea>
          </div>
        </div>

        <!-- SECTION 3: Event Schedule -->
        <h4 class="form-sec-title"><i class="fas fa-clock"></i> 3. Event Schedule</h4>
        <div>
          <div class="form-group">
            <label>Preferred Event Date<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
              <input type="date" name="preferred_date" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Alternate Date (Optional)</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
              <input type="date" name="alternate_date"/>
            </div>
          </div>
        </div>

        <div>
          <div class="form-group">
            <label>Preferred Time<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <input type="time" name="preferred_time" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Event Duration (Hours)</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <input type="number" step="0.5" min="0.5" name="event_duration" placeholder="e.g. 2"/>
            </div>
          </div>
        </div>

        <!-- SECTION 4: Event Mode & Venue -->
        <h4 class="form-sec-title"><i class="fas fa-map-marker-alt"></i> 4. Event Mode & Venue</h4>
        <div>
          <div class="form-group">
            <label>Event Mode<span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <select name="event_mode" required>
                <option value="">Select Mode</option>
                <option value="Offline">Offline</option>
                <option value="Online">Online</option>
                <option value="Hybrid">Hybrid</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Preferred Venue / Location</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
              <input type="text" name="venue" placeholder="Event venue or location"/>
            </div>
          </div>
        </div>

        <div>
          <div class="form-group">
            <label>Online Platform Preference</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <input type="text" name="online_platform" placeholder="Zoom / Google Meet / Teams etc."/>
            </div>
          </div>
          <div class="form-group">
            <label>Expected Number of Participants</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
              <input type="text" name="expected_participants" placeholder="e.g. 50-100"/>
            </div>
          </div>
        </div>

        <button type="submit" class="form-submit" style="margin-top: 1.5rem;">Submit Request</button>
      </form>
    </div>
  </div>
</div>

<!-- ===== EVENT REGISTER MODAL (Attending Existing Event) ===== -->
<div class="modal-overlay modal-hidden" id="reg-modal">
  <div class="modal-box" style="max-width:550px;">
    <div class="modal-header">
      <div class="modal-logos"><img src="Logo/Logo.svg" alt="SVIS" style="height:44px;"/></div>
      <h2>Event Registration</h2>
      <p id="reg-event-title">Join our upcoming event</p>
    </div>
    <button class="modal-close" onclick="hideModal('reg')" aria-label="Close">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
      <form method="POST" action="event.php">
        <input type="hidden" name="form_type" value="event_registration"/>
        <input type="hidden" name="event_id" id="reg-event-id-input"/>
        <input type="hidden" name="alumni_id" id="reg-alumni-id" value="<?php echo htmlspecialchars($alumni_details['id'] ?? ''); ?>"/>
        
        <!-- SECTION 1: Alumni Details (Auto-filled) -->
        <h4 class="form-sec-title"><i class="fas fa-user-graduate"></i> Alumni Details</h4>
        <div>
          <div class="form-group">
            <label>Full Name</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
              <input type="text" name="full_name" id="reg-alumni-name" value="<?= htmlspecialchars($alumni_details['full_name'] ?? '') ?>" readonly class="readonly-input" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Alumni ID</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg></div>
              <input type="text" name="alumni_id" id="reg-alumni-id" value="<?= htmlspecialchars($alumni_details['id'] ?? '') ?>" readonly class="readonly-input" required/>
            </div>
          </div>
        </div>
        
        <div>
          <div class="form-group">
            <label>Batch / Passing Year</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
              <input type="text" name="batch" id="reg-alumni-batch" value="<?= htmlspecialchars($alumni_details['batch_year'] ?? '') ?>" readonly class="readonly-input" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Mobile Number</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5.5a3 3 0 013-3h1.5a1 1 0 01.9.55l1.65 3.3a1 1 0 01-.1 1.05L8.9 8.9a13.04 13.04 0 005.4 5.4l1.5-1.1a1 1 0 011.05-.1l3.3 1.65a1 1 0 01.55.9V18a3 3 0 01-3 3h-1.5C10.1 21 3 13.9 3 5.5z"/></svg></div>
              <input type="tel" name="phone" id="reg-alumni-phone" value="<?= htmlspecialchars($alumni_details['phone'] ?? '') ?>" readonly class="readonly-input" required/>
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <label>Email Address</label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
            <input type="email" name="email" id="reg-alumni-email" value="<?= htmlspecialchars($alumni_details['email'] ?? '') ?>" readonly class="readonly-input" required/>
          </div>
        </div>

        <!-- SECTION 2: Event Details (Auto-filled) -->
        <h4 class="form-sec-title"><i class="fas fa-calendar-check"></i> Event Details</h4>
        <div class="form-group">
          <label>Event Name</label>
          <div class="input-wrap">
            <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
            <input type="text" name="event_name" id="reg-event-name" readonly class="readonly-input" required/>
          </div>
        </div>
        
        <div>
          <div class="form-group">
            <label>Event Date</label>
            <div class="input-wrap">
              <div class="input-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <input type="date" name="event_date" id="reg-event-date" readonly class="readonly-input" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Event Time</label>
            <div class="input-wrap">
              <div class="input-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <input type="time" name="event_time" id="reg-event-time" readonly class="readonly-input" required/>
            </div>
          </div>
        </div>

        <div>
          <div class="form-group">
            <label>Event End Date</label>
            <div class="input-wrap">
              <div class="input-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <input type="date" name="event_end_date" id="reg-event-end-date" readonly class="readonly-input" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Event End Time</label>
            <div class="input-wrap">
              <div class="input-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <input type="time" name="event_end_time" id="reg-event-end-time" readonly class="readonly-input" required/>
            </div>
          </div>
        </div>

        <div>
          <div class="form-group">
            <label>Venue / Mode</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
              <input type="text" name="event_venue" id="reg-event-venue" readonly class="readonly-input" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Event Category</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg></div>
              <input type="text" name="event_category" id="reg-event-category" readonly class="readonly-input" required/>
            </div>
          </div>
        </div>

        <!-- SECTION 3 & 4: Attendance & Payment -->
        <h4 class="form-sec-title"><i class="fas fa-info-circle"></i> Attendance & Payment</h4>
        <div>
          <div class="form-group">
            <label>Will you attend? <span class="req">*</span></label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <select name="attendance" required>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
                <option value="Maybe">Maybe</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Registration Fee</label>
            <div class="input-wrap">
              <div class="input-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
              <input type="text" name="registration_fee" id="reg-event-fee" readonly class="readonly-input" required/>
            </div>
          </div>
        </div>

        <!-- SECTION 5: Consent -->
        <div class="form-group checkbox-group" style="margin-top: 1rem; display: flex; align-items: flex-start; gap: 8px;">
          <input type="checkbox" name="terms_agree" id="terms_agree" value="1" required style="margin-top: 4px;"/>
          <label for="terms_agree" style="font-size: 0.82rem; color: #4b5563; font-weight: 500; cursor: pointer; user-select: none;">
            I agree to the Event Terms &amp; Conditions and confirm my registration. <span class="req">*</span>
          </label>
        </div>

        <button type="submit" class="form-submit" style="margin-top: 1.5rem;">Confirm Registration</button>
      </form>
    </div>
  </div>
</div>

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

<div id="toast-container"></div>

<script>
  const isLoggedIn = <?= (isset($_SESSION['alumni_id']) && !empty($_SESSION['alumni_id'])) ? 'true' : 'false' ?>;

  function openReqModal() {
    if (!isLoggedIn) {
      showToast("Please login to request an event.", "error");
      setTimeout(() => {
        showModal('login');
      }, 500);
      return;
    }
    showModal('req');
  }

  /* Mobile Nav Toggle */
  document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('hamburger-btn');
    const mobileNav = document.getElementById('mobile-menu');
    const hamIcon = document.getElementById('hamburger-icon');

    if (menuBtn && mobileNav) {
      menuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = mobileNav.classList.toggle('open');
        if (hamIcon) hamIcon.style.transform = isOpen ? 'rotate(90deg)' : 'rotate(0)';
      });

      document.addEventListener('click', (e) => {
        if (!menuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
          mobileNav.classList.remove('open');
          if (hamIcon) hamIcon.style.transform = 'rotate(0)';
        }
      });
    }

    // Pagination State
    let upcomingCurrentPage = 1;
    let pastCurrentPage = 1;
    const itemsPerPage = 8;

    function paginateList(listId, paginationId, pageVar, setPageVar) {
      const listContainer = document.getElementById(listId);
      if (!listContainer) return;
      const cards = listContainer.querySelectorAll('.ev-card');
      const totalPages = Math.ceil(cards.length / itemsPerPage);
      
      // Update visibility
      cards.forEach((card, index) => {
        if (index >= (pageVar - 1) * itemsPerPage && index < pageVar * itemsPerPage) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });

      // Render Pagination
      renderEventPagination(paginationId, totalPages, pageVar, (newPage) => {
        setPageVar(newPage);
        paginateList(listId, paginationId, newPage, setPageVar);
        window.scrollTo({ top: 300, behavior: 'smooth' });
      });
    }

    function renderEventPagination(id, total, current, onPageChange) {
      const container = document.getElementById(id);
      if (!container) return;
      container.innerHTML = '';
      if (total <= 1) return;

      const isMobile = window.innerWidth < 600;

      // Previous
      const prev = document.createElement('button');
      prev.className = 'page-btn prev-next';
      prev.innerHTML = isMobile ? '<i class="fas fa-chevron-left"></i>' : '<i class="fas fa-chevron-left"></i> Previous';
      prev.disabled = current === 1;
      prev.onclick = () => onPageChange(current - 1);
      container.appendChild(prev);

      // Numbers
      const delta = isMobile ? 1 : 2;
      let start = Math.max(1, current - delta);
      let end = Math.min(total, start + (delta * 2));
      if (end - start < (delta * 2)) start = Math.max(1, end - (delta * 2));

      for (let i = start; i <= end; i++) {
        const btn = document.createElement('button');
        btn.className = `page-btn ${i === current ? 'active' : ''}`;
        btn.textContent = i;
        btn.onclick = () => onPageChange(i);
        container.appendChild(btn);
      }

      // Next
      const next = document.createElement('button');
      next.className = 'page-btn prev-next';
      next.innerHTML = isMobile ? '<i class="fas fa-chevron-right"></i>' : 'Next <i class="fas fa-chevron-right"></i>';
      next.disabled = current === total;
      next.onclick = () => onPageChange(current + 1);
      container.appendChild(next);
    }

    // Initial Pagination
    paginateList('upcoming-events-list', 'upcoming-pagination', upcomingCurrentPage, (v) => upcomingCurrentPage = v);
    paginateList('past-events-list', 'past-pagination', pastCurrentPage, (v) => pastCurrentPage = v);

    // Initialize Shared Login AJAX
    handleLoginAJAX('login-form');
    
    // Initialize Custom Selects
    if (typeof initCustomSelects === 'function') {
        initCustomSelects();
    }
  });

  function openRegModal(btn) {
    if (!isLoggedIn) {
      showToast("Please login to register for this event.", "error");
      setTimeout(() => {
        showModal('login');
      }, 500);
      return;
    }
    
    // Extract info
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    const date = btn.getAttribute('data-date');
    const time = btn.getAttribute('data-time');
    const endDate = btn.getAttribute('data-end-date');
    const endTime = btn.getAttribute('data-end-time');
    const venue = btn.getAttribute('data-venue');
    const desc = btn.getAttribute('data-description') || '';
    const category = btn.getAttribute('data-category') || 'Reunion';
    const fee = btn.getAttribute('data-fee') || '0.00 (Free)';
    
    // Set fields
    showModal('reg');
    document.getElementById('reg-event-title').textContent = "Registering for: " + name;
    document.getElementById('reg-event-id-input').value = id;
    
    document.getElementById('reg-event-name').value = name;
    // Format date as YYYY-MM-DD for <input type="date">
    const parsedDate = new Date(date);
    const formattedDate = !isNaN(parsedDate)
      ? parsedDate.toISOString().split('T')[0]
      : date;
    document.getElementById('reg-event-date').value = formattedDate;
    // Format time as HH:MM for <input type="time">
    const timeParsed = new Date('1970-01-01 ' + time);
    const formattedTime = !isNaN(timeParsed)
      ? timeParsed.toTimeString().slice(0, 5)
      : '';
    document.getElementById('reg-event-time').value = formattedTime;
    // Format end date as YYYY-MM-DD for <input type="date">
    const parsedEndDate = new Date(endDate);
    const formattedEndDate = !isNaN(parsedEndDate)
      ? parsedEndDate.toISOString().split('T')[0]
      : endDate;
    document.getElementById('reg-event-end-date').value = formattedEndDate;
    // Format end time as HH:MM for <input type="time">
    const endTimeParsed = new Date('1970-01-01 ' + endTime);
    const formattedEndTime = !isNaN(endTimeParsed)
      ? endTimeParsed.toTimeString().slice(0, 5)
      : '';
    document.getElementById('reg-event-end-time').value = formattedEndTime;
    document.getElementById('reg-event-venue').value = venue;
    document.getElementById('reg-event-category').value = category;
    document.getElementById('reg-event-fee').value = fee;
    
  }

  function showModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.remove('modal-hidden');
    m.classList.add('modal-visible');
    document.body.style.overflow = 'hidden';
  }
  function hideModal(type) {
    const m = document.getElementById(type + '-modal');
    if (!m) return;
    m.classList.add('modal-hidden');
    m.classList.remove('modal-visible');
    document.body.style.overflow = '';
  }

  function togglePassword(id) {
    const input = document.getElementById(id);
    if (!input) return;
    input.type = (input.type === 'password') ? 'text' : 'password';
  }

  function toggleEvents(v) {
    const upcoming = document.getElementById('upcoming-events-list');
    const past = document.getElementById('past-events-list');
    const my = document.getElementById('my-events-list');
    const slider = document.getElementById('tab-slider');
    
    if(upcoming) upcoming.style.display = 'none';
    if(past) past.style.display = 'none';
    if(my) my.style.display = 'none';
    
    if (v === 'upcoming') {
      if(upcoming) {
        upcoming.style.display = 'block';
        upcoming.style.animation = 'none';
        upcoming.offsetHeight; 
        upcoming.style.animation = null;
      }
      if(slider) slider.style.transform = 'translateX(0)';
    } else if (v === 'past') {
      if(past) {
        past.style.display = 'block';
        past.style.animation = 'none';
        past.offsetHeight; 
        past.style.animation = null;
      }
      if(slider) slider.style.transform = 'translateX(100%)';
    } else if (v === 'my') {
      if(my) {
        my.style.display = 'block';
        my.style.animation = 'none';
        my.offsetHeight; 
        my.style.animation = null;
      }
      if(slider) slider.style.transform = 'translateX(200%)';
    }
    
    const uBtn = document.getElementById('upcoming-btn');
    const pBtn = document.getElementById('past-btn');
    const mBtn = document.getElementById('my-btn');
    
    if(uBtn) uBtn.classList.toggle('active', v==='upcoming');
    if(pBtn) pBtn.classList.toggle('active', v==='past');
    if(mBtn) mBtn.classList.toggle('active', v==='my');
  }
</script>
</body>
</html>