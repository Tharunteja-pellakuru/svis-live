<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Authentication Check
if (!isset($_SESSION['admin_id'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit();
    }
    // Redirect standalone downloads or pages
    header("Location: login.php");
    exit();
}

// 2. Permission Checker
if (!function_exists('check_admin_permission')) {
    function check_admin_permission(string $action): bool {
        // SuperAdmin has full bypass
        if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'SuperAdmin') {
            return true;
        }
        
        $permissions = $_SESSION['admin_permissions'] ?? [];
        return !empty($permissions[$action]);
    }
}

// 3. Permission Enforcer
if (!function_exists('require_admin_permission')) {
    function require_admin_permission(string $action) {
        if (!check_admin_permission($action)) {
            $_SESSION['flash_msg'] = "Access Denied: You do not have permission to perform this action ($action).";
            $_SESSION['flash_type'] = 'error';
            
            // Check if AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Access Denied. Insufficient permissions.']);
                exit();
            }
            
            // Redirect back to referer or dashboard
            $referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
            // Avoid redirection loop if referer is the current page itself
            $current_file = basename($_SERVER['PHP_SELF']);
            if (strpos($referer, $current_file) !== false) {
                $referer = 'dashboard.php';
            }
            header("Location: " . $referer);
            exit();
        }
    }
}
?>
