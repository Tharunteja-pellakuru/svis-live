<?php
// Session already started by header.php - don't start again
$role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : '';

// Auto-detect active page from current filename
$current = basename($_SERVER['PHP_SELF']);

// Helper: returns ' active' class if the given file matches current page
if (!function_exists('isActive')) {
    function isActive(string $file): string {
        global $current;
        return $current === $file ? ' active' : '';
    }
}
?>

<!-- lato font for sidebar -->
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

<style>
  :root {
    --blue:       #1a56a0;
    --blue-dark:  #0f3566;
    --blue-line:  rgba(255,255,255,0.08);
    --text-dim:   rgba(255,255,255,0.70);
    --text-muted: rgba(255,255,255,0.45);
    --hover-bg:   rgba(255,255,255,0.07);
    --active-bg:  rgba(255,255,255,0.15);
    --active-bar: rgba(255,255,255,0.9);
    --sidebar-w:  255px;
  }

  /* ── Sidebar shell ── */
  .svis-sidebar {
    display: flex;
    flex-direction: column;
    width: var(--sidebar-w);
    height: 100vh;
    position: sticky;
    top: 0;
    background: linear-gradient(180deg, var(--blue-dark) 0%, var(--blue) 100%);
    font-family: 'Lato', sans-serif;
    flex-shrink: 0;
    z-index: 1110;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Dot texture */
  .svis-sidebar::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.055) 1px, transparent 1px);
    background-size: 22px 22px;
    pointer-events: none;
    z-index: 0;
  }

  /* ── Brand header ── */
  .sb-brand {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.85rem 1.25rem 0.7rem;
    border-bottom: 1px solid var(--blue-line);
    text-decoration: none;
  }

  .sb-brand-icon {
    width: 40px;
    height: 40px;
    background:  white;
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .sb-brand-icon svg {
    width: 22px; height: 22px;
    stroke: white; fill: none;
    stroke-width: 1.75;
  }

  .sb-brand-text { display: flex; flex-direction: column; line-height: 1; }
  .sb-brand-name { font-size: 1.15rem; font-weight: 900; color: white; letter-spacing: 0.01em; }
  .sb-brand-tag { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin-top: 4px; }

  /* ── Scrollable nav ── */
  .sb-nav {
    position: relative; z-index: 1;
    flex: 1; overflow-y: auto;
    padding: 0.5rem 0.75rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.1) transparent;
  }
  .sb-nav::-webkit-scrollbar { width: 4px; }
  .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

  .sb-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.13em; text-transform: uppercase; color: var(--text-muted); padding: 0.85rem 0.75rem 0.3rem; }
  .sb-divider { height: 1px; background: var(--blue-line); margin: 0.3rem 0.75rem; }

  /* ── Nav item ── */
  .sb-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.35rem 0.85rem;
    border-radius: 9px;
    text-decoration: none;
    color: var(--text-dim);
    font-size: 0.92rem;
    font-weight: 400;
    transition: all 0.2s;
    margin-bottom: 5px;
  }

  .sb-item:hover { background: var(--hover-bg); color: white; text-decoration: none; }
  .sb-item.active { background: var(--active-bg); color: white; font-weight: 700; }
  .sb-item.active::before { content: ''; position: absolute; left: 0; top: 20%; bottom: 20%; width: 3px; background: var(--active-bar); border-radius: 0 3px 3px 0; }

  .sb-icon {
    width: 26px; height: 26px;
    border-radius: 8px;
    background: rgba(255,255,255,0.07);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background 0.2s;
  }
  .sb-item:hover  .sb-icon { background: rgba(255,255,255,0.12); }
  .sb-item.active .sb-icon { background: rgba(255,255,255,0.2);  }

  .sb-icon svg { width: 14px; height: 14px; stroke: rgba(255,255,255,0.6); fill: none; stroke-width: 1.75; transition: stroke 0.2s; }
  .sb-item:hover  .sb-icon svg, .sb-item.active .sb-icon svg { stroke: white; }

  /* ── Logout footer ── */
  .sb-footer { position: relative; z-index: 1; padding: 0.5rem 0.75rem; border-top: 1px solid var(--blue-line); }
  .sb-logout { display: flex; align-items: center; gap: 10px; padding: 0.45rem 0.85rem; border-radius: 9px; text-decoration: none; color: rgba(255,255,255,0.45); font-size: 0.92rem; transition: all 0.2s; }
  .sb-logout:hover { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.85); text-decoration: none; }

  /* ── Mobile Responsive ── */
  @media (max-width: 1024px) {
    .svis-sidebar {
      position: fixed;
      left: 0;
      transform: translateX(-100%);
    }
    .svis-sidebar.open {
      transform: translateX(0);
    }
    /* Simple mobile toggle backdrop */
    .sb-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 1100;
      display: none;
    }
    .svis-sidebar.open + .sb-backdrop { display: block; }
  }

  /* Mobile Top Bar */
  .mobile-top-bar {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0;
    height: 60px;
    background: var(--blue-dark);
    z-index: 1000;
    padding: 0 1.25rem;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .mobile-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
  }

  .mobile-logo-text {
    color: white;
    font-weight: 900;
    font-size: 1.1rem;
    letter-spacing: -0.02em;
  }

  .mobile-nav-toggle {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.1);
    color: white;
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }

  @media (max-width: 1024px) { 
    .mobile-top-bar { display: flex; }
  }
