<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'SuperAdmin') {
    $_SESSION['flash_msg'] = "Access Denied: SuperAdmin privilege required.";
    $_SESSION['flash_type'] = 'error';
    header("Location: dashboard.php");
    exit();
}
include('header.php');
include('sidebar.php');
include('../db_connect.php');
?>

<?php
$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>

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
        <svg viewBox="0 0 24 24">
          <path d="M20 21v-2a4 4 0 00-4-4h-4"/><circle cx="9" cy="7" r="4"/>
          <path d="M16 11l2 2 4-4"/>
        </svg>
      </div>
      <div>
        <div class="page-header-label">Administration</div>
        <div class="page-header-title">Admin Users</div>
      </div>
    </div>

    <?php if ($_SESSION['admin_role'] === "SuperAdmin"): ?>
    <a href="add_admin.php" class="btn-primary-dash">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Admin
    </a>
    <?php endif; ?>
  </div>

  <style>
    .cell-user { display: flex; align-items: center; gap: 10px; }
    .avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--blue-light); color: var(--blue); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.75rem; flex-shrink: 0; }
    .role-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 11px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
    .role-superadmin { background: rgba(26,86,160,0.1); color: var(--blue); }
    .role-manager { background: var(--green-bg); color: var(--green); }
    .role-default { background: var(--surface-alt); color: var(--text-3); border: 1px solid var(--border-soft); }
    .actions { display: flex; gap: 6px; }
    .btn-edit-sm, .btn-delete-sm { display: inline-flex; align-items: center; gap: 4px; padding: 0 10px; height: 30px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-decoration: none; border: 1.5px solid; transition: all 0.1s; }
    .btn-edit-sm { background: var(--amber-bg); color: var(--amber); border-color: var(--amber-border); }
    .btn-edit-sm:hover { background: #fef3c7; border-color: #fbbf24; color: #92400e; }
    .btn-delete-sm { background: var(--red-bg); color: var(--red); border-color: #f5c6c2; }
    .btn-delete-sm:hover { background: #fee2e2; border-color: #fca5a5; color: #991b1b; }
  </style>

  <?php
  $result = $conn->query("SELECT a.*, r.role_name FROM admin_users a LEFT JOIN admin_roles r ON r.id = a.role_id ORDER BY a.id DESC");
  $total = $result->num_rows;
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
        <span class="panel-head-title">Administrative Team</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $total ?> Users</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="adminSearch" class="search-input" placeholder="Search by name or email..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <table class="dash-table" id="adminTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()):
          $parts = explode(' ', trim($row['name']));
          $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
          $roleSlug = strtolower(str_replace(' ', '', $row['role_name'] ?? ''));
          $roleClass = ($roleSlug === 'superadmin') ? 'role-superadmin' : (($roleSlug === 'manager') ? 'role-manager' : 'role-default');
        ?>
        <tr>
          <td data-label="ID"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
          <td data-label="Name">
            <div class="cell-user">
              <div class="avatar"><?= $initials ?></div>
              <span class="admin-name-text" style="font-weight:700; color:var(--text);"><?= htmlspecialchars($row['name']) ?></span>
            </div>
          </td>
          <td data-label="Email"><span class="admin-email-text" style="font-size:0.87rem; color:var(--text-2);"><?= htmlspecialchars($row['email']) ?></span></td>
          <td data-label="Role"><span class="role-badge <?= $roleClass ?>"><?= htmlspecialchars($row['role_name'] ?? 'N/A') ?></span></td>
          <td data-label="Actions">
            <div class="actions">
              <?php if ($_SESSION['admin_role'] === "SuperAdmin"): ?>
                <a href="edit_admin.php?id=<?= $row['id'] ?>" class="btn-action-sm btn-edit-sm" data-tooltip="Edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                <button type="button" class="btn-action-sm btn-delete-sm" data-url="delete_admin.php?id=<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['name']) ?>" onclick="confirmDelete(this)" data-tooltip="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></button>
              <?php else: ?>
                <span style="font-size:0.75rem; color:var(--text-3); font-style:italic;">No Access</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,37,69,0.4); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1.5rem;">
  <div style="background:var(--surface); max-width:400px; width:100%; padding:2.25rem; border-radius:20px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
    <div style="width:56px; height:56px; background:var(--red-bg); color:var(--red); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></div>
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Delete Admin</h3>
    <p style="font-size:0.92rem; color:var(--text-2); line-height:1.5; margin-bottom:1.5rem;">Are you sure you want to remove <strong id="delName"></strong>? This action cannot be undone.</p>
    <div style="display:flex; gap:10px;"><button onclick="closeModal()" style="flex:1; height:44px; border-radius:10px; font-weight:700; background:var(--surface-alt); border:1.5px solid var(--border); color:var(--text-2); cursor:pointer;">Cancel</button><a id="delConfirm" href="#" style="flex:1; height:44px; border-radius:10px; font-weight:700; background:var(--red); color:white; display:flex; align-items:center; justify-content:center; text-decoration:none;">Yes, Delete</a></div>
  </div>
</div>

<script>
  function confirmDelete(btn) {
    document.getElementById('delName').textContent = btn.dataset.name;
    document.getElementById('delConfirm').href = btn.dataset.url;
    document.getElementById('deleteModal').style.display = 'flex';
  }
  function closeModal() { document.getElementById('deleteModal').style.display = 'none'; }

  // ── Live search for admins ─────────────────────────────────
  const adminSearch = document.getElementById('adminSearch');
  const adminRows = document.querySelectorAll('#adminTable tbody tr');
  adminSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    adminRows.forEach(function(row) {
      const name = (row.querySelector('.admin-name-text') || {}).textContent || '';
      const email = (row.querySelector('.admin-email-text') || {}).textContent || '';
      row.style.display = (name.toLowerCase().includes(q) || email.toLowerCase().includes(q)) ? '' : 'none';
    });
  });
</script>
</body></html>