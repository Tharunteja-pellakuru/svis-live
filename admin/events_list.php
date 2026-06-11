<?php include('header.php'); include('sidebar.php'); include('../db_connect.php'); ?>

<?php
$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>

<div class="dash-main">

  <!-- Toast Flash Message -->
  <?php if (!empty($flash_msg)): ?>
    <div id="toastMessage" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 14px 20px; border-radius: 10px; font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: <?= $flash_type === 'success' ? '#16a34a' : '#ef4444' ?>; color: #ffffff; border: none; transform: translateX(120%); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
      <?php if ($flash_type === 'success'): ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <?php else: ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php endif; ?>
      <?= htmlspecialchars($flash_msg) ?>
    </div>
    <script>
      setTimeout(() => {
        const toast = document.getElementById('toastMessage');
        if (toast) {
          toast.style.transform = 'translateX(0)';
          toast.style.opacity = '1';
          setTimeout(() => { toast.style.transform = 'translateX(120%)'; toast.style.opacity = '0'; }, 3500);
        }
      }, 100);
    </script>
  <?php endif; ?>

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div>
        <div class="page-header-label">Calendar</div>
        <div class="page-header-title">Events Management</div>
      </div>
    </div>
    <?php if (check_admin_permission('add')): ?>
    <a href="events_add.php" class="btn-primary-dash">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Add Event
    </a>
    <?php endif; ?>
  </div>

  <style>
    .event-thumb { width: 56px; height: 56px; border-radius: 10px; overflow: hidden; border: 1.5px solid var(--border-soft); background: var(--surface-alt); display: flex; align-items: center; justify-content: center; }
    .event-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .event-name-cell { font-weight: 700; color: var(--blue-dark); display: block; margin-bottom: 2px; }
    .event-venue-cell { display: flex; align-items: center; gap: 5px; font-size: 0.78rem; color: var(--text-3); }
    .time-badge { display: inline-flex; padding: 3px 10px; background: var(--blue-light); color: var(--blue); font-size: 0.72rem; font-weight: 700; border-radius: 12px; }
    .filter-bar { display: flex; flex-wrap: nowrap; gap: 8px; padding: 1.25rem 1.5rem 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .filter-bar::-webkit-scrollbar { display: none; }
    .filter-pill { display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1.5px solid var(--border); background: var(--surface-alt); color: var(--text-3); cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
    .filter-pill:hover { border-color: var(--blue); color: var(--blue); }
    .filter-pill.active { background: var(--blue); color: white; border-color: var(--blue); }
    .filter-pill .pill-count { font-size: 0.68rem; background: rgba(0,0,0,0.08); padding: 1px 7px; border-radius: 10px; font-weight: 800; }
    .filter-pill.active .pill-count { background: rgba(255,255,255,0.25); }
  </style>

  <?php
    $result = $conn->query("SELECT * FROM events ORDER BY id DESC");
    $events_data = [];
    $count_all = 0; $count_upcoming = 0; $count_past = 0;
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $events_data[] = $row;
            $count_all++;
            if (strtotime($row['start_time']) >= strtotime('today')) {
                $count_upcoming++;
            } else {
                $count_past++;
            }
        }
    }
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <span class="panel-head-title">Event Catalog</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $count_all ?> Events</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="evSearch" class="search-input" placeholder="Search event name, venue..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar" id="evFilterChips">
      <button type="button" class="filter-pill active" data-filter="all">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        All <span class="pill-count"><?= $count_all ?></span>
      </button>
      <button type="button" class="filter-pill" data-filter="upcoming">Upcoming <span class="pill-count"><?= $count_upcoming ?></span></button>
      <button type="button" class="filter-pill" data-filter="past">Past <span class="pill-count"><?= $count_past ?></span></button>
    </div>

    <form id="bulkDeleteForm" action="events_delete.php" method="POST">
    <?php if (check_admin_permission('delete')): ?>
    <div id="bulkActions" style="display:none; padding: 1rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border);">
      <button type="button" onclick="confirmBulkDelete()" class="btn-action-sm btn-delete-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; width:max-content;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Delete Selected
      </button>
    </div>
    <?php endif; ?>
    <table class="dash-table" id="eventsTable">
      <thead>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <th style="width: 40px; text-align:center;"><input type="checkbox" id="selectAll"></th>
          <?php else: ?>
          <th style="width: 40px;"></th>
          <?php endif; ?>
          <th>ID</th>
          <th>Cover</th>
          <th>Event Details</th>
          <th>Timing</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($events_data as $row): 
          $status = (strtotime($row['start_time']) >= strtotime('today')) ? 'upcoming' : 'past';
        ?>
        <tr data-status="<?= $status ?>">
          <?php if (check_admin_permission('delete')): ?>
          <td style="text-align:center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="rowCheckbox"></td>
          <?php else: ?>
          <td></td>
          <?php endif; ?>
          <td data-label="ID"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
          <td data-label="Cover">
            <div class="event-thumb">
              <img src="<?= !empty($row['event_image']) ? '../uploads/'.rawurlencode($row['event_image']) : 'https://ui-avatars.com/api/?name='.urlencode($row['event_name']).'&background=random&color=fff&size=100' ?>">
            </div>
          </td>
          <td data-label="Details">
            <span class="event-name-cell"><?= htmlspecialchars($row['event_name']) ?></span>
            <div class="event-venue-cell"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> <?= htmlspecialchars($row['venue']) ?></div>
          </td>
          <td data-label="Timing">
            <span class="time-badge"><?= date('d M, h:i A', strtotime($row['start_time'])) ?></span>
            <span style="display:block; font-size:0.7rem; color:var(--text-3); margin-top:4px;">to <?= date('d M, h:i A', strtotime($row['end_time'])) ?></span>
          </td>
          <td data-label="Action">
            <div class="actions">
              <a href="event_registrations.php?event_id=<?= $row['id'] ?>" class="btn-action-sm btn-view-sm" data-tooltip="View Registrations">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                  <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
              </a>
              <?php if (check_admin_permission('edit')): ?>
              <a href="events_edit.php?id=<?= $row['id'] ?>" class="btn-action-sm btn-edit-sm" data-tooltip="Edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
              <?php endif; ?>
              <?php if (check_admin_permission('delete')): ?>
              <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDelete(this)" data-url="events_delete.php?id=<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['event_name']) ?>" data-tooltip="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </form>
  </div>