</style>

<div class="mobile-top-bar">
  <a href="dashboard.php" class="mobile-logo">
    <div class="sb-brand-icon" style="width: 32px; height: 32px; border-radius: 8px; background-color:black; background: rgba(255,255,255,0.15); overflow: hidden;">
      <img src="../Logo/Logo.png" alt="SVIS Logo" style="width: 100%; height: 100%; object-fit: contain;"/>
    </div>
    <span class="mobile-logo-text">SVIS Alumni Portal</span>
  </a>
  <button class="mobile-nav-toggle" id="sidebarToggle" aria-label="Toggle Menu">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
  </button>
</div>

<div class="svis-sidebar" id="mainSidebar">

  <!-- Brand -->
  <a href="dashboard.php" class="sb-brand">
    <div class="sb-brand-icon" style="overflow: hidden;">
      <img src="../Logo/Logo.png" alt="SVIS Logo" style="width: 100%; height: 100%; object-fit: contain;"/>
    </div>
    <div class="sb-brand-text">
      <span class="sb-brand-name">SVIS Alumni</span>
      <span class="sb-brand-tag">Admin Panel</span>
    </div>
  </a>

  <!-- Nav -->
  <nav class="sb-nav">

    <!-- General -->
    <div class="sb-label">General</div>
    <a href="dashboard.php" class="sb-item<?= isActive('dashboard.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg></div>
      Dashboard
    </a>
    <a href="home_scroll_list.php" class="sb-item<?= isActive('home_scroll_list.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M8 12h8M12 9v6"/></svg></div>
      Home Scroll Images
    </a>

    <div class="sb-divider"></div>

    <!-- Events -->
    <div class="sb-label">Events</div>
    <a href="events_list.php" class="sb-item<?= isActive('events_list.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
      Events
    </a>

    <a href="event_request.php" class="sb-item<?= isActive('event_request.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4M16 2v4M12 14h4M12 18h2"/></svg></div>
      Event Requests
    </a>

    <a href="all_event_registrations.php" class="sb-item<?= isActive('all_event_registrations.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
      Event Registrations
    </a>

    <div class="sb-divider"></div>

    <!-- Media -->
    <div class="sb-label">Media</div>
    <a href="gallery_category_list.php" class="sb-item<?= isActive('gallery_category_list.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg></div>
      Gallery Categories
    </a>
    <a href="gallery_list.php" class="sb-item<?= isActive('gallery_list.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg></div>
      Images
    </a>
    <a href="videos_list.php" class="sb-item<?= isActive('videos_list.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg></div>
      Videos
    </a>

    <div class="sb-divider"></div>

    <!-- Users -->
    <div class="sb-label">Users</div>
    <a href="users.php" class="sb-item<?= isActive('users.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
      Registered Users
    </a>
    <a href="profile_requests.php" class="sb-item<?= isActive('profile_requests.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M18 8l2 2 4-4"/></svg></div>
      Profile Update Requests
    </a>

    <div class="sb-divider"></div>
    <div class="sb-label">Administration</div>
    <a href="change_password.php" class="sb-item<?= isActive('change_password.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg></div>
      Change Password
    </a>
    <?php if ($role === 'SuperAdmin'): ?>
    <a href="list_roles.php" class="sb-item<?= isActive('list_roles.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg></div>
      Admin Roles
    </a>
    <a href="admin_list.php" class="sb-item<?= isActive('admin_list.php') ?>">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4h-4"/><circle cx="9" cy="7" r="4"/><path d="M16 11l2 2 4-4"/></svg></div>
      Admin Users
    </a>
    <?php endif; ?>

  </nav>

  <!-- Logout -->
  <div class="sb-footer">
    <a href="logout.php" class="sb-logout">
      <div class="sb-icon"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></div>
      Logout
    </a>
  </div>
</div>

<!-- Backdrop -->
<div class="sb-backdrop" id="sidebarBackdrop"></div>

<script>
  (function() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (toggle && sidebar) {
      toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        document.body.classList.toggle('sb-open');
      });
    }

    if (backdrop) {
      backdrop.addEventListener('click', () => {
        sidebar.classList.remove('open');
        document.body.classList.remove('sb-open');
      });
    }
  })();
</script>