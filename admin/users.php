<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
?>
<?php include('header.php'); ?>
<?php include('sidebar.php'); ?>
<?php include('../db_connect.php'); ?>

<?php
$flash_msg  = $_SESSION['flash_msg']  ?? '';
$flash_type = $_SESSION['flash_type'] ?? '';
$unsetResult = isset($_SESSION['flash_msg']);
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    require_admin_permission('edit');
    include('../config.php');
    $user_id = intval($_POST['user_id']);
    $new_status = intval($_POST['status']);
    
    // Fetch user details for email
    $user_res = $conn->query("SELECT full_name, email FROM alumni_register WHERE id = $user_id");
    $user_data = $user_res->fetch_assoc();
    $name = $user_data['full_name'];
    $email = $user_data['email'];

    $token = '';
    if ($new_status === 0) {
        $token = bin2hex(random_bytes(32));
        $stmt = $conn->prepare("UPDATE alumni_register SET verified_status = ?, verify_token = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("isi", $new_status, $token, $user_id);
        }
    } else {
        $stmt = $conn->prepare("UPDATE alumni_register SET verified_status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ii", $new_status, $user_id);
        }
    }

    if ($stmt) {
        if ($stmt->execute()) {
            $_SESSION['flash_msg'] = "User status updated successfully!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_msg'] = "Failed to update user status.";
            $_SESSION['flash_type'] = "danger";
        }
        $stmt->close();

        // Send Email if Approved (1) or Rejected (3) or Pending Email (0)
        if ($new_status == 1 || $new_status == 3 || $new_status === 0) {
            if ($new_status == 1) {
                $subject = "Account Approved - SVIS Alumni Network";
                $content = "
                    <div style='font-family: \"Inter\", \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f0f4ff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(29,78,216,0.1);'>
                        <div style='background: linear-gradient(135deg, #1e3a8a 0%, #1D4ED8 100%); padding: 40px 20px; text-align: center;'>
                            <img src='" . SITE_URL . "/Logo/Logo.png' alt='SVIS Logo' style='height: 80px; margin-bottom: 20px;'>
                            <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;'>You're In!</h1>
                        </div>
                        <div style='padding: 40px 30px; background-color: #ffffff;'>
                            <h2 style='color: #1e3a8a; margin-top: 0; font-size: 20px;'>Congratulations $name,</h2>
                            <p style='color: #4b5563; line-height: 1.7; font-size: 16px;'>Your registration for the <strong>SVIS Alumni Network</strong> has been approved.</p>
                            <div style='background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                                <p style='color: #166534; margin: 0; font-weight: 600; font-size: 15px;'>✅ Account Status: Active</p>
                            </div>
                            <div style='text-align: center; margin: 35px 0;'>
                                <a href='" . SITE_URL . "' style='display: inline-block; background-color: #1D4ED8; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; border: 2px solid #fbbf24;'>Access Portal</a>
                            </div>
                        </div>
                        <div style='background-color: #1e3a8a; padding: 20px; text-align: center; color: #bfdbfe; font-size: 12px;'>
                            © 2026 SVIS Alumni Network. All rights reserved.
                        </div>
                    </div>";
            } elseif ($new_status == 3) {
                $subject = "Registration Status Update - SVIS Alumni Network";
                $content = "
                    <div style='font-family: \"Inter\", \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #fff1f2; border-radius: 16px; overflow: hidden;'>
                        <div style='background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); padding: 40px 20px; text-align: center;'>
                            <img src='" . SITE_URL . "/Logo/Logo.png' alt='SVIS Logo' style='height: 80px; margin-bottom: 20px;'>
                            <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;'>Registration Update</h1>
                        </div>
                        <div style='padding: 40px 30px; background-color: #ffffff;'>
                            <h2 style='color: #991b1b; margin-top: 0; font-size: 20px;'>Hello $name,</h2>
                            <p style='color: #4b5563; line-height: 1.7; font-size: 16px;'>Thank you for your interest in joining the <strong>SVIS Alumni Network</strong>.</p>
                            <div style='background-color: #fff1f2; border-left: 4px solid #ef4444; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                                <p style='color: #991b1b; margin: 0; font-weight: 600; font-size: 15px;'>❌ Status: Not Approved</p>
                                <p style='color: #6b7280; margin: 10px 0 0 0; font-size: 14px;'>We are unable to approve your account at this time. Please contact <strong>info@svishyd.edu.in</strong> for more details.</p>
                            </div>
                        </div>
                        <div style='background-color: #1e3a8a; padding: 20px; text-align: center; color: #bfdbfe; font-size: 12px;'>
                            © 2026 SVIS Alumni Network. All rights reserved.
                        </div>
                    </div>";
            } elseif ($new_status === 0) {
                $verifyLink = SITE_URL . "/verify.php?token=" . $token;
                $subject = "Verify Your Email - SVIS Alumni Network";
                $content = "
                    <div style='font-family: \"Inter\", \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f0f4ff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(29,78,216,0.1);'>
                        <div style='background: linear-gradient(135deg, #1e3a8a 0%, #1D4ED8 100%); padding: 40px 20px; text-align: center;'>
                            <img src='" . SITE_URL . "/Logo/Logo.png' alt='SVIS Logo' style='height: 80px; margin-bottom: 20px;'>
                            <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.02em;'>Welcome to the Network!</h1>
                        </div>
                        <div style='padding: 40px 30px; background-color: #ffffff;'>
                            <h2 style='color: #1e3a8a; margin-top: 0; font-size: 20px;'>Hello $name,</h2>
                            <p style='color: #4b5563; line-height: 1.7; font-size: 16px;'>
                                Thank you for registering with the <strong>SVIS Alumni Network</strong>. We are thrilled to have you join our growing community of graduates.
                            </p>
                            <div style='background-color: #eff6ff; border-left: 4px solid #fbbf24; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                                <p style='color: #1e3a8a; margin: 0; font-weight: 600; font-size: 15px;'>
                                    📋 Registration Status: Pending Email Verification
                                </p>
                                <p style='color: #6b7280; margin: 10px 0 0 0; font-size: 14px;'>
                                    Please verify your email address. Once verified, the administrative team will review your details for activation.
                                </p>
                            </div>
                            <p style='color: #4b5563; line-height: 1.7; font-size: 15px;'>
                                Please verify your email address to ensure you receive all future communications:
                            </p>
                            <div style='text-align: center; margin: 35px 0;'>
                                <a href='$verifyLink' 
                                   style='display: inline-block; background-color: #1D4ED8; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; border: 2px solid #fbbf24; box-shadow: 0 4px 14px rgba(29,78,216,0.3); transition: all 0.3s;'>
                                   Verify Email Address
                                </a>
                            </div>
                            <p style='color: #9ca3af; font-size: 13px; text-align: center; margin-top: 40px;'>
                                If the button above doesn't work, copy and paste this link into your browser:<br>
                                <a href='$verifyLink' style='color: #1D4ED8; word-break: break-all;'>$verifyLink</a>
                            </p>
                        </div>
                        <div style='background-color: #1e3a8a; padding: 30px; text-align: center; color: #bfdbfe; font-size: 14px;'>
                            <p style='margin: 0;'><strong>Sadhu Vaswani International School, Hyderabad</strong></p>
                            <p style='margin: 5px 0 0 0;'>Building Connections, Shaping Futures.</p>
                            <div style='margin-top: 20px; border-top: 1px solid rgba(191,219,254,0.2); padding-top: 20px;'>
                                <p style='margin: 0; font-size: 12px; color: #9ca3af;'>©️ 2026 SVIS Alumni Network. All rights reserved.</p>
                            </div>
                        </div>
                    </div>";
            }
            sendBrevoEmail($email, $name, $subject, $content);
        }
    }
    
    echo "<script>window.location.href='users.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
    
    if (empty($ids) || !is_array($ids)) {
        $_SESSION['flash_msg'] = "No users selected.";
        $_SESSION['flash_type'] = "danger";
        echo "<script>window.location.href='users.php';</script>";
        exit;
    }

    if ($action === 'delete') {
        require_admin_permission('delete');
        $count = 0;
        $delReq = $conn->prepare("DELETE FROM profile_update_requests WHERE alumni_id = ?");
        $delEvent = $conn->prepare("DELETE FROM event_registrations WHERE alumni_id = ?");
        $stmt = $conn->prepare("DELETE FROM alumni_register WHERE id = ?");
        
        foreach ($ids as $user_id) {
            $id = intval($user_id);
            if ($delReq) {
                $delReq->bind_param("i", $id);
                $delReq->execute();
            }
            if ($delEvent) {
                $delEvent->bind_param("i", $id);
                $delEvent->execute();
            }
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) $count++;
            }
        }
        if ($delReq) $delReq->close();
        if ($delEvent) $delEvent->close();
        if ($stmt) $stmt->close();
        
        $_SESSION['flash_msg'] = "$count User(s) deleted successfully!";
        $_SESSION['flash_type'] = "success";
    } elseif ($action === 'approve' || $action === 'reject') {
        require_admin_permission('edit');
        include('../config.php');
        $new_status = ($action === 'approve') ? 1 : 3;
        $count = 0;
        
        foreach ($ids as $user_id) {
            $user_id = intval($user_id);
            
            // Fetch user details for email
            $user_res = $conn->query("SELECT full_name, email FROM alumni_register WHERE id = $user_id");
            if ($user_res && $user_res->num_rows > 0) {
                $user_data = $user_res->fetch_assoc();
                $name = $user_data['full_name'];
                $email = $user_data['email'];

                $stmt = $conn->prepare("UPDATE alumni_register SET verified_status = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("ii", $new_status, $user_id);
                    if ($stmt->execute()) {
                        $count++;
                        
                        // Send Email if Approved (1) or Rejected (3)
                        if ($new_status == 1) {
                            $subject = "Account Approved - SVIS Alumni Network";
                            $content = "
                                <div style='font-family: \"Inter\", \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f0f4ff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(29,78,216,0.1);'>
                                    <div style='background: linear-gradient(135deg, #1e3a8a 0%, #1D4ED8 100%); padding: 40px 20px; text-align: center;'>
                                        <img src='" . SITE_URL . "/Logo/Logo.png' alt='SVIS Logo' style='height: 80px; margin-bottom: 20px;'>
                                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;'>You're In!</h1>
                                    </div>
                                    <div style='padding: 40px 30px; background-color: #ffffff;'>
                                        <h2 style='color: #1e3a8a; margin-top: 0; font-size: 20px;'>Congratulations $name,</h2>
                                        <p style='color: #4b5563; line-height: 1.7; font-size: 16px;'>Your registration for the <strong>SVIS Alumni Network</strong> has been approved.</p>
                                        <div style='background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                                            <p style='color: #166534; margin: 0; font-weight: 600; font-size: 15px;'>✅ Account Status: Active</p>
                                        </div>
                                        <div style='text-align: center; margin: 35px 0;'>
                                            <a href='" . SITE_URL . "' style='display: inline-block; background-color: #1D4ED8; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; border: 2px solid #fbbf24;'>Access Portal</a>
                                        </div>
                                    </div>
                                    <div style='background-color: #1e3a8a; padding: 20px; text-align: center; color: #bfdbfe; font-size: 12px;'>
                                        © 2026 SVIS Alumni Network. All rights reserved.
                                    </div>
                                </div>";
                        } else {
                            $subject = "Registration Status Update - SVIS Alumni Network";
                            $content = "
                                <div style='font-family: \"Inter\", \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #fff1f2; border-radius: 16px; overflow: hidden;'>
                                    <div style='background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); padding: 40px 20px; text-align: center;'>
                                        <img src='" . SITE_URL . "/Logo/Logo.png' alt='SVIS Logo' style='height: 80px; margin-bottom: 20px;'>
                                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;'>Registration Update</h1>
                                    </div>
                                    <div style='padding: 40px 30px; background-color: #ffffff;'>
                                        <h2 style='color: #991b1b; margin-top: 0; font-size: 20px;'>Hello $name,</h2>
                                        <p style='color: #4b5563; line-height: 1.7; font-size: 16px;'>Thank you for your interest in joining the <strong>SVIS Alumni Network</strong>.</p>
                                        <div style='background-color: #fff1f2; border-left: 4px solid #ef4444; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                                            <p style='color: #991b1b; margin: 0; font-weight: 600; font-size: 15px;'>❌ Status: Not Approved</p>
                                            <p style='color: #6b7280; margin: 10px 0 0 0; font-size: 14px;'>We are unable to approve your account at this time. Please contact <strong>info@svishyd.edu.in</strong> for more details.</p>
                                        </div>
                                    </div>
                                    <div style='background-color: #1e3a8a; padding: 20px; text-align: center; color: #bfdbfe; font-size: 12px;'>
                                        © 2026 SVIS Alumni Network. All rights reserved.
                                    </div>
                                </div>";
                        }
                        sendBrevoEmail($email, $name, $subject, $content);
                    }
                    $stmt->close();
                }
            }
        }
        $statusStr = ($new_status == 1) ? 'Approved' : 'Rejected';
        $_SESSION['flash_msg'] = "$count User(s) $statusStr successfully!";
        $_SESSION['flash_type'] = "success";
    }
    
    echo "<script>window.location.href='users.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    require_admin_permission('delete');
    $user_id = intval($_POST['user_id']);
    // Delete related profile update requests first
    $delReq = $conn->prepare("DELETE FROM profile_update_requests WHERE alumni_id = ?");
    if ($delReq) {
        $delReq->bind_param("i", $user_id);
        $delReq->execute();
        $delReq->close();
    }

    // Delete related event registrations
    $delEvent = $conn->prepare("DELETE FROM event_registrations WHERE alumni_id = ?");
    if ($delEvent) {
        $delEvent->bind_param("i", $user_id);
        $delEvent->execute();
        $delEvent->close();
    }

    $stmt = $conn->prepare("DELETE FROM alumni_register WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $_SESSION['flash_msg'] = "User deleted successfully!";
            $_SESSION['flash_type'] = "success";
        } else {
            $_SESSION['flash_msg'] = "Failed to delete user.";
            $_SESSION['flash_type'] = "danger";
        }
        $stmt->close();
    } else {
        $_SESSION['flash_msg'] = "Database error.";
        $_SESSION['flash_type'] = "danger";
    }
    echo "<script>window.location.href='users.php';</script>";
    exit;
}


