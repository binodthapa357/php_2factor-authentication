<?php

// Include the database connection file
include_once "connect.php";

// Initialize the message variable
$message = "";

// Check if email is set in the session
if (!isset($_SESSION['email'])) {
    // Redirect to login page if email is not set in the session
    echo "<script>alert('Session expired or invalid access. Please login again.'); window.location.href = 'login.php';</script>";
    exit();
}

$email = $_SESSION['email'];  // Retrieve email from session

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Fetch the hashed password from the database
    $result = mysqli_query($conn, "SELECT password FROM users WHERE email='$email'");

    if ($result) {
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, redirect to dashboard.php
            echo "<script>alert('Login Successful! Redirecting to Dashboard...'); window.location.href = 'dashboard.php';</script>";
            exit();
        } else {
            $message = "Invalid Password. Please try again.";
        }
    } else {
        $message = "An error occurred while processing your request. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Password Verification Page</title>
</head>
<body>
    <div class="container">
        <div class="signup-form">
            <div class="title">Login</div>
            <form action="" method="post">
                <!-- Password input field -->
                <div class="input-box">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" placeholder="Enter Your Password" required>
                </div>

                <!-- Submit button -->
                <div class="button input-box">
                    <input type="submit" value="Login">
                </div>

                <!-- Link to registration page -->
                <div class="text sign-up-text">
                    Don't have an account? <a href="register.php">Register now</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert for password validation result -->
    <?php
    if ($message != "") {
        echo "<script>alert('$message');</script>";
    }
    ?>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
