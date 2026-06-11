<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('../db_connect.php'); ?>

<?php
// Fetch pending counts and recent items for notifications
// 1. Pending Users
$pendingUsersResult = $conn->query("SELECT id, full_name, created_at FROM alumni_register WHERE verified_status = 2 ORDER BY created_at DESC LIMIT 5");
$pendingUsersCount = $conn->query("SELECT COUNT(*) AS total FROM alumni_register WHERE verified_status = 2")->fetch_assoc()['total'];

// 2. Pending Profiles
$pendingProfilesResult = $conn->query("SELECT p.id, r.full_name, p.created_at FROM profile_update_requests p JOIN alumni_register r ON p.alumni_id = r.id WHERE p.status = 0 ORDER BY p.created_at DESC LIMIT 5");
$pendingProfilesCount = $conn->query("SELECT COUNT(*) AS total FROM profile_update_requests WHERE status = 0")->fetch_assoc()['total'];

// 3. Pending Events
$pendingEventsResult = $conn->query("SELECT id, full_name, start_datetime FROM event_requests WHERE event_status = 0 ORDER BY created_at DESC LIMIT 5");
$pendingEventsCount = $conn->query("SELECT COUNT(*) AS total FROM event_requests WHERE event_status = 0")->fetch_assoc()['total'];

$totalPending = $pendingUsersCount + $pendingProfilesCount + $pendingEventsCount;
?>

