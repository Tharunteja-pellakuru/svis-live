<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('../db_connect.php'); ?>

<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: profile_requests.php");
    exit;
}

$reqId = intval($_GET['id']);

// Fetch the request
$reqQuery = $conn->prepare("
    SELECT p.*, r.full_name as old_name, r.email as old_email, c.name as country_name 
    FROM profile_update_requests p 
    JOIN alumni_register r ON p.alumni_id = r.id 
    LEFT JOIN countries c ON p.country = c.id 
    WHERE p.id = ?
");
$reqQuery->bind_param("i", $reqId);
$reqQuery->execute();
$reqData = $reqQuery->get_result()->fetch_assoc();

if (!$reqData) {
    header("Location: profile_requests.php");
    exit;
}

// Fetch current data for comparison
$alumniId = $reqData['alumni_id'];
$currQuery = $conn->prepare("
    SELECT a.*, c.name as country_name 
    FROM alumni_register a 
    LEFT JOIN countries c ON a.country = c.id 
    WHERE a.id = ?
");
$currQuery->bind_param("i", $alumniId);
$currQuery->execute();
$currData = $currQuery->get_result()->fetch_assoc();

$fields = [
    'full_name' => 'Full Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'gender' => 'Gender',
    'batch_year' => 'Batch Year',
    'bio' => 'Bio',
    'Industry' => 'Industry',
    'Company / Organization Name' => 'Company / Organization Name',
    'City' => 'City',
    'country' => 'Country',
    'education_qualification' => 'Education Qualification',
    'college_university' => 'College/University',
    'education_year' => 'Education Year',
    'linkedin' => 'LinkedIn',
    'instagram' => 'Instagram',
    'Current Occupation' => 'Current Occupation',
    'Designation' => 'Designation',
    'Work Experience' => 'Work Experience',
    'dob' => 'Date of Birth',
    'phonecode' => 'Phone Code',
    'current_address' => 'Current Address'
];
?>

<div class="dash-main">
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
      <div>
        <div class="page-header-label">Verification</div>
        <div class="page-header-title">Verify Profile Update</div>
      </div>
    </div>
    <a href="profile_requests.php" class="btn-back" style="display:flex; align-items:center; gap:8px; text-decoration:none; color:var(--text-2); font-weight:700;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to List
    </a>
  </div>

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
  
  .dash-main { flex: 1; min-width: 0; padding: 2rem 2.25rem; background: var(--bg); min-height: 100vh; }
  
  .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
  .page-header-left { display: flex; align-items: center; gap: 14px; }
  .page-header-icon { width: 46px; height: 46px; background: var(--blue-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .page-header-icon svg { width: 20px; height: 20px; stroke: var(--blue); fill: none; stroke-width: 1.75; }
  .page-header-label { font-size: .72rem; font-weight: 700; letter-spacing: .13em; text-transform: uppercase; color: var(--text-3); margin-bottom: 2px; }
  .page-header-title { font-size: 1.6rem; font-weight: 900; color: var(--blue-dark); line-height: 1.1; }
  
  .btn-back { display: inline-flex; align-items: center; gap: 7px; padding: 0 1.1rem; height: 40px; background: var(--surface); color: var(--text-2); font-size: .85rem; font-weight: 700; border: 1.5px solid var(--border); border-radius: 9px; text-decoration: none; transition: all .15s; }
  .btn-back:hover { background: var(--blue-light); color: var(--blue); border-color: rgba(26,86,160,.2); }

  .panel { background: var(--surface); border: 1px solid var(--border); border-radius: var(--r); box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 1.5rem; }
  .panel-head { padding: 1.25rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; gap: 10px; }
  .panel-head-icon { width: 30px; height: 30px; background: rgba(26,86,160,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; }
  .panel-head-icon svg { width: 14px; height: 14px; stroke: var(--blue); }
  .panel-head-title { font-size: 1rem; font-weight: 800; color: var(--blue-dark); }

  .compare-table { width: 100%; border-collapse: collapse; }
  .compare-table th, .compare-table td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-soft); text-align: left; vertical-align: middle; }
  .compare-table th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-3); width: 25%; background: var(--surface-alt); border-right: 1px solid var(--border-soft); }
  .compare-table .diff-old { color: #9ca3af; font-size: 0.88rem; font-weight: 400; width: 37.5%; }
  .compare-table .diff-new { color: var(--text); font-size: 0.92rem; font-weight: 500; width: 37.5%; }
  
  .compare-table .changed { background: rgba(251, 191, 36, 0.04); }
  .compare-table .changed th { color: #b45309; }
  .compare-table .changed .diff-new { color: #b45309; font-weight: 700; background: rgba(251, 191, 36, 0.08); }

  .img-compare { display: flex; gap: 3rem; padding: 2rem 3rem; background: #fff; align-items: center; border-bottom: 1px solid var(--border-soft); }
  .img-box { text-align: center; }
  .img-box img { width: 100px; height: 100px; border-radius: 14px; object-fit: cover; border: 3px solid var(--blue-light); box-shadow: var(--shadow-sm); }
  .img-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: var(--text-3); margin-top: 10px; letter-spacing: 0.05em; }
  
  .action-footer { position: sticky; bottom: 0; background: var(--surface); padding: 1.25rem 2.25rem; border-top: 1px solid var(--border); display: flex; justify-content: center; gap: 1rem; box-shadow: 0 -4px 20px rgba(0,0,0,0.03); }
  .btn-approve { background: var(--green); color: white; padding: 0.75rem 2.5rem; border-radius: 10px; font-weight: 700; border: none; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 4px 12px var(--green-bg); }
  .btn-approve:hover { background: #086d50; transform: translateY(-1px); }
  .btn-reject { background: var(--red); color: white; padding: 0.75rem 2.5rem; border-radius: 10px; font-weight: 700; border: none; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 4px 12px var(--red-bg); }
  .btn-reject:hover { background: #a93226; transform: translateY(-1px); }
</style>

<div class="dash-main">
  <!-- <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M18 8l2 2 4-4"/></svg>
      </div>
      <div>
        <div class="page-header-label">Verification</div>
        <div class="page-header-title">Profile Update Request</div>
      </div>
    </div>
    <a href="profile_requests.php" class="btn-back">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to List
    </a>
  </div> -->

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></div>
      <span class="panel-head-title">Verification Details</span>
    </div>

    <?php if ($reqData['user_image'] != $currData['user_image']): ?>
    <div class="img-compare">
      <div class="img-box">
        <img src="../uploads/<?= htmlspecialchars($currData['user_image'] ?: 'default.png') ?>" alt="Old">
        <div class="img-label">Current Photo</div>
      </div>
      <div style="color:var(--text-3);"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></div>
      <div class="img-box">
        <img src="../uploads/<?= htmlspecialchars($reqData['user_image'] ?: 'default.png') ?>" alt="New">
        <div class="img-label" style="color:#b45309;">Requested Change</div>
      </div>
    </div>
    <?php endif; ?>

    <table class="compare-table">
      <thead>
        <tr>
          <th>Field Name</th>
          <th>Current Information</th>
          <th>New Information</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($fields as $key => $label): 
          $oldVal = $currData[$key];
          $newVal = $reqData[$key];
          $isChanged = ($oldVal != $newVal);
          
          if ($key === 'country') {
              $oldVal = $currData['country_name'] ?? $oldVal;
              $newVal = $reqData['country_name'] ?? $newVal;
          }
        ?>
        <tr class="<?= $isChanged ? 'changed' : '' ?>">
          <th><?= $label ?></th>
          <td class="diff-old"><?= htmlspecialchars($oldVal ?: '—') ?></td>
          <td class="diff-new"><?= htmlspecialchars($newVal ?: '—') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if (check_admin_permission('edit')): ?>
  <form method="POST" action="handle_profile_request.php" class="action-footer">
    <input type="hidden" name="request_id" value="<?= $reqId ?>">
    <button type="submit" name="action" value="approve" class="btn-approve">Approve Update</button>
    <button type="submit" name="action" value="reject" class="btn-reject">Reject Request</button>
  </form>
  <?php else: ?>
  <div class="action-footer" style="color:var(--text-3); font-weight:700;">
    View-only Mode: You do not have permission to approve/reject update requests.
  </div>
  <?php endif; ?>
</div>
</body></html>
