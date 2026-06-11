<?php 
ob_start();
include('header.php'); 
require_admin_permission('add');
include('sidebar.php'); 
include('../db_connect.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $adddate = date("Y-m-d");

    if (!empty($name)) {
        $file_name = 'dummy_category.png'; // Default empty string which will fall back to dummy image
        $upload_ok = true;

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/category/";
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }

            $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image']['name']));
            $target = $upload_dir . $file_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $upload_ok = false;
                $error = "Failed to upload image. Check folder permissions.";
            }
        } elseif (!empty($_FILES['image']['name']) && $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $upload_ok = false;
            $error = "Upload error code: " . $_FILES['image']['error'];
        }

        if ($upload_ok) {
            $stmt = $conn->prepare("INSERT INTO gallery_category (name, image, adddate) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $file_name, $adddate);
            
            if ($stmt->execute()) {
                $_SESSION['flash_msg'] = "Category added successfully!";
                $_SESSION['flash_type'] = "success";
                echo "<script>window.location.href='gallery_category_list.php';</script>";
                exit();
            } else {
                $error = "Database error: " . $stmt->error;
            }
        }
    } else {
        $error = "Please provide a category name.";
    }

    if (!empty($error)) {
        $_SESSION['flash_msg'] = $error;
        $_SESSION['flash_type'] = "error";
    }
}
?>

<div class="dash-main">

  <!-- Flash Message -->
  <?php if (!empty($_SESSION['flash_msg']) && $_SESSION['flash_type'] === 'error'): ?>
    <div style="padding:13px 18px; margin-bottom:1.2rem; border-radius:9px; font-weight:600; font-size:0.92rem; background:#fee2e2; color:#991b1b; border:1.5px solid #fca5a5; display:flex; align-items:center; gap:10px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?= htmlspecialchars($_SESSION['flash_msg']) ?>
    </div>
    <?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); ?>
  <?php endif; ?>

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </div>
      <div>
        <div class="page-header-label">Multimedia</div>
        <div class="page-header-title">Create Category</div>
      </div>
    </div>
    <a href="gallery_category_list.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to List
    </a>
  </div>

  <style>
    .field { margin-bottom: 1.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap input { width: 100%; height: 44px; padding: 0 14px; font-family: 'Lato', sans-serif; font-size: 0.92rem; background: var(--surface-alt); border: 1.5px solid var(--border); border-radius: 9px; outline: none; transition: all 0.18s; box-sizing: border-box; }
    .input-wrap input:focus { border-color: var(--blue); background: white; box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    .file-drop { border: 2px dashed var(--border); border-radius: 12px; padding: 2rem; text-align: center; background: var(--surface-alt); cursor: pointer; transition: all 0.18s; position: relative; }
    .file-drop:hover { border-color: var(--blue); background: var(--blue-light, #eef4ff); }
    .file-drop input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2; }
    .btn-save-cat { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); }
    .btn-save-cat:hover { background: var(--blue-hover, #1557b0); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></div>
        <span class="panel-head-title">Category Details</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" enctype="multipart/form-data" novalidate>
        
        <div class="field">
          <label class="field-label">Category Name <span style="color:#e53e3e">*</span></label>
          <div class="input-wrap">
            <input type="text" name="name" placeholder="e.g. Campus Events, Graduation" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
          </div>
        </div>

        <div class="field">
          <label class="field-label">Cover Image <span style="font-weight:400; text-transform:none; font-size:0.72rem; color:var(--text-3)">(Optional)</span></label>
          <div class="file-drop">
            <input type="file" name="image" id="catImage" accept=".jpg,.jpeg,.png,.gif,.webp" onchange="updateLabel()">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--blue,#1a56a0)" stroke-width="1.5" style="margin-bottom:8px; display:block; margin-left:auto; margin-right:auto;">
              <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
            </svg>
            <div style="font-weight:700; color:var(--blue-dark,#1a56a0); margin-bottom:2px;" id="fileMain">Choose Category Cover</div>
            <div style="font-size:0.75rem; color:var(--text-3,#888);" id="fileSub">Click or drag image here</div>
          </div>
          <div id="imgPreviewWrap" style="display:none; margin-top:12px; padding:14px; background:var(--surface-alt); border:1.5px solid var(--border-soft); border-radius:12px;">
            <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--blue); margin-bottom:8px;">Image Preview</div>
            <img id="imgPreview" src="" alt="Preview" style="max-height:180px; border-radius:8px; border:1.5px solid var(--border); object-fit:cover; display:block;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
              <span id="imgInfo" style="font-size:0.78rem; color:var(--text-3); font-weight:600;"></span>
              <button type="button" onclick="clearImage()" style="font-size:0.78rem; color:#e53e3e; background:none; border:none; cursor:pointer; padding:0; font-weight:600;">✕ Remove</button>
            </div>
          </div>
        </div>

        <button type="submit" class="btn-save-cat">Create Category</button>
      </form>
    </div>
  </div>
</div>

<script>
  function updateLabel() {
    const input = document.getElementById('catImage');
    if (input.files && input.files[0]) {
      const file = input.files[0];
      document.getElementById('fileMain').textContent = file.name;
      document.getElementById('fileSub').textContent = 'File selected successfully';
      document.getElementById('imgInfo').textContent = file.name + ' · ' + (file.size / 1024).toFixed(1) + ' KB';
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('imgPreview').src = e.target.result;
        document.getElementById('imgPreviewWrap').style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  }
  function clearImage() {
    document.getElementById('catImage').value = '';
    document.getElementById('imgPreview').src = '';
    document.getElementById('imgPreviewWrap').style.display = 'none';
    document.getElementById('fileMain').textContent = 'Choose Category Cover';
    document.getElementById('fileSub').textContent = 'Click or drag image here';
  }
</script>
</body></html>
