<?php
include_once "connect.php";

$message = "";

// Set timezone to Nepal
date_default_timezone_set('Asia/Kathmandu');

// Function to generate alphanumeric OTP code
function generateAlphanumericOTP($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $_SESSION['email'] = $email;  // Store email in session

    $message = "";
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $checkUser = mysqli_num_rows($result);

    if ($checkUser == 0) {
        $message = "No User Found";
    } else {
        $message = "";
        $userData = mysqli_fetch_assoc($result); // Fetch associative array from the result

        $otp = generateAlphanumericOTP(); // Generate alphanumeric OTP
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_set'] = 'false';

        // Get current timestamp for OTP creation in Nepal's timezone
        $otpCreatedAt = date("Y-m-d H:i:s");

        // Update the OTP code and creation time in the database
        $updateQuery = "UPDATE users SET otp_code='$otp', otp_created_at='$otpCreatedAt' WHERE email='$email'";
        mysqli_query($conn, $updateQuery);

        // Sending email with OTP
        if (sendEmail('Login OTP - Authentication Session', 'Authentication', $userData['email'], "Hello " . $userData['name'] . "<br><br>Your OTP for login is '$otp' and it will expire in 2 minutes.")) {
            $_SESSION['otp-sent'] = 'true';
            $message = "OTP Sent! Please Check Your Mail";

            // Redirect to verify_otp.php with an alert message
            echo "<script>alert('$message'); window.location.href = 'verify_otp.php';</script>";
            exit(); // Ensure no further code is executed
        }
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
    <title>Login Page</title>
</head>
<body>
<div class="container">

    <div class="signup-form">
        <div class="title">Login</div>
        <form action="" method="post">


                <div class="input-box">
                    <i class="fas fa-envelope"></i>
                    <input type="text" name="email" placeholder="Enter your email" required>
                </div>

                <div class="button input-box">
                    <input type="submit" value="Request OTP">
                </div>

                <?php
        if ($message != "") {
            echo "<script>alert('$message');</script>";
        }
        ?>

                <div class="text sign-up-text"> Don't have an account? <a href="register.php"> Register Now</a></div>
            </div>
            
        </form>
    </div>
</div>
</body>
</html>

<?php
mysqli_close($conn);
?>
