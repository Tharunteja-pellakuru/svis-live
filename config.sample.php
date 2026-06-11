<?php
/**
 * Configuration file for SVIS Alumni Portal (Template)
 * Copy this file to config.php and enter your credentials.
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_mysql_username');
define('DB_PASS', 'your_mysql_password');
define('DB_NAME', 'your_mysql_database');

// SMTP Configuration
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_smtp_user');
define('SMTP_PASS', 'your_smtp_password');
define('SMTP_FROM', 'your_smtp_from_email');
define('SMTP_FROM_NAME', 'SVIS Alumni School Alumni');

// Site Configuration
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$lowerPath = strtolower($path);
if (substr($lowerPath, -6) === '/admin') {
    $path = substr($path, 0, -6);
} elseif (substr($lowerPath, -6) === '\\admin') {
    $path = substr($path, 0, -6);
}

define('SITE_URL', $protocol . '://' . $host . $path);

define('BREVO_API_KEY', 'your_brevo_api_key_here');

/**
 * Send transactional email using Brevo HTTP API (Port 443)
 * Bypasses all SMTP port blocking firewalls.
 */
function sendBrevoEmail($toEmail, $toName, $subject, $htmlContent) {
    $data = [
        'sender'      => ['name' => SMTP_FROM_NAME, 'email' => SMTP_FROM],
        'to'          => [['email' => $toEmail, 'name' => $toName]],
        'subject'     => $subject,
        'htmlContent' => $htmlContent
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . BREVO_API_KEY,
        'content-type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode !== 201) {
        error_log("Brevo API Error (HTTP $httpCode): " . $response);
    }
    
    curl_close($ch);
    return $httpCode === 201;
}
?>