<div class="dash-main">

  <!-- Page Header -->
  <div class="page-header" style="align-items: flex-end; position: relative;">
    <div>
      <div class="page-header-label">Welcome</div>
      <div class="page-header-title" style="font-size: 2.3rem;"><?php echo $_SESSION['admin_name']; ?> 👋</div>
    </div>
    
    <div class="header-right-actions" style="display:flex; align-items:flex-end; gap: 1.5rem;">
      <!-- Notification System -->
      <div class="notif-wrapper" style="position: relative;">
        <button id="notifToggle" class="notif-btn" aria-label="Notifications">
          <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
          <?php if ($totalPending > 0): ?>
            <span class="notif-badge"><?= $totalPending > 99 ? '99+' : $totalPending ?></span>
          <?php endif; ?>
        </button>

        <!-- Dropdown Menu -->
        <div id="notifDropdown" class="notif-dropdown">
          <div class="notif-header">
            <h3>Notifications</h3>
            <?php if ($totalPending > 0): ?>
              <span class="notif-count"><?= $totalPending ?> New</span>
            <?php endif; ?>
          </div>
          
          <div class="notif-tabs">
            <button class="notif-tab active" data-target="notif-users">Users (<?= $pendingUsersCount ?>)</button>
            <button class="notif-tab" data-target="notif-profiles">Profiles (<?= $pendingProfilesCount ?>)</button>
            <button class="notif-tab" data-target="notif-events">Events (<?= $pendingEventsCount ?>)</button>
          </div>

          <div class="notif-body">
            <!-- Users Tab -->
            <div id="notif-users" class="notif-pane active">
              <?php if ($pendingUsersCount > 0): ?>
                <?php while ($u = $pendingUsersResult->fetch_assoc()): ?>
                  <a href="users.php" class="notif-item">
                    <div class="notif-icon" style="background:var(--blue-light); color:var(--blue);"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                    <div class="notif-content">
                      <div class="notif-title">New Registration</div>
                      <div class="notif-desc"><?= htmlspecialchars($u['full_name']) ?></div>
                      <div class="notif-time"><?= date("M d, H:i", strtotime($u['created_at'])) ?></div>
                    </div>
                  </a>
                <?php endwhile; ?>
                <?php if ($pendingUsersCount > 5): ?>
                  <a href="users.php" class="notif-view-all">View all <?= $pendingUsersCount ?> users</a>
                <?php endif; ?>
              <?php else: ?>
                <div class="notif-empty">No pending user registrations.</div>
              <?php endif; ?>
            </div>

            <!-- Profiles Tab -->
            <div id="notif-profiles" class="notif-pane">
              <?php if ($pendingProfilesCount > 0): ?>
                <?php while ($p = $pendingProfilesResult->fetch_assoc()): ?>
                  <a href="profile_requests.php" class="notif-item">
                    <div class="notif-icon" style="background:rgba(211,84,0,0.1); color:var(--amber);"><svg viewBox="0 0 24 24" width="16" height="16"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div>
                    <div class="notif-content">
                      <div class="notif-title">Profile Update</div>
                      <div class="notif-desc"><?= htmlspecialchars($p['full_name']) ?></div>
                      <div class="notif-time"><?= date("M d, H:i", strtotime($p['created_at'])) ?></div>
                    </div>
                  </a>
                <?php endwhile; ?>
                <?php if ($pendingProfilesCount > 5): ?>
                  <a href="profile_requests.php" class="notif-view-all">View all <?= $pendingProfilesCount ?> requests</a>
                <?php endif; ?>
              <?php else: ?>
                <div class="notif-empty">No pending profile updates.</div>
              <?php endif; ?>
            </div>

            <!-- Events Tab -->
            <div id="notif-events" class="notif-pane">
              <?php if ($pendingEventsCount > 0): ?>
                <?php while ($e = $pendingEventsResult->fetch_assoc()): ?>
                  <a href="event_request.php" class="notif-item">
                    <div class="notif-icon" style="background:rgba(10,122,90,0.1); color:var(--green);"><svg viewBox="0 0 24 24" width="16" height="16"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                    <div class="notif-content">
                      <div class="notif-title">Event Request</div>
                      <div class="notif-desc"><?= htmlspecialchars($e['full_name']) ?></div>
                      <div class="notif-time">For: <?= date("M d", strtotime($e['start_datetime'])) ?></div>
                    </div>
                  </a>
                <?php endwhile; ?>
                <?php if ($pendingEventsCount > 5): ?>
                  <a href="event_request.php" class="notif-view-all">View all <?= $pendingEventsCount ?> requests</a>
                <?php endif; ?>
              <?php else: ?>
                <div class="notif-empty">No pending event requests.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="header-time-wrap">
        <div id="liveTime" class="dash-clock">00:00:00</div>
        <div id="liveDate" class="dash-date">Loading date...</div>
      </div>
    </div>
  </div>

  <script>
    function updateDashTime() {
      const now = new Date();
      const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
      const dateStr = now.toLocaleDateString('en-US', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
      document.getElementById('liveTime').textContent = timeStr;
      document.getElementById('liveDate').textContent = dateStr;
    }
    setInterval(updateDashTime, 1000);
    updateDashTime();
  </script>

  <style>
    /* Dashboard specific grid */
    .header-time-wrap { text-align: right; }
    .dash-clock { font-size: 1.5rem; font-weight: 800; color: var(--blue); letter-spacing: -0.02em; line-height: 1; margin-bottom: 4px; font-variant-numeric: tabular-nums; }
    .dash-date { font-size: 0.88rem; color: var(--text-3); font-weight: 500; }
    @media (max-width: 600px) { .page-header { flex-direction: column; align-items: flex-start !important; gap: 15px; } .header-time-wrap { text-align: left; } }
    
    .section-label { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--text-3); margin-bottom: 0.85rem; margin-top: 0.25rem; }
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem; }
    @media (max-width: 900px) { .stat-grid { grid-template-columns: 1fr; } }
    .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r); padding: 1.35rem 1.5rem; display: flex; align-items: flex-start; gap: 1rem; box-shadow: var(--shadow-sm); position: relative; overflow: hidden; transition: all 0.18s; }
    .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
    .stat-card.c-blue::before { background: var(--blue); }
    .stat-card.c-green::before { background: var(--green); }
    .stat-card.c-amber::before { background: var(--amber); }
    .stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-card.c-blue  .stat-icon { background: rgba(26,86,160,0.10); }
    .stat-card.c-green .stat-icon { background: rgba(10,122,90,0.10); }
    .stat-card.c-amber .stat-icon { background: rgba(211,84,0,0.10); }
    .stat-icon svg { width: 18px; height: 18px; stroke-width: 1.75; fill: none; }
    .stat-card.c-blue  .stat-icon svg { stroke: var(--blue); }
    .stat-card.c-green .stat-icon svg { stroke: var(--green); }
    .stat-card.c-amber .stat-icon svg { stroke: var(--amber); }
    .stat-info { flex: 1; }
    .stat-label { font-size: 0.87rem; font-weight: 500; color: var(--text-2); margin-bottom: 4px; }
    .stat-value { font-size: 2.4rem; font-weight: 700; color: var(--text); line-height: 1; }
    
    .panel-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 2.5rem; }
    @media (max-width: 1100px) { .panel-grid { grid-template-columns: 1fr; } }
    
    .date-badge { display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 0.82rem; font-weight: 600; background: rgba(26,86,160,0.08); color: var(--blue); }
    .badge-pending { display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background: rgba(211,84,0,0.10); color: var(--amber); }

    /* Notification System CSS */
    .notif-btn { position: relative; width: 44px; height: 44px; border-radius: 50%; background: var(--surface); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-2); transition: all 0.2s; box-shadow: var(--shadow-sm); margin-bottom: 5px; }
    .notif-btn:hover { color: var(--blue); border-color: var(--blue-light); background: #f8fafc; }
    .notif-badge { position: absolute; top: -3px; right: -3px; background: var(--red); color: white; font-size: 0.65rem; font-weight: 800; min-width: 18px; height: 18px; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 0 4px; border: 2px solid var(--surface-bg, #f4f7fb); }
    
    .notif-dropdown { position: absolute; top: calc(100% + 10px); right: 0; width: 340px; background: var(--surface); border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid var(--border); z-index: 1000; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1); overflow: hidden; display: flex; flex-direction: column; }
    .notif-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); }
    
    .notif-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #fafbfc; }
    .notif-header h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: var(--text); }
    .notif-count { font-size: 0.7rem; font-weight: 700; color: var(--blue); background: var(--blue-light); padding: 2px 8px; border-radius: 12px; }
    
    .notif-tabs { display: flex; border-bottom: 1px solid var(--border); background: var(--surface); }
    .notif-tab { flex: 1; padding: 0.75rem 0; text-align: center; font-size: 0.78rem; font-weight: 700; color: var(--text-3); background: transparent; border: none; border-bottom: 2px solid transparent; cursor: pointer; transition: all 0.2s; }
    .notif-tab:hover { color: var(--text-2); background: #fafbfc; }
    .notif-tab.active { color: var(--blue); border-bottom-color: var(--blue); }
    
    .notif-body { max-height: 380px; overflow-y: auto; background: var(--surface); }
    .notif-body::-webkit-scrollbar { width: 6px; }
    .notif-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    
    .notif-pane { display: none; flex-direction: column; }
    .notif-pane.active { display: flex; }
    
    .notif-item { display: flex; gap: 12px; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); text-decoration: none; transition: background 0.2s; }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: #f8fafc; }
    
    .notif-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .notif-icon svg { stroke: currentColor; fill: none; stroke-width: 2; }
    
    .notif-content { flex: 1; display: flex; flex-direction: column; gap: 3px; overflow: hidden; }
    .notif-title { font-size: 0.8rem; font-weight: 700; color: var(--text); }
    .notif-desc { font-size: 0.85rem; color: var(--text-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .notif-time { font-size: 0.7rem; color: var(--text-3); font-weight: 500; }
    
    .notif-empty { padding: 3rem 1.5rem; text-align: center; color: var(--text-3); font-size: 0.85rem; }
    .notif-view-all { display: block; text-align: center; padding: 0.85rem; background: #fafbfc; font-size: 0.8rem; font-weight: 700; color: var(--blue); text-decoration: none; border-top: 1px solid var(--border); transition: background 0.2s; }
    .notif-view-all:hover { background: var(--blue-light); }
  </style>

  <!-- Overview -->
  <div class="section-label">Overview</div>
  <div class="stat-grid">
    <?php $usersCount = $conn->query("SELECT COUNT(*) AS total FROM alumni_register WHERE verified_status = 1")->fetch_assoc()['total']; ?>
    <div class="stat-card c-blue">
      <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
      <div class="stat-info"><div class="stat-label">Verified Alumni</div><div class="stat-value"><?= $usersCount ?></div></div>
    </div>

    <?php
    $birthdays = $conn->query("SELECT id, full_name, dob, CASE WHEN DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(dob, '%m-%d'))) < CURDATE() THEN DATE(CONCAT(YEAR(CURDATE()) + 1, '-', DATE_FORMAT(dob, '%m-%d'))) ELSE DATE(CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(dob, '%m-%d'))) END AS next_birthday FROM alumni_register WHERE verified_status = 1 HAVING next_birthday BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY next_birthday");
    $birthdayCount = $birthdays->num_rows;
    ?>
    <div class="stat-card c-green">
      <div class="stat-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M12 3v2M8 5l1.5 1.5M16 5l-1.5 1.5"/></svg></div>
      <div class="stat-info"><div class="stat-label">Birthdays (30 Days)</div><div class="stat-value"><?= $birthdayCount ?></div></div>
    </div>

    <?php $eventCount = $conn->query("SELECT COUNT(*) AS pending FROM event_requests WHERE event_status = 0")->fetch_assoc()['pending']; ?>
    <div class="stat-card c-amber">
      <div class="stat-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4M16 2v4M12 14h4M12 18h2"/></svg></div>
      <div class="stat-info"><div class="stat-label">Event Requests</div><div class="stat-value"><?= $eventCount ?></div></div>
    </div>
  </div>

  <!-- Activity -->
  <div class="section-label">Activity</div>
  <div class="panel-grid">
    <div class="panel">
      <div class="panel-head">
        <div class="panel-head-left">
          <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
          <span class="panel-head-title">Upcoming Birthdays</span>
        </div>
      </div>
      <div class="panel-body">
        <?php if ($birthdayCount > 0): ?>
          <table class="dash-table">
            <thead><tr><th>Name</th><th>Date</th></tr></thead>
            <tbody>
              <?php while ($b = $birthdays->fetch_assoc()): ?>
                <tr>
                  <td data-label="Name"><a href="users_list_view.php?id=<?= $b['id'] ?>" style="color:var(--blue); font-weight:600; text-decoration:none;"><?= htmlspecialchars($b['full_name']) ?></a></td>
                  <td data-label="Date"><span class="date-badge"><?= date("d M", strtotime($b['dob'])) ?></span></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div style="padding:3rem 1.5rem; text-align:center; color:var(--text-3);">No upcoming birthdays.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head">
        <div class="panel-head-left">
          <div class="panel-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4"/></svg></div>
          <span class="panel-head-title">Pending Events</span>
        </div>
      </div>
      <div class="panel-body">
        <?php $eventRequests = $conn->query("SELECT * FROM event_requests WHERE event_status = 0 ORDER BY created_at DESC LIMIT 5"); ?>
        <?php if ($eventRequests->num_rows > 0): ?>
          <table class="dash-table">
            <thead><tr><th>Name</th><th>Start</th><th>Status</th></tr></thead>
            <tbody>
              <?php while ($e = $eventRequests->fetch_assoc()): ?>
                <tr>
                  <td data-label="Name"><?= htmlspecialchars($e['full_name']) ?></td>
                  <td data-label="Start"><?= date("d M, H:i", strtotime($e['start_datetime'])) ?></td>
                  <td data-label="Status"><span class="badge-pending">Pending</span></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div style="padding:3rem 1.5rem; text-align:center; color:var(--text-3);">No pending requests.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  // Notification Dropdown Logic
  const notifBtn = document.getElementById('notifToggle');
  const notifDropdown = document.getElementById('notifDropdown');
  const notifTabs = document.querySelectorAll('.notif-tab');
  const notifPanes = document.querySelectorAll('.notif-pane');

  if(notifBtn && notifDropdown) {
    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDropdown.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if(!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
        notifDropdown.classList.remove('show');
      }
    });

    notifDropdown.addEventListener('click', (e) => {
      e.stopPropagation();
    });
  }

  notifTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Remove active from all tabs and panes
      notifTabs.forEach(t => t.classList.remove('active'));
      notifPanes.forEach(p => p.classList.remove('active'));
      
      // Add active to clicked tab and corresponding pane
      tab.classList.add('active');
      const targetId = tab.getAttribute('data-target');
      document.getElementById(targetId).classList.add('active');
    });
  });
</script>
</body></html>