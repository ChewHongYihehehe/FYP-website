<?php
require 'db.php'; // Include database connection

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expires in 1 hour

        // Update the database with the token and expiry
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send reset link to the user's email
        $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $token;
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: " . $reset_link;
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            $success_message = "Password reset link has been sent to your email.";
        } else {
            $error_message = "Failed to send reset link. Please try again.";
        }
    } else {
        $error_message = "No user found with this email.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="forgot.password.css">
    <title>Forgot Password</title>
</head>
<body>
    <section>
        <div class="form-box">
            <h2>Forgot Password</h2>
            <form action="forgot_password.php" method="post">
                <div class="form-row">
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="email" name="email" required>
                            <label>Email</label>
                        </div>
                    </div>
                </div>
                <span class="error-message"><?php echo $error_message; ?></span>
                <span class="success-message"><?php echo $success_message; ?></span>
                <input type="submit" value="Send Reset Link">
                <div class="login">
                    <p>Remembered your password? <a href="login.php">Login here</a>.</p>
                </div>
            </form>
        </div>
    </section>
</body>
</html>
