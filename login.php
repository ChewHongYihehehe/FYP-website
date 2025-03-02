<?php
include 'connect.php'; // Include database connection

session_start();


$error_message = ""; // Variable to hold error messages
$show_terminated_options = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($row['status'] === 'terminated') {

            $error_message = "This account has been terminated. 
                Please contact administrator or register a new account.";

            $show_terminated_options = true;
        }
        // Only attempt password verification if not terminated
        elseif (password_verify($password, $row['password'])) {

            if ($row['is_verified'] == 0) {
                $error_message = "Please verify your email before logging in.";
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_email'] = $row['email'];
                header("Location: home.php");
                exit();
            }
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Login</title>
    <script>
        // Check if there is an error message to display
        var errorMessage = <?= json_encode($error_message); ?>; // Convert PHP variable to JavaScript

        if (errorMessage) {
            window.onload = function() {
                var errorElement = document.querySelector('.error-message');
                errorElement.innerHTML = errorMessage;
                errorElement.classList.add('show'); // Add the show class to change background
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'login.php';
                });
            };
        }
    </script>
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