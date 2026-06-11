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
</script>
</body></html>
