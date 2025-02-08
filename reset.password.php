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
                    // Set success message to be displayed in SweetAlert
                    $success_message = "Your password has been reset successfully. You will now be redirected to the login page.";
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <section>
        <div class="form-box">
            <h2>Reset Password</h2>
            <form action="" method="post" onsubmit="return validateForm()">
                <div class="form-column">
                    <div class="inputbox">
                        <input type="password" name="new_password" id="new_password" placeholder=" " required oninput="validatePassword()">
                        <label>New Password</label>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <span class="error-message" id="password_error"></span>
                <div class="form-column">
                    <div class="inputbox">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder=" " required oninput="checkPasswordMatch()">
                        <label>Confirm Password</label>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <span class="error-message" id="confirm_password_error"></span>
                <input type="submit" value="Reset Password">
            </form>
        </div>
    </section>

    <script>
        function validatePassword() {
            const passwordInput = document.getElementById("new_password");
            const passwordError = document.getElementById("password_error");
            const password = passwordInput.value;

            let errorMessages = [];

            if (password.length < 8 || password.length > 12) {
                errorMessages.push("Password must be 8-12 characters long. ");
            }

            if (!/[A-Z]/.test(password)) {
                errorMessages.push("Password must contain at least one uppercase letter.");
            }

            if (!/[a-z]/.test(password)) {
                errorMessages.push("Password must container at least one lowercase letter.");
            }

            if (!/\d/.test(password)) {
                errorMessages.push("Password must container at least one number.");
            }

            if (!/[\W_]/.test(password)) {
                errorMessages.push("Password must contain at least one special characters.");
            }

            if (errorMessages.length > 0) {
                passwordError.textContent = errorMessages.join(" ");
            } else {
                passwordError.textContent = "";
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById("new_password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const confirmPasswordError = document.getElementById("confirm_password_error");

            if (password !== confirmPassword) {
                confirmPasswordError.textContent = "Passwords do not match!";
            } else {
                confirmPasswordError.textContent = "";
            }
        }

        function validateForm() {
            const passwordError = document.getElementById("password_error").textContent;
            const confirmPasswordError = document.getElementById("confirm_password_error").textContent;

            if (passwordError || confirmPasswordError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fix the errors before submitting the form.'
                });
                return false;
            }

            return true;
        }

        <?php if (!empty($success_message)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo $success_message; ?>'
            }).then(() => {
                window.location.href = 'login.php';
            });
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?php echo $error_message; ?>'
            });
        <?php endif; ?>
    </script>
</body>

</html>