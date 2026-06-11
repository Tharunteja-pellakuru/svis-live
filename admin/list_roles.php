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

  <style>
    .cell-role { display: flex; align-items: center; gap: 10px; }
    .role-icon { width: 34px; height: 34px; border-radius: 9px; background: var(--blue-light); color: var(--blue); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .perm-badges { display: flex; flex-wrap: wrap; gap: 6px; }
    .perm-badge { 
      display: inline-flex; align-items: center; justify-content: center;
      padding: 4px 12px; border-radius: 8px; 
      font-size: 0.72rem; font-weight: 800; 
      text-transform: uppercase; letter-spacing: 0.03em;
      border: 1.5px solid transparent;
      box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    .p-view { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
    .p-add { background: #eff6ff; color: #2563eb; border-color: #dbeafe; }
    .p-edit { background: #fffbeb; color: #d97706; border-color: #fef3c7; }
    .p-delete { background: #fef2f2; color: #dc2626; border-color: #fee2e2; }
  </style>

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
          <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
      </div>
      <div>
        <div class="page-header-label">Administration</div>
        <div class="page-header-title">Admin Roles</div>
      </div>
    </div>
    <a href="create_role.php" class="btn-primary-dash">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Create Role
    </a>
  </div>

  <?php
    $roles = $conn->query("SELECT * FROM admin_roles ORDER BY id DESC");
    $total = $roles->num_rows;
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
        <span class="panel-head-title">Permissions & Access</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $total ?> Roles</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="roleSearch" class="search-input" placeholder="Search role name..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <table class="dash-table" id="rolesTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Role Name</th>
          <th>Permissions</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $roles->fetch_assoc()):
          $perms = json_decode($row['permissions'], true);
        ?>
        <tr>
          <td data-label="ID"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
          <td data-label="Role">
            <div class="cell-role">
              <div class="role-icon"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div>
              <span class="role-name-text" style="font-weight:700; color:var(--text);"><?= htmlspecialchars($row['role_name']) ?></span>
            </div>
          </td>
          <td data-label="Permissions">
            <div class="perm-badges">
              <span class="perm-badge p-view">View</span>
              <?php if (!empty($perms['add'])): ?><span class="perm-badge p-add">Add</span><?php endif; ?>
              <?php if (!empty($perms['edit'])): ?><span class="perm-badge p-edit">Edit</span><?php endif; ?>
              <?php if (!empty($perms['delete'])): ?><span class="perm-badge p-delete">Delete</span><?php endif; ?>
            </div>
          </td>
          <td data-label="Created"><span style="font-size:0.85rem; color:var(--text-3);"><?= date("d M Y", strtotime($row['created_at'])) ?></span></td>
          <td data-label="Actions">
            <div class="actions">
              <a href="edit_role.php?id=<?= $row['id'] ?>" class="btn-action-sm btn-edit-sm" data-tooltip="Edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
              <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDelete(this)" data-url="delete_role.php?id=<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['role_name']) ?>" data-tooltip="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></button>
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
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Delete Role</h3>
    <p style="font-size:0.92rem; color:var(--text-2); line-height:1.5; margin-bottom:1.5rem;">Are you sure you want to remove <strong id="delName"></strong>? Admins assigned to this role might lose access.</p>
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

  // ── Live search for roles ──────────────────────────────────
  const roleSearch = document.getElementById('roleSearch');
  const roleRows = document.querySelectorAll('#rolesTable tbody tr');
  roleSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    roleRows.forEach(function(row) {
      const name = (row.querySelector('.role-name-text') || {}).textContent || '';
      row.style.display = name.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
</body></html>
