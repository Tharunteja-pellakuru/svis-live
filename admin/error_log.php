<?php
/**
 * Upload Error Log Viewer
 * This file helps diagnose upload issues by displaying PHP error logs
 */

// Ensure we're logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access");
}

include('header.php');
include('sidebar.php');
?>

<div class="dash-main">
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24"><path d="M12 1v22M4.22 4.22l15.56 15.56M1 12h22M4.22 19.78L19.78 4.22"/></svg>
      </div>
      <div>
        <div class="page-header-label">Diagnostics</div>
        <div class="page-header-title">Error Log Viewer</div>
      </div>
    </div>
    <a href="dashboard.php" class="btn-primary-dash" style="background:var(--surface); color:var(--text-2); border:1.5px solid var(--border); box-shadow:none;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back
    </a>
  </div>

  <style>
    .log-container { background: #1e1e1e; color: #d4d4d4; padding: 1.5rem; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 0.85rem; line-height: 1.6; overflow-x: auto; max-height: 500px; overflow-y: auto; }
    .log-error { color: #f48771; }
    .log-warning { color: #dcdcaa; }
    .log-notice { color: #9cdcfe; }
    .log-success { color: #6a9955; }
    .info-box { background: #f0f0f0; border-left: 4px solid #1a56a0; padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px; }
    .info-box strong { color: #1a56a0; }
  </style>

  <div class="info-box">
    <strong>📋 PHP Configuration:</strong><br>
    <?php
      $upload_max = ini_get('upload_max_filesize');
      $post_max = ini_get('post_max_size');
      $tmp_dir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
      $display_errors = ini_get('display_errors');
      $error_reporting = ini_get('error_reporting');
      
      echo "Upload Max Size: <strong>$upload_max</strong> | ";
      echo "POST Max Size: <strong>$post_max</strong> | ";
      echo "Temp Dir: <strong>$tmp_dir</strong><br>";
      echo "Display Errors: <strong>" . ($display_errors ? 'ON' : 'OFF') . "</strong> | ";
      echo "Error Reporting: <strong>" . $error_reporting . "</strong>";
    ?>
  </div>

  <div class="info-box" style="border-left-color: #28a745;">
    <strong>📁 Upload Directory Status:</strong><br>
    <?php
      $upload_dir = __DIR__ . "/../uploads/home_scroll/";
      $exists = is_dir($upload_dir);
      $writable = is_writable($upload_dir);
      $perms = substr(sprintf('%o', fileperms($upload_dir)), -4);
      
      echo "Path: <strong>$upload_dir</strong><br>";
      echo "Exists: <strong>" . ($exists ? '✓ YES' : '✗ NO') . "</strong> | ";
      echo "Writable: <strong>" . ($writable ? '✓ YES' : '✗ NO') . "</strong> | ";
      echo "Permissions: <strong>$perms</strong>";
    ?>
  </div>

  <h3 style="margin-top: 2rem; color: #333;">Error Log Content:</h3>
  
  <div class="log-container">
    <?php
      $log_file = ini_get('error_log');
      
      if ($log_file && file_exists($log_file)) {
        $lines = file($log_file, FILE_IGNORE_NEW_LINES);
        $lines = array_reverse($lines); // Show newest first
        $lines = array_slice($lines, 0, 50); // Last 50 lines
        
        if (empty($lines)) {
          echo '<span class="log-success">✓ No errors recorded</span>';
        } else {
          foreach ($lines as $line) {
            if (strpos($line, 'Error') !== false || strpos($line, 'Fatal') !== false) {
              echo '<span class="log-error">' . htmlspecialchars($line) . '</span><br>';
            } elseif (strpos($line, 'Warning') !== false) {
              echo '<span class="log-warning">' . htmlspecialchars($line) . '</span><br>';
            } elseif (strpos($line, 'Notice') !== false) {
              echo '<span class="log-notice">' . htmlspecialchars($line) . '</span><br>';
            } else {
              echo htmlspecialchars($line) . '<br>';
            }
          }
        }
      } else {
        echo '<span class="log-warning">⚠️ Error log file not found: ' . ($log_file ?: 'No log file configured') . '</span>';
      }
    ?>
  </div>

  <h3 style="margin-top: 2rem; color: #333;">Test Information:</h3>
  
  <div class="info-box" style="border-left-color: #ffc107;">
    <strong>Last Upload Attempt:</strong><br>
    <?php
      if (isset($_SESSION['last_upload_attempt'])) {
        echo htmlspecialchars($_SESSION['last_upload_attempt']);
      } else {
        echo "No upload attempted yet in this session";
      }
    ?>
  </div>

</div>

</body></html>
