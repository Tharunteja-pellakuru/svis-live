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
          <polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/>
        </svg>
      </div>
      <div>
        <div class="page-header-label">Multimedia</div>
        <div class="page-header-title">Video Management</div>
      </div>
    </div>
    <?php if (check_admin_permission('add')): ?>
    <a href="add_video.php" class="btn-primary-dash">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Video
    </a>
    <?php endif; ?>
  </div>

  <style>
    .video-thumb-wrap { width: 80px; height: 50px; border-radius: 8px; overflow: hidden; border: 1.5px solid var(--border-soft); background: var(--surface-alt); display: flex; align-items: center; justify-content: center; position: relative; }
    .video-thumb-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .video-thumb-wrap .play-hint { position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s; }
    .video-thumb-wrap:hover .play-hint { opacity: 1; }
    .video-thumb-wrap .play-hint svg { width: 20px; height: 20px; fill: white; stroke: none; }
    .video-title-cell { font-weight: 700; color: var(--text); font-size: 0.92rem; }
    .video-url-cell { font-size: 0.78rem; color: var(--blue); text-decoration: none; opacity: 0.8; }
    .video-url-cell:hover { text-decoration: underline; opacity: 1; }
    .actions { display: flex; gap: 8px; }
    .btn-action-sm { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; border: 1.5px solid transparent; transition: all 0.15s; }
    .btn-edit-sm { background: var(--blue-light); color: var(--blue); border-color: rgba(26,86,160,0.1); }
    .btn-edit-sm:hover { background: var(--blue); color: white; }
    .btn-delete-sm { background: var(--red-bg); color: var(--red); border-color: rgba(192,57,43,0.1); }
    .btn-delete-sm:hover { background: var(--red); color: white; }
    .search-form-vd { display: flex; align-items: center; background: #fff; border: 1px solid var(--border); border-radius: 8px; padding: 2px; }
    .search-input-vd { border: none; outline: none; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; width: 220px; color: var(--text); }
    .search-icon-vd { background: var(--blue-light); color: var(--blue); width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
    @media (max-width: 768px) { .search-input-vd { width: 100%; flex: 1; } }
  </style>

  <?php
    $result = $conn->query("SELECT * FROM videos ORDER BY id DESC");
    $total = ($result) ? $result->num_rows : 0;
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.94 2C5.12 19.5 12 19.5 12 19.5s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg></div>
        <span class="panel-head-title">Video Library</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $total ?> Videos</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="vdSearch" class="search-input" placeholder="Search video title..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <form id="bulkDeleteForm" action="delete_video.php" method="POST">
    <?php if (check_admin_permission('delete')): ?>
    <div id="bulkActions" style="display:none; padding: 1rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border);">
      <button type="button" onclick="confirmBulkDelete()" class="btn-action-sm btn-delete-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; width:max-content;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Delete Selected
      </button>
    </div>
    <?php endif; ?>
    <table class="dash-table" id="vdTable">
      <thead>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <th style="width: 40px; text-align:center;"><input type="checkbox" id="selectAll"></th>
          <?php else: ?>
          <th style="width: 40px;"></th>
          <?php endif; ?>
          <th>ID</th>
          <th>Preview</th>
          <th>Video Info</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()):
          $youtubeId = '';
          // Robust regex for various YouTube URL formats (watch, embed, shorts, youtu.be)
          if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $row['video_url'], $match)) {
              $youtubeId = $match[1];
          }
          // Use hqdefault.jpg as it's more reliable across all video types
          $thumbUrl = $youtubeId ? "https://img.youtube.com/vi/$youtubeId/hqdefault.jpg" : "https://via.placeholder.com/120x90?text=Video";
        ?>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <td style="text-align:center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="rowCheckbox"></td>
          <?php else: ?>
          <td></td>
          <?php endif; ?>
          <td data-label="ID"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
          <td data-label="Preview">
            <div class="video-thumb-wrap">
              <img src="<?= $thumbUrl ?>" alt="Thumbnail">
              <div class="play-hint"><svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg></div>
            </div>
          </td>
          <td data-label="Video Info">
            <div style="display:flex; flex-direction:column; gap:2px;">
              <span class="video-title-cell"><?= htmlspecialchars($row['title']) ?></span>
              <a href="<?= htmlspecialchars($row['video_url']) ?>" target="_blank" class="video-url-cell">Watch on YouTube →</a>
            </div>
          </td>
          <td data-label="Actions">
            <div class="actions">
              <?php if (check_admin_permission('edit')): ?>
              <a href="edit_video.php?id=<?= $row['id'] ?>" class="btn-action-sm btn-edit-sm" data-tooltip="Edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
              <?php endif; ?>
              <?php if (check_admin_permission('delete')): ?>
              <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDelete(this)" data-url="delete_video.php?id=<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['title']) ?>" data-tooltip="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></button>
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
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Delete Video</h3>
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

  // ── Live search for videos ────────────────────────────────
  const vdSearch = document.getElementById('vdSearch');
  const vdRows = document.querySelectorAll('#vdTable tbody tr');
  vdSearch.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    vdRows.forEach(function(row) {
      const title = (row.querySelector('.video-title-cell') || {}).textContent || '';
      row.style.display = title.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
</body></html>
