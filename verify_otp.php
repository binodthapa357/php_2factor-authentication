<?php
include_once "connect.php";

// Initialize the message variable
$message = "";

// Check if email is set in session
if (!isset($_SESSION['email'])) {
    // Redirect to login page if email is not set in session
    echo "<script>alert('Session expired or invalid access. Please login again.'); window.location.href = 'login.php';</script>";
    exit();
}

$email = $_SESSION['email']; // Retrieve email from session

if (isset($_POST['validate'])) {
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);

    // Fetch the OTP and its creation time from the database
    $result = mysqli_query($conn, "SELECT otp_code, otp_created_at FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $otpCreatedAt = strtotime($user['otp_created_at']);
        $currentTime = time();
        $timeDiff = $currentTime - $otpCreatedAt;

        // Check if the OTP is expired (2 minutes = 120 seconds)
        if ($timeDiff > 120) {
            $message = "OTP has expired. Please request a new one.";
        } elseif ($user['otp_code'] == $otp) {
            // If the OTP matches, redirect to pass_verify.php
            $_SESSION['otp_set'] = 'true';
            echo "<script>alert('OTP Verified Successfully!'); window.location.href = 'password_verify.php';</script>";
            exit();
        } else {
            $message = "Invalid OTP. Please try again.";
        }
    } else {
        $message = "Invalid OTP. Please try again.";
    }
}

if (isset($_POST['resend'])) {
    function generateAlphanumericOTP($length = 6) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    $otp = generateAlphanumericOTP();
    $otpCreatedAt = date("Y-m-d H:i:s"); // Get current time

    // Update the OTP code and OTP creation time in the database
    $updateQuery = "UPDATE users SET otp_code='$otp', otp_created_at='$otpCreatedAt' WHERE email='$email'";
    mysqli_query($conn, $updateQuery);

    // Fetch the user's name for personalized email content
    $userData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email='$email'"));

    // Resend OTP via email
    if (sendEmail('Resend OTP - Authentication Session', 'Authentication', $email, "Hello " . $userData['name'] . "<br><br>Your new OTP for login is '$otp' and it will expire in 2 minutes.")) {
        $_SESSION['otp-sent'] = 'true';
        $message = "OTP Resent! Please check your email.";
    } else {
        $message = "Failed to resend OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <!-- Fontawesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP Page</title>
</head>
<body>
<div class="container">
    <div class="signup-form">
        <div class="title">Verify OTP</div>
        <form action="" method="post">
            <div class="input-box">
                <i class="fas fa-shield-alt"></i>
                <input type="text" name="otp" placeholder="Enter OTP Code" required>
            </div>

            <div class="button input-box">
                <input type="submit" name="validate" value="Validate OTP">
            </div>

            <div class="button input-box">
                <input type="submit" name="resend" value="Resend OTP">
            </div>

            <div class="text sign-up-text">
                Don't have an account? <a href="register.php">Register now</a>
            </div>
        </form>
    </div>
</div>

<!-- Alert for OTP validation result -->
<?php
if ($message != "") {
    echo "<script>alert('$message');</script>";
}
?>
</body>
</html>

<?php
mysqli_close($conn);
?>
