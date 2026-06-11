<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('permission_check.php');
require_admin_permission('edit');
include('../db_connect.php');
include('../config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id']) && isset($_POST['action'])) {
    $reqId = intval($_POST['request_id']);
    $action = $_POST['action'];

    // Fetch request data
    $reqQuery = $conn->prepare("SELECT * FROM profile_update_requests WHERE id = ?");
    $reqQuery->bind_param("i", $reqId);
    $reqQuery->execute();
    $reqData = $reqQuery->get_result()->fetch_assoc();

    if (!$reqData) {
        header("Location: profile_requests.php");
        exit;
    }

    $alumniId = $reqData['alumni_id'];
    $name = $reqData['full_name'];
    $email = $reqData['email'];

    if ($action === 'approve') {
        // Update main table
        $updateSql = "UPDATE alumni_register SET 
            `full_name` = ?, `email` = ?, `phone` = ?, `gender` = ?, `batch_year` = ?, `bio` = ?, `user_image` = ?, 
            `Industry` = ?, `Company / Organization Name` = ?, `City` = ?, `country` = ?, `education_qualification` = ?, 
            `college_university` = ?, `education_year` = ?, `linkedin` = ?, `instagram` = ?, 
            `Current Occupation` = ?, `Designation` = ?, `Work Experience` = ?, `dob` = ?, `phonecode` = ?, `current_address` = ?
            WHERE id = ?";
        
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssssssssssisssssssssssi", 
            $reqData['full_name'], $reqData['email'], $reqData['phone'], $reqData['gender'], 
            $reqData['batch_year'], $reqData['bio'], $reqData['user_image'], $reqData['Industry'], 
            $reqData['Company / Organization Name'], $reqData['City'], $reqData['country'], $reqData['education_qualification'], 
            $reqData['college_university'], $reqData['education_year'], $reqData['linkedin'], 
            $reqData['instagram'], $reqData['Current Occupation'], 
            $reqData['Designation'], $reqData['Work Experience'], $reqData['dob'], 
            $reqData['phonecode'], $reqData['current_address'], $alumniId
        );

        if ($stmt->execute()) {
            // Mark request as approved
            $conn->query("UPDATE profile_update_requests SET status = 1 WHERE id = $reqId");
            
            // Send Approval Email
            sendNotification($email, $name, "Approved");
            
            $_SESSION['flash_msg'] = "Profile update approved successfully!";
            $_SESSION['flash_type'] = "success";
        }
    } elseif ($action === 'reject') {
        // Mark request as rejected
        $conn->query("UPDATE profile_update_requests SET status = 2 WHERE id = $reqId");
        
        // Send Rejection Email
        sendNotification($email, $name, "Rejected");
        
        $_SESSION['flash_msg'] = "Profile update request rejected.";
        $_SESSION['flash_type'] = "warning";
    }

    header("Location: profile_requests.php");
    exit;
}

function sendNotification($email, $name, $status) {
    global $conn;

    if ($status === "Approved") {
        $subject = "Profile Update Approved - SVIS Alumni Network";
        $content = "
            <div style='font-family: \"Inter\", sans-serif; max-width: 600px; margin: 0 auto; background-color: #f0f4ff; border-radius: 16px; overflow: hidden; border: 1px solid #e5eeff;'>
                <div style='background: linear-gradient(135deg, #1e3a8a 0%, #1D4ED8 100%); padding: 30px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 24px;'>Update Verified!</h1>
                </div>
                <div style='padding: 30px; background: white;'>
                    <p>Hello $name,</p>
                    <p>We are pleased to inform you that your profile update request has been <strong>Approved</strong> by the administration. The changes are now reflected on your public profile.</p>
                    <p>Thank you for keeping your information up to date!</p>
                </div>
                <div style='background: #1e3a8a; padding: 20px; text-align: center; color: #bfdbfe; font-size: 12px;'>
                    ©️ 2026 SVIS Alumni Network
                </div>
            </div>";
    } else {
        $subject = "Profile Update Request Update - SVIS Alumni Network";
        $content = "
            <div style='font-family: \"Inter\", sans-serif; max-width: 600px; margin: 0 auto; background-color: #fff1f2; border-radius: 16px; overflow: hidden; border: 1px solid #fee2e2;'>
                <div style='background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); padding: 30px; text-align: center; color: white;'>
                    <h1 style='margin: 0; font-size: 24px;'>Update Not Approved</h1>
                </div>
                <div style='padding: 30px; background: white;'>
                    <p>Hello $name,</p>
                    <p>Regarding your recent profile update request, we regret to inform you that it has <strong>not been approved</strong> at this time.</p>
                    <p>This could be due to inaccurate information or missing details. If you have any questions, please contact the administration.</p>
                </div>
                <div style='background: #1e3a8a; padding: 20px; text-align: center; color: #bfdbfe; font-size: 12px;'>
                    ©️ 2026 SVIS Alumni Network
                </div>
            </div>";
    }
    sendBrevoEmail($email, $name, $subject, $content);
}
?>