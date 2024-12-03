<?php
require 'connect.php'; // Include database connection

session_start();

$error_message = ""; // Variable to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $row['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_email'] = $row['email']; // Optional for user tracking
            header("Location: home.php"); // Redirect to the homepage
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with this email.";
    }
    
    // Reset the statement object (optional, PDO will clean up automatically)
    $stmt = null;
}

// Close the connection (optional, PDO will close automatically when the script finishes)
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>Login</title>
</head>
<body>
    <section>
        <div class="form-box">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <div class="form-column">
                    <div class="inputbox">
                        <input type="email" name="email" required autofocus>
                        <label>Email</label>
                    </div>
                </div>
                <div class="form-column">
                    <div class="inputbox">
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                </div>
                <span class="error-message"><?php echo $error_message; ?></span>
                <input type="submit" value="Login">
                <div class="login">
                    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                    <p><a href="forgot.password.php">Forgot Password? Click here</a></p>
                </div>
            </form>
        </div>
    </section>
</body>
</html>
