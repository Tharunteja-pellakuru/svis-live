<?php
include('permission_check.php');
require_admin_permission('delete');
include('../db_connect.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['flash_msg'] = "Invalid image ID";
    $_SESSION['flash_type'] = "danger";
    header("Location: home_scroll_list.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch image name
$stmt = $conn->prepare("SELECT image_name FROM home_scroll_images WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['flash_msg'] = "Image not found";
    $_SESSION['flash_type'] = "danger";
    header("Location: home_scroll_list.php");
    exit;
}

$row = $result->fetch_assoc();
$imagePath = "../uploads/home_scroll/" . $row['image_name'];

// Delete image file
if (!empty($row['image_name']) && file_exists($imagePath)) {
    unlink($imagePath);
}

// Delete database record
$deleteStmt = $conn->prepare("DELETE FROM home_scroll_images WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    $_SESSION['flash_msg'] = "Image removed from scroll";
    $_SESSION['flash_type'] = "danger";
} else {
    $_SESSION['flash_msg'] = "Error removing image";
    $_SESSION['flash_type'] = "danger";
}

header("Location: home_scroll_list.php");
exit;
?>
