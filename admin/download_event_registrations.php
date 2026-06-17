<?php
include __DIR__ . '/permission_check.php';
include __DIR__ . '/../db_connect.php';

// ── Filter by event ───────────────────────────────────────────
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Get event name for filename
$eventName = 'All_Events';
if ($event_id > 0) {
    $evRes = $conn->query("SELECT event_name FROM events WHERE id = $event_id");
    if ($evRes && $evRes->num_rows > 0) {
        $evRow = $evRes->fetch_assoc();
        $eventName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $evRow['event_name']);
    }
}

$filename = 'SVIS_Event_Registrations_' . $eventName . '_' . date('Y-m-d') . '.csv';

// ── Build query ───────────────────────────────────────────────
$whereClause = $event_id > 0 ? "WHERE r.event_id = $event_id" : '';

$sql = "SELECT
            r.id AS reg_id,
            r.event_id,
            r.alumni_id,
            a.full_name,
            a.email,
            a.phone,
            a.batch_year,
            a.gender,
            a.City,
            e.event_name,
            e.venue AS event_venue,
            e.start_time AS event_date,
            r.event_category,
            r.attendance,
            r.registration_fee,
            r.created_at
        FROM event_registrations r
        JOIN alumni_register a ON r.alumni_id = a.id
        JOIN events e ON r.event_id = e.id
        $whereClause
        ORDER BY r.id DESC";

$result = $conn->query($sql);

// ── CSV download headers ──────────────────────────────────────
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF"; // UTF-8 BOM

$out = fopen('php://output', 'w');

// ── Header row ────────────────────────────────────────────────
fputcsv($out, [
    'Reg #',
    'S.No',
    'Alumni ID',
    'Full Name',
    'Email Address',
    'Phone Number',
    'Gender',
    'Batch Year',
    'City',
    'Event Name',
    'Event Venue',
    'Event Date',
    'Event Category',
    'Attendance',
    'Registration Fee',
    'Registered On',
]);

// ── Data rows ─────────────────────────────────────────────────
if ($result && $result->num_rows > 0) {
    $sno = 1;
    while ($row = $result->fetch_assoc()) {
        $fee        = $row['registration_fee'] > 0 ? 'INR ' . number_format($row['registration_fee'], 2) : 'Free';
        $eventDate  = !empty($row['event_date'])   ? date('d M Y', strtotime($row['event_date']))   : '';
        $regDate    = !empty($row['created_at'])   ? date('d M Y, h:i A', strtotime($row['created_at'])) : '';

        fputcsv($out, [
            $row['reg_id'],
            $sno,
            $row['alumni_id'],
            $row['full_name'],
            $row['email'],
            $row['phone'],
            ucfirst($row['gender']),
            $row['batch_year'],
            $row['City'],
            $row['event_name'],
            $row['event_venue'],
            $eventDate,
            $row['event_category'],
            $row['attendance'],
            $fee,
            $regDate,
        ]);
        $sno++;
    }
}

fclose($out);
exit;
?>
