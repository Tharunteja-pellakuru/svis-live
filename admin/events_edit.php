<?php ob_start(); include('header.php'); require_admin_permission('edit'); include('sidebar.php'); include('../db_connect.php'); 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { $_SESSION['flash_msg'] = "Invalid event ID"; $_SESSION['flash_type'] = "danger"; header("Location: events_list.php"); exit; }
$id = (int)$_GET['id'];
if (isset($_POST['update'])) {
    $event_name = trim($_POST['event_name']); $event_category = trim($_POST['event_category'] ?? 'Networking'); $start_time = $_POST['start_time']; $end_time = $_POST['end_time']; $venue = trim($_POST['venue']); $description = trim($_POST['description']); $old_image = $_POST['old_image']; $new_image = $old_image; $has_fee = trim($_POST['has_registration_fee'] ?? 'No'); $fee_amount = ($has_fee === 'Yes') ? floatval($_POST['registration_fee_amount'] ?? 0) : 0.00;
    $delete_image = isset($_POST['delete_image']) ? true : false;

    if ($delete_image && empty($_FILES['event_image']['name'])) {
        $new_image = "";
        if (!empty($old_image) && file_exists("../uploads/" . $old_image)) { 
            unlink("../uploads/" . $old_image); 
        }
    } elseif (!empty($_FILES['event_image']['name']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) { 
        $temp_image = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['event_image']['name'])); 
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], "../uploads/" . $temp_image)) { 
            $new_image = $temp_image;
            if (!empty($old_image) && file_exists("../uploads/" . $old_image)) { 
                unlink("../uploads/" . $old_image); 
            } 
        } else {
            $_SESSION['flash_msg'] = "Failed to move uploaded file. Check folder permissions or file size."; 
            $_SESSION['flash_type'] = "danger"; 
            header("Location: events_list.php"); 
            exit();
        }
    } elseif (!empty($_FILES['event_image']['name']) && $_FILES['event_image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['flash_msg'] = "Upload error code: " . $_FILES['event_image']['error']; 
        $_SESSION['flash_type'] = "danger"; 
        header("Location: events_list.php"); 
        exit();
    }
    $stmt = $conn->prepare("UPDATE events SET event_name=?, event_category=?, event_image=?, start_time=?, end_time=?, venue=?, description=?, has_registration_fee=?, registration_fee_amount=? WHERE id=?");
    $stmt->bind_param("ssssssssid", $event_name, $event_category, $new_image, $start_time, $end_time, $venue, $description, $has_fee, $fee_amount, $id);
    if ($stmt->execute()) { $_SESSION['flash_msg'] = "Event updated successfully!"; $_SESSION['flash_type'] = "success"; header("Location: events_list.php"); exit(); }
}
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?"); $stmt->bind_param("i", $id); $stmt->execute(); $event = $stmt->get_result()->fetch_assoc();
if (!$event) { $_SESSION['flash_msg'] = "Event not found"; $_SESSION['flash_type'] = "danger"; header("Location: events_list.php"); exit; } ?>
<div class="dash-main">
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></div>
      <div><div class="page-header-label">Calendar</div><div class="page-header-title">Edit Event</div></div>
    </div>
    <a href="events_list.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back to List</a>
  </div>
  <style>
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { .form-grid-2 { grid-template-columns: 1fr; } }
    .field { margin-bottom: 1.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap input, .input-wrap select, .input-wrap textarea { width: 100%; height: 44px; padding: 0 14px; font-family: 'Lato', sans-serif; font-size: 0.92rem; background: var(--surface-alt); border: 1.5px solid var(--border); border-radius: 9px; outline: none; transition: all 0.18s; }
    .input-wrap textarea { height: auto; min-height: 100px; padding: 12px 14px; resize: vertical; }
    .input-wrap input:focus, .input-wrap textarea:focus { border-color: var(--blue); background: white; box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    .current-preview { margin-bottom: 1.25rem; padding: 14px; background: var(--surface-alt); border: 1px solid var(--border-soft); border-radius: 12px; display: flex; align-items: center; gap: 15px; }
    .preview-img { width: 120px; height: 68px; border-radius: 8px; object-fit: cover; border: 2px solid white; box-shadow: var(--shadow-sm); }
    .file-drop { border: 2px dashed var(--border); border-radius: 12px; padding: 1.5rem; text-align: center; background: var(--surface-alt); cursor: pointer; transition: all 0.18s; position: relative; }
    .file-drop:hover { border-color: var(--blue); background: var(--blue-light, #eef4ff); }
    .file-drop input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2; }
    .btn-save-event { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); margin-top: 0.5rem; }
    .btn-save-event:hover { background: var(--blue-hover, #1557b0); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
  </style>
  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left"><div class="panel-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><span class="panel-head-title">Update Event Info</span></div>
    </div>
    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="old_image" value="<?= $event['event_image'] ?>">
        <div class="field"><label class="field-label">Event Name</label><div class="input-wrap"><input type="text" name="event_name" value="<?= htmlspecialchars($event['event_name']) ?>" required></div></div>
        <div class="field"><label class="field-label">Event Category <span style="color:#e53e3e">*</span></label><div class="input-wrap"><select name="event_category" required><option value="REUNION" <?= ($event['event_category'] === 'REUNION') ? 'selected' : '' ?>>REUNION</option><option value="WEBINAR/WORKSHOP" <?= ($event['event_category'] === 'WEBINAR/WORKSHOP') ? 'selected' : '' ?>>WEBINAR/WORKSHOP</option><option value="NETWORKING" <?= ($event['event_category'] === 'NETWORKING') ? 'selected' : '' ?>>NETWORKING</option><option value="CELEBRATION" <?= ($event['event_category'] === 'CELEBRATION') ? 'selected' : '' ?>>CELEBRATION</option></select></div></div>
        <div class="field"><label class="field-label">Event Venue</label><div class="input-wrap"><input type="text" name="venue" list="venue_options" placeholder="Select from dropdown or type a custom venue..." value="<?= htmlspecialchars($event['venue']) ?>" required><datalist id="venue_options"><option value="On Campus"><option value="Online"></datalist></div></div>
        <div class="field"><label class="field-label">Registration Fee Required?</label><div class="input-wrap"><select id="has_registration_fee" name="has_registration_fee" onchange="toggleFeeAmount()"><option value="No" <?= ($event['has_registration_fee'] ?? 'No') === 'No' ? 'selected' : '' ?>>No, it's free</option><option value="Yes" <?= ($event['has_registration_fee'] ?? 'No') === 'Yes' ? 'selected' : '' ?>>Yes, there is a fee</option></select></div></div>
        <div class="field" id="feeAmountField" style="display: <?= ($event['has_registration_fee'] ?? 'No') === 'Yes' ? 'block' : 'none' ?>;"><label class="field-label">Registration Fee Amount (INR)</label><div class="input-wrap"><input type="number" step="0.01" min="0" id="registration_fee_amount" name="registration_fee_amount" value="<?= htmlspecialchars($event['registration_fee_amount'] ?? '') ?>"></div></div>
        <div class="form-grid-2">
          <div class="field"><label class="field-label">Start Time</label><div class="input-wrap"><input type="datetime-local" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($event['start_time'])) ?>" required></div></div>
          <div class="field"><label class="field-label">End Time</label><div class="input-wrap"><input type="datetime-local" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($event['end_time'])) ?>" required></div></div>
        </div>

        <!-- Current Banner -->
        <div class="field">
          <label class="field-label">Current Banner</label>
          <?php if (!empty($event['event_image'])): ?>
            <div class="current-preview" id="currentBanner">
              <img src="../uploads/<?= $event['event_image'] ?>" class="preview-img" id="currentImg">
              <div style="flex: 1;">
                <div style="font-size:0.8rem; font-weight:700; color:var(--text-2);"><?= htmlspecialchars($event['event_image']) ?></div>
                <label style="display:inline-flex; align-items:center; gap:8px; margin-top:8px; font-size:0.8rem; color:#e53e3e; font-weight:600; cursor:pointer;">
                  <input type="checkbox" name="delete_image" value="1" id="deleteImageCheckbox">
                  Delete current banner
                </label>
              </div>
            </div>
          <?php else: ?>
            <div style="padding:12px; font-size:0.85rem; color:var(--text-3); background:var(--surface-alt); border-radius:9px; margin-bottom:1rem;">No banner uploaded yet.</div>
          <?php endif; ?>
        </div>

        <!-- Upload New Banner -->
        <div class="field">
          <label class="field-label">Upload New Banner <span style="font-weight:400; text-transform:none; font-size:0.72rem; color:var(--text-3)">(Optional)</span></label>
          <div class="file-drop" id="fileDrop">
            <input type="file" name="event_image" id="eventImg" accept=".jpg,.jpeg,.png,.gif,.webp" onchange="previewNewImage()">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--blue,#1a56a0)" stroke-width="1.5" style="margin-bottom:8px; display:block; margin-left:auto; margin-right:auto;">
              <rect x="3" y="3" width="18" height="18" rx="2"/>
              <circle cx="8.5" cy="8.5" r="1.5"/>
              <polyline points="21 15 16 10 5 21"/>
            </svg>
            <div style="font-weight:700; color:var(--blue-dark,#1a56a0); margin-bottom:4px;" id="fileMain">Click to upload new banner</div>
            <div style="font-size:0.75rem; color:var(--text-3,#888);" id="fileSub">JPG, PNG, GIF, WEBP &nbsp;·&nbsp; Recommended 16:9</div>
          </div>
          <div id="newPreviewWrap" style="display:none; margin-top:12px; padding:14px; background:var(--surface-alt); border:1.5px solid var(--border-soft); border-radius:12px;">
            <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--blue); margin-bottom:8px;">New Image Preview</div>
            <img id="newPreview" src="" alt="New Preview" style="max-height:180px; border-radius:8px; border:1.5px solid var(--border); object-fit:cover; display:block;">
            <button type="button" onclick="clearNewImage()" style="margin-top:8px; font-size:0.78rem; color:#e53e3e; background:none; border:none; cursor:pointer; padding:0; font-weight:600;">✕ Remove selected image</button>
          </div>
        </div>

        <div class="field"><label class="field-label">Event Description</label><div class="input-wrap"><textarea name="description"><?= htmlspecialchars($event['description']) ?></textarea></div></div>
        <button type="submit" name="update" class="btn-save-event">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<script>
  function toggleFeeAmount() {
    const select = document.getElementById('has_registration_fee');
    const field = document.getElementById('feeAmountField');
    if (select.value === 'Yes') {
      field.style.display = 'block';
    } else {
      field.style.display = 'none';
      document.getElementById('registration_fee_amount').value = '';
    }
  }

  function previewNewImage() {
    const input = document.getElementById('eventImg');
    if (input.files && input.files[0]) {
      const file = input.files[0];
      document.getElementById('fileMain').textContent = file.name;
      document.getElementById('fileSub').textContent = 'Selected · ' + (file.size / 1024).toFixed(1) + ' KB';

      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('newPreview').src = e.target.result;
        document.getElementById('newPreviewWrap').style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  }

  function clearNewImage() {
    document.getElementById('eventImg').value = '';
    document.getElementById('newPreview').src = '';
    document.getElementById('newPreviewWrap').style.display = 'none';
    document.getElementById('fileMain').textContent = 'Click to upload new banner';
    document.getElementById('fileSub').textContent = 'JPG, PNG, GIF, WEBP · Recommended 16:9';
    const deleteCheckbox = document.getElementById('deleteImageCheckbox');
    if(deleteCheckbox) {
        deleteCheckbox.disabled = false;
    }
  }

  // If a new image is selected, the existing one will be replaced anyway, so we disable the delete checkbox
  document.getElementById('eventImg').addEventListener('change', function() {
    const deleteCheckbox = document.getElementById('deleteImageCheckbox');
    if(deleteCheckbox && this.files.length > 0) {
        deleteCheckbox.checked = false;
        deleteCheckbox.disabled = true;
    }
  });
</script>
</body></html>
