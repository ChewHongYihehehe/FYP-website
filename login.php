<?php
require 'connect.php'; // Include database connection

session_start();

$error_message = ""; // Variable to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]); // Execute with the email parameter
    $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result

    if ($row) {
        if (password_verify($password, $row['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            header("Location: home.php"); // Redirect to homepage
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with this email.";
    }
    // No need to close the connection explicitly
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/collection/components/icon/icon.min.css">
    <title>Login</title>
</head>

<body>
    <section>
        <div class="form-box">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <div class="form-row">
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="email" name="email" required>
                            <label>Email</label>
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="password" name="password" required>
                            <label>Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <span class="error-message"><?php echo $error_message; ?></span> <!-- Display error message -->
                <input type="submit" value="Login">
                <div class="login">
                    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                    <p><a href="forgot_password.php">Forgot Password? Click here</a></p> <!-- Forgot Password link -->
                </div>
            </form>
        </div>
    </section>

    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
</body>

</html>