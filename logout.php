<?php
// Start the session
session_start();

// Check if the 'otp' is set in the session
if (isset($_SESSION['otp'])) {
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: login.php");
    exit(); // Ensure no further code is executed after redirect
}
?>
