<?php 
include('header.php'); 
include('sidebar.php'); 
include('../db_connect.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['flash_msg']='Invalid User';$_SESSION['flash_type']='danger';
    header("Location: users.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_showcase_order'])) {
    $user_id = intval($_POST['user_id']);
    $showcase_order = intval($_POST['showcase_order']);
    $stmt = $conn->prepare("UPDATE alumni_register SET showcase_order = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $showcase_order, $user_id);
        if ($stmt->execute()) {
            $_SESSION['flash_msg'] = "Spotlight order saved successfully!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_msg'] = "Failed to save spotlight order.";
            $_SESSION['flash_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['flash_msg'] = "Database error.";
        $_SESSION['flash_type'] = "danger";
    }
    echo "<script>window.location.href='users_list_view.php?id=" . $user_id . "';</script>";
    exit;
}
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT a.*, c.name as country_name FROM alumni_register a LEFT JOIN countries c ON a.country = c.id WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['flash_msg']='User not found';$_SESSION['flash_type']='danger';
    header("Location: users.php"); exit;
}

$user = $result->fetch_assoc();
$parts = explode(' ', trim($user['full_name']));
$initials = strtoupper(substr($parts[0],0,1).(isset($parts[1])?substr($parts[1],0,1):''));
$img = !empty($user['user_image']) ? "../uploads/".$user['user_image'] : "";
$hasImg = !empty($user['user_image']);

$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>

<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  :root{
    --blue:#1a56a0;--blue-dark:#0f3566;--blue-hover:#154a8a;--blue-light:#e8f0fb;
    --bg:#f0f4fa;--surface:#fff;--surface-alt:#f7f9fd;
    --border:rgba(26,86,160,.12);--border-soft:rgba(26,86,160,.07);
    --text:#0f2545;--text-2:#4a6080;--text-3:#8aa0bb;
    --green:#0a7a5a;--green-bg:rgba(15,168,126,.10);
    --red:#c0392b;--red-bg:rgba(192,57,43,.10);
    --amber:#d35400;--amber-bg:rgba(211,84,0,.10);
    --shadow-sm:0 1px 3px rgba(15,53,102,.08),0 1px 2px rgba(15,53,102,.05);
    --r:14px;
  }
  body{display:flex;background:var(--bg);font-family:'Lato',sans-serif;color:var(--text);min-height:100vh}
  .dash-main{flex:1;min-width:0;padding:2rem 2.25rem;overflow-x:hidden}

  .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem}
  .page-header-left{display:flex;align-items:center;gap:14px}
  .page-header-icon{width:46px;height:46px;background:var(--blue-light);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
  .page-header-icon svg{width:20px;height:20px;stroke:var(--blue);fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round}
  .page-header-label{font-size:.72rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--text-3);margin-bottom:2px}
  .page-header-title{font-size:1.6rem;font-weight:900;color:var(--blue-dark);line-height:1.1}
  .btn-back{display:inline-flex;align-items:center;gap:7px;padding:0 1.1rem;height:40px;background:var(--surface);color:var(--text-2);font-family:'Lato',sans-serif;font-size:.85rem;font-weight:700;border:1.5px solid var(--border);border-radius:9px;text-decoration:none;cursor:pointer;transition:background .15s,color .15s,border-color .15s,transform .1s;white-space:nowrap}
  .btn-back svg{width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;transition:stroke .15s,transform .15s}
  .btn-back:hover{background:var(--blue-light);color:var(--blue);border-color:rgba(26,86,160,.2);text-decoration:none}
  .btn-back:hover svg{stroke:var(--blue);transform:translateX(-2px)}

  /* Profile hero */
  .profile-hero{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow-sm);padding:2rem;display:flex;align-items:center;gap:1.5rem;margin-bottom:1.5rem;position:relative;overflow:hidden}
  .profile-hero::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--blue)}
  .profile-avatar{width:80px;height:80px;border-radius:50%;flex-shrink:0;overflow:hidden;border:3px solid var(--blue-light)}
  .profile-avatar img{width:100%;height:100%;object-fit:cover}
  .profile-avatar-init{width:80px;height:80px;border-radius:50%;background:var(--blue-light);color:var(--blue);font-size:1.6rem;font-weight:900;display:flex;align-items:center;justify-content:center;flex-shrink:0;letter-spacing:.03em;border:3px solid rgba(26,86,160,.15)}
  .profile-info h2{font-size:1.4rem;font-weight:900;color:var(--blue-dark);margin-bottom:2px}
  .profile-meta{display:flex;flex-wrap:wrap;gap:12px;margin-top:6px}
  .profile-meta-item{display:inline-flex;align-items:center;gap:5px;font-size:.82rem;color:var(--text-2);font-weight:400}
  .profile-meta-item svg{width:13px;height:13px;stroke:var(--text-3);fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round}
  .status-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 12px;border-radius:20px;font-size:.75rem;font-weight:700}
  .status-pill.verified{background:var(--green-bg);color:var(--green)}
  .status-pill.not-verified{background:rgba(107,114,128,.1);color:#6b7280}
  .status-pill.pending-approval{background:var(--amber-bg);color:var(--amber)}
  .status-pill.rejected{background:var(--red-bg);color:var(--red)}
  .status-pill svg{width:11px;height:11px;fill:none;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}
  .status-pill.verified svg{stroke:var(--green)}
  .status-pill.not-verified svg{stroke:#6b7280}
  .status-pill.pending-approval svg{stroke:var(--amber)}
  .status-pill.rejected svg{stroke:var(--red)}

  /* Info sections */
  .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem}
  @media(max-width:900px){.info-grid{grid-template-columns:1fr}}
  .info-panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow-sm);overflow:hidden}
  .info-panel.full{grid-column:1/-1}
  .info-head{display:flex;align-items:center;gap:9px;padding:.9rem 1.25rem;background:var(--surface-alt);border-bottom:1px solid var(--border-soft)}
  .info-head-icon{width:28px;height:28px;border-radius:7px;background:rgba(26,86,160,.10);display:flex;align-items:center;justify-content:center;flex-shrink:0}
  .info-head-icon svg{width:13px;height:13px;stroke:var(--blue);fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round}
  .info-head-title{font-size:.88rem;font-weight:700;color:var(--text)}

  .info-table{width:100%;border-collapse:collapse}
  .info-table tr{border-bottom:1px solid var(--border-soft)}
  .info-table tr:last-child{border-bottom:none}
  .info-table th{padding:.65rem 1.25rem;font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-3);text-align:left;width:38%;white-space:nowrap;background:transparent}
  .info-table td{padding:.65rem 1.25rem;font-size:.9rem;color:var(--text);font-weight:400}
  .info-table td a{color:var(--blue);text-decoration:none;font-weight:500;word-break:break-all}
  .info-table td a:hover{text-decoration:underline}

  .bio-box{padding:1rem 1.25rem;font-size:.9rem;color:var(--text-2);line-height:1.65;font-weight:400}
  .bio-box:empty::before{content:'No bio provided.';color:var(--text-3);font-style:italic}

  /* Social links row */
  .social-links{display:flex;gap:8px;padding:1rem 1.25rem;flex-wrap:wrap}
  .social-link{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:var(--surface-alt);border:1px solid var(--border-soft);border-radius:8px;text-decoration:none;font-size:.82rem;font-weight:600;color:var(--text-2);transition:background .15s,border-color .15s,color .15s}
  .social-link svg{width:14px;height:14px;stroke:var(--text-3);fill:none;stroke-width:1.75;stroke-linecap:round;stroke-linejoin:round;transition:stroke .15s}
  .social-link:hover{background:var(--blue-light);border-color:rgba(26,86,160,.2);color:var(--blue);text-decoration:none}
  .social-link:hover svg{stroke:var(--blue)}
  .social-empty{padding:1rem 1.25rem;font-size:.85rem;color:var(--text-3);font-style:italic}
