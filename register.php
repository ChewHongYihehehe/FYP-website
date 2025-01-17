<?php
require 'connect.php'; // Include the database connection
require 'vendor/autoload.php'; // Autoload PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$registration_successful = false; // Flag to check if registration was successful
$error_message = ""; // Variable to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $username = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,12}$/", $password)) {
        $error_message = "Password must be 8-12 characters, with at least one uppercase, one lowercase, one number, and one special character.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = "An account with this email already exists.";
        } else {
            // Generate verification token
            $verification_token = bin2hex(random_bytes(50));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

            // Insert new user into the database
            $insert_query = "INSERT INTO users (fullname, email, phone, password, verification_token, is_verified) VALUES (:fullname, :email, :phone, :password, :verification_token, 0)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bindParam(':fullname', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':verification_token', $verification_token, PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Send verification email
                $verification_link = "http://localhost/FYP-website/verify.php?token=" . $verification_token;

                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'huangjiaze81@gmail.com'; // Replace with your email
                    $mail->Password = 'eqygfyfgaoywwvqj'; // Replace with your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('huangjiaze81@gmail.com', 'Step Shoes Shop');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification';
                    $mail->Body = "Hi $username,<br><br>Please verify your email by clicking on the link below:<br><a href='$verification_link'>$verification_link</a><br><br>Thank you!";

                    $mail->send();
                    $registration_successful = true; // Set success flag
                } catch (Exception $e) {
                    $error_message = "Registration successful, but verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error_message = "Database error: " . $stmt->errorInfo()[2];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/collection/components/icon/icon.min.css">
    <title>Register</title>
</head>

<body>
    <section>
        <div class="form-box">
            <h2>Register</h2>
            <form action="register.php" method="post" onsubmit="return validatePasswords()">
                <div class="form-row">
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="text" name="fullname" required>
                            <label>Full name</label>
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                        <div class="inputbox">
                            <input type="email" name="email" required>
                            <label>Email</label>
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>
                        <div class="inputbox">
                            <input type="tel" name="phone" required>
                            <label>Phone</label>
                            <ion-icon name="call-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="password" id="password" name="password" required>
                            <label>Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                        <small class="password-reminder">
                            Password must be 8-12 characters, with at least one uppercase, one lowercase, one number, and one special character.
                        </small>
                        <div class="inputbox">
                            <input type="password" id="confirm_password" name="confirm_password" required oninput="checkPasswordMatch()">
                            <label>Confirm Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                        <span id="password_error" class="error-message"><?php echo $error_message; ?></span>
                    </div>
                </div>
                <input type="submit" value="Register">
                <div class="login">
                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
                </div>
            </form>
        </div>
    </section>

    <script>
        function checkPasswordMatch() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const errorMessage = document.getElementById("password_error");

            if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match!";
            } else {
                errorMessage.textContent = "";
            }
        }

        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,12}$/;

            if (!strongPasswordRegex.test(password)) {
                alert("Password must meet the required criteria.");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>

    <?php if ($registration_successful): ?>
        <script>
            alert("Registration successful! A verification email has been sent to your email address. Please verify to complete the process.");
        </script>
    <?php endif; ?>
</body>

</html>
