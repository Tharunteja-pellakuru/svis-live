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
    
    // Check if any admins are using this role
    $check = $conn->query("SELECT COUNT(*) as count FROM admin_users WHERE role_id = $id");
    $count = $check->fetch_assoc()['count'];
    
    if ($count > 0) {
        $_SESSION['flash_msg'] = "Cannot delete role. $count admin(s) are currently assigned to it.";
        $_SESSION['flash_type'] = "error";
    } else {
        $stmt = $conn->prepare("DELETE FROM admin_roles WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['flash_msg'] = "Role deleted successfully.";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_msg'] = "Error deleting role.";
            $_SESSION['flash_type'] = "error";
        }
        $stmt->close();
    }
}

echo "<script>window.location.href='list_roles.php';</script>";
exit();
?>
