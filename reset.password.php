<?php
require 'connect.php'; // Include database connection

$error_message = "";
$success_message = "";

// Check if token is provided in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and has not expired
    $stmt = $conn->prepare("SELECT email FROM users WHERE reset_token = :reset_token AND reset_token_expiry > NOW()");
    $stmt->bindParam(":reset_token", $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Token is valid
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = $user['email'];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Check if passwords match
            if ($new_password === $confirm_password) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the password and clear the reset token
                $update_stmt = $conn->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE email = :email");
                $update_stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
                $update_stmt->bindParam(":email", $email, PDO::PARAM_STR);

                if ($update_stmt->execute()) {
                    $success_message = "Password has been reset successfully. <a href='login.php'>Login here</a>.";
                } else {
                    $error_message = "Failed to update the password. Please try again.";
                }
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

// Close the connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/reset_password.css">
    <title>Reset Password</title>
</head>
<body>
    <section>
        <div class="form-box">
            <h2>Reset Password</h2>
            <form action="" method="post">
                <div class="form-column">
                    <div class="inputbox">
                        <input type="password" name="new_password" required>
                        <label>New Password</label>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <div class="form-column">
                    <div class="inputbox">
                        <input type="password" name="confirm_password" required>
                        <label>Confirm Password</label>
                        <i class="fas fa-lock"></i>
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
