<?php
include('permission_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<link rel="icon" type="image/png" href="../Logo/FavIcon.png">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
<?php
$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>
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
<div class="d-flex">