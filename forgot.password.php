<?php
require 'connect.php'; // Include database connection
require 'vendor/autoload.php'; // Autoload PHPMailer

date_default_timezone_set('Asia/Kuala_Lumpur');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT fullname FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $username = $user['fullname'];

        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes")); // Token expires in 5 minutes

        // Update the database with the token and expiry
        $stmt = $conn->prepare("UPDATE users SET reset_token = :reset_token, reset_token_expiry = :reset_token_expiry WHERE email = :email");
        $stmt->bindParam(":reset_token", $token, PDO::PARAM_STR);
        $stmt->bindParam(":reset_token_expiry", $expiry, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        // Send reset link to the user's email using PHPMailer
        $reset_link = "http://localhost/FYP-website/reset.password.php?token=" . $token;

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'huangjiaze81@gmail.com'; // Replace with your email
            $mail->Password = 'eqygfyfgaoywwvqj';    // Replace with your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('huangjiaze81@gmail.com', 'Step Shoes Shop');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';

            // Include the new email template
            ob_start();
            include 'email_reset_password.php';
            $body = ob_get_clean();
            $mail->Body = $body;

            $mail->send();
            $success_message = "Password reset link has been sent to your email. The link will expired at " . date("h:i A", strtotime($expiry)) . " on " . date("d-m-Y", strtotime($expiry)) . ".";
        } catch (Exception $e) {
            $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error_message = "No user found with this email.";
    }

    $stmt = null; // Close the statement
}

// Close the connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/forgot.password.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Forgot Password</title>
</head>

<body>
    <section>
        <div class="form-box">
            <h2>Forgot Password</h2>
            <form action="forgot.password.php" method="post">
                <div class="form-row">
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="email" name="email" required>
                            <label>Email</label>
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <span class="error-message"><?php echo $error_message; ?></span>
                <input type="submit" value="Send Reset Link">
                <div class="login">
                    <p>Remembered your password? <a href="login.php">Login here</a>.</p>
                </div>
            </form>
        </div>
    </section>

    <script>
        <?php if ($success_message): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo $success_message; ?>',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        <?php if ($error_message): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?php echo $error_message; ?>',
                confirmButtonText: 'OK'
            });
        <?php endif ?>
    </script>
</body>

</html>