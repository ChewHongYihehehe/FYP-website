<?php
require 'connect.php'; // Database connection

$error_message = "";
$success_message = "";

// Check if token is in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists and has not expired
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = :reset_token AND reset_token_expiry > NOW()");
    $stmt->bindParam(":reset_token", $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Check if passwords match
            if ($new_password === $confirm_password) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update password in the database and clear the reset token
                $stmt = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :reset_token");
                $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
                $stmt->bindParam(":reset_token", $token, PDO::PARAM_STR);
                $stmt->execute();

                $success_message = "Password has been reset successfully. <a href='login.php'>Login here</a>.";
            } else {
                $error_message = "Passwords do not match.";
            }
        }
    } else {
        $error_message = "Invalid or expired token.";
    }
} else {
    $error_message = "No token provided.";
}

// Close the connection (optional, PDO will close automatically when the script finishes)
$conn = null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/reset_password.css">
    <title>Reset Password</title>
</head>
<body>
    <section>
        <div class="form-box">
            <h2>Reset Password</h2>
            <form action="" method="post">
                <div class="form-row">
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="password" name="new_password" required>
                            <label>New Password</label>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="password" name="confirm_password" required>
                            <label>Confirm Password</label>
                        </div>
                    </div>
                </div>
                <span class="error-message"><?php echo $error_message; ?></span>
                <span class="success-message"><?php echo $success_message; ?></span>
                <input type="submit" value="Reset Password">
            </form>
        </div>
    </section>
</body>
</html>
