<?php
session_start();

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Set session timeout limit (e.g., 30 minutes)
$timeout_duration = 1800; // 30 minutes in seconds

// Check if the session has expired due to inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session expired, destroy session and redirect to login page
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Ensure user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>