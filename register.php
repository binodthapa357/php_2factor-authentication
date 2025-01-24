<?php
include_once "connect.php";

if (isset($_SESSION['otp_sent'])) {
    $otp_sent = $_SESSION['otp_sent'];
    $otp_set = $_SESSION['otp_set'];

    if ($otp_sent == "true" && $otp_set == "false") {
        echo "<script>window.location.href = 'verify_otp.php';</script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $errors = [];

    // Validate name (must begin with a capital letter and include at least 5 characters)
    if (!preg_match("/^[A-Z][a-zA-Z ]{4,}$/", $name)) {
        $errors[] = "Name must start with a capital letter and atleast include more than 5 letters";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate password (minimum 8 characters, at least one uppercase letter, one lowercase letter, one number, and one special character)
    if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $checkemailAvailability = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'"));
        if ($checkemailAvailability == 0) {
            if (mysqli_query($conn, "INSERT INTO users(name, email, password) VALUES ('$name', '$email', '$hashedPassword')")) {
                echo "<script>alert('Registration Successful'); window.location.href = 'login.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error in registration. Please try again later.');</script>";
            }
        } else {
            echo "<script>alert('Email is already registered. Please use a different email.');</script>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <script>
        function validateForm() {
            var name = document.forms["registerForm"]["name"].value;
            var email = document.forms["registerForm"]["email"].value;
            var password = document.forms["registerForm"]["password"].value;

            // Name must start with a capital letter and be at least 5 characters long
            var namePattern = /^[A-Z][a-zA-Z ]{4,}$/;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            // Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character
            var passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!namePattern.test(name)) {
                alert("Name must start with a capital letter and be at least 5 characters long.");
                return false;
            }
            if (!emailPattern.test(email)) {
                alert("Invalid email format.");
                return false;
            }
            if (!passwordPattern.test(password)) {
                alert("Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <div class="signup-form">
        <div class="title">Register Now!</div>
        <form name="registerForm" action="" method="post" onsubmit="return validateForm()">
            <div class="input-boxes">
                <div class="input-box">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>
                <div class="input-box">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="input-box">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <div class="button input-box">
                    <input type="submit" value="Submit">
                </div>
                <div class="text sign-up-text">Already have an account? <a href="login.php"> Login Now</a></div>
            </div>
        </form>
    </div>
</div>
</body>
</html>

<?php
mysqli_close($conn);
?>
