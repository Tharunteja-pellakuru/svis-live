<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('header.php');
require_admin_permission('add');
include('sidebar.php');
include('../db_connect.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_name  = trim($_POST['event_name'] ?? '');
    $event_category = trim($_POST['event_category'] ?? 'Networking');
    $has_fee     = trim($_POST['has_registration_fee'] ?? 'No');
    $fee_amount  = ($has_fee === 'Yes') ? floatval($_POST['registration_fee_amount'] ?? 0) : 0.00;
    $start_time  = trim($_POST['start_time'] ?? '');
    $end_time    = trim($_POST['end_time']   ?? '');
    $venue       = trim($_POST['venue']      ?? '');
    $description = trim($_POST['description'] ?? '');
    $file_name   = '';
    $error       = '';

    // Validate required fields
    if (empty($event_name) || empty($start_time) || empty($end_time) || empty($venue)) {
        $error = "Please fill in all required fields.";
    }

    // Handle image upload
    if (empty($error) && !empty($_FILES['event_image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Invalid file type. Only JPG, PNG, GIF, WEBP allowed.";
        } elseif ($_FILES['event_image']['error'] !== UPLOAD_ERR_OK) {
            $error = "File upload error code: " . $_FILES['event_image']['error'];
        } else {
            $upload_dir = "../uploads/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['event_image']['name']));

            if (!move_uploaded_file($_FILES['event_image']['tmp_name'], $upload_dir . $file_name)) {
                $error = "Image upload failed. Check that '../uploads/' folder exists and is writable.";
                $file_name = '';
            }
        }
    }

    // Insert into DB
    if (empty($error)) {
        if (!$conn) {
            $error = "Database connection failed.";
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO events (event_name, event_category, event_image, start_time, end_time, venue, description, has_registration_fee, registration_fee_amount)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                $error = "Prepare failed: " . $conn->error;
            } else {
                $stmt->bind_param("ssssssssd", $event_name, $event_category, $file_name, $start_time, $end_time, $venue, $description, $has_fee, $fee_amount);

                if ($stmt->execute()) {
                    $stmt->close();
                    $_SESSION['flash_msg']  = "Event created successfully!";
                    $_SESSION['flash_type'] = "success";
                    header("Location: events_list.php");
                    exit();
                } else {
                    $error = "Execute failed: " . $stmt->error;
                    $stmt->close();
                }
            }
        }
    }

    $_SESSION['flash_msg']  = $error;
    $_SESSION['flash_type'] = "error";
}
?>

