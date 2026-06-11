<?php 
include('header.php'); 
require_admin_permission('add');
include('sidebar.php'); 
include('../db_connect.php'); 

if (isset($_POST['save'])) {
    $title = trim($_POST['title']);
    $order_no = (int)$_POST['order_no'];
    $banner_type = trim($_POST['banner_type'] ?? 'Large Banner');
    $file_name = "";
    $error = "";
    $success = "";

    // Debug: Log what we received
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Store upload attempt in session for error_log.php viewer
    $_SESSION['last_upload_attempt'] = date('Y-m-d H:i:s') . " - POST[save] set, FILES[image]: " . (isset($_FILES['image']) ? "YES" : "NO");

    // FIXED: Check if file exists AND uploaded successfully
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/../uploads/home_scroll/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (!is_writable($upload_dir)) {
            chmod($upload_dir, 0777);
        }

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_types)) {
            $error = "Only JPG, JPEG, PNG, GIF and WEBP files are allowed.";
        } else {
            $file_name = time() . "_" . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['image']['name']));
            $target = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $file_name;

            if (!file_exists($_FILES['image']['tmp_name'])) {
                $error = "Temporary uploaded file missing.";
            } elseif (!is_uploaded_file($_FILES['image']['tmp_name'])) {
                $error = "Invalid uploaded file.";
            } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $error = "Move Upload Failed.<br>";
                $error .= "Temp File: " . $_FILES['image']['tmp_name'] . "<br>";
                $error .= "Target: " . $target . "<br>";
                $error .= "Upload Dir Writable: " . (is_writable($upload_dir) ? 'YES' : 'NO') . "<br>";
                $error .= "PHP upload_tmp_dir: " . ini_get('upload_tmp_dir');
            } else {
                $stmt = $conn->prepare("INSERT INTO home_scroll_images (image_name, order_no, title, banner_type, date_added) VALUES (?, ?, ?, ?, NOW())");

                if (!$stmt) {
                    $error = "Database Error: " . $conn->error;
                } else {
                    $stmt->bind_param("siss", $file_name, $order_no, $title, $banner_type);

                    if ($stmt->execute()) {
                        $success = "✓ Slide added successfully!";
                    } else {
                        $error = "Database Insert Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        // NEW: Show specific upload error codes
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE   => "File exceeds upload_max_filesize in php.ini",
            UPLOAD_ERR_FORM_SIZE  => "File exceeds MAX_FILE_SIZE in HTML form",
            UPLOAD_ERR_PARTIAL    => "File was only partially uploaded",
            UPLOAD_ERR_NO_FILE    => "No file was uploaded",
            UPLOAD_ERR_NO_TMP_DIR => "Missing temporary folder on server",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk",
            UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the upload"
        ];
        $err_code = $_FILES['image']['error'];
        $error = "Upload Error: " . ($upload_errors[$err_code] ?? "Unknown error code: $err_code");
    } else {
        $error = "Please select an image file to upload.";
    }
}
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </div>
      <div>
        <div class="page-header-label">Branding</div>
        <div class="page-header-title">Add Hero Slide</div>
      </div>
    </div>
    <a href="home_scroll_list.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to List
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #fee; border: 1.5px solid #f88; border-radius: 8px; color: #c33; font-weight: 500;">
      ⚠️ <?= $error ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="toast-notification success-toast" id="successToast">
      <div class="toast-icon">✓</div>
      <div class="toast-message"><?= htmlspecialchars($success) ?></div>
      <button class="toast-close" onclick="closeToast('successToast')">&times;</button>
    </div>
  <?php endif; ?>

  <style>
    /* Toast notifications */
    .toast-notification {
      position: fixed;
      top: 20px;
      right: 20px;
      background: white;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      display: flex;
      align-items: center;
      gap: 12px;
      z-index: 9999;
      animation: slideInRight 0.3s ease-out, slideOutRight 0.3s ease-out 4.7s forwards;
      font-weight: 500;
    }
    
    .toast-notification.success-toast {
      background: #16a34a;
      color: #fff;
    }
    
    .toast-notification.error-toast {
      background: #ef4444;
      color: #fff;
    }
    
    .toast-icon {
      font-size: 1.2rem;
      font-weight: bold;
    }
    
    .toast-message {
      font-size: 0.95rem;
      font-weight: 600;
    }
    
    .toast-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: inherit;
      padding: 0;
      margin-left: 8px;
    }
    
    @keyframes slideInRight {
      from { transform: translateX(400px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(400px); opacity: 0; }
    }

    .field { margin-bottom: 1.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap input, .input-wrap select { width: 100%; height: 44px; padding: 0 14px; font-family: 'Lato', sans-serif; font-size: 0.92rem; background: var(--surface-alt); border: 1.5px solid var(--border); border-radius: 9px; outline: none; transition: all 0.18s; }
    .input-wrap input:focus, .input-wrap select:focus { border-color: var(--blue); background: white; box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    .file-drop { border: 2px dashed var(--border); border-radius: 12px; padding: 2rem; text-align: center; background: var(--surface-alt); cursor: pointer; transition: all 0.18s; position: relative; }
    .file-drop:hover { border-color: var(--blue); background: var(--blue-light); }
    .file-drop input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .btn-save-slide { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); }
    .btn-save-slide:hover { background: var(--blue-hover); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg></div>
        <span class="panel-head-title">Slide Configuration</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" enctype="multipart/form-data" novalidate>
        
        <div class="field">
          <label class="field-label">Slide Image (16:9 Recommended)</label>
          <div class="file-drop">
            <input type="file" name="image" id="scrollImg" accept="image/*" required onchange="updateLabel()">
            <div style="font-weight:700; color:var(--blue-dark); margin-bottom:2px;" id="fileMain">Choose Hero Image</div>
            <div style="font-size:0.75rem; color:var(--text-3);" id="fileSub">Click or drag image here</div>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:1.5rem;">
          <div class="field">
            <label class="field-label">Overlay Title (Optional)</label>
            <div class="input-wrap">
              <input type="text" name="title" placeholder="e.g. Welcome to SVIS Alumni">
            </div>
          </div>

          <div class="field">
            <label class="field-label">Banner Type</label>
            <div class="input-wrap">
              <select name="banner_type" required>
                <option value="All">All</option>
                <option value="Large Banner">Large Banner</option>
                <option value="Small Banner">Small Banner</option>
              </select>
            </div>
          </div>

          <div class="field">
            <label class="field-label">Display Order</label>
            <div class="input-wrap">
              <input type="number" name="order_no" value="1" required>
            </div>
          </div>
        </div>

        <!-- FIXED: Added value="1" -->
        <button type="submit" name="save" value="1" class="btn-save-slide">Add to Scroll</button>
      </form>
    </div>
  </div>
</div>

<script>
  function updateLabel() {
    const input = document.getElementById('scrollImg');
    if (input.files.length > 0) {
      document.getElementById('fileMain').textContent = input.files[0].name;
      document.getElementById('fileSub').textContent = 'Ready to upload';
    }
  }

  function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
      toast.style.animation = 'slideOutRight 0.3s ease-out forwards';
      setTimeout(() => toast.remove(), 300);
    }
  }

  // Auto-close success toast after 5 seconds
  document.addEventListener('DOMContentLoaded', function() {
    const successToast = document.getElementById('successToast');
    if (successToast) {
      setTimeout(() => {
        successToast.style.animation = 'slideOutRight 0.3s ease-out forwards';
        setTimeout(() => successToast.remove(), 300);
      }, 5000);
    }
  });
</script>
</body></html>