</div>

<div id="deleteModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,37,69,0.4); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1.5rem;">
  <div style="background:var(--surface); max-width:400px; width:100%; padding:2.25rem; border-radius:20px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
    <div style="width:56px; height:56px; background:var(--red-bg); color:var(--red); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></div>
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Delete Event</h3>
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

  // ── Live search & Filter for events ───────────────────────
  const evSearchInput = document.getElementById('evSearch');
  const evChips = document.querySelectorAll('#evFilterChips .filter-pill');
  const evRows = document.querySelectorAll('#eventsTable tbody tr');
  let currentFilter = 'all';

  function filterEvents() {
    const query = evSearchInput ? evSearchInput.value.toLowerCase().trim() : '';
    
    evRows.forEach(function(row) {
      if (!row.hasAttribute('data-status')) return;
      const status = row.getAttribute('data-status');
      const name = (row.querySelector('.event-name-cell') || {}).textContent || '';
      const venue = (row.querySelector('.event-venue-cell') || {}).textContent || '';
      const combined = (name + ' ' + venue).toLowerCase();
      
      const matchesSearch = combined.includes(query);
      const matchesFilter = (currentFilter === 'all') || (currentFilter === status);
      
      row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
  }

  if (evSearchInput) evSearchInput.addEventListener('input', filterEvents);
  
  evChips.forEach(chip => {
    chip.addEventListener('click', function() {
      evChips.forEach(c => c.classList.remove('active'));
      this.classList.add('active');
      currentFilter = this.getAttribute('data-filter');
      filterEvents();
    });
  });
</script>
</body></html>