?>


<div class="dash-main">

  <!-- Toast Flash Message -->
  <?php if (!empty($flash_msg)): ?>
    <div id="toastFlash" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 14px 20px; border-radius: 10px; font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: <?= $flash_type === 'success' ? '#16a34a' : '#ef4444' ?>; color: #ffffff; border: none; transform: translateX(120%); opacity: 0; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
      <?php if ($flash_type === 'success'): ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <?php else: ?>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php endif; ?>
      <?= htmlspecialchars($flash_msg) ?>
    </div>
    <script>
      setTimeout(() => {
        const t = document.getElementById('toastFlash');
        if (t) { t.style.transform = 'translateX(0)'; t.style.opacity = '1'; setTimeout(() => { t.style.transform = 'translateX(120%)'; t.style.opacity = '0'; }, 3500); }
      }, 100);
    </script>
  <?php endif; ?>

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <div class="page-header-icon">
        <svg viewBox="0 0 24 24">
          <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
        </svg>
      </div>
      <div>
        <div class="page-header-label">Directory</div>
        <div class="page-header-title">Registered Alumni</div>
      </div>
    </div>
  </div>

  <?php
  // Basic stats
  $totalCount = $conn->query("SELECT COUNT(*) as total FROM alumni_register")->fetch_assoc()['total'];
  $verifiedCount = $conn->query("SELECT COUNT(*) as total FROM alumni_register WHERE verified_status = 1")->fetch_assoc()['total'];
  $pendingEmailCount = $conn->query("SELECT COUNT(*) as total FROM alumni_register WHERE verified_status = 0")->fetch_assoc()['total'];
  $pendingApprovalCount = $conn->query("SELECT COUNT(*) as total FROM alumni_register WHERE verified_status = 2")->fetch_assoc()['total'];
  $rejectedCount = $conn->query("SELECT COUNT(*) as total FROM alumni_register WHERE verified_status = 3")->fetch_assoc()['total'];
  ?>

  <style>
    /* Status Badges */
    .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.02em; }
    .badge-verified { background: var(--green-bg); color: var(--green); }
    .badge-pending { background: var(--amber-bg); color: var(--amber); }
    .badge-rejected { background: var(--red-bg); color: var(--red); }
    .badge-email-verified { background: rgba(54, 162, 235, 0.1); color: #36a2eb; }

    /* Action column styling & alignment fixes */
    .actions {
      gap: 6px !important;
    }
    .actions .btn-delete-sm {
      width: 32px !important;
      height: 32px !important;
    }
    
    @media (min-width: 851px) {
      .actions {
        flex-wrap: nowrap !important;
        align-items: center;
      }
      td[data-label="Action"] {
        min-width: 320px !important;
        white-space: nowrap !important;
      }
    }
    
    @media (max-width: 850px) {
      .actions {
        justify-content: center;
        flex-wrap: wrap;
        width: 100%;
      }
      .dash-table td:last-child::before {
        display: none !important;
      }
      .dash-table td:last-child {
        padding: 1.25rem 1rem !important;
        background: var(--surface-alt) !important;
      }
    }
    
    .user-info { display: flex; align-items: center; gap: 12px; }
    .user-avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--blue-light); color: var(--blue); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 0.8rem; flex-shrink: 0; }
    .user-details { display: flex; flex-direction: column; line-height: 1.3; }
    .user-name { font-weight: 700; color: var(--text); font-size: 0.92rem; text-decoration: none; }
    .user-name:hover { color: var(--blue); text-decoration: underline; }
    .user-email { font-size: 0.82rem; color: var(--text-3); }
    
    .btn-view { display: flex; align-items: center; gap: 6px; padding: 0 12px; height: 32px; background: var(--blue-light); color: var(--blue); border-radius: 8px; font-size: 0.8rem; font-weight: 700; text-decoration: none; border: 1.5px solid rgba(26,86,160,0.1); }
    .btn-view:hover { background: var(--blue); color: white; }
    .btn-delete-user { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; background: var(--red-bg, #fef2f2); color: var(--red, #c0392b); border: 1.5px solid rgba(192,57,43,0.1); cursor: pointer; transition: all 0.15s; }
    .btn-delete-user:hover { background: var(--red, #c0392b); color: white; }
    
    .status-select { height: 32px; padding: 0 10px; border-radius: 8px; border: 1.5px solid rgba(26,86,160,0.1); background: #fff; color: inherit; font-size: 0.8rem; font-weight: 600; cursor: pointer; outline: none; }
    .status-select:focus { border-color: var(--blue); }
    
    .filter-bar { display: flex; flex-wrap: nowrap; gap: 8px; padding: 1.25rem 1.5rem 1rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .filter-bar::-webkit-scrollbar { display: none; }
    .filter-pill { display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1.5px solid var(--border); background: var(--surface-alt); color: var(--text-3); cursor: pointer; transition: all 0.15s; flex-shrink: 0; }
    .filter-pill:hover { border-color: var(--blue); color: var(--blue); }
    .filter-pill.active { background: var(--blue); color: white; border-color: var(--blue); }
    .filter-pill .pill-count { font-size: 0.68rem; background: rgba(0,0,0,0.08); padding: 1px 7px; border-radius: 10px; font-weight: 800; }
    .filter-pill.active .pill-count { background: rgba(255,255,255,0.25); }

    .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .mini-stat { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 1rem 1.25rem; box-shadow: var(--shadow-sm); }
    .mini-stat-label { font-size: 0.75rem; font-weight: 600; color: var(--text-3); text-transform: uppercase; margin-bottom: 4px; }
    .mini-stat-val { font-size: 1.5rem; font-weight: 900; color: var(--blue-dark); }
    
    @media (max-width: 768px) { .stat-row { grid-template-columns: 1fr; } }

    /* ── Excel Download Dropdown ── */
    .excel-dropdown-wrap { position: relative; }
    .btn-excel {
      display: inline-flex; align-items: center; gap: 7px;
      height: 34px; padding: 0 14px;
      background: linear-gradient(135deg, #16a34a, #15803d);
      color: #fff; border: none; border-radius: 8px;
      font-size: 0.8rem; font-weight: 700; cursor: pointer;
      box-shadow: 0 2px 8px rgba(22,163,74,0.3); transition: all 0.15s;
      white-space: nowrap;
    }
    .btn-excel:hover { background: linear-gradient(135deg, #15803d, #166534); box-shadow: 0 4px 14px rgba(22,163,74,0.4); transform: translateY(-1px); }
    .btn-excel svg { flex-shrink: 0; }
    .btn-excel .excel-chevron { transition: transform 0.2s; }
    .btn-excel.open .excel-chevron { transform: rotate(180deg); }

    .excel-dropdown { display: none; position: fixed; background: var(--surface); border: 1.5px solid var(--border); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); min-width: 230px; z-index: 99999; overflow: hidden; animation: dropIn 0.18s ease; }
    .excel-dropdown.show { display: block; }
    @keyframes dropIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
    .excel-dropdown-header {
      padding: 10px 14px 6px;
      font-size: 0.68rem; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.08em; color: var(--text-3); border-bottom: 1px solid var(--border);
    }
    .excel-option {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 14px; font-size: 0.82rem; font-weight: 600;
      color: var(--text); text-decoration: none; transition: background 0.12s;
    }
    .excel-option:hover { background: var(--surface-alt); color: var(--blue); }
    .excel-option .excel-dot {
      width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }
    .excel-option .excel-count {
      margin-left: auto; font-size: 0.68rem; font-weight: 800;
      background: var(--surface-alt); padding: 1px 8px; border-radius: 10px;
      color: var(--text-3);
    }
    .excel-option:hover .excel-count { background: rgba(26,86,160,0.1); color: var(--blue); }
  </style>

  <div class="stat-row">
    <div class="mini-stat"><div class="mini-stat-label">Total Registered</div><div class="mini-stat-val"><?= $totalCount ?></div></div>
    <div class="mini-stat"><div class="mini-stat-label">Verified</div><div class="mini-stat-val"><?= $verifiedCount ?></div></div>
    <div class="mini-stat"><div class="mini-stat-label">Pending Approval</div><div class="mini-stat-val"><?= $pendingApprovalCount ?></div></div>
    <div class="mini-stat"><div class="mini-stat-label">Pending Email</div><div class="mini-stat-val"><?= $pendingEmailCount ?></div></div>
  </div>

  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-head-left">
        <div class="panel-head-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
        <span class="panel-head-title">Alumni Directory</span>
      </div>
      <div class="panel-head-right" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <?php
        $batch_years_query = $conn->query("SELECT DISTINCT batch_year FROM alumni_register WHERE batch_year != '' AND batch_year IS NOT NULL ORDER BY batch_year DESC");
        $batch_years = [];
        if ($batch_years_query) {
            while($by_row = $batch_years_query->fetch_assoc()) {
                $batch_years[] = $by_row['batch_year'];
            }
        }
        ?>
        <select id="yearFilter" class="status-select" style="min-width: 120px;">
          <option value="all">All Years</option>
          <?php foreach($batch_years as $yr): ?>
            <option value="<?= htmlspecialchars($yr) ?>"><?= htmlspecialchars($yr) ?></option>
          <?php endforeach; ?>
        </select>
        
        <div class="search-form">
          <input type="text" id="liveSearch" class="search-input" placeholder="Search name, email, batch..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" autocomplete="off">
          <div class="search-icon"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></div>
        </div>

        <!-- Excel Download Dropdown -->
        <div class="excel-dropdown-wrap">
          <button id="excelDropdownBtn" class="btn-excel" onclick="toggleExcelDropdown()" type="button">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
              <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" y1="13" x2="8" y2="13"/>
              <line x1="16" y1="17" x2="8" y2="17"/>
              <polyline points="10 9 9 9 8 9"/>
            </svg>
            Download Excel
            <svg class="excel-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="excel-dropdown" id="excelDropdown">
            <div class="excel-dropdown-header">📊 Export Users as Excel</div>
            <a href="download_alumni_excel.php?filter=all" class="excel-option">
              <span class="excel-dot" style="background:#6366f1;"></span>
              All Users
              <span class="excel-count"><?= $totalCount ?></span>
            </a>
            <a href="download_alumni_excel.php?filter=approved" class="excel-option">
              <span class="excel-dot" style="background:#16a34a;"></span>
              Approved Users
              <span class="excel-count"><?= $verifiedCount ?></span>
            </a>
            <a href="download_alumni_excel.php?filter=rejected" class="excel-option">
              <span class="excel-dot" style="background:#ef4444;"></span>
              Rejected Users
              <span class="excel-count"><?= $rejectedCount ?></span>
            </a>
            <a href="download_alumni_excel.php?filter=pending-email" class="excel-option">
              <span class="excel-dot" style="background:#f59e0b;"></span>
              Pending Email Users
              <span class="excel-count"><?= $pendingEmailCount ?></span>
            </a>
            <a href="download_alumni_excel.php?filter=pending-approval" class="excel-option">
              <span class="excel-dot" style="background:#36a2eb;"></span>
              Pending Approval Users
              <span class="excel-count"><?= $pendingApprovalCount ?></span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="filter-bar">
      <div class="filter-pill active" data-filter="all">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        All <span class="pill-count"><?= $totalCount ?></span>
      </div>
      <div class="filter-pill" data-filter="pending-email">Pending Email <span class="pill-count"><?= $pendingEmailCount ?></span></div>
      <div class="filter-pill" data-filter="pending-approval">Pending Approval <span class="pill-count"><?= $pendingApprovalCount ?></span></div>
      <div class="filter-pill" data-filter="approved">Approved <span class="pill-count"><?= $verifiedCount ?></span></div>
      <div class="filter-pill" data-filter="rejected">Rejected <span class="pill-count"><?= $rejectedCount ?></span></div>
    </div>

    <?php
    $search = isset($_GET['search']) ? $conn->real_escape_string(trim($_GET['search'])) : '';
    $where_clause = "";
    if ($search !== '') {
        $where_clause = " WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' OR batch_year LIKE '%$search%'";
    }
    
    $sql = "SELECT * FROM alumni_register $where_clause ORDER BY id DESC";
    $result = $conn->query($sql);
    ?>

    <form id="bulkForm" action="users.php" method="POST">
    <input type="hidden" name="bulk_action" id="bulkActionType" value="">
    <?php if (check_admin_permission('edit') || check_admin_permission('delete')): ?>
    <div id="bulkActions" style="display:none; padding: 1rem 1.5rem; background: var(--surface-alt); border-bottom: 1px solid var(--border); align-items: center; gap: 10px; flex-wrap: wrap;">
      <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-3); margin-right: 5px;">Bulk Actions:</span>
      
      <?php if (check_admin_permission('edit')): ?>
      <button type="button" onclick="submitBulkAction('approve')" class="btn-action-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; background: #16a34a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; transition: background 0.15s; width: max-content;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Approve Selected
      </button>
      <button type="button" onclick="submitBulkAction('reject')" class="btn-action-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; transition: background 0.15s; width: max-content;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Reject Selected
      </button>
      <?php endif; ?>

      <?php if (check_admin_permission('delete')): ?>
      <button type="button" onclick="submitBulkAction('delete')" class="btn-action-sm btn-delete-sm" style="display:inline-flex; align-items:center; gap:5px; height:34px; padding:0 14px; font-size:0.8rem; width:max-content; border-radius: 8px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Delete Selected
      </button>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="dash-table" id="usersTable">
        <thead>
          <tr>
            <?php if (check_admin_permission('delete')): ?>
            <th style="width: 40px; text-align:center;"><input type="checkbox" id="selectAll"></th>
            <?php else: ?>
            <th style="width: 40px;"></th>
            <?php endif; ?>
            <th>ID</th>
            <th>Name & Email</th>
            <th>Batch</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Order No</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): 
            $parts = explode(' ', trim($row['full_name']));
            $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
            $statusVal = $row['verified_status'];
            if ($statusVal == 1) {
                $statusClass = 'badge-verified';
                $statusText = 'Verified';
            } elseif ($statusVal == 3) {
                $statusClass = 'badge-rejected';
                $statusText = 'Rejected';
            } elseif ($statusVal == 2) {
                $statusClass = 'badge-email-verified';
                $statusText = 'Email Verified';
            } else {
                $statusClass = 'badge-pending';
                $statusText = 'Pending Email';
            }
          ?>
          <tr>
            <?php if (check_admin_permission('delete')): ?>
            <td style="text-align:center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="rowCheckbox"></td>
            <?php else: ?>
            <td></td>
            <?php endif; ?>
            <td data-label="ID"><span style="font-size:0.8rem; font-weight:700; color:var(--text-3);">#<?= $row['id'] ?></span></td>
            <td data-label="User">
              <div class="user-info">
                <div class="user-avatar"><?= $initials ?></div>
                <div class="user-details">
                  <a href="users_list_view.php?id=<?= $row['id'] ?>" class="user-name"><?= htmlspecialchars($row['full_name']) ?></a>
                  <span class="user-email"><?= htmlspecialchars($row['email']) ?></span>
                </div>
              </div>
            </td>
            <td data-label="Batch"><span style="font-weight:600; color:var(--text-2);"><?= htmlspecialchars($row['batch_year']) ?></span></td>
            <td data-label="Status">
              <span class="badge-status <?= $statusClass ?>">
                <?php if($row['verified_status'] == 1): ?><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg><?php endif; ?>
                <?= $statusText ?>
              </span>
              <?php
                $uId = $row['id'];
                $hasUpd = $conn->query("SELECT id FROM profile_update_requests WHERE alumni_id = $uId AND status = 0")->num_rows > 0;
                if ($hasUpd) echo '<div style="margin-top:4px;"><a href="profile_requests.php" style="font-size:0.65rem; color:#d39e00; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:3px;"><i class="fas fa-exclamation-circle"></i> Pending Update</a></div>';
              ?>
            </td>
            <td data-label="Joined"><span style="font-size:0.85rem; color:var(--text-3);"><?= date("d M Y", strtotime($row['created_at'])) ?></span></td>
            <td data-label="Order No">
              <?php if ($row['verified_status'] == 1 && isset($row['showcase_order']) && $row['showcase_order'] > 0): ?>
                <span style="font-weight:800; color:var(--blue); background:var(--blue-light); padding:2px 8px; border-radius:12px; font-size:0.75rem;"><?= $row['showcase_order'] ?></span>
              <?php else: ?>
                <span style="color:var(--text-3); font-size:0.8rem;">—</span>
              <?php endif; ?>
            </td>
            <td data-label="Action">
              <div class="actions">
                <a href="users_list_view.php?id=<?= $row['id'] ?>" class="btn-view">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  Profile
                </a>
                <div style="margin: 0;">
                  <select name="status" onchange="updateSingleStatus(<?= $row['id'] ?>, this.value)" class="status-select" <?= !check_admin_permission('edit') ? 'disabled' : '' ?>>
                    <option value="0" <?= $row['verified_status'] == 0 ? 'selected' : '' ?>>Pending Email</option>
                    <option value="2" <?= $row['verified_status'] == 2 ? 'selected' : '' ?>>Pending Approval</option>
                    <option value="1" <?= $row['verified_status'] == 1 ? 'selected' : '' ?>>Approved</option>
                    <option value="3" <?= $row['verified_status'] == 3 ? 'selected' : '' ?>>Rejected</option>
                  </select>
                </div>

                <?php if (check_admin_permission('delete')): ?>
                <button type="button" class="btn-action-sm btn-delete-sm" onclick="confirmDeleteUser(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['full_name'])) ?>', '<?= htmlspecialchars(addslashes($row['batch_year'])) ?>')" data-tooltip="Delete">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      </form>
    </div>
  </div>
</div>

<!-- Action Confirmation Modal -->
<div id="confirmationModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(15,37,69,0.4); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1.5rem;">
  <div style="background:var(--surface); max-width:400px; width:100%; padding:2.25rem; border-radius:20px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
    <div id="modalIconContainer" style="width:56px; height:56px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">
      <span id="modalIcon" style="display: flex; align-items: center; justify-content: center;"></span>
    </div>
    <h3 id="modalTitle" style="font-size:1.25rem; font-weight:900; color:var(--text); margin-bottom:0.5rem;">Confirm Action</h3>
    <p id="modalDescription" style="font-size:0.92rem; color:var(--text-2); line-height:1.5; margin-bottom:1.5rem;"></p>
    <div style="display:flex; gap:10px;">
      <button onclick="closeConfirmModal()" style="flex:1; height:44px; border-radius:10px; font-weight:700; background:var(--surface-alt); border:1.5px solid var(--border); color:var(--text-2); cursor:pointer;">Cancel</button>
      <button id="modalConfirmBtn" style="flex:1; height:44px; border-radius:10px; font-weight:700; color:white; border:none; cursor:pointer;">Confirm</button>
    </div>
  </div>
</div>

<script>
  function updateSingleStatus(userId, statusVal) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'users.php';
    form.innerHTML = '<input type="hidden" name="user_id" value="' + userId + '"><input type="hidden" name="status" value="' + statusVal + '"><input type="hidden" name="update_status" value="1">';
    document.body.appendChild(form);
    form.submit();
  }

  // ── Combined live search + status filter ──────────────────
  const searchInput = document.getElementById('liveSearch');
  const yearFilter = document.getElementById('yearFilter');
  const tableBody = document.querySelector('#usersTable tbody');
  const rows = tableBody ? tableBody.querySelectorAll('tr') : [];
  const pills = document.querySelectorAll('.filter-pill');
  let activeFilter = 'all';

  // Map status text/class to filter key
  function getRowStatus(row) {
    const badge = row.querySelector('.badge-status');
    if (!badge) return '';
    if (badge.classList.contains('badge-verified')) return 'approved';
    if (badge.classList.contains('badge-rejected')) return 'rejected';
    if (badge.classList.contains('badge-email-verified')) return 'pending-approval';
    if (badge.classList.contains('badge-pending')) return 'pending-email';
    return '';
  }

  function applyFilters() {
    const query = searchInput.value.toLowerCase().trim();
    const activeYear = yearFilter ? yearFilter.value : 'all';

    rows.forEach(function(row) {
      const name = (row.querySelector('.user-name') || {}).textContent || '';
      const email = (row.querySelector('.user-email') || {}).textContent || '';
      const batch = row.cells[3] ? row.cells[3].textContent.trim() : '';
      const combined = (name + ' ' + email + ' ' + batch).toLowerCase();

      const matchesSearch = combined.includes(query);
      const matchesStatus = (activeFilter === 'all') || (getRowStatus(row) === activeFilter);
      const matchesYear = (activeYear === 'all') || (batch === activeYear);

      row.style.display = (matchesSearch && matchesStatus && matchesYear) ? '' : 'none';
    });
  }

  // Search and dropdown input listeners
  searchInput.addEventListener('input', applyFilters);
  if (yearFilter) yearFilter.addEventListener('change', applyFilters);

  // Filter pill click listener
  pills.forEach(function(pill) {
    pill.addEventListener('click', function() {
      pills.forEach(function(p) { p.classList.remove('active'); });
      this.classList.add('active');
      activeFilter = this.dataset.filter;
      applyFilters();
    });
  });

  // Trigger on load if pre-filled
  if (searchInput.value.trim() !== '') {
    applyFilters();
  }

  // ── Confirmation Modal Logic ──────────────────────────────
  let confirmCallback = null;

  function openConfirmModal(options) {
    const modal = document.getElementById('confirmationModal');
    const title = document.getElementById('modalTitle');
    const desc = document.getElementById('modalDescription');
    const btn = document.getElementById('modalConfirmBtn');
    const iconContainer = document.getElementById('modalIconContainer');
    const icon = document.getElementById('modalIcon');

    title.textContent = options.title;
    desc.innerHTML = options.description;
    btn.textContent = options.confirmText;
    btn.style.background = options.confirmColor;
    iconContainer.style.background = options.iconBg;
    iconContainer.style.color = options.iconColor;
    icon.innerHTML = options.iconSvg;

    confirmCallback = options.onConfirm;
    modal.style.display = 'flex';
  }

  function closeConfirmModal() {
    document.getElementById('confirmationModal').style.display = 'none';
    confirmCallback = null;
  }

  document.getElementById('modalConfirmBtn').addEventListener('click', function() {
    if (confirmCallback) {
      confirmCallback();
    }
    closeConfirmModal();
  });

  // ── Delete / Bulk Actions triggers ───────────────────────
  function confirmDeleteUser(id, name, batch) {
    openConfirmModal({
      title: 'Delete User',
      description: 'Are you sure you want to permanently remove <strong>' + name + '</strong> ' + (batch ? '(Batch: ' + batch + ')' : '') + '? This action cannot be undone.',
      confirmText: 'Yes, Delete',
      confirmColor: 'var(--red, #c0392b)',
      iconBg: 'var(--red-bg, #fef2f2)',
      iconColor: 'var(--red, #c0392b)',
      iconSvg: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>',
      onConfirm: function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'users.php';
        form.innerHTML = '<input type="hidden" name="user_id" value="' + id + '"><input type="hidden" name="delete_user" value="1">';
        document.body.appendChild(form);
        form.submit();
      }
    });
  }

  function submitBulkAction(action) {
    const selected = document.querySelectorAll('.rowCheckbox:checked').length;
    if (selected === 0) return;

    let title = '';
    let desc = '';
    let confirmText = '';
    let confirmColor = '';
    let iconBg = '';
    let iconColor = '';
    let iconSvg = '';

    if (action === 'approve') {
      title = 'Bulk Approve';
      desc = 'Are you sure you want to approve the <strong>' + selected + ' selected user(s)</strong>? This will activate their profiles and send approval emails.';
      confirmText = 'Yes, Approve';
      confirmColor = '#16a34a';
      iconBg = '#f0fdf4';
      iconColor = '#16a34a';
      iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>';
    } else if (action === 'reject') {
      title = 'Bulk Reject';
      desc = 'Are you sure you want to reject the <strong>' + selected + ' selected user(s)</strong>? This will set their status to Rejected and send rejection emails.';
      confirmText = 'Yes, Reject';
      confirmColor = '#ef4444';
      iconBg = '#fff1f2';
      iconColor = '#ef4444';
      iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
    } else if (action === 'delete') {
      title = 'Bulk Delete';
      desc = 'Are you sure you want to permanently delete the <strong>' + selected + ' selected user(s)</strong>? This will remove all their data and cannot be undone.';
      confirmText = 'Yes, Delete';
      confirmColor = 'var(--red, #c0392b)';
      iconBg = 'var(--red-bg, #fef2f2)',
      iconColor = 'var(--red, #c0392b)',
      iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>';
    }

    openConfirmModal({
      title: title,
      description: desc,
      confirmText: confirmText,
      confirmColor: confirmColor,
      iconBg: iconBg,
      iconColor: iconColor,
      iconSvg: iconSvg,
      onConfirm: function() {
        document.getElementById('bulkActionType').value = action;
        document.getElementById('bulkForm').submit();
      }
    });
  }

  // Bulk selection logic
  const selectAll = document.getElementById('selectAll');
  const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
  const bulkActions = document.getElementById('bulkActions');

  function toggleBulkActions() {
    const anyChecked = document.querySelectorAll('.rowCheckbox:checked').length > 0;
    if(bulkActions) bulkActions.style.display = anyChecked ? 'flex' : 'none';
  }

  if (selectAll) {
    selectAll.addEventListener('change', function() {
      rowCheckboxes.forEach(cb => {
        // Only check visible rows
        if (cb.closest('tr').style.display !== 'none') {
          cb.checked = this.checked;
        }
      });
      toggleBulkActions();
    });
  }

  rowCheckboxes.forEach(cb => {
    cb.addEventListener('change', toggleBulkActions);
  });

  // ── Excel Dropdown ────────────────────────────────────────
  function toggleExcelDropdown() {
    const btn = document.getElementById('excelDropdownBtn');
    const dd  = document.getElementById('excelDropdown');
    const isOpen = dd.classList.contains('show');
    if (!isOpen) {
      const rect = btn.getBoundingClientRect();
      dd.style.top   = (rect.bottom + 6 + window.scrollY) + 'px';
      dd.style.right = (window.innerWidth - rect.right) + 'px';
    }
    dd.classList.toggle('show', !isOpen);
    btn.classList.toggle('open', !isOpen);
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const wrap = document.querySelector('.excel-dropdown-wrap');
    if (wrap && !wrap.contains(e.target)) {
      document.getElementById('excelDropdown').classList.remove('show');
      document.getElementById('excelDropdownBtn').classList.remove('open');
    }
  });
</script>
</body></html>