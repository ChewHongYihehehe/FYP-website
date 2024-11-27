<?php
require 'db.php'; // Include database connection

session_start();

$error_message = ""; // Variable to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // Bind parameters
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
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
    $stmt->close(); // Close the statement
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/collection/components/icon/icon.min.css">
    <title>Login</title>
</head>
<body>
    <section>
        <div class="form-box">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <!-- Email field at the top -->
                <div class="form-column">
                    <div class="inputbox">
                        <input type="email" name="email" required autofocus>
                        <label>Email</label>
                        <ion-icon name="mail-outline"></ion-icon>
                    </div>
                </div>

                <!-- Password field below email field -->
                <div class="form-column">
                    <div class="inputbox">
                        <input type="password" name="password" required>
                        <label>Password</label>
                        <ion-icon name="lock-closed-outline"></ion-icon>
                    </div>
                </div>

                <!-- Error message and submit button -->
                <span class="error-message"><?php echo $error_message; ?></span>
                <input type="submit" value="Login">
                
                <div class="login">
                    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                    <p><a href="forgot_password.php">Forgot Password? Click here</a></p>
                </div>
            </form>
        </div>
    </section>

    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
</body>
</html>
