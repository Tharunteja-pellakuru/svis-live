<?php
ob_start();
include('header.php'); 
include('sidebar.php'); 
include('../db_connect.php');

if (!isset($_GET['event_id'])) { 
    echo "<script>alert('Invalid Event'); window.location='events_list.php';</script>"; 
    exit(); 
}

$event_id = intval($_GET['event_id']);

// Fetch event name for display
$event_stmt = $conn->prepare("SELECT event_name FROM events WHERE id = ?");
$event_stmt->bind_param("i", $event_id);
$event_stmt->execute();
$event_res = $event_stmt->get_result();
$display_name = "Event Registrations";
if ($ev_row = $event_res->fetch_assoc()) {
    $display_name = htmlspecialchars($ev_row['event_name']);
}
$event_stmt->close();

// Fetch registrations from event_registrations joined with alumni_register and events
$stmt = $conn->prepare("
    SELECT 
        r.alumni_id, a.full_name, a.batch_year, a.email, a.phone, 
        r.attendance, r.registration_fee, r.created_at, 
        r.event_category, e.venue AS event_venue 
    FROM event_registrations r
    JOIN alumni_register a ON r.alumni_id = a.id
    JOIN events e ON r.event_id = e.id
    WHERE r.event_id = ? 
    ORDER BY r.id DESC
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$registrations = [];
while ($row = $result->fetch_assoc()) {
    $registrations[] = [
        'alumni_id' => $row['alumni_id'],
        'name' => $row['full_name'],
        'phone' => $row['phone'],
        'email' => $row['email'],
        'batch' => $row['batch_year'],
        'attendance' => $row['attendance'],
        'fee' => $row['registration_fee'],
        'category' => $row['event_category'],
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
        <span class="panel-head-title">Registrations List</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= count($registrations) ?> Attendees</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="regSearch" class="search-input" placeholder="Search name, phone, batch..." autocomplete="off">
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
          <th>Attendance</th>
          <th>Fee Paid</th>
          <th>Category &amp; Mode</th>
          <th>Registration Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($registrations) > 0): ?>
          <?php foreach ($registrations as $index => $reg): ?>
          <tr>
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
            <td data-label="Category & Mode">
              <div style="font-size:0.8rem; color:var(--text-2);">
                <strong><?= htmlspecialchars($reg['category']) ?></strong><br>
                <span style="font-size:0.75rem; color:var(--text-3);"><?= htmlspecialchars($reg['venue']) ?></span>
              </div>
            </td>
            <td data-label="Registration Date"><span style="font-size:0.85rem; color:var(--text-2);"><?= date('M d, Y h:i A', strtotime($reg['date'])) ?></span></td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align:center; padding: 3rem 1rem; color:var(--text-3);">
              <svg style="width:48px;height:48px;margin-bottom:10px;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg><br>
              No registrations found for this event yet.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  // Live search for registrations
  const regSearchInput = document.getElementById('regSearch');
  const regRows = document.querySelectorAll('#regTable tbody tr');

  if(regSearchInput) {
    regSearchInput.addEventListener('input', function() {
      const query = this.value.toLowerCase().trim();
      regRows.forEach(function(row) {
        if(row.cells.length < 2) return; // skip empty state row
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
      });
    });
  }
</script>
</body></html>
