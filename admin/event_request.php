<?php
ob_start();
include('header.php'); include('sidebar.php'); include('../db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
    require_admin_permission('delete');
    if (isset($_POST['ids']) && is_array($_POST['ids'])) {
        $stmt = $conn->prepare("DELETE FROM event_requests WHERE id = ?");
        $count = 0;
        foreach ($_POST['ids'] as $req_id) {
            $id = intval($req_id);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) $count++;
        }
        $stmt->close();
        $_SESSION['flash_msg'] = "$count Event request(s) deleted successfully.";
    } else {
        $req_id = intval($_POST['req_id']);
        $stmt = $conn->prepare("DELETE FROM event_requests WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $req_id);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['flash_msg'] = "Event request deleted successfully.";
    }
    $_SESSION['flash_type'] = "success";
    header("Location: event_request.php");
    exit();
}

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
        <svg viewBox="0 0 24 24"><path d="M3 10h18M8 2v4M16 2v4M12 14h4M12 18h2"/><rect x="3" y="4" width="18" height="18" rx="2"/></svg>
      </div>
      <div>
        <div class="page-header-label">Pending Reviews</div>
        <div class="page-header-title">Event Requests</div>
      </div>
    </div>
  </div>

  <style>
    .user-info { display: flex; flex-direction: column; }
    .user-name-cell { font-weight: 700; color: var(--blue-dark); display: block; }
    .user-phone-cell { font-size: 0.8rem; color: var(--text-3); }
    .venue-cell { font-size: 0.88rem; font-weight: 600; color: var(--text-2); }
    .time-cell-main { font-weight: 700; color: var(--text-2); font-size: 0.88rem; }
    .time-cell-sub { font-size: 0.78rem; color: var(--text-3); }
    .status-select-dash { width: 100%; max-width: 150px; height: 36px; padding: 0 10px; font-family: 'Lato', sans-serif; font-size: 0.82rem; font-weight: 700; border: 1.5px solid var(--border); border-radius: 9px; background: var(--surface-alt); color: var(--text-2); outline: none; cursor: pointer; transition: all 0.15s; }
    .status-select-dash:focus { border-color: var(--blue); background: white; }
    .status-verified { color: var(--green); border-color: rgba(10,122,90,0.2); background: var(--green-bg); }
    .status-pending { color: var(--amber); border-color: rgba(211,84,0,0.2); background: var(--amber-bg); }
    .btn-del-req { display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--red-bg, #fef2f2); color: var(--red, #c0392b); border: 1.5px solid rgba(192,57,43,0.1); cursor: pointer; transition: all 0.15s; }
    .btn-del-req:hover { background: var(--red, #c0392b); color: white; }
    .actions-req { display: flex; gap: 8px; align-items: center; }
    .search-form-req { display: flex; align-items: center; background: #fff; border: 1px solid var(--border); border-radius: 8px; padding: 2px; }
    .search-input-req { border: none; outline: none; padding: 6px 12px; font-size: 0.85rem; border-radius: 6px; width: 220px; color: var(--text); }
    .search-icon-req { background: var(--blue-light); color: var(--blue); width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
    @media (max-width: 768px) { .search-input-req { width: 100%; flex: 1; } }
  </style>

  <?php
    $result = $conn->query("SELECT * FROM event_requests ORDER BY id DESC");
    $total = ($result) ? $result->num_rows : 0;
  ?>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 21a10.003 10.003 0 008.384-4.51m-2.408-4.46l.055.09a10.003 10.003 0 011.643 4.51M4.5 12.5h15"/></svg></div>
        <span class="panel-head-title">Submitted Requests</span>
        <span style="font-size:0.75rem; font-weight:700; color:var(--blue); background:var(--blue-light); padding:2px 10px; border-radius:20px; margin-left:8px;"><?= $total ?> Requests</span>
      </div>
      <div class="panel-head-right" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <select id="statusFilter" class="status-select-dash" style="width: 140px; margin: 0; height: 36px; padding: 0 10px;">
          <option value="all">All Status</option>
          <option value="verified">Verified</option>
          <option value="not_verified">Not Verified</option>
        </select>
        <div class="search-form-req">
          <input type="text" id="reqSearch" class="search-input-req" placeholder="Search name, phone, venue..." autocomplete="off">
          <div class="search-icon-req"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <form id="bulkDeleteForm" action="event_request.php" method="POST">
    <input type="hidden" name="delete_request" value="1">
    <?php if (check_admin_permission('delete')): ?>
    <div id="bulkActions" style="display:none; padding: 1rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border);">
      <button type="button" onclick="confirmBulkDelete()" class="btn-action-sm btn-delete-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; width:max-content;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Delete Selected
      </button>
    </div>
    <?php endif; ?>
    <table class="dash-table" id="reqTable">
      <thead>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <th style="width: 40px; text-align:center;"><input type="checkbox" id="selectAll"></th>
          <?php else: ?>
          <th style="width: 40px;"></th>
          <?php endif; ?>
          <th>ID</th>
          <th>Requestor</th>
          <th>Venue / Location</th>
          <th>Requested Time</th>
          <th>Verification</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): 
          $statusClass = $row['event_status'] == '1' ? 'status-verified' : 'status-pending';
        ?>
        <tr>
          <?php if (check_admin_permission('delete')): ?>
          <td style="text-align:center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="rowCheckbox"></td>
          <?php else: ?>
          <td></td>
          <?php endif; ?>
          <td data-label="ID"><span style="font-size:0.78rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
          <td data-label="Requestor">
            <div class="user-info">
              <span class="user-name-cell"><?= htmlspecialchars($row['full_name']) ?></span>
              <span class="user-phone-cell"><?= htmlspecialchars($row['phone']) ?></span>
            </div>
          </td>
          <td data-label="Venue"><span class="venue-cell"><?= htmlspecialchars($row['venue']) ?></span></td>
          <td data-label="Time">
            <div class="time-cell-main"><?= date('d M Y', strtotime($row['start_datetime'])) ?></div>
            <div class="time-cell-sub"><?= date('h:i A', strtotime($row['start_datetime'])) ?> - <?= date('h:i A', strtotime($row['end_datetime'])) ?></div>
          </td>
          <td data-label="Status">
            <select class="status-select-dash <?= $statusClass ?>" onchange="updateStatus(this, <?= $row['id'] ?>)" <?= !check_admin_permission('edit') ? 'disabled' : '' ?>>
              <option value="0" <?= $row['event_status'] == '0' ? 'selected' : '' ?>>Not Verified</option>
              <option value="1" <?= $row['event_status'] == '1' ? 'selected' : '' ?>>Verified</option>
            </select>
          </td>
          <td data-label="Action">
            <div class="actions-req">
              <?php $reqData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>
              <button type="button" class="btn-action-sm btn-view-sm" onclick="viewRequestDetails(<?= $reqData ?>)" data-tooltip="View" style="display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--blue-light); color: var(--blue); border: 1.5px solid rgba(29, 78, 216, 0.1); cursor: pointer; transition: all 0.15s;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
              <?php if (check_admin_permission('delete')): ?>
              <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDeleteReq(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['full_name'])) ?>', '<?= htmlspecialchars(addslashes($row['venue'])) ?>')" data-tooltip="Delete">
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

<!-- Delete Request Confirmation Modal -->
<div id="deleteReqModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,37,69,0.4); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1.5rem;">
  <div style="background:var(--surface); max-width:400px; width:100%; padding:2.25rem; border-radius:20px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
    <div style="width:56px; height:56px; background:var(--red-bg, #fef2f2); color:var(--red, #c0392b); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></div>
    <h3 style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Delete Request</h3>
    <p style="font-size:0.92rem; color:var(--text-2); line-height:1.5; margin-bottom:1.5rem;">Are you sure you want to delete the request from <strong id="delReqName"></strong> for <strong id="delReqVenue" style="color:var(--blue);"></strong>? This cannot be undone.</p>
    <div style="display:flex; gap:10px;">
      <button onclick="closeDeleteReqModal()" style="flex:1; height:44px; border-radius:10px; font-weight:700; background:var(--surface-alt); border:1.5px solid var(--border); color:var(--text-2); cursor:pointer;">Cancel</button>
      <form method="POST" action="event_request.php" style="flex:1; margin:0;">
        <input type="hidden" name="req_id" id="delReqId" value="">
        <input type="hidden" name="delete_request" value="1">
        <button type="submit" style="width:100%; height:44px; border-radius:10px; font-weight:700; background:var(--red, #c0392b); color:white; border:none; cursor:pointer;">Yes, Delete</button>
      </form>
    </div>
  </div>
</div>

<!-- View Request Modal -->
<div id="viewReqModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,37,69,0.4); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1.5rem;">
  <div style="background:var(--surface); max-width:600px; width:100%; border-radius:20px; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.15); max-height: 85vh;">
    
    <div style="padding:1.5rem 2rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
      <h3 style="font-size:1.25rem; font-weight:800; color:var(--text); margin:0;">Event Request Details</h3>
      <button onclick="closeViewReqModal()" style="background:none; border:none; color:var(--text-3); cursor:pointer;"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
    </div>

    <div style="padding:2rem; overflow-y:auto; flex:1;">
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
        
        <!-- Alumni Info -->
        <div style="grid-column: 1 / -1;">
          <h4 style="font-size:0.9rem; color:var(--blue); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb; padding-bottom:5px; margin-bottom:1rem;">1. Alumni Information</h4>
        </div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Full Name:</strong><span id="v_full_name" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Alumni ID:</strong><span id="v_alumni_id" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Batch Year:</strong><span id="v_batch_year" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Phone:</strong><span id="v_phone" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Email:</strong><span id="v_email" style="color:var(--text); font-weight:600;"></span></div>

        <!-- Event Basic Info -->
        <div style="grid-column: 1 / -1; margin-top:0.5rem;">
          <h4 style="font-size:0.9rem; color:var(--blue); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb; padding-bottom:5px; margin-bottom:1rem;">2. Event Basic Details</h4>
        </div>
        <div style="grid-column: 1 / -1;"><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Event Title:</strong><span id="v_event_title" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Category:</strong><span id="v_event_category" style="color:var(--text); font-weight:600;"></span></div>
        <div style="grid-column: 1 / -1;"><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Description:</strong><span id="v_event_description" style="color:var(--text); font-weight:500; font-size:0.95rem; line-height:1.5;"></span></div>
        <div style="grid-column: 1 / -1;"><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Purpose:</strong><span id="v_purpose" style="color:var(--text); font-weight:500; font-size:0.95rem; line-height:1.5;"></span></div>

        <!-- Event Schedule -->
        <div style="grid-column: 1 / -1; margin-top:0.5rem;">
          <h4 style="font-size:0.9rem; color:var(--blue); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb; padding-bottom:5px; margin-bottom:1rem;">3. Event Schedule</h4>
        </div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Preferred Date & Time:</strong><span id="v_preferred_datetime" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Alternate Date:</strong><span id="v_alternate_date" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Duration:</strong><span id="v_event_duration" style="color:var(--text); font-weight:600;"></span></div>

        <!-- Event Mode & Venue -->
        <div style="grid-column: 1 / -1; margin-top:0.5rem;">
          <h4 style="font-size:0.9rem; color:var(--blue); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb; padding-bottom:5px; margin-bottom:1rem;">4. Event Mode & Venue</h4>
        </div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Event Mode:</strong><span id="v_event_mode" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Venue/Location:</strong><span id="v_venue" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Online Platform:</strong><span id="v_online_platform" style="color:var(--text); font-weight:600;"></span></div>
        <div><strong style="color:var(--text-2); font-size:0.85rem; display:block;">Expected Participants:</strong><span id="v_expected_participants" style="color:var(--text); font-weight:600;"></span></div>

      </div>
    </div>
    
    <div style="padding:1.5rem 2rem; border-top:1px solid var(--border); display:flex; justify-content:flex-end; flex-shrink:0;">
      <button onclick="closeViewReqModal()" style="height:44px; padding:0 1.5rem; border-radius:10px; font-weight:700; background:var(--blue); color:white; border:none; cursor:pointer;">Close</button>
    </div>

  </div>
</div>

<div id="toastContainer" style="position:fixed; top:1.5rem; right:1.5rem; z-index:9999; display:flex; flex-direction:column; gap:10px; pointer-events:none;"></div>

<script>
function updateStatus(selectEl, requestId) {
  const statusValue = selectEl.value;
  selectEl.style.opacity = '0.5';
  selectEl.disabled = true;

  if(statusValue == '1') {
    selectEl.className = 'status-select-dash status-verified';
  } else {
    selectEl.className = 'status-select-dash status-pending';
  }

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "update_event_status.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    selectEl.style.opacity = '1';
    selectEl.disabled = false;
    if (this.responseText.trim() === "success") {
      showToast('success', 'Status Updated', 'Verification status changed successfully.');
      if (typeof filterTable === 'function') {
        filterTable();
      }
    } else {
      showToast('danger', 'Update Failed', 'There was an error updating the status.');
    }
  };
  xhr.send("event_status=" + statusValue + "&id=" + requestId);
}

function showToast(type, title, message) {
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.style.cssText = "pointer-events:auto; min-width:300px; padding:1rem 1.25rem; background:white; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.1); border-left:4px solid " + (type==='success'?'var(--green)':'var(--red)') + "; display:flex; flex-direction:column; gap:2px; transform:translateX(120%); transition:transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);";
  toast.innerHTML = `<div style="font-weight:800; color:var(--blue-dark);">${title}</div><div style="font-size:0.8rem; color:var(--text-3);">${message}</div>`;
  container.appendChild(toast);
  setTimeout(() => toast.style.transform = 'translateX(0)', 100);
  setTimeout(() => { toast.style.transform = 'translateX(120%)'; setTimeout(() => toast.remove(), 300); }, 4000);
}

function confirmDeleteReq(id, name, venue) {
  document.getElementById('delReqId').value = id;
  document.getElementById('delReqName').textContent = name;
  document.getElementById('delReqVenue').textContent = venue;
  document.getElementById('deleteReqModal').style.display = 'flex';
}
function closeDeleteReqModal() {
  document.getElementById('deleteReqModal').style.display = 'none';
}

function confirmBulkDelete() {
  const selected = document.querySelectorAll('.rowCheckbox:checked').length;
  document.getElementById('delReqId').value = ''; // Not used for bulk
  document.getElementById('delReqName').textContent = selected + ' selected item(s)';
  document.getElementById('delReqVenue').textContent = '';
  // Set the form inside the modal to submit the bulk array form instead
  const formInModal = document.querySelector('#deleteReqModal form');
  formInModal.onsubmit = function(e) {
    e.preventDefault();
    document.getElementById('bulkDeleteForm').submit();
  };
  document.getElementById('deleteReqModal').style.display = 'flex';
}

// Bulk selection logic
const selectAll = document.getElementById('selectAll');
const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
const bulkActions = document.getElementById('bulkActions');

function toggleBulkActions() {
  const anyChecked = document.querySelectorAll('.rowCheckbox:checked').length > 0;
  if(bulkActions) bulkActions.style.display = anyChecked ? 'block' : 'none';
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

function viewRequestDetails(data) {
  document.getElementById('v_full_name').textContent = data.full_name || 'N/A';
  document.getElementById('v_alumni_id').textContent = data.alumni_id || 'N/A';
  document.getElementById('v_batch_year').textContent = data.batch_year || 'N/A';
  document.getElementById('v_phone').textContent = data.phone || 'N/A';
  document.getElementById('v_email').textContent = data.email || 'N/A';
  
  document.getElementById('v_event_title').textContent = data.event_title || 'N/A';
  document.getElementById('v_event_category').textContent = data.event_category || 'N/A';
  document.getElementById('v_event_description').textContent = data.event_description || data.request_message || 'N/A';
  document.getElementById('v_purpose').textContent = data.purpose || 'N/A';
  
  document.getElementById('v_preferred_datetime').textContent = data.start_datetime ? new Date(data.start_datetime).toLocaleString() : 'N/A';
  document.getElementById('v_alternate_date').textContent = data.alternate_date || 'N/A';
  document.getElementById('v_event_duration').textContent = data.event_duration 
    ? (data.event_duration.toString().toLowerCase().includes('hour') ? data.event_duration : data.event_duration + ' Hours') 
    : 'N/A';
  
  document.getElementById('v_event_mode').textContent = data.event_mode || 'N/A';
  document.getElementById('v_venue').textContent = data.venue || 'N/A';
  document.getElementById('v_online_platform').textContent = data.online_platform || 'N/A';
  document.getElementById('v_expected_participants').textContent = data.expected_participants || 'N/A';

  document.getElementById('viewReqModal').style.display = 'flex';
}
function closeViewReqModal() {
  document.getElementById('viewReqModal').style.display = 'none';
}

// ── Live search and filtering for requests ──────────────────
const reqSearchInput = document.getElementById('reqSearch');
const statusFilterSelect = document.getElementById('statusFilter');
const reqRows = document.querySelectorAll('#reqTable tbody tr');

function filterTable() {
  const query = reqSearchInput.value.toLowerCase().trim();
  const statusFilter = statusFilterSelect.value;

  reqRows.forEach(function(row) {
    // 1. Search filter
    const name = (row.querySelector('.user-name-cell') || {}).textContent || '';
    const phone = (row.querySelector('.user-phone-cell') || {}).textContent || '';
    const venue = (row.querySelector('.venue-cell') || {}).textContent || '';
    const combined = (name + ' ' + phone + ' ' + venue).toLowerCase();
    const matchesSearch = combined.includes(query);

    // 2. Status filter
    const statusSelect = row.querySelector('.status-select-dash');
    const statusVal = statusSelect ? statusSelect.value : ''; // '1' is verified, '0' is not verified
    
    let matchesStatus = true;
    if (statusFilter === 'verified') {
      matchesStatus = (statusVal === '1');
    } else if (statusFilter === 'not_verified') {
      matchesStatus = (statusVal === '0');
    }

    row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
  });

  if (typeof toggleBulkActions === 'function') {
    toggleBulkActions();
  }
}

reqSearchInput.addEventListener('input', filterTable);
statusFilterSelect.addEventListener('change', filterTable);
</script>
</body></html>
