<?php 
ob_start();
include('header.php');
require_admin_permission('edit');
include('sidebar.php');
include('../db_connect.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['flash_msg'] = "Invalid image ID";
    $_SESSION['flash_type'] = "danger";
    echo "<script>window.location.href='gallery_list.php';</script>"; exit();
}

$id = intval($_GET['id']);

if (isset($_POST['update'])) {
    $caption = $_POST['caption'];
    $category_id = $_POST['category_id'];
    $old_image = $_POST['old_image'];
    $new_image = $old_image;

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $temp_image = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image']['name']));
        if (move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $temp_image)) {
            $new_image = $temp_image;
            if (!empty($old_image) && file_exists("../uploads/" . $old_image)) {
                unlink("../uploads/" . $old_image);
            }
        }
    }

    $stmt = $conn->prepare("UPDATE gallery SET category_id=?, image_name=?, caption=? WHERE id=?");
    $stmt->bind_param("issi", $category_id, $new_image, $caption, $id);
    
    if ($stmt->execute()) {
        $_SESSION['flash_msg'] = "Image updated successfully!";
        $_SESSION['flash_type'] = "success";
        echo "<script>window.location.href='gallery_list.php';</script>"; exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM gallery WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    $_SESSION['flash_msg'] = "Image not found";
    $_SESSION['flash_type'] = "danger";
    echo "<script>window.location.href='gallery_list.php';</script>"; exit();
}
$categories = $conn->query("SELECT * FROM gallery_category ORDER BY name ASC");
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </div>
      <div>
        <div class="page-header-label">Multimedia</div>
        <div class="page-header-title">Edit Gallery Image</div>
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
    .preview-wrap { margin-bottom: 1.25rem; padding: 14px; background: var(--surface-alt); border: 1px solid var(--border-soft); border-radius: 12px; display: flex; align-items: center; gap: 15px; }
    .preview-img { width: 100px; height: 100px; border-radius: 8px; object-fit: cover; border: 2px solid white; box-shadow: var(--shadow-sm); }
    .file-drop { border: 2px dashed var(--border); border-radius: 12px; padding: 1.5rem; text-align: center; background: var(--surface-alt); cursor: pointer; transition: all 0.18s; position: relative; }
    .file-drop:hover { border-color: var(--blue); background: var(--blue-light, #eef4ff); }
    .file-drop input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 2; }
    .btn-save-gallery { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); margin-top: 0.5rem; }
    .btn-save-gallery:hover { background: var(--blue-hover, #1557b0); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>
        <span class="panel-head-title">Update Image Content</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" enctype="multipart/form-data" novalidate>
        
        <input type="hidden" name="old_image" value="<?= $row['image_name'] ?>">

        <div class="field">
          <label class="field-label">Category</label>
          <div class="input-wrap">
            <select name="category_id" required>
              <option value="">-- Select Category --</option>
              <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $row['category_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <!-- Current Image -->
        <div class="field">
          <label class="field-label">Current Image</label>
          <?php
            $currentImgUrl = "../uploads/" . rawurlencode($row['image_name']);
            if (empty($row['image_name']) || !file_exists(__DIR__ . "/../uploads/" . $row['image_name'])) {
              $currentImgUrl = "https://via.placeholder.com/100x100?text=No+Img";
            }
          ?>
          <div class="preview-wrap">
            <img src="<?= $currentImgUrl ?>" class="preview-img" id="currentImg">
            <div>
              <div style="font-size:0.8rem; font-weight:700; color:var(--text-2);"><?= htmlspecialchars($row['image_name']) ?></div>
              <div style="font-size:0.75rem; color:var(--text-3);">Currently active in gallery</div>
            </div>
          </div>
        </div>

        <!-- Upload New Image -->
        <div class="field">
          <label class="field-label">Upload New Image <span style="font-weight:400; text-transform:none; font-size:0.72rem; color:var(--text-3)">(Optional)</span></label>
          <div class="file-drop">
            <input type="file" name="image" id="galleryImg" accept=".jpg,.jpeg,.png,.gif,.webp" onchange="previewNewImg()">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--blue,#1a56a0)" stroke-width="1.5" style="margin-bottom:8px; display:block; margin-left:auto; margin-right:auto;">
              <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
            </svg>
            <div style="font-weight:700; color:var(--blue-dark,#1a56a0); margin-bottom:4px;" id="fileMain">Click to upload replacement</div>
            <div style="font-size:0.75rem; color:var(--text-3,#888);" id="fileSub">JPG, PNG, GIF, WEBP</div>
          </div>
          <div id="newPreviewWrap" style="display:none; margin-top:12px; padding:14px; background:var(--surface-alt); border:1.5px solid var(--border-soft); border-radius:12px;">
            <div style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--blue); margin-bottom:8px;">New Image Preview</div>
            <img id="newPreview" src="" alt="New Preview" style="max-height:180px; border-radius:8px; border:1.5px solid var(--border); object-fit:cover; display:block;">
            <button type="button" onclick="clearNewImg()" style="margin-top:8px; font-size:0.78rem; color:#e53e3e; background:none; border:none; cursor:pointer; padding:0; font-weight:600;">✕ Remove selected image</button>
          </div>
        </div>

        <div class="field">
          <label class="field-label">Caption</label>
          <div class="input-wrap">
            <input type="text" name="caption" value="<?= htmlspecialchars($row['caption']) ?>" placeholder="Update caption">
          </div>
        </div>

        <button type="submit" name="update" class="btn-save-gallery">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<script>
  function previewNewImg() {
    const input = document.getElementById('galleryImg');
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
  function clearNewImg() {
    document.getElementById('galleryImg').value = '';
    document.getElementById('newPreview').src = '';
    document.getElementById('newPreviewWrap').style.display = 'none';
    document.getElementById('fileMain').textContent = 'Click to upload replacement';
    document.getElementById('fileSub').textContent = 'JPG, PNG, GIF, WEBP';
  }
</script>
</body></html>
