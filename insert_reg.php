<?php 
include('db_connect.php'); 
include('config.php');
?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name   = trim($_POST['full_name']);
    $email  = trim($_POST['email']);
    $phone  = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $batch  = trim($_POST['batch']);
    $dob    = trim($_POST['dob']);
    $city   = trim($_POST['city']);
    $country = trim($_POST['country']);
    $qualification = trim($_POST['qualification']);
    $college = trim($_POST['college']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $terms  = isset($_POST['terms']) ? 1 : 0;

    # Generate verification token
    $token = bin2hex(random_bytes(32)); // secure 64-char token

    if ($name && $email && $gender && $batch && $password && $terms && $dob && $phone && $city && $country && $qualification && $college) {
        
        # Check if email already exists
$check = $conn->prepare("SELECT id FROM alumni_register WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['status' => 'error', 'message' => '❌ This email is already registered!']);
        exit();
    }
    echo "<script>alert('❌ This email is already registered!'); window.location='index.php';</script>";
    exit();
}

        $stmt = $conn->prepare("
            INSERT INTO alumni_register 
            (full_name, email, phone, gender, batch_year, dob, City, country, education_qualification, college_university, password, terms_agreed, verify_token, verified_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
        ");

        $stmt->bind_param("sssssssssssis", $name, $email, $phone, $gender, $batch, $dob, $city, $country, $qualification, $college, $password, $terms, $token);

        if ($stmt->execute()) {

            # Create verification URL  
            $verifyLink = SITE_URL . "/verify.php?token=" . $token;
            $subject = "Registration Received - SVIS Alumni Network";

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
                                <i style='margin-right: 8px;'>📋</i> Registration Status: Pending Approval
                            </p>
                            <p style='color: #6b7280; margin: 10px 0 0 0; font-size: 14px;'>
                                Our administrative team is currently reviewing your details. You will receive another notification once your account has been approved and activated.
                            </p>
                        </div>

                        <p style='color: #4b5563; line-height: 1.7; font-size: 15px;'>
                            In the meantime, please verify your email address to ensure you receive all future communications:
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
                </div>
            ";

            sendBrevoEmail($email, $name, $subject, $content);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['status' => 'success', 'message' => 'Registration received! Please verify your email while we process your approval.']);
                exit();
            }
            echo "<script>alert('Registration received! Please verify your email while we process your approval.'); window.location='index.php';</script>";

        } else {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['status' => 'error', 'message' => '❌ Registration failed! Please try again.']);
                exit();
            }
            echo "<script>alert('❌ Registration failed! Please try again.'); window.location='index.php';</script>";
        }

        $stmt->close();
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'error', 'message' => '⚠️ Please fill all fields and accept Terms & Conditions']);
            exit();
        }
        echo "<script>alert('⚠️ Please fill all fields and accept Terms & Conditions'); window.location='index.php';</script>";
    }
}
?>