<?php 
ob_start();
include('header.php'); 
require_admin_permission('add');
include('sidebar.php'); 
include('../db_connect.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if POST was truncated due to exceeding post_max_size
    if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $post_max = ini_get('post_max_size');
        $_SESSION['flash_msg'] = "The total size of the selected images is too large. The server limit is $post_max. Please upload fewer or smaller files at once.";
        $_SESSION['flash_type'] = 'error';
        header("Location: gallery_add.php");
        exit();
    }

    $category_id = $_POST['category_id'] ?? '';
    $caption     = $_POST['caption'] ?? '';

    // Validate inputs
    if (empty($category_id)) {
        $_SESSION['flash_msg'] = "Please select a category.";
        $_SESSION['flash_type'] = 'error';
    } elseif (empty($_FILES['image']['name'][0])) {
        $_SESSION['flash_msg'] = "Please select at least one image to upload.";
        $_SESSION['flash_type'] = 'error';
    } else {
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }

        $success_count = 0;
        $error_msgs = [];
        foreach ($_FILES['image']['name'] as $key => $imageName) {
            if ($_FILES['image']['error'][$key] !== UPLOAD_ERR_OK) {
                $err_code = $_FILES['image']['error'][$key];
                $name = htmlspecialchars($imageName);
                if ($err_code === UPLOAD_ERR_INI_SIZE || $err_code === UPLOAD_ERR_FORM_SIZE) {
                    $error_msgs[] = "File '$name' exceeds the server upload size limit.";
                } else {
                    $error_msgs[] = "Failed to upload '$name' (error code: $err_code).";
                }
                continue;
            }
            $tmp_name = $_FILES['image']['tmp_name'][$key];
            $file_name = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($imageName));
            $target    = $upload_dir . $file_name;

            if (move_uploaded_file($tmp_name, $target)) {
                $stmt = $conn->prepare("INSERT INTO gallery (category_id, image_name, caption) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $category_id, $file_name, $caption);
                $stmt->execute();
                $stmt->close();
                $success_count++;
            } else {
                $error_msgs[] = "Failed to save file '" . htmlspecialchars($imageName) . "' to the uploads directory.";
            }
        }

        if ($success_count > 0) {
            $msg = "$success_count image(s) uploaded successfully!";
            if (!empty($error_msgs)) {
                $msg .= "\nNote: Some files failed:\n" . implode("\n", $error_msgs);
                $_SESSION['flash_type'] = 'warning';
            } else {
                $_SESSION['flash_type'] = 'success';
            }
            $_SESSION['flash_msg'] = $msg;
            echo "<script>window.location.href='gallery_list.php';</script>";
            exit();
        } else {
            $_SESSION['flash_msg'] = "All uploads failed:\n" . implode("\n", $error_msgs);
            $_SESSION['flash_type'] = 'error';
            header("Location: gallery_add.php");
            exit();
        }
    }
}
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </div>
      <div>
        <div class="page-header-label">Multimedia</div>
        <div class="page-header-title">Add Gallery Images</div>
      </div>
    </div>
    <a href="gallery_list.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to Gallery
    </a>
  </div>

  <style>
    .field { margin-bottom: 1.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap select, .input-wrap input { width: 100%; height: 44px; padding: 0 14px; font-family: 'Lato', sans-serif; font-size: 0.92rem; background: var(--surface-alt); border: 1.5px solid var(--border); border-radius: 9px; outline: none; transition: all 0.18s; box-sizing: border-box; }
    .input-wrap select:focus, .input-wrap input:focus { border-color: var(--blue); background: white; box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    .file-drop { border: 2px dashed var(--border); border-radius: 12px; padding: 2rem; text-align: center; background: var(--surface-alt); cursor: pointer; transition: all 0.18s; position: relative; }
    .file-drop:hover, .file-drop.dragover { border-color: var(--blue); background: #eef4ff; box-shadow: 0 0 0 4px rgba(26,86,160,0.1); }
    .file-drop input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2; }
    .btn-save-gallery { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); }
    .btn-save-gallery:hover { background: var(--blue-hover, #1557b0); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
  </style>

  <!-- Client-side Error Banner -->
  <div id="jsErrorBanner" style="display:none; padding:13px 18px; margin-bottom:1.2rem; border-radius:9px; font-weight:600; font-size:0.92rem; background:#fee2e2; color:#991b1b; border:1.5px solid #fca5a5; white-space: pre-line;"></div>

  <!-- Flash Message -->
  <?php if (!empty($_SESSION['flash_msg'])): 
    $type = $_SESSION['flash_type'] ?? 'error';
    $bg = ($type === 'success') ? '#edf7ed' : (($type === 'warning') ? '#fff4e5' : '#fee2e2');
    $color = ($type === 'success') ? '#1e4620' : (($type === 'warning') ? '#663c00' : '#991b1b');
    $border = ($type === 'success') ? '#c8e6c9' : (($type === 'warning') ? '#ffe0b2' : '#fca5a5');
  ?>
    <div style="padding:13px 18px; margin-bottom:1.2rem; border-radius:9px; font-weight:600; font-size:0.92rem; background:<?= $bg ?>; color:<?= $color ?>; border:1.5px solid <?= $border ?>; white-space: pre-line;">
      <?= htmlspecialchars($_SESSION['flash_msg']) ?>
    </div>
    <?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
        <span class="panel-head-title">Upload New Content</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" enctype="multipart/form-data" novalidate id="galleryForm">
        
        <div class="field">
          <label class="field-label">Category</label>
          <div class="input-wrap">
            <select name="category_id" required>
              <option value="">-- Select Category --</option>
              <?php
              $catQuery = $conn->query("SELECT * FROM gallery_category ORDER BY name ASC");
              while ($cat = $catQuery->fetch_assoc()):
                $selected = (isset($category_id) && $category_id == $cat['id']) ? 'selected' : '';
              ?>
                <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['name']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="field">
          <label class="field-label">Select Images</label>
          <div class="file-drop" id="dropZone">
            <input type="file" name="image[]" id="galleryInput" multiple required accept=".jpg,.jpeg,.png,.gif,.webp" onchange="updateFileLabel()">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--blue,#1a56a0)" stroke-width="1.5" style="margin-bottom:8px; display:block; margin-left:auto; margin-right:auto;">
              <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
            </svg>
            <div style="font-weight:700; color:var(--blue-dark,#1a56a0); margin-bottom:2px;" id="fileMain">Click or Drag to Upload</div>
            <div style="font-size:0.75rem; color:var(--text-3,#888);" id="fileSub">JPG, PNG, GIF, WEBP (Multiple files allowed)</div>
          </div>
        </div>

        <div class="field">
          <label class="field-label">Caption (Optional)</label>
          <div class="input-wrap">
            <input type="text" name="caption" placeholder="Enter a brief caption for the images" value="<?= htmlspecialchars($caption ?? '') ?>">
          </div>
        </div>

        <button type="submit" name="save" class="btn-save-gallery">Start Uploading</button>
      </form>
    </div>
  </div>
</div>

<script>
  function updateFileLabel() {
    const input = document.getElementById('galleryInput');
    const main = document.getElementById('fileMain');
    const sub = document.getElementById('fileSub');
    const errorBanner = document.getElementById('jsErrorBanner');
    
    // Hide error banner when file selection changes
    errorBanner.style.display = 'none';

    if (input.files.length > 0) {
      let totalSize = 0;
      let fileNames = [];
      for (let i = 0; i < input.files.length; i++) {
        totalSize += input.files[i].size;
        fileNames.push(input.files[i].name);
      }
      
      const totalSizeMB = (totalSize / (1024 * 1024)).toFixed(2);
      
      // Check total size against 40MB limit (post_max_size of XAMPP)
      const limitMB = 40;
      if (totalSizeMB > limitMB) {
        errorBanner.textContent = "⚠️ The total size of selected files (" + totalSizeMB + " MB) exceeds the server limit of " + limitMB + " MB. Please select fewer or smaller images.";
        errorBanner.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        input.value = '';
        main.textContent = 'Click or Drag to Upload';
        sub.textContent = 'JPG, PNG, GIF, WEBP (Multiple files allowed)';
        return;
      }
      
      main.textContent = input.files.length + ' file(s) selected (' + totalSizeMB + ' MB)';
      sub.textContent = fileNames.join(', ').substring(0, 60) + (input.files.length > 2 ? '...' : '');
    } else {
      main.textContent = 'Click or Drag to Upload';
      sub.textContent = 'JPG, PNG, GIF, WEBP (Multiple files allowed)';
    }
  }

  // Setup drag and drop event listeners
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('galleryInput');
  const form = document.getElementById('galleryForm');
  const errorBanner = document.getElementById('jsErrorBanner');

  // Prevent default drag behaviors
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
  });

  function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
  }

  // Highlight drop zone when dragging over
  ['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
      dropZone.classList.add('dragover');
    }, false);
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
      dropZone.classList.remove('dragover');
    }, false);
  });

  // Handle dropped files
  dropZone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
      fileInput.files = files;
      updateFileLabel();
    }
  }, false);

  // Client-side Validation on Submit
  form.addEventListener('submit', function(e) {
    const category = form.querySelector('select[name="category_id"]').value;
    const files = fileInput.files;
    
    errorBanner.style.display = 'none';

    if (!category) {
      e.preventDefault();
      errorBanner.textContent = "Please select a category.";
      errorBanner.style.display = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return false;
    }

    if (files.length === 0) {
      e.preventDefault();
      errorBanner.textContent = "Please select at least one image to upload.";
      errorBanner.style.display = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return false;
    }
  });
</script>
</body></html>
