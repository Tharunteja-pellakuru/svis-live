<?php
date_default_timezone_set("Asia/Kolkata");
$host = "localhost";

$is_localhost = false;
if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $is_localhost = true;
} elseif (strpos(__FILE__, '/Applications/XAMPP/') === 0) {
    $is_localhost = true;
}

if ($is_localhost) {
    $user = "root";
    $pass = "";
    $dbname = "whysocia_Alumni_kse";
} else {
    $user = "svishydedu_u5er"; // your MySQL username
    $pass = "SV!s@1umn!12"; // your MySQL password
    $dbname = "svishydedu_alumni"; // your database name
}

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("SET time_zone = '+05:30'");
?>
