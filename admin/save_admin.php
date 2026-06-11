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

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password_raw = $_POST['password'] ?? '';
$role_id = $_POST['role_id'] ?? '';

if (empty($name) || empty($email) || empty($password_raw) || empty($role_id)) {
    $_SESSION['flash_msg']  = 'All fields are required';
    $_SESSION['flash_type'] = 'danger';
    echo "<script>window.location.href='admin_list.php';</script>";
    exit();
}

$password = password_hash($password_raw, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO admin_users (name, email, password, role_id, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("ssss", $name, $email, $password, $role_id);

if ($stmt->execute()) {
    $_SESSION['flash_msg']  = 'Admin created successfully';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_msg']  = 'Error: ' . $stmt->error;
    $_SESSION['flash_type'] = 'danger';
}
$stmt->close();
echo "<script>window.location.href='admin_list.php';</script>";
exit();
?>
