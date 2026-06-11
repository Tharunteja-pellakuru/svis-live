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
    header("Location: events_list.php");
    exit();
}

$success_count = 0;
$error_count = 0;

foreach ($ids as $id) {
    $id = intval($id);

    // Fetch image file name before deleting
    $stmt = $conn->prepare("SELECT event_image FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        $image = $event['event_image'];

        // Delete associated event registrations
        $deleteRegStmt = $conn->prepare("DELETE FROM event_registrations WHERE event_id = ?");
        $deleteRegStmt->bind_param("i", $id);
        $deleteRegStmt->execute();

        // Delete event record
        $deleteStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $deleteStmt->bind_param("i", $id);

        try {
            if ($deleteStmt->execute()) {
                // Delete image file if it exists
                if (!empty($image) && file_exists("../uploads/" . $image)) {
                    unlink("../uploads/" . $image);
                }
                $success_count++;
            } else {
                $error_count++;
            }
        } catch (mysqli_sql_exception $e) {
            $error_count++;
        }
    } else {
        $error_count++;
    }
}

if ($success_count > 0 && $error_count == 0) {
    $_SESSION['flash_msg'] = "$success_count event(s) deleted successfully";
    $_SESSION['flash_type'] = "danger";
} elseif ($success_count > 0 && $error_count > 0) {
    $_SESSION['flash_msg'] = "$success_count deleted, $error_count failed";
    $_SESSION['flash_type'] = "danger";
} else {
    $_SESSION['flash_msg'] = "Error deleting event(s)";
    $_SESSION['flash_type'] = "danger";
}

header("Location: events_list.php");
exit();
?>