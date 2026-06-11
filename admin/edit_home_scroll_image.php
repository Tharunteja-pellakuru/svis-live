<?php 
include('header.php'); 
require_admin_permission('edit');
include('sidebar.php'); 
include('../db_connect.php'); 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_msg'] = "Invalid image ID";
    $_SESSION['flash_type'] = "danger";
    header("Location: home_scroll_list.php"); exit;
}

$id = (int)$_GET['id'];

if (isset($_POST['update'])) {
    $title = trim($_POST['title']);
    $order_no = (int)$_POST['order_no'];
    $banner_type = trim($_POST['banner_type'] ?? 'Large Banner');
    $old_image = $_POST['old_image'];
    $new_image = $old_image;
    $error = "";
    $success = "";

    if (!empty($_FILES['image']['name'])) {
        $new_image = time() . "_" . basename($_FILES['image']['name']);
        $target = "../uploads/home_scroll/" . $new_image;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $oldPath = "../uploads/home_scroll/" . $old_image;
            if (file_exists($oldPath) && !empty($old_image)) {
                unlink($oldPath);
            }
        } else {
            $error = "File upload failed. Please try again.";
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE home_scroll_images SET image_name = ?, title = ?, banner_type = ?, order_no = ? WHERE id = ?");
        
        if (!$stmt) {
            $error = "Database Error: " . $conn->error;
        } else {
            $stmt->bind_param("sssii", $new_image, $title, $banner_type, $order_no, $id);
            
            if ($stmt->execute()) {
                $success = "✓ Slide updated successfully!";
            } else {
                $error = "Database Update Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM home_scroll_images WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    $_SESSION['flash_msg'] = "Image not found";
    $_SESSION['flash_type'] = "danger";
    header("Location: home_scroll_list.php"); exit;
}
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </div>
      <div>
        <div class="page-header-label">Branding</div>
        <div class="page-header-title">Edit Slide</div>
      </div>
    </div>
    <a href="home_scroll_list.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to List
    </a>
  </div>

  <?php if (!empty($error)): ?>
    <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #fee; border: 1.5px solid #f88; border-radius: 8px; color: #c33; font-weight: 500;">
      ⚠️ <?= htmlspecialchars($error) ?>
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
    
    .toast-icon {
      font-size: 1.2rem;
      font-weight: bold;
    }
    
    .toast-message {
      flex: 1;
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
    .preview-wrap { margin-bottom: 1.25rem; padding: 14px; background: var(--surface-alt); border: 1px solid var(--border-soft); border-radius: 12px; display: flex; align-items: center; gap: 15px; }
    .preview-img { width: 160px; height: 90px; border-radius: 8px; object-fit: cover; border: 2px solid white; box-shadow: var(--shadow-sm); }
    .btn-save-slide { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); }
    .btn-save-slide:hover { background: var(--blue-hover); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg></div>
        <span class="panel-head-title">Update Slide Details</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" enctype="multipart/form-data" novalidate>
        
        <input type="hidden" name="old_image" value="<?= $row['image_name'] ?>">

        <div class="field">
          <label class="field-label">Current Visual</label>
          <div class="preview-wrap">
            <img src="../uploads/home_scroll/<?= rawurlencode($row['image_name']) ?>" class="preview-img">
            <div>
              <div style="font-size:0.8rem; font-weight:700; color:var(--text-2);"><?= htmlspecialchars($row['image_name']) ?></div>
              <div style="font-size:0.75rem; color:var(--text-3);">Currently active on homepage</div>
            </div>
          </div>
          <div class="input-wrap">
            <input type="file" name="image" style="padding:8px 0; border:none; background:none; height:auto;">
          </div>
          <span style="font-size:0.75rem; color:var(--text-3);">Upload only if you want to replace the current image.</span>
        </div>

        <div style="display:grid; grid-template-columns: 2fr 1fr 1fr; gap:1.5rem;">
          <div class="field">
            <label class="field-label">Slide Title</label>
            <div class="input-wrap">
              <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>">
            </div>
          </div>

          <div class="field">
            <label class="field-label">Banner Type</label>
            <div class="input-wrap">
              <select name="banner_type" required>
                <option value="All" <?= (isset($row['banner_type']) && $row['banner_type'] == 'All') ? 'selected' : '' ?>>All</option>
                <option value="Large Banner" <?= (isset($row['banner_type']) && $row['banner_type'] == 'Large Banner') ? 'selected' : '' ?>>Large Banner</option>
                <option value="Small Banner" <?= (isset($row['banner_type']) && $row['banner_type'] == 'Small Banner') ? 'selected' : '' ?>>Small Banner</option>
              </select>
            </div>
          </div>

          <div class="field">
            <label class="field-label">Display Order</label>
            <div class="input-wrap">
              <input type="number" name="order_no" value="<?= $row['order_no'] ?>" required>
            </div>
          </div>
        </div>

        <button type="submit" name="update" class="btn-save-slide">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<script>
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
