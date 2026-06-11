<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'SuperAdmin') {
    $_SESSION['flash_msg'] = "Access Denied: SuperAdmin privilege required.";
    $_SESSION['flash_type'] = 'error';
    header("Location: dashboard.php");
    exit();
}
include('../db_connect.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['flash_msg']  = 'Admin deleted successfully';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_msg']  = 'Error: ' . $stmt->error;
        $_SESSION['flash_type'] = 'error';
    }
    $stmt->close();
}

echo "<script>window.location.href='admin_list.php';</script>";
exit();
?>
