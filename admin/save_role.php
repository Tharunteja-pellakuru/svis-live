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
include("../db_connect.php");

$role_name = $_POST['role_name'];
$permissions = $_POST['permissions'] ?? [];

// Always ensure view access exists
if (!in_array("view", $permissions)) {
    $permissions[] = "view";
}

$permissions_json = json_encode([
    "add" => in_array("add", $permissions),
    "edit" => in_array("edit", $permissions),
    "delete" => in_array("delete", $permissions),
    "view" => true
]);

$sql = "INSERT INTO admin_roles (role_name, permissions) VALUES ('$role_name', '$permissions_json')";
if ($conn->query($sql)) {
    $_SESSION['flash_msg']  = 'Role created successfully';
    $_SESSION['flash_type'] = 'success';
    header("Location: list_roles.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
