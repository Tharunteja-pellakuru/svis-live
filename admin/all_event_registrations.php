<?php
ob_start();
include('header.php'); 
include('sidebar.php'); 
include('../db_connect.php');

$display_name = "All Event Registrations";

// Fetch all events for the filter dropdown
$events_stmt = $conn->prepare("SELECT id, event_name FROM events ORDER BY event_name ASC");
$events_stmt->execute();
$events_res = $events_stmt->get_result();
$events_list = [];
while ($ev_row = $events_res->fetch_assoc()) {
    $events_list[] = $ev_row;
}
$events_stmt->close();

// Fetch ALL registrations from event_registrations joined with alumni_register and events
$stmt = $conn->prepare("
    SELECT 
        r.id as reg_id, r.event_id, r.alumni_id, a.full_name, a.batch_year, a.email, a.phone, 
        r.attendance, r.registration_fee, r.created_at, 
        r.event_category, e.event_name, e.venue AS event_venue 
    FROM event_registrations r
    JOIN alumni_register a ON r.alumni_id = a.id
    JOIN events e ON r.event_id = e.id
    ORDER BY r.id DESC
");
$stmt->execute();
$result = $stmt->get_result();

$registrations = [];
while ($row = $result->fetch_assoc()) {
    $registrations[] = [
        'reg_id' => $row['reg_id'],
        'event_id' => $row['event_id'],
        'alumni_id' => $row['alumni_id'],
        'name' => $row['full_name'],
        'phone' => $row['phone'],
        'email' => $row['email'],
        'batch' => $row['batch_year'],
        'attendance' => $row['attendance'],
        'fee' => $row['registration_fee'],
        'category' => $row['event_category'],
        'event_name' => $row['event_name'],
        'venue' => $row['event_venue'],
        'date' => $row['created_at']
    ];
}
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      </div>
      <div>
        <div class="page-header-label">Event Attendees</div>
        <div class="page-header-title"><?= $display_name ?></div>
      </div>
    </div>
    <a href="events_list.php" class="btn-primary-dash" style="background:var(--surface-alt); border:1px solid var(--border); color:var(--text-2);">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Back to Events
    </a>
  </div>

  <style>
    .user-info { display: flex; flex-direction: column; }
    .user-name-cell { font-weight: 700; color: var(--blue-dark); display: block; }
    .user-phone-cell { font-size: 0.8rem; color: var(--text-3); }
    .batch-badge { display: inline-flex; padding: 4px 10px; background: var(--amber-bg); color: #d97706; font-size: 0.75rem; font-weight: 700; border-radius: 12px; }

    /* Excel Dropdown */
    .excel-dropdown-wrap { position: relative; }
    .btn-excel { display: inline-flex; align-items: center; gap: 7px; height: 34px; padding: 0 14px; background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; border: none; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; box-shadow: 0 2px 8px rgba(22,163,74,0.3); transition: all 0.15s; white-space: nowrap; }
    .btn-excel:hover { background: linear-gradient(135deg, #15803d, #166534); transform: translateY(-1px); }
    .btn-excel .excel-chevron { transition: transform 0.2s; }
    .btn-excel.open .excel-chevron { transform: rotate(180deg); }
    .excel-dropdown { display: none; position: fixed; background: var(--surface); border: 1.5px solid var(--border); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); min-width: 240px; max-height: 320px; overflow-y: auto; z-index: 99999; animation: dropIn 0.18s ease; }
    .excel-dropdown.show { display: block; }
    @keyframes dropIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
    .excel-dropdown-header { padding: 10px 14px 6px; font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-3); border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--surface); }
    .excel-option { display: flex; align-items: center; gap: 10px; padding: 10px 14px; font-size: 0.82rem; font-weight: 600; color: var(--text); text-decoration: none; transition: background 0.12s; }
    .excel-option:hover { background: var(--surface-alt); color: var(--blue); }
    .excel-option .excel-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .excel-option .excel-count { margin-left: auto; font-size: 0.68rem; font-weight: 800; background: var(--surface-alt); padding: 1px 8px; border-radius: 10px; color: var(--text-3); }
    .excel-option:hover .excel-count { background: rgba(26,86,160,0.1); color: var(--blue); }
  </style>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M18 8l2 2 4-4"/></svg></div>
        <span class="panel-head-title">Global Registrations List</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= count($registrations) ?> Attendees</span>
      </div>
      <div class="panel-head-right" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <div class="search-form" style="width: auto;">
          <select id="eventFilter" class="search-input" style="padding-right: 30px; cursor: pointer; text-overflow: ellipsis;">
            <option value="all">All Events</option>
            <?php foreach($events_list as $ev): ?>
              <option value="<?= $ev['id'] ?>"><?= htmlspecialchars($ev['event_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="search-form">
          <input type="text" id="regSearch" class="search-input" placeholder="Search name, event, batch..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>

        <!-- Excel Download Dropdown -->
        <?php
          $totalRegs = count($registrations);
        ?>
        <div class="excel-dropdown-wrap">
          <button id="excelDropdownBtn" class="btn-excel" onclick="toggleExcelDropdown()" type="button">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Download Excel
            <svg class="excel-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="excel-dropdown" id="excelDropdown">
            <div class="excel-dropdown-header">📊 Export Registrations</div>
            <a href="download_event_registrations.php" class="excel-option">
              <span class="excel-dot" style="background:#6366f1;"></span>
              All Events
              <span class="excel-count"><?= $totalRegs ?></span>
            </a>
            <?php foreach($events_list as $ev):
              $evCount = 0;
              foreach($registrations as $r) { if($r['event_id'] == $ev['id']) $evCount++; }
            ?>
            <a href="download_event_registrations.php?event_id=<?= $ev['id'] ?>" class="excel-option">
              <span class="excel-dot" style="background:#36a2eb;"></span>
              <?= htmlspecialchars($ev['event_name']) ?>
              <span class="excel-count"><?= $evCount ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <table class="dash-table" id="regTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Attendee Details</th>
          <th>Batch Year</th>
          <th>Event &amp; Venue</th>
          <th>Attendance</th>
          <th>Fee Paid</th>
          <th>Registration Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($registrations) > 0): ?>
          <?php foreach ($registrations as $index => $reg): ?>
          <tr class="reg-row" data-event-id="<?= $reg['event_id'] ?>">
            <td data-label="#"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);"><?= $index + 1 ?></span></td>
            <td data-label="Attendee Details">
              <div class="user-info">
                <?php if (!empty($reg['alumni_id'])): ?>
                  <a href="users_list_view.php?id=<?= $reg['alumni_id'] ?>" class="user-name-cell" style="text-decoration:none; color:var(--blue-dark);">
                    <?= htmlspecialchars($reg['name']) ?> <i class="fas fa-external-link-alt" style="font-size:0.7rem; margin-left:2px; opacity:0.7;"></i>
                  </a>
                <?php else: ?>
                  <span class="user-name-cell"><?= htmlspecialchars($reg['name']) ?></span>
                <?php endif; ?>
                <span class="user-phone-cell"><?= htmlspecialchars($reg['email']) ?></span>
                <span class="user-phone-cell"><?= htmlspecialchars($reg['phone'] ?? 'N/A') ?></span>
              </div>
            </td>
            <td data-label="Batch Year"><span class="batch-badge"><?= htmlspecialchars($reg['batch']) ?></span></td>
            <td data-label="Event & Venue">
              <div style="font-size:0.85rem; font-weight:700; color:var(--blue-dark);">
                <?= htmlspecialchars($reg['event_name']) ?>
              </div>
              <div style="font-size:0.75rem; color:var(--text-3);">
                <?= htmlspecialchars($reg['venue']) ?>
                <?php if(!empty($reg['category'])) echo " &bull; " . htmlspecialchars($reg['category']); ?>
              </div>
            </td>
            <td data-label="Attendance">
              <?php
                $att = $reg['attendance'];
                $bg = '#eff6ff'; $color = '#1e40af';
                if ($att === 'No') { $bg = '#fef2f2'; $color = '#991b1b'; }
                elseif ($att === 'Maybe') { $bg = '#fffbeb'; $color = '#92400e'; }
              ?>
              <span style="display:inline-flex; padding:4px 10px; background:<?= $bg ?>; color:<?= $color ?>; font-size:0.75rem; font-weight:700; border-radius:12px;">
                <?= htmlspecialchars($att) ?>
              </span>
            </td>
            <td data-label="Fee Paid">
              <span style="font-size:0.85rem; font-weight:600; color:var(--text-2);">
                <?= $reg['fee'] > 0 ? 'INR ' . number_format($reg['fee'], 2) : 'Free' ?>
              </span>
            </td>
            <td data-label="Registration Date"><span style="font-size:0.85rem; color:var(--text-2);"><?= date('M d, Y h:i A', strtotime($reg['date'])) ?></span></td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align:center; padding: 3rem 1rem; color:var(--text-3);">
              <svg style="width:48px;height:48px;margin-bottom:10px;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg><br>
              No event registrations found yet.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  // Live search and filter for registrations
  const regSearchInput = document.getElementById('regSearch');
  const eventFilter = document.getElementById('eventFilter');
  const regRows = document.querySelectorAll('.reg-row');

  function filterTable() {
    const query = regSearchInput ? regSearchInput.value.toLowerCase().trim() : '';
    const selectedEvent = eventFilter ? eventFilter.value : 'all';

    regRows.forEach(function(row) {
      const text = row.textContent.toLowerCase();
      const rowEventId = row.getAttribute('data-event-id');
      
      const matchesSearch = text.includes(query);
      const matchesEvent = (selectedEvent === 'all' || rowEventId === selectedEvent);
      
      if (matchesSearch && matchesEvent) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  if (regSearchInput) {
    regSearchInput.addEventListener('input', filterTable);
  }
  if (eventFilter) {
    eventFilter.addEventListener('change', filterTable);
  }

  // ── Excel Dropdown ─────────────────────────────────────────
  function toggleExcelDropdown() {
    const btn = document.getElementById('excelDropdownBtn');
    const dd  = document.getElementById('excelDropdown');
    const isOpen = dd.classList.contains('show');
    if (!isOpen) {
      const rect = btn.getBoundingClientRect();
      dd.style.top   = (rect.bottom + 6 + window.scrollY) + 'px';
      dd.style.right = (window.innerWidth - rect.right) + 'px';
    }
    dd.classList.toggle('show', !isOpen);
    btn.classList.toggle('open', !isOpen);
  }
  document.addEventListener('click', function(e) {
    const wrap = document.querySelector('.excel-dropdown-wrap');
    if (wrap && !wrap.contains(e.target)) {
      document.getElementById('excelDropdown').classList.remove('show');
      document.getElementById('excelDropdownBtn').classList.remove('open');
    }
  });
</script>
</body></html>
