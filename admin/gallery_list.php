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

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
        </svg>
      </div>
      <div>
        <div class="page-header-label">Multimedia</div>
        <div class="page-header-title">Gallery Management</div>
      </div>
    </div>
    <?php if (check_admin_permission('add')): ?>
    <a href="gallery_add.php" class="btn-primary-dash">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Images
    </a>
    <?php endif; ?>
  </div>

  <style>
    .gallery-img-wrap { width: 54px; height: 54px; border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border-soft); background: var(--surface-alt); display: flex; align-items: center; justify-content: center; }
    .gallery-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .cat-badge { display: inline-flex; padding: 3px 11px; background: var(--blue-light); color: var(--blue); font-size: 0.72rem; font-weight: 700; border-radius: 12px; text-transform: uppercase; }
    .cell-caption { max-width: 320px; color: var(--text); font-weight: 500; font-size: 0.9rem; }
    .filter-bar { display: flex; flex-wrap: nowrap; gap: 8px; padding: 1.25rem 1.5rem 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .filter-bar::-webkit-scrollbar { display: none; }
    .filter-pill { display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1.5px solid var(--border); background: var(--surface-alt); color: var(--text-3); cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
    .filter-pill:hover { border-color: var(--blue); color: var(--blue); }
    .filter-pill.active { background: var(--blue); color: white; border-color: var(--blue); }
    .filter-pill .pill-count { font-size: 0.68rem; background: rgba(0,0,0,0.08); padding: 1px 7px; border-radius: 10px; font-weight: 800; }
    .filter-pill.active .pill-count { background: rgba(255,255,255,0.25); }
  </style>

  <?php
    // Fetch categories for filter
    $catResult = $conn->query("SELECT c.id, c.name, COUNT(g.id) as img_count FROM gallery_category c LEFT JOIN gallery g ON c.id = g.category_id GROUP BY c.id ORDER BY c.name ASC");
    $categories = [];
    while ($c = $catResult->fetch_assoc()) { $categories[] = $c; }

    $sql = "SELECT g.*, c.name AS category_name FROM gallery g LEFT JOIN gallery_category c ON g.category_id = c.id ORDER BY g.id DESC";
    $result = $conn->query($sql);
    $total = ($result) ? $result->num_rows : 0;

    // Count uncategorized
    $uncatRes = $conn->query("SELECT COUNT(*) as cnt FROM gallery WHERE category_id IS NULL OR category_id = 0");
    $uncatCount = $uncatRes->fetch_assoc()['cnt'];
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
        <span class="panel-head-title">Media Library</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $total ?> Assets</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="glSearch" class="search-input" placeholder="Search caption, category..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <!-- Category Filter Bar -->
    <div class="filter-bar">
      <button class="filter-pill active" data-cat="all" onclick="filterCat(this)">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        All <span class="pill-count"><?= $total ?></span>
      </button>
      <?php foreach ($categories as $cat): ?>
        <button class="filter-pill" data-cat="<?= htmlspecialchars($cat['name']) ?>" onclick="filterCat(this)">
          <?= htmlspecialchars($cat['name']) ?> <span class="pill-count"><?= $cat['img_count'] ?></span>
        </button>
      <?php endforeach; ?>
      <?php if ($uncatCount > 0): ?>
        <button class="filter-pill" data-cat="Uncategorized" onclick="filterCat(this)">
          Uncategorized <span class="pill-count"><?= $uncatCount ?></span>
        </button>
      <?php endif; ?>
    </div>

    <form id="bulkDeleteForm" action="gallery_delete.php" method="POST">
    <?php if (check_admin_permission('delete')): ?>
    <div id="bulkActions" style="display:none; padding: 1rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border);">
      <button type="button" onclick="confirmBulkDelete()" class="btn-action-sm btn-delete-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; width:max-content; border-radius: 8px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Delete Selected
      </button>
    </div>
    <?php endif; ?>
    <table class="dash-table" id="glTable">
      <thead>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <th style="width: 40px; text-align:center;"><input type="checkbox" id="selectAll"></th>
          <?php else: ?>
          <th style="width: 40px;"></th>
          <?php endif; ?>
          <th>ID</th>
          <th>Preview</th>
          <th>Category</th>
          <th>Caption</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()):
          $imgUrl = "../uploads/" . rawurlencode($row['image_name']);
          if (empty($row['image_name']) || !file_exists(__DIR__ . "/../uploads/" . $row['image_name'])) {
            $imgUrl = "https://via.placeholder.com/60x60?text=No+Img";
          }
          $category = $row['category_name'] ?: "Uncategorized";
        ?>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <td style="text-align:center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="rowCheckbox"></td>
          <?php else: ?>
          <td></td>
          <?php endif; ?>
          <td data-label="ID"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
          <td data-label="Preview"><div class="gallery-img-wrap"><img src="<?= $imgUrl ?>" alt="Preview"></div></td>
          <td data-label="Category"><span class="cat-badge"><?= htmlspecialchars($category) ?></span></td>
          <td data-label="Caption"><div class="cell-caption"><?= htmlspecialchars($row['caption'] ?: '—') ?></div></td>
          <td data-label="Actions">
            <div class="actions">
              <?php if (check_admin_permission('edit')): ?>
              <a href="gallery_edit.php?id=<?= $row['id'] ?>" class="btn-action-sm btn-edit-sm" data-tooltip="Edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
              <?php endif; ?>
              <?php if (check_admin_permission('delete')): ?>
              <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDelete(this)" data-url="gallery_delete.php?id=<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['caption'] ?: 'Image #'.$row['id']) ?>" data-tooltip="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></button>
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
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Delete Media</h3>
    <p style="font-size:0.92rem; color:var(--text-2); line-height:1.5; margin-bottom:1.5rem;">Are you sure you want to remove <strong id="delName"></strong>? This action cannot be undone.</p>
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

  // ── Combined search + category filter ─────────────────────
  const glSearch = document.getElementById('glSearch');
  const glRows = document.querySelectorAll('#glTable tbody tr');
  let activeCat = 'all';

  function applyFilters() {
    const q = glSearch.value.toLowerCase().trim();
    glRows.forEach(function(row) {
      const caption = (row.querySelector('.cell-caption') || {}).textContent || '';
      const cat = (row.querySelector('.cat-badge') || {}).textContent || '';
      const matchSearch = (caption + ' ' + cat).toLowerCase().includes(q);
      const matchCat = activeCat === 'all' || cat.trim().toLowerCase() === activeCat.toLowerCase();
      row.style.display = (matchSearch && matchCat) ? '' : 'none';
    });
  }

  glSearch.addEventListener('input', applyFilters);

  function filterCat(btn) {
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    activeCat = btn.dataset.cat;
    applyFilters();
  }
</script>
</body></html>
