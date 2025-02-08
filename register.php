<?php
require 'connect.php'; // Include the database connection
require 'vendor/autoload.php'; // Autoload PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$registration_successful = false; // Flag to check if registration was successful
$email_error_message = "";
$phone_error_message = "";
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
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $email_error_message = "An account with this email already exists.";
        } else {
            $check_phone_query = "SELECT * FROM users WHERE phone = :phone";
            $stmt = $conn->prepare($check_phone_query);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $phone_error_message = "An account with this phone number already exists. ";
            } else {
                // Generate verification token
                $verification_token = bin2hex(random_bytes(50));
                $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

                // Insert new user into the database
                $insert_query = "INSERT INTO users (fullname, email, phone, password, verification_token, is_verified, created_at) VALUES (:fullname, :email, :phone, :password, :verification_token, 0, NOW())";
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
                        $mail->setFrom('step@gmail.com', 'Step Shoes Shop');
                        $mail->addAddress($email);

                        ob_start(); // Start output buffering
                        include 'email_verify.php';
                        $email_body = ob_get_clean();

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification';
                        $mail->Body = $email_body;

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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/collection/components/icon/icon.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Register</title>
</head>

<body>
    <section>
        <div class="form-box">
            <h2>Register</h2>
            <form action="register.php" method="post" onsubmit="return validateForm()">
                <div class="form-row">
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="text" name="fullname" id="fullname" required onkeypress="validateFullname(event)">
                            <label>Full name</label>
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                        <span id="fullname_error" class="error-message"></span>
                        <div class="inputbox">
                            <input type="email" name="email" id="email" required oninput="validateEmail()">
                            <label>Email</label>
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>
                        <span id="email_error" class="error-message"><?php echo $email_error_message; ?></span>
                        <div class="inputbox">
                            <input type="tel" name="phone" id="phone" required value="+601" oninput="enforceMalaysianPhoneFormat()">
                            <label>Phone</label>
                            <ion-icon name="call-outline"></ion-icon>
                        </div>
                        <span id="phone_error" class="error-message"><?php echo $phone_error_message; ?></span>
                    </div>
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="password" id="password" name="password" required oninput="validatePassword()">
                            <label>Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                        <span id="password_error" class="error-message"></span>
                        <div class="inputbox">
                            <input type="password" id="confirm_password" name="confirm_password" required oninput="checkPasswordMatch()">
                            <label>Confirm Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                        <span id="confirm_password_error" class="error-message"></span>
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
        function enforceMalaysianPhoneFormat() {
            const phoneInput = document.getElementById("phone");
            const phoneError = document.getElementById("phone_error");
            const prefix = "+601";

            // If the input is empty, set it to the prefix
            if (phoneInput.value === "") {
                phoneInput.value = prefix;
                phoneError.textContent = "";
                phoneError.style.display = "none";
                return;
            }

            // If the input does not start with the prefix, prepend it
            if (!phoneInput.value.startsWith(prefix)) {
                phoneInput.value = prefix;
            }

            // Get the current value after the prefix
            let currentInput = phoneInput.value.replace(prefix, '');

            // Remove any non-digit characters
            currentInput = currentInput.replace(/[^0-9]/g, '');


            // Limit the number of digits to 9 (for a total of 12 characters including the prefix)
            if (currentInput.length > 8) {
                currentInput = currentInput.slice(0, 8);
            }
            if (currentInput.length > 1) {
                currentInput = currentInput.slice(0, 1) + '-' + currentInput.slice(1);
            }
            if (currentInput.length > 5) {
                currentInput = currentInput.slice(0, 5) + ' ' + currentInput.slice(5);
            }

            // Update the input value with the prefix and the digits
            phoneInput.value = prefix + currentInput;

            // Validate phone number length
            if (phoneInput.value.length < 11) {
                phoneError.textContent = "Phone number must be at least 11 characters long.";
                phoneError.style.display = "block";
            } else {
                phoneError.textContent = ""; // Clear error message if valid
                phoneError.style.display = "none";
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const confirmPasswordError = document.getElementById("confirm_password_error");

            if (password !== confirmPassword) {
                confirmPasswordError.textContent = "Passwords do not match!";
            } else {
                confirmPasswordError.textContent = "";
            }
        }

        function validateFullname(event) {
            const fullnameInput = document.getElementById("fullname");
            const char = String.fromCharCode(event.which); // Get the character being typed
            const fullnameError = document.getElementById("fullname_error");

            // Allow letters and spaces only
            if (!/^[a-zA-Z\s]*$/.test(char)) {
                event.preventDefault(); // Prevent the character from being entered
                fullnameError.textContent = "Full name can only contain letters and spaces.";
            } else {
                fullnameError.textContent = "";
            }
        }

        function validateEmail() {
            const emailInput = document.getElementById("email");
            const email = emailInput.value;
            const errorMessage = document.getElementById("email_error");

            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!emailRegex.test(email)) {
                errorMessage.textContent = "Please enter a valid email address.";
                return false;
            }

            const [localPart, domainPart] = email.split("@");
            if (!domainPart) {
                errorMessage.textContent = "Invalid email format.";
                return false;
            }

            const repeatedCharsRegex = /(.)\1{2,}/;
            if (repeatedCharsRegex.test(domainPart)) {
                errorMessage.textContent = "Domain name cannot have repeated characters.";
                return false;
            }

            const commonMisspellings = ["gmal.com", "yaho.com", "hotmal.com", "outlok.com"];
            if (commonMisspellings.includes(domainPart)) {
                errorMessage.textContent = "Did you mean 'gmail.com', 'yahoo.com', 'hotmail.com' or 'outlook.com'?";
                return false;
            }

            const validTLDs = ["com", "org", "net", "edu", "gov", "mil", "info", "io", "co", "me"];
            const domainParts = domainPart.split(".");
            const tld = domainParts[domainParts.length - 1];
            if (!validTLDs.includes(tld)) {
                errorMessage.textContent = "Invalid top-level domain.";
                return false;
            }

            errorMessage.textContent = "";
            return true;
        }

        function validatePassword() {
            const passwordInput = document.getElementById("password");
            const passwordError = document.getElementById("password_error");
            const password = passwordInput.value;

            let errorMessages = [];

            if (password.length < 8 || password.length > 12) {
                errorMessages.push("Password must be 8-12 characters long.");
            }

            if (!/[A-Z]/.test(password)) {
                errorMessages.push("Password must contain at least one uppercase letter.");
            }

            if (!/[a-z]/.test(password)) {
                errorMessages.push("Password must contain at least one lowercase letter.");
            }

            if (!/\d/.test(password)) {
                errorMessages.push("Password must contain at least one number.");
            }

            if (!/[\W_]/.test(password)) {
                errorMessages.push("Password must contain at least one special character.");
            }

            if (errorMessages.length > 0) {
                passwordError.textContent = errorMessages.join(" ");
            } else {
                passwordError.textContent = "";
            }
        }

        function removeErrorMessage(message) {
            const passwordError = document.getElementById("password_error");
            const currentErrors = passwordError.textContent.split(" ");

            const updatedErrors = currentErrors.filter(err => err !== message);
            passwordError.textContent = updatedErrors.join(" ");
        }

        document.getElementById("password").addEventListener("input", function() {
            const password = this.value;

            if (password.length >= 8 && password.length <= 12) {
                removeErrorMessage("Password must be 8-12 characters long.");
            }
            if (/[A-Z]/.test(password)) {
                removeErrorMessage("Password must contain at least one uppercase letter.");
            }
            if (/[a-z]/.test(password)) {
                removeErrorMessage("Password must contain at least one lowercase letter.");
            }
            if (/\d/.test(password)) {
                removeErrorMessage("Password must contain at least one number.");
            }
            if (/[\W_]/.test(password)) {
                removeErrorMessage("Password must contain at least one special character.");
            }
        });

        function validateForm() {
            const fullname = document.getElementById("fullname").value;
            const email = document.getElementById("email").value;
            const phone = document.getElementById("phone").value;
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            let isValid = true; // Flag to track overall validity

            // Check if all fields are filled
            if (!fullname || !email || !phone || !password || !confirmPassword) {
                alert("All fields are required.");
                isValid = false;
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                isValid = false;
            }

            // Check if phone number is valid
            if (phone.length < 11) {
                alert("Phone number must be at least 11 characters long.");
                isValid = false;
            }

            // If any validation fails, prevent form submission
            return isValid;
        }

        <?php if ($registration_successful): ?>
            Swal.fire({
                icon: 'success',
                title: 'Registration successful!',
                text: 'Check your email for a verification link to activate your account.'
            });
        <?php endif; ?>
    </script>
</body>

</html>