<div class="dash-main">

  <!-- Flash Message -->
  <?php if (!empty($_SESSION['flash_msg'])): ?>
    <div style="
      padding: 13px 18px;
      margin-bottom: 1.2rem;
      border-radius: 9px;
      font-weight: 600;
      font-size: 0.92rem;
      display: flex;
      align-items: center;
      gap: 10px;
      background: <?= $_SESSION['flash_type'] === 'success' ? '#d1fae5' : '#fee2e2' ?>;
      color:       <?= $_SESSION['flash_type'] === 'success' ? '#065f46' : '#991b1b' ?>;
      border: 1.5px solid <?= $_SESSION['flash_type'] === 'success' ? '#6ee7b7' : '#fca5a5' ?>;
    ">
      <?php if ($_SESSION['flash_type'] === 'success'): ?>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <?php else: ?>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php endif; ?>
      <?= htmlspecialchars($_SESSION['flash_msg']) ?>
    </div>
    <?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
      </div>
      <div>
        <div class="page-header-label">Calendar</div>
        <div class="page-header-title">Create New Event</div>
      </div>
    </div>
    <a href="events_list.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
      </svg>
      Back to List
    </a>
  </div>

  <style>
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { .form-grid-2 { grid-template-columns: 1fr; } }
    .field { margin-bottom: 1.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap input,
    .input-wrap select,
    .input-wrap textarea { width: 100%; height: 44px; padding: 0 14px; font-family: 'Lato', sans-serif; font-size: 0.92rem; background: var(--surface-alt); border: 1.5px solid var(--border); border-radius: 9px; outline: none; transition: all 0.18s; box-sizing: border-box; color: var(--text); }
    .input-wrap textarea { height: auto; min-height: 100px; padding: 12px 14px; resize: vertical; }
    .input-wrap input:focus,
    .input-wrap select:focus,
    .input-wrap textarea:focus { border-color: var(--blue); background: white; box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    .input-wrap input.is-invalid,
    .input-wrap textarea.is-invalid { border-color: #e53e3e !important; box-shadow: 0 0 0 3px rgba(229,62,62,0.1); }
    .file-drop { border: 2px dashed var(--border); border-radius: 12px; padding: 1.5rem; text-align: center; background: var(--surface-alt); cursor: pointer; transition: all 0.18s; position: relative; }
    .file-drop:hover { border-color: var(--blue); background: var(--blue-light, #eef4ff); }
    .file-drop input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2; }
    .btn-save-event { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); margin-top: 0.5rem; }
    .btn-save-event:hover { background: var(--blue-hover, #1557b0); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
    .btn-save-event:disabled { opacity: 0.65; cursor: not-allowed; }
    .field-error { font-size: 0.78rem; color: #e53e3e; margin-top: 5px; display: none; }
    .required-note { font-size: 0.78rem; color: var(--text-3, #888); margin-bottom: 1.25rem; }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon">
          <svg viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <span class="panel-head-title">Event Configuration</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <p class="required-note"><span style="color:#e53e3e">*</span> Required fields</p>

      <form method="POST" enctype="multipart/form-data" id="eventForm" novalidate>

        <!-- Event Title -->
        <div class="field">
          <label class="field-label" for="event_name">
            Event Name <span style="color:#e53e3e">*</span>
          </label>
          <div class="input-wrap">
            <input type="text" id="event_name" name="event_name"
                   placeholder="Enter event name"
                   value="<?= htmlspecialchars($_POST['event_name'] ?? '') ?>"
                   autocomplete="off" required>
          </div>
          <div class="field-error" id="err_event_name">Event title is required.</div>
        </div>

        <!-- Event Category -->
        <div class="field">
          <label class="field-label" for="event_category">
            Event Category <span style="color:#e53e3e">*</span>
          </label>
          <div class="input-wrap">
            <select id="event_category" name="event_category" required>
              <option value="REUNION" <?= ($_POST['event_category'] ?? '') === 'REUNION' ? 'selected' : '' ?>>REUNION</option>
              <option value="WEBINAR/WORKSHOP" <?= ($_POST['event_category'] ?? '') === 'WEBINAR/WORKSHOP' ? 'selected' : '' ?>>WEBINAR/WORKSHOP</option>
              <option value="NETWORKING" <?= ($_POST['event_category'] ?? 'NETWORKING') === 'NETWORKING' ? 'selected' : '' ?>>NETWORKING</option>
              <option value="CELEBRATION" <?= ($_POST['event_category'] ?? '') === 'CELEBRATION' ? 'selected' : '' ?>>CELEBRATION</option>
            </select>
          </div>
        </div>

        <!-- Venue -->
        <div class="field">
          <label class="field-label" for="venue">
            Event Venue <span style="color:#e53e3e">*</span>
          </label>
          <div class="input-wrap">
            <input type="text" id="venue" name="venue" list="venue_options"
                   placeholder="Select from dropdown or type a custom venue..."
                   value="<?= htmlspecialchars($_POST['venue'] ?? '') ?>"
                   autocomplete="off" required>
            <datalist id="venue_options">
              <option value="On Campus">
              <option value="Online">
            </datalist>
          </div>
          <div class="field-error" id="err_venue">Venue is required.</div>
        </div>

        <!-- Registration Fee Toggle -->
        <div class="field">
          <label class="field-label" for="has_registration_fee">Registration Fee Required?</label>
          <div class="input-wrap">
            <select id="has_registration_fee" name="has_registration_fee" onchange="toggleFeeAmount()">
              <option value="No">No, it's free</option>
              <option value="Yes">Yes, there is a fee</option>
            </select>
          </div>
        </div>

        <div class="field" id="feeAmountField" style="display: none;">
          <label class="field-label" for="registration_fee_amount">Registration Fee Amount (INR)</label>
          <div class="input-wrap">
            <input type="number" step="0.01" min="0" id="registration_fee_amount" name="registration_fee_amount" placeholder="e.g. 500.00" autocomplete="off">
          </div>
        </div>

        <!-- Start & End Time -->
        <div class="form-grid-2">
          <div class="field">
            <label class="field-label" for="start_time">
              Start Time <span style="color:#e53e3e">*</span>
            </label>
            <div class="input-wrap">
              <input type="datetime-local" id="start_time" name="start_time"
                     value="<?= htmlspecialchars($_POST['start_time'] ?? '') ?>"
                     required>
            </div>
            <div class="field-error" id="err_start_time">Start time is required.</div>
          </div>
          <div class="field">
            <label class="field-label" for="end_time">
              End Time <span style="color:#e53e3e">*</span>
            </label>
            <div class="input-wrap">
              <input type="datetime-local" id="end_time" name="end_time"
                     value="<?= htmlspecialchars($_POST['end_time'] ?? '') ?>"
                     required>
            </div>
            <div class="field-error" id="err_end_time">End time must be after start time.</div>
          </div>
        </div>

        <!-- Promotional Banner -->
        <div class="field">
          <label class="field-label">Promotional Banner <span style="font-weight:400; text-transform:none; font-size:0.72rem; color:var(--text-3)">(Optional)</span></label>
          <div class="file-drop" id="fileDrop">
            <input type="file" name="event_image" id="eventImg"
                   accept=".jpg,.jpeg,.png,.gif,.webp"
                   onchange="updateLabel()">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--blue,#1a56a0)" stroke-width="1.5" style="margin-bottom:8px; display:block; margin-left:auto; margin-right:auto;">
              <rect x="3" y="3" width="18" height="18" rx="2"/>
              <circle cx="8.5" cy="8.5" r="1.5"/>
              <polyline points="21 15 16 10 5 21"/>
            </svg>
            <div style="font-weight:700; color:var(--blue-dark,#1a56a0); margin-bottom:4px;" id="fileMain">Click to upload banner</div>
            <div style="font-size:0.75rem; color:var(--text-3,#888);" id="fileSub">JPG, PNG, GIF, WEBP &nbsp;·&nbsp; Recommended 16:9</div>
          </div>
          <div id="imgPreviewWrap" style="display:none; margin-top:10px;">
            <img id="imgPreview" src="" alt="Preview"
                 style="max-height:160px; border-radius:8px; border:1.5px solid var(--border); object-fit:cover; display:block;">
            <button type="button" onclick="clearImage()"
                    style="margin-top:6px; font-size:0.78rem; color:#e53e3e; background:none; border:none; cursor:pointer; padding:0;">
              ✕ Remove image
            </button>
          </div>
        </div>

        <!-- Description -->
        <div class="field">
          <label class="field-label" for="description">Event Description</label>
          <div class="input-wrap">
            <textarea id="description" name="description"
                      placeholder="Provide details about the event..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          </div>
        </div>

        <button type="submit" name="save" class="btn-save-event" id="saveBtn">
          <span id="saveBtnText">Publish Event</span>
        </button>

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

  // ── Image upload preview ──────────────────────────────────
  function updateLabel() {
    const input = document.getElementById('eventImg');
    if (input.files && input.files[0]) {
      const file = input.files[0];
      document.getElementById('fileMain').textContent = file.name;
      document.getElementById('fileSub').textContent  = 'Selected · ' + (file.size / 1024).toFixed(1) + ' KB';

      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('imgPreviewWrap').style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  }

  function clearImage() {
    document.getElementById('eventImg').value       = '';
    document.getElementById('imgPreview').src       = '';
    document.getElementById('imgPreviewWrap').style.display = 'none';
    document.getElementById('fileMain').textContent = 'Click to upload banner';
    document.getElementById('fileSub').textContent  = 'JPG, PNG, GIF, WEBP · Recommended 16:9';
  }

  // ── Client-side validation ────────────────────────────────
  document.getElementById('eventForm').addEventListener('submit', function(e) {
    let valid = true;

    // Required text fields
    ['event_name', 'venue', 'start_time', 'end_time'].forEach(function(id) {
      const el  = document.getElementById(id);
      const err = document.getElementById('err_' + id);
      if (!el || !el.value.trim()) {
        if (el) el.classList.add('is-invalid');
        if (err) err.style.display = 'block';
        valid = false;
      } else {
        el.classList.remove('is-invalid');
        if (err) err.style.display = 'none';
      }
    });

    // End time must be after start time
    const start  = document.getElementById('start_time').value;
    const end    = document.getElementById('end_time').value;
    const errEnd = document.getElementById('err_end_time');
    if (start && end && end <= start) {
      document.getElementById('end_time').classList.add('is-invalid');
      errEnd.textContent   = 'End time must be after start time.';
      errEnd.style.display = 'block';
      valid = false;
    }

    if (!valid) {
      e.preventDefault();
      const firstErr = document.querySelector('.is-invalid');
      if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    // Prevent double submit
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    document.getElementById('saveBtnText').textContent = 'Publishing...';
  });

  // ── Clear error state on input ────────────────────────────
  document.querySelectorAll('.input-wrap input, .input-wrap textarea').forEach(function(el) {
    el.addEventListener('input', function() {
      this.classList.remove('is-invalid');
      const err = document.getElementById('err_' + this.id);
      if (err) err.style.display = 'none';
    });
  });
</script>
</body>
</html>