</style>

<div class="dash-main">

  <!-- Toast Flash Message -->
  <?php if (!empty($flash_msg)): ?>
    <div id="toastFlash" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 14px 20px; border-radius: 10px; font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: <?= $flash_type === 'success' ? '#16a34a' : '#ef4444' ?>; color: #ffffff; border: none; transform: translateX(120%); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
      <?php if ($flash_type === 'success'): ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <?php else: ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php endif; ?>
      <?= htmlspecialchars($flash_msg) ?>
    </div>
    <script>
      setTimeout(() => {
        const t = document.getElementById('toastFlash');
        if (t) { t.style.transform = 'translateX(0)'; t.style.opacity = '1'; setTimeout(() => { t.style.transform = 'translateX(120%)'; t.style.opacity = '0'; }, 3500); }
      }, 100);
    </script>
  <?php endif; ?>

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div>
        <div class="page-header-label">Users</div>
        <div class="page-header-title">Alumni Profile</div>
      </div>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
      <a href="users.php" class="btn-back">
        <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to List
      </a>
    </div>
  </div>

  <!-- Profile Hero -->
  <div class="profile-hero">
    <?php if ($hasImg): ?>
      <div class="profile-avatar"><img src="<?= $img ?>" alt="<?= htmlspecialchars($user['full_name']) ?>"></div>
    <?php else: ?>
      <div class="profile-avatar-init"><?= $initials ?></div>
    <?php endif; ?>
    <div class="profile-info">
      <h2><?= htmlspecialchars($user['full_name']) ?></h2>
      <div class="profile-meta">
        <span class="profile-meta-item">
          <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 7l10 7 10-7"/></svg>
          <?= htmlspecialchars($user['email']) ?>
        </span>
        <?php if (!empty($user['phone'])): ?>
        <span class="profile-meta-item">
          <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
          <?= htmlspecialchars($user['phone']) ?>
        </span>
        <?php endif; ?>
        <span class="profile-meta-item">
          <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          Batch <?= $user['batch_year'] ?>
        </span>
        <?php
          $statusVal = $user['verified_status'];
          $statusLabel = 'Pending Email';
          $statusClass = 'not-verified';
          if ($statusVal == 1) {
              $statusLabel = 'Approved';
              $statusClass = 'verified';
          } elseif ($statusVal == 2) {
              $statusLabel = 'Pending Approval';
              $statusClass = 'pending-approval';
          } elseif ($statusVal == 3) {
              $statusLabel = 'Rejected';
              $statusClass = 'rejected';
          }
        ?>
        <span class="status-pill <?= $statusClass ?>">
          <?php if ($statusVal == 1): ?>
            <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <?php else: ?>
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <?php endif; ?>
          <?= $statusLabel ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Spotlight Form -->
  <?php if ($user['verified_status'] == 1): ?>
  <div style="margin-bottom: 1.5rem;">
    <form method="POST" action="users_list_view.php?id=<?= $user['id'] ?>" style="margin: 0; display:flex; align-items:center; justify-content:space-between; background: var(--surface); padding: 1.25rem 2rem; border-radius: var(--r); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
      <div style="display:flex; align-items:center; gap:16px;">
        <label style="font-size: 0.85rem; font-weight: 800; color: var(--text-2); text-transform: uppercase; letter-spacing: 0.05em;">Spotlight Order (0 for none):</label>
        <input type="number" name="showcase_order" value="<?= $user['showcase_order'] ?>" style="width: 80px; height: 42px; border: 1.5px solid var(--border); border-radius: 8px; font-size: 1.1rem; font-weight: 700; text-align: center; outline: none; transition: border-color 0.15s; color: var(--text);" min="0">
      </div>
      <div>
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <input type="hidden" name="update_showcase_order" value="1">
        <button type="submit" style="background: var(--blue); color: white; border: none; border-radius: 8px; height: 42px; padding: 0 24px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background 0.15s;">Save Settings</button>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <!-- Info Panels -->
  <div class="info-grid">

    <!-- Personal Info -->
    <div class="info-panel">
      <div class="info-head">
        <div class="info-head-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <span class="info-head-title">Personal Information</span>
      </div>
      <table class="info-table">
        <tr><th>Full Name</th><td><?= htmlspecialchars($user['full_name']) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
        <tr><th>Phone</th><td><?= htmlspecialchars($user['phone']) ?></td></tr>
        <tr><th>Gender</th><td><?= htmlspecialchars($user['gender']) ?></td></tr>
        <tr><th>Date of Birth</th><td><?= !empty($user['dob']) ? date("d M Y", strtotime($user['dob'])) : '—' ?></td></tr>
        <tr><th>Batch Year</th><td><?= $user['batch_year'] ?></td></tr>
        <tr><th>Registered</th><td><?= date("d M Y, h:i A", strtotime($user['created_at'])) ?></td></tr>
      </table>
    </div>

    <!-- Professional Info -->
    <div class="info-panel">
      <div class="info-head">
        <div class="info-head-icon"><svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg></div>
        <span class="info-head-title">Professional Information</span>
      </div>
      <table class="info-table">
        <tr><th>Current Occupation</th><td><?= htmlspecialchars($user['Current Occupation'] ?? '') ?: '—' ?></td></tr>
        <tr><th>Designation</th><td><?= htmlspecialchars($user['Designation'] ?? '') ?: '—' ?></td></tr>
        <tr><th>Company / Organization Name</th><td><?= htmlspecialchars($user['Company / Organization Name'] ?? '') ?: '—' ?></td></tr>
        <tr><th>Industry</th><td><?= htmlspecialchars($user['Industry'] ?? '') ?: '—' ?></td></tr>
        <tr><th>Work Experience</th><td><?= !empty($user['Work Experience']) ? $user['Work Experience'].' years' : '—' ?></td></tr>
        <tr><th>City</th><td><?= htmlspecialchars($user['City'] ?? '') ?: '—' ?></td></tr>
        <tr><th>Country</th><td><?= htmlspecialchars($user['country_name'] ?? $user['country'] ?? '') ?: '—' ?></td></tr>
        <tr><th>Current Address</th><td><?= htmlspecialchars($user['current_address'] ?? '') ?: '—' ?></td></tr>
      </table>
    </div>

    <!-- Education -->
    <div class="info-panel">
      <div class="info-head">
        <div class="info-head-icon"><svg viewBox="0 0 24 24"><path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12.5v4.5c0 2 2.686 3.5 6 3.5s6-1.5 6-3.5v-4.5"/></svg></div>
        <span class="info-head-title">Education</span>
      </div>
      <table class="info-table">
        <tr><th>Qualification</th><td><?= htmlspecialchars($user['education_qualification'] ?? '') ?: '—' ?></td></tr>
        <tr><th>College / University</th><td><?= htmlspecialchars($user['college_university'] ?? '') ?: '—' ?></td></tr>
        <tr><th>Year of Completion</th><td><?= htmlspecialchars($user['education_year'] ?? '') ?: '—' ?></td></tr>
      </table>
    </div>

    <!-- Social Profiles -->
    <div class="info-panel">
      <div class="info-head">
        <div class="info-head-icon"><svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg></div>
        <span class="info-head-title">Social Profiles</span>
      </div>
      <?php
        $hasLinkedin = !empty($user['linkedin']);
        $hasFacebook = !empty($user['facebook']);
        $hasInstagram = !empty($user['instagram']);
        $hasSocial = $hasLinkedin || $hasFacebook || $hasInstagram;
      ?>
      <?php if ($hasSocial): ?>
      <div class="social-links">
        <?php if ($hasLinkedin): ?>
        <a href="<?= htmlspecialchars($user['linkedin']) ?>" target="_blank" class="social-link">
          <svg viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
          LinkedIn
        </a>
        <?php endif; ?>
        <?php if ($hasFacebook): ?>
        <a href="<?= htmlspecialchars($user['facebook']) ?>" target="_blank" class="social-link">
          <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
          Facebook
        </a>
        <?php endif; ?>
        <?php if ($hasInstagram): ?>
        <a href="<?= htmlspecialchars($user['instagram']) ?>" target="_blank" class="social-link">
          <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          Instagram
        </a>
        <?php endif; ?>
      </div>
      <?php else: ?>
        <div class="social-empty">No social profiles linked.</div>
      <?php endif; ?>
    </div>

    <!-- Bio -->
    <?php if (!empty($user['bio'])): ?>
    <div class="info-panel full">
      <div class="info-head">
        <div class="info-head-icon"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
        <span class="info-head-title">Bio</span>
      </div>
      <div class="bio-box"><?= nl2br(htmlspecialchars($user['bio'])) ?></div>
    </div>
    <?php endif; ?>

  </div>

</div>

</body>
</html>
