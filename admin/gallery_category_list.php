<?php include('header.php'); include('sidebar.php'); include('../db_connect.php'); ?>

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

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      </div>
      <div>
        <div class="page-header-label">Multimedia</div>
        <div class="page-header-title">Gallery Categories</div>
      </div>
    </div>
    <?php if (check_admin_permission('add')): ?>
    <a href="gallery_category_add.php" class="btn-primary-dash">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Category
    </a>
    <?php endif; ?>
  </div>

  <style>
    .thumb-preview { width: 50px; height: 50px; border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border-soft); background: var(--surface-alt); }
    .thumb-preview img { width: 100%; height: 100%; object-fit: cover; }
    .cat-name-cell { font-weight: 700; color: var(--blue-dark); }
    .date-badge { font-size: 0.82rem; color: var(--text-3); font-weight: 600; }
  </style>

  <?php
    $result = $conn->query("SELECT * FROM gallery_category ORDER BY id DESC");
    $total = ($result) ? $result->num_rows : 0;
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div>
        <span class="panel-head-title">Manage Categories</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $total ?> Categories</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="gcSearch" class="search-input" placeholder="Search category name..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <form id="bulkDeleteForm" action="gallery_category_delete.php" method="POST">
    <?php if (check_admin_permission('delete')): ?>
    <div id="bulkActions" style="display:none; padding: 1rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border);">
      <button type="button" onclick="confirmBulkDelete()" class="btn-action-sm btn-delete-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; width:max-content;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Delete Selected
      </button>
    </div>
    <?php endif; ?>
    <table class="dash-table" id="gcTable">
      <thead>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <th style="width: 40px; text-align:center;"><input type="checkbox" id="selectAll"></th>
          <?php else: ?>
          <th style="width: 40px;"></th>
          <?php endif; ?>
          <th>ID</th>
          <th>Preview</th>
          <th>Category Name</th>
          <th>Added Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): 
          $imgUrl = "../uploads/category/" . rawurlencode($row['image']);
          if (empty($row['image']) || !file_exists(__DIR__ . "/../uploads/category/" . $row['image'])) {
            $imgUrl = "https://via.placeholder.com/60x60?text=No+Img";
          }
        ?>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <td style="text-align:center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="rowCheckbox"></td>
          <?php else: ?>
          <td></td>
          <?php endif; ?>
          <td data-label="ID"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
          <td data-label="Preview"><div class="thumb-preview"><img src="<?= $imgUrl ?>"></div></td>
          <td data-label="Name"><span class="cat-name-cell"><?= htmlspecialchars($row['name']) ?></span></td>
          <td data-label="Added Date"><span class="date-badge"><?= date("d M Y", strtotime($row['adddate'])) ?></span></td>
          <td data-label="Action">
            <div class="actions">
              <?php if (check_admin_permission('edit')): ?>
              <a href="gallery_category_edit.php?id=<?= $row['id'] ?>" class="btn-action-sm btn-edit-sm" data-tooltip="Edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <?php endif; ?>
              <?php if (check_admin_permission('delete')): ?>
              <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDelete(this)" 
                      data-url="gallery_category_delete.php?id=<?= $row['id'] ?>" 
                      data-name="<?= htmlspecialchars($row['name']) ?>" title="Delete">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
              </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    </form>
  </div>
</div>

<div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,37,69,0.4); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1.5rem;">
  <div style="background:var(--surface); max-width:400px; width:100%; padding:2.25rem; border-radius:20px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
    <div style="width:56px; height:56px; background:var(--red-bg); color:var(--red); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></div>
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Delete Category</h3>
    <p style="font-size:0.92rem; color:var(--text-2); line-height:1.5; margin-bottom:1.5rem;">Are you sure you want to delete <strong id="delName"></strong>? Images linked to this category may lose their grouping.</p>
    <div style="display:flex; gap:10px;"><button onclick="closeModal()" style="flex:1; height:44px; border-radius:10px; font-weight:700; background:var(--surface-alt); border:1.5px solid var(--border); color:var(--text-2); cursor:pointer;">Cancel</button><a id="delConfirm" href="#" style="flex:1; height:44px; border-radius:10px; font-weight:700; background:var(--red); color:white; display:flex; align-items:center; justify-content:center; text-decoration:none;">Yes, Delete</a></div>
  </div>
</div>

<script>
  function confirmDelete(btn) {
    document.getElementById('delName').textContent = btn.dataset.name;
    document.getElementById('delConfirm').href = btn.dataset.url;
    document.getElementById('delConfirm').onclick = null;
    document.getElementById('deleteModal').style.display = 'flex';
  }
  function confirmBulkDelete() {
    const selected = document.querySelectorAll('.rowCheckbox:checked').length;
    document.getElementById('delName').textContent = selected + ' selected item(s)';
    document.getElementById('delConfirm').href = '#';
    document.getElementById('delConfirm').onclick = function(e) {
      e.preventDefault();
      document.getElementById('bulkDeleteForm').submit();
    };
    document.getElementById('deleteModal').style.display = 'flex';
  }
  function closeModal() { document.getElementById('deleteModal').style.display = 'none'; }

  // Bulk selection logic
  const selectAll = document.getElementById('selectAll');
  const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
  const bulkActions = document.getElementById('bulkActions');

  function toggleBulkActions() {
    const anyChecked = document.querySelectorAll('.rowCheckbox:checked').length > 0;
    bulkActions.style.display = anyChecked ? 'block' : 'none';
  }

  if (selectAll) {
    selectAll.addEventListener('change', function() {
      rowCheckboxes.forEach(cb => {
        // Only check visible rows
        if (cb.closest('tr').style.display !== 'none') {
          cb.checked = this.checked;
        }
      });
      toggleBulkActions();
    });
  }

  rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', toggleBulkActions);
  });

  // ── Live search ─────────────────────────────────────────────
  const gcSearch = document.getElementById('gcSearch');
  const gcRows = document.querySelectorAll('#gcTable tbody tr');
  gcSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    gcRows.forEach(function(row) {
      const name = (row.querySelector('.cat-name-cell') || {}).textContent || '';
      row.style.display = name.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
</body></html>
