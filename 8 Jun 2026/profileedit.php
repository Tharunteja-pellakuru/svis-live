<?php include('db_connect.php'); 
ini_set('display_errors', 1);
session_start();
if(!isset($_SESSION['alumni_id']) || $_SESSION['alumni_id'] == ""){
  header("location:index.php");
  exit();
}

$sql = "SELECT id, shortname, name, phonecode FROM countries";
$result = $conn->query($sql);
$countries = [];
while ($row = $result->fetch_assoc()) {
    $countries[] = $row;
}

$user_id = $_SESSION['alumni_id'];

// Fetch user data with country name
$query = "SELECT r.*, c.name as country_name FROM alumni_register r LEFT JOIN countries c ON r.country = c.id WHERE r.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update data when form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name  = $_POST['full_name'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];
    $gender     = $_POST['gender'];
    $batch_year = $_POST['batch_year'];
    $bio        = $_POST['bio'] ?? '';
    $Profession = $_POST['Industry'] ?? '';
    $Company    = $_POST['Company_/_Organization_Name'] ?? '';
    $City       = $_POST['City'] ?? '';
    $country    = $_POST['country'] ?? '';
    $current_address = $_POST['current_address'] ?? '';
    $phonecode  = $_POST['phonecode'] ?? '';
    $linkedin   = $_POST['linkedin'] ?? '';
    $instagram  = $_POST['instagram'] ?? '';
    $role       = $_POST['Current_Occupation'] ?? '';
    $designation = $_POST['Designation'] ?? '';
    $experience_years = $_POST['Work_Experience'] ?? '';
    $dob        = $_POST['dob'] ?? '';

    if (!empty($_FILES['user_image']['name'])) {
        $target_dir  = "uploads/";
        $image_name  = time() . "_" . basename($_FILES["user_image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["user_image"]["tmp_name"], $target_file)) {
            $user_image = $image_name;
        } else {
            $user_image = $user['user_image']; 
        }
    } else {
        $user_image = $user['user_image']; 
    }

    $education_qualification_csv = implode(",", $_POST['education_qualification'] ?? []);
    $college_university_csv      = implode(",", $_POST['college_university'] ?? []);
    $education_year_csv          = implode(",", $_POST['education_year'] ?? []);

    $full_name        = $conn->real_escape_string($full_name);
    $email            = $conn->real_escape_string($email);
    $phone            = $conn->real_escape_string($phone);
    $gender           = $conn->real_escape_string($gender);
    $batch_year       = $conn->real_escape_string($batch_year);
    $bio              = $conn->real_escape_string($bio);
    $Profession       = $conn->real_escape_string($Profession);
    $Company          = $conn->real_escape_string($Company);
    $City             = $conn->real_escape_string($City);
    $country          = $conn->real_escape_string($country);
    $current_address  = $conn->real_escape_string($current_address);
    $linkedin         = $conn->real_escape_string($linkedin);
    $instagram        = $conn->real_escape_string($instagram);
    $role             = $conn->real_escape_string($role);
    $designation      = $conn->real_escape_string($designation);
    $experience_years = $conn->real_escape_string($experience_years);
    $dob              = $conn->real_escape_string($dob);

    // Check if there is already a pending request
    $checkPending = $conn->prepare("SELECT id FROM profile_update_requests WHERE alumni_id = ? AND status = 0");
    $checkPending->bind_param("i", $user_id);
    $checkPending->execute();
    $pendingResult = $checkPending->get_result();
    
    if ($pendingResult->num_rows > 0) {
        $pendingReq = $pendingResult->fetch_assoc();
        $reqId = $pendingReq['id'];
        $updateQuery = "UPDATE `profile_update_requests` SET
            `full_name` = '$full_name',
            `email` = '$email',
            `phone` = '$phone',
            `gender` = '$gender',
            `batch_year` = '$batch_year',
            `bio` = '$bio',
            `user_image` = '$user_image',
            `Industry` = '$Profession',
            `Company / Organization Name` = '$Company',
            `City` = '$City',
            `country` = '$country',
            `education_qualification` = '$education_qualification_csv',
            `college_university` = '$college_university_csv',
            `education_year` = '$education_year_csv',
            `linkedin` = '$linkedin',
            `instagram` = '$instagram',
            `Current Occupation` = '$role',
            `Designation` = '$designation',
            `Work Experience` = '$experience_years',
            `dob` = '$dob',
            `phonecode`='$phonecode',
            `current_address` = '$current_address',
            `created_at` = CURRENT_TIMESTAMP
        WHERE `id` = '$reqId'";
    } else {
        $updateQuery = "INSERT INTO `profile_update_requests` (
            `alumni_id`, `full_name`, `email`, `phone`, `gender`, `batch_year`, `bio`, `user_image`, 
            `Industry`, `Company / Organization Name`, `City`, `country`, `education_qualification`, 
            `college_university`, `education_year`, `linkedin`, `instagram`, 
            `Current Occupation`, `Designation`, `Work Experience`, `dob`, `phonecode`, `current_address`
        ) VALUES (
            '$user_id', '$full_name', '$email', '$phone', '$gender', '$batch_year', '$bio', '$user_image', 
            '$Profession', '$Company', '$City', '$country', '$education_qualification_csv', 
            '$college_university_csv', '$education_year_csv', '$linkedin', '$instagram', 
            '$role', '$designation', '$experience_years', '$dob', '$phonecode', '$current_address'
        )";
    }

    if (mysqli_query($conn, $updateQuery)) {
        header("location:profileedit.php?status=pending");
        exit();
    } else {
        $error = mysqli_error($conn);
    }
}

