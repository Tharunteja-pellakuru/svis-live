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
    $_SESSION['flash_msg'] = "Invalid Request";
    $_SESSION['flash_type'] = "danger";
    header("Location: gallery_category_list.php");
    exit;
}

$success_count = 0;
$error_count = 0;

foreach ($ids as $id) {
    $id = (int)$id;

    // Fetch Image Name
    $stmt = $conn->prepare("SELECT image FROM gallery_category WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
        
        // Delete Record
        $deleteStmt = $conn->prepare("DELETE FROM gallery_category WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        
        if ($deleteStmt->execute()) {
            // Delete Image File
            $imagePath = "../uploads/category/" . $category['image'];
            if (!empty($category['image']) && $category['image'] !== 'dummy_category.png' && file_exists($imagePath)) {
                unlink($imagePath);
            }
            $success_count++;
        } else {
            $error_count++;
        }
    } else {
        $error_count++;
    }
}

if ($success_count > 0 && $error_count == 0) {
    $_SESSION['flash_msg'] = "$success_count category(ies) deleted successfully";
    $_SESSION['flash_type'] = "danger";
} elseif ($success_count > 0 && $error_count > 0) {
    $_SESSION['flash_msg'] = "$success_count deleted, $error_count failed";
    $_SESSION['flash_type'] = "danger";
} else {
    $_SESSION['flash_msg'] = "Error deleting category(ies)";
    $_SESSION['flash_type'] = "danger";
}

header("Location: gallery_category_list.php");
exit;
?>
