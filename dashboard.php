<?php
// Include the database connection file
include_once "connect.php";

// Handle logout
if (isset($_POST['logout'])) {
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Redirect to register.php
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS file -->
    <title>Dashboard</title>
</head>
<body>
    <div class="wrapper">
        <h1>Welcome to the Dashboard</h1> <!-- Page Title -->
        
        <!-- Logout Form -->
        <form method="post">
            <div class="input-box button">
                <input type="submit" name="logout" value="Logout"> <!-- Logout Button -->
            </div>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
