<?php
include __DIR__ . '/permission_check.php';
include __DIR__ . '/../db_connect.php';

// ── Filter by status ──────────────────────────────────────────
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';

$validFilters = ['all', 'verified', 'not_verified'];
if (!in_array($filter, $validFilters)) $filter = 'all';

$labelMap = [
    'all'         => 'All_Event_Requests',
    'verified'    => 'Verified_Event_Requests',
    'not_verified'=> 'Not_Verified_Event_Requests',
];

$filename = 'SVIS_' . $labelMap[$filter] . '_' . date('Y-m-d') . '.csv';

// ── Build WHERE clause ────────────────────────────────────────
$whereClause = '';
if ($filter === 'verified')     $whereClause = 'WHERE event_status = 1';
if ($filter === 'not_verified') $whereClause = 'WHERE event_status = 0';

$sql = "SELECT
            id,
            full_name,
            alumni_id,
            batch_year,
            phone,
            email,
            event_title,
            event_category,
            event_description,
            purpose,
            event_mode,
            venue,
            online_platform,
            start_datetime,
            end_datetime,
            alternate_date,
            event_duration,
            expected_participants,
            event_status,
            created_at
        FROM event_requests
        $whereClause
        ORDER BY id DESC";

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
    '#',
    'S.No',
    'Full Name',
    'Alumni ID',
    'Batch Year',
    'Phone',
    'Email',
    'Event Title',
    'Event Category',
    'Event Description',
    'Purpose',
    'Event Mode',
    'Venue / Location',
    'Online Platform',
    'Start Date & Time',
    'Preferred Time',
    'Alternate Date',
    'Event Duration in Hours',
    'Expected Participants',
    'Verification Status',
    'Submitted On',
]);

// ── Data rows ─────────────────────────────────────────────────
if ($result && $result->num_rows > 0) {
    $sno = 1;
    while ($row = $result->fetch_assoc()) {
        $status     = $row['event_status'] == 1 ? 'Verified' : 'Not Verified';
        $startDT    = !empty($row['start_datetime'])  ? date('d M Y, h:i A', strtotime($row['start_datetime']))  : '';
        $endDT      = !empty($row['end_datetime'])    ? date('h:i A', strtotime($row['end_datetime']))           : '';
        $altDate    = !empty($row['alternate_date'])  ? date('d M Y', strtotime($row['alternate_date']))         : '';
        $submittedOn= !empty($row['created_at'])      ? date('d M Y', strtotime($row['created_at']))             : '';

        fputcsv($out, [
            $row['id'],
            $sno,
            $row['full_name'],
            $row['alumni_id'],
            $row['batch_year'],
            $row['phone'],
            $row['email'],
            $row['event_title'],
            $row['event_category'],
            $row['event_description'],
            $row['purpose'],
            $row['event_mode'],
            $row['venue'],
            $row['online_platform'],
            $startDT,
            $endDT,
            $altDate,
            $row['event_duration'],
            $row['expected_participants'],
            $status,
            $submittedOn,
        ]);
        $sno++;
    }
}

fclose($out);
exit;
?>
