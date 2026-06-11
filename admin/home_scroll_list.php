<?php include('header.php'); include('sidebar.php'); include('../db_connect.php'); ?>
<?php
$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
      </div>
      <div>
        <div class="page-header-label">Branding</div>
        <div class="page-header-title">Home Hero Scroll</div>
      </div>
    </div>
    <?php if (check_admin_permission('add')): ?>
    <a href="add_home_scroll_image.php" class="btn-primary-dash">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Slide
    </a>
    <?php endif; ?>
  </div>

  <style>
    .scroll-thumb { width: 100px; height: 56px; border-radius: 8px; overflow: hidden; border: 1.5px solid var(--border-soft); background: var(--surface-alt); }
    .scroll-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .order-badge { display: inline-flex; padding: 2px 10px; background: var(--blue-light); color: var(--blue); font-size: 0.75rem; font-weight: 800; border-radius: 20px; }
    .cell-title { font-weight: 700; color: var(--blue-dark); display: block; }
    .cell-subtitle { font-size: 0.75rem; color: var(--text-3); }
    .actions { display: flex; gap: 8px; align-items: center; }
    .btn-action-sm { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; border: 1.5px solid transparent; transition: all 0.15s; }
    .btn-edit-sm { background: var(--blue-light); color: var(--blue); border-color: rgba(26,86,160,0.1); }
    .btn-edit-sm:hover { background: var(--blue); color: white; }
    .btn-delete-sm { background: var(--red-bg); color: var(--red); border-color: rgba(192,57,43,0.1); }
    .btn-delete-sm:hover { background: var(--red); color: white; }
    .search-form-hs { display: flex; align-items: center; background: #fff; border: 1px solid var(--border); border-radius: 8px; padding: 2px; }
    .search-input-hs { border: none; outline: none; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; width: 220px; color: var(--text); }
    .search-icon-hs { background: var(--blue-light); color: var(--blue); width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
    @media (max-width: 768px) { .search-input-hs { width: 100%; flex: 1; } }
  </style>

  <?php
    $result = $conn->query("SELECT * FROM home_scroll_images ORDER BY order_no ASC, id DESC");
    $total = ($result) ? $result->num_rows : 0;
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg></div>
        <span class="panel-head-title">Scroll Images</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $total ?> Slides</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form-hs">
          <input type="text" id="hsSearch" class="search-input-hs" placeholder="Search title, filename..." autocomplete="off">
          <div class="search-icon-hs"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <table class="dash-table" id="hsTable">
      <thead>
        <tr>
          <th>Order</th>
          <th>Preview</th>
          <th>Content Info</th>
          <th>Date Added</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): 
           $imgPath = __DIR__ . "/../uploads/home_scroll/" . $row['image_name'];
           $imgUrl = "../uploads/home_scroll/" . rawurlencode($row['image_name']);
           if (!file_exists($imgPath) || empty($row['image_name'])) {
               $imgUrl = "https://via.placeholder.com/200x112?text=No+Image";
           }
        ?>
        <tr>
          <td data-label="Order"><span class="order-badge"><?= $row['order_no'] ?></span></td>
          <td data-label="Preview"><div class="scroll-thumb"><img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($row['title']) ?>"></div></td>
          <td data-label="Content">
            <span class="cell-title"><?= htmlspecialchars($row['title'] ?: 'No Title') ?></span>
            <span class="cell-subtitle" style="display:inline-block; margin-top:2px; padding:1px 6px; background:var(--surface-alt); border:1px solid var(--border); border-radius:4px; font-weight:600; color:var(--text-2);"><?= htmlspecialchars($row['banner_type'] ?? 'Large Banner') ?></span><br>
            <span class="cell-subtitle" style="margin-top:2px; display:inline-block;"><?= htmlspecialchars($row['image_name']) ?></span>
          </td>
          <td data-label="Date Added"><span style="font-size:0.85rem; color:var(--text-3); font-weight:600;"><?= date("d M Y", strtotime($row['date_added'])) ?></span></td>
          <td data-label="Action">
            <div class="actions">
              <?php if (check_admin_permission('edit')): ?>
              <a href="edit_home_scroll_image.php?id=<?= $row['id'] ?>" class="btn-action-sm btn-edit-sm" data-tooltip="Edit">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <?php endif; ?>
              <?php if (check_admin_permission('delete')): ?>
              <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDelete(this)" 
                      data-url="delete_home_scroll_image.php?id=<?= $row['id'] ?>" 
                      data-name="<?= htmlspecialchars($row['title'] ?: 'this image') ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
              </button>
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
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Remove Slide</h3>
    <p style="font-size:0.92rem; color:var(--text-2); line-height:1.5; margin-bottom:1.5rem;">Are you sure you want to remove <strong id="delName"></strong> from the home page scroll?</p>
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

  // ── Live search for scroll images ─────────────────────────
  const hsSearchInput = document.getElementById('hsSearch');
  const hsRows = document.querySelectorAll('#hsTable tbody tr');

  hsSearchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    hsRows.forEach(function(row) {
      const title = (row.querySelector('.cell-title') || {}).textContent || '';
      const filename = (row.querySelector('.cell-subtitle') || {}).textContent || '';
      const combined = (title + ' ' + filename).toLowerCase();
      row.style.display = combined.includes(query) ? '' : 'none';
    });
  });
</script>
</body></html>
