<?php
session_start();
include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('../db_connect.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request'])) {
    require_admin_permission('delete');
    if (isset($_POST['ids']) && is_array($_POST['ids'])) {
        $count = 0;
        $stmt = $conn->prepare("DELETE FROM profile_update_requests WHERE id = ?");
        foreach ($_POST['ids'] as $req_id) {
            $id = intval($req_id);
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) $count++;
            }
        }
        if ($stmt) $stmt->close();
        $_SESSION['flash_msg'] = "$count Request(s) deleted successfully.";
        $_SESSION['flash_type'] = "success";
    }
    echo "<script>window.location.href='profile_requests.php';</script>";
    exit;
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

  <!-- Page Header -->
  <!-- <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M18 8l2 2 4-4"/></svg>
      </div>
      <div>
        <div class="page-header-label">Users</div>
        <div class="page-header-title">Profile Update Requests</div>
      </div>
    </div>
  </div> -->

  <?php
  // Basic stats
  $totalRequests = $conn->query("SELECT COUNT(*) as total FROM profile_update_requests")->fetch_assoc()['total'];
  $pendingRequests = $conn->query("SELECT COUNT(*) as total FROM profile_update_requests WHERE status = 0")->fetch_assoc()['total'];
  ?>

<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

<style>
  :root {
    --blue: #1a56a0; --blue-dark: #0f3566; --blue-hover: #154a8a; --blue-light: #e8f0fb;
    --bg: #f0f4fa; --surface: #fff; --surface-alt: #f7f9fd;
    --border: rgba(26,86,160,.12); --border-soft: rgba(26,86,160,.07);
    --text: #0f2545; --text-2: #4a6080; --text-3: #8aa0bb;
    --green: #0a7a5a; --green-bg: rgba(15,168,126,.10);
    --red: #c0392b; --red-bg: rgba(192,57,43,.10);
    --shadow-sm: 0 1px 3px rgba(15,53,102,.08),0 1px 2px rgba(15,53,102,.05);
    --r: 14px;
  }

  .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
  .mini-stat { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1.25rem; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; gap: 5px; }
  .mini-stat-label { font-size: 0.72rem; font-weight: 700; color: var(--text-3); text-transform: uppercase; letter-spacing: 0.05em; }
  .mini-stat-val { font-size: 1.75rem; font-weight: 900; color: var(--blue-dark); }
  
  .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
  .badge-pending { background: #fff9e6; color: #856404; }
  .badge-approved { background: var(--green-bg); color: var(--green); }
  .badge-rejected { background: var(--red-bg); color: var(--red); }

  .filter-bar { display: flex; flex-wrap: nowrap; gap: 8px; padding: 1.25rem 1.5rem 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
  .filter-bar::-webkit-scrollbar { display: none; }
  .filter-pill { display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1.5px solid var(--border); background: var(--surface-alt); color: var(--text-3); cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
  .filter-pill:hover { border-color: var(--blue); color: var(--blue); }
  .filter-pill.active { background: var(--blue); color: white; border-color: var(--blue); }
  .filter-pill .pill-count { font-size: 0.68rem; background: rgba(0,0,0,0.08); padding: 1px 7px; border-radius: 10px; font-weight: 800; }
  .filter-pill.active .pill-count { background: rgba(255,255,255,0.25); }

  .btn-review { display: inline-flex; align-items: center; gap: 7px; padding: 0 14px; height: 36px; background: var(--blue-light); color: var(--blue); border-radius: 9px; font-size: 0.8rem; font-weight: 700; text-decoration: none; border: 1.5px solid rgba(26,86,160,0.08); transition: all 0.2s; }
  .btn-review:hover { background: var(--blue); color: white; border-color: var(--blue); transform: translateY(-1px); }
</style>

<div class="dash-main">
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M18 8l2 2 4-4"/></svg>
      </div>
      <div>
        <div class="page-header-label">Users</div>
        <div class="page-header-title">Profile Update Requests</div>
      </div>
    </div>
  </div>

  <div class="stat-row">
    <div class="mini-stat">
      <div class="mini-stat-label">Total Requests</div>
      <div class="mini-stat-val"><?= $totalRequests ?></div>
    </div>
    <div class="mini-stat">
      <div class="mini-stat-label">Pending Approval</div>
      <div class="mini-stat-val"><?= $pendingRequests ?></div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
        <span class="panel-head-title">Update Request Registry</span>
      </div>
      <div class="panel-head-right">
        <div class="search-form">
          <input type="text" id="liveSearch" class="search-input" placeholder="Search name, email, batch..." autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>
      </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="filter-bar">
      <div class="filter-pill active" data-filter="all">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        All <span class="pill-count"><?= $totalRequests ?></span>
      </div>
      <div class="filter-pill" data-filter="pending">Pending <span class="pill-count"><?= $pendingRequests ?></span></div>
      <div class="filter-pill" data-filter="approved">Approved <span class="pill-count"><?= $conn->query("SELECT COUNT(*) as total FROM profile_update_requests WHERE status = 1")->fetch_assoc()['total'] ?></span></div>
      <div class="filter-pill" data-filter="rejected">Rejected <span class="pill-count"><?= $conn->query("SELECT COUNT(*) as total FROM profile_update_requests WHERE status = 2")->fetch_assoc()['total'] ?></span></div>
    </div>

    <form id="bulkDeleteForm" action="profile_requests.php" method="POST">
    <input type="hidden" name="delete_request" value="1">
    <?php if (check_admin_permission('delete')): ?>
    <div id="bulkActions" style="display:none; padding: 1rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border);">
      <button type="button" onclick="confirmBulkDelete()" class="btn-action-sm btn-delete-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; width:max-content; background: var(--red-bg, #fef2f2); color: var(--red, #c0392b); border: 1.5px solid rgba(192,57,43,0.1); border-radius: 8px; cursor: pointer; transition: all 0.15s;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Delete Selected
      </button>
    </div>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="dash-table" id="requestsTable">
        <thead>
          <tr>
            <?php if (check_admin_permission('delete')): ?>
            <th style="width: 40px; text-align:center;"><input type="checkbox" id="selectAll"></th>
            <?php endif; ?>
            <th>User</th>
            <th>Batch</th>
            <th>Requested On</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT p.*, r.full_name as current_name, r.batch_year as current_batch 
                  FROM profile_update_requests p 
                  JOIN alumni_register r ON p.alumni_id = r.id 
                  ORDER BY p.status ASC, p.created_at DESC";
          $result = $conn->query($sql);
          
          if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
              $statusClass = ($row['status'] == 1) ? 'badge-approved' : (($row['status'] == 2) ? 'badge-rejected' : 'badge-pending');
              $statusText = ($row['status'] == 1) ? 'Approved' : (($row['status'] == 2) ? 'Rejected' : 'Pending');
              $filterVal = ($row['status'] == 1) ? 'approved' : (($row['status'] == 2) ? 'rejected' : 'pending');
          ?>
          <tr data-status="<?= $filterVal ?>">
            <?php if (check_admin_permission('delete')): ?>
            <td style="text-align:center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="rowCheckbox"></td>
            <?php endif; ?>
            <td data-label="User">
              <div class="search-target" style="font-weight:700; color:var(--text);"><?= htmlspecialchars($row['full_name']) ?></div>
              <div class="search-target" style="font-size:0.8rem; color:var(--text-3);"><?= htmlspecialchars($row['email']) ?></div>
            </td>
            <td data-label="Batch" class="search-target"><?= htmlspecialchars($row['batch_year']) ?></td>
            <td data-label="Date"><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
            <td data-label="Status"><span class="badge-status <?= $statusClass ?>"><?= $statusText ?></span></td>
            <td data-label="Action">
              <?php if ($row['status'] == 0): ?>
              <a href="profile_request_view.php?id=<?= $row['id'] ?>" class="btn-review">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Review & Verify
              </a>
              <?php else: ?>
                <span style="font-size:0.8rem; color:var(--text-3); font-style:italic;">Processed</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php 
            endwhile;
          else:
          ?>
          <tr>
            <td colspan="<?= check_admin_permission('delete') ? 6 : 5 ?>" style="text-align:center; padding:3rem; color:var(--text-3);">No profile update requests found.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
      </form>
    </div>
  </div>
</div>

<script>
  function confirmBulkDelete() {
    const selected = document.querySelectorAll('.rowCheckbox:checked').length;
    if(confirm(`Are you sure you want to delete ${selected} selected request(s)? This action cannot be undone.`)) {
      document.getElementById('bulkDeleteForm').submit();
    }
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

  (function() {
    const searchInput = document.getElementById('liveSearch');
    const tableBody = document.querySelector('#requestsTable tbody');
    const rows = tableBody.querySelectorAll('tr:not(:last-child)'); // exclude empty message if exists
    const pills = document.querySelectorAll('.filter-pill');
    let activeFilter = 'all';

    function applyFilters() {
      const query = searchInput.value.toLowerCase().trim();

      rows.forEach(row => {
        if (!row.dataset.status) return;
        
        const targets = row.querySelectorAll('.search-target');
        let combinedText = '';
        targets.forEach(t => combinedText += t.textContent + ' ');
        combinedText = combinedText.toLowerCase();

        const matchesSearch = combinedText.includes(query);
        const matchesStatus = (activeFilter === 'all') || (row.dataset.status === activeFilter);

        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
      });
    }

    searchInput.addEventListener('input', applyFilters);

    pills.forEach(pill => {
      pill.addEventListener('click', function() {
        pills.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        activeFilter = this.dataset.filter;
        applyFilters();
      });
    });
  })();
</script>
</body></html>
