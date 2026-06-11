<?php
include('permission_check.php');
require_admin_permission('edit');
include('../db_connect.php');
include('../config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if(isset($_POST['id']) && isset($_POST['status'])){
    
    $id = intval($_POST['id']);
    $status = intval($_POST['status']);

    $sql = "SELECT * FROM alumni_register WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$name  = $row['full_name'];
$email = $row['email'];


    $stmt = $conn->prepare("UPDATE alumni_register SET verified_status=? WHERE id=?");
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();

    $verifyLink = SITE_URL;

    # Common Mailer Settings
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = TRUE;
    $mail->SMTPSecure = "tls";
    $mail->Port       = SMTP_PORT;
    $mail->Host       = SMTP_HOST;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->IsHTML(true);
    $mail->AddEmbeddedImage(dirname(__DIR__) . '/Logo/Logo.png', 'svis_logo');
    $mail->AddAddress($email, $name);
    $mail->SetFrom(SMTP_FROM, SMTP_FROM_NAME);

    if($status == 1){
        # Approval Email
        $mail->Subject = "Account Approved - SVIS Alumni Network";
        $content = "
            <div style='font-family: \"Inter\", \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f0f4ff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(29,78,216,0.1);'>
                <div style='background: linear-gradient(135deg, #1e3a8a 0%, #1D4ED8 100%); padding: 40px 20px; text-align: center;'>
                    <img src='cid:svis_logo' alt='SVIS Logo' style='height: 80px; margin-bottom: 20px;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.02em;'>You're In!</h1>
                </div>
                <div style='padding: 40px 30px; background-color: #ffffff;'>
                    <h2 style='color: #1e3a8a; margin-top: 0; font-size: 20px;'>Congratulations $name,</h2>
                    <p style='color: #4b5563; line-height: 1.7; font-size: 16px;'>
                        We are happy to inform you that your registration for the <strong>SVIS Alumni Network</strong> has been approved by the administration.
                    </p>
                    <div style='background-color: #f0fdf4; border-left: 4px solid #22c55e; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                        <p style='color: #166534; margin: 0; font-weight: 600; font-size: 15px;'>
                            <i style='margin-right: 8px;'>✅</i> Account Status: Active
                        </p>
                        <p style='color: #4b5563; margin: 10px 0 0 0; font-size: 14px;'>
                            You can now access all features of the portal, including the alumni directory, events registration, and photo galleries.
                        </p>
                    </div>
                    <div style='text-align: center; margin: 35px 0;'>
                        <a href='$verifyLink' style='display: inline-block; background-color: #1D4ED8; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; border: 2px solid #fbbf24; box-shadow: 0 4px 14px rgba(29,78,216,0.3); transition: all 0.3s;'>Access Portal</a>
                    </div>
                </div>
                <div style='background-color: #1e3a8a; padding: 30px; text-align: center; color: #bfdbfe; font-size: 14px;'>
                    <p style='margin: 0;'><strong>Sadhu Vaswani International School, Hyderabad</strong></p>
                    <div style='margin-top: 20px; border-top: 1px solid rgba(191,219,254,0.2); padding-top: 20px;'>
                        <p style='margin: 0; font-size: 12px; color: #9ca3af;'>© 2026 SVIS Alumni Network. All rights reserved.</p>
                    </div>
                </div>
            </div>";
        $mail->MsgHTML($content);
        $mail->Send();
    } elseif($status == 3){
        # Rejection Email
        $mail->Subject = "Registration Status Update - SVIS Alumni Network";
        $content = "
            <div style='font-family: \"Inter\", \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #fff1f2; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05);'>
                <div style='background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); padding: 40px 20px; text-align: center;'>
                    <img src='cid:svis_logo' alt='SVIS Logo' style='height: 80px; margin-bottom: 20px;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;'>Registration Update</h1>
                </div>
                <div style='padding: 40px 30px; background-color: #ffffff;'>
                    <h2 style='color: #991b1b; margin-top: 0; font-size: 20px;'>Hello $name,</h2>
                    <p style='color: #4b5563; line-height: 1.7; font-size: 16px;'>
                        Thank you for your interest in joining the <strong>SVIS Alumni Network</strong>.
                    </p>
                    <div style='background-color: #fff1f2; border-left: 4px solid #ef4444; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                        <p style='color: #991b1b; margin: 0; font-weight: 600; font-size: 15px;'>
                            <i style='margin-right: 8px;'>❌</i> Status: Application Not Approved
                        </p>
                        <p style='color: #6b7280; margin: 10px 0 0 0; font-size: 14px;'>
                            After reviewing your registration details, we are unable to approve your account at this time. This may be due to incomplete information or a mismatch in our records.
                        </p>
                    </div>
                    <p style='color: #4b5563; line-height: 1.7; font-size: 15px;'>
                        If you believe this is an error, please feel free to reach out to us at <strong>info@svishyd.edu.in</strong> with your graduation details.
                    </p>
                </div>
                <div style='background-color: #1e3a8a; padding: 30px; text-align: center; color: #bfdbfe; font-size: 14px;'>
                    <p style='margin: 0;'><strong>Sadhu Vaswani International School, Hyderabad</strong></p>
                    <div style='margin-top: 20px; border-top: 1px solid rgba(191,219,254,0.2); padding-top: 20px;'>
                        <p style='margin: 0; font-size: 12px; color: #9ca3af;'>© 2026 SVIS Alumni Network. All rights reserved.</p>
                    </div>
                </div>
            </div>";
        $mail->MsgHTML($content);
        $mail->Send();
    }

    echo "Status updated successfully!";
}
?>
