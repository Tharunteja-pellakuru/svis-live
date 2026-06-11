<?php 
include('header.php'); 
require_admin_permission('edit');
include('sidebar.php'); 
include('../db_connect.php'); 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_msg'] = "Invalid video ID";
    $_SESSION['flash_type'] = "danger";
    echo "<script>window.location.href='videos_list.php';</script>"; exit;
}

$id = (int)$_GET['id'];

if (isset($_POST['update'])) {
    $video_url   = trim($_POST['video_url']);
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);

    if ($video_url && $title) {
        $update = $conn->prepare("UPDATE videos SET video_url = ?, title = ?, description = ? WHERE id = ?");
        $update->bind_param("sssi", $video_url, $title, $description, $id);
        
        if ($update->execute()) {
            $_SESSION['flash_msg'] = "Video updated successfully!";
            $_SESSION['flash_type'] = "success";
            echo "<script>window.location.href='videos_list.php';</script>";
            exit();
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$video = $stmt->get_result()->fetch_assoc();

if (!$video) {
    $_SESSION['flash_msg'] = "Video not found";
    $_SESSION['flash_type'] = "danger";
    echo "<script>window.location.href='videos_list.php';</script>"; exit;
}
?>

<div class="dash-main">

  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      </div>
      <div>
        <div class="page-header-label">Multimedia</div>
        <div class="page-header-title">Edit Video</div>
      </div>
    </div>
    <a href="videos_list.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to Library
    </a>
  </div>

  <style>
    .field { margin-bottom: 1.5rem; }
    .field-label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--text); letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 0.5rem; }
    .input-wrap input, .input-wrap textarea { width: 100%; height: 44px; padding: 0 14px; font-family: 'Lato', sans-serif; font-size: 0.92rem; background: var(--surface-alt); border: 1.5px solid var(--border); border-radius: 9px; outline: none; transition: all 0.18s; }
    .input-wrap textarea { height: auto; padding: 12px 14px; min-height: 100px; resize: vertical; }
    .input-wrap input:focus, .input-wrap textarea:focus { border-color: var(--blue); background: white; box-shadow: 0 0 0 3.5px rgba(26,86,160,0.09); }
    .btn-save-video { padding: 0 2rem; width: max-content; display: flex; align-items: center; justify-content: center; margin-left: auto; height: 46px; background: var(--blue); color: white; border: none; border-radius: 9px; font-weight: 700; font-size: 0.92rem; cursor: pointer; transition: all 0.18s; box-shadow: 0 2px 8px rgba(26,86,160,0.22); }
    .btn-save-video:hover { background: var(--blue-hover); box-shadow: 0 4px 14px rgba(26,86,160,0.32); }
  </style>

  <div class="panel">
    <div class="panel-head">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 7l-7 5 7 5V7z"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg></div>
        <span class="panel-head-title">Update Content Details</span>
      </div>
    </div>

    <div class="panel-body" style="padding: 1.75rem 1.5rem;">
      <form method="POST" novalidate>
        
        <div class="field">
          <label class="field-label">Video URL</label>
          <div class="input-wrap">
            <input type="text" name="video_url" value="<?= htmlspecialchars($video['video_url']) ?>" required>
          </div>
        </div>

        <div class="field">
          <label class="field-label">Video Title</label>
          <div class="input-wrap">
            <input type="text" name="title" value="<?= htmlspecialchars($video['title']) ?>" required>
          </div>
        </div>

        <div class="field">
          <label class="field-label">Description</label>
          <div class="input-wrap">
            <textarea name="description"><?= htmlspecialchars($video['description']) ?></textarea>
          </div>
        </div>

        <button type="submit" name="update" class="btn-save-video">Save Changes</button>
      </form>
    </div>
  </div>
</div>
</body></html>
