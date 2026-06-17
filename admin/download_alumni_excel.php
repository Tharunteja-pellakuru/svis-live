<?php
include('permission_check.php');
include('../db_connect.php');

// ── Determine filter type ─────────────────────────────────────
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';

$statusMap = [
    'all'              => null,
    'approved'         => 1,
    'pending-approval' => 2,
    'pending-email'    => 0,
    'rejected'         => 3,
];

if (!array_key_exists($filter, $statusMap)) {
    $filter = 'all';
}

$labelMap = [
    'all'              => 'All_Users',
    'approved'         => 'Approved_Users',
    'pending-approval' => 'Pending_Approval_Users',
    'pending-email'    => 'Pending_Email_Users',
    'rejected'         => 'Rejected_Users',
];

$statusLabelMap = [
    0 => 'Pending Email Verification',
    1 => 'Approved',
    2 => 'Pending Admin Approval',
    3 => 'Rejected',
];

$filename = 'SVIS_Alumni_' . $labelMap[$filter] . '_' . date('Y-m-d') . '.csv';

// ── Build query ───────────────────────────────────────────────
$whereClause = '';
if ($statusMap[$filter] !== null) {
    $statusVal   = intval($statusMap[$filter]);
    $whereClause = "WHERE verified_status = $statusVal";
}

$sql = "SELECT
            a.id,
            a.full_name,
            a.email,
            a.phone,
            a.phonecode,
            a.gender,
            a.dob,
            a.batch_year,
            a.City,
            COALESCE(c.name, a.country) AS country_name,
            a.current_address,
            a.`Current Occupation`,
            a.`Designation`,
            a.`Company / Organization Name`,
            a.`Industry`,
            a.`Work Experience`,
            a.education_qualification,
            a.college_university,
            a.education_year,
            a.linkedin,
            a.instagram,
            a.verified_status,
            a.created_at
        FROM alumni_register a
        LEFT JOIN countries c ON c.id = a.country
        $whereClause
        ORDER BY a.id DESC";

$result = $conn->query($sql);

// ── Helper: parse a CSV field from DB safely ──────────────────
function parseEduField($raw, $index) {
    if (empty(trim($raw))) return '';
    $parts = array_map('trim', explode(',', $raw));
    return $parts[$index] ?? '';
}

// ── Find max number of education entries across all rows ──────
// We'll do a quick pass to find the maximum so we can build correct headers
$maxEdu = 1;
$rows   = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eduQ = array_filter(array_map('trim', explode(',', $row['education_qualification'] ?? '')));
        $cnt  = max(1, count($eduQ));
        if ($cnt > $maxEdu) $maxEdu = $cnt;
        $rows[] = $row;
    }
}

// ── Set CSV download headers ──────────────────────────────────
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// UTF-8 BOM so Excel opens it correctly
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

// ── Build header row ──────────────────────────────────────────
$headers = [
    '#',
    'S.No',
    'Full Name',
    'Email Address',
    'Phone Number',
    'Gender',
    'Date of Birth',
    'Batch Year (SVIS)',
    'City',
    'Country',
    'Current Address',
    'Current Occupation',
    'Designation',
    'Company / Organisation',
    'Industry',
    'Work Experience (Yrs)',
];

// Add dynamic education columns based on max entries found
$eduLabels = ['Graduation', 'Intermediate', 'School / SSC'];
for ($i = 0; $i < $maxEdu; $i++) {
    $label = $eduLabels[$i] ?? 'Education ' . ($i + 1);
    $headers[] = $label . ' — Qualification / Degree';
    $headers[] = $label . ' — Institution Name';
    $headers[] = $label . ' — Passing Year';
}

$headers[] = 'LinkedIn';
$headers[] = 'Instagram';
$headers[] = 'Account Status';
$headers[] = 'Registered On';

fputcsv($out, $headers);

// ── Data rows ─────────────────────────────────────────────────
$sno = 1;
foreach ($rows as $row) {
    $statusLabel = $statusLabelMap[$row['verified_status']] ?? 'Unknown';
    $dob         = !empty($row['dob'])        ? date('d M Y', strtotime($row['dob']))        : '';
    $createdAt   = !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '';
    $phone       = ($row['phonecode'] ? '+' . ltrim($row['phonecode'], '+') . ' ' : '') . $row['phone'];

    $eduQ = array_map('trim', explode(',', $row['education_qualification'] ?? ''));
    $eduC = array_map('trim', explode(',', $row['college_university']      ?? ''));
    $eduY = array_map('trim', explode(',', $row['education_year']          ?? ''));

    $dataRow = [
        $row['id'],
        $sno,
        $row['full_name'],
        $row['email'],
        $phone,
        ucfirst($row['gender']),
        $dob,
        $row['batch_year'],
        $row['City'],
        $row['country_name'],
        $row['current_address'],
        $row['Current Occupation'],
        $row['Designation'],
        $row['Company / Organization Name'],
        $row['Industry'],
        $row['Work Experience'],
    ];

    // Education columns — one set of 3 columns per education entry
    for ($i = 0; $i < $maxEdu; $i++) {
        $dataRow[] = $eduQ[$i] ?? '';
        $dataRow[] = $eduC[$i] ?? '';
        $dataRow[] = $eduY[$i] ?? '';
    }

    $dataRow[] = $row['linkedin'];
    $dataRow[] = $row['instagram'];
    $dataRow[] = $statusLabel;
    $dataRow[] = $createdAt;

    fputcsv($out, $dataRow);
    $sno++;
}

fclose($out);
exit;
?>
