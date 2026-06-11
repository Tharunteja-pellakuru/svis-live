<?php
session_start();
include('db_connect.php'); // Database connection file

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email && $password) {
        $stmt = $conn->prepare("
            SELECT id, full_name, email, password 
            FROM alumni_register 
            WHERE email = ? AND verified_status = 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Login successful
                $_SESSION['alumni_id'] = $row['id'];
                $_SESSION['alumni_name'] = $row['full_name'];
                
                if ($isAjax) {
                    echo json_encode(['status' => 'success', 'message' => 'Login successful! Redirecting...', 'redirect' => 'directory.php']);
                    exit();
                }
                header("Location: directory.php");
                exit();
            } else {
                $errorMsg = 'Invalid password. Please try again.';
                if ($isAjax) {
                    echo json_encode(['status' => 'error', 'message' => $errorMsg]);
                    exit();
                }
                echo "<script>alert('❌ $errorMsg'); window.location='index.php';</script>";
            }
        } else {
            $errorMsg = 'No account found with that email or account not yet verified.';
            if ($isAjax) {
                echo json_encode(['status' => 'error', 'message' => $errorMsg]);
                exit();
            }
            echo "<script>alert('⚠️ $errorMsg'); window.location='index.php';</script>";
        }
        $stmt->close();
    } else {
        $errorMsg = 'Please enter both email and password.';
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => $errorMsg]);
            exit();
        }
        echo "<script>alert('⚠️ $errorMsg'); window.location='index.php';</script>";
    }
}
   ?>