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

if (!isset($_GET['id'])) {
    header("Location: list_roles.php");
    exit();
}

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM admin_roles WHERE id = $id");
if ($res->num_rows === 0) {
    header("Location: list_roles.php");
    exit();
}

$role = $res->fetch_assoc();
$perms = json_decode($role['permissions'], true) ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role_name = trim($_POST['role_name']);
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    
    $perm_json = [];
    foreach(['add', 'edit', 'delete'] as $p) {
        $perm_json[$p] = in_array($p, $permissions);
    }
    $perm_str = json_encode($perm_json);

    $stmt = $conn->prepare("UPDATE admin_roles SET role_name = ?, permissions = ? WHERE id = ?");
    $stmt->bind_param("ssi", $role_name, $perm_str, $id);
    
    if ($stmt->execute()) {
        $_SESSION['flash_msg'] = "Role updated successfully.";
        $_SESSION['flash_type'] = "success";
        echo "<script>window.location.href='list_roles.php';</script>";
        exit();
    } else {
        $error = "Error updating role.";
    }
}
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </div>
      <div>
        <div class="page-header-label">Administration</div>
        <div class="page-header-title">Edit Role: <?= htmlspecialchars($role['role_name']) ?></div>
      </div>
    </div>
    <a href="list_roles.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to Roles
    </a>
  </div>

  <style>
    .field { margin-bottom: 1.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap input { width: 100%; height: 44px; padding: 0 14px; font-family: 'Lato', sans-serif; font-size: 0.92rem; background: var(--surface-alt); border: 1.5px solid var(--border); border-radius: 9px; outline: none; transition: all 0.18s; }
    .input-wrap input:focus { border-color: var(--blue); background: white; box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    
    .perm-grid-dash { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-bottom: 2rem; }
    .perm-card-dash { position: relative; border: 1.5px solid var(--border); border-radius: 12px; padding: 1.25rem; cursor: pointer; transition: all 0.18s; background: var(--surface); display: flex; flex-direction: column; align-items: center; gap: 8px; text-align: center; }
    .perm-card-dash:hover { border-color: rgba(26,86,160,0.25); background: var(--surface-alt); }
    .perm-card-dash.checked { border-color: var(--blue); background: var(--blue-light); box-shadow: 0 0 0 3px rgba(26,86,160,0.08); }
    .perm-card-dash input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }
    
    .perm-icon-box { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: all 0.18s; }
    .perm-view .perm-icon-box { background: var(--green-bg); color: var(--green); }
    .perm-add .perm-icon-box { background: rgba(26,86,160,0.08); color: var(--text-3); }
    .checked.perm-add .perm-icon-box { background: rgba(26,86,160,0.15); color: var(--blue); }
    .perm-edit .perm-icon-box { background: rgba(180,83,9,0.06); color: var(--text-3); }
    .checked.perm-edit .perm-icon-box { background: var(--amber-bg); color: var(--amber); }
    .perm-delete .perm-icon-box { background: rgba(192,57,43,0.05); color: var(--text-3); }
    .checked.perm-delete .perm-icon-box { background: var(--red-bg); color: var(--red); }
    
    .perm-check-mark { position: absolute; top: 10px; right: 10px; width: 18px; height: 18px; border-radius: 50%; border: 1.5px solid var(--border); display: flex; align-items: center; justify-content: center; transition: all 0.18s; }
    .checked .perm-check-mark { background: var(--blue); border-color: var(--blue); }
    .perm-check-mark svg { width: 10px; height: 10px; stroke: white; fill: none; stroke-width: 3; opacity: 0; }
    .checked .perm-check-mark svg { opacity: 1; }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></div>
        <span class="panel-head-title">Modify Access Level</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" novalidate>

        <div class="field">
          <label class="field-label">Role Name</label>
          <div class="input-wrap">
            <input type="text" name="role_name" value="<?= htmlspecialchars($role['role_name']) ?>" required>
          </div>
        </div>

        <div style="height: 1px; background: var(--border-soft); margin: 2rem 0;"></div>

        <div class="field-label" style="margin-bottom: 1rem;">Permissions Architecture</div>

        <div class="perm-grid-dash">
          <!-- View (Always On) -->
          <div class="perm-card-dash perm-view checked" style="cursor: default; opacity: 0.85;">
            <div class="perm-check-mark"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="perm-icon-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
            <div style="font-weight:700; color:var(--text);">View</div>
            <div style="font-size:0.7rem; color:var(--green); font-weight:700; background:var(--green-bg); padding:2px 8px; border-radius:10px;">Mandatory</div>
          </div>

          <!-- Add -->
          <label class="perm-card-dash perm-add <?= !empty($perms['add']) ? 'checked' : '' ?>" id="lbl-add">
            <input type="checkbox" name="permissions[]" value="add" <?= !empty($perms['add']) ? 'checked' : '' ?> onchange="togglePerm(this, 'lbl-add')">
            <div class="perm-check-mark"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="perm-icon-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></div>
            <div style="font-weight:700; color:var(--text-2);">Add</div>
            <div style="font-size:0.7rem; color:var(--text-3);">Create new records</div>
          </label>

          <!-- Edit -->
          <label class="perm-card-dash perm-edit <?= !empty($perms['edit']) ? 'checked' : '' ?>" id="lbl-edit">
            <input type="checkbox" name="permissions[]" value="edit" <?= !empty($perms['edit']) ? 'checked' : '' ?> onchange="togglePerm(this, 'lbl-edit')">
            <div class="perm-check-mark"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="perm-icon-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></div>
            <div style="font-weight:700; color:var(--text-2);">Edit</div>
            <div style="font-size:0.7rem; color:var(--text-3);">Modify existing data</div>
          </label>

          <!-- Delete -->
          <label class="perm-card-dash perm-delete <?= !empty($perms['delete']) ? 'checked' : '' ?>" id="lbl-delete">
            <input type="checkbox" name="permissions[]" value="delete" <?= !empty($perms['delete']) ? 'checked' : '' ?> onchange="togglePerm(this, 'lbl-delete')">
            <div class="perm-check-mark"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
            <div class="perm-icon-box"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
            <div style="font-weight:700; color:var(--text-2);">Delete</div>
            <div style="font-size:0.7rem; color:var(--text-3);">Remove permanent data</div>
          </label>
        </div>

        <div style="display:flex; gap:12px;">
          <button type="submit" class="btn-primary-dash" style="flex:1; justify-content:center;">Update Role</button>
          <a href="list_roles.php" class="btn-primary-dash" style="flex:1; justify-content:center; background:var(--surface-alt); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">Cancel</a>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
  function togglePerm(cb, id) {
    const lbl = document.getElementById(id);
    if(cb.checked) lbl.classList.add('checked');
    else lbl.classList.remove('checked');
  }
</script>
</body></html>
