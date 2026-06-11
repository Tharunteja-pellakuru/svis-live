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

$id = $_POST['id'] ?? 0;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role_id = $_POST['role_id'] ?? '';
$password_raw = $_POST['password'] ?? '';

if (empty($id) || empty($name) || empty($email) || empty($role_id)) {
    $_SESSION['flash_msg']  = 'Required fields are missing';
    $_SESSION['flash_type'] = 'danger';
    echo "<script>window.location.href='admin_list.php';</script>";
    exit();
}

if (!empty($password_raw)) {
    $password = password_hash($password_raw, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE admin_users SET name=?, email=?, password=?, role_id=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $password, $role_id, $id);
} else {
    $stmt = $conn->prepare("UPDATE admin_users SET name=?, email=?, role_id=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $role_id, $id);
}

if ($stmt->execute()) {
    $_SESSION['flash_msg']  = 'Admin updated successfully';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_msg']  = 'Error: ' . $stmt->error;
    $_SESSION['flash_type'] = 'danger';
}
$stmt->close();
echo "<script>window.location.href='admin_list.php';</script>";
exit();
?>
