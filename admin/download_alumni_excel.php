<?php
include('permission_check.php');
include('../db_connect.php');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=alumni_filtered_list.xls");

$where = [];

if (!empty($_GET['name'])) {
    $name = $conn->real_escape_string($_GET['name']);
    $where[] = "full_name LIKE '%$name%'";
}

if (!empty($_GET['batch'])) {
    $batch = intval($_GET['batch']);
    $where[] = "batch_year = $batch";
}

if (isset($_GET['status']) && $_GET['status'] !== "") {
    $status = intval($_GET['status']);
    $where[] = "verified_status = $status";
}

$sql = "SELECT id, full_name, email, phone, gender, batch_year, created_at, verified_status 
        FROM alumni_register";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Gender</th>
        <th>Batch</th>
        <th>Created</th>
        <th>Verified Status</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    $status = $row['verified_status'] ? "Verified" : "Not Verified";

    echo "
    <tr>
        <td>{$row['id']}</td>
        <td>{$row['full_name']}</td>
        <td>{$row['email']}</td>
        <td>{$row['phone']}</td>
        <td>{$row['gender']}</td>
        <td>{$row['batch_year']}</td>
        <td>{$row['created_at']}</td>
        <td>$status</td>
    </tr>";
}
echo "</table>";
?>