// Check for pending request to show status message
$pendingQuery = $conn->prepare("SELECT id FROM profile_update_requests WHERE alumni_id = ? AND status = 0");
$pendingQuery->bind_param("i", $user_id);
$pendingQuery->execute();
$hasPending = $pendingQuery->get_result()->num_rows > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Profile - SVIS Alumni Network</title>
    <link rel="icon" type="image/png" href="Logo/FavIcon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="shared.css"/>
    <style>
        /* ── VIEW MODE STYLES ── */
        .view-section { display: block; }
        .edit-section { display: none; padding-top: 2rem; }

        .sec-title-wrap {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .profile-view-hdr {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            margin-top: 1rem;
            margin-bottom: 2.5rem;
            background-color: #fff;
            background-image: repeating-linear-gradient(-45deg, rgba(29, 78, 216, 0.03) 0, rgba(29, 78, 216, 0.03) 1px, transparent 1px, transparent 10px);
            padding: 3rem 2.5rem;
            border-radius: 16px;
            border: 1.5px solid rgba(29, 78, 216, 0.15);
            box-shadow: 0 6px 24px rgba(29, 78, 216, 0.08);
            position: relative;
            overflow: hidden;
        }
        .pv-av {
            width: 140px; height: 140px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid #fff;
            box-shadow: 0 8px 24px rgba(29, 78, 216, 0.2);
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            position: relative;
            z-index: 2;
            flex-shrink: 0;
        }
        .pv-av img { width: 100%; height: 100%; object-fit: cover; }
        .pv-av span { font-size: 3.5rem; font-weight: 700; color: var(--blue); }
        
        .pv-info h2 { font-family: 'Lato', serif; font-size: 1.8rem; color: var(--blue-dark); margin-bottom: 0.3rem; }
        .pv-info p { font-family: 'Poppins', sans-serif; font-size: 0.95rem; color: var(--muted); margin-bottom: 1rem; }
        
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--blue);
            color: #fff;
            padding: 0.7rem 1.6rem;
            border-radius: 999px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid var(--gold);
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }
        .btn-action:hover { 
            background: #1741b0;
            color: #fff;
            box-shadow: 0 8px 25px rgba(29, 78, 216, 0.45);
            transform: scale(1.04);
            border-color: var(--gold);
        }
        .btn-action.secondary { 
            background: #f3f4f6; 
            color: var(--text); 
            border: 1px solid #d1d5db; 
            box-shadow: none; 
        }
        .btn-action.secondary:hover { 
            background: #e5e7eb; 
            transform: translateY(-2px) scale(1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #d1d5db;
        }

        .v-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .v-field { margin-bottom: 0.5rem; }
        .v-label { font-family: 'Poppins', sans-serif; font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem; }
        .v-value { font-family: 'Poppins', sans-serif; font-size: 0.95rem; color: #111827; font-weight: 600; }
        
        .edu-item-v { padding: 0.8rem 0; border-bottom: 1px dashed #e5eeff; }
        .edu-item-v:last-child { border-bottom: none; }

        /* ── FORM STYLES ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .input-icon-inner {
            position: absolute;
            left: 1rem;
            color: var(--muted);
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 2;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.8rem;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
        }
        .form-group textarea { padding-left: 1rem; min-height: 100px; resize: vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            background: #fff;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.1);
        }

        .photo-edit-wrap {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--blue-light);
            border-radius: 12px;
            border: 1px solid var(--blue-mid);
        }
        .avatar-edit-preview {
            width: 90px; height: 90px;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex; align-items: center; justify-content: center;
        }
        .avatar-edit-preview img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-edit-preview span { font-size: 2.5rem; font-weight: 700; color: var(--blue); }

        .edu-row {
            display: grid;
            grid-template-columns: 1fr 1fr 100px auto;
            gap: 1rem;
            background: #f9fafb;
            padding: 1.25rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            align-items: flex-end;
        }
        .edu-row .form-group { margin-bottom: 0; }
        .edu-row input, .edu-row select { padding-left: 1rem !important; }

        .btn-add-edu {
            width: 100%;
            padding: 0.75rem;
            background: #fff;
            border: 2px dashed #d1d5db;
            color: var(--muted);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-add-edu:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-light); }

        .btn-del-edu {
            background: none; border: none; color: #ef4444; font-size: 1.1rem; cursor: pointer; padding: 0.5rem;
        }

        .save-btn-bar {
            margin-top: 3rem;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding-bottom: 2rem;
        }
        .btn-save {
            background: var(--blue);
            color: #fff;
            padding: 0.8rem 2.5rem;
            border-radius: 999px;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border: 2px solid var(--gold);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(29, 78, 216, 0.2);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-save:hover { 
            background: #1741b0;
            transform: scale(1.04); 
            box-shadow: 0 8px 25px rgba(29, 78, 216, 0.45); 
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.8rem 2.5rem;
            border-radius: 999px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            border: 1px solid #d1d5db;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-cancel:hover { background: #e5e7eb; color: #1f2937; }

        @media (max-width: 640px) {
            .v-grid { grid-template-columns: 1fr; }
            .edu-row { grid-template-columns: 1fr; }
            .profile-view-hdr { flex-direction: column; text-align: center; }
            .photo-edit-wrap { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- ===== NAV ===== -->
    <nav class="site-nav">
        <div class="nav-inner">
            <a href="index.php" class="nav-logo">
                <img src="Logo/Logo.svg" alt="SVIS Logo"/>
            </a>
            <div class="nav-links">
                <a href="index.php"    class="nav-link">Home</a>
                <a href="directory.php" class="nav-link">Directory</a>
                <a href="event.php"   class="nav-link">Events</a>
                <a href="about.php"    class="nav-link">About</a>
                <a href="founders.php" class="nav-link">Founders</a>
                <a href="gallery.php"  class="nav-link">Gallery</a>
                <a href="videos.php"   class="nav-link">Videos</a>
                <a href="profileedit.php" class="nav-link active">Profile</a>
            </div>
            <div class="nav-right">
                <a href="logout.php" class="nav-login-btn">Logout</a>
                <button class="hamburger-btn" id="hamburger-btn" aria-label="Menu">
                    <svg id="hamburger-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobile-menu">
        <a href="index.php"    class="nav-link">Home</a>
        <a href="directory.php" class="nav-link">Directory</a>
        <a href="event.php"   class="nav-link">Events</a>
        <a href="about.php"    class="nav-link">About</a>
        <a href="founders.php" class="nav-link">Founders</a>
        <a href="gallery.php"  class="nav-link">Gallery</a>
        <a href="videos.php"   class="nav-link">Videos</a>
        <a href="profileedit.php" class="nav-link active">Profile</a>
        <a href="logout.php" class="nav-login-btn">Logout</a>
    </div>

    <!-- ===== HERO ===== -->
    <div class="policy-hero">
        <h1>Profile Settings</h1>
        <p>Manage your SVIS Alumni account and professional identity</p>
    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="policy-page">
        <div class="policy-container">

            <?php if ($hasPending): ?>
                <div style="background: #fff9e6; border: 1px solid #ffeeba; color: #856404; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-family: 'Poppins', sans-serif; font-size: 0.9rem; font-weight: 500;">
                    <i class="fas fa-clock" style="font-size: 1.2rem; color: #d39e00;"></i>
                    <div>
                        <strong>Update Pending Approval:</strong> Your recent profile changes are currently being reviewed by the administration. Once verified, they will be reflected on your public profile.
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'pending'): ?>
                <div style="background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px; font-family: 'Poppins', sans-serif; font-size: 0.9rem; font-weight: 500;">
                    <i class="fas fa-check-circle" style="font-size: 1.2rem; color: #4caf50;"></i>
                    <div>
                        <strong>Request Submitted:</strong> Your profile update request has been successfully submitted for administrative verification.
                    </div>
                </div>
            <?php endif; ?>

            <!-- ===== VIEW SECTION ===== -->
            <div id="viewSection" class="view-section">
                <div class="profile-view-hdr">
                    <div class="pv-av">
                        <?php if (!empty($user['user_image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($user['user_image']); ?>" alt="Profile"/>
                        <?php else: ?>
                            <span><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="pv-info">
                        <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                        <p><?php echo htmlspecialchars($user['Current Occupation'] ?? 'Alumnus'); ?> • Class of <?php echo $user['batch_year']; ?></p>
                        <button type="button" class="btn-action" onclick="showEdit()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                </div>

                <!-- About Me -->
                <?php if(!empty($user['bio'])): ?>
                <div class="policy-section">
                    <div class="policy-section-header">
                        <div class="sec-title-wrap">
                            <div class="sec-icon"><i class="fas fa-info-circle"></i></div>
                            <h2>About Me</h2>
                        </div>
                    </div>
                    <p style="white-space:pre-line;"><?php echo htmlspecialchars($user['bio']); ?></p>
                </div>
                <?php endif; ?>

                <!-- Personal Info -->
                <div class="policy-section">
                    <div class="policy-section-header">
                        <div class="sec-title-wrap">
                            <div class="sec-icon"><i class="fas fa-user"></i></div>
                            <h2>Personal Information</h2>
                        </div>
                    </div>
                    <div class="v-grid">
                        <div class="v-field">
                            <div class="v-label">Email Address</div>
                            <div class="v-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Gender</div>
                            <div class="v-value"><?php echo $user['gender'] ?: 'Not specified'; ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Date of Birth</div>
                            <div class="v-value"><?php echo $user['dob'] ? date("d M, Y", strtotime($user['dob'])) : 'Not specified'; ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Location</div>
                            <div class="v-value"><?php echo htmlspecialchars($user['City']); ?>, <?php echo htmlspecialchars($user['country_name'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="v-field" style="grid-column: span 2;">
                            <div class="v-label">Current Address</div>
                            <div class="v-value"><?php echo htmlspecialchars($user['current_address'] ?? 'N/A'); ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Phone Number</div>
                            <div class="v-value"><?php echo $user['phonecode'] ? '+'.ltrim($user['phonecode'], '+') : '+91'; ?> <?php echo htmlspecialchars($user['phone']); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Professional Info -->
                <div class="policy-section">
                    <div class="policy-section-header">
                        <div class="sec-title-wrap">
                            <div class="sec-icon"><i class="fas fa-briefcase"></i></div>
                            <h2>Professional Details</h2>
                        </div>
                    </div>
                    <div class="v-grid">
                        <div class="v-field">
                            <div class="v-label">Current Occupation</div>
                            <div class="v-value"><?php echo htmlspecialchars($user['Current Occupation'] ?: 'N/A'); ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Designation</div>
                            <div class="v-value"><?php echo htmlspecialchars($user['Designation'] ?: 'N/A'); ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Company / Organization Name</div>
                            <div class="v-value"><?php echo htmlspecialchars($user['Company / Organization Name'] ?: 'N/A'); ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Industry</div>
                            <div class="v-value"><?php echo htmlspecialchars($user['Industry'] ?: 'N/A'); ?></div>
                        </div>
                        <div class="v-field">
                            <div class="v-label">Work Experience</div>
                            <div class="v-value"><?php echo $user['Work Experience'] ?: '0'; ?> Years</div>
                        </div>
                    </div>
                </div>

                <!-- Education -->
                <div class="policy-section">
                    <div class="policy-section-header">
                        <div class="sec-title-wrap">
                            <div class="sec-icon"><i class="fas fa-graduation-cap"></i></div>
                            <h2>Education History</h2>
                        </div>
                    </div>
                    <?php
                        $edu_q = explode(",", $user['education_qualification'] ?? "");
                        $edu_c = explode(",", $user['college_university'] ?? "");
                        $edu_y = explode(",", $user['education_year'] ?? "");
                        $found = false;
                        for($i=0; $i<count($edu_q); $i++):
                            if(empty(trim($edu_q[$i]))) continue;
                            $found = true;
                    ?>
                    <div class="edu-item-v">
                        <div class="v-value"><?php echo htmlspecialchars($edu_q[$i]); ?></div>
                        <div class="v-value" style="color:var(--muted); font-size:0.85rem;"><?php echo htmlspecialchars($edu_c[$i] ?? ''); ?> • Class of <?php echo htmlspecialchars($edu_y[$i] ?? ''); ?></div>
                    </div>
                    <?php endfor; ?>
                    <?php if(!$found) echo '<p style="color:var(--muted); font-size:0.9rem;">No education details added yet.</p>'; ?>
                </div>

                <!-- Social -->
                <div class="policy-section">
                    <div class="policy-section-header">
                        <div class="sec-title-wrap">
                            <div class="sec-icon"><i class="fas fa-share-alt"></i></div>
                            <h2>Social Presence</h2>
                        </div>
                    </div>
                    <div class="v-grid">
                        <div class="v-field">
                            <div class="v-label">LinkedIn</div>
                            <div class="v-value"><?php echo $user['linkedin'] ? '<a href="'.htmlspecialchars($user['linkedin']).'" target="_blank" style="color:var(--blue); font-weight:600;">View Profile <i class="fas fa-external-link-alt" style="font-size:0.7rem;"></i></a>' : 'Not linked'; ?></div>
                        </div>

                        <div class="v-field">
                            <div class="v-label">Instagram</div>
                            <div class="v-value"><?php echo $user['instagram'] ? '<a href="'.htmlspecialchars($user['instagram']).'" target="_blank" style="color:var(--blue); font-weight:600;">View Profile <i class="fas fa-external-link-alt" style="font-size:0.7rem;"></i></a>' : 'Not linked'; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== EDIT SECTION ===== -->
            <div id="editSection" class="edit-section">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <h2 style="font-family:'Lato',serif; color:var(--blue-dark); margin:0;">Edit Profile</h2>
                    <button type="button" class="btn-action secondary" onclick="showView()">
                        <i class="fas fa-arrow-left"></i> Back to Preview
                    </button>
                </div>

                <form method="POST" action="profileedit.php" enctype="multipart/form-data" id="profileForm">
                    <input type="hidden" name="update_profile" value="1" />

                    <!-- Account Details -->
                    <div class="policy-section">
                        <div class="policy-section-header">
                            <div class="sec-title-wrap">
                                <div class="sec-icon"><i class="fas fa-id-card"></i></div>
                                <h2>Basic Information</h2>
                            </div>
                        </div>

                        <div class="photo-edit-wrap">
                            <div class="avatar-edit-preview">
                                <?php if (!empty($user['user_image'])): ?>
                                    <img id="profilePreview" src="uploads/<?php echo htmlspecialchars($user['user_image']); ?>" alt="Profile"/>
                                <?php else: ?>
                                    <span id="profileInitials"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></span>
                                    <img id="profilePreview" src="" alt="Profile" style="display:none;"/>
                                <?php endif; ?>
                            </div>
                            <div style="text-align:left;">
                                <label for="photoInput" class="btn-action" style="padding:0.5rem 1.2rem; font-size:0.8rem;">
                                    <i class="fas fa-camera"></i> Change Photo
                                </label>
                                <input type="file" id="photoInput" name="user_image" accept="image/*" style="display:none;"/>
                                <p style="font-size:0.75rem; color:var(--muted); margin-top:0.5rem;">JPG, PNG or WEBP. Max 2MB.</p>
                            </div>
                        </div>

                        <div class="v-grid">
                            <div class="form-group">
                                <label>Full Name<span style="color:#ef4444;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fas fa-user input-icon-inner"></i>
                                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email Address<span style="color:#ef4444;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fas fa-envelope input-icon-inner"></i>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Gender<span style="color:#ef4444;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fas fa-venus-mars input-icon-inner"></i>
                                    <select name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option <?php if($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                        <option <?php if($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                        <option <?php if($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <div class="input-wrap">
                                    <i class="fas fa-calendar-day input-icon-inner"></i>
                                    <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Batch Year<span style="color:#ef4444;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fas fa-user-graduate input-icon-inner"></i>
                                    <select name="batch_year" required>
                                        <option value="">Select Year</option>
                                        <?php
                                            for($y=date("Y"); $y>=2008; $y--){
                                                $sel = ($user['batch_year'] == $y) ? 'selected' : '';
                                                echo '<option value="'.$y.'" '.$sel.'>'.$y.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>City<span style="color:#ef4444;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fas fa-city input-icon-inner"></i>
                                    <input type="text" name="City" value="<?php echo htmlspecialchars($user['City'] ?? ''); ?>" required/>
                                </div>
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Current Address</label>
                                <div class="input-wrap" style="height: auto;">
                                    <i class="fas fa-map-marker-alt input-icon-inner" style="top: 15px; transform: none;"></i>
                                    <textarea name="current_address" rows="3" style="width:100%; padding:10px 10px 10px 40px; border:1px solid #e2e8f0; border-radius:12px; font-family:'Inter',sans-serif; transition:all 0.3s;" placeholder="Enter your full current address..."><?php echo htmlspecialchars($user['current_address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Country<span style="color:#ef4444;">*</span></label>
                                <div class="input-wrap">
                                    <i class="fas fa-globe input-icon-inner"></i>
                                    <select name="country" id="countrySelect" required>
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $c) { ?>
                                            <option value="<?php echo $c['id']; ?>" data-phone="<?php echo $c['phonecode']; ?>" <?php echo ($user['country'] == $c['id']) ? 'selected' : ''; ?>>
                                                <?php echo $c['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Phone Number<span style="color:#ef4444;">*</span></label>
                                <div style="display:flex; gap:0.5rem;">
                                    <input type="text" id="phoneCode" name="phonecode" value="<?php echo $user['phonecode'] ? '+'.ltrim($user['phonecode'], '+') : '+91'; ?>" style="width:70px; text-align:center; padding-left:0.5rem;"/>
                                    <div class="input-wrap" style="flex:1;">
                                        <i class="fas fa-phone-alt input-icon-inner"></i>
                                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:1rem;">
                            <label>Short Bio</label>
                            <textarea name="bio" placeholder="A brief introduction about yourself..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                        </div>
                    </div>

                    <!-- Professional -->
                    <div class="policy-section">
                        <div class="policy-section-header">
                            <div class="sec-title-wrap">
                                <div class="sec-icon"><i class="fas fa-briefcase"></i></div>
                                <h2>Professional Experience</h2>
                            </div>
                        </div>
                        <div class="v-grid">
                            <div class="form-group">
                                <label>Current Occupation</label>
                                <div class="input-wrap">
                                    <i class="fas fa-user-tie input-icon-inner"></i>
                                    <input type="text" name="Current Occupation" value="<?php echo htmlspecialchars($user['Current Occupation'] ?? ''); ?>" placeholder="e.g. Software Engineer"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Designation</label>
                                <div class="input-wrap">
                                    <i class="fas fa-id-badge input-icon-inner"></i>
                                    <input type="text" name="Designation" value="<?php echo htmlspecialchars($user['Designation'] ?? ''); ?>" placeholder="e.g. Senior Manager"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Company / Organization Name</label>
                                <div class="input-wrap">
                                    <i class="fas fa-building input-icon-inner"></i>
                                    <input type="text" name="Company / Organization Name" value="<?php echo htmlspecialchars($user['Company / Organization Name'] ?? ''); ?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Industry</label>
                                <div class="input-wrap">
                                    <i class="fas fa-industry input-icon-inner"></i>
                                    <input type="text" name="Industry" value="<?php echo htmlspecialchars($user['Industry'] ?? ''); ?>" placeholder="e.g. Information Technology"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Work Experience (Years)</label>
                                <div class="input-wrap">
                                    <i class="fas fa-history input-icon-inner"></i>
                                    <input type="number" name="Work Experience" value="<?php echo htmlspecialchars($user['Work Experience'] ?? ''); ?>" step="0.5" min="0"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Education -->
                    <div class="policy-section">
                        <div class="policy-section-header">
                            <div class="sec-title-wrap">
                                <div class="sec-icon"><i class="fas fa-graduation-cap"></i></div>
                                <h2>Education Details</h2>
                            </div>
                        </div>
                        <div id="educationRows">
                            <?php
                                $edu_q = explode(",", $user['education_qualification'] ?? "");
                                $edu_c = explode(",", $user['college_university'] ?? "");
                                $edu_y = explode(",", $user['education_year'] ?? "");
                                $count = max(count($edu_q), 1);
                                for($i=0; $i<$count; $i++):
                            ?>
                            <div class="edu-row">
                                <div class="form-group">
                                    <label>Qualification</label>
                                    <input type="text" name="education_qualification[]" value="<?php echo htmlspecialchars($edu_q[$i] ?? ''); ?>" placeholder="e.g. B.Tech"/>
                                </div>
                                <div class="form-group">
                                    <label>College/University</label>
                                    <input type="text" name="college_university[]" value="<?php echo htmlspecialchars($edu_c[$i] ?? ''); ?>" placeholder="e.g. JNTU"/>
                                </div>
                                <div class="form-group">
                                    <label>Year</label>
                                    <select name="education_year[]">
                                        <option value="">Year</option>
                                        <?php
                                            for($y=date("Y"); $y>=1990; $y--){
                                                $sel = (isset($edu_y[$i]) && $edu_y[$i] == $y) ? 'selected' : '';
                                                echo '<option value="'.$y.'" '.$sel.'>'.$y.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <button type="button" class="btn-del-edu removeEducationRow" title="Remove">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <button type="button" class="btn-add-edu" id="addEducationRow">
                            <i class="fas fa-plus"></i> Add More Education
                        </button>
                    </div>

                    <!-- Social -->
                    <div class="policy-section">
                        <div class="policy-section-header">
                            <div class="sec-title-wrap">
                                <div class="sec-icon"><i class="fas fa-share-alt"></i></div>
                                <h2>Social Links</h2>
                            </div>
                        </div>
                        <div class="v-grid">
                            <div class="form-group">
                                <label>LinkedIn URL</label>
                                <div class="input-wrap">
                                    <i class="fab fa-linkedin input-icon-inner" style="color: #0077b5;"></i>
                                    <input type="url" name="linkedin" value="<?php echo htmlspecialchars($user['linkedin'] ?? ''); ?>" placeholder="https://linkedin.com/in/..."/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Instagram URL</label>
                                <div class="input-wrap">
                                    <i class="fab fa-instagram input-icon-inner" style="color: #e4405f;"></i>
                                    <input type="url" name="instagram" value="<?php echo htmlspecialchars($user['instagram'] ?? ''); ?>" placeholder="https://instagram.com/..."/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="save-btn-bar">
                        <button type="button" class="btn-cancel" onclick="showView()">Cancel</button>
                        <button type="submit" class="btn-save">Update Profile</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a  href="index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Home</a></li>
                    <li><a  href="about.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : '' ?>">About</a></li>
                    <li><a  href="directory.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'directory.php') ? 'active' : '' ?>">Directory</a></li>
                    <li><a  href="event.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'event.php') ? 'active' : '' ?>">Events</a></li>
                    <li><a  href="gallery.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'gallery.php') ? 'active' : '' ?>">Gallery</a></li>
                    <li><a  href="videos.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'videos.php') ? 'active' : '' ?>">Videos</a></li>
                    <li><a  href="founders.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'founders.php') ? 'active' : '' ?>">Founders</a></li>
                    <li><a  href="privacy-policy.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'privacy-policy.php') ? 'active' : '' ?>">Privacy Policy</a></li>
                    <li><a  href="terms_use.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'terms_use.php') ? 'active' : '' ?>">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact Info</h3>
                <div class="footer-contact-item"><i class="fas fa-map-marker-alt"></i><span>150-152 Jayabheri Park, Behind Cine Planet Multiplex, Kompally, Hyderabad – 500100, Telangana</span></div>
                <div class="footer-contact-item"><i class="fas fa-phone"></i><span>040-23005000</span></div>
                <div class="footer-contact-item"><i class="fas fa-envelope"></i><span>info@svishyd.edu.in</span></div>
                <div class="footer-contact-item"><i class="fas fa-clock"></i><span>Mon–Fri: 8:15 AM – 3:15 PM<br>Saturday: 8:15 AM – 12:30 PM</span></div>
            </div>
            <div class="footer-col">
                <h3>Location</h3>
                <div class="footer-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15217.29501790504!2d78.478686!3d17.539766!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bcb855ec1fabca7%3A0x216c99b72461c6a0!2sSadhu%20Vaswani%20International%20School!5e0!3m2!1sen!2sin!4v1778574953962!5m2!1sen!2sin" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <p style="margin-top:1rem;">Follow Us</p>
                <div class="footer-socials">
                    <a href="https://www.facebook.com/svishydintsch" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/svishyderabad" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com/c/SadhuVaswaniInternationalSchoolHyderabad" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>©2026 Sadhu Vaswani International School, Hyderabad. All Rights Reserved. | Concept & Design by eparivartan</p>
        </div>
    </footer>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <script>
        const menuBtn   = document.getElementById('hamburger-btn');
        const mobileNav = document.getElementById('mobile-menu');
        const hamIcon   = document.getElementById('hamburger-icon');
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const open = mobileNav.classList.toggle('open');
            if (hamIcon) hamIcon.style.transform = open ? 'rotate(90deg)' : 'rotate(0)';
        });

        document.addEventListener('click', (e) => {
            if (!menuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
                mobileNav.classList.remove('open');
                if (hamIcon) hamIcon.style.transform = 'rotate(0)';
            }
        });

        document.getElementById('photoInput').addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('profilePreview');
                    const initials = document.getElementById('profileInitials');
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                    if(initials) initials.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('countrySelect').addEventListener('change', function() {
            const code = this.options[this.selectedIndex].getAttribute('data-phone');
            if (code) document.getElementById('phoneCode').value = '+' + code;
        });

        document.getElementById('addEducationRow').addEventListener('click', () => {
            const container = document.getElementById('educationRows');
            const row = document.createElement('div');
            row.className = 'edu-row';
            
            let yearsHtml = '<option value="">Year</option>';
            const currentYear = new Date().getFullYear();
            for(let y=currentYear; y>=1990; y--){
                yearsHtml += `<option value="${y}">${y}</option>`;
            }

            row.innerHTML = `
                <div class="form-group">
                    <label>Qualification</label>
                    <input type="text" name="education_qualification[]" placeholder="e.g. B.Tech"/>
                </div>
                <div class="form-group">
                    <label>College/University</label>
                    <input type="text" name="college_university[]" placeholder="e.g. JNTU"/>
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <select name="education_year[]">${yearsHtml}</select>
                </div>
                <button type="button" class="btn-del-edu removeEducationRow" title="Remove">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
            container.appendChild(row);
        });

        document.addEventListener('click', e => {
            if (e.target.closest('.removeEducationRow')) {
                const rows = document.querySelectorAll('.edu-row');
                if (rows.length > 1) {
                    e.target.closest('.edu-row').remove();
                }
            }
        });

        // Show toast if status is success
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') {
            showToast('Profile updated successfully!', 'success');
        }

        function showEdit() {
            document.getElementById('viewSection').style.display = 'none';
            document.getElementById('editSection').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function showView() {
            document.getElementById('editSection').style.display = 'none';
            document.getElementById('viewSection').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':'exclamation-circle'}"></i> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }
    </script>
</body>
</html>
