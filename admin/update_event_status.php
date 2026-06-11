<?php
include('permission_check.php');
require_admin_permission('edit');
include('../db_connect.php');

$id = $_POST['id'];
$status = $_POST['event_status'];

$update = "UPDATE event_requests SET event_status='$status' WHERE id='$id'";

if (mysqli_query($conn, $update)) {
    echo "success";
} else {
    echo "error: " . mysqli_error($conn);
}
?>