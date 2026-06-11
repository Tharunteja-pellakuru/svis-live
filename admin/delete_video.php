    <?php
include('permission_check.php');
require_admin_permission('delete');
include('../db_connect.php');

$ids = [];
if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = $_POST['ids'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $ids = [$_GET['id']];
}

if (empty($ids)) {
    $_SESSION['flash_msg'] = "Invalid Request";
    $_SESSION['flash_type'] = "danger";
    header("Location: videos_list.php");
    exit;
}

$success_count = 0;
$error_count = 0;

$stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
foreach ($ids as $id) {
    $id = (int)$id;
    $stmt->bind_param("i", $id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $success_count++;
    } else {
        $error_count++;
    }
}
$stmt->close();

if ($success_count > 0 && $error_count == 0) {
    $_SESSION['flash_msg'] = "$success_count video(s) deleted successfully";
    $_SESSION['flash_type'] = "danger";
} elseif ($success_count > 0 && $error_count > 0) {
    $_SESSION['flash_msg'] = "$success_count video(s) deleted, $error_count failed";
    $_SESSION['flash_type'] = "danger";
} else {
    $_SESSION['flash_msg'] = "Error deleting video(s)";
    $_SESSION['flash_type'] = "danger";
}

header("Location: videos_list.php");
exit;
?>
