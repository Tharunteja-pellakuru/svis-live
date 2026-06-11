<?php
include('permission_check.php');
require_admin_permission('delete');
include('../db_connect.php');

$ids = [];
if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = $_POST['ids'];
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $ids = [$_GET['id']];
}

if (empty($ids)) {
    $_SESSION['flash_msg'] = 'Invalid Request';
    $_SESSION['flash_type'] = 'danger';
    header("Location: gallery_list.php"); exit();
}

$success_count = 0;
$error_count = 0;

foreach ($ids as $id) {
    $id = intval($id);
    $stmt = $conn->prepare("SELECT image_name FROM gallery WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image = $row['image_name'];
        $stmt->close();

        // Delete record
        $delete = $conn->prepare("DELETE FROM gallery WHERE id=?");
        $delete->bind_param("i", $id);

        if ($delete->execute()) {
            // Delete physical file
            $filePath = "../uploads/" . $image;
            if (!empty($image) && file_exists($filePath)) {
                unlink($filePath);
            }
            $success_count++;
        } else {
            $error_count++;
        }
    } else {
        $stmt->close();
        $error_count++;
    }
}

if ($success_count > 0 && $error_count == 0) {
    $_SESSION['flash_msg'] = "$success_count image(s) deleted successfully";
    $_SESSION['flash_type'] = 'danger';
} elseif ($success_count > 0 && $error_count > 0) {
    $_SESSION['flash_msg'] = "$success_count image(s) deleted, $error_count failed";
    $_SESSION['flash_type'] = 'danger';
} elseif ($error_count > 0) {
    $_SESSION['flash_msg'] = 'Error deleting image(s)';
    $_SESSION['flash_type'] = 'danger';
}

header("Location: gallery_list.php");
exit();
?>
